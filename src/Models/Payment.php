<?php

namespace Pratiksh\Payable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Pratiksh\Payable\Facades\Payable;

class Payment extends Model
{
    const CREDIT = 1;

    const DEBIT = 0;

    protected $guarded = [];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'data' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            $model->receipt_no = $model->receipt_no ?? Payable::receipt_no();
            $model->fiscal_id = $model->fiscal_id ?? Payable::fiscal()->id;
        });
    }

    public function __construct()
    {
        parent::__construct();
        $this->table = config('payable.table_prefix', 'payable_').'payments';
    }

    // Eager Load
    protected $with = ['histories', 'histories.paymentBy', 'histories.verifiedBy', 'fiscal'];

    // Appends
    protected $appends = ['payer_name', 'payer_email'];

    // Accessor
    public function getPayerNameAttribute()
    {
        $history = $this->histories()->latest()->first();

        return $history->paymentBy->name ?? $history->data['payer']['name'] ?? null;
    }

    public function getPayerEmailAttribute()
    {
        $history = $this->histories()->latest()->first();

        return $history->paymentBy->email ?? $history->data['payer']['email'] ?? null;
    }

    // Relationships
    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function fiscal()
    {
        return $this->belongsTo(Fiscal::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function payer()
    {
        return $this->histories()->latest()->first()->paymentBy;
    }

    public function verifier()
    {
        return $this->histories()->latest()->first()->verifiedBy;
    }

    public function by(User $user, ?PaymentHistory $history = null)
    {
        $history = $history ?? $this->histories()->latest()->first();
        $history->update([
            'user_id' => $user->id,
        ]);

        return $this;
    }

    public function byName(string $name)
    {
        return $this->byNameAndEmail($name);
    }

    public function byNameAndEmail(string $name, ?string $email = null)
    {
        $history = $history ?? $this->histories()->latest()->first();

        $data = $history->data;

        $data['payer'] = [
            'name' => $name,
            'email' => $email,
        ];

        $history->update([
            'data' => $data,
        ]);

        return $this;
    }

    public function verifiedBy(User $user, ?PaymentHistory $history = null)
    {
        $history = $history ?? $this->histories()->latest()->first();
        $history->update([
            'verified' => true,
            'verified_by' => $user->id,
        ]);

        return $this;
    }

    public function dr()
    {
        $this->update([
            'type' => self::DEBIT,
        ]);

        return $this;
    }

    // Scopes
    public function scopeCredit($query)
    {
        return $query->where('type', self::CREDIT);
    }

    public function scopeDebit($query)
    {
        return $query->where('type', self::DEBIT);
    }
}
