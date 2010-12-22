<?php
require_once(PATH . "Legacy.Abstraction.class.php");

global $userId;
$userId = $this->User->ID;

// Get querystring variables
if(!is_numeric($_GET['network']) || empty($_GET['network']))
{
	die('No network selected.');
}
else
	$network_id = (int)$_GET['network'];

require_once(PATH.'inc_daterange.php');
$startdate = LegacyAbstraction::$strStartDateVal;
$enddate = LegacyAbstraction::$strEndDateVal;

// Get network name and model
$res = LegacyAbstraction::executeQuery('SELECT TITLE, MODEL FROM '.PREFIX.'aff_network WHERE ID = '.$network_id);
$row = LegacyAbstraction::getRow($res) ;
$networkTitle = $row['TITLE'];
$networkmodel = $row['MODEL'];
$network = $_GET['network'];

/*
$cmd = new SqlCommand("
	SELECT
		category.ID as category_id,
		category.TITLE as category_name,
		SUM(subids.CLICKS) as clicks,
		SUM(subids.CONVERSIONS) AS conversions,
		SUM(subids.REVENUE) as revenue
	FROM
		adpalace_user_aff_network_subid AS subids
		JOIN adpalace_offers AS offers ON
			subids.OFFER_ID = offers.OFFER_ID
			AND subids.NETWORK_ID = offers.NETWORK_ID
		JOIN adpalace_category AS category ON
			offers.CATEGORY_ID = category.ID
	WHERE
		subids.USERID = @user_id
		AND subids.NETWORK_ID = @network_id
		AND subids.STAT_DATE BETWEEN @startdate AND @enddate
	GROUP BY
		category.ID,
		category.TITLE
	ORDER BY
		category.TITLE
	");
$cmd->parameters['user_id'] = $userId;
$cmd->parameters['network_id'] = $network_id;
$cmd->parameters['startdate'] = date('Y-m-d', strtotime($startdate));
$cmd->parameters['enddate'] = date('Y-m-d', strtotime($enddate));
$cmd->execute();
$data = $cmd->getAllRows();
*/
	$stDate = date('Y-m-d', strtotime($startdate));
	$enDate = date('Y-m-d', strtotime($enddate));
	$sql = "
	SELECT
		category.ID as category_id,
		category.TITLE as category_name,
		SUM(subids.CLICKS) as clicks,
		SUM(subids.CONVERSIONS) AS conversions,
		SUM(subids.REVENUE) as revenue
	FROM
		adpalace_user_aff_network_subid AS subids
		JOIN adpalace_offers AS offers ON
			subids.OFFER_ID = offers.OFFER_ID
			AND subids.NETWORK_ID = offers.NETWORK_ID
		JOIN ADPALACE_CATEGORY AS category ON
			offers.CATEGORY_ID = category.ID
	WHERE
		subids.USERID = $userId
		AND subids.NETWORK_ID = $network_id
		AND subids.STAT_DATE BETWEEN '$stDate' AND '$enDate'
	GROUP BY
		category.ID,
		category.TITLE
	ORDER BY
		category.TITLE
	";
		
	$query = mysql_query($sql);
	$rows = array();
	while($row = mysql_fetch_assoc($query))
		$rows[] = $row;
	$data = $rows;
// Create the chart
$chartXML = "<chart showBorder='0' bgAlpha='0,0' caption='Industry' numberPrefix='$' formatNumberScale='0'>";
foreach ($data as $row)
	$chartXML .= "<set label='" . $row['category_name'] . "' value='" . $row['revenue'] . "' />";
$chartXML .= "</chart>";

?>

