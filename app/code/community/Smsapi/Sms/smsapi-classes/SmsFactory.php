<?php

use SMSApi\Exception\SmsapiException;

require_once dirname(__DIR__) . '/vendor/smsapi-client/vendor/autoload.php';

class SmsFactory extends AbstractsSmsapi
{
    public function __construct($username, $password, $url)
    {
        parent::__construct($username, $password, $url);
    }

    public function getActionFactory()
    {
        $smsapi = new \SMSApi\Api\SmsFactory();
        $smsapi->setProxy($this->getSmsapiProxy());
        $smsapi->setClient($this->getSmsapiClient());

        return $smsapi;
    }
}