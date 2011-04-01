<?php

class KBCats extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_kb_cats', 'id', array('parentID', 'title'));
	}
	
	function GetList($intInParentID = 0) {
		$strSQL = 'SELECT id, title FROM bevomedia_kb_cats WHERE (parentID = ' . $intInParentID . ') ORDER BY title ASC';
		$this->Select($strSQL);
	}

}

?>
