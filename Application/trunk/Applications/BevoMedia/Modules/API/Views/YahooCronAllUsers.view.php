<?php
die;
date_default_timezone_set ("America/New_York");
$Temp = (realpath(substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' ));

include($Temp . DIRECTORY_SEPARATOR . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');
require_once(PATH.'QueueTools.include.php');

	$sql = "SELECT * FROM bevomedia_accounts_yahoo WHERE username != '' AND password != '' AND masterAccountId != '' AND enabled = 1 AND deleted  = 0 AND vaultID <> 0";
    $query = mysql_query($sql, $db);
    
    while($row = mysql_fetch_assoc($query))
    {
		require_once(PATH . 'User.class.php');
		$user = new User($row['user__id']);
		
	    if ( ($user->vaultID==0) && (!$this->User->IsSubscribed(User::PRODUCT_INSTALL_NETWORKS)) && ($user->membershipType!='premium') )
		{
			echo "Account not verified...";
			continue;
		}
		
//		if($user->apiCalls <= 2)
//		{
//			echo('Not enough API credit. Buying more...' . "\r\n");
//			$user->AddUserAPICallsCharge();
////			continue;
//		}
    	addYahooAccountToQueue($row['id']);
//		$user->subtractApiCalls(300);
		$user->setLastPPCUpdate(date('c'));
    }
    	
?>