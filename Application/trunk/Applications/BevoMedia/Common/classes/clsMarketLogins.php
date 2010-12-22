<?php

class MarketLogins extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_logins', 'id', array('providerId', 'email', 'password', 'lastLoginDate'));
	}
	
	function GetListByproviderId($intInproviderId) {
		if (!is_numeric($intInproviderId)) {
			return false;
		}
		
		$strSQL = 'SELECT id, email, password FROM bevomedia_market_logins WHERE (providerId = ' . $intInproviderId . ')'; 
		$this->Select($strSQL);
	}
	
	function TryLogin($strInemail) {
		$strSQL = 'SELECT id, password FROM bevomedia_market_logins WHERE (email = \'' . $this->FixString($strInemail) . '\')';
		$this->Select($strSQL);
	}
	
	function Updatepassword($providerId, $password)
	{
		$Sql = "UPDATE bevomedia_market_logins SET password = '".$this->FixString($password)."' WHERE (providerId = {$providerId}) ";		
		$this->Select($Sql);
	}

}

?>
