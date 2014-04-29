<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Helper_Url extends Mage_Core_Helper_Abstract
{
    const SEARCH_QUERY      = '/search/';
    const SEARCH_SUGGEST    = '/search/autosuggest/';

    /**
     * Receive search url by type
     * @param string $type
     * @return string
     */
    public function getSearchUrl($type = self::SEARCH_QUERY)
    {
        return "http://" . Mage::getStoreConfig('magefinder/advanced/search_endpoint') 
            . self::SEARCH_QUERY;
    }

    /**
     * Receive document url
     * @return string
     */
    public function getDocumentUrl()
    {
        return "http://" . Mage::getStoreConfig('magefinder/advanced/doc_endpoint') 
            . "/document/";
    }
}
