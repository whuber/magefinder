<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Block_Adminhtml_Config_Status
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_addRowButtonHtml = array();

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $html = '';
        $html .= $this->_getStatusButtonHtml();
        $html .= '<span id="mf-status" style="margin-left:20px;"></span>';
        $html .= $this->_getUpdateJs();
        return $html;
    }

    protected function _getStatusButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('save')
                    ->setLabel($this->__('Request status'));
        
        if(Mage::getStoreConfigFlag('magefinder/general/access_key')
           && Mage::getStoreConfigFlag('magefinder/general/access_secret')) {
            $button->setOnClick("getStatus()");
        }
        else {
            $button->setDisabled(1);
        }
        return $button->toHtml();
    }
    
    protected function _getUpdateJs()
    {
        $html = "<script type=\"text/javascript\">//<![CDATA[
        function getStatus() {
            new Ajax.Request(
                '" . $this->getUrl('magefinder/adminhtml_ajax/status') . "',
                {
                    onSuccess: function(transport) {
                        $('mf-status').innerHTML = transport.responseJSON.message;
                        if(transport.responseJSON.active == '1') {
                            $('mf-status').setStyle({color: '#659601'});
                        }
                        else {
                            $('mf-status').setStyle({color: '#e41101'});
                        }
                    }
                }
            );
        }
        //]]></script>";
        
        return $html;
    }
}
