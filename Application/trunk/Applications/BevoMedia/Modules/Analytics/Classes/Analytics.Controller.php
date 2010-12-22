<?php

	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
	
	Class AnalyticsController extends ClassComponent
	{
		Public $GUID		= NULL;
		Protected $_db		= false;
		
		Public Function __construct()
		{
			parent::GenerateGUID();
			$this->{'PageHelper'} = new PageHelper();
			$this->{'PageDesc'} = new PageDesc();
			
			if(!isset($_SESSION['User']) || !intval($_SESSION['User']['ID']))
			{
				$_SESSION['loginLocation'] = $_SERVER['REQUEST_URI'];
				header('Location: /BevoMedia/Index/');
				die;
			}
			
			$this->_db = Zend_Registry::get('Instance/DatabaseObj');
			
			$user = new User();
			$user->getInfo($_SESSION['User']['ID']);
			$this->{'User'} = $user;
			Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
		}
		
		
		Public Function _CSVExportAnalyticsDetail()
		{
			/*
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=AccountStats.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			//*/print '<pre>';
			
			
			$userId = $this->User->ID;
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
			$DateFrom = date ( "Y-m-d", strtotime ( "-1 week" ) );
			$DateTo = date ( "Y-m-d" );
			
			if(isset($_GET['DateRange']))
			{
				$D = split('-', $_GET['DateRange']);
				$DateFrom = date ( "Y-m-d", strtotime ( $D[0] ) );
				$DateTo = date ( "Y-m-d", strtotime ( $D[1] ) );
			}
			
			$Sql = "SELECT 
						analytics_domains.Domain as `Domain`,
						0 as Visits,
						0 as AveragePageVisits,
						0 as AverageTimeOnSite,
						0 as PercentNewVisits,
						0 as BounceRate,
						0 as PageViews,
						0 as `Unique`
					FROM analytics_domains WHERE analytics_domains.UserID = $userId";
			$Results = $this->_db->fetchAll($Sql);
			$Accounts = array();

			foreach($Results as $Row)
			{
				$Accounts[$Row->Domain] = $Row;
			}
			
			$Sql = "SELECT
							Domain,
                            SUM(Visits) as Visits, 
                            SUM(AveragePageVisits) as AveragePageVisits,
                            AVG(AverageTimeOnSite) as AverageTimeOnSite,
                            AVG(PercentNewVisits) as PercentNewVisits,
                            AVG(BounceRate) as BounceRate,
                            SUM(PageViews) as PageViews,
                            SUM(Total) as `Unique`
				    FROM
				        analytics_reports_siteusage,
				        analytics_reports,
				        analytics_reports_visitorsoverview,
				        analytics_domains
				    WHERE
				        (analytics_reports_siteusage.ReportID = analytics_reports.ID) AND
				        (analytics_reports_visitorsoverview.ReportID = analytics_reports.ID) AND
				        (analytics_reports.DateFrom  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
				        (analytics_reports.DateTo  BETWEEN DATE('$DateFrom') AND DATE('$DateTo') ) AND
				        (analytics_domains.ID = analytics_reports.DomainID) AND
				        (analytics_domains.UserID = $userId)
				    GROUP BY
				        Domain
				    ORDER BY
				        analytics_reports.DateFrom ASC
				";
				        print $Sql . "<BR><BR>";
			
			$Results = $this->_db->fetchAll($Sql);
						
			if (sizeOf ( $Results )) {
				foreach( $Results as $Row ) {
					foreach($Row as $Key=>$Item)
					{
						$Accounts[$Row->Domain]->{$Key} = $Item;
					}
				}
			}
			
			$this->EchoCSV($Accounts);
			die;
		}
		
		Public Function AnalyticsDetail()
		{
			$this->DefaultDateRange = $this->GetDefaultDateRange();
		}
		
		Public Function AnalyticDemograph()
		{
			$this->DefaultDateRange = $this->GetDefaultDateRange();
		}
		
		Private Function GetDefaultDateRange()
		{
			$DateRange = date('m/j/Y', strtotime('TODAY - 11 DAYS'));
			$DateRange .= '-';
			$DateRange .= date('m/j/Y', strtotime('TODAY'));
			return $DateRange;
		}
		
		Private Function EchoCSV($Arr)
		{
			$Keys = array();
			/*$First = true;
			foreach($Arr[0] as $Key=>$Value)
			{
				if($Key != 'ID')
				{
					echo ($First)?'':",";
					$First = false;
					$Keys[] = $Key;
					echo '"' . $Key . '"';
				}
			}*/
			$Keys = array('Domain', 'Visits', 'AveragePageVisits', 'AverageTimeOnSite', 'PercentNewVisits', 'BounceRate', 'PageViews', 'Unique');
		
			$First = true;
			foreach($Keys as $Key)
			{
				echo ($First)?'':",";
				$First = false;
				echo '"' . $Key . '"';
			}
			
			echo "\r\n";
			
			foreach($Arr as $StatRow)
			{
				$First = true;
				foreach($Keys as $Key)
				{
					echo ($First)?'':",";
					$First = false;
					echo '"' . $StatRow->{$Key}. '"';
				}
				echo "\r\n";
			}
		}
		
	}

?>