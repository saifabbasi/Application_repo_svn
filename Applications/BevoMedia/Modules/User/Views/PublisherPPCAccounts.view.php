<h2 class='adminPageHeading'>Publisher PPC Accounts for <?php print $this->User->getUserName(); ?></h2>

<br/>

<table class='adminPublisherTable width30Pct floatLeft marginRight5Pct' cellpadding=0 cellspacing=0 >
<tr class='splitHeadingRow'>
	<th class='nameCell'>
		Google Adwords
	</th>
</tr>

<?php foreach($this->AdwordsResults as $Key=>$Result):?>
<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td>
		<?php print $Result->username; ?>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->AdwordsResults)):?>
<tr>
	<td colspan="7" class="textAlignCenter">
		<i>You do not have any Adwords Accounts.</i>
	</td>
</tr>
<?php endif?>
<tr class='splitHeadingRow'>
	<td class='textAlignCenter'>
		<a rel="shadowbox;width=640;height=480;player=iframe" href="/BevoMedia/Publisher/GoogleAdwordsAPI.html" title="Google Adwords">
			Manage Adwords Accounts
		</a>
	</td>
</tr>
</table>


<table class='adminPublisherTable width30Pct floatLeft marginRight5Pct' cellpadding=0 cellspacing=0>
<tr class='splitHeadingRow'>
	<th class='nameCell'>
		Yahoo Search Marketing
	</th>
</tr>

<?php foreach($this->YahooResults as $Key=>$Result):?>
<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td>
		<?php print $Result->username; ?>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->YahooResults)):?>
<tr>
	<td class="textAlignCenter">
		<i>You do not have any Yahoo Accounts.</i>
	</td>
</tr>
<?php endif?>
<tr class='splitHeadingRow'>
	<td class='textAlignCenter'>
		<a rel="shadowbox;width=640;height=480;player=iframe" href="/BevoMedia/Publisher/YahooAPI.html" title="Yahoo Search Marketing">
			Manage Yahoo Accounts
		</a>
	</td>
</tr>
</table>


<table class='adminPublisherTable width30Pct floatLeft' cellpadding=0 cellspacing=0>
<tr class='splitHeadingRow'>
	<th class='nameCell'>
		MSN Ad Center
	</th>
</tr>

<?php foreach($this->MSNResults as $Key=>$Result):?>
<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td>
		<?php print $Result->username; ?>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->MSNResults)):?>
<tr>
	<td class="textAlignCenter">
		<i>You do not have any MSN Ad Center Accounts.</i>
	</td>
</tr>
<?php endif?>
<tr class='splitHeadingRow'>
	<td class='textAlignCenter'>
		<a rel="shadowbox;width=640;height=480;player=iframe" href="/BevoMedia/Publisher/MSNAdCenterAPI.html" title="MSN Ad Center">
			Manage MSN Accounts
		</a>
	</td>
</tr>
</table>
