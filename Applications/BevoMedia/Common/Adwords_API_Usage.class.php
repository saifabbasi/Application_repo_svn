<?php
/**
 * Class which calculates Adwords API cost.
 */

/**
 * Class which calculates Adwords API cost.
 *
 * Class which calculates the dollar cost for using the Google Adwords API.  The minimum amount is returned if the user has not expended enough calls to reach that value.
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
Class Adwords_API_Usage {
	/**
	 * @var Zend_DB_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $userId
	 */
	Public $userId;

	/**
	 * @var String $User
	 */
	Public $User;

	/**
	 * @var Integer $totalUpdates
	 */
	Public $totalUpdates;

	/**
	 * @var Integer $totalCalls
	 */
	Public $totalCalls;

	/**
	 * @var Float $totalCost
	 */
	Public $totalCost;
	
	/**
	 * @var Float $totalCredit
	 */
	Public $totalCredit;

	/**
	 * @var Float $Balance
	 */
	Public $balance;
	
	
	Private $CCFee = 0.3;
	Private $CallsFee = 1;
	Private $CallsFeeAmount = 0.25;
	Private $CallsFeePer = 1000;
	Private $MinimumFee = 0.25;
	Private $PercentFee = 1.1;
	
	
	/**
	 * Constructor
	 *
	 * @param Boolean $Console
	 * @param Integer $userId
	 */
	Public Function __construct($Console = false, $userId = false)
	{
		$this->CallsFee = $this->CallsFeeAmount/$this->CallsFeePer;
		
		if($Console === false)
			$this->Init();
		
		if($userId != false)
			$this->RetrieveData($userId);
	}
	
	/**
	 * Returns the cost of processing $Amount of API calls.
	 *
	 * @param Integer $Amount
	 * @return Float
	 */
	Public Function CalcCost($Amount)
	{
		$Fee = $this->CallsFee * $Amount;
		$Fee = ($Fee < $this->MinimumFee)?($this->MinimumFee):($Fee);
		$Fee = ($Fee + $this->CCFee) * $this->PercentFee;
		$Fee = floor($Fee * 100)/100;
		return $Fee;
	}
	
	/**
	 * Sets this $Balance property after calculating the balance.
	 */
	Public Function CalcBalance()
	{
		// <!-- ifdef __SelfHosted__ -->
		
		if (Zend_Registry::get('Application/Mode') != 'SelfHosted')
		{
			$Credit = $this->CalcCredit();
			$Usage = $this->CalcUsage();

			$this->Balance = $Credit - $Usage;
		}
		
		// <!-- endif __SelfHosted__ -->
		
		if (Zend_Registry::get('Application/Mode') == 'SelfHosted')
		{
			$this->Balance = 99999;
			return 99999;
		}
	}

	/**
	 * Sets and returns this $TotalCredit after calculating the credit that the User with this $userId has.
	 *
	 * @return Float
	 */
	Public Function CalcCredit()
	{
		$Total = 0;
		$Rows = $this->_db->fetchAll("SELECT * FROM bevomedia_adwords_api_credit WHERE user__id = " . $this->userId);
		foreach($Rows as $Row)
			$Total += $Row->credit;

		$this->totalCredit = $Total;
		return $Total;
	}

	/**
	 * Sets and returns this $TotalCost after calculating the total cost that the User with this $userId has.
	 * Also sets this $TotalCalls and this $TotalUpdates.
	 *
	 * @return Float
	 */
	private function calcUsage()
	{
		$Total = $this->totalUpdates = $this->totalCalls = 0;
		$Rows = $this->_db->fetchAll("SELECT * FROM bevomedia_adwords_api_usage
				LEFT JOIN bevomedia_accounts_adwords ON bevomedia_accounts_adwords.id = bevomedia_adwords_api_usage.accountsAdwordsId
				WHERE bevomedia_accounts_adwords.user__id =" . $this->userId);
		
		foreach($Rows as $Row)
		{
			$Total += $this->CalcCost($Row->apiCalls);
			$this->totalCalls += $Row->apiCalls;
			$this->totalUpdates++;
		}

		$this->totalCost = $Total;
		return $Total;
	}
	
	/**
	 * Calculates this $Balance for the User matching $userId.
	 *
	 * @param Integer $userId
	 */
	Public Function RetrieveData($userId)
	{
		$this->User = new User($userId);
		$this->userId = $userId;
		$this->CalcBalance();
	}
	
	Private Function Init()
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		$this->_accounts_adwords = new Accounts_Adwords();
	}
	
	/**
	 * Returns array of rows for all Adwords API use that the User matching $userId has across all of their accounts.
	 *
	 * @param Integer $userId
	 * @return Array
	 */
	Public Function GetAllAPICallsForUser($userId)
	{
		$Sql = 'SELECT bevomedia_adwords_api_usage.created as created, apiCalls, username FROM bevomedia_adwords_api_usage LEFT JOIN bevomedia_accounts_adwords ON bevomedia_accounts_adwords.id = bevomedia_adwords_api_usage.accountsAdwordsId WHERE bevomedia_accounts_adwords.user__id = '. $userId;
		$Rows = $this->_db->fetchAll($Sql);
		return $Rows;
	}
	
	/**
	 * Returns array of rows for all Adwords API credit transactions for the User matching $userId.
	 *
	 * @param Integer $userId
	 * @return Array
	 */
	Public Function GetAllCreditForUser($userId)
	{
		$Rows = $this->_db->fetchAll('SELECT * FROM bevomedia_adwords_api_credit WHERE user__id = '. $userId);
		return $Rows;
	}
	
	/**
	 * Returns array of rows for all non deleted entries for Users that have had API usage recorded for the Adwords API.
	 *
	 * @return Array
	 */
	Public Function GetAllUsersWithAPICalls()
	{
		$Rows = $this->_db->fetchAll('SELECT bevomedia_accounts_adwords.user__id as userId FROM bevomedia_adwords_api_usage LEFT JOIN bevomedia_accounts_adwords ON bevomedia_accounts_adwords.id = bevomedia_adwords_api_usage.accountsAdwordsId WHERE bevomedia_adwords_api_usage.deleted = 0');
		return $Rows;
	}
	
	/**
	 * Returns array of rows for all non deleted entries for Users that have had API credit recorded for the Adwords API.
	 *
	 * @return Array
	 */
	Public Function GetAllUsersWithAPICredit()
	{
		$Rows = $this->_db->fetchAll('SELECT * FROM bevomedia_adwords_api_credit WHERE deleted = 0');
		return $Rows;
	}
	
	/**
	 * Console level function for retrieving the current balance for the specified User matching $userId.
	 *
	 * @param Integer $userId
	 * @return Float
	 */
	Public Function ConsoleGetBalance($userId)
	{
		if(!defined('ABSDB'))
			return 0;
			
		// <!-- ifdef __SelfHosted__ -->
		
		
		if (ABSMODE != 'SelfHosted')
		{
			$Sql = "SELECT IF(ISNULL(SUM(Credit)),0,SUM(Credit)) as Credit FROM `bevomedia_adwords_api_credit` WHERE user__id = $userId";
			$Row = mysql_fetch_assoc(mysql_query($Sql, ABSDB));
			$Credit = $Row['Credit'];
			$Sql = "SELECT IF(ISNULL((apiCalls)),0,(apiCalls)) as apiCalls FROM `bevomedia_adwords_api_usage` LEFT JOIN bevomedia_accounts_adwords ON bevomedia_accounts_adwords.id = bevomedia_adwords_api_usage.accountsAdwordsId WHERE bevomedia_accounts_adwords.user__id = $userId";
			$Query = mysql_query($Sql, ABSDB);
			$Cost = 0;
			while(($Row = mysql_fetch_assoc($Query)))
				$Cost += $this->CalcCost($Row['apiCalls']);
			return $Credit - $Cost;
		}
		
		// <!-- endif __SelfHosted__ -->
		
		if (ABSMODE == 'SelfHosted')
		{
			return 99999;
		}
	}
	
	/**
	 * Console level function for retrieving the last API Usage charge for the specified Adword's Account matching $Account_ID.
	 *
	 * @param Integer $AccountID
	 * @return Float
	 */
	Public Function ConsoleGetLastCharge($AccountID)
	{
		if(!defined('ABSDB'))
			return 0;
		$Sql = "SELECT apiCalls FROM `bevomedia_adwords_api_usage` Where accountsAdwordsId = $AccountID ORDER BY Created DESC";
		$Row = mysql_fetch_assoc(mysql_query($Sql, ABSDB));
		$Calls = $Row['apiCalls'];
		$Cost = $this->CalcCost($Calls);
		return $Cost;
	}
	
	/**
	 * Returns an array of bevomedia_adwords_api_usage objects for all users that have had Adwords API usage or credit recorded.
	 *
	 * @return Array
	 */
	Public Function GetAllUsersWithAPIUse()
	{
		$Temp = $Output = array();
		$UsersCalls = $this->GetAllUsersWithAPICalls();
		$UsersCredit = $this->GetAllUsersWithAPICredit();
		foreach($UsersCalls as $Call)
			$Temp[] = $Call->userId;
		foreach($UsersCredit as $Credit)
			$Temp[] = $Credit->userId;

		$Temp = array_unique($Temp);
		foreach($Temp as $T)
			$Output[] = new Adwords_API_Usage(false, $T);
		
		return $Output;
	}
	
	/**
	 * Add $Credit amount of Adwords API credit to the specified User matching $userId.
	 *
	 * @param Integer $userId
	 * @param Float $Credit
	 */
	Public Function AddCredit($userId, $Credit)
	{
		$Data = array('Credit'=>$Credit, 'user__id'=>$userId);
		$this->_db->insert('bevomedia_adwords_api_credit', $Data);
	}
}
?>