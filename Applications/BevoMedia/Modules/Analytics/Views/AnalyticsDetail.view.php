<?php
//*************************************************************************************************


require_once(PATH . "Legacy.Abstraction.class.php");

//*************************************************************************************************
// some comment some comment

include PATH . 'images/charts.php';
//*************************************************************************************************
global $userId, $isSelfHosted;
$userId = $this->User->id;
$isSelfHosted = $this->User->IsSelfHosted();



//*************************************************************************************************
/*$akeywords		= doCleanInput($_GET['akeywords']);
		$subSql			= " ORDER BY EVENT_DATE DESC LIMIT 5";
		if ( $akeywords != "" )
			$subSql		= " AND HEADLINE LIKE '%".handleSingleQuote($akeywords)."%' ORDER BY HEADLINE";

		$arrArticles	= array();
		$res = LegacyAbstraction::executeQuery("SELECT * FROM ".PREFIX."ARTICLE WHERE ISVALID = 'Y' AND APPROVED = 'Y' ".$subSql);
		while ( $row = LegacyAbstraction::getRow($res) )
			$arrArticles[] = $row;
		LegacyAbstraction::free($res);*/

//*************************************************************************************************



$isAnalyticPage = true;
$showRightPanes = false;

//*************************************************************************************************


//*************************************************************************************************



require(PATH . 'inc_daterange.php');

$crRange = LegacyAbstraction::$strDateRangeVal;
$analytic_from = date('Y-m-d', strtotime(LegacyAbstraction::$strStartDateVal));
$analytic_to = date('Y-m-d', strtotime(LegacyAbstraction::$strEndDateVal));


$crTitle = '';

//*************************************************************************************************

//$isOverviewPage = true;
$showRightPanes = true;

//*************************************************************************************************


$sub = "";
$subAN = "";
$save_time = $crRange;

global $DateTo, $DateFrom;
$DateTo = $analytic_to;
$DateFrom = $analytic_from;

//*************************************************************************************************

function listDomains() {
	global $userId;
	$Sql = "SELECT id, domain FROM bevomedia_analytics_domains WHERE user__id = $userId ";
	$Rows = mysql_query ( $Sql );
	$Domains = array ();
	
	if (mysql_num_rows ( $Rows )) {
		while ( $Row = mysql_fetch_assoc ( $Rows ) )
			$Domains [] = $Row;
	}
	return $Domains;
}

