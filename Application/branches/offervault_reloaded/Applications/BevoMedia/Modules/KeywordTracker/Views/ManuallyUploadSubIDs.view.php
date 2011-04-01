<?php echo SoapPageMenu('kwt','subids','upload'); 
echo $this->PageDesc->ShowDesc($this->PageHelper,false); ?>

<div class="pagecontent">
	<form method='post' action='ManuallyUploadSubIDsAssign.html' class="appform widefull" enctype="multipart/form-data">
		
		<label>
			<span>Put a new subId on each line. You can paste just the subids, or a CSV of <i>subid,conversions,revenue</i></span>	
			<textarea class="formtxtarea" name='subids' style='width:95%;' rows='15'></textarea>
		</label>
		
		<label>
			<span>Or upload CSV file:</span>
			<input type="file" name="fileSubIds" value="" />
		</label>
		
		<br />
		
		<label>
			Default revenue per conversion: $<input class="formtxt wide_number" type="text" size="4" name="amount" value="0.00">
		</label>
		
		<label>
			<input class="formsubmit track_upload" type='submit' />
		</label>
		
		
		
	</form>
</div>