<?php

namespace Pratiksh\Payable\Services;

use Carbon\Carbon;
use Pratiksh\Payable\Models\Fiscal;

class Payable
{
    /**
     * Return current active fiscal
     */
    public function fiscal(): Fiscal
    {
        // Current Year fiscal duration
        $fiscal_duration = fiscal_duration();
        if (Fiscal::count() > 0) {
            // Retrieving today's date
            $today = Carbon::now();
            // Retrieving fiscal where todays date falls under
            $fiscal = Fiscal::whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first();
            // If found returns the fiscal
            if (! is_null($fiscal)) {
                return $fiscal;
            } else {
                // Check if today falls under fiscal duration
                if ($today->between($fiscal_duration['start'], $fiscal_duration['end'])) {
                    // Native year based on current year
                    $native_year = Payable::currentYear();
                    // Year based on current year
                    $year = $today->year;
                } else {
                    // If today does not falls under current year fiscal duration. Setting to a year back
                    // Native year based on current year
                    $native_year = Payable::currentYear() - 1;
                    // Year based on current year
                    $year = $today->year - 1;
                }
                // Retrieving valid fiscal duration
                $fiscal_duration = fiscal_duration($year, $native_year);

                // Generating current fiscal collection
                return Fiscal::firstOrCreate([
                    'year' => $native_year,
                    'start_date' => $fiscal_duration['start'],
                    'end_date' => $fiscal_duration['end'],
                ]);
            }
        }

        // If now fiscal collection found creating a new one
        return Fiscal::firstOrCreate([
            'year' => Payable::currentYear(),
            'start_date' => $fiscal_duration['start'],
            'end_date' => $fiscal_duration['end'],
        ]);
    }

    /**
     * Return native year
     */
    public function currentYear(): int
    {
        $config_current_year = config('payable.current_year');
        if ($config_current_year) {
            if (class_exists($config_current_year)) {
                $current_year = new $config_current_year;

                return $current_year();
            }
        }

        $current_year = new CurrentYear;

        return $current_year();
    }

    /**
     * Check if given native year is leap year or not
     */
    public function isLeapYear($year = null): bool
    {
        $config_leap_year = config('payable.leap_year');
        if ($config_leap_year) {
            if (class_exists($config_leap_year)) {
                $leap_year = new $config_leap_year;

                return $leap_year($year);
            }
        }

        $leap_year = new IsLeapYear;

        return $leap_year($year);
    }

    /**
     * Returns receipt no structure
     */
    public function receipt_no($year = null): string
    {
        $config_receipt_no = config('payable.receipt_no');

        if ($config_receipt_no) {
            if (class_exists($config_receipt_no)) {
                $receipt_no = new $config_receipt_no;

                return $receipt_no($year);
            }
        }

        $receipt_no = new ReceiptNo;

        return $receipt_no($year);
    }
}
