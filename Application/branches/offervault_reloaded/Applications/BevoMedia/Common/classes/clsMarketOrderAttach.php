<?php

class MarketOrderAttachments extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_order_attachments', 'id', array('id', 'postId', 'fileName'));
	}
	
	function GetListByOrderID($intInOrderID) {
		$strSQL = 'SELECT bevomedia_market_order_attachments.id, postId, fileName FROM
					(bevomedia_market_order_attachments LEFT JOIN bevomedia_market_order_posts ON bevomedia_market_order_attachments.postId = bevomedia_market_order_posts.id)
					LEFT JOIN bevomedia_market_orders ON bevomedia_market_order_posts.orderId = bevomedia_market_orders.id
					WHERE (bevomedia_market_orders.id = ' . $intInOrderID . ')';
		$this->Select($strSQL);
	}

}

?>
