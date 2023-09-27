<?php

namespace bazzlycodes\payoffice\Providers;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        // $this->publishes([
        //     __DIR__.'/../config/payoffice.php' => config_path('payoffice.php'),
        // ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/payoffice.php', 'payoffice'
        );
    }
}
