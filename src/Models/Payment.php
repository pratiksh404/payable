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

    public function transaction(){
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

    public function verifiedBy(User $user, ?PaymentHistory $history = null)
    {
        $history = $history ?? $this->histories()->latest()->first();
        $history->update([
            'verified' => true,
            'verified_by' => $user->id,
        ]);

        return $this;
    }
}
