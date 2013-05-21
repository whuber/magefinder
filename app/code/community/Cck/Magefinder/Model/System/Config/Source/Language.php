<?php

/**
 * Used in config for language selection
 *
 */
class Cck_Magefinder_Model_System_Config_Source_Language
{
    protected $_allowedLang = array(
        'en',
    );

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = array();
        foreach($this->_allowedLang as $lang) {
            $data[] = array(
                'value' => $lang,
                'label' => Mage::app()->getLocale()->getTranslation($lang, 'language')
            );
        }
        return $data;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $data = array();
        foreach($this->_allowedLang as $lang) {
            $data[$lang] = Mage::app()->getLocale()->getTranslation($lang, 'language');
        }
        return $data;
    }

}
