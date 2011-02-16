<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	$db = Zend_Registry::get('Instance/DatabaseObj');
	$query = "SELECT u.*, n.*, u.STATUS FROM bevomedia_aff_network AS n LEFT JOIN bevomedia_user_aff_network AS u ON n.ID = u.NETWORK__ID AND u.USER__ID = {$this->User->id} WHERE n.ID = '$_GET[network]' AND n.ISVALID = 'Y' ORDER BY n.TITLE";
	$this->Network = $db->fetchRow($query);


?>
<title><?php print htmlentities($this->network->TITLE); ; ?> Network Application - BeVo Media</title>
<link rel="stylesheet" href="/Themes/BevoMedia/bevobar.style.css" type="text/css" media="screen" />
</head>
<body>



<table width=100% height=100%>
	<tr>
		<td >

<div id="bevobar">
	<div id="bevobar_bg"></div>
	<a class="button bevologo" href="https://www.bevomedia.com/">BeVo Media Account Overview</a>
	<form name="addnetwork" id="addnetwork" method="post">
		<div class="row">
		

			<input type="submit" class="formsubmit" name="submit" value="Add this network" onclick="window.location = '/BevoMedia/Publisher/Index.html?ID=<?=$_GET[network]?>'; return false;" style="margin-top: 1px" />
		</div>
	</form>
	<a class="button bevohome" href="https://www.bevomedia.com/">Account Home</a>
</div>

<div id="bevonotice">
	<p><img class="icon" src="/Themes/BevoMedia/img/icon_notice.gif" alt="alert" />To guarantee quality tracking, it is recommended to create a seperate account with this network solely for your BeVo Media Interface, even though you may already be an active publisher!</p>
</div>

		</td>
	</tr>
	<tr>
		<td style="height: 100%;">

	<iframe id="bevowrap" name="bevowrap" width="100%" height="100%" scrolling="yes" frameborder="0" src="<?php print htmlentities($this->Network->signupUrl); ; ?>" >
		<p style="text-align:center; margin:100px;">Something went wrong while trying to access the network website. Please <a href="https://www.bevomedia.com/welcome.php">go back</a> and try again.</p>
	</iframe>

		</td>
	</tr>
</table>
<!-- 
<iframe id="bevowrap" src="<?php print htmlentities($this->Network->signupUrl); ; ?>" name="bevowrap" width="100%" height="auto" scrolling="yes" frameborder="0">
	<p style="text-align:center; margin:100px;">Something went wrong while trying to access the network website. Please <a href="https://www.bevomedia.com/welcome.php">go back</a> and try again.</p>
</iframe>
 -->
</body>
</html>
<?php
exit;