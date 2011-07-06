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
class PeerFly Extends NetworksAbstract {
	
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
	protected $offersApiUrl = 'http://peerfly.com/rss/offers.php?count=all';

	
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
		$Arr['submit_x'] = 10;
		$Arr['submit_y'] = 10;
		
		$this->loginUrl	= 'http://peerfly.com/members.php';
		
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
		
		if (strstr($Header, 'Publisher Login')) 
		{
			return false;	
		}
		
		return true;
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
		
		$FromDate = $ToDate = $Date;
		$this->csvUrl = 'https://peerfly.com/members/exportreports.php?from='.date('m/d/Y', strtotime($Date)).'&to='.date('m/d/Y', strtotime($Date)).'&start='.$Date.'&end='.$Date.'&offer=all';
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
			'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
			'CURLOPT_TIMEOUT' => 60,
			'CURLOPT_FOLLOWLOCATION' => 1,
			'CURLOPT_COOKIEJAR' => sys_get_temp_dir().'/cookiemonster'.md5($this->publisherLogin).'.txt',
			'CURLOPT_COOKIEFILE' => sys_get_temp_dir().'/cookiemonster'.md5($this->publisherLogin).'.txt',
			'CURLOPT_REFERER' => '',
			'CURLOPT_HTTPGET' => 1);
		$CSV = $this->curlIt($this->csvUrl, $arrParams);

		@unlink(sys_get_temp_dir().'/peerfly-'.$this->publisherId.'.csv');
				
		$Handle = fopen(sys_get_temp_dir().'/peerfly-'.$this->publisherId.'.csv', 'w');
		fwrite($Handle, $CSV);
		fclose($Handle);
		
		$Handle = fopen(sys_get_temp_dir().'/peerfly-'.$this->publisherId.'.csv', 'r');
		
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
			
			$SubID = $Arr[3];
			$Clicks = 1;
			$Payout = ($Arr[8]=='Yes')?$Arr[7]:0;
			$Conversions = ($Arr[8]=='Yes')?1:0;
			$OfferID = $Arr[2];
			
			$TempStat = new Stat($Clicks, $Conversions, $Payout, $SubID, $OfferID);
			$Output->addStatObject($TempStat);
		}
		fclose($Handle);
		@unlink(sys_get_temp_dir()+'/peerfly-'.$this->publisherId.'.csv');
		return $Output;
	}



	/**
	 * Retrieve offers.
 	 */
	public function getOffers()
	{
		$Data = file_get_contents($this->offersApiUrl);
		
		$xml = new SimpleXMLElement($Data);
		
		
		$Output = new OfferEnvelope();
		foreach($xml->channel[0]->item as $Offer)
		{
			$OfferObj = new Offer();
			$OfferObj->offerId = $Offer->offerID;
			$OfferObj->name = $Offer->title;
			$OfferObj->description = $Offer->description;
			$OfferObj->countries = explode(', ', $Offer->countries);
			$OfferObj->category = array();
			$OfferObj->payout = $Offer->payout;
			$OfferObj->type = $Offer->type;
			$OfferObj->previewUrl = $Offer->link;
			$OfferObj->ecpc = $Offer->epc;
			$OfferObj->openDate = $Offer->pubDate;
			
			$OfferObj->offerType = 'Lead';
			if (strstr($Offer->payout, '%')) {
				$OfferObj->offerType = 'Sale';
			}
			
			$OfferObj->imageUrl = '';			
			$OfferObj->dateAdded = date('Y-m-d', strtotime($Offer->pubDate));
			
			$Output->addOfferObject($OfferObj);
		}
		return $Output;
	}
	
	
}