/* it's soapdesigned.com */
$(document).ready(function() {
	
	//position
	window.onscroll = function() {
		if(document.documentElement.scrollTop > 215 || self.pageYOffset > 215)
			$('#odial, #opagi_bg').addClass('fix');
		else if(document.documentElement.scrollTop < 215 || self.pageYOffset < 215)
			$('#odial, #opagi_bg').removeClass('fix');
	}
		
	/*general*/
	//expand
	$('#ovault .j_expand, #odial .j_expand').live('click', function() {
		var	target = $('#'+$(this).data('target')),
			closeclass = $('.'+$(this).data('closelcass')+'.active');
			
		if(target.hasClass('active')) {
			target.fadeOut(300, function() {
				$('.hide',target).hide().removeClass('active'); //close possible children
			}).removeClass('active');
				
		} else {
			//close others
			closeclass.fadeOut(300).removeClass('active');
			target.fadeIn(300).addClass('active');
		}
		return false;
	})
	
	$('#ovault .j_orowSelect').live('click', function() {
		ovault_orow_ignoreClick = true;
		undoSave2listSelect();
		return false;
	});
	
	// close
	$('#ovault .j_close, #odial .j_close').click(function() {
		if($(this).hasClass('j_undoSave2listSelect')) 
			undoSave2listSelect();
		
		else {
			var target = $('#'+$(this).data('target'));
			
			target.fadeOut(300, function() {
				$('.hide', target).hide().removeClass('active');
			}).removeClass('active');
		}
		
		return false;
	})
	
	//input label
	$('#ovault input.formtxt, #odial input.formtxt').live('focus', function() {
		if($(this).val() == $(this).prev().html())
			$(this).val('');
		
	})
	
	//hilite
	$('.j_hiliteall').live('click', function() {
		 $(this).focus().select();
	});
	
	/*ini*/
	var hasherror = true;
	
	if(window.location.hash) {		
		var	clean = '',			
			params = {};
			
		hasherror = false;
		
		$.each(window.location.hash.split('&'), function (i, value) {
			value = value.split('=');
			
			if(value[0].indexOf('#') === 0) {
				value[0] = value[0].substr(1);
			}
			
			value1 = value[1].replace(/^A-Za-z0-9-_\+\,\% /g,'');
			value1 = value1.replace(/\%2C/g, ',')
			params[value[0]] = value1.replace(/\+/g, ' ');
		});
		
		//search
		if(params['search'])
			clean = 'search='+params['search'];
		else	hasherror = true;
		
		//type
		if(params['type'] && params['type'] != '')
			clean += '&type='+params['type'];
		else	clean += '&type=lead,sale';
		
		//networks
		if(params['include_networks'] && params['inclde_networks'] != '')
			clean += '&include_networks='+params['include_networks'];
		else	clean += '&include_networks=ALL';
		
		//numresults
		if(	params['numresults'] && params['numresults'] != '' 
			&& (params['numresults'] == 25 || params['numresults'] == 50 || params['numresults'] == 100 || params['numresults'] == 200)
		)
			clean += '&numresults='+params['numresults'];
		else	clean += '&numresults=100';
		
		//page
		if(params['page'] && params['page'] != '')
			clean += '&page='+params['page'];
		else	clean += '&page=1';
		
		//newpage
		if(params['newpage'] && params['newpage'] == 1)
			clean += '&newpage=1';
		
		//sortby
		if(params['sort_by'] && params['sort_by'] != '')
			clean += '&sort_by='+params['sort_by'];
		
		if(!hasherror) {
			doSearch('get=searchresults&'+clean, true);
		}
	}//endif hash
	
	if(hasherror && ovault_cookSearch) { //if no hash but cookie
		if(location.pathname == ovault_searchPage)
			doSearch(ovault_cookSearch, true, 'Your last search has been reactivated.');
		else	updateDialByHash(ovault_cookSearch);
	}
	
	/*orow*/
	//orow expand/collapse
	$('#j_otable tbody .orow').live('click', function() {
		
		//check if the click happened on a select list item or btn
		if(!ovault_orow_ignoreClick) {
			var 	thisrow = $(this);
				ovault_currentOid = $(this).data('oid'),
				addquery = $(this).hasClass('j_oright') ? '&is_oright=1' : ''; //if this is in #oright (also for cache index)
		
			if(thisrow.hasClass('expanded')) { //collapse
				thisrow.removeClass('expanded');
				$('#j_otable tbody .orowbig.j_oid-'+ovault_currentOid).fadeOut(400, function() {
					$(this).remove();
				})
				
			} else { //expand
				
				//check if in ovault_cache already
				if(ovault_cache.offerdetails[ovault_currentOid+addquery]) {
					thisrow.addClass('expanded').after(ovault_cache.offerdetails[ovault_currentOid+addquery]);
					$('#j_otable tbody .orowbig.j_oid-'+ovault_currentOid).slideDown(400);
					
				} else {
					$.ajax({
						type: 'GET',
						url: ovault_ajaxGet+'?get=orowbig&oid='+ovault_currentOid+addquery,
						success: function(r) {
							r = $.parseJSON(r);
							
							if(r.error) {
								ajaxMessage(r);
							} else {
								//add to ovault_cache
								ovault_cache.offerdetails[ovault_currentOid+addquery] = r.html;
								//add to dom
								thisrow.addClass('expanded').after(r.html);
								$('#j_otable tbody .orowbig.j_oid-'+ovault_currentOid).fadeIn(400);
							}
						},
						error: function(r) {
							m = ['Something went wrong, please try again.']
							ajaxMessage(m);
						}
					});
				}//endif in ovault_cache
			}//end collapse/expand
		}//endif ignoreClick
	})//orow expand/collapse
	
	//add/apply shadowbox
	$('a.j_shadowbox').live('click', function() {
		Shadowbox.open({
			content: $(this).attr('href'),
			player: 'iframe',
			title: $(this).attr('title'),
			width: $(this).data('width') ? $(this).data('width') : 640,
			height: $(this).data('height') ? $(this).data('height') : 480,
		});
		return false;
	});
	
	/*odial*/
	//submit
	$('#osearchform').live('submit', function() {			
		var 	error = [],
			search = $('#osearch').val(),
			type = $('#osearch_type').val(),
			include_mysaved = $('#osearch_include_mysaved').val(),
			include_networks = $('#osearch_include_networks').val(),
			numresults = $('#osearch_numresults').val(),
			page = $('#osearch_page').val();
		
		//check options
		if((search == $('#osearch').prev().html()))
			error.push('Please enter a search term. To find all offers from a single network, you can also leave the search field empty or enter * (will only work with 1 network selected).');
		
		if(type == '')
			error.push('Please select at least one conversion type!');
		
		if(include_networks == '' || include_networks == 0)
			error.push('You must include at least one network!');
		
		//errors?
		if(error.length != 0)
			ajaxMessage(error,1);
		
		else {
			//construct string
			var s = 'get=searchresults&search='+search+'&type='+type+'&include_mysaved='+include_mysaved+'&include_networks='+include_networks+'&numresults='+numresults+'&page='+page;
			
			//close any olays
			$('#odial .ovault_olay').fadeOut(300).removeClass('active');
			
			//hide pagination and totalresults
			$('#opagi_bg .totalresults').fadeOut(200);
			$('#opagi .totalresults').fadeOut(100, function() {
				$(this).html('');
			});
			
			if(location.pathname != ovault_searchPage) { //redirect to search page if not on it
				soap_cookCreate(ovault_cook_LastSearch,s,365); //set cookie so that the next page can pick it up
				window.location = ovault_searchPage;
			}
			
			doSearch(s);
			
		}//endif errors
		
		return false;
		
	})//#osearchform submit
	
	//checkbox change
	$('#odial a.ocheck').live('click', function() {
		var	field = $(this).data('hiddenfield'),
			v = $(this).data('value');
			
		if($(this).hasClass('active')) { //uncheck
			odialHiddenFieldUpdate(field, v, 'remove');
			$(this).removeClass('active');
		
		} else { //check
			odialHiddenFieldUpdate(field, v);
			$(this).addClass('active');
		}
		
		return false;
	})//a.ocheck
	
	/*olay*/
	//show olay_simplenext
	$('#numresults_sele .showolay_simplenext').live('click', function() {
		$(this).next().fadeIn(100);
	});
	
	//olaysimple
	$('#numresults_sele .olaysimplenext > *').live('click', function() {
		var val = $(this).data('value');
		$(this).parent().prev().html(val+'<span class="down"></span>');
		$('#osearch_'+$(this).data('hiddenfield')).val(val);
		
		$(this).parent().find('a.active').removeClass('active');
		$(this).addClass('active');
		$(this).parent().fadeOut(300);
		
		return false;
	});
	
	//olay_selelist items
	$('#odial ul.olay_selelist li a').live('click', function() {
		var 	num = $(this).data('number'),
			field = $(this).data('hiddenfield'),
			v = $(this).data('value');
		
		if($(this).hasClass('active')) { //deselect
			odialNumberUpdate(num, true);
			odialHiddenFieldUpdate(field, v, 'remove');
			$(this).removeClass('active');
			
		} else { //add
			odialNumberUpdate(num);
			odialHiddenFieldUpdate(field, v);
			$(this).addClass('active');
		}
			
		return false;
	})
	
	//olay_selelist all/none
	$('#odial a.j_olay_selelist').live('click', function() {
		var 	ul = $('#odial ul.'+$(this).data('ul')),
			field = $(this).data('hiddenfield'),
			action = $(this).data('action'),
			num = $(this).data('number'),
			
			values = []; //in each(), add each item's value to this arr, then pass to odialHiddenFieldUpdate
			
		if(action == 'addall') { //select all
			$('li a', ul).not('.active').each(function() {
				values.push($(this).data('value'));
				odialNumberUpdate(num);
				$(this).addClass('active');
			})
		} else { //remove all
			$('li a.active', ul).each(function() {
				values.push($(this).data('value'));
				odialNumberUpdate(num, true);
				$(this).removeClass('active');
			})
		}
		
		odialHiddenFieldUpdate(field, values, action);
		
		return false;
	})
	
	/*savelists*/
	//create new list
	$('#ovault_createnewlistform, #savelists_oleft_createnewlistform, #savelists_oright_newlistname').live('submit',function() {
			
		if(parseInt(ovault_existSavelistNum) < parseInt(ovault_maxSavelists)) {
			var 	field = $('input.ovault_newlistname', this),
				val = field.val().replace(/[^A-z0-9-_.,:\s]/g,'');
			
			if(val == field.prev().html() || val == '' || val.length < 3 || val.length > 55)
				ajaxMessage('Please enter a valid name for your list! List names should be between 3 and 55 characters long and may not contain any exotic characters.',1);
			
			else 	doCreateNewList(val);
		
		} else {			
			var msg = 'Sorry, but you can\'t have more than '+ovault_maxSavelists+' Offer Lists at the same time. You can delete old lists to make room for new ones.';
			ajaxMessage(msg, true);
		}
		
		return false;
	});
	
	//delete list
	$('#ovault_olay_savelists tbody tr td.delete, #ovault_olay_savelists tbody tr td.delete a, #oright .content .top3 a.ovault_transgray_delete').live('click', function() {
		var	listid = $(this).data('listid')
			listname = $(this).data('listname');
		
		if(confirm("Are you sure you want to delete the\n\n*** "+listname+" ***\n\nOffer List?")) {
			doDeleteList(listid);
		}
		
		return false;
	});
	
	//delete all
	$('#olay_savedlists a.ovault_smallyell_deleteall, #oleft a.ovault_smallgray_deleteall').live('click', function() {
		if(confirm("Are you sure you want to delete ALL your Offer Lists now?")) {
			$.ajax({
				type: 'GET',
				url: ovault_ajaxPut+'?put=deletealllists',
				success: function(r) {					
					r = $.parseJSON(r);					
					if(r.error)
						ajaxMessage(r.error,1);					
					else {
						ovault_existSavelistNum = 0;
						
						if(location.pathname == ovault_searchPage) {
							
							ovault_allSavelists = {}; //kill
							
							//remove html markup
							$('#olay_savedlists .j_olisttable, #olay_savedlists .j_oliststats').fadeOut(1000).remove();
							
							updateOrowSelelistBtn();
							rebuildSelecttable();
							
						} else if(location.pathname == ovault_mysavedPage) {
							//oleft
							$('#oleft tbody tr.oleftrow').each(function() {
								$(this).fadeOut(300, function() {
									$(this).remove();
								}).delay(300);
							});
							
							var html = '<tr class="oleftrow disabled j_list-new active"><td class="hhl">&nbsp;</td><td class="td_oleft"><p class="center">You haven\'t created any Offer Lists yet. Why not <a class="j_expand" href="#" data-target="savelists_oleft_createnewlistform">create one now?</a></p><div class="connector hide"></div></td><td class="hhr">&nbsp;</td></tr>';
					
							$('#oleft tbody').append(html);
							
							//update stats
							$('#oleft .footfeat .hilite h3').html('0');
							
							//oright
							rebuildSavelistOrowrightContent('nolists');
						}
						
						makeSavelistDefault('new', 'New List');											
						ajaxMessage(r.message);
					}
				},
				error: function(r) {
					ajaxMessage('An error occured. Please try again!');
				}
			});
		}//endif confirm
		return false;
	});//delete all lists
	
	
	$('a.ovault_negotiatepayoutterms_trans').live('click', function() {
		
		$.get($(this).attr('href')+'&OfferID='+$(this).attr('OfferID'));
		ajaxMessage('Request successfully sent.');
		
		return false;
	});
	
	
});