function getStats($DomainID) {
	global $DateFrom, $DateTo, $userId, $isSelfHosted;
	
	$DomainID = intval ( $DomainID );
	
	if (($DomainID != - 1) && ($DomainID != 0)) {
		$DomainAdd = " (bevomedia_analytics_reports.domainId = $DomainID) AND ";
	}
	
	if($DateTo == '')
	{
		$DateFrom = date ( "Y-m-d", time () + 86400 );
		$DateTo = date ( "Y-m-d", time () + 86400 );
	}
	
	if ($DomainID == 0) {
		$DateFrom = date ( "Y-m-d", time () + 86400 );
		$DateTo = date ( "Y-m-d", time () + 86400 );
	}
	
	$Sql = "SELECT
                            SUM(visits) as Visits,
                            SUM(averagePageVisits) as averagePageVisits,
                            AVG(averageTimeOnSite) as averageTimeOnSite,
                            AVG(percentNewVisits) as percentNewVisits,
                            AVG(bounceRate) as bounceRate,
                            SUM(pageViews) as pageViews,
                            SUM(total) as total
                        FROM
                            bevomedia_analytics_reports_siteusage,
                            bevomedia_analytics_reports,
                            bevomedia_analytics_reports_visitors_overview,
                            bevomedia_analytics_domains
                        WHERE
                            (bevomedia_analytics_reports_siteusage.ReportID = bevomedia_analytics_reports.ID) AND
                            (bevomedia_analytics_reports_visitors_overview.ReportID = bevomedia_analytics_reports.ID) AND
                            (bevomedia_analytics_reports.DateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                            (bevomedia_analytics_reports.DateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                            {$DomainAdd}
                            (bevomedia_analytics_domains.ID = bevomedia_analytics_reports.DomainID) AND
                            (bevomedia_analytics_domains.user__Id = $userId )
						GROUP BY bevomedia_analytics_domains.ID
                        ORDER BY
                            bevomedia_analytics_reports.ID DESC
                    ";
                            

	$Row = mysql_query ( $Sql );
	
	if (mysql_num_rows ( $Row )) {
		$Row = mysql_fetch_assoc ( $Row );
		return $Row;
	}
	
	return array ();
}

//*************************************************************************************************


function getMonthlyData($XML = true, $SubChart = false) {
	global $userId, $isSelfHosted;
	$DateFrom = date ( "Y-m-d", strtotime ( "-30 days" ) );
	$DateTo = date ( "Y-m-d" );
	
	$Sql = "SELECT
                                SUM(Total) AS `Total`,
                                SUM(Visits) AS `Visits`,
                                SUM(PageViews) AS `PageViews`,
                                bevomedia_analytics_reports.DateFrom as `Date`
                            FROM
                                bevomedia_analytics_reports_siteusage,
                                bevomedia_analytics_reports,
                                bevomedia_analytics_reports_visitors_overview,
                                bevomedia_analytics_domains
                            WHERE
                                (bevomedia_analytics_reports_siteusage.ReportID = bevomedia_analytics_reports.ID) AND
                                (bevomedia_analytics_reports_visitors_overview.ReportID = bevomedia_analytics_reports.ID) AND
                                (bevomedia_analytics_reports.DateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                                (bevomedia_analytics_reports.DateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                                (bevomedia_analytics_domains.ID = bevomedia_analytics_reports.DomainID) AND
                                (bevomedia_analytics_domains.user__Id = $userId)
                            GROUP BY
                                bevomedia_analytics_reports.DateFrom
                            ORDER BY
                                bevomedia_analytics_reports.DateFrom ASC
                        ";
	$Results = mysql_query ( $Sql );
	
	$Data = array ();
	if (mysql_num_rows ( $Results )) {
		while ( $Row = mysql_fetch_assoc ( $Results ) ) {
			$Data [] = $Row;
		}
	}
	
	if($isSelfHosted == '1')
	{
		//$Data = array();
	}
	
	$Days = "<string>Days</string>";
	$UniqueVisits = "<string>Unique Visits</string>";
	$PageViews = "<string>Page Views</string>";
	$PageVisits = "<string>Page Visits</string>";
	
	$i = 0;
	$DateItt = $DateFrom;
	while ( strtotime ( $DateItt ) <= strtotime ( date ( "Y-m-d" ) ) ) {
		$Days .= "<string>" . date ( "m/d/Y", strtotime ( $DateItt ) ) . "</string>\r\n";
		
		$UniqueVisitsNumber = 0;
		$PageViewsNumber = 0;
		$PageVisitsNumber = 0;
		if (@$Data [$i] ["Date"] == $DateItt) {
			$UniqueVisitsNumber = $Data [$i] ["Total"];
			$PageViewsNumber = $Data [$i] ["PageViews"];
			$PageVisitsNumber = $Data [$i] ["Visits"];
			$i ++;
		}
		
		$UniqueVisits .= "<number>{$UniqueVisitsNumber}</number>\r\n";
		$PageViews .= "<number>{$PageViewsNumber}</number>\r\n";
		$PageVisits .= "<number>{$PageVisitsNumber}</number>\r\n";
		
		$DateItt = date ( "Y-m-d", strtotime ( "+1 day", strtotime ( $DateItt ) ) );
	}
	
	if ($SubChart == false) {
		return "
                                                
                                <chart>
                                    <license>GTA9I-PM7Q.O.945CWK-2XOI1X0-7L</license>
                                    <chart_data>
                                        <row>
                                            {$Days}
                                        </row>
                                        <row>
                                            {$UniqueVisits}
                                        </row>
                                        <row>
                                            {$PageViews}
                                        </row>
                                        <row>
                                            {$PageVisits}
                                        </row>

                                    </chart_data>

                                   
                                    <axis_value alpha='0' />
                                    <axis_category alpha='0' />
                                    <axis_ticks value_ticks='false' category_ticks='false' />
                                            
                                    <chart_border top_thickness='0' bottom_thickness='0' left_thickness='0' right_thickness='0' />
                                    
                                    <chart_grid_h alpha='0' />
                                    <chart_grid_v alpha='5' color='000000' thickness='1' />
                                    <chart_pref line_thickness='1' point_shape='none' fill_shape='false' />

                                    <chart_rect positive_alpha='0' y='184' height='36' />
                                    <chart_transition type='dissolve' delay='1' />
                                    <chart_type>Line</chart_type>
                                    
                                    <draw>
                                        <image transition='dissolve' url='/Themes/BevoMedia/images/charts.swf?library_path=/Themes/BevoMedia/images/charts_library&nbsp;xml_source=/BevoMedia/Analytics/AnalyticsDetail.html?XML=getMonthlyDataSubChart' />
                                    </draw>
                                    <filter>
                                    
                                        <shadow id='high' distance='5' angle='45' alpha='35' blurX='10' blurY='10' />

                                        <shadow id='high2' distance='5' angle='45' alpha='35' blurX='10' blurY='10' knockout='true' />
                                        <shadow id='low' distance='2' angle='45' alpha='35' blurX='5' blurY='5' />
                                        <bevel id='bg' angle='90' blurX='0' blurY='100' distance='25' highlightAlpha='10' highlightColor='ffffff' shadowColor='4c5e6f' shadowAlpha='20' inner='true' />
                                    </filter>

                                    <legend layout='hide' />
                                    <series_color>
                                        <color>000080</color>
                                        <color>0000FF</color>
                                        <color>8080FF</color>
                                    </series_color>


                                </chart>
                        ";
	} else {
		return "
                                                
                            <chart>
                                <license>GTA9I-PM7Q.O.945CWK-2XOI1X0-7L</license>
                                <chart_data>
                                    <row>
                                        {$Days}
                                    </row>
                                    <row>
                                        {$UniqueVisits}
                                    </row>

                                    <row>
                                        {$PageViews}
                                    </row>
                                    <row>
                                        {$PageVisits}
                                    </row>
                                </chart_data>
                             
                                                <!-- the child chart (top, scrolling chart) -->
                                <axis_category shadow='low' skip='11' size='10' alpha='80' />
                                <axis_value color='ffffff' shadow='low' show_min='false' />

                                <chart_border top_thickness='1' bottom_thickness='1' left_thickness='1' right_thickness='1' />
                               
                                <chart_grid_h alpha='7' thickness='10' />
                                <chart_grid_v alpha='7' thickness='1' />
                                <chart_guide vertical='true' horizontal='true' thickness='1' color='0' alpha='75' type='dotted' snap_h='true' snap_v='true' radius='3' fill_alpha='75' fill_color='FF4400' line_thickness='0' text_h_alpha='90' text_v_alpha='90' text_color='0' background_color='ff4400' size='10' />
                                <chart_pref line_thickness='2' point_shape='none' fill_shape='false' />
                                <chart_rect bevel='bg' shadow='high' height='100' positive_alpha='75' />
                                <chart_type>Line</chart_type>

                                <filter>

                                    <shadow id='high' distance='5' angle='45' alpha='35' blurX='10' blurY='10' />
                                    <shadow id='low' distance='2' angle='45' alpha='35' blurX='5' blurY='5' />
                                    <shadow id='data' distance='1' angle='45' alpha='15' blurX='2' blurY='2' />
                                    <bevel id='bg' angle='45' blurX='50' blurY='50' distance='10' highlightAlpha='10' highlightColor='ff88ff' shadowAlpha='20' inner='true' />
                                </filter>
                                
                                <scroll scroll_detail='100' transition='dissolve' delay='1' x='88' y='180' width='500' height='50' url_button_1_idle='default' url_button_2_idle='default' url_slider_body='/Themes/BevoMedia/images/charts_library/black.swf' url_slider_handle_1='/Themes/BevoMedia/images/charts_library/preview_handle_1.swf' url_slider_handle_2='/Themes/BevoMedia/images/charts_library/preview_handle_2.swf' button_length='0' slider_handle_length='22' start='400' span='40' reverse_handle='true' drag='true' external_control='false' />

                                <legend shadow='data' y='20' bullet='circle' fill_alpha='0' />
                                
                                <series_color>

                                    <color>000080</color>
                                    <color>0000FF</color>
                                    <color>8080FF</color>
                                </series_color>



                            </chart>

                            ";
	}
}

if (@$_GET ['XML'] == "getMonthlyData") {
	ob_clean ();
	ob_end_clean ();
	echo getMonthlyData ();
	die ();
} else if (@$_GET ['XML'] == "getMonthlyDataSubChart") {
	ob_clean ();
	ob_end_clean ();
	echo getMonthlyData ( true, true );
	die ();
}

//*************************************************************************************************


//*************************************************************************************************


global $userId;
$userId = $this->User->id;
//*************************************************************************************************
require_once (PATH . "Analytics.CURL.class.php");
//$obj = new CURL ( 'test' ); ################################################################################################ RT temp commented out or blank page

//*************************************************************************************************


//************************************************************************************************
$is_registerd = false;
$res = LegacyAbstraction::executeQuery( "SELECT * FROM bevomedia_user_aff_network UAN WHERE user__id = '" . $userId . "' AND STATUS='" . APP_STATUS_ACCEPTED . "' " );

while ( $row = LegacyAbstraction::getRow( $res ) )
$showRightPanes = true;
LegacyAbstraction::free( $res );

if ($is_registerd) {
	$analytic_account = array ();
	//GETTING ANALYTIC ACCOUNT DETAIL OF THE USER
	$res = LegacyAbstraction::executeQuery( "SELECT UAN.LOGIN_ID,UAN.PASSWORD,N.* from bevomedia_user_aff_network UAN, bevomedia_analytic_user_profile N WHERE N.user__Id = UAN.user__Id AND UAN.STATUS = '" . APP_STATUS_ACCEPTED . "' AND UAN.NETWORK_ID =" . NETWORK_ANALYTICS_ID . " AND UAN.user__Id = '" . $userId . "' " );
	while ( $row = LegacyAbstraction::getRow( $res ) ) {
		//$row['ISUSER']	= false;
		

		$analytic_account [] = $row;
	
	}
	LegacyAbstraction::free( $res );
	//*************************************************************************************************
	

	//*************************************************************************************************
	

	$custom_time_check = mktime ( date ( 'H' ) - 4, date ( 'i' ), date ( 's' ), date ( 'm' ), date ( 'd' ), date ( 'Y' ) );
	$expiry_time = date ( 'Y-m-d H:i:s', $custom_time_check );
	if (count ( $analytic_account ) > 0) {
		
		$analytic_save_time = "";
		$analytic_last_update = "";
		$continue = true;
		$res = LegacyAbstraction::executeQuery( "SELECT SAVE_TIME,LAST_UPDATE from bevomedia_analytic_profile_detail WHERE user__Id = '" . $userId . "' AND SAVE_TIME='" . $save_time . "' and LAST_UPDATE>='" . $expiry_time . "' and REPORT_TYPE='Dashboard'" );
		while ( $row = LegacyAbstraction::getRow( $res ) ) {
			
			$continue = false;
		
		}
		LegacyAbstraction::free( $res );
		
		if ($continue or $save_time == 'custom') {
			foreach ( $analytic_account as $analytic_detail ) {
				$url = "https://www.bevomedia.com/Analytic_phpcake/?url=analytics/save_report_data/" . $analytic_detail ['LOGIN_ID'] . "/" . $analytic_detail ['PASSWORD'] . "/" . $analytic_detail ['PROFILE_ID'] . "/" . $analytic_from . "/" . $analytic_to . "/" . $userId . "/" . $save_time . "/Dashboard/";
				
				$rest = $obj->get ( $url );
			
			}
		}
	}
	
	//***********************************************************************************************************************
	$keyword_continue = true;
	$res = LegacyAbstraction::executeQuery( "SELECT SAVE_TIME,LAST_UPDATE from bevomedia_analytic_profile_detail WHERE user__Id = '" . $userId . "' AND SAVE_TIME='" . $save_time . "' and LAST_UPDATE<='" . $expiry_time . "' and REPORT_TYPE='Keywords'" );
	while ( $row = LegacyAbstraction::getRow( $res ) ) {
		
		$keyword_continue = false;
	
	}
	LegacyAbstraction::free( $res );
	
	if ($keyword_continue or $save_time == 'custom') {
		foreach ( $analytic_account as $analytic_detail ) {
			$url = "https://www.bevomedia.com/Analytic_phpcake/?url=analytics/save_keyword_report/" . $analytic_detail ['LOGIN_ID'] . "/" . $analytic_detail ['PASSWORD'] . "/" . $analytic_detail ['PROFILE_ID'] . "/" . $analytic_from . "/" . $analytic_to . "/" . $userId . "/" . $save_time . "/Keywords/";
			
			$rest = $obj->get ( $url );
		}
	}
	
//************************************************************************************************************************


} //end if registerd


//*************************************************************************************************
//*************************************************************************************************
?>

	
	<?=@$info?>
	
<?php /* ##################################################### OUTPUT ############### */ ?>

	<div id="pagemenu">
		<ul>
			<li><a class="active" href="/BevoMedia/Analytics/AnalyticsDetail.html">Main<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticDemograph.html">Demographics<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticSources.html">Traffic Sources<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticContent.html">Content Analysis<span></span></a></li>
		</ul>
		<ul class="floatright">
			<li><a href="/BevoMedia/Publisher/Index.html#PPC">Add new analytics account...</a></li>
		</ul>
	</div>
	
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
	
	<?php /*<div id="pagedesc"<?php global $soap_pagedesc_status; echo $soap_pdcs = $soap_pagedesc_status ? ' class="'.$soap_pagedesc_status.'"' : ''; ?>>
		<div class="left">
			<img src="<?=SCRIPT_ROOT?>img/google_analytic_logo.jpg" alt="" />
		</div>
		<div class="right">
			<h2>Google Analytics</h2>
			<p>BeVo has created its own interface to view and analyze all of your Google analytics stats. Here we load all of the data from your Google Aalytics account and condense it into easy to use information. Simply enter your login info on the <a href="/BevoMedia/Publisher/Index.html">Setup &raquo; My Networks page</a> for Bevo to bring up all of your website statistics.</p>
		</div>
		<div class="clear"></div>
		<?php /* <a class="btn pagedesc_toggle" title="Toggle page description for this page" href="#">Toggle page description for this page</a>* / ?>
	</div> */ ?>
	

	<div style="text-align: center">

<? 
	if (Zend_Registry::get('Application/Mode') == 'SelfHosted')
	{
?>

<!-- BEGIN Chart -->
<script type="text/javascript">
//<![CDATA[
	$(function(){
		//make some charts
		$('#JQueryChartData').visualize({type: 'line'}).appendTo('#JQueryChartDisplay');
	});
//]]>
</script>

<?php
	$DateRange = $this->DefaultDateRange;
	if(isset($_GET['DateRange']))
		$DateRange = $_GET['DateRange'];
		
	$ChartXML = new ChartXMLHelper();
	
	$ChartXML->SetDateRange($DateRange);
	
	$ChartXML->LoadAnalyticsDetailStats($this->User->id);
	$Out = $ChartXML->getJQueryChartOutput('Analytics Stats');
	echo $Out;
?>
<!-- ENDOF Chart -->

<?
	} //if (Zend_Registry::get('Application/Mode') == 'SelfHosted')
?>


<!-- ifdef __SelfHosted__ -->

<?

	if (Zend_Registry::get('Application/Mode') != 'SelfHosted')
	{
?>
	<span style="margin-left: -20px;">
    <?= InsertChart ( '?XML=getMonthlyData', 650, 250, 'ffffff' ); ?>
	</span>
<?
	} //if (Zend_Registry::get('Application/Mode') != 'SelfHosted')
	
?>

<!-- endif __SelfHosted__ -->
    </div>

<form method="get" action="" name="frmRange">
	<table align="right" cellspacing="0" cellpadding="0" class="datetable">
	  <tr>
	    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>" /></td>
		<td><input class="formsubmit" type="submit" /></td>
	  </tr>
	</table>
</form>

	<?php
	
	if (true) {
		?>
		<table width="600" cellspacing="0" cellpadding="3" border="0"
	class="btable">
	<tr class="table_header_big">
		<td class="hhlb">&nbsp;</td>
		<td class="STYLE2">Accounts</td>
		<td class="STYLE2">Uniques</td>
		<td class="STYLE2">Page<br />
		Views</td>
		<td class="STYLE2">Page<br />
		Visits</td>
		<td class="STYLE2">New<br />
		Visits</td>
		<td class="STYLE2">Bounce<br />
		Rate</td>
		<td class="STYLE2">Avg Page Visits</td>
		<td class="STYLE2">Time<br />
		On<br />
		Site</td>
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

	$DomainsList = listDomains();
	foreach ( $DomainsList as $Domain ) {
		$Results = getStats ( $Domain ["id"] );
			?>
	<tr>
		<td>&nbsp;</td>
		<td class="GridRowHead" style="border-left: none;"><span><?=$Domain ["domain"]?></span></td>
		<td style="text-align: center;"><span><?=@number_format ( $Results ['total'], 0 )?></span></td>
		<td style="text-align: center;"><span><?=@number_format ( $Results ['pageViews'], 0 )?></span></td>
		<td style="text-align: center;"><span><?=@number_format ( $Results ['visits'], 0 )?></span></td>
		<td style="text-align: center;"><span><?=@number_format ( $Results ['percentNewVisits'], 2 )?>%</span></td>

		<td style="text-align: center;"><span><?=@number_format ( $Results ['bounceRate'], 2 )?>%</span></td>
		<td style="text-align: center;"><span><?=@number_format ( $Results ['averagePageVisits'], 2 )?></span></td>
		<td style="text-align: center;"><span><?=@number_format ( $Results ['averageTimeOnSite'], 2 )?></span></td>
		<td class="border4" style="border-left: none;">&nbsp;</td>
	</tr>
<?
		}

		if(!sizeof(listDomains())):
		?>
<tr>
	<td colspan='9' style='text-align:center;'>
		There are no analytics accounts installed to your Bevo account. <br>
		<a class="tbtn" href='/BevoMedia/Publisher/Index.html#PPC'>
			Click here to install an account
		</a>
	</td>
	<td class="border4" style="border-left: none;">&nbsp;</td>
</tr>
		
		<?php
		endif;
			
		?>

						<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="8">&nbsp;</td>

		<td class="hhr">&nbsp;</td>
	</tr>
</table>

<a class="tbtn floatright" href="/BevoMedia/Analytics/_CSVExportAnalyticsDetail.html?<?php echo (isset($_GET['DateRange'])?'DateRange='.$_GET['DateRange']:'')?>">Export to CSV</a>
<div class="clear"></div>

<br />

<!--<table width="600" cellspacing="1" cellpadding="3" border="0" class="GridTable">
						<tr>
							<th colspan="5" class="GridBlankRow"></th>
						</tr>
						<tr>
							
						<td class="GridSubHead"> Account</td>
						 <td class="GridSubHead"><?=$anlytic_title?> Pages Visits</td>
						  <td class="GridSubHead"> Direct Visiter</td>
						  <td class="GridSubHead"><?=$anlytic_title?> Referral Visiter</td>
                            <td class="GridSubHead">Search Visiter</td>
						</tr>
                        <tr>
							<td class="GridHead" colspan="5">&nbsp;</td>
						</tr>
<?
		$model = '';
		$totTodayRev = 0;
		$totMTDRev = 0;
		$totImpression = 0;
		$totClicks = 0;
		$totConversions = 0;
		$totCTR = 0;
		$toteCPM = 0;
		
		$showModelHead = true;
		$db_report = array ();
		foreach ( $analytic_account as $analytic_detail ) {
			
			$res = LegacyAbstraction::executeQuery( "SELECT * from bevomedia_analytic_profile_detail WHERE `user__Id` = '" . $userId . "' AND `PROFILE_ID`='" . $analytic_detail ['PROFILE_ID'] . "' and SAVE_TIME='" . $save_time . "' AND REPORT_TYPE='Dashboard'" );
			
			while ( $row = LegacyAbstraction::getRow( $res ) ) {
				//$row['ISUSER']	= false;
				$db_report = "";
				$db_report = $row;
				
				?>
						<tr>
							
							   <td class="GridRowHead"><?=$analytic_detail ['ACCOUNT_NAME']?></td>
                           <td class="GridRowHead"><?=$db_report ['PAGEVISIT']?></td>
                             <td class="GridRowHead"><?=$db_report ['DIRECTVISIT']?></td>
                            <td class="GridRowHead"><?=$db_report ['REFERRELVISIT']?></td>
							  <td class="GridRowHead"><?=$db_report ['SEARCHVISIT']?></td>
						</tr>
<?
			
			}
		
		} //eedn foreach
		

		?>
						<tr>
							<td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
						</tr>
					</table>
					
					
					<br />
					
				<table width="600" cellspacing="1" cellpadding="3" border="0" class="GridTable">
						<tr>
							<th colspan="6" class="GridBlankRow"></th>
						</tr>
						<tr>
							<td class="GridSubHead">Accounts</td>
							<td class="GridSubHead"><?=$anlytic_title?> keywords</td>
						 <td class="GridSubHead"><?=$anlytic_title?> Pages Visits</td>
						  <td class="GridSubHead"> New Visits</td>
						  <td class="GridSubHead"><?=$anlytic_title?> Ave</td>
                            <td class="GridSubHead">Time On Site</td>
						</tr>
                        <tr>
							<td class="GridHead" colspan="6">&nbsp;</td>
							
							
							
							 
						</tr>
<?
		$model = '';
		$totTodayRev = 0;
		$totMTDRev = 0;
		$totImpression = 0;
		$totClicks = 0;
		$totConversions = 0;
		$totCTR = 0;
		$toteCPM = 0;
		
		$showModelHead = true;
		$db_report = array ();
		foreach ( $analytic_account as $analytic_detail ) {
			
			$res = LegacyAbstraction::executeQuery( "SELECT * from bevomedia_analytic_profile_detail WHERE `user__Id` = '" . $userId . "' AND `PROFILE_ID`='" . $analytic_detail ['PROFILE_ID'] . "' and SAVE_TIME='" . $save_time . "' AND REPORT_TYPE='KEYWORDS'" );
			?>
		<tr>
		<td class="GridSubHead"  align="left" style="text-align:left"><b><?=$analytic_detail ['ACCOUNT_NAME']?></b></td><td colspan="5" class="GridSubHead"></td></tr>
	
	<?php
			while ( $row = LegacyAbstraction::getRow( $res ) ) {
				//$row['ISUSER']	= false;
				$db_report = "";
				$db_report = $row;
				
				?>
						<tr>
							<td class="GridSubHead">&nbsp;</td>
							   <td class="GridRowHead"><?=$db_report ['KEYWORDS']?></td>
                           <td class="GridRowHead"><?=$db_report ['PAGEVISIT']?></td>
                            <td class="GridRowHead"><?=$db_report ['NEWVISIT']?></td>
                           <td class="GridRowHead"><?=$db_report ['AVG_VISIT']?></td>
                            <td class="GridRowHead"><?=$db_report ['TIME_ON_SITE']?></td>
						</tr>
<?
			
			}
		
		} //eedn foreach
		

		?>
						<tr>
							<td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
                            <td class="GridFoot" align="right">&nbsp;</td>
                          
                         
						</tr>
	  </table>
		--> <br />



<br />
		
	<?php
	
	} else {
		
		?>
	<div class="SkyBox">
<div class="SkyBoxTopLeft">
<div class="SkyBoxTopRight">
<div class="SkyBoxBotLeft">
<div class="SkyBoxBotRight">
<table width="550" cellspacing="0" cellpadding="5" border="0">
	<tr valign="top">
		<td width="127"><img src="<?=SCRIPT_ROOT?>images/icon-mynetworks.gif"
			width="118" height="127" border=0 alt=""></td>
		<td class="main">
		<h4>Sign Up</h4>
		<p><br>
		You have not provided you Google Analytic account detail yeet.</p>
		<p>Please <a href="/BevoMedia/Publisher/Index.html"
			target="_blank">click here</a> to regier for Google Analytic
		account..</p>
		</td>
	</tr>
</table>

</div>
</div>
</div>
</div>
</div>
	<?php
	
	}
	
	?>
	

