var newWindow;
var newWindow2;
var newWindow3;
var newWindow4;
var newWindow5;
var newWindowp;
var newWindowx;

function winopen(winmenu,winnama,panjang,lebar){

	if (!newWindow || newWindow.closed) {

		if (document.all)
			var xMax = screen.width, yMax = screen.height;
		else
			if (document.layers)
				var xMax = window.outerWidth, yMax = window.outerHeight;
			else
				var xMax = 640, yMax=480;

		var xOffset = (xMax - panjang)/2, yOffset = (yMax - lebar)/2;

		newWindow = window.open(winmenu,winnama,'scrollbars=yes,width='+panjang+',height='+lebar+',screenX='+xOffset+',screenY='+yOffset+',top='+yOffset+',left='+xOffset+',titlebar=0');

	} else if (newWindow.focus) {
		newWindow.focus( );
    }
    
}

function winopen2(winmenu,winnama){

	if (!newWindow2 || newWindow2.closed) {

	    newWindow2 = window.open(winmenu,winnama,'titlebar=0');
	
	} else if (newWindow2.focus) {
		newWindow2.focus( );
    }
    
}

function winopen3(winmenu,winnama,panjang,lebar){

	if (!newWindow3 || newWindow3.closed) {
	    if (document.all)
		    var xMax = screen.width, yMax = screen.height;
	    else
		    if (document.layers)
			    var xMax = window.outerWidth, yMax = window.outerHeight;
	        else
		        var xMax = 1000, yMax=480;

	    var xOffset = (xMax - panjang)/2, yOffset = (yMax - lebar)/2;

		newWindow3 = window.open(winmenu,winnama,'scrollbars=yes,width='+panjang+',height='+lebar+',screenX='+xOffset+',screenY='+yOffset+',top='+yOffset+',left='+xOffset+',titlebar=0');

	} else if (newWindow3.focus) {
		newWindow3.focus( );
    } 
}


function winopen4(winmenu,winnama,panjang,lebar){

showModalDialog(winmenu, winnama, 'width='+panjang+',height='+lebar+',rezisable=1,scrollbars=1')

}


function winopen5(winmenu,winnama,panjang,lebar){

	if (!newWindow5 || newWindow5.closed) {
	    if (document.all)
		    var xMax = screen.width, yMax = screen.height;
	    else
		    if (document.layers)
			    var xMax = window.outerWidth, yMax = window.outerHeight;
	        else
		        var xMax = 1000, yMax=480;

	    var xOffset = (xMax - panjang)/2, yOffset = (yMax - lebar)/2;

		newWindow5 = window.open(winmenu,winnama,'scrollbars=yes,width='+panjang+',height='+lebar+',screenX='+xOffset+',screenY='+yOffset+',top='+yOffset+',left='+xOffset+',titlebar=0');

	} else if (newWindow5.focus) {
		newWindow5.focus( );
    } 
}



function winopenx(winmenu,winnama,panjang,lebar){

	if (!newWindowx || newWindowx.closed) {

		if (document.all)
			var xMax = screen.width, yMax = screen.height;
		else
			if (document.layers)
				var xMax = window.outerWidth, yMax = window.outerHeight;
			else
				var xMax = 640, yMax=480;

		var xOffset = (xMax - panjang)/2, yOffset = (yMax - lebar)/2;

		newWindowx = window.open(winmenu,winnama,'scrollbars=yes,width='+panjang+',height='+(yMax-60)+',screenX='+xOffset+',screenY='+yOffset+',top=20,left=0,titlebar=0');

	} else if (newWindowx.focus) {
		newWindowx.focus( );
  }
    
}

function printPreview(winmenu,winnama,panjang,lebar){

	if (!newWindowp || newWindowp.closed) {
		if (document.all)
			var xMax = screen.width, yMax = screen.height;
	    else
		    if (document.layers)
			    var xMax = window.outerWidth, yMax = window.outerHeight;
	        else
		        var xMax = 640, yMax=480;

	    var xOffset = (xMax - panjang)/2, yOffset = (yMax - lebar)/2;

		newWindowp = window.open(winmenu,winnama,'scrollbars=yes,width='+panjang+',height='+lebar+',screenX='+xOffset+',screenY='+yOffset+',top='+yOffset+',left='+xOffset+',titlebar=0');

    	} else if (newWindowp.focus) {
		newWindowp.focus( );
    } 
}


