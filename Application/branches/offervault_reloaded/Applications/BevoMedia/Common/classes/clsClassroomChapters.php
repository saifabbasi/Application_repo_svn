<?php

class ClassroomChapters extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_classroom_chapters', 'id', array('sectionId', 'chapter', 'title', 'content', 'videoUrl'));
	}
	
	function GetListBySectionID($intInSectionID) {
		if (!is_numeric($intInSectionID)) {
			return false;
		}
		
		$strSQL = 'SELECT id, chapter, title, videoUrl FROM bevomedia_classroom_chapters WHERE (sectionID = ' . $intInSectionID . ') ORDER BY chapter ASC';
		$this->Select($strSQL);
	}

}

?>
