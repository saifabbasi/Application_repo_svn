<?php
/**
 * User Controller
 */

/**
 * User Controller
 *
 * Controller for generic User related pages such as processing log in attempts, changing the user's password, updating profile information,
 * submitting a ticket and more.
 * @category   RCS Framework
 * @package    Controllers
 * @subpackage UsersController
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */

require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/lib_nmi/nmiCustomerVault.class.php');

Class UserController extends ClassComponent
{
	
	/**
	 * @var Mixed $GUID
	 */
	Public $GUID		= NULL;
	
	/**
	 * Constructor
	 */
	Public Function __construct()
	{
		parent::GenerateGUID();
		$this->{'PageHelper'} = new PageHelper();
		$this->{'PageDesc'} = new PageDesc();
		$_db = Zend_Registry::get('Instance/DatabaseObj');
		$useApiKey = false;
		
		if(isset($_GET['apiKeyCreateUser']))
		{
			$this->db = $_db;
			$this->jsonInsertUser();
			die;
		}
		
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
		}
			
		$this->db = $_db;
		if(isset($_SESSION['User']['ID']))
		{
			$user = new User();
			$user->getInfo($_SESSION['User']['ID']);
			$this->{'User'} = $user;
			Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
		}
		
		
		$page = Zend_Registry::get('Instance/Function');
		if(!isset($_SESSION['User']) || !intval($_SESSION['User']['ID']))
		{
			$noLoginNeeded = array('Register', 'Login', 'ProcessLogin');
			if(!in_array($page, $noLoginNeeded))
			{
				$_SESSION['loginLocation'] = $_SERVER['REQUEST_URI'];
				header('Location: /BevoMedia/Index/');
				die;
			}
		}
//		$premiumOnly = array('RackspaceWizard', 'ServerScript', 'SelfHostedLoginDownload');
//		if(in_array($page, $premiumOnly) && $this->User->membershipType == 'basic')
//		{
//		  header('Location: /BevoMedia/Marketplace/Premium.html?from=selfhost');
//		  die;
//		}
		
		if (isset($_GET['v3apps']) && ($_SERVER['SERVER_NAME']=='apps.bevomedia.com')) { 
			setcookie('v3apps', true, time()+3600*24*365, '/');
			setcookie('v3domain', $_GET['domain'], time()+3600*24*365, '/');
			Zend_Registry::set('Instance/LayoutType', 'apps-layout');
			
			header('Location: /'.Zend_Registry::Get('Instance/Application').'/'.Zend_Registry::Get('Instance/Module').'/'.Zend_Registry::Get('Instance/Function').'.html');
			die;
		}
		
		if (isset($_COOKIE['v3apps'])) {
			Zend_Registry::set('Instance/LayoutType', 'apps-layout');
		}
		
	}
	Public Function SelfHostedChangelog()
  {
	$this->hist = $this->db->fetchAll('select * from bevomedia_selfhost_version where public=1 and changelog != "" order by id desc');
	Zend_Registry::set('Instance/LayoutType', 'logged-in-layout');
  }	
	/**
	 * Publisher PPC Accounts Page Functionality
	 */
	Public Function PublisherPPCAccounts()
	{
		$User = $this->User;
		$this->AdwordsResults = $User->GetAllAccountsAdwords();
		$this->YahooResults = $User->GetAllAccountsYahoo();
		$this->MSNResults = $User->GetAllAccountsMSN();
	}
	
	/**
	 * Adwords API Paypal Return Page Functionality
	 */
	Public Function AdwordsAPIPaypalReturn()
	{
		if(isset($_GET['PAID']))
		{
			$APIUse = new Adwords_API_Usage();
			$APIUse->AddCredit($this->User->id, $_GET['AMOUNT']);
			$goto = '/BevoMedia/User/AdwordsAPIUsage.html';
			$goto = $this->PageHelper->URLEncode($goto);
			header('Location: /BevoMedia/Index/CloseShadowbox.html?goto=' . $goto);
			die;
		}
	}
	Public Function ManageStats()
	{
		$this->AdwordsResults = $this->User->getAllAccountsAdwords();
		$this->YahooResults = $this->User->getAllAccountsYahoo();
		$this->MSNResults = $this->User->getAllAccountsMSN();
		$this->AffResults = $this->User->getAllAffiliateAccounts();
		$this->PPCCampaigns = $this->User->getAllPPCCampaigns();
		$this->deleted = false;
		if(!empty($_POST))
		{
	      $dateArray = explode(' - ', $_POST['date']);
	      if (count($dateArray)==1)
	      {
	      	$s = $e = $dateArray[0];
	      } else
	      {
	      	$s = $dateArray[0];
	      	$e = $dateArray[1];
	      }
		  $s = date('Y-m-d', strtotime($s));
		  $e = date('Y-m-d', strtotime($e));
	      
		  $user = 'user__id='.$this->User->id;
//		  $s = date('Y-m-d', strtotime($_POST['sdate']));
//		  $e = date('Y-m-d', strtotime($_POST['edate']));
		  $btwn = "BETWEEN '$s' AND '$e'";
		  // Delete Keyword Tracker Stats
		  if($_POST['kw'] == 'on')
		  {
			$this->db->delete('bevomedia_tracker_clicks_optional', "clickId in (select id from bevomedia_tracker_clicks where $user and clickDate $btwn)");
			$this->db->delete('bevomedia_tracker_clicks', "$user and clickDate $btwn");
			$this->deleted = true;
		  }

		  // Delete Affiliate Network Stats
		  if($_POST['aff'] == 'on')
		  {
			$nets = implode(',', array_keys($_POST['AffNetwork']));
			$this->db->delete('bevomedia_user_aff_network_subid', "$user and statDate $btwn and network__id in ($nets)");
			$this->deleted = true;
		  }
		  // Delete PPC Tracker Stats
		  if($_POST['ppc'] == 'on')
		  {
			$gIds = implode(',', array_map(intval, array_keys($_POST['Adwords'], 'on')));
			$yIds = implode(',', array_map(intval, array_keys($_POST['Yahoo'], 'on')));
			$mIds = implode(',', array_map(intval, array_keys($_POST['MSN'], 'on')));
			$wheres = array();
			if(!empty($gIds)) $wheres[] = "(providerType=1 and accountId in ($gIds))";
			if(!empty($yIds)) $wheres[] = "(providerType=2 and accountId in ($yIds))";
			if(!empty($mIds)) $wheres[] = "(providerType=3 and accountId in ($mIds))";
			if(count($wheres))
			{
			  $cWhere = "where $user AND (" . implode(' OR ', $wheres).")";
			  $cIds = "select id from bevomedia_ppc_campaigns $cWhere";
			  $agIds = "select id from bevomedia_ppc_adgroups where id in ($cIds)";
			  $this->db->exec("delete from bevomedia_ppc_advariations_stats where statDate $btwn AND advariationsId in (select id from bevomedia_ppc_advariations where adGroupId in ($agIds))");
			  $this->db->exec("delete from bevomedia_ppc_keywords_stats where statDate $btwn AND keywordId in (select id from bevomedia_ppc_keywords where adGroupId in ($agIds))");
			  $this->db->exec("delete from bevomedia_ppc_contentmatch_stats where statDate $btwn AND adGroupId in ($agIds)");
			  $this->deleted = true;
			}
		  }
		  
		  if (isset($_POST['campaigns']))
		  { 
		  		foreach ($_POST['deleteCampaigns'] as $CampaignID)
		  		{
		  			$Sql = "SELECT
		  					 	DISTINCT bevomedia_ppc_advariations.id
							FROM 
								bevomedia_ppc_campaigns,
								bevomedia_ppc_adgroups,
								bevomedia_ppc_advariations
							WHERE
								(bevomedia_ppc_campaigns.id = bevomedia_ppc_adgroups.campaignId) AND
								(bevomedia_ppc_advariations.adGroupId = bevomedia_ppc_adgroups.id) 
		  					";
		  			$AdVarID = $this->db->fetchOne($Sql);
		  			
		  			$Sql = "DELETE FROM bevomedia_tracker_clicks WHERE bevomedia_tracker_clicks.creativeId = {$AdVarID}";
		  			$this->db->exec($Sql);
		  			
		  			
		  			$Sql = "DELETE FROM bevomedia_ppc_campaigns WHERE (id = {$CampaignID}) AND (user__id = {$this->User->id})";
		  			$this->db->exec($Sql);
		  		}
		  }
		  
		  if (isset($_POST['campaignStats']))
		  {
		  		foreach ($_POST['deleteCampaignStats'] as $CampaignID)
		  		{
		  			$Sql = "SELECT
		  					 	DISTINCT bevomedia_ppc_advariations.id
							FROM 
								bevomedia_ppc_campaigns,
								bevomedia_ppc_adgroups,
								bevomedia_ppc_advariations
							WHERE
								(bevomedia_ppc_campaigns.id = bevomedia_ppc_adgroups.campaignId) AND
								(bevomedia_ppc_advariations.adGroupId = bevomedia_ppc_adgroups.id) 
		  					";
		  			$AdVarID = $this->db->fetchOne($Sql);
		  			
		  			$Sql = "DELETE FROM bevomedia_tracker_clicks WHERE bevomedia_tracker_clicks.creativeId = {$AdVarID}";
		  			$this->db->exec($Sql);
		  		}
		  }
		}
	}
	
	/**
	 * Change Password Page Functionality
	 */
	Public Function ChangePassword()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		
		$this->Message = false;
		
		if(isset($_POST['changePasswordSubmit']))
		{
			$User = new User($_SESSION['User']['ID']);
			$Valid = $User->Login($User->email, md5($_POST['OldPassword']));
			if(!$Valid || $_POST['NewPassword'] == '' || $_POST['ReNewPassword'] == '' || $_POST['ReNewPassword'] != $_POST['NewPassword'])
				$this->Message = 'INVALID_PASS';
			else {
				$User->ChangePassword($_POST['NewPassword']);
				$this->Message = 'PASS_CHANGED';
			}
		}
	}
	
	Public Function UpdateSelfHostedDownload()
	{
			Zend_Registry::set('Instance/LayoutType', 'blank-layout');
	}
	
	Public Function LightboxFirst()
	{
		Zend_Registry::set('Instance/LayoutType', 'blank-layout');
		
	}
	
	Public Function LightboxTemplate_Final()
	{
		Zend_Registry::set('Instance/LayoutType', 'blank-layout');
		$this->PresetItem = $_GET['STEP'];
		$this->Presets = array(
			'AFFSTEP2'=>array('TITLE'=>'My Networks', 'LINK'=>'/BevoMedia/Publisher/PPCManager.html?STEP=AFFSTEP3', 'CONTENT'=>'Great! BeVo Media is specifically molded towards affiliate marketers to help manage affiliate networks, offers, conversion statistics, PPC Accounts, Media Buys and more! This is the My Networks Page. Scroll through the page and install your affiliate networks, PPC Accounts, and analytics accounts. The My Networks page is also a great place to learn what other publishers rate their user experience with a network. Feel free to rate your favorite, and least favorite networks! Your network stats update once an hour by default.'),
			'AFFSTEP3'=>array('TITLE'=>'PPC Management', 'LINK'=>'/BevoMedia/KeywordTracker/Overview.html?STEP=AFFSTEP4', 'CONTENT'=>'After you install your PPC Accounts you can drill down your account statistics from an account, campaign, adgroup, ad variation and keyword level. If something needs to be changed, you have the ability to easily edit a campaign through the Bevo Interface. <br/><br/>Within the PPC Management Section, is the Bevo Editor. The Bevo Editor allows publishers to easily create campaigns in their entirety. The Bevo Editor features the Cross Post feature that allows users to create a campaign, and then post that campaign to multiple PPC Accounts at once! '),
			'AFFSTEP4'=>array('TITLE'=>'Keyword Tracker', 'LINK'=>'/BevoMedia/Analytics/AnalyticsDetail.html?STEP=AFFSTEP5', 'CONTENT'=>'Bevo Track is a full scale Keyword tracker with API integrated subID, pixel, and postback methods available. Additionally, within Bevo Track is a complete PPV tracker and media buy tracker, reporting your statistics in the most complete, simplistic form possible. Simply put, BevoTrack is the most effortless, yet data intensive keyword tracker on the net!'),
			'AFFSTEP5'=>array('TITLE'=>'Analytics', 'LINK'=>'/BevoMedia/Marketplace/Index.html?STEP=AFFSTEP6', 'CONTENT'=>'After you install your analytics accounts, you can easily view your traffic statistic on the Bevo Interface. No need to log in to multiple analytic accounts to keep track of all of your sites, Bevo brings it all to you on one interface! '),
			'AFFSTEP6'=>array('TITLE'=>'Marketplace', 'LINK'=>'/BevoMedia/Publisher/Classroom.html?STEP=AFFSTEP7', 'CONTENT'=>'The Bevo Marketplace is here to get your projects done. We provide quality work with a fast turnaround time. With the Bevo Marketplace, your projects are just one click away from being completed. The Bevo team is made up of talented people who understand the internet marketing industry.'),
			'AFFSTEP7'=>array('TITLE'=>'Classroom', 'LINK'=>'/BevoMedia/User/AppStore.html?STEP=AFFSTEP8', 'CONTENT'=>'Need to catch up on your online marketing efforts? BevoClass is a full affiliate marketing classroom, providing users with a wealth of information in all aspects of the industry. This information has been gathered by the industry\'s most successful and brightest publishers. Instead of wasting hours on the forums, you can get everything you need here, for free! '),
			'AFFSTEP8'=>array('TITLE'=>'Apps Section', 'LINK'=>'/BevoMedia/User/Index.html?STEP=AFFSTEP9', 'CONTENT'=>'On the Apps page, you can access premium features within Bevo Media. Our Apps include access to download our self hosted version, powerful research tools, geotargetting features and more! Apps are being added on a consistant basis!'),
			'AFFSTEP9'=>array('TITLE'=>'Tutorial Complete!', 'DONE'=>TRUE, 'LINK'=>'#', 'CONTENT'=>'That\'s it! Feel free to click around and check out our support forum if you have any questions! '),
			
			'FIRMSTEP2'=>array('TITLE'=>'My Networks', 'LINK'=>'/BevoMedia/Publisher/PPCManager.html?STEP=FIRMSTEP3', 'CONTENT'=>'The Bevo Media Platform was developed specifically to help facilitate the online marketing efforts for businesses and PPC Firms. As a company, you will have the ability to manage multiple Pay Per Click accounts, Analytic accounts, and track your campaign success in an easy, clear cut manor. <br/><br/>This is the My Networks page. On this page, you install all of your pay per click and analytic accounts. You need to do this before you can access your accounts on the Bevo interface. '),
			'FIRMSTEP3'=>array('TITLE'=>'PPC Management', 'LINK'=>'/BevoMedia/KeywordTracker/Overview.html?STEP=FIRMSTEP4', 'CONTENT'=>'After you install your PPC Accounts you can drill down your account statistics from an account, campaign, adgroup, ad variation and keyword level. If something needs to be changed, you have the ability to easily edit a campaign through the Bevo Interface. <br/><br/>Within the PPC Management Section, is the Bevo Editor. The Bevo Editor allows publishers to easily create campaigns in their entirety. The Bevo Editor features the Cross Post feature that allows users to create a campaign, and then post that campaign to multiple PPC Accounts at once! '),
			'FIRMSTEP4'=>array('TITLE'=>'Keyword Tracker', 'LINK'=>'/BevoMedia/Analytics/AnalyticsDetail.html?STEP=FIRMSTEP5', 'CONTENT'=>'Bevo Media has a  built in keyword tracker. Easily track you campaign performance by the various tracking methods provided. All forms of the tracker are API integrated, making it the most simplistic setup keyword tracker on the net.  '),
			'FIRMSTEP5'=>array('TITLE'=>'Analytics', 'LINK'=>'/BevoMedia/Marketplace/Index.html?STEP=FIRMSTEP6', 'CONTENT'=>'After you install your analytics accounts, you can easily view your traffic statistic on the Bevo Interface. No need to log in to multiple analytic accounts to keep track of all of your sites, Bevo brings it all to you on one interface! '),
			'FIRMSTEP6'=>array('TITLE'=>'Marketplace', 'LINK'=>'/BevoMedia/Publisher/Classroom.html?STEP=FIRMSTEP7', 'CONTENT'=>'The Bevo Marketplace is here to get your projects done. We provide quality work with a fast turnaround time. With the Bevo Marketplace, your projects are just one click away from being completed. The Bevo team is made up of talented people who understand the internet marketing industry.'),
			'FIRMSTEP7'=>array('TITLE'=>'Classroom', 'LINK'=>'/BevoMedia/User/AppStore.html?STEP=FIRMSTEP8', 'CONTENT'=>'Need to catch up on your online marketing efforts? BevoClass is a full affiliate marketing classroom, providing users with a wealth of information in all aspects of the industry. This information has been gathered by the industry\'s most successful and brightest publishers. Instead of wasting hours on the forums, you can get everything you need here, for free! '),
			'FIRMSTEP8'=>array('TITLE'=>'Apps Section', 'LINK'=>'/BevoMedia/User/Index.html?STEP=FIRMSTEP9', 'CONTENT'=>'On the Apps page, you can access premium features within Bevo Media. Our Apps include access to download our self hosted version, powerful research tools, geotargetting features and more! Apps are being added on a consistant basis!'),
			'FIRMSTEP9'=>array('TITLE'=>'Tutorial Complete!', 'DONE'=>TRUE, 'LINK'=>'#', 'CONTENT'=>'That\'s it! Feel free to click around and check out our support forum if you have any questions!'),
		);
	}
	
	/**
	 * Change Profile Page Functionality
	 */
	Public Function PerfConn()
	{
		$goto = '/BevoMedia/User/AppStore.html';
		
		if(isset($_GET['unsubscribe'])) {
			$this->User->clearPerformanceConnectorNiches();
			$this->User->clearPerformanceConnectorPromoMethod();
			$this->User->clearPerformanceConnectorExpLevel();
			$this->User->clearPerformanceConnectorContactEntries();
			$goto = $this->PageHelper->URLEncode($goto);
			header('Location: /BevoMedia/Index/CloseShadowbox.html?goto=' . $goto);
			die;
		}
		
		if(isset($_POST['changeProfileFormSubmit']))
		{
			if ( (trim($_POST['ContactType'])=='IM') && (trim($_POST['im'])=='') || 
				( (trim($_POST['ContactType'])=='Phone') && (trim($_POST['phone'])=='') )
				)
			{
				$this->ErrorMessage = 'You must enter contact info.';	
			} else {
				$Data = $_POST;
				$niche = $Data['niche'];
				$promomethods = $Data['promomethod'];
				$explevels = $Data['explevel'];
				
				$this->User->clearPerformanceConnectorNiches();
				foreach ($niche as $nicheId) {
					$this->User->insertPerformanceConnectorNiche($nicheId);
				}
				
				$this->User->clearPerformanceConnectorPromoMethod();
				foreach ($promomethods as $promoId) {
					$this->User->insertPerformanceConnectorPromoMethod($promoId);
				}
				
				$this->User->clearPerformanceConnectorExpLevel();
				foreach ($explevels as $expId) {
					$this->User->insertPerformanceConnectorExpLevel($expId);
				}
				
				$this->User->clearPerformanceConnectorContactEntries();	
				$this->User->insertPerformanceConnectorContactEntry($_POST['im_service'], $_POST['im'], $_POST['phone']);
				
				$this->Message = 'ACCOUNT_UPDATED';
				$goto = $this->PageHelper->URLEncode($goto);
				header('Location: /BevoMedia/Index/CloseShadowbox.html?goto=' . $goto);
				die;
			}
		}
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
	
		

		$Sql = "SELECT
			bevomedia_name_your_price_niche.*
		FROM
			bevomedia_name_your_price_niche
		ORDER BY
			bevomedia_name_your_price_niche.Name			
		";
		$this->Niches = $this->db->fetchAll($Sql);
		
		$Sql = "SELECT
			bevomedia_user_performanceconnector_promomethods.*
		FROM
			bevomedia_user_performanceconnector_promomethods
		ORDER BY
			bevomedia_user_performanceconnector_promomethods.promomethod
		";
		$this->PromoMethods = $this->db->fetchAll($Sql);
		
		$Sql = "SELECT
			bevomedia_user_performanceconnector_explevels.*
		FROM
			bevomedia_user_performanceconnector_explevels
		ORDER BY
			bevomedia_user_performanceconnector_explevels.id
		";
		$this->ExpLevels = $this->db->fetchAll($Sql);
		
		$userNiches = $this->User->getPerformanceConnectorNiches();
		$this->UserNicheIDs = array();
		foreach ($userNiches as $userNiche) {
			$this->UserNicheIDs[] = $userNiche->niche__id;
		}
		
		$userPromos = $this->User->getPerformanceConnectorPromoMethods();
		$this->UserPromoMethodIDs = array();
		foreach ($userPromos as $userPromo) {
			$this->UserPromoMethodIDs[] = $userPromo->promomethod__id;
		}
		
		$userLevels = $this->User->getPerformanceConnectorExpLevels();
		$this->UserExpLevelIDs = array();
		foreach ($userLevels as $userLevel) {
			$this->UserExpLevelIDs[] = $userLevel->explevel__id;
		}
	}
	
	Public Function ChangeProfile()
	{
		if(isset($_POST['changeProfileFormSubmit']))
		{
			
			$Data = $_POST;
			
			$niche = $Data['niche'];
			$this->User->clearPerformanceConnectorNiches();
			if(isset($_POST['bevoPerformanceConnector']) && $_POST['bevoPerformanceConnector'] == 'on') {
				foreach ($niche as $nicheId) {
					$this->User->insertPerformanceConnectorNiche($nicheId);
				}
			}
			
			$promomethods = $Data['promomethod'];
			$this->User->clearPerformanceConnectorPromoMethod();
			if(isset($_POST['bevoPerformanceConnector']) && $_POST['bevoPerformanceConnector'] == 'on') {
				foreach ($promomethods as $promoId) {
					$this->User->insertPerformanceConnectorPromoMethod($promoId);
				}
			}
			
			$explevels = $Data['explevel'];
			$this->User->clearPerformanceConnectorExpLevel();
			if(isset($_POST['bevoPerformanceConnector']) && $_POST['bevoPerformanceConnector'] == 'on') {
				foreach ($explevels as $expId) {
					$this->User->insertPerformanceConnectorExpLevel($expId);
				}
			}
			
			unset($Data['bevoPerformanceConnector']);
			unset($Data['niche']);
			unset($Data['changeProfileFormSubmit']);
			unset($Data['promomethod']);
			unset($Data['explevel']);
			$this->User->Update($Data);
			$this->Message = 'ACCOUNT_UPDATED';
		}
		

		$Sql = "SELECT
			bevomedia_name_your_price_niche.*
		FROM
			bevomedia_name_your_price_niche
		ORDER BY
			bevomedia_name_your_price_niche.Name			
		";
		$this->Niches = $this->db->fetchAll($Sql);
		
		$Sql = "SELECT
			bevomedia_user_performanceconnector_promomethods.*
		FROM
			bevomedia_user_performanceconnector_promomethods
		ORDER BY
			bevomedia_user_performanceconnector_promomethods.promomethod
		";
		$this->PromoMethods = $this->db->fetchAll($Sql);
		
		$Sql = "SELECT
			bevomedia_user_performanceconnector_explevels.*
		FROM
			bevomedia_user_performanceconnector_explevels
		ORDER BY
			bevomedia_user_performanceconnector_explevels.id
		";
		$this->ExpLevels = $this->db->fetchAll($Sql);
		
		$userNiches = $this->User->getPerformanceConnectorNiches();
		$this->UserNicheIDs = array();
		foreach ($userNiches as $userNiche) {
			$this->UserNicheIDs[] = $userNiche->niche__id;
		}
		
		$userPromos = $this->User->getPerformanceConnectorPromoMethods();
		$this->UserPromoMethodIDs = array();
		foreach ($userPromos as $userPromo) {
			$this->UserPromoMethodIDs[] = $userPromo->promomethod__id;
		}
		
		$userLevels = $this->User->getPerformanceConnectorExpLevels();
		$this->UserExpLevelIDs = array();
		foreach ($userLevels as $userLevel) {
			$this->UserExpLevelIDs[] = $userLevel->explevel__id;
		}
		
	}
	
	/**
	 * Submit Ticket Page Functionality
	 */
	Public Function SubmitTicket()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		if(isset($_POST['submitTicketSubmit']))
		{
			$this->User->SubmitTicket($_POST['Subject'], $_POST['Problem']);
			$this->Message = 'TICKET_ADDED';
		}
	}
	
	/**
	 * Cancel Account Page Functionality
	 */
	Public Function CancelAccount()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		
		$this->Message = false;
		if(isset($_POST['cancelAccountCancelSubmit']))
		{
			$this->Message = 'ACCOUNT_DISABLED';
			$User = new User($_SESSION['User']['ID']);
			$User->DisableUser($User->id);
			unset($_SESSION['User']);
			
		}
		if(isset($_POST['cancelAccountNevermindSubmit']))
		{
			$this->Message = 'THANKS';
		}
	}
	
	Public Function ApiCalls()
	{
	  $this->days = array();
	  $d = strtotime('30 days ago');
	  $end = strtotime('today');
	  while($d <= $end)
	  {
		$d += 3600*24;
		$day = date('Y-m-d', $d);
		$sql = "select sum(amount) from bevomedia_api_calls where user__id={$this->User->id} and date(at) <= '{$day}'";
		$total = $this->db->fetchOne($sql);

		$sql = "select sum(amount) from bevomedia_api_calls where user__id={$this->User->id} and date(at) = '{$day}' and amount < 0";

		$use = $this->db->fetchOne($sql);
		$this->days[] = (object) array(
		  'day' => $day,
		  'day_use' => intval($use),
		  'day_total' => intval($total));
	  }
	  $this->last_fifty_txs = $this->db->fetchAll("select at, amount, reason from bevomedia_api_calls where user__id={$this->User->id} order by at desc limit 50");
	}
	
	/**
	 * Register Page Functionality
	 */
	Public Function Register()
	{ 
	
		if (!isset($_GET['apiKeyCreateUser']))
		{
			Zend_Registry::set('Instance/LayoutType', 'main-layout');
			
			$Token = Zend_Registry::get('Instance/URI_Token');
			
			if (isset($Token[4]) && (strlen($Token[4])==32)) 
			{
				if (isset($Token[5]) && ($Token[5]=='s'))
				{
					setcookie('BevoReferralS', $Token[4], time()+365*24*60*60, '/');
				} else {
					setcookie('BevoReferral', $Token[4], time()+365*24*60*60, '/');
				}
				
				header('Location: /BevoMedia/User/Register.html');
				die;
			}
		}
		$Sql = "SELECT
			bevomedia_name_your_price_niche.*
		FROM
			bevomedia_name_your_price_niche
		ORDER BY
			bevomedia_name_your_price_niche.Name			
		";
		$this->Niches = $this->db->fetchAll($Sql);
		
		$Sql = "SELECT
			bevomedia_user_performanceconnector_promomethods.*
		FROM
			bevomedia_user_performanceconnector_promomethods
		ORDER BY
			bevomedia_user_performanceconnector_promomethods.promomethod
		";
		$this->PromoMethods = $this->db->fetchAll($Sql);
		
		$Sql = "SELECT
			bevomedia_user_performanceconnector_explevels.*
		FROM
			bevomedia_user_performanceconnector_explevels
		ORDER BY
			bevomedia_user_performanceconnector_explevels.id
		";
		$this->ExpLevels = $this->db->fetchAll($Sql);
		
//		echo "register() _POST<br />\n";
//		print_r($_POST);
		
		if(isset($_POST['registerFormSubmit']))
		{ //echo 'registerFormSubmit'."<br >\n";
			$user = new User();
			$niche = $_POST['niche'];
			unset($_POST['niche']);
			$promomethods = $_POST['promomethod'];
			unset($_POST['promomethod']);
			$explevels = $_POST['explevel'];
			unset($_POST['explevel']);
			unset($_POST['ContactType']);
			
			$im_service = $_POST['im_service'];
			$im = $_POST['im'];
			$phone = $_POST['phone'];
			
			unset($_POST['im_service']);
			unset($_POST['im']);
			unset($_POST['phone']);
			
			$Data = $_POST;
			unset($Data['bevoPerformanceConnector']);
			$id = $user->insert($Data);
			if(!$id)
			    die('no id');
			
			if($_POST['bevoPerformanceConnector'] && $_POST['bevoPerformanceConnector'] == 'on') {
				foreach ($niche as $nicheId) {
					$user->insertPerformanceConnectorNiche($nicheId);
				}
				
				foreach ($promomethods as $promoId) {
					$user->insertPerformanceConnectorPromoMethod($promoId);
				}
				
				foreach ($explevels as $expId) {
					$user->insertPerformanceConnectorExpLevel($expId);
				}
				
				
				$user->insertPerformanceConnectorContactEntry($im_service, $im, $phone);
				
			}
		    
			$Mentor = new Mentor();
			$Mentor = $Mentor->GetMentorUsingEmail('ryan@bevomedia.com');
			if($Mentor !== false)
			{
				$Mentor->AddUserToMentor($id);;
			}
			
			setcookie('user_id', $id, 0, "/");
			$_SESSION['User']['ID'] = $id;

			$Sql = "UPDATE bevomedia_user SET lastLogin = NOW() WHERE id = ".$id;
			$this->db->exec($Sql);
			
            header('Location: /BevoMedia/User/Index.html?TUTORIAL=START');
            die;
			
		}
	}
	
	/**
	 * Process Login Page Functionality
	 */
	Public Function ProcessLogin()
	{
		if(isset($_POST['loginFormSubmit']))
		{
			$user = new User();
			
			$LoginPage = '/BevoMedia/Index/Login.html';
			$temp = new User($user->getIdUsingEmail($_POST['Email']));
			
			$addToURL = '';
			if($temp->lastLogin == '0000-00-00 00:00:00')
			{

				$addToURL = '?TUTORIAL=START';
			}
			
			$loginAttempt = $user->login($_POST['Email'], md5($_POST['Password']));

			if($loginAttempt === true)
			{
				$id = $user->getIdUsingEmail($_POST['Email']);
				setcookie('user_id', $id, 0, "/");
				
				$user->getInfo($id);
				if(isset($_POST['Remember']) && $_POST['Remember'] == 'on')
				{
					setcookie('BEVO_REMEMBER_LOGIN_ID', $user->id, time()+60*60*24*30, '/');
				}
				$_SESSION['User']['ID'] = $user->id;
				if(isset($_SESSION['loginLocation']) && !strstr($_SESSION['loginLocation'], '_') )
				{
					header('Location: ' . $_SESSION['loginLocation']);
					unset($_SESSION['loginLocation']);
				}else{
					header('Location: /BevoMedia/User/Index.html' . $addToURL);
				}
			}
			else
				if($loginAttempt == -1)
					header('Location: '.$LoginPage.'?Error=DISABLED');
				else
					header('Location: '.$LoginPage.'?Error=BADPASS');
		}
		die;
	}
	
	/**
	 * Logout Page Functionality
	 */
	Public Function Logout()
	{
		$user = new User();
		$user->Logout();
		if(isset($_COOKIE['BEVO_REMEMBER_LOGIN_ID']))
		{
			unset($_COOKIE['BEVO_REMEMBER_LOGIN_ID']);
			setcookie('user_id', 0, time() - 3600, '/');
			setcookie('_sid', 0, time() - 3600, '/');
			setcookie('BEVO_REMEMBER_LOGIN_ID', 0, time() - 3600, '/');
		}
		header('Location: /BevoMedia/Index/Index.html');
		die;
	}
	
	Public Function _UpdateQueueJSON()
	{
		$CPQ = new CreatePPCQueue();
		$CPQProgress = $CPQ->GetTotalQueueItemsForUser($this->User->id, true);
		print json_encode($CPQProgress);
		exit;
	}
	
	/**
	 * Empty Page Page Functionality
	 */
	Public Function EmptyPage()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		
		$_SESSION['extendSessionHack'] = time();
	}
	
	Public Function RackspaceWizard()
	{
		if ( (($this->User->vaultID==0) && !$this->User->IsSubscribed(User::PRODUCT_FREE_SELF_HOSTED)) || 
		 	 (($this->User->vaultID!=0) && !$this->User->IsSubscribed(User::PRODUCT_SELF_HOSTED_YEARLY_CHARGE))
	   		)
		{
			header('Location: /BevoMedia/User/AddCreditCard.html');
			die;
		}
		
		if($this->User->IsSelfHosted() != '1')
		{
//			$this->User->AddUserSelfHostedCharge();
			$this->db->update('bevomedia_user', array('isSelfHosted'=> 1, 'apiKey' => md5($this->User->id*rand(1024) + print_r(microtime(), true))), "id={$this->User->id}");
		}
	}
	Public Function RackspaceLaunch()
	{
		Zend_Registry::set('Instance/LayoutType', 'blank-layout');
	}
	Public Function SelfHostedLoginContact()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		
		if (isset($_POST['Submit']))
		{
			$MailComponentObject = new MailComponent();
        	$MailComponentObject->setFrom('no-reply@'.$_SERVER['HTTP_HOST']);
        	
			if (isset($_POST['MarketingMethods']))
			{
				$MarketingMethods = '';
				foreach ($_POST['MarketingMethods'] as $Value)
				{
					$MarketingMethods .= $Value.", ";
				}
				$MarketingMethods = substr($MarketingMethods, 0, strlen($MarketingMethods)-2);
			}
			
        	$EmailContent = "
			
			Name: {$_POST['Name']} <br />
			E-mail: {$_POST['Email']} <br />
			";
			
			if (isset($_POST['Company']))
			{
				$EmailContent .= "Company: {$_POST['Company']} <br />";
			}
			
			$EmailContent .= "
			Address: {$_POST['Address']} <br />
			Phone Number: {$_POST['PhoneNumber']} <br />";
			
			if (isset($_POST['AIM']))
			{
				$EmailContent .= "AIM Screen Name: {$_POST['AIM']} <br />";
			}
			
			$EmailContent .=  "
			Are you an: {$_POST['UserType']} <br />
			";
			
			if (isset($_POST['MarketingMethods']))
			{
				$EmailContent .= "What type of marketing methods do you do (check all that apply): {$MarketingMethods} <br />
				
				";
			}
			
			$ini = parse_ini_file(Zend_Registry::get('Application/TrueWorkingDirectory') . 'Applications/BevoMedia/' . 'config.ini', true);
			$adminEmail =  $ini['Instance']['AdminEmail'];
        	
        	$MailComponentObject->setSubject('BevoMedia '.$_POST['MembershipType']);
            $MailComponentObject->setHTML($EmailContent);
            $MailComponentObject->send(array($adminEmail));
			
            $this->User->UpdateIsSelfHosted(1);
            $MembershipType = '';
            if($_POST['MembershipType'] == 'Premier Membership')
            {
            	$MembershipType = 'PREMIUM';
            }
            if($_POST['MembershipType'] == 'Deluxe Membership')
            {
            	$MembershipType = 'DELUXE';
            }
            $this->User->UpdateMembershipType($MembershipType);
            
		}
	}
	
	Public Function ReportBug()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		
		if (!isset($_SESSION['User']['ID']))
		{
			return;
		}
		
		$UserID = $_SESSION['User']['ID'];
		
		if (isset($_POST['Send']))
		{
			if ($_POST['BugDescription']=='') return;
			
			$User = new User();
			$User->getInfo($UserID);
			
			$Message = "
			
			Name: {$User->FirstName} {$User->LastName}<br />
			User ID: {$User->ID}<br />
			E-mail: {$User->Email}<br />
			User-Agent: {$_SERVER['HTTP_USER_AGENT']}<br />
			IP Address: {$_SERVER['REMOTE_ADDR']}<br />
			Time: ".date('m/d/Y G:i:s T')."<br />
			
			<br />Bug Description:<br/><br/>
			
			{$_POST['BugDescription']}
			
					   ";
		    
			$MailComponentObject = new MailComponent();
        	$MailComponentObject->setFrom('no-reply@bevomedia.com');
			
			$MailComponentObject->setSubject('Bug From '.$User->FirstName.' '.$User->LastName);
            $MailComponentObject->setHTML($Message);
            $MailComponentObject->send(array('ryan@bevomedia.com'));
			
			$this->MessageSent = true;
		
		}
	}
	
	Public Function EULA()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
	}
	
	Public Function ContactUs()
	{
		$this->Status = '';
		if (isset($_POST['Submit']))
		{
			$User = new User();
			$User->getInfo($UserID);
			
			$Message = "
			
			Name: {$User->FirstName} {$User->LastName}<br />
			User ID: {$User->ID}<br />
			E-mail: {$User->Email}<br />
			User-Agent: {$_SERVER['HTTP_USER_AGENT']}<br />
			IP Address: {$_SERVER['REMOTE_ADDR']}<br />
			Time: ".date('m/d/Y G:i:s T')."<br />
			
			<br />Message:<br/><br/>
			
			{$_POST['Message']}
			
					   ";
			
			$MailComponentObject = new MailComponent();
        	$MailComponentObject->setFrom('no-reply@bevomedia.com');
			
			$MailComponentObject->setSubject($_POST['Subject']);
            $MailComponentObject->setHTML($Message);
            $MailComponentObject->send(array('ryan@bevomedia.com'));
			
			$this->Status = 'SENT';
			
		}
	}
	
	Public Function Forum()
	{
		$Sql = "SELECT username, email FROM bevomedia_user WHERE id = {$_SESSION['User']['ID']} ";
		$UserInfo = $this->db->fetchRow($Sql);
		
		if ($UserInfo->username!='')
		{
			setcookie('user_id', $_SESSION['User']['ID'], time()+3600*24*31, '/');
			$Username = $this->db->quote($UserInfo->username);
			
			$Sql = "SELECT * FROM phpbb_users WHERE username = {$Username}";
			$CheckPhpBbUsername = $this->db->fetchRow($Sql);
			if ($CheckPhpBbUsername==null)
			{
				if (@$_SERVER['HTTPS'])
					$prefix = "https://";
				else
					$prefix = "http://";
					
				$url = $prefix.$_SERVER["SERVER_NAME"].'/_create_user.php?Username='.urlencode($UserInfo->username).'&Email='.$UserInfo->email;
				file_get_contents($url);
			}
			header('Location: /phpBB3/');
			die;
		}
		
		$Email = $UserInfo->email;
		
		if (isset($_POST['Submit']))
		{
			$Username = $this->db->quote($_POST['Username']);
			$Sql = "SELECT id FROM bevomedia_user WHERE username = {$Username} ";
			$UserInfo = $this->db->fetchRow($Sql);
				
			if ($UserInfo==null)
			{
				$Data = array();
				$Data['username'] = $_POST['Username'];
				$this->db->update('bevomedia_user', $Data, 'id = ' . $_SESSION['User']['ID']);
				
				if ($_SERVER['HTTPS'])
					$prefix = "https://";
				else
					$prefix = "http://";
				
				file_get_contents($prefix.$_SERVER["SERVER_NAME"].'/_create_user.php?Username='.$Username.'&Email='.$Email);
				
				header('Location: /phpBB3/');
				die;
			} else
			{
				$this->UsernameExists = true;
			}			
		}
	}
	
	Public Function KeepAlive()
	{
		die;
	}
	
	Public Function AddCreditCard()
	{
		if (!isset($_SERVER['HTTPS']) && ($_SERVER['SERVER_NAME']!='bevomedia')) {
			header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
			die;
		}
	
		$Sql = 'SELECT
					*
				FROM
					bevomedia_adwords_countries
				ORDER BY
					(code <> "US"), country
				';
		$this->Countries = $this->db->fetchAll($Sql);


		$Sql = 'SELECT
					*
				FROM
					bevomedia_state
				ORDER BY
					name
				';
		$this->States = $this->db->fetchAll($Sql);
	}
	
	Public Function AddCreditCardProcess()
	{
		$Product = $this->User->GetProduct(User::PRODUCT_SERVER_CHARGE);
		
		/* @var $vault nmiCustomerVault */ 
		$vault = new nmiCustomerVault();
		
		$vault->setCcNumber($_POST['CreditCardNumber']);
		$vault->setCcExp($_POST['ExpirationMonth'].$_POST['ExpirationYeah']);
		$vault->setCvv($_POST['CVV']);
		
		
		$vault->setCompany($_POST['Company']);
		$vault->setFirstName($_POST['FirstName']);
		$vault->setLastName($_POST['LastName']);
		$vault->setAddress1($_POST['Address1']);
		$vault->setCity($_POST['City']);
		$vault->setState($_POST['State']);
		$vault->setCountry($_POST['Country']);
		$vault->setZip($_POST['Zip']);
		$vault->setPhone($_POST['Phone']);
		$vault->setEmail($this->User->email);
		
		if ($this->User->vaultID==0) {
			$vault->addAndCharge($Product->Price);
		} else {
			$vault->setCustomerVaultId($this->User->vaultID);
			$vault->update();
		}
		
		$result = $vault->execute();
		
		@file_put_contents('/home/bevo/nmi_log', var_export($result, true), FILE_APPEND);
		
		switch($result['response'])
		{
		    case 1:
		    	unset($_SESSION['AddCreditCardInfo']);
		    	
		    	$vaultLast4Digits = substr($_POST['CreditCardNumber'], -4);
		    	
		    	if ($this->User->vaultID==0) {
		    		$this->User->AddUserServerCharge($result['transactionid']);
		    	}
		    	
		    	$this->User->setVaultID($result['customer_vault_id'], $vaultLast4Digits);

		    	$v3result = file_get_contents('http://affportal.bevomedia.com/user/verify-user/apiKey/'.$this->User->apiKey.'/vault/'.$result['customer_vault_id'].'/last4digits/'.$vaultLast4Digits);
		    	$v3result = json_decode($v3result);
		    	
		    	if ($v3result->success==false) {
		    		$Body = "Error occurred while trying to verify user on v3.<br />
		    				{$v3result->error}
		    				";
					 
					$Headers  = 'MIME-Version: 1.0' . "\r\n";
					$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				 
					mail('error@bevomedia.com', 'V3 verify error', $Body, $Headers);
		    	}
		    	
		    	
		    	header('Location: /BevoMedia/User/CreditCardVerified.html');
		    	die;
		        break;
		    default:
		    	$_SESSION['AddCreditCardInfo'] = $_POST;
		    	$this->User->setVaultID(0);
		    	header('Location: /BevoMedia/User/AddCreditCard.html?Error='.$result['responsetext']);
		    	die;
		}				
	}
	
	Public Function DeleteCreditCard()
	{
		if ($this->User->vaultID==0) {
			header('Location: /BevoMedia/User/CreditCard.html');
	    	die;
		}
		
		/* @var $vault nmiCustomerVault */ 
		$vault = new nmiCustomerVault();
		$vault->setCustomerVaultId($this->User->vaultID);
		$vault->delete();
		
		$result = $vault->execute();
		
		switch($result['response'])
		{
		    case 1:
		    	
		    	$vaultLast4Digits = '';
		    	
		    	$this->User->setVaultID(0, $vaultLast4Digits);

				$v3result = file_get_contents('http://affportal.bevomedia.com/user/unverify-user/apiKey/'.$this->User->apiKey);
		    	$v3result = json_decode($v3result);
		    	
		    	if ($v3result->success==false) {
		    		$Body = "Error occurred while trying to unverify user on v3.<br />
		    				{$v3result->error}
		    				";
					 
					$Headers  = 'MIME-Version: 1.0' . "\r\n";
					$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				 
					mail('error@bevomedia.com', 'V3 unverify error', $Body, $Headers);
		    	}
		    	
		    	
		    	header('Location: /BevoMedia/User/CreditCard.html?RemovedSuccess');
		    	die;
		        break;
		    default:
		    	$_SESSION['AddCreditCardInfo'] = $_POST;
		    	$this->User->setVaultID(0);
		    	header('Location: /BevoMedia/User/CreditCard.html?Error='.$result['responsetext']);
		    	die;
		}	
	
	}
	
