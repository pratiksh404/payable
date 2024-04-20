<?php

namespace Pratiksh\Payable\Contracts;

interface PayableInterface
{
    public function pay(float $amount);
}
