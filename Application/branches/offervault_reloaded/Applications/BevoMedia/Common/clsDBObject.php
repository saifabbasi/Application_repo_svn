<?php

require_once('clsDB.php');

class DBObject {
	public $ID;
	private $strIDName;
	private $strTableName;
	private $arrCols = array();
	
	public $PageSize;
	public $Page;
	public $PageCount;
	public $RowCount;
	private $intRow;
	
	public $objResult;

	function __construct($strInTable, $strInIDName, $arrInCols) {
		$this->strTableName = $strInTable;
		$this->strIDName = $strInIDName;
		
		foreach ($arrInCols as $strThisKey) {
			$this->arrCols[$strThisKey] = null;
		}
		
		$this->PageSize = 0;
		$this->Page = 1;
		$this->RowCount = 0;
	}

	function __get($strInKey) {
		if (isset($this->arrCols[$strInKey]))
			return $this->arrCols[$strInKey]; else
			return ''; 
	}

	function __set($strInKey, $strInVal) {
		if(array_key_exists($strInKey, $this->arrCols)) {
			$this->arrCols[$strInKey] = $this->FixString($strInVal);
			return true;
		}
		return false;
	}
	
	function GetList($strInSQL = '') {
		$arrCols = array_keys($this->arrCols);
		array_unshift($arrCols, $this->strIDName);
		$strColumns = join(", ", $arrCols);
		
		$strSQL = 'SELECT ' . $strColumns . ' FROM ' . $this->strTableName . ' ' . $strInSQL;
		$this->Select($strSQL);
	}

	function GetDetails() {
		global $objDB;
		
		$arrCols = array_keys($this->arrCols);
		array_unshift($arrCols, $this->strIDName);
		$strColumns = join(", ", $arrCols);

		$strSQL = "SELECT " . $strColumns . " FROM " . $this->strTableName . " WHERE (" . $this->strIDName . " = " . $this->ID . ")";
		$objDB->Query($strSQL);
		
		if (mysql_num_rows($objDB->objResult) == 0)
			return false;
		else {
			$arrRow = mysql_fetch_array($objDB->objResult, MYSQL_ASSOC);
			foreach($arrRow as $strThisKey => $strThisVal)
				$this->arrCols[$strThisKey] = $strThisVal;
		}
	}
	
	function Select($strInSQL) {
		global $objDB;
		
		$objDB->Query($strInSQL);
		$this->objResult = $objDB->objResult;
		$this->CountRecords();
	}

	function Insert() {
		global $objDB;

		$arrColumns = join(", ", array_keys($this->arrCols));
		$arrValues = "'" . join("', '", $this->arrCols) . "'";

		$strSQL = "INSERT INTO " . $this->strTableName . " ($arrColumns) VALUES ($arrValues)";
		$objDB->Query($strSQL);

		$this->ID = mysql_insert_id($objDB->objConn);
		return $this->ID;
	}

	function Update() {
		global $objDB;

		unset($this->arrCols[$this->strIDName]);
		$arrStuff = array();
		foreach($this->arrCols as $strThisKey => $strThisVal) {
			if (!is_null($strThisVal)) {
				$arrTemp[] = "$strThisKey = '$strThisVal'";
			}
		}
		
		$strTemp = implode(", ", $arrTemp);		

		$objDB->Query("UPDATE " . $this->strTableName . " SET $strTemp WHERE " . $this->strIDName . " = '" . $this->ID . "'");

		return mysql_affected_rows($objDB->objConn);
	}

	function Delete() {
		global $objDB;
		$objDB->Query("DELETE FROM " . $this->strTableName . " WHERE " . $this->strIDName . " = '" . $this->ID . "'");
		return mysql_affected_rows($objDB->objConn);
	}
	
	function Query($strInSQL) {
		global $objDB;
		$objDB->Query($strInSQL);
		return mysql_affected_rows($objDB->objConn);	
	}
	
	function FixString($strInString) {
		if (get_magic_quotes_gpc()) {
			$strInString = stripslashes($strInString);
		}
		return mysql_real_escape_string($strInString);
	}
	
	function CountRecords() {
		$this->RowCount = mysql_num_rows($this->objResult);
		
		if ($this->PageSize == 0) {
			$this->PageSize = $this->RowCount;
		}
		
		if ($this->PageSize > 0) {
			$this->PageCount = ceil($this->RowCount / $this->PageSize);
		}
		else {
			$this->PageCount = 0;
		}
	}
	
	function MovePage($intInPage = '', $intInPageSize = '') {
		if (is_numeric($intInPage)) {
			$this->Page = $intInPage;
		}
		if (is_numeric($intInPageSize)) {
			$this->PageSize = $intInPageSize;
		}
		$this->CountRecords();
		
		if ($this->Page > $this->PageCount || $this->Page <= 0) {
			return false;
		}
		
		$this->intRow = 0;
		mysql_data_seek($this->objResult, ($this->Page - 1) * $this->PageSize);
	}
	
	function GetRow() {
		if (!$this->objResult) {
			return false;
		}
	    if ($this->intRow >= $this->PageSize) {
			return false;
		}
	    $this->intRow++;
	    return mysql_fetch_array($this->objResult, MYSQL_ASSOC);
	}
	
	function GetRows() {
		$arrRows = array();
		while ($arrThisRow = $this->GetRow()) {
			$arrRows[] = $arrThisRow;
		}
		return $arrRows;
	}

}
?>