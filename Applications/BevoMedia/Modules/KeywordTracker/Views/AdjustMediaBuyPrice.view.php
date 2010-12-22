<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','adjust_media_cpc');
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
	
<form method="post">

	<div class="filtering formslim">
	
	<div class="option">
	<label>Select campaign:</label>
	<select class="formselect" name="CampaignID" onchange="this.form.submit();">
		<option value=""></option>
<?
	foreach ($this->Campaigns as $Campaign)
	{
		$selected = '';
		if (isset($_POST['CampaignID']) && ($Campaign->id==$_POST['CampaignID']) )
		{
			$selected = 'selected';
		}
?>
		<option value="<?=$Campaign->id?>" <?=$selected?>><?=$Campaign->name?></option>
<?
	}
?>
	</select>

	
	
	<br /><br />
	
<?
	if (isset($_POST['CampaignID']) && ($_POST['CampaignID']!=''))
	{
?>
		<label>Ad Group:</label>
		<select class="formselect" name="AdGroupID" onchange="this.form.submit();">
			<option value=''></option>
<?
		foreach ($this->AdGroups as $AdGroup)
		{
			$selected = '';
			if (isset($_POST['AdGroupID']) && ($AdGroup->id==$_POST['AdGroupID']) )
			{
				$selected = 'selected';
			}
?>
			<option value='<?=$AdGroup->id?>' <?=$selected?>><?=$AdGroup->name?></option>
<?
		}
?>
		</select>
<?
	}
?>
	
	<br /><br />
	
	
<?
	if (isset($_POST['AdGroupID']) && ($_POST['AdGroupID']!=''))
	{
?>
		<label>Ad Variation: </label>
		<select class="formselect" name="AdVariationID" onchange="this.form.submit();">
			<option value=''></option>
<?
		foreach ($this->AdVariations as $AdVariation)
		{
			$selected = '';
			if (isset($_POST['AdVariationID']) && ($AdVariation->id==$_POST['AdVariationID']) )
			{
				$selected = 'selected';
			}
?>
			<option value='<?=$AdVariation->id?>' <?=$selected?>><?=$AdVariation->title?></option>
<?
		}
?>
		</select>
<?
	}
?>
	
	</div>


	
	
	
	</div>
	
	<br /><br />
	
	
<?
	if (isset($this->AdVariation))
	{
?>
	<div style="padding: 10px;">
	
		<label>Cost:</label>
		<input class="formtxt wide_number" type="text" name="Cost" value="<?=$this->AdVariation->cost?>" />
		
		<br /><br /><br />
		
		<input type="submit" class="formsubmit track_update applybut_b" name="Submit" value="Update" />
	</div>
	
<?
	}
?>
	
	
	
	</form>
	