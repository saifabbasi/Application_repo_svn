<?php /* it's soapdesigned.com */

/** Builds HTML output for the PPV Spy feature.
  */

class ConstructAjaxOutput {
	
	/*
	static $SelfHosted;
	
	function __construct() {
		//self::$SelfHosted = false;
		
		//if(Zend_Registry::get('Application/Mode') == 'SelfHosted') 
			self::$SelfHosted = Zend_Registry::get('Application/Mode');
		
	}*/
	
	/** orowbig()
	  * Offer details for table
	  * @return html (tr.orowbig)
	  */
	public function orowbig($query=false) {
		
		//fetch offer from db HERE using the following params:
		//$query['params']['oid'] (the offer ID)
		
		require_once(Zend_Registry::get('Application/TrueWorkingDirectory') .'Applications/'. Zend_Registry::get('Instance/Application') .'/Common/PageHelper.class.php');
		$PageHelper = new PageHelper();
		
		$offerID = intval($query['params']['oid']);		
				
		$sql = "SELECT	offers.*,
				category.title as `categoryTitle`,
				network.title as `networkName`,
				network.detail as `networkDescription`,
				network.offerUrl as `network_affUrl`
			FROM
				bevomedia_offers AS offers
				LEFT JOIN bevomedia_category AS category
					ON category.id = offers.category__id
				LEFT JOIN bevomedia_aff_network AS network
					ON network.id = offers.network__id
			WHERE
				offers.id = {$offerID}
		";
		
		$data = mysql_query($sql);
		
		while ($offer = mysql_fetch_object($data)) { 
			
			$sql = "SELECT 
					id
				FROM 
					bevomedia_user_aff_network 
				WHERE 
					(bevomedia_user_aff_network.network__id = {$offer->network__id}) AND
					(bevomedia_user_aff_network.user__id = {$_SESSION['User']['ID']})
				";
			$isMemberOfNetwork = mysql_query($sql);
			$offer->isNetworkMember = (mysql_num_rows($isMemberOfNetwork))?1:0;
			
			//rating
			$offer->userRating = $PageHelper->FixNetworkRating($offer->networkName);
			
			if($offer->userRating == false) {
				$sql = "SELECT	rating
					FROM	bevomedia_user_aff_network_rating
					WHERE	bevomedia_user_aff_network_rating.network__id = {$offer->network__id}			
				";
				$userRating = mysql_query($sql);
				
				if(mysql_num_rows($userRating) > 0) {
					$userRating = mysql_fetch_object($userRating);
					$offer->userRating = $userRating->rating;
					
				} else {
					$offer->userRating = 0;
				}
			}//rating
			
			//if not the slim version (like on the mysavedlists page), get user comments
			$offer->is_oright = false;
			if(!isset($query['params']['is_oright']) || $query['params']['is_oright'] != 1) {
			
				$sql = "SELECT
						*
					FROM
						bevomedia_user_aff_network_rating
					WHERE
						(bevomedia_user_aff_network_rating.network__id = {$offer->network__id}) AND
						(bevomedia_user_aff_network_rating.approved = 1) AND
						(bevomedia_user_aff_network_rating.userComment != '')
					ORDER BY 
						bevomedia_user_aff_network_rating.id DESC
					LIMIT 3
					";
				$ratings = mysql_query($sql);
				
				$offer->ratings = array();
				if(mysql_num_rows($ratings)>0) {
					while ($rating = mysql_fetch_object($ratings)) {
						$offer->ratings[] = $rating;
					}
				} 
			} else {
				$offer->is_oright = true;
				
			}//endif is_oright
			
			//aff link
			$affId = false;
			$offer->affUrl = false;
			$offer->affUrlNotice = false; //fill with notice in case we show a placeholder in their aff URL
			
			if($offer->isNetworkMember == 1 && ($offer->affiliateUrl || $offer->network_affUrl)) {
				
				$sql = "SELECT 	userIdLabel,
						otherIdLabel
					FROM	bevomedia_aff_network 
					WHERE	id = $offer->network__id
					LIMIT 1
				";
				$labelresult = mysql_query($sql);
				while($field = mysql_fetch_object($labelresult)) {
					
					$affField = false;
					
					if($field->userIdLabel == 'Affiliate ID' || $field->userIdLabel == 'Account ID')
						$affField = 'loginId';
					elseif($field->otherIdLabel == 'Affiliate ID' || $field->otherIdLabel == 'Account ID')
						$affField = 'otherId';
					
					if($affField) {						
						//get affiliate ID
						$sql = "SELECT `$affField`
							FROM	bevomedia_user_aff_network
							WHERE	user__id = {$_SESSION['User']['ID']}
							AND	network__id = {$offer->network__id}
							LIMIT 	1
						";
						$affresult = mysql_query($sql);
						if(mysql_num_rows($affresult) == 1) {
							while($affId = mysql_fetch_object($affresult)) {
								$affId = $affId->$affField;
								break;
							}
						}
					}
				}//endwhile labelresult
				
				//if we dont have an affId, use placeholder and tell them
				if(!$affId) {
					$affId = '{YOUR_AFFID}';
					$offer->affUrlNotice = 'Replace <strong>'.$affId.'</strong> with your affiliate ID for '.$offer->networkName.'!';
				}
				
				//get urlstructure: see if offer->affiliateUrl exists, else use networks.offerUrl
				if($offer->affiliateUrl && $offer->affiliateUrl != '') 
					$urlstructure = $offer->affiliateUrl;
				else	$urlstructure = $offer->network_affUrl;	
				
				//replace placeholders
				$offer->affUrl = str_replace(array('{$OfferID}','{$AffiliateID}'), array($offer->offer__id, $affId), $urlstructure);
				
			}//endif affurl
			
			$offer->dateAdded = self::formatDate($offer->dateAdded);
			
			break;
			
		}//endwhile $offer
		
		$out['html'] = self::MakeOrowbig($offer);
		
		return $out;
		
	}//orowbig()
	
