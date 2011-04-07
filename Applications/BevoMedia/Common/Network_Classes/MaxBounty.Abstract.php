<?php

require_once('Networks.Abstract.php');

/**
 * This class uses nusoap.
 */

/**
 * MaxBounty.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
abstract class MaxBountyAbstract Extends NetworksAbstract {
	/**
	 * @access protected
	 * @var string $loginHash
	 */
	protected $loginHash = NULL;
	
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = '';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = '';
	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function login()
	{
		$params = array(
			'user' => $this->publisherLogin,
			'password' => $this->publisherPassword
			);
			
		$this->loginHash = $this->DoSoapRequest('getKey', $params);
		
		if($this->loginHash == '')
		{
			throw new Exception('Login rejected by provider');
			return false;
		}
		else
		{
			$this->logTransaction('Authentication successful.');
			return $this->loginHash;
		}
	}
		
	/**
	 * Retrieve stats for this user.
	 *
	 * @param String $Date
	 * @return Boolean	False if error occurs.
	 */
	public function getStats($Date = '')
	{
		if(!$this->loginHash)
		{
			return false;
		}
		$params = array(
						'keyStr' => $this->loginHash,
						'subId' => ''
					   );
					   
	    if($Date == ''){
			$Date = date('Y-m-d');
		}
		
		$params['startDate'] = $Date;
		$params['endDate'] = $Date;
		
		$Result = $this->DoSoapRequest('getDateRangeSubIDStats', $params);
				
		$Output = new StatEnvelope($Date);
		
		if(!sizeof($Result))
		{
			return $Output;
		}
		foreach ($Result as $Row)
		{
			$SubId = $Row['SUB_ID'];
			//function too slow. blocking queue
			//$SubIdDetail = $this->getDateRangeSubIdDetails($SubId, $Date);
			
			//$OfferID = @$SubIdDetail[0]['OFFER_ID'];
			$Clicks = $Row['CLICKS'];
			$Leads = $Row['LEADS'];
			$Earnings = $Row['EARNINGS'];
			$Earnings = str_replace('$', '', $Earnings);
			
			$TempStat = new Stat($Clicks, $Leads, $Earnings, $SubId, NULL);
			$Output->addStatObject($TempStat);
		}
		
		return $Output;
	}
	
	public function getDateRangeSubIdDetails($SubId, $Date)
	{
		$params = array(
						'keyStr' => $this->loginHash,
						'subId' => $SubId,
						'startDate' => $Date,
						'endDate' => $Date
					   );
		$Result = $this->DoSoapRequest('getDateRangeSubIDDetails', $params);
		return $Result;
	}

	/**
	 * Retrieve offers.
	 *
	 */
	public function getOffers()
	{
		$this->offersLogin();
		
		$params = array('keyStr' => $this->loginHash);

		$Results = $this->DoSoapRequest('campaignList', $params);
		$TotalOffersImported = 0;
				
		$Output = new OfferEnvelope();
		foreach ($Results as $Row)
		{
			$Countries = explode(', ', $Row['COUNTRIES']);
			$ID = $Row['OFFER_ID'];
			$Title = $Row['NAME'];
			$LaunchDate = date('Y-m-d', strtotime($Row['LAUNCH_DATE']));
			$Description = $Row['DESCRIPTION'];
			$PreviewURL = $Row['PREVIEW_URL'];
			if(isset($Row['RATE']))
			{
				$Payout = $Row['RATE'];
			}else{
				$Payout = NULL;
			}
			
			$OfferObj = new Offer();
			$OfferObj->offerId = $ID;
			$OfferObj->name = $Title;
			$OfferObj->openDate = $LaunchDate;
			$OfferObj->description = $Description;
			$OfferObj->previewUrl = $PreviewURL;
			$OfferObj->payout = $Payout;
			$OfferObj->countries = $Countries;
			
			$Output->addOfferObject($OfferObj);
		}
				
		return $Output;
	}
	
	private function offersLogin()
	{
		$params = array(
			'user' => $this->offersUsername,
			'password' => $this->offersPassword
			);
			
		$this->loginHash = $this->DoSoapRequest('getKey', $params);
		
		if($this->loginHash == '')
		{
			return false;
		}
		else
		{
			return $this->loginHash;
		}
	}
	
	/**
	 * Perform a soap request using the nusoapclientw
	 *
	 * @param Mixed $method
	 * @param Mixed $args
	 * @return Array|Boolean
	 */
	Protected Function DoSoapRequest($method, $args)
	{
		$client = new SoapClient($this->apiUrl);
		//$client = new nusoapclientw($this->apiUrl, true);

		try {
			$result = $client->__soapCall($method, $args);
		}catch (Exception $e) {
			echo $this->ApiName().' Fault: '.$e->getMessage(); // TODO: Log the fault instead
			return false;
		}
		
		return $result;
	}
	
}