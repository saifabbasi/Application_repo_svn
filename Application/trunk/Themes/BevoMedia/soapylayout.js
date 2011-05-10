/* it's soapdesigned.com
*/
$(document).ready(function() {
	var	soap_TDcook = '__bevoTDR',
		soap_TDid = '#topdroptop',
		soap_TDopenClass = 'active',
		soap_TDbtn = '.topdroptoggle',
		soap_TDmainBtn = 'a.btn.topmenu_topdroptoggle',
		
		soap_PDcook = '__bevoPDESC',
		soap_PDid = '#pagedesc',
		soap_PDclosedClass = 'closed',
		soap_PDbtn = 'a.pagedesc_toggle',
		
		soap_topSubTimer,
		soap_topSubParentHover = false,
		soap_topSubHover = false;
		
	//toggle topdrop
	$(soap_TDbtn).click(function() {
		var status = soap_cookRead(soap_TDcook);
		if(status == '1') { //close
			$(soap_TDid).fadeOut(400, function() {
				$(soap_TDid).removeClass(soap_TDopenClass);
				$(soap_TDmainBtn).removeClass(soap_TDopenClass);
			});
			soap_cookCreate(soap_TDcook,'0',365);
		} else {//open
			$(soap_TDid).fadeIn(400).addClass(soap_TDopenClass);
			$(soap_TDmainBtn).addClass(soap_TDopenClass);
			soap_cookCreate(soap_TDcook,'1',365);
		}
		return false;
	});
	
	//toggle pagedesc
	$(soap_PDbtn).click(function() {
		var 	page = $(this).attr('href'),
			saved = soap_cookRead(soap_PDcook);			
		page = page.split('#');
		page = page[1];			
		if($(soap_PDid).hasClass(soap_PDclosedClass)) {
			//open
			$(soap_PDid).removeClass(soap_PDclosedClass);

			//news flash: IE has problems with this.
			setTimeout(function() {
				$(soap_PDid).children().css({'display':'block'});
			}, 200);
			
			var 	savedStr = saved.split('%7C'), //pipe
				savedNew;				
			for(i=0;i<=savedStr.length-1;i++) { //rebuild string and leave out the current page
				if((savedStr[i] != page) && (savedStr[i] != 'null') && (savedStr[i]) != 'undefined') {
					if((i > 0) && (savedStr.length > 1))
						savedNew += '|';
					savedNew += savedStr[i];
				}
			}
			soap_cookCreate(soap_PDcook,savedNew,365);
		} else {
			//close
			$(soap_PDid).addClass(soap_PDclosedClass);
			saved = saved+'|'+page;
			soap_cookCreate(soap_PDcook,saved,365);
		}
		return false;		
	});	
	
	//toggle topsubmenu
	$('ul.topmenusub').live('mouseover mouseout', function(eh) {
		if(eh.type == 'mouseover') {
			clearTimeout(soap_topSubTimer);
			soap_topSubHover = true;
		} else {
			soap_topSubHover = false;
			setTimeout(function() {
				soap_topSubClose();
			}, 300);
		}
	});
	
	$('a.topmenu_hassub').live('mouseover mouseout', function(e) {
		target = $(this).attr('href');		
		if(e.type == 'mouseover') { 
			clearTimeout(soap_topSubTimer);
			soap_topSubParentHover = true;			
			if($('ul.topmenusub').hasClass('active'))
				$('ul.topmenusub.active').slideUp(100).removeClass('active');			
			$(target).slideDown(100).addClass('active');
		} else {
			soap_topSubParentHover = false;
			setTimeout(function(){soap_topSubClose();},300);
		}
	}).click(function(){return false;});

	function soap_topSubClose() {
		if(!soap_topSubParentHover && !soap_topSubHover) {
			soap_topSubTimer = setTimeout(function() {
				if(!soap_topSubParentHover && !soap_topSubHover)
					$('ul.topmenusub.active').slideUp(100).removeClass('active');
			},2000);
		}
	}
});

/*kitchen*/
function soap_cookCreate(name,value,days) {
	if(days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function soap_cookRead(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while(c.charAt(0)==' ') c = c.substring(1,c.length);
		if(c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

/*truncate*/
function soap_truncTxt(str,len,ext) {
	len = len ? len : 20;
	ext = ext == undefined ? '...' : ext;
	if(str && str != '') {
		var out = str.substring(0,len);
		if(len < str.length)
			out += ext;
		return out;
	}
}//soap_truncTxt

/*ArrayUnique*/ //filters out dupes in an array
var ArrayUnique = function(origArr) {  
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

