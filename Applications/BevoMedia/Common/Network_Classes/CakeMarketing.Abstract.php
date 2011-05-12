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
abstract class CakeMarketingAbstract Extends NetworksAbstract {
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
			$EndDate = date('Y-m-d 23:59:59');
		}
		
		if ($EndDate==date('Y-m-d', strtotime($EndDate))) 
		{
			$EndDate = $EndDate.' 23:59:59';
		}
		
		$url = $this->apiUrl."?affiliate_id={$this->publisherLogin}&api_key={$this->publisherId}&offer_id=0&start_date=".urlencode($StartDate)."&end_date=".urlencode($EndDate);
		
		$Data = file_get_contents($url);
		
		$Results = array();
		$SubIDResults = array();
		
		libxml_use_internal_errors(true);
		$Xml = simplexml_load_string($Data);
		if (!$Xml)
		{
			return;
		}
		//echo $url; die('da');
		
		$conversionsUrl = $this->apiUrlConversions."?affiliate_id={$this->publisherLogin}&api_key={$this->publisherId}&offer_id=0&start_date=".urlencode($StartDate)."&end_date=".urlencode($EndDate);
		$ConversionData = file_get_contents($conversionsUrl);
		$ConverionXml = simplexml_load_string($ConversionData);
		$count = 0;
		$Output = new StatEnvelope($Date);
		//echo '<pre>'; print_r($ConversionData);
		foreach ($Xml->Click as $Click)
		{
			$Date = date('Y-m-d', strtotime((string)$Click->click_date));
			// echo time().'<br />';
			if ($count++>=1000) break;
			
			// die($conversionsUrl);
			$Conversions = 0;
			$Amount = 0;
			//echo '$Click->offer_id:'.$Click->offer_id.'<br />';
			if (isset($Click->paid_action))
			{
				// $ConversionData = file_get_contents($conversionsUrl);			
				
				if ($ConverionXml)
				{
					$Conversions = 0;
					$Amount = 0;
					reset($ConverionXml->Conversion);
					foreach ($ConverionXml->Conversion as $Conversion)
					{
						//if ($Conversion->offer_id!=$Click->offer_id) continue;
						
						if (trim($Conversion->conversion_id)==trim($Click->paid_action))
						{
						//	echo '$Conversion->offer_id: '.$Conversion->offer_id.'<br />';die;
							$Conversions++;
							$Amount += (float)$Conversion->price;
						}
					}
				}
			}
			
			$ID = (int)$Click->offer_id;
			$SubID = (string)$Click->subid_1;
			
			$Clicks = $Click->total_clicks;
			
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
		
		$url = $this->offersUrl."?affiliate_id={$this->offersAffiliateId}&api_key={$this->offersApiKey}";
		echo $url;die;
		$xmlData = file_get_contents($url);
		print_r($xmlData);die;
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($xmlData);
		
		if (!$xml)
		{
			return;
		}
		
		
		foreach ($xml->Offer as $offer)
		{
			$offerId = $offer->offer_id;
			$offerName = $offer->offer_name;
			$payout = $offer->payout;
			$url = $offer->preview_link;
			$description = $offer->description;
			$offerType = $offer->offer_type;
			$offerLink = $offer->offer_link;
			$thumbnailImageUrl = $offer->thumbnail_image_url;
			
			echo $offerId."\n";
			echo $offerName."\n";
			echo $offerType."\n";
			$countries = array();
			
			if (isset($offer->allowed_countries))
			foreach ($offer->allowed_countries->Country as $country)
			{
				$countries[] = $country->country_abbr;
			}
			
			$categories = array($offer->vertical);
			echo $offer->vertical."\n";
			
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
			$OfferObj->countries = $countries;
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