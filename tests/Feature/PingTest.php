<?php

namespace bazzly\payoffice\tests;


use Throwable;
use bazzly\payoffice\PingServer;
use Orchestra\Testbench\TestCase;

use Psy\Exception\ErrorException;
use Illuminate\Support\Facades\Config;
use function PHPUnit\Framework\assertTrue;

class PingTest extends TestCase
{

    /** @test */
    public function checkPaymentApiUrl(){

        $data = new PingServer('paystack','api.paystack.co',100);
        $server = $data->getUrlServerDetails();
      

        // $fintechCompanies = config::get('payoffice');
        // dd($fintechCompanies);
        
        // foreach($fintechCompanies as $key => $fintechCompany){
        //     $data = new PingServer($fintechCompany[$key]['name'],$fintechCompanies[$key]['APIURL'],100);
        //     $server = $data->getUrlServerDetails();
        //     $companyName = $server['name'];
        //     $APIURL =$server['APIUrl'];
        //     $serverUpStatus = $server['serverStatus'];
        //     $pingStatus = $server['serverPing'];
        //     $preferePing = $server['userPing'];
        // }
        // $data = new PingServer('paystack','api.paystack.co',100);
        // $server = $data->getUrlServerDetails();
        // $name = $server['name'];
        // $APIURL =$server['APIUrl'];
        // $serverUpStatus = $server['serverStatus'];
        // $pingStatus = $server['serverPing'];
        // $preferePing = $server['userPing'];

        // if($serverUpStatus == 'up' && $pingStatus >= $preferePing){
        //     $result = $server ;
        // }else{
        //     $result =   'server is down or did not meet your prefered ping status';
        // }
     dd($server);
    }
}
