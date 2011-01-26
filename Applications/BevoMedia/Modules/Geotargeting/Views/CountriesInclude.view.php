<?php 
	$count = $_GET['count'];
	$landingPageId = $_GET['landingPageId'];
?>
	
	<div id="landingPageCountries_<?=$landingPageId?>_<?=$count?>">
		<select id="country" class="formselect countrySelect" landingPageId="<?=$landingPageId?>" count="<?=$count?>" style="width: 250px" name="country_<?=$landingPageId?>_<?=$count?>_<?=isset($this->Data->ID)?$this->Data->ID:'0'?>">
			<option value="0">Any Country</option>
		<?php 
			foreach ($this->Countries as $Country) {
				$selected = '';
				
				if (isset($this->Data->CountryID)) {
					if ($Country->ID==$this->Data->CountryID) {
						$selected = 'selected';
					}
				}
		?>
			<option value="<?=$Country->ID?>" <?php echo $selected;?>><?=$Country->COUNTRY_NAME?></option>
		<?php 	
			}
		?>	
		</select>
		
		<select id="region" class="formselect regionSelect" style="width: 250px" name="region_<?=$landingPageId?>_<?=$count?>_<?=isset($this->Data->ID)?$this->Data->ID:'0'?>">
			<option value="0">Any Region</option>
		</select>
		
		<select id="city" class="formselect citySelect" style="width: 250px" name="city_<?=$landingPageId?>_<?=$count?>_<?=isset($this->Data->ID)?$this->Data->ID:'0'?>">
			<option value="0">Any City</option>
		</select>
		
		<a class="remove-location" href="#" rowId="<?=$count?>" dbId="<?=isset($this->Data->ID)?$this->Data->ID:'0'?>">Remove</a>
	</div>
	
	<script type="text/javascript">
		var EditRegionID_<?=$landingPageId?>_<?=$count?> = '<?=isset($this->Data->RegionID)?$this->Data->RegionID:''?>'; 
		var EditCityID_<?=$landingPageId?>_<?=$count?> = '<?=isset($this->Data->CityID)?$this->Data->CityID:''?>';
			
		$('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #country').change(function() {

			loadRegions_<?=$landingPageId?>_<?=$count?>($(this).val());

			redrawMarkers();

		});

		function loadRegions_<?=$landingPageId?>_<?=$count?>(CountryID)
		{
			$.get('/BevoMedia/Geotargeting/ListGeoRegions.html?ajax=true&CountyCode='+CountryID, function(regions) {
				var regions = eval(regions);
				
				var region = $('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #region');
				region.html('');

				region.append($("<option/>").val(0).text('Any Region'));
				
				for (var i=0; i<regions.length; i++) {

					region.append($("<option/>").val(regions[i].ID).text(regions[i].REGION));

				}

				region.val(EditRegionID_<?=$landingPageId?>_<?=$count?>);

				if (EditRegionID_<?=$landingPageId?>_<?=$count?>>0) {

					if (EditCityID_<?=$landingPageId?>_<?=$count?>==0) {
						redrawMarkers();
					}
				}
				
				EditRegionID_<?=$landingPageId?>_<?=$count?> = '';


				if (EditCityID_<?=$landingPageId?>_<?=$count?>!='') {
					loadCities_<?=$landingPageId?>_<?=$count?>($('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #country').val(), $('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #region').val());
				}
				
				
			});
		}

		$('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #region').change(function() {

			loadCities_<?=$landingPageId?>_<?=$count?>($('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #country').val(), $(this).val());

			redrawMarkers();

		});

		function loadCities_<?=$landingPageId?>_<?=$count?>(CountryCode, RegionCode)
		{
			$.get('/BevoMedia/Geotargeting/ListGeoCities.html?ajax=true&CountryCode='+CountryCode+'&RegionCode='+RegionCode, function(cities) {
				var cities = eval(cities);
				
				var city = $('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #city');
				city.html('');

				city.append($("<option/>").val(0).text('Any City'));
				
				for (var i=0; i<cities.length; i++) {
					city.append($("<option/>").val(cities[i].ID).text(cities[i].CITY));
				}

				if (EditCityID_<?=$landingPageId?>_<?=$count?>!='') {
					city.val(EditCityID_<?=$landingPageId?>_<?=$count?>);
					
					redrawMarkers();
				}

				EditCityID_<?=$landingPageId?>_<?=$count?> = '';
			});
		}

		$('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #city').change(function() {

			redrawMarkers();

		});

		$('#landingPageCountries_<?=$landingPageId?>_<?=$count?> .remove-location').click(function() {

			if (!confirm('Are you sure you want to remove this location?')) return false;

			$.get('/BevoMedia/Geotargeting/RemoveLocation.html?ID='+$(this).attr('dbId'));
			
			$('#landingPageCountries_<?=$landingPageId?>_'+$(this).attr('rowId')).remove();

			redrawMarkers();
			
			return false;

		});

		if (EditRegionID_<?=$landingPageId?>_<?=$count?>!='') {
			loadRegions_<?=$landingPageId?>_<?=$count?>($('#landingPageCountries_<?=$landingPageId?>_<?=$count?> #country').val());

			if (EditRegionID_<?=$landingPageId?>_<?=$count?>==0) {
				redrawMarkers();
			}
		}

	</script>
	