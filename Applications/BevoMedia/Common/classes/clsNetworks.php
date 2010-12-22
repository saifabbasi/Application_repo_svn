<?php

class Networks extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_aff_network', 'ID', array('title', 'detail', 'model', 'adminCommission', 'currentPayoutRate', 'email', 'im', 'url', 'signupUrl', 'paymentInfo', 'paymentOptions', 'userIdLabel', 'otherIdLabel', 'msgB4Apply', 'networkRequirements', 'adCodeSample', 'AdCodePopSample', 'W9Required', 'LastUpdated', 'isValid'));
	}

}

?>