<?php

namespace Pratiksh\Payable\Services;

use Carbon\Carbon;
use Pratiksh\Payable\Models\Fiscal;
use Pratiksh\Payable\Models\Payment;
use Pratiksh\Payable\Services\CurrentYear;

class Payable
{
    public function fiscal() : Fiscal
    {
        if (Fiscal::count() > 0) {
            $today = Carbon::now();
            $fiscal = Fiscal::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();
            if (!is_null($fiscal)) {
                return $fiscal;
            } else {
                $fiscal_duration = fiscal_duration($today->year - 1, Payable::currentYear() - 1);
                return Fiscal::firstOrCreate([
                    'year' => Payable::currentYear() - 1,
                    'start_date' => $fiscal_duration['start'],
                    'end_date' => $fiscal_duration['end'],
                ]);
            }
        }
        $fiscal_duration = fiscal_duration();
        return Fiscal::firstOrCreate([
            'year' => Payable::currentYear(),
            'start_date' => $fiscal_duration['start'],
            'end_date' => $fiscal_duration['end'],
        ]);
    }
    public function currentYear(): int
    {
        $config_current_year = config('payable.current_year');
        if ($config_current_year) {
            if (class_exists($config_current_year)) {
                $current_year =  new $config_current_year;
                return $current_year();
            }
        }

        $current_year =  new CurrentYear;
        return $current_year();
    }


    public function isLeapYear($year = null): bool
    {
        $config_leap_year = config('payable.leap_year');
        if ($config_leap_year) {
            if (class_exists($config_leap_year)) {
                $leap_year =  new $config_leap_year;
                return $leap_year($year);
            }
        }

        $leap_year =  new IsLeapYear;
        return $leap_year($year);
    }

    public function receipt_no($year = null): string
    {
        $config_receipt_no = config('payable.receipt_no');

        if ($config_receipt_no) {
            if (class_exists($config_receipt_no)) {
                $receipt_no =  new $config_receipt_no;
                return $receipt_no($year);
            }
        }

        $receipt_no =  new ReceiptNo;
        return $receipt_no($year);
    }
}
