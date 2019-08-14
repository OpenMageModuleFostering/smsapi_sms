<?php

use SMSApi\Api\SmsFactory;

require_once dirname(__DIR__) . '/vendor/smsapi-client/vendor/autoload.php';
require_once dirname(__DIR__) . '/smsapi-classes/AbstractsSmsapi.php';

class Sms extends AbstractsSmsapi
{
    private $actionSend;

    /**
     * @param SmsFactory $smsFactory
     */

    public function __construct(SmsFactory $smsFactory)
    {
        $this->actionSend = $smsFactory->actionSend();
    }

    /**
     * @param $phone
     * @param $sender
     * @param $message
     * @param array $options
     * @return \SMSApi\Api\Response\StatusResponse
     */

    public function send($phone, $sender, $message, $options = array())
    {
        $this->actionSend->setTo($phone);
        $this->actionSend->setText($message);

        if ($sender !== 'DEFAULT') {
            $this->actionSend->setSender(urlencode($sender));
        }
        if (in_array('single', $options)) {
            $this->actionSend->setSingle(true);
        }

        return $this->actionSend->execute();
    }
}