<?php echo SoapPageMenu('timetargeting','timetargets','new',true); ?>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>



<script type="text/javascript">
	var countries = <?php echo json_encode($this->Countries); ?>
</script>

<br />

	<div style="width:150px; text-align: left;"><b>Current Time:</b> &nbsp;<div style="float:right;" class="jclock"></div></div>
	
	<script src="/Themes/BevoMedia/jquery.jclock-1.2.0.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(function($) {
	    var options={
	        utc: true,
	        utc_offset: -4
	      };
	    $('.jclock').jclock(options);
	});
	</script>

	<br />

	<form method='post' class="geotargeting">
		
		<label>
			<span style="width: 50px; display: inline-block;">Name: </span>
			<input class="formtxt" name='name' type='text' value=''>			
		</label>
		
		<br /><br />
		
		<a id="AddLandingPage" href="#">Add Landing Page</a>
		
		<br /><br />
		
		<div id="LandingPages">
			
		</div>
		
		<input type="submit" class="create" id="Submit" name="Submit" value="" />
		
	</form>



<script type="text/javascript">

	var landingPagesCount = 0; 

	$('#AddLandingPage').click(function() {
		
		var loadDiv = $(document.createElement('div')).attr('class', 'landingPageContainer').attr('id', 'landingPageContainer_'+landingPagesCount);
		loadDiv.load('/BevoMedia/Timetargeting/LandingPageInclude.html?ajax=true&count='+landingPagesCount);

		$('#LandingPages').append(loadDiv);

		$('#LandingPages').append($(document.createElement('br')));

		landingPagesCount++;
		
		return false;
	});
</script>