function printPage()
{
    document.getElementById('print').style.visibility = 'hidden';
    document.getElementById('close').style.visibility = 'hidden';
    //document.getElementById('update').style.visibility = 'hidden';
    // Do print the page
    if (typeof(window.print) != 'undefined') {
        window.print();
    }
    document.getElementById('print').style.visibility = '';
    document.getElementById('close').style.visibility = '';
    //document.getElementById('update').style.visibility = '';
	
  customer_id     = form_action.document.getElementById('act_customer_id').value;
	
hiddenAction('update_status_pod.php','customer_id='+customer_id+'&print_status=1');
	
	
}

function get_cookie(cookie_name)
{
  var results = parent.parent.document.cookie.match ('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');

  if (results)
    return (unescape(results[2]));
  else
    return null;
}

function rusurelogout(scr_session) 
{
  question = confirm("Logout from this session?");
  
  if (question !="0")
    parent.location = "sys_logout.php";
}


function getObject(obj) {
  var theObj;
  if(document.all) {
    if(typeof obj=="string") {
      return document.all(obj);
    } else {
      return obj.style;
    }
  }
  if(document.getElementById) {
    if(typeof obj=="string") {
      return document.getElementById(obj);
    } else {
      return obj.style;
    }
  }
  return null;
}
//Hitung Sisa karakter di Memo
function Contar(entrada,salida,texto,caracteres) {
  var entradaObj=getObject(entrada);
  var salidaObj=getObject(salida);
  var longitud=caracteres - entradaObj.value.length;
  if(longitud <= 0) {
    longitud=0;
    texto='<span class="disable"> '+texto+' </span>';
    entradaObj.value=entradaObj.value.substr(0,caracteres);
  }
  salidaObj.innerHTML = texto.replace("{CHAR}",longitud);
}

function radioPicked(radioName) {
  
  var radioSet = "";

  for (var k=0; k<document.forms.length; k++) {
    if (!radioSet) {
      radioSet = document.forms [k][radioName];
    }
  }

  if (!radioSet) return false;
  for (k=0; k<radioSet.length; k++) {
    if (radioSet[k].checked) {
      radioValue=radioSet[k].value;
    }
  }
  return radioValue;
}

function actSaveActivity() {
  
  customer_id     = form_action.document.getElementById('act_customer_id').value;
  campaign_id     = form_action.document.getElementById('act_campaign_id').value;
  dialed_date     = form_action.document.getElementById('act_date_time').value;
  dialed_no       = form_action.document.getElementById('act_dialed_no').value;
  place           = form_action.document.getElementById('act_phone_type').value;
  last_status     = form_action.document.getElementById('last_status').value;
  disagree        = form_action.document.getElementById('disagree').value;
  call_later1     = form_action.document.getElementById('call_later1').value;
  call_later2     = form_action.document.getElementById('call_later2').value;
  wilayah		  = form_action.document.getElementById('wilayah_pod').value;
 // hid_wilayah		  = form_action.document.getElementById('hid_wilayah').value;
	
  response_status = radioPicked('tsa_action');
  
  try_again_on = '';
  
  switch(response_status) {
    case "NBPU":
       try_again_on = form_action.document.getElementById('try_again_on_nbpu').value;
       break;
    case "BUSY":
       try_again_on = form_action.document.getElementById('try_again_on_busy').value;
       break;
    case "NOAV":
       try_again_on = form_action.document.getElementById('try_again_on_noav').value;
       break;
    case "STHN":
       try_again_on = form_action.document.getElementById('try_again_on_sthn').value;
       break;
  }
  
  if(response_status=="RQAP") {
    product_id = form_action.document.getElementById('tsa_product').value;
  } else {
    product_id = "";
  }
  
  if(response_status == "ADD"){
	 	product_id = "addon2";
	}

  memo            = form_action.document.getElementById('tsa_notes').value;
  //alert(wilayah+"BLA");
  hiddenAction('act_save_activity.php','customer_id='+customer_id+'&campaign_id='+campaign_id+'&product_id='+product_id+'&dialed_date='+dialed_date+'&dialed_no='+dialed_no+'&place='+place+'&action=2&call_status=1&response_status='+response_status+'&try_again_on='+try_again_on+'&memo='+memo+'&disagree='+disagree+'&call_later1='+call_later1+'&call_later2='+call_later2+'&last_status='+last_status+'&wilayah='+wilayah);
  //if(response_status=="RQAP") {
    //parent.location.reload();
	$('#main_content').load('cust_detail.php?customer_id='+customer_id);
  //}
}

