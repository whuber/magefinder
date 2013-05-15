<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_attr_mapping = array(
        'api_key'   => 'api',
        'store_id'  => 'store_id',
        'name'      => 'name',
        'product_id'=> 'product_id',
        'description' => 'description',
        'short_description' => 'short_description',
    );

    /**
     * Join index array to string by separator
     * Support 2 level array gluing
     * @param array $index
     * @param string $separator
     * @return string
     */
    public function prepareIndexdata($index, $separator = ' ')
    {
        $_index = array();
        foreach ($index as $key => $value) {
            if($keyPos = array_search($key, $this->_attr_mapping)) {
                if (!is_array($value)) {
                    $_index[$keyPos] = $value;
                }
                else {
                    $value = array_unique($value);
                    $_index[$keyPos] = implode($separator, $value);
                }
            }
        }
        return $_index;
    }

}