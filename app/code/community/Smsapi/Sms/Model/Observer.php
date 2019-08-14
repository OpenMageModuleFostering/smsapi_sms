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
class Smsapi_Sms_Model_Observer {
    
    /**
     * 
     * @param type $observer
     * @return type
     */
    
    public function handleStatus($observer) {

        $config =   Mage::getModel('sms/config');
        if ($config->isApiEnabled()==0)
			return; //do nothing if api is disabled

        $order = $observer->getEvent()->getOrder();
		$newStatus = $order->getStatus();
		if (Mage::registry($newStatus.'_executed') == true)
			return;

		Mage::register($newStatus.'_executed', true);
		$message = $config->getMessageTemplate($newStatus); //get template for new status (if active and exists)
		if (!$message)  //return if no active message template
			return;


		//getting last tracking number
		$trackings = Mage::getResourceModel('sales/order_shipment_track_collection')->setOrderFilter($order)->getData();

		if (!empty($trackings)) {
			$last = count($trackings)-1;
			$last_tracking_number = $trackings[$last]['track_number'];
		}
		else
			$last_tracking_number = 'no_tracking'; //if no tracking number set "no_tracking" message for {TRACKINGNUMBER} template


		$address = $order->getShippingAddress();
		if (!$address)
			$address = $order->getBillingAddress();


		try { //try to send message


			//getting order data to generate message template
			$messageOrderData['{NAME}'] = $address->getData('firstname');
			$messageOrderData['{ORDERNUMBER}'] = $order->getIncrement_id();
			$messageOrderData['{ORDERSTATUS}']  = $newStatus;
			$messageOrderData['{TRACKINGNUMBER}'] = $last_tracking_number;
			$messageOrderData['{STORENAME}'] = $config->getApiStoreName();

			$message = strtr($message,$messageOrderData);



			$api = Mage::getModel('sms/apiClient');
			$api->connect();

			//prepare sms content
			$msg['recipient']       = $address->getData('telephone'); //or getBillingAddress
			$msg['message']         = $message;
			$msg['eco']             = $config->isEco(); //eco version - without sender
			$msg['test']            = $config->testMode();
			$msg['single_message']  = $config->isSingle(); //allow_long_sms
			$msg['sender']          = $config->getSender();//sender


			//sending sms and getting API response

			try {

				$response = $api->msgContent($msg)->send();
				$newComment = Mage::helper('sms')->__('SMS notification sent (SMS id:').$response->response[0]->id.') ' ;
				$order->addStatusToHistory($newStatus,$newComment,true);


			} catch (Exception $e) {
				$newComment = Mage::helper('sms')->__('SMS notification sending error: "').$e->getMessage().'"';
				$order->addStatusToHistory($newStatus,$newComment,false);
			}

			$this->checkPointsLimit(); //check


		} catch ( Exception $e ) {
			Mage::log('exception'.$e->getMessage());
		}
        
    }
    
    
    
    /**
     * Generating alert notification if SMSAPI account balance is low
     * 
     * @return none
     */
    
    function checkPointsLimit() {
        
        $config =   Mage::getModel('sms/config');
        if ($config->isApiEnabled()==0) return;  //do nothing if api is disabled
            
            
        $limit = $config->pointsAllertLimit();
        
        if ($limit==0) return; //If limit allert is turned off
        
        
        try {
            
             $api = Mage::getModel('sms/apiClient');
             $api->connect();
             $res = $api->getPoints();
             
             if ($res->points < $limit) { //alert admin if API account balance is lower than $limit
                 
                 Mage::getSingleton('core/session')->addError(Mage::helper('sms')->__($config::LOW_POINTS_WARNING_MESSAGE));
                 
             }
        }
        catch (Exception $e) {
             Mage::log('SMSAPI: '.$e->getMessage());
        }
        
    }
    
    /**
     * Check SMSAPI authorization data (login/password)
     * and throw error notification to admin panel
     * 
     * @return none
     */
    public function  checkAuthorizationData() {
        $config =   Mage::getModel('sms/config');
        if ($config->isApiEnabled()==0) return;  //do nothing if api is disabled
        
        if ($config->getApiLogin() && $config->getApiPassword()) {
            
            try {
                $api = Mage::getModel('sms/apiClient');
                $api->connect()->getPoints();
            }
            catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('sms')->__('SMSAPI: Wrong Password and/or Username'));
            }
        }
        
        
    }
    
    
    
    
}