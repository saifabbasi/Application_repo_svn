<?php
require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');

Class APIController extends ClassComponent
{
	Public Function __construct()
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		$this->{'PageHelper'} = new PageHelper();
		Zend_Registry::set('Instance/LayoutType', 'empty-layout');
	}
	
	Public Function SelfHostedAuthentication()
	{
	    $User = new User();
	    $id = $User->getIdUsingEmail($_GET['username']);
    	$User = new User($id);
    	$loginAttempt = $User->login($_GET['username'], md5($_GET['password']));
    	$md5loginAttempt = $User->login($_GET['username'], $_GET['password']);
		$login = $loginAttempt || $md5loginAttempt;
    	$latestFinal = intval($this->_db->fetchOne("select id from bevomedia_selfhost_version where public=1 order by id desc limit 1"));
    	$latestBeta = intval($this->_db->fetchOne("select id from bevomedia_selfhost_version order by id desc limit 1"));
    	
		$versions = array(
						'latestVersion' => 236, /*
							This is the old value that selfhost copies checked for a new version
							before migrating to the new update process. This version should be set
							to the release # of the selfhost software that contains the migration
							between old-and-new update processes.
						 */
						'latestFinal' => $latestFinal,
						'latestBeta' => $latestBeta);
    	if($login)
    	{
    	    if($User->IsSelfHosted() != '1')
    	    {
    		    $this->_db->update('bevomedia_user', array('isSelfHosted'=> 1, 'apiKey' => md5($id*rand(1024) + print_r(microtime(), true))), "id=$id");

    		    $User = new User($id);
    	    }
    	    
    	    if ( ($User->membershipType!='premium') && ($User->vaultID==0) )
    	    {
    	    	$arr =  array(
    	                  'membershipType' => $User->membershipType,
    	                  'lastNetworkUpdate' => $User->lastNetworkUpdate,
						  'lastPPCUpdate' => $User->lastPPCUpdate,
						  'error' => 'You must have BevoMedia premium and verified account in order to install the Bevo Self Hosted version.'
						  );
			    if ($User->vaultID==0) {
			    	$arr['verified'] = false;	
			    }
			    
	    	    print json_encode(array_merge($arr, $versions));
	    	    exit;
    	    }
    	       	    
    	    
    	    $arr =  array('apiKey' => $User->apiKey,
    	                  'apiCalls' => $User->apiCalls,
    	    			  'totalApiCalls' => $User->GetTotalApiCallsMTD(),
    	                  'membershipType' => $User->membershipType,
    	                  'lastNetworkUpdate' => $User->lastNetworkUpdate,
						  'lastPPCUpdate' => $User->lastPPCUpdate,
    	    			  'ppcSignedUp' => ($User->IsSubscribed(User::PRODUCT_FREE_PPC) || $User->IsSubscribed(User::PRODUCT_PPC_YEARLY_CHARGE)),
    	    			  'ppvSignedUp' => $User->IsSubscribed(User::PRODUCT_PPVSPY_MONTHLY) || $User->IsSubscribed(User::PRODUCT_PPVSPY_YEARLY),
    	    			 );
    	    print json_encode(array_merge($arr, $versions));
    	    exit;
    	}
    	print json_encode(array_merge($versions, array('error' => 'The Bevomedia.com credentials you entered are incorrect!')));
    	exit;
	}
	
	Public Function SqlTables()
	{
	  // Check for API key
	  ignore_user_abort(true);
	  if(!isset($_GET['apiKey']))
	  {
		echo json_encode(array('error' => 'Invalid API key'));
		exit;
	  }
	  $apiKey = mysql_real_escape_string(@$_GET['apiKey']);
	  $finduserId = mysql_query("select id, created from bevomedia_user where apiKey='$apiKey'");
	  if(mysql_num_rows($finduserId) == 0)
	  {
		  echo Zend_Json::encode(array('error' => 'No such user'));
		  exit;
	  }
	  $user = mysql_fetch_assoc($finduserId);
	  $userId = @$user['id'];
	  $lastVersion = (int)$_GET['since'];
	  foreach(glob('/var/www/bevomedia/SQL/trunk/SelfHosted/[0-9]*-*.sql') as $file)
	  {
		$p = explode('-', $file);
		$n = explode('/', $p[0]);
		$v = (int)$n[count($n)-1];
		if($v > $lastVersion)
		  echo file_get_contents($file) . ";\n";
	  }
	  exit;
	}
	Public Function SelfHostedFiles()
	{
	  ignore_user_abort(true);
	  if(!isset($_GET['apiKey']))
	  {
		echo json_encode(array('error' => 'Invalid API key'));
		exit;
	  }
	$apiKey = mysql_real_escape_string(@$_GET['apiKey']);
	$finduserId = mysql_query("select id, created from bevomedia_user where apiKey='$apiKey'");
	  if(mysql_num_rows($finduserId) == 0)
	  {
		  echo Zend_Json::encode(array('error' => 'No such user'));
		  exit;
	  }
	  $user = mysql_fetch_assoc($finduserId);
	  $userId = @$user['id'];
	  if(empty($_GET['file']))
	  {
		require_once(PATH . 'DirectoryHelper.include.php');
		$files = directoryToChecksumArray(PATH.'../../../../selfhost/', true);
		die(json_encode($files));
	  }
	  else
	  {
		$f = $_GET['file'];
		$f = preg_replace('@\.\.@', '', preg_replace('@\/\/@si', '', $f));
		$f = PATH.'../../../../selfhost'.$f;
		if(file_exists($f))
		  die(base64_encode(file_get_contents($f)));
	  }
	}

	Public Function SelfHostedUpdate()
	{
	    ignore_user_abort(true);
    	require_once(PATH . 'SelfHostedUpdate.class.php');
    	require_once(PATH . 'CatchUpJson.class.php');
    	require_once 'Zend/Json.php';
    	if(!isset($_GET['apiKey']))
    	{
    	    echo Zend_Json::encode(array('error' => 'Invalid API key'));
    		exit;
    	}
    	$apiKey = mysql_real_escape_string(@$_GET['apiKey']);
    	$finduserId = mysql_query("select id, created, membershipType, vaultID from bevomedia_user where apiKey='$apiKey'");
    	if(mysql_num_rows($finduserId) == 0)
    	{
    	    echo Zend_Json::encode(array('error' => 'No such user'));
    	    exit;
    	}
    	$user = mysql_fetch_assoc($finduserId);
    	$userId = @$user['id'];
    	
    	if ( (@$user['membershipType']!='premium') && (@$user['vaultID']==0) )
    	{
    		echo Zend_Json::encode(array('error' => 'You need to have a premium account to upgrade/update your stats'));
    	    exit;
    	}
    	
    	$shu = new SelfHostedUpdate($userId);
    
		$cu = new CatchUpJson($shu->userId);
		if(empty($_GET['startDate']))
		{
			$date = date('Y-m-d', strtotime($user['created']));
		}else{
			$date = date('Y-m-d', strtotime($_GET['startDate']));
		}
    	if(true || $shu->updateReady())
    	{
    		mysql_query("update bevomedia_user set lastSelfhostUpdate=NOW() where id=".$userId);
    		$cdate = $shu->getCooldownDate();
    		$shu->updateCooldown();
    		$json = Zend_Json::encode($cu->data($date));
    		print $json;
		} else {
		  print Zend_Json::encode(array());
		}
    	exit;
	}
	
	Public Function GenerateNewAPIKeys()
	{
	    $updated = $this->_db->update('bevomedia_user', array('apiKey'=>''), 'isSelfHosted!=1');
	    foreach($this->_db->fetchAll('select id from bevomedia_user where isSelfHosted=1') as $user)
	        $updated += $this->_db->update('bevomedia_user', array('apiKey' => md5(print_r(microtime(), true) + $user->id*rand(1024))), 'id='.$user->id);
	    echo 'Updated ' . $updated . ' API keys';
	}
	
	Public Function QueuePending()
	{
		echo $this->_db->fetchOne('SELECT COUNT(*) FROM bevomedia_queue WHERE started=0'); 
		exit;
	}
	
	Public Function QueueInProgress()
	{
		echo $this->_db->fetchOne('SELECT COUNT(*) FROM bevomedia_queue WHERE started!=0 AND completed=0');
		exit;
	}
	
	Public Function QueueAverageWait()
	{
		echo floatval($this->_db->fetchOne('SELECT SUM(COALESCE(completed-created,0))/ count(*) as wait FROM bevomedia_queue WHERE completed>(now()-3600*1) AND completed!=0'));
		exit;
	}
	
	Public Function TrackingHits()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_tracker_clicks WHERE clickTime>=(unix_timestamp()-60*15)'));
		exit;
	}
	
	Public Function UserCount()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user'));
		exit;
	}
	Public Function UserPremiumCount()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE membershipType="premium" and deleted=0'));
		exit;
	}
	Public Function UserSelfhostCount()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE isSelfHosted=1 and deleted=0'));
		exit;
	}
	Public Function UserEnabledCount()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE enabled=1 and deleted=0'));
		exit;
	}
	Public Function UserDeletedCount()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE deleted=1'));
		exit;
	}
	Public Function UserPendingApprovalCount()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE deleted=0 and enabled=0'));
		exit;
	}
	Public Function UsersLoggedInToday()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE lastLogin >= DATE_SUB(NOW(), INTERVAL 1 DAY)'));
		exit;
	}
	Public Function UsersLoggedInThisWeek()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE lastLogin >= DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
		exit;
	}
	Public Function SelfhostLoggedInToday()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE lastSelfhostUpdate >= DATE_SUB(NOW(), INTERVAL 1 DAY)'));
		exit;
	}
	Public Function SelfhostLoggedInThisWeek()
	{
		echo floatval($this->_db->fetchOne('SELECT count(*) FROM bevomedia_user WHERE lastSelfhostUpdate >= DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
		exit;
	}
	
	Public Function PPCQueueSubmit()
	{
		require_once(PATH . 'SelfHostedUpdate.class.php');
		if(!isset($_GET['apiKey']) || empty($_GET['apiKey']))
		{
		    die('Your BevoLive credentials are incorrect, or the login was rejected.');
	    	exit;
		}
		$apiKey = mysql_real_escape_string(@$_GET['apiKey']);
		$finduserId = mysql_query("select id from bevomedia_user where apiKey='$apiKey'");
		if(mysql_num_rows($finduserId) == 0)
		{
		    die('No BevoLive user found for this API key');
	    	exit;
		}
		$user = mysql_fetch_assoc($finduserId);
		$userId = @$user['id'];
		if(!empty($_POST) && isset($_POST['jsonObj']))
		{
			$json = @$_POST['jsonObj'];
		} else {
	    	die ('No data submitted');
		}
		require_once(PATH . 'CreatePPC.class.php');
		$CreatePPC = new CreatePPC($json);
		$jsonObj = $CreatePPC->queueInsert($userId);
		$jsonObj->processDelete();
		$this->jsonObj = $jsonObj;
?>
<script src='/Themes/BevoMedia/createppc.js'></script>


<div id='createppc-head' class='header'>
	<h1>Your job has been added to the queue</h1>
	<br class='clearBoth'/>
</div>

<div id='createppc' >

<ul class='menu'>
	<li style='width:20%;'>
		<a class='active' id='createppc_menu-results'>
			Results
		</a>
	</li>
	<li style='width:80%;'>
		&nbsp;
	</li>
</ul>
<br class='clearBoth'/>

<h3>
	Your items have been added to the PPC Create Queue.
</h3>

<br/>
Your campaign additions have been added to your Campaign Editor Queue. <br/>
You may close your browsers or navigate throughout the site as you wish. <br/>
<a onclick="return BevoLive(this,true);" href="http://beta.bevomedia.com/BevoMedia/Publisher/PPCQueueProgress.html?iframe=true&apiKey=<?=$_GET['apiKey']; ?>">
	View your queue progress in the Campaign Editor Queue.
</a> There is also a link to your Campaign Editor Queue in the resource box to your right.<br />
Campaigns you have created may take up to 15-20 minutes to appear in your PPC Account, and up to an hour to appear in your selfhosted Bevo account.
<br/><br/>
<?php
		exit;
	}

	Public Function PaypalPostback()
	{
		include_once (PATH . 'Paypal.class.php');
		$myPaypal = new Paypal();
		
		// Log the IPN results
		$myPaypal->ipnLog = TRUE;
		
		// Enable test mode if needed
		//$myPaypal->enableTestMode();
		// Check validity and write down it
		if ($myPaypal->validateIpn())
		{
		    if ($myPaypal->ipnData['txn_type'] == 'subscr_payment')
		    {
         		$PremiumOrder = new PremiumOrder(@$_POST['custom']);
         		$PremiumOrder->SetPaidAmount(floatval(@$_POST['payment_gross']));
				$PremiumOrder->SetActive();
				
				$MailComponentObject = new MailComponent();
				$MailComponentObject->setFrom('no-reply@bevomedia.com');
	        	
				$User = new User($PremiumOrder->user__id);
				$User->UpdateMembershipType('premium');
				$EmailContent = "Premium Order Information:<br>\r\n
				<br>\r\n
				User: {$User->firstName} {$User->lastName}<br >\r\n
				Phone: {$PremiumOrder->phone}<br>\r\n
				Email: {$PremiumOrder->email}<br>\r\n";
				
				$MailComponentObject->setSubject('Premium Order');
				$MailComponentObject->setHTML($EmailContent);
				$MailComponentObject->send(array('marketplace@bevomedia.com'));
		    } elseif ($myPaypal->ipnData['txn_type'] == 'web_accept')
		    {
		    		        	
				$EmailContent = "Payment recieved:<br>\r\n
				<br>\r\n
				Amount: {$_POST[payment_gross]}<br>\r\n
				Job ID: {$_POST[custom]}<br>\r\n
				Transaction : {$_POST[txn_id]}<br>\r\n
				Paypal Email: {$_POST[payer_email]}<br>\r\n";
				
				$MailComponentObject->setSubject('Marketplace Payment Recieved');
				$MailComponentObject->setHTML($EmailContent);
				$MailComponentObject->send(array('marketplace@bevomedia.com'));
				
		    	$inserts = array();
		    	$inserts['jobId'] = @$_POST['custom'];
		    	$inserts['from'] = @$_POST['payer_email'];
		    	$inserts['amount'] = @$_POST['payment_gross'];
		    	$inserts['txid'] = @$_POST['txn_id'];
		    	$this->_db->insert('bevomedia_marketplace_payment', $inserts);
		    	$this->_db->update('bevomedia_marketplace', array('status'=> 'paid'), 'id='.$_POST['custom']);
		    	$MailComponentObject = new MailComponent();
				$MailComponentObject->setFrom('no-reply@bevomedia.com');
		    }
		    else
		    {
		    	echo 'IPN FAILURE: IPN[payment_status] NOT COMPLETED';
		    }
		}else{
			echo 'IPN FAILURE: IPN VALIDATION FAILED';
		}
		exit;
		//echo 'marketplace@bevomedia.com';
	}
	
	Public Function MSNCronAllUsers()
	{
//		Zend_Registry::set('Instance/LayoutType', 'blank-layout');
//		echo '<pre>';
	}
	
	Public Function ListNetworks()
	{
		if(!isset($_GET['apiKey']))
		{
			echo json_encode(array('error' => 'Invalid API key'));
			exit;
		}
		
		$apiKey = mysql_real_escape_string(@$_GET['apiKey']);
		$finduserId = mysql_query("select id, created from bevomedia_user where apiKey='$apiKey'");
		if(mysql_num_rows($finduserId) == 0)
		{
			echo Zend_Json::encode(array('error' => 'No such user'));
			exit;
		}
  
  
		$Sql = "SELECT
					id,
					title
				FROM
					bevomedia_aff_network
				WHERE
					isValid = 'Y'
				ORDER BY
					id
				";
		$Data = $this->_db->fetchAll($Sql);
		echo Zend_Json::encode($Data);
		die;
	}
	
	Public Function ListOffers()
	{
		if(!isset($_GET['apiKey']))
		{
			echo json_encode(array('error' => 'Invalid API key'));
			exit;
		}
		
		$apiKey = mysql_real_escape_string(@$_GET['apiKey']);
		$finduserId = mysql_query("select id, created from bevomedia_user where apiKey='$apiKey'");
		if(mysql_num_rows($finduserId) == 0)
		{
			echo Zend_Json::encode(array('error' => 'No such user'));
			exit;
		}
  
		if (!isset($_GET['NetworkID']) || (intval($_GET['NetworkID'])==0)) 
		{
			echo Zend_Json::encode(array('error' => 'Invalid Network ID'));
			exit;
		}
  
		$Sql = "SELECT
					offer__id as `offerId`,
					title,
					detail,
					payout,
					dateAdded,
					imageUrl,
					offerType,
					previewUrl
				FROM
					bevomedia_offers
				WHERE
					(archived = 0) AND
					(network__id = ?) AND
					(previewUrl <> '')
				ORDER BY
					title
				";
		$Data = $this->_db->fetchAll($Sql, intval($_GET['NetworkID']));
		echo Zend_Json::encode($Data);
		die;
	}
}