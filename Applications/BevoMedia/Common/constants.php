<?php

	// Company Name
	define('SCRIPT_COMPANY_NAME',			'Bevo Media');

	
	// Default Language ID
	define('SCRIPT_DEFAULT_LANGUAGE',		1000);

	
	// Words to exclude from search
	define('EXCLUDE_FROM_SEARCH',			'y mas en and or in for of');

	
	// Valid upload file extensions
	define('VALID_EXTENSIONS',				'jpg,jpeg,gif,png,flv');

	
	// Default Charset used in admin and public panel
	define('DEFAULT_CHARSET',				'iso-8859-1');

	
	// Long term ad buying discount in percentage
	define("LONG_TERM_DISCOUNT_RATE",		5);


	// Display CurrencySymbol Before/After Price
	define('IS_CURRENCY_AFTER_PRICE',		0);
	

	define("APPROVE_USER_REGISTERATION",	'Y');
	define("APPROVE_WEBSITE_REGISTERATION",	'N');

	define("SELLER_FEATURED_SITE_COST",		15);
	define("SCRIPT_OWNER_FEE_PERCENTAGE",	20);
	define("PAY_PER_CLICK_MIN",				5);

	// No of hours between two clicks from a same IP, to protect clickthrough frauds
	define("CLICKTHROUGH_MIN_HRS_BETWEEN",	-10);

	// Network ads percentage multiple of script owner fee given to webmaster showing a network ad
	define("NETWORK_AD_DISPLAYER_FEE_PERC",	0.7);

	// Value 0/1. If 0 wont send out emails
	define("ISLOCAL",						0);

	// Minimum pay per click price
	define("MIN_PAY_PER_CLICK_PRICE",		0.5);
	define("MIN_PAY_PER_IMPRESSION_PRICE",	1);

	// Do make payments to publishers via CHECK/CHEQUE
	define("SEND_CHEQUE_TO_PUBLISHER",		1);

	// This text appears below ads
	define("ADS_POWERED_BY",				"Powered By");

	// Default country in the dropdown
	define('DEFAULT_COUNTRY',				"United States");


	// Banner ad dimensions
	$BANNER_AD_DIMENSIONS					= array(
												array('width' => 468, 'height'=>60),array('width' => 728, 'height'=>90),array('width' => 300, 'height'=>250),array('width' => 120, 'height'=>600),array('width' => 180, 'height'=>150)
												);
	// Video ad dimensions
	$VIDEO_AD_DIMENSIONS					= array(
												array('width' => 160, 'height'=>120), array('width' => 320, 'height'=>240), array('width' => 640, 'height'=>480)
												);

// ###wfwefdsfdsf123143242### Changeable constants below this line
//********************************************************************************************************	

	// Publisher is paid per ad per id
	define("IS_PUBLISHER_AWARDED_PER_IP",	0);

	// Adult Filter
	define("IS_ADULT_FILTER_OFF",			0);

	// Use advanced popup script
	define("USE_ADVANCED_POPUP_SCRIPT",		1);

	// Advertiser ID
	define("WEBMASTER_ADVERTISERID",		0);

	// Run in optimized mode to server quickly, Default 0.
	define("RUN_IN_OPTIMIZED_MODE",			1);

	// Enable Ad Rotation if all ads are PPC
	define("ENABLE_ADS_ROTATION",			1);

	// Cleanup input for strict checking. Disable it for other languages
	define('DO_DISABLE_INPUT_FILTERING',	0);

	// Maximum featured websites on homepage
	define("MAX_FEATURED_WEBSITES",			6);

	// Bevo Commission Rate (%)
	define("BEVO_COMMISSION_RATE",			3);

//********************************************************************************************************	
// PLUGINS
//********************************************************************************************************	

	// Installed plugins
	define("IS_MAKE_AN_OFFER_INSTALLED",	1);
	define("IS_PER_IMPRESSION_INSTALLED",	1);
	define("IS_VIDEO_AD_INSTALLED",			0);
	define("IS_GEO_TRAGETING_INSTALLED",	0);
	define('IS_AFFILIATES_INSTALLED',		1);
	define("IS_ADPIC_INSTALLED",			0);
	define("IS_PEELAWAY_INSTALLED",			1);

//********************************************************************************************************	
// AFFILIATES CONSTANTS
//********************************************************************************************************	

	define('AFF_IS_CHARITY_INSTALLED',		1);
	define('AFF_ADVERTISER_SHARE',			0.20); // Share of advertiser (0 - 1) Eg: 0.2, 0.25
	define('AFF_PUBLISHER_SHARE',			0.25); // Share of publisher (0 - 1) Eg: 0.2, 0.25

//********************************************************************************************************

	define('APP_STATUS_NOT_APPLIED',		0);
	define('APP_STATUS_APPLIED',			1);
	define('APP_STATUS_FORWARDED',			2);
	define('APP_STATUS_ACCEPTED',			3);
	define('APP_STATUS_REJECTED',			4);
	define('APP_STATUS_DEACTIVATED',		5);

	$APP_STATUS_TITLES							= array();
	$APP_STATUS_TITLES[APP_STATUS_NOT_APPLIED]	= 'Not Applied';
	$APP_STATUS_TITLES[APP_STATUS_APPLIED]		= 'Applied';
	$APP_STATUS_TITLES[APP_STATUS_FORWARDED]	= 'Pending';
	$APP_STATUS_TITLES[APP_STATUS_ACCEPTED]		= 'Accepted';
	$APP_STATUS_TITLES[APP_STATUS_REJECTED]		= 'Rejected';
	$APP_STATUS_TITLES[APP_STATUS_DEACTIVATED]	= 'Deactivated';