	/** searchresults()
	  * search for offers using the dial
	  * @return offers array
	  */
	public function searchresults($query=false) {
		
		//fetch offers from db HERE using the following params:
		//$query['params']['search'] (the search term. search in both OFFER NAME and VERTICAL NAME! to retrieve accurate results, might have to split the search term by space and search for single words.)
		//$query['params']['type'] (can be either "lead", or "sale", or "lead,sale". split the 3rd one by comma and do XOR in the query)
		//$query['params']['include_mysaved'] (1 or 0. if 0, add something like "offers.id NOT IN {db table that stores saved offers for this user}" to the query) (ignore this for now)
		//$query['params']['include_networks'] (is a comma-separated list of at least 1 netword ID. search only in offers from these networks, or ALL
		
		
		//$query['params']['type'] ?? how are we going to get this
		//$query['params']['include_mysaved'] how are we going to save the offers in this table?
		
		$userid = $_SESSION['User']['ID'];
		$out = array();	
		
		/*search*/
		$searchAdd = '';
		if(isset($query['params']['search']) && $query['params']['search'] != '*') {
			
			$terms = explode(' ', $query['params']['search']);			
			
			foreach($terms as $term) {
				if (trim($term)=='') continue;
				
				$term = trim($term);
				
				$searchAdd .= " (bevomedia_offers.title LIKE '%{$term}%') OR ";
				$searchAdd .= " (bevomedia_offers.detail LIKE '%{$term}%') OR ";
			}
			
			if (strlen($searchAdd)>1) {
				$searchAdd = ' AND ('.rtrim($searchAdd, 'OR ').' )';
			} else {
				$searchAdd = '';
			}
			
		} else { //if no search param or star
			
			//allow only if 1 network is selected
			if(strstr(',', $query['params']['include_networks']) === false && is_numeric($query['params']['include_networks'])) {
				
				$searchAdd = '';
				$out['message_once'] = 'Showing all offers from this network! Use * or do a blank search with only 1 network selected to find all of its offers.'; //message_once = uses JS cookie to only show once
			
			} else {
				$out['error'] = 'The * or empty operator only works when you have a single network selected. Please deselect all networks <em>except for one</em> in the Search Sphere, and try again!';
			}
		
		}//end search
		
		
		/*networks*/
		$networksSearchAdd = '';
		if(isset($query['params']['include_networks'])) {
		
			if($query['params']['include_networks'] == 'ALL') {
				//exclude clickbank (1040)
				$networksSearchAdd = " AND (bevomedia_offers.network__id <> '1040')";
				
			} else {			
				$terms = explode(',', $query['params']['include_networks']);
			
				foreach($terms as $term) {
					$term = intval(trim($term));					
					$networksSearchAdd .= " (bevomedia_offers.network__id = {$term} ) OR ";
				}
				
				if (strlen($networksSearchAdd) > 1)
					$networksSearchAdd = ' AND ('.rtrim($networksSearchAdd, 'OR ').' )';
				else 	$networksSearchAdd = '';
			}
			
		}//end networks
		
		
		/*mysaved*/
		$savelistAdd = '';		
		if(!isset($query['params']['include_mysaved']) || $query['params']['include_mysaved'] == 0) {
			
			$savedoffers = array();
			
			$sql = "SELECT offers.offer__id
				FROM bevomedia_user_offerlists_offers AS offers
					LEFT JOIN bevomedia_user_offerlists AS lists
						ON offers.list__id = lists.id
				WHERE lists.user__id = {$userid}
			";
			
			$raw = mysql_query($sql);
			while($saved = mysql_fetch_object($raw)) {
				$savedoffers[] = $saved->offer__id;
			}
			
			if(!empty($savedoffers))
				$savelistAdd = " AND (bevomedia_offers.id NOT IN (".implode(', ', $savedoffers).")) ";
		}
				
		/*limit, range*/
		if(!isset($query['params']['page'])) //do this here - not required on the front
			$query['params']['page'] = 1;
		
		$numresults = isset($query['params']['numresults']) && $query['params']['numresults'] ? $query['params']['numresults'] : 100;
		$numfrom = $query['params']['page'] > 1 ? ($query['params']['page'] - 1) * $numresults : 0;
			
		$limitAdd = " LIMIT $numfrom, $numresults";
		
		
		/*fetch*/
		if(!isset($out['error'])) :
		
			$sql = "SELECT SQL_CALC_FOUND_ROWS
						bevomedia_offers.*,
						bevomedia_category.title as `categoryTitle`,
						bevomedia_aff_network.title as `networkName`
					FROM
						bevomedia_offers
						LEFT JOIN bevomedia_category ON (bevomedia_category.id = bevomedia_offers.category__id),
						bevomedia_aff_network
					WHERE
						(bevomedia_aff_network.id = bevomedia_offers.network__id) AND
						bevomedia_offers.archived = 0
						{$searchAdd}
						{$networksSearchAdd}
						{$savelistAdd}
						AND bevomedia_offers.archived = 0
					ORDER BY 
						bevomedia_offers.payout DESC
						{$limitAdd}
						
			";//die($sql);
				
			$data = mysql_query($sql);
			
			//get total results
			$sqlcount = "SELECT FOUND_ROWS() AS `found_rows`";
			$countresults = mysql_query($sqlcount);
			$countresults = mysql_fetch_object($countresults);
			$out['totalresults'] = $countresults->found_rows;
			
			if($out['totalresults'] == 0)
				$out['message'] = 'Nothing found for this query! Try widening your search terms, or include more networks.'; //overwrites any prev msgs
			
			elseif($out['totalresults'] > 400 && !isset($out['message']) && isset($query['params']['search']) && $query['params']['search'] != '*') //only if we dont have a msg yet
				$out['message'] = $out['totalresults'].' Offers were found for this query! If you want to narrow down the results, try adding more search terms, or select a fewer number of networks.';
			
			/*fetch additional data*/
			$offersArray = array();
			while($offer = mysql_fetch_object($data)) {
				
				/*isNetworkMember*/
				$sql = "SELECT 
						id
					FROM 
						bevomedia_user_aff_network 
					WHERE 
						(bevomedia_user_aff_network.network__id = {$offer->network__id}) AND
						(bevomedia_user_aff_network.user__id = {$_SESSION['User']['ID']})
				";
				$isMemberOfNetwork = mysql_query($sql);
				$offer->isNetworkMember = (mysql_num_rows($isMemberOfNetwork))?1:0;
				
				/*mysaved icon*/
				if(isset($query['params']['include_mysaved']) && $query['params']['include_mysaved'] == 1) {
					
					$savedoffers = array();
					
					$sql = "SELECT offers.offer__id
						FROM bevomedia_user_offerlists_offers AS offers
							LEFT JOIN bevomedia_user_offerlists AS lists
								ON offers.list__id = lists.id
						WHERE offers.offer__id = {$offer->id}
							AND lists.user__id = {$userid}
						LIMIT 1
					";
					
					$offerIsSaved = mysql_query($sql);
					$offer->saved2list = (mysql_num_rows($offerIsSaved)) ? 1 : 0;
				}//end mysaved
				
				/*formatting*/
				//special chars in name
				//$offersArray[$key]->title = htmlentities($offer->title, ENT_QUOTES, 'UTF-8'); //turns errors into null values
				//$offersArray[$key]->title = str_replace('£','&pound;', $offer->title); //doesnt work either...
				
				$offer->dateAdded = self::formatDate($offer->dateAdded);
				$offer->payout = self::formatPayout($offer->payout);
				
				if($offer->categoryTitle == NULL)
					$offer->categoryTitle = '';
					
				if($offer->offerType == '')
					$offer->offerType = 'Lead';
				
				/*add*/
				$offersArray[] = $offer;
				
			}//endwhile offer
						
			/*pagination*/
			$out['pagination'] = self::MakePagination($out['totalresults'], $numresults, $query['params']['page']);
			$out['resultarr'] = $offersArray;
		
		endif; //!$out['error']
		
		if(empty($out))
			$out['error'] = 'Something went wrong, couldn\'t retrieve any offers. Try modifying your search parameters.';
		
		return $out;
		
	}//searchresults()
	
