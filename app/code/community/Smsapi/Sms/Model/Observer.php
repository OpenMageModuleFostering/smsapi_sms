<?php
//File:  app/code/community/Smsapi/Sms/Model/Observer.php

/**
 * @category   Smsapi
 * @package    SMS
 * @copyright  Copyright (c) 2012 Smsapi (http://smsapi.pl/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Marek Jasiukiewicz <dev@jasiukiewicz.pl>
 * ...
 */

require_once dirname(__DIR__) . '/smsapi-classes/UserFactory.php';
require_once dirname(__DIR__) . '/smsapi-classes/User.php';
require_once dirname(__DIR__) . '/smsapi-classes/SmsFactory.php';
require_once dirname(__DIR__) . '/smsapi-classes/Sms.php';

class Smsapi_Sms_Model_Observer
{

    /**
     *
     * @param type $observer
     * @return type
     */

    public function handleStatus($observer)
    {
        /**
         * @var Smsapi_Sms_Model_Config $config
         */
        $config = Mage::getModel('sms/config');
        if ($config->isApiEnabled() == 0)
            return; //do nothing if api is disabled

        $order = $observer->getEvent()->getOrder();
        $stateCode = $order->getStatus();
        $statuses = Mage::getResourceModel('sales/order_status_collection')
            ->toOptionHash();

        $newStatus = $statuses[$stateCode];

        if (Mage::registry($stateCode . '_executed') == true)
            return;

        Mage::register($stateCode . '_executed', true);
        $message = $config->getMessageTemplate($stateCode); //get template for new status (if active and exists)
        if (!$message)  //return if no active message template
            return;


        //getting last tracking number
        $trackings = Mage::getResourceModel('sales/order_shipment_track_collection')->setOrderFilter($order)->getData();

        if (!empty($trackings)) {
            $last = count($trackings) - 1;
            $last_tracking_number = $trackings[$last]['track_number'];
        } else
            $last_tracking_number = 'no_tracking'; //if no tracking number set "no_tracking" message for {TRACKINGNUMBER} template


        $address = $order->getShippingAddress();
        if (!$address)
            $address = $order->getBillingAddress();


        try { //try to send message


            //getting order data to generate message template
            $messageOrderData['{NAME}'] = $address->getData('firstname');
            $messageOrderData['{ORDERNUMBER}'] = $order->getIncrement_id();
            $messageOrderData['{ORDERSTATUS}'] = $newStatus;
            $messageOrderData['{TRACKINGNUMBER}'] = $last_tracking_number;
            $messageOrderData['{STORENAME}'] = $config->getApiStoreName();

            $message = strtr($message, $messageOrderData);


            $smsFactory = new SmsFactory($config->getApiLogin(), $config->getApiPassword(), $config->getSmsapiVersion());
            $sms = new Sms($smsFactory->getActionFactory());

            //prepare sms content
            $msg['recipient'] = $address->getData('telephone');
            $msg['message'] = $message;
            $msg['sender'] = $config->getSender();

            $options = array(
                $config->isSingle()
            );

            try {
                $response = $sms->send($msg['recipient'], $msg['sender'], $msg['message'], $options);
                $newComment = Mage::helper('sms')->__('SMS notification sent (SMS id:') . $response->getList()->response[0]->id . ') ';
                $order->addStatusToHistory($newStatus, $newComment, true);
            } catch (Exception $e) {
                $newComment = Mage::helper('sms')->__('SMS notification sending error: "') . $e->getMessage() . '"';
                $order->addStatusToHistory($newStatus, $newComment, false);
            }

            $this->checkPointsLimit(); //check


        } catch (Exception $e) {
            Mage::log('exception' . $e->getMessage());
        }

    }


    /**
     * Generating alert notification if SMSAPI account balance is low
     *
     * @return none
     */

    function checkPointsLimit()
    {
        /**
         * @var Smsapi_Sms_Model_Config $config
         */

        $config = Mage::getModel('sms/config');
        if ($config->isApiEnabled() == 0) return;  //do nothing if api is disabled


        $limit = $config->pointsAllertLimit();

        if ($limit == 0) return; //If limit allert is turned off


        try {

            $userFactory = new UserFactory($config->getApiLogin(), $config->getApiPassword(), $config->getSmsapiVersion());
            $user = new User($userFactory->getActionFactory());
            $points = $user->getPoints();

            if ($points < $limit) { //alert admin if API account balance is lower than $limit

                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sms')->__($config::LOW_POINTS_WARNING_MESSAGE));

            }
        } catch (Exception $e) {
            Mage::log('SMSAPI: ' . $e->getMessage());
        }

    }

    /**
     * Check SMSAPI authorization data (login/password)
     * and throw error notification to admin panel
     *
     * @return none
     */
    public function checkAuthorizationData()
    {
        /**
         * @var Smsapi_Sms_Model_Config $config
         */
        $config = Mage::getModel('sms/config');
        if ($config->isApiEnabled() == 0) return;  //do nothing if api is disabled

        if ($config->getApiLogin() && $config->getApiPassword()) {

            try {
                $userFactory = new UserFactory($config->getApiLogin(), $config->getApiPassword(), $config->getSmsapiVersion());
                $user = new User($userFactory->getActionFactory());
                $user->getPoints();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sms')->__('SMSAPI: Wrong Password and/or Username'));
            }
        }
    }
}