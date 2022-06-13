<?php

require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.nav.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
require(dirname(__FILE__)."/../class/lib.form.php");
class AssignCampaign extends mysql
{
	function AssignCampaign()
	{
		parent::__construct();
	}	
	
/** get campaign on User level **/
	
	function getActiveCampaign()
	{
		$sql = " select c.CampaignId, c.CampaignName from t_gn_assignment a
				 left join t_gn_customer b on a.CustomerId=b.CustomerId 
				 left join t_gn_campaign c on b.CampaignId=c.CampaignId
				 where c.CampaignStatusFlag=1";
				 
	/** filtering session ***/
	
		$UserId = $this -> getSession('UserId');
		if( $this -> getSession('handling_type') == USER_ROOT ) $sql.=" "; 
		if( $this -> getSession('handling_type') == USER_ADMIN ) $sql.=" AND a.AssignAdmin='$UserId'"; 
		if( $this -> getSession('handling_type') == USER_ACCMANAGER ) $sql.=" AND a.AssignManager='$UserId'";
		if( $this -> getSession('handling_type') == USER_MANAGER ) $sql.=" AND a.AssignMgr='$UserId'";
		if( $this -> getSession('handling_type') == USER_SUPERVISOR ) $sql.=" AND a.AssignSpv='$UserId'";
		if( $this -> getSession('handling_type') == USER_TELESALES )   $sql.=" AND a.AssignSelerId='$UserId'";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CampaignId']] = $rows['CampaignName'];
		}
		return $datas;
	}
	
	
/** get agent by login **/
	
	function getAgentByLogin()
	{
		switch( $this -> getSession('handling_type'))
		{
			case USER_ROOT: 
				$sql = " SELECT * FROM tms_agent a WHERE a.user_state=1 
						 AND a.handling_type='".USER_TELESALES."' "; 
			break;
			
			case USER_ADMIN: 
				$sql = " SELECT * FROM tms_agent a WHERE a.user_state=1 
						 AND a.handling_type='".USER_TELESALES."'
						 AND a.admin_id='".$this -> getSession('UserId')."'";
			break;
			
			case USER_ACCMANAGER:
			$sql = " SELECT * FROM tms_agent a WHERE a.user_state=1 
						 AND a.handling_type='".USER_TELESALES."' 
						 AND a.manager_id='".$this -> getSession('UserId')."'"; 
			break; 
			
			case USER_MANAGER: 
				$sql = " SELECT * FROM tms_agent a WHERE a.user_state=1 
						 AND a.handling_type='".USER_TELESALES."' 
						 AND a.mgr_id='".$this -> getSession('UserId')."'"; 
			break; 
			
			case USER_SUPERVISOR: 
				$sql = " SELECT * FROM tms_agent a WHERE a.user_state=1 
						 AND a.handling_type='".USER_TELESALES."' 
						 AND a.spv_id='".$this -> getSession('UserId')."'"; 
					 
			break;
			
			case USER_TELESALES: 
				$sql = " SELECT * FROM tms_agent a WHERE a.user_state=1 
						 AND a.handling_type='".USER_TELESALES."' 
						 AND a.UserId = '".$this -> getSession('UserId')."'"; 
			break;
			
			case USER_QUALITY: 
				$sql = " SELECT * FROM tms_agent a WHERE a.user_state=1 
						 AND a.handling_type='".USER_TELESALES."' "; 
			break;
		}
		
		// echo USER_ADMIN;
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['UserId']] = $rows['id']." - ".$rows['full_name'];
		}
		return $datas;
	}
}

$ACT_result = new AssignCampaign();


