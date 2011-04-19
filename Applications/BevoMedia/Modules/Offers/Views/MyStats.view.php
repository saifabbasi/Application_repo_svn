<?php
require_once(PATH . "Legacy.Abstraction.class.php");

global $userId;
$userId = $this->User->id;

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
$res = LegacyAbstraction::executeQuery('SELECT TITLE, MODEL FROM bevomedia_aff_network WHERE ID = '.$network_id);
$row = LegacyAbstraction::getRow($res) ;
$networkTitle = $row['TITLE'];
$networkmodel = $row['MODEL'];

$isOffersPage = true;

$network=$_GET['network'];
$offers = isset($_POST['offers'])?$_POST['offers']:'Rev';
$rA      = isset($_POST['offers'])?$_POST['offers']:'Rev';
//$regionB = isset($_POST['regionB'])?$_POST['regionB']:'Earnings';
//$rB      = isset($_POST['regionB'])?$_POST['regionB']:'Earnings';
$timefram = isset($_POST['timefram'])?$_POST['timefram']:'Per Day';
$rC      = isset($_POST['timefram'])?$_POST['timefram']:'Per Day';

$data = array();
if($networkmodel == 'CPA')
{
	
	$stDate = date('Y-m-d', strtotime($startdate));
	$enDate = date('Y-m-d', strtotime($enddate));
	$sql = "
		SELECT
			subids.offer__id as offer_id,
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
			subids.offer__id,
			offers.title
		";
	$query = mysql_query($sql);
	$rows = array();
	while($row = mysql_fetch_assoc($query))
		$rows[] = $row;
	$data = $rows;
	//$data = $cmd->getAllRows();
	
	// Create the chart
	$chartXML = "<chart showBorder='0' bgAlpha='0,0' caption='Offers Overview' numberPrefix='$' formatNumberScale='0'>";
	foreach ($data as $row)
	{
		$offer_name = htmlspecialchars_decode(empty($row['offer_name']) ? 'Unknown' : $row['offer_name']);
		$offer_name = preg_replace('/[^a-z0-9\s]/i', '', $offer_name);
		$chartXML .= "<set label='" . htmlentities($offer_name) . "' value='" . $row['revenue'] . "' />";
	}
	if(!$data)
		@$chartXML .= "<set label='".htmlentities(str_replace("'","",$stDate))."' value='".number_format(0, 2, '.', '')."' />";
	
	$chartXML .= "</chart>";
}
elseif($networkmodel == 'CPM')
{
	// Something else?
}



