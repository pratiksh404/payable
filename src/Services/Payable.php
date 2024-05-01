<?php

namespace Pratiksh\Payable\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Pratiksh\Payable\Models\Fiscal;
use Pratiksh\Payable\Models\Payment;

class Payable
{
    /**
     * Return User Model
     */
    public function user()
    {
        $user_model_name = config('payable.user_model', 'App\Models\User');
        $user_model = new $user_model_name;

        return $user_model;
    }

    /**
     * Return current active fiscal.
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
     * Return native year.
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
     * Check if given native year is leap year or not.
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
     * Returns receipt no structure.
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

    /*
       |--------------------------------------------------------------------------
       | Statistics
       |--------------------------------------------------------------------------
       |
       */
    public function credit(?Carbon $date = null): float
    {
        $query = Payment::credit();
        $query = ! is_null($date)
            ? $query->whereDate('created_at', $date)
            : $query;

        return $query->sum('amount');
    }

    public function debit(?Carbon $date = null): float
    {
        $query = Payment::debit();
        $query = ! is_null($date)
            ? $query->whereDate('created_at', $date)
            : $query;

        return $query->sum('amount');
    }

    public function balance(?Carbon $date = null): float
    {
        $credit = $this->credit($date);
        $debit = $this->debit($date);

        return $credit - $debit;
    }

    public function auditByLastDays($limit = 7, $date_format = 'Y-m-d'): array
    {
        $data = [];
        $start = Carbon::now()->subDays($limit);
        $end = Carbon::now();
        $period = CarbonPeriod::create($start, $end);
        foreach ($period as $date) {
            $credit = $this->credit($date);
            $debit = $this->credit($date);
            $balance = $credit - $debit;
            $data[$date->format($date_format)] = [
                'credit' => $credit,
                'debit' => $debit,
                'balance' => $balance,
            ];
        }

        return $data;
    }

    public function auditByLastMonths($limit = 12, $date_format = 'Y-m'): array
    {
        $data = [];
        $date = Carbon::now()->subMonths($limit);
        $i = 1;
        while ($i <= $limit) {
            $query = Payment::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month);
            $credit = with(clone $query)->credit()->sum('amount');
            $debit = with(clone $query)->debit()->sum('amount');
            $balance = $credit - $debit;
            $data[$date->format($date_format)] = [
                'credit' => $credit,
                'debit' => $debit,
                'balance' => $balance,
            ];
            $date = $date->copy()->addMonth();
            $i++;
        }

        return $data;
    }

    public function auditByFiscal(?Fiscal $fiscal): array
    {
        $fiscal = $fiscal ?? $this->fiscal();
        $query = Payment::where('fiscal_id', $fiscal->id);
        $credit = with(clone $query)->credit()->sum('amount');
        $debit = with(clone $query)->debit()->sum('amount');
        $balance = $credit - $debit;

        return [
            'credit' => $credit,
            'debit' => $debit,
            'balance' => $balance,
        ];
    }

    public function auditByFiscalMonth(?Fiscal $fiscal = null): array
    {
        $data = [];
        $fiscal = $fiscal ?? $this->fiscal();
        $query = Payment::where('fiscal_id', $fiscal->id);
        $start = Carbon::create($fiscal->start_date);
        $end = Carbon::create($fiscal->end_date);
        $date = $start;
        while (! ($date->format('Y-m') === $end->format('Y-m'))) {
            $fiscal_month = with(clone $query)->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month);
            $credit = with(clone $fiscal_month)->credit()->sum('amount');
            $debit = with(clone $fiscal_month)->debit()->sum('amount');
            $balance = $credit - $debit;
            $data[$date->format('Y-m')] = [
                'credit' => $credit,
                'debit' => $debit,
                'balance' => $balance,
            ];
            $date = $date->copy()->addMonth();
        }

        return $data;
    }
}
