<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('max_execution_time', '360');
	ini_set('memory_limit', '500M');
  	date_default_timezone_set('America/New_York');

    if(file_exists('upgrade.lock'))
        die('Upgrade in progress, please wait for the upgrade to finish.<br />Upgrading takes about 10-20 minutes. If you think its stuck, delete the file "upgrade.lock" in this folder.');
	$debug_start_time = microtime(true);
	/**
	 * Lets set the Include path for Zend
	 */
	$IncludePaths = array(
	    realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR .'Externals'),
	    '.',
	);
	set_include_path(implode(PATH_SEPARATOR, $IncludePaths));

	/**
	 * The main Class Component is loaded here because its extended by even the Application Component
	 */
	require(realpath(getcwd()). DIRECTORY_SEPARATOR.'Components'.DIRECTORY_SEPARATOR.'Class'.DIRECTORY_SEPARATOR.'Class.Component.php');
	/**
	 * Load and execute main Application Runtime.
	 */
	require (realpath(getcwd())). DIRECTORY_SEPARATOR.'Components'.DIRECTORY_SEPARATOR.'Application'.DIRECTORY_SEPARATOR.'Application.Component.php';
	$ApplicationObj = new ApplicationComponent();
	$ApplicationObj->Run();
	
	echo "<!-- {$_SERVER['SERVER_ADDR']} -->";
?>