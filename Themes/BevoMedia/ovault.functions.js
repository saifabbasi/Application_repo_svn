/* it's soapdesigned.com */

/*ajaxMessage*/ //no arrays, only strings! sticky: bool if true, its NOT really sticky, just sticks longer and is titled "Notice".
function ajaxMessage(m, sticky) {
	if(sticky) {
		$.gritter.add({
			title: 'Notice',
			text: m,
			time: 5000
		});
	} else {
		$.gritter.add({
			text: m
		});
	}
}//ajaxMessage()

/*doSave2List*/ //is_select bool true if it's  a.ovault_add2list_select calling
function doSave2List(listid, oid, is_select) {
	$.ajax({
		type: 'GET',
		url: ovault_ajaxPut+'?put=save2list&list='+listid+'&oid='+oid,
		success: function(r) {				
			r = $.parseJSON(r);
					
			if(r.error)
				ajaxMessage(r.error,1);

			else {
				//get list name from js obj
				if(ovault_allSavelists['n'+listid])
					ajaxMessage('Offer added to the <em>'+ovault_allSavelists['n'+listid].name+'</em> list.')
				
				else if(r.newlist_created) { //if new list
					msg = 'We have created a new Offer List for you, and the offer has been added to it!<br /><br />';
					msg += 'You can manage your list(s) <a href="/BevoMedia/Offers/MySavedLists.html/">on this page</a>. For example, you can rename this list, as it has been auto-generated. The My Saved Lists page is where you can review any offers that you have added to your lists, as well as download them as a CSV file.';
										
					//add to obj, dom, and make default
					doCreateNewList_AjaxSuccess(r.newlist_created, r.newlist_created.newlistname, 1); //3rd param silent = no msgs
					
					ajaxMessage(msg,1);
				}
				
				//if this comes from select btn, close olay
				if(is_select)										
					undoSave2listSelect();
				
				//add icon to orow
				$('#ovault .orow.j_oid-'+oid+' td.td_saved2list .icon').fadeIn(500).removeClass('hide');
			}
		},
		error: function(r) {
			ajaxMessage('Could not save offer, please try again!',1);
		}
	});
}//doSave2List()

/*doDeleteList*/
function doDeleteList(listid) {
	$.ajax({
		type: 'GET',
		url: ovault_ajaxPut+'?put=deletelist&list='+listid,
		success: function(r) {				
			r = $.parseJSON(r);
			
			if(r.error)
				ajaxMessage(r.error);
			
			else {
				ovault_existSavelistNum--;
				
				var 	statnumber,
					updateOnListDelete, //offercount wrapper that will be updated, per page
					updateRownum, //offer list rows that have to be renumbered
					updateRownumNumberField, //number html tag for the above
					otherlists = false, //class of other savelists, set per page
					lastlist; //last savelist in line, per page
				
				if(location.pathname == ovault_searchPage) {				
					delete ovault_allSavelists['n'+listid]; //remove from obj
					
					updateOrowSelelistBtn();
					removeSavelistDarkTableRow(listid); //remove list from table
					rebuildSelecttable();					
					
					if(ovault_currentSavelist == listid) { //if the deleted list was default
						otherlists = $('#ovault_olay_savelists tbody tr');
						lastlist = otherlists.last();
					}

					statnumber = $('#olay_savedlists .olayfeat h3.j_savelists_listnum');					
					updateOnListDelete = $('#olay_savedlists .j_oliststats .j_updateOnListDelete');					
					updateRownum = $('#ovault_olay_savelists tbody tr');
					updateRownumNumberField = 'td.no';
					
				} else if(location.pathname == ovault_mysavedPage) {
					removeSavelistOleftRow(listid);
					
					//can only delete default/active lists on this page
					otherlists = $('#oleft table tbody tr.oleftrow');
					lastlist = otherlists.last();
					
					//fill default if this was the last one
					if(ovault_existSavelistNum == 0) {
						var html = '<tr class="oleftrow disabled j_list-new active"><td class="hhl">&nbsp;</td><td class="td_oleft"><p class="center">You haven\'t created any Offer Lists yet. Why not <a class="j_expand" href="#" data-target="savelists_oleft_createnewlistform">create one now?</a></p><div class="connector hide"></div></td><td class="hhr">&nbsp;</td></tr>';
					
						$('#oleft tbody').append(html);
						
						rebuildSavelistOrowrightContent('nolists');
					
					} else { //fetch new list and fill content
						
						var listdata = {
							listid: lastlist.data('listid'),
							name: lastlist.data('listname'),
							listcount: lastlist.data('listcount'),
							num_offers: lastlist.data('num_offers'),
							created: lastlist.data('created')
						};
						
						doSavelistFetchOffers(listdata, 1);						
						
					}//endif lists lest
					
					statnumber = $('#oleft .footfeat h3.j_savelists_listnum');
					updateOnListDelete = $('#oleft .footfeat .j_updateOnListDelete');
					updateRownum = $('#oleft tbody tr.oleftrow');
					updateRownumNumberField = 'td.td_oleft h3 span.no';
				
				} //endif page
				
				//make default
				if(otherlists) { //if we have otherlists, that means that the deleted list was default
					if(otherlists.length > 0) { //if we have other lists
						setTimeout(function() { //wait until row has been deleted
							makeSavelistDefault(lastlist.data('listid'), lastlist.data('listname'));	
						}, 500);
					} else {
						makeSavelistDefault('new', 'New List');
					}
				}
				
				//update table row count
				var rownum = 1;
				
				updateRownum.each(function() {
					if(rownum % 2)
						$(this).removeClass('alt');
					else	$(this).addClass('alt');
					$(updateRownumNumberField, this).html(rownum+'.');
					rownum++;
				});
				
				//offercount update
				updateOnListDelete.fadeOut(500, function() {
					$(this).html('<p>Offer stats will be updated the next time you refresh the page.</p>').fadeIn(500);
				});
				
				statnumber.html(ovault_existSavelistNum); //update list stat
				ajaxMessage(r.message);
			}
		},
		error: function(r) {
			ajaxMessage('An error occured. Please try again!');
		}
	});
}//doDeleteList

