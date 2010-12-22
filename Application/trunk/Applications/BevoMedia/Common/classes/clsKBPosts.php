<?php

class KBPosts extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_kb_posts', 'id', array('feedId', 'guid', 'url', 'title', 'content', 'postDate'));
	}
	
	function GetList($intInfeedId = '') {
		$strWhere = '';
		if (is_numeric($intInfeedId)) {
			$strWhere = ' WHERE (feedId = ' . $intInfeedId . ') ';
		}
		
		$strSQL = 'SELECT bevomedia_kb_posts.id, feedId, bevomedia_kb_posts.title, content, postDate, bevomedia_kb_feeds.title as Feedtitle FROM bevomedia_kb_posts
					LEFT JOIN bevomedia_kb_feeds ON bevomedia_kb_posts.feedId = bevomedia_kb_feeds.ID ';
		$strSQL .= $strWhere;
		$strSQL .= 'ORDER BY postDate DESC';
		$this->Select($strSQL);
	}
	
	function GetListBySearch($strInSearch = '') {
		if (strlen($strInSearch) < 4) {
			return false;
		}
		
		/*$strSQL = 'SELECT kb_posts.ID, feedId, kb_posts.title, content, postDate, kb_feeds.title as Feedtitle, MATCH (kb_posts.title, kb_posts.content) AGAINST (\'' . $this->FixString($strInSearch) . '\') AS Score FROM kb_posts
					LEFT JOIN kb_feeds ON kb_posts.feedId = kb_feeds.ID
					WHERE MATCH (kb_posts.title, kb_posts.content) AGAINST (\'' . $this->FixString($strInSearch) . '\')
					ORDER BY Score DESC'; */
		$strSQL = 'SELECT bevomedia_kb_posts.id, feedId, bevomedia_kb_posts.title, content, postDate, bevomedia_kb_feeds.title as Feedtitle FROM bevomedia_kb_posts
					LEFT JOIN bevomedia_kb_feeds ON bevomedia_kb_posts.feedId = bevomedia_kb_feeds.ID
					WHERE (bevomedia_kb_posts.title LIKE \'%' . $this->FixString($strInSearch) . '%\') OR (bevomedia_kb_posts.content LIKE \'%' . $this->FixString($strInSearch) . '%\')
					ORDER BY postDate DESC';
		$this->Select($strSQL);		
	}
	
	function ExistsByGUID($intInfeedId, $strInGUID) {
		if (!is_numeric($intInfeedId) || strlen($strInGUID) < 1) {
			return false;
		}
		$strSQL = 'SELECT id FROM bevomedia_kb_posts WHERE (feedId = ' . $intInfeedId . ' AND guid = \'' . $this->FixString($strInGUID) . '\')';
		$this->Select($strSQL);
	}
	
	function BatchDelete($strInIDs) {
		$strSQL = 'DELETE FROM bevomedia_kb_posts WHERE (id IN (' . $strInIDs . '))';
		$this->Query($strSQL);
	}

}

?>
