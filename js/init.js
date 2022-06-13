$(document).ready(function(){

function loading(){			 
  	$('#loading').ajaxStart(function(){
  		$(this).fadeIn();
  	}).ajaxStop(function(){
  		$(this).fadeOut();
  	});
}

$.ajaxSetup({ cache: false }); 

  	$('#accordion ul li a').click(function() {
  		var url = $(this).attr('href');
			if(url!='#'){
				$('#main_content').load(url);
			return false;
		}
  	});

	$("#accordion").accordion({
		icons: {
		header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		},autoHeight : true
	});
	$("#accordions").accordion({
		icons: {
		header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		},autoHeight : true
	});
	$('#main_content').load('instruction.php');		 	
	$('#form_akuisisi_dialog').dialog({
		width:500,
		autoOpen:false,
		closeOnEscape: false,
   		open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
		modal: true,
  			overlay: {
  				backgroundColor: '#000',
  				opacity: 0.5
  			}
	});
	
	
	
	$("#loginUser").dialog({
		height:100,
		width:200
	 });
	 
	
	$('.formPhoneDialog').dialog({
			title		:'Form Additional Phone',
			bgiframe	:true,
			width		:400,
			height		:270,
			autoOpen	:false,
			modal		:true,
			closeOnEscape: false,
   			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();}
		});
	
	$('#quota_dialog').dialog({
		title		:'Edit Spv quota',
		bgiframe	:true,
		width		:450,
		height		:330,
		position	:['right','bottom'],
		autoOpen	:false,
		modal		:true,
		cache		:false,
		draggable	:false,
		closeOnEscape: false,
   		open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();}
	});
		
	$('.changedRequest').dialog({
			title		:'Form Request to change',
			bgiframe	:true,
			width		:400,
			height		:270,
			autoOpen	:false,
			modal		:true,
			draggable	:true,
			closeOnEscape: false,
   			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();}
		});	  
	$('#content-recording').dialog({
		title:'Play list recording',
		bgiframe:true,
		width:480,
		height:210,
		autoOpen:false,
		cache:false,
		modal:true,
		closeOnEscape: false,
		open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
		buttons: {
			Cancel: function() {
				if(confirm('Do you want to close ?')){
				 $('#main_content').load('recording_list_nav.php');
				 $(this).dialog('close');
				}
				else{
				return false;
				}
			}
		}
		
 	});
 
 
 	var options = { 
        target:  '#password_confirm',
        success: showResponse  // post-submit callback 
     }; 
	  
	$('#pwd').click(function(){
		Password();
	});
	
	$('#usr_logout').click(function(){
		UserLogOut();
	});
	
});


/* jQuery Bugs( jQuery-ui.js - Line 7287 )
 * Untuk menghindari dialog yang di close lewat icon (X)
 * yang terkadang tidak di destroy dengan benar maka untuk
 * membuat dialog sebaik-nya di simpan dalam function 
 */
 
 function showResponse(responseText){
 
      $("#password_confirm").dialog({
  			bgiframe: true,
  			modal: true,
  			buttons: {
  				Ok: function() {
  					$(this).dialog('close');
  				}
  			}
  		});
    } 	

var Password = function(){

 /* clear content value **/
	
	doJava.dom('curr_password').value='';
	doJava.dom('new_password').value='';
	doJava.dom('re_new_password').value='';
	
	$("#pass").dialog({
  			bgiframe: true,
  			autoOpen: false,
  			height: 210,
  			modal: true,
  			buttons: {
  				'Update': function(){
					doJava.File   = 'act_dialog_password.php'; 
  					doJava.Method = 'POST';
					doJava.Params = {
						curr_password   : doJava.dom('curr_password').value,
						new_password    : doJava.dom('new_password').value,
						re_new_password : doJava.dom('re_new_password').value
					}					
					var error = doJava.Post();
						if( error.length < 1 ){
							alert("Success, Update Yours Password");
							$(this).dialog('close');
						}
						else{
							alert(error); return;
						}
  				},
  				Cancel: function() {
  					$(this).dialog('close');
  				}
  			}
  	}).dialog('open');
 } 
	
var UserLogOut = function(){	
		$("#logout").dialog({
			 bgiframe	: true,
			 autoOpen	: false,
			 resizable	: false,
			 height		: 140,
			 modal		: true,
			 overlay: {
					backgroundColor: '#000',
					opacity: 0.5
			 },
			 buttons: {
				'Logout': function() {
					document.location='sys_logout.php';
					},
				Cancel: function() {
					$(this).dialog('close');
					
				}
			 }
			 
		}).dialog('open');
	}