/*doCreateNewList*/
function doCreateNewList(name) {
	$.ajax({
		type: 'GET',
		url: ovault_ajaxPut+'?put=createnewlist&newlistname='+name,
		success: function(r) {				
			r = $.parseJSON(r);
			
			if(r.error)
				ajaxMessage(r.error,1);
			
			else {
				doCreateNewList_AjaxSuccess(r, name);
			}
		},
		error: function(r) {
			ajaxMessage('An error occured. Please try again!',1);
		}
	});
}//doCreateNewList()

/*doCreateNewList_AjaxSuccess*/ //called by doCreateNewList() on ajax success. silent bool true if called by any other function, eg by doSave2List when a new list was created
function doCreateNewList_AjaxSuccess(r, name, silent) {
	ovault_existSavelistNum++;
	var statnumber = false;
		
	if(location.pathname == ovault_searchPage) {			
		
		//add to js obj
		ovault_allSavelists['n'+r.listid] = { 
			id: r.listid,
			name: name
		};
		
		updateOrowSelelistBtn();
		addSavelistDarkTableRow(r.listid, name); //add new list to table
		rebuildSelecttable(); //rebuild the select table too
		
		$('#ovault_createnewlistform.hide').fadeOut(200, function() {
			$('#ovault_newlistname').val('');
			$('#olay_savedlists').fadeOut(600, function() {
				$(this).removeClass('active');
			})
		}).removeClass('active');
	
		statnumber = $('#olay_savedlists .olayfeat h3.j_savelists_listnum'); 										
		
	} else if(location.pathname == ovault_mysavedPage) {
		
		addSavelistOleftRow(r.listid, name);
		makeSavelistDefault(r.listid, name);
		
		//hide form in oleft
		$('#savelists_oleft_createnewlistform').fadeOut(500).removeClass('active');
		$('#savelists_oleft_createnewlistform input.formtxt.ovault_newlistname').val('');
	
		var listdata = {
			listid: r.listid,
			name: name,
			listcount: ovault_existSavelistNum,
			num_offers: 0,
			created: getToday()
		};
		
		rebuildSavelistOrowrightContent('nooffers', listdata);
							
		statnumber = $('#oleft .footfeat h3.j_savelists_listnum'); 
		
	}//endif page
	
	if(statnumber)
		statnumber.html(ovault_existSavelistNum); //update stats
	
	if(!silent && r.message)
		ajaxMessage(r.message);
	
	makeSavelistDefault(r.listid, name); //make this the default
}//doCreateNewList_AjaxSuccess