//********************************************************************************************************	

	// Ad type constants
	define("AD_TYPE_TEXT_AD",				1);
	define("AD_TYPE_PHOTO_AD",				2);
	define("AD_TYPE_BANNER_AD",				4);
	define("AD_TYPE_VIDEO_AD",				8);
	define("AD_TYPE_POPUP_AD",				16);
	define("AD_TYPE_POPUNDER_AD",			32);
	define("AD_TYPE_ADPIC_AD",				64);
	define("AD_TYPE_PEELAWAY_AD",			128);
	define("AD_TYPE_FULLPAGE_AD",			256);
	define("MAX_AD_TYPE_MASK",				511);

	// Ad type description
	$AD_TYPE_DESCRIPTIONS					= array();
	$AD_TYPE_DESCRIPTIONS[]					= AD_TYPE_TEXT_AD;
	$AD_TYPE_DESCRIPTIONS[]					= AD_TYPE_PHOTO_AD;
	$AD_TYPE_DESCRIPTIONS[]					= AD_TYPE_BANNER_AD;
	if ( IS_VIDEO_AD_INSTALLED == 1 )
		$AD_TYPE_DESCRIPTIONS[]				= AD_TYPE_VIDEO_AD;
	$AD_TYPE_DESCRIPTIONS[]					= AD_TYPE_POPUP_AD;
	$AD_TYPE_DESCRIPTIONS[]					= AD_TYPE_POPUNDER_AD;
	if ( IS_ADPIC_INSTALLED == 1 )
		$AD_TYPE_DESCRIPTIONS[]				= AD_TYPE_ADPIC_AD;
	if ( IS_PEELAWAY_INSTALLED == 1 )
		$AD_TYPE_DESCRIPTIONS[]				= AD_TYPE_PEELAWAY_AD;
	$AD_TYPE_DESCRIPTIONS[]					= AD_TYPE_FULLPAGE_AD;

//********************************************************************************************************	

	define('NETWORK_AZOOGLEADS_ID',			1000);
    define('NETWORK_ADBRITE_ID',            1002);
	define('NETWORK_NEVERBLUEADS_ID',		1006);
	define('NETWORK_COPEAC_ID',				1007);
	define('NETWORK_CPASTORM_ID',			1001);
	define('NETWORK_ADTEGRITY_ID',			1009);
	define('NETWORK_REALTECH_ID',			1010);
	define('NETWORK_BANNERCONNECT_ID',		1011);
	define('NETWORK_CPXINTERACTIVE_ID',		1012);
	define('NETWORK_VALUECLICK_ID',			1013);
	define('NETWORK_GOOGLEADSENSE_ID',		1003);
	define('NETWORK_BIDVERTISER_ID',		1014);
	define('NETWORK_CHITIKA_ID',		    1015);
	define('NETWORK_MAXBOUNTY_ID',		    1016);
	define('NETWORK_ANALYTICS_ID',		    1017);
    define('NETWORK_ADVERTISINGCOM_ID', 1018);
    define('NETWORK_TRIBALFUSION_ID', 1021);
    define('NETWORK_BLUELITHUIM_ID', 1020);
    define('NETWORK_BURSTMEDIA_ID', 1019);
    define('NETWORK_ADWORDS_ID', 1032);
    define('NETWORK_YAHOOPUB_ID', 1008);
	define('NETWORK_MARKETLEVERAGE_ID',1031);
    define('NETWORK_CLICKBOOTH_ID',1023);
    define('NETWORK_FLUXADS_ID',1034);
    define('NETWORK_ROIROCKET_ID',1035);
    define('NETWORK_XY7_ID',1037);
    define('NETWORK_CLICKBANK_ID',1040);
    define('NETWORK_COMMISIONJUNCTION_ID',1038);
    define('NETWORK_LINKSHARE_ID',1039);
    define('NETWORK_PEPPERJAM_ID',1036);
    define('NETWORK_SHAREASALE_ID',1042);
    define('NETWORK_AFFILIATE_ID',1030);
//********************************************************************************************************	
   //define client for the network using the directtrack api
	define('COPEAC_CLIENT','intermarkmedia');
	define('CPASTORM_CLIENT','cpastorm');
	define('MARKETLEVERAGE_CLIENT','marketleverage');
	define('CLICKBOOTH_CLIENT',	'integraclick');
    define('FLUXADS_CLIENT','flux');
    define('ROIROCKET_CLIENT','rsmediasolutions');
    define('XY7_CLIENT','rapidresponse');
    define('AFFILIATE_CLIENT','cash4creatives');
//********************************************************************************************************	
  
  //define the api rest URI for the api using the directtrack api
   define("MARKETLEVERAGE_CALL_URL",            "http://users.marketleverage.com/api/soap_affiliate.php");
   define("CLICKBOOTH_CALL_URL",                "https://publishers.clickbooth.com/api/soap_affiliate.php");
   define("FLUXADS_CALL_URL",                "http://123.fluxads.com/api/soap_affiliate.php");
   define("ROIROCKET_CALL_URL",                "http://launch.roirocket.com/api/soap_affiliate.php");
   define("XY7_CALL_URL",                "http://publishers.xy7.com/api/soap_affiliate.php");
   define("AFFILIATE_CALL_URL",          "http://login.tracking101.com/api/soap_affiliate.php");
//********************************************************************************************************  
	define('IMPRESSION_MULTIPLE_OF',		1000);

//********************************************************************************************************	
// Do not Change
//********************************************************************************************************

	define('DO_TIME_EACH_QUERY',			0);
	define('DO_SHOW_ERRORS',				1);

//********************************************************************************************************
?>