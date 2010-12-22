<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/User/AppStore.html">&laquo; All Apps<span></span></a></li>
		<li><a class="active" href="/BevoMedia/PPVSpy/Index.html">PPV Spy<span></span></a></li>
	</ul>
	
</div>

<div id="ppvpanel">
	<div id="ppvp_menu">
		<ul>
			<li><a class="active" href="#" rel="MostSeenPopups">Popups<span></span></a></li>
			<li><a href="#" rel="MostSeenOffers">Offers<span></span></a></li>
			<li><a href="#" rel="MostSeenNiches">Niches<span></span></a></li>
		</ul>
		<ul class="floatright">
			<li><a href="#" rel="SearchbyKeyword">Search<span></span></a></li>
			<li><a href="#" rel="SuggestATarget">Suggest<span></span></a></li>
		</ul>
		<div class="clear"></div>
	</div>
	<div id="ppvp_content">
		<div class="ppvp_content_mode ppvp_content_modefilter visible">
			<div id="ppvp_content_modewrap" class="ppvp_content_popups">
				<div class="ppvp_label ppvp_label_find">Find</div>
				<input type="text" class="formtxt" id="ppvp_filter_txt" name="ppvp_filter_search" value="start typing to use the smart filter..." />
				<a class="btn btn_ppvp_filter_reset" href="#" title="Clear Filter">Clear Filter</a>
				
				<div class="ppvp_label ppvp_label_in">In</div>
				<a class="btn btn_ppvp_apply btn_ppvp_apply_offer active" href="#" rel="offer" title="Search in Offer Names">Search in Offer Names</a>
				<a class="btn btn_ppvp_apply btn_ppvp_apply_niche" href="#" rel="niche" title="Search in Niche Names">Search in Niche Names</a>
				
				<?php /*<div class="ppvp_content_inputfilter_switchview">
					<div class="ppvp_label ppvp_label_switchview">Switch View</div>
					<a class="btn btn_ppvp_view btn_ppvp_view_gallery active" href="#" rel="gallery" title="Switch to Gallery View">Switch to Gallery View</a>
					<a class="btn btn_ppvp_view btn_ppvp_view_list" href="#" rel="list" title="Switch to List View">Switch to List View</a>
					<div class="clear"></div>
				</div>	*/ ?>
				<div class="clear"></div>
			</div>
		</div><!--close modefilter-->
		
		<div class="ppvp_content_mode ppvp_content_modesearch">
			<div class="ppvp_label ppvp_label_searchfor">Search For</div>
			<input type="text" class="formtxt" id="ppvp_search_txt" name="ppvp_search_search" value="any search term" />
			
			<div class="ppvp_label ppvp_label_in">In</div>
			<a class="btn btn_ppvp_searchin btn_ppvp_searchin_poptarget active" href="#" rel="3" title="Search in both Pop URL and Target URL">Search in both Pop URL and Target URL</a>
			<a class="btn btn_ppvp_searchin btn_ppvp_searchin_pop" href="#" rel="2" title="Search in Pop URL only">Search in Pop URL only</a>
			<a class="btn btn_ppvp_searchin btn_ppvp_searchin_target" href="#" rel="1" title="Search in Target URL only">Search in Target URL only</a>
			<input type="hidden" name="search_type" value="3" />
			
			<a class="btn btn_ppvp_searchgo inactive" id="btn_ppvp_searchgo" href="#" title="Search Now">Search Now</a>
		</div><!--close modesearch-->
		
		<div class="ppvp_content_mode ppvp_content_modesuggest">
			<input type="text" class="formtxt" id="ppvp_suggest_txt" name="target" value="Tell us which target URL you'd like us to look into!" />
			<a class="btn btn_ppvp_suggestgo inactive" id="btn_ppvp_suggestgo" href="#" title="Submit"></a>
		</div>
		
	</div><!--close #ppvp_content-->
	
	<div id="ppvp_status" class="ppvp_status_updating">
		<div class="stattag"></div>
		<p class="ppvp_status_updateani">Updating</p>
		<p class="ppvp_status_p"></p>
	</div>
	
	<div id="ppvp_nav"></div>
	<div id="ppvp_totalresults" class="disabled"></div>
	<div id="ppvp_filterresults" class="disabled" title="Click to clear Filter"></div>
	
	<div id="ppvp_nav_numbers">
		<div class="divleft"></div>
		<div class="divcontent"></div>
		<div class="clear"></div>
		<div class="divtop"></div>
	</div><!--close nav_numbers-->
</div><!--close #ppvpanel-->

<div id="ppvspy_notice">
	<div class="soapyell simple ppvc_notice ppvc_notice_filter_nomatch"><p>No matching items, try broadening your filter.</p></div>
	<div class="soapyell simple ppvc_notice ppvc_notice_search"><p>Welcome to the most powerful feature of the Bevo PPV Spy! Enter a search term above and hit the lens on the right to see the magic happen.</p></div>
	<div class="soapyell simple ppvc_notice ppvc_notice_search_noresults"><p>Welcome to the most powerful feature of the Bevo PPV Spy! Enter a search term above and hit the lens on the right to see the magic happen.</p></div>
	<div class="soapyell simple ppvc_notice ppvc_notice_suggest"><p>Have a target URL in mind that you'd like us to check out? Suggest it here and we'll look into it. Keep em coming!</p></div>
