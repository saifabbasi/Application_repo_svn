<?php /* it's soapdesigned.com */

/** Builds HTML output for the PPV Spy feature.
  */

class ConstructAjaxOutput {
	
	/*output*/
	private function out($out=false) {
		
		if(!$out)
			$out['error'] = 'An error occured, please try again.';
		
		/*if(!array_key_exists('raw', $out)) { //if not a raw string
			if(!$out || !is_array($out) || empty($out))
				$out['error'] = 'Oops, something went wrong! Please try again.';
			
			if(is_array($out) && array_key_exists('html', $out) && $out['html']) {
				$out['id'] = uniqid('ppvs_html_'); //add id
				$out['html'] = '<div class="ppvs_html" id="'.$out['id'].'">'.$out['html'].'<div class="clear"></div></div><div class="clear"></div>';
			
			} else	$out['error'] = 'Nothing found for this query! Please try again.';
		}*/
				
		return $out;
	}
	
	/** orowbig()
	  * Offer details for table. output is tr.orowbig 
	  */
	public function orowbig($query=false) {
		
		//fetch offer from db HERE using the following params:
		//$query['params']['oid'] (the offer ID)
		//no need to parse anything after fetching - just fetch the offer object
		
		$offerID = intval($query['params']['oid']);
		
				
		$sql = "SELECT
					bevomedia_offers.*,
					bevomedia_category.title as `categoryTitle`,
					bevomedia_aff_network.title as `networkName`,
					bevomedia_aff_network.detail as `networkDescription`
				FROM
					bevomedia_offers,
					bevomedia_category,
					bevomedia_aff_network
				WHERE
					(bevomedia_category.id = bevomedia_offers.category__id) AND
					(bevomedia_aff_network.id = bevomedia_offers.network__id) AND
					(bevomedia_offers.id = {$offerID})
				"; //echo $sql;die;
		$data = mysql_query($sql);
		
		while ($offer = mysql_fetch_object($data))
		{
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
			
			
			$sql = "SELECT
						rating
					FROM
						bevomedia_user_aff_network_rating
					WHERE
						(bevomedia_user_aff_network_rating.network__id = {$offer->network__id}) AND
						(bevomedia_user_aff_network_rating.user__id = {$_SESSION['User']['ID']})			
					";
			$userRating = mysql_query($sql);
			
			if (mysql_num_rows($userRating)>0) {
				$userRating = mysql_fetch_assoc($userRating);
				$offer->userRating = $userRating['rating'];
			} else {
				$offer->userRating = 0;
			}
			
			
			
			$sql = "SELECT
						rating
					FROM
						bevomedia_user_aff_network_rating
					WHERE
						(bevomedia_user_aff_network_rating.network__id = {$offer->network__id}) AND
						(bevomedia_user_aff_network_rating.user__id = {$_SESSION['User']['ID']})			
					";
			$userRating = mysql_query($sql);
			
			if (mysql_num_rows($userRating)>0) {
				$userRating = mysql_fetch_assoc($userRating);
				$offer->userRating = $userRating['rating'];
			} else {
				$offer->userRating = 0;
			}
			
			
			
			$sql = "SELECT
						*
					FROM
						bevomedia_user_aff_network_rating
					WHERE
						(bevomedia_user_aff_network_rating.network__id = {$offer->network__id}) AND
						(bevomedia_user_aff_network_rating.approved = 1)
					ORDER BY 
						bevomedia_user_aff_network_rating.id DESC
					LIMIT 3
					";
			$ratings = mysql_query($sql);
			
			if (mysql_num_rows($ratings)>0) {
				$offer->ratings = array();
				while ($rating = mysql_fetch_object($ratings)) {
					$offer->ratings[] = $rating;
				}
			} else {
				$offer->ratings = array();
			}
		
			
			
			// $offer->id
			// $offer->title = $offerTEMP->offername
			// $offer->dateAdded = $offerTEMP->dateadded
			// $offer->payout = $offerTEMP->payout
			// $offer->type = $offerTEMP->offerType
			// $offer->categoryTitle = $offerTEMP->vertical
			// $offer->isNetworkMember = $offerTEMP->is_networkmember
			// $offer->networkName = $offerTEMP->networkname
			// $offer->userRating
			// $offer->ratings
			
		}
		
		
		
		//if(!$offer)
		//	$out['error'] = 'This offer seems to be invalid!';
		//else	$out['resultarr'] = $offer;
		//else	
		$out['html'] = self::MakeOrowbig($offer);	
		//$out['resultarr'] = $offer;
		//var_dump($offer);die();
		return $out;
		
	}//orowbig()
	
