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

        $params = $this->_getParams(array(
            'action' => 'update',
            'store' => $storeId,
        ));
        $client->setParameterGet($params);

        try {
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

        $params = $this->_getParams(array(
            'store' => $storeId,
            'action' => 'truncate',
        ));
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

        $params = $this->_getParams(array(
            'store' => $storeId,
            'action' => 'delete',
        ));
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

        $params = $this->_getParams(array(
            'store' => $storeId,
            'q' => $queryText,
            'spell' => (int)Mage::getStoreConfig('magefinder/spellcheck/active'),
        ));
        
        $client->setParameterGet($params);

        try {
            $response = $client->request();
            $this->_logResponse($response, $client);
        } catch (Exception $e) {
            Mage::logException($e);
            return array();
        }

        $data = array();
        if (!$response->isSuccessful()) {
            return $data;
        }
        $resultBody = json_decode($response->getBody());
//        Mage::log($resultBody);
        if ($resultBody->found > 0) {
            foreach ($resultBody->hits as $hit) {
                $data[] = (array)$hit;
            }
        }
        elseif(isset($resultBody->spellcheck)) {
            $_helper = Mage::helper('catalogsearch');
            $suggestions = array();
            foreach($resultBody->spellcheck as $suggestion) {
                $suggestions[] = sprintf(
                    '<a href="%s" class="spellcheck">%s</a>', 
                    $_helper->getResultUrl($suggestion),
                    $suggestion
                );
            }
            $message = Mage::helper('magefinder')->__("Did you mean: %s", implode(', ', $suggestions));
            $_helper->addNoteMessage($message);
        }
        return $data;
    }

    public function suggest($queryText)
    {
		$client = $this->_getSearchClient(
            Cck_Magefinder_Helper_Url::SEARCH_SUGGEST
        );
        
        $params = array(
            'api' => Mage::getStoreConfig('magefinder/general/access_key'),
            'store' => Mage::app()->getStore()->getId(),
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
    

    public function status()
    {
        $client = $this->_getDocClient();

        $params = $this->_getParams(array(
            'action' => 'status'
        ));
        $client->setParameterGet($params);

        try {
            $response = $client->request();
            $this->_logResponse($response, $client);
        } catch (Exception $e) {
            Mage::logException($e);
            return array();
        }

        if (!$response->isSuccessful()) {
            return array();
        }
        $data = json_decode($response->getBody(), true);
        return $data;
    }

    public function index()
    {
        $client = $this->_getDocClient();

        $params = $this->_getParams(array(
            'action' => 'index'
        ));
        $client->setParameterGet($params);

        try {
            $response = $client->request();
            $this->_logResponse($response, $client);
        } catch (Exception $e) {
            Mage::logException($e);
            return array();
        }
        return $this;
    }

    protected function _getDocClient()
    {
        $url = Mage::helper('magefinder/url')->getDocumentUrl();
        return new Zend_Http_Client($url, array(
            'timeout' => 300,
            'useragent' => Mage::helper('magefinder')->getUserAgent()
        ));
    }

    protected function _getSearchClient($type = Cck_Magefinder_Helper_Url::SEARCH_QUERY)
    {
        $url = Mage::helper('magefinder/url')->getSearchUrl($type);
        Mage::log("Url: $url");
        return new Zend_Http_Client($url, array(
            'useragent' => Mage::helper('magefinder')->getUserAgent()
        ));
    }

    protected function _getParams(array $params = array())
    {
        $params['api'] = Mage::getStoreConfig('magefinder/general/access_key');
        $params['hash'] = Mage::helper('magefinder')->generateHash($params);
        return $params;
    }

    protected function _logResponse(Zend_Http_Response $response, Zend_Http_Client $client)
    {
        if (!Mage::getStoreConfigFlag('magefinder/advanced/logging')) {
            return;
        }

        $helper = Mage::helper('magefinder');
        $lastRequest = preg_split('|(?:\r?\n){2}|m', $client->getLastRequest());
        $helper->log($lastRequest[0] . "\n");
        $helper->log($response->getHeadersAsString());
        $helper->log(json_decode($response->getBody()));
    }
}
