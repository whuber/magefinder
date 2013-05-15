<?php

class Cck_Magefinder_Model_Resource_Fulltext_Engine 
{
    protected $_version = null;

    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product') 
    {
        $data    = array();
        $storeId = (int)$storeId;
        foreach ($entityIndexes as $entityId => $index) {
            $index['api_key']       = 'test123';
            $index['store_id']      = (int)$storeId;
            $index['product_id']    = (int)$entityId;
            $data[] = array(
                'type'  => 'add',
                'id'    => (int)$entityId,
                'lang'  => 'en',
                'version' => $this->_getVersion(),
                'fields'  => $index
            );
        }

        if ($data) {
            $this->_getDocAdapter()->import($data);
        }

        return $this;
	}

    protected function _getVersion()
    {
        if(is_null($this->_version)) {
            $this->_version = time() - strtotime("2013-05-01");
        }
        return $this->_version;
    }
    
	public function allowAdvancedIndex()
    {
		return false;
	}

    public function cleanIndex($storeId = null, $entityId = null, $entity = 'product')
    {
        $this->_getDocAdapter()->delete($storeId, $entityId);
    }

    public function prepareEntityIndex($index, $separator = ' ')
    {
        return Mage::helper('magefinder')->prepareIndexdata($index, $separator);
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
    
    protected function _getDocAdapter()
    {
        return Mage::getResourceSingleton('magefinder/cloudsearch');
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

    public function test()
    {
        return true;
    }

    public function __call($method, $args)
    {
        Mage::log($method);
        Mage::log($args);
        return Mage::getResourceSingleton('catalogsearch/fulltext_engine')->$method($args);
    }
    
};
