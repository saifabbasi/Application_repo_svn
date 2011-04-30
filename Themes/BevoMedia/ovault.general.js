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
		
	}).live('blur', function() {
		if($(this).val() == '')
			$(this).val($(this).prev().html());
	
	})
	
	/*cook, back*/
	if(ovault_cookSearch) {
		if(location.pathname == ovault_searchPage)
			doSearch(ovault_cookSearch, true);
		else	updateDialByHash(ovault_cookSearch);
	}
	
	/*setInterval(function()	{
		if(window.location.hash != ovault_cache.current_searchstring) {
			//ovault_cache.searchresults[r.searchstring]
			alert('hash changed');
			ovault_cache.current_searchstring = window.location.hash;
		}
	}, 100);*/
	
	/*orow*/
	//orow expand/collapse
	$('#j_otable tbody .orow').live('click', function() {
		
		//check if the click happened on a select list item or btn
		if(!ovault_orow_ignoreClick) {
			var 	thisrow = $(this);
				ovault_currentOid = $(this).data('oid'); //the network ID to fetch
		
			if(thisrow.hasClass('expanded')) { //collapse
				thisrow.removeClass('expanded');
				$('#j_otable tbody .orowbig.j_oid-'+ovault_currentOid).fadeOut(400, function() {
					$(this).remove();
				})
				
			} else { //expand
				
				//check if in ovault_cache already
				if(ovault_cache.offerdetails[ovault_currentOid]) {
					thisrow.addClass('expanded').after(ovault_cache.offerdetails[ovault_currentOid]);
					$('#j_otable tbody .orowbig.j_oid-'+ovault_currentOid).slideDown(400);
				
				} else {
					$.ajax({
						type: 'GET',
						url: ovault_ajaxGet+'?get=orowbig&oid='+ovault_currentOid,
						success: function(r) {
							r = $.parseJSON(r);
							
							//$('#ovault').prepend(r);
							
							//alert(r.html);
							
							if(r.error) {
								ajaxMessage(r);
							} else {
								//add to ovault_cache
								ovault_cache.offerdetails[ovault_currentOid] = r.html;
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
		if((search == $('#osearch').prev().html()) || (search == ''))
			error.push('Please enter an offer name or vertical to search for!');
		
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
});
