<?php

//File:  app/code/community/Smsapi/Sms/Model/ApiClient.php

/**
 * SMS API client class
 * 
 * @category   Smsapi
 * @package    SMS
 * @copyright  Copyright (c) 2012 Smsapi (http://smsapi.pl/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Marek Jasiukiewicz <dev@jasiukiewicz.pl>
* ...
*/

class Smsapi_Sms_Model_ApiClient {
    
    
    protected $_client = array();
    protected $_sms = array();
    protected $_hSopa;
     

    
    
    public function __construct() {
        
         $this->getClientData();
         
    }


    
    
    /**
     * Get user authorization data from config and save it to $_client
     * 
     * @param type $ssl
     * @return \Smsapi_Sms_Model_ApiClient
     */
    public function getClientData() {
        
        $config =   Mage::getModel('sms/config');
        
        $this->_client = array(
			'username'	=> $config->getApiLogin(),
			'password'	=> md5($config->getApiPassword())
		);
        
        return $this->_client;
        
    }
    
    
    /**
     * Run SoapClient connection
     * 
     * @param type $ssl
     * @return \Smsapi_Sms_Model_ApiClient
     */
    public function connect($ssl = true) {          
        
        $this->_hSopa = new SoapClient(
            ( $ssl == true ? 'https' : 'http' ) .'://www.smsapi.pl/soap/v2/webservice?wsdl',
            array(
                'features'		=> SOAP_SINGLE_ELEMENT_ARRAYS,
                'cache_wsdl'	=> WSDL_CACHE_NONE,
                'trace'         => TRUE
            )
        );
                
        return $this;
        
                
    }
    
    
    
    /**
     * Adding to SMS additional data
     * for SMSAPI Soap Client
     * 
     * 
     * @param type $sms_data
     * @return \Smsapi_Sms_Model_ApiClient
     */
    

    
    public function msgContent($sms_data) {
        
        $this->_sms = $sms_data;
        $this->_sms['idx'] = uniqid();
        $this->_sms['date_send'] = 0;
        $this->_sms['details'] = 1;
        
        return $this;    
        
    }


    
    /**
     * Sending SMS to client.
     * 
     * @return type
     */
    public function send() {
        
        $params = array(
            'client' => $this->_client,
            'sms'    => $this->_sms
        );      

        return $this->_hSopa->send_sms($params);
        
    }
    
    
    
    /**
     * Getting Senders list from SMSAPI site
     * 
     * @return type
     */
    public function getSenders() {
        
        return $this->_hSopa->get_senders($this->_client);
        
        
    }
    
    
    
     /**
     * Getting points amount from SMSAPI account
     * 
     * @return type
     */
    public function getPoints() {   

        return $this->_hSopa->get_points($this->_client);
        
    }
        
    
        
}