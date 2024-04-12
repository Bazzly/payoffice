<?php

namespace bazzly\payoffice\Paystack\Models\Paystack;

use bazzly\payoffice\Paystack\Abstracts\IResponse;
use bazzly\payoffice\Paystack\Concrete\Response;
use bazzly\payoffice\Paystack\ConcreteAbstract\PaymentMethod;

class DefaultPayment extends PaymentMethod {

    public function __construct(string $email, float $amount) {
        parent::__construct($email, $amount);
    }

    public function validate(): IResponse {
        $preValidationResult = $this->preValidate();
        if ($preValidationResult->hasError())
            return $preValidationResult;
        
        return new Response(false, 'OK');
    }

}