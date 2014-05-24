<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */

/**
 * Autocomplete list
 */
class Cck_Magefinder_Block_Autocomplete extends Mage_CatalogSearch_Block_Autocomplete
{
    protected $_magefinderData = null;

    protected function _toHtml()
    {
        if(!Mage::getStoreConfigFlag('magefinder/autosuggest/active')) {
            return parent::_toHtml();
        }
        $html = '';

        if (!$this->_beforeToHtml()) {
            return $html;
        }

        $suggestData = $this->getMagefinderData();
        if (!($count = count($suggestData))) {
            return $html;
        }

        $count--;

        $html = '<ul><li style="display:none"></li>';
        foreach ($suggestData as $index => $item) {
            if ($index == 0) {
                $item['row_class'] .= ' first';
            }

            if ($index == $count) {
                $item['row_class'] .= ' last';
            }
            
            $name = Mage::helper('core/string')->truncate($item['name']);
            
            $query = $this->helper('catalogsearch')->getQueryText();
            $name = preg_replace("%($query)%i", "|***$1***|", $name);
            $name = $this->escapeHtml($name);
            $name = strtr ($name , array('|***' => '<b>', '***|' => '</b>'));

            $html .=  '<li title="product:'.$this->escapeHtml($item['product_id'])
                . '" class="'.$item['row_class'].'">' . ($name).'</li>';
        }

        $html.= '</ul>';

        return $html;
    }
        
    public function getMagefinderData()
    {
        if (!$this->_magefinderData) {
            $query = $this->helper('catalogsearch')->getQueryText();
            $collection = Mage::getResourceSingleton('magefinder/magefinder')->suggest($query);
            $counter = 0;
            $data = array();
            foreach ($collection as $item) {
                $item['row_class'] = (++$counter)%2?'odd':'even';
                $data[] = $item;
            }
            $this->_suggestData = $data;
        }
        return $this->_suggestData;
    }
}