function actSaveConfig() {
  application_theme     = form_action.document.getElementById('application_theme').value;
  hiddenAction('act_save_config.php','application_theme='+application_theme);
}

function actSaveMessage() {
  to            = form_action.document.getElementById('to').value;
  msg_body      = form_action.document.getElementById('msg_body').value;
  
  hiddenAction('save_message.php','to='+to+'&msg_body='+msg_body);
  alert('Pesan di kirim');
  //alert('save_message.php?to='+to+'&msg_body='+msg_body);
  $('#main_content').load('mgt_message.php');
}
function actSaveRef() {
  refid            	= frmact.document.getElementById('refid').value;
  txtname      		= frmact.document.getElementById('txtname').value;
  txthp     		= frmact.document.getElementById('txthp').value;
  txthome     		= frmact.document.getElementById('txthome').value;
  txtfax     		= frmact.document.getElementById('txtfax').value;
  txtaddr     		= frmact.document.getElementById('txtaddr').value;
  customer_id     	= frmact.document.getElementById('act_customer_id').value;
  campaign_id     	= frmact.document.getElementById('act_campaign_id').value;
  recsource     	= frmact.document.getElementById('recsource').value;
  ref_from     		= frmact.document.getElementById('ref_from').value;
  
  if(txtname==''){
	  alert('Nama Harus Di Isi...!!!');
	  return false;
	  }else if(txthp==''){
		    alert('No HP Harus Di Isi...!!!');
	  		return false;
		  }
  
  hiddenAction('save_referral.php','refid='+refid+'&txtname='+txtname+'&txthp='+txthp+'&txthome='+txthome+'&txtfax='+txtfax+'&txtaddr='+txtaddr+'&campaign_id='+campaign_id+'&campaign_id='+campaign_id+'&recsource='+recsource+'&ref_from='+ref_from);
  alert('data tersimpan');
  //$('#main_content').load('cust_detail.php?customer_id='+customer_id);
}

function actSavePulsa() {
  amount   = form_action.document.getElementById('amount').value;
  dest     = form_action.document.getElementById('dest').value;
  orig     = form_action.document.getElementById('orig').value;
  orig_aoc = form_action.document.getElementById('orig_aoc').value;
  
  /*
  hiddenAction('mgt_dist_pulsa_save.php','amount='+amount+'&dest='+dest+'&orig='+orig+'&orig_aoc='+orig_aoc);
  alert('Pulsa di tambahkan sebesar Rp.'+amount+orig_aoc);
  $('#main_content').load('mgt_dist_pulsa.php');
  */
  
  if (dest == ''){
  		alert('Tujuan belum di isi...!'); 
		return false;
  }
	else if (amount == ''){
			alert('Jumlah Pulsa belum di isi...!'); return false;
	}
  else {		
  hiddenAction('mgt_dist_pulsa_save.php','amount='+amount+'&dest='+dest+'&orig='+orig+'&orig_aoc='+orig_aoc+'&orig='+orig);
  //alert('mgt_dist_pulsa_save.php amount='+amount+'&dest='+dest+'&orig='+orig+'&orig_aoc='+orig_aoc+'&orig='+orig);
  alert('Pulsa di tambahkan sebesar Rp.'+amount+'from '+orig_aoc+ 'to '+dest);
  $('#main_content').load('mgt_dist_pulsa.php');
  //alert (amount+" "+dest+" "+orig+" "+orig_aoc);
  }
 
}

