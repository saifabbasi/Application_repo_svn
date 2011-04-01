<?php

require('include.php');

unset($_SESSION['LoginID']);
header('Location: login.php');

?>