<?php

class MSNAccounts extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_accounts_msnadcenter', 'ID', array('user__id', 'username'));
	}
	
	function GetListByUserID($intInUserID) {
		if (!is_numeric($intInUserID)) {
			return false;
		}
		
		$strSQL = 'SELECT ID, Username as Name FROM bevomedia_accounts_msnadcenter WHERE (user__id = ' . $intInUserID . ') AND deleted = 0';
		$this->Select($strSQL);
	}

}

?>