/*doSavelistFetchOffers*/ //dontDefault bool if true, dont makeSavelistDefault (use in doDeleteList when setting default separately)
function doSavelistFetchOffers(listdata, dontDefault) {
	$.ajax({
		type: 'GET',
		url: ovault_ajaxGet+'?get=savelistoffers&list='+listdata.listid,
		success: function(r) {
			r = $.parseJSON(r);
			
			if(r.error) {
				ajaxMessage(r.error);
				
			} else {
				if(!dontDefault)
					makeSavelistDefault(listdata.listid, listdata.name);
				
				rebuildSavelistOrowrightContent(r.resultarr, listdata);				
			}
		},
		error: function(r) {
			ajaxMessage(r);
		}
	});//ajax
}//doSavelistFetchOffers()

/*doSavelistDeleteOffer*/
function doSavelistDeleteOffer(oid, listid) {	
	$.ajax({
		type: 'GET',
		url: ovault_ajaxPut+'?put=deletesavelistoffer&oid='+oid+'&listid='+listid,
		success: function(r) {
			r = $.parseJSON(r);
			
			if(r.error) {
				ajaxMessage(r.error,1);
				
			} else {
				//remove orowbig if exists, and orow
				$('#oright #j_otable tbody tr.orowbig.j_oid-'+oid).fadeOut(500, function() {
					$(this).remove();
				});
				
				$('#oright #j_otable tbody tr.orow.j_oid-'+oid).fadeOut(500, function() {
					$(this).remove();
				});
				
				//update offer counts
				var countfields = [
					$('#oleft .footfeat .hilite.second h3'),
					$('#oleft tbody tr.oleftrow.j_list-'+listid+' td.td_oleft .offercount'),
					$('#oright .conttop .top3 .footfeat .hilite.second h3')
				]
				for(i=0; i<=2; i++) {
					count = parseInt(countfields[i].html()) - 1;
					countfields[i].html(count);
				}
				
				ajaxMessage(r.message);
				
				ovault_orow_ignoreClick = false;
			}
		},
		error: function(r) {
			ajaxMessage(r,1);
		}
	});
}//doSavelistDeleteOffer()

/*insertSave2listSelect*/ //thisbtn: $(this) of calling a.ovault_add2list_select
function insertSave2listSelect(thisbtn) {
	var	wrap = $('#j_olay_savedlists_select_wrap'),
		olay = $('#olay_savedlists_select'),
		container = thisbtn.next('.olay_container'),
		content = wrap.html();
		
	ovault_currentAdd2listSelectOid = thisbtn.data('oid');
	
	thisbtn.addClass('active').attr('id','j_currentSave2listSelectBtn');
	thisbtn.parents('tr.orow').addClass('selectpersist');
	
	wrap.html(''); //remove from wrap to avoid any issues
	$(container).html(content);
	olay.slideDown(300, function() {
		$(this).removeClass('hide');
	});
	
}//insertSave2listSelect

/*undoSave2listSelect*/
function undoSave2listSelect() {							
	var thisbtn = $('#j_currentSave2listSelectBtn');
	
	if(thisbtn.length > 0) { //if a current select list is open
		var	wrap = $('#j_olay_savedlists_select_wrap'),
			olay = $('#olay_savedlists_select'),
			container = thisbtn.next('.olay_container'),
			content = container.html();
			
		thisbtn.removeClass('active').removeAttr('id');
		thisbtn.parents('tr.orow').removeClass('selectpersist');
		olay.fadeOut(300);
		container.html('');
		wrap.html(content);
		olay.addClass('hide'); //cant do this above bc this comes from the var
	}
	
	ovault_currentAdd2listSelectOid = false;
	ovault_orow_ignoreClick = false;
	
}//undoSave2listSelect()

