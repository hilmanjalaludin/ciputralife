<?php
 require("../sisipan/sessions.php");
 require("../fungsi/global.php");
 require("../class/MYSQLConnect.php");
 require('../sisipan/parameters.php');
 require('../class/lib.form.php');
 
 
 /** get form edit alyout ***/
 
 function getFormLayout()
	{
		global $db;
		$sql = " SELECT a.FormLayout , a.FormEditLayout
				 FROM t_gn_formlayout a 
				 LEFT JOIN t_gn_campaignproduct b on a.ProductId=b.ProductId
				 WHERE a.FormConds=1 
				 AND b.CampaignId='".$db -> escPost('CampaignId')."' ";
		//echo $sql;
		
		$qry = $db->execute($sql,__FILE__,__LINE__);	
		if( $qry && ($rows = @mysql_fetch_assoc($qry)))
		{	
			return $rows['FormEditLayout'];
		}
	}
	
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
	
/** get CampaignId By CustomerId **/
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
	

	/** in prosess by QC **/
		
		function QaInProsess(){
			global $db;
				$sql = " Update t_gn_customer a SET a.CustomerReferalProsess=1 WHERE a.CustomerId='".$db->escPost('CustomerId')."'";
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
		
	QaInProsess();	
	 	
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
		
?>
<html>
<script>
	
	var V_DATAS_CUSTOMER = '<?php echo $db->escPost('CustomerId'); ?>';
	var V_DATAS_INITCLAS = '../class/class.referal.detail.php';
		
	$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Exit'],['Show Referal']],
				extMenu  :[['ExitReferal'],['ShowReferal']],
				extIcon  :[['door_out.png'],['group_go.png']],
				extText  :true,
				extInput :true,
				extOption:[]
	});
	
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
	
	var ExitReferal =function(){
		if(confirm('Do you want to exit from this session ?')){
			if( ClearProses() ){
				$('#main_content').load('src_aproval_referal_nav.php');
			}
		}
		else
			//location.reload(); 
			return false;
	}
	
	var ShowReferal = function()
	{
		var xScreen = ($(window).width()-250);
		var yScreen = ($(window).height());
			
		doJava.Params = {
			CustomerId	: V_DATAS_CUSTOMER
		}
		
		doJava.winew.winconfig={
				location	: 'frm.edit.window.referal.php?'+doJava.ArrVal(),
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
	
	getFileRecording();
	getContactHistory();
		
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
												<?php
													//echo $_REQUEST['CustomerId'];
													//echo CampaignIdByCustomerId();
												?>
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
				</tr>
				<!--<tr>
				<!-- frm.edit.qcpolicy.php 
				
					<td>
						<div class="box-shadow" style="padding:1px;">
							<iframe name="iframe_policy_qc" id="iframe_policy_qc" src="frm.edit.qcpolicy.php?action=qcapprove&customerid=<?php //echo $_REQUEST['CustomerId'];?>&campaignid=<?php //echo CampaignIdByCustomerId();?>" width="835px" height="225px" scrolling="YES" style="text-align:center;border:1px solid #dddddd;"></iframe>
						</div>	
					</td>
				</tr>-->
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