<?php
require_once(PATH . "Legacy.Abstraction.class.php");

global $userId, $isSelfHosted;
$userId = $this->User->id;
$isSelfHosted = $this->User->IsSelfHosted();
$isTrackerPage = true;
$stDate = $this->StartDate;
$enDate = $this->EndDate;
$filtering_sql = $this->filter->getSql();
$filtering_rev_sql = $filtering_sql[0];
$filtering_cost_sql = $filtering_sql[1];
 
$dateDiff = ((strtotime($enDate) - strtotime($stDate))/86400)+1;
$data = array();

for ( $i = 0; $i < $dateDiff; $i++ ) {
	$data[date("Y-m-d", strtotime("{$stDate} + {$i} days"))] = array();	
}  


$Sql = "SELECT 
			bevomedia_tracker_clicks.clickDate AS date, 
			SUM(bevomedia_user_aff_network_subid.revenue) AS revenue
		FROM 
			bevomedia_tracker_clicks
			INNER JOIN bevomedia_user_aff_network_subid ON ( (bevomedia_user_aff_network_subid.subId = bevomedia_tracker_clicks.subId) AND (bevomedia_tracker_clicks.clickDate = bevomedia_user_aff_network_subid.statDate) )
		WHERE 
			(bevomedia_tracker_clicks.user__id = {$userId}) AND 
			(bevomedia_tracker_clicks.clickDate BETWEEN '{$stDate}' AND '{$enDate}')
		GROUP BY
			bevomedia_tracker_clicks.clickDate

		"; //echo '<pre>'.$Sql;die;
$query = mysql_query($Sql);
while($row = mysql_fetch_object($query))
{
	if (!isset($data[$row->date]['revenue'])) {
		$data[$row->date]['revenue'] = 0;
	}
	
	$data[$row->date]['revenue'] = $data[$row->date]['revenue']+$row->revenue;
	$data[$row->date]['date'] = $row->date;
}

//$query = "
//	SELECT tc.clickDate AS date, tc.subId
//	FROM bevomedia_tracker_clicks AS tc
//	WHERE tc.user__id = $userId
//	AND tc.clickDate
//	BETWEEN '$stDate'
//	AND '$enDate'
//";
//echo '<pre>'.$query;die;
//
//$query = mysql_query($query);
//
//
//while($row = mysql_fetch_object($query))
//{
//	$sql = "
//		SELECT SUM(afs.revenue) AS revenue
//		FROM bevomedia_user_aff_network_subid AS afs 
//		WHERE afs.user__id = $userId
//		AND afs.subId = {$row->subId}
//		AND afs.statDate
//		BETWEEN '$stDate' 
//		AND '$enDate' 
//	";
////	echo '<pre>'.$sql;die;
//	$result = mysql_query($sql);
//	$revRow = mysql_fetch_object($result);
//	
//	$data[$row->date] = $data[$row->date]+$revRow->revenue;
//	
//}

/*
$query = "
SELECT 
    tc.clickDate as date, COALESCE(SUM(afs.revenue),0) AS revenue
FROM 
    bevomedia_tracker_clicks AS tc FORCE INDEX ( sub_id )
JOIN
    bevomedia_user_aff_network_subid AS afs FORCE INDEX ( SUB_ID )
ON
    tc.subId = afs.subId
WHERE
    afs.user__id = $userId 
AND 
    tc.user__id = $userId 
AND 
    tc.clickDate 
BETWEEN 
    '$stDate' 
AND 
    '$enDate' 
GROUP BY 
    tc.clickDate 
ORDER BY 
    tc.clickDate
";



$query = mysql_query($query);
$data = array();

while($row = mysql_fetch_array($query))
{
	$data[$row['date']] = $row;
}
*/

// Get all the unique creatives
$query = "
		SELECT
			DISTINCT creativeId
		FROM
			bevomedia_tracker_clicks
		WHERE
			clickDate BETWEEN '$stDate' AND '$enDate'
			AND user__id= $userId
			AND creativeId != ''
	";
			
$query = mysql_query($query);
while($row = mysql_fetch_array($query))
{
	if(is_numeric($row['creativeId']))
		$creatives[] = (int)$row['creativeId'];
}
$creatives[] = 999999; // filler



$query = "SELECT
				COALESCE(SUM(stats.Cost),0) AS cost,
				stats.statDate AS date
			FROM
				bevomedia_ppc_advariations AS advars
				JOIN bevomedia_ppc_advariations_stats AS stats ON
					advars.ID = stats.advariationsId
			WHERE
				(advars.apiAdId IN (".join(',',$creatives).")) AND
				(stats.statDate BETWEEN '$stDate' AND '$enDate')
			GROUP BY
				stats.statDate
			ORDER BY
				stats.statDate
	";