	/** searchresults()
	  * search for offers using the dial
	  */
	public function searchresults($query=false) {
		
		//fetch offers from db HERE using the following params:
		//$query['params']['search'] (the search term. search in both OFFER NAME and VERTICAL NAME! to retrieve accurate results, might have to split the search term by space and search for single words.)
		//$query['params']['type'] (can be either "lead", or "sale", or "lead,sale". split the 3rd one by comma and do XOR in the query)
		//$query['params']['include_mysaved'] (1 or 0. if 0, add something like "offers.id NOT IN {db table that stores saved offers for this user}" to the query) (ignore this for now)
		//$query['params']['include_networks'] (is a comma-separated list of at least 1 netword ID. search only in offers from these networks.)
		
		
		//$query['params']['type'] ?? how are we going to get this
		//$query['params']['include_mysaved'] how are we going to save the offers in this table?
		
		
		
		$searchAdd = '';
		if (isset($query['params']['search'])) {
			
			$terms = explode(' ', $query['params']['search']);
			
			
			foreach ($terms as $term)
			{
				if (trim($term)=='') continue;
				
				$term = trim($term);
				
				$searchAdd .= " (bevomedia_offers.title LIKE '%{$term}%') AND ";
			}
			
			if (strlen($searchAdd)>1) {
				$searchAdd = ' AND ('.rtrim($searchAdd, 'AND ').' )';
			} else {
				$searchAdd = '';
			}
		}
		
		
		$networksSearchAdd = '';
		if (isset($query['params']['include_networks'])) {
			$terms = explode(',', $query['params']['include_networks']);
			
			
			foreach ($terms as $term)
			{
				//if (trim($term)=='') continue;
				
				$term = intval(trim($term));
				
				$networksSearchAdd .= " (bevomedia_offers.network__id = {$term} ) OR ";
			}
			
			if (strlen($networksSearchAdd)>1) {
				$networksSearchAdd = ' AND ('.rtrim($networksSearchAdd, 'OR ').' )';
			} else {
				$networksSearchAdd = '';
			}
		}
		
		
		
		$sql = "SELECT
					bevomedia_offers.*,
					bevomedia_category.title as `categoryTitle`,
					bevomedia_aff_network.title as `networkName`
				FROM
					bevomedia_offers,
					bevomedia_category,
					bevomedia_aff_network
				WHERE
					(bevomedia_category.id = bevomedia_offers.category__id) AND
					(bevomedia_aff_network.id = bevomedia_offers.network__id)
					{$searchAdd}
					{$networksSearchAdd}
				"; 
		$data = mysql_query($sql);
		
		$offersArray = array();
		while ($offer = mysql_fetch_object($data))
		{
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
			
			
			// $offer->id
			// $offer->title = $offerTEMP->offername
			// $offer->dateAdded = $offerTEMP->dateadded
			// $offer->payout = $offerTEMP->payout
			// $offer->type = $offerTEMP->offerType
			// $offer->categoryTitle = $offerTEMP->vertical
			// $offer->isNetworkMember = $offerTEMP->is_networkmember
			// $offer->networkName = $offerTEMP->networkname
			
			
			
			//OLD OBJECT
			// $offerTEMP->saved2list = 1; //already saved in a list or not? 
			// $offerTEMP->offername = 'Shields Deluxe Enhanced Pro Ultra Plus';
			// $offerTEMP->dateadded = '12/12/2011';
			// $offerTEMP->payout = '$12.50';
			// $offerTEMP->type = 'Lead';
			// $offerTEMP->vertical = 'Shields &amp; Daggers';
			// $offerTEMP->is_networkmember = 1; //is this user a member of the network?
			// $offerTEMP->networkname = 'CPA Staxx';
			
			$offersArray[] = $offer;
		}
		
		//format price
		foreach($offersArray as $key => $offer) {
			$offersArray[$key]->payout = !stristr($offer->payout, '$')
				? '$'.number_format(intval($offer->payout), 2)
				: number_format(intval($offer->payout), 2);
		}		
		
		$out['resultarr'] = $offersArray;
		return $out;
		
	}//orowbig()
	