?>
<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app -> basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app -> basePath();?>js/javaclass.js"></script>
<script type="text/javascript">
	
	$(function(){
		$('#toolbars').extToolbars({
			extUrl   :'../gambar/icon',
			extTitle :[['Show Detail'],['Checked List'],['Active Assign'],['Not Active Assign'],[],[]],
			extMenu  :[['ShowDetail'],['SelectCampaign'],['ActiveAssign'],['NotActiveAssign'],[],[]],
			extIcon  :[['application_form_magnify.png'],['accept.png'],['application_form_edit.png'],['application_form_edit.png'],[],[]],
			extText  :true,
			extInput :true,
			extOption:[{
						render : 4,
						type   : 'label',
						label  : '<span style=\"color:#8383a4;\"> Notes : Y = Active, N = Not Active </span>'
					},{
						render : 5,
						type   : 'label',
						id	   : 'loading_images',
						label  : "<span style=\"color:#8383a4;\">-</span>"
					}]
			});
			
		//$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	});
	
/* ################ **/
 doJava.SettupFile = function(filename)
{
	if( filename.length> 0 )
	{
		doJava.File = filename;
		doJava.Method= "POST";
	}	
}


/* dojava start loading **/
doJava.StartWait = function(htmlLabel)
{
	if( htmlLabel !='' )
	{
		doJava.dom(htmlLabel).innerHTML = "<span><img src=\"../gambar/loading.gif\" height=\"12px;\"></span>";
	}
}


/* dojava start loading **/
doJava.EndWait = function(htmlLabel)
{
	if( htmlLabel !='' )
	{
		doJava.dom(htmlLabel).innerHTML = "<span style=\"color:#8383a4;\">-</span>";
	}
}


/* select checked data **/

var SelectCampaign = function()
{
	doJava.checkedAll('set_user_campaign');
}

/* ActiveAssign **/

var ActiveAssign = function()
{
	var getActiveAssign = doJava.checkedValue('set_user_campaign');
	if( getActiveAssign!='' )
	{
		new (function(){
			doJava.SettupFile("../class/class.assign.campaign.php");
			doJava.Params = {
				action : 'modul_agt_active',
				getActiveAssign : doJava.Base64.encode(getActiveAssign),
				ModeActive : 'Y'
			}
			
			var response = doJava.eJson();
			if( response.result )
			{
				var message = "Success,\n";
				for( var getUser in response.Users)
				{
					message+= response.Users[getUser]+"  => "+response.Campaign[getUser]+"\n";	
				}
				alert(message);	
				
				ShowDetail();
				//doJava.dom('content_xml').innerHTML = response.content;
			}
		})
	}
	else{
		alert('Error, Please select a rows !')
	}	
}

/* NotActiveAssign **/

var NotActiveAssign = function()
{
	var getActiveAssign = doJava.checkedValue('set_user_campaign');
	if( getActiveAssign!='' )
	{
		new (function(){
			doJava.SettupFile("../class/class.assign.campaign.php");
			doJava.Params = {
				action : 'modul_agt_unactive',
				getActiveAssign : doJava.Base64.encode(getActiveAssign),
				ModeActive : 'N'
			}
			
			var response = doJava.eJson();
			if( response.result )
			{
				var message = "Success,\n";
					for( var getUser in response.Users)
					{
						message+= response.Users[getUser]+"  => "+response.Campaign[getUser]+"\n";	
					}
				alert(message);	
				ShowDetail();
			}
		})
	}	
	else{
		alert('Error, Please select a rows !')
	}
}

/* show detail **/

var ShowDetail = function()
{
	var CampaignId = doJava.checkedValue('campaign_find_id');
	var TmrId = doJava.checkedValue('campaign_tmr_id');
	
	if( CampaignId!='' )
	{
		doJava.StartWait("loading_images");
		new( function(){
			doJava.SettupFile("../class/class.assign.campaign.php")
			doJava.Params = {
				action : 'modul_agt_campaign',
				CampaignId : CampaignId,
				TmrId : TmrId
			}
			
			var response = doJava.eJson();
			if( response.result ){
				doJava.dom('content_xml').innerHTML = response.content;
				doJava.EndWait("loading_images");
			}
		});
	}
	else{
		alert('Error, Please Select Campaign !'); 
		return false;
	}	
}
		
</script>
<!-- CE: content js ----->
<!-- CS : style --->
<style>
	.select2 { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;background-image:url('../gambar/input_bg.png')}
	.input_text2 {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-image:url('../gambar/input_bg.png')}
	.text_header2 { text-align:right;color:#746b6a;font-size:12px;}
	.text_header3 { text-align:right;color:#746b6a;font-size:14px;}
	.select_multiple2 { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
</style>

<!-- CE : style --> 
<!-- CS: Content data distribui --->

<fieldset class="corner">
			<legend class="icon-customers">&nbsp;&nbsp;Assignment Campaign </legend>	
			
			<div style="padding:4px;">
				<fieldset class="corner" style="padding-top:10px;">
					<legend> Options </legend>
					<div>
						<table cellpadding="6px">
						<tr>
							<td class="text_header3" valign="top">Campaign Name</td>
							<td><?php echo $jpForm ->jpListcombo('campaign_find_id','Select',$ACT_result->getActiveCampaign(),NULL,1);?></td>		
							<td class="text_header3" valign="top">Campaign Name</td>
							<td><?php echo $jpForm ->jpListcombo('campaign_tmr_id','Select',$ACT_result->getAgentByLogin(),NULL,1);?></td>			
						</tr>
						</table>
					</div>	
				</fieldset>
			
			</div>
				<div id="toolbars"></div>
				<div id="content_xml" class="box-shadow" style="background-color:#ffffff;padding:4px;margin-left:3px;margin-top:10px;"></div>
</fieldset>
