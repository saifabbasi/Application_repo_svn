<h2 class='adminPageHeading floatRight'>Network Stats (Collapse)</h2>

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

<tr class='textAlignRight <?php echo(1)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<?php print $Result->name; ?>
	</td>
	<td>
		<?php //echo number_format($Result->Stats->Total->Impressions, 0, '.', ',')?>
	</td>
	<td>
		<?php //echo number_format($Result->Stats->Total->Clicks, 0, '.', ',')?>
	</td>
	<td>
		<?php //echo number_format($Result->Stats->Total->Cost, 2, '.', ',')?>
	</td>
	<td colspan='3' class='textAlignCenter'>
	
	</td>
</tr>
	<?php foreach($Result->Accounts as $Account):?>
	<tr class='textAlignRight <?php echo(0)?'lightBlueRow':'darkBlueRow'?> '>
		<td class='textAlignRight'>
			<?php print $Account->username; ?>
		</td>
		<td>
			<?php print number_format($Result->stats->GetPPCStats($Result->helperId, $Account->id)->Impressions, 0, '.', ','); ?>
		</td>
		<td>
			<?php print number_format($Result->stats->GetPPCStats($Result->helperId, $Account->id)->Clicks, 0, '.', ','); ?>
		</td>
		<td>
			$<?php print number_format($Result->stats->GetPPCStats($Result->helperId, $Account->id)->Cost, 2, '.', ','); ?>
		</td>
		<td colspan='3' class='textAlignCenter'>
			<a href='PublisherStatsDetail.html?id=<?php print $Account->userId; ?>' title='<?php $T = new User($Account->userId); echo $T->GetUserName()?>'>
				View User's Stats
			</a>
		</td>
	</tr>
	<?php endforeach?>
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


