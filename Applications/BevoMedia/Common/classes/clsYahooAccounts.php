<?php

class YahooAccounts extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_accounts_yahoo', 'id', array('user__id', 'username', 'password', 'masterAccountId'));
	}
	
	function GetListByUserID($intInUserID) {
		if (!is_numeric($intInUserID)) {
			return false;
		}
		
		
		$strSQL = 'SELECT id AS ID, username AS Username FROM bevomedia_accounts_yahoo WHERE (user__id = ' . $intInUserID . ') AND deleted = 0';

		$this->Select($strSQL);
	}

}

?>