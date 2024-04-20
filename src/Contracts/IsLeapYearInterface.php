<?php

namespace Pratiksh\Payable\Contracts;

interface IsLeapYearInterface
{
    public function __invoke($year = null): bool;
}
