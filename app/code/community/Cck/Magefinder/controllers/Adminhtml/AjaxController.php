<?php 
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */

class Cck_Magefinder_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action
{
    public function statusAction()
    {
        $magefinder = Mage::getResourceModel('magefinder/magefinder');
        $statusData = $magefinder->status();
        
        if('1' == $statusData['active']) {
            $statusData['message'] = $this->__('Your account is active.');
        }
        else {
            $statusData['message'] = $this->__('Your account is not active.');            
        }
        
        $this->getResponse()
                ->setHeader('Content-type', 'application/json')
                ->setBody(json_encode($statusData));
    }
	
}

