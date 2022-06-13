<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.nav.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

/** move data **/
function getListCampaign()
{
	global $db;
	$sql = " SELECT a.CampaignId, a.CampaignNumber, a.CampaignName, a.CampaignStartDate,  a.CampaignEndDate, a.CampaignExtendedDate
				FROM t_gn_campaign a 
				WHERE (IF(( a.CampaignExtendedDate is null OR a.CampaignExtendedDate='0000-00-00 00:00:00'), 
				a.CampaignEndDate >= date_format(date(NOW()),'%Y-%m-%d 00:00:00'),
				a.CampaignExtendedDate >= date_format(date(NOW()),'%Y-%m-%d 23:23:59')))";
							
		$qry = $db -> execute($sql,__FILE__,__LINE__);
		while( $row = $db ->fetcharray($qry)){
			$datas[$row[0]] =  $row[1].' - '.$row[2];
		}
		
		return $datas;
	}
	
	function getListTM()
	{
		global $db;
		$sql = " select a.UserId, a.id, a.full_name 
				from tms_agent a 
				left join tms_agent_profile b on a.profile_id=b.id 
				where b.id=4 and a.spv_id='".$db->getSession('UserId')."'";
		
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
			if( $db ->getSession('handling_type')==USER_ADMIN ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=3 ";
			}
			
			if( $db ->getSession('handling_type')==USER_MANAGER  ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id 
						 where b.id=3 AND a.mgr_id='".$_SESSION['mgr_id']."'";
			}
			
			if( $db ->getSession('handling_type')==USER_SUPERVISOR ){
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
		
		if(  $db ->getSession('handling_type')==9 ){
			$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=2 ";
		}
		
		
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
		
		if(  $db ->getSession('handling_type')==9 ){
			$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=2 ";
		}
		
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
			if( $db ->getSession('handling_type')==9 ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=3 ";
			}
			
			if( $db ->getSession('handling_type')==8 ){
				$sql = " select a.UserId, a.id, a.full_name from tms_agent a left join tms_agent_profile b on a.profile_id=b.id where b.id=3 ";
			}
		
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

	
// function callreason()

 function callreason()
 {
	global $db;
	$sql = "select a.CallReasonId, a.CallReasonDesc from t_lk_callreason a ";
	$qry = $db -> query($sql);
	foreach($qry -> result_array() as $row )
	{
		$datas[$row[0]] =  $row[1];
	}
	return $datas;
 }	

 function branch()
 {
	global $db;
	$sql = "select b.BranchCode, b.BranchName from t_lk_branch b ";
	$qry = $db -> query($sql);
	foreach($qry -> result_array() as $row )
	{
		$datas[$row[1]] =  $row[1];
	}
	return $datas;
 }	

 function gender()
 {
 	global $db;
	$sql = "select b.GenderId, b.Gender from t_lk_gender b";
	$qry = $db -> query($sql);
	foreach($qry -> result_array() as $row )
	{
		$datas[$row[0]] =  $row[1];
	}
	return $datas;
 }
	
?>

<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<!--<script type="text/javascript" src="<?php //echo $app->basePath();?>js/sackAjax.js"></script>-->
<!--<script type="text/javascript" src="<?php //echo $app->basePath();?>js/autocompletes.js"></script>-->
<script type="text/javascript">
//////////////////////////////////////////////////////////////
	
		var USER_SYSTEM_LEVEL  = '<?php echo $_SESSION['handling_type'];?>';
		var USER_LEVEL_ADMIN   = 1; 
		var USER_LEVEL_ROOT    = 9; 
		var USER_LEVEL_MANAGER = 2;
		var USER_LEVEL_SPV 	   = 3;
		var USER_LEVEL_AGENT   = 4;
		var USER_LEVEL_QUALITY = 5;
		
		switch(parseInt(USER_SYSTEM_LEVEL))
		{
			case (USER_LEVEL_SPV) :
				$(function()
				{
					$('#toolbars2').extToolbars({
						extUrl  :'../gambar/icon',
						extTitle:[['Find'],['AssignToAgent'],[],[],[]],
						extMenu :[['Find'],['MoveByTM'],[],[],[]],
						extIcon :[['find.png'],['user_gray.png'],[],[],[]],
						extText  :true,
						extInput :true,
						extOption:[{
							render : 2,
							type   : 'label',
							label  : '&nbsp;Record(s)&nbsp;:0',
							id     : 'loading_images',
							name   : 'loading_images'
							},{
							render : 3,
							type   : 'label',
							label  : 'Distribute Data',
							id     : 'size_data',
							name   : 'size_data',
							},{
							render : 4,
							type   : 'text',
							id     : 'alloc_data_size',
							value  : 0,
							width  : 90,
							name   : 'alloc_data_size'	
						}]
					});
				});
				doJava.dom('size_data').style.color='blue';
				doJava.dom('loading_images').style.color='green';
			break;
			
			case USER_LEVEL_MANAGER :
				$(function()
				{
						$('#toolbars2').extToolbars({
							extUrl  :'../gambar/icon',
							extTitle:[['Find'],['AssignToSPV'],['AssignToAgent'],[],[],[]],
							extMenu :[['Find'],['MoveBySpv'],['MoveByTM'],[],[],[]],
							extIcon :[['find.png'],['user_red.png'],['user_gray.png'],[],[],[]],
							extText  :true,
							extInput :true,
							extOption:[{
										render : 3,
										type   : 'label',
										label  : '&nbsp;Record(s)&nbsp;:0',
										id     : 'loading_images',
										name   : 'loading_images'
									},{
										render : 4,
										type   : 'label',
										label  : 'Distribute Data',
										id     : 'size_data',
										name   : 'size_data',
									},{
										render : 5,
										type   : 'text',
										id     : 'alloc_data_size',
										value  : 0,
										width  : 90,
										name   : 'alloc_data_size'	
									}]
						});
				});
				doJava.dom('size_data').style.color='blue';
				doJava.dom('loading_images').style.color='green';
			break;
			
			case USER_LEVEL_ADMIN :
				$(function()
				{
					$('#toolbars2').extToolbars({
						extUrl  :'../gambar/icon',
						extTitle:[['Find'],['AssignToAM'],['AssignToSPV'],['AssignToAgent'],[],[],[]],
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
				
			case USER_LEVEL_ROOT :
			$(function()
			{
				$('#toolbars2').extToolbars({
					extUrl  :'../gambar/icon',
					extTitle:[['Find'],['AssignToAM'],['AssignToSPV'],['AssignToAgent'],[],[],[]],
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
				doJava.dom('size_data').style.color='blue';
				doJava.dom('loading_images').style.color='green';
			break;
		}
	
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
		//var SellerId = doJava.checkedValue('SellerId');
		var CampaignId = doJava.checkedValue('CampaignId');
		//var SupervisorId = doJava.dom('FromSupervisorId').value;
		//var ManagerId	= doJava.dom('FromMgrId').value;
		var age = doJava.dom('age').value;
		var city = doJava.dom('city').value;
		var gender = doJava.dom('gender').value;
		if(!CampaignId){alert('Please select CampaignID'); return false;}
			doJava.dom('loading_images').innerHTML = '<span style="color:red;"><img src="../gambar/loading.gif" height="15"></span>';
			doJava.File = "../class/class.movedata.php";
			doJava.Params = {
				action: 'get_data_null',
				CampaignId: CampaignId,
				//ManagerId : ManagerId,
				//SupervisorId : SupervisorId,
				//SellerId :SellerId,
				age : age,
				city : city,
				gender : gender
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
		if(ToManagerId=='' ) { alert('Please select "To Manager"!')}
		else{
			if(confirm('Distribute Data To Manager??'))
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
					},50);
				
			}
			
		}
	}
	
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
	
	var MoveBySpv = function()
	{
		
		var CustomerId = doJava.checkedValue('chk_cust_dist');
		var ToManagerId 	= doJava.dom('ToMgrId').value;
		var ToSupervisorId = doJava.dom('ToSupervisorId').value;
		var SizeData = doJava.dom('alloc_data_size').value;
		if(ToSupervisorId=='' ) { alert('Please select to SPV')}
		else{
		 if(confirm('Distribute Data To SPV?'))
		 {	
			doJava.dom('loading_images').innerHTML = '<span style="color:red;"><img src="../gambar/loading.gif" height="15"></span>';
			doJava.File = "../class/class.movedata.php";
			doJava.Method ='POST';
			doJava.Params = {
				action:'move_to_spv',
				CustomerId : CustomerId,
				ToSupervisorId : ToSupervisorId,
				ToManagerId : ToManagerId,
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
		var ToManagerId 	= doJava.dom('ToMgrId').value;
		var ToSupervisorId = doJava.dom('ToSupervisorId').value;
		var ToSellerId = doJava.checkedValue('ToxSellerId');
		var SizeData = doJava.dom('alloc_data_size').value;
		
		if(ToSupervisorId=='' ) { alert('Please select to SPV')}
		if(ToSellerId=='' ) { alert('Please select to Agent')}
		else{
			if(confirm('Distribute Data To Agent ?')){
				doJava.dom('loading_images').innerHTML = '<span style="color:red;"><img src="../gambar/loading.gif" height="15"></span>';
				doJava.File = "../class/class.movedata.php";
				doJava.Method ='POST';
				doJava.Params = {
					action:'move_to_agent',
					CustomerId : CustomerId,
					ToSupervisorId : ToSupervisorId,
					ToManagerId : ToManagerId,
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
		ToAgentBySpv(opt);
	}
	
</script>
<style>
	.select { border:1px solid #dddddd;font-size:11px;background:url('../gambar/input_bg.png');height:22px;}
	.input_text {background:url('../gambar/input_bg.png');font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:215px;
	font-size:11px;height:20px;background-color:#fffccc;}
	.input_autocomplete {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:250px;
	font-size:11px;height:20px;background-color:#fffccc;}	
	.text_header { text-align:right;color:#000;font-size:12px;}
	.select_multiple { border:1px solid #dddddd;height:10px;font-size:11px;background-color:#fffccc;width:200px;}
	.drop_dwn { border:1px solid #dddddd;font-size:11px;height:22px;background-color:#fffccc;width:225px;}
</style>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Distribution Fresh Data </legend>	
	<!-- <input type="text" name="test" id="test" class="input_autocomplete" onkeyup="ajax_showOptions(this,'get_ajax_data',event,'../class/class.movedata.php');">	<br><br>-->
	<!-- <input type="text" name="frommgrid_autocomplete" id="frommgrid_autocomplete" class="input_autocomplete" onkeyup="ajax_showOptions(this,'get_ajax_data',event,'../class/class.movedata.php');">	<br><br>-->
	<div class="box-shadow">
		<table cellpadding="4px;" width="75%" style="margin-top:5px;margin-bottom:5px;" border=0>
			
			<tr>
				<td class="text_header" valign="top" nowrap>To AM </td>
				<td valign="top"> <?php $db ->DBForm ->jpCombo('ToMgrId', 'drop_dwn', getListToMgr(),$_SESSION['mgr_id'],'onChange="ToAgentByMgr(this);"',($_SESSION['mgr_id']?1:0));?>  </td>
				
				<td class="text_header" valign="top" nowrap>To SPV </td>
				<td valign="top"> <div id="toSPVId"> <?php $db ->DBForm ->jpCombo('ToSupervisorId', 'drop_dwn', getListToSpv(),$_SESSION['spv_id'],'onChange="ToAgentBySpv(this);"',($_SESSION['spv_id']?1:0));?></div></td>
			</tr>
			<tr>
				<td class="text_header"valign="top" nowrap> Campaign ID</td>
				<td valign="top"><?php $db ->DBForm ->jpListCombo('CampaignId', 'select_Campaign', getListCampaign());?></td>
				
				<td class="text_header" valign="top" nowrap> To Agent  </td>
				<td valign="top" id="toSellerId"><?php $db ->DBForm ->jpListCombo('ToxSellerId','select',getListTM());?> </td>
			</tr>
			<tr>
				<td class="text_header" valign="top" nowrap>Cust Age </td>
				<td valign="top"><?php $db ->DBForm ->jpInput('age','drop_dwn',null);?></td>
				<td class="text_header" valign="top" nowrap>Cust Gender</td>
				<td valign="top"><?php $db ->DBForm ->jpCombo('gender', 'drop_dwn', gender());?></td>
				<td class="text_header" valign="top" nowrap>Cust City</td>
				<td valign="top"><?php $db ->DBForm ->jpCombo('city', 'drop_dwn', branch());?></td>
			</tr>	
		</table>
	</div>
	
	<div id="toolbars2" class="toolbars"></div>
	<div class="content_table" id="QtyData"></div>
</fieldset>