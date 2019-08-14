<?php
//File:  app/code/community/Smsapi/Sms/Model/Config.php

/**
 * SMS API config class
 *
 *
 * @category   Smsapi
 * @package    SMS
 * @copyright  Copyright (c) 2012 Smsapi (http://smsapi.pl/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Marek Jasiukiewicz <dev@jasiukiewicz.pl>
 * ...
 */
class Smsapi_Sms_Model_Config
{

    const LOW_POINTS_WARNING_MESSAGE = 'Warning: Low points level at your SMSAPI account. Buy credit.';


    /**
     * getting API login from SMSAPI config
     * @return string
     */
    public function getApiLogin()
    {
        return Mage::getStoreConfig('sms/main_conf/apilogin');


    }


    /**
     * getting API password from SMSAPI config
     * @return string
     */
    public function getApiPassword()
    {
        $encrypted_pass = Mage::getStoreConfig('sms/main_conf/apipassword');
        return Mage::helper('core')->decrypt($encrypted_pass);
    }


    /**
     * getting storename from SMSAPI config
     * @return string
     */
    public function getApiStoreName()
    {
        return Mage::getStoreConfig('sms/main_conf/storename');
    }


    /**
     * Checks if allowed only single message
     * @return int
     */
    public function isSingle()
    {
        $confRule = Mage::getStoreConfig('sms/main_conf/allow_long_sms');

        return ($confRule == 1) ? 'single' : 1;

    }

    /**
     * Checks if message is Eco
     * @return int
     */

    public function isEco()
    {

        return 0; //ECO messages turned off

        //$confRule = Mage::getStoreConfig('sms/main_conf/sender');
        //return ($confRule == 'eco_msg') ? 1:0;

    }

    /**
     * getting message sender
     * @return string
     */
    public function getSender()
    {

        return Mage::getStoreConfig('sms/main_conf/sender');

    }

    /**
     * getting SMS templates from SMSAPI config
     * @return string
     */
    public function getMessageTemplate($template)
    {

        $templateContent = Mage::getStoreConfig('sms/templates/status_' . $template);

        if (Mage::getStoreConfig('sms/templates/status_' . $template . '_active') && !empty($templateContent))
            return $templateContent;

    }

    public function pointsAllertLimit()
    {
        return floatval(str_replace(',', '.', Mage::getStoreConfig('sms/main_conf/points_alert_limit')));
    }

    /**
     * checks if module is in test/devel mode
     * @return string
     */
    public function testMode()
    {

        $testmode = Mage::getStoreConfig('sms/admin_mode/test_mode');

        return ($testmode == 1) ? 'test' : 0;

    }

    /**
     * checks if SMSAPI module is enabled
     * @return string
     */
    public function isApiEnabled()
    {

        return Mage::getStoreConfig('sms/main_conf/active');

    }

    /**
     * getting SMSAPI version pl or com
     * @return mixed
     */

    public function getSmsapiVersion()
    {

        return Mage::getStoreConfig('sms/main_conf/plorcom');

    }

}