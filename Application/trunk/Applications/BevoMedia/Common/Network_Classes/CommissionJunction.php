<?php

require_once('Networks.Abstract.php');

/**
 * This class use simpleHTMLDom.
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'simplehtmldom' . DIRECTORY_SEPARATOR . 'SimpleHTMLDom.php');

/**
 * Azoogle.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class CommissionJunction Extends NetworksAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
	/**
	 * @access protected
	 * @var string $apiUrlConversions
	 */
	protected $apiUrlConversions = '';
	
	/**
	 * @access protected
	 * @var string $offersUrl
	 */
	protected $offersUrl = '';
	
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersAffiliateId
	 */
	protected $offersAffiliateId = '';
	
	/**
	 * @access protected
	 * @var string $offersApiKey
	 */
	protected $offersApiKey = '';
	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public function login()
	{ 
		return true;
	}
		
	/**
	 * Retrieve stats for this user and populate database.
	 *
	 * @param String $Date
	 * @return Boolean	False if error occurs.
	 */
	public function getStats($Date = '')
	{
		if (date('m/d/Y', strtotime($Date))==date('m/d/Y'))
		{
			$Date = strtotime("-1 day", strtotime($Date));
			$Date = date('m/d/Y', $Date);
		}
		
        $ini = ini_set("soap.wsdl_cache_enabled","0");
    	$Output = new StatEnvelope($Date);
    	
	    try {

	    	$client = new SoapClient("https://pubcommission.api.cj.com/wsdl/version2/publisherCommissionServiceV2.wsdl", array('trace'=> true));
	      
	        $Results = $client->findPublisherCommissions(array(
	        									"developerKey" => $this->publisherId,
	        									"date" => date('m/d/Y', strtotime($Date)),
	                                            "dateType" => 'event',
	                                            "advertiserIds" => '',
	                                            "websiteIds" => '',
	                                            "actionStatus" => 'all',
	                                            "actionTypes" => 'lead,bonus,click,impression,sale,advanced sale,advanced lead,performance incentive',
	                                            "adIds" => '',
	                                            "countries" => '',
	                                     		"correctionStatus" => 'all',
	                                            "sortBy" => '',
	                                            "sortOrder" => 'asc',));
	       	        
	        
	        $Items = $Results->out->publisherCommissions;
	        if (isset($Results->out->publisherCommissions->PublisherCommissionV2) && is_array($Results->out->publisherCommissions->PublisherCommissionV2)) {
	        	$Items = $Results->out->publisherCommissions->PublisherCommissionV2;
	        }
	        
	        foreach ($Items as $Commission)
	        {
	        	$SubID = $Commission->sId;
	        	$OfferID = $Commission->advertiserId;
        		$Amount = $Commission->commissionAmount;
        		
				$TempStat = new Stat(1, 1, $Amount, $SubID, $OfferID);
				if($TempStat->offerId != '')
				{
					$Output->addStatObject($TempStat);
				}	        	
	        }
	        	
	    } catch (Exception $e){
			echo '<pre>';
	        echo "There was an error with your request or the service is unavailable.\n";
	        print_r ($e);
	    }
		
		return $Output;
	}
	
	
	/**
	 * Retrieve offers.
	 *
	 */
	Public Function getOffers()
	{
		return true;
	}
	
	/**
	 * Login required for offers retrieval.
	 *
	 */
	private function offersLogin()
	{
		return true;
	}
	
}