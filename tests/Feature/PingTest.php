<?php

namespace bazzly\payoffice\tests;

include_once realpath('.' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . '/../config/payoffice.php');
use Throwable;
use bazzly\payoffice\PingServer;
use bazzly\payoffice\Paystack\Paystack;
use Orchestra\Testbench\TestCase;
use Psy\Exception\ErrorException;
use Illuminate\Support\Facades\Config;
use function PHPUnit\Framework\assertTrue;
// use bazzly\payoffice\Paystack\Transfer;
class PingTest extends TestCase
{

    /** @test */
    public function checkPaymentApiUrl(){

        $data = new PingServer('paystack','api.paystack.co',100);
        $server = $data->getUrlServerDetails();

        // how to use on your project
        //     $fintechCompanies =  Config('payoffice');
    
        //     $serverDetails =[];
        //     foreach($fintechCompanies as $key => $fintechCompany){
        //         $name = $fintechCompanies[intval($key)]['name'];
        //         $url = $fintechCompanies[intval($key)]['APIURL'];
        //         $data = new PingServer($name,$url,$userPing);
        //         $server = $data->getUrlServerDetails();
        //         $companyName = $server['name'];
        //         $APIURL =$server['apiurl'];
        //         $serverUpStatus = $server['serverStatus'];
        //         $pingStatus = $server['serverPing'];
        //         $preferePing = $server['userPing'];
        //         $serverDetails[] = [$companyName,$APIURL,$serverUpStatus,$pingStatus,$preferePing];
        //     }
        //     dd($serverDetails);
    
        // }

        // $paymentProvider = $payment->usePaymentProvider('paystack');
        // $fintechCompanies = config('payoffice');
    
        // $serverDetails =[];
        // foreach($fintechCompanies as $key => $fintechCompany){
        //     $name = $fintechCompanies[intval($key)]['name'];
        //     $url = $fintechCompanies[intval($key)]['APIURL'];
        //     $data = new PingServer($name,$url,$userPing);
        //     $server = $data->getUrlServerDetails();
        //     $companyName = $server['name'];
        //     $APIURL =$server['APIUrl'];
        //     $serverUpStatus = $server['serverStatus'];
        //     $pingStatus = $server['serverPing'];
        //     $preferePing = $server['userPing'];
        //     $serverDetails[] = [$companyName,$APIURL,$serverUpStatus,$pingStatus,$preferePing];
        // }
        // dd($serverDetails);
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

    
}


    /** @test */
    public function testGetbalance(){
        $data = new Paystack();
        $data->getBalance();
    }
}