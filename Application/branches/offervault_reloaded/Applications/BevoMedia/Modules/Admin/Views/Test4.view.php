<?php

	require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'CatchUpSql.class.php');
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('max_execution_time', '360');

	ini_set('memory_limit', '500M');
	print '<pre>';
	
	$userId = 28;
	
	$cu = new CatchUpSql($userId);
	$oc = $cu->process('2010-04-06');

	$zipcontent = '';
	foreach($oc as $key=>$value)
	{
		$zipcontent .= $value ."\n";
	}
	
	$zip = new ZipArchive;
	$res = $zip->open('/var/www/www/catchup/catchup-' . $userId . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
	if ($res === TRUE) {
	    $zip->addFromString('sqloutput.sql', $zipcontent);
	    $zip->close();
	}
	exit;
	
?>