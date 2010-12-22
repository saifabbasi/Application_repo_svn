<?php
/**
 * Class used in Admin area for listing PPC Networks and retrieving statistical information related to those networks.
 */

/**
 * Class used in Admin area for listing PPC Networks and retrieving statistical information related to those networks.
 * 
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */

Class Network {
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Array $stats
	 */
	Public $stats;

	/**
	 * @var Array $accounts
	 */
	Public $accounts;
	
	/**
	 * @var String $name
	 */
	Public $name;
	
	/**
	 * @var Integer $type
	 */
	Public $type;
	
	/**
	 * @var Mixed $helperId
	 */
	Public $helperId = false;
	
	/**
	 * @var String $helperType
	 */
	Private $helperType = false;
	
	/**
	 * @var Array $helper
	 */
	Private $helper = array(
			0=>	array(), 
			1=>	array('TYPE'=>'PPC','LABEL'=>'Google Adwords', 'FUNCTIONASSIST'=>'Adwords'),
			2=>	array('TYPE'=>'PPC','LABEL'=>'Yahoo Search Marketing', 'FUNCTIONASSIST'=>'Yahoo'),
			3=>	array('TYPE'=>'PPC','LABEL'=>'MSN Ad Center', 'FUNCTIONASSIST'=>'MSN')
			);
	
			
	/**
	 * Constructor
	 *
	 * @param Integer $ID
	 */
	Public Function __construct($ID = false)
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		if($ID !== false)
			$this->Init($ID);
	}
	
	/**
	 * Assigns parameters to this object from this $Helper array matching the specified $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function init($ID)
	{
		foreach($this->helper as $Key=>$Value)
		{
			if($Key == $ID)
			{
				$this->name = $Value['LABEL'];
				$this->type = $Value['TYPE'];
				$this->functionAssist = $Value['FUNCTIONASSIST'];
				$this->helperId = $ID;
				break;		
			}
		}
	}
	
	/**
	 * Retrieves all stats for all PPC Networks and populates this $Accounts and this $Stats.
	 *
	 */
	Public Function loadStats()
	{
		$this->Accounts = $this->{'GetAll'.$this->functionAssist.'Accounts'}();
		$this->stats = new Network_Stats();
		$this->stats->{'Load'.$this->type.'Stats'}($this->helperId);
	}
	
	/**
	 * Retrieves all rows for Accounts_Adwords.
	 *
	 * @return Array
	 */
	Public Function getAllAdwordsAccounts()
	{
		$Adwords = new Accounts_Adwords();
		return $Adwords->GetAll();
	}
	
	/**
	 * Retrieves all rows for Accounts_Yahoo.
	 *
	 * @return Array
	 */
	Public Function getAllYahooAccounts()
	{
		$Yahoo = new Accounts_Yahoo();
		return $Yahoo->GetAll();
	}
	
	/**
	 * Retrieves all rows for Accounts_MSNAdCenter.
	 *
	 * @return Array
	 */
	Public Function getAllMSNAccounts()
	{
		$MSN = new Accounts_MSNAdCenter();
		return $MSN->GetAll();
	}
		
	/**
	 * Returns an arry of Network objects with stats for Google Adwords, Yahoo, and MSN Ad Center.
	 *
	 * @return Array
	 */
	Public Function getAllNetworksWithStats()
	{
		$Adwords = new Network(1);
		$Adwords->LoadStats();
		$Yahoo = new Network(2);
		$Yahoo->LoadStats();
		$MSN = new Network(3);
		$MSN->LoadStats();
		return array($Adwords, $Yahoo, $MSN);
	}
	
	/**
	 * Sets the specified $Property of this object with the specified $Value provided.
	 * 
	 * @param String $Property
	 * @param Mixed $Value
	 * @return Network
	 */
	Public Function set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
}
?>