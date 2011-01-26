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

	<div style="font-weight: bold; font-size: 14px;">Geotarget Code</div>
	<div>Please place this coding on a blank php file. Viewers will then be redirected accordingly when they access the page.</div>
	<div style="background-color: #efefef; height: 60px; line-height: 60px; text-align: center; border: 1px #ababab solid;">
		<?php 
			$Code = "
						<script type=\"text/javascript\" src=\"http://track.bevomedia.com/geo/{$_GET['ID']}/{$this->User->id}/".'<?=$_SERVER["REMOTE_ADDR"]?>'."\"></script>	
					";
			echo htmlentities($Code);
		?>
	</div>
	
	<br />
	
	<div id="map_canvas" style="width:100%; height:400px"></div>
	
	
	<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=<?=$this->GoogleMaps_API?>" type="text/javascript"></script> 
    
    
	<script type="text/javascript">
		var map = null;
		var geocoder = null;
		var markers  = new Array();
		
	  	function initialize() 
	  	{
	    	var latlng = new google.maps.LatLng(0, 0);

	    	map = new GMap2(document.getElementById("map_canvas"));
	    	map.setUIToDefault();
	    	map.setCenter(latlng, 2);
	    	
	    	geocoder = new GClientGeocoder();
	    	map.addControl(new GLargeMapControl());
	    	map.addControl(new GScaleControl());
	  	}

		initialize();

	  	function showAddress(address) 
	  	{
    		if (geocoder) {
		        geocoder.getLatLng(
		          address,
		          function(point) {
		            if (!point) {
		              alert(address + " not found");
		            } else {
		              var marker = new GMarker(point);
		              map.addOverlay(marker);

		              GEvent.addListener(marker, "click", function() {
	            	  	  marker.openInfoWindowHtml(address);
	            	  });
		              
//		              marker.openInfoWindowHtml(address);
		              markers.push(marker);  
		            }
		          }
		        );
	        }
	    }

	    function clearMarkers()
	    {
			for (var i=0; i<markers.length; i++)
			{
				map.removeOverlay(markers[i]);
			}

			markers = new Array();
	    }

		function redrawMarkers()
		{
			clearMarkers();

			$('.countrySelect').each(function(index) {

				var landingPageId = $(this).attr('landingPageId');
				var count = $(this).attr('count');

				var containerId = '#landingPageCountries_'+landingPageId+'_'+count;
				
				var countryValue = $(containerId+' #country option:selected').text();
				var regionValue = $(containerId+' #region option:selected').text();
				var cityValue = $(containerId+' #city option:selected').text();

				var string = "";

				if (cityValue!="Any City") {
					string += cityValue;
				}

				if (regionValue!="Any Region") {
					string += ((string!='')?", ":'')+regionValue;
				}

				if (countryValue!="Any Country") {
					string += ((string!='')?", ":'')+countryValue;
				}		

				if (string!="") showAddress(string);
				
			});
		}
	    
	</script>
	
	

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
