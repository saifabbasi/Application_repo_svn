<?php

require_once('Networks.Abstract.php');

/**
 * This class use simpleHTMLDom.
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Include' . DIRECTORY_SEPARATOR . 'simplehtmldom' . DIRECTORY_SEPARATOR . 'SimpleHTMLDom.php');

/**
 * LinkTrust.Abstract.php
 *
 * @category   RCS Framework
 * @package    Modules
 * @subpackage Bevomedia
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
abstract class LinkTrustAbstract Extends NetworksAbstract {
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = '';
	
	/**
	 * @access private
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
	 * @var string $offersUrl
	 */
	protected $offersUrl = '';

	/**
	 * @access protected
	 * @var string $viewState
	 */
	protected $viewState = '';

	/**
	 * @access protected
	 * @var string $eventValidation
	 */
	protected $eventValidation = '';

	/**
	 * @access protected
	 * @var string $userNameFieldName
	 */
	protected $userNameFieldName = '';

	/**
	 * @access protected
	 * @var string $userNameFieldId
	 */
	protected $userNameFieldId = '';

	/**
	 * @access protected
	 * @var string $passwordFieldName
	 */
	protected $passwordFieldName = '';

	/**
	 * @access protected
	 * @var string $passwordFieldId
	 */
	protected $passwordFieldId = '';

	/**
	 * @access protected
	 * @var string $submitButtonName
	 */
	protected $submitButtonName = '';

	/**
	 * @access protected
	 * @var string $submitButtonId
	 */
	protected $submitButtonId = '';
	
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
	 * @var string $csvUrl
	 */
	protected $csvUrl = '';

	/**
	 * @access protected
	 * @var string $viewState
	 */
	protected $csvViewState = '';

	/**
	 * @access protected
	 * @var string $eventValidation
	 */
	protected $csvEventValidation = '';
	
	/**
	 * Authenticate with the remote server.
	 *
	 * @return String|Boolean	Authentication Hash or false.
	 */
	Public Function login()
	{
		$Arr = array();
		$Arr = $this->getDotNetVars('', $this->loginUrl);

		$Arr[$this->userNameFieldName] = $this->publisherLogin;
		$Arr[$this->userNameFieldId] = $this->publisherLogin;
		$Arr[$this->passwordFieldName] = $this->publisherPassword;
		$Arr[$this->passwordFieldId] = $this->publisherPassword;
		$Arr[$this->submitButtonName] = 'Login';
		$Arr[$this->submitButtonId] = 'Login';
		$Arr['__EVENTTARGET'] = '';
		$Arr['__EVENTARGUMENT'] = '';
				
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir() . '/cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir() . '/cookiemonster.txt',
						'CURLOPT_REFERER' => 'http://publishers.adfinity.com/Welcome/LogIn.aspx',
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1,
						'CURLOPT_HEADER' => 1);
		$LoginPage = $this->curlIt($this->loginUrl, $arrParams);
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
		$FromDate = $ToDate = $Date;
		
		$Arr = array();
		$Arr['__VIEWSTATE'] = $this->csvViewState;
		
		if (strstr($this->csvUrl, 'OfferStats.aspx')===false)
		{
			//$Arr['__EVENTVALIDATION'] = $this->CSVEventValidation;
			
			$DotNetVars = $this->getDotNetVars();
			//echo '<pre>'; print_r($DotNetVars); die;
			$Arr['__LASTFOCUS'] = '';
			$Arr['__EVENTVALIDATION'] = $DotNetVars['__EVENTVALIDATION'];
			$Arr['ctl00$ContentPlaceHolder1$DateRange$ddlDateType'] = '9';
			$Arr['ctl00$ContentPlaceHolder1$DateRange$ctl00$ctl00'] = $FromDate;
			$Arr['ctl00$ContentPlaceHolder1$DateRange$ctl01$ctl00'] = $ToDate;
			$Arr['ctl00$ContentPlaceHolder1$DateRange$refreshButton'] = 'Generate Report';
			$Arr['ctl00$ContentPlaceHolder1$ddAffiliateReportFilter'] = '1';
			$Arr['__EVENTTARGET'] = '';
			$Arr['__EVENTARGUMENT'] = '';
			//$Arr['ctl00$ContentPlaceHolder1$btnExport'] = 'Export';
			$Arr['ctl00$ContentPlaceHolder1$CampaignSearch$txtCampaignSearch'] = '';
			$Arr['__VIEWSTATEENCRYPTED'] = '';
			$Arr['__VIEWSTATE'] = $DotNetVars['__VIEWSTATE'];
			//echo '<pre>'; print_r($Arr); die;
			$DotNetVars = $this->getDotNetVars($Arr);
			
			//echo '<pre>'; print_r($DotNetVars); die;
			
			
			$Arr['__LASTFOCUS'] = '';
			//$Arr['__EVENTVALIDATION'] = 'ovd8GvJR/xN7RcNrRjE1iArTYr1kTPBFfQ5hHfkhM0unZWhuP74LvlBJUvepscbEHrzpxsmBXgWlsusdMD4l7L6vn4Dl/MtOe9bacmLKPrv1caZd5iy5E+X+AMXG/jqKRgEiyEW1nP7Rcdgvky1YIb6MwvFzA5FimBsJFpAAwbHUEN11xAtxqPtoYGjGNUTC';
			$Arr['__EVENTVALIDATION'] = $DotNetVars['__EVENTVALIDATION'];
			$Arr['__VIEWSTATE'] = $DotNetVars['__VIEWSTATE'];
			$Arr['ctl00$ContentPlaceHolder1$DateRange$ddlDateType'] = '9';
			$Arr['ctl00$ContentPlaceHolder1$DateRange$ctl00$ctl00'] = $FromDate;
			$Arr['ctl00$ContentPlaceHolder1$DateRange$ctl01$ctl00'] = $ToDate;
			$Arr['ctl00$ContentPlaceHolder1$DateRange$refreshButton'] = 'Generate Report';
			$Arr['ctl00$ContentPlaceHolder1$ddAffiliateReportFilter'] = '1';
			$Arr['__EVENTTARGET'] = '';
			$Arr['__EVENTARGUMENT'] = '';
			$Arr['ctl00$ContentPlaceHolder1$btnExport'] = 'Export';
			$Arr['ctl00$ContentPlaceHolder1$CampaignSearch$txtCampaignSearch'] = '';
			$Arr['__VIEWSTATEENCRYPTED'] = '';
			//echo '<pre>'; print_r($Arr); die;
			//$ViewStateArr = $Arr;
			//$ViewStateArr['__VIEWSTATE'] = 'kG7xCZTRAquJx0GhK4Voa2wOB1v0mjBgnGcbBfQYqXnIwareNclHaskLK987ziEqiWkdWdmcmG0Fs0PAM/jzn0VmIAoOydLTpV3XTKWR1Foa1Mqn3hppGTvcz0cN5z9t97TYKHoJ2KIAVyOvMdLFvdr4wWKr+DycQ7zND8C0sAuU/ARnfWwW6kxG1WPH+2gGQsec0X6jap9TgRhDNg8oqPMRYrQ+91dWv/qpUlOSUfkZmpy4FD/B0jct2tur2yxYIH5herTgnIUvFN36m4lVfLWb4s7LG4ASBqRHDSb0HaA+VE1KukLHYE2wnLKZ/mB3cIInWtN62wRS0CCMAzKuwReVQmX4HFM4LMkqB+2qcidAsr5iWFEBGpDPHU4OfVJbqJlFr1RIZUA/hEG0wny76tZdYrG1FhMFTm7qggb/XnlS2khFXdzI1Tz7iyeZvvCeU+cRvifLvyWcVoqjYq8ze5lov8cUn+21QLL74D3LOOgPxxp/Zb5DB9T+gqZT2VsRqZ3hpNMciIKxk+zFCNT1i+1HwWYnv9egq3Cf6Yu70+GaiGaPTWzTpsVJ4kZqu9rz689D5JsQKlwIOVTIvW6WBYcINl/GB+sgeO6oxvo5ifS7cdHGH0eOpzo5heW1t7oot4mSLHU90TeFcZFT7uxl7ugoaIvlgvGX6pBSzuT5ejpIiaf6zciDKzk0vBOHSI3oFLTKiiQMNGTxefzeoHnIbEVxKXa/sT/YVuSw+tf8Yx2wJA6DOog9wPcf3dEthu+nOJp52qvx7vq2bn0r/2ch7bBapJ2jnc0BhFgDohWx5b+U307/wLY2P6344264ZK1WohTEoXht9fusymj6Y2kAO5VcyQr32S2fjcBTQylhJNhdAiccd0s+CbUt4dHFxGXn+kCWzYRSzsautlYDG57AqTpGw3iwPoi6U3BNakt/AYGq38ogXPeFhRHzbMUcpLVT95dm7mjOkuevT3EJvu7xyHNlrv0TsDiniBV+vQZMiwOhaODA4XCEMtibr7KhqAQwNeQNWWD8gru8KHowrl/IhWMC6Us4h2++Mt7TVi+W2xugbkIRycjXdcWmJpdgSYPoWRzvZy0ULooZ5RQwOOL/Q8TqNlu6bSPzn2ubeJW2kb2YEZNPEpjZYH0KOHAPaQ2hF++yXkQ6XeCzKQVTbGx/Zcxnp4bD0XWVOyOEhHabGG4qirHZ9knEXGUmmRo+Vjil9u5d4YrgCHzaEaVwcRAtOeho2rbzZ79VobV6/mLK966kmaOoGyK/A50ymU4Act3d4E85w3DfFO62HSOFme0OWZYc2vxKbV79lTw4QWRkPqUm0VyCHdlJEBTiepXKP0u4em0cZ4jsF0NOr+GXDkLvVHl9eozMIpeRO9p49rCs37FKabmKlIOhl4Q6F70AnU220YqrOyfIaWjlJ3UwVk+YMzz1ikEw2TOGus/gWHbfP/zezaqOQ1sd6J1rmpohQLyvGB2hB+BF8oBZsdUXChG2IVAt6M1X79YOdikpp8WHeLJXs3Udlti/x8IJ1OlO6LFcb4WKlypCccoORXlRfdfvN5cTeao0/Njd';
			// unset($ViewStateArr['ctl00$ContentPlaceHolder1$btnExport']);
			// unset($ViewStateArr['ctl00$ContentPlaceHolder1$DateRange$refreshButton']);
			// unset($ViewStateArr['__EVENTVALIDATION']);
		} else {
			$DotNetVars = $this->getDotNetVars();
			
			$Arr['__EVENTVALIDATION'] = $DotNetVars['__EVENTVALIDATION'];
			$Arr['__VIEWSTATE'] = $DotNetVars['__VIEWSTATE'];
			//echo '<pre>'; print_r($DotNetVars); die;
			$Arr['__EVENTTARGET'] = '';
			$Arr['__EVENTARGUMENT'] = '';
			$Arr['__LASTFOCUS'] = '';
			$Arr['_ctl0:ContentPlaceHolder1:ddOffer'] = 'All';
			$Arr['_ctl0:ContentPlaceHolder1:ddOfferType'] = 'All';
			$Arr['_ctl0:ContentPlaceHolder1:ddCategory'] = '';
			$Arr['_ctl0:ContentPlaceHolder1:ddSort'] = 'FriendlyName';
			$Arr['_ctl0:ContentPlaceHolder1:ddSortDirection'] = '';
			$Arr['_ctl0:ContentPlaceHolder1:ddCountryFilter'] = '-';
			$Arr['_ctl0:ContentPlaceHolder1:ddDateRange'] = '0';
			$Arr['_ctl0:ContentPlaceHolder1:dsFrom:txt_Date'] = $ToDate;
			$Arr['_ctl0:ContentPlaceHolder1:dsTo:txt_Date'] = $ToDate;
			$Arr['_ctl0:ContentPlaceHolder1:dgOffers:_ctl0:imgExport.x'] = '12';
			$Arr['_ctl0:ContentPlaceHolder1:dgOffers:_ctl0:imgExport.y'] = '10';
		}
		if($this->loginHash == NULL)
		{
			return false;
		}
		
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir().'/cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir().'/cookiemonster.txt',
						'CURLOPT_REFERER' => $this->csvUrl,
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1);
		$CSV = $this->curlIt($this->csvUrl, $arrParams);
		
		
		
		if (strstr($CSV, '<body ') && strstr($CSV, '<table '))
		{
			if(get_class($this) == 'EWA' || get_class($this) == 'CPAStaxx' ) {
				// EWA is currently outputing unexpected results
			}else{
				return;	
			}
		}
		
		