$query = mysql_query($query);

$preData = array();
while($row = mysql_fetch_array($query))
{
	$preData[$row['date']] = $row;
}

$startDate = strtotime($stDate);
$endDate = strtotime($enDate);

while ($startDate<=$endDate) {
	$date = date('Y-m-d', $startDate);
	if (!isset($preData[$date])) {
		$preData[$date] = array('date' => $date, 'cost' => 0);
	}
	
	$startDate = strtotime('+1 day', $startDate);
}

foreach ($preData as $row)
{
	
	if(!empty($data[$row['date']]))
	{
		@$data[$row['date']]['cost'] = $row['cost'];
		@$data[$row['date']]['profit'] = $data[$row['date']]['revenue'] - $row['cost'] ;
	}else{
		$data[$row['date']] = array();
		$data[$row['date']]['cost'] = $data[$row['date']]['profit'] = $data[$row['date']]['revenue'] = 0;
		$data[$row['date']]['date'] = $row['date'];
	}
	
	$csql = "SELECT 
					COALESCE(SUM(s.Cost),0) AS cost
			FROM ((((`bevomedia_ppc_keywords_stats` `s`
			         JOIN `bevomedia_ppc_keywords` `k` ON((`s`.`keywordId` = `k`.`id`)))
			        JOIN `bevomedia_ppc_adgroups` `a` ON((`k`.`adGroupId` = `a`.`id`)))
			       JOIN `bevomedia_ppc_campaigns` `c` ON((`c`.`id` = `a`.`campaignId`)))
			      JOIN `bevomedia_keyword_tracker_keywords` `keyword` ON((`k`.`keywordId` = `keyword`.`id`)))
			WHERE
				c.user__id = $userId
				AND s.statDate BETWEEN '$row[date]' AND '$row[date]'
			GROUP BY
				c.user__id
				
			ORDER BY `s`.`statDate`,
			         `keyword`.`keyword`

	
			";
	
//	echo '<pre>'.$csql; die;
	
//	$csql = "
//			SELECT
//				COALESCE(SUM(stats.Cost),0) AS cost
//			FROM
//				bevomedia_view_ppc_stats AS stats
//			WHERE
//				stats.user__id = $userId
//				AND stats.statDate BETWEEN '$row[date]' AND '$row[date]'
//			GROUP BY
//				stats.user__id
//		";
				
	$cquery = mysql_query($csql);
	$crow = @mysql_fetch_assoc($cquery);
	@$data[$row['date']]['cost'] = (isset($crow['cost']))?$crow['cost']:0;
	@$data[$row['date']]['profit'] = $data[$row['date']]['revenue'] - $data[$row['date']]['cost'];
}


function sort_by_date($a, $b) {
	return  (strtotime($a['date']) > strtotime($b['date']));
}

uasort($data, 'sort_by_date');


// Create the chart XML
$steps = (count($data) > 10) ? count($data) / 10 : 1;
$chartXML = "<chart showBorder='0' bgAlpha='0,0' numberPrefix='$' formatNumberScale='0' labelDisplay='ROTATE' lineThickness='1' showValues='0' labelStep='".$steps."' slantLabels='1'>";

$chartXML .= "<categories>";
foreach ($data as $row)
	$chartXML .= "<category label='".date('m/d/Y', strtotime($row['date']))."'/>";
$chartXML .= "</categories>";

// Revenue
$chartXML .= "<dataset seriesName='Revenue'>";
foreach ($data as $row)
	$chartXML .= "<set value='".number_format($row['revenue'], 2, '.', '')."' />";
$chartXML .= "</dataset>";

// Profit
$chartXML .= "<dataset seriesName='Profit'>";
foreach ($data as $row)
	@$chartXML .= "<set value='".number_format($row['profit'], 2, '.', '')."' />";
$chartXML .= "</dataset>";

$chartXML .= "</chart>";


if(isset($_GET['ExportCSV']) && $_GET['ExportCSV'] == 'FILE')
{
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=Overview.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	print '"Date","Revenue","Expense","Profit","ROI"' . "\r\n";
	foreach($data as $row)
	{
		$roi = ($row['cost'] == 0) ? 0 : $row['profit'] / $row['cost'] * 100;
		print "\"$row[date]\",\"$row[revenue]\",\"$row[cost]\",\"$row[profit]\",\"$roi\"\r\n";
	}
	exit;
}

?>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','overview','overview'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
	

