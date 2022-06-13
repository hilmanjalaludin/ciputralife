<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	require("../class/lib.form.php");

/** move data **/

	function getListCampaign(){
		global $db;
		$sql = " SELECT 
					a.CampaignId, a.CampaignNumber, 
					a.CampaignName, a.CampaignStartDate, 
					a.CampaignEndDate, a.CampaignExtendedDate
				FROM t_gn_campaign a 
				WHERE (IF(( a.CampaignExtendedDate is null OR a.CampaignExtendedDate='0000-00-00 00:00:00'), 
					   date( a.CampaignEndDate)>=date(NOW()),
					   date( a.CampaignExtendedDate) >=date(NOW())))";
							
		$qry = $db -> execute($sql,__FILE__,__LINE__);
		while( $row = $db ->fetcharray($qry)){
			$datas[$row[0]] =  $row[1].' - '.$row[2];
		}
		
		return $datas;
	}
	
/** move SPV **/	

	function getListSpv()
	{
		global $db;
			if( $db ->getSession('handling_type')==1 ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=3 ";
			}
			
			if( $db ->getSession('handling_type')==2  ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id 
						 where b.id=3 AND a.mgr_id='".$_SESSION['mgr_id']."'";
			}
			
			if( $db ->getSession('handling_type')==3 ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id 
						 where b.id=3 AND a.spv_id= '".$_SESSION['UserId']."'";
			}
			
			$qry = $db ->query($sql);
			foreach($qry -> result_array() as $row )
			{
				$datas[$row[0]] =  $row[1].' - '.$row[2];
			}
			
			return $datas;
	}
	
/** get list to mgr **/

	function getListToMgr()
	{
		global $db;
		
		if(  $db ->getSession('handling_type')==1 ){
			$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=2 ";
		}	
		
		if(  $db ->getSession('handling_type')==2){
			$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id 
					 where b.id=2 AND a.mgr_id='".$_SESSION['UserId']."'";
		}	
		
		if( $db ->getSession('handling_type')==3 ){	
			$sql = " select a.UserId, a.id, a.full_name from tms_agent a  left join tms_agent_profile b on a.profile_id=b.id 
					where b.id=2 AND a.mgr_id='".$_SESSION['mgr_id']."'";
		}
		
		$qry = $db -> execute($sql,__FILE__,__LINE__);
		while( $row = $db ->fetcharray($qry)){
			$datas[$row[0]] =  $row[1].' - '.$row[2];
		}
		
		return $datas;
	}	
	
/** get list am **/
	
	function getListMgr()
	{
		global $db;
		
		if(  $db ->getSession('handling_type')==1 ){
			$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=2 ";
		}	
		
		if(  $db ->getSession('handling_type')==2){
			$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id 
					 where b.id=2 AND a.mgr_id='".$_SESSION['UserId']."'";
		}	
		
		if( $db ->getSession('handling_type')==3 ){	
			$sql = " select a.UserId, a.id, a.full_name from tms_agent a  left join tms_agent_profile b on a.profile_id=b.id 
					 where b.id=2 AND a.mgr_id='".$_SESSION['mgr_id']."'";
		}
		
		$qry = $db ->query($sql);
		foreach($qry -> result_array() as $row )
		{
			$datas[$row[0]] =  $row[1].' - '.$row[2];
		}
		
		return $datas;
	}	
	
	
/** move to SPV **/	

	function getListToSpv()
	{
		global $db;
			if( $db ->getSession('handling_type')==1 ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=3 ";
			}
			
			if( $db ->getSession('handling_type')==2  ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id 
						 where b.id=3 AND a.mgr_id='".$_SESSION['mgr_id']."'";
			}
			
			if( $db ->getSession('handling_type')==3 ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id 
						 where b.id=3 AND a.spv_id= '".$_SESSION['UserId']."'";
			}
			
			$qry = $db ->query($sql);
			foreach($qry -> result_array() as $row )
			{
				$datas[$row[0]] =  $row[1].' - '.$row[2];
			}
			
			return $datas;
	}	
?>

