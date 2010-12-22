<?php

class KeywordTrackerKeywords extends DBObject {

	function __construct() {
		parent::__construct('keyword_tracker_keywords', 'ID', array('ID', 'Keyword'));
	}
	
	function KeywordExists($strInKeyword) {
		$strSQL = 'SELECT ID FROM keyword_tracker_keywords WHERE (Keyword = \'' . $this->FixString($strInKeyword) . '\')';
		$this->Select($strSQL);
	}

}

?>