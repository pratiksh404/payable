<?php

namespace Pratiksh\Payable\Services;

use Carbon\Carbon;
use Pratiksh\Payable\Contracts\IsLeapYearInterface;

class IsLeapYear implements IsLeapYearInterface
{
    /**
     * Leap year logic
     * Determine how leap is year is identified.
     */
    public function __invoke($year = null): bool
    {
        $year = $year ?? Carbon::now()->year;

        return Carbon::create($year)->isLeapYear();
    }
}
