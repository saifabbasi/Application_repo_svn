<?php

class KBFeeds extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_kb_feeds', 'id', array('title', 'url', 'lastRequest'));
	}
	
	function BatchDelete($strInIDs) {
		$strSQL = 'DELETE FROM bevomedia_kb_posts WHERE (id IN (' . $strInIDs . '))';
		$this->Query($strSQL);
	}

}

?>
