<?php

	ini_set('memory_limit', '64M');

	if (!isset($_GET['apiKey']))
	{
		$array = array('error' => 'You need to have a premium account to upgrade.');
		echo json_encode($array);
		return ;	
	}
	
	require_once('../Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');
	
	$apiKey = @mysql_real_escape_string($_GET['apiKey']);
	$Sql = "SELECT id FROM bevomedia_user WHERE (apiKey = '{$apiKey}') AND (membershipType = 'premium')";
	$Result = @mysql_query($Sql);
	if (@mysql_num_rows($Result))
	{
		$Data = file_get_contents(getcwd().'/latest.zip');
		
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="latest.zip"'); 
		header('Content-Transfer-Encoding: binary');
		
		echo $Data;
		return;
	}
	

	{
		$array = array('error' => 'You need to have a premium account to upgrade.');
		echo json_encode($array);
		return ;	
	}
	