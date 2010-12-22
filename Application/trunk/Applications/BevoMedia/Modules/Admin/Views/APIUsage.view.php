<h2 class='adminPageHeading'>API Usage</h2>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr class='textAlignRight'>
	<th class='textAlignLeft'>
		Publisher
	</th>
	<th>
		Updates
	</th>
	<th>
		API Calls
	</th>
	<th colspan='3' class='textAlignCenter'>
		Admin Functions
	</th>
</tr>

<?php $Key = 1?>
<?php foreach($this->Results as $ResultKey=>$Result):?>
<?php $Key++?>
<?php if(!$Result->userId):?>
<!--
<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td colspan='9'>
		<i>User removed from system...</i>
		<?php $Key--?>
	</td>
</tr>
 -->
<?php continue?>
<?php endif?>
<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft'>
		<a href='ViewPublisher.html?id=<?php print $Result->userId; ?>'><?php print $Result->User->getUserName(); ?></a>
	</td>
	<td>
		<?php print $Result->totalUpdates; ?>
	</td>
	<td>
		<?php print number_format($Result->totalCalls, 0, '.', ','); ?>
	</td>
	<td>
		<a href='APIUsageDetails.html?id=<?php print $Result->userId; ?>'>Details</a>
	</td>
	<td>
		<a href='AddAPICreditToUser.html?id=<?php print $Result->User->id; ?>' title='Add API Credit to <?php print $Result->User->getUserName(); ?>' rel='shadowbox;width=300;height=120;player=iframe'>
			Add Credit
		</a>
	</td>
	<td>
		<a href='PublisherPPCAccounts.html?id=<?php print $Result->userId; ?>' title='<?php print sizeOf($Result->User->GetDailyAccountsAdwords()); ?> Adwords accounts scheduled for daily updates out of <?php print sizeOf($Result->User->GetAllAccounts()); ?> total accounts.'>
			PPC Accounts (<?php print sizeOf($Result->User->GetDailyAccountsAdwords()); ?>/<?php print sizeOf($Result->User->GetAllAccounts()); ?>)
		</a>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->Results)):?>
<tr>
	<td colspan="7" class="textAlignCenter">
		<i>No API Usage has been Recorded</i>
	</td>
</tr>
<?php endif?>
</table>