function actSaveDistData() {
  amount            = form_action.document.getElementById('amount').value;
  dest      		= form_action.document.getElementById('dest').value;
  orig     			= form_action.document.getElementById('orig').value;
  anak     			= form_action.document.getElementById('anak').value;
  list_anak     	= form_action.document.getElementById('list_anak').value;
  
  hiddenAction('mgt_dist_data_save.php','amount='+amount+'&dest='+dest+'&orig='+orig+'&anak='+anak+'&list_anak='+list_anak);
  //alert(amount+dest+orig+anak+list_anak);
  $('#main_content').load('mgt_dist_data.php');
}

function updateStatusFax(customer_id) {
  
  hiddenAction('update_status_fax.php','customer_id='+customer_id);
   //alert(customer_id);
   alert('fax sedang di kirim...!!! \nklik OK dan tunggu sampai Loading selesai!');
   $('#main_content').load('cust_app_request_list_nav.php');
}


function saveApp(){
	 	customer_id     = form_action.document.getElementById('act_customer_id').value;
  		campaign_id     = form_action.document.getElementById('act_campaign_id').value;
  		product_id 		= form_action.document.getElementById('resend_product_id').value;
  hiddenAction('act_save_app.php','customer_id='+customer_id+'&campaign_id='+campaign_id+'&product_id='+product_id+'&action=resend');
  alert('App Di kirim ke SPV.......');
  //alert(customer_id+' '+campaign_id+' '+product_id)
  $('#main_content').load('cust_detail.php?customer_id='+customer_id);
	}

function sendAppPerPage(){
	 	customer_id     = form_action.document.getElementById('act_customer_id').value;
  		campaign_id   	= form_action.document.getElementById('act_campaign_id').value;
  		product_id 		= form_action.document.getElementById('resend_product_id').value;
		send_fax_page 	= form_action.document.getElementById('send_fax_page').value;
		total_fax_page 	= form_action.document.getElementById('total_fax_page').value;
		
		var send=send_fax_page*1;
		
  hiddenAction('act_save_app.php','customer_id='+customer_id+'&campaign_id='+campaign_id+'&product_id='+product_id+'&action=resendPerPage&pagesent='+send_fax_page);
  	alert('App Di kirim ke SPV.......');
 	alert("Kirim halaman ke "+(send+1)+" sampai halaman ke "+total_fax_page);
	 $('#main_content').load('cust_detail.php?customer_id='+customer_id);
	}


function searchCustomer () {
  search_field  = $('#search_field').val();
  keyword       = $('#search_keyword').val();
  data_status   = $('#search_data_status').val()
  agent_id      = $('#search_agent_id').val();
  campaign_id   = $('#search_campaign_id').val();
  disagree	    = $('#search_disagree').val();
  date_start    = $('#datepicker1').val();
  date_end      = $('#datepicker2').val();
  
  $('#main_content').load('cust_list_nav.php?search_field='+search_field+'&keyword='+keyword+'&data_status='+data_status+'&agent_id='+agent_id+'&campaign_id='+campaign_id+'&date_start='+date_start+'&date_end='+date_end+'&disagree='+disagree);
  
}

function searchCustomerDup() {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  agent_id      = frmSearch.document.getElementById('search_agent_id').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  
  $('#main_content').load('cust_list_pod_dup_nav.php?search_field='+search_field+'&keyword='+keyword+'&agent_id='+agent_id+'&date_start='+date_start+'&date_end='+date_end);
/* 
  
  $('#main_content').load('cust_list_pod_dup_nav.php?search_field='+search_field+'&keyword='+keyword);
  //alert(date_start+' '+date_end);
  
 */  //alert('cust_list.php?search_field='+search_field+'&keyword='+keyword+'&data_status='+data_status+'&agent_id='+agent_id+'&campaign_id='+campaign_id+'&date_start='+date_start+'&date_end='+date_end);
}



