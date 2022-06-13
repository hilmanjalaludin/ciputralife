<?php
require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
require('../sisipan/parameters.php');
require('../class/lib.form.php');

?>
<html>
<script>
	var V_DATAS_INITCLAS = '../class/class.qccontact.detail.php';
	var V_DATAS_CUSTOMER = '<?php echo $db->escPost('CustomerId'); ?>';
	
	$('#toolbars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Exit']],
		extMenu  :[['exitApprove']],
		extIcon  :[['arrow_left.png']],
		extText  :true,
		extInput :true,
		extOption:[]
	});
	
	var exitApprove =function(){
		$('#main_content').load('dta_qc_approval_nav.php');
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
	
	getContactHistory();
</script>
<style>
	.ul-recording { list-style-type:square;font-family:Arial;}
	.ul-recording a{color:red;line-height:20px;text-decoration:none;font-size:10px;font-weight:bold;}
	.ul-recording a:hover{color:green;font-weight:bold;text-decoration:yes;font-size:10px;}
</style>
<body>
<fieldset class="corner" style="margin-top:-0px;border:1px solid #dddddd;">
	<legend class="icon-customers">&nbsp;&nbsp;Customer Approved</legend>	
		<div id="toolbars" class="corner" style="width:'100%';margin-bottom:4px;"></div>

		<table border=0 width="100%" cellpadding="0px" cellspacing="0px">
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
				</tr>
				<tr style="display:none;">
				<!-- frm.edit.qcpolicy.php -->
				
					<td>
						<div class="box-shadow" style="padding:1px;">
							<iframe name="iframe_policy_qc" id="iframe_policy_qc" src="frm.preview.policy.php?action=qcapprove&customerid=<?php echo $_REQUEST['CustomerId'];?>&campaignid=<?php echo CampaignIdByCustomerId();?>" width="1100px;" height="225px" scrolling="YES" style="text-align:center;border:1px solid #dddddd;"></iframe>
						</div>	
					</td>
				</tr>
				<tr>
					<td>
						<div class="box-shadow" style="padding:1px;height:300px;" id="contact_history">	</div>	
					</td>
				</tr>
			</table>
</fieldset>
</body>
</html>