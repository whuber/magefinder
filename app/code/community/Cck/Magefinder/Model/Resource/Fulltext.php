<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Model_Resource_Fulltext extends Mage_CatalogSearch_Model_Resource_Fulltext
{
    /**
     * Prepare results for query
     *
     * @param Mage_CatalogSearch_Model_Fulltext $object
     * @param string                            $queryText
     * @param Mage_CatalogSearch_Model_Query    $query
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function prepareResult($object, $queryText, $query)
    {
        if (!Mage::getStoreConfigFlag('magefinder/general/active')) {
            return parent::prepareResult($object, $queryText, $query);
        }
        $adapter = $this->_getWriteAdapter();
        if (!$query->getIsProcessed()) {

            $result = $this->_getSearchAdapter()->query($queryText, (int)$query->getStoreId());
            $data = array();
            foreach ($result as $item) {
                $item['query_id'] = (int)$query->getId();
                $data[] = $item;
            }
            if($data) {
                $adapter->insertOnDuplicate(
                    $this->getTable('catalogsearch/result'),
                    $data
                );
            }

            $query->setIsProcessed(1);
        }

        return $this;
    }

    /**
     * Retrieve connection for query data
     *
     * @return Cck_Magefinder_Model_Resource_Cloudsearch
     */
    protected function _getSearchAdapter()
    {
        return Mage::getResourceSingleton('magefinder/magefinder');
    }
}
