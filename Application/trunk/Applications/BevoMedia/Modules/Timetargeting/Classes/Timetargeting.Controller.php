<?php

	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
    require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/Filter.php');
	
	Class TimetargetingController extends ClassComponent
	{
		Public $GUID		= NULL;

		/* @var Zend_Db_Adapter  */
		Private $db;
		
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
			
			$user = new User();
			$user->getInfo($_SESSION['User']['ID']);
			$this->{'User'} = $user;
			Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
			
			$this->db = Zend_Registry::get('Instance/DatabaseObj');

			if (Zend_Registry::Get('Instance/Function') != 'RequiresVerified') {
			    if ($user->vaultID == 0) {
			        header('Location: /BevoMedia/Geotargeting/RequiresVerified.html');
			        exit;
		        }
	        }
		}
		
		Public Function EditTimetarget()
		{
			if (!isset($_GET['ID'])) {
				header('Location: /');
				die;
			}
			
			$ID = intval($_GET['ID']);
			// echo '<pre>';print_r($_POST); die;
			//EditTimetarget
			if ( isset($_POST['Submit']) ) {
				$timetargetName = $_POST['name'];
				
				$updateArr = array(
									'Name'		=> $timetargetName,
									);
				$this->db->update('bevomedia_timetargets', $updateArr, " ID = {$ID} ");
				$dbTimetargetId = $ID;
				
				foreach ($_POST as $Key => $Value) {
					if (!strstr($Key, 'landingPageURL_')) continue;
						
						$landingPageId = explode('_', $Key); $dbLandingPageId = intval($landingPageId[2]); $landingPageId = $landingPageId[1];
						$landingPageName = $Value;
						
						if ($dbLandingPageId>0) {
							$updateArr = array(
												'TimetargetID' 	=> $dbTimetargetId,
												'URL'		=> $landingPageName,
												);
							$this->db->update('bevomedia_timetargets_landing_pages', $updateArr, " ID = $dbLandingPageId ");
						} else {
							$insertArr = array(
											'TimetargetID' 	=> $dbTimetargetId,
											'URL'		=> $landingPageName,
											);
							$this->db->insert('bevomedia_timetargets_landing_pages', $insertArr);
							$dbLandingPageId = $this->db->lastInsertId();
						}
//						$dbLandingPageId = $this->db->lastInsertId();
												
						foreach ($_POST as $Key1 => $Value1) {
							if (!strstr($Key1, "day_{$landingPageId}")) continue;
							
							$dayId = explode('_', $Key1); $dbLandingPageLocationId = intval($dayId[2]);
							
							$day = $Value1;
							
							$fromTime = $_POST["fromTime_{$landingPageId}"];
							$toTime = $_POST["toTime_{$landingPageId}"];
							
							if ($dbLandingPageLocationId>0) {
								$updateArr = array(
											'LandingPageID' 	=> $dbLandingPageId,
											'Day'			=> $day,
											'Start'			=> $fromTime,
											'End'			=> $toTime,
											);
											
								$this->db->update('bevomedia_timetargets_landing_pages_times', $updateArr, " ID = {$dbLandingPageLocationId} ");
							} else {
								$insertArr = array(
											'LandingPageID' 	=> $dbLandingPageId,
											'Day'			=> $day,
											'Start'			=> $fromTime,
											'End'			=> $toTime,
											);
								$this->db->insert('bevomedia_timetargets_landing_pages_times', $insertArr);
								$dbLandingPageLocationId = $this->db->lastInsertId();
							}
							
						}
						
					
					
				}
				
				header('Location: /BevoMedia/Timetargeting/Index.html');
				die;
			}
			//EditGeotarget
			
			
			
			$Sql = "SELECT
						bevomedia_timetargets.ID,
						bevomedia_timetargets.Name,
						bevomedia_timetargets_landing_pages.ID as `LocationID`,
						bevomedia_timetargets_landing_pages.URL
					FROM
						bevomedia_timetargets,
						bevomedia_timetargets_landing_pages						
					WHERE
						(bevomedia_timetargets_landing_pages.TimetargetID = bevomedia_timetargets.ID) AND
						(bevomedia_timetargets.ID = ?) AND 
						(bevomedia_timetargets.UserID = ?) 
					";
			$urls = $this->db->fetchAll($Sql, array($ID, $this->User->id));
			
			$this->Name = $urls[0]->Name;
			$this->URLs = $urls;
			
			foreach ($this->URLs as $Key => $URL)
			{
				continue;
				$Sql = "SELECT
							ip_location_cities.ID as CityID,
							ip_location_countries.ID as CountryID,
							ip_location_regions.ID as RegionID
						FROM
							bevomedia_geotargets_landing_pages_locations,
							ip_location_cities,
							ip_location_countries,
							ip_location_regions
						WHERE
							(bevomedia_geotargets_landing_pages_locations.CityID = ip_location_cities.CITY) AND
							(bevomedia_geotargets_landing_pages_locations.RegionID = ip_location_regions.REGION) AND
							(bevomedia_geotargets_landing_pages_locations.CountryID = ip_location_countries.COUNTRY_CODE) AND
							(ip_location_countries.ID = ip_location_regions.CountryID) AND
							(ip_location_regions.ID = ip_location_cities.RegionID) AND
							(bevomedia_geotargets_landing_pages_locations.LandingPageID = ?)		
						";
				$locations = $this->db->fetchAll($Sql, array($URL->ID));
				
				$this->URLs[$Key]->locations = $locations;
			}
		}
		
		
		Public Function Index()
		{
			$sql = "SELECT
						*
					FROM
						bevomedia_timetargets
					WHERE
						UserID = ?
					";
			$results = $this->db->fetchAll($sql, $this->User->id);
			
			foreach ($results as $key => $row) 
			{
				$sql = "SELECT
							URL
						FROM
							bevomedia_timetargets_landing_pages
						WHERE
							(bevomedia_timetargets_landing_pages.TimetargetID = ?)				
						";
				$results[$key]->urls = $this->db->fetchAll($sql, $row->ID);
			}
			
			$this->timetargets = $results; 			
			
		}
		
		Public Function RemoveTimetarget()
		{
			if (!isset($_GET['ID'])) {
				return;
			}
			
			$ID = intval($_GET['ID']);
			
			if ($ID==0) {
				die;
			}
			
			$this->db->delete('bevomedia_timetargets', " ID = {$ID} ");
			
			$sql = "SELECT ID FROM bevomedia_timetargets_landing_pages WHERE TometargetID = ?";
			$rows = $this->db->fetchAll($sql, $ID);
			
			$this->db->delete('bevomedia_timetargets_landing_pages', " GeotargetID = {$ID} ");
			
			foreach ($rows as $row) {
				$this->db->delete('bevomedia_timetargets_landing_pages_times', " LandingPageID = {$row->ID} ");
			}
			
			exit;
		}
		
		Public Function ListGeoCountries()
		{
			$Sql = "SELECT
						ID,
						COUNTRY_NAME
					FROM
						ip_location_countries
					ORDER BY
						(COUNTRY_CODE = 'US') DESC, COUNTRY_NAME ASC 
					"; 
			return $this->db->fetchAll($Sql);			
		}
		
		Public Function ListGeoRegions($CountyCode = '')
		{
			if (isset($_GET['ajax'])) {
				$CountyCode = $_GET['CountyCode'];
			} 
			
			$Sql = "SELECT
						ID,
						REGION
					FROM
						ip_location_regions
					WHERE 
						(ip_location_regions.CountryID = ?) 
					ORDER BY
						REGION 
					"; 
			$results = $this->db->fetchAll($Sql, $CountyCode);

			if (isset($_GET['ajax'])) {
				echo json_encode($results);
				die;
			}
			
			return $results;
		}
		
		Public Function ListGeoCities($RegionCode = '')
		{
			if (isset($_GET['ajax'])) {
				$RegionCode = $_GET['RegionCode'];
			} 
			
			$Sql = "SELECT
						ID,
						CITY
					FROM
						ip_location_cities
					WHERE
						(RegionID = ?) 
					ORDER BY
						CITY
					"; 
			$results = $this->db->fetchAll($Sql, array($RegionCode));

			if (isset($_GET['ajax'])) {
				echo json_encode($results);
				die;
			}
			
			return $results;
		}
		
		Public Function CountriesInclude()
		{
			$this->Countries = $this->ListGeoCountries();
			
			if (!isset($_GET['ID'])) {
				return;
			}
			
			$Sql = "SELECT
						bevomedia_geotargets_landing_pages_locations.*
					FROM
						bevomedia_geotargets_landing_pages_locations
					WHERE
						(bevomedia_geotargets_landing_pages_locations.ID = ?)			
					";
			$this->Data = $this->db->fetchAll($Sql, $_GET['ID']);
			
			foreach ($this->Data as $Key => $Row)
			{
				if ($Row->CityID!='0') {
					$Row->CityID = $this->GetCityID($Row->CityID);
				}
				
				if ($Row->RegionID!='0') {
					$Row->RegionID = $this->GetRegionID($Row->RegionID);
				}
				
				if ($Row->CountryID!='0') {
					$Row->CountryID = $this->GetCountryID($Row->CountryID);
				}
				
				$this->Data[$Key] = $Row;
			}
			
//			$Sql = "SELECT
//						bevomedia_geotargets_landing_pages_locations.ID,
//						ip_location_cities.ID as CityID,
//						ip_location_countries.ID as CountryID,
//						ip_location_regions.ID as RegionID
//					FROM
//						bevomedia_geotargets_landing_pages_locations,
//						ip_location_cities,
//						ip_location_countries,
//						ip_location_regions
//					WHERE
//						( (bevomedia_geotargets_landing_pages_locations.CityID = ip_location_cities.CITY) OR (bevomedia_geotargets_landing_pages_locations.CityID = 0) ) AND
//						( (bevomedia_geotargets_landing_pages_locations.RegionID = ip_location_regions.REGION) OR (bevomedia_geotargets_landing_pages_locations.RegionID) ) AND
//						(bevomedia_geotargets_landing_pages_locations.CountryID = ip_location_countries.COUNTRY_CODE) AND
//						(ip_location_countries.ID = ip_location_regions.CountryID) AND
//						(ip_location_regions.ID = ip_location_cities.RegionID) AND
//						(bevomedia_geotargets_landing_pages_locations.ID = ?)			
//					";
//			$this->Data = $this->db->fetchAll($Sql, $_GET['ID']);
			if (count($this->Data)>0) {
				$this->Data = $this->Data[0];
			} else {
				$this->Data = array();
			}
		}
		
		Public Function NewTimetarget()
		{
			if (isset($_POST['Submit'])) {
				// echo '<pre>';print_r($_POST); die;
				$geotargetName = $_POST['name'];
				
				$insertArr = array(
									'UserID' 	=> $this->User->id,
									'Name'		=> $geotargetName,
									);
				$this->db->insert('bevomedia_timetargets', $insertArr);
				$dbGeotargetId = $this->db->lastInsertId();
				
				foreach ($_POST as $Key => $Value) {
					if (!strstr($Key, 'landingPageURL_')) continue;
						
						$landingPageId = explode('_', $Key); $landingPageId = $landingPageId[1];
						$landingPageName = $Value;
						
						
						$insertArr = array(
											'TimetargetID' 	=> $dbGeotargetId,
											'URL'		=> $landingPageName,
											);
						$this->db->insert('bevomedia_timetargets_landing_pages', $insertArr);
						$dbLandingPageId = $this->db->lastInsertId();
						
						
						foreach ($_POST as $Key1 => $Value1) {
							if (!strstr($Key1, "day_{$landingPageId}")) continue;
							
							$day = $Value1;
							
							$fromTime = $_POST["fromTime_{$landingPageId}"];
							$toTime = $_POST["toTime_{$landingPageId}"];
							
							$insertArr = array(
											'LandingPageID' 	=> $dbLandingPageId,
											'Day'			=> $day,
											'Start'			=> $fromTime,
											'End'			=> $toTime,
											);
							$this->db->insert('bevomedia_timetargets_landing_pages_times', $insertArr);
							$dbLandingPageLocationId = $this->db->lastInsertId();
						}
						
					
				}
				
				header('Location: /BevoMedia/Timetargeting/EditTimetarget.html?ID='.$dbGeotargetId);
				die;
			}
		}
		

		Public Function LandingPageInclude()
		{
			if (!isset($_GET['ID'])) {
				return;
			}
			
			$Sql = "SELECT
						*
					FROM
						bevomedia_timetargets_landing_pages
					WHERE
						(bevomedia_timetargets_landing_pages.ID = ?)			
					";
			$this->Data = $this->db->fetchAll($Sql, $_GET['ID']);
			
			if (count($this->Data)>0) {
				$this->Data = $this->Data[0];
				
				$Sql = "SELECT
							*
						FROM
							bevomedia_timetargets_landing_pages_times
						WHERE
							(bevomedia_timetargets_landing_pages_times.LandingPageID = ?)				
						";
				$this->Data->Times = $this->db->fetchRow($Sql, $this->Data->ID); 
			} else {
				$this->Data = array();
			}
		}
		
		Public Function RemoveLandingPage()
		{
			if (!isset($_GET['ID'])) {
				return;
			}
			
			$ID = intval($_GET['ID']);
			
			if ($ID==0) die;
			
			$this->db->delete('bevomedia_geotargets_landing_pages', " ID = {$ID} ");
			
			die;
		}
		
		Public Function RemoveLocation()
		{
			if (!isset($_GET['ID'])) {
				return;
			}
			
			$ID = intval($_GET['ID']);
			
			if ($ID==0) die;
			
			$this->db->delete('bevomedia_geotargets_landing_pages_locations', " ID = {$ID} ");
			
			die;
		}
		
		Private Function GetCountryCode($ID)
		{
			if ($ID==0) return 0;
			
			$Sql = "SELECT
						COUNTRY_CODE
					FROM
						ip_location_countries
					WHERE
						(ip_location_countries.ID = ?)
					";
			$Row = $this->db->fetchRow($Sql, $ID);
			return $Row->COUNTRY_CODE;
		} 
		
		Private Function GetRegionCode($ID)
		{
			if ($ID==0) return 0;
			
			$Sql = "SELECT
						REGION
					FROM
						ip_location_regions
					WHERE
						(ip_location_regions.ID = ?)
					";
			$Row = $this->db->fetchRow($Sql, $ID);
			return $Row->REGION;
		} 
		
		Private Function GetCityCode($ID)
		{
			if ($ID==0) return 0;
			
			$Sql = "SELECT
						CITY
					FROM
						ip_location_cities
					WHERE
						(ip_location_cities.ID = ?)
					";
			$Row = $this->db->fetchRow($Sql, $ID);
			return $Row->CITY;
		} 
		
		Private Function GetCountryID($Code)
		{
			if ($Code=='0') return 0;
			
			$Sql = "SELECT
						ID
					FROM
						ip_location_countries
					WHERE
						(ip_location_countries.COUNTRY_CODE = ?)
					";
			$Row = $this->db->fetchRow($Sql, $Code);
			return $Row->ID;
		} 
		
		Private Function GetRegionID($Code)
		{
			if ($Code=='0') return 0;
			
			$Sql = "SELECT
						ID
					FROM
						ip_location_regions
					WHERE
						(ip_location_regions.REGION = ?)
					";
			$Row = $this->db->fetchRow($Sql, $Code);
			return $Row->ID;
		} 
		
		Private Function GetCityID($Code)
		{
			if ($Code=='0') return 0;
			
			$Sql = "SELECT
						ID
					FROM
						ip_location_cities
					WHERE
						(ip_location_cities.CITY = ?)
					";
			$Row = $this->db->fetchRow($Sql, $Code);
			return $Row->ID;
		} 
		
		Public Function RequiresVerified()
		{
		
		}
		 
		
	}

?>
