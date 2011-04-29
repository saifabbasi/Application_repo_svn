<?php /* OLD PAGE HEADER, before offerzzz. can go!

<?php
//*************************************************************************************************

require_once(PATH . "Legacy.Abstraction.class.php");

		global $userId;
		$userId = $this->User->id;
//*************************************************************************************************

		$isOffersPage		= true;
		$showOffersPanes	= true;

//*************************************************************************************************

		//$arrModels		= array('CPA', 'CPM', 'CPC');
		$arrModels		= array('CPA');
		
//*************************************************************************************************

		$today = date('Y-m-d');
		
		$arrNetworks = array();
		$res = LegacyAbstraction::executeQuery("SELECT N.ID, N.TITLE, N.MODEL, S.IMPRESSIONS, S.CLICKS, S.CONVERSIONS, S.REVENUE FROM bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network_stats S ON S.user__id = '".$userId."' AND S.network__id = N.ID AND S.statDate = '".$today."' WHERE ISVALID = 'Y' ORDER BY N.MODEL, N.TITLE");
		while ( $row = LegacyAbstraction::getRow($res) )
			$arrNetworks[] = $row;
		LegacyAbstraction::free($res);

//*************************************************************************************************

		$arrNetsJoined = array();
		$res = LegacyAbstraction::executeQuery("SELECT N.MODEL, N.ID, N.TITLE, UAN.STATUS FROM bevomedia_aff_network N, bevomedia_user_aff_network UAN WHERE UAN.user__id = '".$userId."' AND UAN.STATUS = '".APP_STATUS_ACCEPTED."' AND UAN.network__id = N.ID AND N.ISVALID = 'Y' ORDER BY N.MODEL, N.TITLE");
		while ( $row = LegacyAbstraction::getRow($res) )
		{
			$row['ISUSER']	= false;

			$arrNetsJoined[] = $row;
		}
		LegacyAbstraction::free($res);

//*************************************************************************************************

		// Call template

//*************************************************************************************************
?>
	
	<script language="JavaScript" src="<?=SCRIPT_ROOT?>style/publisher-offers.js.php?sriptRoot=<?=SCRIPT_ROOT?>&langFolder=<?=$langFolder?>"></script>
<script language="javascript">
function check(){
var e = document.getElementById("nwAll");
e.checked = false;
var e = document.getElementById("nwMy");
e.checked = false;
}
function un_check(){
  for (var i = 0; i < document.frm.elements.length; i++) {
    var e = document.frm.elements[i];
    if ((e.id != 'nwAll' && e.id != 'ttAll'&&e.id != 'ttWeb'&&e.id != 'ttSrh'&&e.id != 'ttEml'&&e.id != 'ttInc') && (e.type == 'checkbox')) {
e.checked = false;
    }
  }
}
function un_check_my(){
  for (var i = 0; i < document.frm.elements.length; i++) {
    var e = document.frm.elements[i];
    if ((e.id != 'nwMy' && e.id != 'ttMy'&&e.id != 'ttWeb'&&e.id != 'ttSrh'&&e.id != 'ttEml'&&e.id != 'ttInc') && (e.type == 'checkbox')) {
e.checked = false;
    }
  }
}
function check1(){
var e = document.getElementById("ttAll");
e.checked = false;
}
function un_check1(){
  for (var i = 0; i < document.frm.elements.length; i++) {
    var e = document.frm.elements[i];
    if (e.id == 'ttWeb'||e.id == 'ttSrh' || e.id == 'ttEml' || e.id == 'ttInc') {
e.checked = false;
    }
  }
}
</script>
	
<?= @$info ?>

*/ ?>

