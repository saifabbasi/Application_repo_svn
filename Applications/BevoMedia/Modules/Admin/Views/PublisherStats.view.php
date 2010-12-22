<h2 class='adminPageHeading floatRight'>Publisher Stats (Totals)</h2>

<a href='PublisherStatsCollapse.html'>Collapse View</a>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr class='textAlignRight'>
	<th class='textAlignLeft'>
		Publisher 
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
	<th class='textAlignCenter'>

	</th>
	<th class='textAlignCenter'>

	</th>
	<th class='textAlignCenter'>

	</th>
</tr>

<?php 
	$TotalImpressions = 0;
	$TotalClicks = 0;
	$TotalCost = 0;
?>
<?php foreach($this->Results as $Key=>$Result):?>
<?php 
	$TotalImpressions += $Result->Stats->Total->Impressions;
	$TotalClicks += $Result->Stats->Total->Clicks;
	$TotalCost += $Result->Stats->Total->Cost;
?>

<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<a href='ViewPublisher.html?id=<?php print $Result->id; ?>'><?php print $Result->getUserName(); ?></a>
	</td>
	<td>
		<?php print number_format($Result->Stats->Total->Impressions, 0, '.', ','); ?>
	</td>
	<td>
		<?php print number_format($Result->Stats->Total->Clicks, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($Result->Stats->Total->Cost, 2, '.', ','); ?>
	</td>
	<td colspan='3' class='textAlignCenter'>
		<a href='PublisherStatsDetail.html?id=<?php print $Result->id; ?>'>
			View User's Stats
		</a>
	</td>
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
</tr>
</table>


