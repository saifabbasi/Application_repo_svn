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

$Queue = new QueueComponent();
$Queue->SandboxDirectory = ABSPATH . 'Applications/BevoMedia/Common/QueueSandbox';

$i=0;
while($i <= 20){
	$Continue = $Queue->ProcessNextInQueue();
	$i++;
	if(!$Continue)
	{
		break;
	}
}

?>