	/*private methods*/
	
	/** MakeOrowbig()
	  * constructs HTML for the offer details box
	  */
	private function MakeOrowbig($offer) {
		/*if(!$offer)
			$out = false;
		else {*/
			$out = '<tr class="orowbig j_oid-'.$offer['id'].' hide" data-oid="'.$offer['id'].'">';
				$out .= '<td class="border">&nbsp;</td><td class="td_info" colspan="3"><div class="td_inner">';
				$out .= '<div class="floatleft"><a class="ovault_othumb" href="#" title="Click to view large">';
					$out .= '<img src="/Themes/BevoMedia/img_new/othumb_default.gif" alt="" /><span></span>';
				$out .= '</a>';
				$out .= '<a class="btn ovault_importoffer" href="#">Import this offer into my network</a>';
				$out .= '<div class="clear"></div></div>';
				
				$out .= '<div class="floatright">';
				$out .= '<h3>'.$offer['title'].'</h3>';
				$out .= '<small>Added '.$offer['dateAdded'].'</small>';
				
				$out .= '<div class="otitle otitle_offerdesc"></div>';
				$out .= '<p></p>';
				
				$out .= '<div class="olink">';
					$out .= '<input type="text" class="formtxt" readonly value="http://google.com/" />';
					$out .= '<a class="btn ovault_visiticon" href="http://google.com/" title="Open link in a new tab" target="_blank">Visit</a>';
				$out .= '</div>';
			$out .= '</div><div class="clear"></div></div><!--close td_inner-->';
			$out .= '</td>';
			
			$out .= '<td class="td_nw" colspan="2"><div class="td_inner"><div class="otitle otitle_network noborder"></div>';
			$out .= '<div class="onwpic">';
				$out .= '<img class="nwpic w120" src="/Themes/BevoMedia/img/networklogos/uni/1068.png" alt="" title="Dadingo" />';
			
			$out .= '<p class="bordertop aligncenter">Publisher\'s Rating:<br />';
				$out .= '<img src="/Themes/BevoMedia/img/star-on.gif" id="img_rating_top_month_1068_1" onmouseover="" onmouseout="" style="" align="absbottom" border="0" /><img src="/Themes/BevoMedia/img/star-on.gif" id="img_rating_top_month_1068_2" onmouseover="" onmouseout="" style="" align="absbottom" border="0" /><img src="/Themes/BevoMedia/img/star-on.gif" id="img_rating_top_month_1068_3" onmouseover="" onmouseout="" style="" align="absbottom" border="0" /><img src="/Themes/BevoMedia/img/star-on.gif" id="img_rating_top_month_1068_4" onmouseover="" onmouseout="" style="" align="absbottom" border="0" /><img src="/Themes/BevoMedia/img/star-on.gif" id="img_rating_top_month_1068_5" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />';
			$out .= '</p><!--close publishers rating-->';
		$out .= '</div><!--close div.onwpic-->';
		
		$out .= '<p>You\'re already a member of this network!</p>';
		$out .= '<div class="icon icon_ovault_nwmember_bigwhite"></div>';
		$out .= '<a class="btn ovault_gotomystats_trans" href="/BevoMedia/Offers/MyStats.html">Go to my stats</a>';
		
		$out .= '</div><!--close td_inner-->';
	$out .= '</td>';
	$out .= '<td class="td_nwdesc" colspan="3">';
		$out .= '<div class="td_inner">';

		$out .= '<div class="otitle otitle_networkdesc"></div>';
		$out .= '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>';
		
		$out .= '<div class="otitle otitle_latestnwreviews noborder"></div>';
			$out .= '<ul class="ovault_boxlist hastitle"><li>not a fan</li><li>famous network, tons of offers</li><li>The amount of help and solid advice they give out is awesome</li></ul>';
		$out .= '</div><!--close td_inner-->';
	$out .= '</td>';
	$out .= '<!--<td class="tail">&nbsp;</td>-->';
$out .= '</tr><!--close .orowbig-->';

		/*}//endif $oid
		*/
		
		return $out;
	}//makeOrowbig()
	
	
	
	
	
	
	#####################################
	
	
	
	
	
	
	
