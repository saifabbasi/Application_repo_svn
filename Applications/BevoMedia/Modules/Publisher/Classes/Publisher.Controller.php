<?php

	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/ShowMovie.include.php');
	
	Class PublisherController extends ClassComponent
	{
		Public $GUID		= NULL;
		
		Protected $_db 		= false;
		
		Public Function __construct()
		{
			parent::GenerateGUID();
			$this->{'PageHelper'} = new PageHelper();
			$this->{'PageDesc'} = new PageDesc();
			
			$useApiKey = false;
			if(isset($_GET['apiKey']))
			{
			    $useApiKey = true;
                $user = new User();
                $userId = $user->getUserIdByAPIKey($_GET['apiKey']);
                $user->getInfo(intval($userId));
                if(empty($userId))
                {
                    echo '<div style="background: #fff; color: #F00;">';
                    echo '<br /><br />';
                    echo "This service requires a BevoMedia.com account; you haven't setup a BevoMedia.com account to sync with this selfhosted account.";
                    echo '<br /><br />';
                    echo '</div>';
                    exit;
                }
                $this->User = $user;
                $_SESSION['User']['ID'] = $user->id;
                $_SESSION['apiKey'] = $_GET['apiKey'];
				$this->isApi = true;
				$this->isApiStr = "&apiKey=".$_GET['apiKey'];
			}
			
			if(!$useApiKey && (!isset($_SESSION['User']) || !intval($_SESSION['User']['ID'])))
			{
				$_SESSION['loginLocation'] = $_SERVER['REQUEST_URI'];
				header('Location: /BevoMedia/Index/');
				exit;
			}
            if(!$useApiKey)
            {
			    $user = new User();
			    $user->getInfo($_SESSION['User']['ID']);
			    $this->{'User'} = $user;
            }
			$this->_db = Zend_Registry::get('Instance/DatabaseObj');
			$this->db = Zend_Registry::get('Instance/DatabaseObj');
			
			Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
			
			if (Zend_Registry::get('Instance/Function')=='OvernightAffiliate') 
			{
				if ( ($user->vaultID==0) )
				{
					header('Location: /BevoMedia/User/AddCreditCard.html');
					die;
				}
			}
		}
		
		Public Function CreatePPCSubmit()
		{
			if(isset($_GET['DELETE']) && !empty($_GET['DELETE']))
			{
				require_once(PATH . 'CreatePPC.class.php');
				$CreatePPC = new CreatePPC(false);
				$CreatePPC->delete($_GET['DELETE']);
				header('Location: CreatePPC.html');
				exit;
			}

			if(!empty($_POST))
			{
				$json = $_POST['jsonObj'];
			}
			elseif(isset($_GET['resubmit_id']))
			{
				
				if($_GET['resubmit_id']) {
				    $_db = Zend_Registry::get('Instance/DatabaseObj');
				    $ID = intval($_GET['resubmit_id']);
			        $Sql = "SELECT id, label, created, json FROM bevomedia_createppc_session WHERE id=$ID";
			        $Row = $_db->fetchRow($Sql);
			        $json = $Row->json;
				}
			} else {
			    header('Location: CreatePPC.html');
			}
			require_once(PATH . 'CreatePPC.class.php');
				
			$CreatePPC = new CreatePPC($json);
			
			if(isset($_GET['saveSession']))
			{
				$label = '';
				if(isset($_POST['label']) && !empty($_POST['label']))
				{
					$label = $_POST['label'];
				}
				$jsonObj = $CreatePPC->save($this->User->id, $label);
				header('Location: CreatePPCSaved.html');
				exit;
			}
			if(isset($_POST['autosave']) && $_POST['autosave'] == 'on')
			{
				$jsonObj = $CreatePPC->save($this->User->id, 'Autosave');
			}
			
			$jsonObj = $CreatePPC->queueInsert();
			$jsonObj->processDelete();
			$this->jsonObj = $jsonObj;
		}
		
		Public Function CreatePPC_CrossPostExistingCampaign()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$ppc_campaign_id = $_GET['ID'];
			$User = $this->User;
			$this->User = $User;

			$this->AdwordsResults = $User->GetAllAccountsAdwords();
			$this->YahooResults = $User->GetAllAccountsYahoo();
			$this->MSNAdCenterResults = $User->GetAllAccountsMSN();
			//new create ppc class
			require_once(PATH . 'CreatePPC.class.php');
			$CreatePPC = new CreatePPC(false);
				
			//provider int type to class name conversion
			$this->providerSet = array(1=>'Adwords', 2=>'Yahoo',3=>'MSNAdCenter');
		    $this->UPPER_PROVIDERS = array(1=>'ADWORDS', 2=>'YAHOO', 3 => 'MSN');
			//select campaign from local tables
			$ppc_campaign = $this->_db->fetchRow("SELECT apiCampaignId, providerType, accountId, name FROM bevomedia_ppc_campaigns WHERE id = {$ppc_campaign_id}");
			//generate class name from retrieved row
			$class = 'Accounts_' . $this->providerSet[$ppc_campaign->providerType];
			$account = new $class();
			//retrieve credentials and initiate the class
			$account->getInfo($ppc_campaign->accountId);
            $this->Campaign = $account->getCampaign($ppc_campaign_id);
            $allAdGroups = $account->getAdGroups($ppc_campaign_id);
            $this->AdGroups = array();
            foreach($allAdGroups as $ag)
            {
              $id = $ag->id;
              $ag = $account->getAdGroup($ag->id); // includes the negative keywords
              $ag->Keywords = $account->getKeywords($id);
              $ag->Variations = $account->getAdVariations($id);
              $this->AdGroups[] = $ag;
            }
		}
		
		Public Function CreatePPC()
		{
			$User = $this->User;
			$this->User = $User;

			$this->AdwordsResults = $User->GetAllAccountsAdwords();
			$this->YahooResults = $User->GetAllAccountsYahoo();
			$this->MSNAdCenterResults = $User->GetAllAccountsMSN();
			
			$this->campaignSet = array('Adwords'=>'ADWORDS', 'Yahoo'=>'YAHOO','MSNAdCenter'=>'MSN');
			$this->adgroupSet = array();
			$this->advarSet = array();
			$this->keywordSet = array();
			
			$this->campaignProviders = $this->adgroupProviders = array();
			
			foreach($this->campaignSet as $kCS=>$cS)
			{
				$Temp = array();
				$TempStr = $kCS.'Results';
				$TempClass = 'Accounts_'.$kCS;
				foreach($this->{$TempStr} as $Key=>$Value)
				{
					$this->$TempStr[$Key]->{'Account'} = new $TempClass($this->User->id);
					$this->$TempStr[$Key]->Account->id = $Value->id;
					$Temp[$Value->id] = array();
					foreach($this->$TempStr[$Key]->Account->getCampaigns() as $C)
					{
						$this->campaignProviders[$C->id] = $cS;
						$Temp[$C->accountId][] = $C;
						$this->adgroupSet[$C->id] = array();
						foreach($this->$TempStr[$Key]->Account->getAdGroups($C->id) as $A)
						{
							$this->adgroupProviders[$A->id] = $cS;
							$this->adgroupSet[$A->campaignId][] = $A;
							
							$this->advarSet[$A->id] = array();
							foreach($this->$TempStr[$Key]->Account->getAdVariations($A->id) as $AV)
							{
								$this->advarSet[$AV->adGroupId][] = $AV;
							}
							
							$this->keywordSet[$A->id] = array();
							foreach($this->$TempStr[$Key]->Account->getKeywords($A->id) as $KW)
							{
								$this->keywordSet[$KW->adGroupId][] = $KW;
							}
						}
					}
				}
				
				$this->{$cS} = $Temp;
			}
			
			
			$AdwC = new Adwords_Countries();
			$Countries = $AdwC->GetAllCountries();
			$this->Countries = $Countries;
			
			//$this->keywordSet = $this->advarSet = array();
			
			$_db = Zend_Registry::get('Instance/DatabaseObj');
			$Sql = "SELECT id, label, created, json FROM bevomedia_createppc_session WHERE (user__id = {$this->User->id} AND deleted = '0') ORDER BY created DESC LIMIT 0,3";
			$Rows = $_db->fetchAll($Sql);
			if($Rows)
			{
				require_once(PATH . 'CreatePPC.class.php');
				foreach($Rows as $Row)
				{
					$Temp = new CreatePPC($Row->json);
					$Row->Content = $Temp->summaryInfo();
					$Row->ContentExtended = $Temp->summaryInfoExtended();
					$Row->JSON = str_replace('\"', '\\\"', $Row->json);
					$this->savedSessions[] = $Row;
				}
			}else{
				$this->savedSessions = false;
			}
		}
		
		Public Function SelectAdwordsGeoTargets()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$AdwC = new Adwords_Countries();
			$Countries = $AdwC->GetAllCountries();
			$this->Countries = $Countries;
		}
		
		Public Function _RateNetwork()
		{
			$_db = Zend_Registry::get('Instance/DatabaseObj');
			$Sql = "SELECT id FROM bevomedia_user_aff_network_rating WHERE user__id = {$this->User->id} AND network__id = $_GET[ID]";
			$Row = $_db->fetchRow($Sql);
			if(!$Row)
			{
			}else{
				$Sql = "DELETE FROM bevomedia_user_aff_network_rating WHERE id = $Row->id";
				$_db->exec($Sql);
			}
			$Sql = "INSERT INTO bevomedia_user_aff_network_rating (user__id, network__id, rating) VALUES ({$this->User->id}, $_GET[ID], $_GET[Rating])";
			$_db->exec($Sql);
			header('Location: /BevoMedia/Index/CloseShadowbox.html?goto={PARENT}');
			exit;
		}
		
		Public Function Index()
		{
			$db = Zend_Registry::get('Instance/DatabaseObj');
			$query = "SELECT n.*, u.status FROM bevomedia_aff_network AS n LEFT JOIN bevomedia_user_aff_network AS u ON n.id = u.network__id AND u.user__id = {$this->User->id} WHERE n.model = 'CPA' AND n.isValid = 'Y' ORDER BY n.title";
			$this->CpaNetworks = $db->fetchAll($query);
			foreach($this->CpaNetworks as $Key=>$Network)
			{
				$Sql = "SELECT AVG(rating) AS RATING FROM bevomedia_user_aff_network_rating WHERE network__id = {$Network->id} GROUP BY network__id";
				$Row = $db->fetchRow($Sql);
				if(!$Row)
					$Row = 0;
				else
					$Row = $Row->RATING;
				$this->CpaNetworks[$Key]->rating = $Row;
				
				if ($this->CpaNetworks[$Key]->title=='FireLead')
				{
					$this->FireLead = $this->CpaNetworks[$Key];
				}
				
				if ($this->CpaNetworks[$Key]->title=='EpicDirect')
				{
					$this->EpicDirect = $this->CpaNetworks[$Key];  
				}
				
				if ($this->CpaNetworks[$Key]->title=='EWA')
				{
					$this->Network1 = $this->CpaNetworks[$Key];  
				}
				
				if ($this->CpaNetworks[$Key]->title=='Blue Global Media')
				{
					$this->Network2 = $this->CpaNetworks[$Key];  
				}
				
				if ($this->CpaNetworks[$Key]->title=='CrushAds')
				{
					$this->Network3 = $this->CpaNetworks[$Key];  
				}
			}
			
			$RtArr = array('Adwords', 'Yahoo', 'MSN', 'Analytics');
			foreach($RtArr as $Key=>$Value)
			{
				$Temp = new stdClass();
				$Temp->ID = $Key+1;
				$Temp->Rating = 0;
				$Sql = "SELECT AVG(rating) AS RATING FROM bevomedia_user_aff_network_rating WHERE network__id = {$Temp->ID} GROUP BY rating";
				$Row = $db->fetchRow($Sql);
				if($Row)
					$Temp->rating = $Row->RATING;
				$this->{$Value . 'Rating'} = $Temp;
			}
			
		}

		Public Function ApplyAdd()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');

			$network_id = (int)$_GET['network'];
			
			$db = Zend_Registry::get('Instance/DatabaseObj');
			$query = "SELECT * FROM bevomedia_aff_network WHERE id = {$network_id}";
			$this->network = $db->fetchRow($query);
		}

		Public Function EditNetwork()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');

			$network_id = (int)$_GET['network'];

			$db = Zend_Registry::get('Instance/DatabaseObj');
			$query = "SELECT * FROM bevomedia_aff_network WHERE id = {$network_id}";
			$this->Network = $db->fetchRow($query);
			$this->Network->passwordLabel = 'Password';
			
			if(isset($_GET['delete']))
			{
			  $db->delete('bevomedia_user_aff_network', 'user__id='.$this->User->id.' and network__id='.(int)$_GET['delete']);
			  $this->message = "Account deleted successfully!";

			}
			if($_POST)
			{
				if (isset($_POST['otherid'])) {
					$_POST['otherid'] = trim($_POST['otherid']);
				}
				
				$data = array(
					'network__id' => $network_id,
					'user__id' => $this->User->id,
					'loginId' => isset($_POST['loginid']) ? $_POST['loginid'] : '',
					'password' => isset($_POST['password']) ? $_POST['password'] : '',
					'otherId' => isset($_POST['otherid']) ? $_POST['otherid'] : '',
					'status' => 3
					);
				$where = array(
					'network__id' => $network_id,
					'user__id' => $this->User->id,
					);

				$query = "SELECT * FROM bevomedia_user_aff_network WHERE network__id = {$network_id} AND user__id = {$this->User->id}";
				$this->UserNetwork = $db->fetchRow($query);
				if(empty($this->UserNetwork))
				{
					// Insert
					$db->insert('bevomedia_user_aff_network', $data);
				}
				else
				{
					// Update
					$db->update('bevomedia_user_aff_network', $data, 'id = ' . $this->UserNetwork->id);
				}

				$this->message = 'Changes saved.';
			}

			$query = "SELECT * FROM bevomedia_user_aff_network WHERE network__id = {$network_id} AND user__id = {$this->User->id}";
			$this->UserNetwork = $db->fetchRow($query);

			if(empty($this->UserNetwork))
			{
				$this->UserNetwork = new stdClass;
				$this->UserNetwork->loginId = '';
				$this->UserNetwork->password = '';
				$this->UserNetwork->otherId = '';
			}
		  if($this->User->isSelfHosted() == '1' && !empty($this->message))
				$this->message .= " Changes will take up to an hour to appear in your selfhost pages.";
		}

		Public Function NetworkRedirect()
		{
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
		}
		
		Public Function YahooSM()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		}
		
		Public Function YahooAPI()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$Yahoo = new Accounts_Yahoo();
			$Yahoo->user__id = $this->User->id;
			
			if(isset($_GET['EnableDailyUpdate']))
				$Yahoo->EnableAccount($_GET['EnableDailyUpdate']);
			
			if(isset($_GET['DisableDailyUpdate']))
				$Yahoo->DisableAccount($_GET['DisableDailyUpdate']);
				
			if(isset($_POST['adwordsAddAccountSubmit']))
			{
				$invalid = false;
					
				foreach($_POST as $k=>$v)
				{
					$this->{$k.'FormValue'} = $v;
					if($v == '')
					{
						$this->{$k.'Invalid'} = true;
						$invalid = true;
					}
				}
				
				if(!$invalid)
				{
				  require_once(PATH.'QueueTools.include.php');
					$Insert = $_POST;
					$Insert['User_ID'] = $this->User->id;
					$ID = $Yahoo->Insert($Insert);
					$this->InstallSuccess = true;
					$this->newId = $ID;
					$Yahoo->getInfo($ID);
					addYahooAccountToQueue($ID);
					$Yahoo->VerifyAccountAPI();
				}
			}
			
			if(isset($_GET['VerifyEmail']))
			{
				$Yahoo->getInfo($_GET['VerifyEmail']);
				$this->Verified = $Yahoo->VerifyAccountAPI();
			}
			
			if(isset($_GET['EditEmail']))
			{
				$this->editEmail = true;
				$Yahoo->getInfo($_GET['EditEmail']);
				foreach($Yahoo as $k=>$v)
				{
					$this->{$k.'FormValue'} = $v;
				}
			}
			
			if(isset($_GET['DeleteEmail']))
				$Yahoo->Delete($_GET['DeleteEmail']);
			
			
			if(isset($_POST['adwordsEditAccountSubmit']))
			{
				unset($_POST['adwordsEditAccountSubmit']);
				$Yahoo->Update($_POST);
				header('Location: YahooAPI.html');
				exit;
			}
			
			$this->InstalledAccounts = $Yahoo->GetInstalledAccounts();
			$this->NotInstalled = $Yahoo->GetNotInstalled();
			$this->DisabledAccounts = $Yahoo->GetDisabledAccounts();
		}
		
		Public Function YahooSelectXML()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$Yahoo = new Accounts_Yahoo();
			$Yahoo->User_ID = $this->User->id;
			
			$this->InstalledAccounts = $Yahoo->GetInstalledAccounts();
			$this->NotInstalled = $Yahoo->GetNotInstalled();
			$this->DisabledAccounts = $Yahoo->GetDisabledAccounts();
		}
		
		Public Function AdwordsSelectXML()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$Adwords= new Accounts_Adwords();
			$Adwords->User_ID = $this->User->id;
			
			$this->InstalledAccounts = $Adwords->GetInstalledAccounts();
			$this->NotInstalled = $Adwords->GetNotInstalled();
			$this->DisabledAccounts = $Adwords->GetDisabledAccounts();
		}
		
		Public Function MSNSelectXML()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$Adwords= new Accounts_MSNAdCenter();
			$Adwords->User_ID = $this->User->id;
			
			$this->InstalledAccounts = $Adwords->GetInstalledAccounts();
			$this->NotInstalled = $Adwords->GetNotInstalled();
			$this->DisabledAccounts = $Adwords->GetDisabledAccounts();
		}
		
		Public Function AdwordsAdURLFormat()
		{
			$AdHelper = new AdHelperPPC($this->User->id);
			$Ads = $AdHelper->LoadAdsByProvider(1);
			$Ads = $AdHelper->FormatRowsAsCampaignArray($Ads);
			
			$this->Ads = $Ads;
		}
		
		Public Function MSNAdURLFormat()
		{
			$AdHelper = new AdHelperPPC($this->User->id);
			$Ads = $AdHelper->LoadAdsByProvider(3);
			$Ads = $AdHelper->FormatRowsAsCampaignArray($Ads);
			
			$this->Ads = $Ads;
		}
		
		Public Function AdGroupAdURLFormat()
		{
			if(isset($_GET['ID']))
			{
				$ProviderImage = array('-', 'adwordswhite.jpg', '-yahoo-', 'adcenter.jpg');
				$AdHelper = new AdHelperPPC($this->User->id);
				$Ads = $AdHelper->LoadAdsByAdGroup($_GET['ID']);
				if(sizeof($Ads)>0)
				{
					$this->AdGroupName = $Ads[0]->AdGroupName;
					$this->AdGroupID = $Ads[0]->adGroupId;
					$this->CampaignName = $Ads[0]->CampaignName;
					$this->ProviderImage = $ProviderImage[$Ads[0]->providerType];
					$this->ProviderType = $Ads[0]->providerType;
					$Ads = $AdHelper->FormatRowsAsCampaignArray($Ads);
				}
				$this->Ads = $Ads;
			}else{
				$this->Ads = array();
			}
		}
		
		Public Function GoogleAdwords()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		}
		
		Public Function GoogleAdwordsAPI()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$Adwords = new Accounts_Adwords();
			$Adwords->user__id = $this->User->id;
			
			if(isset($_GET['EnableDailyUpdate']))
				$Adwords->EnableAccount($_GET['EnableDailyUpdate']);
			
			if(isset($_GET['DisableDailyUpdate']))
				$Adwords->DisableAccount($_GET['DisableDailyUpdate']);
				
			if(isset($_POST['adwordsAddAccountSubmit']))
			{
				$invalid = false;
					
				foreach($_POST as $k=>$v)
				{
					$this->{$k.'FormValue'} = $v;
					if($v == '')
					{
						$this->{$k.'Invalid'} = true;
						$invalid = true;
					}
				}
				
				if(!$invalid)
				{
					$this->CreditRemaining = $this->User->GetAdwordsAPIBalance();
					$Insert = $_POST;
					$Insert['user__id'] = $this->User->id;
					$ID = $Adwords->Insert($Insert);
					$this->InstallSuccess = true;
					$this->newId = $ID;
					$Adwords->getInfo($ID);
				  require_once(PATH.'QueueTools.include.php');
					$Adwords->VerifyAccountAPI();
					addAdwordsAccountToQueue($ID);
				}
			}
			
			if(isset($_GET['VerifyEmail']))
			{
				$Adwords->getInfo($_GET['VerifyEmail']);
				$this->Verified = $Adwords->VerifyAccountAPI();
			}
			
			if(isset($_GET['EditEmail']))
			{
				$this->editEmail = true;
				$Adwords->getInfo($_GET['EditEmail']);
				foreach($Adwords as $k=>$v)
				{
					$this->{$k.'FormValue'} = $v;
				}
			}
			
			if(isset($_GET['DeleteEmail']))
				$Adwords->Delete($_GET['DeleteEmail']);
			
			
			if(isset($_POST['adwordsEditAccountSubmit']))
			{
				unset($_POST['adwordsEditAccountSubmit']);
				$Adwords->Update($_POST);
				header('Location: GoogleAdwordsAPI.html');
				exit;
			}
			
			$this->InstalledAccounts = $Adwords->GetInstalledAccounts();
			$this->NotInstalled = $Adwords->GetNotInstalled();
			$this->DisabledAccounts = $Adwords->GetDisabledAccounts();
		}
		
		Public Function GoogleAnalytics()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		}
		
	
		Public Function GoogleAnalyticsAPI()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$Analytics = new Accounts_Analytics();
			$Analytics->user__id = $this->User->id;
			
			if(isset($_GET['EnableDailyUpdate']))
				$Analytics->EnableAccount($_GET['EnableDailyUpdate']);
			
			if(isset($_GET['DisableDailyUpdate']))
				$Analytics->DisableAccount($_GET['DisableDailyUpdate']);
				
			if(isset($_POST['analyticsAddAccountSubmit']))
			{
				$invalid = false;
					
				foreach($_POST as $k=>$v)
				{
					$this->{$k.'FormValue'} = $v;
					if($v == '')
					{
						$this->{$k.'Invalid'} = true;
						$invalid = true;
					}
				}
				
				if(!$invalid)
				{
					$Insert = $_POST;
					$Insert['user__id'] = $this->User->id;
					$Analytics->Insert($Insert);
					$this->InstallSuccess = true;
				}
			}
			
			if(isset($_GET['VerifyEmail']))
			{
				$Analytics->getInfo($_GET['VerifyEmail']);
				$this->Verified = $Analytics->VerifyAccountAPI();
			}
			
			if(isset($_GET['EditEmail']))
			{
				$this->editEmail = true;
				$Analytics->getInfo($_GET['EditEmail']);
				foreach($Analytics as $k=>$v)
				{
					$this->{$k.'FormValue'} = $v;
				}
			}
			
			if(isset($_GET['DeleteEmail']))
				$Analytics->Delete($_GET['DeleteEmail']);
			
			
			if(isset($_POST['analyticsEditAccountSubmit']))
			{
				unset($_POST['analyticsEditAccountSubmit']);
				$Analytics->Update($_POST);
				header('Location: GoogleAnalyticsAPI.html');
				exit;
			}
			
			$this->InstalledAccounts = $Analytics->GetInstalledAccounts();
			$this->NotInstalled = $Analytics->GetNotInstalled();
			$this->DisabledAccounts = $Analytics->GetDisabledAccounts();
		}
		
		Public Function MSNAdCenter()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		}
		
		Public Function MSNAdCenterAPI()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$MSN = new Accounts_MSNAdCenter();
			$MSN->user__id = $this->User->id;
			
			if(isset($_GET['EnableDailyUpdate']))
				$MSN->EnableAccount($_GET['EnableDailyUpdate']);
			
			if(isset($_GET['DisableDailyUpdate']))
				$MSN->DisableAccount($_GET['DisableDailyUpdate']);
				
			if(isset($_POST['adwordsAddAccountSubmit']))
			{
				$invalid = false;

				foreach($_POST as $k=>$v)
				{
					$this->{$k.'FormValue'} = $v;
					if($v == '')
					{
						$this->{$k.'Invalid'} = true;
						$invalid = true;
					}
				}
				
				if(!$invalid)
				{
				  require_once(PATH.'QueueTools.include.php');
					$Insert = $_POST;
					$Insert['user__id'] = $this->User->id;
					$ID = $MSN->Insert($Insert);
					$this->InstallSuccess = true;
					$this->newId = $ID;
					$MSN->getInfo($ID);
					addMSNAccountToQueue($ID);
					$MSN->VerifyAccountAPI();
				}
			}
			
			if(isset($_GET['VerifyEmail']))
			{
				$MSN->getInfo($_GET['VerifyEmail']);
				$this->Verified = $MSN->VerifyAccountAPI();
			}
			
			if(isset($_GET['EditEmail']))
			{
				$this->EditEmail = true;
				$MSN->getInfo($_GET['EditEmail']);
				foreach($MSN as $k=>$v)
				{
					$this->{$k.'FormValue'} = $v;
				}
			}
			
			if(isset($_GET['DeleteEmail']))
				$MSN->Delete($_GET['DeleteEmail']);
			
			
			if(isset($_POST['adwordsEditAccountSubmit']))
			{
				unset($_POST['adwordsEditAccountSubmit']);
				$MSN->Update($_POST);
				header('Location: MSNAdCenterAPI.html');
				exit;
			}
			
			$this->InstalledAccounts = $MSN->GetInstalledAccounts();
			$this->NotInstalled = $MSN->GetNotInstalled();
			$this->DisabledAccounts = $MSN->GetDisabledAccounts();
		}

		Public Function AdGroupStatsPPC()
		{
			$AdGroupID = $_GET['ID'];
			$DateRange = $this->DateRangeHelper();
			$this->DateRange = $DateRange[0] . '-' . $DateRange[1];
			
			$Stats = new Network_Stats();
			$this->ProviderType = $Stats->GetProviderTypeFromAdGroupID($AdGroupID);
			$this->Provider = $this->GetStringFromProviderType($this->ProviderType);
			$this->AdGroupName = $Stats->GetAdGroupName($AdGroupID);
			$this->KeywordStats = $Stats->GetAllKeywordStatsForAdGroup($AdGroupID, $DateRange[0], $DateRange[1]);

			$AdGroupParents = $Stats->GetParentsForAdGroup($AdGroupID);
			$this->CampaignID = $AdGroupParents->CampaignID;
			$this->CampaignName = $AdGroupParents->CampaignName;
			$this->AccountID = $AdGroupParents->AccountID;
			$this->AccountName = $Stats->GetAccountName($this->AccountID, $this->ProviderType);
			
			$this->AdGroupID = $AdGroupID;
		
			$ContentMatch = $Stats->GetContentMatchStats($AdGroupID, $DateRange[0], $DateRange[1]);
			if($ContentMatch != false)
			{
				$ContentMatch->Name = "{Content Match}";
				$ContentMatch->FormattedName = "<i>&nbsp;Content&nbsp;Match</i>";
				$ContentMatch->CTR = 0;
				$this->KeywordStats[] = $ContentMatch;
			}
			
			usort($this->KeywordStats, array($this, "sortNameAsc"));
						
			if(isset($_GET['sort']))
			{
				$sortType = $_GET['sort'];
				$sortOrder = 'Asc';
				
				if(isset($_GET['sort_order']))
					$sortOrder = $_GET['sort_order'];
					
				usort($this->KeywordStats, array($this, "sort".$sortType.$sortOrder));
			}
		
			$StatsFields = array();
			foreach($Stats->GetEmptyStats(true) as $StatField=>$Zero)
			{
				$StatsFields[] = $StatField;
			}
			$this->KeywordFields = $Stats->GetEmptyStats();
			$this->KeywordStatsTotal = new stdClass();
			foreach($this->KeywordStats as $Key=>$Row)
			{
				foreach($Row as $Item=>$Value)
				{
					$this->KeywordStatsTotal->{$Item} += $Value;
				}
			}
			if(sizeOf($this->KeywordStats))
			{
				$this->KeywordStatsTotal->CTR /= sizeOf($this->KeywordStats);
				$this->KeywordStatsTotal->AvgCPC /= sizeOf($this->KeywordStats);
			}
			
			$AdHelper = new AdHelperPPC($this->User->id);
			$Ads = $AdHelper->LoadAdsByAdGroup($AdGroupID);
			$Ads = $AdHelper->FormatRowsAsCampaignArray($Ads);
			$this->Ads = $Ads;
			$this->FormatURLAdsCount = @count($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized']);
		}

		Public Function AdGroupSearchQueryPPC()
		{
			$AdGroupID = $_GET['ID'];
			$DateRange = $this->DateRangeHelper();
			$this->DateRange = $DateRange[0] . '-' . $DateRange[1];
			
			$Stats = new Network_Stats();
			$this->ProviderType = $Stats->GetProviderTypeFromAdGroupID($AdGroupID);
			$this->AdGroupName = $Stats->GetAdGroupName($AdGroupID);
			$this->QueryStats = $Stats->GetSearchQueryStats($AdGroupID, $DateRange[0], $DateRange[1]);

			$AdGroupParents = $Stats->GetParentsForAdGroup($AdGroupID);
			$this->Provider = $this->GetStringFromProviderType($this->ProviderType);
			$this->CampaignID = $AdGroupParents->CampaignID;
			$this->CampaignName = $AdGroupParents->CampaignName;
			$this->AccountID = $AdGroupParents->AccountID;
			$this->AccountName = $Stats->GetAccountName($this->AccountID, $this->ProviderType);
			
			$AdHelper = new AdHelperPPC($this->User->id);
			$this->Ads = $AdHelper->LoadAdsByAdGroup($AdGroupID);
			
			
			if(isset($_GET['sort']))
			{
				$sortType = $_GET['sort'];
				$sortOrder = 'Asc';
				
				if(isset($_GET['sort_order']))
					$sortOrder = $_GET['sort_order'];
					
				usort($this->AdVarStats, array($this, "sort".$sortType.$sortOrder));
			}
			
			$this->AdGroupID = $AdGroupID;
			
			$AdHelper = new AdHelperPPC($this->User->id);
			$Ads = $AdHelper->LoadAdsByAdGroup($AdGroupID);
			$Ads = $AdHelper->FormatRowsAsCampaignArray($Ads);
			$this->Ads = $Ads;

			$this->FormatURLAdsCount = @count($this->Ads[$this->CampaignName][$this->AdGroupName]['NeedToBeOptimized']);
		}

		Public Function AdGroupAdVariationsPPC()
		{
			$AdGroupID = $_GET['ID'];
			$DateRange = $this->DateRangeHelper();
			$this->DateRange = $DateRange[0] . '-' . $DateRange[1];
			
			$Stats = new Network_Stats();
			$this->ProviderType = $Stats->GetProviderTypeFromAdGroupID($AdGroupID);
			$this->AdGroupName = $Stats->GetAdGroupName($AdGroupID);
			$this->AdVarStats = $Stats->GetAllAdVariationStatsForAdGroup($AdGroupID, $DateRange[0], $DateRange[1]);

			$AdGroupParents = $Stats->GetParentsForAdGroup($AdGroupID);
			$this->Provider = $this->GetStringFromProviderType($this->ProviderType);
			$this->CampaignID = $AdGroupParents->CampaignID;
			$this->CampaignName = $AdGroupParents->CampaignName;
			$this->AccountID = $AdGroupParents->AccountID;
			$this->AccountName = $Stats->GetAccountName($this->AccountID, $this->ProviderType);
			
			$AdHelper = new AdHelperPPC($this->User->id);
			$Ads = $AdHelper->LoadAdsByAdGroup($AdGroupID);
			$FormatURLAds = array();
			if(sizeOf($Ads) > 0)
			{
				foreach($Ads as $Ad)
				{
					if(!$AdHelper->CheckAdURL($Ad->url, $Ad->providerType))
					{
						$FormatURLAds[] = $Ad;
					}
				}
			}
			$this->FormatURLAdsCount = sizeOf($FormatURLAds);
				
					
			if(isset($_GET['sort']))
			{
				$sortType = $_GET['sort'];
				$sortOrder = 'Asc';
				
				if(isset($_GET['sort_order']))
					$sortOrder = $_GET['sort_order'];
					
				usort($this->AdVarStats, array($this, "sort".$sortType.$sortOrder));
			}
			
			$this->AdGroupID = $AdGroupID;
		}

		Public Function _AdGroupStatsChartXML()
		{
			$ID = 1;
			if(isset($_GET['ID']))
				$ID = $_GET['ID'];
				
			$DateRange = $this->GetDefaultDateRange();
			if(isset($_GET['DateRange']))
				$DateRange = $_GET['DateRange'];

			$ChartXML = new ChartXMLHelper();
			$ChartXML->SetDateRange($DateRange);
			if(isset($_GET['Field']))
			{
				$ChartXML->Field = $_GET['Field'];
			}
			if(isset($_GET['Keywords']))
			{
				$ChartXML->StatsShowRows = $_GET['Keywords'];
			}
			$ChartXML->LoadAdGroupStats($ID);
			echo $ChartXML;
			exit;
		}
	
	
		Public Function _CSVExportAccountStatsPPC()
		{
			//*
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=AccountStats.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			//*/print '<pre>';
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
			$this->AccountStatsPPC();
			$this->EchoCSV($this->CampaignStats);
			exit;
		}

		Public Function _CSVExportAdGroupAdVariationsPPC()
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=AdVariationStats.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
			$this->AdGroupAdVariationsPPC();
			$this->EchoCSV($this->AdVarStats);
			exit;
		}

		Public Function _CSVExportAdGroupStatsPPC()
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=AdGroupStats.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
			$this->AdGroupStatsPPC();
			$this->EchoCSV($this->KeywordStats);
			exit;
		}
		
		Public Function _CSVExportCampaignStatsPPC()
		{
			//*
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=CampaignStats.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			//*/print '<pre>';
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
			$this->CampaignStatsPPC();
			$this->EchoCSV($this->AdGroupStats);
			exit;
		}
		
		Public Function CampaignStatsPPC()
		{
			$CampaignID = $_GET['ID'];
			$DateRange = $this->DateRangeHelper();
			$this->DateRange = $DateRange[0] . '-' . $DateRange[1];
			
			$Stats = new Network_Stats();
			$this->ProviderType = $Stats->GetProviderTypeFromCampaignID($CampaignID);
			$this->CampaignName = $Stats->GetCampaignName($CampaignID);

			if(!$this->CampaignName)
				$CampaignID = -1;
				
			$this->AdGroupStats = $Stats->GetAllAdGroupStatsForCampaign($CampaignID, $DateRange[0], $DateRange[1]);
			
			if($CampaignID !== -1)
			{
				$AdGroupParents = $Stats->GetParentsForCampaign($CampaignID);
				$this->Provider = $this->GetStringFromProviderType($this->ProviderType);
				$this->AccountID = $AdGroupParents->AccountID;
				$this->AccountName = $Stats->GetAccountName($this->AccountID, $this->ProviderType);
			}

			$this->CampaignID = $CampaignID;
			
			if(isset($_GET['sort']))
			{
				$sortType = $_GET['sort'];
				$sortOrder = 'Asc';
				
				if(isset($_GET['sort_order']))
					$sortOrder = $_GET['sort_order'];
					
				usort($this->AdGroupStats, array($this, "sort".$sortType.$sortOrder));

			}
			$this->AdGroupFields = $Stats->GetEmptyStats();
			$StatsFields = array();
			foreach($Stats->GetEmptyStats(true) as $StatField=>$Zero)
			{
				$StatsFields[] = $StatField;
			}
			$this->AdGroupStatsTotal = new stdClass();
			foreach($this->AdGroupStats as $Key=>$Row)
			{
				foreach($Row as $Item=>$Value)
				{
					$this->AdGroupStatsTotal->{$Item} += $Value;
				}
			}
			if(sizeOf($this->AdGroupStats))
			{
				$this->AdGroupStatsTotal->CTR /= sizeOf($this->AdGroupStats);
				$this->AdGroupStatsTotal->AvgCPC /= sizeOf($this->AdGroupStats);
			}
		}
		
		Public Function sortImpressionsDesc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->impressions > $b->impressions;
		}
		Public Function sortImpressionsAsc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->impressions < $b->impressions;
		}
		
		Public Function sortClicksDesc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->clicks > $b->clicks;
		}
		Public Function sortClicksAsc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->clicks< $b->clicks;
		}
		
		Public Function sortCTRDesc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->ctr > $b->ctr;
		}
		Public Function sortCTRAsc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->ctr < $b->ctr;
		}
		
		Public Function sortAvgCPCDesc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->avgCpc > $b->avgCpc;
		}
		Public Function sortAvgCPCAsc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->avgCpc < $b->avgCpc;
		}
		
		Public Function sortCostDesc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->cost > $b->cost;
		}
		Public Function sortCostAsc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->cost < $b->cost;
		}
		
		Public Function sortNameDesc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->name > $b->name;
		}
		Public Function sortNameAsc($a, $b)
		{
			if(isset($a->name))
			{
				if($a->name == '{Content Match}')
					return -1;
				if($b->name == '{Content Match}')
					return 1;
			}
			return $a->name < $b->name;
		}
		
		
		
		Public Function _CampaignStatsChartXML()
		{
			$ID = 1;
			if(isset($_GET['ID']))
				$ID = $_GET['ID'];
				
			$DateRange = $this->GetDefaultDateRange();
			if(isset($_GET['DateRange']))
				$DateRange = $_GET['DateRange'];

			$ChartXML = new ChartXMLHelper();
			$ChartXML->SetDateRange($DateRange);
			if(isset($_GET['Field']))
			{
				$ChartXML->Field = $_GET['Field'];
			}
			if(isset($_GET['AdGroups']))
			{
				$ChartXML->StatsShowRows = $_GET['AdGroups'];
			}
			$ChartXML->LoadCampaignStats($ID);
			echo $ChartXML;
			exit;
		}
		
		Public Function PPCManager()
		{
			$this->DefaultDateRange = $this->GetDefaultDateRange();
		}
		
		Public Function _PPCManagerStatsChartXML2()
		{
			$ID = 1;
			if(isset($_GET['ID']))
				$ID = $_GET['ID'];
				
			$DateRange = $this->GetDefaultDateRange();
			if(isset($_GET['DateRange']))
				$DateRange = $_GET['DateRange'];

			$ChartXML = new ChartXMLHelper();
			$ChartXML->SetDateRange($DateRange);
			
			$ChartXML->LoadPPCManagerStats($ID);
			$Out = $ChartXML->getJQueryChartOutput();
			exit;
		}
		
		Public Function _PPCManagerStatsChartXML()
		{
			$ID = 1;
			if(isset($_GET['ID']))
				$ID = $_GET['ID'];
				
			$DateRange = $this->GetDefaultDateRange();
			if(isset($_GET['DateRange']))
				$DateRange = $_GET['DateRange'];

			$ChartXML = new ChartXMLHelper();
			$ChartXML->SetDateRange($DateRange);
			
			$ChartXML->LoadPPCManagerStats($ID);
			echo $ChartXML;
			exit;
		}
		
		
		Private Function EchoCSV($Arr)
		{
			$Keys = array('name', 'clicks', 'impressions', 'ctr', 'avgCpc', 'cost');
		
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
		
		Public Function AccountStatsPPC()
		{
			$this->Provider = $_GET['Provider'];
			$this->ProviderType = $this->GetProviderFromString($this->Provider);
			$AccountID = $_GET['ID'];
			$DateRange = $this->DateRangeHelper();
			$this->DateRange = $DateRange[0] . '-' . $DateRange[1];
			$Stats = new Network_Stats();
			$this->CampaignStats = $Stats->GetAllCampaignStatsForAccount($AccountID, $this->ProviderType, $DateRange[0], $DateRange[1]);
			$this->AccountName = $Stats->GetAccountName($AccountID, $this->ProviderType);
			$this->AccountID = $AccountID;

			$this->CampaignFields = $Stats->GetEmptyStats();
			$StatsFields = array();
			foreach($Stats->GetEmptyStats(true) as $StatField=>$Zero)
			{
				$StatsFields[] = $StatField;
			}
			$this->CampaignStatsTotal = new stdClass();
			foreach($this->CampaignStats as $Key=>$Row)
			{
				foreach($Row as $Item=>$Value)
				{
					
					$this->CampaignStatsTotal->{$Item} += $Value;
					
				}
			}
			if(isset($_GET['sort']))
			{
				$sortType = $_GET['sort'];
				$sortOrder = 'Asc';
				
				if(isset($_GET['sort_order']))
					$sortOrder = $_GET['sort_order'];
					
				usort($this->CampaignStats, array($this, "sort".$sortType.$sortOrder));

			}
			
			if(sizeOf($this->CampaignStats))
			{
				$this->CampaignStatsTotal->CTR /= sizeOf($this->CampaignStats);
				$this->CampaignStatsTotal->AvgCPC /= sizeOf($this->CampaignStats);
			}
		}
		
		Public Function _AccountStatsChartXML()
		{
			$ID = $ProviderType = 1;
			if(isset($_GET['ID']))
				$ID = $_GET['ID'];
			if(isset($_GET['ProviderType']))
				$ProviderType = $_GET['ProviderType'];
				
			$DateRange = $this->GetDefaultDateRange();
			if(isset($_GET['DateRange']))
				$DateRange = $_GET['DateRange'];

			$ChartXML = new ChartXMLHelper();
			$ChartXML->SetDateRange($DateRange);
			if(isset($_GET['Field']))
			{
				$ChartXML->Field = $_GET['Field'];
			}
			if(isset($_GET['Campaigns']))
			{
				$ChartXML->StatsShowRows = $_GET['Campaigns'];
			}
			$ChartXML->LoadAccountStats($ID, $ProviderType);
			echo $ChartXML;
			exit;
		}
		
		Public Function PPCQueueProgress()
		{
		    if(@$_GET['iframe'] == 'true')
		    {
		        Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		        echo '<link href="http://bsh/Themes/BevoMedia/main.css" rel="stylesheet" type="text/css" />';
                echo '<link href="http://bsh/Themes/BevoMedia/style.css" rel="stylesheet" type="text/css" />';
                echo '<link href="http://bsh/Themes/BevoMedia/default.css" rel="stylesheet" type="text/css" />';
		        echo '<div style="background: white">';
		    }
			$CPQ = new CreatePPCQueue();
			if(isset($_GET['hide']))
			{
			  $CPQ->HideQueueItemForUser($this->User->id, (int)$_GET['hide']);
			}
			$this->QueueItems = $CPQ->GetAllQueueItemsForUser($this->User->id);
		}
		
		Private Function GetProviderFromString($String)
		{
			$Providers = array('ADWORDS'=>1, 'YAHOO'=>2, 'MSN'=>3);
			return (int)$Providers[strtoupper($String)];
		}
		
		Private Function GetStringFromProviderType($Type)
		{
			$Providers = array(1=>'Adwords', 2=>'Yahoo', 3=>'MSN');
			if(!in_array($Type, array_keys($Providers)))
				return 0;
			return $Providers[$Type];
		}
		
		Private Function GetDefaultDateRange()
		{
			$DateRange = date('m/j/Y', strtotime('TODAY - 1 DAYS'));
			$DateRange .= '-';
			$DateRange .= date('m/j/Y', strtotime('TODAY - 0 DAYS'));
			return $DateRange;
		}
		
		Private Function DateRangeHelper()
		{
			$DateRangeString = $this->GetDefaultDateRange();
			if(isset($_GET['DateRange']))
				$DateRangeString = $_GET['DateRange'];
			
			$DateRange = explode('-', $DateRangeString);
			if(sizeOf($DateRange) < 2)
			{
				$DateRange[1] = $DateRange[0];
			}
			return $DateRange;
		}
		
		Public Function NetworkRating()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			if (isset($_POST['Submit']))
			{
				$Sql = "SELECT id FROM bevomedia_user_aff_network_rating WHERE user__id = {$this->User->id} AND network__id = $_GET[ID]";
				$Row = $this->db->fetchRow($Sql);
				if(!$Row)
				{
				
				}else
				{
					$Sql = "DELETE FROM bevomedia_user_aff_network_rating WHERE id = $Row->id";
					$this->db->exec($Sql);
				}
				
				$Comment = $this->db->quote($_POST['comment']);
				
				$Sql = "INSERT INTO bevomedia_user_aff_network_rating (user__id, network__id, rating, userComment) VALUES ({$this->User->id}, $_GET[ID], $_GET[Rating], $Comment)";
				$this->db->exec($Sql);
				// header('Location: /BevoMedia/Index/CloseShadowbox.html?goto={PARENT}');
				// exit;
			}
			
		}
		
		Public Function Reviews()
		{
			$this->PageHelper->Area = 'MyNetworks';
			
			$NetworkID = intval($_GET['NetworkID']);
			$Sql = "SELECT
						id,
						rating,
						network__id,
						userComment
					FROM
						bevomedia_user_aff_network_rating
					WHERE
						(bevomedia_user_aff_network_rating.approved = 1) AND
						(bevomedia_user_aff_network_rating.network__id = {$NetworkID}) AND
						(bevomedia_user_aff_network_rating.userComment <> '')
					";
			$this->Reviews = $this->db->fetchAll($Sql);
		}
		
		Public Function networks_new()
		{
			$db = Zend_Registry::get('Instance/DatabaseObj');
			$query = "SELECT n.*, u.status FROM bevomedia_aff_network AS n LEFT JOIN bevomedia_user_aff_network AS u ON n.id = u.network__id AND u.user__id = {$this->User->id} WHERE n.model = 'CPA' AND n.isValid = 'Y' ORDER BY n.title";
			$this->CpaNetworks = $db->fetchAll($query);
			foreach($this->CpaNetworks as $Key=>$Network)
			{
				$Sql = "SELECT AVG(rating) AS RATING FROM bevomedia_user_aff_network_rating WHERE (network__id = {$Network->id}) AND (approved=1) GROUP BY network__id";
				$Row = $db->fetchRow($Sql);
				if(!$Row)
					$Row = 0;
				else
					$Row = $Row->RATING;
				$this->CpaNetworks[$Key]->rating = $Row;
				
				
				if ($this->CpaNetworks[$Key]->title=='FireLead')
				{
					$this->FireLead = $this->CpaNetworks[$Key];
				}
				
				if ($this->CpaNetworks[$Key]->title=='ClickBooth')
				{
					$this->ClickBooth = $this->CpaNetworks[$Key];  
				}
				
				if ($this->CpaNetworks[$Key]->title=='EpicDirect')
				{
					$this->EpicDirect = $this->CpaNetworks[$Key];  
				}
			}
			
			$RtArr = array('Adwords', 'Yahoo', 'MSN', 'Analytics');
			foreach($RtArr as $Key=>$Value)
			{
				$Temp = new stdClass();
				$Temp->ID = $Key+1;
				$Temp->Rating = 0;
				$Sql = "SELECT AVG(rating) AS RATING FROM bevomedia_user_aff_network_rating WHERE network__id = {$Temp->ID} GROUP BY rating";
				$Row = $db->fetchRow($Sql);
				if($Row)
					$Temp->rating = $Row->RATING;
				$this->{$Value . 'Rating'} = $Temp;
			}
			
		}
		
		Public Function Verify()
		{
			if (!isset($_SERVER['HTTPS'])) {
				header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				die;
			}
		}
		
		Public Function VerifyResearch()
		{
			if (!isset($_SERVER['HTTPS'])) {
				header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				die;
			}
		}
		
		Public Function VerifyAdwords()
		{
			if (!isset($_SERVER['HTTPS'])) {
				header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				die;
			}
		}
		
		Public Function VerifySelfHosted()
		{
			if (!isset($_SERVER['HTTPS'])) {
				header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				die;
			}
		}
		
		Public Function VerifyPPC()
		{
			if (!isset($_SERVER['HTTPS'])) {
				header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				die;
			}
		}
		
		Public Function PPVSpyHelp()
		{
		
		}
		
	}

?>