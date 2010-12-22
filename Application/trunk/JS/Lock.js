function LockRecord(Application, Module, ID) { 
  dojo.xhrGet( { 
    // The following URL must match that used to test the server.
    url: BaseURL+Application+"-"+Module+"/Lock-"+Module+".html/?"+Module+"ID="+ID+"&ajax=true", 
    handleAs: "text",

    timeout: 5000, // Time in milliseconds

    load: function(response, ioArgs) {  
      dojo.byId("LockRecordDiv").innerHTML = response;  
      return response; 
    },

  	error: function(response, ioArgs) { 
    	//console.error("HTTP status code: ", ioArgs.xhr.status); 
        return response; 
    }	

  });
}

function LockRecordService(Application, Module, ID)
{
	TimerArray[TimerArray.length] = setInterval("LockRecord('"+Application+"', '"+Module+"', '"+ID+"')", 10000);//10 Seconds 
	//LockRecord(Application, Module, ID);//Get the first one in there
}
