<?php 

/**
 * Creates and manages objects of the Adwords_Countries table.
 */

/**
 * Creates and manages objects of the Adwords_Countries table.
 * 
 * The Adwords_Countries table contains the country name and corresponding two character code.
 * @todo Add examples.
 * @todo Add table structure.
 * 
 * @todo This class was originally intended only to be used with the Adwords API since that is where the CSV database dump originated from.  This class / table was later used by the other PPC Provider API's and should have its name changed to something more generic to reflect that use.
 * 
 * @category	BevoMedia
 * @package 	Application
 * @subpackage 	Common
 * @copyright 	Copyright (c) 2009 RCS
 * @author 		RCS
 * @version 	0.1
 */
class Adwords_Countries {
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $ID
	 */
	Public $ID;
	
	/**
	 * @var String $Code
	 */
	Public $Code;

	/**
	 * @var String $Country
	 */
	Public $Country;
	
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
	 * @param String $ID
	 * @return Adwords_Countries
	 */
	Public Function GetInfo($ID = false)
	{
		if(!isset($this->ID) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->ID;
		
		$result = $this->_db->fetchRow('SELECT * FROM bevomedia_adwords_countries WHERE id = ?', $ID);
		foreach($result as $key=>$value)
		{
			$this->Set($key, $value);
		}
		
		return $this;
	}
	
	/**
	 * Returns an array of Adwords_Countries objects corresponding to all countries within the table.
	 *
	 * @return Array
	 */
	Public Function GetAllCountries()
	{
		$Output = array();
		$Countries = $this->_db->fetchAll('SELECT * FROM bevomedia_adwords_countries');
		foreach($Countries as $Country)
			$Output[] = new Adwords_Countries($Country->id);
			
		return $Output;
	}
	
	/**
	 * Sets the specified $Property of this object with the specified $Value provided.
	 * 
	 * @param String $Property
	 * @param Mixed $Value
	 * @return Adwords_Countries
	 */
	Public Function Set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
}


?>