//	Public Function PayResearchYearly()
//	{
//		$Product = $this->User->GetProduct(User::PRODUCT_RESEARCH_YEARLY_CHARGE);
//		
//		$Vault = new nmiCustomerVault();
//		$Vault->setCvv('999');
//		$Vault->setCustomerVaultId($this->User->vaultID);
//		$Vault->charge($Product->Price);
//		$Result = $Vault->execute();
//		
//		
//		switch($Result['response'])
//		{
//			case 1: //Success
//				$TransactionID = $Result['transactionid'];
//				
//				$Array = array (
//								'UserID'		=> $this->User->id,
//								'ProductID'		=> $Product->ID,
//								'Price'			=> $Product->Price,
//								'Date'			=> date('Y-m-d H:i:s'),
//								'Paid' 			=> 1,
//								'PaidDate'		=> date('Y-m-d H:i:s'),
//								'TransactionID' => $TransactionID,
//								
//								);
//				
//				$this->db->insert('bevomedia_user_payments', $Array);
//
//				header('Location: /BevoMedia/User/VerifyResearchFinish.html');
//				die;
//				break;
//			default:
//				
//				$Body = "Research payment for user \"{$UserID}\" has failed.<br /><br />
//		    			 Error: {$Result['responsetext']}<br /><br />
//		    			 Amount:  \${$Product->Price}		    	
//	    				";
//				 
//				$Headers  = 'MIME-Version: 1.0' . "\r\n";
//				$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//				 
//	//			mail('ryan@bevomedia.com', 'Recurring User Payment Failed', $Body, $Headers);
//	
//				header('Location: /BevoMedia/Publisher/VerifyResearchConfirm.html?ajax=true&Error='.$Result['responsetext']);
//				die;
//				
//				break;
//		}
//	}
	
	Public Function PayPPCYearly()
	{
		$Product = $this->User->GetProduct(User::PRODUCT_PPC_YEARLY_CHARGE);
		
		$Vault = new nmiCustomerVault();
//		$Vault->setCvv('999');
		$Vault->setCustomerVaultId($this->User->vaultID);
		$Vault->charge($Product->Price);
		$Result = $Vault->execute();
		
		
		switch($Result['response'])
		{
			case 1: //Success
				$TransactionID = $Result['transactionid'];
				
				$Array = array (
								'UserID'		=> $this->User->id,
								'ProductID'		=> $Product->ID,
								'Price'			=> $Product->Price,
								'Date'			=> date('Y-m-d H:i:s'),
								'Paid' 			=> 1,
								'PaidDate'		=> date('Y-m-d H:i:s'),
								'TransactionID' => $TransactionID,
								
								);
				
				$this->db->insert('bevomedia_user_payments', $Array);

				header('Location: /BevoMedia/User/VerifyPPCFinish.html?ajax=true');
				die;
				break;
			default:
				
				$Body = "Research payment for user \"{$UserID}\" has failed.<br /><br />
		    			 Error: {$Result['responsetext']}<br /><br />
		    			 Amount:  \${$Product->Price}		    	
	    				";
				 
				$Headers  = 'MIME-Version: 1.0' . "\r\n";
				$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				 
	//			mail('ryan@bevomedia.com', 'Recurring User Payment Failed', $Body, $Headers);
	
				header('Location: /BevoMedia/Publisher/VerifyPPCConfirm.html?ajax=true&Error='.$Result['responsetext']);
				die;
				
				break;
		}
	}
	
	Public Function PayPPVSpyYearly()
	{
		$Product = $this->User->GetProduct(User::PRODUCT_PPVSPY_YEARLY);
		$Price = $this->User->GetPPVSpyOneTimePrice();
		
		$Vault = new nmiCustomerVault();
//		$Vault->setCvv('999');
		$Vault->setCustomerVaultId($this->User->vaultID);
		$Vault->charge($Price);
		$Result = $Vault->execute();
		
		
		
		switch($Result['response'])
		{
			case 1: //Success
				$TransactionID = $Result['transactionid'];
				
				$Array = array (
								'UserID'		=> $this->User->id,
								'ProductID'		=> $Product->ID,
								'Price'			=> $Price,
								'Date'			=> date('Y-m-d H:i:s'),
								'Paid' 			=> 1,
								'PaidDate'		=> date('Y-m-d H:i:s'),
								'TransactionID' => $TransactionID,
								
								);
				
				$this->db->insert('bevomedia_user_payments', $Array);

				header('Location: /BevoMedia/User/VerifyPPVSpyFinish.html?ajax=true');
				die;
				break;
			default:
				
				$Body = "Research payment for user \"{$UserID}\" has failed.<br /><br />
		    			 Error: {$Result['responsetext']}<br /><br />
		    			 Amount:  \${$Product->Price}		    	
	    				";
				 
				$Headers  = 'MIME-Version: 1.0' . "\r\n";
				$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				 
	//			mail('ryan@bevomedia.com', 'Recurring User Payment Failed', $Body, $Headers);
	
				header('Location: /BevoMedia/Publisher/VerifyPPVSpyConfirm.html?ajax=true&Error='.$Result['responsetext']);
				die;
				
				break;
		}
	}
	
	Public Function PayPPVSpyMonthly()
	{
		$Product = $this->User->GetProduct(User::PRODUCT_PPVSPY_MONTHLY);
		
		$Vault = new nmiCustomerVault();
//		$Vault->setCvv('999');
		$Vault->setCustomerVaultId($this->User->vaultID);
		$Vault->charge($Product->Price);
		$Result = $Vault->execute();
		
		
		switch($Result['response'])
		{
			case 1: //Success
				$TransactionID = $Result['transactionid'];
				
				$Array = array (
								'UserID'		=> $this->User->id,
								'ProductID'		=> $Product->ID,
								'Price'			=> $Product->Price,
								'Date'			=> date('Y-m-d H:i:s'),
								'Paid' 			=> 1,
								'PaidDate'		=> date('Y-m-d H:i:s'),
								'TransactionID' => $TransactionID,
								
								);
				
				$this->db->insert('bevomedia_user_payments', $Array);

				header('Location: /BevoMedia/User/VerifyPPVSpyFinish.html?ajax=true&monthly=1');
				die;
				break;
			default:
				
				$Body = "Research payment for user \"{$UserID}\" has failed.<br /><br />
		    			 Error: {$Result['responsetext']}<br /><br />
		    			 Amount:  \${$Product->Price}		    	
	    				";
				 
				$Headers  = 'MIME-Version: 1.0' . "\r\n";
				$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				 
	//			mail('ryan@bevomedia.com', 'Recurring User Payment Failed', $Body, $Headers);
	
				header('Location: /BevoMedia/Publisher/VerifyPPVSpyConfirm.html?ajax=true&Error='.$Result['responsetext']);
				die;
				
				break;
		}
	}
	
	Public Function PayAdWatcherYearly()
	{
		$Product = $this->User->GetProduct(User::PRODUCT_ADWATCHER_YEARLY);
		$Price = $Product->Price;
		
		$Vault = new nmiCustomerVault();
		$Vault->setCustomerVaultId($this->User->vaultID);
		$Vault->charge($Price);
		$Result = $Vault->execute();
		
		
		
		switch($Result['response'])
		{
			case 1: //Success
				$TransactionID = $Result['transactionid'];
				
				$Array = array (
								'UserID'		=> $this->User->id,
								'ProductID'		=> $Product->ID,
								'Price'			=> $Price,
								'Date'			=> date('Y-m-d H:i:s'),
								'Paid' 			=> 1,
								'PaidDate'		=> date('Y-m-d H:i:s'),
								'TransactionID' => $TransactionID,
								
								);
				
				$this->db->insert('bevomedia_user_payments', $Array);
				
				$Array = array (
								'UserID'		=> $this->User->id,
								'Registered'	=> 0,
								);
				$this->db->insert('bevomedia_product_adwatcher', $Array);

				header('Location: /BevoMedia/User/VerifyAdWatcherFinish.html?ajax=true');
				die;
				break;
			default:
				
				$Body = "Research payment for user \"{$UserID}\" has failed.<br /><br />
		    			 Error: {$Result['responsetext']}<br /><br />
		    			 Amount:  \${$Product->Price}		    	
	    				";
				 
				$Headers  = 'MIME-Version: 1.0' . "\r\n";
				$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				 
	//			mail('ryan@bevomedia.com', 'Recurring User Payment Failed', $Body, $Headers);
	
				header('Location: /BevoMedia/Publisher/VerifyAdWatcherConfirm.html?ajax=true&Error='.$Result['responsetext']);
				die;
				
				break;
		}
	}
	
	Public Function PayAdWatcherMonthly()
	{
		$Product = $this->User->GetProduct(User::PRODUCT_ADWATCHER_MONTHLY);
		
		$Vault = new nmiCustomerVault();
		$Vault->setCustomerVaultId($this->User->vaultID);
		$Vault->charge($Product->Price);
		$Result = $Vault->execute();
		
		
		switch($Result['response'])
		{
			case 1: //Success
				$TransactionID = $Result['transactionid'];
				
				$Array = array (
								'UserID'		=> $this->User->id,
								'ProductID'		=> $Product->ID,
								'Price'			=> $Product->Price,
								'Date'			=> date('Y-m-d H:i:s'),
								'Paid' 			=> 1,
								'PaidDate'		=> date('Y-m-d H:i:s'),
								'TransactionID' => $TransactionID,
								
								);
				
				$this->db->insert('bevomedia_user_payments', $Array);

				$Array = array (
								'UserID'		=> $this->User->id,
								'Registered'	=> 0,
								);
				$this->db->insert('bevomedia_product_adwatcher', $Array);
				
				header('Location: /BevoMedia/User/VerifyAdWatcherFinish.html?ajax=true&monthly=1');
				die;
				break;
			default:
				
				$Body = "Research payment for user \"{$UserID}\" has failed.<br /><br />
		    			 Error: {$Result['responsetext']}<br /><br />
		    			 Amount:  \${$Product->Price}		    	
	    				";
				 
				$Headers  = 'MIME-Version: 1.0' . "\r\n";
				$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				 
	//			mail('ryan@bevomedia.com', 'Recurring User Payment Failed', $Body, $Headers);
	
				header('Location: /BevoMedia/Publisher/VerifyAdWatcherConfirm.html?ajax=true&Error='.$Result['responsetext']);
				die;
				
				break;
		}
	}
	
	Public Function PaySelfHostedYearly()
	{
		$Product = $this->User->GetProduct(User::PRODUCT_SELF_HOSTED_YEARLY_CHARGE);
		
		$Vault = new nmiCustomerVault();
//		$Vault->setCvv('999');
		$Vault->setCustomerVaultId($this->User->vaultID);
		$Vault->charge($Product->Price);
		$Result = $Vault->execute();
		
		
		switch($Result['response'])
		{
			case 1: //Success
				$TransactionID = $Result['transactionid'];
				
				$Array = array (
								'UserID'		=> $this->User->id,
								'ProductID'		=> $Product->ID,
								'Price'			=> $Product->Price,
								'Date'			=> date('Y-m-d H:i:s'),
								'Paid' 			=> 1,
								'PaidDate'		=> date('Y-m-d H:i:s'),
								'TransactionID' => $TransactionID,
								
								);
				
				$this->db->insert('bevomedia_user_payments', $Array);

				header('Location: /BevoMedia/User/VerifySelfHostedFinish.html?ajax=true');
				die;
				break;
			default:
				
				$Body = "Research payment for user \"{$UserID}\" has failed.<br /><br />
		    			 Error: {$Result['responsetext']}<br /><br />
		    			 Amount:  \${$Product->Price}		    	
	    				";
				 
				$Headers  = 'MIME-Version: 1.0' . "\r\n";
				$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				 
	//			mail('ryan@bevomedia.com', 'Recurring User Payment Failed', $Body, $Headers);
	
				header('Location: /BevoMedia/Publisher/VerifySelfHostedConfirm.html?ajax=true&Error='.$Result['responsetext']);
				die;
				
				break;
		}
	}
	
	Public Function PayAdwords()
	{
		$Product = $this->User->GetProduct(User::PRODUCT_GOOGLE_ADWORDS);
		
		$Array = array (
								'UserID'		=> $this->User->id,
								'ProductID'		=> $Product->ID,
								'Price'			=> $Product->Price,
								'Date'			=> date('Y-m-d H:i:s'),
								'Paid' 			=> 1,
								'PaidDate'		=> date('Y-m-d H:i:s'),
								'TransactionID' => 0,								
								);
				
		$this->db->insert('bevomedia_user_payments', $Array);
		
		header('Location: /BevoMedia/Publisher/GoogleAdwordsAPI.html');
		die;
	}
	
	Public Function Invoice()
	{
		$this->defaultDateRange = date('m/d/Y', strtotime('-1 month')).' - '.date('m/d/Y');
		
		if (isset($_GET['Invoice'])) {
			
			$Sql = "SELECT
						TransactionID
					FROM
						bevomedia_user_payments
					WHERE
						(bevomedia_user_payments.ID = ?) AND
						(bevomedia_user_payments.UserID = ?)			
					";
			$Row = $this->db->fetchRow($Sql, array($_GET['Invoice'], $this->User->id));
			
			if (isset($Row->TransactionID)) 
			{
				$Arr = array('RequestedInvoice' => 1);
				$this->db->update('bevomedia_user_payments', $Arr, ' id = '.intval($_GET['Invoice']));
				
//				$Text = "User has requested an invoice. Transaction ID: {$Row->TransactionID} ";
//				mail('invoice@bevomedia.com', 'Invoice Request', $Text);

								
				header('Location: /BevoMedia/User/Invoice.html'.(isset($_GET['DateRange'])?'?DateRange='.$_GET['DateRange']:''));
				die;
			}
			
		}
		
		$StartDate = date('Y-m').'-1';
		$EndDate = date('Y-m-d');
		
		if (isset($_GET['DateRange']))
		{
			$StartDate = explode(' - ',$_GET['DateRange']);
			
			if (count($StartDate)>1) 
			{
				$EndDate = $StartDate[1];
			} else
			{
				$EndDate = $StartDate[0];
			}
			
			$EndDate = date('Y-m-d', strtotime($EndDate));
			$StartDate = date('Y-m-d', strtotime($StartDate[0]));
		}
		
		
		$Sql = "SELECT
					bevomedia_user_payments.*,
					bevomedia_products.Quantity,
					bevomedia_products.ProductName
				FROM
					bevomedia_user_payments,
					bevomedia_products
				WHERE
					(bevomedia_user_payments.ProductID = bevomedia_products.ID) AND
					(DATE(bevomedia_user_payments.Date) BETWEEN ? AND ?) AND
					(bevomedia_user_payments.Deleted = 0) AND 
					(bevomedia_user_payments.Price > 0) AND
					(bevomedia_user_payments.UserID = ?)
				ORDER BY
					Date
				";
		$this->Payments = $this->db->fetchAll($Sql, array($StartDate, $EndDate, $this->User->id));
	}
	
	Public Function Referrals()
	{
		$this->Referrals = $this->User->ListReferrals();
	}
	
	Public Function Index() 
	{
		$this->VerifiedBoxFirstLogin = false;
		
		$Sql = "SELECT
					ID
				FROM
					bevomedia_new_rev_model_login
				WHERE
					(UserID = ?)		
				";
		$Row = $this->db->fetchRow($Sql, $this->User->id);
		if (!isset($Row->ID)) {
			$this->VerifiedBoxFirstLogin = true;
			
			if (!isset($_GET['TUTORIAL']))
			{
				$Arr = array('UserID' => $this->User->id);
				$this->db->insert('bevomedia_new_rev_model_login', $Arr);
			}
		}		
	}
	
