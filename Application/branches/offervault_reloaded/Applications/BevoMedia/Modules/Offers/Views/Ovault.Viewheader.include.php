<?php	/*
	initial functions for the offervault. belongs in the header of every offervault view.
	*/

	//savelists
	//read db to find out if user already has list(s) or not
	//then read cookie to find out which one was the last one (if any)
	//set $OfferSaveList to list ID or "new" if no list exists yet. echo it for the js var currentSaveList.
	
	global $ovaultSavelist;
	$ovaultSavelist = array('cookie'=>'__bevoOLSL');	
	
	$TEMP = "CREATE TABLE IF NOT EXISTS bevomedia_user_offerlists(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user__id INT(10),
			name VARCHAR(255),
			created TIMESTAMP DEFAULT NOW()
			) TYPE=MyISAM
	"; //just for reference
	
	$TEMP = "CREATE TABLE IF NOT EXISTS bevomedia_user_offerlists_offers(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			offer__id INT(11),
			list__id INT(11),
			added TIMESTAMP DEFAULT NOW()
			) TYPE=MyISAM
	"; //just for reference
	
	function OvaultSaveListIni() {
		global $ovaultSavelist;
		
		$out = false;
		
		$sql = "SELECT 
				*
			FROM 
				bevomedia_user_offerlists
			WHERE 
				(bevomedia_user_offerlists.user__id = {$_SESSION['User']['ID']})
			ORDER BY
				id
			";
		$raw = mysql_query($sql);
		
		$ovaultSavelist['exist_num'] = mysql_num_rows($raw);
		
		if($ovaultSavelist['exist_num'] == 0) {
			$out = 'new';
			
		} else {
			$last = false;
			
			//check cook
			if(isset($_COOKIE[$ovaultSavelist['cookie']]) && is_numeric($_COOKIE[$ovaultSavelist['cookie']])) {
				$last = intval(trim($_COOKIE[$ovaultSavelist['cookie']]));
			}
			
			$ovaultSavelist['lists'] = array();
			$js = ''; //for the JS object: only id and name, with the id as the key
			
			while($obj = mysql_fetch_object($raw)) {
				
				//get no. of offers in list
				$sql = "SELECT 
						COUNT(*)
					FROM 
						bevomedia_user_offerlists_offers 
					WHERE 
						(bevomedia_user_offerlists_offers.list__id = {$obj->id})
					";					
				$offers = mysql_query($sql);				
				$obj->num_offers = mysql_fetch_row($offers);
				$obj->num_offers = $obj->num_offers[0];
				
				if($last && $obj->id == $last)
					$out = $last;
				
				$ovaultSavelist['lists'][$obj->id] = $obj; //make the id the key
				
				$js['n'.$obj->id]['id'] = $obj->id;
				$js['n'.$obj->id]['name'] = $obj->name;
			}
			
			if(!empty($ovaultSavelist['lists']) && (!$last || !isset($ovaultSavelist['lists'][$last]))) {//if we have lists but no cookie exists, use the last updated one and setcookie
				$item = end($ovaultSavelist['lists']); //just use the last list in line
				$out = $item->id;
			}
			
			if($out)
				setcookie($ovaultSavelist['cookie'], $out, time()+60*60*24*30*12, '/'); //1y
		}
		
		$out = $out ? $out : 'new';
		
		if(isset($js) && is_array($js) && !empty($js)) {
			$ovaultSavelist['js_obj'] = json_encode($js);
		} else	$ovaultSavelist['js_obj'] = '';
		
		return $out;
	}//OfferSaveListIni
	
	$ovaultSavelist['current'] = OvaultSaveListIni();
	//$ovaultSavelist['maxlists'] = $this->User->vaultID == 0 ? 1 : 10; //allowed number of lists: verfied users get more
	$ovaultSavelist['maxlists'] = 10; //10 for all.
	
?>

<link href="/Themes/BevoMedia/gritter.css" rel="stylesheet" type="text/css" />
<script src="/Themes/BevoMedia/jquery.gritter.js" type="text/javascript"></script>
<script type="text/javascript">
	var	ovault_cook_LastSaveList = '<?php echo $ovaultSavelist['cookie']; ?>',
		ovault_currentSavelist = '<?php echo $ovaultSavelist['current']; ?>', //if "new", no list exists yet and a new one will be created automatically when they save2list.
		ovault_existSavelistNum = '<?php echo $ovaultSavelist['exist_num']?>',
		ovault_maxSavelists = '<?php echo $ovaultSavelist['maxlists']?>',
		ovault_allSavelists = {}, //raw js obj of existing lists. gets updated on create/delete. used for savelist select in offer rows.
		
		ovault_orow_ignoreClick = false,
		
		ovault_ajaxGet = 'ajaxGetContent.html',
		ovault_ajaxPut = 'ajaxPutContent.html',
		ovault_searchPage = '/BevoMedia/Offers/Index.html',
		ovault_mysavedPage = '/BevoMedia/Offers/MySavedLists.html',
		
		ovault_cache = [],
		
		ovault_cook_LastSearch = '__bevoOLSearch',		
		
		ovault_cookSearch = soap_cookRead(ovault_cook_LastSearch),
		
		//current
		ovault_currentOid, //the current offer id that is being fetched for orowbig
		ovault_currentAdd2listSelectOid = false; //the current offer id that will get added to the selected list on ovault_add2list_select click in offer rows
	
	ovault_savelisttmp = '<?php echo $ovaultSavelist['js_obj']; ?>';
	if(ovault_savelisttmp != '')
		ovault_allSavelists = (ovault_savelisttmp);	
		
	ovault_cache.offerdetails = []; //index = the offer ID
	ovault_cache.searchresults = []; //index = the actual search string
	ovault_cache.current_searchstring = false; //the current search string, set after ajax success 
</script>
<script src="/Themes/BevoMedia/ovault.functions.js" type="text/javascript"></script>
<script src="/Themes/BevoMedia/ovault.general.js" type="text/javascript"></script>
