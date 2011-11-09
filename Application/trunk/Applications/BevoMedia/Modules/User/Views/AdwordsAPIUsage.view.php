<h2 class='floatRight adminPageHeading'>Adwords API Usage for <?php print $this->User->getUserName(); ?></h2>

<h3 class='floatLeft'>Remaining Balance:
	<span style='color:<?php echo($this->APIUse->Balance<0)?'#880000':'#008800'?>;'>
		$<?php print number_format($this->APIUse->Balance, 2, '.', ','); ?>
	</span>
</h3>

<br class='clearBoth'/>

<table class='adminPublisherTable' cellpadding="0" cellspacing="0">
<tr class='textAlignRight'>
	<th class='textAlignLeft'>
		Date 
	</th>
	<th>
		Credit
	</th>
</tr>

<?php foreach($this->CreditResults as $Key=>$Result):?>
<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<?php print date('F j, Y - g:i a', strtotime($Result->created)); ?>
	</td>
	<td>
		$<?php print number_format($Result->credit, 2, '.', ','); ?>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->CreditResults)):?>
<tr>
	<td colspan="2" class="textAlignCenter">
		<i>No API Credit has been Added</i>
	</td>
</tr>
<?php endif?>

<tr class='textAlignRight'>
	<td>
		<b>Total Credit:</b> 
	</td>
	<td>
		$<?php print number_format($this->APIUse->totalCredit	, 2, '.', ','); ?>
	</td>
</tr>


<tr class='lightBlueRow'>
	<td class='textAlignLeft'>
		<a rel="shadowbox;width=640;height=480;player=iframe" href="/BevoMedia/Publisher/GoogleAdwordsAPI.html" title="Google Adwords">
			Modify Adwords API Accounts
		</a>
		<br/>
		<a href="PublisherPPCAccounts.html" title="You have <?php print sizeOf($this->User->getAllAccounts()); ?> total PPC accounts.">
			View All PPC Accounts (<?php print sizeOf($this->User->getAllAccounts()); ?>)
		</a>
	</td>
	<td class='textAlignRight'>
		<a href='AdwordsAPIPaypal.html'>Purchase API Credit</a>
	</td>
</tr>

</table>

<br/><br/>

<table class='adminPublisherTable' cellpadding="0" cellspacing="0">
<tr class='textAlignRight'>
	<th class='textAlignLeft'>
		Date 
	</th>
	<th>
		Account 
	</th>
	<th>
		API Calls
	</th>
	<th>
		Cost
	</th>
</tr>

<?php foreach($this->Results as $Key=>$Result):?>
<tr class='textAlignRight <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
	<td class='textAlignLeft nameCell'>
		<?php print date('F j, Y - g:i a', strtotime($Result->created)); ?>
	</td>
	<td>
		<?php print $Result->username; ?>
	</td>
	<td>
		<?php print number_format($Result->apiCalls, 0, '.', ','); ?>
	</td>
	<td>
		$<?php print number_format($this->APIUse->CalcCost($Result->apiCalls), 2, '.', ','); ?>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->Results)):?>
<tr>
	<td colspan="4" class="textAlignCenter">
		<i>No API Usage has been Recorded</i>
	</td>
</tr>
<?php endif?>
<tr class='textAlignRight'>
	<td colspan='2'>
	<b>Totals:</b>
	</td>
	<td>
		<?php print number_format($this->APIUse->totalCalls, 0, '.', ','); ?>
	</td>
	<td>
		 $<?php print number_format($this->APIUse->totalCost, 2, '.', ','); ?>
	</td>
</tr>
</table>


<br/><br/>


<br class='clearBoth'/>