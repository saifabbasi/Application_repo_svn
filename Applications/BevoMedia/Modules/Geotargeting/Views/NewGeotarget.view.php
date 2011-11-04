<?php echo SoapPageMenu('geotargeting','geotargets','new',true); ?>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>



<script type="text/javascript">
	var countries = <?php echo json_encode($this->Countries); ?>
</script>

<br />


	<form method='post' class="geotargeting">
		
		<label>
			<span style="width: 50px; display: inline-block;">Name: </span>
			<input class="formtxt" name='name' type='text' value='' />			
		</label>
		
		<br /><br />
		
		<a id="AddLandingPage" href="#">Add Landing Page</a>
		|
		<a id="AddLandingDaytarget" href="#">Add Landing Daytarget</a>
		
		<br /><br />
		
		<div id="LandingPages">
			
		</div>
		
		<input type="submit" class="create" id="Submit" name="Submit" value="" />
		
	</form>



<script type="text/javascript">

	var landingPagesCount = 0; 

	$('#AddLandingPage').click(function() {
		
		var loadDiv = $(document.createElement('div')).attr('class', 'landingPageContainer').attr('id', 'landingPageContainer_'+landingPagesCount);
		loadDiv.load('/BevoMedia/Geotargeting/LandingPageInclude.html?ajax=true&count='+landingPagesCount);

		$('#LandingPages').append(loadDiv);

		$('#LandingPages').append($(document.createElement('br')));

		landingPagesCount++;
		
		return false;
	});

	$('#AddLandingDaytarget').click(function() {
		
		var loadDiv = $(document.createElement('div')).attr('class', 'landingPageContainer').attr('id', 'landingPageContainer_'+landingPagesCount);
		loadDiv.load('/BevoMedia/Geotargeting/DaytargetInclude.html?ajax=true&count='+landingPagesCount);

		$('#LandingPages').append(loadDiv);

		$('#LandingPages').append($(document.createElement('br')));

		landingPagesCount++;
		
		return false;
	});
</script>
