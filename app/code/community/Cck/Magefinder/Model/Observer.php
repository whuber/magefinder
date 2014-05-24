<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */

class Cck_Magefinder_Model_Observer
{
    /**
     * Forward search result to product view
     *
     * @param   Varien_Event_Observer $observer
     * @return  Cck_Magefinder_Model_Observer
     */
    public function redirectProductSearch(Varien_Event_Observer $observer)
    {
        $action = $observer->getControllerAction();
        $query  = $action->getRequest()->getParam('q');
        $id     = preg_match("%^product:([0-9]*)$%", $query, $match)?(int)$match[1]:false;
        if($id) {
            $product = Mage::getModel('catalog/product')->load($id);
            if($product->isInStock()) {
                $forward = new Mage_Core_Controller_Varien_Exception();
                $forward->prepareForward(
                        'view', 
                        'product', 
                        'catalog', 
                        array('id' => $id)
                );
                throw $forward;
            }
        }
        return $this;
    }

    public function __redirectProductSearch(Varien_Event_Observer $observer)
    {
        $action = $observer->getControllerAction();
        $query  = $action->getRequest()->getParam('q');
        $id     = preg_match("%^product:([0-9]*)$%", $query, $match)?(int)$match[1]:false;
        if($id) {
            $product = Mage::getModel('catalog/product')->load($id);
            if($product->isInStock()) {
                $url = Mage::helper('catalog/product')->getProductUrl($product);
                $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $action->getResponse()->setRedirect($url);
            }
        }
        return $this;
    }

}