	/*public methods*/
	public function MostSeenPopups($query=false, $xml=false) {
		$out = self::MakeHTMLGallery($query, $xml);
		return self::out($out);
	}
	
	public function MostSeenOffers($query=false, $xml=false) {		
		$out = self::MakeHTMLList_MostSeenOffers($query, $xml);
		return self::out($out);
	}
	
	public function MostSeenNiches($query=false, $xml=false) {
		$out = self::MakeHTMLList_MostSeenNiches($query, $xml);
		return self::out($out);
	}
	
	public function SearchbyKeyword($query=false, $xml=false) {
		$out = self::MakeHTMLGallery($query, $xml);
		return self::out($out);
	}
	
	public function Similar($query=false, $xml=false) {
		$out = self::MakeHTMLGallery($query, $xml);
		return self::out($out);
	}
	
	public function Poptargets($query=false, $xml=false) {
		$out = self::MakeHTMLTextarea($query, $xml);
		return self::out($out);
	}
	
	/*build methods*/
	private function MakeHTMLGallery($query=false, $xml=false) {
		if($xml) {			
			$num_results = 0;
			$html = '';
			
			$pagenav = self::MakePagenav($query, $xml);
			
			//POPS
			foreach($xml->pops->pop_up as $pop) {
				
				//construct similar links
				foreach(array('popped_url','niche','offer') as $lt) {
					$pop->links->viewpops->$lt = urlencode('search_for=pops&search_by='.$lt.'&url='.$pop->short_popped_url.'&offer_id='.$pop->offer_id.'&niche_id='.$pop->niche_id);						   
					$pop->links->viewtargets->$lt = urlencode('search_for=targets&search_by='.$lt.'&url='.$pop->short_popped_url.'&offer_id='.$pop->offer_id.'&niche_id='.$pop->niche_id);
				}
				
				//HTML
				//.ppvs_item, .ppvs_item_offer, .ppvs_item_niche are used by JS filter
				$html .= '<div class="ppvs_item galitem';
					$html .= $num_results % 5 ? '' : ' first'; //first item in row = clear:left;
								
				$html .= '">
					<a class="itemthumb" rel="shadowbox" href="'.$pop->screengrab_location.'">
						<img src="'.$pop->tn_screengrab_location.'" alt="" />
						<div class="itemthumb_viewlarge" title="Click to view large"></div>
					</a>
					<div class="itemtitle">
						<h3 class="ppvs_item_offer">'.$pop->offer.'</h3>
						<p class="ppvs_item_niche">'.$pop->niche.'</p>
					</div>
					
					<div class="itemlinks">';
					
						//stuff that doesnt exist on all pages
						if(property_exists($pop, 'popped_url')) { //so they dropped the http:// part from all links... let's add it back 
							$pop->links->pop = strpos($pop->popped_url, 'http://') !== 0 ? 'http://'.$pop->popped_url : $pop->popped_url;
							$html .= '<a class="btn btn_ppvc_openpop_gal" target="_blank" href="'.$pop->links->pop.'">Open this Pop</a>';
						}
						
						if(property_exists($pop, 'target_url')) {
							$pop->links->target = strpos($pop->target_url, 'http://') !== 0 ? 'http://'.$pop->target_url : $pop->target_url;
							$html .= '<a class="btn btn_ppvc_opentarget_gal" target="_blank" href="'.$pop->links->target.'">Open this Target</a>';
						}
								
						$html .= '
						<div class="clear"></div>
						<div class="itemlinks_below">						
							<div class="floatleft">
								<a class="btn btn_ppvc_similar btn_ppvc_viewpops_campaign notxt" href="#" rel="'.$pop->links->viewpops->popped_url.'" title="View Pops for this Campaign">View Pops for this Campaign</a>
								<a class="btn btn_ppvc_similar btn_ppvc_viewpops_niche notxt" href="#" rel="'.$pop->links->viewpops->niche.'" title="View Pops for this Niche">View Pops for this Niche</a>
								<a class="btn btn_ppvc_similar btn_ppvc_viewpops_offer notxt" href="#" rel="'.$pop->links->viewpops->offer.'" title="View Pops for this Offer">View Pops for this Offer</a>
							</div>
							<div class="floatright">
								<a class="btn btn_ppvc_viewtargets_campaign notxt" href="AjaxGetContent.html?page=Poptargets&paramstring='.$pop->links->viewtargets->popped_url.'" rel="shadowbox;width=600;height=500" title="Targets for this Campaign">Pop up a list of all Targets for this Campaign</a>
								
								<a class="btn btn_ppvc_viewtargets_niche notxt" href="AjaxGetContent.html?page=Poptargets&paramstring='.$pop->links->viewtargets->niche.'" rel="shadowbox;width=600;height=500" title="Targets for this Niche">Pop up a list of all Targets for this Niche</a>
								
								<a class="btn btn_ppvc_viewtargets_offer notxt" href="AjaxGetContent.html?page=Poptargets&paramstring='.$pop->links->viewtargets->offer.'" rel="shadowbox;width=600;height=500" title="Targets for this Offer">Pop up a list of all Targets for this Offer</a>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>'; //close .galitem
			
				$num_results++;
				
				//$html .= $num_results % 5 ? '' : '<div class="clear"></div>'; //dont do this or when filter is applied, itll be ugly. istead add css class to galitem
				
			}//endforeach pop
			
			//$out['html'] = $html ? '<div class="ppvs_html" rel="'.$query['tab'].'">'.$html.'</div>' : false;
			$out['html'] = $html ? $html : false; //wrapper .ppvs_html + #id gets added out()
			//$out['meta']['tab'] = $query['tab'];
			$out['meta']['num_results'] = $xml->summary->results ? strval($xml->summary->results) : $num_results;
			//$out['meta']['pagenav'] = $pagenav ? $pagenav : false;
			$out['meta']['pagenav'] = $pagenav;
			
		//if xml
		} else	$out['error'] = 'The requested data could not be retrieved.';
		
		return $out;
		
	}//MakeHTMLGallery()
	
