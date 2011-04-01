
	<form method="post" id="PriceForm">
		Select Niche:
		<select id="NicheID" name="NicheID">
			<option value="0"></option>
<?php 
	foreach ($this->Niches as $Niche) {
		$selected = '';
		if ($Niche->ID==$_GET['NicheID']) $selected = 'selected';
?>
			<option value="<?php echo $Niche->ID?>" <?php echo $selected;?>><?php echo $Niche->Name?></option>
<?php 
	}
?>			
		</select>
		<br />
<?php 
	if (isset($this->Networks)) {
?>
		Networks:
		<table>
<?php 
		foreach ($this->Networks as $Network) {			
?>	
			<tr>
				<td><?php echo $Network->NetworkName?></td>
				<td>
					<input type="text" name="Network_<?php echo $Network->ID?>" value="<?php echo isset($Network->Rate->Rate)?$Network->Rate->Rate:0;?>" />%
				</td>
			</tr>
<?php 
		}
?>
		</table>
<?php 
	}
?>
		<br />
		<input type="submit" name="Submit" value="Save" />
		
	</form>

	
	<script type="text/javascript">
		$('#NicheID').change(function() {
			window.location = '/BevoMedia/Admin/NameYourPrice.html?NicheID='+$(this).val();
		});
	</script>