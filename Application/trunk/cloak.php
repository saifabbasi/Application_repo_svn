<?php 
	$config = parse_ini_file('config.ini', true);
	
	date_default_timezone_set ("America/New_York");
    /* MySQL Database */
    define('DATABASE_HOST', $config['Database/Production']['Host']);
    define('DATABASE_NAME', $config['Database/Production']['Name']);
    define('DATABASE_USER', $config['Database/Production']['User']);
    define('DATABASE_PASS', $config['Database/Production']['Pass']);
    require_once ('Applications/BevoMedia/Common/database.php');

	
	if(!isset($_GET['id'])) die();
	$cloakId = $_GET['id']; 
	$cookie = '';
	if(isset($_GET['cookie']))
		$cookie = $_GET['cookie'];
	$con = new SqlConnection();
	$con->open();
	error_reporting(E_ALL);
	
	$cmd = new SqlCommand("SELECT destination FROM bevomedia_cloak WHERE publicId = @publicId", $con);
	$cmd->parameters['publicId'] = $cloakId;
	$cmd->execute();
	$Row = $cmd->getRow();
	if(!isset($Row['destination']))
		$redirect_site_url = false;
	else
		$redirect_site_url = $Row['destination'];
	if(substr($redirect_site_url,0, 6) == 'ROTATE')
	{
        $rotate_id = explode('.',$redirect_site_url);
        $cmd = new SqlCommand("SELECT * FROM bevomedia_offer_rotator_link WHERE groupId = @groupId AND deleted=0 ORDER BY Ratio DESC", $con);
        $cmd->parameters['groupId'] = intval($rotate_id[1]);
		$rows = array();
		$cmd->execute();
        while($row = $cmd->getRow())
        {
	        $rows[] = $row;
        }
        $possible = array();
        foreach($rows as $row)
        {
                for($i=0; $i<$row['ratio']; $i++)
                {
                        $possible[] = $row['link'];
                }
        }
        $random = rand(1,sizeof($possible)) - 1;
        $redirect_site_url = $possible[$random];
	}else{

		$redirect_site_url .=  $cookie;
	}
?>

<html>
	<head>
		<title>Redirect</title>
		<meta name="robots" content="noindex">
		<meta http-equiv="refresh" content="0; url=<? echo $redirect_site_url; ?>">
	</head>
	<body>
	
		<form name="form1" id="form1" method="get" action="/redirect.php">
			<input type="hidden" name="q" value="<? echo $redirect_site_url; ?>"/>
		</form>
		<script type="text/javascript">
			document.form1.submit();
		</script>
		
		
		<div style="padding: 30px; text-align: center;">
			You are being automatically redirected.<br/><br/>
			Page Stuck? <a href="<? echo $redirect_site_url; ?>">Click Here</a>.
		</div>
	</body> 
</html> 