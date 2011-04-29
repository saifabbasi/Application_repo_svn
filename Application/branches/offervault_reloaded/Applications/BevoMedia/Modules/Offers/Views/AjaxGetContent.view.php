<?php /* it's soapdesigned.com */

/** NOT AN OUTPUT PAGE!
  * This script accepts GET params via Ajax, fetches the XML object from PPVSpy via API, and returns the correct content.
  * It should never be loaded in the browser directly.
  *
  * Todo at a later date since this file is loaded thru Ajax:
  *	To prevent abuse, load session & app header and check if user is logged in and verified (and is allowed to use this feature)
  *	Maybe keep the overhead as small as possible and not load the entire app header with all classes.
  *	There is currently NO PROTECTION on this page!
  *
  */

  
/* ########################## load session and add user check here ############################## */


/*set statics*/
//array of the allowed GET['page'] params. param is added in ajax: it's a token for the API's page name.
$allowed_gets = array('orowbig', 'searchresults');
$query = array();
$out = array();
$error = false;

/*start script*/
if($_GET && isset($_GET['get']) && in_array($_GET['get'], $allowed_gets)) {
	
	$query['get'] = str_replace(array('\'','"'),'',htmlspecialchars(trim($_GET['get'])));

	switch($query['get']) {
		case 'orowbig' : //tr.orowbig for offer details
			if(isset($_GET['oid']) && !empty($_GET['oid']) && is_numeric($_GET['oid']))
				$query['params']['oid'] = intval(trim($_GET['oid']));
			else	$error = 'The offer you have requested doesn\'t seem to exist. Please try again!';
		break;
		
		case 'searchresults' :
			$required_keys = array('search','type','include_networks');
			
			foreach($_GET as $key => $value) {
				
				$f = str_replace(array('\'','"'), '', strip_tags(trim($_GET[$key])));
				
				if($key == 'include_networks') {
					$terms = explode(',', $f);
					$goodterms = array();
					foreach($terms as $term) {
						if(trim($term) != '')
							$goodterms[] = trim(intval($term));
					}
					$goodterms = array_unique($goodterms);
					$f = implode(',',$goodterms);
			
				} elseif($key == 'numresults' || $key == 'page' || $key == 'newpage') {
					$tmp = intval($_GET[$key]); 
					$f = $tmp > 0 ? $tmp : '';
				
				}
				
				if($f != '' || !empty($f))
					$query['params'][$key] = $f;
				
				elseif(in_array($key, $required_keys)) {
					$error = 'You have either entered an invalid search term, or a required option is missing. Please try again!';
					break;
				}
			}
			
			//newpage overrides/resets page
			if(array_key_exists('newpage', $query['params']) && $query['params']['newpage'] != '') {
				$query['params']['page'] = $query['params']['newpage'];
				unset($query['params']['newpage']);
			}
			
		break;
		
	}//end switch
	
	if($error) {
		$out['error'] = $error;
	
	} elseif(!empty($query['params'])) {
			
		//build request
		include_once dirname(__FILE__).'/Ovault_ConstructAjaxOutput_class.view.php';
		$construct = new ConstructAjaxOutput();
		
		$out = $construct->$query['get']($query);		
	}
}//endif GET

/*output*/
//if(!array_key_exists('html', $out) || !array_key_exists('error', $out))
if(count($out) == 0) {
	$out['error'] = 'Oops, something went wrong! Please try again.';

//} elseif(array_key_exists('html', $out)) {
//	echo $out['html'];

} else {
	//construct sanitized search string and add it
	$out['searchstring'] = http_build_query($query['params']);
	$out['get_raw'] = '<pre>'.print_r($_GET, true).'</pre>';
	
	/*if($query['get'] == 'orowbig') {
		//echo '<pre>';
		var_dump($out['resultarr']);
		//echo '</pre>';
		die();
	}*/
	echo json_encode($out);
}

die(); //figuring this line out took a while.
