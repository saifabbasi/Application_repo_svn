<h2 class='adminPageHeading floatRight'>Network Stats (Totals)</h2>

<a href='NetworkStatsAverages.html'>Averages View</a>
&nbsp;&nbsp;|&nbsp;&nbsp;
<a href='NetworkStatsCollapse.html'>Collapse View</a>

 
<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr class='textAlignRight'>
	<th class='textAlignLeft'>
		Network 
	</th>
	<th>
		Impressions
	</th>
	<th>
		Clicks
	</th>
	<th>
		Cost
	</th>
	<?php /*?>
	<th class='textAlignCenter'>

	</th>
	<th class='textAlignCenter'>

	</th>
	<th class='textAlignCenter'>

	</th>
	<?php */?>
</tr>

<?php 
	$TotalImpressions = 0;
	$TotalClicks = 0;
	$TotalCost = 0;
?>
<?php foreach($this->Results as $Key=>$Result):?>
<?php 
	$TotalImpressions += $Result->stats->Total->impressions;
	$TotalClicks += $Result->stats->Total->clicks;
	$TotalCost += $Result->stats->Total->cost;
?>

<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<?php /*?><a href='#ViewPublisher.html?id=<?php print $Result->helperID; ?>'><?php */?>
			<?php print $Result->name; ?>
		<?php /*?></a><?php */?>
	</td>
	<td>
		<?php print number_format($Result->stats->Total->impressions, 0, '.', ','); ?>
	</td>
	<td>
		<?php print number_format($Result->stats->Total->clicks, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($Result->stats->Total->cost, 2, '.', ','); ?>
	</td>
	<?php /*?>
	<td colspan='3' class='textAlignCenter'>
		<a href='PublisherStatsDetail.html?id=<?php print $Result->helperID; ?>'>
			View Network Stats
		</a>
	</td>
	<?php */?>
</tr>
<?php endforeach?>

<tr class='splitHeadingRow fontWeightBold'>
	<td class='textAlignRight'>
		<b>Total</b>
	</td>
	<td class='textAlignRight'>
		<?php echo number_format($TotalImpressions, 0, '.', ',')?>
	</td>
	<td class='textAlignRight'>
		<?php echo number_format($TotalClicks, 0, '.', ',')?>
	</td>
	<td class='textAlignRight'>
		$<?php echo number_format($TotalCost, 2, '.', ',')?>
	</td>
	<?php /*?>
	<td>
		<a href='#'>
		</a>
	</td>
	<td>
		<a href='#'>
		</a>
	</td>
	<td>
		<a href='#'>
		</a>
	</td>
	<?php */?>
</tr>
</table>

