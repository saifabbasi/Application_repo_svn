<?php

class ClassroomSections extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_classroom_sections', 'ID', array('title', 'image', 'sortOrder'));
	}
	
	function GetList() {	
		$strSQL = 'SELECT id, title, image FROM bevomedia_classroom_sections ORDER BY sortOrder ASC';
		$this->Select($strSQL);
	}

}

?>
