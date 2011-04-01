<?php

/**
 * Creates and manages objects of the Mentor table.
 */

/**
 * Creates and manages objects of the Mentor table.
 *
 * Creates and manages objects of the Mentor table.
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
Class Mentor {
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $id
	 */
	Public $id;
	
	/**
	 * @var String $name
	 */
	Public $name;
	
	/**
	 * @var String $email
	 */
	Public $email;
	
	/**
	 * @var String $aim
	 */
	Public $aim;
	
	/**
	 * @var String $phone
	 */
	Public $phone;
	
	/**
	 * @var String $created
	 */
	Public $created;
	
	/**
	 * @var Integer $deleted
	 */
	Public $deleted;
	
	/**
	 * @var String $_mentor_table_name
	 */
	Private $_mentor_table_name = 'bevomedia_mentor';
	
	/**
	 * @var String $_mentor_to_user_table_name
	 */
	Private $_mentor_to_user_table_name = 'bevomedia_mentor_to_user';
	
	/**
	 * Constructor
	 *
	 * @param String $ID
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
	 * @param String $ID
	 * @return Mentor
	 */
	Public Function getInfo($ID = false)
	{
		if(!isset($this->id) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->id;
		
		$result = $this->_db->fetchRow('SELECT * FROM ' . $this->_mentor_table_name . ' WHERE id = ?', $ID);
		foreach($result as $key=>$value)
		{
			$this->Set($key, $value);
		}
		
		return $this;
	}
	
	/**
	 * Inserts a new row into the table using the values provided by $Data and returns the table insert ID.
	 *
	 * @todo Modify to have quoteInto functionality and to explicitly specify which values to use.
	 *
	 * @param Array $Data
	 * @return Integer
	 */
	Public Function insert($Data)
	{
		$this->_db->insert($this->_mentor_table_name, $Data);
		$this->id = $this->_db->lastInsertId();
		return $this->id;
	}
	
	/**
	 * Updates the table setting values within $Data where the row ID equals $Data[ID].
	 *
	 * @param Array $Data
	 */
	Public Function update($Data)
	{
		$this->_db->update($this->_mentor_table_name, $Data, 'id = ' . $this->id);
		$this->getInfo();
	}
	
	/**
	 * Returns an array of User objects that belong to this Mentor.
	 *
	 * @return Array
	 */
	Public Function getMentorsUsers()
	{
		$Output = array();
		$Users = $this->_db->fetchAll('SELECT * FROM ' . $this->_mentor_to_user_table_name . ' WHERE mentor__id = ' . $this->id);
		foreach($Users as $User)
			$Output[] = new User($User->user__id);
			
		return $Output;
	}
	
	/**
	 * Assigns the User with the specified $User_ID to this Mentor.
	 *
	 * @param Integer $User_ID
	 */
	Public Function addUserToMentor($UserID)
	{
		$Data = array('user__id'=>$UserID, 'mentor__id'=>$this->id);
		$this->removeUserFromMentor($UserID);
		$this->_db->insert($this->_mentor_to_user_table_name, $Data);
	}
	
	/**
	 * Removes the User with the specified $User_ID from havnig a mentor.
	 *
	 * @param Integer $User_ID
	 */
	Public Function removeUserFromMentor($UserID)
	{
		$this->_db->delete($this->_mentor_to_user_table_name, 'user__id = ' . $UserID);
	}
	
	/**
	 * Updates the table to set the Mentor with the specified $ID to be Deleted.
	 *
	 * @param Integer $ID
	 */
	Public Function deleteMentor($ID)
	{
		$Data = array('Deleted'=>1);
		$this->_db->update($this->_mentor_table_name, $Data, 'id = ' . $ID);
	}
	
	/**
	 * Updates the table to remove Deleted status from the Mentor with the specified $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function restoreMentor($ID)
	{
		$Data = array('Deleted'=>0);
		$this->_db->update($this->_mentor_table_name, $Data, 'id = ' . $ID);
	}
	
	/**
	 * Returns an array of Mentor objects corresponding to all Mentors in the table.
	 *
	 * @todo Optimize SQL
	 * @return Array
	 */
	Public Function getAllMentors()
	{
		$Output = array();
		$Mentors = $this->_db->fetchAll('SELECT * FROM ' . $this->_mentor_table_name);
		foreach($Mentors as $Mentor)
			$Output[] = new Mentor($Mentor->id);
			
		return $Output;
	}
	
	/**
	 * Returns a Mentor object for the entry with an Email matching the specified $Email.
	 * Returns false if Mentor could not be found.
	 *
	 * @param String $Email
	 * @return Mentor | Boolean
	 */
	Public Function getMentorUsingEmail($Email)
	{
		$Sql = 'SELECT id FROM ' . $this->_mentor_table_name . ' WHERE email = "' . $Email . '" AND deleted = 0';
		$Row = $this->_db->fetchRow($Sql);
		if($Row)
		{
			return new Mentor($Row->id);
		}else{
			return false;
		}
	}
	
	/**
	 * Returns an array of Mentor objects corresponding to all non deleted Mentors in the table.
	 *
	 * @return Array
	 */
	Public Function getAllNonDeletedMentors()
	{
		$Output = array();
		$Mentors = $this->_db->fetchAll('SELECT * FROM '. $this->_mentor_table_name .' WHERE deleted = 0');
		foreach($Mentors as $Mentor)
			$Output[] = new Mentor($Mentor->id);
			
		return $Output;
	}
	
	/**
	 * Sets the specified $Property of this object with the specified $Value provided.
	 *
	 * @param String $Property
	 * @param Mixed $Value
	 * @return Mentor
	 */
	Public Function set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
}
?>