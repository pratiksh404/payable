<?php
namespace Pratiksh\Payable\Contracts;

interface ReceiptNoInterface{
    public function __invoke($year = null): string;
}