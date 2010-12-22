<?php
// phpBB 3.0.x auto-generated configuration file
// Do not change anything in this file!


$Dir = getcwd();
$Dir = substr($Dir, 0, strpos($Dir, "phpBB3"));

$Config = parse_ini_file($Dir.'config.ini', true);

$Mode = $Config['Application']['Mode'];
$DbInfo = $Config['Database/'.$Mode];


$dbms = 'mysql';
$dbhost = $DbInfo['Host'];
$dbport = $DbInfo['Port'];
$dbname = $DbInfo['Name'];
$dbuser = $DbInfo['User'];
$dbpasswd = $DbInfo['Pass'];
$table_prefix = 'phpbb_';
$acm_type = 'file';
$load_extensions = '';

@define('PHPBB_INSTALLED', true);
// @define('DEBUG', true);
// @define('DEBUG_EXTRA', true);
?>