<script src='/Themes/BevoMedia/selectadwordsgeotargets.js'></script>

<h2>Countries</h2>

<form name='selectadwordsgeotarget' id='selectadwordsgeotarget'>
<input type='submit' value='Submit' />
<input type='reset' />
<br/><br/>
<?php $QuarterL = $Quarter = ceil(sizeOf($this->Countries)/3)?>
<div class='floatLeft'>
	<?php foreach($this->Countries as $Iter=>$Country):?>
	<?php if($Iter == $Quarter):?>
		</div>
		<div class='floatLeft'>
		<?php $Quarter += $QuarterL?>
	<?php endif?>
	<label>
		<input type='checkbox' name='Country[]' value='<?php print $Country->code; ?>' class="<?php print $Country->country; ?>" /><?php print $Country->country; ?><br/>
	</label>
	<?php endforeach?>
</div>
<br class='clearBoth'/>
<input type='submit' value='Submit' />
<input type='reset' />
</form>

<script language='javascript'>
	selectadwordsgeotargets.init();
</script>