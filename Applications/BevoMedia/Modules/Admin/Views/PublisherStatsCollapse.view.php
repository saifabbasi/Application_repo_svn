<h2 class='adminPageHeading floatRight'>Publisher Stats (Collapse)</h2>

<a href='PublisherStats.html'>Totals View</a>

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

<?php if($Result->Stats->Total->Impressions == 0 && $Result->Stats->Total->Clicks == 0 && $Result->Stats->Total->Cost == 0):?>
<tr class='textAlignRight <?php echo(1)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<a href='ViewPublisher.html?id=<?php print $Result->id; ?>'><?php print $Result->getUserName(); ?></a>
	</td>
	<td>
		0
	</td>
	<td>
		0
	</td>
	<td>
		$0.00
	</td>
	<td colspan='3'>
	
	</td>
</tr>
<?php else:?>
<tr class='textAlignRight <?php echo(1)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<a href='ViewPublisher.html?id=<?php print $Result->id; ?>'><?php print $Result->getUserName(); ?></a>
	</td>
	<td colspan='3'>

	</td>
	<td colspan='3' class='textAlignCenter'>
		<a href='PublisherStatsDetail.html?id=<?php print $Result->id; ?>'>View User's Stats</a>
	</td>
</tr>
<tr class='textAlignRight <?php echo(0)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignRight fontWeightBold'>
		Google Adwords
	</td>
	<td>
		<?php print number_format($Result->Stats->AdwordsTotal->Impressions, 0, '.', ','); ?>
	</td>
	<td>
		<?php print number_format($Result->Stats->AdwordsTotal->Clicks, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($Result->Stats->AdwordsTotal->Cost, 2, '.', ','); ?>
	</td>
	<td colspan='3' class='textAlignCenter'>
		<?php print sizeOf($Result->getAllAccountsAdwords()); ?> Accounts
	</td>
</tr>
<tr class='textAlignRight <?php echo(0)?'lightBlueRow':'darkBlueRow'?> '>

	<td class='textAlignRight fontWeightBold'>
		Yahoo Search Marketing
	</td>
	<td>
		<?php print number_format($Result->Stats->YahooTotal->Impressions, 0, '.', ','); ?>
	</td>
	<td>
		<?php print number_format($Result->Stats->YahooTotal->Clicks, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($Result->Stats->YahooTotal->Cost, 2, '.', ','); ?>
	</td>
	<td colspan='3' class='textAlignCenter'>
		<?php print sizeOf($Result->getAllAccountsYahoo()); ?> Accounts
	</td>
</tr>
<tr class='textAlignRight <?php echo(0)?'lightBlueRow':'darkBlueRow'?> '>

	<td class='textAlignRight fontWeightBold'>
		MSN AdCenter
	</td>
	<td>
		<?php print number_format($Result->Stats->MSNTotal->Impressions, 0, '.', ','); ?>
	</td>
	<td>
		<?php print number_format($Result->Stats->MSNTotal->Clicks, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($Result->Stats->MSNTotal->Cost, 2, '.', ','); ?>
	</td>
	<td colspan='3' class='textAlignCenter'>
		<?php print sizeOf($Result->getAllAccountsMSN()); ?> Accounts
	</td>
</tr>
<?php endif?>
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


