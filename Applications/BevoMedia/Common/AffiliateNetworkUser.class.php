<?php
/**
 * Affiliate Network User Class
 */

/**
 * Affiliate Network User Class
 * 
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */

Class AffiliateNetworkUser {
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var String $ID
	 */
	Public $ID;
	
	
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
	 * @return AffiliateNetworkUser
	 */
	Public Function GetInfo($ID = false)
	{
		if(!isset($this->id) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->id;
		
		$Result = $this->_db->fetchRow('SELECT * FROM bevomedia_user_aff_network WHERE id = ?', $ID);
		foreach($Result as $Key=>$Value)
		{
			$this->set($Key, $Value);
		}
		
		return $this;
	}

	
	/**
	 * Returns an array of AffiliateNetworkUser objects for all entries within the bevomedia_user_aff_network table that belong to the specified $UserID.
	 * @param Integer $UserID
	 * 
	 * @return Array
	 */
	Public Function GetAllAffiliateNetworksForUser($UserID)
	{
		$Output = array();
		$AffNetworks = $this->_db->fetchAll('SELECT bevomedia_user_aff_network.id AS id FROM bevomedia_user_aff_network LEFT JOIN bevomedia_aff_network ON bevomedia_aff_network.id = bevomedia_user_aff_network.network__id WHERE user__id = ? AND isValid = \'Y\' ORDER BY title', $UserID);
		foreach($AffNetworks as $AffNetwork)
		{
			$Output[] = new AffiliateNetworkUser($AffNetwork->id);
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