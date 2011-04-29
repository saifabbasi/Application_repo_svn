<?php

require_once('Networks.Abstract.php');

/**
 * This class uses nusoap.
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'nusoap' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'nusoap.php');

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
abstract class AzoogleAbstract Extends NetworksAbstract {
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
	 * @var string $offerTrackUrl
	 */
	protected $offerTrackingCodesUrl = '';
	
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
		$args = array(
			'publisher_id' => $this->publisherId,
			'publisher_login' => $this->publisherLogin,
			'publisher_password' => $this->publisherPassword
		);
		
		$args_log = var_export($args, true);
					 
		$results = $this->DoSoapRequest('authenticate', $args);
		
		if($results !== false && !empty($results))
		{
			$this->logTransaction('Login success.', 'success', print_r($results, true));
			$this->loginHash = $results;
			return $results;
		}
		else
		{
			$this->logTransaction('Login failure.', 'warning', print_r($results, true));
			return false;
		}
	}
	
	/**
	 * Retrieve stats for this user.
	 *
	 * @param String $Date
	 * @return Boolean	False if error occurs.
	 */
	Public Function GetStats($Date = '')
	{
		if($Date == '')
		{
			$Date = date('Y-m-d');
		}
		
		$args = array(
			'login_hash'		=> $this->loginHash,
			'publisher_id'		=> $this->publisherId,
			'offer_id'			=> 'ALL',
			'start_date'		=> $Date,
			'end_date'			=> $Date
		);

		$Output = new StatEnvelope($Date);
		
		//$this->logTransaction("Get Stats args: ".var_export($args, true));
		$this->logTransaction('Retrieving stats for ' . $Date);

		$Results = $this->DoSoapRequest('getSubHits', $args);
		
		if($Results === false)
		{
			$this->logTransaction('Soap request failed.');
			return false;
		}
		
		//$this->logTransaction("Get Stats Results: ".var_export($Results, true));
		$this->logTransaction('Processing stats ... ');
		print_r($Results);
		foreach ( $Results as $Row )
		{
			$TempStat = new Stat($Row['hits'], $Row['leads'], $Row['amount'], $Row['sub'], $Row['offer_id']);
			if($TempStat->offerId != '')
			{
				$Output->addStatObject($TempStat);
			}
		}
		
		return $Output;
	}

	/**
	 * Retrieve offers.
	 *
	 */
	Public Function getOffers()
	{
		$Output = new OfferEnvelope();
		
		
		$this->publisherId = '37376';
		$this->publisherLogin = 'ryan@bevomedia.com';
		$this->publisherPassword = 'yoyoyo1025';
		$this->login();
		
		
		$args = array(
			'login_hash'		=> $this->loginHash,
			'publisher_id'		=> $this->publisherId
		);
				

		$Results = $this->DoSoapRequest('listOffers', $args);		
		
		$Results = array_reverse($Results);
		
		foreach ($Results as $Key => $OfferID) 
		{
			echo $Key.' '.count($Results)."\n";
			
			$args = array(
				'login_hash'		=> $this->loginHash,
				'publisher_id'		=> $this->publisherId,
				'offer_id'			=> $OfferID,
			); 
			$Offer = $this->DoSoapRequest('offerDetails', $args);	
			$Offer = $Offer[0];
			print_r($Offer);
			if (isset($Offer['open_date'])) {
				if (strtotime($Offer['open_date'])<strtotime("-1 year")) {
					break;
				}
			}
			
			if ($Offer['title']=='') continue;
			
			$Countries = array();
			foreach ($Offer['accepts'] as $Country) 
			{
				$Countries[] = $Country['name'];
			}
			
			
			$Categories = array();
			foreach ($Offer['categories'] as $Category) 
			{
				$Categories[] = $Category;
			}
			
			
			$OfferObj = new Offer();
			$OfferObj->offerId = $Offer['offer_id'];
			$OfferObj->name = $Offer['title'];
			$OfferObj->description = $Offer['description'];
			$OfferObj->countries = $Countries;
			$OfferObj->category = $Categories;
			$OfferObj->payout = $Offer['amount'];
			
			$OfferObj->offerType = 'Lead';
			if (strstr($OfferObj->payout, '%')) {
				$OfferObj->offerType = 'Sale';
			}
			
			$OfferObj->previewUrl = '';
			$OfferObj->imageUrl = '';			
			$OfferObj->dateAdded = $Offer['open_date'];
			
			
			$Output->addOfferObject($OfferObj);
		}
		
		return $Output;
		
		
		
		
die;
		
		$url = 'https://login.azoogleads.com/rss.php?login=ryan@bevomedia.com&passhash=3a3f9ced1a39b4babe7b18d766db70b5';
		$Data = file_get_contents($url);
		
		$Xml = new SimpleXMLElement($Data);
		foreach ($Xml->channel->item as $Offer)
		{
			print_r($Offer);die;
			$OfferID = (string)$Offer->attributes()->offerId;
			$Category = array((string)$Offer->attributes()->category);
			//$Categories[] = $Category;
			$Title = (string)$Offer->attributes()->offerName;
			$Detail = (string)$Offer->OfferTagLine;
			$StartDate = (string)$Offer->OpenDate;
			$ExpireDate = (string)$Offer->ExpiryDate;
			$Payout = (string)$Offer->OfferBounty;
			$TrackUrl	= $this->offerTrackingCodesUrl.$OfferID;
			$Countries = array();
			foreach ($Offer->TrafficRestriction->Country as $Country)
			{
				$Countries[] = (string)$Country;
			}
			
			$OfferObj = new Offer();
			$OfferObj->offerId = $OfferID;
			$OfferObj->name = $Title;
			$OfferObj->category = $Category;
			$OfferObj->description = $Detail;
			$OfferObj->openDate = $StartDate;
			$OfferObj->expireDate = $ExpireDate;
			$OfferObj->payout = $Payout;
			$OfferObj->trackUrl = $TrackUrl;
			$OfferObj->countries = $Countries;
			
			$Output->addOfferObject($OfferObj);
		}
		
		
		
		
		$CurlOptions = array(
			//'CURLOPT_URL'=>'http://reports.azoogleads.com/offer_xml/offer_xmlv2.xml',
			'CURLOPT_HEADER'=>false,
			'CURLOPT_RETURNTRANSFER'=>1,
			'CURLOPT_SSL_VERIFYPEER'=>false,
			'CURLOPT_SSL_VERIFYHOST'=>false,
			'CURLOPT_CONNECTTIMEOUT'=>50,
			'CURLOPT_FOLLOWLOCATION'=>true,
			'CURLOPT_USERAGENT'=>'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7',
			'CURLOPT_USERPWD'=>$this->offersUsername.':'.$this->offersPassword
		);
$this->login();
		$url = 'https://login.azoogleads.com/rss.php?login=ryan@bevomedia.com&passhash='.$this->loginHash;echo $url;die;
		$data = file_get_contents($url);
		echo $data;die;
		
		$Data = $this->curlIt('http://reports.azoogleads.com/offer_xml/offer_xmlv2.xml', $CurlOptions);
		$Xml = new SimpleXMLElement($Data);
		foreach ($Xml->OfferList->Offer as $Offer)
		{
			//$Categories[] = array();
			$OfferID = (string)$Offer->attributes()->offerId;
			$Category = array((string)$Offer->attributes()->category);
			//$Categories[] = $Category;
			$Title = (string)$Offer->attributes()->offerName;
			$Detail = (string)$Offer->OfferTagLine;
			$StartDate = (string)$Offer->OpenDate;
			$ExpireDate = (string)$Offer->ExpiryDate;
			$Payout = (string)$Offer->OfferBounty;
			$TrackUrl	= $this->offerTrackingCodesUrl.$OfferID;
			$Countries = array();
			foreach ($Offer->TrafficRestriction->Country as $Country)
			{
				$Countries[] = (string)$Country;
			}
			
			$OfferObj = new Offer();
			$OfferObj->offerId = $OfferID;
			$OfferObj->name = $Title;
			$OfferObj->category = $Category;
			$OfferObj->description = $Detail;
			$OfferObj->openDate = $StartDate;
			$OfferObj->expireDate = $ExpireDate;
			$OfferObj->payout = $Payout;
			$OfferObj->trackUrl = $TrackUrl;
			$OfferObj->countries = $Countries;
			
			$Output->addOfferObject($OfferObj);
		}
		
		return $Output;
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
		$client = new nusoapclientw($this->apiUrl, true);
		$result = $client->call($method, $args);

		if($client->fault)
		{
			echo $this->ApiName().' Fault: '.$client->faultstring; // TODO: Log the fault instead
			return false;
		}
		else
		{
			$err = $client->getError();
			if($err)
			{
				echo $this->ApiName().' Error: '.$err; // TODO: Log the error instead
				return false;
			}
			else
			{
				return $result;
			}
		}
	}
	

}