<?php echo SoapPageMenu('timetargeting','timetargets','existing',true); ?>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>



<script type="text/javascript">
	var countries = <?php echo json_encode($this->Countries); ?>
</script>

<br />


<form method='post' class="geotargeting">
	
	<label>
		<span  style="width: 50px; display: inline-block;">Name: </span>
		<input class="formtxt" name='name' type='text' value='<?php echo $this->Name;?>'>			
	</label>
	
	<br /><br />
	
	<a id="AddLandingPage" href="#">Add Landing Page</a>
	
	<br /><br />
	
	<div id="LandingPages">
		
	</div>
	
	<input type="submit" class="edit" id="Submit" name="Submit" value="" />
	
</form>

<br /><br />

<div style="font-weight: bold; font-size: 14px;">Timetarget Code</div>
<div>Please place this coding on a blank php file. Viewers will then be redirected accordingly when they access the page.</div>
<textarea class="code" style="width: 100%; margin: 0px; line-height: 20px; padding: 0px; padding-top: 20px;" wrap="off"><?php 
		$Code = "<script type=\"text/javascript\" src=\"http://track.bevomedia.com/day/{$_GET['ID']}/{$this->User->id}/".'<?=urlencode(urlencode(base64_encode(gzcompress(serialize($_GET),9))))?>'."\"></script>";
		echo htmlentities($Code);
	?></textarea>

<br />




<script type="text/javascript">

	var landingPagesCount = 0; 

	$('#AddLandingPage').click(function() {

		return addLandingPage(0);
		
	});

	function addLandingPage(id) {

		var loadDiv = $(document.createElement('div')).attr('class', 'landingPageContainer').attr('id', 'landingPageContainer_'+landingPagesCount);
		loadDiv.load('/BevoMedia/Timetargeting/LandingPageInclude.html?ajax=true&count='+landingPagesCount+'&ID='+id);

		$('#LandingPages').append(loadDiv);

		$('#LandingPages').append($(document.createElement('br')));

		landingPagesCount++;
		
		return false;
		
	}

	$(document).ready(function() {

		$('textarea.code').live('click', function() {
			$(this).select();
		})
		
	});
</script>

<script type="text/javascript">

<?php 
	foreach ($this->URLs as $URL) {
?>
	addLandingPage(<?php echo $URL->LocationID;?>);
<?php
	}
?>

</script>

	