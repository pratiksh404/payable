<?php

namespace Pratiksh\Payable\Traits;

use Illuminate\Support\Facades\Auth;
use Pratiksh\Payable\Models\Payment;
use Pratiksh\Payable\Models\PaymentHistory;

trait HasPayable
{
    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }


    public function pay(float $amount): Payment
    {
        $payment = $this->payments()->create([
            'amount' => $amount
        ]);

       $this->paymentHistory($payment, PaymentHistory::CREATED);

        return $payment;
    }

    public function modifyPay(Payment $payment, float $amount)
    {
        $old_amount = $payment->amount;

        $payment->update([
            'amount' => $amount
        ]);

        $this->paymentHistory($payment, PaymentHistory::UPDATED, $old_amount);

        return $payment;
    }

    public function paymentHistory(Payment $payment, int $action,$old_amount = null)
    {
        $payment->histories()->create([
            'user_id' => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
            'action' => $action,
            'old_amount' => $action == PaymentHistory::UPDATED ? $old_amount : null,
            'changed_amount' =>  $action == PaymentHistory::UPDATED ? $payment->amount : null
        ]);
    }
}
