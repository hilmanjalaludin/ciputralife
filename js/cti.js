function callDial(phone_no) { 	
  var dialedNumber = phone_no;
  var now = new Date();
  
  // Can also be used as a standalone function
  //dateFormat(now, "dddd, mmmm dS, yyyy, h:MM:ss TT");
  // Saturday, June 9th, 2007, 5:46:21 PM
  
  // You can use one of several named masks
  //now.format("isoDateTime");


  form_action.document.getElementById('act_date_time').value  = dateFormat(now, "yyyy-mm-dd hh:MM:ss");
  form_action.document.getElementById('act_dialed_no').value  = dialedNumber;
  form_action.document.getElementById('act_phone_type').value = form_action.document.getElementById('prime_phone').options[form_action.document.getElementById('prime_phone').selectedIndex].text;
  //form_action.document.getElementById('prime_phone').text;
  custid = form_action.document.getElementById('act_customer_id').value;

  if(dialedNumber && dialedNumber != "0" && dialedNumber!="93261908" && dialedNumber!="02193261908"){
    alert('Dialings '+phone_no+'...');
	cd();
  } else {
    alert('Please select phone number.');
    return false;
  }
  //form_action.document.getElementById('save_action').disabled=false;
  //parent.frames[1].ctiapplet.callDial('',phone_no,'');
  //document.ctiapplet.callDialCustomer('', phone_no, '', custid);
}
function setPhoneNo(listbox) {
  var phoneNum = listbox.options[listbox.selectedIndex].value;
}





var mins
var secs;
//var rpic = window.form_action.document.getElementById('last_status').value;

function cd() {
 	mins = 1 * m("00"); // change minutes here
 	secs = 0 + s(":01"); // change seconds here (always add an additional second to your total)
 	redo();
}

function m(obj) {
 	for(var i = 0; i < obj.length; i++) {
  		if(obj.substring(i, i + 1) == ":")
  		break;
 	}
 	return(obj.substring(0, i));
}

function s(obj) {
 	for(var i = 0; i < obj.length; i++) {
  		if(obj.substring(i, i + 1) == ":")
  		break;
 	}
 	return(obj.substring(i + 1, obj.length));
}

function dis(mins,secs) {
 	var disp;
 	if(mins <= 9) {
  		disp = " 0";
 	} else {
  		disp = " ";
 	}
 	disp += mins + ":";
 	if(secs <= 9) {
  		disp += "0" + secs;
 	} else {
  		disp += secs;
 	}
 	return(disp);
}

function redo() {
 	secs--;
 	if(secs == -1) {
  		secs = 5;
  		mins--;
 	}
 	document.cd.disp.value = dis(mins,secs); // setup additional displays here.
 	if((mins == 0) && (secs == 0)) {
		window.form_action.document.getElementById('wilayah_pod').disabled=false;
		if($("#wilayah_pod").val()!=""){
			onWilayah();
		}
		//form_action.document.getElementById('tsa_action_pod').disabled=false;
		
 	} else {
 		cd = setTimeout("redo()",100);
 	}
}

function onWilayah(){
		var wilayah = $("#wilayah_pod").val();
		var send_fax = $("#send_fax").val();
		
		if(wilayah!=""){
			window.form_action.document.getElementById('close_action').disabled=true;
			window.form_action.document.getElementById('save_action').disabled=false;
			window.form_action.document.getElementById('request-application').disabled=false;
			window.form_action.document.getElementById('request-addon').disabled=false;
			document.getElementById('link-reff').disabled=false;
			
			if (wilayah=="0")
			{
				//alert(window.form_action.document.getElementById('last_status').value);
				window.form_action.document.getElementById('tsa_action_pod').disabled=false;
			}else if (wilayah=="1")
			{
				if(window.form_action.document.getElementById('last_status').value!='RPIC' && send_fax!=1){
					window.form_action.document.getElementById('tsa_action_pod').disabled=true;
                                        $("#tsa_action_pod").attr('checked', false);	
				}else{
					//alert(window.form_action.document.getElementById('last_status').value);
					window.form_action.document.getElementById('tsa_action_pod').disabled=false;
					$("#tsa_action_pod").attr('checked', false);
				}
			}
		}else{
			window.form_action.document.getElementById('close_action').disabled=false;
			window.form_action.document.getElementById('save_action').disabled=true;
			window.form_action.document.getElementById('request-application').disabled=true;
			window.form_action.document.getElementById('request-addon').disabled=true;
			document.getElementById('link-reff').disabled=true;
			
			//alert(window.form_action.document.getElementById('last_status').value);
			window.form_action.document.getElementById('tsa_action_pod').disabled=true;
			$("#tsa_action_pod").attr('checked', false);
			
		}
}

function init() {
  cd();
}
//window.onload = init;
//cd()