<?php	//savelists
	//read db to find out if user already has list(s) or not
	//then read cookie to find out which one was the last one (if any)
	//set $OfferSaveList to list ID or "new" if no list exists yet. echo it for the js var currentSaveList.
	
	global $ovaultSavelist;
	$ovaultSavelist = array('cookie'=>'__bevoOLSL');	
	
	$sql = "CREATE TABLE IF NOT EXISTS bevomedia_user_offer_savelists(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user__id INT(10),
			created TIMESTAMP DEFAULT NOW(),
			updated TIMESTAMP DEFAULT NOW(),
			name VARCHAR(255),
			offers_array LONGTEXT
			) TYPE=MyISAM
	";
	
	function OvaultSaveListIni() {
		global $ovaultSavelist;
		
		$out = false;
		
		$sql = "SELECT 
				*
			FROM 
				bevomedia_user_offer_savelists
			WHERE 
				(bevomedia_user_offer_savelists.user__id = {$_SESSION['User']['ID']})
			ORDER BY
				id
			";
		$raw = mysql_query($sql);
		
		if(mysql_num_rows($raw) == 0) {
			$out = 'new';
			
		} else {
			$last = false;
			
			//check cook
			if(isset($_COOKIE[$ovaultSavelist['cookie']]) && is_numeric($_COOKIE[$ovaultSavelist['cookie']])) {
				$last = intval(trim($_COOKIE[$ovaultSavelist['cookie']]));
			}
			
			$ovaultSavelist['lists'] = array();
			while($obj = mysql_fetch_object($raw)) {
				
				if($last && $obj->id == $last)
					$out = $last;
				
				$ovaultSavelist['lists'][$obj->id] = $obj; //make the id the key
			}
			
			if(!empty($ovaultSavelist['lists']) && (!$last || !isset($ovaultSavelist['lists'][$last]))) {//if we have lists but no cookie exists, use the last updated one and setcookie
				$item = end($ovaultSavelist['lists']); //just use the last list in line
				$out = $item->id;
			}
			
			if($out)
				setcookie($ovaultSavelist['cookie'], $out, time()+60*60*24*30*12, '/'); //1y
		}
		
		$out = $out ? $out : 'new';
		
		return $out;
	}//OfferSaveListIni
	
	$ovaultSavelist['current'] = OvaultSaveListIni();
	
?>

<?php /* ##################################################### OUTPUT ############### */ ?>
<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Offers/BestPerformers.html">Best Performing Offers<span></span></a></li>
		<li><a class="active" href="/BevoMedia/Offers/Index.html">Search<span></span></a></li>
		<li><a href="/BevoMedia/Offers/MySavedLists.html">My Saved Lists<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false, false, false, 'ovault'); //disable toggle, custom css class
?>

<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault_Odial_include.view.php'; ?>

<div class="pagecontent" id="ovault">
	<?php include 'Applications/BevoMedia/Modules/Offers/Views/Ovault.Pagecontent.include.php'; ?>
</div><!--close pagecontent#ovault-->