	private function MakeHTMLList_MostSeenOffers($query=false, $xml=false) {
		if($xml) {			
			$num_results = 0;
			
			$html = '<table width="100%" cellspacing="0" cellpadding="10" border="0" class="btable">
					<tr class="table_header">
						<td class="hhl">&nbsp;</td>
						<td>Offer</td>
						<td>Niche</td>
						<td>View Pops for this...</td>
						<td>View Targets for this...</td>
						<td class="hhr">&nbsp;</td>
					</tr>';
			
			foreach($xml->pops->pop_up as $pop) {
				
				$num_results++;
				
				//construct similar links
				foreach(array('offer','niche') as $lt) {
					$pop->links->viewpops->$lt = urlencode('search_for=pops&search_by='.$lt.'&url='.$pop->short_popped_url.'&offer_id='.$pop->offer_id.'&niche_id='.$pop->niche_id);
					$pop->links->viewtargets->$lt = urlencode('search_for=targets&search_by='.$lt.'&url='.$pop->short_popped_url.'&offer_id='.$pop->offer_id.'&niche_id='.$pop->niche_id);
				}
				
				//HTML
				$html .= '<tr class="ppvs_item table_row">
						<td class="border">&nbsp;</td>
						<td class="ppvs_item_offer ppvc_big">'.$pop->offer.'</td>
						<td class="ppvs_item_niche ppvc_small">'.$pop->niche.'</td>
						<td class="ppvc_btns">
							<a class="btn btn_ppvc_similar btn_ppvc_viewpops_offer" href="#" rel="'.$pop->links->viewpops->offer.'" title="View Pops for this Offer">View Pops for this Offer</a>
							<a class="btn btn_ppvc_similar btn_ppvc_viewpops_niche" href="#" rel="'.$pop->links->viewpops->niche.'" title="View Pops for this Niche">View Pops for this Niche</a>
							<div class="clear"></div>
						</td>
						<td class="ppvc_btns">
							<a class="btn btn_ppvc_viewtargets_offer" href="AjaxGetContent.html?page=Poptargets&paramstring='.$pop->links->viewtargets->offer.'" rel="shadowbox;width=600;height=500" title="Targets for this Offer">Pop up a list of all Targets for this Offer</a>
							<a class="btn btn_ppvc_viewtargets_niche" href="AjaxGetContent.html?page=Poptargets&paramstring='.$pop->links->viewtargets->niche.'" rel="shadowbox;width=600;height=500" title="Targets for this Niche">Pop up a list of all Targets for this Niche</a>
						</td>
						<td class="tail">&nbsp;</td>
					</tr>';
			}//endforeach pop
			
			$html .= '<tr class="table_footer">
					<td class="hhl"></td>
					<td style="border-left: none;" colspan="4"></td>
					<td class="hhr"></td>
				</tr>
			</table>';				
			
			//$out['html'] = $html ? '<div class="ppvs_html" rel="'.$query['tab'].'">'.$html.'</div>' : false;
			$out['html'] = $html ? $html : false; //wrapper .ppvs_html + #id gets added out()
			//$out['meta']['tab'] = $query['tab'];
			$out['meta']['num_results'] = $xml->summary->results ? strval($xml->summary->results) : $num_results;
			
		//if xml
		} else	$out['error'] = 'The requested data could not be retrieved.';
		
		return $out;
		
	}//MakeHTMLList_MostSeenOffers()
	
