<?php
namespace Pratiksh\Payable\Services;

use Pratiksh\Payable\Models\Payment;
use Pratiksh\Payable\Facades\Payable;
use Pratiksh\Payable\Contracts\ReceiptNoInterface;

class ReceiptNo implements ReceiptNoInterface{
    public function __invoke($year = null) : string
    {
        $year = $year ?? Payable::fiscal()->year;
        return $year . '-' . str_pad(Payment::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}