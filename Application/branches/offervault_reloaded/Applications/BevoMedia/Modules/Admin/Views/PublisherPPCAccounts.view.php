<h2 class='adminPageHeading'>Publisher PPC Accounts for <?php print $this->User->GetUserName(); ?></h2>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr class='textAlignLeft'>
	<th>
		ID
	</th>
	<th>
		Username
	</th>
	<th>
		Password
	</th>
	<th>
		Enabled
	</th>
	<th>
		Created On
	</th>
	<th>
		API Update
	</th>
</tr>

<tr class='splitHeadingRow'>
	<td colspan='6' class='nameCell'>
		Google Adwords
	</td>
</tr>

<?php foreach($this->AdwordsResults as $Key=>$Result):?>
<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td>
		<?php print $Result->id; ?>
	</td>
	<td>
		<?php print $Result->username; ?>
	</td>
	<td>
		<?php print $Result->password; ?>
	</td>
	<td>
		<?php print ($Result->enabled=='1')?'Enabled':'Disabled'; ?>
	</td>
	<td>
		<?php print date('M d, Y', strtotime($Result->created)); ?>
	</td>
	<td>
		<a href='AdwordsAPIUpdate.html?id=<?php print $Result->id; ?>' rel='shadowbox;width=640;height=480;player=iframe' title='API Update'>
			Update Stats Now
		</a>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->AdwordsResults)):?>
<tr>
	<td colspan="7" class="textAlignCenter">
		<i>This user does not have any Google Adwords Accounts.</i>
	</td>
</tr>
<?php endif?>


<tr>
	<td colspan='6'>
		<br/>
	</td>
</tr>
<tr class='splitHeadingRow'>
	<td colspan='6' class='nameCell'>
		Yahoo Search Marketing
	</td>
</tr>

<?php foreach($this->YahooResults as $Key=>$Result):?>
<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td>
		<?php print $Result->id; ?>
	</td>
	<td>
		<?php print $Result->username; ?>
	</td>
	<td>
		<?php print $Result->password; ?>
	</td>
	<td>
		<?php print ($Result->enabled=='1')?'Enabled':'Disabled'; ?>
	</td>
	<td>
		<?php print date('M d, Y', strtotime($Result->created)); ?>
	</td>
	<td>
		<?php print $Result->masterAccountId; ?>
	</td>
	<td>
		<a href='YahooAPIUpdate.html?id=<?php print $Result->id; ?>' rel='shadowbox;width=640;height=480;player=iframe' title='API Update'>
			Update Stats Now
		</a>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->YahooResults)):?>
<tr>
	<td colspan="7" class="textAlignCenter">
		<i>This user does not have any Yahoo Accounts.</i>
	</td>
</tr>
<?php endif?>

<tr>
	<td colspan='6'>
		<br/>
	</td>
</tr>
<tr class='splitHeadingRow'>
	<td colspan='6' class='nameCell'>
		MSN Ad Center
	</td>
</tr>

<?php foreach($this->MSNResults as $Key=>$Result):?>
<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td>
		<?php print $Result->id; ?>
	</td>
	<td>
		<?php print $Result->username; ?>
	</td>
	<td>
		<?php print $Result->password; ?>
	</td>
	<td>
		<?php print ($Result->enabled=='1')?'Enabled':'Disabled'; ?>
	</td>
	<td>
		<?php print date('M d, Y', strtotime($Result->created)); ?>
	</td>
	<td>
		
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->MSNResults)):?>
<tr>
	<td colspan="7" class="textAlignCenter">
		<i>This user does not have any MSN Ad Center Accounts.</i>
	</td>
</tr>
<?php endif?>


</table>