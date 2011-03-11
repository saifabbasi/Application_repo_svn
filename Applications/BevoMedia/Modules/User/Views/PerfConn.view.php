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
	<br/>
	Hold down Ctrl to select multiple niches.
	</label>
	
	<label for="Y">
	<span class="label">&nbsp;<br/>&nbsp;<br/></span>
	Promotion Method: <br/>
	<?php foreach($this->PromoMethods as $PromoMethod):?>
		<?php $selected = (in_array($PromoMethod->id, $this->UserPromoMethodIDs))?'checked="checked"':'';?>
	<input type="checkbox" name="promomethod[]" value="<?php echo $PromoMethod->id;?>" <?php echo $selected;?>/><?php echo $PromoMethod->promomethod;?>
	<br/>
	<?php endforeach;?>
	</label>
	
	<label for="Z">
	<span class="label">&nbsp;<br/>&nbsp;<br/></span>
	Experience: <br/>
	<?php foreach($this->ExpLevels as $ExpLevel):?>
		<?php $selected = (in_array($ExpLevel->id, $this->UserExpLevelIDs))?'checked="checked"':'';?>
	<input type="radio" name="explevel[]" value="<?php echo $ExpLevel->id;?>" <?php echo $selected?>/><?php echo $ExpLevel->explevel;?>&nbsp;
	<?php endforeach;?>
	</label>
	
	</div>
	<br/><br/>
	
	
	<br/>
	
	<input class="formsubmit tbtn" type='submit' name='changeProfileFormSubmit' value="Apply Changes" />


</form>