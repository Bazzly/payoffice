<?php

namespace bazzly\payoffice\Providers;

use payoffice;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $config = realpath(__DIR__.'resources/config/payoffice.php');

        $this->publishes([
            $config => config_path('payoffice.php')
        ]);
    }

    public function register(): void
    {
        // $this->mergeConfigFrom(
        //     __DIR__.'/../config/payoffice.php', 'payoffice'
        // );
        // $this->publishes([
        //     __DIR__.'../config/payoffice.php' =>  config_path('payoffice.php'),
        //  ], 'config');
    }
}
