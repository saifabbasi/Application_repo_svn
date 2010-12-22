<h2 class='adminPageHeading floatRight'>Affiliate Networks</h2>

 
<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr class='textAlignRight'>
	<th class='textAlignLeft'>
		Network
	</th>
	<th>
		Model
	</th>
	<th>
		Users
	</th>
	<th>
		
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

<?php foreach($this->AffNetworks as $Key=>$Result):?>
<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<?php /*?><a href='#ViewPublisher.html?id=<?php print $Result->helperID; ?>'><?php */?>
			<?php print $Result->title; ?>
		<?php /*?></a><?php */?>
	</td>
	<td>
		<?php print $Result->model; ?>
	</td>
	<td>
		<?php $TempUsers = sizeof($Result->getAllUsersForThisNetwork())?>
		<?php if($TempUsers > 0):?>
			<a href='AffiliateNetworkUsers.html?id=<?php print $Result->id; ?>'>View
		<?php endif?>
			<?php echo $TempUsers?> Users
		<?php if($TempUsers > 0):?>
			</a>
		<?php endif?>
	</td>
	<td>
		
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

</table>


