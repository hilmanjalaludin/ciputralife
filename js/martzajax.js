var xmlHttp
var divProgressBar

function hiddenAction(v_file, v_url_params) { 
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null) {
		alert ("Browser does not support HTTP Request")
	 	return
	}
	
	var url		= v_file+'?'+v_url_params
	
	url=url+"&sid="+Math.random()
	//alert(url)
	xmlHttp.onreadystatechange=stateChanged
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)	
}
	
function stateChanged() { 
	xmlHttp.readyState==4;
}
	
function GetXmlHttpObject() {
	var xmlHttp=null;
	try {
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e) {
		//Internet Explorer
	 	try {
	 		xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
	 	}
	 	catch (e) {
	 		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	 	}
	}
	return xmlHttp;
}