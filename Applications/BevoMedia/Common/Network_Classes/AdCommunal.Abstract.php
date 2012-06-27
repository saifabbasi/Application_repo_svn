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
abstract class AdCommunalAbstract Extends NetworksAbstract {
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
	    $StartDate = $EndDate = $Date;
		if (empty($Date))
		{
			$StartDate = date('Y-m-d');
			$EndDate = date('Y-m-d');
		}
		
		
		$url = $this->apiUrl."?u={$this->publisherLogin}&n={$this->publisherId}&d1=".urlencode($StartDate)."&d2=".urlencode($EndDate).'&r=hits';
		
		$Data = file_get_contents($url);
		
		$Handle = fopen($this->temp_dir().'/stats.csv', 'w');
		fwrite($Handle, $Data);
		fclose($Handle);
		
		$Handle = fopen($this->temp_dir().'/stats.csv', 'r');
		
		$OfferID = '';
		$Campaign = '';
		$Line = 0;
		$Output = new StatEnvelope($Date);
		while (($Arr = fgetcsv($Handle, 0, ",")) !== FALSE)
		{
		    
			if ($Line++<=0)
			{
				continue;
			}
						
			$SubID = @$Arr[3];
			$Payout = @$Arr[6];
			$Clicks = @$Arr[4];
			
			$Impressions = @$Arr[4];
			$Clicks = @$Arr[5];
			$Conversions = @$Arr[5];
				
			$Date = date('Y-m-d', strtotime($FromDate));
			
			$TempStat = new Stat($Clicks, $Conversions, $Payout, $SubID, $OfferID);
			
			$Output->addStatObject($TempStat);
		}
		fclose($Handle);
		//var_dump($this->temp_dir()+'/stats.csv');
		@unlink($this->temp_dir()+'/stats.csv');
		
		
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
		
		
		$url = $this->offersUrl."?u={$this->offersAffiliateId}&p={$this->offersApiKey}";

		$xmlData = file_get_contents($url);
		
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($xmlData);
		
		if (!$xml)
		{
			return;
		}
		
		
		foreach ($xml->adcommunal->offers->offer as $offer)
		{
			
			$offerId = $offer->offerid;
			$offerName = $offer->offername;
			$payout = $offer->offerpayout;
			$url = $offer->creative_url;
			$description = $offer->description;
			$offerLink = $offer->offerURL;
			$thumbnailImageUrl = '';
			
			
			echo $offerId."<br />\n";
//			echo $offerName."\n";
//			echo $offerType."\n";
			$countries = array();
			
			$categories = array($offer->category);
//			echo $offer->vertical."\n";
			
			$OfferObj = new Offer();
			$OfferObj->offerId = $offerId;
			$OfferObj->name = $offerName;
			$OfferObj->description = $description;
			$OfferObj->previewUrl = $url;
			$OfferObj->imageUrl = $thumbnailImageUrl;
			$OfferObj->offerType = 'Lead';
			if (strstr($payout, '%')) {
				$OfferObj->offerType = 'Sale';
			}
			
			$OfferObj->payout = str_replace('$', '', $payout);
			$OfferObj->dateAdded = date('Y-m-d');
			$OfferObj->countries = array();
			$OfferObj->category = $categories;
			$Output->addOfferObject($OfferObj);
		}
		
		
		return $Output;
	}
	
	/**
	 * Login required for offers retrieval.
	 *
	 */
	private function offersLogin()
	{
		return true;
	}
	
	private function parseOfferDesc($intInOfferID)
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
		
		$objHTML->clear();
		unset($objHTML);
		unset($offerDesc);
		
		return $strOfferDesc;
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