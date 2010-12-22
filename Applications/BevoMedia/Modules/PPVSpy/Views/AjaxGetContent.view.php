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
$allowed_pages = array('MostSeenPopups','MostSeenOffers','MostSeenNiches','SearchbyKeyword','SearchbyKeywordPaged','Similar','Poptargets','SuggestATarget');
$key='237f3f76g2f487gfiwvbbwwf7g34';
$apibase = 'http://distantsunmedia.com/api/';
$query = array('page'=>'','tab'=>'','params'=>array(),'paramstring'=>'');
$out = array();
$error = false;

/*start script*/
if($_GET && isset($_GET['page'])) {
	
	$page = htmlspecialchars(trim($_GET['page']));
	
	if($page != '' && in_array($page, $allowed_pages)) {

		switch($page) { //assign URL params for API thru token
			case 'MostSeenPopups' :
				$query['page'] = 'most-seen.php'; 	//api filename
				$query['tab'] = $page;			//unique tab name
				$query['params']['search_for'] = 'pops';
			break;
			case 'MostSeenOffers' :	
				$query['page'] = 'most-seen.php';
				$query['tab'] = $page;
				$query['params']['search_for'] = 'offers';
			break;
			case 'MostSeenNiches' :	
				$query['page'] = 'most-seen.php';
				$query['tab'] = $page;
				$query['params']['search_for'] = 'niches';
			break;
			case 'SearchbyKeyword' :
				$query['page'] = 'keyword-search.php';
				$query['tab'] = $page;
				$query['params']['q'] = strip_tags(trim($_GET['q']));
				$query['params']['search_type'] = is_numeric($_GET['search_type']) ? trim($_GET['search_type']) : 3;
			break;
			case 'SearchbyKeywordPaged' :
				$page = 'SearchbyKeyword'; //goes to same method as above
				
				$query['page'] = 'keyword-search.php';
				$query['tab'] = 'SearchbyKeyword';
				
				//clean up paramstring rel="q%3Da%26search_type%3D3"
				//$query['paramstring'] = urldecode(strip_tags(trim($_GET['paramstring']))); //for api
				$query['paramstring'] = $_GET['paramstring']; //for api
				$query['params']['search_type'] = is_numeric($_GET['search_type']) ? trim($_GET['search_type']) : 3; //for class, gets passed extra
			break;
			case 'Similar' :
				$query['page'] = 'similar.php';
				$query['tab'] = $page;
				$query['paramstring'] = strip_tags(trim($_GET['paramstring']));
			break;
			case 'Poptargets' : //list of target URLs in shadowbox iframe
				$query['page'] = 'similar.php';
				$query['tab'] = $page;
				$query['paramstring'] = strip_tags(trim($_GET['paramstring']));
			break;
			case 'SuggestATarget' :
				$query['page'] = 'suggest-target.php';				
				$target = strip_tags(trim($_GET['target']));
				
				if(strlen($target)<=3)
					$error = 'Your suggestion was not submitted because it was too short. Please try again!';
				else	$query['paramstring'] = 'target='.$target;
			break;
			
		}//end switch GET"page"
		
		if($error) {
			
			$out['error'] = $error;
		
		} elseif($query['page'] != '' && (!empty($query['params']) || $query['paramstring'] != '')) {
				
			//build request
			$xmlrequest = $apibase.$query['page'].'?';
			
			if($query['paramstring'] != '')
				$xmlrequest .= $query['paramstring'].'&';			
			else {
				foreach($query['params'] as $k => $v)
					$xmlrequest .= $k.'='.$v.'&';
			}
			
			$xmlrequest .= 'key='.$key;
			
			$xml = simplexml_load_file($xmlrequest);			
			
			/*if($page == 'SearchbyKeyword') {
				var_dump($_GET);
				die();
			//} //*/
			
			if($page == 'SuggestATarget') //out[error] should rather be named "message" since its not always an error... whatever
				$out['error'] = 'Thank you. Your target has been added to the queue. Please check back in 24 to 48 hours.';

			elseif($xml) {
				include_once dirname(__FILE__).'/PPVSpy.ConstructAjaxOutput.class.php'; //do this here to save resources in case any of the IF conditions above fail
				$construct = new ConstructAjaxOutput();
				
				$out = $construct->$page($query, $xml);
			
			}//endif xml response
		}//endif/else params & !error
	}//endif allowed page
}//endif GET

/*output*/
//if(!array_key_exists('html', $out) || !array_key_exists('error', $out))
if(count($out) == 0)
	$out['error'] = 'Oops, something went wrong! Please try again.';

elseif(array_key_exists('raw', $out))
	echo $out['raw'];

else	echo json_encode($out);

die(); //figuring this line out took a while.
