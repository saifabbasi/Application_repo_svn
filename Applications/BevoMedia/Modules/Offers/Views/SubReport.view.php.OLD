<?php

require_once(PATH . "Legacy.Abstraction.class.php");

global $userId, $isSelfHosted;
$userId = $this->User->id;
$isSelfHosted = $this->User->IsSelfHosted();


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
$res = LegacyAbstraction::executeQuery('SELECT title, model FROM bevomedia_aff_network WHERE ID = '.$network_id);
$row = LegacyAbstraction::getRow($res) ;
$networkTitle = $row['title'];
$networkmodel = $row['model'];
$network = $_GET['network'];

$isOffersPage = true;
/*
$cmd = new SqlCommand("
	SELECT
		subids.SUB_ID AS sub_id,
		offers.OFFER_ID AS offer_id,
		offers.TITLE AS offer_name,
		SUM(subids.CLICKS) as clicks,
		SUM(subids.CONVERSIONS) AS conversions,
		SUM(subids.REVENUE) as revenue
	FROM
		adpalace_user_aff_network_subid AS subids
		LEFT JOIN adpalace_offers AS offers ON
			subids.OFFER_ID = offers.OFFER_ID
			AND subids.NETWORK_ID = offers.NETWORK_ID
	WHERE
		subids.USERID = @user_id
		AND subids.NETWORK_ID = @network_id
		AND subids.STAT_DATE BETWEEN @startdate AND @enddate
	GROUP BY
		subids.SUB_ID,
		offers.OFFER_ID,
		offers.TITLE
	ORDER BY
		offers.TITLE
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
		subids.subId AS sub_id,
		offers.offer__id AS offer_id,
		offers.title AS offer_name,
		SUM(subids.clicks) as clicks,
		SUM(subids.conversions) AS conversions,
		SUM(subids.revenue) as revenue
	FROM
		bevomedia_user_aff_network_subid AS subids
		LEFT JOIN bevomedia_offers AS offers ON
			subids.offer__id = offers.offer__id
			AND subids.network__id = offers.network__id
	WHERE
		subids.user__id = $userId
		AND subids.network__id = $network_id
		AND subids.statDate BETWEEN '$stDate' AND '$enDate'
	GROUP BY
		subids.subId,
		offers.offer__id,
		offers.title
	ORDER BY
		offers.title";
		
	
	
	$query = mysql_query($sql);
	$rows = array();
	while($row = mysql_fetch_assoc($query))
		$rows[] = $row;
	$data = $rows;




if(isset($_GET['ExportCSV']) && $_GET['ExportCSV'] == 'FILE')
{
	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=SubReport.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	print '"Sub ID","Offer","Clicks","Conversions","Conv. Rate","Earnings","EPC"' . "\r\n";
	foreach($data as $row)
	{
		$temp = array();
		$temp['sub_id'] = $row['sub_id'];
		$temp['offer_name'] = ($row['offer_name'] == '')?'Unknown ('.($row['offer_id']=='')?'[Unknown]':$row['offer_id'].')':$row['offer_name'];
		$temp['clicks'] = (isset($row['clicks'])?($row['clicks']):'0'); $row['clicks'] = $temp['clicks'];
		$temp['conversions'] = (isset($row['conversions'])?($row['conversions']):'0');
		$temp['convrate'] = ($row['clicks'] != 0 ? $row['conversions'] / $row['clicks'] : 0) * 100;
		$temp['earnings'] = (isset($row['revenue'])?($row['revenue']):'0');
		$temp['epc'] = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];

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

<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-offer-detail.js.php?sriptRoot=<?=SCRIPT_ROOT?>&langFolder=<?=$langFolder?>"></script>
<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-subreport.js.php?r=<?=$r?>"></script>

	<?= @$info ?>
	
	<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu"></div>
	<div id="pagesubmenu">
		<ul>
			<li><a href="Stats.html?network=<?php echo $network_id?>">Main<span></span></a></li>
			<li><a class="active" href="SubReport.html?network='.$network_id.'">Sub Report<span></span></a></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false,false,'/networkoffers/'.$network_id.'.png'); ?>
	
	<center>

<form method="get" action="" name="frmRange">
<input type="hidden" name="network" value="<?php echo $network_id; ?>" />
<table align="right" cellspacing="0" cellpadding="0" class="datetable">
  <tr>
    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>" /></td>
	<td><input class="formsubmit" type="submit" /></td>
  </tr>
</table>
</form>
<br />
<!-- start table !-->
<table cellspacing="0" cellpadding="3" border="0" class="btable">
   <tr class="table_header">
        <td class="hhl">&nbsp;</td>
        <td width="30%" style="text-align: center;">Sub ID</td>
        <td style="text-align: center;">Clicks</td>
        <td style="text-align: center;">Conversions</td>
        <td style="text-align: center;">Conv. Rate</td>
        <td style="text-align: center;">Earnings</td>
        <td style="text-align: center;">EPC</td>
        <td class="hhr">&nbsp;</td>
    </tr>
  	<?php
	$clicks = 0; $conversions = 0; $previousOffer = null;
	foreach($data as $row)
	{
		if($previousOffer != $row['offer_id'])
		{
			echo '
				<tr>
					<td class="border">&nbsp;</td>
					<td colspan="6" class="STYLE4" style="border-left: none;">'.htmlentities($row['offer_name']).'</td>
					<td class="tail">&nbsp;</td>
				</tr>
			';
			$previousOffer = $row['offer_id'];
		}
		
		$clicks += $row['clicks'];
		$conversions += $row['conversions'];
		@$revenue += $row['revenue'];
		?>
		<tr>
			<td class="border">&nbsp;</td>
			<td><?php echo htmlentities($row['sub_id']); ?></td>
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


<a class="tbtn floatright" href='?<?php echo $_SERVER['QUERY_STRING']?>&ExportCSV=FILE' >Export to CSV</a>
<div class="clear"></div>

<br/><br/>