if(isset($_GET['ExportCSV']) && $_GET['ExportCSV'] == 'FILE')
{
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=Stats.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	print '"Offer","Clicks","Conversions","Conv. Rate","Earnings","EPC"' . "\r\n";
	foreach($data as $row)
	{
		$temp = array();
		$temp['offer_name'] = ($row['offer_name'] == '')?'Unknown ('.$row['offer_id'].')':$row['offer_name'];
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
	<?= @$info ?>
	
	<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu"></div>
	<div id="pagesubmenu">
		<ul>
			<li><a class="active" href="Stats.html?network=<?php echo $network_id?>">Main<span></span></a></li>
			
			<?php if($networkmodel == 'CPA')
				echo '<li><a href="SubReport.html?network='.$network_id.'">Sub Report<span></span></a></li>';
			else {
				echo '<li><a href="StatsIndustry.html?network='.$network_id.'">Stats Industry<span></span></a></li>';
				echo '<li><a href="OfferAnalysis.html?network='.$network_id.'">Offer Analysis<span></span></a></li>';
			} ?>			
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false,false,'/networkoffers/'.$network_id.'.png'); ?>
	
	<center>
	
<?php
	//echo(Stats_menu2($network));
?>

<?
	if ($networkTitle=='ClickBank')
	{
?>
	<div align='center'>
		Clickbank stats update once a day at 3:30am
	</div>
<?
	}
?>

<?

// Graphs and data tables
if($networkmodel=='CPA')
{
	?>

	<!-- START Script Block for Chart chartOverview -->
	<div id="chartOverviewDiv" align="center">
		Chart.
	</div>
	<script type="text/javascript">
		//Instantiate the Chart
		var chart_chartOverview = new FusionCharts("/Themes/BevoMedia/chart_swf/Column2D.swf", "chartOverview", "600", "380", "0", "0");
		//Provide entire XML data using dataXML method
		chart_chartOverview.setDataXML("<?php echo $chartXML?>");
		//Finally, render the chart.
		chart_chartOverview.render("chartOverviewDiv");
	</script>
	<!-- END Script Block for Chart chartOverview -->

	<form method="get" action="" name="frmRange">
	<input type="hidden" name="network" value="<?php echo $network_id; ?>" />
	<table align="right" cellspacing="0" cellpadding="0" class="datetable">
	  <tr>
	    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>" /></td>
		<td><input class="formsubmit" type="submit" /></td>
	  </tr>
	</table>
	</form>
	
	<!-- start table !-->
	<table cellspacing="0" cellpadding="3" border="0" class="btable">
	   <tr class="table_header">
	        <td class="hhl">&nbsp;</td>
	        <td width="30%">Offer</td>
	        <td style="text-align: center;">Clicks</td>
	        <td style="text-align: center;">Conversions</td>
	        <td style="text-align: center;">Conv. Rate</td>
	        <td style="text-align: center;">Earnings</td>
	        <td style="text-align: center;">EPC</td>
	        <td class="hhr">&nbsp;</td>
	    </tr>
	  	<?php
		/*
		echo('<tr>
			<td class="border">&nbsp;</td>
			<td colspan="8" class="STYLE4" style="border-left: none;">Offers </td>
			<td class="tail">&nbsp;</td>
			</tr>');
		*/
		$clicks = 0; $conversions = 0;
		foreach($data as $row)
		{
			$clicks += $row['clicks'];
			$conversions += $row['conversions'];
			@$revenue += $row['revenue'];
			
			$Temp = htmlentities($row['offer_name']);
			
			if ( strstr($Temp, '&acirc;')  &&  (strpos($Temp, '&acirc;')==0) )
			{
				$row['offer_name'] = substr($row['offer_name'], strpos($row['offer_name'], ' ')+1);
			}
			
			?>
			<tr>
				<td class="border">&nbsp;</td>
				<td><?php echo htmlentities(!empty($row['offer_name']) ? $row['offer_name'] : 'Unknown ('.(@$row['offer_id'] ? $row['offer_id'] : 'No ID #').')'); ?></td>
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
			<td class="number"><?php echo @number_format($clicks, 0); ?></td>
			<td class="number"><?php echo @number_format($conversions, 0); ?></td>
			<td class="number"><?php echo @number_format(($clicks != 0 ? $conversions / $clicks : 0) * 100, 2).'%'; ?></td>
			<td class="number"><?php echo '$'.@number_format($revenue, 2); ?></td>
			<td class="number"><?php echo '$'.@number_format(($clicks != 0 ? $revenue / $clicks : 0), 2); ?></td>
			<td class="tail">&nbsp;</td>
		</tr>
		<tr class="table_footer">
			<td class="hhl">&nbsp;</td>
			<td colspan="6">&nbsp;</td>
			<td class="hhr">&nbsp;</td>
		</tr>
	</table>
	<!-- end table !-->
	<?php
}
else
{
	
//*************************************************************************************************

		$arrModels		= array('CPA', 'CPC', 'CPM');

		$crNetworks		= is_array(@$_GET['crNetworks']) ? @$_GET['crNetworks'] : $arrModels;
		$crRange		= @$_GET['crRange'] == '' ? 'today' : @$_GET['crRange'];
		
		$crStartDate	= @$_GET['crStartDate'];
		$crEndDate		= @$_GET['crEndDate'];

		$crStartDate2	= @$_GET['crStartDate'];
		$crEndDate2		= @$_GET['crEndDate'];
		
		$custom_time=mktime (0,0,0,date('m'),date('d')-1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');

		$crTitle		= '';

//*************************************************************************************************

		$isOverviewPage = true;
		//$showRightPanes = true;

//*************************************************************************************************

		$sub	= "";
		$subAN	= "";
		$save_time=$crRange;

		if ( $crRange == 'yesterday' )
		{
			$crStartDate	= LegacyAbstraction::addDays($today, -1);
			$crEndDate		= LegacyAbstraction::addDays($today, -1);
			$crStartDate2	= LegacyAbstraction::addDays($today, -1);
			$crEndDate2		= LegacyAbstraction::addDays($today, -1);
			$sub			= "AND S.STAT_DATE = '".$crStartDate."' ";
			$subAN			= "AND I.EVENT_DATETIME = '".$crStartDate."' ";
			$crTitle		= "Yesterday's";
			$anlytic_title	="Yesterday's";
			
			
		$custom_time=mktime (0,0,0,date('m'),date('d')-2,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$custom_time=mktime (0,0,0,date('m'),date('d')-1,date('Y'));
		$analytic_to=date('Y-m-d',$custom_time);
		}
		
		elseif ( $crRange == 'thisweek' )
		{
			$crStartDate	= LegacyAbstraction::addDays($today, -date('w', $tsToday));
			$crEndDate		= LegacyAbstraction::addDays($crStartDate, 6);
			$crStartDate2	= LegacyAbstraction::addDays($today, -date('w', $tsToday));
			$crEndDate2		= LegacyAbstraction::addDays($crStartDate, 6);
			$sub			= "AND S.STAT_DATE >= '".$crStartDate."' AND S.STAT_DATE <= '".$crEndDate."' ";
			$subAN			= "AND I.EVENT_DATETIME >= '".$crStartDate."' AND I.EVENT_DATETIME <= '".$crEndDate."' ";
			$crTitle		= "This week's";
			$anlytic_title	="This week's";
		$custom_time=mktime (0,0,0,date('m'),-date('w'),date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
		}
		
		elseif ( $crRange == 'thismonth' )
		{
			$crStartDate	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
			$crEndDate		= LegacyAbstraction::addDays($crStartDate, date('t', $tsToday)-1);
			$crStartDate2	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
			$crEndDate2		= LegacyAbstraction::addDays($crStartDate, date('t', $tsToday)-1);
			$sub			= "AND S.STAT_DATE >= '".$crStartDate."' AND S.STAT_DATE <= '".$crEndDate."' ";
			$subAN			= "AND I.EVENT_DATETIME >= '".$crStartDate."' AND I.EVENT_DATETIME <= '".$crEndDate."' ";
			$crTitle		= "This months's";
			$anlytic_title	="This months's";
		$custom_time=mktime (0,0,0,date('m'),1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
		}
		
		elseif ( $crRange == 'thisyear' )
		{
			$crStartDate	= date('Y-01-01', $tsToday);
			$crEndDate		= date('Y-12-31', $tsToday);
			$crStartDate2	= date('Y-01-01', $tsToday);
			$crEndDate2		= date('Y-12-31', $tsToday);
			$sub			= "AND S.STAT_DATE >= '".$crStartDate."' AND S.STAT_DATE <= '".$crEndDate."' ";
			$subAN			= "AND I.EVENT_DATETIME >= '".$crStartDate."' AND I.EVENT_DATETIME <= '".$crEndDate."' ";
			$crTitle		= "This year's";
			$anlytic_title	="year's";
		$custom_time=mktime (0,0,0,1,1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
		}
		
		elseif ( $crRange == 'custom' )
		{
			$crStartDate	= LegacyAbstraction::handleSingleQuote($crStartDate);
			$crEndDate		= LegacyAbstraction::handleSingleQuote($crEndDate);
			$crStartDate2	= LegacyAbstraction::handleSingleQuote($crStartDate);
			$crEndDate2		= LegacyAbstraction::handleSingleQuote($crEndDate);
			$sub			= "AND S.STAT_DATE >= '".$crStartDate."' AND S.STAT_DATE <= '".$crEndDate."' ";
			$subAN			= "AND I.EVENT_DATETIME >= '".$crStartDate."' AND I.EVENT_DATETIME <= '".$crEndDate."' ";
			$crTitle		= formatDate($crStartDate)." - ".formatDate($crEndDate);
			
		$analytic_from=$crStartDate;
		$analytic_to=$crEndDate;
			
			
		}
		
		else
		{
			$today = date('Y-m-d');
			$crStartDate	= $today;
			$crEndDate		= $today;
			$crStartDate2	= LegacyAbstraction::addDays($today, -7);
			$crEndDate2		= $today;
//			$crStartDate2	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
//			$crEndDate2		= LegacyAbstraction::addDays($crStartDate, date('t', $tsToday)-1);
			$sub			= "AND S.STAT_DATE = '".$crStartDate."' ";
			$subAN			= "AND I.EVENT_DATETIME = '".$crStartDate."' ";
			$crTitle		= "Today's";
			$anlytic_title	="Today's";
			
		$custom_time=mktime (0,0,0,date('m'),date('d')-1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
			
			
		}

//*************************************************************************************************

		$arrNetworks	= array();
		$arrNewNetworks = array();
		foreach ( $arrModels as $model )
		{
			$arrNetworks[$model]	= array();
			$arrNewNetworks[$model]	= array();
		}

//*************************************************************************************************

		$tsToday = time();
		$nnStartDate	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
		$nnEndDate		= LegacyAbstraction::addDays($nnStartDate, date('t', $tsToday)-1);

		$sql = "SELECT N.MODEL, N.ID, N.TITLE, N.ADMIN_COMMISSION, IF(N.MODEL='CPA', 'n/a', SUM(S.IMPRESSIONS)) AS IMPRESSIONS, SUM(S.CLICKS) AS CLICKS, SUM(S.CONVERSIONS) AS CONVERSIONS, SUM(S.REVENUE)*(100-N.ADMIN_COMMISSION)/100 AS REVENUE, ((SUM(S.REVENUE)*(100-N.ADMIN_COMMISSION)/100)*1000)/IF(SUM(S.IMPRESSIONS)>0, SUM(S.IMPRESSIONS), 1) AS ECPM, UAN.STATUS FROM ".PREFIX."user_aff_network UAN, ".PREFIX."aff_network N LEFT OUTER JOIN ".PREFIX."user_aff_network_stats S ON S.USERID = '".$userId."' AND S.NETWORK_ID = N.ID ".$sub." WHERE N.ID = UAN.NETWORK_ID AND UAN.USERID = '".$userId."' AND UAN.STATUS = '".APP_STATUS_ACCEPTED."' AND N.ISVALID = 'Y' GROUP BY N.ID ORDER BY N.MODEL, N.TITLE";
		$res = LegacyAbstraction::executeQuery($sql);
		while ( $row = LegacyAbstraction::getRow($res) )
		{
			$res2 = LegacyAbstraction::executeQuery("SELECT SUM(REVENUE) AS REVENUE FROM ".PREFIX."user_aff_network_stats WHERE USERID = '".$userId."' AND NETWORK_ID = ".$row['ID']." AND STAT_DATE >= '".$nnStartDate."' AND STAT_DATE <= '".$nnEndDate."'");
			$row2 = LegacyAbstraction::getRow($res2);
			
			$row['MTD']		= (float)($row2['REVENUE']*((100-$row['ADMIN_COMMISSION'])/100));
			
			$row['ISUSER']	= false;
			
			$arrNetworks[$row['MODEL']][] = $row;
		}
		
		LegacyAbstraction::free($res);
		
//*************************************************************************************************

		asort($arrNetworks);

//*************************************************************************************************

		$arrNetsJoined = array();
		$res = LegacyAbstraction::executeQuery("SELECT N.MODEL, N.ID, N.TITLE, UAN.STATUS FROM ".PREFIX."aff_network N, ".PREFIX."user_aff_network UAN WHERE UAN.USERID = '".$userId."' AND UAN.STATUS = '".APP_STATUS_ACCEPTED."' AND UAN.NETWORK_ID = N.ID AND N.ISVALID = 'Y' ORDER BY N.MODEL, N.TITLE");
		while ( $row = LegacyAbstraction::getRow($res) )
		{
			$row['ISUSER']	= false;

			$arrNetsJoined[] = $row;
		}
		LegacyAbstraction::free($res);

//*************************************************************************************************

		$showSearchOffers	= true;

//*************************************************************************************************



//*************************************************************************************************

require_once(PATH."include/analytic_fetch_data.php");

$obj=new CURL('test');
		//$anlytic_title	="Today's";
		/*$custom_time=mktime (0,0,0,date('m'),date('d')-1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
*/
$analytic_account = array();
												//GETTING ANALYTIC ACCOUNT DETAIL OF THE USER
$res = LegacyAbstraction::executeQuery("SELECT UAN.LOGIN_ID,UAN.PASSWORD,N.* from ".PREFIX."user_aff_network UAN, ".PREFIX."analytic_user_profile N WHERE N.USERID = UAN.USERID AND UAN.STATUS = '".APP_STATUS_ACCEPTED."' AND UAN.NETWORK_ID =".NETWORK_ANALYTICS_ID." AND UAN.USERID = '".$userId."' ");
		while ( $row = LegacyAbstraction::getRow($res) )
		{
			//$row['ISUSER']	= false;

			$analytic_account[] = $row;
			
			
			
			
		}
		LegacyAbstraction::free($res);
		$custom_time=mktime (0,0,0,date('m'),date('d')-1,date('Y'));
		$from_date=date('Y-m-d',$custom_time);
		$to_date=date('Y-m-d');
		
		if(count($analytic_account)>0)
		{
		
		$analytic_save_time="";
		$analytic_last_update="";
		$continue=true;
		$custom_time_check=mktime (date('H')-4,date('i'),date('s'),date('m'),date('d'),date('Y'));
		$expiry_time=date('Y-m-d H:i:s',$custom_time_check);
		$res = LegacyAbstraction::executeQuery("SELECT SAVE_TIME,LAST_UPDATE from ".PREFIX."ANALYTIC_PROFILE_DETAIL WHERE USERID = '".$userId."' AND SAVE_TIME='".$save_time."' and LAST_UPDATE>='".$expiry_time."' and REPORT_TYPE='Dashboard'");
		while ( $row = LegacyAbstraction::getRow($res) )
		{
		
		$continue=false;
				
		}
		LegacyAbstraction::free($res);
		
		//UPDATAING ANALYTIC DATA BY CALLING PHPCACKE FUNCTION
									
		if($continue or $save_time=='custom')
		{
		foreach ( $analytic_account as $analytic_detail )
		{
		$url="https://www.bevomedia.com/Analytic_phpcake/?url=analytics/save_report_data/".$analytic_detail['LOGIN_ID']."/".$analytic_detail['PASSWORD']."/".	$analytic_detail['PROFILE_ID']."/".$analytic_from."/".$analytic_to."/".$userId."/".$save_time."/Dashboard/";
		
		
		
		$rest=$obj->get($url);
		
		}
		}
		
	
	}
?>
<!-- end dumb header!-->
<!--alternative code -->
<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<tr>
				<td style="text-align: center;"><form  action="publisher-offers-stats.php?network=<?=$network?>" method="post" id="fTs" name="fTs">
                <strong></strong><select name="offers" onchange="submitf()">
                <option value='Rev' <? if($rA=='Rev')echo 'selected';?>>Rev</option>
                <option value='Impressions' <? if($rA=='Impressions')echo 'selected';?>>Impressions</option>
                <option value="Clicks" <? if($rA=='Clicks')echo 'selected';?>>Clicks</option>
                <option value='Conversions' <? if($rA=='Conversions')echo 'selected';?>>Conversions</option>
                <option value='CTR' <? if($rA=='CTR')echo 'selected';?>>CTR</option>
                <option value='eCPM' <? if($rA=='eCPM')echo 'selected';?>>eCPM</option>
                </select>
              
                <strong></strong><select name="timefram" onchange="submitf()">
                <option value='Per Day' <? if($rC=='Per Day')echo 'selected';?>>Per Day</option>
                <option value='Per Week' <? if($rC=='Per Week')echo 'selected';?>>Per Week</option>
                <option value="Per Month" <? if($rC=='Per Month')echo 'selected';?>>Per Month</option>
                <option value='Per Year' <? if($rC=='Per Year')echo 'selected';?>>Per Year</option>
                </select>
                
                
              
</form>
 <?=InsertChart ( '/assets/graphs/stats-industry.php?userId='.$userId.'&timefram='.$timefram.'&rB='.@$regionB.'&offers='.$offers, 600, 380, 'ffffff' );?>   </td>
			</tr>
            
		</table>
<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable">
						<tr class="table_header">
							<td class="hhl">&nbsp;</td>
							
							<td  style="text-align: left;">Week Rev</td>
							<td  style="text-align: center;">MTD Rev</td>
							<td  style="text-align: center;">Impressions</td>
							<td   style="text-align: center;">Clicks</td>
							<td   style="text-align: center;">Conversions</td>
							<td  style="text-align: center;">CTR</td>
							<td  style="text-align: center;">eCPM</td>
							<td class="hhr">&nbsp;</td>
						</tr>
                        
                        <tr>
							<td class="border">&nbsp;</td>
							<td class="GridHead" colspan="7" style="border-left: none;"><?=$networkTitle?></td>
							<td class="tail">&nbsp;</td>
						</tr>
                        
                        <tr>
							<td class="border">&nbsp;</td>
							
							<td  style="text-align: center;"><span>03-03-09</span></td>
							<td  style="text-align: center;"><span>$3.06</span></td>
							<td  style="text-align: center;"><span>20</span></td>
							<td  style="text-align: center;"><span>2</span></td>
							<td  style="text-align: center;"><span>0.5</span></td>
							<td  style="text-align: center;" ><span>0</span></td>
							<td  style="text-align: center;" ><span>$0.00</span></td>
							<td class="tail">&nbsp;</td>
						</tr>
                        
                         <tr>
							<td class="border">&nbsp;</td>
							<td  style="text-align: center;"><span>07-03-09</span></td>
							<td  style="text-align: center;"><span>$4.00</span></td>
							<td  style="text-align: center;"><span>50</span></td>
							<td  style="text-align: center;"><span>3</span></td>
							<td  style="text-align: center;"><span>0.75</span></td>
							<td  style="text-align: center;" ><span>0</span></td>
							<td  style="text-align: center;" ><span>$0.00</span></td>
							<td class="tail">&nbsp;</td>
						</tr>
                        
                         <tr>
							<td class="border">&nbsp;</td>
							<td  style="text-align: center;"><span>05-03-09</span></td>
							<td  style="text-align: center;"><span>$0.00</span></td>
							<td  style="text-align: center;"><span>20</span></td>
							<td  style="text-align: center;"><span>1</span></td>
							<td  style="text-align: center;"><span>0.25</span></td>
							<td  style="text-align: center;" ><span>0</span></td>
							<td  style="text-align: center;" ><span>$0.00</span></td>
							<td class="tail">&nbsp;</td>
						</tr>
                        
                         <tr>
							<td class="border">&nbsp;</td>
							<td  style="text-align: center;"><span>06-03-09</span></td>
							<td  style="text-align: center;"><span>$0.00</span></td>
							<td  style="text-align: center;"><span>5</span></td>
							<td  style="text-align: center;"><span>0</span></td>
							<td  style="text-align: center;"><span>0</span></td>
							<td  style="text-align: center;" ><span>0</span></td>
							<td  style="text-align: center;" ><span>$0.00</span></td>
							<td class="tail">&nbsp;</td>
						</tr>
                        
                         <tr>
							<td class="border">&nbsp;</td>
							<td  style="text-align: center;"><span>07-03-09</span></td>
							<td  style="text-align: center;"><span>$1.00</span></td>
							<td  style="text-align: center;"><span>20</span></td>
							<td  style="text-align: center;"><span>0</span></td>
							<td  style="text-align: center;"><span>0</span></td>
							<td  style="text-align: center;" ><span>0</span></td>
							<td  style="text-align: center;" ><span>$0.00</span></td>
							<td class="tail">&nbsp;</td>
						</tr>
                        
                         <tr>
							<td class="border">&nbsp;</td>
							<td  style="text-align: center;"><span>08-03-09</span></td>
							<td  style="text-align: center;"><span>$0.00</span></td>
							<td  style="text-align: center;"><span>18</span></td>
							<td  style="text-align: center;"><span>2</span></td>
							<td  style="text-align: center;"><span>0</span></td>
							<td  style="text-align: center;" ><span>0</span></td>
							<td  style="text-align: center;" ><span>$0.00</span></td>
							<td class="tail">&nbsp;</td>
						</tr>
                        
                         <tr>
							<td class="border">&nbsp;</td>
							<td  style="text-align: center;"><span>09-03-09</span></td>
							<td  style="text-align: center;"><span>$0.00</span></td>
							<td  style="text-align: center;"><span>7</span></td>
							<td  style="text-align: center;"><span>0</span></td>
							<td  style="text-align: center;"><span>0</span></td>
							<td  style="text-align: center;" ><span>0</span></td>
							<td  style="text-align: center;" ><span>$0.00</span></td>
							<td class="tail">&nbsp;</td>
						</tr>
                        
                        	<tr class="table_footer">
							<td class="hhl"></td>
							<td style="border-left: none;" colspan="7"></td>
							<td class="hhr"></td>
						</tr>
					</table>
                        
<!-- end alternative code -->
<?php }?>


<a class="tbtn floatright" href='?<?php echo $_SERVER['QUERY_STRING']?>&ExportCSV=FILE' >Export to CSV</a>
<div class="clear"></div>

<br/><br/>
