<?php

class MarketOrderPosts extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_order_posts', 'id', array('orderId', 'user__id', 'providerId', 'post', 'oostDate'));
	}
	
	function GetListByOrderID($intInOrderID) {
		$strSQL = 'SELECT bevomedia_market_order_posts.id, bevomedia_market_order_posts.user__id, providerId, post, postDate, bevomedia_market_providers.name AS ProviderName, bevomedia_user_info.company_Name AS UserName FROM
					(bevomedia_market_order_posts LEFT JOIN bevomedia_market_providers ON bevomedia_market_order_posts.providerId = bevomedia_market_providers.id)
					LEFT JOIN bevomedia_user_info ON bevomedia_market_order_posts.userId = bevomedia_user_info.user__id
					WHERE (bevomedia_market_order_posts.orderId = ' . $intInOrderID . ')
					ORDER BY postDate ASC';
		$this->Select($strSQL);
	}

}

?>
