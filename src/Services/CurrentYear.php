<?php
namespace Pratiksh\Payable\Services;

use Carbon\Carbon;
use Pratiksh\Payable\Contracts\CurrentYearInterface;

class CurrentYear implements CurrentYearInterface{
    public function __invoke() : int{
        return Carbon::now()->year;
    }
}