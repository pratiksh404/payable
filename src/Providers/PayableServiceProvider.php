<?php

namespace Pratiksh\Payable\Providers;

use Illuminate\Support\ServiceProvider;
use Pratiksh\Payable\Console\Commands\MakePaymentGatewayCommand;
use Pratiksh\Payable\Services\Payable;

class PayableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
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
        // Register Package Commands
        $this->registerCommands();
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/payable.php', 'payable');

        // Register the main class to use with the facade
        $this->app->singleton('payable', function () {
            return new Payable;
        });
    }

    /**
     * Publish Resources.
     */
    private function publishResources()
    {
        // Publish Config File
        $this->publishes([
            __DIR__.'/../../config/payable.php' => config_path('payable.php'),
        ], 'payable-config');
        // Publish Migration Files
        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'payable-migrations');
    }

    /**
     * Register Package Command.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            MakePaymentGatewayCommand::class,
        ]);
    }
}
