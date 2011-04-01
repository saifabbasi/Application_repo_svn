<?php 

Class LandingPageRotatorGroup {
	
	Protected $_db = false;
	
	Public $ID;
	
	Public $UserID;
	
	Public $PubID;
	
	Public $Created;
	
	Public $Deleted;
	
	Public $Links = array();
	Public $LadingPages = array();
	
	Public Function __construct($ID = false)
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		if($ID !== false)
		{
			$this->id = $ID;
			$this->GetInfo(); 
		}
	}
	
	Public Function PopulateLandingPages()
	{
		$Result = $this->_db->fetchAll('SELECT link AS Link FROM bevomedia_landing_page_rotator_link WHERE groupId = ?', $this->id);
		foreach($Result as $Row)
		{
			$this->Links[] = $Row->Link;
		}
	}
	
	Public Function PopulateLandingPagesFull()
	{
		$LandingPages = new LandingPageRotatorLink();
		$this->LandingPages = $LandingPages->GetAllForGroupID($this->id);
	}
	
	Public Function DeleteThisGroup()
	{
		if(!isset($this->id))
		{
			return false;
		}
		
		$this->_db->update('bevomedia_landing_page_rotator_group', array('deleted'=>1), 'id = '.$this->id);
	}
	
	Public Function GetAllForUser($UserID)
	{
		$Output = array();
		$Result = $this->_db->fetchAll('SELECT id FROM bevomedia_landing_page_rotator_group WHERE user__id = ? AND deleted = 0', $UserID);
		foreach($Result as $Row)
		{
			$Temp = new LandingPageRotatorGroup($Row->id);
			$Output[] = $Temp;
		}
		return $Output;
	}
	
	Public Function GetInfo($ID = false)
	{
		if(!isset($this->id) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->id;
		
		$Result = $this->_db->fetchRow('SELECT * FROM bevomedia_landing_page_rotator_group WHERE id = ?', $ID);
		foreach($Result as $Key=>$Value)
		{
			$this->Set($Key, $Value);
		}
		
		return $this;
	}
	
	Public Function Insert($Label, $UserID, $ID = false)
	{
		$Insert = array('label'=>$Label, 'user__id'=>$UserID);
		if($ID !== false)
		{
			$Insert['id'] = $ID;
		}
		$this->_db->insert('bevomedia_landing_page_rotator_group', $Insert);
		return $this->_db->lastInsertId();
	}
	
	Public Function Set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
}