<script type="text/javascript">
$(document).ready(function() {
	/*offer vault*/
	var	ajaxGet = 'AjaxGetContent.html',
		ajaxPut = 'AjaxPutContent.html',
		cache = [],
		
		cook_LastSearch = '__bevoOLSearch',
		cook_LastSaveList = '<?php echo $ovaultSavelist['cookie']; ?>',
		
		cookSearch = soap_cookRead(cook_LastSearch),
		
		//current
		currentOid, //the current offer id that is being fetched for orowbig
		currentSaveList = '<?php echo $ovaultSavelist['current']; ?>'; //if "new", no list exists yet and a new one will be created automatically when they save2list.
		
	cache.offerdetails = []; //index = the offer ID
	cache.searchresults = []; //index = the actual search string
	cache.current_searchstring = false; //the current search string, set after ajax success
	
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
	
	// close
	$('#ovault .j_close, #odial .j_close').click(function() {
		var target = $('#'+$(this).data('target'));
		
		target.fadeOut(300, function() {
			$('.hide', target).hide().removeClass('active');
		}).removeClass('active');
		
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
	if(cookSearch) {
		doSearch(cookSearch, true);
	}
	
	/*setInterval(function()	{
		if(window.location.hash != cache.current_searchstring) {
			//cache.searchresults[r.searchstring]
			alert('hash changed');
			cache.current_searchstring = window.location.hash;
		}
	}, 100);*/
	
	//orow expand/collapse
	$('#j_otablecont .orow').live('click', function() {
		var thisrow = $(this);
		currentOid = $(this).data('oid'); //the network ID to fetch
		
		if($(this).hasClass('expanded')) { //collapse
			thisrow.removeClass('expanded');
			$('#j_otablecont .orowbig.j_oid-'+currentOid).fadeOut(400, function() {
				$(this).remove();
			})
			
		} else { //expand
			
			//check if in cache already
			if(cache.offerdetails[currentOid]) {
				thisrow.addClass('expanded').after(cache.offerdetails[currentOid]);
				$('#j_otablecont .orowbig.j_oid-'+currentOid).slideDown(400);
			
			} else {
				$.ajax({
					type: 'GET',
					url: ajaxGet+'?get=orowbig&oid='+currentOid,
					success: function(r) {
						r = $.parseJSON(r);
						
						//$('#ovault').prepend(r);
						
						//alert(r.html);
						
						if(r.error) {
							ajaxMessage(r);
						} else {
							//add to cache
							cache.offerdetails[currentOid] = r.html;
							//add to dom
							thisrow.addClass('expanded').after(r.html);
							$('#j_otablecont .orowbig.j_oid-'+currentOid).fadeIn(400);
						}
					},
					error: function(r) {
						m = ['Something went wrong, please try again.']
						ajaxMessage(m);
					}
				});
			}//endif in cache
		}//end collapse/expand
	})//orow expand/collapse
	
	/*search dial*/
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
			ajaxMessage(error);
		
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
			
			doSearch(s);
			
		}//endif errors
		
		return false;
		
	})//#osearchform submit
	
	//paginate
	$('#opagi .numbers a.j_num:not(.active), #opagi .numbers a.j_prevnext').live('click', function() {
		s = cache.current_searchstring + '&newpage='+$(this).data('page'); //newpage overrides page
		
		//alert('paginate s: '+s);
		doSearch(s);
		
		return false;
	});
	
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

	/*save2list*/
	//save to list
	$('.orow a.ovault_add2list').live('click', function() {
		var oid = parseInt($(this).data('oid'));		
		doSave2List(currentSaveList, oid);				
	});
	
	//create new list
	$('#ovault_createnewlistform').live('submit',function() {
		var field = $('#ovault_newlistname');
		
		if(field.val() == field.prev().html() || field.val() == '')
			ajaxMessage('Please enter a name for your list!');
		else	doCreateNewList(field.val());
		
		return false;
	});
	
	
	/*
	
	FUNCTIONS
	
	*/
	
	/*ajaxMessage*/
	function ajaxMessage(m) {
		if(m.constructor.toString().indexOf("Array") == -1)
			alert(m);
		else {
			for(i=0; i<=m.length-1; i++)
				alert(m[i]);
		}
	}//ajaxMessage()
	
	/*doSave2List*/
	function doSave2List(list, oid) {
		$.ajax({
			type: 'GET',
			url: ajaxPut+'?put=save2list&list='+list+'&oid='+oid,
			success: function(r) {
				
				r = $.parseJSON(r);
				
				ajaxMessage(r);
			},
			error: function(r) {
				ajaxMessage('Could not save offer, please try again!');
			}
		});
	}//doSave2List()
	
	/*doCreateNewList*/
	function doCreateNewList(name) {
		$.ajax({
			type: 'GET',
			url: ajaxPut+'?put=createnewlist&newlistname='+name,
			success: function(r) {
				
				r = $.parseJSON(r);
				
				if(r.error)
					ajaxMessage(r.error);
				
				else {
					addSavelistDarkTableRow(r.listid, name); //add new list to table
					makeSavelistDefault(r.listid, name); //make this the default
					
					$('#ovault_createnewlistform.hide').fadeOut(200).removeClass('active');
					$('#ovault_newlistname').val('');
					
					ajaxMessage(r.message);
				}
			},
			error: function(r) {
				ajaxMessage('An error occured. Please try again!');
			}
		});
	}//doCreateNewList()
	
	/*doSearch*/ //updateDial bool set to true only if calling from cook or hash
	function doSearch(s, updateDial) {
		
		var target = $('#j_otablecont');
		
		if(cache.searchresults[s]) { //dupe code - same as below. maybe outsource to func later
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
				url: ajaxGet+'?'+s,
				success: function(r) {
					r = $.parseJSON(r);
					
					if(r.error) {
						ajaxMessage(r.error);
						
					} else {						
						cache.searchresults[r.searchstring] = r.resultarr;
						cache.current_searchstring = r.searchstring;
						window.location.hash = r.searchstring;
												
						//set cookie
						soap_cookCreate(cook_LastSearch,r.searchstring,365);
						
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
		}//endif cached or not
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
			
			tmp = unique(tmp); //filter out dupes
			
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
		
		currentSaveList = listid;
		soap_cookCreate(cook_LastSaveList,listid,365);
		
		var 	today = getToday(),
			nicename = name+' ('+today+')';
		
		$('#odial .save .selebtn').html(nicename+'<span class="down"></span>');
		$('#olay_savedlists .olaytopflag_big').html(nicename);
	
	}//makeSavelistDefault()
	
	/*addOfferTableRow*/ //adds 1 row. passed var must be an object from AjaxGetContent, usually r.resultarr[i]
	function addOfferTableRow(offer) {
		var out;
		out += '<tr class="orow j_old-'+offer['id']+'" data-oid="'+offer['id']+'" title="Click to expand or collapse this offer">';
		out += '<td class="border">&nbsp;</td>';
		
		//saved2list
		out += '<td class="td_saved2list" style="width:15px;">';
			out += offer['saved2list'] == 1 ? '<div class="icon icon_ovault_added2list" title="You have already saved this offer"></div>' : '&nbsp;';
		out += '</td>';
		
		//savelist
		out += '<td class="td_savelist" style="width:40px;">';
			out += '<a class="btn ovault_add2list" href="#" data-oid="'+offer['id']+'" title="Add this offer to the active list">Add</a>';
			out += '<a class="btn ovault_add2list_select" href="#" data-oid="'+offer['id']+'" title="Select a list to add this offer to...">Select</a></td>';
			
		//offername
		out += '<td class="td_offername" style="width:465px;"><p>'+offer['title']+'<span>Added '+offer['dateAdded']+'</span></p></td>';
			
		//payout
		out += '<td class="td_payout" style="width:54px;"><p>'+offer['payout']+'</p></td>';
			
		//type
		out += '<td class="td_type" style="width:41px;"><p>'+offer['type']+'</p></td>';
			
		//vertical
		out += '<td class="td_vertical" style="width:123px;"><p>'+offer['categoryTitle']+'</p></td>';
			
		//network
		out += '<td class="td_network" colspan="2" style="width:120px;"><p class="icon';
			out += offer['isNetworkMember'] == 1 ? ' icon_nwmember' : '';
		out += '">'+offer['networkName']+'</p></td>';
			
		out += '</tr>';
		
		return out;
	}//addOfferTableRow
	
	/*addSavelistDarkTableRow*/ //adds a newly created savelist to the .odarktable
	function addSavelistDarkTableRow(listid, name) {
		
		var 	parent = $('#ovault_olay_savelists tbody'),
			listnum = $('tr', parent).length,
			/*
			
			SOMEHOW this doesnt recognize js-added rows... and it omits the 1st html+= and messes everything up. Maybe count the rows on page load and keep in a var?
			
			*/
			thisnum = listnum+1,
			today = getToday(),
			html = '',
			before = '',
			after = '';
			
		html += '<tr class="j_list-'.listid;
		html += listnum % 2 ? '' : ' alt';
		html += ' hide">';
		
		html += '<td class="no">'+thisnum+'.</td><td class="name">'+name+'<span>Created: '+today+'</span></td>';
		html += '<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td><td class="view"><a class="btn icon_ovault_savelist_view" href="#">View</a></td><td class="download"><a class="btn icon_ovault_savelist_csv" href="#">CSV</a></td><td class="delete"><a class="btn icon_ovault_savelist_delete" href="#">Delete</a></td></tr>';
		
		//if this is their first list ever, we also have to build the table, and the parent changes
		if(currentSaveList == 'new') {
			parent = $('#olay_savedlists .olaycont');
			before = '<div class="olaybox nomarginbutt"><div class="olayboxtitle myofferlists"><a class="btn ovault_smallyell_deleteall" href="#">Delete All Lists</a></div><table cellspacing="0" cellpadding="0" id="ovault_olay_savelists" class="odarktable"><thead><tr><td class="no">&nbsp;</td><td class="name">Name</td><td class="use">Use</td><td class="view">View</td><td class="download">Download</td><td class="delete">Delete</td></tr></thead><tbody>';
			after = '</tbody></table></div><div class="clear"></div>';
		}
		
		$(before+html+after).appendTo(parent).fadeIn(500, function() {
			if(currentSaveList == 'new') { //also show the normal box heading
				$('#olay_savedlists .j_olay_savedlists_nolists').slideUp(200, function() {
					$('#olay_savedlists .j_olay_savedlists_havelists').slideDown(400).removeClass('hide');
				}).addClass('hide');				
			}
		}).removeClass('hide');
		
		
	}//addSavelistDarkTableRow()

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
	
	/*kitchen*
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}

	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}*/
	
	var unique = function(origArr) {  
		var newArr = [],  
		origLen = origArr.length,  
		found,  
		x, y;
	
		for ( x = 0; x < origLen; x++ ) {  
			found = undefined;  
			for ( y = 0; y < newArr.length; y++ ) {  
				if ( origArr[x] === newArr[y] ) {  
					found = true;  
					break;  
				}  
			}  
			if ( !found) newArr.push( origArr[x] );  
		}  
		return newArr;  
	}; 
});
</script>















<?php /* OLD INDEX



##############################################################
##############################################################
##############################################################
##############################################################
##############################################################


				<form method=get action="Search.html" name="frm" class="appform" id="offmain_searchform">
				<table class="btable wide2x3" cellspacing="0" cellpadding="5">
					<tr class="table_header">
						<td class="hhl"></td>
						<td colspan="2" style="text-align: center;">
						</td>
						<td class="hhr"></td>
					</tr>
                    <tr>
                    <td class="border">&nbsp;</td>
                    
                    <td colspan="2" width="222"><span><center><img src="<?=SCRIPT_ROOT?>img/pagedesc_bevovault.png" border=0 alt=""></center></span></td>
                    <td class="tail">&nbsp;</td>
                    </tr>
					<tr>
						<td class="border">&nbsp;</td>
						<td>Search: </td>
						<td>
							<input class="formtxt" type="text" size="30" name="title" class="effect">
						</td>
						<td class="tail">&nbsp;</td>
					</tr>
					<tr valign="top">
						<td class="border">&nbsp;</td>
						<td>Network: </td>
						<td>
							<input class="formcheck" type="checkbox" id="nwAll" name="network[]" value="" checked onclick="un_check()">All Networks
							<input class="formcheck" type="checkbox" id="nwMy" name="network[]" value="-1" onclick="un_check_my()">My Networks
<?
				$count = 0;
				$idArray = '';
				foreach ( $arrNetsJoined as $network )
				{
					if ( $network['STATUS'] != APP_STATUS_ACCEPTED || $network['MODEL'] != 'CPA' )
						continue;

					$idArray .= ', "'.$network['ID'].'"';
						echo '<br>';
?>
							<input type="checkbox" id="nw<?=$network['ID']?>" name="network[]" value="<?=$network['ID']?>" onclick="check()"><label style="display: inline-block" for="nw<?=$network['ID']?>"><?=$network['TITLE']?></label>
<?
				}
?>
							<script language="JavaScript">
							<!--
							var nwIds = new Array(<?=substr($idArray, 2)?>);
							//-->
							</script>
						</td>
						<td class="tail">&nbsp;</td>
					</tr>

					<tr valign="top">
						<td class="border">&nbsp;</td>
						<td colspan="2"  style="text-align: right; padding-top: 2px;">
							<input class="formsubmit off_search baseeffect search" type="submit" value="Search" />
							<?php /* this looks like it could confuse people
							<input type="reset" value="Default" class="baseeffect default" style="color: white"> * / ?>
						</td>
						<td class="tail">&nbsp;</td>
					</tr>

					<tr class="table_footer">
						<td class="hhl"></td>
						<td colspan="2">&nbsp;</td>
						<td class="hhr"></td>
					</tr>
				</table>
				</form>
				<table class="btable floatleft" cellspacing="0" cellpadding="5" border="0" style="float: left; width: 270px;">
					<tr class="table_header_small">
						<td class="hhls" style="border: none;"></td>
						<td style="border: none;" colspan="2">&nbsp;</td>
						<td class="hhrs" style="border: none;"></td>
					</tr>
			<?
				$count = 0;
				foreach ( $arrModels as $model )
				{
			?>
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="2"><span><?=$model?></span></td>
						<td class="tail">&nbsp;</td>
					</tr>
			<?
					foreach ( $arrNetsJoined as $network )
					{
						if ( $model != $network['MODEL'] )
							continue;
						$count++;
			?>
					<tr>
						<td class="border">&nbsp;</td>
						<td style="width: 140px;"><span><?=$network['TITLE']?></span></td>
						<td style="width:130px;">
                        <input class="statsBut formsubmit off_stats" type="submit" onclick="location.href='Stats.html?network=<?=$network['ID']?>'">
						<? if ( $network['MODEL'] == 'CPA' ) { ?>
							<input class="offersBut formsubmit off_offers" type="submit" onclick="location.href='Search.html?network[]=<?=$network['ID']?>'" />
						<? } elseif ( $network['ISUSER'] ) { ?>
							<input class="codesBut formsubmit off_codes" type="submit" value="" onclick="location.href='http://www.bevomedia.com/publisher-new-network-code.php?networkId=<?=$network['ID']?>'" />
						<? } else { ?>
							<input class="codesBut formsubmit off_codes" type="submit" value="" onclick="location.href='http://www.bevomedia.com/publisher-network-code.php?networkId=<?=$network['ID']?>'" />
						<? } ?>
						
                        </td>
                        
                        
                        
						<td class="tail">&nbsp;</td>
					</tr>
			<?
					}
				}
			?>
			
			<?php if($count == 0):?>
			
					<tr>
						<td class="border">&nbsp;</td>
						<td colspan="2">
							<center>
								<a class="tbtn" href="/BevoMedia/Publisher/Index.html">
    		                    	You do not currently have any networks installed. Please click here to install them.
								</a>
							</center>
                        </td>
                        
                        
                        
						<td class="tail">&nbsp;</td>
					</tr>
			<?php endif?>
					<tr class="table_footer">
						<td class="hhl"></td>
						<td colspan="2">&nbsp;</td>
						<td class="hhr"></td>
					</tr>
				</table> */ ?>