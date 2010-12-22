<img border="0" width="175" alt="" align="left" style="margin:5px;" src="/Themes/BevoMedia/img/adcenter.jpg">

<h2>Keyword Tracker Optimizer</h2>
<h5>Optimize Ads to Properly Gather Stats that use the BevoMedia Tracker<br/>
	for MSN Ad Center Ad Variations<br/>
	<br/>
	<div class='textAlignRight'>
		<a href='#'>Optimize Ads in All Campaigns Now</a>
	</div>
	
</h5>

<br class="clearBoth">

<table cellpadding=0 cellspacing=0 class='adminPublisherTable'>
<tr>
	<th>Campaign</th>
	<th>AdGroup</th>
	<th class='textAlignRight '>Ads Needing Optimization</th>
</tr>


<?php foreach($this->Ads as $CampaignKey=>$Campaign):?>
	<tr class='lightBlueRow'>
		<td class='nameCell'>
			<?php echo $CampaignKey?>
		</td>
		<td class='textAlignRight' colspan='2'>
			<!-- <a href='#'>View All AdGroups</a> -->
		</td>
	</tr>

	<?php foreach($Campaign as $AdGroupKey=>$AdGroup):?>
		<tr class='darkBlueRow'>
			<td class='textAlignCenter'></td>
			<td>
				<a href='AdGroupAdURLFormat.html?ID=<?php print $AdGroup['Rows'][0]->adGroupId; ?>'>
					<?php echo $AdGroupKey?>
				</a>
			</td>
			<td class='textAlignRight nameCell fontWeightNormalForced'>*There are <?php echo @count($AdGroup['NeedToBeOptimized'])?> ads in this Ad Group That are Not Optimized <!-- <span style='font-weight: normal;'> of <?php echo @count($AdGroup['Rows'])?> Total Ads</span> --></td>
		</tr>
	<?php endforeach?>
<?php endforeach?>


</table>