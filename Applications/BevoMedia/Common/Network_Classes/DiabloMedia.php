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
class DiabloMedia Extends NetworksAbstract {
	
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
	protected $offersApiUrl = '';

	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = '53981';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'yoyoyo1025';
	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function login()
	{
		$Arr = array();
		
		$Arr['Id'] = $this->publisherLogin;
		$Arr['Password'] = $this->publisherPassword;
		$Arr['url'] = '/default/index/index/?';

		$this->loginUrl	= 'http://pub.diablomedia.com/auth/login/';
		
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		unlink(sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt');
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
						'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
						'CURLOPT_REFERER' => $this->loginUrl, 
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1,
						'CURLOPT_HEADER' => 1);
		$Header = $this->curlIt($this->loginUrl, $arrParams);
		
		if (strstr($Header, 'Publisher Control Panel')) 
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function offersLogin()
	{
		$Arr = array();
		
		$Arr['Id'] = $this->offersUsername;
		$Arr['Password'] = $this->offersPassword;
		$Arr['url'] = '/default/index/index/?';

		$this->loginUrl	= 'http://pub.diablomedia.com/auth/login/';
		
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		unlink(sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt');
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
						'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
						'CURLOPT_REFERER' => $this->loginUrl,
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1,
						'CURLOPT_HEADER' => 1);
		$Header = $this->curlIt($this->loginUrl, $arrParams);
		
		if (strstr($Header, 'Publisher Control Panel')) 
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
		$this->login();
		
		if($Date == '')
		{
			$Date = date('Y-m-d');
		}
		$FromDate = $ToDate = $Date;
		
		
		$jsonUrl = 'http://pub.diablomedia.com/reports/subid/start/'.$Date.'/end/'.$Date.'/?output=json';
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
			'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
			'CURLOPT_TIMEOUT' => 60,
			'CURLOPT_FOLLOWLOCATION' => 1,
			'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
			'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
			'CURLOPT_REFERER' => '',
			'CURLOPT_HTTPGET' => 1);
		$JsonData = $this->curlIt($jsonUrl, $arrParams);
		
		$JsonData = json_decode($JsonData);
				
		$TotalClicks = 0;
		$TotalPayout = 0;
		$TotalConversions = 0;
		$Line = 0;
		
		$Output = new StatEnvelope($Date);
		
		foreach ($JsonData->data as $Click)
		{
			$SubID = $Click->sid;
			$Clicks = $Click->clicks;
			$Payout = $Click->revenue;
			$Conversions = $Click->leads;
			$OfferID = $Click->id;
			
			$TempStat = new Stat($Clicks, $Conversions, $Payout, $SubID, $OfferID);
			$Output->addStatObject($TempStat);
		}
		
		return $Output;
	}



	/**
	 * Retrieve offers.
 	 */
	public function getOffers()
	{
		$this->offersLogin();
		
		$JsonData = $this->loadOfferPage();
		
		$Data = json_decode($JsonData);

		$Data = $Data->data;
		
		$Output = new OfferEnvelope();
		foreach($Data as $Offer)
		{
			$OfferObj = new Offer();
			$OfferObj->offerId = $Offer->id;
			$OfferObj->name = $Offer->name;
			$OfferObj->countries = array();
			$OfferObj->category = array();
			$OfferObj->payout = $Offer->payout;
			$OfferObj->type = $Offer->type;
			$OfferObj->ecpc = $Offer->epc;
			
			$OfferObj->offerType = 'Lead';
			if (strstr($Offer->payout, '%')) {
				$OfferObj->offerType = 'Sale';
			}
			
			$OfferObj->previewUrl = 'http://www.dmclix.com/c/'.$Offer->id.'/53981?sid=';
			$OfferObj->imageUrl = '';			
			$OfferObj->dateAdded = date('Y-m-d');
			
			
			$Output->addOfferObject($OfferObj);
		}
		return $Output;
	}
	
	private function loadOfferPage()
	{
		global $OffersURL;
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
						'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
						'CURLOPT_REFERER' => 'http://pub.diablomedia.com/',
						'CURLOPT_HTTPHEADER' => array("Accept: application/json", "X-Requested-With: XMLHttpRequest", "X-Request: JSON")
						);

		$strOffers = $this->curlIt('http://pub.diablomedia.com/campaigns/list/?page=1&perpage=200&sorton=name&sortby=ASC', $arrParams);
		return $strOffers;
	}
	
	
}