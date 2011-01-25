<?php

/**
 * Abstract Accounts_Abstract Class
 */

/**
 * Abstract class that will be reimplemented for use with database stored object data.
 * 
 * Accounts_Abstract Base Table Structure:
 * <pre>
 * +----------+------------------+------+-----+-------------------+----------------+
 * | Field    | Type             | Null | Key | Default           | Extra          |
 * +----------+------------------+------+-----+-------------------+----------------+
 * | id       | int(10) unsigned | NO   | PRI | NULL              | auto_increment |
 * | userId   | int(10) unsigned | NO   |     | NULL              |                |
 * | username | varchar(64)      | NO   |     | NULL              |                |
 * | password | varchar(64)      | NO   |     | NULL              |                |
 * | enabled  | tinyint(1)       | NO   |     | 1                 |                |
 * | created  | timestamp        | NO   |     | CURRENT_TIMESTAMP |                |
 * | deleted  | tinyint(1)       | NO   |     | NULL              |                |
 * +----------+------------------+------+-----+-------------------+----------------+
 * </pre>
 * 
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
Abstract Class Accounts_Abstract {
	
	/**
	 * @var String $_table_name
	 */
	Protected $_table_name;
	
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $id
	 */
	Public $id;

	/**
	 * @var Integer $user__id
	 */
	Public $user__id;

	/**
	 * @var String $username
	 */
	Public $username;

	/**
	 * @var String $password
	 */
	Public $password;
	
	/**
	 * @var String $api_key
	 */
	Public $api_key;
	
	/**
	 * @var String $enabled
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
	 * Accounts_Abstract Object Constructor
	 *
	 * @param integer $User_ID
	 */
	Public Function __construct($User_ID = false)
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
		
		if($User_ID !== false)
			$this->user__id = $User_ID;
	}
	
	/**
	 * Populates the object with information from the database
	 * 
	 * @param integer $ID
	 * 
	 * @return Accounts_Abstract
	 */
	Public Function getInfo($ID = false)
	{
		if(!is_int($this->id) && empty($ID))
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->id;
		$Result = $this->_db->fetchRow('SELECT * FROM ' . $this->_table_name . ' WHERE id = ?', $ID);
		if(!$Result)
			return false;
		foreach($Result as $Key=>$Value)
		{
			$this->set($Key, $Value);
		}
		
		return $this;
	}
	
	/**
	 * Sets the account as enabled in the database.
	 *
	 * @param integer $ID
	 */
	Public Function EnableAccount($ID)
	{
		$this->_db->exec("UPDATE $this->_table_name SET enabled = 1 WHERE id = $ID");
	}
	
	/**
	 * Sets the account as Disabled in the database.
	 *
	 * @param integer $ID
	 */
	Public Function DisableAccount($ID)
	{
		$this->_db->exec("UPDATE $this->_table_name SET enabled = 0 WHERE id = $ID");
	}
	
	/**
	 * Returns all rows from the table that belong to this User_ID and are have a valid Username and Password.
	 *
	 * @param integer $User_ID
	 * @return Array
	 */
	Public Function getInstalledAccounts($User_ID = false)
	{
		if($User_ID === false)
			$User_ID = $this->user__id;
			
		return $this->_db->fetchAll('SELECT * FROM ' . $this->_table_name . ' WHERE user__id = ? AND enabled = 1 AND username != "" AND password != "" AND deleted = 0', $User_ID);
	}
	
	/**
	 * Returns all rows from the table that belong to this User_ID which do not have a valid Password.
	 * 
	 * @param integer $User_ID
	 * @return Array
	 */
	Public Function getNotInstalled($User_ID = false)
	{
		if($User_ID === false)
			$User_ID = $this->user__id;
			
		return $this->_db->fetchAll('SELECT * FROM ' . $this->_table_name . ' WHERE user__id = ? AND enabled = 1 AND password = "" AND deleted = 0', $User_ID);
	}
	
	/**
	 * Returns all rows from the table that belong to this User_ID which have been disabled by the user.
	 * 
	 * @param integer $User_ID
	 * @return Array
	 */	
	Public Function getDisabledAccounts($User_ID = false)
	{
		if($User_ID === false)
			$User_ID = $this->user__id;
			
		return $this->_db->fetchAll('SELECT * FROM ' . $this->_table_name . ' WHERE user__id = ? AND enabled = 0 AND deleted = 0', $User_ID);
	}
	

	/**
	 * Returns all rows from the table.
	 * 
	 * @return Array
	 */
	Public Function getAll()
	{
		return $this->_db->fetchAll('SELECT * FROM ' . $this->_table_name . ' WHERE deleted = 0');
	}
	
	/**
	 * Returns all rows from the table that belong to this User_ID which have not been deleted.
	 *
	 * @param integer $User_ID
	 * @return Array
	 */
	Public Function getAllAccounts($User_ID = false)
	{
		if($User_ID === false)
			$User_ID = $this->user__id;
			
		return $this->_db->fetchAll('SELECT * FROM ' . $this->_table_name . ' WHERE user__id = ? AND deleted = 0', $User_ID);
	}
	
	/**
	 * Updates the table setting values within $Data where the row ID equals $Data[ID].
	 *
	 * @param Array $Data
	 */
	Public Function update($Data)
	{
		$this->_db->update($this->_table_name, $Data, "id = $Data[id]");
	}
	
	/**
	 * Updates the table setting the Deleted column to 1 where the row ID equals $ID.
	 *
	 * @param integer $ID
	 */
	Public Function delete($ID)
	{
		$this->getInfo($ID);
		$Data = array('Deleted'=>1);
		$this->_db->update($this->_table_name, $Data, "id = $ID");
		if($this->providerType && $this->user__id)
		{
		  $user = "user__id=" . $this->user__id;
		  $accIds = "select id from " . $this->_table_name . " where username='" . $this->username . "' and $user";
		  $cWhere = "where $user AND providerType={$this->providerType} AND accountId in ($accIds)";
		  $cIds = "select id from bevomedia_ppc_campaigns $cWhere";
		  $agIds = "select id from bevomedia_ppc_adgroups where id in ($cIds)";
		  /* Delete stats */
		  $this->_db->exec("delete from bevomedia_ppc_advariations_stats where advariationsId in (select id from bevomedia_ppc_advariations where adGroupId in ($agIds))");
		  $this->_db->exec("delete from bevomedia_ppc_keywords_stats where keywordId in (select id from bevomedia_ppc_keywords where adGroupId in ($agIds))");
		  $this->_db->exec("delete from bevomedia_ppc_contentmatch_stats where adGroupId in ($agIds)");
		  /* delete campaigns */
		  $this->_db->exec("delete from bevomedia_ppc_advariations where adGroupId in ($agIds)");
		  $this->_db->exec("delete from bevomedia_ppc_keywords where adGroupId in ($agIds)");
		  $this->_db->exec("delete from bevomedia_ppc_adgroups where campaignId in ($cIds)");
		  $this->_db->exec("delete from bevomedia_ppc_campaigns $cWhere");
		}
	}
	
	
	/**
	 * Inserts a new row into the table using the values provided by $Data and returns the table insert ID.
	 *
	 * Example:
	 * <code>
	 * // NOTE: This code will not work since this is an abstract class.
	 * // It is provided to be used as an example for reimplementation.
	 * 
	 * // Create a new associative array of data to be inserted.
	 * $Data = array();
	 * $Data['User_ID'] = 1;
	 * $Data['Username'] = 'JohnDoe';
	 * $Data['Password'] = 'Password12';
	 * 	
	 * // Create a new instance of Accounts_Abstract.
	 * $Account = new Accounts_Abstract();
	 * 
	 * // Insert the data and return the id of the inserted row.
	 * $InsertID = $Account->Insert($Data);
	 * </code>
	 * 
	 * @param Array $Data	The values to be inserted into the table.
	 * @return Integer		The id of the inserted row.
	 */
	Public Function insert($Data)
	{
		$Insert = array();
		$Insert['User_ID'] = $Data['user__id'];
		$Insert['Username'] = $Data['Username'];
		$Insert['Password'] = $Data['Password'];
		$Insert['APIKey'] = @$Data['APIKey'];
		
		foreach($Insert as $Key=>$Value)
			$Insert[$Key] = $this->_db->quote($Value);
			
		$Sql = "INSERT INTO $this->_table_name (user__id, Username, Password, api_key) VALUES ($Insert[User_ID], $Insert[Username], $Insert[Password], $Insert[APIKey])";

		$this->_db->exec($Sql);
		$this->id = $this->_db->lastInsertId();
		
		$this->getInfo();
		
		return $this->id;
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