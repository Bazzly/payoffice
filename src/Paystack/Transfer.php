<?php

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace bazzly\payoffice\Paystack;

use \GuzzleHttp\Client;
use bazzly\payoffice\TransRef;
// use bazzly\payoffice\Paystack\Exceptions\IsNullException;
// use bazzly\payoffice\Paystack\Exceptions\PaymentVerificationFailedException;
use Illuminate\Support\Facades\Config;
use bazzly\payoffice\Paystack\Exceptions\IsNullException;
use bazzly\payoffice\Paystack\Exceptions\PaymentVerificationFailedException;

class Transfer
{

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_REVERSED = 'reversed';
    const STATUS_FAILED = 'failed';
 
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_REJECTED = 'rejected';
  
    const STATUS = 'status';
    const CUSTOMER_ID = 'customer';
    const START_DATE = 'from';
    const END_DATE = 'to';
    const AMOUNT = 'amount';
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
    protected $accName, $accNumber , $bankName;

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




    // ============ BUSINESS CHECKS ==================
    private function checkBussinessStatus(){

    }

    //==============Banks=======================

    public function retriveBankDetails(string $bankName){
        $this->url = '/bank';
        unset($this->banks);
        $this->banks = $this->setHttpResponse($this->url, 'GET', [])->getData();

        foreach ($this->banks  as $key => $data) {
            if($data['name'] == $bankName) {
                $bank = $this->banks[$key];
            }
        }
       
       return $bank;
    }



