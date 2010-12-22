<?php
session_start();

$Temp = (realpath(substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . '..' ));
include($Temp . DIRECTORY_SEPARATOR . 'Applications' . DIRECTORY_SEPARATOR . 'BevoMedia'. DIRECTORY_SEPARATOR .'Common' . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');

//CHANGED
require_once(PATH.'AbsoluteIncludeHelper.include.php');
require_once(PATH.'Legacy.Abstraction.class.php');

//include(PATH . 'include' . DIRECTORY_SEPARATOR . 'include.php');

?>