<?php //echo renderChart("assets/charts/MSLine.swf", "", $chartXML, "chartOverview", 600, 300, false, false); ?>


<form method="get">
	<div align="right">
		<label for="datepicker">Date(s)</label>
		<input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?= isset($_REQUEST['DateRange'])?htmlentities($_REQUEST['DateRange']):date('m/d/Y', strtotime('-1 day')).' - '.date('m/d/Y') ; ?>" />
		<input class="formsubmit btn_go_flush" type="submit">
	</div>
</form>

<br />

<!-- BEGIN Chart -->
<script type="text/javascript">
	$(function(){
		//make some charts
		$('#JQueryChartData').visualize({type: 'line'}).appendTo('#JQueryChartDisplay');
	});
</script>

<?php

	$ChartXML = new ChartXMLHelper();
	
	if(isset($_GET['Field']))
	{
		$ChartXML->Field = $_GET['Field'];
	}
	if(isset($_GET['Campaigns']))
	{
		$ChartXML->StatsShowRows = $_GET['Campaigns'];
	}
	
	$ChartData = array();
	$dra = array();
	$revenue = new stdClass();
	$revenue->Name = 'Revenue';
	$revenue->Stats = array();
	$cost = new stdClass();
	$cost->Name = 'Cost';
	$cost->Stats = array();
	foreach($data as $row)
	{
		$dra[] = array($row['date']);
		$revenue->Stats[] = $row['revenue'];
		$cost->Stats[] = $row['cost'];
	}
	
	$ChartData = array($revenue, $cost);

	$ChartXML->SetDateRangeArray($dra);
	$ChartXML->SetData($ChartData);
	
	$Out = $ChartXML->getJQueryChartOutput('Overview', 'JQueryChartData', 'JQueryChartDisplay', '', '0');
		echo $Out;
?>
<!-- ENDOF Chart -->

<style>
.visualize .visualize-info { padding: 3px 5px; background: #fafafa; border: 1px solid #888; position: relative; top: -20px; right: 10px; opacity: .8; }
.visualize .visualize-info { right: 0px; top: 225px; width: 100%; float:right; margin-bottom: 35%; }
</style>

<br/><br/><br/><br/>

<table cellspacing="0" class="btable" width="600">
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td>Date</td>
		<td width="100" style="text-align: center" class="STYLE4">Revenue</td>
		<td width="100" style="text-align: center" class="STYLE4">Expense</td>
		<td width="100" style="text-align: center" class="STYLE4">Profit</td>
		<td width="100" style="text-align: center" class="STYLE4">ROI</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	<tbody>
		<?php
		$i = 0;
		$total_revenue = 0; $total_cost = 0; $total_profit = 0;
		foreach($data as $row)
		{
			$roi = ($row['cost'] == 0) ? 0 : $row['profit'] / $row['cost'] * 100;
			?>
			<tr<?php if($i++ % 2 == 1) { echo ' class="AltRow"'; } ?>>
				<td class="border">&nbsp;</td>
				<td><?php echo date('M j, Y', strtotime($row['date'])); ?> <!-- <?php echo $row['date']?> --></td>
				<td class="number">$<?php echo number_format($row['revenue'], 2); ?></td>
				<td class="number">$<?php echo number_format($row['cost'], 2); ?></td>
				<td class="number">$<?php echo number_format($row['profit'], 2); ?></td>
				<td class="number"><?php echo number_format($roi, 2).'%'; ?></td>
				<td class="tail">&nbsp;</td>
			</tr>
			<?php
			$total_revenue += $row['revenue'];
			$total_cost += $row['cost'];
			$total_profit += $row['profit'];
		}
		
		$total_roi = ($total_cost == 0) ? 0 : $total_profit / $total_cost * 100;
		?>
		<tr class="total">
			<td class="border">&nbsp;</td>
			<td>Total</td>
			<td class="number">$<?php echo number_format($total_revenue, 2); ?></td>
			<td class="number">$<?php echo number_format($total_cost, 2); ?></td>
			<td class="number">$<?php echo number_format($total_profit, 2); ?></td>
			<td class="number"><?php echo number_format($total_roi, 2).'%'; ?></td>
			<td class="tail">&nbsp;</td>
		</tr>
	</tbody>
	<tfoot>
		<tr class="table_footer">
			<td class="hhl">&nbsp;</td>
			<td colspan="5">&nbsp;</td>
			<td class="hhr">&nbsp;</td>
		</tr>
	</tfoot>
</table>

<a class="tbtn floatright" href='?<?php echo $_SERVER['QUERY_STRING']?>&ExportCSV=FILE' >Export to CSV</a>

<br/><br/>