<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/sackAjax.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/autocompletes.js"></script>
<script type="text/javascript">
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
	
	$(function()
	{
			// $('#userGroup').corner();
			// $('#menu_available').corner();
			// $('.corner').corner();
			$('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[['Find'],['Move By AM'],['Move By SPV'],['Move By TM'],[],[],[]],
				extMenu :[['Find'],['MoveByAM'],['MoveBySpv'],['MoveByTM'],[],[],[]],
				extIcon :[['find.png'],['user_red.png'],['user_red.png'],['user_gray.png'],[],[],[]],
				extText  :true,
				extInput :true,
				extOption:[{
							render : 4,
							type   : 'label',
							label  : '&nbsp;Record(s)&nbsp;:0',
							id     : 'loading_images',
							name   : 'loading_images'
						},{
							render : 5,
							type   : 'label',
							label  : 'Distribute Data',
							id     : 'size_data',
							name   : 'size_data',
						},{
							render : 6,
							type   : 'text',
							id     : 'alloc_data_size',
							value  : 0,
							width  : 90,
							name   : 'alloc_data_size'	
						}]
			});
	});
	
	
	// doJava.dom('frommgrid_autocomplete').addEventListener("keyup",function(e){
		// if(e.keyCode==13){
			// return e.currentTarget.value	
		// }	
	// });
	
	doJava.dom('size_data').style.color='red';
	doJava.dom('loading_images').style.color='green';
	
	var RenderAjax = function(handle,UserId)
	{	
		doJava.File = "../class/class.movedata.php";
		doJava.Params = {
			action: 'get_data_user',
			handle : handle,
			UserId : UserId,
		}
		
		return doJava.eJson();
	}
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
	
	var AgentBySpv = function(opt)
	{
		doJava.File = "../class/class.movedata.php";
		doJava.Params = {
			action: 'get_agent_byspv',
			SupervisorId : opt.value,
		}
		doJava.dom('dSellerId').innerHTML = doJava.Post();
		var XML = RenderAjax(3,opt.value);
			var options_mgr = doJava.dom('FromMgrId').options;
			var select_mgr  = doJava.dom('FromMgrId');
				for( var i =0; i<options_mgr.length; i++)
				{
					if( select_mgr[i].value ==XML.mgr_id ){
						select_mgr.selectedIndex = i;
					}
				}
		
	}
	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
			
	var CheckListData = function()
	{
		doJava.checkedAll('chk_cust_dist');
		
		var set_list_data = doJava.name('chk_cust_dist');
		var total = 0;
		for(var i= 0; i<set_list_data.length; i++ )
		{
			if( set_list_data[i].checked ) total+=1;
		}
		
		doJava.dom('alloc_data_size').value = total;	
	}

	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
			
	var RandomClick = function()
	{
		var set_list_data = doJava.name('chk_cust_dist');
		var total = 0;
		for(var i =0; i<set_list_data.length; i++ )
		{
			if( set_list_data[i].checked ) total+=1;
			
		}
		
		doJava.dom('alloc_data_size').value = total;	
	}
	
	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
			
	var ToAgentBySpv = function(opt)
	{
		
		doJava.File = "../class/class.movedata.php";
		doJava.Params = {
			action: 'get_toagent_byspv',
			SupervisorId : opt.value,
		}
		doJava.dom('toSellerId').innerHTML = doJava.Post();
		var XML = RenderAjax(3,opt.value);
			var options_mgr = doJava.dom('ToMgrId').options;
			var select_mgr  = doJava.dom('ToMgrId');
				for( var i =0; i<options_mgr.length; i++)
				{
					if( select_mgr[i].value ==XML.mgr_id ){
						select_mgr.selectedIndex = i;
					}
				}
		
	
	}
	

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
		
		
	var Find = function()
	{
		var SellerId = doJava.SelArrVal('SellerId');
		var CampaignId = doJava.SelArrVal('CampaignId');
		var SupervisorId = doJava.dom('FromSupervisorId').value;
		var ManagerId	= doJava.dom('FromMgrId').value;
		
			doJava.dom('loading_images').innerHTML = '<span style="color:red;"><img src="../gambar/loading.gif" height="15"></span>';
			doJava.File = "../class/class.movedata.php";
			doJava.Params = {
				action: 'get_data_null',
				CampaignId: CampaignId,
				ManagerId : ManagerId,
				SupervisorId : SupervisorId,
				SellerId :SellerId
			}
		
		var JsonData = doJava.eJson();
			doJava.dom('QtyData').innerHTML = JsonData.table;
			doJava.dom('loading_images').innerHTML = '<span style="color:green;">&nbsp;Record(s)&nbsp;: '+JsonData.total+'  </span>';
			doJava.dom('alloc_data_size').value = 0;
	}
	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
	
	var MoveByAM = function()
	{
		var CustomerId 		= doJava.checkedValue('chk_cust_dist');
		var ToManagerId 	= doJava.dom('ToMgrId').value;
		var SizeData 		= doJava.dom('alloc_data_size').value;
		
		if(ToManagerId=='' ) { alert('Please select to Manager')}
		else{
			if(confirm('Do you want to retrive?'))
			{	
				doJava.dom('loading_images').innerHTML = '<span style="color:red;"><img src="../gambar/loading.gif" height="15"></span>';
				doJava.File = "../class/class.movedata.php";
				doJava.Method ='POST';
				doJava.Params = {
					action:'move_to_mgr',
					CustomerId : CustomerId,
					ToManagerId : ToManagerId,
					SizeData : SizeData
				}
				
				var result = doJava.eJson();
				var maximal_interval = 10;
				var total_interval =0;	
					doJava.dom('loading_images').innerHTML = '<span style="color:green;">'+result.spv+'&nbsp; : &nbsp;'+result.total+'  </span>';
					
					
					var UltarAuto = setInterval(function(){
						var sisa_interval = parseInt(maximal_interval - total_interval);
							doJava.dom('loading_images').innerHTML = "Please Wait ( "+total_interval+" )...";
							if( sisa_interval <=0 ){ 
								Find(); 
								clearInterval(UltarAuto);	
							}
						total_interval++;
					},1000);
			}	
		}	
	}
	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
	
	var MoveBySpv = function()
	{
		var CustomerId = doJava.checkedValue('chk_cust_dist');
		var ToSupervisorId = doJava.dom('ToSupervisorId').value;
		var SizeData = doJava.dom('alloc_data_size').value;
		
		if(ToSupervisorId=='' ) { alert('Please select to SPV')}
		else{
		 if(confirm('Do you want to retrive?'))
		 {	
			doJava.dom('loading_images').innerHTML = '<span style="color:red;"><img src="../gambar/loading.gif" height="15"></span>';
			doJava.File = "../class/class.movedata.php";
			doJava.Method ='POST';
			doJava.Params = {
				action:'move_to_spv',
				CustomerId : CustomerId,
				ToSupervisorId : ToSupervisorId,
				SizeData : SizeData
			}
			
			var result = doJava.eJson();
			doJava.dom('loading_images').innerHTML = '<span style="color:green;">'+result.spv+'&nbsp; : &nbsp;'+result.total+'  </span>';
			Find();
		  }	
		}	
	}
	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
		
	
	var MoveByTM = function()
	{
		var CustomerId = doJava.checkedValue('chk_cust_dist');
		var ToSupervisorId = doJava.dom('ToSupervisorId').value;
		var ToSellerId = doJava.SelArrVal('ToxSellerId');
		var SizeData = doJava.dom('alloc_data_size').value;
		
		if(ToSupervisorId=='' ) { alert('Please select to SPV')}
		else{
			if(confirm('Do you want to Move ?')){
				doJava.dom('loading_images').innerHTML = '<span style="color:red;"><img src="../gambar/loading.gif" height="15"></span>';
				doJava.File = "../class/class.movedata.php";
				doJava.Method ='POST';
				doJava.Params = {
					action:'move_to_agent',
					CustomerId : CustomerId,
					ToSupervisorId : ToSupervisorId,
					ToSellerId : ToSellerId,
					SizeData : SizeData
				}
				
				var result = doJava.eJson();
				doJava.dom('loading_images').innerHTML = '<span style="color:green;">'+result.spv+'&nbsp; : &nbsp;'+result.total+' - Peragent '+result.peragent+' - to agent '+ result.agent+'</span>';
				Find();
			}	
		}
	}
	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
		
	var AgentByMgr = function(opt)
	{
		doJava.File = "../class/class.movedata.php";
		doJava.Params = {
			action: 'get_fromspv_byam',
			ManagerId : opt.value,
		}
		doJava.dom('dSPVId').innerHTML = doJava.Post();
	}
	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
		
	var ToAgentByMgr = function(opt)
	{
		doJava.File = "../class/class.movedata.php";
		doJava.Params = {
			action: 'get_tospv_byam',
			ManagerId : opt.value,
		}
		doJava.dom('toSPVId').innerHTML = doJava.Post();
	}
	
