<?php

class Smsapi_Sms_Block_Buttons extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){

        $this->setElement($element);
        
		$url = 'https://ssl.smsapi.pl/rejestracja?lang=pl';
        

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('button')
                    ->setLabel(Mage::helper('sms')->__('Open registration form'))
                    ->setOnClick("window.open('$url','window1','width=970, height=705, scrollbars=1, resizable=1'); return false;")
                    ->toHtml();

        return $html;
        
    }
}
