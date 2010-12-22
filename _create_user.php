<?
	
	if (!isset($_GET['Username']) || !isset($_GET['Email']))
	{
		return false;
	}
	
	
	define('AVOID_AUTOLOGIN', true);
	
	
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
		
	define('IN_PHPBB', true);
	$phpbb_root_path = 'phpBB3/';
	$phpEx = 'php';
	include($phpbb_root_path . 'common.' . $phpEx);
	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
	include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
	
	$Username = $_GET['Username'];
	$Email = $_GET['Email'];
	
	
	
	$user_row = array(
		'username'				=> $Username,
		'user_password'			=> phpbb_hash("aaa"),
		'user_email'			=> $Email,
		'group_id'				=> (int) 2,
		'user_timezone'			=> (float) -5,
		'user_dst'				=> 0,
		'user_lang'				=> 'en',
		'user_type'				=> 0,
		'user_actkey'			=> '',
		'user_ip'				=> '',
		'user_regdate'			=> time(),
		'user_inactive_reason'	=> 0,
		'user_inactive_time'	=> 0,
	);

	
	// Register user...
	$user_id = user_add($user_row, $cp_data);
	
	

?>