</div>

<div id="ppvspy_content"></div>
<div id="ppvspy_loading"></div>
<div id="ppvspy_message">
	<div class="soapyell simple black ppvc_notice ppvc_notice_ajaxmessage"></div>
</div>

<script type="text/javascript">
$(document).ready(function() {
		
	var 	ppvs_panel ='#ppvpanel',
		ppvs_canvas = '#ppvspy_content',
		ppvs_status = '#ppvp_status',
		ppvs_status_p = 'p.ppvp_status_p',
		ppvs_nav = '#ppvp_nav',
		ppvs_navnumbers = '#ppvp_nav_numbers',
		ppvs_totalresults = '#ppvp_totalresults',
		ppvs_filterresults = '#ppvp_filterresults',
		
		ppvs_loading = '#ppvspy_loading',
		
		ppvs_notice = '#ppvspy_notice',
		ppvs_filternotice_nomatch = ppvs_notice+' .ppvc_notice_filter_nomatch',
		
		ppvs_message = '#ppvspy_message', //#message is above content, #notice below
		ppvs_message_ajax = ppvs_message+' .ppvc_notice_ajaxmessage',
		//ppvs_filternotice_search = ppvs_notice+' .ppvc_notice_search',
		//ppvs_filternotice_suggest = ppvs_notice+' .ppvc_notice_suggest',
		
		//pagenav
		soap_ppvsnavTimer,
		ppvs_show_navnumbers = false,		//is true when pagination exists = show on hover
		ppvs_hover_navnumbers = false,		//true while hover on circle or navnumbers
		ppvs_show_totalresults = false,		//true when totalresults are showing
		ppvs_show_filterresults = false,	//for identifying initial filter operation and knowing when to show filterresults
		
		ppvs_navbtn_names = new Array('first','back','last','forward'),	//the 4 arrow btns
		ppvs_navbtns = [],		//key = nabttn_name, val bool
		
		//panel mode (filter or search)
		ppvs_modediv = '#ppvp_content .ppvp_content_mode',
		
		ppvs_modefilter_wrap = '#ppvp_content_modewrap',		//only in filter mode. no styling, but classname defines width of input field
		ppvs_mode_filter = '#ppvp_content .ppvp_content_modefilter',
		ppvs_mode_search = '#ppvp_content .ppvp_content_modesearch',
		ppvs_mode_suggest = '#ppvp_content .ppvp_content_modesuggest', //below: items that are not always visible
			ppvs_mode_filteritem_applyoffer = '#ppvp_content .ppvp_content_modefilter a.btn_ppvp_apply_offer',
			ppvs_mode_filteritem_switchview = '#ppvp_content .ppvp_content_modefilter .ppvp_content_inputfilter_switchview',
		ppvs_activemode = 'filter',	//filter, search, or suggest. set in soap_ppvsTogglePanel, used there + in soap_ppvsValidateInputTxt. redundant (ppvs_activetab contains
						//this) bc we need to call togglepanel b4 we set ppvs_activetab to the new one, and we need to prevent slideup/down if already in this tab.
		
		//ajax
		ppvs_ajaxgetcontent = 'AjaxGetContent.html?page=',
		
		//cache
		ppvs_activetab = 'MostSeenPopups',	//currently active tab. starts off with pops.
		ppvs_path = 'parent',				//either "parent" or "kid_$params". is set onclick by button to keep all levels cached + parent info.
		ppvs_cache = [],		//cached content for tabs that have already been fetched
		
		//filter
		ppvs_filterapply_btns = ppvs_panel+' a.btn_ppvp_apply',
		
		soap_ppvsfilterTimer,
		ppvs_filterinput = '#ppvp_filter_txt', 	//the filter input field (either #ppvp_filter_txt, #ppvp_search_txt, or #ppvp_suggest_txt). changed on tab change		
		
		//ppvs_filterdefault = 'start typing to use the smart filter...',	//same as above. changes by type of parent (filter, search, or suggest
		//ppvs_searchdefault = 'any search term',
		ppvs_inputdefault = 'start typing to use the smart filter...', //changes on tab change in TogglePanel()
		
		ppvs_activepagetype = 'parent',		//either parent or kid. used to save old filter before a page change
		ppvs_filterapply = 'offer',	//offer or niche - what the filter applies to > classes .ppvs_item_offer or .ppvs_item_niche
		ppvs_filtersearch_current = false,		//the current (to-be-applied) filter (not search or suggest, those have their own var)
		ppvs_filter = [],		//similar structure and behavior as cache: no path. kids of activetab are 'apply' and 'str'.
						//8 stored filters total: (mostpop x 3 + search) + (1 similar for each parent)
						
		//search
		ppvs_searchsuggest_current = false,	//the current search and suggest field value. shared btw the 2. switched in togglePanel()
		
		ppvs_searchgo = '#btn_ppvp_searchgo',
		ppvs_searchinput = '#ppvp_search_txt',
		ppvs_searchin_btns = ppvs_panel+' a.btn_ppvp_searchin',		
		ppvs_searchin_current = 3,		//current "search in" param, 3 = search in both (default)
		
		//suggest
		ppvs_suggestgo = '#btn_ppvp_suggestgo',
		ppvs_suggestinput = '#ppvp_suggest_txt';
		
						
	/*panel*/
	//position
	window.onscroll = function() {
		if(document.documentElement.scrollTop > 215 || self.pageYOffset > 215)
			$(ppvs_panel).addClass('fix');
		else if(document.documentElement.scrollTop < 215 || self.pageYOffset < 215)
			$(ppvs_panel).removeClass('fix');
	}
	
	/*tabs*/
	$('#ppvp_menu a').live('click', function(e) {
		e.preventDefault();
			
		var target = $(this).attr('rel');
		
		if((target != ppvs_activetab) || (ppvs_path != 'parent')) { //if this is a diff tab or we are coming from a child
			
			soap_ppvsSaveFilter(ppvs_activepagetype); //old filter with old active pagetype
			ppvs_activepagetype = 'parent'; //set new pagetype
			
			soap_ppvsClearCanvas();
			soap_ppvsTogglePanel(target);
			
			$('#ppvp_menu a').removeClass('active');
			$(this).addClass('active');
			
			ppvs_activetab = target; //now set activetab and path
			ppvs_path = 'parent';
			
			if(ppvs_activetab == 'SearchbyKeyword') {
				soap_ppvsNotice('search');
				soap_ppvsToggleStatus();
				soap_ppvsRemoveAllPagination(); //LATERRRRR##### when checking which page to render (parent or kid), move this to condition "no cache exists for search"
				soap_ppvsHideFilterResults(); //same as above
				
				//if theres already something in the search field, enable go and fill the var
				soap_ppvsValidateInputTxt($(ppvs_searchinput).val());
				if(ppvs_searchsuggest_current)
					$(ppvs_searchinput).focus();
				
				/*} else {
					//############################################################################################# LATERRR
					//add overlay while focused, and focus. same in render() for filter
					//$(ppvs_searchinput).focus();
				}*/
			
			} else if(ppvs_activetab == 'SuggestATarget') {
				soap_ppvsNotice('suggest');
				soap_ppvsToggleStatus();
				soap_ppvsRemoveAllPagination();
				soap_ppvsHideFilterResults(); 
				
				soap_ppvsValidateInputTxt($(ppvs_suggestinput).val());
				if(ppvs_searchsuggest_current)
					$(ppvs_suggestinput).focus();
			
			} else { //if a mostpop tab
				soap_ppvsNotice();
				
				if(!ppvs_cache[ppvs_activetab] || !ppvs_cache[ppvs_activetab][ppvs_path]) { //if we dont have a cache, FETCH
					$.ajax({
						type: 'GET',
						url: ppvs_ajaxgetcontent+ppvs_activetab,
						success: function(r) {
							soap_ajaxSuccess(r);
						},
						error : function(r) {
							soap_ajaxMessage(r);
						}
					});
				
				} else { //no fetching
					soap_ppvsRender();
				}//endif no cache
				
			}//endif page type
		}//endif diff target or coming from child
	});
	
	/*similar, pagenav*/
	$('a.btn_ppvc_similar, a.btn_ppvp_nav, #ppvp_nav_numbers .divcontent a').live('click', function(e) {
		e.preventDefault();
		
		var paramstring;
		
		params = $(this).attr('rel');
		ppvs_path = 'kid_'+params;
		
		//if($(this).is('a.btn_ppvc_similar'))  //DO save filter when paginating to carry it along
		soap_ppvsSaveFilter(ppvs_activepagetype);
		ppvs_activepagetype = 'kid';
		soap_ppvsClearCanvas();
		
		if(!$(this).is('a.btn_ppvc_similar') && ppvs_activetab == 'SearchbyKeyword') //for numbers and nav, check what page we're on for the api target url
			paramstring = 'SearchbyKeywordPaged&search_type='+ppvs_searchin_current+'&paramstring='+params; //different from initial search bc rel attr is a mess
		else	paramstring = 'Similar&paramstring='+params;
		
		
		//check cache
		if(ppvs_cache[ppvs_activetab][ppvs_path]) { //no fetching
			soap_ppvsRender();
		
		} else { //else this is a new page
			$.ajax({
				type: 'GET',
				url: ppvs_ajaxgetcontent+paramstring,
				success: function(r) {					
					//alert(r);
					soap_ajaxSuccess(r);
				},
				error : function(r) {
					soap_ajaxMessage(r);
				}
			});
		}
	});
	
	/*search*/
	//go //initial search only. pagination is happening in the above, although the request gets sent to the search page as well
	$(ppvs_searchgo).live('click', function(e) {
		e.preventDefault();
		
		if(ppvs_searchsuggest_current && ppvs_searchin_current) { //ppvs_searchterm_current is set on input, searchin on btn change, but check nevertheless
			var params = 'q='+ppvs_searchsuggest_current+'&search_type='+ppvs_searchin_current;
							//since this is only the initial search, not a paged page, we dont have a currentpage param
							
			soap_ppvsClearCanvas();
			//dont save filter to cache bc that happens on tabswitch or backbtn already
			
			ppvs_activetab = 'SearchbyKeyword';
			ppvs_path = 'kid_'+params; //search has no parent
			
			/*
			BACKBTN:
			- saves old filter like tab + similar do!
			*/
			$.ajax({
				type: 'GET',
				url: ppvs_ajaxgetcontent+ppvs_activetab+'&'+params,
				//url: ppvs_ajaxgetcontent+'SearchbyKeyword&paramstring='+params,
				success: function(r) {
					//alert(r);
					soap_ajaxSuccess(r);
				},
				error : function(r) {
					soap_ajaxMessage(r);
				}
			});
		}
	});
	
	//input
	//$(ppvs_searchinput).bind('keyup input paste', soap_ppvsValidateInputTxt($(ppvs_searchinput).val()));
	$(ppvs_searchinput).bind('keyup input paste', function() {
		soap_ppvsValidateInputTxt($(this).val());		
	});
	 
	//switch searchin
	$(ppvs_searchin_btns).live('click', function(e) {
		e.preventDefault();
		
		ppvs_searchin_current = $(this).attr('rel');
		
		$(ppvs_searchin_btns).removeClass('active'); //remove from both btns
		$(this).addClass('active');
	});
	
	/*suggest*/
	//go
	$(ppvs_suggestgo).live('click', function(e) {
		e.preventDefault();
		
		if(ppvs_searchsuggest_current) {
							
			//soap_ppvsClearCanvas();
			//dont save filter to cache bc that happens on tabswitch or backbtn already
			
			//ppvs_activetab = 'SearchbyKeyword';
			//ppvs_path = 'kid_'+params; //search has no parent
			//ppvs_activetab = false;
			//ppvs_path = false;
			
			$.ajax({
				type: 'GET',
				url: ppvs_ajaxgetcontent+'SuggestATarget&target='+ppvs_searchsuggest_current,
				success: function(r) {
					r = $.parseJSON(r);
					soap_ajaxMessage(r.error);					
				},
				error : function(r) {
					r = $.parseJSON(r);
					soap_ajaxMessage(r);
				}
			});
		}
	});
	
	//input
	$(ppvs_suggestinput).bind('keyup input paste', function() {
		soap_ppvsValidateInputTxt($(this).val());		
	});
		
	
	/*show page numbers*/
	$(ppvs_status).live('mouseover mouseout', function(eh) {
		if($(this).hasClass('haspages')) {
			if(eh.type == 'mouseover') {
				clearTimeout(soap_ppvsnavTimer);
				$(ppvs_navnumbers).slideDown(250);
				ppvs_hover_navnumbers = true;
				
			} else {
				ppvs_hover_navnumbers = false;
				soap_ppvsnavTimer = setTimeout(function() {
					soap_ppvsNavnumbersClose();
				}, 300);
			}
		}
	});
	
	$(ppvs_navnumbers).live('mouseover mouseout', function(e) {	
		if(e.type == 'mouseover') { 
			clearTimeout(soap_ppvsnavTimer);
			ppvs_hover_navnumbers = true;
		} else {
			ppvs_hover_navnumbers = false;
			soap_ppvsnavTimer = setTimeout(function() {
				soap_ppvsNavnumbersClose();
			}, 300);
		}
	});
	
	/*filter*/
	//focus
	$(ppvs_filterinput+', '+ppvs_searchinput+', '+ppvs_suggestinput).live('focus', function() {
		if($(this).val() == ppvs_inputdefault)
			$(this).val('');
	}).live('blur', function() {
		if($(this).val() == '')
			$(this).val(ppvs_inputdefault);
	});
	
	//input
	 $(ppvs_filterinput).bind('keyup input paste', function(){
		//clearTimeout(soap_ppvsfilterTimer);
		//soap_ppvsfilterTimer = setTimeout(function() {
			ppvs_filtersearch_current = $(this).val();
			soap_ppvsApplyFilter();
		//}, 500);
	});
	
	//switch apply
	$(ppvs_filterapply_btns).live('click', function(e) {
		e.preventDefault();
		
		ppvs_filterapply = $(this).attr('rel');
		
		$(ppvs_filterapply_btns).removeClass('active'); //remove from both btns
		$(this).addClass('active');
		
		soap_ppvsApplyFilter();
	});
	
	//clear
	$(ppvs_panel+' a.btn_ppvp_filter_reset, '+ppvs_filterresults).live('click', function(e) {
		e.preventDefault();
		soap_ppvsClearFilter();
	});
		
	/*initial*/
	$.ajax({
		type: 'GET',
		url: ppvs_ajaxgetcontent+ppvs_activetab,
		success: function(r) {
			//alert(r);
			//*
			soap_ajaxSuccess(r);
			//*/
		},
		error : function(r) {
			soap_ajaxMessage(r);
		}
	});
	
	/*soap_ajaxSuccess*/
	function soap_ajaxSuccess(r) {
		r = $.parseJSON(r);
		
		if(r.error) {
			soap_ajaxMessage(r.error);
			
			if(ppvs_activetab == 'SearchbyKeyword')
				soap_ppvsToggleStatus('noresults'); //bring back the search status
		
		} else {
			if(!ppvs_cache[ppvs_activetab]) //check this because it may exist already and we dont wanna overwrite it
				ppvs_cache[ppvs_activetab] = [];
		
			ppvs_cache[ppvs_activetab][ppvs_path] = []; //works for both parents and kids. no need to check exist here cuz ajaxSuccess only runs when nothing exists
		
			//map response to caching arr
			ppvs_cache[ppvs_activetab][ppvs_path]['html'] = r.html;
			ppvs_cache[ppvs_activetab][ppvs_path]['id'] = r.id;
			
			if(r.meta.num_results)
				ppvs_cache[ppvs_activetab][ppvs_path]['num_results'] = r.meta.num_results;
			
			if(r.meta.pagenav && r.meta.pagenav.numbers) { //if this is paginated
				ppvs_cache[ppvs_activetab][ppvs_path]['pagenav'] = [];
				ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['numbers'] = r.meta.pagenav.numbers;
				
				if(r.meta.pagenav.showing)
					ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['showing'] = r.meta.pagenav.showing;
				
				if(r.meta.pagenav.totalresults)
					ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['totalresults'] = r.meta.pagenav.totalresults;
				
				if(r.meta.pagenav.nav) {
					ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['nav'] = [];					
					
					for(i=0; i<=3; i++) {
						if(r.meta.pagenav.nav[ppvs_navbtn_names[i]])
							ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['nav'][ppvs_navbtn_names[i]] = r.meta.pagenav.nav[ppvs_navbtn_names[i]];
					}
				}
			}//endif pagenav&numbers
			
			soap_ppvsRender(); //output
		}
		
	}
	
	/*soap_ajaxMessage*/
	function soap_ajaxMessage(r) {		
		//$(ppvs_message_ajax).html('<p>'+r+'</p>').fadeIn(400, function() {
		$(ppvs_message_ajax).html('<p>'+r+'</p>').animate({
			width: 600,
			height: +50,
			paddingTop: 60,
			marginTop: -5,
			marginLeft: -60
		}, 100).fadeIn(700).delay(2000).fadeOut(1000, function() {
			$(this).html('').removeAttr('style');
		})
		//});
		
		//LATERRRRRRRRRR: on error, go back to last page. => add lastpage token to var and pull from cache.
	}
	
	/*soap_ppvsRender*/
	function soap_ppvsRender() {
		
		//clear notice if search
		if(ppvs_activetab == 'SearchbyKeyword')
			soap_ppvsNotice(); 
		
		//pagination
		if(ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']) {
			
			//status
			soap_ppvsToggleStatus('render', 'range', ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['showing']); //if we have pagination, show the range
			
			//nav btns
			if(ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['nav']) {
				for(i=0; i<=3; i++) {
					if(ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['nav'][ppvs_navbtn_names[i]]) { //if we have a navbtn
						if(ppvs_navbtns[ppvs_navbtn_names[i]]) { //if the old navbtn already exists
							$(ppvs_nav+' a.btn_ppvp_nav_'+ppvs_navbtn_names[i])
								.attr('rel',ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['nav'][ppvs_navbtn_names[i]]); //just update the rel attr
						
						} else { //spawn new btn
							soap_ppvsSpawnNavBtn(ppvs_navbtn_names[i], ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['nav'][ppvs_navbtn_names[i]]);
						}
					} else if(ppvs_navbtns[ppvs_navbtn_names[i]]) { //if no new one but an old one,
						soap_ppvsRemoveNavbtn(ppvs_navbtn_names[i]); //remove it
					}
						
				}
			}
			
			//totalresults
			if(ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['totalresults']) { //if we have a number
				$(ppvs_totalresults).html('<p>'+ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['totalresults']+'</p>');
				
				if(!ppvs_show_totalresults) { //node is hidden by default so just check if we have to show it
					$(ppvs_totalresults).delay(1000).slideDown(500);
					ppvs_show_totalresults = true; //and update var
				}					
			
			} else if(ppvs_show_totalresults) { //if no number, but its visible, hide it
				$(ppvs_totalresults).slideUp(500).empty();
				ppvs_show_totalresults = false;
			}
			
			//numbers
			$(ppvs_navnumbers+' .divcontent').html(ppvs_cache[ppvs_activetab][ppvs_path]['pagenav']['numbers']);
			var ppvs_navnumbers_w = $(ppvs_navnumbers).outerWidth(false)/2-3;
			$(ppvs_navnumbers).css({'margin-left':'-'+ppvs_navnumbers_w+'px'});
			ppvs_show_navnumbers = true;
			
		//if no pagination
		} else {
			soap_ppvsToggleStatus('render', 'total', ppvs_cache[ppvs_activetab][ppvs_path]['num_results']); //show no range but total number
			//$(ppvs_status).removeClass('haspages'); //just to be safe
			//$(ppvs_status_p).removeClass('range').html(ppvs_cache[ppvs_activetab][ppvs_path]['num_results']);
			soap_ppvsRemoveAllPagination();
		}
		
		//canvas
		$(ppvs_canvas).html(ppvs_cache[ppvs_activetab][ppvs_path]['html']);
		Shadowbox.setup();
		//$(ppvs_canvas+' .loading').delay(500).fadeOut(500).remove();
		$(ppvs_loading).fadeOut(100);
		
		//add initial filter
		if(ppvs_filter[ppvs_activetab]) { //if a filter exists for this page
			var pathtype = false; //parent or kid
			
			if((ppvs_path == 'parent') && (ppvs_filter[ppvs_activetab]['parent'])) //if this is a parent page and it has a filter
				pathtype = 'parent';
			
			else if(ppvs_filter[ppvs_activetab]['kid']) // else this is a kid, so if this has a filter,
				pathtype = 'kid';
			
			if(pathtype) { //if we got something
				ppvs_filtersearch_current = ppvs_filter[ppvs_activetab][pathtype]['str'];
				$(ppvs_filterinput).val(ppvs_filtersearch_current);
				
				ppvs_filterapply = ppvs_filter[ppvs_activetab][pathtype]['apply'];
				$(ppvs_filterapply_btns).removeClass('active'); //remove from both btns
				$(ppvs_filterapply_btns+'_'+ppvs_filterapply).addClass('active');
				
				soap_ppvsApplyFilter();
			
			//if no filter
			} else	soap_ppvsResetFilterPanel();
		
		//if no filter
		} else	soap_ppvsResetFilterPanel();
		
		//############################################################################################# LATERRR
		//add overlay while focused, and focus
		//focus filter if not search or suggest (happens on tab change for those)
		//$(ppvs_filterinput).focus();
		
		
	}
	
	/*soap_ppvsClearCanvas*/
	function soap_ppvsClearCanvas() {
		$(ppvs_loading).fadeIn(100);
		$(ppvs_canvas+' .ppvs_html').fadeOut(500).removeClass('showing').delay(500);
		soap_ppvsToggleStatus('clear');
		//$(ppvs_status_p).removeClass('range').html(''); //just in case, remove the range class
	}
	
	/*soap_ppvsTogglePanel*/ //set new panel input type
	function soap_ppvsTogglePanel(type) { //type can be MostSeenPopups, MostSeenOffers, MostSeenNiches, SearchbyKeyword, SuggestATarget, Similar
		//dont worry about updaing the filter itself - render() already does this!
		
		//mostseen + similar
		if(type == 'MostSeenPopups' || type == 'MostSeenOffers' || type == 'MostSeenNiches' || type == 'Similar') {
			ppvs_inputdefault = 'start typing to use the smart filter...';
			
			if(ppvs_activemode != 'filter') { //if not already in a filter mode, switch to filter
				$(ppvs_modediv).slideUp(250).removeClass('visible');
				$(ppvs_mode_filter).slideDown().addClass('visible');
				
				ppvs_activemode = 'filter';
			}
			
			//show/hide apply btns
			if(type == 'MostSeenPopups' || type == 'Similar')
				$(ppvs_modefilter_wrap).removeClass().addClass('ppvp_content_popups');
			
			else if(type == 'MostSeenOffers')
				$(ppvs_modefilter_wrap).removeClass().addClass('ppvp_content_offers');
				
			else if(type == 'MostSeenNiches')
				$(ppvs_modefilter_wrap).removeClass().addClass('ppvp_content_niches');
				
			
		//search
		} else if(type == 'SearchbyKeyword') {
			ppvs_inputdefault = 'any search term';
			ppvs_searchsuggest_current = ($(ppvs_searchinput).val() == '' || $(ppvs_searchinput).val() == ppvs_inputdefault) ? $(ppvs_searchinput).val() : false;
			
			if(ppvs_activemode != 'search') {
				$(ppvs_modediv).slideUp(250).removeClass('visible');
				$(ppvs_mode_search).slideDown().addClass('visible');
				
				ppvs_activemode = 'search';
			}
			
			$(ppvs_mode_filteritem_switchview).addClass('visible');
			$(ppvs_mode_filteritem_applyoffer).addClass('visible');
			
		//target
		} else if(type == 'SuggestATarget') {
			ppvs_inputdefault = 'Tell us which target URL you\'d like us to look into!';
			ppvs_searchsuggest_current = ($(ppvs_suggestinput).val() == '' || $(ppvs_suggestinput).val() == ppvs_inputdefault) ? $(ppvs_suggestinput).val() : false;
			
			if(ppvs_activemode != 'suggest') {
				$(ppvs_modediv).slideUp(250).removeClass('visible');
				$(ppvs_mode_suggest).slideDown().addClass('visible');
				
				ppvs_activemode = 'suggest';
			}
		
		}
	}

	/*soap_ppvsSpawnNavBtn*/	
	function soap_ppvsSpawnNavBtn(name, rel) {
		var 	btnclass = 'btn_ppvp_nav_'+name,
			capname = name.charAt(0).toUpperCase() + name.substr(1),
			btn = '<a class="btn btn_ppvp_nav '+btnclass+' disabled" href="#" rel="'+rel+'" title="'+capname+'">'+capname+'</a>';
		
		$(btn).appendTo(ppvs_nav).slideDown(250);
		//$(ppvs_nav).append(btn).slideDown(500);
		
		ppvs_navbtns[name] = true;
	}
	
	/*soap_ppvsRemoveNavbtn*/
	function soap_ppvsRemoveNavbtn(name) {
		$(ppvs_nav).find('a.btn_ppvp_nav_'+name).slideUp(250).remove();		
		ppvs_navbtns[name] = false;
		//ppvs_navbtns.splice(name, 1); //remove key from arr
	}
	
	/*soap_ppvsRemoveAllPagination*/
	function soap_ppvsRemoveAllPagination() { //reset all vars and remove any nodes
		for(i=0; i<=3; i++)
			soap_ppvsRemoveNavbtn(ppvs_navbtn_names[i]); //remove all nav btns
		ppvs_navbtns = [];
		
		$(ppvs_totalresults).slideUp(1000).empty();
		ppvs_show_totalresults = false;
		
		$(ppvs_navnumbers).slideUp(300);
		$(ppvs_navnumbers+' .divcontent').empty();
		ppvs_show_navnumbers = false;
	}
	
	/*soap_ppvsNavnumbersClose*/
	function soap_ppvsNavnumbersClose() {
		if(!ppvs_hover_navnumbers) {
			soap_ppvsnavTimer = setTimeout(function() {
				if(!ppvs_hover_navnumbers)
					$(ppvs_navnumbers).slideUp(300);
			},2000);
		}
	}
	
	/*soap_ppvsResetFilterPanel*/ //called on tab switch when there is no cached filter
	function soap_ppvsResetFilterPanel() {
		ppvs_filtersearch_current = false;
		$(ppvs_filterinput).val(ppvs_inputdefault);
		
		soap_ppvsHideFilterResults();
		
		if(ppvs_activetab == 'MostSeenNiches' && ppvs_path == 'parent') //if in Niches parent tab
			ppvs_filterapply = 'niche';
		else	ppvs_filterapply = 'offer';
		
		$(ppvs_filterapply_btns).removeClass('active'); //remove from both btns
		$(ppvs_filterapply_btns+'_'+ppvs_filterapply).addClass('active');
		
		$(ppvs_filternotice_nomatch).hide();
	}
	
	/*soap_ppvsClearFilter*/
	function soap_ppvsClearFilter() { //reverts filter for this page and clears cache for it. id = # of html wrapper
		var id = '#'+ppvs_cache[ppvs_activetab][ppvs_path]['id'];
		
		soap_ppvsHideFilterResults();
		
		//reset panel
		//$(ppvs_filterinput).val('');
		$(ppvs_filterinput).val('');
		
		//clear cache
		if(ppvs_filter[ppvs_activetab] && ppvs_filter[ppvs_activetab][ppvs_activepagetype])
			ppvs_filter[ppvs_activetab][ppvs_activepagetype] = false;
		
		ppvs_filtersearch_current = false;
		
		//show all elements
		$(id).removeClass('first_override');
		$(id+' .ppvs_item').each(function() {
			$(this).show().removeClass('invisible first_new');
		});
		
		//$(ppvs_filternotice_nomatch).hide();
		soap_ppvsNotice();
	}
	
	/*soap_ppvsSaveFilter*/ //run on page change (via tab or similar) BEFORE updating ppvs_activetab!
	function soap_ppvsSaveFilter(type) { //type is either parent or kid. only storing 2 filters per tab.
		
		if(ppvs_filtersearch_current) {	//if we have a current filter
			if(!ppvs_filter[ppvs_activetab]) //if none is stored yet for this parent
				ppvs_filter[ppvs_activetab] = [];
			
			if(!ppvs_filter[ppvs_activetab][type]) //if none is stored yet for this parent's page type
				ppvs_filter[ppvs_activetab][type] = [];
			
			//update
			ppvs_filter[ppvs_activetab][type]['str'] = ppvs_filtersearch_current;
			ppvs_filter[ppvs_activetab][type]['apply'] = ppvs_filterapply;
		}//endif filter not empty
	}
	
	/*soap_ppvsHideFilterResults*/
	function soap_ppvsHideFilterResults() {
		$(ppvs_filterresults).slideUp(500, function() {
			$(this).html('');
		});
		ppvs_show_filterresults = false;
	}
	
	/*soap_ppvsApplyFilter*/
	function soap_ppvsApplyFilter() {
		var	id = '#'+ppvs_cache[ppvs_activetab][ppvs_path]['id'],
			itemnum = 0,	//for checking if we have any results
			itemstr;
		
		if((ppvs_filtersearch_current != '') && (ppvs_filtersearch_current != ppvs_inputdefault)) {
			
			var search = ppvs_filtersearch_current.toLowerCase();
			
			//add override class to parent, so that galitem.first dont clear anymore
			$(id).addClass('first_override');
			
			$(id+' .ppvs_item').each(function() {
				itemstr = $('.ppvs_item_'+ppvs_filterapply, $(this)).html().toLowerCase();
				
				if(itemstr.indexOf(search) == 0) { //MATCH (item name begins with search string)
					$(this).show().removeClass('invisible first_new'); //.first_override removes clearing for .first
					if(itemnum % 5 === 0)
						$(this).addClass('first_new'); //new first, ha
					
					itemnum++;
					
				} else	$(this).hide().addClass('invisible').removeClass('first_new');
			});
			
			$(ppvs_filterresults).html('<p>'+itemnum+'</p>'); //show # of filtered results, even if 0
			
			if(!ppvs_show_filterresults) { //if filterresults arent showing yet
				ppvs_show_filterresults = true;
				$(ppvs_filterresults).slideDown(500);
			}
		
		} else { //if empty, show all
			soap_ppvsClearFilter();
			itemnum = 'all';
		}
		
		if(itemnum == 0) 
			$(ppvs_filternotice_nomatch).show();
		else	$(ppvs_filternotice_nomatch).hide();
		
	}
	
	/*soap_ppvsNotice*/ //toggles the passed notices. search, search_noresults, suggest. called w/o param = hide all notices AND MESSAGES
	function soap_ppvsNotice(what) { //called only on tab switch. dont use this in filterApply() to save overhead.
		//close all
		$(ppvs_notice+' .ppvc_notice').hide(1, function() {
			if(what) {
				div = ppvs_notice+' .ppvc_notice_'+what;
				$(div).fadeIn(200);
			}
		});
		$(ppvs_message+' .ppvc_notice').hide(); //hide all messages
		
		/*$(ppvs_notice+' .ppvc_notice').each(function() {
			$(this).hide();
			if(what) {
				div = ppvs_notice+' .ppvc_notice_'+what;
				$(div).fadeIn(200);
			}
		});*/
	}
	
	/*soap_ppvsToggleStatus*/ //change the circle status class for each of the tabs
	function soap_ppvsToggleStatus(caller, resulttype, result) { //caller = function thats calling this, resulttype = number or range, result = str to fill
		var nuclass;
		
		if(caller == 'clear')
			nuclass = 'ppvp_status_updating';
			
		else if(ppvs_activetab == 'SearchbyKeyword') {
			if(caller == 'render')
				nuclass = 'ppvp_status_results';
			else	nuclass = 'ppvp_status_search';
		
		} else if(ppvs_activetab == 'SuggestATarget')
			nuclass = 'ppvp_status_suggest';
		
		else	nuclass = 'ppvp_status_results';
		
		//update the graphic
		$(ppvs_status).removeClass().addClass(nuclass);
		
		//number or range
		if(result) {
			if(resulttype == 'total') { //single number
				$(ppvs_status).removeClass('haspages');
				$(ppvs_status_p).removeClass('range').html(result);
			
			} else	{ //else its a range
				$(ppvs_status).addClass('haspages');
				$(ppvs_status_p).addClass('range').html(result);
			}
		}//endif result
	}
	
	/*soap_ppvsValidateInputTxt*/ //checks if the search text entered adheres to our high quality standards, and en-/disables the GO btn
	function soap_ppvsValidateInputTxt(txt) { //txt = $(ppvs_searchinput).val();. func used by keyup in field + on tab switch to search
		
		var gobtn = (ppvs_activetab == 'SearchbyKeyword') ? ppvs_searchgo : ppvs_suggestgo;		
		
		if(txt == '' || txt == ppvs_inputdefault || txt.length < 3) {
			ppvs_searchsuggest_current = false;
			$(gobtn).addClass('inactive');
			
		} else{
			ppvs_searchsuggest_current = txt;
			$(gobtn).removeClass('inactive');
		}
		
		//LATER add pregmatch character check: disallow tags or quotes etc, trim
	}
	
	/*convert json to js*/
	$.parseJSON = function(src) {
		//if(typeof(JSON) == 'object' && JSON.parse)
		//	return JSON.parse(src);
		return eval("(" + src + ")");
	};
	
	/*catch json errors*/ //DEPRECIATED
	function jsonCleanup(response){
		var doc = window.document;
	
		try {
			response = eval("(" + doc.body.innerHTML + ")");
		} catch(err){
			response = {};
		}
	
		return response;
	}
});
</script>
