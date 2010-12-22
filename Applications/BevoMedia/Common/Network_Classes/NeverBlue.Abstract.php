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
abstract class NeverBlueAbstract Extends NetworksAbstract {
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
		// Authentication is not required for this Network
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
		$URL = 'http://'.urlencode($this->publisherLogin).':'.urlencode($this->publisherPassword).'@secure.neverblue.com/service/aff/v1/rest/reportSchedule/?';
		$URL.= 'dateStart='.$Date.'&';
		$URL.= 'dateEnd='.$Date.'&';
		//$URL.= 'dateStart=2009-12-1&';
		$URL.= 'relativeDate=&';
		$URL.= 'campaign=0&';
		$URL.= 'type=subid';
		
		$Data = @file_get_contents($URL);
		
		//$Xml = new SimpleXMLElement($Data);
		
		libxml_use_internal_errors(true);
		$Xml = simplexml_load_string($Data);
		
		if (!$Xml)
		{
			return;
		}
		
		 // echo '<pre>'; print_r($Xml);
		 //die;
		$Status = (string)$Xml->status;
		$JobID = (string)$Xml->reportJob;
		
		if ($Status=='success')
		{
			while (1)
			{
				$ResultNeverBlue = $this->ReportURL($JobID);
				if (!$ResultNeverBlue)
				{
					sleep(5);
					echo date('H:i:s') . "SLEEPING (5)\n";
					continue;
				}
				break;
			}
			
		}
		
		return $ResultNeverBlue;
	}
	
	public function ReportURL($ID)
	{
		$Date = date('Y-m-d');
		if (isset($_GET['Yesterday']))
		{
			$Date = date('Y-m-d', strtotime('-1 day'));
		}
		if(isset($_POST['Date']))
		{
			$Date = date('Y-m-d', strtotime($_POST['Date']));
		}
		
		$URL = 'http://'.urlencode($this->publisherLogin).':'.urlencode($this->publisherPassword).'@secure.neverblue.com/service/aff/v1/rest/reportDownloadUrl/?';
		$URL.= 'reportJob='.$ID.'&';
		$URL.= 'format=xml';
		
		$Data = @file_get_contents($URL);
		echo date('H:i:s') . "Requesting stats report...\n";
		
		// $Xml = new SimpleXMLElement($Data);
		$Xml = simplexml_load_string($Data);
		echo date('H:i:s') . "Parsing report info...\n";
		if (!$Xml)
		{
			return true;
		}
		
		$Status = (string)$Xml->status;
		
		if ($Status!='success') return false;
		
		$ReportID = (string)$Xml->reportJob;
		$DownloadURL = (string)$Xml->downloadUrl;
		// echo $DownloadURL."<br/>\r\n";
		echo date('H:i:s') . "Getting report...\n";
		$Data = file_get_contents($DownloadURL);

		$impressions	= 0;
		$clicks			= 0;
		$conversions	= 0;
		$revenue		= 0;
		$ecpm			= 0;
	
		// $Xml = new SimpleXMLElement($Data);
		echo date('H:i:s') . "Parsing report...\n";
		$Xml = simplexml_load_string($Data);
		
		if (!$Xml)
		{
			return true;
		}
		echo date('H:i:s') . "Building envelope...\n";
		$Output = new StatEnvelope($Xml->parameters->end_date);
		foreach ($Xml->table->rows->row as $Row)
		{
			$Arr['SubID'] = (string)$Row->cell[0];
			$Arr['CampaignID'] = (string)$Row->cell[1];
			$Arr['CampaignName'] = (string)$Row->cell[2];
			$Arr['Impressions'] = (string)$Row->cell[3];
			$Arr['Clicks'] = (string)$Row->cell[4];
			$Arr['Conversions'] = (string)$Row->cell[5];
			$Arr['Payout'] = (string)$Row->cell[7];
			$Arr['EPC'] = (string)$Row->cell[9];
			
			// echo '<pre>';
			// print_r($Arr);
			
			
			$clicks			+= $Arr['Clicks'];
			$conversions	+= $Arr['Conversions'];
			$revenue		+= $Arr['Payout'];
			$impressions	+= $Arr['Impressions'];
			$ecpm			+= $Arr['EPC'];

			$OfferID		= trim($Arr['CampaignID']);
			$SubID			= trim($Arr['SubID']);
			
			$TempStat = new Stat($Arr['Clicks'], $Arr['Conversions'], $Arr['Payout'], $Arr['SubID'], $Arr['CampaignID']);
			
			if ($OfferID != '')
			{
				$Output->addStatObject($TempStat);
			}
		}
		
		echo date('H:i:s') . "Done!\n";
		return $Output;
	}


	/**
	 * Retrieve offers.
	 *
	 */
	public function getOffers()
	{
		$URL = 'http://'.urlencode($this->offersUsername).':'.urlencode($this->offersPassword).'@secure.neverblue.com/service/aff/v1/rest/getOffers/';
	
		$Data = file_get_contents($URL);
		
		$Xml = new SimpleXMLElement($Data);
		
		$TotalOffersInserted = 0;
		
		$Output = new OfferEnvelope();
		foreach ($Xml->offers->offer as $Row)
		{
			$ID = (string)$Row->idOffer;
			$Title = (string)$Row->title;
			$LaunchDate = date('Y-m-d', strtotime($Row->launched));
			$Payout = 0;
			$Description = '';
			
			$Count = 0;
			$LastPayout = 0;
			foreach ($Row->actions->action as $Action)
			{
				$Name = (string)$Action->name;
				$LastPayout = (string)$Action->staticPayout;
				if(isset($Action->description))
				{
					$Description = (string)$Action->description;
				}
				if ($Name=="US")
				{
					$Payout = (string)$Action->staticPayout;
					break;
				}
				$Count++;
			}
			if ( ($Payout==0) && ($Count==1) )
			{
				$Payout = $LastPayout;
			}
			
			if ($Payout==0) continue;
			
			$OfferObj = new Offer();
			$OfferObj->offerId = $ID;
			$OfferObj->name = $Title;
			$OfferObj->payout = $Payout;
			$OfferObj->openDate = $LaunchDate;
			$OfferObj->description = $Description;
			$OfferObj->category = (string)$Action->primaryCategory;
			$OfferObj->subcategory = (string)$Action->secondaryCategory;
			
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
		print $method . '<br>';
		$client = new nusoapclientw($this->apiUrl, true);
		$result = $client->call($method, $args);

		if($client->fault)
		{
			echo $this->name.' Fault: '.$client->faultstring; // TODO: Log the fault instead
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