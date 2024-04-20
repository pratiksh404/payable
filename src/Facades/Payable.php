<?php

namespace Pratiksh\Payable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pratiksh\Payable\Skeleton\SkeletonClass
 */
class Payable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payable';
    }
}
