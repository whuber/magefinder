<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Block_Adminhtml_Config_Header
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Render config info block
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $domain = 'www.magefinder.de';
        $html = $this->__("To activate this extension you need an account from <a href='http://%s' target='_blank'>%s</a>", $domain, $domain);
        return $this->_decorateRowHtml($element, $html);
    }
}
