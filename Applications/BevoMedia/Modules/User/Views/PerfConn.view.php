<style>
body {
	background: #fff;
	text-align: center;
	font-family: Tahoma;
	}
</style>

<form method="post">
	<?php $userHasNiche = (count($this->UserNicheIDs) > 0);?>
	<label for="X">
		<h2>Select Niches: </h2>
	<select name="niche[]" size="10" id="niche" class="required formselect" rel="Niche" multiple="multiple">
		<?php 
			foreach ($this->Niches as $Niche) {
				$selected = '';
				if (in_array($Niche->ID, $this->UserNicheIDs)) {
					$selected = 'selected="selected"';
				}
		?>
				<option value="<?php echo $Niche->ID?>" <?php echo $selected;?>><?php echo $Niche->Name?></option>
		<?php 
			}
		?>
	</select>
	</label>
	
	<br/><br/>
	
	
	<br/>
	
	<input class="formsubmit tbtn" type='submit' name='changeProfileFormSubmit' value="Apply Changes" />


</form>