	/** savelistoffers()
	  * fetches offers that a user saved in a savelist
	  * @return offers array
	  */
	public function savelistoffers($query=false) {
		$out = array();
		
		$sql = "SELECT	offers.id,
				offers.title,
				offers.detail,
				offers.payout,
				offers.offerType AS type,
				offers.dateAdded,
				offers.network__id AS network__id,
				
				networks.title AS networkName,				
				category.title AS categoryTitle
				
			FROM 	bevomedia_offers AS offers
				
				LEFT JOIN bevomedia_aff_network AS networks
					ON networks.id = offers.network__id
					
				LEFT JOIN bevomedia_category  AS category
					ON category.id = offers.category__id
				
				LEFT JOIN bevomedia_user_offerlists_offers AS listoffers
					ON offers.id = listoffers.offer__id
				
				LEFT JOIN bevomedia_user_offerlists AS lists
					ON listoffers.list__id = lists.id
				
			WHERE	lists.id = {$query['params']['listid']}
				AND lists.user__id = {$_SESSION['User']['ID']}
				AND offers.archived = 0
				
			GROUP BY offers.id
			ORDER BY offers.payout
		";
		
		$raw = mysql_query($sql);
		
		$offersArray = array();
		if(mysql_num_rows($raw) > 0) {
			while($offer = mysql_fetch_object($raw)) {				
				
				$sql = "SELECT 
							id
						FROM 
							bevomedia_user_aff_network 
						WHERE 
							(bevomedia_user_aff_network.network__id = {$offer->network__id}) AND
							(bevomedia_user_aff_network.user__id = {$_SESSION['User']['ID']})
						";
				$isMemberOfNetwork = mysql_query($sql);
				$offer->isNetworkMember = (mysql_num_rows($isMemberOfNetwork)) ? 1 : 0;
				
				//date
				//$offer->dateAdded_nice = date('M j, Y', strtotime($offer->dateAdded));
				$offer->dateAdded = self::formatDate($offer->dateAdded);
				$offer->payout = self::formatPayout($offer->payout);
				
				if($offer->type == '')
					$offer->type = 'Lead';
						
				$offersArray[] = $offer;
			}//endwhile
			
		}//endif results
		
		$out['resultarr'] = $offersArray;
					
		return $out;
		
	}//savelistoffers()
	
	
	/*private methods*/
	