	private function MakeHTMLList_MostSeenNiches($query=false, $xml=false) {
		if($xml) {			
			$num_results = 0;
			
			$html = '<table width="100%" cellspacing="0" cellpadding="10" border="0" class="btable">
					<tr class="table_header">
						<td class="hhl">&nbsp;</td>
						<td>Niche</td>
						<td>View Pops for this...</td>
						<td>View Targets for this...</td>
						<td class="hhr">&nbsp;</td>
					</tr>';
			
			foreach($xml->pops->pop_up as $pop) {
				
				$num_results++;
				
				//construct similar links
				$pop->links->viewpops->niche = urlencode('search_for=pops&search_by=niche&url=&offer_id=&niche_id='.$pop->niche_id);
				$pop->links->viewtargets->niche = urlencode('search_for=targets&search_by=niche&url=&offer_id=&niche_id='.$pop->niche_id);
				
				//HTML
				$html .= '<tr class="ppvs_item table_row">
						<td class="border">&nbsp;</td>
						<td class="ppvs_item_niche ppvc_big">'.$pop->niche.'</td>
						<td class="ppvc_btns">
							<a class="btn btn_ppvc_similar btn_ppvc_viewpops_niche" href="#" rel="'.$pop->links->viewpops->niche.'" title="View Pops for this Niche">View Pops for this Niche</a>
							<div class="clear"></div>
						</td>
						<td class="ppvc_btns">
							<a class="btn btn_ppvc_viewtargets_niche" href="AjaxGetContent.html?page=Poptargets&paramstring='.$pop->links->viewtargets->niche.'" rel="shadowbox;width=600;height=500" title="Targets for this Niche">Pop up a list of all Targets for this Niche</a>
						</td>
						<td class="tail">&nbsp;</td>
					</tr>';
			}//endforeach pop
			
			$html .= '<tr class="table_footer">
					<td class="hhl"></td>
					<td style="border-left: none;" colspan="3"></td>
					<td class="hhr"></td>
				</tr>
			</table>';				
			
			//$out['html'] = $html ? '<div class="ppvs_html" rel="'.$query['tab'].'">'.$html.'</div>' : false;
			$out['html'] = $html ? $html : false; //wrapper .ppvs_html + #id gets added out()
			//$out['meta']['tab'] = $query['tab'];
			$out['meta']['num_results'] = $xml->summary->results ? strval($xml->summary->results) : $num_results;
			
		//if xml
		} else	$out['error'] = 'The requested data could not be retrieved.';
		
		return $out;
		
	}//MakeHTMLList_MostSeenNiches()
	
