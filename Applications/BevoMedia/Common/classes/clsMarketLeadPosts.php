<?php

class MarketLeadposts extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_lead_posts', 'id', array('leadId', 'user__id', 'providerId', 'post', 'postDate'));
	}
	
	function GetListByleadId($intInleadId) {
		$strSQL = 'SELECT bevomedia_market_lead_posts.id, bevomedia_market_lead_posts.user__id, providerId, post, postDate, bevomedia_market_providers.Name AS ProviderName, bevomedia_user_info.companyName AS UserName FROM
					(bevomedia_market_lead_posts LEFT JOIN bevomedia_market_providers ON bevomedia_market_lead_posts.providerId = bevomedia_market_providers.id)
					LEFT JOIN bevomedia_user_info ON bevomedia_market_lead_posts.user__id = bevomedia_user_info.user__id
					WHERE (bevomedia_market_lead_posts.leadId = ' . $intInleadId . ')
					ORDER BY postDate ASC';
		$this->Select($strSQL);
	}

}

?>
