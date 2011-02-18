<?php 
// Include path for Zend
$IncludePaths = array(
    realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR .'../../../../Externals'),
    '.',
);
set_include_path(implode(PATH_SEPARATOR, $IncludePaths));

// Include dependencies
include_once('Zend/Service/Abstract.php');
include_once('Zend/Http/Client.php');
include_once('Zend/Dom/Query.php');


abstract class ScrapeAbstract extends Zend_Service_Abstract
{
    protected $_login_endPoint = '';
    protected $_stats_endPoint = '';
    protected $_offers_endPoint = '';
    
    protected abstract function _getLoginPostArray();
    protected abstract function _getStatsPostArray();
    protected abstract function _getOffersPostArray();
    
    protected $_client;
    
    public function __construct()
    {
        $this->_client = new Zend_Http_Client();
    }
    
    protected function login()
    {
        // Get necessary login post variables
        $loginPostArray = $this->_getLoginPostArray();
        
        // Reset the client
        //$this->_client->resetParameters();
        
        // To turn cookie stickiness on, set a Cookie Jar
        $this->_client->setCookieJar();
        
        // Set the location for the login request
        $this->_client->setUri($this->_login_endPoint);
        
        // Set the post variables
        foreach ($loginPostArray as $loginPostKey=>$loginPostValue) {
            $this->_client->setParameterPost($loginPostKey, $loginPostValue);
        }
        
        // Submit the reqeust
        $response = $this->_client->request(Zend_Http_Client::POST);
    }
    
    public function getStatsBody()
    {
        // Login
        $this->login();
        
        // Get the necessary stat post variables
        $statsPostArray = $this->_getStatsPostArray();
        
        // Reset the client
        //$this->_client->resetParameters();
        
        // Set the location for the login request
        $this->_client->setUri($this->_offers_endPoint);
        
        // Set the post variables
        foreach ($statsPostArray as $statsPostKey=>$statsPostValue) {
            $this->_client->setParameterPost($statsPostKey, $statsPostValue);
        }
        $result = $this->_client->request(Zend_Http_Client::POST);
    
        //check to make sure that the result isnt a HTTP error
        if($result->isError()){
            throw new Exception('Client returned error: ' . $result->getMessage());
        }
        
        return $result->getBody();
        
    }
    
    public function getOffersBody()
    {
        // Login
        $this->login();
        
        // Get the necessary stat post variables
        $offerPostArray = $this->_getOffersPostArray();
        
        // Reset the client
        //$this->_client->resetParameters();
        
        // Set the location for the login request
        $this->_client->setUri($this->_offers_endPoint);
        
        // Set the post variables
        foreach ($offerPostArray as $offerPostKey=>$offerPostValue) {
            $this->_client->setParameterPost($offerPostKey, $offerPostValue);
        }
        $result = $this->_client->request(Zend_Http_Client::POST);

        //check to make sure that the result isnt a HTTP error
        if($result->isError()){
            throw new Exception('Client returned error: ' . $result->getMessage());
        }
        
        try{
            //setup the query object with the result body (HTML page)
            $query = new Zend_Dom_Query($result->getBody());
            $domCollection = $query->query('[@id="campaigns_holder"]');
        }catch(Zend_Dom_Exception $e){
            throw new Exception('Error Loading Document: ' . $e);
        }
        
        return $result->getBody();
        return $domCollection->current()->nodeValue;
    }
    
    public function getOfferDetailBody($offerId)
    {
        // Login
        $this->login();
        
        // Get the necessary stat post variables
        $offerPostArray = $this->_getOffersPostArray();
        
        // Reset the client
        //$this->_client->resetParameters();
        
        // Set the location for the login request
        $this->_client->setUri($this->_offerDetails_endPoint . $offerId);
        
        $this->_client->setParameterGet('id', $offerId);
        
        $result = $this->_client->request(Zend_Http_Client::GET);

        //check to make sure that the result isnt a HTTP error
        if($result->isError()){
            throw new Exception('Client returned error: ' . $result->getMessage());
        }
        
        return $result->getBody();
    }
    
    protected function getNodeValue($search, $body) 
    {
        try{
            //setup the query object with the result body (HTML page)
            $query = new Zend_Dom_Query($body);
            $domCollection = $query->query($search);
        }catch(Zend_Dom_Exception $e){
            throw new Exception('Error Loading Document: ' . $e);
        }
        
        if ($domCollection->current()) {
            /* @var $domNode DOMElement */
            return $domCollection->current()->nodeValue;
        }else{
            return '';
        }
    }
    
}