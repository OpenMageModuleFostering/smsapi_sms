<?php

class Smsapi_Sms_IndexController extends Mage_Core_Controller_Front_Action
{
    public function sendsmsAction()
    {
        $params = $this->_request->getParams();
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
            $api = Mage::getModel('sms/apiClient');
            $api->connect();


            //prepare sms content
            $msg['recipient'] = $params['rec']; //or getBillingAddress
            $msg['message'] = $params['msg'];
            $msg['eco'] = $config->isEco(); //eco version - without sender
            $msg['test'] = $config->testMode();
            $msg['single_message'] = $config->isSingle(); //allow_long_sms
            $msg['sender'] = $config->getSender();//sender

            if (strpos($msg['message'], '{TRACKINGNUMBER}') !== false && !isset($msgWithTrackingNumber)) {
                $res['message'] = Mage::helper('sms')->__('You have used {TRACKINGNUMBER} variable but tracking number is not defined. Please delete {TRACKINGNUMBER} variable from the message.');
                $res['class'] = 'error-msg';
                $this->_response->setBody(json_encode($res));
            } else {
                $msg['message'] = strtr($msg['message'], $msgWithTrackingNumber);
                try {
                    $api->msgContent($msg)->send();
                    $res['class'] = 'success-msg';
                    $res['message'] = Mage::helper('sms')->__('SMS notification sent');
                    $this->_response->setBody(json_encode($res));
                } catch (Exception $e) {
                    $res['message'] = Mage::helper('sms')->__('SMS notification error');
                    $res['class'] = 'error-msg';
                    $this->_response->setBody(json_encode($res));
                }
            }
            //sending sms and getting API response

            $observer->checkPointsLimit(); //check


        } catch (Exception $e) {
            Mage::log('exception' . $e->getMessage());
        }


    }
}