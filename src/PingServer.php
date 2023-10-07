<?php

namespace bazzly\payoffice;


use Throwable;
use GuzzleHttp\Client;
use Psy\Exception\ErrorException;
use Illuminate\Support\Facades\Config;
use GO\Scheduler;
use Illuminate\Database\Eloquent\Model;
use bazzly\payoffice\Models\PingsMonitoring;

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

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (!(error_reporting() & $errno)) {
                return false;
            }
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
        try {
            $tB = microtime(true);
            $fP = fSockOpen($host, $port, $errno, $errstr, $timeout);
        if ($fP) {
            $tA = microtime(true);
        $severStatus = round((($tA - $tB) * 1000), 0) ;
        $data = [
            "SERVERSTATUS" => self::UPSTATUS,
            "HOSTNAME" => $host,
            "SERVERPING" => $severStatus
        ];
        return $data;
           
        }
   
    }
    catch (Throwable $e) {
        $data = [
            "SERVERSTATUS" => self::DOWNSTATUS,
            "HOSTNAME" => $host,
            "SERVERPING" => 0 
        ];
        return $data;
        fclose($fP);

    }

      
    }

    /**
     *  Check all host and return the ping status 
     *  For the each payment system to know if server is down or up
     */
    public function getUrlServerDetails()
    {

        $ping = $this->ping($this->paymentApiUrl, $this->port, $this->timeout);
        try {
            $data = [
                'name'=>$this->name,
                'apiurl'=>$ping['HOSTNAME'],
                'serverStatus'=>$ping['SERVERSTATUS'],
                'serverPing' =>$ping['SERVERPING'],
                'userPing' =>  $this->preferSeverPing == null ? 10 : $this->preferSeverPing,   
        ];
    
        }
        catch (Throwable $e) {
            $data =  $e->getMessage() . PHP_EOL;
        }
        PingsMonitoring::query()->create($data);
        return   $data;
    }


    // public function storePingData($name){
    //     $data = $this->getUrlServerDetails();
    //     return PingsMonitoring::query()->create($data);
    // }

    // protected function setScheduler($runTask = null)
    // {
    //     // Create a new scheduler
    //     $scheduler = new Scheduler();
    //     // Schedule jobs
    //     $scheduler->call([
    //         PingsMonitoring::query()->create($this->getUrlServerDetails()),
    //     ])->hourly();
    //     // $scheduler->call($this->storePingData())->$runTask == null ? 'hourly()' : $runTask;
    //     // Run the scheduler
    //     $scheduler->run();
    // }

}
