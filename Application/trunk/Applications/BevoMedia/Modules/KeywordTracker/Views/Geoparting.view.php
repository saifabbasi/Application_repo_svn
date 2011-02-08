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

<br/><br/><br/><br/>
<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
	    <td class="hhl">&nbsp;</td>
		<td>Location</td>
		<td>Clicks</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
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
           <?php echo $value ?>
        </td>
		<td class="tail"></td>
    </tr>
    
    <?php /* foreach ($this->data['data'] as $data): ?>
    <?php 
        switch($groupBy) {
        case 'city':
            if ($data->CITY != $keySplit[2]) {
                continue;
            }
        case 'region':
            if ($data->REGION != $keySplit[1]) {
                continue;
            }
        case 'country':
            if ($data->COUNTRY_NAME != $keySplit[0]) {
                continue;
            }
        }
    ?>
    <tr>
		<td class="border"></td>
            <td colspan="9">
            <?php echo $value ?>
        </td>
		<td class="tail"></td>
    </tr>
    <?php endforeach; */?>

<?php endforeach;?>
    </tbody>
	<tr class="table_footer">
		<td class="hhl"></td>
		<td colspan="2"></td>
		<td class="hhr"></td>
	</tr>
</table>
		
		<?php
		/*
		$i = 0; $previous_account = '';
		$total_clicks = 0; $total_conversions = 0; $total_revenue = 0; $total_cost = 0; $total_profit = 0;
		if(count($data) > 0)
		{
			foreach($data as $row)
			{
				if($previous_account != $row['account_name'])
				{
					?>
					<tr>
						<td class="border"></td>
						<td colspan="9"><strong><?php echo htmlentities($row['account_name']); ?></strong></td>
						<td class="tail"></td>
					</tr>
					<?php
					$previous_account = $row['account_name'];
				}
				
				@$ctr = ($row['clicks'] == 0) ? 0 : $row['conversions'] / $row['clicks'] * 100;
				@$cpc = ($row['clicks'] == 0) ? 0 : $row['cost'] / $row['clicks'];
				@$epc = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];
				?>
				<tr<?php if($i++ % 2 == 1) { echo ' class="AltRow"'; } ?>>
					<td class="border"></td>
					<td><?php echo htmlentities($row['name']); ?></td>
					<td class="number"><?php echo @number_format($row['clicks'], 0); ?></td>
					<td class="number"><?php echo @number_format($row['conversions'], 0); ?></td>
					<td class="number"><?php echo @number_format($ctr, 2); ?>%</td>
					<td class="number">$<?php echo @number_format($row['revenue'], 2); ?></td>
					<td class="number">$<?php echo @number_format($row['cost'], 2); ?></td>
					<td class="number">$<?php echo @number_format($row['profit'], 2); ?></td>
					<td class="number">$<?php echo @number_format($cpc, 2); ?></td>
					<td class="number">$<?php echo @number_format($epc, 2); ?></td>
					<td class="tail"></td>
				</tr>
				<?php
				@$total_clicks += $row['clicks'];
				@$total_conversions += $row['conversions'];
				@$total_revenue += $row['revenue'];
				@$total_cost += $row['cost'];
				@$total_profit += $row['profit'];
			}
		}
		else
		{
			?>
			<tr>
				<td class="border"></td>
				<td colspan="9">No active campaigns found for the selected time frame.</td>
				<td class="tail"></td>
			</tr>
			<?php
		}
		
		@$total_ctr = ($total_clicks == 0) ? 0 : $total_conversions / $total_clicks * 100;
		@$total_cpc = ($total_clicks == 0) ? 0 : $total_cost / $total_clicks;
		@$total_epc = ($total_clicks == 0) ? 0 : $total_revenue / $total_clicks;
		?>
		<tr class="total">
			<td class="border"></td>
			<td>Total</td>
			<td class="number"><?php echo @number_format($total_clicks, 0); ?></td>
			<td class="number"><?php echo @number_format($total_conversions, 0); ?></td>
			<td class="number"><?php echo @number_format($total_ctr, 2); ?>%</td>
			<td class="number">$<?php echo @number_format($total_revenue, 2); ?></td>
			<td class="number">$<?php echo @number_format($total_cost, 2); ?></td>
			<td class="number">$<?php echo @number_format($total_profit, 2); ?></td>
			<td class="number">$<?php echo @number_format($total_cpc, 2); ?></td>
			<td class="number">$<?php echo @number_format($total_epc, 2); ?></td>
			<td class="tail"></td>
		</tr>
	</tbody>
	<tr class="table_footer">
		<td class="hhl"></td>
		<td colspan="9"></td>
		<td class="hhr"></td>
	</tr>
</table>
*/
?>
