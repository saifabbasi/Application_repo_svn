<?php

/**
 * Creates and manages objects of the Accounts_Analytics table.
 */

/**
 * Creates and manages objects of the Accounts_Analytics table.
 * 
 * Creates and manages objects of the Accounts_Analytics table.
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */

Class Accounts_Analytics Extends Accounts_Abstract {
	
	/**
	 * @var String $_table_name
	 */
		
	protected $_table_name = 'bevomedia_accounts_analytics';
	

	/**
	 * Return if account is recognized on the API server.
	 * If account is recognized, updates the table with Verified equal to '1'.
	 * If account is not recognized, updates the table with Verified equal to '0'.
	 *
	 * @return boolean
	 */
	Public Function VerifyAccountAPI()
	{
		require_once(PATH . 'analytics_api/gapi.class.php');
		$API = new gapi($this->username, $this->password);
		
		if(isset($API->AuthFailure))
		{
			$this->Update(array("verified"=>0, "id"=>$this->id));
			return false;
		}else{
			$this->Update(array("verified"=>1, "id"=>$this->id));
			return true;
		}
	}
	
	Public Function GetMonthlyStats($UserID, $StartDate, $EndDate)
	{
		$Sql = "SELECT
                   SUM(Visits) AS `Visits`,
                   SUM(PageViews) AS `PageViews`,
                   SUM(Total) AS `Total`,
                   bevomedia_analytics_reports.DateFrom as `Date`
               FROM
                   bevomedia_analytics_reports_siteusage,
                   bevomedia_analytics_reports,
                   bevomedia_analytics_reports_visitors_overview,
                   bevomedia_analytics_domains
               WHERE
                   (bevomedia_analytics_reports_siteusage.ReportID = bevomedia_analytics_reports.ID) AND
                   (bevomedia_analytics_reports_visitors_overview.ReportID = bevomedia_analytics_reports.ID) AND
                   (bevomedia_analytics_reports.DateFrom BETWEEN DATE('$StartDate') AND DATE('$EndDate') ) AND
                   (bevomedia_analytics_reports.DateTo BETWEEN DATE('$StartDate') AND DATE('$EndDate') ) AND
                   (bevomedia_analytics_domains.ID = bevomedia_analytics_reports.DomainID) AND
                   (bevomedia_analytics_domains.user__id = $UserID)
               GROUP BY
                   bevomedia_analytics_reports.DateFrom
               ORDER BY
                   bevomedia_analytics_reports.DateFrom ASC";
                   
		$Output = $this->_db->fetchRow($Sql);
		if(!$Output)
			return $this->GetEmptyStats();

		return $Output;
	}
	
	Public Function GetDemographicStats($UserID, $DomainID, $StartDate, $EndDate)
	{
		$DomainAdd = '';
		$DomainID = intval($DomainID);
		
		if ( ($DomainID!=-1) && ($DomainID!=0) )
		{
		    $DomainAdd = " (bevomedia_analytics_reports.domainId = $DomainID) AND ";
		}
		
		if ($DomainID==0)
		{
		    $StartDate = date("Y-m-d", time()+86400);
		    $EndDate = date("Y-m-d", time()+86400);
		}
         
		$Sql = "SELECT
				    name AS `Name`,
				    SUM( value ) AS  `Value` 
				FROM
				    bevomedia_analytics_reports_countries,
				    bevomedia_analytics_reports,
				    bevomedia_analytics_domains
				WHERE
				    (bevomedia_analytics_reports_countries.reportId = bevomedia_analytics_reports.id) AND
				    (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$StartDate') AND DATE('$EndDate') ) AND
				    (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$StartDate') AND DATE('$EndDate') ) AND
				    {$DomainAdd}
				    (bevomedia_analytics_domains.id = bevomedia_analytics_reports.domainId) AND
				    (bevomedia_analytics_domains.user__id = $UserID)
				GROUP BY 
				    Name
				ORDER BY
				    SUM( Value ) DESC
            ";
		$Output = $this->_db->fetchAll($Sql);
		return $Output;
	}
	
	Public Function GetTrafficSourcesStats($UserID, $DomainID, $StartDate, $EndDate)
	{
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
		                ROUND((SUM(SearchEnginesRawValue)/(SUM(DirectTrafficRawValue)+SUM(SearchEnginesRawValue)))*100) AS `SearchEnginesPercentValue` ,
		                SUM(SearchEnginesRawValue) AS `SearchEnginesRawValue`,
		                DirectTrafficPercentValue,
		                ROUND((SUM(DirectTrafficRawValue)/(SUM(DirectTrafficRawValue)+SUM(SearchEnginesRawValue)))*100) AS `DirectTrafficPercentValue` ,
		                SUM(DirectTrafficRawValue) AS `DirectTrafficRawValue`
		            FROM
		                bevomedia_analytics_reports_trafficsources,
		                bevomedia_analytics_reports,
		                bevomedia_analytics_domains
		            WHERE
		                (bevomedia_analytics_reports_trafficsources.reportId = bevomedia_analytics_reports.id) AND
		                (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$StartDate') AND DATE('$EndDate') ) AND
		                (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$StartDate') AND DATE('$EndDate') ) AND
		                {$DomainAdd}
		                (bevomedia_analytics_domains.ID = bevomedia_analytics_reports.domainId) AND
		                (bevomedia_analytics_domains.user__id = $UserID)
		            GROUP BY 
		                (1=1)
		            ORDER BY
		                bevomedia_analytics_reports.id DESC
		        ";
		$Row = $this->_db->fetchRow($Sql);
		
		if($Row)
		{
			return $Row;
		}

		return $this->GetEmptyTrafficSourceStats();
	}
	
	Public Function GetEmptyTrafficSourceStats()
	{ 
		$Row = new stdClass();
	    $Row->SearchEnginesPercentValue = 1;
	    $Row->SearchEnginesRawValue = 1;
	    $Row->DirectTrafficPercentValue = 1;
	    $Row->DirectTrafficRawValue = 1;
	    return $Row;
	}
	
	Public Function GetNewVisitorStats($UserID, $DomainID, $StartDate, $EndDate)
	{
		$DomainID = intval($DomainID);
		$DomainAdd = '';
		
		if ( ($DomainID!=-1) && ($DomainID!=0) )
		{
		    $DomainAdd = " (bevomedia_analytics_reports.domainId = $DomainID) AND ";
		}
		
		if ($DomainID==0)
		{
		    $StartDate = date("Y-m-d", time()+86400);
		    $EndDate = date("Y-m-d", time()+86400);
		}
		
		$Sql = "SELECT
		                ROUND(SUM(ROUND(visits*percentNewVisits/100))/SUM(visits)*100) as `PercentNewVisits`,
		                SUM(visits) as `Visits`
		            FROM
		                bevomedia_analytics_reports_siteusage,
		                bevomedia_analytics_reports,
		                bevomedia_analytics_domains
		            WHERE
		                (bevomedia_analytics_reports_siteusage.reportId = bevomedia_analytics_reports.id) AND
		                (bevomedia_analytics_reports.dateFrom  BETWEEN DATE('$StartDate') AND DATE('$EndDate') ) AND
		                (bevomedia_analytics_reports.dateTo  BETWEEN DATE('$StartDate') AND DATE('$EndDate') ) AND
		                {$DomainAdd}
		                (bevomedia_analytics_domains.id = bevomedia_analytics_reports.DomainID) AND
		                (bevomedia_analytics_domains.user__id = $UserID)
		            ORDER BY
		                bevomedia_analytics_reports.id DESC
		        ";  
		                
		$Row = $this->_db->fetchRow($Sql);
		
		if ($Row)
		{            
		    $NewVisitorsPercent = str_replace('%', '', $Row->PercentNewVisits);
		    $NewVisitorsPercent /= 100;
		    $NewVisitors = $Row->Visits*$NewVisitorsPercent;
		    $ReturningVisitors = $Row->Visits-$NewVisitors;
		}
		
		return array("NewVisitors" => round($NewVisitors), "ReturningVisitors" => round($ReturningVisitors) );
		
	}
	
	Public Function GetEmptyStats($Pos = false)
	{
		$Output = new stdClass();
		
		$Output->Total = 0;
		$Output->Visits = 0;
		$Output->PageViews = 0;
		
		return $Output;
	}
}
?>