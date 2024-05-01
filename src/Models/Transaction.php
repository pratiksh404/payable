<?php

namespace Pratiksh\Payable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pratiksh\Payable\Models\Payment;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['code',
'payment_method',
'amount',
'success',
'data'];

    protected $casts = [
        'data' => 'array',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->table = config('payable.table_prefix', 'payable_') . 'transactions';
    }

    public function setDataAttribute($data)
    {
        $this->attributes['data'] = json_encode($data);
    }

    // Relationship
    public function payment(){
        return $this->hasOne(Payment::class);
    }
}
