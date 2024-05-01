<?php

namespace Pratiksh\Payable\Contracts;

interface PaymentGatewayInterface
{
    public function initiate(float $amount, $return_url, ?array $arguments = null);

    public function inquiry($transaction_id, ?array $arguments = null): array;

    public function isSuccess(array $inquiry, ?array $arguments = null): bool;

    public function requestedAmount(array $inquiry, ?array $arguments = null): float;
}
