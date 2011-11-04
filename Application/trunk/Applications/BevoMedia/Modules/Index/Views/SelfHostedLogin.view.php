<?php
		$loginerror = isset($_GET['Error']);
		$loginerror = $loginerror ? 'No match! Please enter a correct username and password. Your login attempt has been logged.' : false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex" />

<title>Bevo Media - The Full-Scale Affiliate Management Platform</title>

<link rel="stylesheet" href="<?= $this->{'System/BaseURL'} ?>Themes/BevoMedia/selfhostlogin-style.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?= $this->{'System/BaseURL'} ?>Themes/BevoMedia/jquery.js"></script>
<script type="text/javascript" src="<?= $this->{'System/BaseURL'} ?>Themes/BevoMedia/soap_formaction.js"></script>
</head>
<body>
<div id="wrap">
	<div id="loginform">
		<form method="post" action="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/User/ProcessLogin.html">
		<?php if($loginerror) {
			echo '<div class="formtitle error">Error</div>';
			echo '<div class="msg"><p>'.$loginerror.'</p></div>';
		} else 	echo '<div class="formtitle login">Login</div>'; ?>
			<input type="hidden" name="loginFormSubmit" value="true" />
			<div class="row">
				<label for="username">Username</label>
				<input type="text" class="formtxt" id="username" name="Email" />
			</div>
			<div class="row">
				<label for="pwd">Password</label>
				<input type="password" class="formtxt" id="pwd" name="Password" />
			</div>
			<div class="row">
				<input type="submit" class="formsubmit" value="Login" />
			</div>
		</form>
		<div id="badge_selfhosted">Self-Hosted Version</div>
	</div>
	<div id="bevologo">Bevo Media</div>
</div>
</body>
</html>