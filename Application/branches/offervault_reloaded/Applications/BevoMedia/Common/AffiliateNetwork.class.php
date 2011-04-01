<?php
/**
 * Affiliate Network Class
 */

/**
 * Affiliate Network Class
 *
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */

Class AffNetwork {
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var String $ID
	 */
	Public $ID;
	
	/**
	 * @var String $_aff_network_table_name
	 */
	Private $_aff_network_table_name = 'bevomedia_aff_network';
	
	/**
	 * @var String $_aff_network_users_table_name
	 */
	Private $_aff_network_users_table_name = 'bevomedia_user_aff_network';
	
	/**
	 * Constructor
	 *
	 * @param Integer $ID
	 */
	Public Function __construct($ID = false)
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		if($ID !== false)
		{
			$this->id = $ID;
			$this->getInfo();
		}
	}
	
	/**
	 * Populates the object with information from the database.
	 *
	 * @param Integer $ID
	 *
	 * @return AffiliateNetwork
	 */
	Public Function getInfo($ID = false)
	{
		if(!isset($this->id) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->id;
		
		$Result = $this->_db->fetchRow('SELECT * FROM ' . $this->_aff_network_table_name . ' WHERE id = ?', $ID);

		if(!$Result)
		{
			print $ID;
			return false;
		}
		foreach($Result as $Key=>$Value)
		{
			$this->set($Key, $Value);
		}
		
		return $this;
	}

	/**
	 * Returns an array of AffiliateNetwork objects for all networks within the bevomedia_aff_network table.
	 *
	 * @return Array
	 */
	Public Function getAllAffiliateNetworksByModel($Model)
	{
		$Output = array();
		$AffNetworks = $this->_db->fetchAll('SELECT * FROM ' . $this->_aff_network_table_name . ' WHERE model = ? ORDER BY title', $Model);
		
		$DTs = array('ClickBooth', 'Copeac', 'FluxAds', 'ROIRocket', 'XY7', 'CommissionEmpire', 'Rextopia', 'Wotogepa');
		foreach($AffNetworks as $AffNetwork)
		{
			//if(in_array($AffNetwork->TITLE, $DTs))
			//only display direct track networks
			$Output[] = new AffNetwork($AffNetwork->id);
		}
		return $Output;
	}

	/**
	 * Returns an array of User objects for all users that belong to this network.
	 *
	 * @see User
	 * @return Array
	 */
	Public Function getAllUsersForThisNetwork()
	{
		$Output = array();
		$AffNetUsers = $this->_db->fetchAll('SELECT * FROM ' . $this->_aff_network_users_table_name . ' WHERE network__id = ?', $this->id);
		foreach($AffNetUsers as $AffNetUser)
		{
			$TempUser = new User($AffNetUser->user__id);
			$TempUser->AffiliateNetworkUser = new AffiliateNetworkUser($AffNetUser->id);
			$Output[] = $TempUser;
		}
		
		return $Output;
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
	
}