/*doSearch*/
//updateDial bool set to true only if calling from cook or hash
//customMessage str optional custom message, e.g. when auto-search was loaded by hash or cookie
function doSearch(s, updateDial, customMessage) {
	
	var target = $('#j_otable tbody');
	
	if (s.indexOf('newpage')==-1) {
		ovault_cache.current_page = 1;
	}
	
	if (s.indexOf('sort_by')==-1) {
		ovault_cache.sort_by = '';
		ovault_cache.sort_by_direction = '';
	}
	
	$.ajax({
		type: 'GET',
		url: ovault_ajaxGet+'?'+s,
		success: function(r) {
			r = $.parseJSON(r);
			
			if(r.error) {
				ajaxMessage(r.error,1);
				
			} else {
				
				soap_cookCreate(ovault_cook_LastSearch,r.searchstring,365);
				
				ovault_cache.searchresults[r.searchstring] = r.resultarr;
				ovault_cache.current_searchstring = r.searchstring;
				window.location.hash = r.searchstring;
				
				target.html(''); //remove old content
				for(var i in r.resultarr) { //add to dom
					target.append(addOfferTableRow(r.resultarr[i]));
				}
				
				//total results
				$('#opagi_bg .totalresults').fadeIn(500);
				$('#opagi .totalresults').html(r.totalresults).fadeIn(400);
				
				//pagination
				if(r.pagination) {
					$('#opagi_bg .numbers').fadeIn(400, function() {
						$('#opagi .numbers').html(r.pagination).fadeIn(300);
					});
				} else {
					$('#opagi .numbers').fadeOut(400, function() {
						$(this).html('');
					});
					$('#opagi_bg .numbers').fadeOut(300);
				}
				
				updateOrowSelelistBtn();
				
				if(updateDial)
					updateDialByHash(r.searchstring);
				
				if(customMessage)
					ajaxMessage(customMessage,1);
				
				if(r.message)
					ajaxMessage(r.message,1);
				
				if(r.message_once) {
					var once = soap_cookRead(ovault_cook_messageOnce);
					
					if(!once) {
						ajaxMessage(r.message_once,1);
						soap_cookCreate(ovault_cook_messageOnce,1,14);
					}	
				}
				
			}//endif r.error
		},
		error: function(r) {
			ajaxMessage(r,1);
		}
	});//ajax
}//doSearch()

/*updateDialByHash*/ //takes r.searchstring, updates everything in the dial. Use after cook or hash.
function updateDialByHash(searchstring) {
	//populate dial	
	var params = {};
	$.each(searchstring.split('&'), function (i, value) {
		value = value.split('=');
		value1 = value[1].replace(/^A-Za-z0-9-_\+\,\% /g,'');
		value1 = value1.replace(/\%2C/g, ',')
		params[value[0]] = value1.replace(/\+/g, ' ');
	});
	

	if(params['search']) {
		$('#osearch').val(unescape(params['search']));	
	} else {
		$('#osearch').val('');
	}
	
	//networks
	if(params['include_networks'] && params['inclde_networks'] != '') {
		var nwcount = 0;
		
		//if ALL
		if(params['include_networks'] == 'ALL' || params['include_networks'] == 'all' || params['include_networks'] == 'All') {
			
			params['include_networks'] = 'ALL'; //overwrite with allcaps
			
			$('#olay_networks ul.j_olay_allnetworkslist li a, #olay_networks ul.j_olay_mynetworklist li a').each(function() {
				nwcount++;
				$(this).addClass('active');
			});
			
		} else {	
			var tmp = params['include_networks'].split(',');		
			tmp = ArrayUnique(tmp); //filter out dupes
					
			//first deactivate all
			$('#olay_networks ul.j_olay_allnetworkslist li a, #olay_networks ul.j_olay_mynetworklist li a').removeClass('active');
			//then activate the right ones
			$.each(tmp, function(i, value) {
				$('#olay_networks ul.j_olay_allnetworkslist li a, #olay_networks ul.j_olay_mynetworklist li a').each(function() {
					if($(this).hasClass('j_nwid-'+value)) {
						nwcount++;
						$(this).addClass('active');
					}
				});
			});
		}//endif all
		
		//update hidden and number
		$('#osearch_include_networks').val(params['include_networks']);
		$('#number_networks').html(nwcount);
	}
	
	//type
	if(params['type'] && params['type'] != '') {
		tmp = params['type'].split(',');
		
		if(tmp.length == 2) {
			$('#osearchform .ocheck_lead').addClass('active');
			$('#osearchform .ocheck_sale').addClass('active');
			$('#osearch_type').val('lead,sale');
		} else if(tmp[0] == 'lead') {
			$('#osearchform .ocheck_lead').addClass('active');
			$('#osearchform .ocheck_sale').removeClass('active');
			$('#osearch_type').val('lead');
		} else if($tmp[0] == 'sale') {
			$('#osearchform .ocheck_lead').removeClass('active');
			$('#osearchform .ocheck_sale').addClass('active');
			$('#osearch_type').val('sale');
		}
	}
	
	//mysaved
	if(params['include_mysaved'] && params['include_mysaved'] != '') {
		$('#osearchform .ocheck_mysaved').addClass('active');
		$('#osearch_include_mysaved').val('1');
	}
	
	//numresults
	if(params['numresults'] && params['numresults'] != '') {
		$('#osearch_numresults').val(params['numresults']);
		$('#numresults_sele .showolay_simplenext').html(params['numresults']+'<span class="down"></span>');
		$('#numresults_sele .olaysimplenext > *').each(function() {
			if($(this).hasClass('numresults-'+params['numresults']))
				$(this).addClass('active');
			else	$(this).removeClass('active');
		});
	}
}//updateDialByHash()

