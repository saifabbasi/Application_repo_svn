<h2 class='adminPageHeading'>Add Demo Data to <?php print isset($this->user)?$this->user->FirstName:''; ?> <?php print isset($this->user)?$this->user->LastName:''; ?></h2>

<form method="post">

<div class='floatRight' style="width:45%;">	
	<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
	<tr class='textAlignLeft'>
		<th>
			Select Options:
		</th>
	</tr>
	<tr class='splitHeadingRow'>
		<td class='nameCell'>
			Date
		</td>
	</tr>
	<tr class='lightBlueRow'>
		<td class='textAlignCenter '>
			<input class='textAlignCenter' name="date" value="<?php echo date('m/d/Y')?>"/>
		</td>
	</tr>

	<tr class='lightBlueRow'>
		<td class='textAlignCenter '>
			Affiliate Network Stats: <input name="doaffnetworks" value="ON" type="checkbox"/>
		</td>
	</tr>
	<tr class='darkBlueRow'>
		<td class='textAlignCenter '>
			&nbsp;
		</td>
	</tr>
	
	<tr class='lightBlueRow'>
		<td class='textAlignCenter '>
			<input type="submit" name="submitAddForm"/>
		</td>
	</tr>

<?php /*?>
	<tr class='splitHeadingRow'>
		<td class='nameCell'>
			Impressions Range
		</td>
	</tr>
	<tr class='lightBlueRow'>
		<td class='textAlignCenter '>
			<input class='textAlignCenter' name="impmin" value="0"/>
			to
			<input class='textAlignCenter' name="impmax" value="1000"/>
		</td>
	</tr>

	<tr class='splitHeadingRow'>
		<td class='nameCell'>
			Click Range
		</td>
	</tr>
	<tr class='lightBlueRow'>
		<td class='textAlignCenter '>
			<input class='textAlignCenter' name="clickmin" value="0"/>
			to
			<input class='textAlignCenter' name="clickmax" value="100"/>
		</td>
	</tr>
	

	<tr class='splitHeadingRow'>
		<td class='nameCell'>
			Cost Range
		</td>
	</tr>
	<tr class='lightBlueRow'>
		<td class='textAlignCenter '>
			<input class='textAlignCenter' name="costmin" value="0.05"/>
			to
			<input class='textAlignCenter' name="costmax" value="1.00"/>
		</td>
	</tr>
<?php */?>

</table>
</div>


<div class='floatLeft' style="width:50%;">	
	<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
	<tr class='textAlignLeft'>
		<th>
			&nbsp;
		</th>
		<th>
			Select Accounts:
		</th>
	</tr>
	<tr class='splitHeadingRow'>
		<td colspan='3' class='nameCell'>
			Google Adwords
		</td>
	</tr>
	<?php foreach($this->AdwordsResults as $Key=>$Result):?>
	<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
		<td>
			<input type='checkbox' checked='checked' name='Adwords[]' value="<?php print $Result->id; ?>">
		</td>
		<td>
			<?php print $Result->username; ?>
		</td>
	</tr>
	<?php endforeach?>
	<?php if(!sizeOf($this->AdwordsResults)):?>
	<tr>
		<td colspan='3' class="textAlignCenter">
			<i>This user does not have any Google Adwords Accounts.</i>
		</td>
	</tr>
	<?php endif?>
	
	<tr class='splitHeadingRow'>
		<td colspan='3' class='nameCell'>
			Yahoo Search Marketing
		</td>
	</tr>
	<?php foreach($this->YahooResults as $Key=>$Result):?>
	<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
		<td>
			<input type='checkbox' checked='checked' name='Yahoo[]' value="<?php print $Result->id; ?>">
		</td>
		<td>
			<?php print $Result->username; ?>
		</td>
	</tr>
	<?php endforeach?>
	<?php if(!sizeOf($this->YahooResults)):?>
	<tr>
		<td colspan='3' class="textAlignCenter">
			<i>This user does not have any Yahoo Search Marketing Accounts.</i>
		</td>
	</tr>
	<?php endif?>
	
	<tr class='splitHeadingRow'>
		<td colspan='3' class='nameCell'>
			MSN AdCenter
		</td>
	</tr>
	<?php foreach($this->MSNResults as $Key=>$Result):?>
	<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
		<td>
			<input type='checkbox' checked='checked' name='MSNAdCenter[]' value="<?php print $Result->id; ?>">
		</td>
		<td>
			<?php print $Result->username; ?>
		</td>
	</tr>
	<?php endforeach?>
	<?php if(!sizeOf($this->MSNResults)):?>
	<tr>
		<td colspan='3' class="textAlignCenter">
			<i>This user does not have any MSN AdCenter Accounts.</i>
		</td>
	</tr>
	<?php endif?>

	<tr class='splitHeadingRow'>
		<td colspan='3' class='nameCell'>
			Google Analytics
		</td>
	</tr>
	<?php foreach($this->AnalyticsResults as $Key=>$Result):?>
	<tr class='textAlignLeft <?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?> '>
		<td>
			<input type='checkbox' checked='checked' name='Analytics[]' value="<?php print $Result->id; ?>">
		</td>
		<td>
			<?php print $Result->username; ?>
		</td>
	</tr>
	<?php endforeach?>
	<?php if(!sizeOf($this->AnalyticsResults)):?>
	<tr>
		<td colspan='3' class="textAlignCenter">
			<i>This user does not have any Google Analytics Accounts.</i>
		</td>
	</tr>
	<?php endif?>
	</table>
</div>

</form>