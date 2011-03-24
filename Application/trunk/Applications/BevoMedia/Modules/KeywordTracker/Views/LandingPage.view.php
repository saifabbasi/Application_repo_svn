<?php
require_once(PATH . "Legacy.Abstraction.class.php");

global $userId, $isSelfHosted;
$userId = $this->User->id;
$isSelfHosted = $this->User->IsSelfHosted();
$stDate = $this->StartDate;
$enDate = $this->EndDate;
$isTrackerPage = true;

$Sql = "SELECT 
			clicks.referrerUrl AS referrerUrl,
			lps.landingPageUrl AS url,
			COALESCE( COUNT( DISTINCT  `clicks`.`id` ) , 0 ) AS clicks, 
			(COALESCE( SUM(  `subid`.`clicks` ) , 0 ) + COALESCE( SUM(  `clicks`.`clickThrough` ) , 0 )) AS click_thrus,
			
			
			COALESCE( SUM(  `subid`.`conversions` ) , 0 ) AS actions
		FROM (
		 `bevomedia_tracker_clicks`  `clicks` 
		LEFT JOIN  `bevomedia_user_aff_network_subid`  `subid` ON ( (
		(
		 `clicks`.`user__id` =  `subid`.`user__id`
		)
		AND (
		 `clicks`.`subId` =  `subid`.`subId`
		)
		AND (
		 `subid`.`statDate` >=  `clicks`.`clickDate`
		) )
		)
		JOIN bevomedia_tracker_landing_pages AS lps ON (  `clicks`.`landingPageId` = lps.id ) 
		AND (
		`clicks`.`user__id` = {$userId}
		)
		AND (
		`clicks`.`clickDate` 
		BETWEEN  '$stDate'
		AND  '$enDate'
		)
		)
		GROUP BY  
			clicks.id
		ORDER BY lps.landingPageUrl
		";
//$query = "
//		SELECT
//			lps.landingPageUrl AS url,
//			COALESCE(COUNT(DISTINCT stats.id), 0) AS clicks,
//			COALESCE(SUM(stats.clicks), 0) AS click_thrus,
//			COALESCE(SUM(stats.conversions), 0) AS actions
//		FROM
//			bevomedia_view_click_stats AS stats
//			JOIN bevomedia_tracker_landing_pages AS lps ON
//				stats.landingPageId = lps.id
//		WHERE
//			stats.user__id = $userId
//			AND stats.clickDate BETWEEN '$stDate' AND '$enDate'
//		GROUP BY
//			lps.landingPageUrl
//		ORDER BY
//			lps.landingPageUrl
//	";
$query = mysql_query($Sql);

$data = array();

while($row = mysql_fetch_assoc($query))
{
	if(isset($data[$row['url']])) {
	    $data[$row['url']]['referrerUrls'][] = $row;
	    $data[$row['url']]['clicks'] += $row['clicks'];
	    $data[$row['url']]['click_thrus'] += $row['click_thrus'];
	    $data[$row['url']]['actions'] += $row['actions'];
	}else {
	    $data[$row['url']] = $row;
	    $data[$row['url']]['referrerUrls'] = array($row);
	}
}

if(isset($_GET['ExportCSV']) && $_GET['ExportCSV'] == 'FILE')
{
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=Offer.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    print '"URL","Clicks","Click Throughs","CTR","Actions","Signup %"' . "\r\n";
    foreach($data as $row)
    {
        $temp = array();
        $temp['url'] = $row['url'];
        $temp['clicks'] = (isset($row['clicks'])?($row['clicks']):'0'); $row['clicks'] = $temp['clicks'];
        $temp['click_thrus'] = (isset($row['click_thrus'])?($row['click_thrus']):'0');
        $temp['ctr'] = ($row['clicks'] == 0) ? 0 : $row['click_thrus'] / $row['clicks'] * 100;
        $temp['actions'] = (isset($row['actions'])?($row['actions']):'0'); $row['actions'] = $temp['actions'];
        $temp['signup_rate'] = ($row['click_thrus'] == 0) ? 0 : $row['actions'] / $row['click_thrus'] * 100;

        $First = true;
        foreach($temp as $v)
        {
            echo ($First)?'':",";
            $First = false;
            print '"' . $v .'"';
        }
        print "\r\n";
    }
    exit;
}
?>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','overview','lp'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>


<form method="get">
<div class="option" align="right" style="line-height: 24px;">
	<label for="datepicker">Date(s)</label>
	<input type="text" value="<?=$this->DateRangeString?>" id="datepicker" name="DateRange" class="formtxt">
	<input class="formsubmit track_apply floatright" type="submit" value="Apply">
</div>
</form>

<br />

<style type="text/css">
.landingPageRow {
	cursor: pointer;
}
.referrerRow {
	display: none;
	color: #666;
}
.referrerRow tr td {
	background-color: #DFE9FF;
	}
