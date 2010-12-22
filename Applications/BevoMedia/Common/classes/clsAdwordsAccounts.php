<?php

class AdwordsAccounts extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_accounts_adwords', 'id', array('user__id', 'username'));
	}
	
	function ExistsByEmail($strInEmail) {
		if (strlen(strInEmail) < 1) {
			return false;
		}
		
		$strSQL = 'SELECT ID, User__ID as UserID FROM bevomedia_accounts_adwords WHERE (AdwordsEmail = \'' . $this->FixString($strInEmail) . '\')';
		$this->Select($strSQL);
	}
	
	function GetListByUserID($intInUserID) {
		if (!is_numeric($intInUserID)) {
			return false;
		}
		
		$strSQL = 'SELECT id AS ID, username as AdwordsEmail FROM bevomedia_accounts_adwords WHERE (user__id = ' . $intInUserID . ') AND deleted = 0';
		$this->Select($strSQL);
	}
	

}

?>