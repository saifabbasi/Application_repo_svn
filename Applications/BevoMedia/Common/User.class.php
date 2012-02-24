<?php

/**
 * User Class
 */

/**
 * User Class
 * 
 * User Class
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */

Class User {
	
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $id
	 */
	Public $id;
	
	/**
	 * @var String $email
	 */
	Public $email;
	
	/**
	 * @var String $password
	 */
	Public $password;
	
	/**
	 * @var Integer $enabled
	 */
	Public $enabled;
	
	/**
	 * @var String $created
	 */
	Public $created;
	
	/**
	 * @var Integer $deleted
	 */
	Public $deleted;
	
	/**
	 * @var Integer $isSelfHosted
	 */
	Public $isSelfHosted;
	
	/**
	 * @var String $membershipType
	 */
	Public $membershipType;

	/**
	 * @var integer
	 */
	Public $apiCalls;
	
	
	/**
	 * @var string
	 */
	Public $apiKey;
	
	Public $lastNetworkUpdate;
	Public $lastPPCUpdate;
	
	/**
	 * @var String $firstName
	 */
	Public $firstName;
	
	/**
	 * @var String $lastName
	 */
	Public $lastName;
	
	/**
	 * @var String $companyName
	 */
	Public $companyName;
	
	/**
	 * @var String $address
	 */
	Public $address;
	
	/**
	 * @var String $city
	 */
	Public $city;
	
	/**
	 * @var String $state
	 */
	Public $state;
	
	/**
	 * @var String $zip
	 */
	Public $zip;
	
	/**
	 * @var String $country
	 */
	Public $country;
	
	/**
	 * @var String $phone
	 */
	Public $phone;
	
	/**
	 * @var String $website
	 */
	Public $website;
	
	/**
	 * @var String $messenger
	 */
	Public $messenger;
	
	/**
	 * @var String $messengerHandle
	 */
	Public $messengerHandle;
	
	/**
	 * @var String $marketingMethod
	 */
	Public $marketingMethod;
	
	/**
	 * @var String $marketingMethodOther
	 */
	Public $marketingMethodOther;
	
	/**
	 * @var String $howHeard
	 */
	Public $howHeard;
	
	/**
	 * @var String $comments
	 */
	Public $comments;
	
	/**
	 * @var Mixed $Stats
	 */
	Public $Stats;
	
	
	/**
	 * @var Mixed $lastLogin
	 */
	Public $lastLogin;
	
	
	/**
	 * @var String $Timezone
	 */
	Public $Timezone;
	
	/**
	 * @var String $_mentor_to_user_table_name
	 */
	Private $_mentor_to_user_table_name = 'bevomedia_mentor_to_user';
	
	
	const PRODUCT_SERVER_CHARGE = 'Server Charge';
	const PRODUCT_SELF_HOSTED_YEARLY_CHARGE = 'Self-Hosted';
	const PRODUCT_API_CALLS_CHARGE = 'API Calls';
	const PRODUCT_PPC_YEARLY_CHARGE = 'PPC';
	const PRODUCT_GOOGLE_ADWORDS = 'Google Adwords';	
	const PRODUCT_PPVSPY_MONTHLY = 'PPVSpy Monthly';
	const PRODUCT_PPVSPY_YEARLY = 'PPVSpy';
	
	const PRODUCT_ADWATCHER_MONTHLY = 'AdWatcher Monthly';
	const PRODUCT_ADWATCHER_YEARLY = 'AdWatcher';
	
	const PRODUCT_DUMMY_PRODUCT = 'Dummy Product';
	
	const PRODUCT_FREE_SELF_HOSTED = 'Free Self-Hosted';
	const PRODUCT_INSTALL_NETWORKS = 'Free Install Networks';
	const PRODUCT_FREE_PPC = 'Free PPC';
	const PRODUCT_FREE_RESEARCH = 'Free Research';
	const PRODUCT_FREE_PPVSPY = 'Free PPVSpy';
	
	const PRODUCT_PPVSPY_REFERRAL_PRICE = 'PPVSpy Referral Price';
	
	
	/**
	 * Constructor
	 *
	 * @param Integer $ID
	 */
	Public Function __construct($ID = false)
	{
		if(class_exists('Zend_Registry'))
		{
			$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		}else{
			$IncludePaths = array(
			    realpath(ABSPATH .'Externals'),
			    '.',
			);
			set_include_path(implode(PATH_SEPARATOR, $IncludePaths));
			
			require_once ABSPATH . 'Externals/Zend/Db.php';
			$config = array(
			    'host'     => ABSDBHOST,
			    'username' => ABSDBUSER,
			    'password' => ABSDBPASS,
			    'dbname'   => ABSDBNAME,
			);
			
			$this->_db = Zend_Db::factory('PDO_MYSQL', $config);
			$this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
		}

		if($ID !== false)
		{
			$this->id = $ID;
			$this->getInfo();
		}		
	}
	
	/**
	 * Unsets the User parameter from $_SESSION effectively logging this User out.
	 */
	Public Function Logout()
	{
		unset($_SESSION['User']);
	}
	
	/**
	 * Populates this $Stats with a new User_Stats class using this $ID.
	 * @see User_Stats
	 */
	Public Function LoadStats()
	{
		$this->Stats = new User_Stats($this->id);
	}
	
	/**
	 * Returns '1' if the account for this use is self hosted and '0' if is a self hosted install
	 * or if the account is not self hosted
	 * @return string
	 */
	Public Function IsSelfHosted()
	{
		if(class_exists('Zend_Registry'))
		{
			if(Zend_Registry::get('Application/Mode') == 'SelfHosted')
			{
				return '0';
			}
		}
		return $this->isSelfHosted;	
	}
	
	/**
	 * Removes api calls from the user
	 */
	Public Function subtractApiCalls($amount, $reason = '')
	{
		$Data = array('amount' => -1*$amount, 'user__id' => $this->id, 'reason' => $reason);
		$this->_db->insert('bevomedia_api_calls', $Data);
		$this->getInfo($this->id);
		$new = $this->apiCalls;
		if($new < 0)
		  $new = 0;
		$Data = array('apiCalls'=>$new);
		$this->_db->update('bevomedia_user', $Data, 'id = ' . $this->id);
	}
	
	Public Function addApiCalls($amount, $reason = '')
	{
		$Data = array('amount' => $amount, 'user__id' => $this->id, 'reason' => $reason);
		$this->_db->insert('bevomedia_api_calls', $Data);
		$this->getInfo($this->id);
		$new = $this->apiCalls;
		$Data = array('apiCalls'=>$new);
		$this->_db->update('bevomedia_user', $Data, 'id = ' . $this->id);
	}
	Public Function setLastNetworkUpdate($at)
	{
		$Data = array('lastNetworkUpdate'=>$at);
		$this->_db->update('bevomedia_user', $Data, 'id = ' . $this->id);
		$this->lastNetworkUpdate = $at;
	}
	Public Function setLastPPCUpdate($at)
	{
		$Data = array('lastPPCUpdate'=>$at);
		$this->_db->update('bevomedia_user', $Data, 'id = ' . $this->id);
		$this->lastPPCUpdate = $at;
	}
	Public Function setVaultID($vaultID, $vaultLast4Digits = '0000') 
	{
		$Data = array('vaultID' => $vaultID, 'vaultLast4Digits' => $vaultLast4Digits);
		$this->_db->update('bevomedia_user', $Data, 'id = ' . $this->id);
	}
	/**
	 * Returns an array of rows from the Accounts_Adwords table that have a valid Username and Password.
	 *
	 * @return Array
	 */
	Public Function getDailyAccountsAdwords()
	{
		$AcctAdwords = new Accounts_Adwords($this->id);
		return $AcctAdwords->GetInstalledAccounts();
	}
	
	/**
	 * Returns array of all rows from the Accounts_Analytics table for the User matching this $ID.
	 *
	 * @return Array
	 */
	Public Function getAllAccountsAnalytics()
	{
		$AcctAnalytics = new Accounts_Analytics($this->id);
		return $AcctAnalytics->GetAllAccounts();
	}
	
	/**
	 * Returns array of all rows from the Accounts_Adwords table for the User matching this $ID.
	 *
	 * @return Array
	 */
	Public Function getAllAccountsAdwords()
	{
		$AcctAdwords = new Accounts_Adwords($this->id);
		return $AcctAdwords->GetAllAccounts();
	}
	
	/**
	 * Returns array of all rows from the Accounts_Yahoo table for the User matching this $ID.
	 *
	 * @return Array
	 */
	Public Function getAllAccountsYahoo()
	{
		$AcctYahoo = new Accounts_Yahoo($this->id);
		return $AcctYahoo->GetAllAccounts();
	}
	
	/**
	 * Returns array of all rows from the Accounts_MSN table for the User matching this $ID.
	 *
	 * @return Array
	 */
	Public Function getAllAccountsMSN()
	{
		$AcctMSN = new Accounts_MSNAdCenter($this->id);
		return $AcctMSN->GetAllAccounts();
	}
	
	/**
	 * Return array of all rows from all three PPC Providers for all users.
	 *
	 * @return Array
	 */
	Public Function getAllAccounts()
	{
		$Output = array();
		foreach($this->GetAllAccountsAdwords() as $Adwords)
			$Output[] = $Adwords;
		foreach($this->GetAllAccountsYahoo() as $Yahoo)
			$Output[] = $Yahoo;
		foreach($this->GetAllAccountsMSN() as $MSN)
			$Output[] = $MSN;
			
		return $Output;
	}
	Public Function getAllAffiliateAccounts()
	{
	  return $this->_db->fetchAll('select un.*, an.title as title from bevomedia_user_aff_network un left join bevomedia_aff_network an on (an.id=un.network__id) where un.user__id='.$this->id);
	}
	
	/**
	 * Return the current Adwords API Balance for the User matching this $ID.
	 *
	 * @return Float
	 */
	Public Function getAdwordsAPIBalance()
	{
		$APIUse = new Adwords_API_Usage(false, $this->id);
		return $APIUse->Balance;
	}
	
	/**
	 * Populates the object with information from the database.
	 * 
	 * @param Integer $User_ID
	 * 
	 * @return User
	 */
	Public Function getInfo($User_ID = false)
	{
		if(!isset($this->id) && $User_ID == false)
		{
			return false;
		}
		if($User_ID == false)
			$User_ID = $this->id;
		$Sql = 'SELECT bevomedia_user.id as id, bevomedia_user.*, bevomedia_user_info.*, bevomedia_user_timezone.timezone as timezone FROM bevomedia_user LEFT JOIN bevomedia_user_info USING (id) LEFT JOIN bevomedia_user_timezone ON (bevomedia_user_timezone.user__id = bevomedia_user.id) WHERE bevomedia_user.id = ?';
		$Result = $this->_db->fetchRow($Sql, $User_ID);
		if($Result)
		{
			foreach($Result as $Key=>$Value)
			{
				$this->set($Key, $Value);
			}
			$this->id = $User_ID;
		}else{
			return false;
		}
		$sql = "select sum(amount) from bevomedia_api_calls where user__id={$this->id}";
		$this->apiCalls = $this->_db->fetchOne($sql);
		if($this->apiCalls < 0)
		  $this->apiCalls = 0;
		return $this;
	}
	
	/**
	 * Return the concatenation of this $FirstName and this $LastName separated by a single space.
	 *
	 * @return String
	 */
	Public Function getUserName()
	{
		return $this->firstName . ' ' . $this->lastName;
	}
	
	/**
	 * Return the ID of a User matching the specified $Email.
	 * Return false otherwise.
	 * 
	 * @param String $Email
	 * @return Mixed
	 */
	Public Function getIdUsingEmail($Email)
	{
		$Result = $this->_db->fetchRow('SELECT * FROM bevomedia_user WHERE email = ?', $Email);
		if(!$Result || !sizeOf($Result))
			return false;
		return $Result->id;
	}
	
	Public Function getVaultID() 
	{
		$Result = $this->_db->fetchRow('SELECT vaultID FROM bevomedia_user WHERE id = ?', $this->id);
		if(!$Result || !sizeOf($Result))
			return false;
			
		return $Result->vaultID;
	}
	
	/**
	 * Return true if the specified $Email exists within the User table.
	 *
	 * @param String $Email
	 * @return Boolean
	 */
	Public Function emailExists($Email)
	{
		$Result = $this->_db->fetchRow('SELECT * FROM bevomedia_user WHERE email = ?', $Email);
		if(sizeOf($Result) == 0 || !$Result)
			return false;
		else
			return true;
	}
	
	/**
	 * Return the name of the mentor that this user is assigned to.
	 * If the user is not assigned to a mentor this returns false.
	 *
	 * @return Mixed
	 */
	Public Function getMentorName()
	{
		$Mentor = $this->_db->fetchRow('SELECT * FROM ' . $this->_mentor_to_user_table_name . ' WHERE user__id = ' . $this->id);
		if(!sizeOf($Mentor) || !$Mentor)
			return false;
		$Mentor = new Mentor($Mentor->mentor__id);
		return $Mentor->name;
	}
	
	/**
	 * Return a Mentor object matching this user's assigned mentor.
	 * If the user is not assigned to a mentor this returns false.
	 * 
	 * @see Mentor
	 * @return Mixed
	 */
	Public Function getMyMentor()
	{
		$Mentor = $this->_db->fetchRow('SELECT * FROM ' . $this->_mentor_to_user_table_name . ' WHERE user__id = ' . $this->id);
		if(!sizeOf($Mentor) || !$Mentor)
			return false;
		$Mentor = new Mentor($Mentor->mentor__id);
		return $Mentor;
	}
	
	/**
	 * Inserts a new row into the table using the values provided by $Data and returns the table insert ID.
	 *
	 * @todo				Add sample code.
	 * 
	 * @param Array $Data	The values to be inserted into the table.
	 * @return Integer		The id of the inserted row.
	 */
	Public Function Insert($Data)
	{
		$Insert = array();
		$Insert['Email'] = $Data['Email'];
		$Insert['Password'] = $Data['Password'];
		$Insert['Username'] = $Data['Username'];
		
		/* @var $MCAPI MCAPI */
		$MCAPI = new MCAPI(); 
		
		$result = $MCAPI->listSubscribe('7650380bc2', $Insert['Email'], array('FNAME' => $Data['FirstName'], 'LNAME' => $Data['LastName']), 'html', false);
		
		
//New Rev Model - No free API Calls
//		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Basic_API_Units'";
//		$apiCalls = intval($this->_db->fetchOne($Sql));
		
		
		$Sql = "SELECT value FROM bevomedia_settings WHERE name = 'Auto_Accept_New_Applicants'";
		$Auto_Accept_New_Applicants = $this->_db->fetchRow($Sql);
		$Auto_Accept_New_Applicants = intval($Auto_Accept_New_Applicants->value);
		
		
		if($this->EmailExists($Insert['Email']) == false)
		{
			foreach($Insert as $Key=>$Value)
				$Insert[$Key] = $this->_db->quote($Value);
			
			$Sql = "Insert INTO bevomedia_user (email, username, password) VALUES ($Insert[Email], $Insert[Username], md5($Insert[Password]))";
			$this->_db->exec($Sql);
			$this->id = $this->_db->lastInsertId();
			
			if (isset($Data['apiKey'])) {
				$Sql = "UPDATE bevomedia_user SET apiKey = {$Data['apiKey']} WHERE id = {$this->id} ";
				$this->_db->exec($Sql);
			}
		}else{
			echo 'ERROR EMAIL ALREADY TAKEN';
			return 0;
		}
		
		$this->InsertInfo($Data, $this->id);
		
		$Timezone = $Data['Timezone'];
		$this->InsertTimezone($Timezone, $this->id);
		if ($Auto_Accept_New_Applicants==1)
		{
			$this->EnableUser($this->id);
		}
		
		if (isset($_COOKIE['BevoReferral']) && (strlen($_COOKIE['BevoReferral'])==32) ) 
		{
			$ReferrerID = $this->FindUserIdByReferralCode($_COOKIE['BevoReferral']);
			
			if ($ReferrerID)
			{
				$Array = array(
								'ReferrerID' => $ReferrerID,
								'UserID'	 => $this->id,			
							  );
			    $this->_db->insert('bevomedia_referrals', $Array);
			}	
		} else
		if (isset($_COOKIE['BevoReferralS']) && (strlen($_COOKIE['BevoReferralS'])==32) ) 
		{
			$ReferrerID = $this->FindUserIdByReferralCode($_COOKIE['BevoReferralS']);
			
			if ($ReferrerID)
			{
				$Array = array(
								'ReferrerID' => $ReferrerID,
								'UserID'	 => $this->id,			
							  );
			    $this->_db->insert('bevomedia_referrals', $Array);
			    $ReferID = $this->_db->lastInsertId();
			    
			    $ReferrerUser = new User($ReferrerID);
			    if ($ReferrerUser->IsSubscribed(User::PRODUCT_PPVSPY_REFERRAL_PRICE)) {
				    $Array = array(
									'ReferID' => $ReferID			
								  );
				    $this->_db->insert('bevomedia_referrals_ppvspy', $Array);
			    }
			}
		}
		
		
		$this->GetInfo();
//New Rev Model - No free API Calls
//		$this->addApiCalls($apiCalls, 'New Account');
		return $this->id;
	}
	
	/**
	 * Updates the UserInfo table setting values within $Data where the row ID equals this $ID.
	 *
	 * @param Array $Data
	 */
	Public Function Update($Data)
	{
		$Timezone = $Data['Timezone'];
		unset($Data['Timezone']);
		$this->_db->update('bevomedia_user_info', $Data, 'id = ' . $this->id);
		$this->UpdateTimezone($Timezone, $this->id);
		$this->GetInfo();
	}
	
	/**
	 * Updates the User and UserInfo table setting rows to Deleted status where the row ID matches the specified $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function DeleteUser($ID)
	{
		$Data = array('deleted'=>1);
		$this->_db->update('bevomedia_user', $Data, 'ID = ' . $ID);
		$this->_db->update('bevomedia_user_info', $Data, 'ID = ' . $ID);
	}
	
	Public Function UpdateIsSelfHosted($Status, $ID = NULL)
	{
		if($ID == NULL)
		{
			$ID = $this->id;
		}
		$Data = array('isSelfHosted'=>$Status);
		$this->_db->update('bevomedia_user', $Data, 'id = ' . $ID);
	}
	
	Public Function UpdateMembershipType($MembershipType, $ID = NULL)
	{
		if($ID == NULL)
		{
			$ID = $this->id;
		}
		$Data = array('membershipType'=>$MembershipType);
		$this->_db->update('bevomedia_user', $Data, 'id = '.$ID);
		if($MembershipType == 'premium')
		  $this->addApiCalls(250000, 'Upgraded to Premium');
	}
	
	
	/**
	 * Permanently removes rows from User and UserInfo where the row ID matches this $ID.
	 */
	Public Function PermanentDelete()
	{
		$this->_db->delete('bevomedia_user', 'ID = ' . $this->id);
		$this->_db->delete('bevomedia_user_info', 'ID = ' . $this->id);
	}
	
	/**
	 * Updates the User and UserInfo table unsetting the Deleted status from rows where the row ID matches the specified $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function RestoreUser($ID)
	{
		$Data = array('deleted'=>0);
		$this->_db->update('bevomedia_user', $Data, 'id = ' . $ID);
		$this->_db->update('bevomedia_user_info', $Data, 'id = ' . $ID);
	}
	
	/**
	 * Returns the amount of notes that this user has been given by Admins.
	 * @see Admin
	 * @see User_Notes::Insert()
	 *
	 * @return Index
	 */
	Public Function getNoteCount()
	{
		$Notes = new User_Notes();
		$Notes = $Notes->GetAllNotes($this->id);
		return sizeOf($Notes);
	}
	
	/**
	 * Returns an array of User objects for all rows within the User table.
	 * 
	 * @see User
	 * @return Array
	 */
	Public Function getAllUsers()
	{
		$Output = array();
		$Users = $this->_db->fetchAll('SELECT * FROM bevomedia_user');
		foreach($Users as $User)
			$Output[] = new User($User->id);
			
		return $Output;
	}
	
	/**
	 * Returns an array of User objects for all rows within the User table.
	 * 
	 * @see User
	 * @return Array
	 */
	Public Function getAllActiveUsers()
	{
		$Sql = "SELECT 
					bevomedia_user.*
				FROM 
					bevomedia_user,
					bevomedia_tracker_clicks
				WHERE 
					(deleted = 0) AND
					(bevomedia_tracker_clicks.user__id = bevomedia_user.id)
				GROUP BY
					bevomedia_tracker_clicks.user__id
				";
		
		$Output = array();
		$Users = $this->_db->fetchAll($Sql);
		foreach($Users as $User)
			$Output[] = new User($User->id);
			
		return $Output;
	}
	
	/**
	 * Returns an array of User objects for all rows within the User table where the specified $Search matches any of the following properties: <br/>
	 * FirstName, LastName, Email, Comments, Website, MessengerHandle
	 *
	 * @param String $Search
	 * @return Array
	 */
	Public Function searchUsers($Search = false)
	{
		$Output = array();
		if($Search === false)
			return $Output;
			
		$Users = $this->_db->fetchAll("SELECT bevomedia_user.id as id FROM bevomedia_user LEFT JOIN bevomedia_user_info ON bevomedia_user_info.id = bevomedia_user.id
					WHERE (	firstName LIKE '%$Search%' OR lastName LIKE '%$Search%' OR 
							email LIKE '%$Search%' OR comments LIKE '%$Search%' OR 
							website LIKE '%$Search%' OR messengerHandle LIKE '%$Search%'
							)");
		foreach($Users as $User)
			$Output[] = new User($User->id);
			
		return $Output;
	}
	
	/**
	 * Returns an array of User objects for all non deleted users within the User table.
	 *
	 * @return Array
	 */
	Public Function getAllNonDeletedUsers()
	{
		$Output = array();
		$Users = $this->_db->fetchAll('SELECT * FROM bevomedia_user WHERE deleted = 0');
		foreach($Users as $User)
			$Output[] = new User($User->id);
			
		return $Output;
	}
	
	/**
	 * Returns an array of User objects for all non deleted users within the User table.
	 *
	 * @return Array
	 */
	Public Function getAllSelfHostedUsers()
	{
		$Output = array();
		$Users = $this->_db->fetchAll('SELECT * FROM bevomedia_user WHERE isSelfHosted != 0');
		foreach($Users as $User)
			$Output[] = new User($User->id);
			
		return $Output;
	}
	
	/**
	 * Returns an array of User objects for all non deleted users within the User table and executes the LoadStats() function on each User entry.
	 * 
	 * @see LoadStats()
	 * @return Array
	 */
	Public Function getAllNonDeletedUsersWithStats()
	{
		$Output = array();
		$Users = $this->_db->fetchAll('SELECT * FROM bevomedia_user WHERE Deleted = 0');
		foreach($Users as $User)
		{
			$Temp = new User($User->id);
			$Temp->LoadStats();
			$Output[] = $Temp;
		}	
		return $Output;
	}
	
	/**
	 * Return an array of User objects for all rows within the User table that have been marked as deleted.
	 *
	 * @return Array
	 */
	Public Function getAllDeletedUsers()
	{
		$Output = array();
		$Users = $this->_db->fetchAll('SELECT * FROM bevomedia_user WHERE Deleted = 1');
		foreach($Users as $User)
			$Output[] = new User($User->id);
			
		return $Output;
	}
	
	/**
	 * Return an array of User objects for all rows within the User table that have their Enabled value set to '0'.
	 *
	 * @return Array
	 */
	Public Function getNewApplications()
	{
		$Output = array();
		$Users = $this->_db->fetchAll('SELECT * FROM bevomedia_user WHERE Enabled = 0');
		foreach($Users as $User)
			$Output[] = new User($User->id);
			
		return $Output;
	}
	
	/**
	 * Inserts a new row into the UserInfo table for the specified $User_ID using the information provided within the associative $Data array.
	 *
	 * @param Array $Data
	 * @param Integer $User_ID
	 */
	Public Function InsertInfo($Data, $User_ID)
	{
		$Username = $Data['Username'];
		$Email = $Data['Email'];
		
		$Insert = array();
		$Insert['id'] = $User_ID;
		unset($Data['Password']);
		unset($Data['re-enter_password']);
		unset($Data['Email']);
		unset($Data['registerFormSubmit']);
		unset($Data['EULAAccepted']);
		unset($Data['Timezone']);
		unset($Data['Username']);
		foreach($Data as $Key=>$Value)
		{
			$Insert[$Key] = $Value;
		}
		$this->_db->Insert('bevomedia_user_info', $Insert);
		
		if (@$_SERVER['HTTPS'])
			$prefix = "https://";
		else
			$prefix = "http://";
		
		
		file_get_contents($prefix.$_SERVER["SERVER_NAME"].'/_create_user.php?Username='.$Username.'&Email='.$Email);
	}
	
	/**
	 * Inserts a new row into the User_Timezone table for the specified $User_ID.
	 *
	 * @param Array $Timezone
	 * @param Integer $User_ID
	 */
	Public Function InsertTimezone($Timezone, $User_ID)
	{
		$Insert = array();
		$Insert['user__id'] = $User_ID;
		$Insert['timezone'] = $Timezone;
		$this->_db->Insert('bevomedia_user_timezone', $Insert);
	}
	
	/**
	 * Update the User_Timezone table and set Timezone to '$Timezone' where the User_ID matches $User_ID.
	 *
	 * @param String $Timezone
	 * @param Integer $User_ID
	 */
	Public Function updateTimezone($Timezone, $User_ID)
	{
		$NumRows = $this->_db->exec("UPDATE bevomedia_user_timezone SET timezone = '$Timezone' WHERE user__id = $User_ID");
		if($NumRows == 0)
		{
			$this->InsertTimezone($Timezone, $User_ID);
		}
	}
	
	/**
	 * Update the User table and set Enabled to '1' where the row ID matches $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function enableUser($ID)
	{
		$this->_db->exec("UPDATE bevomedia_user SET enabled = 1 WHERE id = $ID");
		
	}
	
	/**
	 * Update the User table and set Enabled to '0' where the row ID matches $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function disableUser($ID)
	{
		$this->_db->exec("UPDATE bevomedia_user SET enabled = 0 WHERE id = $ID");
	}
	
	/**
	 * Generate and send this User a password reset code so that they may change their password.
	 * If the user already has an outstanding reset code this will remove it and create a new one.
	 */
	Public Function resetPassword()
	{
		$Code = $this->generateResetCode();
		$Email = $this->email;
		$Subject = 'Reset Password Request from ' . $_SERVER['HTTP_HOST'];
		$Body = <<<END
This is an automatically generated email from $_SERVER[HTTP_HOST] regarding a password reset.<br/>
If you did not request a password reset then please disregard this email.<br/>
<br/>
Otherwise, follow this link to reset your password:<br/>
<a href="http://$_SERVER[HTTP_HOST]/BevoMedia/Index/ResetPassword.html?EmailCode=$Code&Email=$Email">http://$_SERVER[HTTP_HOST]/BevoMedia/Index/ResetPassword.html?EmailCode=$Code&Email=$Email</a>
END;
		
		$this->ClearResetCode();
		$this->InsertResetCode($Code);
		$this->SendEmail($Email, $Subject, $Body);
	}
	
	/**
	 * Returns true if the provided password reset $Code matches the existing code for this User.
	 * Returns false otherwise.
	 * 
	 * @param String $Code
	 * @return Boolean
	 */
	Public Function verifyResetCode($Code)
	{
		$Row = $this->_db->fetchRow('SELECT * FROM bevomedia_user_reset_password WHERE ID = ' . $this->id . ' AND Hash = "' . $Code .'"');
		if(!$Row || !sizeOf($Row))
			return false;
		else
			return true;
			
	}
	
	/**
	 * Update the User table and set Password to specified $NewPassword.
	 *
	 * @param String $NewPassword
	 */
	Public Function changePassword($NewPassword)
	{
		$this->_db->exec("UPDATE bevomedia_user  SET Password = md5('$NewPassword') WHERE ID = $this->id");
	}
	
	/**
	 * Send an email to the specified $To recipient with a subject of $Subject containing $Body.
	 *
	 * @param String $To
	 * @param String $Subject
	 * @param String $Body
	 */
	Private Function sendEmail($To, $Subject, $Body)
	{
		$MailComponentObject = new MailComponent();
		$MailComponentObject->setFrom('no-reply@'.$_SERVER['HTTP_HOST']);
		$MailComponentObject->setSubject($Subject);
		$MailComponentObject->setHTML($Body);
		$MailComponentObject->send(array($To));
	}
	
	/**
	 * Removes all password reset codes for the User matching this $ID.
	 */
	Public Function clearResetCode()
	{
		$this->_db->delete('bevomedia_user_reset_password', 'id = ' . $this->id);
	}
	
	/**
	 * Insert a new row into the UserResetPassword table for this $ID.
	 *
	 * @param String $Code
	 */
	Public Function insertResetCode($Code)
	{
		$Data = array('ID'=>$this->id, 'Hash'=>$Code);
		$this->_db->insert('bevomedia_user_reset_password', $Data);
	}
	
	/**
	 * Removes all explevel entries for the User matching this $ID.
	 */
	Public Function clearPerformanceConnectorExpLevel()
	{
		$this->_db->delete('bevomedia_user_performanceconnector_explevel', 'user__Id = ' . $this->id);
	}
	/**
	 * Insert a new row into the PerformanceConnectorExpLevel table for this $ID.
	 *
	 * @param String $nicheId
	 */
	Public Function insertPerformanceConnectorExpLevel($expId)
	{
		$Data = array('user__id'=>$this->id, 'explevel__id'=>$expId);
		$this->_db->insert('bevomedia_user_performanceconnector_explevel', $Data);
	}
	Public Function getPerformanceConnectorExpLevels()
	{
		$query = $this->_db->select()->from('bevomedia_user_performanceconnector_explevel')->where("user__id = ?", array($this->id));
		$rows = $this->_db->fetchAll($query);
		return $rows;
	}
	
	/**
	 * Removes all promomethod entries for the User matching this $ID.
	 */
	Public Function clearPerformanceConnectorPromoMethod()
	{
		$this->_db->delete('bevomedia_user_performanceconnector_promomethod', 'user__Id = ' . $this->id);
	}
	/**
	 * Insert a new row into the PerformanceConnectorPromoMethod table for this $ID.
	 *
	 * @param String $nicheId
	 */
	Public Function insertPerformanceConnectorPromoMethod($promoId)
	{
		$Data = array('user__id'=>$this->id, 'promomethod__id'=>$promoId);
		$this->_db->insert('bevomedia_user_performanceconnector_promomethod', $Data);
	}
	Public Function getPerformanceConnectorPromoMethods()
	{
		$query = $this->_db->select()->from('bevomedia_user_performanceconnector_promomethod')->where("user__id = ?", array($this->id));
		$rows = $this->_db->fetchAll($query);
		return $rows;
	}
	
	/**
	 * Removes all niche entries for the User matching this $ID.
	 */
	Public Function clearPerformanceConnectorNiches()
	{
		$this->_db->delete('bevomedia_user_performanceconnector_niche', 'user__Id = ' . $this->id);
	}
	/**
	 * Insert a new row into the PerformanceConnectorNiche table for this $ID.
	 *
	 * @param String $nicheId
	 */
	Public Function insertPerformanceConnectorNiche($nicheId)
	{
		$Data = array('user__id'=>$this->id, 'niche__id'=>$nicheId);
		$this->_db->insert('bevomedia_user_performanceconnector_niche', $Data);
	}
	
	/**
	 * Removes all network entries for the User matching this $ID.
	 */
	Public Function clearPerformanceConnectorEntries()
	{
		$this->_db->delete('bevomedia_user_performanceconnector', 'user__Id = ' . $this->id);
	}
	/**
	 * Insert a new row into the PerformanceConnectorNiche table for this $ID.
	 *
	 * @param String $networkId
	 */
	Public Function insertPerformanceConnectorEntry($networkId)
	{
		$Data = array('user__id'=>$this->id, 'network__id'=>$networkId);
		$this->_db->insert('bevomedia_user_performanceconnector', $Data);
	}
	
	/**
	 * Insert a new row into the PerformanceConnectorContact table for this $ID.
	 *
	 * @param String $networkId
	 */
	Public Function insertPerformanceConnectorContactEntry($IMService, $IM, $Phone)
	{
		$Data = array('user__id'=>$this->id, 'im_service'=>$IMService, 'im' => $IM, 'phone' => $Phone);
		$this->_db->insert('bevomedia_user_performanceconnector_contact', $Data);
	}
	
	/**
	 * Removes all network entries for the User matching this $ID.
	 */
	Public Function clearPerformanceConnectorContactEntries()
	{
		$this->_db->delete('bevomedia_user_performanceconnector_contact', 'user__Id = ' . $this->id);
	}
	
	Public Function getPerformanceConnectorContact()
	{
		$query = $this->_db->select()->from('bevomedia_user_performanceconnector_contact')->where("user__id = ?", array($this->id));
		$row = $this->_db->fetchRow($query);
		return $row;
	}
	
	Public Function getPerformanceConnectorNiches()
	{
		$query = $this->_db->select()->from('bevomedia_user_performanceconnector_niche')->where("user__id = ?", array($this->id));
		$rows = $this->_db->fetchAll($query);
		return $rows;
	}
	
	/**
	 * Return an alphanumeric string with the specified $Length.
	 *
	 * @param Integer $Length
	 * @return String
	 */
	Public Function generateResetCode($Length = 32)
	{
		$Alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
		$Alpha = str_split($Alpha);
		$Output = '';
		while(strlen($Output) < $Length)
		{
			$Output .= $Alpha[rand(0, sizeOf($Alpha)-1)];
		}
		return $Output;
	}
	
	/**
	 * Insert a new row into the User_Tickets table with a Subject of $Subject and a body of $Problem for the User matching this $ID.
	 *
	 * @param String $Subject
	 * @param String $Problem
	 */
	Public Function submitTicket($Subject, $Problem)
	{
		
		$User = new User();
		$User->getInfo($this->id);
						   
	    $Problem .= "\r\n
		
					User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n
					IP Address: {$_SERVER['REMOTE_ADDR']}\r\n
					Time: ".date('m/d/Y G:i:s T')."\r\n
		
					";
	    
		$Message = "
		
		Name: {$User->firstName} {$User->lastName}<br />
		User ID: {$User->id}<br />
		E-mail: {$User->Email}<br />
		User-Agent: {$_SERVER['HTTP_USER_AGENT']}<br />
		IP Address: {$_SERVER['REMOTE_ADDR']}<br />
		Time: ".date('m/d/Y G:i:s T')."<br />
		
		<br />Bug Description:<br/><br/>
		
		$Problem
		
				   ";

					
		$Problem = nl2br($Problem);
		
		$Insert = array('subject'=>$Subject, 'problem'=>$Problem, 'user__id'=>$this->id);
		$this->_db->insert('bevomedia_user_tickets', $Insert);
		
		$MailComponentObject = new MailComponent();
		$MailComponentObject->setFrom('no-reply@bevomedia.com');
		
		$MailComponentObject->setSubject('Bug From '.$User->firstName.' '.$User->lastName);
		$MailComponentObject->setHTML($Message);
		$MailComponentObject->send(array('ryan@bevomedia.com'));
	}
	
	/**
	 * Returns true if the specified $Email and $Password match a row within the User table.
	 * Returns false if the specified $Email and $Password do not match any rows within the User table.
	 * Returns -1 if the specified $Email and $Password match a row and that User is not Enabled.
	 *
	 * @param String $Email
	 * @param String $Password
	 * @return Mixed
	 */
	Public Function login($Email, $Password)
	{
		if($this->emailExists($Email))
		{
			$Password = ($this->_db->quote($Password));
			$Result = $this->_db->fetchRow("SELECT * FROM bevomedia_user WHERE email = ? AND password = $Password and enabled=1 and deleted=0", $Email);
			if(sizeOf($Result) == 0 || !$Result)
				return false;
			else
			{
				if($Result->enabled)
				{
					$Sql = "UPDATE bevomedia_user SET lastLogin = NOW() WHERE id = ".$Result->id;
					$this->_db->exec($Sql);
					return true;
				}
				else
					return -1;
			}
		}
		return false;
	}
	Public Function getUserIdByAPIKey($apiKey)
	{
		$Result = $this->_db->fetchRow("SELECT id FROM bevomedia_user WHERE apiKey = ?", $apiKey);
		if(sizeOf($Result) == 0 || !$Result)
			return false;
		else
			return $Result->id;
		return false;
	}
	/**
	 * Sets the specified $Property of this object with the specified $Value provided.
	 * 
	 * @param String $Property
	 * @param Mixed $Value
	 * @return Accounts_Abstract
	 */
	Public Function set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
	
	Public Function CheckForPPVStats($UserID)
	{
		$Sql = "SELECT
				    bevomedia_tracker_clicks.clickDate
				FROM
				    bevomedia_ppc_campaigns
				    INNER JOIN bevomedia_ppc_adgroups ON (bevomedia_ppc_adgroups.campaignId  = bevomedia_ppc_campaigns.id)
				    INNER JOIN bevomedia_ppc_advariations ON (bevomedia_ppc_advariations.adGroupId  = bevomedia_ppc_adgroups.id)
				    INNER JOIN bevomedia_tracker_clicks ON ( (bevomedia_tracker_clicks.creativeId = bevomedia_ppc_advariations.apiAdId) AND (bevomedia_tracker_clicks.user__id = bevomedia_ppc_campaigns.user__id) ) 
				WHERE
				    (bevomedia_ppc_campaigns.user__id = ".intval($UserID).") AND
				    (bevomedia_ppc_campaigns.providerType >= 5)  
			    LIMIT 1
				";
//		
//		
//		$Sql = "SELECT
//				    bevomedia_tracker_clicks.clickDate
//				FROM
//				    bevomedia_ppc_campaigns,
//				    bevomedia_ppc_adgroups,
//				    bevomedia_ppc_advariations,
//				    bevomedia_tracker_clicks
//				WHERE
//				    (bevomedia_ppc_adgroups.campaignId  = bevomedia_ppc_campaigns.id) AND
//				    (bevomedia_ppc_advariations.adGroupId  = bevomedia_ppc_adgroups.id) AND
//				    (bevomedia_tracker_clicks.creativeId = bevomedia_ppc_advariations.apiAdId) AND
//				    (bevomedia_ppc_campaigns.user__id = ".intval($UserID).") AND
//				    (bevomedia_ppc_campaigns.providerType >= 5) AND
//				    (bevomedia_tracker_clicks.user__id = ".intval($UserID).") 
//			    LIMIT 1
//				";
//		$Sql = "SELECT
//					tc.id,
//					tc.clickDate
//				FROM
//					bevomedia_tracker_clicks tc
//				INNER JOIN
//					bevomedia_tracker_clicks_optional tco
//					ON tco.clickId = tc.id
//				INNER JOIN
//					bevomedia_ppc_advariations pav
//					ON pav.apiAdId = tc.creativeId
//				INNER JOIN
//					bevomedia_ppc_advariations_stats pavs
//					ON pavs.advariationsId = pav.id
//				INNER JOIN
//					bevomedia_ppc_adgroups pa
//					ON pa.id = pav.adGroupId
//				INNER JOIN
//					bevomedia_ppc_campaigns pc
//					ON (pc.user__id = tc.user__id AND pc.id = pa.campaignId)
//				WHERE
//					(tc.user__id = ".intval($UserID).") AND
//					(pc.ProviderType >= 5)
//				ORDER BY
//					tc.id DESC
//				LIMIT 1		
//				";
		$Result = $this->_db->fetchRow($Sql);
		return $Result;
	}
	
	Public Function CheckForPPCStats($UserID)
	{
		$Sql = "SELECT
				    bevomedia_tracker_clicks.clickDate
				FROM
				    bevomedia_ppc_campaigns
				    INNER JOIN bevomedia_ppc_adgroups ON (bevomedia_ppc_adgroups.campaignId  = bevomedia_ppc_campaigns.id)
				    INNER JOIN bevomedia_ppc_advariations ON (bevomedia_ppc_advariations.adGroupId  = bevomedia_ppc_adgroups.id)
				    INNER JOIN bevomedia_tracker_clicks ON ( (bevomedia_tracker_clicks.creativeId = bevomedia_ppc_advariations.apiAdId) AND (bevomedia_tracker_clicks.user__id = bevomedia_ppc_campaigns.user__id) ) 
				WHERE
				    (bevomedia_ppc_campaigns.user__id = ".intval($UserID).") AND
				    (bevomedia_ppc_campaigns.providerType in (1,2,3))  
			    LIMIT 1
				";
		
//		$Sql = "SELECT
//					MAX(tc.clickDate)
//				FROM
//					bevomedia_tracker_clicks tc
//				INNER JOIN
//					bevomedia_tracker_clicks_optional tco
//					ON tco.clickId = tc.id
//				INNER JOIN
//					bevomedia_ppc_advariations pav
//					ON pav.apiAdId = tc.creativeId
//				INNER JOIN
//					bevomedia_ppc_advariations_stats pavs
//					ON pavs.advariationsId = pav.id
//				INNER JOIN
//					bevomedia_ppc_adgroups pa
//					ON pa.id = pav.adGroupId
//				INNER JOIN
//					bevomedia_ppc_campaigns pc
//					ON (pc.user__id = tc.user__id AND pc.id = pa.campaignId)
//				WHERE
//					(tc.user__id = ".intval($UserID).") AND
//					(pc.ProviderType in (1,2,3))
//				group BY
//					tc.user__id
//				LIMIT 1		
//				";
		$Result = $this->_db->fetchRow($Sql);
		return $Result;
	}
	
	Public Function CheckForMediaBuyStats($UserID)
	{
		$Sql = "SELECT
			    bevomedia_tracker_clicks.clickDate
			FROM
			    bevomedia_ppc_campaigns
			    INNER JOIN bevomedia_ppc_adgroups ON (bevomedia_ppc_adgroups.campaignId  = bevomedia_ppc_campaigns.id)
			    INNER JOIN bevomedia_ppc_advariations ON (bevomedia_ppc_advariations.adGroupId  = bevomedia_ppc_adgroups.id)
			    INNER JOIN bevomedia_tracker_clicks ON ( (bevomedia_tracker_clicks.creativeId = bevomedia_ppc_advariations.apiAdId) AND (bevomedia_tracker_clicks.user__id = bevomedia_ppc_campaigns.user__id) ) 
			WHERE
			    (bevomedia_ppc_campaigns.user__id = ".intval($UserID).") AND
			    (bevomedia_ppc_campaigns.providerType = 4)  
		    LIMIT 1
			";
		
//		$Sql = "SELECT
//					MAX(tc.clickDate)
//				FROM
//					bevomedia_tracker_clicks tc
//				INNER JOIN
//					bevomedia_tracker_clicks_optional tco
//					ON tco.clickId = tc.id
//				INNER JOIN
//					bevomedia_ppc_advariations pav
//					ON pav.apiAdId = tc.creativeId
//				INNER JOIN
//					bevomedia_ppc_advariations_stats pavs
//					ON pavs.advariationsId = pav.id
//				INNER JOIN
//					bevomedia_ppc_adgroups pa
//					ON pa.id = pav.adGroupId
//				INNER JOIN
//					bevomedia_ppc_campaigns pc
//					ON (pc.user__id = tc.user__id AND pc.id = pa.campaignId)
//				WHERE
//					(pc.user__id = ".intval($UserID).") AND
//					(tc.user__id = ".intval($UserID).") AND
//					(pc.providerType = 4)
//				GROUP BY
//					tc.user__id
//				LIMIT 1		
//				";
		$Result = $this->_db->fetchRow($Sql);
		return $Result;
	}
	
	Public Function GetProduct($ProductName)
	{
		$Product = $this->_db->fetchRow('SELECT * FROM bevomedia_products WHERE ProductName = ?', $ProductName);
			
		return $Product;
	}

	Public Function GetProducts()
	{
		$Sql = "SELECT 
					* 
				FROM 
					bevomedia_products
				";
		
		$Products = $this->_db->fetchAll($Sql);
			
		return $Products;
	}
	
	Public Function IsSubscribed($ProductName)
	{
		$Sql = "SELECT
					MAX(bevomedia_user_payments.Date) AS `Date`
				FROM
					bevomedia_products,
					bevomedia_user_payments
				WHERE
					(bevomedia_user_payments.ProductID = bevomedia_products.ID) AND 
					(bevomedia_products.ProductName = ?) AND
					(bevomedia_user_payments.UserID = ?) AND
					(DATE_ADD(bevomedia_user_payments.Date, interval bevomedia_products.TermLength day) > NOW()) AND
					(bevomedia_user_payments.Deleted = 0)
				GROUP BY
					bevomedia_user_payments.UserID
				";
		$Results = $this->_db->fetchAll($Sql, array($ProductName, $this->id));
		return (count($Results)>0);
	}
	
	Public Function Subscribe($ProductName)
	{
		$Product = $this->GetProduct($ProductName);
		
		$Data = array('UserID' => $this->id, 'ProductID' => $Product->ID, 'Price' => 0, 'Paid' => 1);
	 	$this->_db->insert('bevomedia_user_payments', $Data, 'id = ' . $this->id);
	}
	
	Public Function Unsubscribe($ProductName)
	{
		$Product = $this->GetProduct($ProductName);
		
		$Sql = "SELECT
					bevomedia_user_payments.ID,
					MAX(bevomedia_user_payments.Date)
				FROM 
					bevomedia_user_payments
				WHERE
					(bevomedia_user_payments.ProductID = ?) AND
					(bevomedia_user_payments.Deleted = 0) AND
					(bevomedia_user_payments.UserID = ?)
				GROUP BY
					bevomedia_user_payments.ProductID
				";
		$Result = $this->_db->fetchRow($Sql, array($Product->ID, $this->id));
		
		if (is_object($Result)) {
			$Array = array('Deleted' => 1);
			$this->_db->update('bevomedia_user_payments', $Array, ' ID = '.$Result->ID);
		}
		
	}
	
	Public Function AddUserServerCharge($TransactionID)
	{
		$Product = $this->GetProduct(User::PRODUCT_SERVER_CHARGE);
		
		$Data = array(
						'UserID' => $this->id, 
						'ProductID' => $Product->ID, 
						'Price' => $Product->Price, 
						'Date' => date('Y-m-d H:i:s'), 
						'Paid' => 1, 
						'PaidDate' => date('Y-m-d H:i:s'),
						'TransactionID' => $TransactionID
				);
	 	$this->_db->insert('bevomedia_user_payments', $Data, 'id = ' . $this->id);
	}
	
	Public Function AddUserSelfHostedCharge()
	{
		$Product = $this->GetProduct(User::PRODUCT_SELF_HOSTED_YEARLY_CHARGE);
		
		$Data = array('UserID' => $this->id, 'ProductID' => $Product->ID, 'Price' => $Product->Price);
	 	$this->_db->insert('bevomedia_user_payments', $Data, 'id = ' . $this->id);
	}
	
	Public Function AddUserAPICallsCharge()
	{
		$Product = $this->GetProduct(User::PRODUCT_API_CALLS_CHARGE);
		
		$this->addApiCalls((int)$Product->Quantity, User::PRODUCT_API_CALLS_CHARGE);
		
		$Data = array('UserID' => $this->id, 'ProductID' => $Product->ID, 'Price' => $Product->Price);
	 	$this->_db->insert('bevomedia_user_payments', $Data, 'id = ' . $this->id);
	}
	
	Public Function AddUserPPVCharge()
	{
//		$this->subtractApiCalls(50, 'PPV Charge');
	}
	
//	Public Function IsSignedUpForResearch()
//	{
//		$Sql = "SELECT
//					MAX(bevomedia_user_payments.Date) AS `Date`
//				FROM
//					bevomedia_products,
//					bevomedia_user_payments
//				WHERE
//					(bevomedia_user_payments.ProductID = bevomedia_products.ID) AND 
//					(bevomedia_products.ProductName = ?) AND
//					(bevomedia_user_payments.UserID = ?) AND
//					(bevomedia_user_payments.Deleted = 0)
//				GROUP BY
//					bevomedia_user_payments.UserID
//				";
//		$Results = $this->_db->fetchAll($Sql, array(User::PRODUCT_RESEARCH_YEARLY_CHARGE, $this->id));
//		return (count($Results)>0);
//	}
	
	Public Function IsSignedUpForSelfHosted()
	{
		$Sql = "SELECT
					MAX(bevomedia_user_payments.Date) AS `Date`
				FROM
					bevomedia_products,
					bevomedia_user_payments
				WHERE
					(bevomedia_user_payments.ProductID = bevomedia_products.ID) AND 
					(bevomedia_products.ProductName = ?) AND
					(bevomedia_user_payments.UserID = ?) AND
					(bevomedia_user_payments.Deleted = 0)
				GROUP BY
					bevomedia_user_payments.UserID
				";
		$Results = $this->_db->fetchAll($Sql, array(User::PRODUCT_SELF_HOSTED_YEARLY_CHARGE, $this->id));
		return (count($Results)>0);
	}
	
	Public Function IsSignedUpForCampaignEditor()
	{
		$Sql = "SELECT
					MAX(bevomedia_user_payments.Date) AS `Date`
				FROM
					bevomedia_products,
					bevomedia_user_payments
				WHERE
					(bevomedia_user_payments.ProductID = bevomedia_products.ID) AND 
					(bevomedia_products.ProductName = ?) AND
					(bevomedia_user_payments.UserID = ?) AND
					(bevomedia_user_payments.Deleted = 0) 
				GROUP BY
					bevomedia_user_payments.UserID
				";
		$Results = $this->_db->fetchAll($Sql, array(User::PRODUCT_PPC_YEARLY_CHARGE, $this->id));
		return (count($Results)>0);
	}
	
	Public Function IsSignedUpForGoogleAdwords()
	{
		$Sql = "SELECT
					MAX(bevomedia_user_payments.Date) AS `Date`
				FROM
					bevomedia_products,
					bevomedia_user_payments
				WHERE
					(bevomedia_user_payments.ProductID = bevomedia_products.ID) AND 
					(bevomedia_products.ProductName = ?) AND
					(bevomedia_user_payments.UserID = ?) AND
					(bevomedia_user_payments.Deleted = 0) 
				GROUP BY
					bevomedia_user_payments.UserID
				";
		$Results = $this->_db->fetchAll($Sql, array(User::PRODUCT_GOOGLE_ADWORDS, $this->id));
		return (count($Results)>0);
	}
	
	Public Function GetTotalApiCallsMTD()
	{
		$DateStart = date('Y').'-'.date('m').'-1';
		$DateStart = date('Y-m-d', strtotime($DateStart));
		
		$Sql = "SELECT
					SUM(bevomedia_api_calls.amount) as `Total`
				FROM
					 bevomedia_api_calls
				 WHERE
				 	(bevomedia_api_calls.amount < 0) AND
				 	(bevomedia_api_calls.user__id = ?) AND
				 	(bevomedia_api_calls.at >= ?)
				";
		
		$Row = $this->_db->fetchRow($Sql, array($this->id, $DateStart));
		
		if (!isset($Row->Total)) 
		{
			return 0;
		}
		
		return abs($Row->Total);
	}
	
	Public Function GetTotalIncomeGenerated($UserID = null)
	{
		$Sql = "SELECT
					SUM(bevomedia_user_payments.Price) AS `Total`
				FROM
					bevomedia_user_payments
				WHERE
					(bevomedia_user_payments.UserID = ?) AND
					(bevomedia_user_payments.TransactionID > 0) 		
				";
		$Result = $this->_db->fetchCol($Sql, ($UserID==null)?$this->id:$UserID);
		return $Result[0];
	}
	 
	Public Function FindUserIdByReferralCode($Code)
	{
		$Sql = "SELECT 
					id 
				FROM 
					bevomedia_user 
				WHERE 
					MD5(id) = ?
				";
		$Row = $this->_db->fetchRow($Sql, $Code);
		if (isset($Row->id))
		{
			return $Row->id;
		}
		
		return false;
	}
	
	Public Function ListReferrals($UserID = null)
	{
		if ($UserID==null) {
			$Rate = floatval($this->referralRate);
			$PPVSpyReferralRate = floatval($this->ppvSpyReferralRate);
		} else {
			$User = new User($UserID);
			$Rate = floatval($User->referralRate);
			$PPVSpyReferralRate = floatval($User->ppvSpyReferralRate);
		}
		
		$PPVSpyAdd = "";
		if ($PPVSpyReferralRate>0) {
			$Rate = $PPVSpyReferralRate;
			$PPVSpyAdd = " ((bevomedia_user_payments.ProductID = 12) OR (bevomedia_user_payments.ProductID = 13)) AND ";
		}
		
		$Sql = "SELECT
					bevomedia_user.id,
					bevomedia_user_info.firstName,
					bevomedia_user_info.lastName,
					bevomedia_referrals.Date,
					SUM(bevomedia_user_payments.Price)*({$Rate}/100.0) AS `Total`,
					SUM(bevomedia_user_payments.Price) AS `TotalRevenue`
				FROM
					bevomedia_referrals,
					bevomedia_user,
					bevomedia_user_info,
					bevomedia_user_payments
				WHERE
					{$PPVSpyAdd}
					(bevomedia_user.id = bevomedia_referrals.UserID) AND
					(bevomedia_user_info.id = bevomedia_user.id) AND
					(bevomedia_user_payments.UserID = bevomedia_referrals.UserID) AND
					(bevomedia_user_payments.TransactionID > 0) AND
					(bevomedia_user_payments.PaidDate >= DATE_SUB(now(), interval 1 year)) AND
					(bevomedia_referrals.ReferrerID = ?)  
				GROUP BY
					bevomedia_referrals.UserID
				";
		return $this->_db->fetchAll($Sql, ($UserID==null)?$this->id:$UserID); 
	}
	
	Public Function ReferredBy()
	{
		$Sql = "SELECT
					ReferrerID
				FROM
					bevomedia_referrals
				WHERE
					bevomedia_referrals.UserID = ?		
				";
		$Row = $this->_db->fetchRow($Sql, $this->id);
		if (isset($Row->ReferrerID))
		{
			$user = new User($Row->ReferrerID);
			return $user;
		}
		
		return null;
	}
	
	Public Function GetPPVSpyOneTimePrice()
	{
		$Product = $this->GetProduct(User::PRODUCT_PPVSPY_YEARLY);
		
		$Sql = "SELECT 
					bevomedia_referrals.ID,
					bevomedia_referrals.ReferrerID
				FROM 
					bevomedia_referrals_ppvspy,
					bevomedia_referrals 
				WHERE 
					(bevomedia_referrals.ID = bevomedia_referrals_ppvspy.ReferID) AND 
					(bevomedia_referrals.UserID = ?)
				";
		$Row = $this->_db->fetchRow($Sql, $this->id);
		
		if (isset($Row->ID)) {
			
			$ParentUser = new User($Row->ReferrerID);
			if ($ParentUser->IsSubscribed(User::PRODUCT_PPVSPY_REFERRAL_PRICE)) {
				return 497;
			}
			
		}
		
		return $Product->Price;
	}
	
	Public Function getAllPPCCampaigns()
	{
		$Sql = "SELECT
					id,
					name
				FROM
					bevomedia_ppc_campaigns
				WHERE
					(bevomedia_ppc_campaigns.user__id = ?)
				ORDER BY
					name
				";
		return $this->_db->fetchAll($Sql, $this->id);
	}
	
}
?>