<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-offer-detail.js.php?sriptRoot=<?=SCRIPT_ROOT?>&langFolder=<?=$langFolder?>"></script>
	
	<?= @$info ?>
	
	<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu"></div>
	<div id="pagesubmenu">
		<ul>
			<li><a href="Stats.html?network=<?php echo $network_id?>">Main<span></span></a></li>
			<li><a href="StatsIndustry.html?network=<?php echo $network_id; ?>">Stats Industry<span></span></a></li>
			<li><a class="active" href="OfferAnalysis.html?network=<?php echo $network_id; ?>">Offer Analysis<span></span></a></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false,false,'/networkoffers/'.$network_id.'.png'); ?>
	
	<center>

<?php //echo renderChart("/assets/charts/Pie2D.swf", "", $chartXML, "chartIndustry", 600, 300, false, false); ?>

	<!-- START Script Block for Chart chartOverview -->
	<div id="chartOverviewDiv" align="center">
		Chart.
	</div>
	<script type="text/javascript">	
		//Instantiate the Chart	
		var chart_chartOverview = new FusionCharts("/assets/charts/Pie2D.swf", "chartOverview", "600", "380", "0", "0");
		//Provide entire XML data using dataXML method
		chart_chartOverview.setDataXML("<?php echo $chartXML?>");
		//Finally, render the chart.
		chart_chartOverview.render("chartOverviewDiv");
	</script>	
	<!-- END Script Block for Chart chartOverview -->
	
<form method="get" action="" name="frmRange">
<input type="hidden" name="network" value="<?php echo $network_id; ?>" />
<table align="right" cellspacing="0" cellpadding="0" style="margin-bottom: 5px">
  <tr>
    <td><input type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>" style="width: 135px;"/></td>
	<td><input type="image" src="/Themes/BevoMedia/img/gobutton.gif" width="30" height="25" />
  </tr>
</table>
</form>

<!-- start table !-->
<table cellspacing="0" cellpadding="3" border="0" class="btable">
   <tr class="table_header">
        <td class="hhl">&nbsp;</td>
        <td width="30%" style="text-align: center;">Industry</td>
        <td style="text-align: center;">Clicks</td>
        <td style="text-align: center;">Conversions</td>
        <td style="text-align: center;">Conv. Rate</td>
        <td style="text-align: center;">Earnings</td>
        <td style="text-align: center;">EPC</td>
        <td class="hhr">&nbsp;</td>
    </tr>
  	<?php
	$clicks = 0; $conversions = 0;
	foreach($data as $row)
	{
		$clicks += $row['clicks'];
		$conversions += $row['conversions'];
		@$revenue += $row['revenue'];
		?>
		<tr>
			<td class="border">&nbsp;</td>
			<td><?php echo htmlentities($row['category_name']); ?></td>
			<td class="number"><?php echo number_format($row['clicks'], 0); ?></td>
			<td class="number"><?php echo number_format($row['conversions'], 0); ?></td>
			<td class="number"><?php echo number_format(($row['clicks'] != 0 ? $row['conversions'] / $row['clicks'] : 0) * 100, 2).'%'; ?></td>
			<td class="number"><?php echo '$'.number_format($row['revenue'], 2); ?></td>
			<td class="number"><?php echo '$'.number_format(($row['clicks'] != 0 ? $row['revenue'] / $row['clicks'] : 0), 2); ?></td>
			<td class="tail">&nbsp;</td>
		</tr>
		<?php
	}
	?>
	<tr class="total">
		<td class="border">&nbsp;</td>
		<td>Total</td>
		<td class="number"><?php echo number_format($clicks, 0); ?></td>
		<td class="number"><?php echo number_format($conversions, 0); ?></td>
		<td class="number"><?php echo number_format(($clicks != 0 ? $conversions / $clicks : 0) * 100, 2).'%'; ?></td>
		<td class="number"><?php echo '$'.@number_format($revenue, 2); ?></td>
		<td class="number"><?php echo '$'.number_format(($clicks != 0 ? $revenue / $clicks : 0), 2); ?></td>
		<td class="tail">&nbsp;</td>
	</tr>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="6">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>
<!-- end table !-->

