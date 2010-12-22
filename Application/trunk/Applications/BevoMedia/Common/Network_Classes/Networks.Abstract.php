<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Response_Objects' . DIRECTORY_SEPARATOR . 'StatEnvelope.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Response_Objects' . DIRECTORY_SEPARATOR . 'OfferEnvelope.php');

ini_set('max_execution_time',0);
ini_set('display_errors', 'On');
ini_set('memory_limit', '1024M');

error_reporting(E_ALL);
if(!defined('ABSMODE'))
{
    $ConfigFile = parse_ini_file('config.ini', true);
    $Mode = $ConfigFile['Application']['Mode'];
    define('ABSMODE', $Mode);
    define('ABSDBHOST', $ConfigFile['Database/'.$Mode]['Host']);
    define('ABSDBUSER', $ConfigFile['Database/'.$Mode]['User']);
    define('ABSDBPASS', $ConfigFile['Database/'.$Mode]['Pass']);
    define('ABSDBNAME', $ConfigFile['Database/'.$Mode]['Name']);
}
/**
 * Network.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
abstract class NetworksAbstract {
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
	Abstract Public Function login();
	Abstract Public Function getStats($Date = '');
	Abstract Public Function getOffers();
	
	public function temp_dir() {
		if(empty($this->jobId))
		  $this->setJobId("no_job_id_"+rand(10000));
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
	
  /**
   * Perform a curl request using the provided URL $strInURL and options $arrInCurlOpts
   *
   * @param String $strInURL
   * @param Array $arrInCurlOpts
   * @return Mixed
   */
  Protected Function curlIt($strInURL, $arrInCurlOpts = array())
  {
    $ch = curl_init($strInURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
    if (!empty($arrInCurlOpts)) {
      foreach ($arrInCurlOpts as $strThisOpt => $strThisVal) {
        curl_setopt($ch, constant($strThisOpt), $strThisVal);
      }
    }
    $result = curl_exec($ch);
    return $result;
  }
	
}//Abstract Class NetworksAbstract