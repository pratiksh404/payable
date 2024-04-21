<?php

use Carbon\Carbon;
use Pratiksh\Payable\Facades\Payable;
use Pratiksh\Payable\Models\Fiscal;

/**
 * Return start and end date of fiscal year.
 *
 * @return array
 */
if (! function_exists('fiscal_duration')) {
    function fiscal_duration($year = null, $native_year = null): array
    {
        $year = $year ?? Carbon::now()->year;
        $native_year = $native_year ?? Payable::currentYear();
        $fiscal_start = Carbon::create($year.'-'.config('payable.start_date', '7-16'));
        if (Payable::isLeapYear($native_year)) {
            $fiscal_start = $fiscal_start->addDay();
        }
        $fiscal_end = $fiscal_start->copy()->addYear()->subDay();

        return [
            'start' => $fiscal_start,
            'end' => $fiscal_end,
        ];
    }
}

/**
 * Return current fiscal model.
 *
 * @return Fiscal
 */
if (! function_exists('active_fiscal')) {
    function active_fiscal(): Fiscal
    {
        return Payable::fiscal();
    }
}