</style>
<script>
$(document).ready(function(){

	$('.landingPageRow').click(function() {
		var val = $('.btn', this).attr('src');
		if($(this).parent().next().css('display') == 'none') {
			val = val.replace('plus', 'minus');
		}else{
			val = val.replace('minus', 'plus');
		}
		$('.btn', this).attr('src', val);
		$(this).parent().next().toggle();
	});
	
});

</script>

<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td>Landing Page URL</td>
		<td style="text-align: center;">Clicks</td>
		<td style="text-align: center;">Click Throughs</td>
		<td style="text-align: center;">CTR</td>
		<td style="text-align: center;">Actions</td>
		<td style="text-align: center;">Signup %</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
	<?php
	$i = 0;
	$total_clicks = 0; $total_conversions = 0; $total_revenue = 0; $total_cost = 0;
	if(count($data) > 0)
	{
	    foreach($data as $row)
	    {
	        $ctr = ($row['clicks'] == 0) ? 0 : $row['click_thrus'] / $row['clicks'] * 100;
	        $signup_rate = ($row['click_thrus'] == 0) ? 0 : $row['actions'] / $row['click_thrus'] * 100;
	        ?>
		<tr class="landingPageRow" <?php if($i++ % 2 == 1) { echo ' class="AltRow"'; } ?>>
			<td class="border">&nbsp;</td>
			<td>
				<?php echo htmlentities($row['url']); ?>
				<?php if(count($row['referrerUrls']) > 0):?>
					<img class="btn" align="right" src="/Themes/BevoMedia/img/button-plus.gif" />
				<?php endif;?>
			
			</td>
			<td class="number"><?php echo number_format($row['clicks'], 0); ?></td>
			<td class="number"><?php echo number_format($row['click_thrus'], 0); ?></td>
			<td class="number"><?php echo number_format($ctr, 2); ?>%</td>
			<td class="number"><?php echo number_format($row['actions'], 0); ?></td>
			<td class="number"><?php echo number_format($signup_rate, 2); ?>%</td>
			<td class="tail">&nbsp;</td>
		</tr>
		</tbody>
		
		<tbody class="referrerRow">
			<?php foreach($row['referrerUrls'] as $refRow):?>
		        <?php 
		        $ctr = ($refRow['clicks'] == 0) ? 0 : $refRow['click_thrus'] / $refRow['clicks'] * 100;
		        $signup_rate = ($refRow['click_thrus'] == 0) ? 0 : $refRow['actions'] / $refRow['click_thrus'] * 100;
		        ?>
			<tr <?php if($i++ % 2 == 1) { echo ' class="AltRow"'; } ?>>
				<td class="border">&nbsp;</td>
				<td style="word-wrap: break-word">
					<div style="width: 600px;">
					<b>Referrer:</b> <?php echo ($refRow['referrerUrl'] != '')?htmlentities($refRow['referrerUrl']):'<i>No Referrer</i>'; ?>
					</div>
				</td>
				<td class="number"><?php echo number_format($refRow['clicks'], 0); ?></td>
				<td class="number"><?php echo number_format($refRow['click_thrus'], 0); ?></td>
				<td class="number"><?php echo number_format($ctr, 2); ?>%</td>
				<td class="number"><?php echo number_format($refRow['actions'], 0); ?></td>
				<td class="number"><?php echo number_format($signup_rate, 2); ?>%</td>
				<td class="tail">&nbsp;</td>
			</tr>
			<?php endforeach;?>
		</tbody>
		
		<tbody>
		<?php
		$total_clicks += $row['clicks'];
		@$total_click_thrus += $row['click_thrus'];
		@$total_actions += $row['actions'];
	    }
	}
	else
	{
	    ?>
		<tr>
			<td class="border">&nbsp;</td>
			<td colspan="6">No landing pages found for the selected time frame.</td>
			<td class="tail">&nbsp;</td>
		</tr>
		<?php
	}

	$total_ctr = ($total_clicks == 0) ? 0 : $total_click_thrus / $total_clicks * 100;
	@$total_signup_rate = ($total_click_thrus == 0) ? 0 : $total_actions / $total_click_thrus * 100;
	?>
		<tr class="total">
			<td class="border">&nbsp;</td>
			<td>Total</td>
			<td class="number"><?php echo number_format($total_clicks, 0); ?></td>
			<td class="number"><?php echo @number_format($total_click_thrus, 0); ?></td>
			<td class="number"><?php echo @number_format($total_ctr, 2); ?>%</td>
			<td class="number"><?php echo @number_format($total_actions, 0); ?></td>
			<td class="number"><?php echo @number_format($total_signup_rate, 2); ?>%</td>
			<td class="tail">&nbsp;</td>
		</tr>
	</tbody>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="6">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>


<a class="tbtn floatright" href='?<?php echo $_SERVER['QUERY_STRING']?>&ExportCSV=FILE'>Export to CSV</a>

<br />
<br />

<script type="text/javascript">
$(document).ready(function () {
	$('#datepicker').daterangepicker();
});
</script>

