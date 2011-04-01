<img border="0" width="175" alt="" align="left" style="margin:5px;" src="/Themes/BevoMedia/img/<?php print $this->ProviderImage; ?>">

<h2>Keyword Tracker Optimizer - AdGroup</h2>
<h5>Optimize Ads to Properly Gather Stats that use the BevoMedia Tracker<br/>
	for the Ad Variations in the "<?php print $this->AdGroupName; ?>" AdGroup.<br/>
	<br/>
	
	<div class='textAlignRight'>
	<?php if($this->ProviderType == '1'):?>
		<a href='/BevoMedia/Publisher/AdwordsAdURLFormat.html'>Adwords Campaigns</a> | 
	<?php endif?>
	<?php if($this->ProviderType == '3'):?>
		<a href='/BevoMedia/Publisher/MSNAdURLFormat.html'>MSN Campaigns</a> | 
	<?php endif?>
	
		<a rel="shadowbox;width=640;height=480;player=iframe" href='/BevoMedia/API/OptimizeAdGroupAds.html?ID=<?php echo $_GET['ID']?>'>Optimize All Ads in this AdGroup Now</a>
	</div>
</h5>

<br class="clearBoth">

<table cellpadding=0 cellspacing=0 class='adminPublisherTable'>
<tr>
	<th>Campaign</th>
	<th>AdGroup</th>
	<th class='textAlignRight'></th>
</tr>
<tr class='lightBlueRow'>
	<th><?php print $this->CampaignName; ?></th>
	<th class='nameCell'><?php print $this->AdGroupName; ?></th>
	<th class='textAlignRight'><?php print @count($this->ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized']); ?> Ads Need Optimization</th>
</tr>
</table>

<table cellpadding=0 cellspacing=0 class='adminPublisherTable'>
<?php foreach($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized'] as $Ad):?>
<tr>
<td>
	<?php
		$lineOne = stripslashes($Ad->description);
		$lineTwo = false;
	?>
	<p style="width: 250px; margin: 10px; line-height: 1.231; font-family: Arial; font-size: 13px;">
		<a style="text-decoration: underline; font-size: 123.1%; color: rgb(0, 0, 222);" href="<?php print $Ad->url; ?>"><?php print $Ad->title; ?></a>
		<br>
		<span><?php echo $lineOne?></span><br>
		<?php if($lineTwo):?>
			<span><?php echo $lineTwo?></span><br>
		<?php endif?>
		<span style="color: rgb(0, 128, 0);"><span style="font-weight: bolder;"><?php print $Ad->displayUrl; ?></span></span>
	</p>
</td>
<td class='textAlignLeft width100Pct'>
	<b>Current URL:</b><br/>
		<input class='width100Pct' type='text' value='<?php print $Ad->url; ?>'>
	<b>Optimized URL:</b><br/>
		<input class='width100Pct' type='text' value='<?php print $Ad->optimizedUrl; ?>'>
	<br/>
	
	<a href='/BevoMedia/API/OptimizeSingleAd.html?ID=<?php print $Ad->id; ?>' rel='shadowbox;width=640;height=240;player=iframe'>Optimize this Ad</a>
</td>
</tr>
<?php endforeach?>

</table>