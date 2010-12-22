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
        $isAnalyticPage    = true;
		
//*************************************************************************************************


    function getStats($DomainID)
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
                        (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                        (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                        {$DomainAdd}
                        (bevomedia_analytics_domains.id = bevomedia_analytics_reports.domainId) AND
                        (bevomedia_analytics_domains.user__id = $userId)
                    ORDER BY
                        bevomedia_analytics_reports.id DESC
                ";
        $Row = mysql_query($Sql);
            
        if (mysql_num_rows($Row))
        {
            $Row = mysql_fetch_assoc($Row);
            return $Row;
        }
        
        return array();
    }
    
    function getContentOverview($DomainID)
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
                        SUM(pageViews) as `PageViews`
                    FROM
                        bevomedia_analytics_reports_siteusage,
                        bevomedia_analytics_reports,
                        bevomedia_analytics_domains
                    WHERE
                        (bevomedia_analytics_reports_siteusage.reportId = bevomedia_analytics_reports.id) AND
                        (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                        (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                        {$DomainAdd}
                        (bevomedia_analytics_domains.id = bevomedia_analytics_reports.domainId) AND
                        (bevomedia_analytics_domains.user__id = $userId)
                ";
        $Rows = mysql_query($Sql);
        $Row = mysql_fetch_assoc($Rows);
        $TotalPageViews = floatval($Row["PageViews"]);
        if ($TotalPageViews==0) $TotalPageViews = 1;
        
        $Sql = "SELECT
                        SUM(pageViews) AS `Pageviews`,
                        ROUND(SUM(pageViews)/{$TotalPageViews}*100) AS `PercentPageviews`,
                        page AS `Page`
                    FROM
                        bevomedia_analytics_reports_contentoverview,
                        bevomedia_analytics_reports,
                        bevomedia_analytics_domains
                    WHERE
                        (bevomedia_analytics_reports_contentoverview.reportId = bevomedia_analytics_reports.id)  AND
                        (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                        (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
                        {$DomainAdd}
                        (bevomedia_analytics_domains.id = bevomedia_analytics_reports.domainId) AND
                        (bevomedia_analytics_domains.user__id = $userId)
                    GROUP BY
                        page
                    ORDER BY
                        percentPageViews DESC
                ";
                        
        $Rows = mysql_query($Sql);
        $ResultArr = array();
        
        if (mysql_num_rows($Rows))
        {
            while ($Row = mysql_fetch_assoc($Rows))
                $ResultArr[] = $Row;
        }
        return $ResultArr;
    }


//*************************************************************************************************
		//$akeywords		= doCleanInput($_GET['akeywords']);
		/*
		$akeywords = $_GET['akeywords'];
		$subSql			= " ORDER BY EVENT_DATE DESC LIMIT 5";
		if ( $akeywords != "" )
			$subSql		= " AND HEADLINE LIKE '%".handleSingleQuote($akeywords)."%' ORDER BY HEADLINE";

		$arrArticles	= array();
		$res = LegacyAbstraction::executeQuery("SELECT * FROM ".PREFIX."ARTICLE WHERE ISVALID = 'Y' AND APPROVED = 'Y' ".$subSql);
		while ( $row = LegacyAbstraction::getRow($res) )
			$arrArticles[] = $row;
		LegacyAbstraction::free($res);
		*/

//*************************************************************************************************

		$isAnalyticPage	= true;
		$showRightPanes		= false;

//*************************************************************************************************

//*************************************************************************************************


		
		$analytic_from= $DateFrom;
		$analytic_to= $DateTo;

		$crTitle		= '';

//*************************************************************************************************
		$sub	= "";
		$subAN	= "";
		$save_time=$crRange;

		if ( $crRange == 'yesterday' )
		{
		
			$crTitle		= "Yesterday's";
			$anlytic_title	="Yesterday's";
			
			
		$custom_time=mktime (0,0,0,date('m'),date('d')-2,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$custom_time=mktime (0,0,0,date('m'),date('d')-1,date('Y'));
		$analytic_to=date('Y-m-d',$custom_time);
		}
		
		elseif ( $crRange == 'thisweek' )
		{
			
		$crTitle		= "This week's";
		$anlytic_title	="This week's";
		$custom_time=mktime (0,0,0,date('m'),-date('w'),date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
		}
		
		elseif ( $crRange == 'thismonth' )
		{
			
			$crTitle		= "This months's";
			$anlytic_title	="This months's";
		$custom_time=mktime (0,0,0,date('m'),1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
		}
		
		elseif ( $crRange == 'thisyear' )
		{
			
			$crTitle		= "This year's";
			$anlytic_title	="year's";
		$custom_time=mktime (0,0,0,1,1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
		}
		
		elseif ( $crRange == 'custom' )
		{
		
			
		$analytic_from=$crStartDate;
		$analytic_to=$crEndDate;
			
			
		}
		
		else
		{
			
			$crTitle		= "Today's";
			$anlytic_title	="Today's";
			
		$custom_time=mktime (0,0,0,date('m'),date('d')-1,date('Y'));
		$analytic_from=date('Y-m-d',$custom_time);
		$analytic_to=date('Y-m-d');
			
			
		}

//*************************************************************************************************

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

//*************************************************************************************************

	

//*************************************************************************************************

//*************************************************************************************************

		

//*************************************************************************************************
//require_once("analytic_fetch_data.php");
//$obj=new CURL('test');

//*************************************************************************************************

	

//*************************************************************************************************
?>

<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/welcome.js.php?r=<?=$r?>"></script>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Analytics/AnalyticsDetail.html">Main<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticDemograph.html">Demographics<span></span></a></li>
			<li><a href="/BevoMedia/Analytics/AnalyticSources.html">Traffic Sources<span></span></a></li>
			<li><a class="active" href="/BevoMedia/Analytics/AnalyticContent.html">Content Analysis<span></span></a></li>
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
    <?php
     
    if(true)
    {
    ?>

    
    
<form method="get" name="frmRange">
<input type='hidden' name='DomainID' value='<?php echo @$_GET['DomainID']?>'/>
<p align="right">
<table align="right" cellspacing="0" cellpadding="0" class="datetable">
  <tr>
    <td><input class="formtxt" type="text" name="DateRange" id="datepicker" value="<?php echo LegacyAbstraction::$strDateRangeVal; ?>" /></td>
	<td><input class="formsubmit" type="submit" /></td>
  </tr>
</table>
</p>
</form>

<div class="box">
	Domain:
	<select class="formselect" name="Domain" onchange="window.location = 'AnalyticContent.html?DomainID='+this.value; ">
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
</div>

<br /><br />
    
    
<?
if(isset($_GET['DomainID']))
	$Results = getStats($_GET["DomainID"]);
?>
    
        <table  class="btable small" cellspacing="0">
            <tr class="table_header_big tinyhead">
                <td class="hhlb">&nbsp;</td>
                <td>Uniques</td>
                <td>Page Views</td>
                <td>Page Visits</td>
                <td>New Visits</td>
                <td>Bounce Rate</td>
                <td>Avg.</td>
                <td>Time On Site</td>
                <td class="hhrb">&nbsp;</td>
            </tr>
<?

    if (@$_GET["DomainID"]!="")
    {
?>
            <tr>
                <td>&nbsp;</td>
                <td style="border-left: none;"><?=@number_format($Results['total'], 2)?></td>
                <td><?=@number_format($Results['pageViews'], 2)?></td>
                <td><?=@number_format($Results['visits'], 2)?></td>
                <td><?=@number_format($Results['percentNewVisits'], 2)?></td>
                <td><?=@number_format($Results['bounceRate'], 2)?></td>
                <td><?=@number_format($Results['averagePageVisits'], 2)?></td>
                <td><?=@number_format($Results['averageTimeOnSite'], 2)?></td>
                <td style="border-left: none;">&nbsp;</td>
            </tr>
<?
    } else
    {
?>
            <tr>
                <td>&nbsp;</td>
                <td style="border-left: none; text-align: center;" colspan="7">Please select a site. If you would like to add a site please <a href="/BevoMedia/Publisher/Index.html?Open=Analytics">Click Here</a>.</td>
                <td style="border-left: none;">&nbsp;</td>
            </tr>
<?
    }
?>
            <tr class="table_footer">
                <td class="hhl" align="right">&nbsp;</td>
                <td style="border: none;" colspan="7">&nbsp;</td>
                <td class="hhr" align="right">&nbsp;</td>
            </tr>
        </table>
        
        
<?
    $Results = getContentOverview(@$_GET["DomainID"]);
?>
        <table  class="btable small" cellspacing="0">
            <tr class="table_header_big tinyhead">
                <td class="hhlb">&nbsp;</td>
                <td>Pages</td>
                <td>Pageviews</td>
                <td>% Pageviews</td>
                <td class="hhrb">&nbsp;</td>
            </tr>
<?

	function chopPage($s)
	{
		if(strlen($s) <= 20)
			return $s;
			
		$output = '';
		while(strlen($s) > 20)
		{
			$output .= substr($s, 0, 20) . ' ';
			$s = substr($s, 20);
		}
		return $output;
	}

    if (@$_GET["DomainID"]!="")
    {
        if (count($Results)>0)
        {
            foreach ($Results as $Page)
            {
?>
            <tr>
                <td>&nbsp;</td>
                <td style="border-left: none; "><div style="width:400px; overflow:hidden; word-wrap:break-word;"><?=($Page['Page'])?></div></td>
                <td><?=$Page['Pageviews']?></td>
                <td><?=$Page['PercentPageviews']?></td>
                <td style="border-left: none;">&nbsp;</td>
            </tr>
<?
            }
        } else
        {
?>
            <tr>
                <td>&nbsp;</td>
                <td style="border-left: none; text-align: center;" colspan="3">No data.</td>
                <td style="border-left: none;">&nbsp;</td>
            </tr>
<?
        }
    } else
    {
?>
            <tr>
                <td>&nbsp;</td>
                <td style="border-left: none; text-align: center;" colspan="3">Please select a site. If you would like to add a site please <a href="/BevoMedia/Publisher/Index.html?Open=Analytics">Click Here</a>.</td>
                <td style="border-left: none;">&nbsp;</td>
            </tr>
<?
    }//count
?>

            <tr class="table_footer">
                <td class="hhl" align="right">&nbsp;</td>
                <td style="border: none;" colspan="3">&nbsp;</td>
                <td class="hhr" align="right">&nbsp;</td>
            </tr>
        </table>
        
<?
    }
?>
<p></p><br />


 </center>
</div><!--close pagecontent-->
