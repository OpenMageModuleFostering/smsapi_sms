<?php

require_once dirname(__DIR__) . '/smsapi-classes/AbstractsSmsapi.php';
require_once dirname(__DIR__) . '/vendor/smsapi-client/vendor/autoload.php';

class SenderFactory extends AbstractsSmsapi
{
    public function __construct($username, $password, $url)
    {
        parent::__construct($username, $password, $url);
    }

    public function getActionFactory()
    {
        $smsApi = new \SMSApi\Api\SenderFactory();
        $smsApi->setProxy($this->getSmsapiProxy());
        $smsApi->setClient($this->getSmsapiClient());

        return $smsApi;
    }
}