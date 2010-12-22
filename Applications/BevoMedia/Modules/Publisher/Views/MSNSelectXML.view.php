<img src='/Themes/<?php print $this->PageHelper->Application; ?>/img/adcenter.jpg'>

<br/><br/>

<table class='shadowbox-table <?php print ($this->editEmail)?('displayNone'):(''); ?>'>
	<tr>
		<th colspan='4'>
			Select Account to Manually Upload Report
		</th>
	</tr>
	
	<?php if(sizeOf($this->InstalledAccounts)):?>
	<?php foreach($this->InstalledAccounts as $Account):?>
	<tr>
		<td class='textAlignLeft'>
			<?php echo$Account->Username?>
		</td>
		<td class='textAlignRight'>
			<a target='_parent' href='MSNManualUpload.html?ID=<?php print $Account->iD; ?>'>
				Select
			</a>
		</td>
		<td class='textAlignRight'>

		</td>
		<td class='textAlignRight smallItalics'>
			API Daily Update Account
		</td>
	</tr>
	<?php endforeach?>	
	<?php endif?>
	
	<?php if(sizeOf($this->NotInstalled)):?>
	<?php foreach($this->NotInstalled as $Account):?>
	<tr>
		<td class='textAlignLeft'>
			<?php echo$Account->Username?>
		</td>
		<td class='textAlignRight'>
			<a target='_parent' href='MSNManualUpload.html?ID=<?php print $Account->iD; ?>'>
				Select
			</a>
		</td>
		<td class='textAlignRight'>

		</td>
		<td class='textAlignRight smallItalics'>
			
		</td>
	</tr>
	<?php endforeach?>	
	<?php endif?>
	
	<?php if(sizeOf($this->DisabledAccounts)):?>
	<?php foreach($this->DisabledAccounts as $Account):?>
	<tr>
		<td class='textAlignLeft'>
			<?php print $Account->username; ?>
		</td>
		<td class='textAlignRight'>
			<a target='_parent' href='MSNManualUpload.html?ID=<?php print $Account->iD; ?>'>
				Select
			</a>
		</td>
		<td class='textAlignRight'>

		</td>
		<td class='textAlignRight'>
		
		</td>
	</tr>
	<?php endforeach?>	
	<?php endif?>

</table>