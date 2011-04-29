<?php /* it's soapdesigned.com */

/** AjaxPutContent
  * receives commands to write to DB from ajax via GET
  *
  */


/*set statics*/
$allowed_puts = array('save2list','createnewlist');
$query = array();
$out = array();
$error = false;

/*start script*/
if($_GET && isset($_GET['put']) && in_array($_GET['put'], $allowed_puts)) {
	
	$query['put'] = str_replace(array('\'','"'),'',htmlspecialchars(trim($_GET['put'])));

	switch($query['put']) {
		case 'createnewlist':
			if(isset($_GET['newlistname']) && $_GET['newlistname'] != '') {
				
				$val = preg_replace('/[^A-z0-9-_\.\,:\s]/','',trim($_GET['newlistname']));
				
				if(strlen($val) < 3 || strlen($val) > 55)
					$error = 'Sorry, but your list name has to be at least 3 characters short and can not be longer than 55 characters! Please try again.';
				else	$query['params']['newlistname'] = $val;					
			
			} else	$error = 'It seems that you haven\t specified a name for your list. Enter a name and try again!';
		break;
		
	}//end switch
	
	if($error) {
		$out['error'] = $error;
	
	} elseif(!empty($query['params'])) {
			
		//build request
		include_once dirname(__FILE__).'/Ovault_ParseAjaxInput_class.view.php';
		$parse = new ParseAjaxInput();
		
		$out = $parse->$query['put']($query);		
	}
}//endif GET

/*output*/
if(count($out) == 0) {
	$out['error'] = 'Oops, something went wrong! Please try again.';

} else {
	$out['input'] = http_build_query($query['params']);
	echo json_encode($out);
}

die(); //figuring this line out took a while.
