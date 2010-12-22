<?php /* it's soapdesigned.com */

/** Builds HTML output for the PPV Spy feature.
  */

class ConstructAjaxOutput {
	
	/*output*/
	private function out($out=false) {
		if(!array_key_exists('raw', $out)) { //if not a raw string
			if(!$out || !is_array($out) || empty($out))
				$out['error'] = 'Oops, something went wrong! Please try again.';
			
			if(is_array($out) && array_key_exists('html', $out) && $out['html']) {
				$out['id'] = uniqid('ppvs_html_'); //add id
				$out['html'] = '<div class="ppvs_html" id="'.$out['id'].'">'.$out['html'].'<div class="clear"></div></div><div class="clear"></div>';
			
			} else	$out['error'] = 'Nothing found for this query! Please try again.';
		}
				
		return $out;
	}
	
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
						$html .= property_exists($pop, 'popped_url') 
								? '<a class="btn btn_ppvc_openpop_gal" target="_blank" href="'.$pop->popped_url.'">Open this Pop</a>'
								: '';
						$html .= property_exists($pop, 'target_url') 
								? '<a class="btn btn_ppvc_opentarget_gal" target="_blank" href="'.$pop->target_url.'">Open this Target</a>'
								: '';
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
	
	private function MakeHTMLTextarea($query=false, $xml=false) { //for shadowbox iframe
		if($xml) {			
			$urls = array();
			$pre_html = '<html><style type="text/css">
					<!--
					html { background:#fff; overflow:hidden; font-family:Helvetica, Arial, sans-serif; }
					textarea { width:400; height:350px; padding:5px; margin:0 auto; display:block; color:#999; background:#f2f2f2; font-size:11px; border:1px solid #ccc; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; box-shadow:#e6e6e6 1px 1px 2px; -moz-box-shadow:#e6e6e6 1px 1px 2px; -webkit-box-shadow:#e6e6e6 1px 1px 2px; }
					textarea:focus { background:#fff; color:#666; border-color:#b2b2b2 !important; }
					h2,p { margin:20px 0; color:#333; text-align:center; line-height:12px; }
					p { color:#999; margin:0 0 20px; font-size:12px; }
					-->
					</style>';
						
			foreach($xml->pops->pop_up as $pop)
				$urls[] = $pop->target_url;				
				
			$urls = array_unique($urls);
						
			$out['raw'] = count($urls) > 0 ? $pre_html.'<h2>'.count($urls).' Targets</h2><p>Copy-paste this list of target URLs into your PPV network.</p>
							<textarea rows="1" cols="1">'.implode("\r", $urls).'</textarea></html>'
							
					: $pre_html.'<p>Currently no Target URLs available. Please try again later, we\'re updating daily!</p></html>';
			
		//if xml
		} else	$out['raw'] = $pre_html.'<p>The requested data could not be retrieved.</p>';
		
		return $out;
	
	}//MakeHTMLTextarea()
	
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