<?php
function genRandomString($length = 10) {
    $characters = "23456789abcdefghjkmnpqrstuvwxyz";
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters)-1)];
    }
    return $string;
}
$user = @$_GET['user'];
$key = @$_GET['key'];
$cmd = @$_GET['cmd'];
$size = @$_GET['size'];
if($cmd && in_array($cmd, array('start', 'status', 'setup')))
{
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	set_time_limit(0);
	ignore_user_abort(true);
	if($cmd == 'start')
	{
		$cmd = "python /home/linked/bevo/tools/rackspace-start.py $user $key $size";
		$out = exec($cmd);
		$s = explode(" ", $out);
		if($s[0] == 'OK!')
		{
			$ip = $s[1];
			$server_pass = $s[2];
			$next = "RackspaceLaunch.html?user=$user&key=$key&ip=$ip&server_pass=$server_pass&cmd=status";
			echo '$("#t").html("Starting the server...");';
			echo '$("#extra").html("Your servers IP is '.$ip.'");';
			echo '$.getScript("'.$next.'")';
		} else {
			echo '$("#t").html("Error launching node, please contact Bevo Support");$("#wait").hide()';
		}
	} elseif($cmd == 'status') {
		$ip = @$_GET['ip'];
		$server_pass = @$_GET['server_pass'];
		$cmd = "python /home/linked/bevo/tools/rackspace-check-node.py $user $key $ip";
		$out = exec($cmd);
		if($out == '0')
		{
			$next = "RackspaceLaunch.html?user=$user&key=$key&ip=$ip&server_pass=$server_pass&cmd=setup";
			echo '$("#t").html("Configuring your server..."); ';
			echo '$.getScript("'.$next.'")';
		} else {
			$next = "RackspaceLaunch.html?user=$user&key=$key&ip=$ip&server_pass=$server_pass&cmd=status";
			echo '$("#t").html("Waiting for your server to start... This takes up to 10 minutes...");';
			echo 'function getstatus() { ';
			echo '  $.getScript("'.$next.'")';
			echo '}';
			echo 'setTimeout(getstatus, 15000);';
		}
	} elseif($cmd == 'setup') {
		$ip = @$_GET['ip'];		
		$server_pass = @$_GET['server_pass'];
		$root_pass = genRandomString(12);
		$dbuser = 'bevo_'.genRandomString(4);
		$dbpass = 'bevo_'.genRandomString(4);
		$dbname = 'bevo_db_'.genRandomString(2);
		$cmd = "python /home/linked/bevo/tools/rackspace-setup.py $user $key $ip $server_pass $root_pass $dbuser $dbpass $dbname";
		$out = exec($cmd);
		echo <<<EOF
$("#t").html("Complete!");
$("#wait").hide();
link=$("<a>");
link.attr("href", "$out");
link.attr("target", "_blank");
link.html("Click here to finish the setup process");
$("#extra").html(link);
EOF;
		$Message = "
Your Rackspace Server is now configured with BevoMedia Selfhost!<br />
You need to finish the installer here:<br />
<a href='$out'>$out</a><br />
<br />
And then you can log in here:<br />
<a href='http://$ip'>http://$ip</a><br />
<br />
Here are some server details you shouldn't lose:<br />
IP: $ip<br />
Server Root Password: $server_pass<br />
MySQL Root Password: $root_pass<br />
MySQL Bevo User: $dbuser<br />
MySQL Bevo Password: $dbpass<br />
MySQL Bevo Database: $dbname<br />
<br />
Thanks for using Rackspace and Bevo!<br />
-The Bevo Team<br /><br />
";
		$MailComponentObject = new MailComponent();
		$MailComponentObject->setFrom('no-reply@bevomedia.com');
		
		$MailComponentObject->setSubject('Your Bevo Selfhost Rackspace Instance');
		$MailComponentObject->setHTML($Message);
		$MailComponentObject->send(array($this->User->email));
			
	}
	exit;
}
?>
<html>
<head>
<style type="text/css">
<!--
	body { width:640px; background:url(/Themes/BevoMedia/img/rack-apiloaderpop.jpg) #ffffff no-repeat; text-align:center; font-family:Helvetica, Arial, sans-serif; padding:0; margin:0; overflow-x: hidden}
	h2 { margin:240px 0 0; color:#E01A2C; font-weight:bold; letter-spacing:-.05em; font-size:23px; }
	p { margin:10px 0 0; font-size:16px; color:#0071bc; }
	img { margin:50px auto; display:block; }
-->

</style>
<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>
</head>
<body>
	<h2 id="t">Launching your instance, this can take a few minutes...</h2>
	<img src="/Themes/BevoMedia/img/rack-loader.gif" id="wait" alt="" />
	<span id="extra"></span><br />
	<p width="90%"><b>DO NOT</b> refresh the browser. <b>DO NOT</b> close this window.<br />
	If you close this window, or the installer fails, you need to log into Rackspace and shut down the node we just launched, <b>or you will be billed for extra servers.</b>
<script>
$(function() { 
	$.getScript('RackspaceLaunch.html?user=<?= $user ?>&key=<?= $key ?>&size=<?= $size ?>&cmd=start');
});

</script>
</body></html>