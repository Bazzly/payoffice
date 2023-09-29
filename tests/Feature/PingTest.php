<?php

namespace bazzly\payoffice\tests;


use bazzly\payoffice\PingServer;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Config;

use function PHPUnit\Framework\assertTrue;

class PaymentTest extends TestCase
{
    /** @test */
    public function my_test()
    {
        // Default data companyName,apiURL and prefered ping
        // default preset ping is 10ms user can decide to increase it when they are experiencing large payment
        $data = new PingServer('paystack','api.paystack.co');
        $companyName = $data->getUrlServerDetails()['companyName'];
        $serverUpStatus = $data->getUrlServerDetails()['serverStatus'];
        $pingStatus = $data->getUrlServerDetails()['serverPing'];
        $preferePing = $data->getUrlServerDetails()['userPing'];
        $userPrefencePing =  $preferePing == null ? 10 : $preferePing;
        // dd($data->getUrlServerDetails(), $userPrefencePing);
        if($serverUpStatus == 'up' && $pingStatus >= $userPrefencePing ){
            assertTrue($serverUpStatus == 'up' && $pingStatus >= 10);
        }else{
            // default payment option will be use
        }
     

    }
}