var UserChangeList = function(){
	$(function()
	{
		$('#change_request_dialog').dialog({
			title:' Change Request',
			bgiframe: true,
  			autoOpen: false,
  			height: 240,
			width:500,
			position:['center','center'],
  			modal: true,
  			buttons:{
				Exit :function()
				{
					$(this).dialog('close');
						$(this).empty()
							$(this).dialog('destroy')
				},
				
				Send:function()
				{
					 var ITM_VALUE = doJava.dom('cb_request_type').value
					 var NEW_VALUE = doJava.dom('txt_new_value').value
					 var PHN_TYPE  = doJava.dom('cb_phone_type').value 
					
					 	doJava.File = '../class/tpl.contact.detail.php';
						doJava.Method ="POST"; 
						doJava.Params = {
							action : 'send_request_item',
							item_value : ITM_VALUE,
							//item_old_value  : OLD_VALUE,
							item_new_value  : NEW_VALUE,
							item_customer   : CustomerId,
							item_phone_type : PHN_TYPE	  		
						}
						
					//if( (ITM_VALUE!='') && (OLD_VALUE!='') && (NEW_VALUE!=''))
					if( (PHN_TYPE!='') && (NEW_VALUE!=''))
					{
						var error = doJava.Post();
							if( error==1)
							{ 
								alert('Success sending the request!');
									$(this).dialog('close');
										$(this).empty()
											$(this).dialog('destroy')		
							}
							else { alert('Failed sending the request!');}
					}
					else{ alert('Input is not complete!')}
				}
			}
		 }).dialog('open').load(InitPhp+"action=change_request&customerid="+CustomerId+"&campaignid="+CampaignId);
		 
	  });
   } 	
 
 /* start from here ..
  * disable for menu aksess if user click contact detail data 
  * Or User Click back to home return object sparated 
  * definer class object data  :) 
  * author : omens 
  */
 
	var ActiveWarning = function(array_id){
		this.menu_class_id=[];
		if( array_id!='' ){
			this.menu_class_id = array_id;
		}	
		// alert(array_id);
		this.aksess_level = false;
	};
	
	ActiveWarning.prototype.messages = (function(){
		alert('You Can\'t click menu, please !');
		return false;
	});
	
	ActiveWarning.prototype.NotActive = function(){
		for( var current in this.menu_class_id )
		{	
			try{
				doJava.dom(this.menu_class_id[current].id).href = "javascript:void(0);";
				doJava.dom(this.menu_class_id[current].id).addEventListener("click", this.messages);
			}
			catch(e){
				console.log(e+"-->"+this.menu_class_id[current].id);
			}
		}
		
		this.aksess_level = true;
	}
	
	ActiveWarning.prototype.Active = function(){
		for( var current in this.menu_class_id ){
			try{
				doJava.dom(this.menu_class_id[current].id).href = this.menu_class_id[current].name;
				doJava.dom(this.menu_class_id[current].id).removeEventListener("click", this.messages);
			}
			catch(e){
				console.log(e+"-->"+this.menu_class_id[current].id);
			}
		}
		this.aksess_level = false;
	}
	
	ActiveWarning.prototype.Home = function(){
		if( !this.aksess_level){
			$('#main_content').load('instruction.php');
		}
		else{
			this.messages();
		}
	}

/* stop this here ..
 * author : omens 
 */
 
 var ChatWith = function(){
	new (function(){
		var winX = ($(window).width()/2);
		var winY = ($(window).height()/2);
			doJava.File = "act_window_gent_popup.php";
			doJava.Params = { act:'show_agent_ready'}
			newwindow=window.open(doJava.getWindowUrl(),'name','height=300,width=400,top='+winY+',left='+winX+'');
			if (window.focus) {newwindow.focus()}
		return false;
	});
 }
 
 var ChatWith = function(){
	new (function(){
		var winX = ($(window).width()/2);
		var winY = ($(window).height()/2);
			doJava.File = "act_window_gent_popup.php";
			doJava.Params = { act:'show_agent_ready'}
			newwindow=window.open(doJava.getWindowUrl(),'ChatWith','height=300,width=400,top='+winY+',left='+winX+'');
			if (window.focus) {newwindow.focus()}
		return false;
	});
 }

 var newWindowScore;
 var OpenWindowScoring = function(CustomerId){
	var CustomerId = CustomerId;
	new(function(){
		var winX = ($(window).width()/2);
		var winY = ($(window).height()/4);
			doJava.File = "act.window.scoring.php";
			doJava.Params = { 
				act:'show_agent_ready',
				CustomerId : CustomerId
			}
			newWindowScore = window.open(doJava.getWindowUrl(),'Scoring','height=380,width=350,top='+winY+',left='+winX+'');
			if (window.focus) {newWindowScore.focus()}
		return false;
	});
 }
 
 var EL_DATA_POINT = new Array(); 
 var WindowRender = function(point_number, elements_name)
 {
	EL_DATA_POINT = elements_name;
	doJava.dom('nilai_data').value = point_number;
 }
 