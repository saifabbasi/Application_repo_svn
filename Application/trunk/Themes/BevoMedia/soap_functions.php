<?php
/** soap_functions.php
  * front-end functions for the new BevoMedia login section
  * @author Robert Augustin <robert@soapdesigned.com>
  
  * created 100717
  */
  
//yum cookiez
define('SOAP_COOK_TOPDROP', '__bevoTDR'); //(0|1) if true, the topdrop is open (expanded).

/** soap_topdrop_status
  * used in header.php to add classnames to div#topdroptop and a.topmenu_topdroptoggle,
  * in case the cookie tells us to have the topdrop open
  * @return CSS class name for open topdrop
  */
function soap_topdrop_status() {
	$class_open = 'active';
	$o = false;
	
	if(isset($_COOKIE[SOAP_COOK_TOPDROP]) && is_numeric($_COOKIE[SOAP_COOK_TOPDROP])) { //if cookie exists
		$cook = trim($_COOKIE[SOAP_COOK_TOPDROP]);
		if($cook == 1) { //if the cookie says "yep"
			$o = $class_open; //we have an output, and we refresh the cookie, unchanged
			setcookie(SOAP_COOK_TOPDROP, 1, time()+60*60*24*30*12, '/'); //1 year
		}
	}
	
	return $out = $o ? $o : false;
}
