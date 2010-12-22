<?php

$intLoginID = '';
if (isset($_SESSION['Admin']))
	$intLoginID = $_SESSION['Admin'];

if (!is_numeric($intLoginID)) {
	if (strpos($_SERVER['SCRIPT_NAME'], 'login.php') === false) {
		header('Location: login.php?Ref=' . urlencode($_SERVER['REQUEST_URI']));
	}
}

?>