/*odialHiddenFieldUpdate*/
//hiddenfield = 2nd part of the ID of the hidden field, after #osearch_
//value = either the value of a single button, or an array of multiple values (for addall and removeall)
//action (optional) if not set, it's getting added
function odialHiddenFieldUpdate(hiddenfield, value, action) {
	var	hiddenfield = $('#osearch_'+hiddenfield), //target hidden field
		currval = hiddenfield.val(); //current val of field
		
		bool = ((value === 0) || (value === 1)) ? true : false; //whether or not the field is boolean (right now only true for include_mysaved)
	
	if(action == 'remove') {
		if(bool)
			hiddenfield.val(0);
		else	hiddenfield.val(currval.replace(value, ''));
		
	} else if(action == 'removeall' && value.length > 0) {
		for(i=0; i<=value.length-1; i++) {
			currval = currval.replace(value[i], '');
			hiddenfield.val(currval);
		}
		
	} else if(action == 'addall' && value.length > 0) {
		var tmp;
		for(i=0; i<=value.length-1; i++)
			tmp += ','+value[i];
		
		hiddenfield.val(currval+tmp);
	
	} else { //add single
		if(bool)
			hiddenfield.val(1);
		else	hiddenfield.val(currval+','+value);		
	}	
}//odialNumberUpdate()

/*odialNumberUpdate*/
function odialNumberUpdate(id, subtract) {
	if(subtract)
		$('#'+id).html(parseInt($('#'+id).html())-1);
	else	$('#'+id).html(parseInt($('#'+id).html())+1);			
}//odialNumberUpdate()

/*makeSavelistDefault*/ //plugs in the passed list as the default. 
//redirect bool if true, no msgs are spawned and we redirect to the list management page
function makeSavelistDefault(listid, name, redirect) {
	
	var 	newlistname = 'My First Savelist (auto-generated)', //static name of autogenerated list
		newlistname_short = 'My First Savelist' //short version
	
	ovault_currentSavelist = listid;
	soap_cookCreate(ovault_cook_LastSaveList,listid,365);
	
	name = name == newlistname ? newlistname_short : soap_truncTxt(name);
	
	if(redirect) {
		window.location = ovault_mysavedPage; //savelist loads from cookie
	
	} else {
		if(location.pathname == ovault_searchPage) { //on search page only
			$('#odial .save .selebtn').html(name+'<span class="down"></span>');
			$('#olay_savedlists .olaytopflag_big').html(name);
			
			if(listid != 'new')
				if(name != newlistname_short) //if we didnt just delete the last list, or spawn a long msg about the auto-generated new one
					ajaxMessage('The <em>'+name+'</em> list is now the default!');
		
		} else if(location.pathname == ovault_mysavedPage) {
			$('#oleft tbody tr.active').removeClass('active');
			$('#oleft tbody tr.j_list-'+listid).addClass('active');
		}
	}

}//makeSavelistDefault()

