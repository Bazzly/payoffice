<?php


return [
    /**
     * Paystack requires the following information for every
     * payment processing
     */
    [
        'name' => "paystack",
        'APIURL' =>  "api.paystack.co",
    'paystack'=>[
    /**
     * Public Key From Paystack Dashboard
     *
     */
    'publicKey' => getenv('PAYSTACK_PUBLIC_KEY'),

    /**
     * Secret Key From Paystack Dashboard
     *
     */
    'secretKey' => getenv('PAYSTACK_SECRET_KEY'),

    /**
     * Paystack Payment URL
     *
     */
    'paymentUrl' => getenv('PAYSTACK_PAYMENT_URL'),

    /**
     * Optional email address of the merchant
     *
     */
    'merchantEmail' => getenv('PAYSTACK_MERCHANT_EMAIL'),
        ]
    ],
    /**
     * Flutterwave requires the following information for every
     * payment processing
     */

    [
        'name' => "flutterwave",
        'APIURL' => "api.flutterwave.com",
    'flutterwave'=>[
        /**
        * * Public Key: Your Rave publicKey. Sign up on https://dashboard.flutterwave.com/ to get one from your settings page
        *
        */
        'publicKey' => env('FLW_PUBLIC_KEY'),
        /**
         * * Secret Key: Your Rave secretKey. Sign up on https://dashboard.flutterwave.com/ to get one from your settings page
         * *
         * */
        'secretKey' => env('FLW_SECRET_KEY'),
        /**
         * * Prefix: Secret hash for webhook
         * *
         * */
        'secretHash' => env('FLW_SECRET_HASH', ''),
            
    ]


    ],
    /**
     * Interswitch requires the following information for every
     * payment processing
     */

    [
        'name' => "interswitch",
        'APIURL' => "apps.qa.interswitchng.com",
            /**
      * Integration method. Could be WEBPAY, PAYDIRECT or COLLEGEPAY. Default is WEBPAY
      */
      'interswitch'=>[
        'gatewayType' => env('INTERSWITCH_GATEWAY_TYPE', 'WEBPAY'),

        /**
         * Currency, Naira is default
        */
        'currency' => env('INTERSWITCH_CURRENCY', 566),

        /**
        * Site redirection url as defined by the user
        */
        'siteRedirectURL' => env('INTERSWITCH_SITE_REDIRECT_URL'),

        /**
        * Site redirection path that works internally. Do not change
        */
        'fixedRedirectURL' => 'interswitch-redirect',

        /**
        * current environment (TEST or LIVE)
        */
        'env' => env('INTERSWITCH_ENV', 'TEST'),

        /**
        * Split payment or not
        */
        'split' => env('INTERSWITCH_SPLIT', false),

        /**
        * Name of Institution(CollegePay split payment only)
        */
        'college' => env('INTERSWITCH_COLLEGE', 'Unnamed'),

        /**
        * send mail to user on successfull completion of transaction
        */
        'send_mail' => env('INTERSWITCH_SEND_MAIL', false),
      ],
    'live' => [
        'macKey' => env('INTERSWITCH_MAC_KEY'),
        'initializationURL' => 'https://sandbox.interswitchng.com/webpay/pay',
        'transactionStatusURL' => 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json',
        'productID' => env('INTERSWITCH_PRODUCT_ID'),
        'payItemID' => env('INTERSWITCH_PAY_ITEM_ID')
        ]
    ],

    /**
     * Remita requires the following information for every
     * payment processing
     */

    [
        'name' => "remita",
        'APIURL' => "remitademo.net",
        'interswitch'=>[
            'MERCHANTID' => env('MERCHANTID',"2547916"),
            "SERVICETYPEID" => env('SERVICETYPEID',"4430731"),
            "FUNDINGACCOUNT" => env('FUNDINGACCOUNT',"6973738333"),
            "FUNDINGBANKCODE" => env('FUNDINGBANKCODE',"011"),
            "APIKEY" => env('APIKEY',"1946"),
            "MANDATETPYE" => env('MANDATETPYE',"DD"),
            "CHECKSTATUSURL" => "http://www.remitademo.net/remita/ecomm",
            "MANDATESURL" => "http://www.remitademo.net/remita/ecomm/mandate",
            "GATEWAYURL" => "http://www.remitademo.net/remita/ecomm/mandate/setup.reg",
            "STOPMANDATEURL" => "http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/stop",
            "DIRECTBILLINGURL" => "http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/send",
            "CANCELDIRECTBILLINGURL" => "http://www.remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/stop",
        ]
 
    ],

];
