<?php

namespace Pratiksh\Payable\Services;

use Exception;
use Pratiksh\Payable\Contracts\PaymentGatewayInterface;
use Pratiksh\Payable\Models\Payment;
use Pratiksh\Payable\Models\Transaction;

class PaymentGateway
{
    /**
     * Payment Gateway Service.
     */
    public $gateway;

    public $gateway_name;

    /**
     * HasPayable Model.
     */
    public $model;

    /**
     * Request Payloads.
     */
    public $product_id;

    public $product_name;

    public $return_url;

    public $amount;

    public function __construct(PaymentGatewayInterface $gateway, $model)
    {
        $this->gateway = $gateway;
        $this->gateway_name = $this->getGatewayName($gateway);
        $this->model = $model;
        if (! method_exists($model, 'payments')) {
            throw new Exception(basename($model).' does not have payments method.');
        }
    }

    /**
     * Payment Initiate
     *
     * @return \Illuminate\Http\Response
     */
    public function pay(float $amount, $return_url, $product_id = null, $product_name = null)
    {
        $this->amount = $amount;
        $this->return_url = $return_url;
        $this->product_id = $product_id ?? $this->model->id ?? throw new Exception('product_id not provided.');
        $this->product_name = $product_name ?? $this->model->name ?? throw new Exception('product_name not provided.');

        return $this->gateway->pay(
            $this->amount,
            $this->return_url,
            $this->product_id,
            $this->product_name
        );
    }

    /**
     * Returns payment with gateway transaction and history
     */
    public function process($transaction_id, ?array $arguments = null): Payment
    {
        $request = $arguments;
        $inquiry = $this->gateway->inquiry($transaction_id, $arguments);

        $data = [
            'responses' => [
                'request' => $request,
                'inquiry' => $inquiry,
            ],
        ];

        $success = $this->gateway->isSuccess($inquiry);
        $requested_amount = $this->gateway->requestedAmount($inquiry);

        $transaction = Transaction::create([
            'code' => $transaction_id,
            'payment_method' => $this->gateway_name,
            'amount' => $requested_amount,
            'success' => $success,
            'data' => $data,
        ]);

        if (! Transaction::has('payment')->where('code', $transaction_id)->where('success', true)->exists()) {
            if ($success) {
                $payment = $this->model->pay($requested_amount);
                $payment->update([
                    'transaction_id' => $transaction->id,
                ]);
                $payment->histories()->latest()->first()->update([
                    'verified' => true,
                ]);

                return $payment;
            } else {
                throw new Exception('Payment not register. Payment gateway response states payment is not complete.');
            }
        } else {
            throw new Exception('Successful transaction record of '.$transaction_id.' already exists.');
        }
    }

    /**
     * Payment Gateway Service Class Name
     */
    private function getGatewayName(PaymentGatewayInterface $gateway): string
    {
        $name = get_class($gateway);
        $exploded_name = explode('\\', $name);

        return array_pop($exploded_name);
    }
}
