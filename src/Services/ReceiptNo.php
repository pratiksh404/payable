<?php

namespace Pratiksh\Payable\Services;

use Pratiksh\Payable\Contracts\ReceiptNoInterface;
use Pratiksh\Payable\Facades\Payable;
use Pratiksh\Payable\Models\Payment;

class ReceiptNo implements ReceiptNoInterface
{
    /**
     * Returns receipt no structure.
     */
    public function __invoke($year = null): string
    {
        $year = $year ?? Payable::fiscal()->year;

        return $year.'-'.str_pad(Payment::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
