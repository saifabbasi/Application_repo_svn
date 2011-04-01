<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','subids','upload'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper,false); ?>
	
<div class="pagecontent">
<form method='post' action='ManuallyUploadSubIDsComplete.html' class="appform tricol">
	<label>
		If the subid already exists, I want the new value to <input selected type="radio" name="overwrite" value="t"> <b>overwrite</b> the old value <input type="radio" name="overwrite" value="f"> <b>add to</b> the old value.
	</label>
	
	<table width='100%'>
		<tr>
			<th>
				Subid
			</th>
			<th>
				# Conversions Subid Produced
			</th>
			<th>
				Revenue Subid Generated
			</th>
		</tr>
		
<?php foreach($this->SubIDs as $SubID => $s){
		?>
		<tr>
			<td>
				<input class="formtxt disabled" type='text' disabled="disabled" value="<?php echo $SubID?>" />
				<input type='hidden' name='SubID[]' value="<?php echo $SubID?>" />
			</td>
			<td>
			<input class="formtxt" type='text' name='SubIDAction[]' value='<?=$s['conv']?>' />
			</td>
			<td>
			<input class="formtxt" type='text' name='SubIDValue[]' value='<?=$s['value']?>' />
			</td>
		</tr>
		<?php } ?>
	</table>
	<br/>
	<input class="formsubmit track_submit" type='submit'/>
	<br /><br />	
</form>
</div><!--close pagecontent-->