<?php
 require("../sisipan/sessions.php");
 require("../fungsi/global.php");
 require("../class/MYSQLConnect.php");
 require('../sisipan/parameters.php');
 require('../class/lib.form.php');
 

 /** get Call result Code bypass from Custid **/
 
	function getCallResultCode(){
		global $db;
		
		$sql = "SELECT  d.CallReasonCode
				FROM t_gn_customer a INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
					LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
					LEFT join t_lk_callreason d on a.CallReasonId =d.CallReasonId
					left join tms_agent e on a.SellerId=e.UserId
				WHERE a.CustomerId='".$db->escPost('CustomerId')."'";
				
				
		$codec = $db -> fetchval($sql,__FILE__,__LINE__);
		if( $codec ) : return $codec;
		else : return null;
		endif;
	}
	

	/** in prosess by QC **/
		
		function QaInProsess(){
			global $db;
				$sql = " Update t_gn_customer a SET a.QaProsess=1 WHERE a.CustomerId='".$db->escPost('CustomerId')."'";
				$db -> execute($sql,__FILE__,__line__);
		}
	/* function get QC Status **/
		
		function getStatusInQA(){
			global $db;
			
			$datas = array();
			$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc
					from t_lk_callreason a 
					left join t_lk_callreasoncategory b on a.CallReasonCategoryId=b.CallReasonCategoryId
					where a.CallReasonContactedFlag=3
					and b.CallReasonCategoryCode='QA'
					ORDER BY a.CallReasonLevel ASC";

			
			$qry = $db -> execute($sql,__FILE__,__line__);	
			while( $row = $db ->fetchrow($qry)){
				$datas[] = $row;
			}
			return $datas;
		}
		
	 //QaInProsess();	
	 
	 
	 	
	function getDataCollMon()
	{
		global $db;
			$sql = " select 
							((a.SubCategoryId)-1) as s_form, 
							concat('A_INIT',a.SubCategoryId,'h') as s_object,
							concat('sliderValue',a.SubCategoryId,'h') as s_name,
							a.SubCategoryDesc as n_label, 
							a.StartNumber as n_minValue, 
							a.EndNumber as n_maxValue,
							'0' as n_value,
							a.StepNumber as n_step
							from coll_subcategory_collmon a
							where a.SubCategoryFlags=1 ";
			$qry = $db -> query($sql);		 
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[][$rows['s_object']] = array
				(
					's_form'  => $rows['s_form'],
					's_name'  => $rows['s_name'],
					'n_minValue' => $rows[n_minValue],
					'n_maxValue' => $rows[n_maxValue],
					'n_value' => $rows[n_value],
					'n_step' => $rows[n_step],
					'n_label' => $rows['n_label']
				); 
			}
			return $datas;
	}
	function CampaignIdByCustomerId()
	{
		global $db;
		$sql = " SELECT a.CampaignId from t_gn_customer a WHERE a.CustomerId ='".$db->escPost('CustomerId')."'";
		$qry = $db -> query($sql);
		if( !$qry -> EOF() )
		{
			return $qry -> result_singgle_value();
		}
	}
	
		
