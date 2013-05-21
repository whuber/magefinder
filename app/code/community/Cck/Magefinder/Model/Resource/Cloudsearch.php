<?php

class Cck_Magefinder_Model_Resource_Cloudsearch
{

    public function import($data) 
    {
//		Mage::log("Import " . json_encode($data));
		$client = $this->_getDocClient();
		$client->setRawData(json_encode($data), "application/json");
//		$response = $client->request("POST");
//		Mage::log("import" . print_r($response, 1));
//        die(__METHOD__);
	}
    
    public function delete($storeId, $productIds = null)
    {
        //TODO - delete whole index or specific products
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
        
        foreach($params as $key => $val) {
            $client->setParameterGet($key, (string)$val);
        }

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
        if($resultBody->hits->found > 0) {
            foreach($resultBody->hits->hit as $hit) {
                $data[] = array(
                    'product_id' => $hit->data->product_id[0],
                    'relevance' => $hit->data->text_relevance[0],
                );
            }
        }
//        Mage::log($data);
        return $data;
    }
    
    protected function _getDocClient()
    {
        $url = "http://" . Mage::getStoreConfig('magefinder/advanced/doc_endpoint') 
                . "/2011-02-01/documents/batch";
		return new Zend_Http_Client($url);
    }
    
    protected function _getSearchClient()
    {
        $url = "http://" . Mage::getStoreConfig('magefinder/advanced/search_endpoint') 
                . "/search.php";
		return new Zend_Http_Client($url);
    }

}
