<h2 class='adminPageHeading floatRight'>Network Stats (Averages)</h2>

<a href='NetworkStats.html'>Totals View</a>

 
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
	$TotalImpressions += $Result->stats->Total->Impressions;
	$TotalClicks += $Result->stats->Total->Clicks;
	$TotalCost += $Result->stats->Total->Cost;
?>

<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<?php print $Result->name; ?>
	</td>
	<td>
		~<?php print number_format($Result->stats->Total->Impressions / sizeOf($Result->Accounts), 0, '.', ','); ?>
		[<?php print number_format($Result->stats->Total->Impressions, 0, '.', ','); ?>]
	</td>
	<td>
		~<?php print number_format($Result->stats->Total->Clicks / sizeOf($Result->Accounts), 0, '.', ','); ?>
		[<?php print number_format($Result->stats->Total->Clicks, 0, '.', ','); ?>]
	</td>
	<td>
		~$<?php print number_format($Result->stats->Total->Cost / sizeOf($Result->Accounts), 2, '.', ','); ?>
		[$<?php print number_format($Result->stats->Total->Cost, 2, '.', ','); ?>]
	</td>
	<td colspan='3' class='textAlignCenter'>
		<?php print sizeOf($Result->accounts); ?> Accounts
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


