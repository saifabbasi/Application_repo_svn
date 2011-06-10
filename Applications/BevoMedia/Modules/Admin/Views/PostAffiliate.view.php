<?php 
	if (isset($this->Success) && ($this->Success==true))
	{
?>
	<script type="text/javascript">
		parent.location.reload();
	</script>
<?php 
	}
?>

	
	<h2>Post Affiliate Review</h2>
	
	
	<div class="col col1">

	<?php 
		if (isset($this->ErrorMessage))
		{
	?>
		<div style="color: #f00;"><?=$this->ErrorMessage?></div>
		<br /><br />
	<?php 
		}
	?>
	
		<form method="post" action="">
					
		<table style="text-align: left;">
			
			<tr>
				<td>From Username</td>
				<td>
					<input type="text" class="formtxt" name="Username" value="<?=isset($this->Post)?$this->Post->Username:''?>" />
				</td>
			</tr>				
			
			<tr>
				<td>Affiliate Name</td>
				<td>
					<input type="text" class="formtxt" name="Name" value="<?=isset($this->Post)?$this->Post->Name:''?>" />
				</td>
			</tr>
			
			<tr>
				<td>Email</td>
				<td>
					<input type="text" class="formtxt" name="Email" value="<?=isset($this->Post)?$this->Post->Email:''?>" />
				</td>
			</tr>
					
			<tr>
				<td>Address</td>
				<td>
					<input type="text" class="formtxt" name="Address" value="<?=isset($this->Post)?$this->Post->Address:''?>" />
				</td>
			</tr>
			
			<tr>
				<td>Phone</td>
				<td>
					<input type="text" class="formtxt" name="Phone" value="<?=isset($this->Post)?$this->Post->Phone:''?>" />
				</td>
			</tr>			
			
			<tr>
				<td>Known Individuals attached to this company</td>
				<td>
					<input type="text" class="formtxt" name="KnownAttachedIndividuals" value="<?=isset($this->Post)?$this->Post->KnownAttachedIndividuals:''?>" />
				</td>
			</tr>	
			
			<tr>
				<td>Custom Heat</td>
				<td>
					<input type="text" class="formtxt" name="CustomHeat" value="<?=isset($this->Post)?$this->Post->CustomHeat:'0'?>" />
				</td>
			</tr>
			
			<tr valign="top">
				<td>Detail</td>
				<td>
					<textarea class="formtxtarea" name="Text" rows="6"><?=isset($this->Post)?$this->Post->Text:''?></textarea>
				</td>
			</tr>
			
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" class="tbtn" name="Save" value="Save" />
				</td>
			</tr>
				
			
			</table>
			
		</form>
	
	</div>
	