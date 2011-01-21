<?php 
	$count = $_GET['count'];
?>

	<fieldset id="landingPageFieldset_<?=$count?>" style="padding: 5px;">
		<legend>Landing Page</legend>
		
		<label>
			<span style="width: 120px; display: inline-block;">Landing Page URL:</span>
			<input type="text" class="formtxt landingPageUrl" id="landingPageURL_<?=$count?>" name="landingPageURL_<?=$count?>_<?=isset($this->Data->ID)?$this->Data->ID:'0'?>" value="<?=isset($this->Data->URL)?$this->Data->URL:''?>" />
		</label>
		
		<a id="addCountry" class="add-location" href="#" rowId="<?=$count?>">Add Location</a>
		|
		<a id="addCountry" class="remove-landing-page" href="#" rowId="<?=$count?>" dbId="<?=isset($this->Data->ID)?$this->Data->ID:'0'?>">Remove Landing Page</a>
		
		<br /><br />
		
		<fieldset style="margin-left: 120px; padding: 5px;">
		<legend>Locations</legend>
			<div id="landingPageCountries">
			
			</div>
		</fieldset>
		
	</fieldset>
	
	<script type="text/javascript">

		var countriesCount = 0;
	
		$('#landingPageFieldset_<?=$count?> #addCountry').click(function() {

			return addLocation_<?=$count?>(0);
			
		});

		function addLocation_<?=$count?>(ID)
		{
			var parent = $('#landingPageFieldset_<?=$count?> #landingPageCountries');
			var loadDiv = $(document.createElement('div')).attr('id', 'landingPageCountry_'+countriesCount);

			loadDiv.load('/BevoMedia/Geotargeting/CountriesInclude.html?ajax=true&count='+countriesCount+'&landingPageId=<?=$count?>&ID='+ID);
			$('#landingPageFieldset_<?=$count?> #landingPageCountries').append(loadDiv);

			countriesCount++;

			return false;
		}

		$('#landingPageFieldset_<?=$count?> .remove-landing-page').click(function() {

			if (!confirm('Are you sure you want to remove this landing page?')) return false;

			$.get('/BevoMedia/Geotargeting/RemoveLandingPage.html?ID='+$(this).attr('dbId'));
			
			$('#landingPageFieldset_'+$(this).attr('rowId')).remove();

			return false;
		});
	
	</script>
	
	
	
	<script type="text/javascript">

	<?php 
		if (isset($this->Data->Locations))
		foreach ($this->Data->Locations as $Location) {
	?>
		addLocation_<?=$count?>(<?php echo $Location->ID;?>);
	<?php
		}
	?>
	
	</script>
	
	