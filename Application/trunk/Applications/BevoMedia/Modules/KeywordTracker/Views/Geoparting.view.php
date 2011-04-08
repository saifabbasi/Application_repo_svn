<?php 

if ($this->User->vaultID == 0) {
    header('Location: /BevoMedia/Geotargeting/RequiresVerified.html');
	exit;
}

if (!isset($_GET['sortBy'])) {
	$_GET['sortBy'] = 'clicks';
	$_GET['sortDirection'] = 'desc';
}

?>

<?php 
	function sortItems($a, $b)
	{
		if ($_GET['sortBy']=='clicks') 
		{
			if ($a['clicks']==$b['clicks']) {
				return 0;
			}
			
			if ($_GET['sortDirection']=='asc') {
				return ($a['clicks']<$b['clicks'])?-1:1;
			} else {
				return ($a['clicks']>$b['clicks'])?-1:1;
			}
		} else 
		if ($_GET['sortBy']=='conversions') 
		{
			if ($a['conversions']==$b['conversions']) {
				return 0;
			}
			
			if ($_GET['sortDirection']=='asc') {
				return ($a['conversions']<$b['conversions'])?-1:1;
			} else {
				return ($a['conversions']>$b['conversions'])?-1:1;
			}
		}
	}
	
	if (isset($this->data['results']) && is_array($this->data['results'])) {
		uasort($this->data['results'], 'sortItems');
	}
?>

<?php echo SoapPageMenu('kwt','geotargeting','geoparting', false); ?>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<?php 
	$campaign = isset($_GET['campaign'])?$_GET['campaign']:0;
	$adGroup = isset($_GET['adGroup'])?$_GET['adGroup']:0;
	$groupBy = isset($_GET['groupBy'])?$_GET['groupBy']:'country';
?>
<form method="get">

<div class="filtering formslim">
	<div class="col-left">
		<div class="option">
			<label for="ppcadgroup">Campaign</label>
			<select class="formselect" name="campaign" id="campaign">
				<option value="0">--</option>
				<?php
					$sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = ".$this->User->id." AND Name != '' ORDER BY Name"; 
					$query = mysql_query($sql);
					while($row = mysql_fetch_array($query))
					{
						echo '<option value="'.$row['id'].'"';
						if($row['id'] == $campaign)
							echo ' selected="selected"';
						echo '>'.htmlentities($row['name']) .'</option>';
					}
				?>
			</select>
		</div>
		
		<div class="option">
    		<label for="pcccampaign">AdGroup</label>
    		<select class="formselect" name="adGroup" id="adgroup">
    			<option value="0">--</option>
    			<?php
    				if(!empty($campaign))
    				{
    					$sql = "SELECT 
    								bevomedia_ppc_adgroups.id, 
    								bevomedia_ppc_adgroups.name 
    							FROM 
    								bevomedia_ppc_adgroups,
    								bevomedia_ppc_campaigns
    							WHERE 
    								(bevomedia_ppc_campaigns.id = bevomedia_ppc_adgroups.campaignId) AND
    								(user__id = ".$this->User->id.") AND 
    								(campaignId = ".$campaign.")
    							ORDER BY name";
    					$query = mysql_query($sql);
    					while($row = mysql_fetch_array($query))
    					{
    						echo '<option value="'.$row['id'].'"';
    						if($row['id'] == $adGroup)
    							echo ' selected="selected"';
    						echo '>'.htmlentities($row['name']).'</option>';
    					}
    				}
    				?>
    		</select>
    	</div>
    	
    	<div class="option">
    		<label>Group By:</label>
			<label>
				<input type="radio" name="groupBy" value="country" <?=($groupBy=='country')?'checked':''?>> Country
			</label>
			<label>
				<input type="radio" name="groupBy" value="region" <?=($groupBy=='region')?'checked':''?>> Region
			</label>
			<label>
				<input type="radio" name="groupBy" value="city" <?=($groupBy=='city')?'checked':''?>> City
			</label>	
    	</div>
    			
	</div>
	<div class="col-right">
		<div class="option">
			<label for="datepicker">Date(s)</label>
			<input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?= htmlentities($this->DateRange); ?>" />
		</div>
	</div>
	<div class="actions">
		<input class="formsubmit track_apply floatright" type="submit" name="submit" value="Apply" />
		<div class="clear"></div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {

		$('#campaign').change( function() {
			
			$.getJSON("/BevoMedia/KeywordTracker/json.html?list=advar&ppcadgroup=" + $(this).val(), function(data) {
				var options = '<option value="0">--</option>';
				for (var i = 0; i < data.length; i++) {
					options += '<option value="' + data[i].id + '">' + data[i].title + '</option>';
				}
				$('#adgroup').html(options);
				$('#adgroup').val('');
			});
		});

		

	});
