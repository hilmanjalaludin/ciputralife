<?php
 require("../sisipan/sessions.php");
 require("../fungsi/global.php");
 require("../class/MYSQLConnect.php");
 require('../sisipan/parameters.php');
 
 
 /** get Call result Code bypass from Custid **/
 //print_r($_REQUEST);
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
		
		
	 QaInProsess();	
		
?>
<script>
	
	var V_DATAS_CUSTOMER = '<?php echo $db->escPost('CustomerId'); ?>';
	var V_DATAS_CODECT	 = '<?php echo getCallResultCode(); ?>';	
	var V_DATAS_INITCLAS = '../class/class.qccontact.detail.php';
	
	
	var getContactHistory = function(){
			$(function(){
				doJava.Params = { 
					action:'history_contact',
					customerid:V_DATAS_CUSTOMER
				}
				$('#contact_history').load( V_DATAS_INITCLAS+'?'+doJava.ArrVal() );
			});
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
				$('#main_content').load('dta_qc_nav.php');
			}
		}
		else
			return false;
	}
	
	var saveApprove = function(){
		var verified_status = doJava.checkedValue('apprv_status');
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
			doJava.File = V_DATAS_INITCLAS;
			doJava.Params = {
				action : 'save_qc',
				customerid : V_DATAS_CUSTOMER,
				status : verified_status,
				codec : V_DATAS_CODECT,
				notes : notes_qc
			}
			
			var error = doJava.Post();
			if( error==1){
				alert("Success, Save Verified! ");
				getContactHistory();
			}
			else
				alert("Failed, Save Verified! ");
		
		}
	}
	
	getFileRecording();
	getContactHistory();
	$('.corner').corner();
		
</script>
<style>
	.ul-recording { list-style-type:square;font-family:Arial;}
	.ul-recording a{color:red;line-height:20px;text-decoration:none;font-size:10px;font-weight:bold;}
	.ul-recording a:hover{color:green;font-weight:bold;text-decoration:yes;font-size:10px;}
</style>
<fieldset class="corner" style="margin-top:-0px;border:1px solid #dddddd;">
	<legend class="icon-customers">&nbsp;&nbsp;Approval Detail </legend>	
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
						<div class="box-shadow" style="border:1px solid #dddddd;width:250px;height:405px;width:'100%';padding-left:4px;">
							<table width="70%" style="margin-top:10px;" cellpadding="5px;">
								<tr>
									<th colspan="2" style="text-align:left;color:red;">
										<input type="radio" name="apprv_status" id="apprv_status" value="1"> Verified QC</th>
								</tr>
								
								<tr>
									<th colspan="2" style="text-align:left;color:red;">
									<input type="radio" name="apprv_status" id="apprv_status" value="2"> Pending QC</th>
								</tr>
								<tr>
									<th colspan="2" style="text-align:left;color:red;">
									<input type="radio" name="apprv_status" id="apprv_status" value="3"> Reject QC</th>
								</tr>
								<tr>
									<td colspan="2">
										<textarea id="notes_qc" style="width:230px;font-family:Consolas;font-size:11px;height:240px;border:1px solid #dddddd;background-color:#FFFCCC;"></textarea>
									</td>
								<tr>
									<td>  </td>
									<td> 
										<a href="javascript:void(0);" class="sbutton" onclick="saveApprove();" style="margin-right:2px;"><span>&nbsp;Save</span></a>
										<a href="javascript:void(0);" class="sbutton" onclick="exitApprove();" style="margin-left:2px;"><span>&nbsp;Exit</span></a>
									</td>
								</tr>
								</table>
								
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="box-shadow" style="padding:1px;">
							<iframe src="frm.edit.qcpolicy.php?action=qcapprove&customerid=<?php echo $_REQUEST['CustomerId'];?>&campaignid=<?php echo $_REQUEST['CampaignId'];?>" width="835px" height="250px" style="text-align:center;overflow:'hidden';border:1px solid #dddddd;"></iframe>
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