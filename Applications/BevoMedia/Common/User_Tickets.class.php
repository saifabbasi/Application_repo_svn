<?php

Class User_Tickets {
	Protected $_db = false;
	
	Public $id, $user__id, $subject, $problem, $solved, $solvedTimetamp, $created, $deleted;
	
	Public Function __construct($ID = false)
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		if($ID !== false)
		{
			$this->id = $ID;
			$this->GetInfo();
		}
	}
	
	/**
	 * Populates the object with information from the database.
	 * 
	 * @param Integer $ID
	 * @return User_Tickets
	 */
	Public Function getInfo($ID = false)
	{
		if(!isset($this->id) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->id;
		
		$result = $this->_db->fetchRow('SELECT * FROM bevomedia_user_tickets WHERE id = ?', $ID);
		foreach($result as $key=>$value)
		{
			$this->set($key, $value);
		}
		
		return $this;
	}
	
	/**
	 * Returns an array of User_Tickets objects for all non deleted rows in the User_Tickets table.
	 *
	 * @return Array
	 */
	Public Function getAllTickets()
	{
		$Output = array();
		$Tickets = $this->_db->fetchAll('SELECT * FROM bevomedia_user_tickets WHERE deleted = 0 ORDER BY id desc');
		foreach($Tickets as $Ticket)
			$Output[] = new User_Tickets($Ticket->id);
			
		return $Output;
	}
	
	/**
	 * Updates the User_Tickets table to set Solved to '1' and the Solved_Timestamp to the current timestamp for the row ID matching $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function solve($ID = false)
	{
		if($ID === false)
			$ID = $this->id;
			
		$this->_db->exec("UPDATE bevomedia_user_tickets SET solved = 1, solvedTimestamp = NOW() WHERE id = $ID");
	}

	/**
	 * Updates the User_Tickets table to set Deleted to '1' for the row ID matching $ID.
	 *
	 * @param Integer $ID
	 */
	Public Function delete($ID = false)
	{
		if($ID === false)
			$ID = $this->id;
			
		$this->_db->exec("UPDATE bevomedia_user_tickets SET deleted = 1 WHERE id = $ID");
	}
	
	/**
	 * Returns a concatenated string of the FirstName and LastName of the User matching this $User_ID.
	 *
	 * @return String
	 */
	Public Function getUserName()
	{
		$User = new User($this->user__id);
		if(!isset($User->id))
			return false;
			
		return $User->firstName . '&nbsp;' . $User->lastName;
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
?>