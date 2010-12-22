<h2 class='floatRight adminPageHeading'>Adwords API Usage for <?php print $this->User->getUserName(); ?></h2>

<h3 class='floatLeft'>Remaining Balance:
	<span style='color:<?php echo($this->APIUse->Balance<0)?'#880000':'#008800'?>;'>
		$<?php print number_format($this->APIUse->Balance, 2, '.', ','); ?>
	</span>
</h3>

<br class='clearBoth'/>

<table class='adminPublisherTable leftFloater' cellpadding=0 cellspacing=0>
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
</table>




<table class='adminPublisherTable rightFloater' cellpadding=0 cellspacing=0>
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

<tr>
	<td colspan='2'>
		&nbsp;
	</td>
</tr>
<tr class='lightBlueRow textAlignCenter'>
	<td colspan='2'>
		<a href='AddAPICreditToUser.html?id=<?php print $this->User->id; ?>' title='Add API Credit to <?php print $this->User->GetUserName(); ?>' rel='shadowbox;width=300;height=120;player=iframe'>Add API Credit</a>
	</td>
</tr>
</table>


<br class='clearBoth'/>