?>
<html>
<script>
	
	var V_DATAS_CUSTOMER = '<?php echo $db->escPost('CustomerId'); ?>';
	var V_DATAS_CODECT	 = '<?php echo getCallResultCode(); ?>';	
	var V_DATAS_INITCLAS = '../class/class.suspend.detail.php';
	
	var winFrame = window.iframe_policy_qc;
	var phoneCall  = {
						initPhone  :  false,
						initType   : '', 
						initNumber : '',
						InitLater  : false,
						initCreate : false,
						initCall   : false,
						initStatus : 0,
						initChoose : false,
						CustomerId : V_DATAS_CUSTOMER
					 }
					 
	
	$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Edit Cust Name'],['DC score']],
				extMenu  :[['EditCustomer'],['newScore']],
				extIcon  :[['folder_user.png'],['application_edit.png']],
				extText  :true,
				extInput :true,
				extOption:[]
	});
	var newScore = function(){
		var xScreen = (Ext.DOM.window.screen.availWidth-200);
		var yScreen = (Ext.DOM.window.screen.availHeight);
			doJava.winew.winconfig= { // get dojava object 
				location	: '../class/class.redirect.php?page=showScoring&CustomerId='+V_DATAS_CUSTOMER,
				width		: xScreen,
				height		: yScreen, 
				windowName	: 'NewScoringWindow',
				resizable	: true, menubar : false, 
				scrollbars	: true,  status : false, 
				toolbar		: false
			}
			
			try {
				if( !doJava.winew.winHwnd.closed) doJava.winew.winClose();
				doJava.winew.open();
			
			}
			catch(e){
				console.log(e)
			}
	}
	var EditCustomer = function(){
		var CustomerId= V_DATAS_CUSTOMER;
		if( CustomerId!=''){
			var windowX = window.open('frm.edit.custname.php?CustomerId='+CustomerId,"myWindowPdf","height=210,width=800,menubar=no,status=no");
			windowX.close();
			windowX=window.open('frm.edit.custname.php?CustomerId='+CustomerId,"myWindowPdf","height=210,width=800,menubar=no,status=no");
		}
	}
	
	var getContactHistory = function(){
			$(function(){
				doJava.Params = { 
					action:'history_contact',
					customerid:V_DATAS_CUSTOMER
				}
				$('#contact_history').load( V_DATAS_INITCLAS+'?'+doJava.ArrVal() );
			});
	}
	
	var showWindow = function(){
		var xScreen = ($(window).width()-250);
		  var yScreen = ($(window).height());
			
			doJava.Params = {
				CustomerId:V_DATAS_CUSTOMER
			}
			
			doJava.winew.winconfig={
					location	: '../coll_mon/coll.mon.index.php?'+doJava.ArrVal(),
					width		: xScreen,
					height		: yScreen,
					windowName	: 'windowName',
					resizable	: false, 
					menubar		: false, 
					scrollbars	: true, 
					status		: false, 
					toolbar		: false
			};
			
			if( !doJava.winew.winHwnd.closed) doJava.winew.winClose();
				doJava.winew.open();
		
		 	
	}
	
	var getFileRecording = function(){
			$(function(){
				doJava.Params = { 
					action:'get_recording',
					customerid:V_DATAS_CUSTOMER
				}
				$('#recording_file').load( V_DATAS_INITCLAS+'?'+doJava.ArrVal() );
			});
	}
	
	var playRecording = function(filename){
		$(function(){
				doJava.Params = { 
					action:'play_recording',
					rec_id:filename,
					customerid:V_DATAS_CUSTOMER
				}
				$('#recording_play').load( V_DATAS_INITCLAS+'?'+doJava.ArrVal() );
		});
	}

 /* clear prosess verified **/
 
	var ClearProses = function(){
		doJava.File = V_DATAS_INITCLAS;
		
			doJava.Params ={
				action:'reset_prosess',
				customerid:V_DATAS_CUSTOMER
			}
		var error = doJava.Post();
		if( error==1) { return true; }
		else { return false; }
	}
	
 /* exit  prosess verified **/	
 
	var exitApprove =function(){
		
		if(confirm('Do you want to exit from this session ?')){
			if( ClearProses() ){
				$('#main_content').load('dta_suspend_nav.php');
			}
		}
		else
			return false;
	}
	
	
	var hangupCustomer = function(){	
		if( phoneCall.initCall )
		{
			phoneCall.initCall = false;
			document.ctiapplet.callHangup()
		}
		else{
			alert("No call activity!");
		}
	}
	
	var setCallNumber = function(phoneNumber){
		if( phoneNumber!=''){
			phoneCall.initPhone = true;
			phoneCall.initNumber = phoneNumber;
		}
		else{
			phoneCall.initPhone = false;
			phoneCall.initNumber = '';
		}
	}
	
	var dialCustomer = function()
	{
		if(phoneCall.initPhone)
		{
			if( document.ctiapplet.getAgentStatus()==AGENT_READY){
				//phoneCall.initNumber = '02132066189';
				document.ctiapplet.callDialCustomer('',phoneCall.initNumber,V_DATAS_CUSTOMER,V_DATAS_CUSTOMER);	
				phoneCall.initCall = true;
				phoneCall.initPhone = true;
				setTimeout(function(){
					var call_contact = document.getElementsByName('apprv_status');
						for(var i = 0; i< call_contact.length; i++)
						{
							call_contact[i].disabled = false;
						}
				},5000);
			}
			else{
				alert('Please set Ready!'); return;
			}
		}else{ alert('Please select the phone number!'); return false; }
	}
	
	
	var saveApprove = function(){
	
		var verified_status = doJava.checkedValue('apprv_status');
		var qulity_score = doJava.dom('nilai_data').value;
		var notes_qc = doJava.Value('notes_qc');
		if( verified_status==''){
			alert('Please select status !');
			return false;
		}
		else if( notes_qc==''){
			alert('Notes Can not empty !');
			return false;
		}
		else{
			stop_database('stop_counter_endtime'); // save acw duration timer 	
			doJava.File = V_DATAS_INITCLAS;
			doJava.Params = {
				action 	: 'save_qc',
				customerid : V_DATAS_CUSTOMER,
				status : verified_status,
				codec : V_DATAS_CODECT,
				notes : notes_qc,
				qulity_score : qulity_score
			}
			var error = doJava.eJson();
			if( error.result ==1)
			{
				alert("Success, Save Verified! ");
				//newWindowScore.close();
				getContactHistory();
			}
			else{ alert("Failed, Save Verified! "); } 
		
		}
	}
	
	var getContactReason = function(){
		$(function(){
			doJava.Params = { 
				action:'reason_contact',
				customerid:V_DATAS_CUSTOMER
			}
			$('#contact_reason_call').load( V_DATAS_INITCLAS+'?'+doJava.ArrVal() );
		});
		

	}
	
	$(function(){
		getFileRecording();
		getContactHistory();
		getContactReason();
	});
