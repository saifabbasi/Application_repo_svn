<?
require_once(PATH.'AbsoluteIncludeHelper.include.php');
function addYahooAccountToQueue($id)
{
    global $db;
    $sql = "SELECT * FROM bevomedia_accounts_yahoo WHERE id = " . $id;
    $row = mysql_fetch_assoc(mysql_query($sql, $db));
    $user_id = $row['user__id'];
    $username = $row['username'];
    $password = $row['password'];
    $masterAccountId = $row['masterAccountId'];
    
    $reportType = 'AdSummary';
    $reportName = $reportType . '-'. $id . '-' . time();
    
    $Queue = new QueueComponent();
    $JobID =  $Queue->CreateJobID('Yahoo Account Update', $user_id);
    $reportId = 0;
    try{
        $yahoo_api = new yahoo_api($username, $password, $masterAccountId, $id);
        if($yahoo_api->disabled)
            return true;
        else
            print "Account ($username) Added" . "\n";
        $reportId = $yahoo_api->addReport($reportName, $reportType);
    } catch (Exception $e) {
        $msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description, output) VALUES
        (
            (select id from bevomedia_queue where jobId='".$JobID."'),
            now(), now(), 'YAHOO', 'error', 'Error adding report request #'.$reportId, mysql_real_escape_string(print_r($e, true));
        )";
        mysql_query($msgQuery);
        return;
    }
    if ($reportId!=0)
    {
	    $envelope = $yahoo_api->rcsQueueAdVarsOutput($reportId, $JobID, $user_id);
	    $Queue->SendEnvelope($JobID, $envelope);
    }
}

function addAdWordsAccountToQueue($id)
{
	$sql = 'SELECT * FROM bevomedia_accounts_adwords WHERE id = ' . $id;
	$query = mysql_query($sql);
	$row = mysql_fetch_assoc($query);
	
	$Queue = new QueueComponent();
	$JobID =  $Queue->CreateJobID('Adwords Update Account', $row['user__id']);
	$PATH = PATH;
	$envelope = <<<END
<?php
require_once('{$PATH}adwords_api/adwords_import.php');
require_once('{$PATH}Accounts_Adwords.class.php');
update($row[user__id], $row[id], '$JobID');
\$account = new Accounts_Adwords();
\$account->setQueueJobId('$JobID');
\$account->GetInfo($id);
//echo "Updated " . \$account->UpdateCampaignsFromAPI() . " campaigns";
?>
END;
    $User = new User($row['user__id']);
    $User->setLastPPCUpdate(date('c'));
	$Queue->SendEnvelope($JobID, $envelope);
}
function insMSNAccountId($id, $accountId)
{
	global $db;
	$sql = "SELECT * FROM bevomedia_accounts_msnadcenter_accountId WHERE accountsMSNAdCenterId = '$id'";
	$query = mysql_query($sql, $db);
	if(mysql_num_rows($query) == 0)
	{
		$sql = "INSERT INTO bevomedia_accounts_msnadcenter_accountId (accountsMSNAdCenterId) VALUES ('$id')";
		mysql_query($sql);
		$db_id = mysql_insert_id();
	}else{
		$row = mysql_fetch_assoc($query);
		$db_id = $row['id'];
	}
	$sql = "UPDATE bevomedia_accounts_msnadcenter_accountId SET accountId = $accountId WHERE id = $db_id";
	mysql_query($sql);
}
function addMSNAccountToQueue($id)
{
  global $db;
  $sql = "SELECT * FROM bevomedia_accounts_msnadcenter WHERE id = " . $id;
  $row = mysql_fetch_assoc(mysql_query($sql, $db));

  $username = $row['username'];
  $password = $row['password'];
  $userId = $row['user__id'];
  $msn_account_id = $row['id'];

  $reportType = 'Daily Report';
  

  $msn = new msn_api($username, $password);
  
  $results = $msn->getAccounts();
  
  @$resultsArray = $results->GetAccountsResult->AdCenterAccount;
  if (!is_array($resultsArray)) {
  	$resultsArray = array(0 => $resultsArray);
  }
  
  foreach ($resultsArray as $key => $result) {
  	  
  	  $reportName = $reportType . '-' . $id. '-'. $key . '-' . time();
  	
	  @$api_accountId = $result->AccountId;
	  
	  
	  //echo $api_accountId."\n";die;
	  if(empty($api_accountId))
	  {
	//      mysql_query("UPDATE bevomedia_accounts_msnadcenter SET enabled=0 WHERE id=$id");
	      return;
	  }
	  insMSNAccountId($msn_account_id, $api_accountId);
	  
	  $msn = new msn_api($username, $password, '', false, $api_accountId);
	  
      $reportId = $msn->addReport($reportName);
      echo "reportId: ".$api_accountId.":".$reportId."\n";
      
	  $Queue = new QueueComponent();
	  $JobId =  $Queue->CreateJobid('MSN Account Update', $userId);
	  $envelope = $msn->rcsQueueOutput($reportId, $JobId, $userId, $msn_account_id);
	  $Queue->SendEnvelope($JobId, $envelope);
  }
}