<?php
date_default_timezone_set ("America/New_York");

if(isset($_SERVER['argc']) && $_SERVER['argc']>0)
{
	$tmp = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], 'Applications'));
	include($tmp . '/Applications/BevoMedia/' . 'Common/AbsoluteIncludeHelper.include.php');
}
else
{
	$tmp = substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], 'www'));
	include($tmp . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');
}

require_once(ABSPATH . 'Applications/BevoMedia/Common/msn_api/msn_api.php');
require_once(PATH.'QueueTools.include.php');
    
$sql = "SELECT * FROM bevomedia_accounts_msnadcenter WHERE username != '' AND password != '' AND enabled = 1 AND deleted = 0  ";
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
	
//	if($user->apiCalls <= 2)
//	{
//		echo('Not enough API credit. Buying more...');
//		$user->AddUserAPICallsCharge();
////		continue;
//	}
	
    $user->setLastPPCUpdate(date('c'));
	addMSNAccountToQueue($row['id']);
//	$user->subtractApiCalls(3);
}	
    
?>