    public function createRecipient(){
        $data =[
            "type"=>"nuban", 
            "name"=>$this->accName, 
            "account_number"=>$this->accNumber, 
            "bank_code"=>$this->retriveBankDetails($this->bankName)['code'], 
            "currency"=>$this->setCurrency()
        ];
        $this->url ='/transferrecipient';
       $this->response = $this->setHttpResponse($this->url, 'POST', $data)->getResponse();
       
       $result = $this->response['message'];

       switch ($result) {
           case self::RECIPIENT:
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
            case self::STATUS_RECEIVED:
                $validate = 3;
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
        $otp = request()->otp;
        $data=[
         "transfer_code"=>$referencecode, 
         "otp"=>$otp
        ];
        $this->response = $this->setHttpResponse($finalizeTransferUrl , 'POST', $data);
        dd($this->response);
    }

    public function sendMonyToAccDetails(int $amount, string $accName, string $accNumber, 
    string $bankName){
        // set user info to transfer money to
        $this->accName = $accName ; 
        $this->accNumber = $accNumber;
        $this->bankName = $bankName;
        $transferUrl = '/transfer';
        // check if beneficiary (recipient) is created
        if($this->createRecipient()){
            $referencecode = $this->response['data']['recipient_code'];
            $data=[
                "source"=>"balance", 
                "reference"=>TransRef::getHashedToken(), 
                "reason"=>"testing transfer", 
                "amount"=>$amount * 100, 
                "recipient"=>$referencecode
            ];
            // dd('here',$referencecode,$transferUrl,$data);
            $message = $this->checkTransferRequirement($transferUrl , 'POST', $data);

            if($message === 1){
                            // redirect to page that set otp data
            return $this->redirectTogetOtp($referencecode);
            }
            if($message === 2){
                // Get finalized transfer data
                return $data = $this->response['data'];
            }
            if($message === 3){
                // redirect to page that set otp data
               return $message .'_'. 3;
            }
          
    
        }
        // catch error 
    }




    /**

     * Initiate a payment request to Paystack
     * Included the option to pass the payload to this method for situations
     * when the payload is built on the fly (not passed to the controller from a view)
     * @return Paystack
     */

    public function makeTransferRequest($data = null)
    {
        if ($data == null) {

            $quantity = intval(request()->quantity ?? 1);

            $data = array_filter([
                "amount" => intval(request()->amount) * $quantity,
                "reference" => request()->reference,
                "email" => request()->email,
                "plan" => request()->plan,
                "first_name" => request()->first_name,
                "last_name" => request()->last_name,
                "callback_url" => request()->callback_url,
                "currency" => ($this->setCurrency() != ""  ? $this->setCurrency() : "NGN"),

                /*
                    Paystack allows for transactions to be split into a subaccount -
                    The following lines trap the subaccount ID - as well as the ammount to charge the subaccount (if overriden in the form)
                    both values need to be entered within hidden input fields
                */
                "subaccount" => request()->subaccount,
                "transaction_charge" => request()->transaction_charge,

                /**
                 * Paystack allows for transaction to be split into multi accounts(subaccounts)
                 * The following lines trap the split ID handling the split
                 * More details here: https://paystack.com/docs/payments/multi-split-payments/#using-transaction-splits-with-payments
                 */
                "split_code" => request()->split_code,

                /**
                 * Paystack allows transaction to be split into multi account(subaccounts) on the fly without predefined split
                 * form need an input field: <input type="hidden" name="split" value="{{ json_encode($split) }}" >
                 * array must be set up as:
                 *  $split = [
                 *    "type" => "percentage",
                 *     "currency" => "KES",
                 *     "subaccounts" => [
                 *       { "subaccount" => "ACCT_li4p6kte2dolodo", "share" => 10 },
                 *       { "subaccount" => "ACCT_li4p6kte2dolodo", "share" => 30 },
                 *     ],
                 *     "bearer_type" => "all",
                 *     "main_account_share" => 70,
                 * ]
                 * More details here: https://paystack.com/docs/payments/multi-split-payments/#dynamic-splits
                 */
                "split" => request()->split,
                /*
                * to allow use of metadata on Paystack dashboard and a means to return additional data back to redirect url
                * form need an input field: <input type="hidden" name="metadata" value="{{ json_encode($array) }}" >
                * array must be set up as:
                * $array = [ 'custom_fields' => [
                *                   ['display_name' => "Cart Id", "variable_name" => "cart_id", "value" => "2"],
                *                   ['display_name' => "Sex", "variable_name" => "sex", "value" => "female"],
                *                   .
                *                   .
                *                   .
                *                  ]
                *          ]
                */
                'metadata' => request()->metadata
            ]);
        }

        $this->setHttpResponse('/transaction/initialize', 'POST', $data);

        return $this;
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
     * Get the authorization url from the callback response
     * @return Paystack
     */
    public function getAuthorizationUrl($data = null)
    {
        $this->makeTransferRequest($data);

        $this->baseUrl = $this->getResponse()['data']['authorization_url'];

        return $this;
    }

    /**
     * Get the authorization callback response
     * In situations where Laravel serves as an backend for a detached UI, the api cannot redirect
     * and might need to take different actions based on the success or not of the transaction
     * @return array
     */
    public function getAuthorizationResponse($data)
    {
        $this->makeTransferRequest($data);

        $this->baseUrl = $this->getResponse()['data']['authorization_url'];

        return $this->getResponse();
    }

    /**
     * Hit Paystack Gateway to Verify that the transaction is valid
     */
    private function verifyTransactionAtGateway($transaction_id = null)
    {
        $transactionRef = $transaction_id ?? request()->query('trxref');

        $relativeUrl = "/transaction/verify/{$transactionRef}";

        $this->response = $this->client->get($this->baseUrl . $relativeUrl, []);
    }

    /**
     * True or false condition whether the transaction is verified
     * @return boolean
     */
    public function isTransactionVerificationValid($transaction_id = null)
    {
        $this->verifyTransactionAtGateway($transaction_id);

        $result = $this->getResponse()['message'];

        switch ($result) {
            case self::VS:
                $validate = true;
                break;
            case self::ITF:
                $validate = false;
                break;
            default:
                $validate = false;
                break;
        }

        return $validate;
    }

    /**
     * Get Payment details if the transaction was verified successfully
     * @return json
     * @throws PaymentVerificationFailedException
     */
    public function getPaymentData()
    {
        if ($this->isTransactionVerificationValid()) {
            return $this->getResponse();
        } else {
            throw new PaymentVerificationFailedException("Invalid Transaction Reference");
        }
    }



    /**
     * Get Access code from transaction callback respose
     * @return string
     */
    public function getAccessCode()
    {
        return $this->getResponse()['data']['access_code'];
    }

    /**
     * Generate a Unique Transaction Reference
     * @return string
     */
    public function genTranxRef()
    {
        return TransRef::getHashedToken();
    }

    /**
     * Get all the customers that have made transactions on your platform
     * @return array
     */
    public function getAllCustomers()
    {
        $this->setRequestOptions();

        return $this->setHttpResponse("/customer", 'GET', [])->getData();
    }

    /**
     * Get all the plans that you have on Paystack
     * @return array
     */
    public function getAllPlans()
    {
        $this->setRequestOptions();

        return $this->setHttpResponse("/plan", 'GET', [])->getData();
    }

    /**
     * Get all the transactions that have happened overtime
     * @return array
     */
    public function getAllTransactions()
    {
        $this->setRequestOptions();

        return $this->setHttpResponse("/transaction", 'GET', [])->getData();
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

    /**
     * Create a plan
     */
    public function createPlan()
    {
        $data = [
            "name" => request()->name,
            "description" => request()->desc,
            "amount" => intval(request()->amount),
            "interval" => request()->interval,
            "send_invoices" => request()->send_invoices,
            "send_sms" => request()->send_sms,
            "currency" => request()->currency,
        ];

        $this->setRequestOptions();

        return $this->setHttpResponse("/plan", 'POST', $data)->getResponse();
    }

    /**
     * Fetch any plan based on its plan id or code
     * @param $plan_code
     * @return array
     */
    public function fetchPlan($plan_code)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/plan/' . $plan_code, 'GET', [])->getResponse();
    }

    /**
     * Update any plan's details based on its id or code
     * @param $plan_code
     * @return array
     */
    public function updatePlan($plan_code)
    {
        $data = [
            "name" => request()->name,
            "description" => request()->desc,
            "amount" => intval(request()->amount),
            "interval" => request()->interval,
            "send_invoices" => request()->send_invoices,
            "send_sms" => request()->send_sms,
            "currency" => request()->currency,
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/plan/' . $plan_code, 'PUT', $data)->getResponse();
    }

    /**
     * Create a customer
     */
    public function createCustomer()
    {
        $data = [
            "email" => request()->email,
            "first_name" => request()->fname,
            "last_name" => request()->lname,
            "phone" => request()->phone,
            "metadata" => request()->additional_info /* key => value pairs array */

        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/customer', 'POST', $data)->getResponse();
    }

    /**
     * Fetch a customer based on id or code
     * @param $customer_id
     * @return array
     */
    public function fetchCustomer($customer_id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/customer' . $customer_id, 'GET', [])->getResponse();
    }

    /**
     * Update a customer's details based on their id or code
     * @param $customer_id
     * @return array
     */
    public function updateCustomer($customer_id)
    {
        $data = [
            "email" => request()->email,
            "first_name" => request()->fname,
            "last_name" => request()->lname,
            "phone" => request()->phone,
            "metadata" => request()->additional_info /* key => value pairs array */

        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/customer/' . $customer_id, 'PUT', $data)->getResponse();
    }

    /**
     * Export transactions in .CSV
     * @return array
     */
    public function exportTransactions()
    {
        $data = [
            "from" => request()->from,
            "to" => request()->to,
            'settled' => request()->settled
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/transaction/export', 'GET', $data)->getResponse();
    }

    /**
     * Create a subscription to a plan from a customer.
     */
    public function createSubscription()
    {
        $data = [
            "customer" => request()->customer, //Customer email or code
            "plan" => request()->plan,
            "authorization" => request()->authorization_code
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/subscription', 'POST', $data)->getResponse();
    }

    /**
     * Get all the subscriptions made on Paystack.
     *
     * @return array
     */
    public function getAllSubscriptions()
    {
        $this->setRequestOptions();

        return $this->setHttpResponse("/subscription", 'GET', [])->getData();
    }

    /**
     * Get customer subscriptions
     *
     * @param integer $customer_id
     * @return array
     */
    public function getCustomerSubscriptions($customer_id)
    {
        $this->setRequestOptions();

        return $this->setHttpResponse('/subscription?customer=' . $customer_id, 'GET', [])->getData();
    }

    /**
     * Get plan subscriptions
     *
     * @param  integer $plan_id
     * @return array
     */
    public function getPlanSubscriptions($plan_id)
    {
        $this->setRequestOptions();

        return $this->setHttpResponse('/subscription?plan=' . $plan_id, 'GET', [])->getData();
    }

    /**
     * Enable a subscription using the subscription code and token
     * @return array
     */
    public function enableSubscription()
    {
        $data = [
            "code" => request()->code,
            "token" => request()->token,
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/subscription/enable', 'POST', $data)->getResponse();
    }

    /**
     * Disable a subscription using the subscription code and token
     * @return array
     */
    public function disableSubscription()
    {
        $data = [
            "code" => request()->code,
            "token" => request()->token,
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/subscription/disable', 'POST', $data)->getResponse();
    }

    /**
     * Fetch details about a certain subscription
     * @param mixed $subscription_id
     * @return array
     */
    public function fetchSubscription($subscription_id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/subscription/' . $subscription_id, 'GET', [])->getResponse();
    }

    /**
     * Create pages you can share with users using the returned slug
     */
    public function createPage()
    {
        $data = [
            "name" => request()->name,
            "description" => request()->description,
            "amount" => request()->amount
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/page', 'POST', $data)->getResponse();
    }

    /**
     * Fetches all the pages the merchant has
     * @return array
     */
    public function getAllPages()
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/page', 'GET', [])->getResponse();
    }

    /**
     * Fetch details about a certain page using its id or slug
     * @param mixed $page_id
     * @return array
     */
    public function fetchPage($page_id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/page/' . $page_id, 'GET', [])->getResponse();
    }

    /**
     * Update the details about a particular page
     * @param $page_id
     * @return array
     */
    public function updatePage($page_id)
    {
        $data = [
            "name" => request()->name,
            "description" => request()->description,
            "amount" => request()->amount
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/page/' . $page_id, 'PUT', $data)->getResponse();
    }

    /**
     * Creates a subaccount to be used for split payments . Required    params are business_name , settlement_bank , account_number ,   percentage_charge
     *
     * @return array
     */

    public function createSubAccount()
    {
        $data = [
            "business_name" => request()->business_name,
            "settlement_bank" => request()->settlement_bank,
            "account_number" => request()->account_number,
            "percentage_charge" => request()->percentage_charge,
            "primary_contact_email" => request()->primary_contact_email,
            "primary_contact_name" => request()->primary_contact_name,
            "primary_contact_phone" => request()->primary_contact_phone,
            "metadata" => request()->metadata,
            'settlement_schedule' => request()->settlement_schedule
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/subaccount', 'POST', array_filter($data))->getResponse();
    }

    /**
     * Fetches details of a subaccount
     * @param subaccount code
     * @return array
     */
    public function fetchSubAccount($subaccount_code)
    {

        $this->setRequestOptions();
        return $this->setHttpResponse("/subaccount/{$subaccount_code}", "GET", [])->getResponse();
    }

    /**
     * Lists all the subaccounts associated with the account
     * @param $per_page - Specifies how many records to retrieve per page , $page - SPecifies exactly what page to retrieve
     * @return array
     */
    public function listSubAccounts($per_page, $page)
    {

        $this->setRequestOptions();
        return $this->setHttpResponse("/subaccount/?perPage=" . (int) $per_page . "&page=" . (int) $page, "GET")->getResponse();
    }


    /**
     * Updates a subaccount to be used for split payments . Required params are business_name , settlement_bank , account_number , percentage_charge
     * @param subaccount code
     * @return array
     */

    public function updateSubAccount($subaccount_code)
    {
        $data = [
            "business_name" => request()->business_name,
            "settlement_bank" => request()->settlement_bank,
            "account_number" => request()->account_number,
            "percentage_charge" => request()->percentage_charge,
            "description" => request()->description,
            "primary_contact_email" => request()->primary_contact_email,
            "primary_contact_name" => request()->primary_contact_name,
            "primary_contact_phone" => request()->primary_contact_phone,
            "metadata" => request()->metadata,
            'settlement_schedule' => request()->settlement_schedule
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse("/subaccount/{$subaccount_code}", "PUT", array_filter($data))->getResponse();
    }

    // public static function getBalance(){
    //     $dataProvider = '';
    //     $result = $dataProvider->getBalance();
    //     unset($dataProvider);

    //     dd($result);
    // }

}
