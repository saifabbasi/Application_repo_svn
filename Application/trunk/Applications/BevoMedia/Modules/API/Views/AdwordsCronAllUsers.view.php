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
require_once(PATH.'QueueTools.include.php');

require_once(PATH . 'User.class.php');

$sql = "SELECT aa.*, u.membershipType FROM bevomedia_accounts_adwords aa LEFT JOIN bevomedia_user u ON (u.id = aa.user__id) WHERE aa.username != '' AND aa.password != '' AND aa.enabled = 1 AND aa.deleted = 0 AND u.enabled = 1 AND u.deleted = 0";
var_dump($sql);

$query = mysql_query($sql, $db);
$thisMo = date('Y-m-01 00:00:00');
while($row = mysql_fetch_assoc($query))
{
	$user = new User($row['user__id']);
	
	$sql = "SELECT count(*) as num FROM bevomedia_queue WHERE user__id={$row['user__id']} AND type='Adwords Update Account' AND created > '$thisMo'";
	$r = mysql_query($sql);
	$c = mysql_fetch_assoc($r);
	
	if ( ($user->vaultID==0) && (!$user->IsSubscribed(User::PRODUCT_INSTALL_NETWORKS)) && ($user->membershipType!='premium') )
	{
		echo "Account not verified...";
		continue;
	}
	
	addAdwordsAccountToQueue($row['id']);
	
}


?>