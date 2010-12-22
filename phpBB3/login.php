<?

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

//print_r($user->data);

$user->session_begin();
//$auth->login("imitrikeski@rcs.us", "a", true, 1, 0);


$user_row = array(
					'username'				=> 'aaa',
					'user_password'			=> phpbb_hash("aaa"),
					'user_email'			=> 'test@test.com',
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

				if ($config['new_member_post_limit'])
				{
					$user_row['user_new'] = 1;
				}

				// Register user...
				$user_id = user_add($user_row, $cp_data);
				
				
			

//header("Location: index.php");

