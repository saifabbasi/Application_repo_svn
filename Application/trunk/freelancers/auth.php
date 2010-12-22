<?php
//session_start();

$intLoginID = '';
if (isset($_SESSION['LoginID']))
	$intLoginID = $_SESSION['LoginID'];

if (!is_numeric($intLoginID)) 
{
	if (strpos($_SERVER['SCRIPT_NAME'], 'login.php') === false) {
		header('Location: login.php?Ref=' . urlencode($_SERVER['REQUEST_URI']));
	}
}

?>