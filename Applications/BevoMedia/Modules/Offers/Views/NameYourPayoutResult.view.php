<div id="pagemenu">
	<ul>
<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
		<li><a href="/BevoMedia/Offers/Index.html">Offers<span></span></a></li>
<?php 
	}
?>
		<li><a href="/BevoMedia/Offers/NameYourPayout.html">Name Your Payout<span></span></a></li>
	</ul>
	<ul class="floatright">
		<li><a class="active" href="#">Your Results<span></span></a></li>
	</ul>
</div>

<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page 
?>

<?php 
	if (!isset($_COOKIE['v3apps']))
	{
?>
<div class="pagecontent nyp aligncenter">
	<h3>Success!</h3>
	<p>We have found a match for your desired payout. The winning network is <?php echo $this->NetworkName?>:</p>
	<br />
	
	<a href="/BevoMedia/Publisher/ApplyAdd.html?network=<?php echo $this->AffNetworkID?>" rel="shadowbox;width=640;height=480;player=iframe" >
		<img class="nwpic" src="/Themes/BevoMedia/img/networklogos/uni/<?php echo $this->AffNetworkID ?>.png" />
	</a>
	<a class="btn nw_applyadd" href="/BevoMedia/Publisher/ApplyAdd.html?network=<?php echo $this->AffNetworkID?>" rel="shadowbox;width=640;height=480;player=iframe" >Apply/add</a>
	<a href="/BevoMedia/Publisher/ApplyAdd.html?network=<?php echo $this->AffNetworkID?>" rel="shadowbox;width=640;height=480;player=iframe" >Click to join this network now</a>
	<br /><br />
	
	<p>A Bevo representative will email you to confirm your negotiated payout and get you in touch with your <?php echo $this->NetworkName?> representative. This process can take up to 24 hours.</p>
</div>
<?php 
	} else
	{
?>
<div class="pagecontent nyp aligncenter">
	<h3>Success!</h3>
	<p>We have found a match for your desired payout. The winning network is <?php echo $this->NetworkName?>:</p>
	<br />
	
	<a href="http://<?php echo $_COOKIE['v3domain']; ?>/user_affiliate_network/edit/network/<?php echo $this->AffNetworkID?>" target="_blank">
		<img class="nwpic" src="/Themes/BevoMedia/img/networklogos/uni/<?php echo $this->AffNetworkID ?>.png" />
	</a>
	<a class="btn nw_applyadd" href="http://<?php echo $_COOKIE['v3domain']; ?>/user_affiliate_network/edit/network/<?php echo $this->AffNetworkID?>" target="_blank" >Apply/add</a>
	<a href="http://<?php echo $_COOKIE['v3domain']; ?>/user_affiliate_network/edit/network/<?php echo $this->AffNetworkID?>" target="_blank" >Click to join this network now</a>
	<br /><br />
	
	<p>A Bevo representative will email you to confirm your negotiated payout and get you in touch with your <?php echo $this->NetworkName?> representative. This process can take up to 24 hours.</p>
</div>
<?php 
	}
?>