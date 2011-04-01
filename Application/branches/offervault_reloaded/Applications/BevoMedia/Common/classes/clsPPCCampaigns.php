<?php

class PPCCampaigns extends DBObject {
	function __construct() {
		parent::__construct('ppc_campaigns', 'ID', array('UserID', 'ProviderType', 'AccountID', 'Name', 'Updated'));
	}
	
	function Exists($intInUserID, $intInProviderType, $intInAccountID, $strInName) {
		if (!is_numeric($intInUserID) || !is_numeric($intInProviderType) || !is_numeric($intInAccountID) || strlen($strInName) < 1) {
			return false;
		}
		$strSQL = 'SELECT ID FROM ppc_campaigns WHERE (UserID = ' . $intInUserID . ' AND ProviderType = ' . $intInProviderType . ' AND AccountID = ' . $intInAccountID . ' AND Name = \'' . $this->FixString($strInName) . '\')';
		$this->Select($strSQL);
	}
	
	function GetList($intInUserID, $intInProvider, $intInAccountID) {
		if (!is_numeric($intInUserID) || !is_numeric($intInProvider) || !is_numeric($intInAccountID)) {
			return false;
		}
		$strSQL = 'SELECT ID, Name FROM ppc_campaigns WHERE (UserID = ' . $intInUserID . ' AND ProviderType = ' . $intInProvider . ' AND AccountID = ' . $intInAccountID . ')';
		
		$this->Select($strSQL);
	}

}

?>