<?php /* it's soapdesigned.com */

/** AjaxPutContent
  * receives commands to write to DB from ajax via GET
  *
  */


/*set statics*/
$allowed_puts = array('save2list','createnewlist','deletelist','deletealllists','deletesavelistoffer','renamelist');
$query = array();
$out = array();
$error = false;

/*start script*/
if($_GET && isset($_GET['put']) && in_array($_GET['put'], $allowed_puts)) {
	
	$query['put'] = str_replace(array('\'','"'),'',htmlspecialchars(trim($_GET['put'])));

	switch($query['put']) {
		case 'save2list' :
			foreach(array('list','oid') as $k) {
				if(isset($_GET[$k]) && is_numeric($_GET[$k]) && $_GET[$k] != 0)
					$query['params'][$k] = intval(trim($_GET[$k]));
				else	$error = 'There seems to be an error with the offer or list you\'re trying to add. Please try again and let us know if the error persists!';
			}
		break;
		case 'createnewlist' :
			if(isset($_GET['newlistname']) && $_GET['newlistname'] != '') {
				
				$val = preg_replace('/[^A-z0-9-_\.\,:\s]/','',trim($_GET['newlistname']));
				
				if(strlen($val) < 3 || strlen($val) > 55)
					$error = 'Sorry, but your list name has to be at least 3 characters short and can not be longer than 55 characters! Please try again.';
				else	$query['params']['newlistname'] = $val;
			
			} else	$error = 'It seems that you haven\t specified a name for your list. Enter a name and try again!';
		break;
		case 'deletelist' :
			if(isset($_GET['list']) && $_GET['list'] != '' && is_numeric($_GET['list'])) {
				$query['params']['listid'] = intval(trim($_GET['list']));
				
			} else	$error = 'The list you want to delete doesn\'t seem to exist! Please let us know if the error persists.';
		break;
		case 'deletealllists' :
			$query['params']['dummy'] = 1;
		break;
		case 'deletesavelistoffer' :
			foreach(array('oid','listid') as $k) {
				if(isset($_GET[$k]) && is_numeric($_GET[$k]) && $_GET[$k] != 0)
					$query['params'][$k] = intval(trim($_GET[$k]));
				else	$error = 'The offer you want to remove from the list doesn\'t seem to exist in the list, or invalid list selected. Please refresh the page and try again, and let us know if the error persists!';
			}
			
		break;
		case 'renamelist' :
			if(isset($_GET['listid']) && is_numeric($_GET['listid']) && $_GET['listid'] != 0)
				$query['params']['listid'] = intval(trim($_GET['listid']));
			else	$error = 'This list seems to be invalid, please refresh the page and try again.';
			
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
	echo json_encode($out);
}

die(); //figuring this line out took a while.
