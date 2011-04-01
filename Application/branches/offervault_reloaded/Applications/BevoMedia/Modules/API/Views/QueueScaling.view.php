<?php

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

$IncludePaths = array(
    ABSPATH . 'Externals',
    '.',
);
set_include_path(implode(PATH_SEPARATOR, $IncludePaths));
require_once(ABSPATH . 'Externals/Zend/Db.php');

require_once(ABSPATH . 'Externals/Zend/Service/Amazon/Ec2/Instance.php');


$EC2S = new EC2Scale();
$EC2S->checkQueueForThreshold();

?>