<h2 class='adminPageHeading floatRight'>Affiliate Network Users for <?php print $this->AffiliateNetwork->title; ?></h2>

 
<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr class='textAlignRight'>
	<th class='textAlignLeft'>
		User
	</th>
	<th>
		Network Credentials
	</th>
	<th colspan="2">
		Update Stats
	</th>
</tr>

<?php foreach($this->AffNetUsers as $Key=>$Result):?>
<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<?php /*?><a href='#ViewPublisher.html?id=<?php print $Result->helperID; ?>'><?php */?>
			<?php print $Result->firstName; ?> <?php print $Result->lastName; ?>
		<?php /*?></a><?php */?>
	</td>
	<td>
		<?php print $Result->AffiliateNetworkUser->loginId; ?>
		
		<?php //echo $Result->AffiliateNetworkUser->PASSWORD?>
	</td>
	<td class='textAlignRight'>
		<a href='AffiliateNetworkUserAPIUpdate.html?id=<?php print $Result->AffiliateNetworkUser->id; ?>' rel='shadowbox;width=640;height=480;player=iframe'>Simple</a>
	</td>
	<td style="width: 100px;">
		<a href='AffiliateNetworkUserAPIUpdateSelectDate.html?id=<?php print $Result->AffiliateNetworkUser->id; ?>' rel='shadowbox;width=640;height=480;player=iframe'>Select Date</a>
	</td>
</tr>
<?php endforeach?>

</table>


