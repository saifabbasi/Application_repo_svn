<?php

require_once('Networks.Abstract.php');

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'simplehtmldom' . DIRECTORY_SEPARATOR . 'SimpleHTMLDom.php');


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
class PaydayPays Extends NetworksAbstract {
	
	/**
	 * @access private
	 * @var string $publisherId
	 */
	protected $publisherId = '';
	
	
	/**
	 * @access protected
	 * @var string $statsApiUrl
	 */
	protected $statsUrl = 'https://www.paydaypays.com/publishers/index';
	
	/**
	 * @access protected
	 * @var string $conversionsApiUrl
	 */
	protected $conversionsApiUrl = '';
	
	/**
	 * @access protected
	 * @var string $offersApiUrl
	 */
	protected $offersUrl = 'https://www.paydaypays.com/publishers/offers';

	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'michael.chambrello@bevomedia.com';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'hYhehOVyXA8U';
	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function login()
	{
		$Arr = array();		
		$Arr['email'] = $this->publisherLogin;
		$Arr['password'] = $this->publisherPassword;
		$Arr['submit'] = 'true';
		
		$this->loginUrl	= 'https://www.paydaypays.com/login';
		
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
		
		if (strstr($Header, 'Displaying results for')) 
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
		
		$Arr['email'] = $this->offersUsername;
		$Arr['password'] = $this->offersPassword;
		$Arr['submit'] = 'true';
		
		$this->loginUrl	= 'https://www.paydaypays.com/login';
		
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
		
		if (strstr($Header, 'Displaying results for')) 
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
		
		$Page = $this->loadStatsPage($Date);
		
		$HTML = str_get_html($Page);
		
		$Stats = $HTML->find('.png-box');
		
		$ClicksArray = array();
		$ConversionsArray = array();
		
		$Clicks = $Stats[1];
		$Conversions = $Stats[2];

		$Clicks = $Clicks->find('.c .c2 .content .data .data-table tbody tr');		
		foreach ($Clicks as $OfferClick)
		{
			$Click = $OfferClick->find("td");
			
			if (count($Click)==0) continue;
			
			if ($Click[1]->plaintext=='&nbsp;') continue;
			
			$ClickArray['OfferName'] = $Click[1]->plaintext;
			$ClickArray['TotalClicks'] = $Click[2]->plaintext;
			$ClickArray['TotalConversions'] = $Click[3]->plaintext;
			$ClickArray['ConversionRate'] = $Click[4]->plaintext;
			$ClickArray['Revenue'] = $Click[5]->plaintext;
			$ClickArray['EPC'] = $Click[6]->plaintext;
			
			$ClicksArray[] = $ClickArray;
		}
		
		$Conversions = $Conversions->find('.c .c2 .content .data .data-table tbody tr');		
		foreach ($Conversions as $OfferConversion)
		{
			$Conversion = $OfferConversion->find("td");
			
			if (count($Conversion)==0) continue;
			if (!isset($Conversion[1])) continue;
			
			if ($Conversion[1]->plaintext=='') continue;
			
			$ConversionArray['OfferName'] = $Conversion[1]->plaintext;
			$ConversionArray['SubID'] = $Conversion[2]->plaintext;
			$ConversionArray['Revenue'] = $Conversion[3]->plaintext;
			
			$ConversionsArray[] = $ConversionArray;
		}
		
		
		$Output = new StatEnvelope($Date);
		foreach ($ConversionsArray as $Conversion)
		{
			$SubID = $Conversion['SubID'];
			$Clicks = 1;
			$Payout = $Conversion['Revenue'];
			$Conversions = 1;
			
			$OfferName = mysql_real_escape_string($Conversion['OfferName']);
			$Sql = "SELECT offer__id FROM bevomedia_offers WHERE (title = '{$OfferName}')";
			$Row = mysql_query($Sql);
			$Row = mysql_fetch_assoc($Row);
			
			$OfferID = $Row['offer__id'];
			
			$TempStat = new Stat($Clicks, $Conversions, $Payout, $SubID, $OfferID);
			$Output->addStatObject($TempStat);
			
			foreach ($ClicksArray as $Key => $Click)
			{
				if ($Click['OfferName']==$Conversion['OfferName'])
				{
					$ClicksArray[$Key]['TotalClicks'] = $ClicksArray[$Key]['TotalClicks']-1; 
				}
			}
		}
		
		foreach ($ClicksArray as $Click)
		{
			if ($Click['TotalClicks']==0) continue;
			
			$SubID = NULL;
			$Clicks = $Click['TotalClicks'];
			$Payout = 0;
			$Conversions = 0;
			
			$OfferName = mysql_real_escape_string($Click['OfferName']);
			$Sql = "SELECT offer__id FROM bevomedia_offers WHERE (title = '{$OfferName}')";
			$Row = mysql_query($Sql);
			$Row = mysql_fetch_assoc($Row);
			
			$OfferID = $Row['offer__id'];
			
			$TempStat = new Stat($Clicks, $Conversions, $Payout, $SubID, $OfferID);
			$Output->addStatObject($TempStat);
		}
		
		return $Output;
//		
//		print_r($ClicksArray);
//		print_r($ConversionsArray);
//		
//		echo $Clicks[0]->plaintext;		
		die;
		
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
		
		$Page = $this->loadOfferPage();
		$HTML = str_get_html($Page);
		
		$Offers = $HTML->find('.tabs-area .tabset li a');
		
		$Output = new OfferEnvelope();
		foreach ($Offers as $Offer)
		{
			$OfferName = $Offer->plaintext;
			$OfferID = $Offer->href;
			echo $OfferID.":::".$Offer->plaintext."\n";
			
			$OfferID = substr($OfferID, 1);
			
			$OfferDetailData = $HTML->find('div[id='.$OfferID.'] .two-columns .r-content .sub-box dl');
			
			$OfferDetailData = $OfferDetailData[0]->find('dd');
			
			$Payout = $OfferDetailData[0]->plaintext;
			$Category = $OfferDetailData[3]->plaintext;
			$Date = date('Y-m-d', strtotime($OfferDetailData[6]->plaintext));
			
			if ($Date=='1970-01-01') continue;
			
			$OfferObj = new Offer();
			$OfferObj->offerId = $OfferID;
			$OfferObj->name = $OfferName;
			$OfferObj->countries = array();
			$OfferObj->category = $Category;
			$OfferObj->payout = $Payout;
						
			$OfferObj->offerType = 'Sale';
			
			$OfferObj->previewUrl = '';
			$OfferObj->imageUrl = '';			
			$OfferObj->dateAdded = $Date;
			
			
			$Output->addOfferObject($OfferObj);
			
		}
		
		return $Output;
		
		die;
		
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
						'CURLOPT_REFERER' => '',
						'CURLOPT_HTTPHEADER' => array("Accept: application/json", "X-Requested-With: XMLHttpRequest", "X-Request: JSON")
						);

		$strOffers = $this->curlIt($this->offersUrl, $arrParams);
		return $strOffers;
	}
	
	private function loadStatsPage($Date)
	{
		$Arr = array();		
		$Arr['date1'] = $Date;
		$Arr['date2'] = $Date;
		$Arr['csv'] = 'false';
		
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
						'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster'.$this->publisherLogin.md5($this->publisherPassword).'.txt',
						'CURLOPT_REFERER' => '',
						'CURLOPT_HTTPHEADER' => array("Accept: application/json", "X-Requested-With: XMLHttpRequest", "X-Request: JSON"),
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1,
						);

		$strOffers = $this->curlIt($this->statsUrl, $arrParams);
		return $strOffers;
	}
	
	
}