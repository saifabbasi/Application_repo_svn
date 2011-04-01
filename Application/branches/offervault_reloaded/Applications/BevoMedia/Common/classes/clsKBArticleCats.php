<?php

class KBArticleCats extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_kb_articles_cats', 'ID', array('ArticleID', 'CatID'));
	}
	
	function GetListByArticleID($intInArticleID) {
		$strSQL = 'SELECT catIdD FROM bevomedia_kb_article_cats WHERE (articleId = ' . $intInArticleID . ')';
		$this->Select($strSQL);
	}
	
	function DeleteByArticleID($intInArticleID) {
		$strSQL = 'DELETE FROM bevomedia_kb_article_cats WHERE (articleId = ' . $intInArticleID . ')';
		$this->Query($strSQL);
	}

}

?>
