<?php

class Cck_Magefinder_Model_Resource_Cloudsearch
{
    protected $_search_endpoint = 'search-magefinder-test-pyokc5khfqsu64m4i7qxba6vz4.eu-west-1.cloudsearch.amazonaws.com';
    protected $_doc_endpoint    = 'doc-magefinder-test-pyokc5khfqsu64m4i7qxba6vz4.eu-west-1.cloudsearch.amazonaws.com';

    public function import($data) 
    {
        $url = "http://" . $this->_doc_endpoint . "/2011-02-01/documents/batch";
//		Mage::log("Import " . json_encode($data));
		$client = new Zend_Http_Client($url);
		$client->setRawData(json_encode($data), "application/json");
		$response = $client->request("POST");
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
        $url = "http://" . $this->_search_endpoint . "/2011-02-01/search";
		$client = new Zend_Http_Client($url);
		$client->setParameterGet('q', $queryText);
		$client->setParameterGet('bq', 'store_id:'.$storeId);
		$client->setParameterGet('return-fields', 'product_id,name,text_relevance');
		$response = $client->request();
        
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

}
