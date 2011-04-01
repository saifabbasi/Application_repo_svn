<?php 
$Temp = (realpath(substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . '..' ));
require_once($Temp . DIRECTORY_SEPARATOR . 'Applications/BevoMedia/Common/AbsoluteIncludeHelper.include.php');

function analytics_import_update($User_ID)
{
	$imp = new Analytics_Import(true, $User_ID);
	$imp->ProcessImport();
}
?>