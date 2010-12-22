<?php

/**
 * User_Notes Class
 */

/**
 * User_Notes Class
 * 
 * User_Notes Class
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
Class User_Notes {
	
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $ID
	 */
	Public $ID;
		
	/**
	 * @var Integer $User_ID
	 */
	Public $User_ID;
		
	/**
	 * @var Integer $Admin_ID
	 */
	Public $Admin_ID;

	/**
	 * @var String $Note
	 */
	Public $Note;
	
	/**
	 * @var String $Created
	 */
	Public $Created;

	/**
	 * @var Integer $Deleted
	 */
	Public $Deleted;
	
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
			$this->ID = $ID;
			$this->GetInfo();
		}
	}
	
	/**
	 * Populates the object with information from the database.
	 * 
	 * @param Integer $ID
	 * @return User_Notes
	 */
	Public Function GetInfo($ID = false)
	{
		if(!isset($this->ID) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->ID;
		
		$result = $this->_db->fetchRow('SELECT * FROM bevomedia_user_notes WHERE id = ?', $ID);
		foreach($result as $key=>$value)
		{
			$this->Set($key, $value);
		}
		
		return $this;
	}
	
	/**
	 * Returns an array of User_Notes objects for all rows within the User_Notes table which have not been marked 
	 * as Deleted and belong to the User matching the specified $User_ID.
	 *
	 * @param Integer $User_ID
	 * @return Array
	 */
	Public Function GetAllNotes($User_ID)
	{
		$Output = array();
		$Notes = $this->_db->fetchAll('SELECT * FROM bevomedia_user_notes WHERE deleted = 0 AND user__id = ?', $User_ID);
		foreach($Notes as $Note)
			$Output[] = new User_Notes($Note->id);
			
		return $Output;
	}
	
	/**
	 * Return the Username of the Admin matching this $Admin_ID.
	 *
	 * @return String
	 */
	Public Function GetAdminName()
	{
		$Admin = new Admin($this->admin__id);
		return $Admin->username;
	}

	/**
	 * Update the User_Notes table setting Deleted = '1' where the row ID matches $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function Delete($ID = false)
	{
		if($ID === false)
			$ID = $this->ID;
			
		$this->_db->exec("UPDATE bevomedia_user_notes SET deleted = 1 WHERE id = $ID");
	}
	
	/**
	 * Inserts a new row into the table using the values provided by $Data and returns the table insert ID.
	 *
	 * @param Array $Data
	 * @return Integer
	 */
	Public Function Insert($Data)
	{
		$Insert = $Data;
		
		foreach($Insert as $k=>$v)
			$Insert[$k] = $this->_db->quote($v);
		
		$sql = "INSERT INTO bevomedia_user_notes (user__id, admin__id, note) VALUES ($Insert[User_ID], $Insert[Admin_ID], $Insert[Note])";

		$this->_db->exec($sql);
		$this->ID = $this->_db->lastInsertId();
		
		$this->GetInfo();
		
		return $this->ID;
	}
	
	/**
	 * Returns an array of User objects for all rows within the User table.
	 * 
	 * @return Array
	 */
	Public Function GetAllUsers()
	{
		$Output = array();
		$Users = $this->_db->fetchAll('SELECT * FROM User');
		foreach($Users as $User)
			$Output[] = new User($User->ID);
			
		return $Output;
	}
	
	/**
	 * Sets the specified $Property of this object with the specified $Value provided.
	 * 
	 * @param String $Property
	 * @param Mixed $Value
	 * @return Accounts_Abstract
	 */
	Public Function Set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
}
?>