<?php

global $objDB;
$objDB = new DBConnection(DATABASE_URL, DATABASE_USERID, DATABASE_PASSWORD, DATABASE_NAME);
$objDB->Open();

class DBConnection {
	var $objConn;
	
	var $strServer;
	var $strUser;
	var $strPass;
	var $strName;
	
	public $objResult;

	
	function __construct($strInServer = '', $strInUser = '', $strInPass = '', $strInName = '') {
		$this->strServer = $strInServer;
		$this->strUser = $strInUser;
		$this->strPass = $strInPass;
		$this->strName = $strInName;
	}
	
	function Open() {
		$this->objConn = mysql_connect($this->strServer, $this->strUser, $this->strPass);
		if (!$this->objConn) { die(mysql_error()); }
		
		mysql_select_db($this->strName, $this->objConn);
	}
	function Close() {
		mysql_close($objConn);	
	}
	function Query($strInSQL) {
		$this->objResult = mysql_query($strInSQL, $this->objConn) OR die( "<PRE>".mysql_error()."<br />".debug_print_backtrace());
	}
}

?>
