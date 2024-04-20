<?php

namespace Pratiksh\Payable\Services;

use Carbon\Carbon;
use Pratiksh\Payable\Contracts\CurrentYearInterface;

class CurrentYear implements CurrentYearInterface
{
    /**
     * Returns current year.
     * You may return your home country native year
     */
    public function __invoke(): int
    {
        return Carbon::now()->year;
    }
}
