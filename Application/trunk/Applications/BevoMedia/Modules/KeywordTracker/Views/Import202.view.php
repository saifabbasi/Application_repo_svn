<?php 
	echo SoapPageMenu('kwt','overview', 'import202');
	echo $this->PageDesc->ShowDesc($this->PageHelper); 
?>
	
<br /><br /><br />

<?php 
	if (isset($this->Error))
	{
?>
	<div style="border: 1px #E85163 solid; background-color: #F5AEB6; width: 90%; height: 45px; line-height: 45px; color: #000; text-align: center; margin-left: 40px;">
		<?php echo $this->Error;?>
	</div>
	<br /><br /><br />
<?php 
	}
?>

<?php 
	if (isset($this->Success))
	{
?>
	<div style="border: 1px #5AC468 solid; background-color: #A6FFB2; width: 90%; height: 45px; line-height: 45px; color: #000; text-align: center; margin-left: 40px;">
		<?php echo $this->Success;?>
	</div>
	<br /><br /><br />
<?php 
	}
?>

<div style="text-align: center">
	<form method="post" id="signupForm" class="signupForm" action="/BevoMedia/KeywordTracker/Import202.html" enctype="multipart/form-data">
					
		<label for="NetworkName">
			<span class="label">Tracking202 Export File:</span>
			<input type="file" name="File" value="" id="File" class="required" />
		</label>
		
		<input type="submit" id="Upload" name="Upload" value="Upload" />
		<br /><br />
		
	</form>
</div>
