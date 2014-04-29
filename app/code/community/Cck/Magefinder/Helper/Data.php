<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_attr_mapping = null;
    protected $_excl_mapping = null;
    protected $_version      = null;
    protected $_user_agent   = null;

    /**
     * Join index array to string by separator
     * Supports attribute mapping from config
     * @param array $index
     * @return string
     */
    public function prepareIndexdata($index)
    {
        $_index = array();
        $text_garbage = array();
        foreach ($index as $key => $value) {
            if (in_array($key, $this->_getExcludeMapping())) continue;
            $attr = Mage::getSingleton('eav/config')
                        ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $key);

            if ($keyPos = array_search($key, $this->_getAttributeMapping())) {
                if (!is_array($value)) {
                    switch ($attr->getFrontendInput()) {
                        case 'price':
                            $_index[$keyPos] = $value * 100;
                            break;
                        default:
                            $_index[$keyPos] = $value;
                    }
                } else {
                    $value = array_unique($value);
                    switch ($attr->getFrontendInput()) {
                        case 'text':
                        case 'textarea':
                            $_index[$keyPos] = implode(', ', $value);
                            break;
                        case 'select':
                        case 'multiselect':
                            $_index[$keyPos] = array_values($value);
                            break;
                        case 'price':
                            $_index[$keyPos] = min($value) * 100;
                        default:
                            break;
                    }
                }
            } else {
                if (!is_array($value)) {
                    $text_garbage[] = $value;
                } else {
                    $value = array_unique($value);
                    switch ($attr->getFrontendInput()) {
                        case 'text':
                        case 'textarea':
                            $text_garbage[] = implode(', ', $value);
                            break;
                        case 'select':
                        case 'multiselect':
                            $_index['literal_garbage'] = array_values($value);
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        $_index['text_garbage'] = implode(', ', array_unique($text_garbage));
        return $_index;
    }

    protected function _getAttributeMapping()
    {
        if (is_null($this->_attr_mapping)) {
            $data = unserialize(Mage::getStoreConfig('magefinder/advanced/mapping'));
            $_mapping = array();
            foreach ($data as $row) {
                $_mapping[$row['search_attribute']] = $row['attribute'];
            }
            $this->_attr_mapping = $_mapping;
        }
        return $this->_attr_mapping;
    }

    protected function _getExcludeMapping()
    {
        if (is_null($this->_excl_mapping)) {
            $data = unserialize(Mage::getStoreConfig('magefinder/advanced/mapping'));
            $_mapping = array('status');
            foreach ($data as $row) {
                if (empty($row['search_attribute'])) {
                    $_mapping[] = $row['attribute'];
                }
            }
            $this->_excl_mapping = $_mapping;
        }
        return $this->_excl_mapping;
    }

    public function generateHash($params)
    {
        ksort($params);
        $string = '';
        foreach ($params as $key => $val) {
            $string .= "$key:$val::";
        }
        $string .= 'secret:'.Mage::getStoreConfig('magefinder/general/access_secret');
        return md5($string);
    }

    public function getUserAgent()
    {
        if (is_null($this->_user_agent)) {
            $this->_user_agent = "Magefinder Client v"
                . Mage::getConfig()->getModuleConfig('Cck_Magefinder')->version;
        }
        return $this->_user_agent;
    }

    public function log($message)
    {
        if (Mage::getStoreConfigFlag('magefinder/advanced/logging')) {
            Mage::log($message, null, 'magefinder.log', true);
        }
    }
}
