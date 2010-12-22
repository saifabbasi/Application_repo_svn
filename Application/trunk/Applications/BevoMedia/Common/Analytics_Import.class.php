<?php 

/**
 * Class which uses the Google Analytics API to retrieve statistical data about a website.
 */

function debug_analytics_import($str)
{
	echo $str . "\n";
}

/**
 * Class which uses the Google Analytics API to retrieve statistical data about a website.
 * 
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
Class Analytics_Import
{
	/**
	 * @var Integer $User_ID
	 */
	Private $User_ID;
	
	/**
	 * Constructor
	 *
	 * @param Boolean $Console
	 * @param Integer $User_ID
	 */
	Public Function __construct($Console = false, $User_ID = false)
	{
		if($Console === false)
			$this->Init();
			
		$this->User_ID = $User_ID;
			
	}
	
	/**
	 * Begin the import process for this Analytics account.
	 *
	 * @return Boolean
	 */
	Public Function ProcessImport()
	{
		$Creds = $this->GetGACreds();
		$Date = date('Y-m-d', strtotime('yesterday'));
		debug_analytics_import('>>BEGIN ANALYTICS IMPORT ' . $Date);
		foreach($Creds as $Cred)
		{
			$Email = $Cred['Username'];
			$Pass = $Cred['Password'];
			
			debug_analytics_import('ATTEMPT LOGIN: ' . $Email . ' / ' . $Pass);
			try {
				$GA = new gapi($Email, $Pass);
				if(isset($GA->AuthFailure) && $GA->AuthFailure == '1')
				{
					debug_analytics_import('AUTH FAILED!');
					continue;
				}
			} catch(Exception $e)
			{
				print $e->getMessage();
				continue;
			}
			
			debug_analytics_import('REQUESTING ACCOUNT DATA');
			$GA->requestAccountData();
			$Domains = array();
			foreach($GA->getResults() as $Domain)
			{
				$Domains[] = array($Domain, $Domain->getProfileId());	
			}
			
			foreach($Domains as $Domain)
			{
				$DomainTitle = $Domain[0];
				$ProfileID = $Domain[1];
			
				debug_analytics_import('DOMAIN TITLE: ' . $DomainTitle);
				debug_analytics_import('PROFILE ID: ' . $ProfileID);
				
				if(false == ($DomainID = $this->GetDomainID($DomainTitle)))
				{
					$DomainID = $this->InsertDomain($DomainTitle);
				}
				if(false == ($ReportID = $this->GetReportID($DomainID, $Date, $Date)))
				{
					$ReportID = $this->InsertReport($DomainID, $Date, $Date);
				}
				
				
				$GA->requestReportData($ProfileID,array('date'),array('pageviews','newVisits','visits','pageviews','bounces','timeonsite'), null, null, $Date, $Date);
				$this->InsertSiteUsage($ReportID, $GA->getResults());
				
				$GA->requestReportData($ProfileID,array('country'),array('visits'), null, null, $Date, $Date);
				$this->InsertCountries($ReportID, $GA->getResults());
				
				$GA->requestReportData($ProfileID,array('medium'),array('visits'), null, null, $Date, $Date);
				$this->InsertTrafficSources($ReportID, $GA->getResults());
				
				$GA->requestReportData($ProfileID,array('landingPagePath'),array('pageviews'), null, null, $Date, $Date);
				$this->InsertContentOverview($ReportID, $GA->getResults());
			}
		}
		
		return true;
	}
	
	Private Function InsertContentOverviewPage($ReportID, $Page, $Views, $TotalViews)
	{
		$PercentPageViews = 0;
		if($TotalViews > 0)
			$PercentPageViews = $Views / $TotalViews;
			
		$PercentPageViews *= 100;
		
		$Sql = "INSERT INTO bevomedia_bevomedia_analytics_reports_contentoverview (ReportID, Pageviews, PercentPageviews, Page)
														VALUES('$ReportID', '$Views', '$PercentPageViews', '$Page')";
		mysql_query($Sql);
	}
	
	Private Function InsertContentOverview($ReportID, $Results)
	{
		$Sql = "SELECT ID FROM bevomedia_analytics_reports_contentoverview WHERE ReportID = $ReportID";
		$Query = mysql_query($Sql, ABSDB);
		if(mysql_num_rows($Query) > 0)
		{
			$Sql = "DELETE FROM bevomedia_analytics_reports_contentoverview WHERE ReportID = $ReportID";
			mysql_query($Sql);
		}
		
		$Total = $this->GetTotalViews($ReportID);
		foreach($Results as $Result)
		{
			$Page = $Result->getLandingPagePath();
			$Metrics = $Result->getMetrics();
			$Visits = $Metrics['pageviews'];
			$this->InsertContentOverviewPage($ReportID, $Page, $Visits, $Total);	
		}
	}
	
	Private Function InsertTrafficSources($ReportID, $Results)
	{
		$Direct = 0;
		$SearchEngine = 0;
		foreach($Results as $Result)
		{
			$Dimension = $Result->getMedium();
			$Metrics = $Result->getMetrics();
			if($Dimension == '(none)')
				$Direct += $Metrics['visits'];
			if($Dimension == 'organic')
				$SearchEngine += $Metrics['visits'];
		}
	
		$Sql = "SELECT ID FROM bevomedia_analytics_reports_trafficsources WHERE ReportID = $ReportID";
		$Query = mysql_query($Sql, ABSDB);
		if(mysql_num_rows($Query) > 0)
		{
			$Sql = "DELETE FROM bevomedia_analytics_reports_trafficsources WHERE ReportID = $ReportID";
			mysql_query($Sql);
		}
		
		$Total = $Direct + $SearchEngine;
		
		$SearchEnginePct = 0;
		$DirectPct = 0;
		if($Total > 0)
		{
			$SearchEnginePct = $SearchEngine / $Total;
			$DirectPct = $Direct / $Total;
		}
		$Sql = "INSERT INTO bevomedia_analytics_reports_trafficsources (ReportID, SearchEnginesPercentValue, SearchEnginesRawValue, DirectTrafficPercentValue, DirectTrafficRawValue)
													VALUES	($ReportID, '$SearchEnginePct', '$SearchEngine', '$DirectPct', '$Direct' )";
		mysql_query($Sql);
	}
	
	Private Function InsertCountry($ReportID, $Country, $Visits)
	{
		$Sql = "INSERT INTO bevomedia_analytics_reports_countries (ReportID, Name, Value) VALUES ('$ReportID', '$Country', '$Visits')";
		mysql_query($Sql);
	}
	
	Private Function InsertCountries($ReportID, $Results)
	{
		$Sql = "SELECT ID FROM bevomedia_analytics_reports_countries WHERE ReportID = $ReportID";
		$Query = mysql_query($Sql, ABSDB);
		if(mysql_num_rows($Query) > 0)
		{
			$Sql = "DELETE FROM bevomedia_analytics_reports_countries WHERE ReportID = $ReportID";
			mysql_query($Sql);
		}
		
		foreach($Results as $Result)
		{
			$Country = $Result->getCountry();
			$Visits = $Result->getMetrics();
			$Visits = $Visits['visits'];
			$this->InsertCountry($ReportID, $Country, $Visits);
		}
	}
	
	Private Function InsertSiteUsage($ReportID, $Results)
	{
		$Data = $Results[0]->getMetrics();
		$Visits = $Data['visits'];
		$PageViews = $Data['pageviews'];
		debug_analytics_import('PAGE VIEWS: ' . $PageViews);
		debug_analytics_import('PAGE VISITS: ' . $Visits);
		
		if($Visits == 0)
		{
			$BounceRate = $AveragePageVisits = $PercentNewVisits = $AverageTimeOnSite = 0;
		}else{
			$AverageTimeOnSite = gmdate("H:i:s", $Data['timeOnSite']/$Visits);
			$BounceRate = ($Data['bounces'] / $Visits) * 100;
			$AveragePageVisits = $PageViews / $Visits;
			$PercentNewVisits = ($Data['newVisits'] / $Visits) * 100;
		}
		
		$Sql = "SELECT ID FROM bevomedia_analytics_reports_siteusage WHERE ReportID = $ReportID";
		$Query = mysql_query($Sql, ABSDB);
		if(mysql_num_rows($Query) > 0)
		{
			$Sql = "DELETE FROM bevomedia_analytics_reports_siteusage WHERE ReportID = $ReportID";
			mysql_query($Sql);
		}
		
		$Sql = "SELECT ID FROM bevomedia_analytics_reports_visitors_overview WHERE ReportID = $ReportID";
		$Query = mysql_query($Sql, ABSDB);
		if(mysql_num_rows($Query) > 0)
		{
			$Sql = "DELETE FROM bevomedia_analytics_reports_visitors_overview WHERE ReportID = $ReportID";
			mysql_query($Sql);
		}
		
		$Sql = "INSERT INTO bevomedia_analytics_reports_visitors_overview (ReportID, Total) VALUES ('$ReportID', '$Visits')";
		mysql_query($Sql);
		
		$Sql = "INSERT INTO bevomedia_analytics_reports_siteusage (ReportID, Visits, AveragePageVisits, AverageTimeOnSite, PercentNewVisits, BounceRate, PageViews) 
												VALUES 	('$ReportID', '$Visits', '$AveragePageVisits', '$AverageTimeOnSite', '$PercentNewVisits', '$BounceRate', '$PageViews' )";
		mysql_query($Sql);
	}
	
	Private Function InsertReport($DomainID, $DateFrom, $DateTo)
	{
		$Sql = "INSERT INTO bevomedia_analytics_reports (DomainID, DateFrom, DateTo) VALUES ('$DomainID', '$DateFrom', '$DateTo')";
		$Query = mysql_query($Sql);
		return mysql_insert_id();
	}
	
	Private Function InsertDomain($Domain)
	{
		$Sql = "INSERT INTO bevomedia_analytics_domains (user__id, Domain) VALUES ('$this->User_ID', '$Domain')";
		$Query = mysql_query($Sql, ABSDB);
		return mysql_insert_id();
	}
		
	Private Function GetGACreds()
	{
		$Sql = "SELECT Username, Password FROM bevomedia_accounts_analytics WHERE user__id = {$this->User_ID} AND Enabled = 1 AND Deleted = 0";
		$Query = mysql_query($Sql, ABSDB);
		$Rows = array();
		while($Row = (mysql_fetch_assoc($Query)))
		{
			$Rows[] = $Row;
		}
		return $Rows;
	}
	
	/**
	 * Return report ID matching specified $DomainID within range from $DateFrom to $DateTo.
	 *
	 * @param Integer $DomainID
	 * @param String $DateFrom
	 * @param String $DateTo
	 * @return Integer
	 */
	Public Function GetReportID($DomainID, $DateFrom, $DateTo)
	{
		$Sql = "SELECT ID FROM bevomedia_analytics_reports WHERE DomainID = $DomainID AND DateFrom = '$DateFrom' AND DateTo = '$DateTo'";
		$Query = mysql_query($Sql, ABSDB);
		if(mysql_num_rows($Query) == 0)
			return false;
		$Row = mysql_fetch_assoc($Query);
		return $Row['ID'];
	}
	
	/**
	 * Return total views for matching $ReportID.
	 *
	 * @param Integer $ReportID
	 * @return Integer
	 */
	Public Function GetTotalViews($ReportID)
	{
		$Sql = "SELECT Total FROM bevomedia_analytics_reports_visitors_overview WHERE ReportID = $ReportID";
		$Query = mysql_query($Sql, ABSDB);
		if(mysql_num_rows($Query) == 0)
			return 0;
		$Row = mysql_fetch_assoc($Query);
		return $Row['Total'];
	}
	
	/**
	 * Return domain ID for matching $Domain and this $User_ID.
	 *
	 * @param String $Domain
	 * @return Integer
	 */
	Public Function GetDomainID($Domain)
	{
		$Sql = "SELECT ID FROM bevomedia_analytics_domains WHERE Domain ='$Domain' AND user__id = $this->User_ID";
		$Query = mysql_query($Sql, ABSDB);
		if(mysql_num_rows($Query) == 0)
			return false;
		$Row = mysql_fetch_assoc($Query);
		return $Row['ID'];
	}
	
	/**
	 * Init function.
	 *
	 * @todo Implement use of ABSDB
	 */
	Private Function Init()
	{
		
	}
}

?>