function searchFaxResend(){
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  campaign_id   = frmSearch.document.getElementById('search_campaign_id').value;
  status_apps   = frmSearch.document.getElementById('search_status_apps').value;
  
  
  $('#main_content').load('resend_fax_list_nav.php?search_field='+search_field+'&keyword='+keyword+'&campaign_id='+campaign_id+'&status_apps='+status_apps);
  //alert(search_field+' '+keyword+''+status_apps);
}

function searchFaxResendAll(){
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  campaign_id   = frmSearch.document.getElementById('search_campaign_id').value;
  status_apps   = frmSearch.document.getElementById('search_status_apps').value;
  
  
  $('#main_content').load('resend_fax_list_all_nav.php?search_field='+search_field+'&keyword='+keyword+'&campaign_id='+campaign_id+'&status_apps='+status_apps);
  //alert(search_field+' '+keyword+''+status_apps);
}

function searchApp () {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  product_id	= frmSearch.document.getElementById('search_product_id').value;
  campaign_id   = frmSearch.document.getElementById('search_campaign_id').value;
  status_apps   = frmSearch.document.getElementById('search_status_apps').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  
  $('#main_content').load('cust_app_request_list_nav.php?search_field='+search_field+'&keyword='+keyword+'&product_id='+product_id+'&campaign_id='+campaign_id+'&date_start='+date_start+'&date_end='+date_end+'&status_apps='+status_apps);
}

function searchPremier () {
  download	    = frmSearch.document.getElementById('download').value;  
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  
  $('#main_content').load('report_print_premier_request_list_nav.php?download='+download+'&date_start='+date_start+'&date_end='+date_end);
}

function searchAppPrintAddon () {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  product_id	= frmSearch.document.getElementById('search_product_id').value;
  campaign_id   = frmSearch.document.getElementById('search_campaign_id').value;
  status_apps   = frmSearch.document.getElementById('search_status_apps').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  search_date      = frmSearch.document.getElementById('search_date').value;

  $('#main_content').load('cust_print_app_request_list_nav_addon.php?search_field='+search_field+'&keyword='+keyword+'&product_id='+product_id+'&campaign_id='+campaign_id+'&date_start='+date_start+'&date_end='+date_end+'&status_apps='+status_apps+'&search_date='+search_date);
}
function searchCustomerPOD(){
		search_field    = frmSearch.document.getElementById('search_field').value;
		date_start      = frmSearch.document.getElementById('datepicker1').value;
		date_end        = frmSearch.document.getElementById('datepicker2').value;
		$('#main_content').load('cust_list_nav_2.php?search_field='+search_field+'&date_start='+date_start+'&date_end='+date_end);
		}
function searchCustomerPOD_adv(){
		search_field    = frmSearch.document.getElementById('search_field').value;
		date_start      = frmSearch.document.getElementById('datepicker1').value;
		date_end        = frmSearch.document.getElementById('datepicker2').value;
		$('#main_content').load('download_POD_adv.php?search_field='+search_field+'&date_start='+date_start+'&date_end='+date_end);
		}
function searchCustomerPODjak(){
                search_field    = frmSearch.document.getElementById('search_field').value;
			    search_field_wilayah    = frmSearch.document.getElementById('search_field_wilayah').value;
                date_start      = frmSearch.document.getElementById('datepicker1').value;
                date_end        = frmSearch.document.getElementById('datepicker2').value;
                $('#main_content').load('download_POD_jak.php?search_field='+search_field+'&search_field_wilayah='+search_field_wilayah+'&date_start='+date_start+'&date_end='+date_end);
				//alert('download_POD_jak.php?search_field='+search_field+'&search_field_wilayah='+search_field_wilayah+'&date_start='+date_start+'&date_end='+date_end);
                }
function searchAppPrint () {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  product_id	= frmSearch.document.getElementById('search_product_id').value;
  campaign_id   = frmSearch.document.getElementById('search_campaign_id').value;
  status_apps   = frmSearch.document.getElementById('search_status_apps').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  
  $('#main_content').load('cust_print_app_request_list_nav.php?search_field='+search_field+'&keyword='+keyword+'&product_id='+product_id+'&campaign_id='+campaign_id+'&date_start='+date_start+'&date_end='+date_end+'&status_apps='+status_apps);
}

