<?php 
    if ( ($this->User->getVaultID()==0) && (Zend_Registry::get('Instance/Function')!='AddCreditCard') && (!$this->User->IsSubscribed(User::PRODUCT_INSTALL_NETWORKS)) && ($this->User->membershipType!='premium') )
	{
		echo '<script type="text/javascript"> window.location = "/BevoMedia/Publisher/Verify.html?ajax=true"; </script>';
		die;
	}		
?>

<div class="wrap">
<script language="javascript" src="/Themes/BevoMedia/jquery.js"></script>
<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>
<style type="text/css">
#tooltip{
	line-height: 1.231; font-family: Arial; font-size: 13px;
	position:absolute;
	border:1px solid #333;
	background:#f7f5d1;
	padding:2px 5px;
	display:none;
	width:285px;
	margin-left: -330px;
	}
.tooltip {
	color: #ffffff;
	text-decoration: none !important;
	font-weight: bold;
	font-size: 12pt;
	}
.tooltip.defaultLink {
	color: maroon;
	font-size: 12px;
	font-style: normal;
	font-weight: normal;
	font-size: 12px;
	}
.successInstall {
	background-color: #008800;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
.failInstall {
	background-color: #880000;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
</style>



<h1 style='text-align:center;'><img src="/Themes/BevoMedia/img/networklogos/Large/<?php print $this->Network->id?>.png" alt="<?php print htmlentities($this->Network->title)?>" /></h1>

<?php if(isset($this->message)): ?>
<p class="updated"><?php print htmlentities($this->message); ; ?></p>
<script type="text/javascript">
	window.setTimeout('closeThis()', 1500);

	function closeThis()
	{
		parent.Shadowbox.close();
	}
</script>
<?php endif; ?>



<form method="post">

<table class="dataentry">
<pre>
	<?php if($this->Network->userIdLabel != ''): ?>
	<tr>
		<th><?php print htmlentities($this->Network->userIdLabel)?></th>
		<td>
			<input type="text" name="loginid" size="35" value="<?php print htmlentities($this->UserNetwork->loginId) ?>" />
			<?php if(in_array($this->Network->title, array('ClickBooth', 'Copeac', 'FluxAds', 'ROIRocket', 'XY7', 'CommissionEmpire', 'Rextopia', 'Wotogepa', 'Market Leverage', 'Adfinity', 'AdEx', 'BlinkAds', 'EWA', 'Epicenter', 'FireLead'))):?>
				<br/>
				<i>Your <?php print htmlentities($this->Network->userIdLabel); ?> is not your email address.  
				<?php if(in_array($this->Network->title, array('ClickBooth', 'Copeac', 'FluxAds', 'ROIRocket', 'XY7', 'CommissionEmpire', 'Rextopia', 'Wotogepa', 'Market Leverage'))) {?>
				<br/>Ex: CD1234</i>
				<?php } else { ?>
				<br/>Ex: 12345</i>
				<?php } ?>
			<?php endif?>
		</td>
	</tr>
	
	<?php endif; ?>
	<?php if ( ($this->Network->passwordLabel != '') && (!in_array($this->Network->id, array(1028))) ): ?>
	<tr>
		<th><?php print htmlentities($this->Network->passwordLabel); ; ?></th>
		<td>
			<input type="password" name="password" size="35" value="<?php print htmlentities($this->UserNetwork->password) ?>" />
		</td>
	</tr>
	<?php endif; ?>
	<?php if($this->Network->otherIdLabel != ''): ?>
	<tr>
		<th><?php print htmlentities($this->Network->otherIdLabel); ; ?></th>
		<td>
			<input type="text" name="otherid" size="35" value="<?php print htmlentities($this->UserNetwork->otherId); ; ?>" />
		  <?php if (($this->Network->otherIdLabel == 'API Key') && (($this->Network->id==1062) || ($this->Network->id==1059)) ) { ?>
			<a class="tooltip" title="You must enter your API Key for this network to access your stats. To retrieve your API key, login to the network and go to the &quot;Support -&gt; Stats API&quot; Page. Then click on ">
				<img src="/Themes/BevoMedia/img/questionMarkIcon.png" width="12" height="12" />
			</a>
		  <? } else { ?>
		  	<a class="tooltip" title="You must enter your API Key for this network to access your stats. To retrieve your API key, login to the network and go to the &quot;Reports&quot; Page. Then select the API tool and copy your unique API Key">
				<img src="/Themes/BevoMedia/img/questionMarkIcon.png" width="12" height="12" />
			</a>
		  <? } ?>
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<th></th>
		<td>
			<input type="image" src="/Themes/BevoMedia/img/savechanges-blue-big.jpg" value="Save Changes" />
<?php

	if ($this->Network->title =='ClickBank')
	{
?>
			<div align='center' style="font-size: 10px; text-align: left;">
				*Clickbank stats update once a day at 3:30am
			</div>
<?
	}

?>

		</td>
	</tr>
</table>

<?php
if(file_exists(dirname(__FILE__).'/Snippets/NetworkInstructions/'.$this->Network->id.'.html'))
	include(dirname(__FILE__).'/Snippets/NetworkInstructions/'.$this->Network->id.'.html');
?>

</form>

</div>
<a onclick="return confirm('Are you sure you want to delete this network? This cannot be undone!');" href="EditNetwork.html?delete=<?=$this->Network->id?>">Remove this affiliate network</a><br />
<? if(!empty($_GET['apiKey'])) { ?>
  <br />
  <br />
  <h3>Add this network to my BevoLive account: <?=$this->User->email?></h3>
  You are entering these details on your Bevo Live account.<br />Changes will take up to an hour to appear in your selfhost version.

<? } ?>