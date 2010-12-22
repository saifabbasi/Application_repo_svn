<?php 

Class LandingPageRotatorLink {
	
	Protected $_db = false;
	
	Public $ID;
	
	Public $GroupID;
	
	Public $Link;
	
	Public $Ratio;
	
	Public $Deleted;
	
	Public Function __construct($ID = false)
	{
		$this->_db = Zend_Registry::get('Instance/DatabaseObj');
		if($ID !== false)
		{
			$this->ID = $ID;
			$this->GetInfo(); 
		}
	}
	
	Public Function GetAllForGroupID($GroupID)
	{
		$Output = array();
		$Result = $this->_db->fetchAll('SELECT ID FROM bevomedia_landing_page_rotator_link WHERE groupId = ?', $GroupID);
		foreach($Result as $Row)
		{
			$Temp = new LandingPageRotatorLink($Row->ID);
			$Output[] = $Temp;
		}
		return $Output;
	}
	
	Public Function GetInfo($ID = false)
	{
		if(!isset($this->ID) && $ID == false)
		{
			return false;
		}
		
		if($ID == false)
			$ID = $this->ID;
		
		$Result = $this->_db->fetchRow('SELECT * FROM bevomedia_landing_page_rotator_link WHERE id = ?', $ID);
		foreach($Result as $Key=>$Value)
		{
			$this->Set($Key, $Value);
		}
		
		return $this;
	}
	
	Public Function Insert($Link, $Ratio, $GroupID)
	{
		$Insert = array('Link'=>$Link, 'Ratio'=>$Ratio, 'GroupID'=>$GroupID);
		$this->_db->insert('bevomedia_landing_page_rotator_link', $Insert);
		return $this->_db->lastInsertId();
	}
	
	Public Function Set($Property, $Value)
	{
		$this->{$Property} = $Value;
		return $this;
	}
}