<?php

class MarketLeadAttachments extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_bevomedia_market_lead_attachments', 'id', array('id', 'postId', 'fileName'));
	}
	
	function GetListByleadId($intInleadId) {
		$strSQL = 'SELECT bevomedia_market_lead_attachments.id, postId, fileName FROM
					(bevomedia_market_lead_attachments LEFT JOIN bevomedia_market_lead_posts ON bevomedia_market_lead_attachments.postId = bevomedia_market_lead_posts.id)
					LEFT JOIN bevomedia_market_leads ON bevomedia_market_lead_posts.leadId = bevomedia_market_leads.id
					WHERE (bevomedia_market_leads.id = ' . $intInleadId . ')';
		$this->Select($strSQL);
	}

}

?>
