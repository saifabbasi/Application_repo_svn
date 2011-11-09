<style>
body {
	background: #fff;
	text-align: center;
	font-family: Tahoma;
	}
</style>

<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>

<?php 
	if (isset($this->ErrorMessage))
	{
?>
	<div style="color: #f00; text-align: center;"><?=$this->ErrorMessage?></div>
<?php 
	}
?>

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
				<option value="<?php echo $Niche->ID?>" <?php echo $selected;?>><?php echo htmlentities($Niche->Name);?></option>
		<?php 
			}
		?>
	</select>
	<br/>
	Hold down Ctrl to select multiple niches.
	</label>
	<br/>
	<br/>
	<div style="text-align: left;  width: 200px; margin-top: 0px; margin: auto; ">
	Promotion Method: <br/>
	<?php foreach($this->PromoMethods as $PromoMethod):?>
		<?php $selected = (in_array($PromoMethod->id, $this->UserPromoMethodIDs))?'checked="checked"':'';?>
	<input type="checkbox" name="promomethod[]" value="<?php echo $PromoMethod->id;?>" <?php echo $selected;?>/><?php echo $PromoMethod->promomethod;?>
	<br/>
	<?php endforeach;?>
	</div>
	
	<label for="Z">
	<span class="label">&nbsp;<br/></span>
	Experience: <br/>
	<?php foreach($this->ExpLevels as $ExpLevel):?>
		<?php $selected = (in_array($ExpLevel->id, $this->UserExpLevelIDs))?'checked="checked"':'';?>
	<input type="radio" name="explevel[]" value="<?php echo $ExpLevel->id;?>" <?php echo $selected?>/><?php echo $ExpLevel->explevel;?>&nbsp;
	<?php endforeach;?>
	</label>
	
	<br /><br />
	
	<span style="font-size: 10px;">The Bevo Performance Connector provides users with personal help. What is the best way for a Bevo Representative to get in contact with you?</span>
	<br />
	
	Contact:
	<div style="text-align: left;  width: 250px; margin-top: 0px; margin: auto; ">
		
		<label style="width: 80px; display: inline-block;">
			&nbsp;	 
		</label>
		<select id="ContactType" name="ContactType">
			<option>Phone</option>
			<option>IM</option>
		</select>
		
		
		<br /><br />
		
		<div id="ContactIM" style="display: none;">
			<label style="width: 80px; display: inline-block;">IM Service:</label>
			<select name="im_service">
				<option></option>
				<option>AIM</option>
				<option>Gtalk</option>
				<option>Skype</option>
				<option>Yahoo/MSN</option>
			</select>
			
			<label style="width: 80px; display: inline-block;">IM Name:</label>
			<input type="text" name="im" value="" />
		</div>
		
		
		<div id="ContactPhone">
			<label style="width: 80px; display: inline-block;">Phone:</label>
			<input type="text" name="phone" value="" />
 		</div>
	
	</div>
	<br/><br/>
	
	
	<br/>
	
	<input class="formsubmit tbtn" type='submit' name='changeProfileFormSubmit' value="Apply Changes" />

	<script type="text/javascript">
		$(document).ready(function() {
			$('#ContactType').change(function() {
				if ($(this).val()=='Phone') {
					$('#ContactIM').hide();
					$('#ContactPhone').show();
				} else {
					$('#ContactIM').show();
					$('#ContactPhone').hide();
				}
			});
		});
	</script>
</form>