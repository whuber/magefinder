<?php

class Cck_Magefinder_Model_Resource_Cloudsearch
{

    public function import($data) 
    {
//		Mage::log("Import " . json_encode($data));
//		Mage::log($data);
		$client = $this->_getDocClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'action' => 'update',
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $client->setRawData(json_encode($data), "application/json");
            $response = $client->request("POST");
//            Mage::log($response);
        } catch (Exception $e) {
            Mage::logException($e);
        }
	}
    
    public function truncate($storeId)
    {
		$client = $this->_getDocClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'store' => $storeId,
            'action' => 'truncate',
            'version' => Mage::helper('magefinder')->getVersion(),
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $response = $client->request();
            Mage::log($response);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }
    
    public function delete($storeId, $productIds)
    {
		$client = $this->_getDocClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'store' => $storeId,
            'action' => 'delete',
            'version' => Mage::helper('magefinder')->getVersion(),
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        $data = array();
        foreach($productIds as $id) {
            $data[] = Mage::helper('magefinder')->getCfId($id, $storeId);
        }
        try {
            $client->setRawData(json_encode($data), "application/json");
            $response = $client->request("POST");
//            Mage::log($response);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
    
    public function query($queryText, $storeId)
    {
//        Mage::log(__METHOD__ . " ($storeId)");
//        Mage::log($queryText);
		$client = $this->_getSearchClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'store' => $storeId,
            'q' => $queryText,
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $response = $client->request();
//            Mage::log($client->getLastRequest());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        
        $data = array();
        if($response->getStatus() != 200) {
            return $data; 
        }
        $resultBody = json_decode($response->getBody());
//        Mage::log($resultBody);
        if($resultBody->found > 0) {
            foreach($resultBody->hits as $hit) {
                $data[] = (array)$hit;
            }
        }
//        Mage::log($data);
        return $data;
    }
    
    protected function _getDocClient()
    {
        $url = "http://" . Mage::getStoreConfig('magefinder/advanced/doc_endpoint') 
                . "/doc.php";
		return new Zend_Http_Client($url);
    }
    
    protected function _getSearchClient()
    {
        $url = "http://" . Mage::getStoreConfig('magefinder/advanced/search_endpoint') 
                . "/search.php";
		return new Zend_Http_Client($url);
    }

}
