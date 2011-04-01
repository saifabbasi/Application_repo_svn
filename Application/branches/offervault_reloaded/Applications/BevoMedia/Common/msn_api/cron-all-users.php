<?php

	$path = '/home/bevomedia/domains/bevomedia.com/private_html/';

    include($path . 'yahoo_api/Components/Queue/Queue.Component.php');

    
	$DBLink = mysql_connect('localhost', 'bevomedia_admin', 'yoyoyo_1225');
	mysql_select_db('bevomedia_admart', $DBLink);
    $sql = "SELECT * FROM msn_accounts WHERE Name != '' AND Password != ''";
    $query = (mysql_query($sql, $DBLink));

    while($row = mysql_fetch_assoc($query))
    	addMSNAccountToQueue($row['ID']);
    	
    function disabled($id)
    {
    	$sql = 'SELECT ID FROM msn_accounts_disabled WHERE ID = ' . $id;
    	$query = mysql_query($sql);
    	$c = mysql_num_rows($query);
    	return($c > 0);
    }

    function addMSNAccountToQueue($id)
    {
    	if(disabled($id))
    		return;

    	global $path;
    	
    	require_once($path . 'msn_api/msn_api.php');
	    require_once($path . 'yahoo_api/Components/Queue/Queue.Component.php');
	    
		$DBLink = mysql_connect('localhost', 'bevomedia_admin', 'yoyoyo_1225');
		mysql_select_db('bevomedia_admart', $DBLink);
	    $sql = "SELECT * FROM msn_accounts WHERE ID = " . $id;
	    $row = mysql_fetch_assoc(mysql_query($sql, $DBLink));
	
	    $username = $row['Name'];
	    $password = $row['Password'];
	    $user_id = $row['UserID'];
	    $msn_account_id = $row['ID'];
		
		$reportType = 'TestReport';
		$reportName = $reportType . '-' . time();
		
		$msn = new msn_api($username, $password);
		
		if(isset($reportId))
			$reportId = $reportId;
		else
			$reportId = $msn->addReport($reportName);
			
		$report = $msn->getReportFile($reportId);
		if($report == 'PENDING')
		{
			$Queue = new QueueComponent();
			$JobID =  $Queue->CreateJobID();
			$envelope = $msn->rcsQueueOutput($reportId, $user_id, $msn_account_id);
			$Queue->SendEnvelope($JobID, $envelope);
		}else{
			require($path . 'msn_api/msn_api_import.php');
			echo (int)UploadReport($user_id, $msn_account_id, $report);
		}
    }
    
    
?>