</script>
<style>
	.select { border:1px solid #dddddd;font-size:11px;background-color:#fffccc;height:22px;width:250px;}
	.input_text {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;
	font-size:11px;height:20px;background-color:#fffccc;}
	.input_autocomplete {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:250px;
	font-size:11px;height:20px;background-color:#fffccc;}	
	.text_header { text-align:right;color:#000;font-size:12px;}
	.select_multiple { border:1px solid #dddddd;height:120px;font-size:11px;background-color:#fffccc;width:200px;}
</style>
</style>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Move New Data </legend>	
	<!-- <input type="text" name="test" id="test" class="input_autocomplete" onkeyup="ajax_showOptions(this,'get_ajax_data',event,'../class/class.movedata.php');">	<br><br>-->
	<!-- <input type="text" name="frommgrid_autocomplete" id="frommgrid_autocomplete" class="input_autocomplete" onkeyup="ajax_showOptions(this,'get_ajax_data',event,'../class/class.movedata.php');">	<br><br>-->
	<div class="box-shadow">
		<table cellpadding="8px;" width="75%" style="margin-top:5px;margin-bottom:5px;" border=0>
			<tr>
				<td class="text_header" rowspan=3 valign="top" nowrap> Campaign ID</td>
				<td valign="top" rowspan=3><?php $jpForm ->jpMultiple('CampaignId', 'select_multiple', getListCampaign());?></td>
				<td class="text_header" valign="top" nowrap>From AM </td>
				<td valign="top"><?php $jpForm -> jpCombo('FromMgrId', 'select', getListMgr(),$_SESSION['mgr_id'],'onChange="AgentByMgr(this);"',($_SESSION['mgr_id']?1:0));?>  </td>
				<td class="text_header" valign="top" nowrap>To AM </td>
				<td valign="top"> <?php $jpForm -> jpCombo('ToMgrId', 'select', getListToMgr(),$_SESSION['mgr_id'],'onChange="ToAgentByMgr(this);"',($_SESSION['mgr_id']?1:0));?>  </td>
			</tr>
			<tr>
				<td class="text_header" valign="top" nowrap>From SPV </td>
				<td valign="top"><div id="dSPVId"><?php $jpForm -> jpCombo('FromSupervisorId', 'select', getListSpv(),$_SESSION['spv_id'],'onChange="AgentBySpv(this);"',0);?></div></td>
				<td class="text_header" valign="top" nowrap>To SPV </td>
				<td valign="top"> <div id="toSPVId"> <?php $jpForm -> jpCombo('ToSupervisorId', 'select', getListToSpv(),$_SESSION['spv_id'],'onChange="ToAgentBySpv(this);"',0);?></div></td>
			</tr>		
			<tr>
				<td class="text_header" valign="top" nowrap> From Agent  </td>
				<td valign="top" ><div id="dSellerId"> <?php $jpForm -> jpCombo('SellerId','select',array());?></div></td>
				
				<td class="text_header" valign="top" nowrap> To Agent  </td>
				<td valign="top" > <div id="toSellerId"><?php $jpForm -> jpCombo('ToxSellerId','select',array());?> </div></td>
			</tr>	
		</table>
	</div>
	
	<div id="toolbars" class="toolbars"></div>
	<div class="content_table" id="QtyData"></div>
</fieldset>