function searchAppSpv () {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  campaign_id   = frmSearch.document.getElementById('search_campaign_id').value;
  product_id	= frmSearch.document.getElementById('search_product_id').value;
  status_apps   = frmSearch.document.getElementById('search_status_apps').value;
  agent		    = frmSearch.document.getElementById('search_agent_id').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  date_start3    = frmSearch.document.getElementById('datepicker3').value;
  date_end4      = frmSearch.document.getElementById('datepicker4').value;

  $('#main_content').load('cust_app_request_list_nav_spv.php?search_field='+search_field+'&keyword='+keyword+'&campaign_id='+campaign_id+'&date_start='+date_start+'&date_end='+date_end+'&status_apps='+status_apps+'&agent='+agent+'&product_id='+product_id+'&date_start3='+date_start3+'&date_end4='+date_end4);
}


function searchPickup () {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  campaign_id   = frmSearch.document.getElementById('search_campaign_id').value;
  print_status  = frmSearch.document.getElementById('print_status').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  
  $('#main_content').load('cust_request_pickup_list_nav.php?search_field='+search_field+'&keyword='+keyword+'&campaign_id='+campaign_id+'&print_status='+print_status+'&date_start='+date_start+'&date_end='+date_end);
}

function searchPickupSpv () {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  campaign_id   = frmSearch.document.getElementById('search_campaign_id').value;
  status_apps   = frmSearch.document.getElementById('search_status_apps').value;
  agent		    = frmSearch.document.getElementById('search_agent_id').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  date_start3    = frmSearch.document.getElementById('datepicker3').value;
  date_end4      = frmSearch.document.getElementById('datepicker4').value;
  
  $('#main_content').load('cust_request_pickup_list_nav_spv.php?search_field='+search_field+'&keyword='+keyword+'&campaign_id='+campaign_id+'&date_start='+date_start+'&date_end='+date_end+'&status_apps='+status_apps+'&agent='+agent+'&date_start3='+date_start3+'&date_end4='+date_end4);
}

function searchCallHistory () {
  phone_no      = frmSearch.document.getElementById('search_phone_no').value;
  caller_id     = frmSearch.document.getElementById('search_caller_id').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;
  
  $('#main_content').load('mgt_call_history.php?phone_no='+phone_no+'&caller_id='+caller_id+'&date_start='+date_start+'&date_end='+date_end);
}

function searchUser () {
  agent_name  = frmSearch.document.getElementById('search_agent_name').value;
  group_id    = frmSearch.document.getElementById('search_group_id').value;
  profile_id  = frmSearch.document.getElementById('search_profile_id').value;
  
  $('#main_content').load('util_user.php?agent_name='+agent_name+'&group_id='+group_id+'&profile_id='+profile_id);
}


function searchCallLater () {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  data_status   	= frmSearch.document.getElementById('search_data_status').value;
  
  $('#main_content').load('cust_call_later_list_nav.php?search_field='+search_field+'&keyword='+keyword+'&data_status='+data_status);
}



//Check Try Again On
var pause_for = 10; //minutes
var tryAmount = 1;
var tryCount  = 0;

function checkTryAgainOn(start) {
  tryCount++;
  if (tryCount == tryAmount) {
    tryCount = 0;
  }
  setTimeout("checkTryAgainOn(1)", pause_for * 60000);
  if(start==1) {
    showInfoBar("There is prospect (ABDUL LATIF AZZAN [02130753330]) need to called back.");
  }
}

function searchRecording () {
  search_field  = frmSearch.document.getElementById('search_field').value;
  keyword       = frmSearch.document.getElementById('search_keyword').value;
  agent_id      = frmSearch.document.getElementById('search_agent_id').value;
  date_start    = frmSearch.document.getElementById('datepicker1').value;
  date_end      = frmSearch.document.getElementById('datepicker2').value;

  $('#main_content').load('recording_list_nav.php?search_field='+search_field+'&keyword='+keyword+'&agent_id='+agent_id+'&date_start='+date_start+'&date_end='+date_end);
}

