<?php

namespace bazzly\payoffice;

use COM;
use Illuminate\Support\Facades\Config;
// use Orchestra\Testbench\Foundation\Config;

class CheckServer
{
    /**
     * Your server is down
     */
    const DOWNSTATUS = 'down';

    /**
     *  Your serve is up
     */
    const UPSTATUS = "up";

    // protected array $host;
    protected $port = 80, $timeout = 10;

    public function __construct()
    {
        // $this->host;
        $this->setPaymentUrlHosts();
        // $this->timeout;
    }

    /**
     * Get Base Url from Paystack config file
     */
    public function setPaymentUrlHosts()
    {

        // dd($this->ping("api.paystack.co", $this->port, $this->timeout));
        // Check all host in array and return the ping status 
        // for the each payment system
        $paymentUrls = [
            "paystack" => Config::get('payoffice.paystack.pingUrl'),
            "flutterwave" => Config::get('payoffice.flutterwave.pingUrl'),
            "interswitch" => Config::get('payoffice.interswitch.pingUrl'),
            "remita" => Config::get('payoffice.remita.pingUrl'),
        ];
        foreach ($paymentUrls as $key => $paymentUrl) {
            dd($paymentUrls);
            $urls[] = $this->ping($paymentUrl, $this->port, $this->timeout);
        }
        dd($urls);
    }

    // public function urlLiveCheck($host, $port, $timeout)
    // {


    //     $tB = microtime(true);
    //     $fP = fSockOpen($host, $port, $errno, $errstr, $timeout);
    //     if (!$fP) {
    //         return "down";
    //     }
    //     $tA = microtime(true);
    //     return round((($tA - $tB) * 1000), 0) . " ms";
    // }


    protected function ping($host, $port, $timeout)
    {
        $tB = microtime(true);
        $fP = fSockOpen($host, $port, $errno, $errstr, $timeout);

        if (!$fP) {
            $data = [
                "SERVESTATUS" => self::DOWNSTATUS,
                "HOSTNAME" => $host,
                "SERVERPING" => 0 . " ms"
            ];
            return json_encode($data, true);
        }
        $tA = microtime(true);
        $severStatus = round((($tA - $tB) * 1000), 0) . " ms";
        $data = [
            "SERVERSTATUS" => self::UPSTATUS,
            "HOSTNAME" => $host,
            "SERVERPING" => $severStatus
        ];
        return json_encode($data, true);
    }

    // public static function cheknow()
    // {
    //     $this;
    // }
}
