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
    
    // =====
    

    protected $jobId = false;
	/**
	 * @access protected
	 * @var string debugLogging
	 */
	protected $debugLogging = false;
	/**
	 * @access protected
	 * @var string $apiName
	 */
	protected $apiName = NULL;
	
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = NULL;
	
	/**
	 * @access protected
	 * @var string $publisherId
	 */
	protected $publisherId = NULL;
	
	/**
	 * @access protected
	 * @var string $publisherLogin
	 */
	protected $publisherLogin = NULL;
	
	/**
	 * @access protected
	 * @var string $publisherPassword
	 */
	protected $publisherPassword = NULL;
	
	/**
	 * @access protected
	 * @var string $networkId
	 */
	protected $networkId = 0;
	
	/**
	 * @access protected
	 * @var string $userId
	 */
	protected $userId = 0;
	
	protected $userAffNetworkId = 0;
	
	/**
	 * @access protected
	 * @var string $logFileName
	 */
	protected $logFileName = NULL;
	
	/**
	* @access protected
	* @var string $_db
	*/
	protected $_db = NULL;
	
	/**
	 * Class Prototypes that will be implemented by extending classes
	 */
	public function temp_dir() {
		if(empty($this->jobId))
		  $this->setJobId("no_job_id_"+rand(1,10000));
		$d=PATH."/QueueSandbox/".$this->jobId."_".$this->ApiName();
		if(!file_exists($d))
			mkdir($d, 0777, true);
		return $d;
	}
	
	Protected Function DB()
	{
	    if(!$this->_db)
	    {
	        $this->_db = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
	        mysql_select_db(ABSDBNAME, $this->_db);
	    }
	    return $this->_db;
	}
	
	/**
	 * User ApiName
	 * @see ApiName
	 * @return string
	 */
	Public Function ApiName() {
		if ($this->apiName == NULL) {
			$this->apiName = get_class ( $this );
		}
		return $this->apiName;
	}
	/**
	 * User setApiUrl
	 * @see ApiUrl
	 * @param string $ApiUrl
	 * @return void
	 */
	Public Function setApiUrl($ApiUrl) {
		$this->apiUrl = $ApiUrl;
	
	} //Public Function setApiUrl($ApiUrl)
	
	Public Function setJobId($id)
	{
	    $this->jobId = $id;
	}

	/**
	 * User ApiUrl
	 * @see setApiUrl
	 * @return string
	 */
	Public Function ApiUrl() {
		return $this->apiUrl;
	
	} //Public Function ApiUrl()
	

	/**
	 * User setPublisherId
	 * @see PublisherId
	 * @param string $PublisherId
	 * @return void
	 */
	Public Function setPublisherId($PublisherId) {
		$this->publisherId = $PublisherId;
	
	} //Public Function setPublisherId($PublisherId)
	

	/**
	 * User PublisherId
	 * @see setPublisherId
	 * @return string
	 */
	Public Function PublisherId() {
		return $this->publisherId;
	
	} //Public Function PublisherId()
	

	/**
	 * User setPublisherLogin
	 * @see PublisherLogin
	 * @param string $PublisherLogin
	 * @return void
	 */
	Public Function setPublisherLogin($PublisherLogin) {
		$this->publisherLogin = $PublisherLogin;
	
	} //Public Function setPublisherLogin($PublisherLogin)
	

	/**
	 * User PublisherLogin
	 * @see setPublisherLogin
	 * @return string
	 */
	Public Function PublisherLogin() {
		return $this->publisherLogin;
	
	} //Public Function PublisherLogin()
	

	/**
	 * User setPublisherPassword
	 * @see PublisherPassword
	 * @param string $PublisherPassword
	 * @return void
	 */
	Public Function setPublisherPassword($PublisherPassword) {
		$this->publisherPassword = $PublisherPassword;
	
	} //Public Function setPublisherPassword($PublisherPassword)
	

	/**
	 * User PublisherPassword
	 * @see setPublisherPassword
	 * @return string
	 */
	Public Function PublisherPassword() {
		return $this->publisherPassword;
	
	} //Public Function PublisherPassword()
	

	/**
	 * User setNetworkId
	 * @see NetworkId
	 * @param string $NetworkId
	 * @return void
	 */
	Public Function setNetworkId($NetworkId) {
		$this->networkId = $NetworkId;
	
	} //Public Function setNetworkId($NetworkId)
	

	/**
	 * User NetworkId
	 * @see setNetworkId
	 * @return string
	 */
	Public Function NetworkId() {
		return $this->networkId;
	
	} //Public Function NetworkId()
	

	/**
	 * User setUserId
	 * @see UserId
	 * @param string $UserId
	 * @return void
	 */
	Public Function setUserId($UserId) {
		$this->userId = $UserId;
	
	} //Public Function setUserId($UserId)
	

	/**
	 * User UserId
	 * @see setUserId
	 * @return string
	 */
	Public Function UserId() {
		return $this->userId;
	
	} //Public Function UserId()
	

	/**
	 * User setLogFileName
	 * @see LogFileName
	 * @param string $LogFileName
	 * @return void
	 */
	Public Function setLogFileName($LogFileName = "") {
		$this->LogFileName = $LogFileName;
	} //Public Function setLogFileName($LogFileName)
	

	/**
	 * User LogFileName
	 * @see setLogFileName
	 * @return string
	 */
	Public Function LogFileName() {
		return $this->logFileName;
	} //Public Function LogFileName()

	
	/**
	 * Log this transaction
	 * @param string $Text
	 * @return void
	 */
	Public Function logTransaction($Text, $status = 'message', $output = '') {
		if ($this->debugLogging == true) {
			echo "{$this->ApiName()}\t$Text\r\n";
		}
		if($this->jobId)
		{
		    $db = $this->DB();
		    $name = mysql_real_escape_string($Text, $db);
		    $output = mysql_real_escape_string($output);
		    $sql = "INSERT INTO bevomedia_queue_log (description, queueId, status, output) VALUES ('$name', (SELECT id FROM bevomedia_queue WHERE jobId='{$this->jobId}'), '$status', '$output')";
            mysql_query($sql, $db);
		} else { // Do filesystem logging
    		if ($this->logFileName === NULL)
    			$this->setLogFileName (  $this->temp_dir() . "/" . $this->ApiName() . "_log_" . time () );
    		@mkdir ( dirname ( $this->LogFileName ), 0777, true );
    		
    		$fp = fopen ( $this->LogFileName, 'a' );
    		fwrite ( $fp, date ( 'r' ) . $Text . "\r\n" );
    		fclose ( $fp );
		}
	} //Protected Function logTransaction($Text)
	

	Public Function DebugLogging() {
		$this->debugLogging = true;
	}
    
}