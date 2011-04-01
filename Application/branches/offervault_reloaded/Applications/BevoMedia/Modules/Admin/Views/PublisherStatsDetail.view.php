<h2 class='adminPageHeading floatRight'>Publisher Stats Detail for <?php print $this->User->GetUserName(); ?></h2>

<!--
<a href='PublisherStats.html'>Back to All Publishers</a>
-->

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

<?php $Result = $this->User?>
<?php 
	$TotalImpressions = $Result->Stats->Total->Impressions;
	$TotalClicks = $Result->Stats->Total->Clicks;
	$TotalCost = $Result->Stats->Total->Cost;
?>

<tr class='textAlignRight <?php echo(1)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		Google Adwords
	</td>
	<td colspan='6'>
	
	</td>
</tr>

<?php foreach($this->User->GetAllAccountsAdwords() as $Key=>$Value):?>
<tr class='textAlignRight <?php echo(0)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignRight'>
		<?php print $Value->username; ?>
	</td>
	<td>
		<?php print number_format($this->User->Stats->GetAdwordsStats($Value->id)->Impressions, 0, '.', ','); ?>
	</td>
	<td>
		<?php print number_format($this->User->Stats->GetAdwordsStats($Value->id)->Clicks, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($this->User->Stats->GetAdwordsStats($Value->id)->Cost, 2, '.', ','); ?>
	</td>
	<td colspan='3' class='textAlignCenter'>
		<?php //echo sizeOf($Result->GetAllAccountsAdwords())?> 
	</td>
</tr>
<?php endforeach?>


<tr class='textAlignRight <?php echo(1)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		Yahoo Search Marketing
	</td>
	<td colspan='6'>
	
	</td>
</tr>
<?php foreach($this->User->GetAllAccountsYahoo() as $Key=>$Value):?>
<tr class='textAlignRight <?php echo(0)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignRight'>
		<?php print $Value->username; ?>
	</td>
	<td>
		<?php print number_format($this->User->Stats->GetYahooStats($Value->id)->Impressions, 0, '.', ','); ?>
	</td>
	<td>
		<?php print number_format($this->User->Stats->GetYahooStats($Value->id)->Clicks, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($this->User->Stats->GetYahooStats($Value->id)->Cost, 2, '.', ','); ?>
	</td>
	<td colspan='3' class='textAlignCenter'>
		<?php //echo sizeOf($Result->GetAllAccountsAdwords())?> 
	</td>
</tr>
<?php endforeach?>

<tr class='textAlignRight <?php echo(1)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		MSN AdCenter
	</td>
	<td colspan='6'>
	
	</td>
</tr>
<?php foreach($this->User->GetAllAccountsMSN() as $Key=>$Value):?>
<tr class='textAlignRight <?php echo(0)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignRight'>
		<?php print $Value->username; ?>
	</td>
	<td>
		<?php print number_format($this->User->Stats->GetMSNStats($Value->id)->Impressions, 0, '.', ','); ?>
	</td>
	<td>
		<?php print number_format($this->User->Stats->GetMSNStats($Value->id)->Clicks, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($this->User->Stats->GetMSNStats($Value->id)->Cost, 2, '.', ','); ?>
	</td>
	<td colspan='3' class='textAlignCenter'>
		<?php //echo sizeOf($Result->GetAllAccountsAdwords())?> 
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


