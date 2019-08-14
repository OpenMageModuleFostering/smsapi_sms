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

require_once dirname(dirname(__DIR__)) .'/smsapi-classes/SenderFactory.php';
require_once dirname(dirname(__DIR__)) .'/smsapi-classes/Sender.php';


class Smsapi_Sms_Model_Adminhtml_Balancelow {
    
    
    /**
     * Getting senders from SMSAPI and 
     * generate attributes for configuration panel
     * 
     * 
     * @return array
     */
    public function toOptionArray() {

        $observer = new Smsapi_Sms_Model_Observer();
        $observer->checkPointsLimit();
    }
    
    
    
}