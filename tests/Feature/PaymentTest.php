<?php

namespace bazzlycodes\payoffice\tests;

use Orchestra\Testbench\TestCase;
use bazzlycodes\payoffice\Paystack;
use bazzlycodes\payoffice\CheckServer;

class PaymentTest extends TestCase
{
    /** @test */
    public function my_test()
    {
        $data = new CheckServer;
        // dd($data = $data->setPaymentUrlHosts());
    }
}
