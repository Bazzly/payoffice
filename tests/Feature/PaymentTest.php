<?php

namespace bazzly\payoffice\tests;

use Orchestra\Testbench\TestCase;
use bazzly\payoffice\Paystack;
use bazzly\payoffice\CheckServer;

class PaymentTest extends TestCase
{
    /** @test */
    public function my_test()
    {
        $data = new CheckServer;
        // dd($data = $data->setPaymentUrlHosts());
    }
}
