<?php

class User extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_user', 'ID', array('EMAIL'));
	}

}

?>