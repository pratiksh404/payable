<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Database Configurations
    |--------------------------------------------------------------------------
    |
    */
    'table_prefix' => 'payable_',

    /*
    |--------------------------------------------------------------------------
    | Fiscal Configurations
    |--------------------------------------------------------------------------
    |
    */
    'fiscal_auto_update' => true,
    'start_date' => '7-16',
    'leap_year' => \Pratiksh\Payable\Services\IsLeapYear::class,
    'current_year' => \Pratiksh\Payable\Services\CurrentYear::class,

    /*
    |--------------------------------------------------------------------------
    | Receipt No Structure
    |--------------------------------------------------------------------------
    */
    'receipt_no' => \Pratiksh\Payable\Contracts\ReceiptNoInterface::class,

    /*
    |--------------------------------------------------------------------------
    | Default User Table
    |--------------------------------------------------------------------------
    |
    */
    'user_model' => App\Models\User::class,
    'user_table_primary_key' => 'id',
];
