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
	
	
	/*if(m.constructor.toString().indexOf("Array") == -1)
		
	else {
		for(i=0; i<=m.length-1; i++)
			alert(m[i]);
	}*/
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
				else	ajaxMessage(r.message);	
				
				//if this comes from select btn, close olay
				if(is_select)										
					undoSave2listSelect();
				
				//add icon to orow
				$('#ovault .orow.j_oid-'+oid+' td.td_saved2list .icon').fadeIn(500). removeClass('hide');
			}
		},
		error: function(r) {
			ajaxMessage('Could not save offer, please try again!');
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
				//remove from js obj
				delete ovault_allSavelists['n'+listid];
				
				removeSavelistDarkTableRow(listid); //remove list from table
				rebuildSelecttable();
				
				//if the deleted list was default
				if(ovault_currentSavelist == listid) {
					var	item = $('#ovault_olay_savelists tbody tr'),
						last = item.last();
					
					setTimeout(function() { //wait until row has been deleted
						if(item.length > 0){
							makeSavelistDefault(last.data('listid'), last.data('listname'));	
						
						} else {
							makeSavelistDefault('new', 'New List');
						}
					}, 500);		
				}

				ovault_existSavelistNum--; //decrease existing number of savelists
				
				//update stats: adjust list number, hide offer number until next page load
				var h3 = $('#olay_savedlists .j_oliststats h3.j_savelists_listnum'); 
				listnum = parseInt(h3.html())-1;
				h3.html(listnum);
				
				$('#olay_savedlists .j_oliststats .j_hideonListDelete').fadeOut(500).remove();
				$('<p class="hide">Offer stats will be updated the next time you refresh the page.</p>')
					.appendTo($('#olay_savedlists .j_oliststats')).fadeIn(500).removeClass('hide');

				//update table row count and alt class by going through each and counting from scratch
				var rownum = 1;
				$('#ovault_olay_savelists tbody tr').each(function() {
					if(rownum % 2 == 0)
						$(this).addClass('alt');
					else	$(this).removeClass('alt');
					$('td.no', this).html(rownum+'.');
					rownum++;
				});
				
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
				ajaxMessage(r.error);
			
			else {
				//add to js obj
				ovault_allSavelists['n'+r.listid] = {
					id: r.listid,
					name: name
				};
				
				addSavelistDarkTableRow(r.listid, name); //add new list to table
				rebuildSelecttable(); //rebuild the select table too
				ovault_existSavelistNum++; //increase existing number of savelists 
									
				//update stats
				var h3 = $('#olay_savedlists .olayfeat h3.j_savelists_listnum'); 
				listnum = parseInt(h3.html())+1;
				h3.html(listnum);					
				
				$('#ovault_createnewlistform.hide').fadeOut(200).removeClass('active');
				$('#ovault_newlistname').val('');
				
				ajaxMessage(r.message);
				makeSavelistDefault(r.listid, name); //make this the default
			}
		},
		error: function(r) {
			ajaxMessage('An error occured. Please try again!');
		}
	});
}//doCreateNewList()

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

/*doSearch*/ //updateDial bool set to true only if calling from cook or hash
function doSearch(s, updateDial) {
	
	var target = $('#j_otable tbody');
	
	if(ovault_cache.searchresults[s]) { //dupe code - same as below. maybe outsource to func later
		/*
		
		make this work laterrrrrrrrrrrrr
		
		*/
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
	
	} else {
	
		$.ajax({
			type: 'GET',
			url: ovault_ajaxGet+'?'+s,
			success: function(r) {
				r = $.parseJSON(r);
				
				if(r.error) {
					ajaxMessage(r.error);
					
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
					
					if(updateDial)
						updateDialByHash(r.searchstring);
				}
			},
			error: function(r) {
				ajaxMessage(r);
			}
		});
	}//endif ovault_cached or not
}//doSearch()

