<?php

namespace Pratiksh\Payable\Models;

use Illuminate\Support\Str;
use Pratiksh\Payable\Models\Fiscal;
use Illuminate\Database\Eloquent\Model;
use Pratiksh\Payable\Models\PaymentHistory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Pratiksh\Payable\Facades\Payable;

class Payment extends Model
{
    protected $fillable = ['amount','data'];

    protected $keyType = 'string';

 

    public $incrementing = false;

    protected $casts = [
        'data' => 'array'
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
        $this->table = config('payable.table_prefix', 'payable_') . 'payments';
    }


    // Relationships
    public function paymentable(): MorphTo{
        return $this->morphTo();
    }
    public function fiscal(){
        return $this->belongsTo(Fiscal::class);
    }
    public function histories(){
        return $this->hasMany(PaymentHistory::class);
    }
    public function paymentBy(){
        return $this->histories->latest()->first();
    }
    public function verifiedBy(){
        return $this->histories->latest()->first();
    }
}
