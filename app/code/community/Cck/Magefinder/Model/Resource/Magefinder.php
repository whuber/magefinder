<?php
/**
 * Magefinder extension
 *
 * @category   Cck
 * @package    Cck_Magefinder
 */
class Cck_Magefinder_Model_Resource_Magefinder
{

    public function import($data, $storeId) 
    {
		$client = $this->_getDocClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'action' => 'update',
            'store' => $storeId,
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
//            Mage::log($data);
            $client->setRawData(json_encode($data), "application/json");
            $response = $client->request("POST");
            $this->_logResponse($response, $client);
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
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $response = $client->request();
            $this->_logResponse($response, $client);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
    
    public function delete($storeId, $productIds)
    {
		$client = $this->_getDocClient();
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'store' => $storeId,
            'action' => 'delete',
        );
        
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        $client->setParameterGet($params);

        try {
            $client->setRawData(json_encode($productIds), "application/json");
            $response = $client->request("POST");
            $this->_logResponse($response, $client);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
    
    public function query($queryText, $storeId)
    {
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
            $this->_logResponse($response, $client);
        } catch (Exception $e) {
            Mage::logException($e);
            return array();
        }
        
        $data = array();
        if($response->getStatus() != 200) {
            return $data; 
        }
        $resultBody = json_decode($response->getBody());
        if($resultBody->found > 0) {
            foreach($resultBody->hits as $hit) {
                $data[] = (array)$hit;
            }
        }
        return $data;
    }
    
    protected function _getDocClient()
    {
        $url = Mage::helper('magefinder/url')->getDocumentUrl();
		return new Zend_Http_Client($url, array(
            'timeout' => 300,
            'useragent' => Mage::helper('magefinder')->getUserAgent()
        ));
    }
    
    protected function _getSearchClient()
    {
        $url = Mage::helper('magefinder/url')->getSearchUrl(
                Cck_Magefinder_Helper_Url::SEARCH_QUERY
        );
		return new Zend_Http_Client($url, array(
            'useragent' => Mage::helper('magefinder')->getUserAgent()
        ));
    }
    
    protected function _logResponse(Zend_Http_Response $response, Zend_Http_Client $client)
    {
        if(!Mage::getStoreConfigFlag('magefinder/advanced/logging')) {
            return;
        }

        $helper = Mage::helper('magefinder');
        $lastRequest = preg_split('|(?:\r?\n){2}|m', $client->getLastRequest());
        $helper->log($lastRequest[0] . "\n");
        $helper->log($response->getHeadersAsString());
        $helper->log(json_decode($response->getBody()));
    }

}
