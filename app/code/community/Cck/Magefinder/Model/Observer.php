<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Model_Observer
{

    public function triggerReindex($observer)
    {
        if(Mage::getStoreConfigFlag('magefinder/general/active')) {
            Mage::getResourceModel('magefinder/magefinder')->index();
        }
        
        return $this;
    }

}
