<?php

namespace bazzly\payoffice;


use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->publishes([
            __DIR__.'/../config/payoffice.php' =>  config_path('payoffice.php'),
         ], 'config');

    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/payoffice.php', 'payoffice.php'
        );

    }
}
