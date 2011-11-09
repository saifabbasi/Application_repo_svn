<?php
//print '<pre>';
//print_r($this->TrackerRows);
//print '</pre>';
?>
<div>
<b>Date Range:</b>
<?php print $this->dateRange; ?>
&nbsp;
<i>(<b>StartDate:</b><?php print $this->startDate; ?>, <b>EndDate:</b><?php print $this->endDate; ?>)</i>
<br/>

<b>User ID:</b>
<?php print $this->user->ID; ?>
<br/>

<br/>

<table cellpadding="0" cellspacing="0" >
<tr>
	<th>
		#
	</th>
	<th>
		Click ID
	</th>
	<th>
		Referrer URL
	</th>
	<th>
		Orig Page URL
	</th>
	<th>
		Bid Keyword
	</th>
	<th>
		Raw Keyword
	</th>
	<th>
		Creative ID
	</th>
	<th>
		Click Time
	</th>
	<th>
		SumClick
	</th>
	<th>
		SumRevenue
	</th>
	<th>
		SumConv
	</th>
	<th>
		SumCost
	</th>
</tr>
<?php $totalClicks = $totalConv = $totalClicksAds = $totalCost = 0;?>
<?php foreach($this->TrackerRows as $Key=>$Row):?>

<?php 
	$totalClicks += ($Row->sumClick);
	$totalConv += ($Row->sumConv);
	$totalCost += ($Row->sumCost);
	$totalRevenue += ($Row->sumRevenue);
?>
<tr>
	<td>
		<?php echo $Key?>
	</td>
	<td>
		<?php print $Row->click_id; ?>
	</td>
	<td>
		<p style='word-wrap: break-word; width: 300px; height: 1em; overflow: hidden;'>
			<?php print $Row->referrer_url; ?>
		</p>
	</td>
	<td>
		<p style='word-wrap: break-word; width: 300px; height: 1em; overflow: hidden;'>
			<?php print $Row->orig_page_url; ?>
		</p>
	</td>
	<td>
		<?php print $Row->bidKeyword; ?> (<?php print $Row->bid_keyword_id; ?>)
	</td>
	<td>
		<?php print $Row->rawKeyword; ?> (<?php print $Row->raw_keyword_id; ?>)
	</td>
	<td>
		<div style='border-bottom: dotted 1px #888888;' title='[AccountID:<?php print $Row->accountID; ?>, Provider:<?php print $Row->providerType; ?>] <?php print $Row->campaignName; ?> > <?php print $Row->adGroupName; ?>'><?php print $Row->creative_id; ?></div>
	</td>
	<td>
		<?php print $Row->click_time; ?>
	</td>
	<td style="text-align:center;">
		<?php print $Row->sumClick; ?>
	</td>
	<td style="text-align:center;">
		<?php print $Row->sumRevenue; ?>
	</td>
	<td style="text-align:center;">
		<?php print $Row->sumConv; ?>
	</td>
	<td style="text-align:center;">
		<?php print $Row->sumCost; ?>
	</td>
</tr>
<?php endforeach?>
<tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<th>
		<?php echo $totalClicks?>
	</th>
	<th>
		<?php echo $totalClicksAds?>
	</th>
	<th>
		<?php echo $totalConv?>
	</th>
	<th>
		<?php echo $totalCost?>
	</th>
</tr>
</table>

<!-- STATS ROWS -->

<table style="float:left; margin-right: 100px;">
<tr>
	<th>#</th><th>Keyword</th><th>SumClick</th><th>SumCost</th>
</tr>
<?php $totalClicks = $totalConv = $totalClicksAds = $totalCost = 0;?>
<?php foreach($this->StatRows as $Key=>$Row):?>
<?php 
	$totalClicks += ($Row->sumClick); 
	$totalCost += ($Row->sumCost); 
?>
<tr>
	<td><?php echo $Key?></td>
	<td><?php print $Row->keyword; ?>(<?php print $Row->keywordID; ?>)[<?php print $Row->bidKeywordID; ?>]</td>
	<td style="text-align:center;"><?php print $Row->sumClick; ?></td>
	<td style="text-align:center;"><?php print $Row->sumCost; ?></td>
</tr>
<?php endforeach?>
<tr>
	<td></td>
	<td></td>
	<th><?php echo $totalClicks?></th>
	<th><?php echo $totalCost?></th>
</tr>
</table>

<!-- MODDED STATS ROWS -->

<table style="float:left; margin-right: 100px;">
<tr>
	<th>
		#
	</th>
	<th>
		Keyword
	</th>
	<th>
		SumClick
	</th>
</tr>
<?php $totalClicks = $totalConv = $totalClicksAds = 0;?>
<?php foreach($this->ModStatRows as $Key=>$Row):?>

<?php 
	$totalClicks += ($Row->sumClick); 
?>
<tr>
	<td>
		<?php echo $Key?>
	</td>
	<td>
		<?php print $Row->keyword; ?>
	</td>
	<td style="text-align:center;">
		<?php print $Row->sumClick; ?>
	</td>
</tr>

<?php endforeach?>

<tr>
	<td>
	
	</td>
	<td>
	
	</td>
	<th>
		<?php echo $totalClicks?>
	</th>
</tr>
</table>

<!-- SEARCH QUERY STATS ROWS -->

<table>
<tr>
	<th>
		#
	</th>
	<th>
		Keyword
	</th>
	<th>
		Query
	</th>
	<th>
		SumClick
	</th>
	<th>
		SumCost
	</th>
</tr>
<?php $totalClicks = $totalCost = 0;?>
<?php foreach($this->SearchRows as $Key=>$Row):?>

<?php 
	$totalClicks += ($Row->sumClick); 
	$totalCost += ($Row->sumCost); 
?>
<tr>
	<td>
		<?php echo $Key?>
	</td>
	<td>
		<?php print $Row->keyword; ?>
	</td>
	<td>
		<?php print $Row->query; ?>
	</td>
	<td style="text-align:center;">
		<?php print $Row->sumClick; ?>
	</td>
	<td style="text-align:center;">
		<?php print $Row->sumCost; ?>
	</td>
</tr>

<?php endforeach?>

<tr>
	<td>
	
	</td>
	<td>
	
	</td>
	<td>
	
	</td>
	<th>
		<?php echo $totalClicks?>
	</th>
	<th>
		<?php echo $totalCost?>
	</th>
</tr>
</table>
</div>