/*updateOrowSelelistBtn*/ //shows or hides the orow sele btn. call after every search and after every list add/delete action, after setting ovault_existSavelistNum
function updateOrowSelelistBtn() {
	if(ovault_existSavelistNum <= 1) { //1 or no lists
		$('#ovault .orow .td_savelist a.ovault_add2list_select').addClass('permahide');
	
	} else { //more than 1 list
		$('#ovault .orow .td_savelist a.ovault_add2list_select').removeClass('permahide');
	}	
}//updateOrowSelelistBtn()

/*addOfferTableRow*/
//adds 1 row. passed var must be an object from ovault_ajaxGetContent, usually r.resultarr[i]
//oright bool if true, formats row for Mysavedlists.html
function addOfferTableRow(offer, oright) {
	
	var out = '';
	out += '<tr class="orow';
		out += oright ? ' j_oright' : '';
	out += ' j_oid-'+offer['id']+'" data-oid="'+offer['id']+'" title="Click to expand or collapse this offer">';
	out += '<td class="border">&nbsp;</td>';
	
	if(!oright) {
		//saved2list
		//out += '<td class="td_saved2list" style="width:15px;">';
		out += '<td class="td_saved2list">';
			out += '<div class="icon icon_ovault_added2list';
			out += offer['saved2list'] == 1 ? '' : ' hide';
			out += '" title="You have already saved this offer"></div>';
		out += '</td>';
		
		//savelist
		//out += '<td class="td_savelist" style="width:40px;">';
		out += '<td class="td_savelist">';
			out += '<a class="btn ovault_add2list j_orowSelect" href="#" data-oid="'+offer['id']+'" title="Add this offer to the active list">Add</a>';
			out += '<a class="btn ovault_add2list_select j_orowSelect" href="#" data-oid="'+offer['id']+'" title="Select a list to add this offer to...">Select</a><div class="olay_container"></div></td>';
	}
		
	//offername
	//out += '<td class="td_offername" style="width:465px;"><p>'+offer['title']+'<span>Added '+offer['dateAdded']+'</span></p></td>';
	out += '<td class="td_offername"><p>'+offer['title']+offer['dateAdded']+'</p></td>';
		
	//payout
	//out += '<td class="td_payout" style="width:54px;"><p>'+offer['payout']+'</p></td>';
	out += '<td class="td_payout"><p>'+offer['payout']+'</p>';
	out += '</td>';
		
	//type
	//out += '<td class="td_type" style="width:41px;"><p>'+offer['type']+'</p></td>';
	out += '<td class="td_type"><p>'+offer['offerType']+'</p></td>';
		
	//vertical
	//out += '<td class="td_vertical" style="width:123px;"><p>'+offer['categoryTitle']+'</p></td>';
	out += '<td class="td_vertical"><p>'+offer['categoryTitle']+'</p></td>';
		
	//network
	//out += '<td class="td_network" colspan="2" style="width:120px;"><p class="icon';
	out += '<td class="td_network" colspan="';
		out += oright ? '' : '2';
	out += '"><p class="icon';
		out += offer['isNetworkMember'] == 1 ? ' icon_nwmember' : '';
	out += '">'+offer['networkName']+'</p></td>';
	
	if(oright) {
		out += '<td class="td_delete"><a class="btn ovault_olay_close_gray" href="#">Delete this offer from list</a>';
		out += '<td class="tail">&nbsp;</td>';
	}
		
	out += '</tr>';
	
	return out;
}//addOfferTableRow

