<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Model_Resource_Fulltext_Engine 
{
    /**
     * Add entity data to cloud search system
     *
     * @param int    $entityId
     * @param int    $storeId
     * @param array  $index
     * @param string $entity 'product'
     * @return Cck_Magefinder_Model_Resource_Fulltext_Engine
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product') 
    {
        $data    = array();
        $storeId = (int)$storeId;
        foreach ($entityIndexes as $entityId => $index) {
            $index['product_id']    = (int)$entityId;
            $data[] = $index;
        }

        if ($data) {
            $this->_getDocAdapter()->import($data, $storeId);
        }

        return $this;
    }

    /**
     * Define if current search engine supports advanced index
     *
     * @return bool
     */
    public function allowAdvancedIndex()
    {
        return false;
    }

    /**
     * Remove entity data from cloud search system
     *
     * @param int $storeId
     * @param int $entityId
     * @param string $entity 'product'
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Engine
     */
    public function cleanIndex($storeId = null, $entityId = null, $entity = 'product')
    {
        if(is_null($entityId)) {
            $this->_getDocAdapter()->truncate($storeId);
        } else {
            $this->_getDocAdapter()->delete($storeId, $entityId);
        }
    }

    /**
     * Prepare index array as a string
     *
     * @param array $index
     * @return string
     */
    public function prepareEntityIndex($index)
    {
        return Mage::helper('magefinder')->prepareIndexdata($index);
    }

    /**
     * Retrieve allowed visibility values for current engine
     *
     * @return array
     */
    public function getAllowedVisibility()
    {
        return Mage::getSingleton('catalog/product_visibility')->getVisibleInSearchIds();
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLeyeredNavigationAllowed()
    {
        return true;
    }

    /**
     * Define if engine is avaliable
     *
     * @return bool
     */
    public function test()
    {
        return Mage::getStoreConfigFlag('magefinder/general/active');
    }

    /**
     * Retrieve connection for index data
     *
     * @return Cck_Magefinder_Model_Resource_Cloudsearch
     */
    protected function _getDocAdapter()
    {
        return Mage::getResourceSingleton('magefinder/magefinder');
    }

    /**
     * Debugging class for unexpected method calls for catalogsearch engine
     * 
     * @param type $method
     * @param type $args
     * @return type
     */
    public function __call($method, $args)
    {
        Mage::log($method);
        Mage::log($args);
        return Mage::getResourceSingleton('catalogsearch/fulltext_engine')->$method($args);
    }
}
