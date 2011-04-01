function $$(name)
{
    return document.getElementById(name);
}

function ShowDashboard()
{
	ShowDiv('DashboardDiv');
}

function SubmitSearchForm(RedirectURL, SearchTermDiv)
{
	window.location = RedirectURL+'/'+$$(SearchTermDiv).value;
}

function HideDiv(Div) {
	$$(Div).style.display = 'none';
}

function ShowDiv(Div) {
	$$(Div).style.display = 'inline';
	
}

function ShowHideDiv(Div){
	if($$(Div).style.display == 'none')
	{
		ShowDiv(Div);
		
	}else
	{
		HideDiv(Div);
		
	}
	
}

function getRandomNum(lbound, ubound) 
{
    return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
}
function getRandomChar(number, lower, upper, other, extra) 
{
    var numberChars = "0123456789";
    var lowerChars = "abcdefghijklmnopqrstuvwxyz";
    var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var otherChars = "`~!@#$%^&*()-_=+[{]}\\|;:'\",<.>/? ";
    var charSet = extra;
    if (number == true)
    charSet += numberChars;
    if (lower == true)
    charSet += lowerChars;
    if (upper == true)
    charSet += upperChars;
    if (other == true)
    charSet += otherChars;
    return charSet.charAt(getRandomNum(0, charSet.length));
}
function getPassword(length, extraChars, firstNumber, firstLower, firstUpper, firstOther, latterNumber, latterLower, latterUpper, latterOther) 
{
    var rc = "";
    if (length > 0)
    rc = rc + getRandomChar(firstNumber, firstLower, firstUpper, firstOther, extraChars);
    for (var idx = 1; idx < length; ++idx) {
    rc = rc + getRandomChar(latterNumber, latterLower, latterUpper, latterOther, extraChars);
    }
    return rc;
}

function getPageSizeWithScroll()
{
	if (window.innerHeight && window.scrollMaxY) {// Firefox
		yWithScroll = window.innerHeight + window.scrollMaxY;
		xWithScroll = window.innerWidth + window.scrollMaxX;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		yWithScroll = document.body.scrollHeight;
		xWithScroll = document.body.scrollWidth;
	} else { // works in Explorer 6 Strict, Mozilla (not FF) and Safari
		yWithScroll = document.body.offsetHeight;
		xWithScroll = document.body.offsetWidth;
  	}
	arrayPageSizeWithScroll = new Array(xWithScroll,yWithScroll);
	//alert( 'The height is ' + yWithScroll + ' and the width is ' + xWithScroll );
	return arrayPageSizeWithScroll;
}
