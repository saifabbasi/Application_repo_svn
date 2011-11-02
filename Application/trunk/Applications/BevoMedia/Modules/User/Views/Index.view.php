<?php
//*************************************************************************************************

require_once(PATH . "Legacy.Abstraction.class.php");

		require(PATH . 'classes/clsAdwordsAccounts.php');
		require(PATH . 'classes/clsYahooAccounts.php');
		require(PATH . 'classes/clsMSNAccounts.php');
		require(PATH . 'classes/clsPPCKeywordStats.php');

//*************************************************************************************************

		global $userId, $isSelfHosted;
		$userId = $this->User->id;
		$isSelfHosted = $this->User->IsSelfHosted();
		
		$today = date('Y-m-d', time());
		$tsToday = time();
		
		$langFilename2 = 'publisher-offers.php';

//*************************************************************************************************

		include PATH.'images/charts.php';

//*************************************************************************************************

		//MODIFICATION
		//$arrModels		= array('CPA', 'CPC', 'CPM');
		
		$arrModels		= array('CPA');

		@$crNetworks		= is_array($_GET['crNetworks']) ? $_GET['crNetworks'] : $arrModels;
		@$crRange		= $_GET['crRange'] == '' ? 'today' : $_GET['crRange'];
		
		if(isset($_GET['DateRange']))
		{
			$DateRange = explode('-', $_GET['DateRange']);
			if(sizeof($DateRange)<2)
			{
				$DateRange = array($DateRange[0], $DateRange[0]);
			}
			$_GET['crStartDate'] = $DateRange[0];
			$_GET['crEndDate'] = $DateRange[1];
			if(sizeOf($DateRange) == 1)
				$_GET['crEndDate'] = $DateRange[0];
			
			$crRange = 'custom';
		} else
		{
			$crRange = 'custom';
			$_GET['crStartDate'] = date('m/d/Y', strtotime("-1 days"));
			$_GET['crEndDate'] = date('m/d/Y');
			$_GET['DateRange'] = $_GET['crStartDate'].' - '.$_GET['crEndDate'];
		}
		
		
		
		@$crStartDate	= $_GET['crStartDate'];
		@$crEndDate		= $_GET['crEndDate'];

		@$crStartDate2	= $_GET['crStartDate'];
		@$crEndDate2		= $_GET['crEndDate'];
		
		
		$custom_time=mktime (0,0,0,date('m'),date('d')-7,date('Y'));
		global $analytic_from, $analytic_to;
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
			$sub			= "AND S.statDate = '".$crStartDate."' ";
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
			@$crStartDate	= LegacyAbstraction::addDays($today, -date('w', $tsToday));
			@$crEndDate		= LegacyAbstraction::addDays($crStartDate, 6);
			@$crStartDate2	= LegacyAbstraction::addDays($today, -date('w', $tsToday));
			@$crEndDate2		= LegacyAbstraction::addDays($crStartDate, 6);
			@$sub			= "AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' ";
			@$subAN			= "AND I.EVENT_DATETIME >= '".$crStartDate."' AND I.EVENT_DATETIME <= '".$crEndDate."' ";
			@$crTitle		= "This week's";
			@$anlytic_title	="This week's";
		$custom_time=mktime (0,0,0,date('m'),-date('w'),date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
		}
		
		elseif ( $crRange == 'thismonth' )
		{
			@$crStartDate	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
			@$crEndDate		= LegacyAbstraction::addDays($crStartDate, date('t', $tsToday)-1);
			@$crStartDate2	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
			@$crEndDate2		= LegacyAbstraction::addDays($crStartDate, date('t', $tsToday)-1);
			@$sub			= "AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' ";
			@$subAN			= "AND I.EVENT_DATETIME >= '".$crStartDate."' AND I.EVENT_DATETIME <= '".$crEndDate."' ";
			@$crTitle		= "This months's";
			$anlytic_title	="This months's";
		$custom_time=mktime (0,0,0,date('m'),1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
		}
		
		elseif ( $crRange == 'thisyear' )
		{
			@$crStartDate	= date('Y-01-01', $tsToday);
			@$crEndDate		= date('Y-12-31', $tsToday);
			@$crStartDate2	= date('Y-01-01', $tsToday);
			@$crEndDate2		= date('Y-12-31', $tsToday);
			@$sub			= "AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' ";
			@$subAN			= "AND I.EVENT_DATETIME >= '".$crStartDate."' AND I.EVENT_DATETIME <= '".$crEndDate."' ";
			@$crTitle		= "This year's";
			@$anlytic_title	="year's";
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
			$sub			= "AND S.statDate >= '".date('Y-m-d', strtotime($crStartDate))."' AND S.statDate <= '".date('Y-m-d', strtotime($crEndDate))."' ";
			$subAN			= "AND I.EVENT_DATETIME >= '".date('Y-m-d', strtotime($crStartDate))."' AND I.EVENT_DATETIME <= '".date('Y-m-d', strtotime($crEndDate))."' ";
			//$crTitle		= formatDate($crStartDate)." - ".formatDate($crEndDate);
			
			$analytic_from= date('Y-m-d', strtotime($crStartDate));
			$analytic_to= date('Y-m-d', strtotime($crEndDate));
		}
		
		else
		{
			$crStartDate	= $today;
			$crEndDate		= $today;
			$crStartDate2	= LegacyAbstraction::addDays($today, -1);
			$crEndDate2		= $today;
//			$crStartDate2	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
//			$crEndDate2		= LegacyAbstraction::addDays($crStartDate, date('t', $tsToday)-1);
			$sub			= "AND S.statDate = '".$crStartDate."' ";
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

		
		
		@$nnStartDate	= LegacyAbstraction::addDays($today, -date('d', $tsToday)+1);
		@$nnEndDate		= LegacyAbstraction::addDays($nnStartDate, date('t', $tsToday)-1);
		
		if(!isset($DateRange))
		{
			$crStartDate = date('Y-m-d', strtotime(LegacyAbstraction::addDays($today, -1)));
			$crEndDate = date('Y-m-d', strtotime(LegacyAbstraction::addDays($today, 0)));
		}else{
			$crStartDate = date('Y-m-d', strtotime($DateRange[0]));
			$crEndDate = date('Y-m-d', strtotime($DateRange[1]));
		}
		
		$nnStartDate = date('Y-m-01', strtotime($nnStartDate));
		$nnEndDate = date('Y-m-d', strtotime($nnEndDate));
		
		
		//$sql = "SELECT N.MODEL, N.ID, N.TITLE, N.adminCommission, SUM(S.clicks) AS clicks, SUM(S.CONVERSIONS) AS CONVERSIONS, SUM(S.REVENUE)*(100-N.adminCommission)/100 AS REVENUE, ((SUM(S.REVENUE)*(100-N.adminCommission)/100)*1000)/1 AS ECPM, UAN.STATUS FROM bevomedia_user_aff_network UAN, bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network_stats S ON S.user__id = '".$userId."' AND S.network__id = N.ID ".$sub." WHERE N.ID = UAN.NETWORK_ID AND UAN.USERID = '".$userId."' AND UAN.STATUS = '".APP_STATUS_ACCEPTED."' AND N.ISVALID = 'Y' GROUP BY N.ID ORDER BY N.MODEL, N.TITLE";
		//MODIFIED SQL
		$sql = "SELECT
					S.id,
					N.model,
					N.id,
					N.title,
					N.adminCommission,
					SUM( S.clicks ) AS clicks,
					SUM( S.CONVERSIONS ) AS CONVERSIONS,
					SUM(S.REVENUE)*(100-N.adminCommission)/100 AS REVENUE,
					((SUM(S.REVENUE)*(100-N.adminCommission)/100)*1000)/1 AS ECPM
		
				FROM bevomedia_aff_network N

				LEFT JOIN bevomedia_user_aff_network_subid S ON S.network__id = N.ID
				AND S.statDate >= '$crStartDate'
				AND S.statDate <= '$crEndDate'

				WHERE S.user__id = $userId
				AND N.ISVALID = 'Y'

				GROUP BY N.ID
				ORDER BY N.TITLE
				";
		
		/*print '<!--DEBUG ONE:' . "\n";
		print $sql;
		print '-->';*/
		//$sql = "SELECT N.MODEL, N.ID, N.TITLE, N.adminCommission, SUM(S.clicks) AS clicks, SUM(S.CONVERSIONS) AS CONVERSIONS, SUM(S.REVENUE)*(100-N.adminCommission)/100 AS REVENUE, ((SUM(S.REVENUE)*(100-N.adminCommission)/100)*1000)/1 AS ECPM, UAN.STATUS FROM bevomedia_user_aff_network UAN, bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network_stats S ON S.user__id = '".$userId."' AND S.network__id = N.ID ".$sub." WHERE N.ID = UAN.NETWORK_ID AND UAN.USERID = '".$userId."' AND UAN.STATUS = '".APP_STATUS_ACCEPTED."' AND N.ISVALID = 'Y' GROUP BY N.ID ORDER BY N.MODEL, N.TITLE";
		$res = LegacyAbstraction::executeQuery($sql);
		while ( $row = LegacyAbstraction::getRow($res) )
		{
			$row['adminCommission'] = 0;
			//$sql2 = "SELECT SUM(REVENUE) AS REVENUE FROM bevomedia_user_aff_network_stats WHERE USERID = '".$userId."' AND NETWORK_ID = ".$row['ID']." AND STAT_DATE BETWEEN '".$nnStartDate."' AND '".$nnEndDate."'";
			$sql2 = "SELECT
					S.id,
					N.model,
					N.id,
					N.title,
					N.adminCommission,
					SUM( S.clicks ) AS clicks,
					SUM( S.conversions ) AS CONVERSIONS,
					SUM(S.revenue)*(100-N.adminCommission)/100 AS REVENUE,
					((SUM(S.revenue)*(100-N.adminCommission)/100)*1000)/1 AS ECPM
		
				FROM bevomedia_aff_network N

				LEFT JOIN bevomedia_user_aff_network_subid S ON S.id = N.id
				AND S.statDate >= '$nnStartDate'
				AND S.statDate <= '$nnEndDate'

				WHERE S.user__id = $userId
				AND N.isValid = 'Y'
				AND N.id = {$row['id']}

				GROUP BY N.id
				ORDER BY N.title";
			 
			$res2 = LegacyAbstraction::executeQuery($sql2);
			$row2 = LegacyAbstraction::getRow($res2);
			
			if (count($row2)>0)
			{
				$row['MTD']		= (float)($row2['REVENUE']*((100-$row['adminCommission'])/100));
	
				$row['ISUSER']	= false;
			}
			
			$arrNetworks[$row['model']][] = $row;
		}
		
		LegacyAbstraction::free($res);
//*************************************************************************************************

		//asort($arrNetworks);

//*************************************************************************************************

		$arrNetsJoined = array();
		$res = LegacyAbstraction::executeQuery("SELECT N.model, N.ID, N.TITLE, UAN.STATUS FROM bevomedia_aff_network N, bevomedia_user_aff_network UAN WHERE UAN.user__id = '".$userId."' AND UAN.status = '".APP_STATUS_ACCEPTED."' AND UAN.id = N.ID AND N.ISVALID = 'Y' ORDER BY N.TITLE");
		while ( $row = LegacyAbstraction::getRow($res) )
		{
			$row['ISUSER']	= false;

			$arrNetsJoined[] = $row;
		}
		LegacyAbstraction::free($res);
		
//*************************************************************************************************

		$showSearchOffers	= true;

//*************************************************************************************************

/* PPC Functions */

LegacyAbstraction::$strStartDateVal = $crStartDate;
LegacyAbstraction::$strEndDateVal = $crEndDate;

function ListAdwordsAccounts() {
	global $userId, $isSelfHosted;
	
	$objAdwords = new AdwordsAccounts();
	$objAdwords->GetListByUserID($userId);
	
	if ($objAdwords->RowCount == 0) {
?>
  <tr>
    <td class="border">&nbsp;</td>
    <td colspan="5">No accounts found. <a class="tbtn" href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/Index.html#PPC">Add Account...</a></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	$blnAltRow = false;
	$arrStats = LoadAccountStats(1);
	
	
	while ($arrThisRow = $objAdwords->GetRow()) {
		@$arrThisStats = $arrStats[$arrThisRow['ID']];
		
		if (!is_array($arrThisStats)) {
			$arrThisStats['NetImpr'] = 0;
			$arrThisStats['NetClicks'] = 0;
			$arrThisStats['AvgCPC'] = 0;
			$arrThisStats['AvgCPM'] = 0;
			$arrThisStats['NetCost'] = 0;
			$arrThisStats['AvgPos'] = 0;
		}
				
		if ($arrThisStats['NetImpr'] != 0) {
			$arrThisStats['NetCtr'] = round($arrThisStats['NetClicks'] / $arrThisStats['NetImpr'], 2);
		}
		else {
			$arrThisStats['NetCtr'] = 0;
		}
		
		
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td class="border">&nbsp;</td>
    <td><a href="/BevoMedia/Publisher/AccountStatsPPC.html?Provider=Adwords&nbsp;ID=<?php echo $arrThisRow['ID']; ?>"><?php echo htmlspecialchars($arrThisRow['AdwordsEmail']); ?></a></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetImpr']; ?></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetClicks']; ?></td>
	<td style="text-align: center;"><?php echo round($arrThisStats['NetCtr'] *100 , 2); ?>%</td>
	<td style="text-align: center;">$<?php echo $arrThisStats['NetCost']; ?></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
	
	if ($objAdwords->RowCount > 0)
	{
?>
				  <tr>
					<td class="border">&nbsp;</td>
					<td colspan="5"><a class="tbtn" href="<?= Zend_Registry::get('System/BaseURL')?>/BevoMedia/Publisher/Index.html#PPC">Add Account...</a></td>
					<td class="tail">&nbsp;</td>
				  </tr>

<?
	}
}

function ListYahooAccounts() {
	global $userId, $isSelfHosted;
	
	$objYahoo = new YahooAccounts();
	$objYahoo->GetListByUserID($userId);
	
	if ($objYahoo->RowCount == 0) {
?>
  <tr>
    <td class="border">&nbsp;</td>
    <td colspan="5">No accounts found. <a class="tbtn" href="<?= Zend_Registry::get('System/BaseURL')?>/BevoMedia/Publisher/Index.html#PPC">Add Account...</a></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	$blnAltRow = false;
	$arrStats = LoadAccountStats(2);
	
	while ($arrThisRow = $objYahoo->GetRow()) {
		@$arrThisStats = $arrStats[$arrThisRow['ID']];
		
		
		if (!is_array($arrThisStats)) {
			$arrThisStats['NetImpr'] = 0;
			$arrThisStats['NetClicks'] = 0;
			$arrThisStats['AvgCPC'] = 0;
			$arrThisStats['AvgCPM'] = 0;
			$arrThisStats['NetCost'] = 0;
			$arrThisStats['AvgPos'] = 0;
		}
				
		if ($arrThisStats['NetImpr'] != 0) {
			$arrThisStats['NetCtr'] = round($arrThisStats['NetClicks'] / $arrThisStats['NetImpr'], 2);
		}
		else {
			$arrThisStats['NetCtr'] = 0;
		}
		
		
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td class="border">&nbsp;</td>
    <td><a href="/BevoMedia/Publisher/AccountStatsPPC.html?Provider=Yahoo&nbsp;ID=<?php echo $arrThisRow['ID']; ?>"><?php echo htmlspecialchars($arrThisRow['Username']); ?></a></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetImpr']; ?></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetClicks']; ?></td>
	<td style="text-align: center;"><?php echo round($arrThisStats['NetCtr'] * 100, 2); ?>%</td>
	<td style="text-align: center;">$<?php echo $arrThisStats['NetCost']; ?></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
	
	if ($objYahoo->RowCount > 0)
	{
?>
				  <tr>
					<td class="border">&nbsp;</td>
					<td colspan="5"><a class="tbtn" href="<?= Zend_Registry::get('System/BaseURL')?>/BevoMedia/Publisher/Index.html#PPC">Add Account...</a></td>
					<td class="tail">&nbsp;</td>
				  </tr>

<?
	}
	
	
}
function ListMSNAccounts() {
	global $userId, $isSelfHosted;
	
	$objMSN = new MSNAccounts();
	$objMSN->GetListByUserID($userId);
	
	if ($objMSN->RowCount == 0) {
?>
  <tr>
    <td class="border">&nbsp;</td>
    <td colspan="5">No accounts found. <a class="tbtn" href="<?= Zend_Registry::get('System/BaseURL')?>/BevoMedia/Publisher/Index.html#PPC">Add Account...</a></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		return false;
	}
	$blnAltRow = false;
	$arrStats = LoadAccountStats(3);
	
	while ($arrThisRow = $objMSN->GetRow()) {
		@$arrThisStats = $arrStats[$arrThisRow['ID']];
		
		
		if (!is_array($arrThisStats)) {
			$arrThisStats['NetImpr'] = 0;
			$arrThisStats['NetClicks'] = 0;
			$arrThisStats['AvgCPC'] = 0;
			$arrThisStats['AvgCPM'] = 0;
			$arrThisStats['NetCost'] = 0;
			$arrThisStats['AvgPos'] = 0;
		}
				
		if ($arrThisStats['NetImpr'] != 0) {
			$arrThisStats['NetCtr'] = round($arrThisStats['NetClicks'] / $arrThisStats['NetImpr'], 2);
		}
		else {
			$arrThisStats['NetCtr'] = 0;
		}
		
		
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td class="border">&nbsp;</td>
    <td><a href="/BevoMedia/Publisher/AccountStatsPPC.html?Provider=MSN&nbsp;ID=<?php echo $arrThisRow['ID']; ?>"><?php echo htmlspecialchars($arrThisRow['Name']); ?></a></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetImpr']; ?></td>
	<td style="text-align: center;"><?php echo $arrThisStats['NetClicks']; ?></td>
	<td style="text-align: center;"><?php echo round($arrThisStats['NetCtr'] * 100, 2); ?>%</td>
	<td style="text-align: center;">$<?php echo $arrThisStats['NetCost']; ?></td>
	<td class="tail">&nbsp;</td>
  </tr>
<?php
		if ($blnAltRow) { $blnAltRow = false; } else { $blnAltRow = true; }
	}
	
	
	if ($objMSN->RowCount > 0)
	{
?>
				  <tr>
					<td class="border">&nbsp;</td>
					<td colspan="5"><a class="tbtn" href="<?= Zend_Registry::get('System/BaseURL')?>/BevoMedia/Publisher/Index.html#PPC">Add Account...</a></td>
					<td class="tail">&nbsp;</td>
				  </tr>

<?
	}
	
}

function LoadAccountStats($intInProvider) {
	global $userId;
	
	$objStats = new PPCKeywordStats();
	$objStats->GetStatsByProvider($userId, $intInProvider, LegacyAbstraction::$strStartDateVal, LegacyAbstraction::$strEndDateVal);
	
	
	if ($objStats->RowCount == 0) {
		return false;
	}
	
	$arrStats = array();
	
	while ($arrThisRow = $objStats->GetRow()) {
		$arrStats[$arrThisRow['accountId']] = $arrThisRow;
	}
	
	return $arrStats;
}

/* Analytics Functions */
function listDomains()
{
	global $userId;
	$Sql = "SELECT id, domain FROM bevomedia_analytics_domains WHERE user__id = $userId ";
	$Rows = mysql_query($Sql);
	$Domains = array();
	
	if (mysql_num_rows($Rows))
	{
		while ($Row = mysql_fetch_assoc($Rows))
			$Domains[] = $Row;
	}
	
	return $Domains;
}

function getStats($DomainID)
{
	global $DateFrom, $DateTo, $userId;
	global $analytic_from, $analytic_to;
	global $isSelfHosted;
	
	$DomainID = intval($DomainID);
	
	if ( ($DomainID!=-1) && ($DomainID!=0) )
	{
		$DomainAdd = " (bevomedia_analytics_reports.domainId = $DomainID) AND ";
	}
	
	if ($DomainID==0)
	{
		$DateFrom = date("Y-m-d", time()+86400);
		$DateTo = date("Y-m-d", time()+86400);
	}
	
	$total = array();
	
	$Sql = "SELECT
					visits,
					averagePageVisits,
					averageTimeOnSite,
					percentNewVisits,
					bounceRate,
					pageViews,
					total
				FROM
					bevomedia_analytics_reports_siteusage,
					bevomedia_analytics_reports,
					bevomedia_analytics_reports_visitors_overview,
					bevomedia_analytics_domains
				WHERE
					(bevomedia_analytics_reports_siteusage.reportId = bevomedia_analytics_reports.id) AND
					(bevomedia_analytics_reports_visitors_overview.reportId = bevomedia_analytics_reports.id) AND
					(bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$analytic_from') AND DATE('$analytic_to') ) AND
					(bevomedia_analytics_reports.dateTo  BETWEEN DATE('$analytic_from') AND DATE('$analytic_to') ) AND
					{$DomainAdd}
					(bevomedia_analytics_domains.id = bevomedia_analytics_reports.domainId) AND
					(bevomedia_analytics_domains.user__id = $userId)
				ORDER BY
					bevomedia_analytics_reports.id DESC
			";
	$Query = mysql_query($Sql);

	
	if (mysql_num_rows($Query))
	{
		while($Row = mysql_fetch_assoc($Query))
		{
			foreach($Row as $Key=>$Value)
			{
				if(!isset($total[$Key]))
					$total[$Key] = 0;
				$total[$Key] += $Value;
			}
		}
	}
	
	return $total;
}
$is_registerd=false;
		$res = LegacyAbstraction::executeQuery("SELECT * FROM bevomedia_user_aff_network UAN WHERE user__id = '".$userId."' AND STATUS='".APP_STATUS_ACCEPTED."' ");
		
		while ( $row = LegacyAbstraction::getRow($res) )
			$is_registerd=true;
			$showRightPanes		= true;
		LegacyAbstraction::free($res);
		
//*************************************************************************************************

		// Call template
		
function ksOnlyNums($var) { return ((int)$var > 0); }
$networks		= array_filter($crNetworks, 'ksOnlyNums');

function ksOnlyNotNums($var) { return ((int)$var <= 0); }
$models			= array_filter($crNetworks, 'ksOnlyNotNums');

$networkSql		= "";
if ( count($networks) > 0 )
	$networkSql	.= "AND N.ID IN (".implode(',', $networks).") ";

if ( count($models) > 0 )
	$networkSql	.= "AND N.model IN ('".implode("','", $models)."') ";
	
	
	
	
	
	

$sql		= "SELECT N.ID AS NETWORK_ID, SUM(S.CONVERSIONS) AS CONVERSIONS, SUM(S.REVENUE)*(100-N.adminCommission)/100 AS REVENUE FROM bevomedia_user_aff_network UAN, bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network_stats S ON S.network__id = N.ID AND S.user__id = '".$userId."' AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' WHERE N.ISVALID = 'Y' AND UAN.id = N.ID AND UAN.user__id = '".$userId."' ".$networkSql." GROUP BY N.ID";


$arrNets	= array(null);
$arrData1	= array("Conversions");
$arrData2	= array("Revenue ".SCRIPT_DOLLAR);

	$DateRange = str_replace('-','/',$crStartDate).'-'.str_replace('-','/',$crEndDate);
	if(isset($_GET['DateRange']))
		$DateRange = $_GET['DateRange'];

	$ChartXML = new ChartXMLHelper();
	$ChartXML->SetDateRange($DateRange);
	$DateRangeArray = $ChartXML->GetDateRangeArray();
	$DateRangeArray2 = array();
	for($i=0; $i<sizeOf($DateRangeArray); $i+=2)
	{
		$DateRangeArray2[] = @array($DateRangeArray[$i][0],$DateRangeArray[$i+1][1]);
	}
	$ChartXML->SetDateRangeArray($DateRangeArray2);
	$DateRange = $ChartXML->GetDateRangeArray();

$RawDataRight = array();
$temp = new stdClass();
$temp->Name = 'Conversions';
$temp->Stats = array();
$RawDataRight[0] = $temp;
$temp = new stdClass();
$temp->Name = 'Revenue';
$temp->Stats = array();
$RawDataRight[1] = $temp;

foreach($DateRange as $Date)
{
	$crStartDate = $Date[0];
	$crEndDate = $Date[1];
	$sql		= "SELECT N.title, N.id AS NETWORK_ID, SUM(S.CONVERSIONS) AS CONVERSIONS, SUM(S.REVENUE)*(100-N.adminCommission)/100 AS REVENUE FROM bevomedia_user_aff_network UAN, bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network_stats S ON S.network__id = N.ID AND S.user__id = '".$userId."' AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' WHERE N.ISVALID = 'Y' AND UAN.id = N.ID AND UAN.user__id = '".$userId."' ".$networkSql." GROUP BY N.ID";

	$c = $r = 0;
	$res = LegacyAbstraction::executeQuery($sql);
	
	while ( $row = LegacyAbstraction::getRow($res) )
	{
		$title = LegacyAbstraction::getColumn("aff_network", $row['NETWORK_ID']);

		$c += $row['CONVERSIONS']===null?'0':$row['CONVERSIONS'];
		$r += $row['REVENUE']===null?'0.000000':$row['REVENUE'];
		
	}
	$RawDataRight[0]->Stats[] = $c;
	$RawDataRight[1]->Stats[] = $r;
}


if ( count($arrNets) <= 1 )
{
	$arrNets[]	= 'none';
	$arrData1[]	= '0';
	$arrData2[]	= '0';
}



/************************************/
?>

<?php
$networkSql		= "";
if ( count($networks) > 0 )
	$networkSql	.= "AND N.ID IN (".implode(',', $networks).") ";

if ( count($models) > 0 )
	$networkSql	.= "AND N.model IN ('".implode("','", $models)."') ";


$Sql = "SELECT N.TITLE, N.ID AS NETWORK_ID, SUM(S.CONVERSIONS) AS CONVERSIONS, SUM(S.REVENUE)*(100-N.adminCommission)/100 AS REVENUE FROM bevomedia_user_aff_network UAN, bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network_stats S ON S.network__id = N.ID AND S.user__id = '".$userId."' AND S.statDate >= '".$crStartDate."' AND S.statDate <= '".$crEndDate."' WHERE N.ISVALID = 'Y' AND UAN.NETWORK__ID = N.ID AND UAN.user__id = '".$userId."' ".$networkSql." GROUP BY N.ID";
$Query = mysql_query($Sql);
$RawData = array();
while($Row = mysql_fetch_assoc($Query))
{
	$RawData[] = $Row;
}

?>

<?php /* ################################################################################# OUTPUT ############################ */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/Index.html#PPC">New PPC/Analytics Account<span></span></a></li>
			<li><a href="<?= Zend_Registry::get('System/BaseURL') ?>BevoMedia/Publisher/Index.html">New CPA Account<span></span></a></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div class="flashchart">
	<script language="JavaScript" src="https://beta.bevomedia.com/style/AC_RunActiveContent.js"></script>
		       <script language="JavaScript">
		       <!--
		       var requiredMajorVersion = 9;
		       var requiredMinorVersion = 0;
		       var requiredRevision = 45;
		       //-->
		       </script>
	<script language="JavaScript" type="text/javascript">
	<!--
	if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	       alert("This page requires AC_RunActiveContent.js.");
	} else {
	       var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	       if(hasRightVersion) {
		       AC_FL_RunContent(
			       'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,45,0',
			       'width', '300',
			       'height', '200',
			       'scale', 'noscale',
			       'salign', 'TL',
			       'bgcolor', '#fff',
			       'wmode', 'opaque',
			       'movie', '<?=Zend_Registry::get('System/BaseURL');?>/Themes/BevoMedia/images/charts',
			       'src', '<?=Zend_Registry::get('System/BaseURL');?>/Themes/BevoMedia/images/charts',
			       'FlashVars', 'library_path=<?=urlencode("".Zend_Registry::get('System/BaseURL')."/Themes/BevoMedia/images/charts_library")."&nbsp;xml_source=".urlencode(''.Zend_Registry::get('System/BaseURL').'Themes/BevoMedia/images/charts/welcome_graph_left_datagen.php?crStartDate='.date('Y-m-d', strtotime($crStartDate)).'&nbsp;crEndDate='.date('Y-m-d', strtotime($crEndDate)).'&nbsp;userId='.$userId)?>',
			       'id', 'my_chart',
			       'name', 'my_chart',
			       'menu', 'true',
			       'allowFullScreen', 'true',
			       'allowScriptAccess','sameDomain',
			       'quality', 'high',
			       'align', 'middle',
			       'pluginspage', 'https://www.macromedia.com/go/getflashplayer',
			       'play', 'true',
			       'devicefont', 'false'
			       );
	       } else {
		       var alternateContent = 'This content requires the Adobe Flash Player. ';
		       document.write(alternateContent);
	       }
	}
	// -->
	</script>
	<noscript>
	       <P>This content requires JavaScript.</P>
	</noscript>
	<script language="JavaScript" type="text/javascript">
	<!--
	if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	       alert("This page requires AC_RunActiveContent.js.");
	} else {
	       var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	       if(hasRightVersion) {
		       AC_FL_RunContent(
			       'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,45,0',
			       'width', '300',
			       'height', '200',
			       'scale', 'noscale',
			       'salign', 'TL',
			       'bgcolor', '#fff',
			       'wmode', 'opaque',
			       'movie', '<?=Zend_Registry::get('System/BaseURL');?>/Themes/BevoMedia/images/charts',
			       'src', '<?=Zend_Registry::get('System/BaseURL');?>/Themes/BevoMedia/images/charts',
			       'FlashVars', 'library_path=<?=urlencode("".Zend_Registry::get('System/BaseURL')."/Themes/BevoMedia/images/charts_library")."&nbsp;xml_source=".urlencode(''.Zend_Registry::get('System/BaseURL').'/Themes/BevoMedia/images/charts/welcome_graph_right_datagen.php?crStartDate='.date('Y-m-d', strtotime($crStartDate)).'&nbsp;crEndDate='.date('Y-m-d', strtotime($crEndDate)).'&nbsp;userId='.$userId)?>',
			       'id', 'my_chart',
			       'name', 'my_chart',
			       'menu', 'true',
			       'allowFullScreen', 'true',
			       'allowScriptAccess','sameDomain',
			       'quality', 'high',
			       'align', 'middle',
			       'pluginspage', 'https://www.macromedia.com/go/getflashplayer',
			       'play', 'true',
			       'devicefont', 'false'
			       );
	       } else {
		       var alternateContent = 'This content requires the Adobe Flash Player. ';
		       document.write(alternateContent);
	       }
	}
	// -->
	</script>
	<noscript>
	       <P>This content requires JavaScript.</P>
	</noscript>
</div><!--close .flashchart -->

<!-- ENDOF Chart -->



<?php if(isset($_GET['TUTORIAL'])):?>
<script language="javascript">
	firstlogin.init();
</script>
<?php endif?>

	<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/welcome.js.php?r=<?=$r?>"></script>

	<center>
	
	<?= @$info ?>

<?php /*?>
		<table width="100%" cellspacing="0" cellpadding="6" border="0">
			<tr>
				<td style="text-align: center;"><?= InsertChart ( SCRIPT_ROOT.'images/charts/welcome_graph_left_datagen.php?t='.time().'&nbsp;userId='.$userId.'&nbsp;crStartDate='.$crStartDate.'&nbsp;crEndDate='.$crEndDate.makeGetArray($crNetworks, 'crNetworks[]'), 280, 220, 'ffffff' ); ?></td>
				<td style="text-align: center;"><?= InsertChart ( SCRIPT_ROOT.'images/charts/welcome_graph_right_datagen.php?t='.time().'&nbsp;userId='.$userId.'&nbsp;crStartDate='.$crStartDate2.'&nbsp;crEndDate='.$crEndDate2.makeGetArray($crNetworks, 'crNetworks[]'), 280, 220, 'ffffff' ); ?></td>
			</tr>
		</table>
<?php */ ?>
<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>
<style type="text/css">
#tooltip{
	line-height: 1.231; font-family: Arial; font-size: 13px;
	position:absolute;
	border:1px solid #333;
	background:#f7f5d1;
	padding:2px 5px;
	display:none;
	width:285px;
	}
.tooltip {
	color: #ffffff;
	text-decoration: none !important;
	font-weight: bold;
	font-size: 12pt;
	}
.tooltip.defaultLink {
	color: maroon;
	font-size: 12px;
	font-style: normal;
	font-weight: normal;
	font-size: 12px;
	}
.successInstall {
	background-color: #008800;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
.failInstall {
	background-color: #880000;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
</style>

<br />
<br />
<!-- BEGIN Date Range Form -->
<form method="get" name="frmRange" id="form_frmRange">

<table cellspacing="0" cellpadding="3" class="datetable">
  <tr>
  	<td>
  		<a title="Click in the textbox to change the date range of the data you wish to appear in the tables below." class="tooltip">
			<img height="12" width="12" src="/Themes/BevoMedia/img/questionMarkIcon.png"/>
		</a>
		&nbsp;
  	</td>
  	<td><input class="formtxt" id="datepicker" type="text" name="DateRange" value="<?php echo (!isset($_GET['DateRange']))?('Click to Change Date Range'):$_GET['DateRange']; ?>" /></td>
	<td><input class="formsubmit" type="submit" /></td>
  </tr>
</table>

</form>
<!-- ENDOF Date Range Form -->

					<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable">
						<tr class="table_header">
							<td class="hhl">&nbsp;</td>
							<td >&nbsp;</td>
							<td  style="text-align: left;"><?=$crTitle?> Rev</td>
							<td  style="text-align: center;">MTD Rev</td>
							<td   style="text-align: center;">Clicks</td>
							<td   style="text-align: center;">Conversions</td>
							<td   style="text-align: center;">Conversion %</td>
							<td  style="text-align: center;">EPC</td>
							<td class="hhr">&nbsp;</td>
						</tr>
<?
	$model			= '';
	$totTodayRev	= 0;
	$totMTDRev		= 0;
	$totImpression	= 0;
	$totClicks		= 0;
	$totConversions	= 0;
	$totCTR			= 0;
	$toteCPM		= 0;
	$totEPC			= 0;
	$totConvPct		= 0;
	$AffNetwork = new AffiliateNetworkUser();
	//print '<pre style="text-align:left;">';
	$AffNetworks = $AffNetwork->GetAllAffiliateNetworksForUser($this->User->id);

	foreach($AffNetworks as $AffNetwork)
	{
		$AffNetwork = new AffNetwork($AffNetwork->network__id);
		//echo '<pre>'; print_r($AffNetwork);
		/*
		if(!isset($AffNetwork->model))
			print_r($AffNetwork);
		*/
		if(!isset($arrNetworks[$AffNetwork->model]))
			continue;
		$match = false;
		$matchId = 0;
		foreach($arrNetworks[$AffNetwork->model] as $keyItt => $arrNetwork)
		{
			if($arrNetwork['id'] == $AffNetwork->id)
			{
				$match = true;
				$matchId = $keyItt;
			}
		}
		$temp = array();
		foreach($AffNetwork as $Key=>$Value)
			$temp[$Key] = $Value;

		$sql2 = "SELECT SUM(REVENUE) AS REVENUE FROM bevomedia_user_aff_network_stats WHERE USERID = '".$userId."' AND NETWORK_ID = ".$row['ID']." AND STAT_DATE BETWEEN '".$nnStartDate."' AND '".$nnEndDate."'";
		$sql2 = "SELECT
				S.id,
				N.model,
				N.id,
				N.title,
				N.adminCommission,
				SUM( S.clicks ) AS clicks,
				SUM( S.conversions ) AS CONVERSIONS,
				SUM(S.revenue )*(100-N.adminCommission)/100 AS REVENUE,
				((SUM(S.revenue )*(100-N.adminCommission)/100)*1000)/1 AS ECPM
	
			FROM bevomedia_aff_network N
	
			LEFT JOIN bevomedia_user_aff_network_subid S ON S.network__id = N.ID
			AND S.statDate >= '$nnStartDate'
			AND S.statDate <= '$nnEndDate'
	
			WHERE S.user__id = $userId
			AND N.ISVALID = 'Y'
			AND N.id = {$AffNetwork->id}
	
			GROUP BY N.id
			ORDER BY N.title";

		$res2 = LegacyAbstraction::executeQuery($sql2);
		$row2 = LegacyAbstraction::getRow($res2);
		$row2['MTD'] = (float)($row2['REVENUE']*((100-$row['adminCommission'])/100));

		
		$temp['CONVERSIONS'] = 0;
		$temp['clicks'] = 0;
		$temp['ECPM'] = 0;
		$temp['REVENUE'] = 0;
		$temp['MTD'] = $row2['MTD'];
//		echo '<pre>'; print_r($temp); 
//		echo '$AffNetwork->model: '.$AffNetwork->model.', match: '.$match.'<br/>';
		if(!$match)
		{
			$arrNetworks[$AffNetwork->model][] = $temp;
		} else
		{
			if ($arrNetworks[$AffNetwork->model][$matchId]['MTD']==0)
			{
				$arrNetworks[$AffNetwork->model][$matchId]['MTD'] = $temp['MTD'];
			}
		}
			
	}
//echo '<pre>';print_r($arrNetworks);die;
	//print '<pre style="text-align:left;">';
	//print_r($arrNetworks);
	//print '</pre>';
	
	function titleSort($a, $b)
	{
		return ($a['title'] < $b['title'])?-1:1;
	}
	
	$rowCount = 0;
	foreach ( $arrModels as $model )
	{
		$showModelHead = true;

		usort($arrNetworks[$model], "titleSort");
		foreach ( $arrNetworks[$model] as $network )
		{
			//print_r($network['TITLE']);
			if ( !in_array($network['id'], $crNetworks) && !in_array($network['model'], $crNetworks) )
				continue;
			
			if ( $showModelHead )
			{
				$showModelHead = false;
?>
						<tr>
							<td class="border">&nbsp;</td>
							<td class="GridHead" colspan="7" style="border-left: none;"><?=$model?></td>
							<td class="tail">&nbsp;</td>
						</tr>
<?
			}
			$CTR	= number_format($network['model'] == 'CPA' ? LegacyAbstraction::divideEx($network['CONVERSIONS'], $network['clicks']) : LegacyAbstraction::divideEx($network['clicks'], $network['IMPRESSIONS']), 2);
			$eCPM	= $network['ECPM'];
			$rowCount++;
?>
						<tr>
							<td class="border">&nbsp;</td>
							<td  style="text-align: left;"><span><a href='/BevoMedia/Offers/MyStats.html?network=<?php echo $network['id']?>'><?=$network['title']?></a></span></td>
							<td  style="text-align: center;"><span><?=LegacyAbstraction::getFormattedPrice($network['REVENUE'])?></span></td>
							<td  style="text-align: center;"><span><?=LegacyAbstraction::getFormattedPrice($network['MTD'])?></span></td>
							<td  style="text-align: center;"><span><?=(int)$network['clicks']?></span></td>
							<td  style="text-align: center;"><span><?=(int)$network['CONVERSIONS']?></span></td>
							<td  style="text-align: center;"><span><?=number_format(LegacyAbstraction::divideEx($network['CONVERSIONS'], $network['clicks'])*100,1)?>%</span></td>
							<td  style="text-align: center;" ><span>$<?=number_format(LegacyAbstraction::divideEx($network['REVENUE'], $network['clicks']),2)?></span></td>
							<td class="tail">&nbsp;</td>
						</tr>
<?
			$totTodayRev	+= $network['REVENUE'];
			$totMTDRev		+= $network['MTD'];
			$totImpression	+= $network['model']=='CPA'?0:(int)$network['impressions'];
			$totClicks		+= $network['clicks'];
			$totConversions	+= $network['CONVERSIONS'];
			$totCTR			+= $CTR;
			$totEPC			+= LegacyAbstraction::divideEx($network['REVENUE'], $network['clicks']);
			$totConvPct		+= LegacyAbstraction::divideEx($network['CONVERSIONS'], $network['clicks']);
			$toteCPM		+= $network['model']=='CPA'?0:($eCPM*$network['impressions']);
		}
	}
?>

		<?php if($rowCount == 0):?>
		
						<tr>
							<td class="border">&nbsp;</td>
							<td colspan='7' style="text-align: center; font-weight: bold;">You currently do not have any CPA Networks installed.<br/><a class="tbtn" href='/BevoMedia/Publisher/Index.html'>Click here to install your networks.</a></td>
							<td class="tail">&nbsp;</td>
						</tr>
		
		<?php else:?>
						<tr>
							<td class="border">&nbsp;</td>
							<td  style="text-align: right; font-weight: bold;"><span>TOTAL:</span></td>
							<td  style="text-align: center; font-weight: bold;"><span><?=LegacyAbstraction::getFormattedPrice($totTodayRev)?></span></td>
							<td  style="text-align: center; font-weight: bold;"><span><?=LegacyAbstraction::getFormattedPrice($totMTDRev)?></span></td>
							<td  style="text-align: center; font-weight: bold;"><span><?=$totClicks?></span></td>
							<td  style="text-align: center; font-weight: bold;"><span><?=number_format($totConversions)?></span></td>
							<td  style="text-align: center; font-weight: bold;"><span><?=number_format($totConvPct*100,1)?>%</span></td>
							<td  style="text-align: center; font-weight: bold;"><span>$<?=number_format($totEPC,2)?></span></td>
							<td class="tail">&nbsp;</td>
						</tr>
		<?php endif?>
						<tr class="table_footer">
							<td class="hhl"></td>
							<td style="border-left: none;" colspan="7"></td>
							<td class="hhr"></td>
						</tr>
					</table>
                    <br /><br />

<!-- PPC Table -->
                 <table border="0" cellpadding="0" cellspacing="0" class="btable">
                   <tr class="table_header">
					  <td class="hhl"><!-- --></td>
                      <td style="text-align: center;">Account</td>
                      <td style="text-align: center;">Impressions</td>
					  <td style="text-align: center;">Clicks</td>
                      <td style="text-align: center;">CTR</td>
                      <td style="text-align: center;">Cost</td>
                      <td class="hhr"><!-- --></td>
                    </tr>
<!-- Google Accounts -->
                    <tr>
                    <td class="border" style=" background-color: #ffffff;">&nbsp;</td>
                     <td colspan="5" style=" background-color: #ffffff;" >
                     <span><img src="<?=SCRIPT_ROOT?>img/galogo.jpg"></span></td>
                     <td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListAdwordsAccounts(); ?>
                    

<!-- Yahoo Accounts -->
                     <tr>
						<td class="border" style=" background-color: #ffffff;">&nbsp;</td>
						<td style="border-left: none; background-color: #ffffff;" colspan="5"  ><span><img style="margin-left: 5px;" src="<?=SCRIPT_ROOT?>img/ysmlogo.gif"></span></td>
						<td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListYahooAccounts(); ?>
<!-- MSN Accounts -->
                    <tr>
                    <td class="border" style=" background-color: #ffffff;">&nbsp;</td>
                     <td colspan="5" style=" background-color: #ffffff;" >
                     <span><img src="<?=SCRIPT_ROOT?>img/adcentersmall.gif"></span></td>
                     <td class="tail" style=" background-color: #ffffff;">&nbsp;</td>
                    </tr>
<?php ListMSNAccounts(); ?>

<!-- Footer -->
					 <tr class="table_footer">
					  <td class="hhl">&nbsp;</td>
					  <td colspan="5">&nbsp;</td>
					  <td class="hhr">&nbsp;</td>
					 </tr>
                  </table>
<!-- End PPC Table -->
					
                    <br /><br />
                    
<!-- Analytics Table -->
                    <?php if(sizeof(listDomains()))
	{
	?>
		<table cellspacing="0" cellpadding="3" border="0" class="btable">
						<tr class="table_header_big">
							<td class="hhlb">&nbsp;</td>
							<td class="STYLE2">Accounts</td>
							<td class="STYLE2">Uniques</td>
							<td class="STYLE2">Page<br />Views</td>
							<td class="STYLE2">Page<br />Visits</td>
						  	<td class="STYLE2">New<br />Visits</td>
							<td class="STYLE2">Bounce<br />Rate</td>
                            <td class="STYLE2">Avg Page<br/> Visits</td>
                            <td class="STYLE2">Time<br />On<br />Site</td>
                            <td class="hhrb">&nbsp;</td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
							<td class="GridHead" style="border-left: none;">Analytics</td>
							<td class="GridSubHead">&nbsp;</td>
							<td class="GridSubHead">&nbsp;</td>
							<td class="GridSubHead">&nbsp;</td>
							<td class="GridSubHead">&nbsp;</td>
							<td class="GridSubHead">&nbsp;</td>
                            <td class="GridSubHead">&nbsp;</td>
							 <td class="GridSubHead">&nbsp;</td>
							<td class="border4" style="border-left: none;">&nbsp;</td>
                           
							 
						</tr>
<?
    foreach (listDomains() as $Domain)
    {
        $Results = getStats($Domain["id"]);
?>
						<tr>
							<td>&nbsp;</td>
							<td class="GridRowHead" style="border-left: none;"><span><?=$Domain["domain"]?></span></td>
                            <td style="text-align: center;"><span><?=@number_format($Results['total'], 0)?></span></td>
                            <td style="text-align: center;"><span><?=@number_format($Results['pageViews'], 0)?></span></td>
                            <td style="text-align: center;"><span><?=@number_format($Results['visits'], 0)?></span></td>
                            <td style="text-align: center;"><span><?=@number_format($Results['percentNewVisits'], 2)?></span></td>
							
                            <td style="text-align: center;"><span><?=@number_format($Results['bounceRate'], 2)?>%</span></td>
                            <td style="text-align: center;"><span><?=@number_format($Results['averagePageVisits'], 2)?></span></td>
                            <td style="text-align: center;"><span><?=@number_format($Results['averageTimeOnSite'], 2)?></span></td>
                            <td class="border4" style="border-left: none;">&nbsp;</td>
						</tr>
<?
    }
?>

						<tr class="table_footer">
							<td class="hhl">&nbsp;</td>
							<td colspan="8">&nbsp;</td>

							<td class="hhr">&nbsp;</td>
						</tr>
	  </table>
                   <?php
					
					}else{
						?>
								<table width="600" cellspacing="0" cellpadding="3" border="0" class="btable">
						<tr class="table_header_big">
							<td class="hhlb">&nbsp;</td>
							<td class="STYLE2">Accounts</td>
							<td class="STYLE2">Uniques</td>
							<td class="STYLE2">Page<br />Views</td>
							<td class="STYLE2">Page<br />Visits</td>
						  	<td class="STYLE2">New<br />Visits</td>
							<td class="STYLE2">Bounce<br />Rate</td>
                            <td class="STYLE2">Avg Page<br/> Visits</td>
                            <td class="STYLE2">Time<br />On<br />Site</td>
                            <td class="hhrb">&nbsp;</td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
							<td class="GridHead" style="border-left: none;">Analytics</td>
							<td class="GridSubHead" colspan='7'>&nbsp;</td>
							<td class="border4" style="border-left: none;">&nbsp;</td>
						</tr>
						  <tr>
							<td class="border">&nbsp;</td>
							<td colspan="8">No accounts found.<a class="tbtn" href="<?= Zend_Registry::get('System/BaseURL')?>/BevoMedia/Publisher/Index.html#PPC">Add Account...</a></td>
							<td class="tail">&nbsp;</td>
						  </tr>
						<tr class="table_footer">
							<td class="hhl">&nbsp;</td>
							<td colspan="8">&nbsp;</td>

							<td class="hhr">&nbsp;</td>
						</tr>
						</table>
						
						<?php
					}
					
					?>
                    
<!-- End Analytics Table -->
                    
                    

					<br>


					
								<font class="main">
									* Most CPA Network Stats Update Once An Hour.<br>
									* PPC and Analytics accounts update once a day.<br>
								</font>
							

	</center>
	
<script language="javascript">
function customRepNetwork(objCheckbox, model)
{
	if ( objCheckbox.checked )
		document.getElementById('model_'+model).checked = false;
}

function customRepModel(objCheckbox, model)
{
	if ( !objCheckbox.checked )
		return;

	eval("networks = model_"+model+";");

	for ( i=0; i<networks.length; i++ )
		document.getElementById('network_'+networks[i]).checked = false;
}

function customRepRange(objCheckbox)
{
	document.getElementById('customRepRangeCustom').style.display = objCheckbox.value == 'custom' ? 'block' : 'none';
}

function nwAllClicked(chk)
{
	if ( chk )
	{
		for ( i=0; i<nwIds.length; i++ )
			document.getElementById('nw'+nwIds[i]).checked = false;
	}
}

function nwOtherClicked(chk)
{
	if ( chk )
	{
		document.getElementById('nwAll').checked = false;
	}
}

</script>

<?php 
	if ( (($this->VerifiedBoxFirstLogin) && !isset($_GET['TUTORIAL']) && !isset($_GET['STEP'])) || (isset($_GET['VerifyBox'])) ) {
?>
<script type="text/javascript">
	$(document).ready(function() {
		Shadowbox.open({
	        content:    '/BevoMedia/Publisher/VerifyTutorial.html?ajax=true&nbsp;FirstLogin=1',
	        player:     "iframe",
	        title:      "Verify",
	        height:     480,
	        width:      640
	    });
	});
</script>
<?php 
	}
?>
