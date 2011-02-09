<?php

	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
    require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/Filter.php');
	
	Class KeywordTrackerController extends ClassComponent
	{
		Public $GUID		= NULL;		
		
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
			$this->tracker_menu = $this->GenerateTrackerMenu();
			$this->DefaultDateRange = $this->GetDefaultDateRange();
			$this->DateRangeString = $this->DefaultDateRange;
			if(isset($_COOKIE['DateRange']))
				$this->DateRangeString = $_COOKIE['DateRange'];
			if(isset($_GET['DateRange']))
				$this->DateRangeString = $_GET['DateRange'];
			
			$this->DateRange = explode('-', $this->DateRangeString);
			if(sizeOf($this->DateRange) < 2)
			{
				$this->DateRange[1] = $this->DateRange[0];
			}
			$this->StartDate = date('Y-m-d', strtotime($this->DateRange[0]));
			$this->EndDate = date('Y-m-d', strtotime($this->DateRange[1]));
			$this->filter = new Filter($this->StartDate, $this->EndDate, $user->id);
			$this->CostView = $this->filter->costView;
		}
		
		Public Function OfferRotationSetup()
		{
			$OfferGroups = new OfferRotatorGroup();
			$this->OfferGroups = $OfferGroups->GetAllForUser($this->User->id);
			foreach($this->OfferGroups as $OfferGroup)
			{
				$OfferGroup->PopulateOffers();
			}
		}
		
		Public Function LandingPageRotationSetup()
		{
			$LandingPageGroups = new LandingPageRotatorGroup();
			$this->LandingPageGroups = $LandingPageGroups->GetAllForUser($this->User->id);
			foreach($this->LandingPageGroups as $LandingPageGroup)
			{
				$LandingPageGroup->PopulateLandingPages();
			}
		}

		
		
		Public Function _OfferRotationDelete()
		{
			if(!isset($_GET['ID']))
			{
				//Please specify an ID
			}else{
				$OfferGroup = new OfferRotatorGroup($_GET['ID']);
				$OfferGroup->DeleteThisGroup();
			}
			header('Location: OfferRotationSetup.html');
			die;
		}
		
		Public Function _LandingPageRotationDelete()
		{
			if(!isset($_GET['ID']))
			{
				//Please specify an ID
			}else{
				$LandingPageGroup = new LandingPageRotatorGroup($_GET['ID']);
				$LandingPageGroup->DeleteThisGroup();
			}
			header('Location: LandingPageRotationSetup.html');
			die;
		}
		
		Public Function OfferRotationEdit()
		{
			if(!isset($_GET['ID']))
			{
				header('Location: OfferRotationSetup.html');
				die;
			}
		
			$OfferGroup = new OfferRotatorGroup($_GET['ID']);
			$OfferGroup->PopulateOffersFull();
			$this->OfferGroup = $OfferGroup;
			if(isset($_POST) && isset($_POST['edit']))
			{
				$label = $_POST['groupLabel'];
				$id = $_POST['id'];
				
				$this->_db = Zend_Registry::get('Instance/DatabaseObj');
								
				$this->_db->delete('bevomedia_offer_rotator_group', 'id = ' . $id);
				$this->_db->delete('bevomedia_offer_rotator_link', 'groupId = ' . $id);

				$OfferGroup = new OfferRotatorGroup();
				$GroupID = $OfferGroup->Insert($label, $this->User->id, $id);
				foreach($_POST['link'] as $Key=>$Value)
				{
					$OfferLink = new OfferRotatorLink();
					$OfferLink->Insert($Value, $_POST['ratio'][$Key], $GroupID);
				}
				
				header('Location: OfferRotationSetup.html');
				die;
			}
		}
		
		Public Function LandingPageRotationEdit()
		{
			if(!isset($_GET['ID']))
			{
				header('Location: LandingPageRotationSetup.html');
				die;
			}
		
			$LandingPageGroup = new LandingPageRotatorGroup($_GET['ID']);
			$LandingPageGroup->PopulateLandingPagesFull();
			$this->LandingPageGroup = $LandingPageGroup;
			if(isset($_POST) && isset($_POST['edit']))
			{
				$label = $_POST['groupLabel'];
				$id = $_POST['id'];
				
				$this->_db = Zend_Registry::get('Instance/DatabaseObj');
								
				$this->_db->delete('bevomedia_landing_page_rotator_group', 'id = ' . $id);
				$this->_db->delete('bevomedia_landing_page_rotator_link', 'groupId = ' . $id);

				$LandingPageGroup = new LandingPageRotatorGroup();
				$GroupID = $LandingPageGroup->Insert($label, $this->User->id, $id);
				foreach($_POST['link'] as $Key=>$Value)
				{
					$LandingPageLink = new LandingPageRotatorLink();
					$LandingPageLink->Insert($Value, $_POST['ratio'][$Key], $GroupID);
				}
				
				header('Location: LandingPageRotationSetup.html');
				die;
			}
		}

		
		Public Function OfferRotationNew()
		{
			if(isset($_POST) && sizeof($_POST) > 0)
			{
				$label = $_POST['groupLabel'];
				
				$OfferGroup = new OfferRotatorGroup();
				$GroupID = $OfferGroup->Insert($label, $this->User->id);
				foreach($_POST['link'] as $Key=>$Value)
				{
					$OfferLink = new OfferRotatorLink();
					$OfferLink->Insert($Value, $_POST['ratio'][$Key], $GroupID);
				}
				
				header('Location: OfferRotationSetup.html');
				die;
			}
		}
		
		Public Function LandingPageRotationNew()
		{
			if(isset($_POST) && sizeof($_POST) > 0)
			{
				$label = $_POST['groupLabel'];
				
				$LandingPageGroup = new LandingPageRotatorGroup();
				$GroupID = $LandingPageGroup->Insert($label, $this->User->id);
				foreach($_POST['link'] as $Key=>$Value)
				{
					$LandingPageLink = new LandingPageRotatorLink();
					$LandingPageLink->Insert($Value, $_POST['ratio'][$Key], $GroupID);
				}
				
				header('Location: LandingPageRotationSetup.html');
				die;
			}
		}
		
		Public Function OfferRotationNewAjax()
		{
			$this->PostComplete = false;
			
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			if(isset($_POST) && sizeof($_POST) > 0)
			{
				$label = $_POST['groupLabel'];
				
				$OfferGroup = new OfferRotatorGroup();
				$GroupID = $OfferGroup->Insert($label, $this->User->id);
				foreach($_POST['link'] as $Key=>$Value)
				{
					$OfferLink = new OfferRotatorLink();
					$OfferLink->Insert($Value, $_POST['ratio'][$Key], $GroupID);
				}
				
				$this->PostComplete = true;
			
				$this->OfferLinkID = $GroupID;
				$OfferGroups = new OfferRotatorGroup();
				$this->OfferGroups = $OfferGroups->GetAllForUser($this->User->id);
				foreach($this->OfferGroups as $OfferGroup)
				{
					$OfferGroup->PopulateOffers();
				}
				//header('Location: OfferRotationSetup.html');
				//die;
			}
		}
		
		
		Public Function AjaxTracker()
		{
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
		}
		
		Public Function Code()
		{
			$_db = Zend_Registry::get('Instance/DatabaseObj');
			if(isset($_GET['delete']) && intval($_GET['delete']))
			{
			  $_db->delete('bevomedia_tracker_getcodes', 'user__id='.$this->User->id.' and id='.intval($_GET['delete']));
			  header('Location: CreatedCodes.html');
			  die;
			}
			// Media Buy - ProviderType: 4
			$Sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = " . $this->User->id . " AND providerType = 4";
			$this->MediaBuyCampaigns = $_db->fetchAll($Sql);
			$Sql = "SELECT adgroup.id as id, adgroup.name as name FROM bevomedia_ppc_adgroups adgroup JOIN bevomedia_ppc_campaigns campaign ON adgroup.campaignId = campaign.id WHERE campaign.user__id = " . $this->User->id . " AND campaign.providerType = 4";
			$this->MediaBuyAdGroups = $_db->fetchAll($Sql);
			
			// PPV - TrafficVance - ProviderType: 5
			$Sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = " . $this->User->id . " AND providerType = 5";
			$this->PPVCampaigns = $_db->fetchAll($Sql);
			
			// AdOn - ProviderType: 6
			$Sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = " . $this->User->id . " AND providerType = 6";
			$this->AdOnCampaigns = $_db->fetchAll($Sql);
			
			// Media Traffic - medtraf - ProviderType: 7
			$Sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = " . $this->User->id . " AND providerType = 7";
			$this->MedTrafCampaigns = $_db->fetchAll($Sql);
			
			// DirectCPV - dircpv - ProviderType: 8
			$Sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = " . $this->User->id . " AND providerType = 8";
			$this->DirectCPVCampaigns = $_db->fetchAll($Sql);
			
			// DirectCPV - dircpv - ProviderType: 9
			$Sql = "SELECT id, name FROM bevomedia_ppc_campaigns WHERE user__id = " . $this->User->id . " AND providerType = 9";
			$this->LeadImpactCampaigns = $_db->fetchAll($Sql);
			
			// Retrieve offer rotator items
			$OfferGroups = new OfferRotatorGroup();
			$this->OfferGroups = $OfferGroups->GetAllForUser($this->User->id);
			
			// Retrieve landing page rotator items
			$LandingPageGroups = new LandingPageRotatorGroup();
			$this->LandingPageGroups = $LandingPageGroups->GetAllForUser($this->User->id);
		}

		Public Function json()
		{
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
		}
		
		Public Function ManuallyUploadSubIDsAssign()
		{
			if(!isset($_POST) || !isset($_POST['subids']) /*|| !isset($_FILES['fileSubIds'])*/)
			{
				header('Location: ManuallyUploadSubIDs.html');
				exit;
			}

			if ($_FILES['fileSubIds']['name']!='')
			{
				$TempName = $_FILES['fileSubIds']['tmp_name'];
				$FileData = file_get_contents($TempName);	
				$_POST['subids'] = $FileData;
			}

			$this->SubIDs = array();
			$this->DefaultAmount = floatval($_POST['amount']);
			foreach(explode("\n", $_POST['subids']) as $sub)
			{
			  $conversions = 1;
			  $value = $this->DefaultAmount;
			  if(stripos($sub, ',') !== false)
			  {
				$a = explode(',' , $sub);
				if (trim($a[0])=='Tracking ID')
				{
					continue;	
				}
				
				if(count($a) == 3)
				{
				  $sub = $a[0];
				  $conversions = $a[1];
				  $value = str_replace('$', '', $a[2]);
			    } else
			    {
			    	$sub = $a[0];
			    	$conversions = 1; 
			    	$value = str_replace('$', '', $a[1]);
			    }
			  }
			  $sub = trim($sub);
			  if(empty($sub))
				break;
			  if(!isset($this->SubIDs[$sub]) || !is_array($this->SubIDs[$sub]))
				  $this->SubIDs[$sub] = array('value' => 0,
					'conv' => 0);
			  $this->SubIDs[$sub]['conv'] += $conversions;
			  $this->SubIDs[$sub]['value'] += $value;
			}
		}

		Public Function ManuallyUploadSubIDsComplete()
		{
			$db = Zend_Registry::get('Instance/DatabaseObj');
			$this->InsertCount = 0;
			foreach($_POST['SubID'] as $Key=>$SubID)
			{
				$Sql = "SELECT
				
						FROM
							bevomedia_tracker_clicks
						WHERE
							
				
						";
				$Actions = $_POST['SubIDAction'][$Key];
				$Revenue = $_POST['SubIDValue'][$Key];
				$Vals = array('statDate'=>date('Y-m-d'), 'subId'=>$SubID, 'clicks'=>$Actions, 'conversions'=>$Actions, 'revenue'=>$Revenue, 'user__id'=>$this->User->id);
				if(@$_POST['overwrite'] == 't')
				  $db->delete('bevomedia_user_aff_network_subid', 'user__id = '.$this->User->id.' AND subId="'.mysql_real_escape_string($SubID).'"');
				$db->insert('bevomedia_user_aff_network_subid', $Vals);
				$this->InsertCount++;
			}
		}
		
		Public Function CreatedCode()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$db = Zend_Registry::get('Instance/DatabaseObj');
			$sql = "SELECT * FROM bevomedia_tracker_getcodes WHERE id = {$_GET['ID']}";
			$this->codeRow = $db->fetchRow($sql);
		}
		
		Public Function CreatedCodes()
		{
			$db = Zend_Registry::get('Instance/DatabaseObj');
			$sql = "SELECT * FROM bevomedia_tracker_getcodes WHERE user__id = {$this->User->id}";
			$this->codeRows = $db->fetchAll($sql);
		}
		
		Public Function Raw()
		{
			error_reporting(E_COMPILE_ERROR);
			
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
			$db = Zend_Registry::get('Instance/DatabaseObj');
			
			$ProviderType = false;
			$AccountID = false;
			$CampaignID = false;
			
			$SQHelper = new SearchQueryStatHelper();
			$SQOut = $SQHelper->Process($this->StartDate, $this->EndDate, $this->User->id, $ProviderType, $AccountID, $CampaignID);
			$this->TrackerRows = $SQOut->TrackerRows;
			$this->StatRows = $SQOut->StatRows;
			$this->ModStatRows = $SQOut->ModStatRows;
			$this->SearchRows = $SQOut->SearchQueryRows;
		}
		
		Public Function Overview()
		{
			/*
			if(!isset($_GET['DateRange']))
			{
				$this->DateRange = $this->GetDefaultDateRange();
			}else{
				$this->DateRange = $_GET['DateRange'];
			}
			
			$ProviderType = (isset($_GET['ppcprovider']) && $_GET['ppcprovider'] != '')?($_GET['ppcprovider']):false;
			$AccountID = (isset($_GET['ppcaccount']) && $_GET['ppcaccount'] != '')?($_GET['ppcaccount']):false;
			$CampaignID = (isset($_GET['ppccampaign']) && $_GET['ppccampaign'] != '')?($_GET['ppccampaign']):false;
			
			$TempDate = explode('-', $this->DateRange);

			if(sizeof($TempDate) == 1)
			{
				$TempDate[1] = $TempDate[0];
			}
			
			$this->StartDate = $StartDate = date('Y-m-d', strtotime($TempDate[0]));
			$this->EndDate = $EndDate = date('Y-m-d', strtotime($TempDate[1]));
			
			$SQHelper = new SearchQueryStatHelper();
			$SQOut = $SQHelper->GetDailyTotals($this->StartDate, $this->EndDate, $this->User->id, $ProviderType, $AccountID, $CampaignID);
		*/
		}
		
		Public Function Exact()
		{
		    if($this->CostView == 'smart')
		        $this->Exact_CostsSmart();
		    else
		        $this->Exact_CostsStatic();
		    $force_cost = false;
		    if($this->CostView == 'none')
		        $force_cost = 0;
		    if($this->CostView == 'static')
		        $force_cost = $this->filter->staticCost;
		    if($force_cost !== false)
		    {
		        foreach($this->data as $kw => $row)
		        {
		            $c = $force_cost * @$row['clicks'];
		            $this->data[$kw]['cost'] = $c;
		            $this->data[$kw]['profit'] = @$this->data[$kw]['revenue'] - $c;
		        }
		    }
		}
		
		Public Function Exact_CostsSmart()
        {
            $this->data = array();
            return;
            if($this->EndDate == date('Y-m-d', strtotime('TODAY')))
            {
                $this->EndDate = date('Y-m-d', strtotime('YESTERDAY'));
                $this->ExactSmartTodayWarning = true;
                $this->filter = new Filter($this->StartDate, $this->EndDate, $this->User->id);
            }
            if($this->StartDate == date('Y-m-d', strtotime('TODAY')))
            {
                $this->StartDate = date('Y-m-d', strtotime('YESTERDAY'));
                $this->ExactSmartTodayWarning = true;
                $this->filter = new Filter($this->StartDate, $this->EndDate, $this->User->id);
            }
			$ProviderType = (isset($_GET['ppcprovider']) && $_GET['ppcprovider'] != '')?($_GET['ppcprovider']):false;
			if($ProviderType === false)
			{
				$ProviderType = 'PPC';
			}
			$AccountID = (isset($_GET['ppcaccount']) && $_GET['ppcaccount'] != '')?($_GET['ppcaccount']):false;
			$CampaignID = (isset($_GET['ppccampaign']) && $_GET['ppccampaign'] != '')?($_GET['ppccampaign']):false;
			
			
			$SQHelper = new SearchQueryStatHelper();
			$SQOut = $SQHelper->Process($this->StartDate, $this->EndDate, $this->User->id, $ProviderType, $AccountID, $CampaignID);
			$this->TrackerRows = $SQOut->TrackerRows;
			$this->StatRows = $SQOut->StatRows;
			$this->ModStatRows = $SQOut->ModStatRows;
			$this->SearchRows = $SQOut->SearchQueryRows;
            $data = array();
        	foreach($this->TrackerRows as $Key=>$Value)
        	{
        		$data[$Key] = array();
        		foreach($Value as $VKey=>$VValue)
        		{
        			$data[$Key][$VKey] = $VValue;
        		}
        		$data[$Key]['keyword'] = $Value->RawKeyword;
        		$data[$Key]['clicks'] = $Value->sumClick;
        		$data[$Key]['conversions'] = $Value->sumConv;
        		if(!isset($Value->sumRevenue))
        			$Value->sumRevenue = 0;
        		$data[$Key]['revenue'] = $Value->sumRevenue;
        		$data[$Key]['cost'] = isset($Value->sumCost)?($Value->sumCost):(0);
        	}
        	$this->data = $data;
		}
		
		Public Function Exact_CostsStatic()
		{
		    $userId = $this->User->id;
	        $stDate = $this->StartDate;
	        $enDate = $this->EndDate;



			$Arr = $this->FilterKWTables($this->StartDate, $this->EndDate, $this->User->id);
			$table_add_join = $Arr['table_add_join'];
			$table_add = $Arr['table_add'];
			$where_add = $Arr['where_add'];
			$where_cost_add = $Arr['where_cost_add'];


			$sql = "
        			SELECT
        				keywords.keyword,
        				bidkeywords.keyword as bidkeyword,
                        COALESCE(COUNT(DISTINCT stats.id), 0) AS clicks,
        				COALESCE(SUM(subid.conversions), 0) AS conversions,
                       	COALESCE(SUM(subid.revenue), 0) AS revenue                       	
        			FROM
        				(`bevomedia_tracker_clicks` `stats`
					      LEFT JOIN `bevomedia_user_aff_network_subid` `subid` ON(((`stats`.`user__id` = `subid`.`user__id`)
                                                               AND (`stats`.`subId` = `subid`.`subId`)
                                                               AND (`subid`.`statDate` >= `stats`.`clickDate`))))
                                                               
        				LEFT JOIN bevomedia_keyword_tracker_keywords AS keywords ON
        					stats.rawKeywordId = keywords.ID
        				LEFT JOIN bevomedia_keyword_tracker_keywords AS bidkeywords ON
        					stats.bidKeywordId = bidkeywords.ID
        				LEFT JOIN bevomedia_ppc_advariations advars ON advars.apiAdId = stats.creativeId
        				LEFT JOIN bevomedia_ppc_adgroups adgroups ON adgroups.ID = advars.AdGroupID
        				LEFT JOIN bevomedia_ppc_campaigns campaigns ON (campaigns.ID = adgroups.CampaignID AND campaigns.user__id = {$userId})
        				
        				{$table_add_join}
        			WHERE
        				stats.user__id = $userId
        				AND stats.clickDate BETWEEN '$stDate' AND '$enDate'
        	            AND apiAdId != 0
        	            AND campaigns.ProviderType in (1,2,3)
        	            {$where_add}
        			GROUP BY
        				keywords.keyword
        			ORDER BY
        				keywords.keyword
        		";
//echo '<pre>'.$sql; die;
        		
        	$query = mysql_query($sql);
        		
        	$data = array();
        	$no_kw = array();
        	while($row = mysql_fetch_assoc($query))
        	{
        	    if($row['clicks'] == 0)
    			{
    				continue;
    			}
        		if($row['keyword'] == '')
        		{
        		    $row['keyword'] = !empty($row['bidkeyword']) ? $row['bidkeyword'] : ' [no keyword data]';
        		    $no_kw[$row['bidkeyword']] = $row;
        			continue;
        		}
        		$data[$row['keyword']] = $row;
        	}
        	foreach($no_kw as $kw=>$row)
        	{
        	    if(empty($data[$kw]))
        	    {
        	        $data[$kw] = $row;
        	    }
        	    else
        	    {
        	        $data[$kw]['clicks'] += $row['clicks'];
        	        $data[$kw]['conversions'] += $row['conversions'];
        	        $data[$kw]['revenue'] += $row['revenue'];
        	    }
        	    foreach(array('clicks','conversions','revenue') as $c)
        	        if(!isset($data[$kw][$c]))
        	            $data[$kw][$c] = 0;
        	}
        	foreach($data as $keyword=>$row)
        	{
        		$data[$keyword]['ctr'] = ($row['clicks'] == 0) ? 0 : $row['conversions'] / $row['clicks'] * 100;
        		$data[$keyword]['epc'] = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];
        		$data[$keyword]['cost'] = 0;
        		$data[$keyword]['profit'] = $row['revenue'];
        		$data[$keyword]['cpc'] = !isset($row['cpc']) ? 0 : $row['cpc'] ;
        	}
        	
            $this->data = $data;
		}
		
		Public Function Broad()
		{
		    $this->Broad_Costs();
		    $force_cost = false;
		    if($this->CostView == 'none')
		        $force_cost = 0;
		    if($this->CostView == 'static')
		        $force_cost = $this->filter->staticCost;
		    
		    if($force_cost !== false)
		    {
		        foreach($this->data as $kw => $row)
		        {
		            $c = $force_cost * $row['clicks'];
		            $this->data[$kw]['cost'] = $c;
		            $this->data[$kw]['profit'] = $this->data[$kw]['revenue'] - $c;
		        }
		    }
		    if($this->CostView == 'smart')
		    {
		        $click_discrepency = ($this->total_clicks - $this->assigned_clicks);
		        $cost_discrepency = ($this->total_cost - $this->assigned_cost);
		        $force_cost = $click_discrepency > 0 ? $cost_discrepency / $click_discrepency : 0;
		        foreach($this->data as $kw => $row)
		        {
		            if(!empty($row['cost']) || $row['clicks'] > $click_discrepency)
		                continue;
		            $c = $force_cost * $row['clicks'];
		            $this->data[$kw]['cost'] = $c;
		            $this->data[$kw]['profit'] = $this->data[$kw]['revenue'] - $c;
		            $cost_discrepency -= $c;
		            $click_discrepency -= $row['clicks'];
		        }
		    }
		}
		
		Protected Function FilterKWTables($StartDate, $EndDate, $userId)
		{
			$request = array_merge($_COOKIE, $_GET);
			
			
			$costView = in_array(@$request['costs'], array('none', 'static', 'smart')) ? $request['costs'] : 'none';
	        $staticCost = isset($request['staticCost']) ? floatval($request['staticCost']) : .5; 

       		$DateRange = ($StartDate == $EndDate) ? date('m/d/Y', strtotime($StartDate)) : date('m/d/Y', strtotime($StartDate)) . ' - ' . date('m/d/Y', strtotime($EndDate));
			
			$filter_ppcprovider = 0;
			$filter_ppcaccount = 0;
			$filter_ppccampaign = 0;
			$filter_ppcadgroup = 0;
			$filter_keyword = '';
	        $filter_keywordid = 0;
	        $filter_visitorip = '';
        	$filter_visitoripid = 0;
	        
	        
			if(!empty($request['ppcprovider']) && is_numeric($request['ppcprovider']))
			{
        		$filter_ppcprovider = (int)$request['ppcprovider'];	        
			}
			
			if(!empty($request['ppcaccount']) && is_numeric($request['ppcaccount']))
			{
        		$filter_ppcaccount = (int)$request['ppcaccount'];
			}
			
			if(!empty($request['ppccampaign']) && is_numeric($request['ppccampaign']))
			{
        		$filter_ppccampaign = (int)$request['ppccampaign'];
			}
			
			if(!empty($request['ppcadgroup']) && is_numeric($request['ppcadgroup']))
			{
        		$filter_ppcadgroup= (int)$request['ppcadgroup'];
			}
			
			
	        if(!empty($request['keyword']))
	        {
	        	$filter_keyword = $request['keyword'];
	        	$sql = "SELECT id FROM bevomedia_keyword_tracker_keywords WHERE keyword = '".mysql_real_escape_string($filter_keyword)."'";
	        	$query = mysql_query($sql);
	        	if($row = mysql_fetch_array($query))
	        		$filter_keywordid = $row['id'];
	        }
	        
	        
	        if(!empty($request['visitorip']))
	        {
	        	$filter_visitorip = $request['visitorip'];
	        	$sql = "SELECT id FROM bevomedia_tracker_ips WHERE ipAddress = '".mysql_real_escape_string($filter_visitorip)."'";
	        	$query = mysql_query($sql);
	        	if($row = mysql_fetch_array($query))
	        		$filter_visitoripid = $row['id'];
	        }
	        
	        $table_add_join = '';
	        $table_add = '';
	        $where_add = '';
	        $where_cost_add = '';
	        if($filter_ppcprovider != 0)
	        {
	        	if ($filter_ppcprovider==1)
	        	{
	        		$table_add = 'bevomedia_accounts_adwords'; 
	        	} else
	        	if ($filter_ppcprovider==2)
	        	{
	        		$table_add = 'bevomedia_accounts_yahoo'; 
	        	} else
	        	if ($filter_ppcprovider==3)
	        	{
	        		$table_add = 'bevomedia_accounts_msnadcenter'; 
	        	}
	        	
				$table_add_join = "LEFT JOIN {$table_add} ON (campaigns.AccountID = {$table_add}.id) ";
				
				$where_add .= ' AND campaigns.ProviderType = '.intval($filter_ppcprovider);
				
				$where_add .= " AND ({$table_add}.deleted = 0) ";
	        } else 
	        {
	        	$table_add_join .= "LEFT JOIN bevomedia_accounts_adwords ON (campaigns.AccountID = bevomedia_accounts_adwords.id) \n";
	        	$table_add_join .= "LEFT JOIN bevomedia_accounts_yahoo ON (campaigns.AccountID = bevomedia_accounts_yahoo.id) \n";
	        	$table_add_join .= "LEFT JOIN bevomedia_accounts_msnadcenter ON (campaigns.AccountID = bevomedia_accounts_msnadcenter.id) \n";
	        	
	        	$where_add .= " AND ( ";
	        	$where_add .= "  (bevomedia_accounts_adwords.deleted = 0) OR (bevomedia_accounts_yahoo.deleted = 0) OR (bevomedia_accounts_msnadcenter.deleted = 0) OR  ";
	        	$where_add .= "  (bevomedia_accounts_adwords.deleted IS NULL) OR (bevomedia_accounts_yahoo.deleted IS NULL) OR (bevomedia_accounts_msnadcenter.deleted IS NULL)  ";
	        	$where_add .= " ) ";
	        }
	        
	        if($filter_ppcaccount != 0)
	        {
	        	$where_add .= ' AND '.$table_add.'.id = '.$filter_ppcaccount;
	        	$where_cost_add .= ' AND '.$table_add.'.id = '.$filter_ppcaccount;
	        }
	        
	        if($filter_ppccampaign != 0)
	        {
	        	$where_add .= ' AND campaigns.id = '.$filter_ppccampaign;
	        	$where_cost_add .= ' AND campaigns.id = '.$filter_ppccampaign;
	        }
	        
			if($filter_ppcadgroup != 0)
	        {
//	        	$table_add .= ' , bevomedia_ppc_adgroups ';
//	        	$table_add_join .= " LEFT JOIN bevomedia_ppc_adgroups ON (campaigns.id = bevomedia_ppc_adgroups.campaignId) ";
	        	
	        	$where_add .= ' AND adgroups.id = '.$filter_ppcadgroup;
	        	$where_cost_add .= ' AND adgroups.id = '.$filter_ppcadgroup;
	        }
	        
	        if(!empty($filter_keyword))
	        {
	        	$where_add .= ' AND (stats.raw_keyword_id = '.$filter_keywordid.' OR stats.bid_keyword_id = '.$filter_keywordid.')';
	        }
	        
	        if(!empty($filter_visitorip))
			{
	        	$where_add .= ' AND stats.ipId = '.$filter_visitoripid;
			}
			
			
			// Set all as cookies
	        $filter_cookie_expiration = 0;
	        $filter_cookie_path = '/';
	        @setcookie('costs', $costView, $filter_cookie_expiration, $filter_cookie_path);
	        @setcookie('staticCost', $staticCost, $filter_cookie_expiration, $filter_cookie_path);
	        @setcookie('ppcprovider', $filter_ppcprovider, $filter_cookie_expiration, $filter_cookie_path);
	        @setcookie('ppcaccount', $filter_ppcaccount, $filter_cookie_expiration, $filter_cookie_path);
	        @setcookie('ppccampaign', $filter_ppccampaign, $filter_cookie_expiration, $filter_cookie_path);
	        @setcookie('DateRange', $DateRange, $filter_cookie_expiration, $filter_cookie_path);
	        @setcookie('keyword', $filter_keyword, $filter_cookie_expiration, $filter_cookie_path);
	        @setcookie('visitorip', $filter_visitorip, $filter_cookie_expiration, $filter_cookie_path);
	        
	        return array('table_add_join' => $table_add_join, 'table_add' => $table_add, 'where_add' => $where_add, 'where_cost_add' => $where_cost_add);
		}
		
		Protected Function Broad_Costs()
		{
			$user__id = $this->User->id;
	        $stDate = $this->StartDate;
	        $enDate = $this->EndDate;



			$Arr = $this->FilterKWTables($this->StartDate, $this->EndDate, $this->User->id);
			$table_add_join = $Arr['table_add_join'];
			$table_add = $Arr['table_add'];
			$where_add = $Arr['where_add'];
			$where_cost_add = $Arr['where_cost_add'];


	        
	        $sql = "
			SELECT
			    keywords.keyword,
			    COALESCE(COUNT(DISTINCT stats.id), 0) AS clicks,
				COALESCE(SUM(subid.conversions), 0) AS conversions,
			   	COALESCE(SUM(subid.revenue), 0) AS revenue,
			    kws.MatchType as matchtype
			FROM
				(`bevomedia_tracker_clicks` `stats`
			      LEFT JOIN `bevomedia_user_aff_network_subid` `subid` ON(((`stats`.`user__id` = `subid`.`user__id`)
			                                                               AND (`stats`.`subId` = `subid`.`subId`)
			                                                               AND (`subid`.`statDate` >= `stats`.`clickDate`))))
			    LEFT JOIN bevomedia_keyword_tracker_keywords AS keywords ON
			    	stats.bidKeywordId = keywords.ID
			    LEFT JOIN bevomedia_ppc_advariations advars ON advars.apiAdId = stats.creativeId
			    LEFT JOIN bevomedia_ppc_adgroups adgroups ON adgroups.ID = advars.AdGroupID
			    LEFT JOIN bevomedia_ppc_campaigns campaigns ON (campaigns.ID = adgroups.CampaignID AND campaigns.user__id = {$user__id})

			    left JOIN bevomedia_ppc_keywords kws ON
			    	stats.bidKeywordId = kws.ID
		    	{$table_add_join}
			        WHERE
			                stats.user__id = $user__id
			                AND stats.clickDate BETWEEN '$stDate' AND '$enDate'
			                AND apiAdId != 0
			                AND campaigns.ProviderType in (1,2,3)
			                {$where_cost_add}
			                {$where_add}
			        GROUP BY
			                keywords.keyword
			        ORDER BY
			                keywords.keyword
	        ";
//echo '<pre>';echo $sql;die;
	        
	        $query = mysql_query($sql);

        	$data = array();
        	$this->total_clicks = 0;
        	while($row = mysql_fetch_assoc($query))
        	{
        		$data[$row['keyword']] = $row;
        		$this->total_clicks += @$row['clicks'];
        		$data[$row['keyword']]['ctr'] = ($row['clicks'] == 0) ? 0 : $row['conversions'] / $row['clicks'] * 100;
        		$data[$row['keyword']]['epc'] = ($row['clicks'] == 0) ? 0 : $row['revenue'] / $row['clicks'];
        		$data[$row['keyword']]['cost'] = 0;
        		$data[$row['keyword']]['profit'] = $row['revenue'];
        		$data[$row['keyword']]['ctr'] = 0;
        		$data[$row['keyword']]['cpc'] = 0;
        		switch($row['matchtype'])
        		{
        			case 1:
        				$data[$row['keyword']]['keyword'] = '"' . $row['keyword'] . '"';
        				break;
        			case 2:
        				$data[$row['keyword']]['keyword'] = '[' . $row['keyword'] . ']';
        				break;
        		}
        	}
        	
        	
        	$table_add_join = str_replace('campaigns.', 'c.', $table_add_join);
        	$where_cost_add = str_replace('campaigns.', 'c.', $where_cost_add);
        	$where_cost_add = str_replace('adgroups.', 'a.', $where_cost_add);
        	$where_add = str_replace('campaigns.', 'c.', $where_add);
        	
        	$query = "SELECT 
        					keyword.keyword,
	        				COALESCE(SUM(s.clicks),0) AS clicks,
	        				COALESCE(SUM(s.Cost),0) AS cost	
						FROM ((((`bevomedia_ppc_keywords_stats` `s`
						         JOIN `bevomedia_ppc_keywords` `k` ON((`s`.`keywordId` = `k`.`id`)))
						        JOIN `bevomedia_ppc_adgroups` `a` ON((`k`.`adGroupId` = `a`.`id`)))
						       JOIN `bevomedia_ppc_campaigns` `c` ON((`c`.`id` = `a`.`campaignId`)))
						      JOIN `bevomedia_keyword_tracker_keywords` `keyword` ON((`k`.`keywordId` = `keyword`.`id`)))
						      {$table_add_join}
						WHERE
							c.user__id = $user__id
							AND c.accountId != 0
	        				AND statDate BETWEEN '$stDate' AND '$enDate'
	        				$where_cost_add
	        				{$where_add}
						GROUP BY
							keyword.keyword
						";
	        				
//        	echo '<pre>'.$query; die;
	        				
        	                        
        	// Get cost data
//        	$query = "
//        			SELECT
//        				keyword,
//        				COALESCE(SUM(clicks),0) AS clicks,
//        				COALESCE(SUM(Cost),0) AS cost
//        			FROM
//        				bevomedia_view_ppc_stats
//        				LEFT JOIN bevomedia_ppc_campaigns campaigns
//        					ON (campaigns.ID = bevomedia_view_ppc_stats.campaignId)
//       					{$table_add_join}
//        			WHERE
//        				bevomedia_view_ppc_stats.user__id = $user__id
//						AND campaigns.AccountId != 0
//        				AND statDate BETWEEN '$stDate' AND '$enDate'
//        				$where_cost_add
//        			GROUP BY
//        				keyword
//        		";
        	$query = mysql_query($query);
        	$this->total_cost = 0;
        	$this->assigned_clicks = 0;
        	$this->assigned_cost = 0;
        	while($row = mysql_fetch_assoc($query))
        	{
        		if(!empty($data[$row['keyword']]))
        		{
        			$data[$row['keyword']]['cost'] = @$row['cost'];
        			
                	$this->assigned_clicks += @$row['clicks'];
                	$this->assigned_cost += @$row['cost'];
        		}
                $this->total_cost += @$row['cost'];
	        }
            $this->data = $data;
		}

		Public Function MediaBuys()
		{
			
		}
		
		Public Function MediaBuysRecentClicks()
		{
			$DateRange = $this->GetDefaultDateRange();
			if(isset($_GET['DateRange']))
			{
				$DateRange = $_GET['DateRange'];
			}
			$DateSet = explode('-', $DateRange);
			if(!isset($DateSet[1]))
			{
				$DateSet[1] = $DateSet[0];
			}
			$this->DateRange = $DateSet[0] . '-' . $DateSet[1];
			$StartDate = $DateSet[0];
			$EndDate = $DateSet[1];
			
			$this->StartDate = date('Y-m-d', strtotime($StartDate));
			$this->EndDate = date('Y-m-d', strtotime($EndDate));
		}
		
		Public Function PPVStats()
		{
			$db = Zend_Registry::get('Instance/DatabaseObj');
			
		    $StartDate = $this->StartDate;
		    $EndDate = $this->EndDate;
			$ProviderType = $this->filter->filter_ppcprovider = isset($_GET['provider'])?($_GET['provider']):(false);
			$Campaign = $this->filter->filter_ppccampaign = (isset($_GET['campaign']) && !empty($_GET['campaign']))?($_GET['campaign']):(false);
			$UserID = $this->User->id;
			$AndSql = "";
			$AndSql .= !empty($ProviderType)?' AND pc.ProviderType = '.$ProviderType :' AND pc.ProviderType >= 5';
			$AndSql .= !empty($Campaign)?' AND pc.ID = '.$Campaign:'';
			
			$this->provider = $ProviderType;
			$this->campaign = $Campaign;
			
			
			$Sql = "SELECT
					tc.*,
					tco.data as data,
					pc.*,
					pa.*,
					pc.name as CampaignName,
					pa.name as AdGroupName,
					count(DISTINCT tc.id) as sumClick,
					sum(afs.clicks) as sumClicks,
					sum(afs.conversions) as sumConv,
					sum(afs.revenue) as sumRevenue,
					( max(pavs.cost) * (count(DISTINCT tc.id)) ) as sumCost,
					max(pavs.cost) as cost
				FROM
					bevomedia_tracker_clicks tc
				LEFT JOIN
					bevomedia_tracker_clicks_optional tco
					ON tco.clickId = tc.id
				LEFT JOIN
					bevomedia_ppc_advariations pav
					ON pav.apiAdId = tc.creativeId
				LEFT JOIN
					bevomedia_ppc_advariations_stats pavs
					ON pavs.advariationsId = pav.id
				LEFT JOIN
					bevomedia_ppc_adgroups pa
					ON pa.id = pav.adGroupId
				LEFT JOIN
					bevomedia_ppc_campaigns pc
					ON (pc.user__id = tc.user__id AND pc.id = pa.campaignId)
				LEFT JOIN
					bevomedia_user_aff_network_subid afs
					ON (afs.user__id= tc.user__id AND afs.subId = tc.subId)
				WHERE
					pc.user__id = {$UserID}
					AND clickDate
						BETWEEN '{$StartDate}' AND '{$EndDate}'
					AND tc.creativeId != ''
					{$AndSql}
				GROUP BY
					data
				ORDER BY
					data
				";
					
			$this->StatRows = $db->fetchAll($Sql);
			
		}
		
		Public Function VisitorInfo()
		{
		  $userId = $this->User->id;
		  $subId = mysql_real_escape_string(@$_GET['subId']);
		  
		  $q = "SELECT `click`.`user__id` AS `user__id`,
				       `click`.`subId` AS `subId`,
				       `click`.`clickTime` AS `clickTime`,
				       `ip`.`ipAddress` AS `ipAddress`,
				       `click`.`referrerUrl` AS `referrerUrl`,
				       `campaign`.`name` AS `campaignName`,
				       `adgroup`.`name` AS `adgroupName`,
				       `creative`.`title` AS `creativeTitle`,
				       `lp`.`landingPageUrl` AS `lp`,
				       `bid_keyword`.`keyword` AS `bidKeyword`,
				       `raw_keyword`.`keyword` AS `rawKeyword`,
				       
				       FROM_UNIXTIME(click.clickTime) as at,
						bevomedia_tracker_clicks_optional.data as optional,
						afs.conversions as conv
				FROM (((((((`bevomedia_tracker_clicks` `click`
				            JOIN `bevomedia_tracker_ips` `ip` ON((`click`.`ipId` = `ip`.`id`)))
				           JOIN `bevomedia_tracker_landing_pages` `lp` ON((`click`.`landingPageId` = `lp`.`id`)))
				          LEFT JOIN `bevomedia_ppc_advariations` `creative` ON((`click`.`creativeId` = `creative`.`apiAdId`)))
				         LEFT JOIN `bevomedia_ppc_adgroups` `adgroup` ON((`creative`.`adGroupId` = `adgroup`.`id`)))
				        LEFT JOIN `bevomedia_ppc_campaigns` `campaign` ON(((`adgroup`.`campaignId` = `campaign`.`id`)
				                                                           AND (`campaign`.`user__id` = `click`.`user__id`))))
				       LEFT JOIN `bevomedia_keyword_tracker_keywords` `bid_keyword` ON((`click`.`bidKeywordId` = `bid_keyword`.`id`)))
				      LEFT JOIN `bevomedia_keyword_tracker_keywords` `raw_keyword` ON((`click`.`rawKeywordId` = `raw_keyword`.`id`)))
				      
				      LEFT JOIN bevomedia_user_aff_network_subid afs ON (afs.subId = click.subId AND afs.user__id = click.user__id)
				      LEFT JOIN bevomedia_tracker_clicks_optional ON click.subId = bevomedia_tracker_clicks_optional.clickId
				      
				WHERE 
					click.user__id = $userId and click.subId = '$subId'
				GROUP BY 
					click.subId, clickTime
				
		  		";
		  
//		  echo $q; die;
		  
//		  $q = "
//		SELECT
//			recent_visitors.*,
//			FROM_UNIXTIME(recent_visitors.clickTime) as at,
//			bevomedia_tracker_clicks_optional.data as optional,
//			afs.conversions as conv,
//			afs.revenue as rev
//		FROM
//			bevomedia_view_recent_visitors AS recent_visitors
//		LEFT JOIN bevomedia_user_aff_network_subid afs ON (afs.subId = recent_visitors.subId AND afs.user__id = recent_visitors.user__id)
//		LEFT JOIN bevomedia_tracker_clicks_optional ON recent_visitors.subId = bevomedia_tracker_clicks_optional.clickId
//			WHERE recent_visitors.user__id = $userId and recent_visitors.subId = '$subId'
//			GROUP BY recent_visitors.subId, clickTime
//		  ";
		  $q = preg_replace( '/\s+/', ' ', $q);
		  $this->click = $this->db->fetchRow($q);
		  Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		}

		Public Function VisitorJSON()
		{
		  function v($p, $d)
		  {
			if(isset($_REQUEST[$p])&&!empty($_REQUEST[$p]))
			  return mysql_real_escape_string($_REQUEST[$p]);
			else
			  return $d;
		  }
		  $sDate = strtotime(date('Y-m-d', strtotime(v('startDate', 'today'))) . ' 00:00:00');
		  $eDate = strtotime(date('Y-m-d', strtotime(v('endDate', 'today'))) . ' 23:59:59');
		  
		  
		  
		  $where = "clickTime >= $sDate and clickTime < $eDate";
		  $whereTABLE = "(bevomedia_tracker_clicks.clickTime >= $sDate) AND (bevomedia_tracker_clicks.clickTime < $eDate)";
		  $search = v('search', false);
		  $userId = $this->User->id;
		  $order = v('o', 'clicktime');
		  $orderDir = v('o_dir', 'desc');
		  $lStart = v('start', 0);
		  $lEnd = v('end', 250);
		  
		  $Sql = "SELECT
		  				*
		  			FROM
		  				bevomedia_tracker_clicks
	  				WHERE
	  					(bevomedia_tracker_clicks.user__id = ?) AND
	  					{$whereTABLE}
  					GROUP BY 
						bevomedia_tracker_clicks.subId				
					ORDER BY $order $orderDir
						  
					LIMIT $lStart, $lEnd
		  		";
  		  $Clicks = $this->db->fetchAll($Sql, $userId);
  		  
  		  foreach ($Clicks as $Click) {
  		  		
  		  		$Click->at = date('Y-m-d H:i:s', $Click->clickTime);
  		  	
  		  		$Sql = "SELECT ipAddress FROM bevomedia_tracker_ips WHERE id = ?";
  		  		$ip = $this->db->fetchRow($Sql, $Click->ipId);
  		  		$Click->ipAddress = @$ip->ipAddress;
  		  		
  		  		$Sql = "SELECT landingPageUrl FROM bevomedia_tracker_landing_pages WHERE id = ?";
  		  		$landingPage = $this->db->fetchRow($Sql, $Click->landingPageId);
  		  		$Click->lp = @$landingPage->landingPageUrl;
  		  	
  		  		$Sql = "SELECT title, adGroupId FROM bevomedia_ppc_advariations WHERE apiAdId = ? ";
  		  		$adVar = $this->db->fetchRow($Sql, $Click->creativeId);
  		  		$Click->creativeTitle = @$adVar->title;
  		  		
  		  		
  		  		$Sql = "SELECT name, campaignId FROM bevomedia_ppc_adgroups WHERE id = ? ";
  		  		$adGroup = $this->db->fetchRow($Sql, $adVar->adGroupId);
  		  		$Click->adgroupName = @$adGroup->name;
  		  		
  		  		
  		  		$Sql = "SELECT name FROM bevomedia_ppc_campaigns WHERE id = ?";
  		  		$adCampaign = $this->db->fetchRow($Sql, $adGroup->campaignId);
  		  		$Click->campaignName = @$adCampaign->name;
  		  		
  		  		
  		  		$Sql = "SELECT keyword FROM bevomedia_keyword_tracker_keywords WHERE id = ?";
  		  		$bidKeyword = $this->db->fetchRow($Sql, $Click->bidKeywordId);
  		  		$Click->bidKeyword = @$bidKeyword->keyword;
  		  		
  		  		
  		  		$Sql = "SELECT keyword FROM bevomedia_keyword_tracker_keywords WHERE id = ?";
  		  		$rawKeyword = $this->db->fetchRow($Sql, $Click->rawKeywordId);
  		  		$Click->rawKeyword = @$rawKeyword->keyword;
  		  		
  		  		
  		  		$Sql = "SELECT data FROM bevomedia_tracker_clicks_optional WHERE clickId = ? ";
  		  		$optional = $this->db->fetchRow($Sql, $Click->subId);
  		  		$Click->optional = @$optional->data;
  		  		
  		  		$Sql = "SELECT conversions FROM bevomedia_user_aff_network_subid WHERE (bevomedia_user_aff_network_subid.subId = ?) AND (bevomedia_user_aff_network_subid.user__id = ?)";
  		  		$converted = $this->db->fetchRow($Sql, array($Click->subId, $Click->user__id));
  		  		$Click->conv = @$converted->conversions;
  		  		
  		  		
//  		  		$Click->conv = $Click->clickThrough;
  		  }
  		  
  		  
  		  
  		  $Sql = "SELECT
		  				COUNT(*) as `Total`
		  			FROM
		  				bevomedia_tracker_clicks
	  				WHERE
	  					(bevomedia_tracker_clicks.user__id = ?) AND
	  					{$whereTABLE}
  					
		  		";
  		  $ClicksTotal = $this->db->fetchRow($Sql, $userId);
  		  
  		  
  		  $arr = array('results' => $Clicks, 'passback' => @$_GET['passback'], 'count' => $ClicksTotal->Total );
		  die(json_encode($arr));
		  
		}
		
		Private Function GetDefaultDateRange()
		{
			$DateRange = date('m/j/Y', strtotime('TODAY - 1 DAYS'));
			$DateRange .= '-';
			$DateRange .= date('m/j/Y', strtotime('TODAY'));
			return $DateRange;
		}
		
		Private Function GenerateTrackerMenu()
		{
			$output = <<<END
			
<style>	/* General */
	#cssdropdown, #cssdropdown ul { position: absolute; z-index: 100; list-style: none; font-weight: bold;}
	#cssdropdown, #cssdropdown * { padding: 0; margin: 0; font-weight: bold; color: #ffffff;}
	
	/* Head links */
	#cssdropdown li.headlink { line-height: 26px; width: 95px; float: left; margin-left: -1px; text-align: center; }
	#cssdropdown li.headlink a { display: block; text-decoration: none; font-size: 11px; }
	#cssdropdown li.headlink a:hover { background: url(/Themes/BevoMedia/img/bluegradientarrow.gif)}
	
	/* Child lists and links */
	#cssdropdown li.headlink ul {width: 120px; display: none; border-top: 1px black solid; text-align: left; }
	#cssdropdown li.headlink:hover ul {margin-top: 2px; display: block; border: 2px solid #DFDFDF; padding: 2px;}
	#cssdropdown li.headlink ul li a {line-height: 26px; text-align: center; height: 26px; background: url(/Themes/BevoMedia/img/bluegradientarrowgray.gif) top; }
	#cssdropdown li.headlink ul li a:hover { text-decoration: none; color: #fff;  background: url(/Themes/BevoMedia/img/bluegradientarrow.gif)}
	
	/* Pretty styling */
	#cssdropdown a { } #cssdropdown ul li a:hover { text-decoration: none; }
	#cssdropdown li.headlink { height: 28px; background-color: white; background-image: url(/Themes/BevoMedia/img/bluegradientarrowgray.gif); }
	#cssdropdown li.headlink ul {  }
</style>
<script language="javascript">
	window.onload = function()
	{
		var lis = document.getElementsByTagName('li');
		for(i = 0; i < lis.length; i++)
		{
			var li = lis[i];
			if (li.className == 'headlink')
			{
				if(this.getElementsByTagName)
				{
					li.onmouseover = function() { this.getElementsByTagName('ul').item(0).style.display = 'block'; }
					li.onmouseout = function() { this.getElementsByTagName('ul').item(0).style.display = 'none'; }
				}
			}
		}
	}
</script>
<ul id="cssdropdown">
	<li class="headlink">
		<a href="Overview.html">Overview</a>
		<ul>
		  <li><a href="Overview.html">Overview Stats</a></li>
		  <li><a href="LandingPage.html">Landing Page Stats</a></li>
		  <li><a href="VisitorSpy.html">Visitor Spy</a></li>
		  <li><a href="ManuallyUploadSubIDs.html">Manual SubID Upload</a></li>
		  <li><a href="CreatedCodes.html">Created Codes</a></li>
		  <li><a href="OfferRotationSetup.html">Offer Rotators</a></li>
		  <li><a href="LandingPageRotationSetup.html">LP Rotators</a></li>
		  <li><a href="AdjustMediaBuyPrice.html">Adjust Media CPC</a></li>
		  <li><a href="SubIDLookup.html">SubID Lookup</a></li>
		</ul>
	</li>
	<li class="headlink">
		<a href="#">PPC Tracker</a>
		 <ul>
		  <li><a href="Broad.html">Bidded KWs</a></li>
		  <li><a href="Exact.html">Exact KWs</a></li>
		  <li><a href="Campaign.html">Campaigns</a></li>
		  <li><a href="Adgroup.html">Ad Groups</a></li>
		  <li><a href="Offer.html">Ad Variations</a></li>
		 </ul>
	</li>
	<li class="headlink">
		<a href="PPVStats.html">PPV Tracker</a>
	</li>
	<li class="headlink" style="width: 106px;">
		<a href="MediaBuys.html">Media Buy Stats</a>
	</li>

	<li class="headlink" style="width: 140px;">
		<a href="Code.html">Retrieve Tracking Code</a>
		<ul>
		  <li><a href="Code.html?Select=google">Google Adwords</a></li>
		  <li><a href="Code.html?Select=yahoo">Yahoo! Search</a></li>
		  <li><a href="Code.html?Select=msn">MSN AdCenter/Bing</a></li>
		  <li><a href="Code.html?Select=trafficvance">Traffic Vance</a></li>
		  <li><a href="Code.html?Select=adon">AdOn Network</a></li>
		  <li><a href="Code.html?Select=mediatraffic">Media Traffic</a></li>
		  <li><a href="Code.html?Select=dircpv">DirectCPV</a></li>
		  <li><a href="Code.html?Select=leadimpact">Lead Impact</a></li>
		  <li><a href="Code.html?Select=other">Other Media Buy</a></li>
		</ul>
	</li>
	<li class="headlink">
		<a href="/BevoMedia/Publisher/PPCTutorials.html">Tutorials</a>
	</li>
</ul>

<br class="clearBoth"/>
<br/>

<!--
<center>
<table><tr>
	<td><a href="Overview.html"><img src="/Themes/BevoMedia/img/overview.gif" style="border:none"></a></td>
	<td><a href="Campaign.html"><img src="/Themes/BevoMedia/img/tcamp.gif" style="border:none"></a></td>
	<td><a href="Adgroup.html"><img src="/Themes/BevoMedia/img/tadgroup.gif" style="border:none"></a></td>
	<td><a href="Offer.html"><img src="/Themes/BevoMedia/img/advariation.gif" style="border:none"></a></td>
	<td><a href="LandingPage.html"><img src="/Themes/BevoMedia/img/landingpage.gif" style="border:none"></a></td>
</tr>
<tr>
	<td><a href="Broad.html"><img src="/Themes/BevoMedia/img/tBroadkw.gif" style="border:none"></a></td>
	<td><a href="Exact.html"><img src="/Themes/BevoMedia/img/texactkw.gif" style="border:none"></a></td>
	<td><a href="Visitor.html"><img src="/Themes/BevoMedia/img/tvisitinfo.gif" style="border:none"></a></td>
	<td><a href="Code.html"><img src="/Themes/BevoMedia/img/tcode.gif" style="border:none"></a></td>
	<td><a href="MediaBuys.html"><img src="/Themes/BevoMedia/img/button_mediabuys.gif" style="border:none"></a></td>
</tr><tr><td height=25></td></tr></table>
</center>
-->
END;
			return $output;
			
		}
		
		Public Function AdjustMediaBuyPrice()
		{
			if (isset($_POST['Submit']) && ($_POST['AdVariationID']!=''))
			{
				$Array = array('cost' => $_POST['Cost']);
				$this->db->update('bevomedia_ppc_advariations_stats', $Array, ' advariationsId = '.intval($_POST['AdVariationID']));
			}
		
			$Sql = "SELECT
						id,
						name
					FROM
						bevomedia_ppc_campaigns
					WHERE
						(user__id = {$_SESSION['User']['ID']}) AND
						(providerType <> 1) AND 
						(providerType <> 2) AND 
						(providerType <> 3) 
					";
			$this->Campaigns = $this->db->fetchAll($Sql);
			
			if (isset($_POST['CampaignID']) && ($_POST['CampaignID']!='') )
			{
				$CampaignID = intval($_POST['CampaignID']);
				$Sql = "SELECT
							id,
							name
						FROM
							bevomedia_ppc_adgroups
						WHERE
							(bevomedia_ppc_adgroups.campaignId = {$CampaignID})
						";
				$this->AdGroups = $this->db->fetchAll($Sql);
			}
			
			if (isset($_POST['AdGroupID']) && ($_POST['AdGroupID']!='') )
			{
				$AdGroupID = intval($_POST['AdGroupID']);
				$Sql = "SELECT
							id,
							title
						FROM
							bevomedia_ppc_advariations
						WHERE
							(bevomedia_ppc_advariations.adGroupId = {$AdGroupID})
						";
				$this->AdVariations = $this->db->fetchAll($Sql);
			}
			
			if (isset($_POST['AdVariationID']) && ($_POST['AdVariationID']!='') )
			{
				$AdVariationID = intval($_POST['AdVariationID']);
				$Sql = "SELECT
							id,
							cost
						FROM
							bevomedia_ppc_advariations_stats
						WHERE
							(bevomedia_ppc_advariations_stats.advariationsId = {$AdVariationID})
						";
				$this->AdVariation = $this->db->fetchRow($Sql);
			}
			
			
		}
		
		Public Function SubIDLookup()
		{
			function v($p, $d)
			{
				if(isset($_REQUEST[$p])&&!empty($_REQUEST[$p]))
				  return mysql_real_escape_string($_REQUEST[$p]);
				else
				  return $d;
			}
		  
			$search = v('search', false);
			$userId = $this->User->id;
			$lStart = v('start', 0);
			$lEnd = v('end', 250);
		  
			if ($search!='')
			{
				$searchTerms = '';
				$searchArr = explode(',', $search);
				
				foreach ($searchArr as $searchItem)
				{
					$searchItem = trim($searchItem);
					$searchItem = $this->db->quote($searchItem);
					
					$searchTerms .= "(click.subId = $searchItem) OR";
				}		
				$searchTerms = substr($searchTerms, 0, -2);
				$searchTerms = '('.$searchTerms.')';
				
//				$search = $this->db->quote($search);
			  
				$Sql = "
				SELECT 
					click.user__id AS user__id,
					click.subId AS subId,
					click.clickTime AS clickTime,
					ip.ipAddress AS ipAddress,
					click.referrerUrl AS referrerUrl,
					campaign.name AS campaignName,
					adgroup.name AS adgroupName,
					creative.title AS creativeTitle,
					lp.landingPageUrl AS lp,
					bid_keyword.keyword AS bidKeyword,
					raw_keyword.keyword AS rawKeyword ,
					
					FROM_UNIXTIME(click.clickTime) as at,
					bevomedia_tracker_clicks_optional.data as optional,
					afs.conversions as conv
				FROM 
					(
						(
							(
								(
									(
										(
											(bevomedia_tracker_clicks click join bevomedia_tracker_ips ip on((click.ipId = ip.id))) 
											join bevomedia_tracker_landing_pages lp on ((click.landingPageId = lp.id))
										) 
										left join bevomedia_ppc_advariations creative on ((click.creativeId = creative.apiAdId))
									)
									left join bevomedia_ppc_adgroups adgroup on((creative.adGroupId = adgroup.id))
								) 
									left join bevomedia_ppc_campaigns campaign on (((adgroup.campaignId = campaign.id) and (campaign.user__id = click.user__id)))
							) 
							
							left join bevomedia_keyword_tracker_keywords bid_keyword on((click.bidKeywordId = bid_keyword.id))
							
						) 
						
						left join bevomedia_keyword_tracker_keywords raw_keyword on((click.rawKeywordId = raw_keyword.id))
					) 
					
					LEFT JOIN bevomedia_user_aff_network_subid afs ON (afs.subId = click.subId AND afs.user__id = click.user__id)
					LEFT JOIN bevomedia_tracker_clicks_optional ON click.subId = bevomedia_tracker_clicks_optional.clickId
					
				WHERE 
					(click.user__id = $userId) AND
					$searchTerms
				GROUP BY click.subId, clickTime
				ORDER BY clicktime desc 
				LIMIT 0, 250
			";
			  
//echo '<pre>'.$Sql; die;
			  
			$r = $this->db->fetchAll($Sql);
			$this->Results = $r;
			return;
		  }
		  
		  $this->Results = array();
		}
		
		Public Function SubIDLookupVisitorInfo()
		{
		    $userId = $this->User->id;
		    $subId = mysql_real_escape_string(@$_GET['subId']);
		  
		    $Sql = "
				SELECT 
					click.user__id AS user__id,
					click.subId AS subId,
					click.clickTime AS clickTime,
					ip.ipAddress AS ipAddress,
					click.referrerUrl AS referrerUrl,
					campaign.name AS campaignName,
					adgroup.name AS adgroupName,
					creative.title AS creativeTitle,
					lp.landingPageUrl AS lp,
					bid_keyword.keyword AS bidKeyword,
					raw_keyword.keyword AS rawKeyword ,
					
					FROM_UNIXTIME(click.clickTime) as at,
					bevomedia_tracker_clicks_optional.data as optional,
					afs.conversions as conv
				FROM 
					(
						(
							(
								(
									(
										(
											(bevomedia_tracker_clicks click join bevomedia_tracker_ips ip on((click.ipId = ip.id))) 
											join bevomedia_tracker_landing_pages lp on ((click.landingPageId = lp.id))
										) 
										left join bevomedia_ppc_advariations creative on ((click.creativeId = creative.apiAdId))
									)
									left join bevomedia_ppc_adgroups adgroup on((creative.adGroupId = adgroup.id))
								) 
									left join bevomedia_ppc_campaigns campaign on (((adgroup.campaignId = campaign.id) and (campaign.user__id = click.user__id)))
							) 
							
							left join bevomedia_keyword_tracker_keywords bid_keyword on((click.bidKeywordId = bid_keyword.id))
							
						) 
						
						left join bevomedia_keyword_tracker_keywords raw_keyword on((click.rawKeywordId = raw_keyword.id))
					) 
					
					LEFT JOIN bevomedia_user_aff_network_subid afs ON (afs.subId = click.subId AND afs.user__id = click.user__id)
					LEFT JOIN bevomedia_tracker_clicks_optional ON click.subId = bevomedia_tracker_clicks_optional.clickId
					
				WHERE 
					(click.user__id = $userId) AND
					(click.subId = '$subId')
				GROUP BY click.subId, clickTime
			";
		  
			$this->click = $this->db->fetchRow($Sql);
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		}
		
		Public Function LandingRotationNewAjax()
		{
			$this->PostComplete = false;

			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			if(isset($_POST) && sizeof($_POST) > 0)
			{
				$label = $_POST['groupLabel'];
				
				$LandingPageGroup = new LandingPageRotatorGroup();
				$GroupID = $LandingPageGroup->Insert($label, $this->User->id);
				foreach($_POST['link'] as $Key=>$Value)
				{
					$LandingPageLink = new LandingPageRotatorLink();
					$LandingPageLink->Insert($Value, $_POST['ratio'][$Key], $GroupID);
				}
				
				$this->PostComplete = true;

				$this->LandingPageLinkID = $GroupID;
				$LandingPageGroups = new LandingPageRotatorGroup();
				$this->LandingPageGroups = $LandingPageGroups->GetAllForUser($this->User->id);
				foreach($this->LandingPageGroups as $LandingPageGroup)
				{
					$LandingPageGroup->PopulateLandingPages();
				}			
			}
		}
		
		Public Function Import202()
		{
			global $userId;
			$userId = $this->User->id;
			
			function resultToArray($result)
		    {
		        if (!$result)
		        {
		            return array();    
		        }
		        
		        $array = array();
		        while ($row = mysql_fetch_object($result))
		        {
		            $array[] = $row;   
		        }   
		        
		        return $array;
		    }
		    
		    function insertIpAddress($ipAddress)
		    {
		
		        $ipAddress = mysql_real_escape_string($ipAddress);
		        
		        $sql = "SELECT
		                    id
		                FROM
		                    bevomedia_tracker_ips
		                WHERE
		                    (bevomedia_tracker_ips.ipAddress = '{$ipAddress}')        
		                "; 
		        $result = mysql_query($sql);
		        if (mysql_num_rows($result)>0)
		        {
		            $result = resultToArray($result);
		            $result = $result[0];
		            return $result->id;
		        } else
		        {
		            $sql = "INSERT INTO bevomedia_tracker_ips (ipAddress) VALUES ('{$ipAddress}') ";
		            $result = mysql_query($sql);
		            return mysql_insert_id();
		        }
		    }
		    
		    function insertCampaign($name, $type = 4)
		    {
		        global $userId;
		        		        
		        $name = trim($name);
		        
		        $sql = "SELECT id FROM bevomedia_ppc_campaigns WHERE (user__id = {$userId}) AND (name = '{$name}')";
		        $result = mysql_query($sql);
		        if (mysql_num_rows($result)>0) {
		            $id = mysql_fetch_object($result);
		            return $id->id;      
		        } else {        
		            mysql_query('INSERT INTO bevomedia_ppc_campaigns (user__id,ProviderType,AccountID,Name) VALUES ('. $userId . ', '.$type. ', 0, "' . $name . '")') or die(mysql_error());
		            return mysql_insert_id();
		        }
		    }
		    
		    function insertAdGroup($name, $cID)
		    {
		        global $userId;
		        		        
		        $name = trim($name);
		        
		        $sql = "SELECT id FROM bevomedia_ppc_adgroups WHERE (CampaignID = $cID) AND (name = '{$name}') ";
		        $result = mysql_query($sql);
		        if (mysql_num_rows($result)>0) {
		            $id = mysql_fetch_object($result);
		            return $id->id;      
		        } else {         
		            mysql_query('INSERT INTO bevomedia_ppc_adgroups (CampaignID, Name) VALUES ('. $cID . ', "' . $name . '")');
		            return mysql_insert_id();
		        }
		    }
		    
		    function insertAdVar($name, $agID)
		    {
		        global $userId;
		        		        
		        $name = trim($name);
		        
		        $sql = "SELECT id FROM bevomedia_ppc_advariations WHERE (adGroupId = $agID) AND (title = '{$name}') ";
		        $result = mysql_query($sql);
		        if (mysql_num_rows($result)>0) {
		            $id = mysql_fetch_object($result);
		            return $id->id;      
		        } else {
		            mysql_query('INSERT INTO bevomedia_ppc_advariations (adGroupId, title, apiAdId) VALUES ('. $agID . ',  "' . $name . '", 1)');
		            $id = mysql_insert_id();
		            mysql_query("UPDATE bevomedia_ppc_advariations SET apiAdId = {$id} WHERE id = {$id}");
		            return $id;
		        }
		    }
		    
		    function insertKeyword()
		    {
		        global $userId;
		        
		        $sql = "SELECT id FROM bevomedia_keyword_tracker_keywords WHERE (keyword = '') ";
		        $result = mysql_query($sql);
		        if (mysql_num_rows($result)>0) {
		            $id = mysql_fetch_object($result);
		            return $id->id;      
		        } else {
		            mysql_query("INSERT INTO bevomedia_keyword_tracker_keywords (keyword) VALUES ('')");
		            return mysql_insert_id();
		        }
		    }
		    
		    function insertLandingPage()
		    {
		        global $userId;
		        
		        $sql = "SELECT id FROM bevomedia_tracker_landing_pages WHERE (user__id = {$userId}) AND (landingPageUrl = 'Unknown') ";
		        $result = mysql_query($sql);
		        if (mysql_num_rows($result)>0) {
		            $id = mysql_fetch_object($result);
		            return $id->id;      
		        } else {
		            mysql_query("INSERT INTO bevomedia_tracker_landing_pages (user__id, landingPageUrl) VALUES ({$userId}, 'Unknown')");
		            return mysql_insert_id();
		        }
		    }
			
			
			if (isset($_POST['Upload']))
			{
			    $count = 0;
			    if (($handle = fopen($_FILES['File']['tmp_name'], "r")) !== FALSE) {
			        
			        while (($data = fgetcsv($handle, 9999, "\t")) !== FALSE) {
			     
			            if ($count++==0) {
			            	
			            	if (count($data)<16) {
			            		$this->Error = 'Wrong file format. Please try again.';
			            		break;
			            	}
			            	
			            	continue;
			            }
			            
			            $subId = 'P'.mysql_real_escape_string($data[0]);
			            $time = strtotime($data[1]);
			            $ipAddress = mysql_real_escape_string($data[7]);
			            $offerName = mysql_real_escape_string($data[8]);
			            $referrerUrl = mysql_real_escape_string($data[10]);
			            
			            $ipAddressId = insertIpAddress($ipAddress);
			            $campaignId = insertCampaign($offerName);
			            $adGroupId = insertAdGroup("ImportAdVar", $campaignId);
			            $adVarId = insertAdVar("ImportAdVar", $adGroupId);
			            $landingPageId = insertLandingPage();
			            $keywordId = insertKeyword();
			            
			            $clickDate = date('Y-m-d', $time);
			            
			            
			            $sql = "INSERT INTO bevomedia_tracker_clicks (subId, user__id, ipId, creativeId, referrerUrl, clickDate, clickTime, rawKeywordId, bidKeywordId, landingPageId) 
			                    VALUES ('{$subId}', {$userId}, $ipAddressId, $adVarId, '{$referrerUrl}', '{$clickDate}', $time, $keywordId, $keywordId, $landingPageId) 
			                    ";
			                        
			            mysql_query($sql);
			        }
			        
			        $this->Success = 'Stats successfully uploaded.';
			        
			        fclose($handle);
			    }
			}
			
		}
		
		Public Function Geoparting()
		{
			$this->DateRange = (isset($_GET['DateRange'])?$_GET['DateRange']:date('m/d/Y'));
			
			$this->data = array();
			
			if (isset($_GET['submit']))
			{
				$this->data = $this->GeopartingData();
			}
			
		}

		Public Function GeopartingData()
		{
			$campaign = isset($_GET['campaign'])?$_GET['campaign']:0;
			$adGroup = isset($_GET['adGroup'])?$_GET['adGroup']:0;
			$groupBy = isset($_GET['groupBy'])?$_GET['groupBy']:'country';
			
			$dateRange = isset($_GET['DateRange'])?$_GET['DateRange']:'';
			
			$startDate = $endDate = '';
			if (strstr($dateRange, ' - ')) {
				$dates = explode(' - ', $dateRange);
				$startDate = $dates[0];
				$endDate = $dates[1];
			} else {
				$startDate = $endDate = $dateRange;
			}
			
			if ($startDate=='') return array();
			
			$startDate = date('Y-m-d', strtotime($startDate));
			$endDate = date('Y-m-d', strtotime($endDate));
			
			$sql = "SELECT
						bevomedia_tracker_clicks.id, 
						bevomedia_tracker_clicks.ipId,
						bevomedia_tracker_ips.ipAddress,
						bevomedia_tracker_ips.ipLocationID
					FROM
						bevomedia_tracker_clicks
						LEFT JOIN bevomedia_ppc_advariations creative ON (bevomedia_tracker_clicks.creativeId = creative.apiAdId)
						LEFT JOIN bevomedia_ppc_adgroups adgroup ON (creative.adGroupId = adgroup.id)
					    LEFT JOIN bevomedia_ppc_campaigns campaign ON (
					        (adgroup.campaignId = campaign.id) AND (campaign.user__id = bevomedia_tracker_clicks.user__id)
				        ),
						bevomedia_tracker_ips
					WHERE
						(bevomedia_tracker_ips.id = bevomedia_tracker_clicks.ipId) AND
						(bevomedia_tracker_clicks.clickDate BETWEEN DATE(?) AND DATE(?)) AND 
						(bevomedia_tracker_clicks.user__id = ?)
					";
			
			$sqlParameters = array($startDate, $endDate, $this->User->id);
			
			if ($campaign != 0) {
			    $sql .= "\n AND (campaign.id = ?) ";
			    $sqlParameters[] = $campaign;
			}
			if ($adGroup != 0) {
			    $sql .= "\n AND (adgroup.id = ?) ";
			    $sqlParameters[] = $adGroup;
		    }
			
			$data = $this->db->fetchAll($sql, $sqlParameters);
			
			$results = array();
			foreach ($data as $key => $item)
			{
				if ($item->ipLocationID==0) {
					$sql = "SELECT ID, COUNTRY_NAME, REGION, CITY FROM `ip_location` where `IP_TO` >=INET_ATON(?) limit 1;";
					$ipAddress = $this->db->fetchRow($sql, array($item->ipAddress));
					if ($ipAddress) {
						$data[$key]->COUNTRY_NAME = $ipAddress->COUNTRY_NAME;
						$data[$key]->REGION = $ipAddress->REGION;
						$data[$key]->CITY = $ipAddress->CITY;
						
						$updateArr = array( 'ipLocationID' => $ipAddress->ID );
						$updateCount = $this->db->update('bevomedia_tracker_ips', $updateArr, ' id ='.$item->id);
						if (!$updateCount) {
						    $insertArr = $updateArr;
						    $insertArr['ipAddress'] = $item->ipAddress;
						    $insertCount = $this->db->insert('bevomedia_tracker_ips', $insertArr);
					    }
					} else {
						$data[$key]->COUNTRY_NAME = '';
						$data[$key]->REGION = '';
						$data[$key]->CITY = '';
					}
				} else {
					$sql = "SELECT ID, COUNTRY_NAME, REGION, CITY FROM `ip_location` where ID = ? limit 1;";
					$ipAddress = $this->db->fetchRow($sql, array($item->ipLocationID));
					
					$data[$key]->COUNTRY_NAME = $ipAddress->COUNTRY_NAME;
					$data[$key]->REGION = $ipAddress->REGION;
					$data[$key]->CITY = $ipAddress->CITY;
				}
				
				
				if ($groupBy=='city') {
					@$results[$data[$key]->COUNTRY_NAME.','.$data[$key]->REGION.','.$data[$key]->CITY]++;
				} else
				if ($groupBy=='region') {
					@$results[$data[$key]->COUNTRY_NAME.','.$data[$key]->REGION]++;
				} else
				{
					@$results[$data[$key]->COUNTRY_NAME]++;
				}
				
				ksort($results);
				/*
				if ($groupBy=='city') {
					@$results[$data[$key]->COUNTRY_NAME][$data[$key]->REGION][$data[$key]->CITY]++;
				} else
				if ($groupBy=='region') {
					@$results[$data[$key]->COUNTRY_NAME][$data[$key]->REGION]++;
				} else
				{
					@$results[$data[$key]->COUNTRY_NAME]++;
				}
				*/
				
			}
			
			$output = array('results'=>$results, 'data'=>$data);
			return $output;
		}
	}

?>
