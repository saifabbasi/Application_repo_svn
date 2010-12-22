<?php

require_once('Networks.Abstract.php');

/**
 * This class uses nusoap.
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'nusoap' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'nusoap.php');

/**
 * DirectTrack.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
abstract class DirectTrackAbstract Extends NetworksAbstract {
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
	 * @access protected
	 * @var string $apiClient
	 */
	protected $apiClient = '';
	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function login()
	{
		// Authentication is not required for this Network
	}
	
	public function test($Date = '')
	{
	}
	/**
	 * Retrieve stats for this user.
	 *
	 * @param String $Date
	 * @return Boolean	False if error occurs.
	 */
	Public Function GetStats($Date = '')
	{
		$args = array(
			'client' => $this->apiClient,
			'add_code' => $this->publisherLogin,
			'password' => $this->publisherPassword,
			'primary' => 'subid1',
			'secondary' => 'subid2',
			'tertiary' => 'subid3',
			'quaternary' => 'subid4',
			'keyword' => '',
			'program_id' => '',
			'start_date' => $Date,
			'end_date' => $Date
		);
		
		$results = $this->DoSoapRequest('getSubIDStats', $args);
		if($results !== false)
		{
			$records = simplexml_load_string($results);
			var_dump($records);
			$Output = new StatEnvelope($Date);
			$t_clicks = 0;
			$t_leads = 0;
			$t_commission = 0;
			foreach($records as $record)
			{
				$t_clicks += (int)$record->clicks;
				$t_leads += (int)$record->leads;
				$t_commission += (float)$record->commission;
				$TempStat = new Stat((int)$record->clicks, (int)$record->leads, (float)$record->commission, (string)$record->primary_group, (string)$record->campaign_id);
				$Output->addStatObject($TempStat);
			}
			//pull overall stats to account for missing subids
			$args = array(
				'client' => $this->apiClient,
				'add_code' => $this->publisherLogin,
				'password' => $this->publisherPassword,
				'program_id' => '',
				'start_date' => $Date,
				'end_date' => $Date
			);
			$results = $this->DoSoapRequest('dailyStatsInfo', $args);
			if($results !== false)
			{
			  $records = simplexml_load_string($results);
			  foreach($records->dailystats as $s)
				if($s->date == $Date)
				  $today = $s;
			}
			if(empty($today))
			  echo 'No totals for today';
			else
			{
			  $d_clicks = (int)$today->clicks - $t_clicks;
			  $d_leads = (int)$today->leads - $t_leads;
			  $d_commission = (float)$today->commission - $t_commission;
			  $TempStat = new Stat($d_clicks, $d_leads, $d_commission, 'No Subid', 0);
			  echo "Adjusting for nosubid discrepency: ";
			  var_dump($TempStat);
			  $Output->addStatObject($TempStat);
			}
			return $Output;
		}
		else
		{
			return false;
		}
	}
			
	Public Function GetRssFeed($Login_ID, $Password)
	{
		$XmlData = $this->GetOfferInfo('');
		
		$Xml = simplexml_load_string($XmlData);
		if ($Xml===FALSE)
		{
			return false;
		}
		
		$ResultArr = array();
		
		foreach ($Xml->program AS $Item)
		{
			$Result = array();
		
			$Result['ID'] = (string)$Item->program_id;
			$Result['Title'] = (string)$Item->program_name;
			$Result['Description'] = (string)$Item->program_description;
			$Result['Link'] = (string)$Item->url;
			
			$Result['CampaignType'] = (string)$Item->campaign_type;
			$Result['Description'] = (string)$Item->program_description;
			$Result['StartDate'] = (string)$Item->start_date;
			$Result['Url'] = (string)$Item->url;
			$Result['Payout'] = '0';
			foreach ($Item->payout as $Payout)
			{
				foreach ($Payout as $Price)
				{
					$Result['Payout'] = (string)$Price;
					$Result['Payout'] = trim(str_replace('$', '', $Result['Payout']));
				}
			}
			$Result['Expiration'] = (string)$Item->expiration->date;
			$Result['StartDate'] = (string)$Item->start_date;
			$CountrySpecific = $Item->country_specific;
			
			
			$Countries = array();
			if (is_object($CountrySpecific->country))
			{
				foreach ($CountrySpecific->country as $Country)
				{
					$Countries[] = (string)$Country;
				}
			}
			
			$Result['Countries'] = $Countries;
			
			$Result['Categories'] = array();
		
		
			
			$ResultArr[] = $Result;
		}
		
		// echo "<pre>";
		// print_r($ResultArr);
		// die;
		return $ResultArr;
	}
	
	public function GetOfferInfo($ID)
	{
		$args = array(
			'client' => $this->apiClient,
			'add_code' => $this->offersUsername,
			'password' => $this->offersPassword,
			'program_id' => $ID,
			'ignore_campaign_images' => '',
			'category' => ''
		);
		
		$results = $this->DoSoapRequest('campaignInfo', $args);
		if($results !== false)
		{
			$results = htmlspecialchars_decode($results);
			return $results;
		}
		else
		{
			return false;
		}
	}
	
	Public Function ProcessOfferXml($Data)
	{
		// print_r($Data); die;
		
		if (trim($Data)=='') return array();
		
		$Xml = new SimpleXMLElement(trim($Data));
		
		
		// print_r($Xml); echo "<br/>\n\n";
		// echo $Data; die;
		
		$Result['CampaignType'] = (string)$Xml->program->campaign_type;
		$Result['Description'] = (string)$Xml->program->program_description;
		$Result['StartDate'] = (string)$Xml->program->start_date;
		$Result['Url'] = (string)$Xml->program->url;
		foreach ($Xml->program->payout as $Payout)
		{
			foreach ($Payout as $Price)
			{
				$Result['Payout'] = (string)$Price;
				$Result['Payout'] = trim(str_replace('$', '', $Result['Payout']));
			}
		}
		$Result['Expiration'] = (string)$Xml->program->expiration->date;
		$Result['StartDate'] = (string)$Xml->program->start_date;
		// print_r($Result); die;
		$CountrySpecific = $Xml->program->country_specific;
		
		
		$Countries = array();
		if (is_object($CountrySpecific->country))
		{
			foreach ($CountrySpecific->country as $Country)
			{
				$Countries[] = (string)$Country;
			}
		}
		
		$Result['Countries'] = $Countries;
		
		return $Result;
	}
	
	Public Function getOffers()
	{
		$Login_ID= $this->offersUsername;
		$Password = $this->offersPassword;
		$Results = $this->GetRssFeed($Login_ID, $Password);
		
		$Output = new OfferEnvelope();
		foreach($Results as $Result)
		{
			$OfferObj = new Offer();
			$OfferObj->offerId = $Result['ID'];
			$OfferObj->name = $Result['Title'];
			$OfferObj->description = $Result['Description'];
			$OfferObj->previewUrl = $Result['Url'];
			$OfferObj->type = $Result['CampaignType'];
			$OfferObj->openDate = $Result['StartDate'];
			$OfferObj->expireDate = $Result['Expiration'];
			$OfferObj->payout = $Result['Payout'];
			$OfferObj->countries = $Result['Countries'];
			$OfferObj->category = $Result['Categories'];
			
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
		$result = html_entity_decode($client->call($method, $args));
		
		if($client->fault)
		{
			$this->logTransaction($client->fault, 'error', print_r($client, true));
			return false;
		}
		else
		{
			$err = $client->getError();
			if($err)
			{
				$this->logTransaction($err, 'error', print_r($client, true));
				return false;
			}
			else
			{
				return $result;
			}
		}
	}
	

}