/*addSavelistDarkTableRow*/ //adds a newly created savelist to the .odarktable
function addSavelistDarkTableRow(listid, name) {
		
	var 	parent = $('#ovault_olay_savelists tbody'),
		today = getToday(),
		html = '',
		before = '',
		after = '';
		
	html += '<tr class="j_list-'+listid;
	html += ovault_existSavelistNum % 2 == 0 ? ' alt' : '';
	html += '" data-listid="'+listid+'" data-listname="'+name+'">';
	
	html += '<td class="no">'+ovault_existSavelistNum+'.</td><td class="name">'+soap_truncTxt(name,27)+'<span>Created: '+today+'</span></td>';
	html += '<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td><td class="view"><a class="btn icon_ovault_savelist_view" href="#">View</a></td>';
	html += '<td class="delete" data-listid="'+listid+'" data-listname="'+name+'"><a class="btn icon_ovault_savelist_delete" href="#" data-listid="'+listid+'" data-listname="'+name+'">Delete</a></td></tr>';
	
	//if this is their first list, we also have to build the table, and the parent changes
	if(ovault_existSavelistNum == 1) {
		parent = $('#olay_savedlists .olaycont .floatleft');
		before = '<div class="olaybox nomarginbutt j_olisttable"><div class="olayboxtitle myofferlists"><a class="btn ovault_smallyell_deleteall" href="#">Delete All Lists</a></div><table cellspacing="0" cellpadding="0" id="ovault_olay_savelists" class="odarktable"><thead><tr><td class="no">&nbsp;</td><td class="name">Name</td><td class="use">Use</td><td class="view">View</td><td class="delete">Delete</td></tr></thead><tbody>';
		after = '</tbody></table></div><div class="clear"></div>';
	}
	
	$(before+html+after).appendTo(parent).fadeIn(500, function() {
		if(ovault_existSavelistNum == 1) { //also show the normal box heading if new
			$('#olay_savedlists .j_olay_savedlists_nolists').slideUp(200, function() {
				$('#olay_savedlists .j_olay_savedlists_havelists').slideDown(400).removeClass('hide');
			}).addClass('hide');				
		}
	})		
}//addSavelistDarkTableRow()

/*removeSavelistDarkTableRow*/ //deletes a table row
function removeSavelistDarkTableRow(listid) {
	var table = '#ovault_olay_savelists';
	$(table+' tbody tr.j_list-'+listid).fadeOut('500').remove().delay(100, function() {
		if($(table+' tbody tr').length == 0) { //if this was the last row, we also have to remove the table
			$('#olay_savedlists .j_olisttable').fadeOut(200, function() {
				$(this).remove();
			});
		}	
	});
		
	
}//removeSavelistDarkTableRow

/*rebuildSelecttable*/ //called after list create/delete to update the select olay for orows
function rebuildSelecttable() {
	var seletable = $('#ovault_olay_savelists_select tbody');
	
	seletable.html('');		
	var selecount = 1;
	
	$.each(ovault_allSavelists, function(i, list) {
			
		var r;
		r += '<tr title="Select this list" class="j_list-'+list.id;			
		r += selecount % 2 ? '' : ' alt';
		r += '" data-listid="'+list.id+'" data-listname="'+list.name+'">';
		r += '<td class="no">'+selecount+'.</td>';
		r += '<td class="name">'+soap_truncTxt(list.name,27);
		//r += '<span>Created: '.$nicedate.'</span>'; //LATER: see if we can add the unix timestamp via php and add it to obj
		r += '</td><td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td></tr>';
		
		seletable.append(r);
		selecount++;
	});
	
}//rebuildSelecttable()

/*addSavelistOleftRow*/
function addSavelistOleftRow(listid, name) {
	var	parent = $('#oleft tbody'),
		today = getToday(),
		html = '';
		
	html += '<tr class="oleftrow j_list-'+listid+'" data-listid="'+listid+'" data-listname="'+name+'" data-listcount="'+ovault_existSavelistNum+'" data-num_offers="0" data-created="'+today+'">';
		html += '<td class="hhl">&nbsp;</td>';
		html += '<td class="td_oleft">';
			html += '<h3><span class="no">'+ovault_existSavelistNum+'</span> '+soap_truncTxt(name,27)+'</h3>';
			html += '<span class="created">Created: '+today+'</span>';
			html += '<div class="offercount">0</div>';
			html += '<div class="connector hide"></div>';
		html += '</td><td class="hhr">&nbsp;</td></tr>';
		
	//if this is the first list, need to first remove existing content
	if(ovault_existSavelistNum == 1) { //var has been increased before calling func
		parent.html('');
	}
	
	parent.append(html);
	
}//addSavelistOleftRow

/*removeSavelistOleftRow*/
function removeSavelistOleftRow(listid) {
	$('#oleft tbody tr.oleftrow.j_list-'+listid).fadeOut('500').remove();
}//removeSavelistOleftRow

