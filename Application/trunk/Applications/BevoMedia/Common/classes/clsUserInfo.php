<?php

class UserInfo extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_user_info', 'ID', array('CompanyName', 'FirstName', 'LastName', 'Address', 'City', 'State', 'Country', 'Zip'));
	}

}

?>