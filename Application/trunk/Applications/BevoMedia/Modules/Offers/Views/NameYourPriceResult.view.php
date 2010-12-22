<div id="pagemenu">
	
</div>

<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page ?>



	<div style="text-align: center;">

	<?php echo $this->NetworkName?> has been elected the winning bidder. A Bevo Representative will email you to
	confirm your negotiated payout and get you in touch with your <?php echo $this->NetworkName?>
	Representative. This process can take up 24 hours.
	
	<br /><br />
	
	<img class="nwpic small" src="/Themes/BevoMedia/img/networklogos/uni/<?php echo $this->AffNetworkID ?>.png" /><br /><br />
	
	<a href="/BevoMedia/Publisher/ApplyAdd.html?network=<?php echo $this->AffNetworkID?>" rel="shadowbox;width=640;height=480;player=iframe" >Apply/Add this network</a>
	
	</div>
	