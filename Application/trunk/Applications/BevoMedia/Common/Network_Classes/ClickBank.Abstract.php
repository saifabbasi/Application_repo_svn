<?php

require_once('Networks.Abstract.php');

/**
 * ClickBank.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
abstract class ClickBankAbstract Extends NetworksAbstract {
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = '';
	
	/**
	 * @access protected
	 * @var string $csvUrl
	 */
	protected $csvUrl = '';
	
	/**
	 * @access private
	 * @var string $loginHash
	 */
	private $loginHash = NULL;
	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	public function login()
	{
		$Arr = array();
		
		$Arr['nick'] = $this->publisherLogin;
		$Arr['pass'] = $this->publisherPassword;
		
		$Arr['j_username'] = $this->publisherLogin;
		$Arr['j_password'] = $this->publisherPassword;
		
		$this->loginUrl	= 'https://'.$this->publisherLogin.'.accounts.clickbank.com/account/login';
		
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster.txt',
						'CURLOPT_REFERER' => $this->loginUrl,
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1,
						'CURLOPT_HEADER' => 1);
		$Header = $this->curlIt($this->loginUrl, $arrParams);

		$End = strpos($Header, 'Content-Type');
		$Start = strpos($Header, 'Set-Cookie');
		$Parts = split('Set-Cookie: ',substr($Header, $Start, $End-$Start));
		$Cookies = array();
		foreach ($Parts as $Co)
		{
			$Cd = split(';',$Co);
			if (!empty($Cd[0]))
			{
				$Cv = explode("=", $Cd[0]);
				$Cookies[$Cv[0]] = $Cv[1];
			}
		}
		if(isset($Cookies['token']))
		{
			if($Cookies['token'] == 'null')
			{
				$this->loginHash = NULL;
				return false;
			}
		}else if(isset($Cookies['JSESSIONID']))
		{
			$this->loginHash = $Cookies['JSESSIONID'];
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
		if($this->loginHash == NULL)
		{
			throw new Exception('Login rejected by provider');
		}
		
		if($Date == '')
		{
			$Date = date('Y-m-d');
		}
		$FromDate = $ToDate = $Date;
		$this->csvUrl = 'https://'.$this->publisherLogin.'.accounts.clickbank.com/account/affiliate.htm?d-16390-o=1&d-16390-p=1&6578706f7274=1&d-16390-s=8&menu=2&tableDisplay=TABLE&d-16390-e=1&dataType=SALES&dataDisplayType=DOLLARS&startDate='.$FromDate.'&endDate='.$ToDate.'&dateRange=CUSTOM&displayChart=true&timeframe=daily&compareTo=NONE&menu=2&dimension=TRACKING_ID';
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
			'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
			'CURLOPT_TIMEOUT' => 60,
			'CURLOPT_FOLLOWLOCATION' => 1,
			'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster.txt',
			'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster.txt',
			'CURLOPT_REFERER' => '',
			'CURLOPT_HTTPGET' => 1);
		$CSV = $this->curlIt($this->csvUrl, $arrParams);
			
		$fileName = '/clickbankstats'.time().'.csv';
		
		@unlink(sys_get_temp_dir()+$fileName);
		$Handle = fopen(sys_get_temp_dir().$fileName, 'w');
		fwrite($Handle, $CSV);
		fclose($Handle);
		
		$Handle = fopen(sys_get_temp_dir().$fileName, 'r');
		
		$TotalClicks = 0;
		$TotalPayout = 0;
		$TotalConversions = 0;
		$Line = 0;
		
		$Output = new StatEnvelope($Date);
		
		while (($Arr = fgetcsv($Handle, 0, ",")) !== FALSE)
		{
			if ($Line++==0)
			{
				continue;
			}
			
			$SubID = $Arr[0];
			$Clicks = $Arr[1];
			$Payout = $Arr[16];
			$Conversions = $Arr[8];
			
			$TempStat = new Stat($Clicks, $Conversions, $Payout, $SubID);
			$Output->addStatObject($TempStat);
		}
		fclose($Handle);
		@unlink(sys_get_temp_dir()+$fileName);
		return $Output;
	}
	
	/**
	 * Retrieve offers.
	 *
	 */
	public function getOffers()
	{
		$Zip = file_get_contents('http://www.clickbank.com/feeds/marketplace_feed_v1.xml.zip');
		
		$FileName = tempnam(sys_get_temp_dir(), 'CB');
		$Handle = fopen($FileName, 'w');
		fwrite($Handle, $Zip);
		fclose($Handle);
		
		$Zip = zip_open($FileName);
		if (!$Zip)
		{
			return '';
		}
		
		$Output = new OfferEnvelope();
		while ($ZipEntry = zip_read($Zip))
		{
			$Name = zip_entry_name($ZipEntry);
			if (strstr($Name, '.xml'))
			{
				$XmlFileName = tempnam(sys_get_temp_dir(), 'CB');
				
				$Data = zip_entry_read($ZipEntry, zip_entry_filesize($ZipEntry));
				
				$Xml = new SimpleXMLElement($Data);
				
				$TotalOffersInserted = 0;
				
				foreach ($Xml->Category as $Category)
				{
					$CategoryName = (string)$Category->Name;
					
					foreach ($Category->Site as $Site)
					{
						$OfferID = (string)$Site->Id;
						$Title = (string)$Site->Title;
						$Description = (string)$Site->Description;
						$Payout = (string)$Site->TotalEarningsPerSale;
						
						$OfferObj = new Offer();
						$OfferObj->offerId = $OfferID;
						$OfferObj->name = $Title;
						$OfferObj->category = $CategoryName;
						$OfferObj->description = $Description;
						$OfferObj->payout = $Payout;
						$OfferObj->openDate = (string)$Site->ActivateDate;
						//$OfferObj->percent = (string)$Site->PercentPerSale;
						
						$OfferObj->offerType = 'Lead';
						if (strstr($Payout, '%')) {
							$OfferObj->offerType = 'Sale';
						}
						
						$OfferObj->imageUrl = '';
						$OfferObj->dateAdded = $OfferObj->openDate;
						
						$Output->addOfferObject($OfferObj);
					}
				}
			}
		}
		return $Output;

	}
	
}