	private function MakePagenav($query=false, $xml=false) {
		$params = false;
		
		//if similar
		if($xml->summary->search_for && $xml->summary->search_by && ($xml->summary->offer_id || $xml->summary->niche_id)
			&& $xml->summary->results && $xml->summary->current_page && $xml->summary->total_pages > 1) {
				
			$params = 'search_for='.$xml->summary->search_for
				.'&search_by='.$xml->summary->search_by
				.'&url='.$xml->summary->short_popped_url
				.'&offer_id='.$xml->summary->offer_id
				.'&niche_id='.$xml->summary->niche_id;
			
		//elseif search
		} elseif($xml->summary->query && $xml->summary->results && $xml->summary->current_page && $xml->summary->rows_per_page && $query['params']['search_type']) {
			
			$params = 'q='.$xml->summary->query.'&search_type='.$query['params']['search_type']; //REPLACE query with xml once this param is implemented by the guys
			
		}

		//only display pagination for similar or search
		if($params) :
		
			$params = urlencode(str_replace(array('\'','"'),'',$params));
			
			$currange_from = intval($xml->summary->offset)+1;
			$currange_to =  intval($xml->summary->rows_per_page) * intval($xml->summary->current_page);
			if($currange_to > $xml->summary->results) //adjust last page's currange_to to actual max result
				$currange_to = $xml->summary->results;	
			
			$pagenav = array(
				'showing' => $currange_from.'-'.$currange_to,
				'totalresults' => number_format(intval($xml->summary->results)),
				'nav' => array(), //values = only the rel="$params" string. html gets added via js if this string exists.
				'numbers' => ''
			);
			
			$currentpage = intval($xml->summary->current_page);
			$totalpages = intval($xml->summary->total_pages);
			
			//back and first links
			if($currentpage > 1) { //if not on page 1. first page never gets 'currentpage' param or the JS cache token wont match
				$prevpage = $currentpage-1;			
				$pagenav['nav']['first'] = $params;
				$pagenav['nav']['back'] = $params;				
				$pagenav['nav']['back'] .= $prevpage != 1 ? urlencode('&currentpage='.$prevpage) : ''; //if not on 2nd page, add currentpage param
			}
			
			//loop
			for($i = ($currentpage-8); $i < (($currentpage+8)+1); $i++) {
				if(($i > 0) && ($i <= $totalpages)) { //if this page exists
					$rel = $i == 1 ? $params : $params.urlencode('&currentpage='.$i); //dont add currentpage for the 1st one to maintain caching
					$pagenav['numbers'] .= '<a';
					$pagenav['numbers'] .= $i == $currentpage ? ' class="active"' : ''; //current page
					$pagenav['numbers'] .= ' href="#" rel="'.$rel.'">'.$i.'</a>';
					$rel = false;
				}
			}
			
			//forward link
			if($currentpage != $totalpages) { //if not on last page
				$nextpage = $currentpage+1;
				$pagenav['nav']['last'] = $params.urlencode('&currentpage='.$totalpages);
				$pagenav['nav']['forward'] = $params.urlencode('&currentpage='.$nextpage);
			}
			
			$out = $pagenav;
			
		//endif params
		else :	$out = false;
		endif;
		
		return $out;
		
	}//MakePagenav()
}
