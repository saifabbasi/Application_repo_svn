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

		global $DateFrom, $DateTo;
		require(PATH . 'inc_daterange.php');
		
		$crRange = LegacyAbstraction::$strDateRangeVal;
		$DateFrom = date('Y-m-d', strtotime(LegacyAbstraction::$strStartDateVal));
		$DateTo = date('Y-m-d', strtotime(LegacyAbstraction::$strEndDateVal));
		
		
		$crTitle = '';
          
          
//****************************** *******************************************************************
        function generateTrafficSourcesXml($DomainID, $XML = true)
        {
            global $DateFrom, $DateTo, $userId, $isSelfHosted;
            $DomainID = intval($DomainID);
            $DomainAdd = '';
                    if($DomainID == false)
			{
	            $XML = "
	            
	<chart>
		<license>GTA9I-PM7Q.O.945CWK-2XOI1X0-7L</license>
		<chart_data>
			<row>
				<null/>
				<string>Please select a site.</string>
				<string></string>
				<string></string>
			</row>
			<row>
				<null/>
				<number>30</number>
				<number>30</number>
				<number>30</number>
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
	            return $XML;
			}
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
                            ROUND((SUM(SearchEnginesRawValue)/(SUM(DirectTrafficRawValue)+SUM(SearchEnginesRawValue)))*100) AS `SearchEnginesPercentValue` ,
                            SUM(SearchEnginesRawValue) AS `SearchEnginesRawValue`,
                            ROUND((SUM(DirectTrafficRawValue)/(SUM(DirectTrafficRawValue)+SUM(SearchEnginesRawValue)))*100) AS `DirectTrafficPercentValue` ,
                            SUM(DirectTrafficRawValue) AS `DirectTrafficRawValue`
                        FROM
                            bevomedia_analytics_reports_trafficsources,
                            bevomedia_analytics_reports,
                            bevomedia_analytics_domains
                        WHERE
                            (bevomedia_analytics_reports_trafficsources.ReportID = bevomedia_analytics_reports.id) AND
                            (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                            (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                            {$DomainAdd}
                            (bevomedia_analytics_domains.id = bevomedia_analytics_reports.domainId) AND
                            (bevomedia_analytics_domains.user__id = $userId)
                        GROUP BY
                            (1=1)
                        ORDER BY
                            bevomedia_analytics_reports.ID DESC
                    ";
            $Row = mysql_query($Sql);

            if (mysql_num_rows($Row))
            {
                $Row = mysql_fetch_assoc($Row);
                
                if (!$XML)
                {
                    return $Row;
                }
            } else
            {
                if ($XML == false) return array();
            }
            
            if ( ($DomainID==0) || ($Row['DirectTrafficRawValue']=="") )
            {
                $Row = array();
                $Row[DirectTrafficRawValue] = 1;
                $Row[SearchEnginesRawValue] = 0;
            }
			

         
            $XML = "
            
<chart>
	<license>GTA9I-PM7Q.O.945CWK-2XOI1X0-7L</license>
	<chart_data>
		<row>
			<null/>
			<string>Direct Traffic</string>
			<string>Search Engines</string>
		</row>
		<row>
			<null/>
			<number>{$Row['DirectTrafficRawValue']}</number>
			<number>{$Row['SearchEnginesRawValue']}</number>
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
        
        if (@$_GET['XML']=="generateTrafficSourcesXml")
        {
            ob_clean();
            ob_end_clean();
            if(!isset($_GET['DomainID']) || (isset($_GET['DomainID']) && $_GET['DomainID'] == ''))
            {
            	$_GET['DomainID'] = false;
            }
            echo generateTrafficSourcesXml($_GET["DomainID"]);
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
?>


<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/welcome.js.php?r=<?=$r?>"></script>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Analytics/AnalyticsDetail.html">Main<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticDemograph.html">Demographics<span></span></a></li>
			<li><a class="active" href="/BevoMedia/Analytics/AnalyticSources.html">Traffic Sources<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticContent.html">Content Analysis<span></span></a></li>
		</ul>
		<ul class="floatright">
			<li><a href="/BevoMedia/Publisher/Index.html#PPC">Add new analytics account...</a></li>
		</ul>
	</div>
	
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div class="pagecontent analytics_page">	
    <center>
    
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
	    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>"/></td>
		<td><input class="formsubmit" type="submit" /></td>
	  </tr>
	</table>
</form>

<div class="box">
Domain:
<select class="formselect" name="Domain" onchange="window.location = 'AnalyticSources.html?DomainID='+this.value; ">
    <option value="">Please select a domain</option>
<?
    foreach (listDomains() as $Domain)
    {
        $Selected = "";
        if ($_GET["DomainID"]==$Domain["ID"]) $Selected = "selected";
?>
    <option value="<?=$Domain["id"]?>" <?=$Selected?>><?=$Domain["domain"]?></option>
<?
    }
?>
    <option value="-1" <?=((@$_GET["DomainID"]==-1)?"selected":"")?>>Combined</option>
</select>
</div>


<!-- BEGIN Chart -->
<?php

	$DateRange = $this->DefaultDateRange;
	if(isset($_GET['DateRange']))
		$DateRange = $_GET['DateRange'];

	$ChartXML = new ChartXMLHelper();
	
	$ChartXML->SetDateRange(date('m/j/Y', strtotime($DateFrom)) . '-'. date('m/j/Y', strtotime($DateTo)));
	
	if(!isset($_GET['DomainID']))
		$_GET['DomainID'] = '';
		
	$ChartXML->LoadAnalyticsTrafficSourcesStats($this->User->id, $_GET['DomainID']);
	$Out = $ChartXML->getJQueryChartOutput('Country Stats');
	if($isSelfHosted == '1')
	{
		// DONT GENERATE CHART
	}else{
		echo $Out;
	}
?>

<script type="text/javascript">
	$(function(){
		//make some charts
		$('#JQueryChartData').visualize({type: 'pie'}).appendTo('#JQueryChartDisplay');
	});
</script>
<!-- ENDOF Chart -->
    
    
    
<?
    $TrafficData = @generateTrafficSourcesXml(@$_GET["DomainID"], false);
?>
    
<table border="0px" cellspacing="0" class="btable" style="width: 400px">
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td align="center">#</td>
		<td align="center">Traffic sources</td>
		<td align="center">%</td>
		<td class="hhr">&nbsp;</td>
	</tr>
<?
    if (@$_GET["DomainID"]!="")
    {
?>
	<tr>
		<td>&nbsp;</td>
		<td style="border-left: none;" align="center"><strong>1.</strong></td>
		<td align="center"><strong>Direct Traffic</strong></td>
		<td align="center"><strong><?=@number_format($TrafficData["DirectTrafficPercentValue"], 2)?>% (<?=@number_format($TrafficData["DirectTrafficRawValue"], 2)?>)</strong></td>
		<td style="border-left: none;" class="border4">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="border-left: none;" align="center"><strong>2.</strong></td>
		<td align="center"><strong>Search Engines</strong></td>
		<td align="center"><strong><?=@number_format($TrafficData["SearchEnginesPercentValue"], 2)?>% (<?=@number_format($TrafficData["SearchEnginesRawValue"], 2)?>)</strong></td>
		<td class="border4" style="border-left: none;">&nbsp;</td>
	</tr>
<?
    } else
    {
?>
	<tr>
		<td>&nbsp;</td>
		<td style="border-left: none;" align="center" colspan="3">Please select a site. If you would like to add a site please <a href="/BevoMedia/Publisher/Index.html?Open=Analytics">Click Here</a>.</td>
		<td class="border4" style="border-left: none;">&nbsp;</td>
	</tr>
<?
    }
?>
	<tr class="table_footer">
		<td class="hhl">&nbsp;</td>
		<td colspan="3">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	</tr>
</table>

<br />


 </center>
</div><!--close pagecontent-->
