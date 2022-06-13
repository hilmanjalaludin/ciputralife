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
		
?>
<script>
	
	var V_DATAS_CUSTOMER = '<?php echo $db->escPost('CustomerId'); ?>';
	var V_DATAS_INITCLAS = '../class/class.datacontact.detail.php';
	
	var winFrame = window.iframe_policy_qc;
	
	$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Exit']],
				extMenu  :[['exitApprove']],
				extIcon  :[['folder_user.png']],
				extText  :true,
				extInput :true,
				extOption:[]
	});
	
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

 /* exit  prosess verified **/	
 
	var exitApprove =function(){
		if(confirm('Do you want to exit from this session ?')){
			if( 1==1 ){
				$('#main_content').load('src_data_nav.php');
			}
		}
		else
			return false;
	}

/* get history call ***/

	new(function(){
		$(function(){
			doJava.Params = { 
				action:'history_contact',
				customerid:V_DATAS_CUSTOMER
			}
			$('#contact_history').load( V_DATAS_INITCLAS+'?'+doJava.ArrVal() );
		});
	})
	
/* get recoding call ***/
	
	new(function(){
		$(function(){
			doJava.Params = { 
				action:'get_recording',
				customerid:V_DATAS_CUSTOMER
			}
			$('#recording_file').load( V_DATAS_INITCLAS+'?'+doJava.ArrVal() );
		});
	});
	
/* get informasi data call ***/
	
	new(function(){
		doJava.File = V_DATAS_INITCLAS;
		doJava.Params = {
			action :'get_information',
			customerid : V_DATAS_CUSTOMER
		}
		doJava.Load('content_information_customer')
	});
		
</script>
<style>
	.ul-recording { list-style-type:square;font-family:Arial;}
	.ul-recording a{color:red;line-height:20px;text-decoration:none;font-size:10px;font-weight:bold;}
	.ul-recording a:hover{color:green;font-weight:bold;text-decoration:yes;font-size:10px;}
	.txt_header { color:#703c04; text-align:right;font-weight:bold;font-size:12px;border-bottom:1px solid #dddddd;}
	.txt_input { color:#000000;font-size:11px; background:url('../gambar/input_bg.png'); text-align:left; height:16px;width:200px; border:1px solid #c9bb81;padding-left:2px;}
</style>
<fieldset class="corner" style="margin-top:-0px;border:1px solid #dddddd;">
	<legend class="icon-customers">&nbsp;&nbsp;Data Detail </legend>	
		<div id="toolbars" class="corner" style="width:'100%';margin-bottom:4px;"></div>
		<table border=0 width="99%" cellpadding="0px" cellspacing="0px">
				<tr>
					<td valign="top">
						<div class="box-shadow" style="height:180px;">
							<table width="99%">
								<tr>
									<td valign="top" id="xx"> 
										<fieldset style="border:1px solid #dddddd;"> 
											<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;">Recording List</legend>
											<div class="box-shadow" id="recording_file" style="width:'100%';overflow:auto;height:130px;border:1px solid #dddddd;"></div>
										</fieldset>
									</td>
									<td valign="top" >
										<fieldset style="border:1px solid #dddddd;"> 
											<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;">Play Recording</legend>
											<div id="recording_play" style="width:'100%';overflow:auto;height:140px;border:0px solid #dddddd;"> </div>
										</fieldset>	
									</td>
								</tr>	
							</table>
						</div>
					</td>
				</tr>
				<tr>
				<td>
					<div class="box-shadow" style="padding:1px;">
						<fieldset class="corner">
							<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;">Customer Information</legend>
							<div class="box-shadow" id="content_information_customer"></div>
						</fieldset>
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