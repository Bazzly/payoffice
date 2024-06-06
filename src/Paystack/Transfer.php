<?php

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bazzly\Payoffice\Paystack;

use \GuzzleHttp\Client;
use Bazzly\Payoffice\TransRef;
// use Bazzly\Payoffice\Paystack\Exceptions\IsNullException;
// use Bazzly\Payoffice\Paystack\Exceptions\PaymentVerificationFailedException;
use Illuminate\Support\Facades\Config;
use Bazzly\Payoffice\Paystack\Exceptions\IsNullException;
use Bazzly\Payoffice\Paystack\Exceptions\PaymentVerificationFailedException;

class Transfer
{

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_REVERSED = 'reversed';
    const STATUS_FAILED = 'failed';
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_REJECTED = 'rejected';
    const RECIPIENT = 'Transfer recipient created successfully';
    const STATUS_OTP = 'Transfer requires OTP to continue';
    const STATUS_QUE = 'Transfer has been queued';
    const STATUS_RECEIVED = 'Transfers retrieved';

   



    /**
     * Issue Secret Key from your Paystack Dashboard
     * @var string
     */
    protected $secretKey;

    /**
     * Business currency type form Paystack Dashboard
     * @var string
     */
    protected $currency;

    /**
     * Instance of Client
     * @var Client
     */
    protected $client;

    /**
     *  Response from requests made to Paystack
     * @var mixed
     */
    protected $response;

    /**
     * Paystack API base Url
     * @var string
     */
    protected $baseUrl;

      /**
     * Paystack Url
     * @var string
     */
    protected $url;

    /**
     * Country bank to retrive
     * @var string
     */
    protected $country;
    protected $accName, $accNumber , $bankName,$bankCode;

    /**
     * Country bank to retrive
     * @var string
     */
    protected $banks = [];


    /**
     * Authorization Url - Paystack payment page
     * @var string
     */
    protected $authorizationUrl;

    public function __construct()
    {
        $this->setKey();
        $this->setBaseUrl();
        $this->setRequestOptions();
        $this->setCurrency();
    }

    /**
     * Get Base Url from Paystack config file
     */
    public function setBaseUrl()
    {
        $this->baseUrl = Config('payoffice.0.paystack.paymentUrl');
    }

        /**
     * Get Base Url from Paystack config file
     */
    public function setCurrency()
    {
        $this->currency = Config('payoffice.0.paystack.currency');
    }

    /**
     * Get secret key from Paystack config file
     */
    public function setKey()
    {
        $this->secretKey = Config('payoffice.0.paystack.secretKey');
    }

