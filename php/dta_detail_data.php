<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');

?>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script>

 /* define  code call **/
	
	var V_INTWTHSPS = 402;
	var V_INTWTHSLF = 401;
	var V_CALLBACKL = 310;

/* define info customers **/
	
	var CustomerId = '<?php echo $db->escPost('customerid'); ?>';
	var CampaignId = '<?php echo $db->escPost('campaignid'); ?>';
	var InitPhp	   = '../class/tpl.detail.data.php?';
	var phoneCall  = {
						initPhone  :  false,
						initType   : '', 
						initNumber : '',
						InitLater  : false,
						initCreate : false,
						initCall   : false,
						initStatus : 0
					 }
	
	
/* on top content **/
	
	var getDefaultContact = function(){
		$(function(){
			$('#contact_default_info').load(InitPhp+"action=default_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}
	
 /* Home **/
 
	var getHomeContact = function(){
		$(function(){
			$('#contact_home').load(InitPhp+"action=home_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}
	
/* Office **/
	
	var getOfficeContact = function(){
		$(function(){
			$('#contact_office').load(InitPhp+"action=office_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}
	

/* action get Open call **/
	
	var getContactHistory = function(){
		$(function(){
			$('#contact_cust_history').load(InitPhp+"action=history_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}

/* action get Open call **/

	
	var getContactReason = function(){
		$(function(){
			$('#contact_reason_call').load(InitPhp+"action=reason_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});

	}
	
	var sectioncardinfo=function(){
			$(function(){
			$('#section_card_info').load(InitPhp+"action=card_info&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
		}

	
/* action get Open call **/
	
	var getCallReasontext = function(callStatus, cond){
		if( cond ){
			$(function(){
				$('#contact_reason_text').load(InitPhp+'action=call_reason_text&call_status='+callStatus);
			})
		}
	}

/* action get Open call **/
	
	$(function(){
		getDefaultContact();
		getHomeContact();
		getOfficeContact();		
		getContactHistory();
		//getContactReason();
		sectioncardinfo();
	});
	
</script>
<h3 style="margin-left:5px;margin-top:2px;background-color:#ffffff;font-family:Arial;color:red;padding:6px;font-weight:bold;" class="box-shadow" >Customer Detail </h3>
<div class="contact_detail"> 
	<table width="100%" border=0>
		<tr>
			<td  width="60%" valign="top">
				<div id="contact_default_info" class="box-shadow box-left-top" style="margin-bottom:8px;"></div>
				<div class="box-shadow">
					<table width="99%" style="margin-bottom:8px;"  align="center">
						<tr>
							<td id="contact_home" width="50%" valign="top" style="background-color:#FFFFFF;"></td>
							<td id="contact_office" width="50%" valign="top" style="background-color:#FFFFFF;"></td>
						</tr>	
					</table>
				</div>
				<div id="section_card_info" class="box-shadow box-left-top" style="margin-bottom:8px;"></div>
				<div class="box-shadow">
					<table width="99%" align="center">
						<tr>
							<td id="contact_cust_history" width="50%"></td>
						</tr>	
					</table>
				</div>
				
			</td>
			
		</tr>
	</table>
	
</div>