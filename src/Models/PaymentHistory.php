<?php

namespace Pratiksh\Payable\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentHistory extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Payment History Actions Flags
    |--------------------------------------------------------------------------
    |
    */
    const CREATED = 1;

    const DELETED = 0;

    const UPDATED = 2;

    protected $guarded = [];

    public function __construct()
    {
        parent::__construct();
        $this->table = config('payable.table_prefix',
            'payable_'
        ).'payment_histories';
    }

    // Relationships
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