</script>
<style>
	.ul-recording { list-style-type:square;font-family:Arial;}
	.ul-recording a{color:red;line-height:20px;text-decoration:none;font-size:10px;font-weight:bold;}
	.ul-recording a:hover{color:green;font-weight:bold;text-decoration:yes;font-size:10px;}
</style>
<body>
<fieldset class="corner" style="margin-top:-0px;border:1px solid #dddddd;">
	<legend class="icon-customers">&nbsp;&nbsp;Approval Detail </legend>	
		<div id="toolbars" class="corner" style="width:'100%';margin-bottom:4px;"></div>

		<table border=0 width="70%" cellpadding="0px" cellspacing="0px">
				<tr>
					<td valign="top">
						<div class="box-shadow" style="height:180px;">
							<table>
								<tr>
									<td valign="top" id="xx"> 
										<fieldset style="border:1px solid #dddddd;"> 	
											<legend>Recording List </legend>
												<div class="box-shadow" id="recording_file" style="width:400px;overflow:auto;height:130px;border:1px solid #dddddd;"></div>
										</fieldset>
									</td>
									<td valign="top" >
										<fieldset style="border:1px solid #dddddd;"> 	
											<legend>Play Recording </legend>
											<div id="recording_play" style="width:370px;overflow:auto;height:140px;border:0px solid #dddddd;"> </div>
										</fieldset>	
									</td>
								</tr>	
							</table>
						</div> 
					</td>
					<td valign="top" rowspan="3">
						<div class="box-shadow" style="border:1px solid #dddddd;width:250px;height:450px;width:'100%';padding-left:4px;">
							<table width="70%" style="margin-top:2px;" cellpadding="5px;">
								<tr>
									<td nowrap style="text-align:left;color:red;">Phone Number </td>
									<td nowrap style="height:28px;" id="phone_primary_number"> 
										<div id="contact_reason_call"></div>
									</td>
								</tr>
								
								<tr>
									<td style="height:18px;">&nbsp;</td>
									<td nowrap style="height:18px;" align="left">
										<img src="../gambar/PhoneCall.png" width="35px" height="35px" style="cursor:pointer;" title="Dial..." onclick="dialCustomer();">
										<img src="../gambar/HangUp.png" width="35px" height="35px" style="cursor:pointer;" title="Hangup..." onclick="hangupCustomer();">
										
									</td>
								</tr>
								<?php
									foreach( $db -> Entity -> ReasonLabelQualitySuspend() as $KeyCodes => $KeyNames ) { ?>
										<tr>
											<th colspan="2" style="text-align:left;color:red;"> 
												<input type="radio" name="apprv_status" id="apprv_status" value="<?php echo $KeyCodes; ?>" disabled> <?php echo $KeyNames;?>
											</th>
										</tr>
								<?php } ?>
								<tr>
									<td colspan="2">
										<textarea id="notes_qc" style="width:230px;font-family:Consolas;font-size:11px;height:200px;border:1px solid #dddddd;background-color:#FFFCCC;"></textarea>
									</td>
								</tr>
								
								<tr style="display:none;">
									<td>Score</td>
									<td><input type="text" name="nilai_data" id="nilai_data" 
											style="font-family:Arial;font-size:12px; border:1px solid #FF4321;text-align:center;width:30px;background-color:#FFFCCC;" readonly="true"></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td> 
										<!--<a href="javascript:void(0);" class="sbutton" onclick="OpenWindowScoring(window.V_DATAS_CUSTOMER);" style="margin-right:3px;"><span>&nbsp;Scoring</span></a>-->
										<a href="javascript:void(0);" class="sbutton" onclick="saveApprove();" style="margin-right:2px;"><span>&nbsp;Save</span></a>
											<!--<a href="javascript:void(0);" class="sbutton" onclick="EditName();" style="margin-left:2px;"><span>&nbsp;E.Name</span></a>-->
										<a href="javascript:void(0);" class="sbutton" onclick="exitApprove();" style="margin-left:2px;"><span>&nbsp;Exit</span></a>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr>
				<!-- frm.edit.qcpolicy.php -->
				
					<td>
						<div style="height:220px;overflow:auto;border:1px solid #dddddd;">
							<iframe name="iframe_policy_qc" id="iframe_policy_qc" src="form.axa.qa.product.php?action=qcapprove&customerid=<?php echo $_REQUEST['CustomerId'];?>&campaignid=<?php echo CampaignIdByCustomerId();?>" width="835px" height="800px" scrolling="yes" style="text-align:center;border:1px solid #dddddd;" ></iframe>
						</div>	
					</td>
				</tr>
				<tr>
					<td>
						<div class="box-shadow" style="padding:1px;height:300px;" id="contact_history">
						</div>	
					</td>
				</tr>
			</table>
</fieldset>
</body>
</html>