	/** MakePagination()
	  * constructs HTML
	  */
	private function MakePagination($totalresults=0, $numresults=100, $currentpage=1) {
		if($totalresults > $numresults) {
			$totalpages = ceil($totalresults/$numresults);
			
			//pattern: 
			//	we have 14 slots (besides first and last)
			//	if there is enough room, we always try to place the current page in the 7th slot (not counting First)
			//
			//	we always show 4 single increments/decrements on each side of the current page, that makes 7 slots. 14-8 = 6 remaining slots min.
			//		(if currentpage has less than 4 singles left on either side, there are more remaining slots)
			//	after the singles, we increment by 10 for each following slot. E.g. if the last "single" slot on the right side was 14, it's 24, 34, 44, 54, 64, etc. Same for decrements.
			
			/*
			<a class="first" href="#">First</a>
			<a class="n2" href="#">2</a>
			<a class="n3" href="#">3</a>
			<a class="n4" href="#">4</a>
			<a class="n5" href="#">5</a>
			<a class="n6" href="#">6</a>
			<a class="n7" href="#">7</a>
			<a class="n8" href="#">8</a>
			<a class="n9" href="#">9</a>
			<a class="n10" href="#">10</a>
			<a class="n11" href="#">11</a>
			<a class="n12" href="#">12</a>
			<a class="n13" href="#">13</a>
			<a class="n14" href="#">14</a>
			<a class="n15" href="#">15</a>
			<a class="last" href="#">Last</a>
			
			<a class="btn j_prevnext ovault_opagi_prev" href="#">Previous Page</a>
			<a class="btn j_prevnext ovault_opagi_next" href="#">Next Page</a>
			*/
			
			//start with currentpage
			$o = array(); //array with values = page numbers. fill with more than 14 slots; slice and remap to a.class 2 thru 15 later.
			
			if($currentpage > 1 && $currentpage < $totalpages) //if currentpage is somewhere in-between first and last
				$o[] = $currentpage;
			
			//singles left
			if($currentpage > 1) {
				for($i=$currentpage-1; $i>=$currentpage-4; $i--) {
					if($i > 1) {
						$o[] = $i;
					}
				}
			}
			
			//singles right
			if($currentpage < $totalpages) {
				for($i=$currentpage+1; $i<=$currentpage+4; $i++) {
					if($i < $totalpages) {
						$o[] = $i;
					}
				}
			}
			
			//fill right
			$remainingSlots = 14-count($o); //can be anywhere between 5 and 9
			$smallestExistingSlot = !empty($o) ? min($o) : 1;
			$largestExistingSlot = !empty($o) ? max($o) : 1;
			
			if($largestExistingSlot < $totalpages) {
				$next = $largestExistingSlot;
				
				for($i=1; $i<=14; $i++) {
					
					if($totalpages-$largestExistingSlot >= $remainingSlots*10) //if we have slots to fill and increments of 10 are possible
						$nextIncrement = 10;
					else 	$nextIncrement = 1; //else fill with single increments
					
					$next = $next+$nextIncrement;
					
					if($next < $totalpages) {
						$o[] = $next;
					}
				}
			}			
			
			//same for left
			if($smallestExistingSlot > 1) {
				$next = $smallestExistingSlot;
				
				for($i=1; $i<=14; $i++) {
					if($smallestExistingSlot-$remainingSlots*10 > 1) //if we have slots to fill and 10 increments of 10 are possible
						$nextIncrement = 10;
					else 	$nextIncrement = 1; //else fill with single increments
					
					$next = $next-$nextIncrement;
					
					if($next > 1) {
						$o[] = $next;
					}
				}
			}
			
			//now sort
			sort($o);
			$o = array_unique($o);
			
			$slotsLeft = array_search($currentpage, $o);
			$slotsRight = count($o)-$slotsLeft-1; //minus one for the current page
			
			//get the lowest slot on the left that we can use so that we ideally have 6 on left and 7 on right of current
			$offset = $slotsLeft >= 6 ? $slotsLeft-6 : 0; //max. 6 slots on the left, otherwise we start at the first one
			
			$final = array_slice($o, $offset, 14);
			
			//finally build the markup
			$out = '';
			for($i=0; $i<=count($final)-1; $i++) {
				$num = $i+2;
				$out .= '<a class="j_num n'.$num;
					$out .= $final[$i] == $currentpage ? ' active' : '';
				$out .= '" href="#" data-page="'.$final[$i].'" title="Page '.$final[$i].'">'.$final[$i].'</a>';
			}
			
			//first and last
			$first = '<a class="j_num first';
				$first .= $currentpage == 1 ? ' active' : '';
				$first .= '" href="#" data-page="1" title="Page 1">First</a>';			
			
			$last = '<a class="j_num ';
				if(count($final) == 14)
					$last .= 'last';
				else {
					$number = count($final)+2;
					$last .= 'n'.$number;
				}
				//$last .= count($final) == 14 ? 'last' : 'n'.count($final)+2;
				$last .= $currentpage == $totalpages ? ' active' : '';
				$last .= '" href="#" data-page="'.$totalpages.'" title="Page '.$totalpages.'">Last</a>';
				
			//prev and next
			$prevnext = '';
			$prev = $currentpage-1;
			$next = $currentpage+1;
			
			if($currentpage > 1) //prev
				$prevnext .= '<a class="btn j_prevnext ovault_opagi_prev" href="#" data-page="'.$prev.'" title="Page '.$prev.'">Previous Page</a>';
			if($next <= $totalpages) //next
				$prevnext .= '<a class="btn j_prevnext ovault_opagi_next" href="#" data-page="'.$next.'" title="Page '.$next.'">Next Page</a>';
				
			$out = $first.$out.$last.$prevnext;
		
		} else	$out = false;
		
		return $out;
	
	}//MakePagination()
	
