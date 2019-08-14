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


class Smsapi_Sms_Model_Adminhtml_Attributes {
    
    
    /**
     * Getting senders from SMSAPI and 
     * generate attributes for configuration panel
     * 
     * 
     * @return array
     */
    public function toOptionArray() {

        $attributes = array(
            //array('label'=>Mage::helper('sms')->__('Use ECO SMS (cheaper version without sender)'),'value'=>'eco_msg') //eco messages disabled
        );

        
        $api = Mage::getModel('sms/apiClient');
        $config =   Mage::getModel('sms/config');
        
        try {
            
            $response = $api->connect()->getSenders();
            if ($response->result!=0)
                return $attributes;

            if (!empty($response->senders)) foreach ($response->senders as $sender) {
                array_push($attributes, array('label'=>$sender,'value'=>$sender));
            }

        }
        catch ( Exception $e) {
             Mage::log('SMSAPI (getSenders()): '.$e->getMessage());
        }
        
        if (empty($attributes))
        	array_push($attributes, array('label'=>'SMSAPI.pl','value'=>'SMSAPI.pl'));
        
        return $attributes;
        
    }
    
    
    
}