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
class CPADNA Extends NetworksAbstract {
	/**
	 * @access protected
	 * @var string $loginUrl
	 */
	protected $loginUrl = 'http://cpadna.com/Login.aspx';
	
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
	protected $offersUrl = 'http://cpadna.com/Admin/CPA/Offers.aspx';

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
	protected $userNameFieldName = 'ctl00$cntMain$lgnLogin$UserName';

	/**
	 * @access protected
	 * @var string $userNameFieldId
	 */
	protected $userNameFieldId = 'ctl00_cntMain_lgnLogin_UserName';

	/**
	 * @access protected
	 * @var string $passwordFieldName
	 */
	protected $passwordFieldName = 'ctl00$cntMain$lgnLogin$Password';

	/**
	 * @access protected
	 * @var string $passwordFieldId
	 */
	protected $passwordFieldId = 'ctl00_cntMain_lgnLogin_Password';

	/**
	 * @access protected
	 * @var string $submitButtonName
	 */
	protected $submitButtonName = 'ctl00$cntMain$lgnLogin$Login';

	/**
	 * @access protected
	 * @var string $submitButtonId
	 */
	protected $submitButtonId = 'ctl00_cntMain_lgnLogin_Login';
	
	/**
	 * @access protected
	 * @var string $offersUsername
	 */
	protected $offersUsername = 'networks@bevomedia.com';
	
	/**
	 * @access protected
	 * @var string $offersPassword
	 */
	protected $offersPassword = 'bevo1025';
	
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
		return true;
	}

	

	/**
	 * Retrieve offers.
 	 */
	public function getOffers()
	{
		$this->offersLogin();
		
		$Results = array();
		
		$Offset = 1;
		
		while ($Offset<5) {
			
			$Page = $this->loadOfferPage(&$Offset);
			
			$HTML = str_get_html($Page);
	
			$Offers = array();
			
			
			// $OfferRows = $HTML->find('a[target]');
			$OfferRows1 = $HTML->find('.rgRow');
			$OfferRows2 = $HTML->find('.rgAltRow');
			
			$OfferRows = array_merge($OfferRows1, $OfferRows2);
			
			if (empty($OfferRows))
			{
				return;
			}
			
			
			
			foreach ($OfferRows as $OfferRow)
			{
				$Data = array();
	
				$tds = $OfferRow->find('td');
				
				$Data = array();
				
				$categoryName = $tds[1]->plaintext;
				$offerId = $tds[2]->plaintext;
				$offerName = $tds[4]->plaintext;
				$payout = $tds[5]->plaintext;
				$expire = $tds[6]->plaintext;
				
				$Data['OfferID'] = $offerId;
				$Data['Title'] = $offerName;
				$Data['Description'] = '';
				$Data['Starts'] = date('Y-m-d');
				$Data['Expires'] = date('Y-m-d', strtotime($expire));
				$Data['Categories'][] = $categoryName;
				$Data['Payout'] = $payout;
				
			
				$Results[] = $Data;
			}
			
			$Offset++;
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
			$OfferObj->countries = array();
			$OfferObj->category = $Result['Categories'];
			$OfferObj->ecpc = @$Result['ECPC'];
			$OfferObj->percent = @$Result['Percent'];
			$OfferObj->payout = $Result['Payout'];
			$OfferObj->type = 'Lead';
			$OfferObj->previewUrl = '';
			
			$OfferObj->offerType = 'Lead';
			if (strstr($Result['Payout'], '%')) {
				$OfferObj->offerType = 'Sale';
			}
			
			$OfferObj->imageUrl = '';
			$OfferObj->dateAdded = $Result['Starts'];
			
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
		$sql = "SELECT NOW();";
		mysql_query($sql);
		
		
		  $array = $this->getDotNetVars('', $this->offersUrl);

  if ($Offset>1) {
   
   $page = 7+($Offset-2)*2;
   if ($page<10) $page = '0'.$page;
   
   $array['ctl00$smScriptManager'] = 'ctl00$ctl00$cntMain$grdOffersPanel|ctl00$cntMain$grdOffers$ctl00$ctl03$ctl01$ctl07';
   $array['ctl00_radMenu_ClientState'] = '';
   $array['ctl00_cntMain_txtName_text'] = '';
   $array['ctl00$cntMain$txtName'] = '';
   $array['ctl00_cntMain_txtName_ClientState'] = '';
   $array['ctl00$cntMain$lbxCategories'] = '';
   $array['ctl00$cntMain$lbxPromoMethods'] = '';
   $array['ctl00$cntMain$lbxCreativeTypes'] = '';
   $array['ctl00$cntMain$lbxCountries'] = '';
   $array['ctl00$cntMain$grdOffers$ctl00$ctl03$ctl01$PageSizeComboBox'] = 100;
   $array['ctl00_cntMain_grdOffers_ctl00_ctl03_ctl01_PageSizeComboBox_ClientState'] = '{"logEntries":[],"value":"100","text":"100","enabled":true}';
   $array['ctl00_cntMain_grdOffers_ClientState'] = '';
   $array['ctl00_RadFormDecorator1_ClientState'] = '';
   $array['__EVENTTARGET'] = 'ctl00$cntMain$grdOffers$ctl00$ctl03$ctl01$ctl'.$page;
   $array['__EVENTARGUMENT'] = '';
   $array['__ASYNCPOST'] = 'true';
   $array['RadAJAXControlID'] = 'ctl00_RadAjaxManager1';
   
  }
  
  
  	$arrParams = array('CURLOPT_SSL_VERIFYPEER' => FALSE,
						'CURLOPT_USERAGENT' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
						'CURLOPT_TIMEOUT' => 60,
						'CURLOPT_FOLLOWLOCATION' => 1,
						'CURLOPT_COOKIEJAR' => $this->temp_dir() . '/cookiemonster.txt',
						'CURLOPT_COOKIEFILE' => $this->temp_dir() . '/cookiemonster.txt',
						'CURLOPT_REFERER' => '');
  
  	$postdata = '';
	foreach ($array as $Key => $Value)
	{
		$postdata .= urlencode($Key).'='.urlencode($Value).'&';
	}
	
	$arrParams['CURLOPT_POSTFIELDS'] = $postdata;
  
  $strOffers = $this->curlIt($this->offersUrl, $arrParams);
  
  
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
