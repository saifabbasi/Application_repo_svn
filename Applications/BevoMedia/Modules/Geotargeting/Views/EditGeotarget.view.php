<?php echo SoapPageMenu('geotargeting','geotargets','existing',true); ?>
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

	<div style="font-weight: bold; font-size: 14px;">Geotarget Code:</div>
	<div style="background-color: #efefef; height: 60px; line-height: 60px; text-align: center; border: 1px #ababab solid;">
		<?php 
			$Code = "
						<script type=\"text/javascript\" src=\"http://track.bevomedia.com/geo/{$_GET['ID']}/{$this->User->id}/".'<?=$_SERVER["REMOTE_ADDR"]?>'."\"></script>	
					";
			echo htmlentities($Code);
		?>
	</div>

<script type="text/javascript">

	var landingPagesCount = 0; 

	$('#AddLandingPage').click(function() {

		return addLandingPage(0);
		
	});

	function addLandingPage(id) {

		var loadDiv = $(document.createElement('div')).attr('class', 'landingPageContainer').attr('id', 'landingPageContainer_'+landingPagesCount);
		loadDiv.load('/BevoMedia/Geotargeting/LandingPageInclude.html?ajax=true&count='+landingPagesCount+'&ID='+id);

		$('#LandingPages').append(loadDiv);

		$('#LandingPages').append($(document.createElement('br')));

		landingPagesCount++;
		
		return false;
		
	}
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