Public Function MyProducts()
	{
		if (isset($_GET['ID'])) {
			$Sql = "SELECT
						ID
					FROM	
						bevomedia_user_payments
					WHERE
						(bevomedia_user_payments.ID = ?) AND
						(bevomedia_user_payments.UserID = ?)			
					";
			$Check = $this->db->fetchRow($Sql, array($_GET['ID'], $this->User->id));
			if (isset($Check->ID)) {
				$Arr = array('Cancelled' => 1);
				$this->db->update('bevomedia_user_payments', $Arr, ' ID = '.intval($_GET['ID']));
			}
		}
		
		if (isset($_GET['UpgradeID'])) {
			
			$Sql = "SELECT
						bevomedia_user_payments.ID,
						bevomedia_user_payments.Date,
						DATE_ADD(bevomedia_user_payments.Date, interval bevomedia_products.TermLength day) as `TermEnds` 
					FROM	
						bevomedia_user_payments,
						bevomedia_products
					WHERE
						(bevomedia_products.ID = bevomedia_user_payments.ProductID) AND 
						(bevomedia_user_payments.ID = ?) AND
						(bevomedia_user_payments.UserID = ?)			
					";
			$Check = $this->db->fetchRow($Sql, array($_GET['UpgradeID'], $this->User->id));
			if (isset($Check->ID)) {				
				$Product = $this->User->GetProduct(User::PRODUCT_PPVSPY_YEARLY);			
				$Array = array (
							'UserID' => $this->User->id,
							'ProductID' => $Product->ID,
							'Price'	=> $Product->Price,
							'Date' => $Check->TermEnds,
							'Paid' => 0
							);			
				$this->db->insert('bevomedia_user_payments', $Array);	
				
				header('Location: /BevoMedia/User/MyProducts.html');
				die;
			}
			
		}
		
		$Sql = "SELECT
					bevomedia_user_payments.ID,
					ProductName,
					bevomedia_user_payments.Price,
					bevomedia_products.TermLength,
					bevomedia_user_payments.Date,
					bevomedia_user_payments.Cancelled
				FROM
					bevomedia_user_payments,
					bevomedia_products
				WHERE
					(bevomedia_products.ID = bevomedia_user_payments.ProductID) AND
					(bevomedia_user_payments.UserID = ?) AND
					(bevomedia_products.ProductName <> 'API Calls') AND
					(bevomedia_products.ProductName <> 'Server Charge') AND
					(bevomedia_products.ProductName NOT LIKE 'Free %') AND
					(DATE_ADD(bevomedia_user_payments.Date, interval bevomedia_products.TermLength day) > NOW()) AND
					(bevomedia_user_payments.Deleted = 0)
				";
		$this->Products = $this->db->fetchAll($Sql, $this->User->id);
	}
	
	public function Webinar()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		
		$Sql = "SELECT
					*
				FROM	
					bevomedia_webinars
				WHERE
					(bevomedia_webinars.Date >= NOW())
				LIMIT 1
				";
		$this->WebinarInfo = $this->db->fetchRow($Sql);
		
		if (isset($this->WebinarInfo->ID))
		{
			$Sql = "SELECT
						ID
					FROM
						bevomedia_webinar_users
					WHERE
						(bevomedia_webinar_users.UserID = ?)		
					";
			$UserOptIn = $this->db->fetchRow($Sql, array($this->User->id));
			if (!isset($UserOptIn->ID)) 
			{
				header('Location: /BevoMedia/User/WebinarOptIn.html');
				die;
			}
		}
	}
	
	Public Function WebinarOptIn()
	{
		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
		
		if (isset($_GET['optIn']))
		{
			$Array = array( 'UserID' => $this->User->id );
			$this->db->insert('bevomedia_webinar_users', $Array);
			
			header('Location: /BevoMedia/User/Webinar.html');
			die;
		}
	}
	
	Public Function OpenAdWatcher()
	{
		$Sql = "SELECT
					*
				FROM
					bevomedia_product_adwatcher
				WHERE
					(bevomedia_product_adwatcher.UserID = ?)		
				";
		$Row = $this->db->fetchRow($Sql, array($this->User->id));
		if (!isset($Row->ID)) {
			header('Location: /');
			die;
		}
		
		if ($Row->Registered==0) 
		{
			$Array = array('Registered' => 1);
			$this->db->update('bevomedia_product_adwatcher', $Array, 'ID = '.$Row->ID);
			
			header('Location: http://www.example.com/register?email='.$Row->email);
			die;
		} else
		{
			$password = strrev($Row->email);
			$hash = md5($password);

			header('Location: http://www.example.com/login?u='.$Row->email.'&p='.$password);
			die;
		}
		
		die;
	}
	

	public function jsonUserPerformanceConnector()
	{ 
		$promoMethodsMapper = array(1 => 3, 2 => 5, 3 => 2, 4 => 1, 5 => 4);//key = v2, value = v3
		$promoMethodsMapper = array_flip($promoMethodsMapper);
		$experienceLevelsMapper = array(1 => 1, 2 => 2, 3 => 3);//key = v2, value = v3
		$experienceLevelsMapper = array_flip($experienceLevelsMapper);
		$nichesMapper = array(
								1 => 3, 2 => 7, 3 => 30, 4 => 8, 5 => 19, 
								6 => 4, 7 => 1, 8 => 20, 9 => 29, 10 => 9, 
								11 => 2, 12 => 6, 13 => 13, 14 => 10, 15 => 23, 
								16 => 27, 17 => 16, 18 => 26, 19 => 12, 20 => 15, 
								21 => 17, 22 => 11, 23 => 25, 24 => 14, 25 => 28, 
								26 => 22, 27 => 5, 28 => 24, 29 => 18, 30 => 21, 
								);//key = v3, value = v2
								
		$messengers = array(1 => 'AIM', 2 => 'Gtalk', 3 => 'ICQ', 4 => 'MSN', 5 => 'Skype', 6 => 'Yahoo');
								
		
		$jsonData = urldecode($_POST['data']);
		$data = (array)json_decode($jsonData); 

		$this->User->clearPerformanceConnectorNiches();
		foreach ($data['performanceConnector']->niches as $nicheId) {
			$nicheId = $nichesMapper[$nicheId];
			$this->User->insertPerformanceConnectorNiche($nicheId);
		}
		
		$this->User->clearPerformanceConnectorPromoMethod();
		foreach ($data['performanceConnector']->promotionMethods as $promoId) {
			$promoId = $promoMethodsMapper[$promoId];
			$this->User->insertPerformanceConnectorPromoMethod($promoId);
		}
		
		$this->User->clearPerformanceConnectorExpLevel();
		$expId = $experienceLevelsMapper[$data['performanceConnector']->promotionExperience];
		$this->User->insertPerformanceConnectorExpLevel($expId);
		
		
		$this->User->clearPerformanceConnectorContactEntries();	
		$this->User->insertPerformanceConnectorContactEntry($messengers[$data['userMessenger']['_messenger']], $data['userMessenger']['_userName'], $data['userMessenger']['_phone']);
		
		die;
	}
	
	public function jsonVerifyUnverifyUser()
	{ 
		$jsonData = urldecode($_POST['data']);
		$data = (array)json_decode($jsonData); 
		
		$userId = (int) $data['userId'];
		$verify = (bool) $data['verify'];

		if ($verify) {
			$sql = "UPDATE bevomedia_user SET vaultID = 1, vaultLast4Digits = 1234 WHERE id = {$userId} ";
			$this->db->exec($sql);
		} else {
			$sql = "UPDATE bevomedia_user SET vaultID = 0, vaultLast4Digits = 0 WHERE id = {$userId} ";
			$this->db->exec($sql);
		}
		
		
		die;
	}
	
	public function jsonInsertNicheTopOffer()
	{ 
		$nichesMapper = array(
								1 => 3, 2 => 7, 3 => 30, 4 => 8, 5 => 19, 
								6 => 4, 7 => 1, 8 => 20, 9 => 29, 10 => 9, 
								11 => 2, 12 => 6, 13 => 13, 14 => 10, 15 => 23, 
								16 => 27, 17 => 16, 18 => 26, 19 => 12, 20 => 15, 
								21 => 17, 22 => 11, 23 => 25, 24 => 14, 25 => 28, 
								26 => 22, 27 => 5, 28 => 24, 29 => 18, 30 => 21, 
								);//key = v3, value = v2
		
		$jsonData = urldecode($_POST['data']);
		$data = json_decode($jsonData);
		
		$nicheId = $nichesMapper[$data->nicheId];
		$networkOfferId = $data->networkOfferId;
		$networkId = $data->networkId;
		
		$sql = "SELECT id FROM bevomedia_offers WHERE network__id = ? AND offer__id = ?";
		$offerInfo = $this->db->fetchRow($sql, array($networkId, $networkOfferId));

		if (!isset($offerInfo->id)) {
			die;
		}
		
		$sql = "SELECT id, offerId FROM bevomedia_niche_top_offers WHERE nicheId = ? AND offerId = ?";
		$row = $this->db->fetchRow($sql, array($nicheId, $offerInfo->id));
		
		if (!isset($row->id))
		{
			$this->db->insert('bevomedia_niche_top_offers', array('nicheId' => $nicheId, 'offerId' => $offerInfo->id));
		}
		
		die;
	}
	
	public function jsonDeleteNicheTopOffer()
	{ 
		$nichesMapper = array(
								1 => 3, 2 => 7, 3 => 30, 4 => 8, 5 => 19, 
								6 => 4, 7 => 1, 8 => 20, 9 => 29, 10 => 9, 
								11 => 2, 12 => 6, 13 => 13, 14 => 10, 15 => 23, 
								16 => 27, 17 => 16, 18 => 26, 19 => 12, 20 => 15, 
								21 => 17, 22 => 11, 23 => 25, 24 => 14, 25 => 28, 
								26 => 22, 27 => 5, 28 => 24, 29 => 18, 30 => 21, 
								);//key = v3, value = v2
		
		$jsonData = urldecode($_POST['data']);
		$data = json_decode($jsonData);
		
		$nicheId = $nichesMapper[$data->nicheId];
		$networkOfferId = $data->networkOfferId;
		$networkId = $data->networkId;
		
		$sql = "SELECT id FROM bevomedia_offers WHERE network__id = ? AND offer__id = ?";
		$offerInfo = $this->db->fetchRow($sql, array($networkId, $networkOfferId));

		if (!isset($offerInfo->id)) {
			die;
		}
		
		$sql = "DELETE FROM bevomedia_niche_top_offers WHERE nicheId = {$nicheId} AND offerId = {$offerInfo->id} ";
		$this->db->exec($sql);
		
		die;
	}
	
	public function jsonInsertUser()
	{
//		print_r($_POST);
		$jsonData = urldecode($_POST['data']);
		$data = json_decode($jsonData);
//		print_r($data);
		$postData = array();
		$postData['FirstName'] = $data->user->_firstName;
		$postData['LastName'] = $data->user->_lastName;
		$postData['Email'] = $data->user->_email;
		$postData['Password'] = $data->user->_password1;
		$postData['re-enter_password'] = $data->user->_password1;
		$postData['CompanyName'] = $data->user->_companyName;
		$postData['Address'] = $data->user->_address;
		$postData['City'] = $data->user->_city;
		$postData['State'] = $data->user->_state;
		$postData['Zip'] = $data->user->_zip;
		$postData['Country'] = $data->user->_country;
		$postData['apiKey'] = $data->user->_apiKey;
		$postData['Phone'] = '';
		$postData['Website'] = '';
		$postData['MarketingMethodOther'] = $data->user_messenger->_messenger;
		$postData['MessengerHandle'] = $data->user_messenger->_userName;
		$postData['Username'] = $data->user->_username;
		$postData['HowHeard'] = $data->how_heard->_howHeard;
		$postData['Comments'] = $data->how_heard->_comment;
		
		
		$postData['Timezone'] = 'Etc/GMT+12';
		
		$postData['EULAAccepted'] = '1';
		$postData['registerFormSubmit'] = 'Submit Query';
		
//		echo "postData:<br />\n";
//		print_r($postData);
		
//		echo "_POST:<br />\n";
		$_POST = $postData;
//		print_r($_POST);
//		print_r($postData);
		$this->Register();
		die('end jsonInsertUser');
	}
	
	Public Function App()
	{
		Zend_Registry::set('Instance/LayoutType', 'blank-layout');
	}
	
}

?>
