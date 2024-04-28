<?php

namespace Pratiksh\Payable\Traits;

use Illuminate\Support\Facades\Auth;
use Pratiksh\Payable\Models\Payment;
use Pratiksh\Payable\Models\PaymentHistory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Pratiksh\Payable\Contracts\PaymentGatewayInterface;

trait HasPayable
{
    /**
     * The `payments` function defines a polymorphic relationship in PHP where an object can have
     * multiple payments associated with it.
     *
     * @return The `payments()` function is returning a morphMany relationship in Laravel. This
     *             relationship allows the model to have multiple payments associated with it. The `Payment::class`
     *             specifies the related model class, and `'paymentable'` is the morph name used for the
     *             relationship.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    /**
     * The function `pay` creates a new payment record with the specified amount and logs the payment
     * creation in the payment history.
     *
     * @param float amount The `pay` function takes a parameter `amount` of type float, which represents
     * the amount to be paid. This amount is used to create a new payment entry in the database with the
     * specified amount. After creating the payment entry, the function also logs this action in the
     * payment history as a "
     * @return Payment The `pay` function is returning the `Pratiksh\Payable\Models\Payment` object after creating a new payment
     *                 entry with the specified amount and recording the payment history as "CREATED".
     */
    public function pay(float $amount): Payment
    {
        $payment = $this->payments()->create([
            'amount' => $amount,
        ]);

        $this->paymentHistory($payment, PaymentHistory::CREATED);

        return $payment;
    }

    /**
     * This PHP function modifies the amount of a payment and logs the change in payment history.
     *
     * @param Payment payment The `payment` parameter in the `modifyPay` function is an instance of the
     * `Payment` class. It represents the payment object that you want to modify the amount for.
     * @param float amount The `amount` parameter in the `modifyPay` function is a float type variable
     * that represents the new amount to be set for the payment. This function updates the amount of a
     * payment object to the specified amount and also logs the payment history for the update.
     * @return The `modifyPay` function is returning the updated `Payment` object after modifying the
     *             payment amount and recording the payment history.
     */
    public function modifyPay(Payment $payment, float $amount)
    {
        $old_amount = $payment->amount;

        $payment->update([
            'amount' => $amount,
        ]);

      $this->paymentHistory($payment, PaymentHistory::UPDATED, $old_amount);

        return $payment;
    }

    /**
     * The function `paymentHistory` creates a payment history record with user ID, IP address, action,
     * old amount, and changed amount.
     *
     * @param Payment payment The `payment` parameter is an instance of the `Payment` model class. It is
     * used to store information related to a payment transaction.
     * @param int action The `action` parameter in the `paymentHistory` function is an integer that
     * represents the type of action being performed on the payment. It is used to determine the type of
     * history record to be created.
     * @param old_amount The `old_amount` parameter in the `paymentHistory` function is used to store the
     * previous amount before an update action is performed on a payment. It is only used when the action
     * is `PaymentHistory::UPDATED`. If the action is not an update, the `old_amount` will be set
     */
    public function paymentHistory(Payment $payment, int $action, $old_amount = null)
    {
        return PaymentHistory::create([
            'payment_id' => $payment->id,
            'user_id' => Auth::check() ? Auth::user()->id : null,
            'ip_address' => request()->ip(),
            'action' => $action,
            'old_amount' => $action == PaymentHistory::UPDATED ? $old_amount : null,
            'changed_amount' => $action == PaymentHistory::UPDATED ? $payment->amount : null,
        ]);
    }
}
