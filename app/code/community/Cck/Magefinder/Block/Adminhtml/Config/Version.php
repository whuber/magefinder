<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Block_Adminhtml_Config_Version
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * Read version number from config.xml of Magefinder module
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $html = '<div id="magefinder_version">';
        $html .= Mage::getConfig()->getModuleConfig('Cck_Magefinder')->version;
        $html .= '</div>';
        return $html;
    }
}
