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

	
	<h2>Post Comment</h2>
		
	
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
					
			<input type="hidden" name="PostID" value="<?=$_GET['ID']?>" />
					
					
			<table width="300" style="text-align: left;">
				<tr>
					<td>Username</td>
					<td>
						<input type="text" class="formtxt" name="Username" value="<?=isset($this->Comment)?$this->Comment->Username:''?>" />
					</td>
				</tr>
				
				<tr>
					<td>Title</td>
					<td>
						<input type="text" class="formtxt" name="Title" value="<?=isset($this->Comment)?$this->Comment->Title:''?>" />
					</td>
				</tr>
				
				<tr>
					<td>Comment</td>
					<td>
						<textarea class="formtxtarea" name="Text" rows="6"><?=isset($this->Comment)?$this->Comment->Text:''?></textarea>
					</td>
				</tr>
				
				<tr>
					<td colspan="2">
						<input type="submit" class="tbtn" name="Save" value="Save" />
					</td>
				</tr>
			</table>
					
		</form>
	
	</div>
	