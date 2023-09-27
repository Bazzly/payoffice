<?php


return [
    /**
     * Paystack requires the following information for every
     * payment processing
     */

    'paystack' => [
        'publicKey' => getenv('PAYSTACK_PUBLIC_KEY'),
        'secretKey' =>  getenv('PAYSTACK_SECRET_KEY'),
        'paymentUrl' => 'api.paystack.co/transaction/initialize',
        'marchantEmil' => getenv('MERCHANT_EMAIL'),
        'pingUrl' => "api.paystack.co"
    ],

    /**
     * Flutterwave requires the following information for every
     * payment processing
     */

    'flutterwave' =>
    [
        'publicKey' => getenv('FLUTTERWAVE_PUBLIC_KEY'),
        'secretKey' => getenv('FLUTTERWAVE_SECRET_KEY'),
        'paymentUrl' => getenv('FLUTTERWAVE_PAYMENT_URL'),
        'marchantEmil' => getenv('FLUTTERWAVE_EMAIL'),
        'pingUrl' => "api.flutterwave.com"
    ],

    /**
     * Interswitch requires the following information for every
     * payment processing
     */

    'interswitch' =>
    [
        'publicKey' => getenv('INTERSWITCH_PUBLIC_KEY'),
        'secretKey' => getenv('INTERSWITCH_SECRET_KEY'),
        'paymentUrl' => getenv('INTERSWITCH_PAYMENT_URL'),
        'marchantEmil' => getenv('INTERSWITCH_EMAIL'),
        'pingUrl' => "api.flutterwave.com"
    ],

    /**
     * Remita requires the following information for every
     * payment processing
     */

    'remita'
    => [
        'publicKey' => getenv('REMITA_PUBLIC_KEY'),
        'secretKey' => getenv('REMITA_SECRET_KEY'),
        'paymentUrl' => getenv('REMITA_PAYMENT_URL'),
        'marchantEmil' => getenv('REMITA_EMAIL'),
        'pingUrl' => "api.flutterwave.com"
    ],

];
