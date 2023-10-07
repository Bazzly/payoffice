<?php

namespace bazzly\payoffice;


use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishMigrations();
        $this->publishes([
            __DIR__.'/../config/payoffice.php' =>  config_path('payoffice.php'),
         ], 'config');

    }

        /**
     * Publish migration files.
     *
     * @return void
     */
    private function publishMigrations()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'pings_monitoring');
    }

    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->mergeConfigFrom(
            __DIR__.'/../config/payoffice.php', 'payoffice.php'
        );

    }
}