	/** MakeOrowbig()
	  * constructs HTML for the offer details box
	  */
	private function MakeOrowbig($offer) {
		/*if(!$offer)
			$out = false;
		else {*/
		
			$out = '<tr class="orowbig j_oid-'.$offer->id.' hide" data-oid="'.$offer->id.'">';
				$out .= '<td class="border">&nbsp;</td>';
				$out .= '<td class="td_info" colspan="';
					$out .= $offer->is_oright ? '4' : '3'; //if oright, we have a slightly different layout
				$out .= '"><div class="td_inner">';
				$out .= '<div class="floatleft">';
				
				//have image url?
				if(property_exists($offer, 'imageUrl') && $offer->imageUrl && $offer->imageUrl != '')
					$imageTag = '<img src="'.$offer->imageUrl.'" alt="" />';
				else	$imageTag = '<img src="/Themes/BevoMedia/img_new/othumb_default.gif" alt="" />';
				
				//have preview URL?
				if(property_exists($offer, 'previewUrl') && $offer->previewUrl && $offer->previewUrl != '') {
					$out .= '<a class="ovault_othumb" href="'.$offer->previewUrl.'" title="Preview offer in a new tab" target="_blank">';
					$out .= $imageTag;
					$out .= '<span class="btn ovault_visiticon_transyell"></span>
						</a>';
					
				} else {
					$out .= '<div class="ovault_othumb">';
						$out .= $imageTag;
					$out .= '</div>';				
				}
				
				//aff link
				$out .= '<div class="olinkbox">';
					if($offer->affUrl) {
						$out .= '<div class="otitle otitle_olink noborder"></div>';
						$out .= $offer->affUrlNotice ? '' : '<a class="btn ovault_transgray_testit_link" href="'.$offer->affUrl.'" target="_blank" title="Test your Affilate URL (opens in a new tab)">Test Link</a>'; //only show when we dont have a notice = when we have an affID
						$out .= '<textarea class="formtxtarea j_hiliteall" rows="1" cols="1" readonly>'.$offer->affUrl.'</textarea>';
						$out .= $offer->affUrlNotice ? '<p class="affurlnotice">'.$offer->affUrlNotice.'</p>' : '';
						$out .= '<p class="disclaimer">Note: Make sure your link works! If it does\'t, it means that this offer requires network approval before running.</p>';
					
					} elseif($offer->isNetworkMember == 1) {
						$out .= '<p>You can find this offer in '.$offer->networkName.'\'s interface by searching for the <strong>Offer ID</strong> (to the right).</p>';
						
					} else {
						$out .= '<p>We\'d love to give you your Affiliate Link for this offer right now, but you\'re not a member of '.$offer->networkName.' yet! <a class="nw_applyadd" href="/BevoMedia/Publisher/ApplyAdd.html?network='.$offer->network__id.'"><strong>Click Here</strong> to apply now</a>.</p>';
						
					}
				$out .= '</div>';
				
				//cake import
				/*
				if(property_exists($offer, 'importUrl') && $offer->importUrl && $offer->importUrl != '') {
					$out .= '<a class="btn ovault_importoffer" href="'.$offer->importUrl.'">Import this offer into my network</a>';
				}*/
				
				$out .= '<div class="clear"></div></div>';
				
				$out .= '<div class="floatright">';
				$out .= '<h3>'.$offer->title.'</h3>';
				$out .= $offer->dateAdded ? '<small>'.$offer->dateAdded.'</small>' : '&nbsp;';
				
				$out .= '<div class="otitle otitle_offerdesc"></div>';
				$out .= '<p>'.$offer->detail.'</p>';
				
				//offer ID
				//$out .= '<div class="onwidbox">'; //distinguish btw oid (bevo offer id) and onwid (network's offer id)
					$out .= '<div class="otitle otitle_onwid noborder"></div>
						<div class="clear"></div>
						<input type="text" class="formtxt onwid j_hiliteall" readonly value="'.$offer->offer__id.'" />
						<p class="nolineheight">This offer\'s ID in '.$offer->networkName.'\'s interface</p>
						<div class="clear"></div>';
				//$out .= '</div>';	
				
				//olink
				/*if(property_exists($offer, 'affUrl') && $offer->affUrl) {
					$out .= '<div class="olink">';
						$out .= '<input type="text" class="formtxt" readonly value="'.$offer->affUrl.'" />';
						$out .= '<a class="btn ovault_visiticon" href="'.$offer->affUrl.'" title="Click to test your affiliate link (opens in new tab)" target="_blank">Visit</a>';
					$out .= '</div>';
				}*/
				
			$out .= '</div><div class="clear"></div></div><!--close td_inner-->';
			$out .= '</td>';
			
			$out .= '<td class="td_nw" colspan="';
				$out .= $offer->is_oright ? '3' : '2'; //if oright, 3 cols
			$out .= '"><div class="td_inner"><div class="otitle otitle_network noborder"></div>';
			$out .= '<div class="onwpic">';
				$out .= '<img class="nwpic w120" src="/Themes/BevoMedia/img/networklogos/uni/'.$offer->network__id.'.png" alt="'.$offer->networkName.'" />';
			
			//rating stars (just show, dont allow to rate)
			$out .= '<p class="bordertop aligncenter">Publisher\'s Rating: ';
			$out .= $offer->userRating > 0 ? $offer->userRating.' / 5' : '';
			$out .= '<br />';
			
			for($i=1; $i<=5; $i++) {
				$ratingstate = $offer->userRating >= $i ? 'on' : 'off'; 		
				$out .= '<img src="/Themes/BevoMedia/img/star-'.$ratingstate.'.gif" align="absbottom" />';
			}
			
			$out .= '</p>';
			
		$out .= '</div><!--close div.onwpic-->';
		
		//is member of nw or not
		if($offer->isNetworkMember == 1) {
			$out .= '<p>You\'re already a member of this network!</p>';
			$out .= '<div class="icon icon_ovault_nwmember_bigwhite"></div>';
			$out .= '<a class="btn ovault_gotomystats_trans" href="/BevoMedia/Offers/MyStats.html?network='.$offer->network__id.'">Go to my stats</a>';
		
		} else {
			$out .= '<p>You\'re not a member of this network yet! Become one now:</p>';
			//$out .= '<a class="btn nw_applyadd" href="/BevoMedia/Publisher/ApplyAdd.html?network='.$offer->network__id.'" rel="shadowbox;width=640;height=480;player=iframe">Apply to join this network</a>';
			$out .= '<a class="btn nw_applyadd" href="/BevoMedia/Publisher/ApplyAdd.html?network='.$offer->network__id.'">Apply to join this network</a>';
		}
		
		$out .= '</div><!--close td_inner-->';
	$out .= '</td>';
	
	if(!$offer->is_oright) { //only in search results
	
		$out .= '<td class="td_nwdesc" colspan="3">';
			$out .= '<div class="td_inner">';
	
			//offer description
			if(property_exists($offer, 'networkDescription') && $offer->networkDescription && $offer->networkDescription != '') {
				$out .= '<div class="otitle otitle_networkdesc"></div>';
				$out .= '<p>'.$offer->networkDescription.'</p>';
			}
			
			//reviews
			if(property_exists($offer, 'ratings') && is_array($offer->ratings) && !empty($offer->ratings) && $offer->ratings != '') {
				$out .= '<div class="otitle otitle_latestnwreviews noborder"></div>';
				$out .= count($offer->ratings) >= 3 ? '<a class="btn ovault_transgray_readall_reviews" href="/BevoMedia/Publisher/Reviews.html?NetworkID='.$offer->network__id.'" title="Go to the review page for '.$offer->networkName.'">Read all reviews</a>' : '';
				
				$out .= '<div class="clear"></div>';
				$out .= '<ul class="ovault_boxlist hastitle">';
					foreach($offer->ratings as $review) {
						$out .= $review->userComment != '' ? '<li>'.$review->userComment.'</li>' : '';
					}
				$out .= '</ul>';
			}
			
			$out .= '</div><!--close td_inner-->';
		$out .= '</td>';
	
	}
	
	$out .= '<!--<td class="tail">&nbsp;</td>-->';
$out .= '</tr><!--close .orowbig-->';

		/*}//endif $oid
		*/
		
		return $out;
	}//makeOrowbig()
	
	
	/** formatPayout
	  */
	public function formatPayout($payout=false) {
		$payout = preg_replace('/[^0-9\.]/', '', $payout);
		if($payout) {
			$null_payouts = array('0','0.0','0.00');
			if(in_array($payout, $null_payouts))
				$payout = false;
			else	$payout = '$'.number_format($payout, 2);
		} else	$payout = false;
		
		$payout = $payout ? $payout : '<span class="small">Upon request</span>';
		
		return $payout;				
	}//formatPayout()
	
	/** formatDate
	  */
	public function formatDate($date=false) {
		if($date == '0000-00-00 00:00:00' || $date == '')
			$date = '&nbsp;';
		else	$date = 'Added '.date('F j, Y', strtotime($date));
		
		$date = '<span>'.$date.'</span>';
		
		return $date;
	}//formatDate
}
