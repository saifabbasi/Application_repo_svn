<?php
Class PremiumOrder
{	
	/**
	 * @var Zend_Db_Adapter_Abstract $_db
	 */
	Protected $_db = false;
	
	/**
	 * @var Integer $id
	 */
	Public $id;
	
	/**
	 * @var String $user__id
	 */
	Public $user__id;
	
	/**
	 * @var String $Email
	 */
	Public $Email;
	
	/**
	 * @var String $Phone
	 */
	Public $Phone;
	
	/**
	 * @var Integer payed
	 */
	Public $payed;
	
	/**
	 * @var String $Created
	 */
	Public $Created;
	
	/**
	 * @var Integer $Deleted
	 */
	Public $Deleted;
	/**
	 * @var Integer $Active
	 */
	Public $Active;
	
	
	/**
	 * Constructor
	 *
	 * @param Integer $id
	 */
	Public Function __construct($id = false)
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		if($id !== false)
		{
			$this->id = $id;
			$this->GetInfo();
		}		
	}

	/**
	 * Populates the object with information from the database.
	 * 
	 * @param Integer $id
	 * 
	 * @return PremiumOrder
	 */
	Public Function GetInfo($id = false)
	{
		if(!isset($this->id) && $id == false)
		{
			return false;
		}
		
		if($id == false)
			$id = $this->id;
		
		$Result = $this->_db->fetchRow('SELECT * FROM bevomedia_premium_orders WHERE id = ?', $id);
		foreach($Result as $Key=>$Value)
		{
			$this->Set($Key, $Value);
		}
		
		return $this;
	}
	
	/**
	 * Inserts a new row into the table using the values provided by $Insert and returns the table insert id.
	 * 
	 * @param Array $Insert	The values to be inserted into the table.
	 * @return Integer		The id of the inserted row.
	 */
	Public Function Insert($Input)
	{
		$Insert = array();
		$AllowedKeys = array('user__id', 'Email', 'Phone');
		foreach($Input as $Key=>$Value)
		{
			if(!in_array($Key, $AllowedKeys))
				continue;
				
			$Insert[$Key] = $this->_db->quote($Value);
		}
		$Sql = "Insert INTO bevomedia_premium_orders (user__id, Email, Phone) VALUES ($Insert[user__id], $Insert[Email], $Insert[Phone])";
		$this->_db->exec($Sql);
		$this->id = $this->_db->lastInsertid();
		
		$this->GetInfo();
		
		return $this->id;
	}
	
	Public Function SetActive()
	{
		$Sql = "Update bevomedia_premium_orders SET active = '1' WHERE id = '{$this->id}'";
		$this->_db->exec($Sql);
	}
	
	Public Function SetPaidAmount($amount = 0)
	{
		$Sql = "Update bevomedia_premium_orders SET paid = $amount WHERE id = '{$this->id}'";
		$this->_db->exec($Sql);
	}
	
	/**
	 * Sets the specified $Property of this object with the specified $Value provided.
	 * 
	 * @param String $Property
	 * @param Mixed $Value
	 * @return bevomedia_premium_orders
	 */
	Public Function Set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
	
}
?>