/*updateDialByHash*/ //takes r.searchstring, updates everything in the dial. Use after cook or hash.
function updateDialByHash(searchstring) {
	//populate dial
	//search=diet&type=lead&include_networks=1028,1028,1028,1028,1028
	//var params = cook.split('&');
	
	var params = {};
	$.each(searchstring.split('&'), function (i, value) {
		value = value.split('=');
		value1 = value[1].replace(/^A-Za-z0-9-_\+\,\% /g,'');
		value1 = value1.replace(/\%2C/g, ',')
		params[value[0]] = value1.replace(/\+/g, ' ');
	});
	

	if(params['search'] && params['search'] != '') {
		$('#osearch').val(params['search']);	
	}
	
	//networks
	if(params['include_networks'] && params['inclde_networks'] != '') {
		$('#osearch_include_networks').val(params['include_networks']);
		var tmp = params['include_networks'].split(',');
		
		tmp = ArrayUnique(tmp); //filter out dupes
		
		var nwcount = 0;
		
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
		//update number
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

/*makeSavelistDefault*/ //plugs in the passed list as the default
function makeSavelistDefault(listid, name) {
	
	ovault_currentSavelist = listid;
	soap_cookCreate(ovault_cook_LastSaveList,listid,365);
	
	name = soap_truncTxt(name);
	
	$('#odial .save .selebtn').html(name+'<span class="down"></span>');
	$('#olay_savedlists .olaytopflag_big').html(name);
	
	if(listid != 'new')
		ajaxMessage('The <em>'+name+'</em> list is now the default!',1);

}//makeSavelistDefault()

/*addOfferTableRow*/ //adds 1 row. passed var must be an object from ovault_ajaxGetContent, usually r.resultarr[i]
function addOfferTableRow(offer) {
	var out;
	out += '<tr class="orow j_oid-'+offer['id']+'" data-oid="'+offer['id']+'" title="Click to expand or collapse this offer">';
	out += '<td class="border">&nbsp;</td>';
	
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
		
	//offername
	//out += '<td class="td_offername" style="width:465px;"><p>'+offer['title']+'<span>Added '+offer['dateAdded']+'</span></p></td>';
	out += '<td class="td_offername"><p>'+offer['title']+'<span>Added '+offer['dateAdded']+'</span></p></td>';
		
	//payout
	//out += '<td class="td_payout" style="width:54px;"><p>'+offer['payout']+'</p></td>';
	out += '<td class="td_payout"><p>'+offer['payout']+'</p></td>';
		
	//type
	//out += '<td class="td_type" style="width:41px;"><p>'+offer['type']+'</p></td>';
	out += '<td class="td_type"><p>'+offer['type']+'</p></td>';
		
	//vertical
	//out += '<td class="td_vertical" style="width:123px;"><p>'+offer['categoryTitle']+'</p></td>';
	out += '<td class="td_vertical"><p>'+offer['categoryTitle']+'</p></td>';
		
	//network
	//out += '<td class="td_network" colspan="2" style="width:120px;"><p class="icon';
	out += '<td class="td_network" colspan="2"><p class="icon';
		out += offer['isNetworkMember'] == 1 ? ' icon_nwmember' : '';
	out += '">'+offer['networkName']+'</p></td>';
		
	out += '</tr>';
	
	return out;
}//addOfferTableRow

/*addSavelistDarkTableRow*/ //adds a newly created savelist to the .odarktable
function addSavelistDarkTableRow(listid, name) {
	
	var 	parent = $('#ovault_olay_savelists tbody'),
		listnum = parseInt($('tr', parent).length),
		thisnum = listnum+1,
		thislist = $('#ovault_olay_savelists tbody tr.j_list-'+listid), //the created node, for unhiding below
		today = getToday(),
		html = '',
		before = '',
		after = '';
		
	html += '<tr class="j_list-'+listid;
	html += listnum % 2 == 0 ? '' : ' alt';
	html += '" data-listid="'+listid+'" data-listname="'+name+'">';
	
	html += '<td class="no">'+thisnum+'.</td><td class="name">'+soap_truncTxt(name,27)+'<span>Created: '+today+'</span></td>';
	html += '<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td><td class="view"><a class="btn icon_ovault_savelist_view" href="#">View</a></td><td class="download"><a class="btn icon_ovault_savelist_csv" href="#">CSV</a></td><td class="delete"><a class="btn icon_ovault_savelist_delete" href="#">Delete</a></td></tr>';
	
	//if this is their first list ever, we also have to build the table, and the parent changes
	if(ovault_currentSavelist == 'new') {
		parent = $('#olay_savedlists .olaycont');
		before = '<div class="olaybox nomarginbutt"><div class="olayboxtitle myofferlists"><a class="btn ovault_smallyell_deleteall" href="#">Delete All Lists</a></div><table cellspacing="0" cellpadding="0" id="ovault_olay_savelists" class="odarktable"><thead><tr><td class="no">&nbsp;</td><td class="name">Name</td><td class="use">Use</td><td class="view">View</td><td class="download">Download</td><td class="delete">Delete</td></tr></thead><tbody>';
		after = '</tbody></table></div><div class="clear"></div>';
	}
	
	$(before+html+after).appendTo(parent).fadeIn(500, function() {
		if(ovault_currentSavelist == 'new') { //also show the normal box heading
			$('#olay_savedlists .j_olay_savedlists_nolists').slideUp(200, function() {
				$('#olay_savedlists .j_olay_savedlists_havelists').slideDown(400).removeClass('hide');
			}).addClass('hide');				
		}
	})		
}//addSavelistDarkTableRow()

/*removeSavelistDarkTableRow*/ //deletes a table row
function removeSavelistDarkTableRow(listid) {
	$('#ovault_olay_savelists tbody tr.j_list-'+listid).fadeOut('500').remove();
	
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
