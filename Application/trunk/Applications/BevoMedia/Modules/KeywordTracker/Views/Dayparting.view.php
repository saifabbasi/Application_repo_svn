<?php 

if ($this->User->vaultID == 0) {
    header('Location: /BevoMedia/Geotargeting/RequiresVerified.html');
	exit;
}
?>

<?php echo SoapPageMenu('kwt','timetargeting','dayparting', false); ?>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<?php 
	$campaign = isset($_GET['campaign'])?$_GET['campaign']:0;
	$adGroup = isset($_GET['adGroup'])?$_GET['adGroup']:0;
	$groupBy = isset($_GET['groupBy'])?$_GET['groupBy']:'time';
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
				<input type="radio" name="groupBy" value="time" <?=($groupBy=='time')?'checked="checked"':''?>> Time
			</label>
			<label>
				<input type="radio" name="groupBy" value="day" <?=($groupBy=='day')?'checked="checked"':''?>> Day
			</label>	
			<label>
				<input type="radio" name="groupBy" value="timeDay" <?=($groupBy=='timeDay')?'checked="checked"':''?>> Time &amp; Day
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
		<div style="float:left; width:50%; text-align: right;"><b>Current Time:</b> &nbsp;<div style="float:right;" class="jclock"></div></div>
	
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
 
 
 
<br/>
<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
	    <td class="hhl">&nbsp;</td>
		<td>When</td>
		<td>Clicks</td>
		<td>Conversions</td>
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

