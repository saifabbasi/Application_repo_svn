<?php
date_default_timezone_set ("America/New_York");
$Temp = (realpath(substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' ));
require_once($Temp . DIRECTORY_SEPARATOR . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');

$sql = "SELECT * FROM bevomedia_accounts_analytics WHERE username != '' AND password != '' AND Enabled = 1 AND Deleted = 0 AND verified = 1  GROUP BY user__id";

$query = mysql_query($sql, $db);
   
while($row = mysql_fetch_assoc($query))
{	
	require_once(PATH . 'User.class.php');
	$user = new User($row['user__id']);
	
	if ( ($user->vaultID==0) && (!$user->IsSubscribed(User::PRODUCT_INSTALL_NETWORKS)) && ($user->membershipType!='premium') )
	{
		echo "Account not verified...";
		continue;
	}
	
//	if($user->apiCalls <= 1)
//	{
//		echo('Not enough API credit. Buying more...');
//		$user->AddUserAPICallsCharge();
////		continue;
//	}
	
	addAnalyticsAccountToQueue($row['user__id']);
//	$user->subtractApiCalls(2);
}


function addAnalyticsAccountToQueue($id)
{		
	$Queue = new QueueComponent();
	$JobID =  $Queue->CreateJobID();
	$PATH = PATH;
	$envelope = <<<END
<?php
	require_once('{$PATH}analytics_api/analytics_import.php');
	analytics_import_update($id);
?>
END;
	$Queue->SendEnvelope($JobID, $envelope);
}
?>