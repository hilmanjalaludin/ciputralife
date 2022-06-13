<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	SetNoCache();
	
/* get user online or not online on tms_agent where	state=1 ***/

	function getListUserByLogin() 
	{
		global $db;
		
		if($db->getSession('handling_type') ==1) $sql  = " SELECT * FROM tms_agent a where a.user_state=1";
		if($db->getSession('handling_type') ==3) $sql  = " SELECT * FROM tms_agent a where a.user_state=1 and a.spv_id ='".$db->getSession('spv_id')."' and a.handling_type=4";
		else
		{
			$sql  = " SELECT * FROM tms_agent a where a.user_state=1 AND a.UserId NOT IN('".$_SESSION['UserId']."')";
		}
		
		return $sql;
	}
	
?>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script>
	
/* default of class controller data ***/
	
	
	
/* SendOnlineUser **/
	
	var SendOnlineUser = function()
	{
		var user_online_list = doJava.checkedValue('user_online');
		var text_message = doJava.dom('text_message').value;
		if 	(user_online_list==''){
			{alert("Please Select User")}
		}else
		{
			if (text_message=='')
			{alert("Text message is empty")}
			else{
				doJava.File ='../class/class.broadcast.msg.php';
				doJava.Params = {
					action:'send_user_online',
					user_list : user_online_list,
					text_message : text_message
				}
				
				var message_error = doJava.eJson();
				if( message_error.result==1){
					alert(message_error.msg);
				}
			}
		}
	}
	
/*  SendOfflineUser ***/
	
	var SendOfflineUser = function()
	{
		var user_online_list = doJava.checkedValue('user_online');
		var text_message = doJava.dom('text_message').value;
		if 	(user_online_list==''){
			{alert("Please Select User")}
		}else
		{
			if (text_message=='')
			{alert("Text message is empty")}
			else{
				doJava.File ='../class/class.broadcast.msg.php';
				doJava.Params = {
					action:'send_user_offline',
					user_list : user_online_list,
					text_message : text_message
				}
				
				var message_error = doJava.eJson();
				if( message_error.result==1){
					alert(message_error.msg);
				}
			}
		}	
	}
	
/* SendAllUser ******/
	
	var SendAllUser = function()
	{
		var user_online_list = doJava.checkedValue('user_online');
		var text_message = doJava.dom('text_message').value;
		if 	(user_online_list==''){
			{alert("Please Select User")}
		}else
		{
			if (text_message=='')
			{alert("Text message is empty")}
			else{
			doJava.File ='../class/class.broadcast.msg.php';
			doJava.Params = {
				action:'send_user_all',
				user_list : user_online_list,
				text_message : text_message
			}
			
			var message_error = doJava.eJson();
				if( message_error.result==1){
					alert(message_error.msg);
				}
			}
		}	
	}
	
/* jquery reader on ready ******/
	
	$(function(){
		$('#toolbars').extToolbars({
			extUrl   :'../gambar/icon',
			extTitle :[['Send to User Online'],['Send to User Offline'],['Send to all User'],],
			extMenu  :[['SendOnlineUser'],['SendOfflineUser'],['SendAllUser']],
			extIcon  :[['user_suit.gif'],['user_red.png'],['group.png']],
			extText  :true,
			extInput :false,
			extOption:[{
				render:1,
				type:'text',
				id:'v_benefit', 	
				name:'v_benefit',
				value:'',
				width:200
					}]
		});
	});
</script>
<style> .select:hover{background-color:#e6f69c;} </style>
<fieldset class="corner" style="border:1px solid #ddd;">
	<legend class="icon-menulist"> &nbsp;&nbsp;Broadcast Messages </legend>
	<div id="toolbars" style="margin-bottom:10px;margin-top:5px;"></div>
	<div class="box-shadow">
		<table cellpadding="4px" border=0>
			<tr>
				<td>
					<div style="border:0px solid #ddd;height:500px;overflow:auto;width:450px;padding:2px;">
						<table border=0 width="99%" cellspacing=0 align="LEFT" style="border-bottom:1px solid #dff4d4;border-right:1px solid #dff4d4;">
							<tr>
								<td style="border-top:1px solid #dff4d4;border-left:1px solid #dff4d4;font-family:Arial;font-size:12px;font-weight:bold;background-color:#9dbcd2;height:24px;color:#032740;" align="center">
								<a ahref="javascript:void(0);" style="cursor:pointer;" onclick="javascript:doJava.checkedAll('user_online');">#</a></td>
								<td style="padding-left:6px;border-top:1px solid #dff4d4;border-left:1px solid #dff4d4;font-family:Arial;font-size:12px;font-weight:bold;background-color:#9dbcd2;height:24px;color:#032740;" align="left">Agent Name</td>
							</tr>	
								<?php 
									$qry = $db -> query( getListUserByLogin() );
									
									$i = 0;
									foreach( $qry -> result_assoc() as $rows )
									{ 
										$color=($i%2!=0?'#f6f7f0':'#FFFFFF');
										$ImagesIcon = ($rows['logged_state']?'../gambar/icon/emoticon_grin.png':'../gambar/icon/emoticon_unhappy.png');
										$ImagesTitle = ($rows['logged_state']?'Online':'Offline');
										?> 
										<tr bgcolor="<?php echo $color;?>" class="select"> 
											<td style="color:#2a3301;border-top:1px solid #bdd7b0;border-left:1px solid #bdd7b0;text-align:center;">
											<input type="checkbox" name="user_online" id="user_online" value="<?php echo $rows['UserId']; ?>"></td>
											<td style="font-size:12px;color:#2a3301;border-top:1px solid #bdd7b0;border-left:1px solid #bdd7b0;padding-left:6px;"><?php echo $rows['id']; ?> - <?php echo $rows['full_name']; ?>
												&nbsp;<span title="<?php echo $ImagesTitle; ?>"> <img src="<?php echo $ImagesIcon; ?>"></span>
											</td>
										</tr>	
										<?php 
									$i++;
								} ?>
						</table>
					</div>
				</td>
				<td valign="top">
				<div class="box-shadow">
					<fieldset style="border:0px solid #dddddd;">
						<legend><b>Text Message</b></legend>
						<textarea name="text_message" id="text_message" style="border:1px solid #dddddd; font-family:consolas;font-size:12px;color:green;background-color:#fffccc;height:200px;width:400px;"></textarea>
					</fieldset>
				</div>	
				</td>
			</tr>	
		</table>	
	</div>
</fieldset>	