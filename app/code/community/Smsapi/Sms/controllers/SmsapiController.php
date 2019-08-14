<?php

use \SMSApi\Exception\SmsapiException;

require_once dirname(__DIR__) . '/smsapi-classes/Sms.php';
require_once dirname(__DIR__) . '/smsapi-classes/Sender.php';
require_once dirname(__DIR__) . '/smsapi-classes/SenderFactory.php';
require_once dirname(__DIR__) . '/smsapi-classes/SmsFactory.php';

class Smsapi_Sms_SmsapiController extends Mage_Adminhtml_Controller_Action
{
    public function sendsmsAction()
    {
        $params = $this->_request->getParams();
        /**
         * @var Smsapi_Sms_Model_Config $config
         */
        $config = Mage::getModel('sms/config');
        $observer = new Smsapi_Sms_Model_Observer();

        $res['status'] = 'success';

        $trackNumbersIter = 0;
        for ($i = 0; $i < count($params); $i++) {
            if (isset($params['trackingnumber' . $i])) {
                $msgWithTrackingNumber['{TRACKINGNUMBER}'] = $params['trackingnumber' . $i];
                $trackNumbersIter++;
            }
        }

        try {
            $smsFactory = new SmsFactory($config->getApiLogin(), $config->getApiPassword(), $config->getSmsapiVersion());
            $sms = new Sms($smsFactory->getActionFactory());

            //prepare sms content
            $msg['recipient'] = $params['rec']; //or getBillingAddress
            $msg['message'] = $params['msg'];
            $msg['sender'] = $config->getSender();//sender

            $options = array(
                $config->isSingle()
            );

            if (strpos($msg['message'], '{TRACKINGNUMBER}') !== false && !isset($msgWithTrackingNumber)) {
                $res['message'] = Mage::helper('sms')->__('You have used {TRACKINGNUMBER} variable but tracking number is not defined. Please delete {TRACKINGNUMBER} variable from the message.');
                $res['class'] = 'error-msg';
                $this->_response->setBody(json_encode($res));
            } else {
                $msg['message'] = isset($msgWithTrackingNumber) ? strtr($msg['message'], $msgWithTrackingNumber) : $msg['message'];
                try {
                    $sms->send($msg['recipient'], $msg['sender'], $msg['message'], $options);
                    $res['class'] = 'success-msg';
                    $res['message'] = Mage::helper('sms')->__('SMS notification sent');
                    $this->_response->setBody(json_encode($res));
                } catch (SmsapiException $e) {
                    $res['message'] = Mage::helper('sms')->__('SMS notification error');
                    $res['message'] = $e->getMessage();
                    $res['class'] = 'error-msg';
                    $this->_response->setBody(json_encode($res));
                }
            }

        } catch (Exception $e) {
            Mage::log('exception' . $e->getMessage());
        }
    }
}