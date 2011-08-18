<?php

require_once('Networks.Abstract.php');



/**
 * HasOffers.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class Essociate Extends NetworksAbstract {
	
	/**
	 * @access private
	 * @var string $publisherId
	 */
	protected $publisherId = '';
	
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsApiUrl = '';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersApiUrl = 'http://feeds.essociate.com/offers.xml?apikey=7h4kH73nGsg37';

	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function login()
	{
		$Arr = array();
		
		$Arr['a'] = 'login';
		$Arr['j'] = '';
		$Arr['LogType'] = 'a';
		$Arr['UserName'] = $this->publisherLogin;
		$Arr['Password'] = $this->publisherPassword;
		
		
		$this->loginUrl	= 'https://secure.essociate.com/login';
		
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster'.md5($this->publisherLogin).'.txt',
						'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster'.md5($this->publisherLogin).'.txt',
						'CURLOPT_REFERER' => $this->loginUrl,
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1,
						'CURLOPT_HEADER' => 1);
		$Header = $this->curlIt($this->loginUrl, $arrParams);
		
		if (strstr($Header, 'Summary for Today')) 
		{
			return true;	
		}
		
		return false;
	}
	
	
	/**
	 * Retrieve stats for this user.
	 *
	 * @param String $Date
	 * @return Boolean	False if error occurs.
	 */
	Public Function getStats($Date = '')
	{
		if($Date == '')
		{
			$Date = date('Y-m-d');
		}
		
		unlink(sys_get_temp_dir().'/cookiemonster'.md5($this->publisherLogin).'.txt');
		
		if (!$this->login()) {
			return null;
		}
		
		$clicksUrl = 'https://secure.essociate.com/my/stats_export/essoc_'.date('d', strtotime($Date)).'_3_'.date('m', strtotime($Date)).'_'.date('Y', strtotime($Date)).'_default.csv?Day='.date('d', strtotime($Date)).'&ExportTable=3&Month='.date('m', strtotime($Date)).'&Year='.date('Y', strtotime($Date)).'&do=default&Export=csv';		
		$conversionsUrl = 'https://secure.essociate.com/my/stats_export/essoc_'.date('d', strtotime($Date)).'_2_'.date('m', strtotime($Date)).'_'.date('Y', strtotime($Date)).'_default.csv?Day='.date('d', strtotime($Date)).'&ExportTable=2&Month='.date('m', strtotime($Date)).'&Year='.date('Y', strtotime($Date)).'&do=default&Export=csv';

		$clicksData = $this->getUrlData($clicksUrl);
		$conversionsData = $this->getUrlData($conversionsUrl);
		
		$clicksArray = $this->csvToArray($clicksData);
		$conversionsArray = $this->csvToArray($conversionsData);

		$TotalClicks = 0;
		$TotalPayout = 0;
		$TotalConversions = 0;
		
		$Output = new StatEnvelope($Date);
		
		foreach ($clicksArray as $clicks) {
			$OfferID = $clicks[0];
			$Clicks = $clicks[1];
			$Payout = 0;
			$Conversions = 0;
			$SubID = '';
			
			foreach ($conversionsArray as $conversions) {
				if ($conversions[0]==$clicks[0]) {
					$Conversions = $conversions[1];
					$Payout = $conversions[2];
				}
			}
			
			$TempStat = new Stat($Clicks, $Conversions, $Payout, $SubID, $OfferID);
			$Output->addStatObject($TempStat);
		}
		
		return $Output;
	}

	public function getUrlData($Url)
	{
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
			'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
			'CURLOPT_TIMEOUT' => 60,
			'CURLOPT_FOLLOWLOCATION' => 1,
			'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster'.md5($this->publisherLogin).'.txt',
			'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster'.md5($this->publisherLogin).'.txt',
			'CURLOPT_REFERER' => '',
			'CURLOPT_HTTPGET' => 1);
		$CSV = $this->curlIt($Url, $arrParams);
		return $CSV;
	}
	
	public function csvToArray($data)
	{
		$handle = fopen('php://memory', 'rw'); 
		fwrite($handle, $data); 
		rewind($handle); 
		
		$ResultArray = array();
		
		$Line = 0;
		while (($Arr = fgetcsv($handle, 0, ",")) !== FALSE)
		{
			if ($Line++<=1)
			{
				continue;
			}
			
			if ($Arr[0]=='Totals') break;
			
			$ResultArray[] = $Arr;			
		}
		fclose($handle);

		return $ResultArray;
	}

	/**
	 * Retrieve offers.
 	 */
	public function getOffers()
	{
		$Data = file_get_contents($this->offersApiUrl);
		
		$specialCharacters = array('®');
		$Data = str_replace($specialCharacters, '', $Data);
				
		$xml = new SimpleXMLElement($Data);
		
		
		$Output = new OfferEnvelope();
		foreach($xml->offer as $Offer)
		{
			$OfferObj = new Offer();
			
			$OfferObj->name = $Offer->title;
			$OfferObj->description = $Offer->description_short;
			$OfferObj->countries = array();
			$OfferObj->category = array();
			$OfferObj->payout = $Offer->commission;
			$OfferObj->type = $Offer->commission_type;
			$OfferObj->previewUrl = $Offer->encrypted_url;
			
			$OfferObj->imageUrl = $Offer->site_image_large;
			
			if ($OfferObj->imageUrl=='') continue;
			
			$pathInfo = pathinfo($OfferObj->imageUrl);
			$OfferObj->offerId = $pathInfo['filename'];
						
			$OfferObj->offerType = 'Lead';
			if (strstr($Offer->payout, '%')) {
				$OfferObj->offerType = 'Sale';
			}
					
			$OfferObj->dateAdded = date('Y-m-d', strtotime($Offer->pubDate));
			
			$Output->addOfferObject($OfferObj);
		}
		return $Output;
	}
	
	
}