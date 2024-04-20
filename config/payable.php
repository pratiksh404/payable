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
    'table_name' => 'payments',

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
    'user_table' => 'users',
    'user_table_primary_key' => 'id'
];