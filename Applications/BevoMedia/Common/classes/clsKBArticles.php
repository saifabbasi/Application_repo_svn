<?php

class KBArticles extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_kb_articles', 'id', array('authorId', 'title', 'content', 'date'));
	}
	
	function GetListByCatID($intInCatID = 0) {
		$strSQL = 'SELECT id, authorId, title, content, date FROM bevomedia_kb_articles
					RIGHT JOIN bevomedia_kb_article_cats ON bevomedia_kb_articles.id = bevomedia_kb_article_cats.articleId
					WHERE (bevomedia_kb_article_cats.catId = ' . $intInCatID . ') ORDER BY title ASC';
		$this->Select($strSQL);
	}
	
	function GetListBySearch($strInSearch = '', $intInCatID = 0) {
		if (strlen($strInSearch) < 4) {
			return false;
		}
		
		if ($intInCatID != 0) {
			$strWhere = '(bevomedia_kb_article_cats.catId = ' . $intInCatID . ') AND ';
		}
		
		$strSQL = 'SELECT ID, authorId, title, content, date, MATCH (bevomedia_kb_articles.title, bevomedia_kb_articles.content) AGAINST (\'' . $this->FixString($strInSearch) . '\') AS Score FROM bevomedia_kb_articles
					RIGHT JOIN bevomedia_kb_article_cats ON bevomedia_kb_articles.id = bevomedia_kb_article_cats.articleId
					WHERE ' . $strWhere . '
					MATCH (bevomedia_kb_articles.title, bevomedia_kb_articles.content) AGAINST (\'' . $this->FixString($strInSearch) . '\')
					ORDER BY Score DESC';
		$this->Select($strSQL);		
	}

}

?>
