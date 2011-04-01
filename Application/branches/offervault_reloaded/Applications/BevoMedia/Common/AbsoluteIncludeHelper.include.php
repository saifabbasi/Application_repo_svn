<?php

	/**
	 * Sets the ABSPATH variable relative to this files location
	 */
	if(!defined('ABSPATH'))
	{
		$ABSPATH_TEMP = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
		define('ABSPATH', $ABSPATH_TEMP);
	}
	
	/**
	 * Sets default timezone to 'America/New_York'
	 */
	//date_default_timezone_set('America/New_York');
	
	
	/**
	 * Defines a database connection that can be used by classes and files when the Zend Framework database functionality is unavailable.
	 */
	if(!defined('ABSDBHOST'))
	{
		$abs_ini = parse_ini_file(ABSPATH . 'config.ini', true);
		
		$Mode = $abs_ini['Application']['Mode'];
		
		define('ABSMODE', $Mode);
		define('ABSDBHOST', $abs_ini['Database/'.$Mode]['Host']);
		define('ABSDBUSER', $abs_ini['Database/'.$Mode]['User']);
		define('ABSDBPASS', $abs_ini['Database/'.$Mode]['Pass']);
		define('ABSDBNAME', $abs_ini['Database/'.$Mode]['Name']);
		
		if(isset($abs_ini['Webservices']))
		{
			define('ABSWEBSERVICESDIR', $abs_ini['Webservices']['Directory']);
			
		}//$abs_ini['Webservices']
		
		global $db;
		$db = mysql_connect(ABSDBHOST, ABSDBUSER, ABSDBPASS);
		mysql_select_db(ABSDBNAME, $db);
		define('ABSDB', $db);
	}
	
	/**
	 * If PATH is not defined, set PATH to the following value.
	 */
	if(!defined('PATH'))
		define('PATH', ABSPATH.'Applications/BevoMedia/Common/');
		
	/**
	 * Include various class files.
	 */
	require_once(ABSPATH . 'Components/Class/Class.Component.php');
	if(!class_exists('QueueComponent'))
		require_once(ABSPATH . 'Components/Queue/Queue.Component.Console.php');
		
	if(!class_exists('EC2Scale'))
		require_once(ABSPATH . 'Components/Queue/EC2.Scaling.Component.php');


	require_once(ABSPATH . 'Applications/BevoMedia/Common/analytics_api/gapi.class.php');
	require_once(ABSPATH . 'Applications/BevoMedia/Common/msn_api/msn_api.php');
	require_once(ABSPATH . 'Applications/BevoMedia/Common/yahoo_api/yahoo_api.php');
	require_once(ABSPATH . 'Applications/BevoMedia/Common/adwords_api/apility.php');
	require_once(ABSPATH . 'Applications/BevoMedia/Common/adwords_api/apility_assist.php');
	
	require_once(ABSPATH . 'Applications/BevoMedia/Common/Analytics_Import.class.php');
	require_once(ABSPATH . 'Applications/BevoMedia/Common/Adwords_API_Usage.class.php');

	require_once(ABSPATH . 'Applications/BevoMedia/Common/CloakRedirect.class.php');
	require_once(ABSPATH . 'Applications/BevoMedia/Common/DirectLink.class.php');
	require_once(PATH .'User.class.php');
	require_once(PATH .'Accounts_Abstract.class.php');
	require_once(PATH .'Accounts_PPC_Abstract.class.php');
	require_once(PATH .'Accounts_Adwords.class.php');
	require_once(PATH .'Accounts_Yahoo.class.php');
	require_once(PATH .'Accounts_MSNAdCenter.class.php');
	
	require_once(PATH . 'JSON.php');
	
	
?>