</script>
</form>

<?php //echo '<pre>'; print_r($this->data['results']); ?>

<br/>
<?php 
	$sortUrl = '/BevoMedia/KeywordTracker/Geoparting.html?';
	foreach ($_GET as $Key => $Value)
	{
		if ($Key=='sortBy') continue;
		if ($Key=='sortDirection') continue;
		
		$sortUrl .= $Key.'='.$Value.'&';
	}
	/*
	if (isset($_GET['sortBy']))
	{
		if ($_GET['sortBy'])
			$sortUrl .= 
	}*/
	
	
	
?>
<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
	    <td class="hhl">&nbsp;</td>
		<td>Location</td>
		<td><a href="<?=$sortUrl?>sortBy=clicks&sortDirection=<? echo (($_GET['sortBy']=='clicks') && ($_GET['sortDirection']=='desc'))?'asc':'desc';  ?>">Clicks</a></td>
		<td><a href="<?=$sortUrl?>sortBy=conversions&sortDirection=<? echo (($_GET['sortBy']=='conversions') && ($_GET['sortDirection']=='desc'))?'asc':'desc';  ?>">Conversions</a></td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
<?php if(isset($this->data['results'])):?>
<?php foreach($this->data['results'] as $key=>$value): ?>
<?php
    $keySplit = explode(',', $key);
    foreach($keySplit as $keySplitKey=>$keySplitItem) {
        $keySplit[$keySplitKey] = ucwords(strtolower($keySplitItem));
    }
    $key = implode($keySplit, ' &gt; ');
?>
    <script language="javascript">
        $(document).ready(function(){
            showAddress("<?php echo implode($keySplit, ':');?>", "Clicks: <?php echo $value['clicks'];?><br/>\nConversions: <?php echo $value['conversions']?>");
        });
    </script>

    <tr>
		<td class="border"></td>
        <td colspan="1">
            <b><?php echo $key;?></b>
        </td>
        <td>
           <?php echo $value['clicks'] ?>
        </td>
        <td>
           <?php echo $value['conversions'] ?>
        </td>
		<td class="tail"></td>
    </tr>

<?php endforeach;?>
<?php endif;?>
    </tbody>
	<tr class="table_footer">
		<td class="hhl"></td>
		<td colspan="3"></td>
		<td class="hhr"></td>
	</tr>
</table>

<br/><br/><br/>

<div id="map_canvas" style="width:100%; height:400px"></div> 
<?php /*
<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=ABQIAAAAgl5Nx3uJ69j4bI4WcxOLfxT8lA5YgEtP1VCCPa_Q-VTD4uCnhhRGlmZX60OJWzrDgRH0Jg-q2zhnGw" type="text/javascript"></script> 
*/ ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=ABQIAAAAgl5Nx3uJ69j4bI4WcxOLfxTE7Nt0VSXPfmZOGdFPbd5cXCuKbhQ2zudNBOu-iXhz8tbbkLuWoA_O2Q" type="text/javascript"></script> 



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

  	function showAddress(address, additional) 
  	{
  	    if (!additional) {
  	        additional = '';
        }else{
            additional = '<br/>' + additional;
        }
		if (geocoder) {
	        geocoder.getLatLng(
	          address,
	          function(point) {
	            if (!point) {
	              //alert(address + " not found");
	            } else {
	              var marker = new GMarker(point);
	              map.addOverlay(marker);

	              GEvent.addListener(marker, "click", function() {
            	  	  marker.openInfoWindowHtml(address + " " + additional);
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
 
	$(document).ready(function() {
 
		$('textarea.code').live('click', function() {
			$(this).select();
		})
		
	});
</script> 
