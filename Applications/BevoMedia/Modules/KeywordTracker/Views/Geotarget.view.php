<?php echo SoapPageMenu('kwt','rotators','rotators_lp_overview',true); ?>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<script type="text/javascript">
	var countries = <?php echo json_encode($this->Countries); ?>
</script>

<br /><br />


	<form method='post'>
		
		<label>
			<span>Name</span>
			<input class="formtxt" name='name' type='text' value='' />			
		</label>
		
		<br /><br />
		
		<a id="AddLandingPage" href="#" style="display: inline-block;">Add Landing Page</a>
		
		<br /><br /><br />
		
		<div id="LandingPages">
			
		</div>
		
	</form>


</div>

<script type="text/javascript">
	$('#AddLandingPage').click(function() {
		var totalLandingPage = $('.landingPageUrl').length;

		
		var landingPageDiv = $(document.createElement('div')).css('border', '1px #efefef solid');

		//landing page url
		var landingPageUrlInput = $(document.createElement('input')).addClass('formtxt').addClass('landingPageUrl').attr('name', 'landingPageUrl_'+totalLandingPage);
		var landingPageUrlLabel = $(document.createElement('label'));
		var landingPageUrlSpan = $(document.createElement('span'));

		landingPageUrlSpan.html('Landing Page:');
		
		landingPageUrlLabel.append(landingPageUrlSpan);
		landingPageUrlLabel.append(landingPageUrlInput);

		landingPageDiv.append(landingPageUrlLabel);
		//landing page url
		
		
		//landing page countries
		var landingPageCountryInput = $(document.createElement('select')).addClass('formselect').addClass('landingPageCountry').attr('name', 'landingPageUrl_'+totalLandingPage);
		var landingPageCountryLabel = $(document.createElement('label'));
		var landingPageCountrySpan = $(document.createElement('span'));

		landingPageUrlSpan.html('Landing Page:');
		
		//landing page countries

		
		$('#LandingPages').append(landingPageDiv);

		return false;
	});
</script>