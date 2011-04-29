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
abstract class HitpathAbstract Extends NetworksAbstract {
	/**
	 * @access protected
	 * @var string $apiUrl
	 */
	protected $apiUrl = '';
	
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
  Public function login()
  {
    $this->logTransaction('Login: ' . $this->publisherLogin);
    $postdata = 'username=' . $this->publisherLogin . '&password=' . $this->publisherPassword;
    $arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
      'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
      'CURLOPT_TIMEOUT' => 60,
      'CURLOPT_FOLLOWLOCATION' => 1,
      'CURLOPT_COOKIEJAR' => $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt',
      'CURLOPT_COOKIEFILE' => $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt',
      'CURLOPT_REFERER' => 'https://affiliate.a4dtracker.com',
      'CURLOPT_POSTFIELDS' => $postdata,
      'CURLOPT_POST' => 1);
    $LoginPage = $this->curlIt($this->loginUrl, $arrParams);
  }
		
	/**
	 * Retrieve stats for this user and populate database.
	 *
	 * @param String $Date
	 * @return Boolean	False if error occurs.
	 */
	public function getStats($Date = '')
	{
	    $StartDate = $EndDate = $Date;
		if (empty($Date))
		{
			$StartDate = date('Y-m-d');
			$EndDate = date('Y-m-d');
		}
		
		
		$ApiKey = $this->publisherId;
		$args = array(
						'key' => $ApiKey,
						'type' => 'clicks',
						'start' => $StartDate,
						'end' => $EndDate,
						'format' => 'xml',
						'nozip' => '1'
					 );
		$Query = '';
		foreach ($args as $Key => $Value)
		{
			$Query .= $Key.'='.$Value.'&';
		}
		
		$Data = file_get_contents($this->apiUrl."?".$Query);
		if (empty($Data))
		{
		    $this->logTransaction("Server returned empty data");
		    return;
		}
		if (stristr($Data, "There were errors"))
		{
			$this->logTransaction("There were errors.\n" . print_r($Data, true));
			return;
		} else
		if (stristr($Data, "Reports are available every"))
		{
			$this->logTransaction("Reports are available every 15 minutes.");
			$this->logTransaction($Data);
			return;
		} else
		if (stristr($Data, "You really need an api key"))
		{
			$this->logTransaction("You really need an api key");
			return;
		}
		$this->logTransaction('Processing data.');
		
		$Results = array();
		$SubIDResults = array();
		
		libxml_use_internal_errors(true);
		$Xml = simplexml_load_string($Data);
		if (!$Xml)
		{
			return;
		}
		
		$Output = new StatEnvelope($Date);
		foreach ($Xml->click as $Click)
		{
			$Date = date('Y-m-d', strtotime((string)$Click->time));
			
			$Amount = (string)$Click->amount;
			$ID = (int)$Click->id;
			$SubID = (string)$Click->c1;
			$Conversions = 0;
			$Clicks = 1;
			if ($Amount!='0') $Conversions = 1;
			
			$TempStat = new Stat($Clicks, $Conversions, $Amount, $SubID, $ID);
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
		
		//ini_set("max_execution_time", 0);
		$this->offersLogin();
		

		$Offset = 0;
		while ($Page = $this->loadOfferPage($Offset))
		{
			$HTML = str_get_html($Page);
			$Offers = array();
			
			$OfferRows = $HTML->find('a');
			if (empty($OfferRows))
			{
				break;
			}
			
			foreach ($OfferRows as $OfferRow)
			{
				$AName = $OfferRow->name;
				if (strlen($AName) < 1)
				{
					continue;
				}
				
				$intThisOfferID = trim(substr($AName, 2));
				
				if (!is_numeric($intThisOfferID))
				{
					continue;
				}

				$offerDetails = $OfferRow->find('td.camplistcampname');

				$strThisOfferTitle = trim($offerDetails[0]->plaintext);
				$strThisOfferPayout = trim($offerDetails[1]->plaintext);
				
				// Split Offer Payout
				$arrThisPayout = explode(' ', $strThisOfferPayout);
				$intThisOfferPayout = $arrThisPayout[0];
				
				$Offers[$intThisOfferID] = array('Title' => $strThisOfferTitle, 'Payout' => $intThisOfferPayout);
														
			}
			
			$HTML->clear();
			unset($HTML);
			unset($OfferRows);
			
			foreach ($Offers as $OfferID => $Offer)
			{
				$OfferObj = new Offer();
				$OfferObj->offerId = $OfferID;
				$OfferObj->name = $Offer['Title'];
				$info = $this->parseOfferDescAndPreviewUrl($OfferID);
				
				$OfferObj->description = $info[0];
				$OfferObj->previewUrl = $info[1];
				
				$OfferObj->offerType = 'Lead';			
				if (strstr($Offer['Payout'], '%')) {
					$OfferObj->offerType = 'Sale';					
				}
				
				$OfferObj->payout = str_replace('$', '', $Offer['Payout']);
				$Output->addOfferObject($OfferObj);
				
				$OfferObj->imageUrl = '';
				$OfferObj->dateAdded = date('Y-m-d');
			}

			$Offset += 20;
			if($Output->IsEmpty() == false)
			{
				
			}
		}
		
		$CookiePath = $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt';
		unlink($CookiePath);
		
		return $Output;
	}
	
	/**
	 * Login required for offers retrieval.
	 *
	 */
	private function offersLogin()
	{
		global $LoginURL;
		$postdata = 'username=' . $this->offersUsername . '&password=' . $this->offersPassword;
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt',
						'CURLOPT_REFERER' => 'https://affiliate.a4dtracker.com',
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1);
		$LoginPage = $this->curlIt($this->loginUrl, $arrParams);
	}
	
	private function parseOfferDescAndPreviewUrl($intInOfferID)
	{
		global $OffersURL;
		
		$arrParams = array(
			'CURLOPT_SSL_VERIFYPEER' => FALSE,
			'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
			'CURLOPT_TIMEOUT' => 60,
			'CURLOPT_FOLLOWLOCATION' => 1,
			'CURLOPT_COOKIEJAR' => $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt',
				'CURLOPT_COOKIEFILE' => $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt',
			'CURLOPT_REFERER' => 'https://affiliate.a4dtracker.com'
		);
		
		$strOffer = $this->curlIt($this->offersUrl . '?action=offerdetails&progid=' . $intInOfferID . '&rowid=All' . $intInOfferID, $arrParams);
		
		$objHTML = str_get_html($strOffer);
		
		$offerDesc = $objHTML->find('td.campdesc');
		$strOfferDesc = @trim($offerDesc[0]->plaintext);
		
		
//		$offerPreviewUrl = $objHTML->find('input[value=Preview]');

		$previewUrl = '';
		
//			$previewUrl = $offerPreviewUrl[0]->href;
			$urlInfo = parse_url($this->offersUrl);
			$previewUrl = $urlInfo['scheme'].'://'.$urlInfo['host'].'/rd/r.php?sid='.$intInOfferID.'&pub=451866&c1=';
		
		
		
		$objHTML->clear();
		unset($objHTML);
		unset($offerDesc);
		
		return array($strOfferDesc, $previewUrl);
	}

	
	private function loadOfferPage($Offset)
	{
		global $OffersURL;
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir().DIRECTORY_SEPARATOR.'cookiemonster.txt',
						'CURLOPT_REFERER' => 'https://affiliate.a4dtracker.com');
		$strOffers = $this->curlIt($this->offersUrl . '?action=offerlist&listid=All_list&offset=' . $Offset, $arrParams);
		return $strOffers;
	}
	
}