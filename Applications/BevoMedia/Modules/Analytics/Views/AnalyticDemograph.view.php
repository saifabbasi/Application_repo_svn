<?php
  //*************************************************************************************************
require_once(PATH . "Legacy.Abstraction.class.php");
//*************************************************************************************************
if(!isset($_GET["DomainID"]))
{
	if(isset($_SESSION['_GET_DomainID']))
	{
		$_GET['DomainID'] = $_SESSION['_GET_DomainID'];
	}
}else{
	$_SESSION['_GET_DomainID'] = $_GET['DomainID'];
}

global $userId, $isSelfHosted;
$userId = $this->User->id;
$isSelfHosted = $this->User->IsSelfHosted();

require(PATH . 'inc_daterange.php');

$crRange = LegacyAbstraction::$strDateRangeVal;
$analytic_from = date('Y-m-d', strtotime(LegacyAbstraction::$strStartDateVal));
$analytic_to = date('Y-m-d', strtotime(LegacyAbstraction::$strEndDateVal));
     
global $DateFrom, $DateTo;
$DateFrom = $analytic_from;
$DateTo = $analytic_to;
        
//****************************** *******************************************************************
        function generateNewVisitorsXml($DomainID, $XML = true)
        {
            global $DateFrom, $DateTo, $userId, $isSelfHosted;
            
            
            $DomainID = intval($DomainID);
            $DomainAdd = '';
            
            if ( ($DomainID!=-1) && ($DomainID!=0) )
            {
                $DomainAdd = " (bevomedia_analytics_reports.domainId = $DomainID) AND ";
            }
            
            if ($DomainID==0)
            {
                $DateFrom = date("Y-m-d", time()+86400);
                $DateTo = date("Y-m-d", time()+86400);
            }
            $Sql = "SELECT
                            ROUND(SUM(ROUND(Visits*PercentNewVisits/100))/SUM(Visits)*100) as `PercentNewVisits`,
                            SUM(Visits) as `Visits`
                        FROM
                            bevomedia_analytics_reports_siteusage,
                            bevomedia_analytics_reports,
                            bevomedia_analytics_domains
                        WHERE
                            (bevomedia_analytics_reports_siteusage.reportId = bevomedia_analytics_reports.id) AND
                            (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                            (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                            {$DomainAdd}
                            (bevomedia_analytics_domains.ID = bevomedia_analytics_reports.domainId) AND
                            (bevomedia_analytics_domains.user__id = $userId)
                        ORDER BY
                            bevomedia_analytics_reports.id DESC
                    ";
                            
            $Row = mysql_query($Sql);
            
            if (mysql_num_rows($Row)>0)
            {
                $Row = mysql_fetch_assoc($Row);
                $NewVisitorsPercent = str_replace('%', '', $Row['PercentNewVisits']);
                $NewVisitorsPercent /= 100;
                $NewVisitors = $Row['Visits']*$NewVisitorsPercent;
                $ReturningVisitors = $Row['Visits']-$NewVisitors;
                
                if (!$XML)
                {
                    return array("NewVisitors" => round($NewVisitors), "ReturningVisitors" => round($ReturningVisitors) );
                }
            }
            
            if ( ($DomainID==0) || (($NewVisitors==0) && ($ReturningVisitors==0))    )
            {
                $NewVisitors = 1;
            }
            
            $XML = "
            
                        <chart>
                            <license>GTA9I-PM7Q.O.945CWK-2XOI1X0-7L</license>
                            <chart_data>
                                <row>
                                    <null/>
                                    <string>New Visitor</string>
                                    <string>Returning Visitor</string>
                                </row>
                                <row>
                                    <null/>
                                    <number>{$NewVisitors}</number>
                                    <number>{$ReturningVisitors}</number>
                                </row>
                            </chart_data>

                                <chart_label shadow='low' alpha='65' size='10' position='inside' as_percentage='true' />
                            <chart_pref select='true' drag='false' rotation_x='50' />
                            <chart_rect x='85' y='50' width='300' height='180' positive_alpha='0' />
                            <chart_type>3d pie</chart_type>
                            
                           
                            <filter>
                                <shadow id='high' distance='3' angle='45' alpha='50' blurX='10' blurY='10' />
                                <shadow id='low' distance='2' angle='45' alpha='60' blurX='10' blurY='10' />
                                <bevel id='bg' angle='90' blurX='0' blurY='200' distance='50' highlightAlpha='15' shadowAlpha='15' type='inner' />
                            </filter>
                            
                            <legend shadow='low' layout='horizontal' margin='20' x='65' y='-10' width='345' height='25' fill_alpha='0' color='#000000'  />
                            
                            <context_menu full_screen='false' />
                        </chart>
            
                       ";
            return ($XML);
        }
        
        if (@$_GET['XML']=="generateNewVisitorsXml")
        {
            ob_clean();
            ob_end_clean();
            echo generateNewVisitorsXml($_GET["DomainID"]);
            die;
        }
        
        
//****************************** *******************************************************************
        function generateCountriesXml($DomainID, $XML = true)
        {
            global $DateFrom, $DateTo, $userId, $isSelfHosted;
            

            $DomainAdd = '';
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
            
            $Sql = "SELECT
                            name AS `Name`,
                            SUM( Value ) AS  `Value`
                        FROM
                            bevomedia_analytics_reports_countries,
                            bevomedia_analytics_reports,
                            bevomedia_analytics_domains
                        WHERE
                            (bevomedia_analytics_reports_countries.reportId = bevomedia_analytics_reports.id) AND
                            (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                            (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                            {$DomainAdd}
                            (bevomedia_analytics_domains.id = bevomedia_analytics_reports.domainId) AND
                            (bevomedia_analytics_domains.user__id = $userId)
                        GROUP BY
                            Name
                        ORDER BY
                            SUM( Value ) DESC
                       
                    ";
            $Rows = mysql_query($Sql);
            
            $ResultArr = array();
            if (mysql_num_rows($Rows))
            {
                while ($Row = mysql_fetch_assoc($Rows))
                {
                    $ResultArr[] = $Row;
                }
            }
            
            
            if (!$XML)
            {
                return $ResultArr;
            }
            
            if ( ($DomainID==0) || (count($ResultArr)==0) )
            {
                $ResultArr = array( array("Name" => "United States", "Value" => "1") );
            }
            
            $XML = "
                        <chart>
                            <license>GTA9I-PM7Q.O.945CWK-2XOI1X0-7L</license>
                            <chart_data>
                                <row>
                                    <null/>
                       ";

            foreach ($ResultArr as $Country)
            {
                $XML .= '<string>'.$Country['Name']."</string>\n";
            }


            $XML .= "
                                </row>
                                <row>
                                    <null/>
                        ";
                    
            foreach ($ResultArr as $Country)
            {
                $XML .= '<number>'.$Country['Value']."</number>\n";
            }
            
            $XML .= "
            
                            </row>
                        </chart_data>

                        <chart_label shadow='low' color='ffffff' alpha='95' size='10' position='inside' as_percentage='true' />
                        <chart_pref select='true' />
                        <chart_rect x='50' y='85' width='300' height='175' />
                        <chart_transition type='scale' delay='0.5' duration='0.2' order='category' />
                        <chart_type>donut</chart_type>

                        
                        <filter>
                            <shadow id='low' distance='2' angle='45' color='0' alpha='40' blurX='5' blurY='5' />
                            <shadow id='high' distance='5' angle='45' color='0' alpha='40' blurX='10' blurY='10' />
                            <shadow id='soft' distance='2' angle='45' color='0' alpha='20' blurX='5' blurY='5' />
                            <bevel id='data' angle='45' blurX='5' blurY='5' distance='3' highlightAlpha='15' shadowAlpha='25' type='inner' />
                            <bevel id='bg' angle='45' blurX='50' blurY='50' distance='10' highlightAlpha='35' shadowColor='0000ff' shadowAlpha='25' type='full' />
                            <blur id='blur1' blurX='75' blurY='75' quality='1' />
                        </filter>
                        
                        <context_menu full_screen='false' />
                        <legend transition='dissolve' x='50' width='300' bevel='low' fill_alpha='0' line_alpha='0' bullet='circle' size='12' color='#000000' alpha='100' />

                        <series_color>
                            <color>88aaff</color>
                            <color>88dd11</color>
                            <color>4e62dd</color>
                            <color>ff8811</color>
                            <color>4d4d4d</color>
                        </series_color>
                        <series_explode>
                            <number>0</number>
                            <number>0</number>
                            <number>0</number>
                            <number>25</number>
                            <number>0</number>
                        </series_explode>
                        <series transfer='true' />
                    </chart>
                    ";
            
            
            return ($XML);
        }
            
        if (@$_GET['XML']=="generateCountriesXml")
        {
            ob_clean();
            ob_end_clean();
            echo generateCountriesXml($_GET["DomainID"]);
            die;
        }
//****************************** *******************************************************************

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
        
//****************************** *******************************************************************
        $isAnalyticPage    = true;
//*************************************************************************************************
//*************************************************************************************************
        // Call template

        
//*************************************************************************************************
?>



<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/welcome.js.php?r=<?=@$r?>"></script>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Analytics/AnalyticsDetail.html">Main<span></span></a></li>
			<li><a class="active" href="/BevoMedia/Analytics/AnalyticDemograph.html">Demographics<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticSources.html">Traffic Sources<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticContent.html">Content Analysis<span></span></a></li>
		</ul>
		<ul class="floatright">
			<li><a href="/BevoMedia/Publisher/Index.html#PPC">Add new analytics account...</a></li>
		</ul>
	</div>
	
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div class="pagecontent analytics_page" style="text-align: center">	
        
<?
    if (count(listDomains())==0)
    {
?>
    <div style="font-weight: bold; text-align: center; color: #f00;">You currently do not have any analytics accounts registered to your Bevo Account. To install an account, please <a href="/BevoMedia/Publisher/Index.html?Open=Analytics">Click Here</a>.</div>
    <br />
<?
    }
?>
    
    
<form method="get" name="frmRange">
	<input type='hidden' name='DomainID' value='<?php echo @$_GET['DomainID']?>'/>
	<table align="right" cellspacing="0" cellpadding="0" class="datetable">
	  <tr>
	    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>" /></td>
		<td><input class="formsubmit" type="submit" /></td>
	  </tr>
	</table>
</form>

<div class="box">
Domain:
<select class="formselect" name="Domain" onchange="window.location = '?DomainID='+this.value; ">
    <option value="">Please select a domain</option>
<?
    foreach (listDomains() as $Domain)
    {
        $Selected = "";
        if ($_GET["DomainID"]==$Domain["id"]) $Selected = "selected";
?>
    <option value="<?=$Domain["id"]?>" <?=$Selected?>><?=$Domain["domain"]?></option>
<?
    }
?>
    <option value="-1" <?=((@$_GET["DomainID"]==-1)?"selected":"")?>>Combined</option>
</select>

</div><!--close box-->


    <?  //if (@$_GET["DateRange"]!="") $AddURL = "&DateRange=$_GET[DateRange]"; echo @InsertChart ( "AnalyticDemograph.html?XML=generateNewVisitorsXml&DomainID={$_GET['DomainID']}".$AddURL , 480, 300, 'ffffff' );?>
    

<!-- BEGIN Chart -->
<script type="text/javascript">
//<![CDATA[
	$(function(){
		//make some charts
		$('#JQueryChartData2').visualize({type: 'pie'}).appendTo('#JQueryChartDisplay2');
	});
//]]>
</script>

<?php

	$DateRange = $this->DefaultDateRange;
	if(isset($_GET['DateRange']))
		$DateRange = $_GET['DateRange'];

	$ChartXML = new ChartXMLHelper();
	
	$ChartXML->SetDateRange(date('m/j/Y', strtotime($DateFrom)) . '-'. date('m/j/Y', strtotime($DateTo)));
	
	if(!isset($_GET['DomainID']))
		$_GET['DomainID'] = '';
		
	$ChartXML->LoadAnalyticsNewVisitorStats($this->User->id, $_GET['DomainID']);
	$Out = $ChartXML->getJQueryChartOutput('Visitor Stats', 'JQueryChartData2', 'JQueryChartDisplay2');
	if($isSelfHosted == '1')
	{
		// DONT GENERATE CHART
	}else{
		echo $Out;
	}
	
?>
<!-- ENDOF Chart -->
<?
    $VisitorsArr = @generateNewVisitorsXml($_GET["DomainID"], false);
    
?>
                  
<table class="btable" cellspacing="0" style="width: 400px;" border="0" cellpadding="2">
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td align="center" style="width: 190px;">New visitors</td>
		<td align="center">Returning visitors</td>
		<td class="hhr">&nbsp;</td>
	</tr>
<?
    if (@$_GET["DomainID"]!="")
    {
?>
	<tr>
		<td>&nbsp;</td>
		<td align="center" style="border-left: none;"><?=$VisitorsArr["NewVisitors"]?></td>
		<td align="center"><?=$VisitorsArr["ReturningVisitors"]?></td>
		<td class="border4" style="border-left: none;">&nbsp;</td>
	</tr>
<?
    } else
    {
?>
	<tr>
		<td>&nbsp;</td>
		<td align="center" style="border-left: none; text-align: center" colspan="2">Please select a site. If you would like to add a site please <a href="/BevoMedia/Publisher/Index.html?Open=Analytics">Click Here</a>.</td>
		<td class="border4" style="border-left: none;">&nbsp;</td>
	</tr>
<?
    }
?>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="2">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>


<?  //if (@$_GET["DateRange"]!="") @$AddURL = "&DateRange=$_GET[DateRange]";  echo @InsertChart ( "AnalyticDemograph.html?XML=generateCountriesXml&DomainID={$_GET['DomainID']}".$AddURL , 400, 300, 'ffffff' );?>


<!-- BEGIN Chart -->
<script type="text/javascript">
//<![CDATA[
	$(function(){
		//make some charts
		$('#JQueryChartData').visualize({type: 'pie'}).appendTo('#JQueryChartDisplay');
	});
//]]>
</script>

<?php

	$DateRange = $this->DefaultDateRange;
	if(isset($_GET['DateRange']))
		$DateRange = $_GET['DateRange'];

	$ChartXML = new ChartXMLHelper();
	
	$ChartXML->SetDateRange(date('m/j/Y', strtotime($DateFrom)) . '-'. date('m/j/Y', strtotime($DateTo)));
		
	$ChartXML->LoadAnalyticsDemographStats($this->User->id, $_GET['DomainID']);
	$Out = $ChartXML->getJQueryChartOutput('Country Stats');
	if($isSelfHosted == '1')
	{
		// DONT GENERATE CHART
	}else{
		echo $Out;
	}
?>
<!-- ENDOF Chart -->

<?
    @$CountriesReport = generateCountriesXml($_GET["DomainID"], false);
?>

<table style="width: 400px;" border="0" cellspacing="0" class="btable">
<tr class="table_header">
	<td class="hhl">&nbsp;</td>
	<td align="center" colspan="2">Countries</td>
	<td class="hhr">&nbsp;</td>
</tr>
<?
    if (@$_GET["DomainID"]!="")
    {
        if (count($CountriesReport)>0)
        {
            foreach ($CountriesReport as $Country)
            {
?>
                <tr>
                    <td>&nbsp;</td>
                    <td align="center" style="border-left: none;"><strong><?=$Country['Name']?></strong></td>
                    <td class="" style="border-left: none;"><?=$Country['Value']?></td>
                    <td class="tail" style="border-left: none;">&nbsp;</td>
                </tr>
<?
            }
        } else //count
        {
?>
                <tr>
                    <td>&nbsp;</td>
                    <td align="center" style="border-left: none; text-align: center;" colspan="2">No data.</td>
                    <td class="border4" style="border-left: none;">&nbsp;</td>
                </tr>
<?
        }
    } else
    {
?>
<tr>
	<td>&nbsp;</td>
	<td align="center" style="border-left: none;" colspan="2">Please select a site. If you would like to add a site please <a href="/BevoMedia/Publisher/Index.html?Open=Analytics">Click Here</a>.</td>
    <td class="border4" style="border-left: none;">&nbsp;</td>
</tr>
<?
    }
?>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="2">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>


</div><!--close pagecontent-->


