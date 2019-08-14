<?php
//File:  app/code/community/Smsapi/Sms/Model/Adminhtml/Attributes.php

/**
 * SMS API Configuration attributes class
 *
 * @category   Smsapi
 * @package    SMS
 * @copyright  Copyright (c) 2012 Smsapi (http://smsapi.pl/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Marek Jasiukiewicz <dev@jasiukiewicz.pl>
 * ...
 */

require_once dirname(dirname(__DIR__)) . '/smsapi-classes/SenderFactory.php';
require_once dirname(dirname(__DIR__)) . '/smsapi-classes/Sender.php';


class Smsapi_Sms_Model_Adminhtml_Attributes
{


    /**
     * Getting senders from SMSAPI and
     * generate attributes for configuration panel
     *
     *
     * @return array
     */
    public function toOptionArray()
    {

        /**
         * @var Smsapi_Sms_Model_Config $config
         */
        $config = Mage::getModel('sms/config');

        try {
            $senderFactory = new SenderFactory($config->getApiLogin(), $config->getApiPassword(), $config->getSmsapiVersion());
            $sender = new Sender($senderFactory->getActionFactory(), null, null, '--DEFAULT--');
            return $sender->getSenders();
        } catch (\SMSApi\Exception\SmsapiException $e) {
            return [Mage::helper('sms')->__('Authorization fail')];
        }
    }
}