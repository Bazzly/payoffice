<?php

namespace bazzly\payoffice;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class PingServer
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
    protected $port = 443, $timeout = 10;
    public $paymentApiUrl;
    public $name;
    public $preferSeverPing;
    
    /**
     * __construct
     *
     * @param  mixed $name name of fintech company
     * @param  mixed $url API url for the fintech company
     * @param  mixed $preferSeverPing users prefered ping before you can accept to use the payment
     * @return void
     */
    public function __construct(string $name, string $url, int $preferSeverPing = null )
    {
        $this->paymentApiUrl = $url;
        $this->name = $name;
        $this->preferSeverPing = $preferSeverPing;
    }


    protected function ping($host, $port, $timeout)
    {
        $tB = microtime(true);
        $fP = fSockOpen($host, $port, $errno, $errstr, $timeout);

        if (!$fP) {
            $data = [
                "SERVESTATUS" => self::DOWNSTATUS,
                "HOSTNAME" => $host,
                "SERVERPING" => 0 
            ];
            return $data;
        }
        $tA = microtime(true);
        $severStatus = round((($tA - $tB) * 1000), 0) ;
        $data = [
            "SERVERSTATUS" => self::UPSTATUS,
            "HOSTNAME" => $host,
            "SERVERPING" => $severStatus
        ];
        return $data;
    }

    /**
     *  Check all host and return the ping status 
     *  For the each payment system to know if server is down or up
     */
    public function getUrlServerDetails()
    {

        $ping = $this->ping($this->paymentApiUrl, $this->port, $this->timeout);
        
        $data = [
                'companyName'=>$this->name,
                'companyUrl'=>$ping['HOSTNAME'],
                'serverStatus'=>$ping['SERVERSTATUS'],
                'serverPing' =>$ping['SERVERPING'],
                'userPing' =>$this->preferSeverPing,   
        ];
        
        return   $data;
    }


}