/*rebuildSavelistOrowrightContent*/ //listdata obj of all list metadata (optional)
function rebuildSavelistOrowrightContent(resultarr, listdata) {
	var target = $('#oright');
	
	if(resultarr == 'nolists') {
		nolists = $('#j_oright_defaults_body_nolists').html();
		target.html(nolists);
	
	} else { //if we have lists
		var html = $('#j_oright_defaults_body').html();
		
		target.html(html);					
		$('table.btable', target).attr('id', 'j_otable'); //add table id
		
		var table = $('#j_otable tbody');
		
		if(resultarr == 'nooffers' || !resultarr[0]) {
			//have to do this here
			nooffers = '<tr class="message"><td class="border">&nbsp;</td><td colspan="6" style="padding:25px 0;text-align:center;">';
			nooffers += 'You can start adding offers to this list! To find new offers, use the Bevo Search Sphere at the top. Then use the yellow button to the left of every offer in the search results to add that offer to your list.';
			nooffers += '</td><td class="tail">&nbsp;</td></tr>';
			
			table.append(nooffers);
			
		} else {
			for(var i in resultarr) { //add to dom
				table.append(addOfferTableRow(resultarr[i],1));
			}
		}
		
		if(listdata) {
			//fill in header
			$('#oright .content .conttop .top1 p').html(listdata.listcount+'.');
			$('#oright .content .conttop .top2 h2').html(listdata.name);
			$('#oright .content .conttop .top2 .subsmall').html(listdata.created);
			$('#oright .content .conttop .top3 .footfeat .hilite.second h3').html(listdata.num_offers);
			
			//form+btns
			$('#oright .content .conttop .top2 form.ovault_mysaved_renamelistform').attr('id','ovault_mysaved_renamelistform-'+listdata.listid).data('listid',listdata.listid).data('listname',listdata.name);
			$('#oright .content .conttop .top2 form.ovault_mysaved_renamelistform input.formtxt.ovault_renamelistname').val(listdata.name);
			$('#oright .content .conttop .top2 form.ovault_mysaved_renamelistform a.btn.ovault_olay_close_gray').data('target','ovault_mysaved_renamelistform-'+listdata.listid);
			$('#oright .content .conttop .top2 a.btn.ovault_transgray_rename').data('target','ovault_mysaved_renamelistform-'+listdata.listid);			
			$('#oright .content .conttop .top3 a.btn.ovault_transgray_delete').data('listid',listdata.listid).data('listname',listdata.name);
			
			//if we have at least 1 offer
			if(listdata.num_offers >= 1)
				$('#oright .conttop .top4').append('<a class="tbtn" href="?ExportTo=CSV">Export to CSV</a><div class="clear"></div>');			
		}
		
	} //endif nolists
	
	adjustOrightHeight();
	
}//rebuildSavelistOrowrightContent

/*adjustOrightHeight*/ //call on page load and after any ajax
function adjustOrightHeight() {
	var oleft_height = $('#oleft').outerHeight(false) - 130;
	$('#oright .content').css({minHeight:oleft_height});
}//adjustSavelistOrightHeight

/*json*/
$.parseJSON = function(src) {
	//if(typeof(JSON) == 'object' && JSON.parse)
	//	return JSON.parse(src);
	return eval("(" + src + ")");
};

/*today*/
function getToday() {
	var	months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],			
		currentTime = new Date(),
		month = months[currentTime.getMonth()],
		day = currentTime.getDate(),
		today = month+' '+day;
	
	return today;
}

/*rating stars*/
//network star rating
function ratingTill(id, amnt, state) {
	for(var i=1; i<6; i++) {
		document.getElementById(id+"_"+i).src = '/Themes/BevoMedia/img/star-off.gif';
	}
	for(var i=1; i<amnt+1; i++) {
		document.getElementById(id+"_"+i).src = '/Themes/BevoMedia/img/star-on.gif';
	}
}

function ratingRst(id, amnt) {
	for(var i=1; i<6; i++) {
		document.getElementById(id+"_"+i).src = '/Themes/BevoMedia/img/star-on.gif';
	}
	for(var i=amnt+1; i<6; i++) {
		document.getElementById(id+"_"+i).src = '/Themes/BevoMedia/img/star-off.gif';
	}
}
