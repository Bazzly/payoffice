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
        $data = new PingServer('paystack','api.paystack.co',180);
        $server = $data->getUrlServerDetails();
        // dd($server);
        $companyName = $server['companyName'];
        $APIURL =$server['APIUrl'];
        $serverUpStatus = $server['serverStatus'];
        $pingStatus = $server['serverPing'];
        $preferePing = $server['userPing'];
        assertTrue($serverUpStatus == 'up' && $pingStatus >= $preferePing);
        if($serverUpStatus == 'up' && $pingStatus >= $preferePing){
            dd($companyName,$APIURL,$serverUpStatus,$pingStatus);
        }else{
           dd('server is down or did not meet your prefered ping status');
        }
     

    }
}
