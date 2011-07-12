<?php
	require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/IncludeHelper.include.php');
	require_once (Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/BevoMedia/Common/lib_nmi/nmiCustomerVault.class.php');
	include(PATH.'AbsoluteIncludeHelper.include.php');
	Class AdminController extends ClassComponent
	{
		Public $GUID		= NULL;
		
		Public Function __construct()
		{
			parent::GenerateGUID();
			$this->{'PageHelper'} = new PageHelper();
			Zend_Registry::set('Instance/LayoutType', 'admin-layout');
			require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application').'/Common/Admin.class.php');
			$this->Page = Zend_Registry::get('Instance/Function');
			$this->db = Zend_Registry::get('Instance/DatabaseObj');
			if($this->Page != 'Test')
			{
				if(isset($_SESSION['Admin']['ID']))
				{
					$Admin = new Admin();
					$Admin->getInfo($_SESSION['Admin']['ID']);
					$this->{'Admin'} = $Admin;
					Zend_Registry::set('Instance/LayoutType', 'admin-layout');
				}
				
				if(!isset($_SESSION['Admin']) || !intval($_SESSION['Admin']['ID']))
				{
					$page = Zend_Registry::get('Instance/Function');
					$noLoginNeeded = array('Login', 'ProcessLogin', 'Logout');
					if(!in_array($page, $noLoginNeeded))
					{
						header('Location: /BevoMedia/Admin/Login.html');
						die;
					}
				}
			}
		}
		
		Public Function ManualCharge()
		{
			$this->{'resultMessage'} = '';
			if ( isset ( $_POST['userId'] ) ) {
				$userId = $_POST['userId'];
				$amount = $_POST['amount'];
				
				$query = "
					SELECT
					    *
				    FROM
					    bevomedia_user
				    WHERE
				        bevomedia_user.id = {$userId}
				";
				
				$user = current($this->db->fetchAssoc($query));
				
				$vaultId = $user['vaultID'];
				
				$Vault = new nmiCustomerVault();
				$Vault->setCustomerVaultId($vaultId);
				$Vault->charge($amount);
				
				$Result = $Vault->execute();
				
				
				switch($Result['response'])
				{
					case 1: //Success
						$this->{'resultMessage'} = 'Success!';
						
						break;
					default:
						
						$Body = "Misc payment for user \"{$UserID}\" has failed.<br /><br />
				    			 Error: {$Result['responsetext']}<br /><br />
				    			 Amount:  \${$Total}		    	
			    				";
						 
						$Headers  = 'MIME-Version: 1.0' . "\r\n";
						$Headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						 
						mail('ryan@bevomedia.com', 'Recurring User Payment Failed', $Body, $Headers);
						
						$this->{'resultMessage'} = 'Error! See below and email for details <br/><br/>'. $Body;						
						
						break;
				}
				
				
				
			}
		}
		
		Public Function GivePremium()
		{
			$o = new PremiumOrder();
			$i = array ('user__id' => $_GET['user'],
					'Email' => 'ADMIN',
					'Phone' => 'ADMIN');
			$o->Insert($i);
			$o->SetActive();
			$u = new User();
			$u->getInfo($_GET['user']);
			$u->UpdateMembershipType('premium');
			header('Location: /BevoMedia/Admin/ViewPublisher.html?id='.$_GET['user']);
			exit;
		}	  
		Public Function QueueJSON()
		{	
		  function v($p, $d)
		  {
			if(isset($_REQUEST[$p])&&!empty($_REQUEST[$p]))
			  return mysql_real_escape_string($_REQUEST[$p]);
			else
			  return $d;
		  }
		  function quote_safe($v)
		  {
			return '"' . mysql_real_escape_string($v) . '"';
		  }
		  $sDate = date('Y-m-d', strtotime(v('startDate', 'today'))) . ' 00:00:00';
		  $eDate = date('Y-m-d', strtotime(v('endDate', 'today'))) . ' 23:59:59';
		  $where = "q.created between '$sDate' and '$eDate' and type not like '%REQUEUED'";
		  $filter = json_decode(urldecode(stripslashes((v('filter', '{}')))));
		  $search = v('search', false);
		  if($search)
		  {
			$s = "\"$search\"";
			$ls = "like \"%$search%\"";
			$where .= "
			  and (q.id=$s 
			  or jobId=$s 
			  or user__id=$s 
			  or user__id in (select id from bevomedia_user where email $ls)
			  or type $ls) ";
		  }
		  $filters = array();
		  foreach($filter as $field_in => $vals)
		  {
			$fin = "eq";
			$op = "=";
			if(strpos($field_in, '__'))
			{
			  $fs = explode("__", $field_in);
			  $field_in = $fs[0];
			  switch ($fs[1]) {
			   case 'lt': $op = '<'; break;
			   case 'gt': $op = '>'; break;
			   case 'lte': $op = '<='; break;
			   case 'gte': $op = '>='; break;
			   case 'not': $op = '!='; break;
			   case 'eq': $op = '='; break;
			   case 'in': $op = 'in'; break;
			   case 'like': $op = 'like'; break;
			  }
			}
			switch($field_in) {
				case 'user': $field = 'user__id'; break;
				case 'type': $field = 'type'; break;
			}
			if(is_array($vals))
			{
			  $op = "in";
			  $val = "(".implode(', ', array_map("quote_safe", $vals)) .")";
			} else {
			  $val = '"'.mysql_real_escape_string($vals).'"';
			}
			if(empty($field) || empty($val))
			  break;
			$clause = "$field $op $val";
			$filters[] = $clause;
		  }
		  if(!empty($filters))
			  $where .= "AND " . implode("AND", $filters);
		  $order = v('o', 'id');
		  $orderDir = v('o_dir', 'desc');
		  $lStart = v('start', 0);
		  $lEnd = v('end', 50);
		  $q = "
			SELECT q.id as id, jobId, user__id, u.email as user, type, started, completed, hidden, q.deleted,q.created,
			TIME_TO_SEC(TIMEDIFF(completed, started)) as time,
			REPLACE(instanceIP, 'inet addr:', '') as host
			FROM
			bevomedia_queue q 
			LEFT JOIN bevomedia_user u on u.id = user__id
			WHERE $where
			ORDER BY $order $orderDir LIMIT $lStart, $lEnd
		  ";
		  $r = $this->db->fetchAssoc($q);
		  $count = $this->db->fetchOne("SELECT count(*) FROM bevomedia_queue q WHERE $where");
		  foreach($r as $i => $row)
		  {
			$r[$i]['warning'] = $this->db->fetchOne("select count(*) from bevomedia_queue_log where status='warning' and queueId=".$row['id']);
			$r[$i]['success'] = $this->db->fetchOne("select count(*) from bevomedia_queue_log where status='success' and queueId=".$row['id']);
			$r[$i]['error'] = $this->db->fetchOne("select count(*) from bevomedia_queue_log where status='error' and queueId=".$row['id']);
			$r[$i]['message'] = $this->db->fetchOne("select count(*) from bevomedia_queue_log where status='message' and queueId=".$row['id']);
			$r[$i]['queued'] = $this->db->fetchOne("select count(*) from bevomedia_queue_log where status='queued' and queueId=".$row['id']);
		  }
		  $arr = array('results' => $r,
			'passback' => @$_GET['passback'],
			'count' => $count);	
		  die(json_encode($arr));
		}
		
		Public Function PublisherJSON()
		{	
		  function v($p, $d)
		  {
			if(isset($_REQUEST[$p])&&!empty($_REQUEST[$p]))
			  return mysql_real_escape_string($_REQUEST[$p]);
			else
			  return $d;
		  }
		  function quote_safe($v)
		  {
			return '"' . mysql_real_escape_string($v) . '"';
		  }
		  $sDate = date('Y-m-d', strtotime(v('startDate', 'one year ago'))) . ' 00:00:00';
		  $eDate = date('Y-m-d', strtotime(v('endDate', 'today'))) . ' 23:59:59';
		  $where =  "true";
		  $filter = json_decode(urldecode(stripslashes((v('filter', '{}')))));
		  $search = v('search', false);
		  if($search)
		  {
			$s = "\"$search\"";
			$ls = "like \"%$search%\"";
			$where = "
			  (u.id=$s 
			  or email $ls)";
		  }
		  $filters = array();
		  foreach($filter as $field_in => $vals)
		  {
			$fin = "eq";
			$op = "=";
			if(strpos($field_in, '__'))
			{
			  $fs = explode("__", $field_in);
			  $field_in = $fs[0];
			  switch ($fs[1]) {
			   case 'lt': $op = '<'; break;
			   case 'gt': $op = '>'; break;
			   case 'lte': $op = '<='; break;
			   case 'gte': $op = '>='; break;
			   case 'not': $op = '!='; break;
			   case 'eq': $op = '='; break;
			   case 'in': $op = 'in'; break;
			   case 'like': $op = 'like'; break;
			  }
			}
			switch($field_in) {
				case 'user': $field = 'user__id'; break;
				case 'type': $field = 'type'; break;
			}
			if(is_array($vals))
			{
			  $op = "in";
			  $val = "(".implode(', ', array_map("quote_safe", $vals)) .")";
			} else {
			  $val = '"'.mysql_real_escape_string($vals).'"';
			}
			if(empty($field) || empty($val))
			  break;
			$clause = "$field $op $val";
			$filters[] = $clause;
		  }
		  if(!empty($filters))
			  $where .= "AND " . implode("AND", $filters);
		  $order = v('o', 'id');
		  $orderDir = v('o_dir', 'desc');
		  $lStart = v('start', 0);
		  $lEnd = v('end', 50);
		  $q = "
			SELECT u.id, email, CONCAT(i.firstName, ' ', i.lastName) as name, u.created as created, u.deleted as deleted, lastLogin, enabled
			FROM
			bevomedia_user u
			LEFT JOIN bevomedia_user_info i ON (u.id=i.id)
			WHERE $where
			ORDER BY $order $orderDir LIMIT $lStart, $lEnd
		  ";
		  $r = $this->db->fetchAssoc($q);
		  $count = $this->db->fetchOne("SELECT count(*) FROM bevomedia_user u WHERE $where");
		  $arr = array('results' => $r,
			'passback' => @$_GET['passback'],
			'count' => $count);	
		  die(json_encode($arr));
		}
		Public Function PublisherExportCSV()
		{
			if(isset($_POST['self-hosted']))
			{
				$sql = 'SELECT * FROM bevomedia_user WHERE deleted = "' . ($_POST['status'] == 'active'?'0':'1') . '"';
				if($_POST['self-hosted'] == 'no')
				{
					$sql .= ' AND isSelfHosted = "0"';
				}elseif($_POST['self-hosted'] == 'premium')
				{
					$sql .= ' AND isSelfHosted = "1" AND membershipType = "PREMIUM"';
				}elseif($_POST['self-hosted'] == 'deluxe')
				{
					$sql .= ' AND isSelfHosted = "1" AND membershipType = "DELUXE"';
				}
				
				$db = Zend_Registry::get('Instance/DatabaseObj');
				$result = $db->fetchAll($sql);
				if(!$result)
					return;
				$csvOutput = '';
				foreach($result[0] as $key=>$value)
				{
					$csvOutput .= '"' . $key . '",';
				}
				$csvOutput = substr($csvOutput, 0, -1) . "\n";
				foreach($result as $row)
				{
					foreach($row as $key=>$value)
					{
						$csvOutput .= '"' . $value . '",';
					}
					$csvOutput = substr($csvOutput, 0, -1) . "\n";
				}
				$this->output = $csvOutput;
			}
		}
		
		Public Function AffiliateNetworks()
		{
			$AffNetwork = new AffNetwork();
			$this->AffNetworks = $AffNetwork->getAllAffiliateNetworksByModel('CPA');
		}
		
		Public Function AffiliateNetworkUsers()
		{
			$AffNetwork = new AffNetwork($_GET['id']);
			$this->AffiliateNetwork = $AffNetwork;
			$this->AffNetUsers = $AffNetwork->GetAllUsersForThisNetwork();
		}
		
		Public Function AffiliateNetworkUserAPIUpdate()
		{
			print '<pre>';
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			// Include AbsoluteIncludeHelper file
			require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');
			// Incldue StatImport file
			require_once(PATH . 'StatImport.class.php');

			// Retrieve information about this Affilate Network Account
			$AffNetUser = new AffiliateNetworkUser($_GET['id']);
			
			// Retrieve information about the Affiliate Network at this Account belongs to
			$AffNetwork = new AffNetwork($AffNetUser->network__id);
			
			// Retrieve the Affiliate Network name and process a Webservices Class name
			$NetworkName = $AffNetwork->title;
			$NetworkName = str_replace('Affiliate.com', 'AffiliateDotCom', $NetworkName);
			$NetworkName = str_replace('NeverblueAds', 'NeverBlue', $NetworkName);
			
			// Check to see if the processed Webservices Class name has an existing Class file
			if(file_exists(ABSWEBSERVICESDIR . '/Network_Classes/' . $NetworkName . '.php'))
			{
				// Include the Network Class
				require_once(ABSWEBSERVICESDIR . '/Network_Classes/' . $NetworkName . '.php');
				
				// This Network class exists, instantiate and populate credential information
				$Network = new $NetworkName();
				$Network->setPublisherId($AffNetUser->otherId);
				$Network->setPublisherLogin($AffNetUser->loginId);
				$Network->setPublisherPassword($AffNetUser->password);

				// Login to the Network
				$Network->login();
				
				// Set the Date for stat retrieval
				$Date = date('Y-m-d');
				$Date = '2010-03-20';
				
				// Retrieve the stats for the specified Date
				$Stats = $Network->getStats($Date);
				//print $Network->
				
				// Check if stat retrieval was successful
				if($Stats)
				{
					// Instantiate StatImport object
					$StatImport = new StatImport($AffNetwork->id, $AffNetUser->user__id);
					
					// Import the Stats into the local tables
					$StatImport->processStatEnvelope($Stats);
					
					// TODO: Create output for the Network that's easily read by the user
				}else{
					die('Stats import failed.');
				}
			}else{
				die('Network class does not exist:' . "<br/>\n" . ABSWEBSERVICESDIR . '/Network_Classes/' . $NetworkName . '.php');
			}
			
			exit;
		}
		
		Public Function AffiliateNetworkUserAPIUpdateSelectDate()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$this->id = $_GET['id'];
			$DT = array('ClickBooth', 'Copeac', 'FluxAds', 'ROIRocket', 'XY7', 'CommissionEmpire', 'Rextopia', 'Wotogepa');
			$HitPath = array('Ads4Dough', 'Convert2Media', 'W4');
			$LinkTrust = array('AdEx', 'Adfinity', 'BlinkAds', 'EWA', 'Firelead', 'Epicenter');
			
			
			if(isset($_POST['FormSubmit']))
			{
				$this->id = $_POST['id'];
				
				$Date = date('Y-m-d', strtotime($_POST['Date']));
				
				// Include AbsoluteIncludeHelper file
				require_once(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'AbsoluteIncludeHelper.include.php');
				
				// Incldue StatImport file
				require_once(PATH . 'StatImport.class.php');
	
				// Retrieve information about this Affilate Network Account
				$AffNetUser = new AffiliateNetworkUser($this->id);
								
				// Retrieve information about the Affiliate Network at this Account belongs to
				$AffNetwork = new AffNetwork($AffNetUser->network__id);
				
				// Retrieve the Affiliate Network name and process a Webservices Class name
				$NetworkName = $AffNetwork->title;
				$NetworkName = str_replace('Affiliate.com', 'AffiliateDotCom', $NetworkName);
				$NetworkName = str_replace('NeverblueAds', 'NeverBlue', $NetworkName);
				
				// Check to see if the processed Webservices Class name has an existing Class file
				if(file_exists(ABSWEBSERVICESDIR . '/Network_Classes/' . $NetworkName . '.php'))
				{
					// Include the Network Class
					require_once(ABSWEBSERVICESDIR . '/Network_Classes/' . $NetworkName . '.php');
					
					// This Network class exists, instantiate and populate credential information
					$Network = new $NetworkName();
					$Network->setPublisherId($AffNetUser->otherId);
					$Network->setPublisherLogin($AffNetUser->loginId);
					$Network->setPublisherPassword($AffNetUser->password);
	
					// Login to the Network
					$Network->login();
										
					// Retrieve the stats for the specified Date
					$Stats = $Network->getStats($Date);
										
					// Check if stat retrieval was successful
					if($Stats)
					{
						// Instantiate StatImport object
						$StatImport = new StatImport($AffNetwork->id, $AffNetUser->user__id);
						
						// Import the Stats into the local tables
						$StatImport->processStatEnvelope($Stats);
						
						// TODO: Create output for the Network that's easily read by the user
					}else{
						die('Stats import failed.');
					}
				}else{
					die('Network class does not exist:' . "<br/>\n" . ABSWEBSERVICESDIR . '/Network_Classes/' . $NetworkName . '.php');
				}
			}
		}
		
		Public Function LoginAsPublisher()
		{
			if(!isset($_GET['id']))
			{
				header('Location: Publishers.html');
				die;
			}
			
			$_SESSION['User']['ID'] = $_GET['id'];
			header('Location: /BevoMedia/User/Index.html');
			die;
		}
		
		Public Function DeleteMentor()
		{
			if(isset($_GET['id']))
			{
				$Mentor = new Mentor();
				$Mentor->DeleteMentor($_GET['id']);
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function RestoreMentor()
		{
			if(isset($_GET['id']))
			{
				$Mentor = new Mentor();
				$Mentor->restoreMentor($_GET['id']);
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function Mentors()
		{
			$Mentor = new Mentor();
			$this->AllMentors = $Mentor->GetAllMentors();
		}
		
		Public Function MarketplaceJobs()
		{
			$this->jobs = $this->db->fetchAll('select * from bevomedia_marketplace where status != "deleted" order by created desc');
		}
		
		Public Function PremiumOrders()
		{
		  $this->orders = $this->db->fetchAll('select * from bevomedia_premium_orders order by created desc limit 50');	
		}
		Public Function MarketplaceCompleted()
		{
			if(isset($_GET['id']))
			{
				$job = $this->db->fetchRow('select * from bevomedia_marketplace where id='.$_GET['id']);
				$this->db->update('bevomedia_marketplace', array('status' => 'complete'), 'id='.$_GET['id']);
				$MailComponentObject = new MailComponent();
				$MailComponentObject->setFrom('no-reply@bevomedia.com');
	        	$EmailContent = "Marketplace Job Complete:<br>\r\n
					<br>\r\n
					Your marketplace job \"{$job->projectName}\" has been completed!<br >\r\n";
				$MailComponentObject->setSubject('Marketplace Job Complete');
	        	$EmailContent .= "<br>\r\n<br>\r\n
				We'll be contacting you shortly for you to pick up the deliverables.<br>\r\n
				<br>\r\n
				Thanks,<br>\r\n
				-The BevoMedia Team<br>\r\n
				";
				$MailComponentObject->setHTML($EmailContent);
				$MailComponentObject->send(array($job->contactEmail));
			}
			header('Location: MarketplaceJobs.html');
		}
		Public Function MarketplaceAccept()
		{
			if(isset($_POST))
			{
				$job = $this->db->fetchRow('select * from bevomedia_marketplace where id='.$_POST['id']);
				$update = array('quotedPrice' => $_POST['price'],
								'description' => $_POST['description'],
								'status' => 'pendingAccept');
				$this->db->update('bevomedia_marketplace', $update, 'id='.$_POST['id']);
				$price_change = floatval($job->quotedPrice) != floatval($_POST['price']);
				$description_change = floatval($job->description) != floatval($_POST['description']);
				$status_change = $job->status != 'pendingAccept';
				if($price_change || $description_change || $status_change)
				{
					$MailComponentObject = new MailComponent();
					$MailComponentObject->setFrom('no-reply@bevomedia.com');
		        	if($status_change)
		        	{
						$EmailContent = "Marketplace Job Approved:<br>\r\n
						<br>\r\n
						Your marketplace job \"{$job->projectName}\" has been approved!<br >\r\n";
						$MailComponentObject->setSubject('Marketplace Job Approved');
		        	} else {
						$EmailContent = "Marketplace Job Updated:<br>\r\n
						<br>\r\n
						We've made a change to your marketplace job \"{$job->projectName}\"<br >\r\n";
						$MailComponentObject->setSubject('Marketplace Job Updated');
		        	}
		        	if($price_change)
						$EmailContent .= "Price Quote: {$job->quotedPrice}<br>\r\n";
					if($description_change)
						$EmailContent .= "Description: {$job->description}<br>\r\n";
					$EmailContent .= "<br>\r\n<br>\r\n
					For work on the project to begin, you must visit the
					<a href='http://beta.bevomedia.com/BevoMedia/Marketplace/'>Bevo Marketplace</a>
					and accept the job quote.<br>\r\n
					<br>\r\n
					Thanks,<br>\r\n
					-The BevoMedia Team<br>\r\n
					";
					$MailComponentObject->setHTML($EmailContent);
					$MailComponentObject->send(array($job->contactEmail));
				}
			}
			header('Location: MarketplaceJobs.html');
		}
		
		Public Function SelfHostedPublishers()
		{
			$User = new User();
			$this->AllUsers = $User->getAllSelfHostedUsers();
		}
		
		Public Function Publishers()
		{
		}
		
		Public Function SearchPublishers()
		{
			$this->Mentor = new Mentor();
		
			if(isset($_POST['assignMentorsToUser']))
			{
				$this->Mentor->id = $_POST['Mentor_ID'];
				foreach($_POST['User_CB'] as $User_ID)
				{
					$this->Mentor->AddUserToMentor($User_ID);
				}
			}
			if(isset($_POST['clearMentorsFromUsers']))
			{
				$this->Mentor->id = $_POST['Mentor_ID'];
				foreach($_POST['User_CB'] as $User_ID)
				{
					$this->Mentor->RemoveUserFromMentor($User_ID);
				}
			}
				
			$search = false;
			if(isset($_GET['searchValue']))
			{
				$search = $_GET['searchValue'];
			}
			$User = new User();
			$this->AllUsers = $User->SearchUsers($search);
		}
		
		Public Function EmailPublishers()
		{
			$this->SuccessSent = false;
			
		
			
			if(isset($_POST['emailPublishersSubmit']))
			{
				$subject = $_POST['subject'];
				$body = $_POST['message'];
				$recipients = array();
				
				
				$options = array();
				$options['list_id'] = '7650380bc2';
				$options['subject'] = $subject;
				$options['from_email'] = 'contact@bevomedia.com';
				$options['from_name'] = 'BevoMedia Team';
				$options['to_email'] = 'BevoMedia User';
				$options['title'] = date('m/d/Y H:i:s');
				
				$MCAPI = new MCAPI();
				$result = $MCAPI->campaignCreate('plaintext', $options, array('text' => $body));
				$this->SuccessSent = $MCAPI->campaignSendNow($result);
				
				
				return;
				
				
				foreach($emails as $email)
					if(trim($email) !== '')
						$recipients[] = trim($email);
						
				$recipients = array_unique($recipients);
				
				if($_POST['method'] == 'ALL_EXCEPT_SET')
				{
					$User = new User();
					$AllUsers = $User->GetAllNonDeletedUsers();
					$emails = array();
					foreach($AllUsers as $User)
						$emails[] = $User->Email;
						
					PageHelper::RemoveArrayElements($emails, $recipients);
					
					$recipients = $emails;
				}
				
				$sent = 0;
				foreach($recipients as $recipient)
				{
					$sent++;
					$this->SendEmail($recipient, $subject, $body);
				}
				
				$this->SuccessSent = $sent;
			}
		}
		
		Private Function SendEmail($to, $subject, $body)
		{
			$MailComponentObject = new MailComponent();
			$MailComponentObject->setFrom('no-reply@'.$_SERVER['HTTP_HOST']);
			$MailComponentObject->setSubject($subject);
			$MailComponentObject->setHTML($body);
			$MailComponentObject->send(array($to));
		}
		
		Public Function AddUsersToEmailPublishers()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$User = new User();
			
			$Sql = "SELECT
						bevomedia_user.id, 
						bevomedia_user.email,
						bevomedia_user_info.firstName,
						bevomedia_user_info.lastName 
					FROM 
						bevomedia_user,
						bevomedia_user_info
					WHERE 
						(bevomedia_user_info.id = bevomedia_user.id) AND
						(bevomedia_user.deleted = 0)
			
					";
			
			$this->AllUsers = $this->db->fetchAll($Sql);
		}
		
		Public Function ViewMentorUsers()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$Mentor = new Mentor($_GET['id']);
			$this->AllUsers = $Mentor->getMentorsUsers();
		}
		
		Public Function RemoveUsersMentor()
		{
			$User_ID = $_GET['id'];
			$Mentor = new Mentor();
			$Mentor->RemoveUserFromMentor($User_ID);
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function AllPublishers()
		{
			$User = new User();
			$this->AllUsers = $User->GetAllUsers();
		}
		
		Public Function DeletedPublishers()
		{
			$User = new User();
			$this->AllUsers = $User->GetAllDeletedUsers();
		}
		
		Public Function NewApplications()
		{
			$User = new User();
			$this->AllUsers = $User->GetNewApplications();
		}
		
		Public Function EnableUser()
		{
			if(isset($_GET['id']))
			{
				$User = new User();
				$User->EnableUser($_GET['id']);
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function DisableUser()
		{
			if(isset($_GET['id']))
			{
				$User = new User();
				$User->DisableUser($_GET['id']);
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function DeleteUser()
		{
			if(isset($_GET['id']))
			{
				$User = new User();
				$User->DeleteUser($_GET['id']);
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function RestoreUser()
		{
			if(isset($_GET['id']))
			{
				$User = new User();
				$User->RestoreUser($_GET['id']);
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function SolveTicket()
		{
			if(isset($_GET['id']))
			{
				$User_Tickets = new User_Tickets();
				$User_Tickets->Solve($_GET['id']);
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function Test()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			print '<pre class="textAlignLeft">';
		}
		
		Public Function ChangePassword()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$this->Message = false;
			
			if(isset($_POST['changePasswordSubmit']))
			{
				$Admin = new Admin($_SESSION['Admin']['ID']);
				$Valid = $Admin->login($Admin->username, $_POST['OldPassword']);
				if(!$Valid || $_POST['NewPassword'] == '' || $_POST['ReNewPassword'] == '' || $_POST['ReNewPassword'] != $_POST['NewPassword'])
					$this->Message = 'INVALID_PASS';
				else {
					$Admin->changePassword($_POST['NewPassword']);
					$this->Message = 'PASS_CHANGED';
				}
			}
		}
		
		Public Function DeleteTicket()
		{
			if(isset($_GET['id']))
			{
				$User_Tickets = new User_Tickets();
				$User_Tickets->Delete($_GET['id']);
			}
			
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			die;
		}
		
		Public Function AddDemoData()
		{
			$this->User = new User($_GET['id']);
			
			$this->AdwordsResults = $this->User->GetAllAccountsAdwords();
			$this->YahooResults = $this->User->GetAllAccountsYahoo();
			$this->MSNResults = $this->User->GetAllAccountsMSN();
			$this->AnalyticsResults = $this->User->GetAllAccountsAnalytics();
//			print_r($_POST);die;
			
			if(isset($_POST['submitAddForm']))
			{
				$StatDate = date('Y-m-d', strtotime($_POST['date']));
				$db = Zend_Registry::get('Instance/DatabaseObj');
				
				$AffNetworkUser = new AffiliateNetworkUser();
				$this->NetworkResults = $AffNetworkUser->GetAllAffiliateNetworksForUser($this->User->id);
				
				$OfferTitleString = 'Demo Offer';
				$OfferIDString = 'Demo';
				$OfferSubID = 'DemoSub';
				if(isset($_POST['doaffnetworks']))
				{
					foreach($this->NetworkResults as $U)
					{
						$Sql = "SELECT ID FROM adpalace_user_aff_network_stats WHERE STAT_DATE = '$StatDate' AND NETWORK_ID = $U->NETWORK_ID AND USERID = $U->USERID";
						$Network = $db->fetchRow($Sql);
						if($Network)
						{
							$Sql = "DELETE FROM adpalace_user_aff_network_stats WHERE ID = {$Network->id}";
							$db->exec($Sql);
						}
						$db->insert('adpalace_user_aff_network_stats', array('USERID'=>$this->User->id, 'NETWORK_ID'=>$U->NETWORK_ID, 'STAT_DATE'=>$StatDate,
																			 'IMPRESSIONS'=>rand(0,100), 'CLICKS'=>rand(0,25), 'CONVERSIONS'=>rand(0,10), 'REVENUE'=>rand(0,10000)/100));
						
						$Sql = "SELECT ID FROM adpalace_offers WHERE NETWORK_ID = $U->NETWORK_ID AND USERID = $U->USERID AND TITLE = '$OfferTitleString' AND OFFER_ID = '$OfferIDString'";
						$Offer = $db->fetchRow($Sql);
						if($Offer)
						{
							$OfferID = $Offer->id;
						}else{
							$db->insert('adpalace_offers', array('USERID'=>$U->USERID, 'TITLE'=>$OfferTitleString, 'NETWORK_ID'=>$U->NETWORK_ID, 'OFFER_ID'=>$OfferIDString));
							$OfferID = $db->lastInsertId();
						}
						
						$Sql = "SELECT ID FROM adpalace_user_aff_network_subid WHERE OFFER_ID = '$OfferIDString' AND STAT_DATE = '$StatDate' AND NETWORK_ID = $U->NETWORK_ID AND USERID = $U->USERID";
						$Network = $db->fetchRow($Sql);
						if($Network)
						{
							$Sql = "DELETE FROM adpalace_user_aff_network_subid WHERE ID = {$Network->id}";
							$db->exec($Sql);
						}
						$db->insert('adpalace_user_aff_network_subid', array('USERID'=>$this->User->id, 'NETWORK_ID'=>$U->NETWORK_ID, 'STAT_DATE'=>$StatDate, 'OFFER_ID'=>$OfferIDString, 'SUB_ID'=>$OfferSubID,
																			 'CLICKS'=>rand(100,1000), 'CONVERSIONS'=>rand(0,10), 'REVENUE'=>rand(0,10000)/100));
						
						
						$Sql = "SELECT click_id FROM tracker_clicks WHERE sub_id= '$OfferSubID' AND click_date = '$StatDate' AND user_id = $U->USERID";
						$Click = $db->fetchRow($Sql);
						if($Click)
						{
							$Sql = "DELETE FROM tracker_clicks WHERE click_id = {$Click->click_id}";
							$db->exec($Sql);
						}
						$db->insert('tracker_clicks', array('sub_id'=>$OfferSubID, 'user_id'=>$U->USERID, 'click_date'=>$StatDate));
						
					}
				}
				
				$Accts = array('1'=>'Adwords', '2'=>'Yahoo', '3'=>'MSNAdCenter');
				
				$CampaignString = "Demo Campaign";
				$AdGroupString = "Demo Ad Group";
				$KeywordString = "Demo Keyword";
				
				foreach($Accts as $Key=>$Value)
				{
					if(isset($_POST[$Value]))
					{
						foreach($_POST[$Value] as $ID)
						{
							$Acct = 'Accounts_' . $Value;
							$Account = new $Acct($this->User->id);
							$Account->getInfo($ID);
							$Sql = "SELECT id FROM bevomedia_ppc_campaigns WHERE name = '$CampaignString' AND accountId = $ID AND user__id = {$this->User->id} AND providerType = $Key";
							$Campaign = $db->fetchRow($Sql);
							if($Campaign)
							{
								$CampaignID = $Campaign->id;
							}else{
								$CampaignID = $Account->addCampaign($CampaignString);
							}
							$Sql = "SELECT id FROM bevomedia_ppc_adgroups WHERE name = '$AdGroupString' AND campaignId = $CampaignID ";
							$AdGroup = $db->fetchRow($Sql);
							if($AdGroup)
							{
								$AdGroupID = $AdGroup->id;
							}else{
								$AdGroupID = $Account->addAdGroup($AdGroupString, $CampaignID);
							}
							$KeywordID = $Account->getKeywordId($KeywordString);
							
							$Sql = "SELECT id FROM bevomedia_ppc_keywords WHERE keywordId = $KeywordID AND adGroupId = $AdGroupID";
							$Keyword = $db->fetchRow($Sql);
							if($Keyword)
							{
								$KeywordID = $Keyword->id;
							}else{
								$KeywordID = $Account->addKeyword($AdGroupID, "$KeywordString", "1.0", "http://www.google.com");
							}
							
							$Sql = "SELECT id FROM bevomedia_ppc_keywords_stats WHERE keywordId = $KeywordID AND statDate = '$StatDate'";
							$StatRow = $db->fetchRow($Sql);
							if($StatRow)
							{
								$Sql = "DELETE FROM bevomedia_ppc_keywords_stats WHERE id = {$StatRow->id}";
								$db->exec($Sql);
							}
							$Imps = rand(0, 1000);
							$Clicks = rand(0, 50);
							$Cost = rand(0, 100) / 100;
							
							$Sql = "INSERT INTO bevomedia_ppc_keywords_stats (keywordId, impressions, clicks, cost, statDate) VALUES ($KeywordID, $Imps, $Clicks, $Cost, '$StatDate')";
							$db->exec($Sql);
							
							$Sql = "SELECT id FROM bevomedia_ppc_advariations WHERE adGroupId = $AdGroupID ";
							$AdVar = $db->fetchRow($Sql);
							if($AdVar)
							{
								$AdVarID = $AdVar->id;
							}else{
								$AdVarID = $Account->addAdVariation($AdGroupID, 'Demo Title', 'http://beta.bevomedia.com/', 'beta.bevomedia.com', 'This is a demo description for a demo ad.');
							}
							
							$Sql = "DELETE FROM bevomedia_ppc_advariations_stats WHERE advariationsId = $AdVarID AND statDate = '$StatDate' ";
							$db->exec($Sql);
							
							$db->insert('bevomedia_ppc_advariations_stats', array('statDate'=>$StatDate, 'advariationsId'=>$AdVarID, 'pos'=>rand(0,50)/10,
																		'clicks'=>rand(50,500), 'impressions'=>rand(500,10000), 'cost'=>rand(0,1000)/100));
							
						}
					}
				}
				
				$DomainString = "beta.bevomedia.com";
				
				if(isset($_POST['Analytics']))
				{
					foreach($_POST['Analytics'] as $ID)
					{
						$A = new Accounts_Analytics($this->User->id);
						$A->GetInfo($ID);
						
						$Sql = "SELECT id FROM bevomedia_analytics_domains WHERE user__id = {$this->User->id} AND domain = '$DomainString'";
						$Domain = $db->fetchRow($Sql);
						if($Domain)
						{
							$DomainID = $Domain->id;
						}else{
							$db->insert("bevomedia_analytics_domains", array('user__id'=>$this->User->id, 'domain'=>$DomainString));
							$DomainID = $db->lastInsertId();
						}
						
						$Sql = "SELECT id FROM bevomedia_analytics_reports WHERE domainId = $DomainID AND dateFrom = '$StatDate' AND dateTo = '$StatDate' ";
						$Report = $db->fetchRow($Sql);
						if($Report)
						{
							$ReportID = $Report->id;
							$db->delete('bevomedia_analytics_reports_visitorsoverview', "reportId = $ReportID");
							$db->delete('bevomedia_analytics_reports_trafficsources', "reportId = $ReportID");
							$db->delete('bevomedia_analytics_reports_countries', "reportId = $ReportID");
							$db->delete('bevomedia_analytics_reports_siteusage', "reportId = $ReportID");
							
						}else{
							$db->insert("bevomedia_analytics_reports", array('domainId'=>$DomainID, 'dateFrom'=>$StatDate, 'dateTo'=>$StatDate));
							$ReportID = $db->lastInsertId();
						}
						
						$VisitorTotal = rand(0,500);
						$db->insert('bevomedia_analytics_reports_visitorsoverview', array('reportId'=>$ReportID, 'total'=>$VisitorTotal));
						$db->insert('bevomedia_analytics_reports_trafficsources', array('reportId'=>$ReportID, 'searchEnginesPercentValue'=>rand(0,100), 'searchEnginesRawValue'=>rand(0,1000), 'directTrafficPercentValue'=>rand(0,100), 'DirectTrafficRawValue'=>rand(0,1000)));
						$db->insert('bevomedia_analytics_reports_countries', array('reportId'=>$ReportID, 'name'=>'United States', 'value'=>rand(0,100)));
						$db->insert('bevomedia_analytics_reports_countries', array('reportId'=>$ReportID, 'name'=>'Canada', 'Value'=>rand(0,100)));
						$db->insert('bevomedia_analytics_reports_siteusage', array('reportId'=>$ReportID, 'visits'=>rand(0,1000), 'averagePageVisits'=>rand(0,250)/100, 'averageTimeOnSite'=>rand(0,60), 'percentNewVisits'=>rand(0,100), 'bounceRate'=>rand(0,100), 'pageViews'=>rand(0,1000)));
					}
				}
			}
		}
		
		Public Function ViewPublisher()
		{
			$this->User = new User($_GET['id']);
			
			if (isset($_GET['Subscribe']))
			{
				$this->User->Subscribe($_GET['ProductName']);
				header('Location: /BevoMedia/Admin/ViewPublisher.html?id='.$_GET['id']);
				die;
			}
			
			if (isset($_GET['Unsubscribe']))
			{
				$this->User->Unsubscribe($_GET['ProductName']);
				header('Location: /BevoMedia/Admin/ViewPublisher.html?id='.$_GET['id']);
				die;
			}
			
			if(isset($_POST) && !empty($_POST['changepw']))
			{
			  $this->db->update('bevomedia_user', array('password'=>md5($_POST['changepw'])), 'id ='.$_GET['id']);
			  $this->pwChanged = true;
			}
		}
		
		Public Function PerformanceConnector()
		{
			$sql = "SELECT 
					bu.id AS userId, 
					bu.email AS userEmail,
					GROUP_CONCAT(DISTINCT ban.title SEPARATOR ', ') AS networks,
					GROUP_CONCAT(DISTINCT bnyp.Name SEPARATOR ', ') AS niches,
					GROUP_CONCAT(DISTINCT bupes.explevel SEPARATOR ', ') AS experience,
					GROUP_CONCAT(DISTINCT bupps.promomethod SEPARATOR ', ') AS promomethods
					FROM 
						bevomedia_user AS bu
					LEFT JOIN bevomedia_user_performanceconnector AS bupc 
						ON bupc.user__id = bu.id
					LEFT JOIN bevomedia_user_performanceconnector_niche AS bupn
						ON bu.id = bupn.user__id
					LEFT JOIN bevomedia_aff_network AS ban 
						ON ban.id = bupc.network__id
					LEFT JOIN bevomedia_name_your_price_niche AS bnyp
						ON bnyp.ID = bupn.niche__id
					LEFT JOIN bevomedia_user_performanceconnector_explevel AS bupe
						ON bupe.user__id = bu.id
					LEFT JOIN bevomedia_user_performanceconnector_explevels AS bupes
						ON bupes.id = bupe.explevel__id
					LEFT JOIN bevomedia_user_performanceconnector_promomethod AS bupp
						ON bupp.user__id = bu.id
					LEFT JOIN bevomedia_user_performanceconnector_promomethods as bupps
						ON bupps.id = bupp.promomethod__id
					WHERE (bupn.id IS NOT NULL OR bupc.id IS NOT NULL)
					GROUP BY bu.id
					ORDER BY bu.id DESC
						";
			$perfConn = $this->db->fetchAll($sql);
			$this->perfConn = $perfConn;
		}
		
		Public Function PerformanceConnectorEdit()
		{
			$this->User = new User($_GET['id']);
			if(isset($_POST['submit'])) {
				$this->User->clearPerformanceConnectorEntries();
				if (isset($_POST['network'])) {
					foreach($_POST['network'] as $network) {
						$this->User->insertPerformanceConnectorEntry($network);
					}
				}
				header('Location: PerformanceConnector.html');
				exit();
			}
			
			$sql = "SELECT
					ban.id AS id,
					ban.title AS title
					FROM 
						bevomedia_user_performanceconnector_networks AS bupn
					INNER JOIN bevomedia_aff_network AS ban
						ON ban.id = bupn.network__id
					
					";
			$networks = $this->db->fetchAll($sql);
			$this->networks = $networks;
			
			$userNetworks = array();
			$sql = "SELECT
					network__id AS id
					FROM
					bevomedia_user_performanceconnector
					WHERE
					user__id = {$this->User->id}
					";
			$userNetworksCollection = $this->db->fetchAll($sql);
			foreach ($userNetworksCollection as $userNetwork) {
				$userNetworks[] = $userNetwork->id;
			}
			$this->userNetworks = $userNetworks;
		}
		
		Public Function EditPublisher()
		{
			$this->User = new User($_GET['id']);
			
			$Sql = "SELECT
						bevomedia_name_your_price_niche.*
					FROM
						bevomedia_name_your_price_niche
					ORDER BY
						bevomedia_name_your_price_niche.Name			
					";
			$this->Niches = $this->db->fetchAll($Sql);
			
			$userNiches = $this->User->getPerformanceConnectorNiches();
			$this->UserNicheIDs = array();
			foreach ($userNiches as $userNiche) {
				$this->UserNicheIDs[] = $userNiche->niche__id;
			}
			
			if(isset($_POST['editPublisherSubmit']))
			{
				$UpdateArray = array('referralRate' => $_POST['referralRate'], 'ppvSpyReferralRate' => $_POST['ppvSpyReferralRate']);
				$this->db->update('bevomedia_user', $UpdateArray, ' ID = '.$this->User->id);
				
				$Data = $_POST;
				unset($Data['editPublisherSubmit']);
				unset($Data['referralRate']);
				unset($Data['ppvSpyReferralRate']);
								
				$niche = $Data['niche'];
				unset($Data['niche']);
				$this->User->Update($Data);

				$this->User->clearPerformanceConnectorNiches();
				foreach ($niche as $nicheId) {
					$this->User->insertPerformanceConnectorNiche($nicheId);
				}
				
				/* @var $MCAPI MCAPI */
				$MCAPI = new MCAPI(); 
				
				$result = $MCAPI->listSubscribe('7650380bc2', $this->User->email, array('FNAME' => $this->User->firstName, 'LNAME' => $this->User->lastName), 'html', false);
				 
				header('Location: ViewPublisher.html?id=' . $_GET['id']);
				die;
			}
		}
		
		Public Function Tickets()
		{
			$User_Tickets = new User_Tickets();
			$this->Tickets = $User_Tickets->GetAllTickets();
		}
		
		Public Function ViewNotes()
		{
			$this->User = new User($_GET['id']);
			$Notes = new User_Notes();
			$this->Notes = $Notes->GetAllNotes($_GET['id']);
		}
		
		Public Function Index()
		{
			$this->CountInvoiceRequests = $this->TotalInvoiceRequests(); 
		}
		
		Public Function AddMentor()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$Mentor = new Mentor();
			if(isset($_GET['EditMentor']))
				$Mentor->GetInfo($_GET['EditMentor']);
			
			$this->Mentor = $Mentor;
				
			$goto = '/BevoMedia/Admin/Mentors.html';
			$goto = $this->PageHelper->URLEncode($goto);
			
			if(isset($_POST['editMentorSubmit']))
			{
				$Data = $_POST;
				unset($Data['editMentorSubmit']);
				$Mentor->Update($Data);
				
				header('Location: /BevoMedia/Index/CloseShadowbox.html?goto=' . $goto);
				die;
			}
			
			if(isset($_POST['addMentorSubmit']))
			{
				$Data = $_POST;
				unset($Data['addMentorSubmit']);

				$Mentor->Insert($Data);
				header('Location: /BevoMedia/Index/CloseShadowbox.html?goto=' . $goto);
				die;
			}
		}
		
		Public Function AddNote()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			$this->user__id = $_GET['id'];
			
			if(isset($_POST['addNoteSubmit']))
			{
				$Data = array();
				$Data['user__id'] = $_POST['User_ID'];
				$Data['note'] = $_POST['Note'];
				$Data['admin__id'] = $_SESSION['Admin']['ID'];
				
				$Notes = new User_Notes();
				$Notes->Insert($Data);
				
				$goto = '/BevoMedia/Admin/ViewNotes.html?id=' . $_POST['User_ID'];
				$goto = $this->PageHelper->URLEncode($goto);
				header('Location: /BevoMedia/Index/CloseShadowbox.html?goto=' . $goto);
				die;
			}
		}
		
		Public Function DeleteNote()
		{
			$Note = new User_Notes($_GET['id']);
			$Note->Delete();
			
			header('Location: /BevoMedia/Admin/ViewNotes.html?id=' . $Note->user__id);
			die;
		}
		
		Public Function PublisherStatsDetail()
		{
			if(!isset($_GET['id']))
			{
				header('Location: PublisherStats.html');
				die;
			}
			
			$this->User = new User($_GET['id']);
			$this->User->LoadStats();
		}
	
		Public Function PublisherStatsCollapse()
		{
			$User = new User();
			$this->Results = $User->GetAllNonDeletedUsersWithStats();
		}
		
		Public Function PublisherStats()
		{
			$User = new User();
			$this->Results = $User->GetAllNonDeletedUsersWithStats();
		}
		
		Public Function NetworkStats()
		{
			$Network = new Network();
			$this->Results = $Network->GetAllNetworksWithStats();
		}
		
		Public Function NetworkStatsCollapse()
		{
			$Network = new Network();
			$this->Results = $Network->GetAllNetworksWithStats();
		}
		
		Public Function NetworkStatsAverages()
		{
			$Network = new Network();
			$this->Results = $Network->GetAllNetworksWithStats();
			$this->Network = $Network;
		}
		
		Public Function APIUsage()
		{
			$APIUse = new API_Usage();
			$this->Results = $APIUse->GetAllUsersWithAPIUse();
		}
		
		Public Function APIUsageDetails()
		{
			if(!isset($_GET['id']))
			{
				header('Location: APIUsage.html');
				die;
			}
			$User = new User($_GET['id']);
			$this->User = $User;
			$APIUse = new Adwords_API_Usage(false, $_GET['id']);
			$this->APIUse = $APIUse;
			$this->Results = $APIUse->GetAllAPICallsForUser($User->id);
			$this->CreditResults = $APIUse->GetAllCreditForUser($User->id);
		}
		
		Public Function PublisherPPCAccounts()
		{
			if(!isset($_GET['id']))
			{
				header('Location: Publishers.html');
				die;
			}
			
			$User = new User($_GET['id']);
			$this->User = $User;
			$this->AdwordsResults = $User->GetAllAccountsAdwords();
			$this->YahooResults = $User->GetAllAccountsYahoo();
			$this->MSNResults = $User->GetAllAccountsMSN();
		}
		
		Public Function DenyUser()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			if(isset($_GET['Confirm']))
			{
				$User = new User($_GET['id']);
				//$User->PermanentDelete();
				$MailComponentObject = new MailComponent();
	        	$MailComponentObject->setFrom('no-reply@'.$_SERVER['HTTP_HOST']);
				$EmailContent = <<<END
Dear {$User->GetUserName()}:<br/>\n
<br/>\n
We regret to inform you that you have been denied as a BeVo Media<br/>\n
publisher. For more information, please email help@bevomedia.com.<br/>\n
<br/>\n
If you wish to be reconsidered for admittance, please re-apply in 3-4 weeks.<br/>\n
<br/>\n
Regards,<br/>\n
The BeVo Media Team<br/>\n
http://bevomedia.com<br/>\n
END;
	            $MailComponentObject->setHTML($EmailContent);
	            $MailComponentObject->setSubject('BevoMedia Application Denied');
	            $MailComponentObject->send(array('ryan@bevomedia.com'));
				
				$goto = '{PARENT}';
				header('Location: /BevoMedia/Index/CloseShadowbox.html?goto=' . $goto);
				die;
			}
		}
		
		
		Public Function YahooAPIUpdate()
		{
    		Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');

			if(isset($_GET['Confirm']) && isset($_GET['id']))
			{
			    $id = intval(@$_GET['id']);
			    $db = Zend_Registry::get('Instance/DatabaseObj');
		        $sql = "SELECT * FROM bevomedia_accounts_yahoo WHERE id = " . $id;
	            $row = mysql_fetch_assoc(mysql_query($sql));
		
        	    $username = $row['username'];
        	    $password = $row['password'];
        	    $masterAccountId = $row['masterAccountId'];
        	    $user_id = $row['user__id'];
        		
        		$reportType = 'AdSummary';
        		$reportName = $reportType . '-' . time();
        		
        		$yahoo_api = new yahoo_api($username, $password, $masterAccountId, $id);
        		if($yahoo_api->disabled)
        		{
        			print "This account is disabled!";
        			return true;
        		}
        		else
        		{
        			print "Account ($username) Added" . "\n";
        		}
        		$Queue = new QueueComponent();
        		$JobID =  $Queue->CreateJobID('Yahoo: ' . $username, $user_id);
                $reportId = 0;
                try{
        			$reportId = $yahoo_api->addReport($reportName, $reportType);
                } catch (Exception $e) {
                   	$msgQuery = "INSERT INTO bevomedia_queue_log (queueId, started, completed, provider, status, description, output) VALUES
            		(
            			(select id from bevomedia_queue where jobId='".$JobID."'),
           		 		now(), now(), 'YAHOO', 'error', 'Error adding report request #'.$reportId, mysql_real_escape_string(print_r($e, true));
            		)";
            		mysql_query($msgQuery);
            		return;
                }
        		$envelope = $yahoo_api->rcsQueueAdVarsOutput($reportId, $JobID, $user_id);
        		$Queue->SendEnvelope($JobID, $envelope);
			}
		}
		
		Public Function AdwordsAPIUpdate()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			if(isset($_GET['Confirm']) && isset($_GET['id']))
			{
			    $id = intval($_GET['id']);
			
                $sql = 'SELECT * FROM bevomedia_accounts_adwords WHERE id = ' . $id;
            	$query = mysql_query($sql);
            	$row = mysql_fetch_assoc($query);
            	$Queue = new QueueComponent();
            	$JobID =  $Queue->CreateJobID('Adwords Update Account', $row['user__id']);
            	$PATH = PATH;
            	$envelope = <<<END
<?php
require_once('{$PATH}adwords_api/adwords_import.php');
require_once('{$PATH}Accounts_Adwords.class.php');
update($row[user__id], $row[id], '$JobID', strtotime('YESTERDAY'), strtotime('TODAY'));
\$account = new Accounts_Adwords();
\$account->setQueueJobId('$JobID');
\$account->GetInfo($id);
//echo "Updated " . \$account->UpdateCampaignsFromAPI() . " campaigns";
?>
END;
	            $Queue->SendEnvelope($JobID, $envelope);
	            echo 'Added job '.$JobID.' to queue';
			}
		}
		
		Public Function AddAPICreditToUser()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			if(isset($_POST['addCreditSubmit']))
			{
				$user = new User($_GET['id']);
				$user->addApiCalls(intval($_POST['Credit']), 'Free calls from an admin');
				$goto = '{PARENT}';
				header('Location: /BevoMedia/Index/CloseShadowbox.html?goto=' . $goto);
				die;
			}
		}
		
		Public Function Login()
		{
			if(isset($_GET['Error']))
			{
				$this->{'Error'.$_GET['Error']} = true;
			}
		}
		
		Public Function ProcessLogin()
		{
			if(isset($_POST['loginFormSubmit']))
			{
				$Admin = new Admin();
				$loginAttempt = $Admin->login($_POST['Username'], $_POST['Password']);
				
				if($loginAttempt === true)
				{
					$Admin->getInfo($Admin->getIdUsingUsername($_POST['Username']));
					$_SESSION['Admin']['ID'] = $Admin->id;
					header('Location: /BevoMedia/Admin/Index.html');
					exit;
				}
				else
					if($loginAttempt == -1)
						header('Location: /BevoMedia/Admin/Login.html?Error=DISABLED');
					else
						header('Location: /BevoMedia/Admin/Login.html?Error=BADPASS');
			}
			die;
		}
		
		Public Function Logout()
		{
			$Admin = new Admin();
			$Admin->Logout();
			header('Location: /BevoMedia/Index/Index.html');
			die;
		}
		
		Public Function Settings()
		{
			$Db = Zend_Registry::get('Instance/DatabaseObj');
			 
			if (isset($_POST['Submit']))
			{
				foreach ($_POST as $Name => $Value)
				{
				    if($Name == 'Submit')
				        continue;
					if (strstr($Name, '_HiddenField'))
					{
						$Name = str_replace('_HiddenField', '', $Name);
					}
					
                    if($Db->fetchRow('SELECT * FROM bevomedia_settings WHERE name=?', $Name))
					    $Db->update('bevomedia_settings', array('value' => $Value), "name='{$Name}'");
					else
					    $Db->insert('bevomedia_settings', array('name' => $Name, 'value' => $Value));
					
			    }
			}
			
			$Sql = "SELECT * FROM bevomedia_settings ORDER BY name";
			$Results = $Db->fetchAll($Sql);
			$Data = array();
			foreach ($Results as $Result)
			{
				$Data[$Result->name] = $Result->value;
			}
			
			$this->Data = $Data;
			
		}
		
		Public Function Queue()
		{
			if (isset($_GET['Resubmit']) && isset($_GET['id']))
			{
				$_GET['id'] = intval($_GET['id']);
				
				$Db = Zend_Registry::get('Instance/DatabaseObj');
				$Sql = "UPDATE bevomedia_queue SET started = '0000-00-00 00:00:00', completed = '0000-00-00 00:00:00', output = '', instanceIP = '' WHERE id = {$_GET['id']} ";
				$Db->exec($Sql);
				$Sql = "DELETE FROM bevomedia_queue_log WHERE queueId = {$_GET['id']} ";
				$Db->exec($Sql);
				header('Location: '.Zend_Registry::get('System/BaseURL').'BevoMedia/Admin/Queue.html?Page='.$_GET['Page']);
				exit;
			}
		}
		
		Public Function QueueItem()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$ID = intval($_GET['id']);
			$Db = Zend_Registry::get('Instance/DatabaseObj');
			
			$Sql = "SELECT id, envelope, output FROM bevomedia_queue WHERE id = {$ID}";
			$Result = $Db->fetchAll($Sql);
			
			$this->{'output'} = $Result[0]->output;
			$this->{'envelope'} = $Result[0]->envelope;
		}
		
		Public Function QueueData($Page ,$Limit = 30)
		{
			$QueueTotal = $this->QueueTotal();
			
			$Page = intval($Page);
			if ($Page==0) $Page = 1;
			
			$Limit = intval($Limit);
			$Start = ($Page-1)*$Limit;
			
			$Db = Zend_Registry::get('Instance/DatabaseObj');
			
			$Sql = "SELECT
						id,
						type,
						jobId,
						user__id,
						started,
						completed,
						output
					FROM
						bevomedia_queue
					ORDER BY
						id DESC
					LIMIT
			{$Start}, {$Limit}
					";
			return $Db->fetchAll($Sql);
		}
		
		Public Function QueueTotal()
		{
			$Db = Zend_Registry::get('Instance/DatabaseObj');
			
			$Sql = "SELECT COUNT(id) AS `Total` FROM bevomedia_queue";
			$Result = $Db->fetchAll($Sql);
			
			return $Result[0]->Total;
		}
		
		Public Function Paginate($CurrentPageNumber, $TotalRecords, $PaginationSize, $PageSize = 30)
		{
			$Start = $CurrentPageNumber-floor($PaginationSize/2)-1;
			if ($Start<1) $Start = 1;
			
			$TotalPages = $TotalRecords/$PageSize;
			if ($TotalPages>floor($TotalPages)) $TotalPages = floor($TotalPages)+1;
			
			
			$End = $Start+$PaginationSize;
			if ($End>$TotalPages) $End = $TotalPages;
			
			if ( ($End-$Start) < $PaginationSize )
			{
				$Start = $End-$PaginationSize;
			}
			
			if ($Start<1) $Start = 1;
			
			$ResultArr = array();
			
			if ($CurrentPageNumber>1)
			{
				$ResultArr['1__'] = 'First';
				$ResultArr[($CurrentPageNumber-1).'_'] = 'Previous';
			}
			
			for ($i = $Start; $i <= $End; $i++)
			{
				if ($i==$TotalPages)
					$ResultArr[$i] = $TotalPages; else
				if ($i==1)
					$ResultArr[$i] = '1'; else
					$ResultArr[$i] = $i;
			}
			
			if ( ($CurrentPageNumber<$TotalPages) && ($TotalPages>1) )
			{
				$ResultArr[($CurrentPageNumber+1).'_'] = 'Next';
				$ResultArr[$TotalPages.'__'] = 'Last';
			}
			
			return $ResultArr;
		}
		
		Public Function Crons()
		{
			$Page = 0;
			if (isset($_GET['Page'])) $Page = intval(str_replace('_', '', $_GET['Page']));
			$this->{'CronsTotal'} = $this->CronsTotal();
			$this->{'Pages'} = $this->Paginate($Page, $this->{'CronsTotal'}, 10, 30);
			$this->{'CronsData'} = $this->CronsData($Page);
		}
		
		Public Function CronItem()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$ID = intval($_GET['id']);
			$Db = Zend_Registry::get('Instance/DatabaseObj');
			
			$Sql = "SELECT id, result FROM bevomedia_crons_logs WHERE id = {$ID}";
			$Result = $Db->fetchAll($Sql);
			
			$this->{'result'} = $Result[0]->result;
		}
		
		Public Function CronsData($Page ,$Limit = 30)
		{
			$CronsTotal = $this->CronsTotal();
			
			$Page = intval($Page);
			if ($Page==0) $Page = 1;
			
			$Limit = intval($Limit);
			$Start = ($Page-1)*$Limit;
			
			
			$Db = Zend_Registry::get('Instance/DatabaseObj');
			
			$Sql = "SELECT
						id,
						name,
						started,
						completed,
						result
					FROM
						bevomedia_crons_logs
					ORDER BY
						id DESC
					LIMIT
						{$Start}, {$Limit}
					";
			return $Db->fetchAll($Sql);
		}
		
		Public Function CronsTotal()
		{
			$Db = Zend_Registry::get('Instance/DatabaseObj');
			
			$Sql = "SELECT COUNT(id) AS `Total` FROM bevomedia_crons_logs";
			$Result = $Db->fetchAll($Sql);
			
			return $Result[0]->Total;
		}
		
		Public Function PublisherRatings()
		{
			$this->PageHelper->Area = 'Publishers';
			$this->PageHelper->SubHeading = 'Publisher Ratings';
			
			if (isset($_GET['Approve']))
			{
				$ID = intval($_GET['Approve']);
				$Data = array('approved' => 1);
				$this->db->update('bevomedia_user_aff_network_rating', $Data, 'id = '.$ID);
			} else
			if (isset($_GET['Disapprove']))
			{
				$ID = intval($_GET['Disapprove']);
				$Data = array('approved' => 0);
				$this->db->update('bevomedia_user_aff_network_rating', $Data, 'id = '.$ID);
			}
		
			$Add = '';
			if (!isset($_GET['ViewAll']))
			{
				$Add = " AND (bevomedia_user_aff_network_rating.approved = 0)";
			}
		
			$Sql = "SELECT 
						bevomedia_user_aff_network_rating.id,
						bevomedia_user_aff_network_rating.rating,
						bevomedia_user_aff_network_rating.userComment,
						bevomedia_user_aff_network_rating.approved,
						bevomedia_aff_network.title as `networkTitle`,
						bevomedia_user_info.firstName,
						bevomedia_user_info.lastName,
						bevomedia_user_aff_network_rating.user__id
					FROM 
						bevomedia_user_aff_network_rating,
						bevomedia_user_info,
						bevomedia_aff_network
					WHERE
						(bevomedia_user_aff_network_rating.network__id = bevomedia_aff_network.id) AND
						(bevomedia_user_aff_network_rating.user__id = bevomedia_user_info.id)
						{$Add}
					";
			$this->Ratings = $this->db->fetchAll($Sql);
		}
		
		Public Function AllPublishersWithStats()
		{
			$User = new User();
			$this->AllUsers = $User->getAllActiveUsers();

			foreach ($this->AllUsers as $Key => $UserItt)
			{
				$AnyFound = false;

				
				
				$this->AllUsers[$Key]->PPVStats = false;
				if ($PPVStats = $User->CheckForPPVStats($UserItt->id))
				{
					$AnyFound = true;
					$this->AllUsers[$Key]->PPVStats = $PPVStats;
				}
				
				$this->AllUsers[$Key]->PPCStats = false;
				if ($PPCStats = $User->CheckForPPCStats($UserItt->id))
				{
					$AnyFound = true;
					$this->AllUsers[$Key]->PPCStats = $PPCStats;
				}
				
				$this->AllUsers[$Key]->MediaBuyStats = false;
				if ($MediaBuyStats = $User->CheckForMediaBuyStats($UserItt->id))
				{
					$AnyFound = true;
					$this->AllUsers[$Key]->MediaBuyStats = $MediaBuyStats;
				}
				
				if (!$AnyFound)
				{
					unset($this->AllUsers[$Key]);
					continue;
				}
			}
			

		}
		
		Public Function TrackerClicksPerUser()
		{
			$Sql = "SELECT
						COUNT(bevomedia_tracker_clicks.id) as `TotalClicks`,
						bevomedia_tracker_clicks.user__id,
						bevomedia_user.email,
						bevomedia_user_info.firstName,
						bevomedia_user_info.lastName
					FROM
						bevomedia_tracker_clicks,
						bevomedia_user,
						bevomedia_user_info
					WHERE
						(bevomedia_user.id = bevomedia_tracker_clicks.user__id) AND
						(bevomedia_user_info.id = bevomedia_user.id)  
					GROUP BY
						bevomedia_tracker_clicks.user__id
					";	
			
			$this->UsersData = $this->db->fetchAll($Sql);
			
		
		}
		
		Public Function NetworkPayments()
		{
			if (isset($_GET['Deactivate']))
			{
				$Arr = array('Active' => 0);
				$this->db->update('bevomedia_network_contact_info', $Arr, ' ID = '.intval($_GET['Deactivate']));
			}
			
			if (isset($_GET['Activate']))
			{
				$Arr = array('Active' => 1);
				$this->db->update('bevomedia_network_contact_info', $Arr, ' ID = '.intval($_GET['Activate']));
			}
			
			$Sql = "SELECT
						*
					FROM
						bevomedia_network_contact_info
					";
			$Networks = $this->db->fetchAll($Sql);
			foreach ($Networks as $Key => $Network)
			{
				$Sql = "SELECT
							*
						FROM
							bevomedia_network_payments
						WHERE
							NetworkContactID = {$Network->ID}
						";
				$Networks[$Key]->Payments = $this->db->fetchAll($Sql);
				$Networks[$Key]->PaymentTermInfo = $this->GetPaymentPlans($Network->PaymentTermID);
			}
			
			$this->Networks = $Networks;
		}	
		
		Public Function GetPaymentPlans($ID = null)
		{
			if ($ID!=null)
			{
				$Add = ' WHERE (bevomedia_network_payment_terms.ID = '.intval($ID).')';
			}
	
			$Sql = "SELECT
						*
					FROM
						bevomedia_network_payment_terms
						$Add
					ORDER BY
						ID
					"; 
						$Results = $this->db->fetchAll($Sql);
	
						if ( ($ID!=null) && (count($Results)>0) )
						{
							return $Results[0];
						}
	
						return $Results;
		}		
		
		Public Function UserPayments()
		{
			$Add = '';
			if (isset($_GET['DateRange']))
			{
				$Temp = explode(' - ', $_GET['DateRange']);
				if (count($Temp)==2) 
				{
					$Date1 = date('Y-m-d', strtotime($Temp[0]));
					$Date2 = date('Y-m-d', strtotime($Temp[1]));
					
					$Add = " AND (DATE(bevomedia_user_payments.Date) BETWEEN DATE('{$Date1}') AND DATE('{$Date2}') ) ";
				} else
				if (count($Temp)==1)
				{
					$Date = date('Y-m-d', strtotime($Temp[0]));
					$Add = " AND (DATE(bevomedia_user_payments.Date) = DATE('{$Date}') ) ";
				}
			}
			
			$Sql = "SELECT
						bevomedia_user.id,
						bevomedia_user.email,
						bevomedia_user_info.firstName,
						bevomedia_user_info.lastName,
						SUM(bevomedia_user_payments.Price) as `Total`		
					FROM
						bevomedia_user_payments,
						bevomedia_user,
						bevomedia_user_info
					WHERE
						(bevomedia_user_payments.UserID = bevomedia_user.id) AND
						(bevomedia_user_info.id = bevomedia_user.id)	
						{$Add}	
					GROUP BY
						bevomedia_user.id
					ORDER BY
						Total DESC
					";
			$this->Results = $this->db->fetchAll($Sql);
		}
		
		Public Function Referrals()
		{			
			$Sql = "SELECT
						bevomedia_user.id,
						bevomedia_user_info.firstName,
						bevomedia_user_info.lastName,
						bevomedia_referrals.Date,
						COUNT(distinct(bevomedia_user.id)) as `TotalUsers`,
						SUM(bevomedia_user_payments.Price)*(bevomedia_user.referralRate/100.0) AS `TotalRevenue`
					FROM
						bevomedia_referrals,
						bevomedia_user,
						bevomedia_user_info,
						bevomedia_user_payments
					WHERE
						(bevomedia_user.id = bevomedia_referrals.ReferrerID) AND
						(bevomedia_user_info.id = bevomedia_user.id) AND
						(bevomedia_user_payments.UserID = bevomedia_referrals.UserID) AND
						(bevomedia_user_payments.TransactionID > 0) AND
						(bevomedia_user_payments.PaidDate >= DATE_SUB(now(), interval 1 year)) AND
						(bevomedia_user.ppvSpyReferralRate = 0)
					GROUP BY
						bevomedia_referrals.ReferrerID
				";
			$this->Referrals = $this->db->fetchAll($Sql);
			
			$Sql = "SELECT
						bevomedia_user.id,
						bevomedia_user_info.firstName,
						bevomedia_user_info.lastName,
						bevomedia_referrals.Date,
						COUNT(distinct(bevomedia_user.id)) as `TotalUsers`,
						SUM(bevomedia_user_payments.Price)*(bevomedia_user.ppvSpyReferralRate/100.0) AS `TotalRevenue`
					FROM
						bevomedia_referrals,
						bevomedia_user,
						bevomedia_user_info,
						bevomedia_user_payments
					WHERE
						(bevomedia_user.id = bevomedia_referrals.ReferrerID) AND
						(bevomedia_user_info.id = bevomedia_user.id) AND
						(bevomedia_user_payments.UserID = bevomedia_referrals.UserID) AND
						(bevomedia_user_payments.TransactionID > 0) AND
						(bevomedia_user_payments.PaidDate >= DATE_SUB(now(), interval 1 year)) AND
						(bevomedia_user.ppvSpyReferralRate > 0) AND
						((bevomedia_user_payments.ProductID = 12) OR (bevomedia_user_payments.ProductID = 13)) 
					GROUP BY
						bevomedia_referrals.ReferrerID
				";
			$this->Referrals = array_merge($this->Referrals, $this->db->fetchAll($Sql));
		}

		Public Function InvoiceRequests()
		{
			if (isset($_GET['markAsSent'])) {
				$Array = array('RequestedInvoice' => 0);
				$this->db->update('bevomedia_user_payments', $Array, ' id = '.intval($_GET['markAsSent']));	
				
				header('Location: /BevoMedia/Admin/InvoiceRequests.html');
				die;
			}
			
			$Sql = "SELECT
						bevomedia_user_info.firstName,
						bevomedia_user_info.lastName,
						bevomedia_user_payments.id,
						bevomedia_user_payments.UserID,
						bevomedia_user_payments.TransactionID,
						bevomedia_user_payments.Price
					FROM
						bevomedia_user_payments,
						bevomedia_user,
						bevomedia_user_info
					WHERE
						(bevomedia_user_payments.UserID = bevomedia_user.id) AND
						(bevomedia_user.id = bevomedia_user_info.id) AND
						(RequestedInvoice = 1)
					";
			$this->AllInvoices = $this->db->fetchAll($Sql);
		}
		
		Public Function TotalInvoiceRequests()
		{
			$Sql = "SELECT COUNT(ID) as `Total` FROM bevomedia_user_payments WHERE RequestedInvoice = 1 ";
			$Total = $this->db->fetchRow($Sql);
			return isset($Total->Total)?$Total->Total:0; 
		}
		
		Public Function NameYourPrice()
		{
			if (isset($_POST['Submit'])) {
				$this->db->delete('bevomedia_name_your_price_rates', ' NicheID = '.intval($_GET['NicheID']));
				
				foreach ($_POST as $Key => $Value) {
					if (!strstr($Key, 'Network_')) continue;
					
					$tmp = explode('_', $Key);
					$ID = $tmp[1];
					
					$Array = array(
									'NicheID' 	=> intval($_GET['NicheID']),
									'NetworkID' => $ID,
									'Rate' 		=> $Value
								  );
				  	$this->db->insert('bevomedia_name_your_price_rates', $Array);
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
			
			if (isset($_GET['NicheID']) && intval($_GET['NicheID'])>0) {
				$this->Networks = $this->ListNameYourPriceNetworks();
				
				foreach ($this->Networks as $Key => $Network) {
					$this->Networks[$Key]->Rate = $this->ListNameYourPriceRates($_GET['NicheID'], $Network->ID); 
				}
				
//				$this->Rates = $this->ListNameYourPriceRates($_GET['NicheID']);
			}
			
		}
		
		Public Function ListNameYourPriceNetworks()
		{
			$Sql = "SELECT
						*
					FROM
						bevomedia_name_your_price_networks
					";
			return $this->db->fetchAll($Sql);
		}
		
		Public Function ListNameYourPriceRates($NicheID, $NetworkID)
		{
			$Sql = "SELECT
						bevomedia_name_your_price_rates.*
					FROM
						bevomedia_name_your_price_rates
					WHERE 
						(bevomedia_name_your_price_rates.NicheID = ?) AND
						(bevomedia_name_your_price_rates.NetworkID = ?)			
					";
			return $this->db->fetchRow($Sql, array($NicheID, $NetworkID));
		}
		
		Private Function CalculateRevenueToday($ProductID, $Count = false) 
		{
			$Add = '';
			if (!$Count) {
				$Add = "SUM(Price) as `Total`";
			} else {
				$Add = "COUNT(Price) as `Total`";
			}
			
			$Sql = "SELECT
						{$Add}
					FROM
						bevomedia_user_payments
					WHERE
						(bevomedia_user_payments.Deleted = 0) AND
						(DATE(bevomedia_user_payments.PaidDate) = DATE(NOW())) AND
						(bevomedia_user_payments.ProductID = ?) AND
						(bevomedia_user_payments.Paid = 1) AND
						(bevomedia_user_payments.PaidDate <> '0000-00-00 00:00:00')
					";
			return reset($this->db->fetchCol($Sql, $ProductID));
		}
		
		Private Function CalculateRevenueMTD($ProductID, $Count = false) 
		{
			$Add = '';
			if (!$Count) {
				$Add = "SUM(Price) as `Total`";
			} else {
				$Add = "COUNT(Price) as `Total`";
			}
			
			$FromDate = date('Y').'-'.date('m').'-01';
			$Sql = "SELECT
						{$Add}
					FROM
						bevomedia_user_payments
					WHERE
						(bevomedia_user_payments.Deleted = 0) AND
						(DATE(bevomedia_user_payments.PaidDate) BETWEEN DATE('{$FromDate}') AND NOW()) AND
						(bevomedia_user_payments.ProductID = ?) AND
						(bevomedia_user_payments.Paid = 1) AND
						(bevomedia_user_payments.PaidDate <> '0000-00-00 00:00:00')					
					";
			return reset($this->db->fetchCol($Sql, $ProductID));
		}
		
		Private Function CalculateRevenueOverall($ProductID, $Count = false) 
		{
			$Add = '';
			if (!$Count) {
				$Add = "SUM(Price) as `Total`";
			} else {
				$Add = "COUNT(Price) as `Total`";
			}
			
			$Sql = "SELECT
						{$Add}
					FROM
						bevomedia_user_payments
					WHERE
						(bevomedia_user_payments.Deleted = 0) AND
						(bevomedia_user_payments.ProductID = ?) AND
						(bevomedia_user_payments.Paid = 1) AND
						(bevomedia_user_payments.PaidDate <> '0000-00-00 00:00:00')
					";
			return reset($this->db->fetchCol($Sql, $ProductID));
		}
		
		Private Function CalculateRefundsOverall($ProductID, $Count = false) 
		{
			$Add = '';
			if (!$Count) {
				$Add = "SUM(Price) as `Total`";
			} else {
				$Add = "COUNT(Price) as `Total`";
			}
			
			$Sql = "SELECT
						{$Add}
					FROM
						bevomedia_user_payments
					WHERE
						(bevomedia_user_payments.Deleted = 1) AND
						(bevomedia_user_payments.ProductID = ?) AND
						(bevomedia_user_payments.Paid = 1) AND
						(bevomedia_user_payments.PaidDate <> '0000-00-00 00:00:00')
					";
			return reset($this->db->fetchCol($Sql, $ProductID));
		}
		
		Private Function CalculateNetworkPaymentsToday($Count = false) 
		{
			$Add = '';
			if (!$Count) {
				$Add = "SUM(Amount) as `Total`";
			} else {
				$Add = "COUNT(Amount) as `Total`";
			}
			
			$Sql = "SELECT
						{$Add}
					FROM
						bevomedia_network_payments
					WHERE
						(DATE(Created) = DATE(NOW())) 
					";
			return reset($this->db->fetchCol($Sql));
		}
		
		Private Function CalculateNetworkPaymentsMTD($Count = false) 
		{
			$Add = '';
			if (!$Count) {
				$Add = "SUM(Amount) as `Total`";
			} else {
				$Add = "COUNT(Amount) as `Total`";
			}
			
			$FromDate = date('Y').'-'.date('m').'-01';
			$Sql = "SELECT
						{$Add}
					FROM
						bevomedia_network_payments
					WHERE
						(DATE(Created) BETWEEN DATE('{$FromDate}') AND NOW()) 				
					";
			return reset($this->db->fetchCol($Sql));
		}
		
		Private Function CalculateNetworkPaymentsOverall($Count = false) 
		{
			$Add = '';
			if (!$Count) {
				$Add = "SUM(Amount) as `Total`";
			} else {
				$Add = "COUNT(Amount) as `Total`";
			}
			
			$Sql = "SELECT
						{$Add}
					FROM
						bevomedia_network_payments
					";
			return reset($this->db->fetchCol($Sql));
		}
		
		Public Function Revenue()
		{
			//PPVSpy
			$this->PPVSpyToday = $this->CalculateRevenueToday(12)+$this->CalculateRevenueToday(13);
			$this->PPVSpyMTD = $this->CalculateRevenueMTD(12)+$this->CalculateRevenueMTD(13);
			$this->PPVSpyOverall = $this->CalculateRevenueOverall(12)+$this->CalculateRevenueOverall(13);
			$this->PPVSpyRefunds = $this->CalculateRefundsOverall(12)+$this->CalculateRefundsOverall(13);
			
			$this->PPVSpyTodayCount = $this->CalculateRevenueToday(12, true)+$this->CalculateRevenueToday(13, true);
			$this->PPVSpyMTDCount = $this->CalculateRevenueMTD(12, true)+$this->CalculateRevenueMTD(13, true);
			$this->PPVSpyOverallCount = $this->CalculateRevenueOverall(12, true)+$this->CalculateRevenueOverall(13, true);
			//PPVSpy
			
			//PPVSpyMonthly
			$this->PPVSpyMonthlyToday = $this->CalculateRevenueToday(12);
			$this->PPVSpyMonthlyMTD = $this->CalculateRevenueMTD(12);
			$this->PPVSpyMonthlyOverall = $this->CalculateRevenueOverall(12);
			$this->PPVSpyMonthlyRefunds = $this->CalculateRefundsOverall(12);
			
			$this->PPVSpyMonthlyTodayCount = $this->CalculateRevenueToday(12, true);
			$this->PPVSpyMonthlyMTDCount = $this->CalculateRevenueMTD(12, true);
			$this->PPVSpyMonthlyOverallCount = $this->CalculateRevenueOverall(12, true);
			//PPVSpyMonthly
			
			//PPVSpyOneTime
			$this->PPVSpyOneTimeToday = $this->CalculateRevenueToday(13);
			$this->PPVSpyOneTimeMTD = $this->CalculateRevenueMTD(13);
			$this->PPVSpyOneTimeOverall = $this->CalculateRevenueOverall(13);
			$this->PPVSpyOneTimeRefunds = $this->CalculateRefundsOverall(13);
			
			$this->PPVSpyOneTimeTodayCount = $this->CalculateRevenueToday(13, true);
			$this->PPVSpyOneTimeMTDCount = $this->CalculateRevenueMTD(13, true);
			$this->PPVSpyOneTimeOverallCount = $this->CalculateRevenueOverall(13, true);
			//PPVSpyOneTime
			
			//Server
			$this->ServerToday = $this->CalculateRevenueToday(2);
			$this->ServerMTD = $this->CalculateRevenueMTD(2);
			$this->ServerOverall = $this->CalculateRevenueOverall(2);
			$this->ServerRefunds = $this->CalculateRefundsOverall(2);
			
			$this->ServerTodayCount = $this->CalculateRevenueToday(2, true);
			$this->ServerMTDCount = $this->CalculateRevenueMTD(2, true);
			$this->ServerOverallCount = $this->CalculateRevenueOverall(2, true);
			//Server
			
			//SelfHosted
			$this->SelfHostedToday = $this->CalculateRevenueToday(3);
			$this->SelfHostedMTD = $this->CalculateRevenueMTD(3);
			$this->SelfHostedOverall = $this->CalculateRevenueOverall(3);
			$this->SelfHostedRefunds = $this->CalculateRefundsOverall(3);
			
			$this->SelfHostedTodayCount = $this->CalculateRevenueToday(3, true);
			$this->SelfHostedMTDCount = $this->CalculateRevenueMTD(3, true);
			$this->SelfHostedOverallCount = $this->CalculateRevenueOverall(3, true);
			//SelfHosted
			
			//PPC
			$this->PPCToday = $this->CalculateRevenueToday(5);
			$this->PPCMTD = $this->CalculateRevenueMTD(5);
			$this->PPCOverall = $this->CalculateRevenueOverall(5);
			$this->PPCRefunds = $this->CalculateRefundsOverall(5);
			
			$this->PPCTodayCount = $this->CalculateRevenueToday(5, true);
			$this->PPCMTDCount = $this->CalculateRevenueMTD(5, true);
			$this->PPCOverallCount = $this->CalculateRevenueOverall(5, true);
			//PPC
			
			//NetworkPayments
			$this->NetworkPaymentOverall = $this->CalculateNetworkPaymentsOverall();
			$this->NetworkPaymentToday = $this->CalculateNetworkPaymentsToday();
			$this->NetworkPaymentMTD = $this->CalculateNetworkPaymentsMTD();
			
			$this->NetworkPaymentOverallCount = $this->CalculateNetworkPaymentsOverall(true);
			$this->NetworkPaymentTodayCount = $this->CalculateNetworkPaymentsToday(true);
			$this->NetworkPaymentMTDCount = $this->CalculateNetworkPaymentsMTD(true);
			//NetworkPayments
		}
		
		Public Function BrokerTrackingPlatforms()
		{
			$this->TopMenu = $this->BrokersMenu();
			
			
			$Sql = "SELECT 
						*
					FROM
						broker_tracking_platforms
					ORDER BY
						Name
					";
			
			$this->TrackingPlatforms = $this->db->fetchAll($Sql);
		}
		
		Public Function BrokerTrackingPlatformForm()
		{
			$this->TopMenu = $this->BrokersMenu();
			
			
			$ID = intval($_GET['ID']);
			
			if (isset($_POST['Save'])) {
				
				$Enabled = isset($_POST['Enabled'])?1:0;
				
				if ($ID==0) {
					$InsertArray = array('Name' => $_POST['Name'], 'Enabled' => $Enabled);
					$this->db->insert('broker_tracking_platforms', $InsertArray);
				} else {
					$UpdateArray = array('Name' => $_POST['Name'], 'Enabled' => $Enabled);
					$this->db->update('broker_tracking_platforms', $UpdateArray, 'ID = '.$ID);
				}
				
				header('Location: /BevoMedia/Admin/BrokerTrackingPlatforms.html');
				die;
			}
			
			$Sql = "SELECT
						*
					FROM
						broker_tracking_platforms
					WHERE
						(broker_tracking_platforms.ID = ?)
					";
			$this->TrackingPlatform = $this->db->fetchRow($Sql, $ID);
		}
		
		Public Function BrokerNetworks()
		{
			$this->TopMenu = $this->BrokersMenu();
			
			
			if (isset($_GET['EnableID'])) {
				$UpdateArray = array(
											'Enabled' => 1,
									);
				$this->db->update('broker_networks', $UpdateArray, 'ID = '.intval($_GET['EnableID']));
				
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				die;
				
//				header('Location: /Bevomedia/Admin/BrokerNetworks.html');
//				die;
			}
			
			if (isset($_GET['DisableID'])) {
				$UpdateArray = array(
											'Enabled' => 0,
									);
				$this->db->update('broker_networks', $UpdateArray, 'ID = '.intval($_GET['DisableID']));
				
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				die;
				
//				header('Location: /Bevomedia/Admin/BrokerNetworks.html');
//				die;
			}
			
			
			
			
			$Sql = "SELECT 
						*
					FROM
						broker_networks
					ORDER BY
						Name
					";
			
			$this->BrokerNetworks = $this->db->fetchAll($Sql);
		}
		
		Public Function BrokerNetworkForm()
		{
			$this->TopMenu = $this->BrokersMenu();
			
			
			$ID = intval($_GET['ID']);
			
			
			if (isset($_POST['Save'])) {
				
				if (!isset($_POST['IsIntegrated'])) {
					$_POST['IsIntegrated'] = 0;
				}
				
				if ($ID==0) {
					$InsertArray = array(
											'Username' => $_POST['Username'],
											'Password' => $_POST['Password'],
											'Name' => $_POST['Name'],
											'TrackingPlatformID' => $_POST['TrackingPlatformID'],
											'Email' => $_POST['Email'],
											'Phone' => $_POST['Phone'],
											'NetworkID' => $_POST['AffiliateNetworkID'],
											'PaymentPlan' => floatval($_POST['PaymentPlan']),
											'PaymentPlanTerm' => $_POST['PaymentPlanTerm'],
											'IsIntegrated' => intval($_POST['IsIntegrated']),
									);
					$this->db->insert('broker_networks', $InsertArray);
				} else {
					$UpdateArray = array(
											'Username' => $_POST['Username'],
											'Password' => $_POST['Password'],
											'Name' => $_POST['Name'],
											'TrackingPlatformID' => $_POST['TrackingPlatformID'],
											'Email' => $_POST['Email'],
											'Phone' => $_POST['Phone'],
											'NetworkID' => $_POST['AffiliateNetworkID'],
											'PaymentPlan' => floatval($_POST['PaymentPlan']),
											'PaymentPlanTerm' => $_POST['PaymentPlanTerm'],
											'IsIntegrated' => intval($_POST['IsIntegrated']),
									);
					$this->db->update('broker_networks', $UpdateArray, 'ID = '.$ID);
				}
				
				header('Location: /BevoMedia/Admin/BrokerNetworks.html');
				die;
			}
			
			$Sql = "SELECT
						*
					FROM
						broker_networks
					WHERE
						(broker_networks.ID = ?)
					";
			$this->BrokerNetwork = $this->db->fetchRow($Sql, $ID);
			
			
			
			$Sql = "SELECT 
						*
					FROM
						broker_tracking_platforms
					ORDER BY
						Name
					";
			
			$this->TrackingPlatforms = $this->db->fetchAll($Sql);
			
			$Sql = "SELECT
						id,
						title
					FROM
						bevomedia_aff_network
					ORDER BY
						title			
					";
			$this->AffiliateNetworks = $this->db->fetchAll($Sql);
		}
		
		Public Function BrokersMenu()
		{
			$TopMenu = '
				
				<a href="/Bevomedia/Admin/BrokerTrackingPlatforms.html">Tracking Platforms</a>
				|
				<a href="/BevoMedia/Admin/Networks.html">Broker Networks</a>
				|
				<a href="/BevoMedia/Admin/BrokerNetworkForm.html?ID=0">Insert Network</a>
				|
				<a href="/BevoMedia/Admin/BrokerTrackingPlatformForm.html?ID=0">Insert Tracking Platform</a>
				
				<br /><br />
			';
			
			return $TopMenu;
		}
		
		
		Public Function BlacklistAffiliates()
		{
			if (isset($_GET['DeleteID'])) 
			{
				$DeleteID = $_GET['DeleteID'];
				
				$this->db->delete('broker_blacklist_affiliate', ' ID = '.$DeleteID);
				
				header('Location: /BevoMedia/Admin/BlacklistAffiliates.html');
				die;				
			}
			
			$Sql = "SELECT
					broker_blacklist_affiliate.ID,
					broker_blacklist_affiliate.Name,
					broker_blacklist_affiliate.Email,
					broker_blacklist_affiliate.UserID,
					broker_blacklist_affiliate.Username,
					broker_blacklist_affiliate.Text,
					broker_blacklist_affiliate.Created,
					broker_networks.Name as `NetworkName`
				FROM
					broker_blacklist_affiliate,
					broker_networks
				WHERE
					(broker_networks.ID = broker_blacklist_affiliate.UserID)
				GROUP BY
					broker_blacklist_affiliate.ID
				ORDER BY
					broker_blacklist_affiliate.Created DESC		
				";
		
			$this->Posts = $this->db->fetchAll($Sql);
		}
		
		Public Function ViewAffiliatePost()
		{
			if (isset($_GET['DeleteID'])) 
			{
				$PostID = intval($_GET['ID']);
				$DeleteID = intval($_GET['DeleteID']);
				
				$this->db->delete('broker_blacklist_affiliate_comments', ' ID = '.$DeleteID);
				
				header('Location: /BevoMedia/Admin/ViewAffiliatePost.html?ID='.$PostID);
				die;				
			}
			
			$PostID = $_GET['ID'];
		
			$Sql = "SELECT
						broker_blacklist_affiliate.*,
						broker_networks.Name as `NetworkName`
					FROM
						broker_blacklist_affiliate,
						broker_networks
					WHERE
						(broker_networks.ID = broker_blacklist_affiliate.UserID) AND
						(broker_blacklist_affiliate.ID = ?)
			
					";		
			$this->Post = $this->db->fetchRow($Sql, array($PostID));
	
			
			
			$Sql = "SELECT
						broker_blacklist_affiliate_comments.*,
						broker_networks.Name as `NetworkName`
					FROM
						broker_blacklist_affiliate_comments,
						broker_networks
					WHERE
						(broker_networks.ID = broker_blacklist_affiliate_comments.UserID) AND
						(broker_blacklist_affiliate_comments.PostID = ?)
					ORDER BY
						broker_blacklist_affiliate_comments.Created
					";
			$this->Comments = $this->db->fetchAll($Sql, array($PostID));
		}
		
		Public Function PostAffiliateComment()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$this->Success = false;
			
			if (isset($_POST['Save']))
			{
				
				if ( ($_POST['Text']=='') )
				{
					$this->ErrorMessage = 'You must enter comment.';
					return;
				}
				
				$Array = array (
								'PostID' => $_POST['PostID'],
								'UserID' => 1,
								'Title' => $_POST['Title'],
								'Text' => nl2br($_POST['Text']),
								'Username' => $_POST['Username'],
							);
							
				if (!isset($_GET['CommentID'])) 
				{
					$this->db->insert('broker_blacklist_affiliate_comments', $Array);
				} else 
				{
					$this->db->update('broker_blacklist_affiliate_comments', $Array, ' ID = '.$_GET['CommentID']);
				}
				
				$this->Success = true;
				
				return;
			}
			
			if (isset($_GET['CommentID']))
			{
				$Sql = "SELECT * FROM broker_blacklist_affiliate_comments WHERE ID = ?";
				$this->Comment = $this->db->fetchRow($Sql, $_GET['CommentID']); 
			}
		}
		
		Public Function BlacklistAdvertisers()
		{
			if (isset($_GET['DeleteID'])) 
			{
				$DeleteID = $_GET['DeleteID'];
				
				$this->db->delete('broker_blacklist_advertiser', ' ID = '.$DeleteID);
				
				header('Location: /BevoMedia/Admin/BlacklistAdvertisers.html');
				die;				
			}
			
			
			$Sql = "SELECT
						broker_blacklist_advertiser.ID,
						broker_blacklist_advertiser.Name,
						broker_blacklist_advertiser.Email,
						broker_blacklist_advertiser.UserID,
						broker_blacklist_advertiser.Username,
						broker_blacklist_advertiser.Text,
						broker_blacklist_advertiser.Created,
						broker_networks.Name as `NetworkName`
					FROM
						broker_blacklist_advertiser,
						broker_networks
					WHERE
						(broker_networks.ID = broker_blacklist_advertiser.UserID)
					GROUP BY
						broker_blacklist_advertiser.ID
					ORDER BY
						broker_blacklist_advertiser.Created DESC		
					";
			
			$this->Posts = $this->db->fetchAll($Sql);		
		}
		
		Public Function ViewAdvertiserPost()
		{
			if (isset($_GET['DeleteID'])) 
			{
				$PostID = intval($_GET['ID']);
				$DeleteID = intval($_GET['DeleteID']);
				
				$this->db->delete('broker_blacklist_advertiser_comments', ' ID = '.$DeleteID);
				
				header('Location: /BevoMedia/Admin/ViewAdvertiserPost.html?ID='.$PostID);
				die;				
			}
			
			
			$PostID = $_GET['ID'];
			
			$Sql = "SELECT
						broker_blacklist_advertiser.*,
						broker_networks.Name as `NetworkName`
					FROM
						broker_blacklist_advertiser,
						broker_networks
					WHERE
						(broker_networks.ID = broker_blacklist_advertiser.UserID) AND
						(broker_blacklist_advertiser.ID = ?)
			
					";		
			$this->Post = $this->db->fetchRow($Sql, array($PostID));
	
			
			
			$Sql = "SELECT
						broker_blacklist_advertiser_comments.*,
						broker_networks.Name as `NetworkName`
					FROM
						broker_blacklist_advertiser_comments,
						broker_networks
					WHERE
						(broker_networks.ID = broker_blacklist_advertiser_comments.UserID) AND
						(broker_blacklist_advertiser_comments.PostID = ?)
					ORDER BY
						broker_blacklist_advertiser_comments.Created
					";
			$this->Comments = $this->db->fetchAll($Sql, array($PostID));
		}
		
		Public Function PostAdvertiserComment()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$this->Success = false;
			
			if (isset($_POST['Save']))
			{
				
				if ( ($_POST['Text']=='') )
				{
					$this->ErrorMessage = 'You must enter comment.';
					return;
				}
				
				$Array = array (
								'PostID' => $_POST['PostID'],
								'UserID' => 1,
								'Title' => $_POST['Title'],
								'Text' => nl2br($_POST['Text']),
								'Username' => $_POST['Username'],
							);
				if (!isset($_GET['CommentID'])) 
				{
					$this->db->insert('broker_blacklist_advertiser_comments', $Array);
				} else 
				{
					$this->db->update('broker_blacklist_advertiser_comments', $Array, ' ID = '.$_GET['CommentID']);
				}
				
				$this->Success = true;
				
				return;
			}
			
			if (isset($_GET['CommentID']))
			{
				$Sql = "SELECT * FROM broker_blacklist_advertiser_comments WHERE ID = ?";
				$this->Comment = $this->db->fetchRow($Sql, $_GET['CommentID']); 
			}
		}
		
		Public Function PostAffiliate()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$this->Success = false;
			if (isset($_POST['Save']))
			{
				if ($_POST['Name']=='') {
					$this->ErrorMessage = 'You must enter name.';
					return;	
				}
				
				$Array = array (
								'UserID' => 1,
								'Name' => $_POST['Name'],
								'Email' => $_POST['Email'],
								'Address' => $_POST['Address'],
								'Text' => nl2br($_POST['Text']),
								'Username' => $_POST['Username'],
								'KnownAttachedIndividuals' => $_POST['KnownAttachedIndividuals'],
								'CustomHeat' => $_POST['CustomHeat'],
								'Phone' => $_POST['Phone'],
							);
							
				if (!isset($_GET['ID'])) 
				{
					$this->db->insert('broker_blacklist_affiliate', $Array);
				} else 
				{
					$this->db->update('broker_blacklist_affiliate', $Array, ' ID = '.$_GET['ID']);
				}
				
				$this->Success = true;
				return;
			}
			
			if (isset($_GET['ID']))
			{
				$Sql = "SELECT * FROM broker_blacklist_affiliate WHERE ID = ?";
				$this->Post = $this->db->fetchRow($Sql, $_GET['ID']); 
			}
		}		
		
		Public Function PostAdvertiser()
		{
			Zend_Registry::set('Instance/LayoutType', 'shadowbox-layout');
			
			$this->Success = false;
			if (isset($_POST['Save']))
			{
				if ($_POST['Name']=='') {
					$this->ErrorMessage = 'You must enter name.';
					return;	
				}
				
				$Array = array (
								'UserID' => 1,
								'Name' => $_POST['Name'],
								'Email' => $_POST['Email'],
								'Address' => $_POST['Address'],
								'Text' => nl2br($_POST['Text']),
								'Username' => $_POST['Username'],
								'KnownAttachedIndividuals' => $_POST['KnownAttachedIndividuals'],
								'CustomHeat' => $_POST['CustomHeat'],
								'Phone' => $_POST['Phone'],
							);
							
				if (!isset($_GET['ID'])) 
				{
					$this->db->insert('broker_blacklist_advertiser', $Array);
				} else 
				{
					$this->db->update('broker_blacklist_advertiser', $Array, ' ID = '.$_GET['ID']);
				}
				
				$this->Success = true;
				return;
			}
			
			if (isset($_GET['ID']))
			{
				$Sql = "SELECT * FROM broker_blacklist_advertiser WHERE ID = ?";
				$this->Post = $this->db->fetchRow($Sql, $_GET['ID']); 
			}
		}

		Public Function OffersQueue()
		{
			$Sql = "SELECT
						*
					FROM
						bevomedia_queue
					WHERE
						(bevomedia_queue.user__id IS NULL) AND
						(bevomedia_queue.`type` LIKE  '%offers%')
					ORDER BY
						bevomedia_queue.started DESC
					";
			$this->Offers = $this->db->fetchAll($Sql);
		}
		
		Public Function NetworksJSON()
		{	
			function v($p, $d)
		    {
				if(isset($_REQUEST[$p])&&!empty($_REQUEST[$p]))
			  		return mysql_real_escape_string($_REQUEST[$p]);
				else
			  		return $d;
		    }
			
		    $Search = v('search', false);
		    $SearchAdd = "";
			if($Search && ($Search!=''))
		  	{
		  		$SearchAdd = " AND (broker_networks.Name LIKE '%{$Search}%' OR broker_networks.Username LIKE '%{$Search}%' OR broker_networks.Email LIKE '%{$Search}%' OR broker_networks.ID LIKE '%{$Search}%') ";
		  	}
		  	
		  	$Order = v('o', 'id');
		  	$OrderDir = v('o_dir', 'desc');
		  	$lStart = v('start', 0);
		  	$lEnd = v('end', 50);
		    
			$Sql = "SELECT
						*
					FROM
						broker_networks
					WHERE 
						(1=1)
						{$SearchAdd}
					ORDER BY
						{$Order} {$OrderDir}
					LIMIT
						{$lStart}, {$lEnd}
					";
			$Resuts = $this->db->fetchAssoc($Sql);
		  	$Count = $this->db->fetchOne("SELECT count(*) FROM broker_networks WHERE (1=1) {$SearchAdd}");
		  	$Arr = array('results' => $Resuts,
				'passback' => @$_GET['passback'],
				'count' => $Count);
		  		
		  	die(json_encode($Arr));
		}
		
		Public Function Networks()
		{
			$this->TopMenu = $this->BrokersMenu();
		}
		
		Public Function BrokerNetworkView()
		{
			if (isset($_GET['LoginID']))
			{
				//set POST variables
				$url = 'http://networks.bevomedia.com/BevoMedia/Index/Index.html?LoginID='.$_GET['LoginID'].'&Key='.md5('Secure'.$_GET['LoginID']);
				header('Location: '.$url);
				die;
			}
			
			$ID = intval($_GET['ID']);
			
			$Sql = "SELECT
						*
					FROM
						broker_networks
					WHERE
						(broker_networks.ID = ?)
					";
			$this->BrokerNetwork = $this->db->fetchRow($Sql, $ID);
			
			
			$Sql = "SELECT 
						Name
					FROM
						broker_tracking_platforms
					WHERE
						(ID = ?)
					";
			
			$this->TrackingPlatform = $this->db->fetchOne($Sql, $this->BrokerNetwork->TrackingPlatformID);
			
			$Sql = "SELECT
						title
					FROM
						bevomedia_aff_network
					ORDER BY
						title			
					";
			$this->AffiliateNetwork = $this->db->fetchOne($Sql, $this->BrokerNetwork->NetworkID);
			
		}
		
	}

?>