function makeSyncRequest(url, div) 
{
	url += '&SomethingUnique='+Math.random();
	document.body.style.cursor = "wait";
	
	if (window.XMLHttpRequest) 
	{              
		AJAX=new XMLHttpRequest();              
	} 
	else 
	{                                  
		AJAX=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	if (AJAX) 
	{
		AJAX.open("GET", url, false);                             
		AJAX.send(null);
		if(div == "")
		{
			document.body.style.cursor = "default";
			return AJAX.responseText;
		}
		else
		{
			document.getElementById(div).innerHTML = AJAX.responseText;
			document.body.style.cursor = "default";
		}
        

	} 
	else 
	{
		document.body.style.cursor = "default";
		return false;
	}
	
	document.body.style.cursor = "default";
}


function makeRequest(url, div) 
{
	if(url == ''){ return; }
	url += '&SomethingUnique='+Math.random();
	try
    {
        var httpRequest;
        document.body.style.cursor = "wait";
        
        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType("text/xml");
                // See note below about this line
            }
        } 
        else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                } 
                catch (e) {
                           try {
                                httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                               } 
                             catch (e) {}
                          }
                                       }

        if (!httpRequest) {
            alert("Giving up :( Cannot create an XMLHTTP instance");
            return false;
        }
        httpRequest.onreadystatechange = function() 
        {
            if (eval("typeof " + "alertContents" + " == 'function'")) 
            {
                alertContents(httpRequest, div); 
            }
        };
        httpRequest.open("GET", url, true);
        httpRequest.send("");
    }
    catch (err)
    {
    
    }

}

function alertContents(httpRequest, div) 
{
    try
    {
    	
        if (httpRequest.readyState == 4) {
        	//We can do the 404 and 500 because our framework has a handler for it
            if (httpRequest.status == 200 || httpRequest.status == 404 || httpRequest.status == 500) 
            {
            	
            	var responseText = httpRequest.responseText;
                if (httpRequest.responseText.indexOf("<hideDiv")>-1)
                {
                    var temp, str;
                    str = responseText;
                    temp = str.substr(str.indexOf("<hideDiv"), str.indexOf("</hideDiv>")-str.indexOf("<hideDiv"));

                    tempArr = temp.split("\"");
                    var divHide = tempArr[1];
                    divHide = divHide.replace(/^\s+|\s+$/g, '');
                    document.getElementById(divHide).style.display = 'none';
                    var temp = str.substr(0, str.indexOf("<hideDiv"));
                    temp+=str.substr(str.indexOf("</hideDiv>")+("</hideDiv>").length);
                    responseText = temp;
                } else
                if (httpRequest.responseText.indexOf("<jsFormValues>")>-1)
                {
                    var tag = "<jsFormValues>";
                    
                    var temp = responseText.substr(responseText.indexOf("<jsFormValues>")+tag.length, responseText.indexOf("</jsFormValues>")-responseText.indexOf("<jsFormValues>")-tag.length);
                    var vars = temp.split("|");
                    var form_name = vars[0];
                    for (var i=1; i<vars.length; i++)
                    {
                        var values = vars[i].split(":");
                        document.forms[form_name].elements[values[0]].value = values[1];
                    }
                    
                    responseText = httpRequest.responseText.substr(responseText.indexOf("</jsFormValues>")+("</jsFormValues>").length);
                } else
                if (httpRequest.responseText.indexOf("<hideShadowbox")>-1)
                {
                    //responseText = httpRequest.responseText.substr(responseText.indexOf("</hideShadowbox>")+("</hideShadowbox>").length);
                    Shadowbox.close();
                } else
                if (httpRequest.responseText.indexOf("<jsReloadPage")>-1)
                {
                    location.reload(true);
                } 
                
                if (httpRequest.responseText.indexOf("<jsCallFunction")>-1)
                {
                    var functionName = httpRequest.responseText.substr(httpRequest.responseText.indexOf("<jsCallFunction>")+("<jsCallFunction>").length);
                    functionName = functionName.substr(0, functionName.indexOf("</jsCallFunction>"));
                    //responseText = responseText.substr(responseText.indexOf("</jsCallFunction>")+("</jsCallFunction>").length);
                    
                    responseText = responseText.replace('<jsCallFunction>'+functionName+'</jsCallFunction>', '');
                    eval(functionName);   
                }
                
        
                if (httpRequest.responseText.indexOf("<script")>-1)
                {
                    var functionName = httpRequest.responseText.substr(httpRequest.responseText.indexOf("<script>")+("<script>").length);
                    functionName = functionName.substr(0, functionName.indexOf("</script>"));
                    
                    responseText = responseText.replace('<script>'+functionName+'</script>', '');
                    eval(functionName);   
                }

       
                document.getElementById(div).innerHTML = responseText;
                document.body.style.cursor = "default";
        	    
            } else {
                //alert("There was a problem with the request.");
            	
            }
        }
        
    
    }
    catch (err)
    {
    	
    
    }

}

