<?php

namespace Pratiksh\Payable\Providers;

use Pratiksh\Payable\Services\Payable;
use Illuminate\Support\ServiceProvider;
use Pratiksh\Payable\Contracts\IsLeapYearInterface;
use Pratiksh\Payable\Services\CurrentYear;
use Pratiksh\Payable\Services\IsLeapYear;

class PayableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'Payable');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'Payable');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishResources();
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__. '/../../config/payable.php', 'payable');

        // Register the main class to use with the facade
        $this->app->singleton('payable', function () {
            return new Payable;
        });
    }

    /**
     * Publish Resources
     */
    private function publishResources(){
        // Publish Config File
        $this->publishes([
            __DIR__ . '/../../config/payable.php' => config_path('payable.php'),
        ], 'payable-config');
        // Publish Migration Files
        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'payable-migrations');
    }
}