    /**
     * Set options for making the Client request
     */
    private function setRequestOptions()
    {
        $authBearer = 'Bearer ' . $this->secretKey;

        $this->client = new Client(
            [
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => $authBearer,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json'
                ]
            ]
        );
    }

        /**
     * Get the whole response from a get operation
     * @return array
     */
    private function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }

    /**
     * Get the data response from a get operation
     * @return array
     */
    private function getData()
    {
        return $this->getResponse()['data'];
    }



    // ============ BUSINESS CHECKS ==================
    private function checkBussinessStatus(){

    }

    //==============Banks=======================

    // public function retriveBankDetails(string $bankName){
    //     dd($bankName); 
    //     $this->url = '/bank';
    //     unset($this->banks);
    //     $this->banks = $this->setHttpResponse($this->url, 'GET', [])->getData();

    //     foreach ($this->banks  as $key => $data) {
    //         if($data['name'] == $bankName) {
    //             $bank = $this->banks[$key];
    //         }
    //     }
       
    //    return $bank;
    // }



    public function createRecipient(){
        $data =[
            "type"=>"nuban", 
            "name"=>$this->accName, 
            "account_number"=>$this->accNumber, 
            "bank_code"=>$this->bankCode, 
            "currency"=>$this->setCurrency()
        ];
        $this->url ='/transferrecipient';
        $this->response = $this->setHttpResponse($this->url, 'POST', $data)->getResponse();
       
        $result = $this->response['message'];
    
       switch ($result) {
           case self::RECIPIENT:
            // dd($result, $this->response['recipient_code'] );
               $validate = true;
               break;
           default:
               $validate = false;
               break;
       }

       return $validate;

    }



    public function checkTransferRequirement($transferUrl,$method, $data){

    $this->response = $this->setHttpResponse($transferUrl, $method, $data)->getResponse();
    $result = $this->response['message'];
       switch ($result) {
           case self::STATUS_OTP:
               $validate = 1;
               break;
            case self::STATUS_QUE:
                $validate = 2;
                break;
           default:
               $validate = false;
               break;
       }

       return $validate;

    }

            /**
     * Fluent method to redirect to Paystack Payment Page
     */
    public function redirectTogetOtp($referencecode)
    { 

        $finalizeTransferUrl = '/transfer/finalize_transfer';
    
        $data=[
         "transfer_code"=>$referencecode, 
         "otp"=>request()->otp,
        ];
        $this->response = $this->setHttpResponse($finalizeTransferUrl , 'POST', $data);

    }


    public function sendMonyToAccDetails(string $accName, string $accNumber, 
    string $bankName,$metadata){
      
        // set user info to transfer money to
        $this->accName = $accName ; 
        $this->accNumber = $accNumber;
        $this->bankName = $bankName;
        $this->bankCode = $metadata['bankCode'];
        $transferUrl = '/transfer';
        // dd($metadata['source']);
        // check if beneficiary (recipient) is created
    
        if($this->createRecipient()){
            $referencecode = $this->response['data']['recipient_code'];
       
            $data=[
                "source"=>$metadata['source'], 
                "amount"=>$metadata['amount'], 
                "reference"=>$metadata['reference'], 
                "recipient"=>$referencecode,
                "reason"=>$metadata['reason'],
            ];
        
   
            $message = $this->checkTransferRequirement($transferUrl , 'POST', $data);
         
            if($message === 1){
                            // redirect to page that set otp data
            $this->redirectTogetOtp($referencecode);
            // return $data = $this->response['data'];
            }
            if($message === 2){
                // Get finalized transfer data
                // You must save reference to your database 
                // with payment details for you to be able 
                // to retrive transaction status
                return $data = $this->response['data'];
            }
    
          
    
        } else {
            throw new PaymentVerificationFailedException("Invalid Transaction");
        }

    }
    
    public function getVerifyTransfer(string $reference){
    $transactionReference = $reference;
    $relativUrl='/transfer/verify/';
    $url=$this->setBaseUrl().$relativUrl.$transactionReference;
    $this->response = $this->setHttpResponse($url , 'GET', [])->getData();
    return $this->response;
}

public function getBalance(string $ledger = null){
    if($ledger != null){
        $relativUrl='/balance'.'/'.$ledger;
        $url=$this->setBaseUrl().$relativUrl;
        $this->response = $this->setHttpResponse($url , 'GET', [])->getData();
        return $this->response; 
    }
    $relativUrl='/balance';
    $url=$this->setBaseUrl().$relativUrl;
    $this->response = $this->setHttpResponse($url , 'GET', [])->getData();
    return $this->response;
}
  
    /**
     * @param string $relativeUrl
     * @param string $method
     * @param array $body
     * @return Paystack
     * @throws IsNullException
     */
    private function setHttpResponse($relativeUrl, $method, $body = [])
    {
        if (is_null($method)) {
            throw new IsNullException("Empty method not allowed");
        }

        $this->response = $this->client->{strtolower($method)}(
            $this->baseUrl . $relativeUrl,
            ["body" => json_encode($body)]
        );

        return $this;
    }

        /**
     * Get a list of all supported banks and their properties
     * @param $country - The country from which to obtain the list of supported banks, $per_page - Specifies how many records to retrieve per page , 
     * $use_cursor - Flag to enable cursor pagination on the endpoint
     * @return array
     */
    public function getBanks(?string $country = null, int $per_page = 50, bool $use_cursor = false)
    {
        if (!$country)
            $country = request()->country ?? 'nigeria';

        $this->setRequestOptions();
        return $this->setHttpResponse("/bank/?country=" . $country . "&use_cursor=" . $use_cursor . "&perPage=" . (int) $per_page, "GET")->getResponse();
    }

    /**
     * Confirm an account belongs to the right customer
     * @param $account_number - Account Number, $bank_code - You can get the list of bank codes by calling the List Banks endpoint
     * @return array
     */
    public function confirmAccount(string $account_number, string $bank_code)
    {

        $this->setRequestOptions();
        return $this->setHttpResponse("/bank/resolve/?account_number=" . $account_number . "&bank_code=" . $bank_code, "GET")->getResponse();
    }

}