//		print $CSV;
//		 echo '<pre>'; echo htmlentities(print_r($CSV));
//		 die;

		$Handle = fopen($this->temp_dir().'/stats.csv', 'w');
		fwrite($Handle, $CSV);
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
			// $OfferID = '';
			// $Campaign = '';
			$Payout = '';
			$Impressions = 0;
			$Clicks = 0;
			$CTR = 0;
			$ECPM = 0;
			$EPC = 0;
			$Commission = 0;
			$SubID = '';
			
			if (strstr($this->csvUrl, 'OfferStats.aspx')===false)
			{
				if (strstr($Arr[0], 'Totals:')) break;

				if(!isset($Arr[3]))
					continue;
					
				if ($Arr[0]!='')
				{
					$OfferID = @$Arr[0];
					$Campaign = @$Arr[1];
					if(get_class($this) == 'EWA' || get_class($this) == 'CPAStaxx') {
						// patch for EWA
						if($Arr[2] == '' && $Arr[3] == '') {
							continue;
						}
					}else{
						continue;
					}
				}
				$SubID = @$Arr[2];
				$Payout = @$Arr[12];
				$Impressions = @$Arr[4];
				$Clicks = @$Arr[5];
				
				// $OfferID = $Arr[1];
				// $Campaign = $Arr[2];
				//$Payout = $Arr[4];
				$Impressions = @$Arr[4];
				$Clicks = @$Arr[5];
				$CTR = @$Arr[9];
				$ECPM = @$Arr[11];
				$EPC = @$Arr[12];
				$Conversions = @$Arr[7];
				//$Commission = $Arr[13];
			} else {
				$OfferID = @$Arr[0];
				$Campaign = @$Arr[1];
				$Payout = @$Arr[10];
				$Impressions = @$Arr[4];
				$Clicks = @$Arr[5];
			}
			
			if (count($Arr)<3) return;
			
			$Date = date('Y-m-d', strtotime($FromDate));
			
			$TempStat = new Stat($Clicks, $Conversions, $Payout, $SubID, $OfferID);
			
			$Output->addStatObject($TempStat);
		}
		fclose($Handle);
		//var_dump($this->temp_dir()+'/stats.csv');
		@unlink($this->temp_dir()+'/stats.csv');
		return $Output;
	}

	function getSubIds($OfferID, $FromDate = '', $ToDate = '')
	{
		if ($FromDate=='' || $ToDate == '')
		{
			$FromDate = date('m/d/Y');
			$ToDate = date('m/d/Y');
		}
		
		if (isset($_GET['Yesterday']))
		{
			$FromDate = date('m/d/Y', strtotime('-1 day'));
			$ToDate = $FromDate;
		}
		
		if (isset($_GET['Date']))
		{
			$FromDate = date('Y-m-d', strtotime($_GET['Date']));
			$ToDate = $FromDate;
		}
		
		$Url = $this->SubIdUrl.'?DateFrom='.$FromDate.'&DateTo='.$ToDate.'&CID='.$OfferID;
		$Arr = $this->getDotNetVars('', $Url);
		$Arr['btnExportToExcel.x'] = 6;
		$Arr['btnExportToExcel.y'] = 6;
		$Arr['btnExportToExcel_x'] = 6;
		$Arr['btnExportToExcel_y'] = 6;
		
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir().'/cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir().'/cookiemonster.txt',
						'CURLOPT_REFERER' => $Url,
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1);
		$CSV = $this->curlIt($Url, $arrParams);
		
		$Handle = fopen($this->temp_dir()+'/statssubids.csv', 'w');
		fwrite($Handle, $CSV);
		fclose($Handle);
		
		$Handle = fopen($this->temp_dir()+'/statssubids.csv', 'r');
		
		$Count = 0;
		$Results = array();
		while (($Arr = fgetcsv($Handle, 0, ",")) !== FALSE)
		{
			if ($Count++==0) continue;
			$Results[] = $Arr;
		}
		
		fclose($Handle);
		
		//echo '<pre>'; print_r($CSV); die;
		
		return $Results;
	}
	


	/**
	 * Retrieve offers.
 	 */
	public function getOffers()
	{
		$this->offersLogin();
		
		$Offset = 0;
		$FirstOffset = 0;
		
		if (isset($this->myOffersUrl)) {
			$Offset = -1;
			$FirstOffset = -1;
		}
		
		$Results = array();
		
		while ($Offset<5) {
			
			$FirstOffset = $Offset;
			$Page = $this->loadOfferPage(&$Offset);
			
			
			$HTML = str_get_html($Page);
	
			$Offers = array();
			
			
			// $OfferRows = $HTML->find('a[target]');
			$OfferRows1 = $HTML->find('.itemstyle');
			$OfferRows2 = $HTML->find('.alternatingitemstyle');
			
			$OfferRows = array_merge($OfferRows1, $OfferRows2);
			
			if (empty($OfferRows))
			{
				return;
			}
			
			
			
			foreach ($OfferRows as $OfferRow)
			{
				$Data = array();
	
				$Hrefs = $OfferRow->find('a');
				foreach ($Hrefs as $Href)
				{
					if (strstr($Href->id, 'lnkCountry'))
					{
						$Data['Countries'] = $Href->plaintext;
					} else
					if (strstr($Href->id, 'lnkPreview'))
					{
						$Data['PreviewUrl'] = $Href->href;
					}
				}
				$Span = $OfferRow->find('span');
			    $Anchor = $OfferRow->find('a');	
				$Title = $Anchor[1];
				$Data['Title'] = $Title->plaintext;
			
			
				foreach ($Span as $SpanItem)
				{
					if (strstr($SpanItem->id, 'lblDescription'))
					{
						$Data['Description'] = $SpanItem->plaintext;
					} else
					if (strstr($SpanItem->id, 'lblStart'))
					{
						$Date = str_replace('Start: ', '', $SpanItem->plaintext);
						if ($SpanItem->plaintext!='')
							$Data['Starts'] = date('Y-m-d', strtotime($Date)); else
							$Data['Starts'] = '0000-00-00';
					} else
					if (strstr($SpanItem->id, 'lblExpires'))
					{
						$Date = str_replace('Expires: ', '', $SpanItem->plaintext);
						if ($SpanItem->plaintext!='')
							$Data['Expires'] = date('Y-m-d', strtotime($Date)); else
							$Data['Expires'] = '0000-00-00';
					} else
					if (strstr($SpanItem->id, 'lblCategory'))
					{
						$Data['Categories'][] = $SpanItem->plaintext;
					} else
					if (strstr($SpanItem->id, 'lblECPC'))
					{
						$Data['ECPC'] = $SpanItem->plaintext;
					} else
					if (strstr($SpanItem->id, 'lblPercent'))
					{
						$Data['Percent'] = $SpanItem->plaintext;
					} else
					if ( (strstr($SpanItem->id, 'lblPayout')) || (strstr($SpanItem->id, 'lblPrice')) )
					{
						$Data['Payout'] = $SpanItem->plaintext;
					} else
					if (strstr($SpanItem->id, 'lblType'))
					{
						$Data['Type'] = $SpanItem->plaintext;
					} else
					if (strstr($SpanItem->id, 'lblOfferID'))
					{
						$Data['OfferID'] = str_replace('CID ', '', $SpanItem->plaintext);
					}
				}
				
				$Results[] = $Data;
			}
			
			
			if ($FirstOffset==$Offset) {
				break;
			}
						
		}
		
		
		
		$Total = count($Results);
		for ($i=0; $i<($Total); $i++)
		{
			if (!isset($Results[$i]))
			{
				continue;
			}
			
			if (!isset($Results[$i]['Description']))
			{
				$Results[$i] = array_merge($Results[$i+1], $Results[$i]);
				unset($Results[$i+1]);
			}
		}
		
		
		$HTML->clear();
		unset($HTML);
		unset($Page);
		unset($OfferRows);
		
		$Output = new OfferEnvelope();
		foreach($Results as $Result)
		{
			$OfferObj = new Offer();
			$OfferObj->offerId = $Result['OfferID'];
			$OfferObj->name = $Result['Title'];
			$OfferObj->description = $Result['Description'];
			$OfferObj->openDate = $Result['Starts'];
			$OfferObj->expireDate = @$Result['Expires'];
			$OfferObj->countries = $Result['Countries'];
			$OfferObj->category = $Result['Categories'];
			$OfferObj->ecpc = @$Result['ECPC'];
			$OfferObj->percent = @$Result['Percent'];
			$OfferObj->payout = $Result['Payout'];
			$OfferObj->type = $Result['Type'];
			$OfferObj->previewUrl = $Result['PreviewUrl'];
			
			$OfferObj->offerType = 'Lead';
			if (strstr($Result['Payout'], '%')) {
				$OfferObj->offerType = 'Sale';
			}
			
			$OfferObj->imageUrl = '';
			$OfferObj->dateAdded = $Result['Starts'];
			print_r($OfferObj);
			$Output->addOfferObject($OfferObj);
		}
		return $Output;
	}
	
	private function offersLogin()
	{
		$Arr = array();
		$Arr = $this->getDotNetVars('', $this->loginUrl);

		$Arr[$this->userNameFieldName] = $this->offersUsername;
		$Arr[$this->userNameFieldId] = $this->offersUsername;
		$Arr[$this->passwordFieldName] = $this->offersPassword;
		$Arr[$this->passwordFieldId] = $this->offersPassword;
		$Arr[$this->submitButtonName] = 'Login';
		$Arr[$this->submitButtonId] = 'Login';
		
		$Arr['__EVENTTARGET'] = '';
		$Arr['__EVENTARGUMENT'] = '';
				
		$postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir() . '/cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir() . '/cookiemonster.txt',
						'CURLOPT_REFERER' => $this->loginUrl,
						'CURLOPT_POSTFIELDS' => $postdata,
						'CURLOPT_POST' => 1);
		$LoginPage = $this->curlIt($this->loginUrl, $arrParams);
	}
	
	
	private function loadOfferPage($Offset)
	{
		echo "loadOfferPage {$Offset} \n";
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir() . '/cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir() . '/cookiemonster.txt',
						'CURLOPT_REFERER' => 'http://publishers.adfinity.com/Home.aspx');
		
		if ($Offset>=1) {
			$Arr = $this->getDotNetVars('', $this->offersUrl);
			$Arr['__EVENTTARGET'] = 'ctl00$ContentPlaceHolder1$pagingCotrol$lnk'.$Offset;
			
			$postdata = '';
			foreach ($Arr as $Key => $Value)
			{
				$postdata .= urlencode($Key).'='.urlencode($Value).'&';
			}
			
			$arrParams['CURLOPT_POSTFIELDS'] = $postdata;
		}
		
		if ($Offset==-1) {
			$strOffers = $this->curlIt($this->myOffersUrl, $arrParams);
		} else {
			$strOffers = $this->curlIt($this->offersUrl, $arrParams);	
		}
		
		if ($Offset==-1) {
			$Offset = 0;
		} else
		if (strstr($strOffers, 'ctl00_ContentPlaceHolder1_pagingCotrol_lnk'.($Offset+1))) {
			$Offset = $Offset+1;
		}
		
		return $strOffers;
	}
	
	private function getDotNetVars($Arr = '', $Url = '')
	{
		if ($Arr=='') $Arr = array();
		
		$vs_postdata = '';
		foreach ($Arr as $Key => $Value)
		{
			$vs_postdata .= urlencode($Key).'='.urlencode($Value).'&';
		}
		
		$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir().'/cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir().'/cookiemonster.txt',
						'CURLOPT_REFERER' => $this->loginUrl,
						'CURLOPT_POSTFIELDS' => $vs_postdata,
						'CURLOPT_POST' => 1,
						'CURLOPT_HEADER' => 1 );

		if ($Url=='')
			$Source = $this->curlIt($this->csvUrl, $arrParams);
		else
			$Source = $this->curlIt($Url, $arrParams);
		
		$Start = '<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="';
		
		$ViewStatePage = substr($Source, stripos($Source, $Start)+strlen($Start));
		$ViewStatePage = substr($ViewStatePage, 0, stripos($ViewStatePage, '" />'));
		
		$Result['__VIEWSTATE'] = $ViewStatePage;
		$this->viewState = $ViewStatePage;
		
		$Start = '<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="';
		if (strstr($Source, $Start))
		{
			$EventValidationPage = substr($Source, stripos($Source, $Start)+strlen($Start));
			$EventValidationPage = substr($EventValidationPage, 0, stripos($EventValidationPage, '" />'));
		} else
		{
			$EventValidationPage = '';
		}
		
		if(strpos($Source, 'action="LogIn.aspx"') !== false)
		{
			$this->loginHash = NULL;
		}else{
			$this->loginHash = 'SUCCESS';
		}
		$Result['__EVENTVALIDATION'] = $EventValidationPage;
		$this->eventValidation = $EventValidationPage;
		return $Result;
	}
}
