<?php
namespace Pratiksh\Payable\Contracts;

interface CurrentYearInterface{
    public function __invoke(): int;
}