<?php

/**
 * Creates and manages objects of the Admin table.
 */

/**
 * Creates and manages objects of the Admin table.
 * 
 * Creates and manages objects of the Admin table.
 * @todo Add examples.
 * @todo Add table structure.
 * 
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
Class Admin {
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $id
	 */
	Public $id;
	
	/**
	 * @var String $username
	 */
	Public $username;

	/**
	 * @var String $password
	 */
	Public $password;
	
	/**
	 * @var String $created
	 */
	Public $created;

	/**
	 * @var Integer $deleted
	 */
	Public $deleted;
	
	/**
	 * @var String $_admin_table_name
	 */
	Private $_admin_table_name = 'bevomedia_admin';
	
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
	 * Modifies the password to $NewPassword for this Admin.
	 *
	 * @param String $NewPassword
	 */
	Public Function changePassword($NewPassword)
	{
		$this->_db->exec("UPDATE " . $this->_admin_table_name . " SET password = md5('$NewPassword') WHERE id = $this->id");
	}

	
	/**
	 * Unsets the Admin variable from the Session effectively logging the admin out.
	 */
	Public Function Logout()
	{
		unset($_SESSION['Admin']);
	}
	
	/**
	 * Populates the object with information from the database.
	 * 
	 * @param String $ID
	 * @return Admin
	 */
	Public Function getInfo($ID = false)
	{
		if(!isset($this->id) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->id;
		
		$result = $this->_db->fetchRow('SELECT * FROM ' . $this->_admin_table_name . ' WHERE ID = ?', $ID);
		foreach($result as $key=>$value)
		{
			$this->Set($key, $value);
		}
		
		return $this;
	}
	
	/**
	 * Returns the ID for an Admin with Username matching $Username.
	 *
	 * @param String $Username
	 * @return Integer
	 */
	Public Function getIdUsingUsername($Username)
	{
		$result = $this->_db->fetchRow('SELECT * FROM ' . $this->_admin_table_name . ' WHERE username = ?', $Username);
		return $result->id;
	}
	
	/**
	 * Returns true if an Admin with Username of $Username exists.
	 * Retrusn false otherwise.
	 *
	 * @param String $Username
	 * @return Boolean
	 */
	Public Function emailExists($Username)
	{
		$Result = $this->_db->fetchRow('SELECT * FROM ' . $this->_admin_table_name . ' WHERE username = ?', $Username);
		if(sizeOf($Result) == 0 || !$Result)
			return false;
		else
			return true;
	}
	
	/**
	 * Inserts a new row into the table using the values provided by $Data and returns the table insert ID.
	 * 
	 * @param Array $Data
	 * @return Integer
	 */
	Public Function insert($Data)
	{
		$Insert = array();
		$Insert['Username'] = $Data['Username'];
		$Insert['Password'] = $Data['Password'];
		
		if($this->EmailExists($Insert['Username']) == false)
		{
			foreach($Insert as $k=>$v)
				$Insert[$k] = $this->_db->quote($v);
			
			$sql = "Insert INTO " . $this->_admin_table_name . " (username, password) VALUES ($Insert[Username], md5($Insert[Password]))";

			$this->_db->exec($sql);
			$this->id = $this->_db->lastInsertId();
		}else{
			echo 'ERROR EMAIL ALREADY TAKEN';
		}
		
		$this->insertInfo($Data, $this->id);
		$this->getInfo();
		
		return $this->id;
	}
	
	/**
	 * Returns true if an Admin exists with the matching $Username and $Password.
	 * Returns false otherwise.
	 *
	 * @param String $Username
	 * @param String $Password
	 * @return Boolean
	 */
	Public Function login($Username, $Password)
	{
		$Password = ($this->_db->quote($Password));
		$Result = $this->_db->fetchRow("SELECT * FROM " . $this->_admin_table_name . " WHERE username = ? AND password = md5($Password)", $Username);
		if(sizeof($Result) == 0 || !$Result)
		{
			return false;
		}else{
			return true;
		}
	}
	
	/**
	 * Sets the specified $Property of this object with the specified $Value provided.
	 * 
	 * @param String $Property
	 * @param Mixed $Value
	 * @return Admin
	 */
	Public Function set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
}
?>