<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$NavPages -> setPage(10);	
	
	$sql = "select 
				a.CustomerId, 
				b.CampaignName, 
				a.CustomerFirstName, 
				a.CustomerCity, 
				a.CustomerDOB,
				c.CallReasonDesc,
				d.GenderShortCode,
				f.full_name as tso,
				g.full_name as spv,
				h.full_name as mgr,
				a.CustomerUpdatedTs
			from t_gn_customer a
			left join t_gn_campaign b on a.CampaignId = b.CampaignId
			left join t_lk_callreason c on a.CallReasonId = c.CallReasonId
			left join t_lk_gender d on a.GenderId = d.GenderId
			left join t_gn_assignment e on a.CustomerId = e.CustomerId
			left join tms_agent f on e.AssignSelerId = f.UserId
			left join tms_agent g on e.AssignSpv = g.UserId
			left join tms_agent h on e.AssignMgr = h.UserId";
	
	if($db->getSession('handling_type') == 9 || $db->getSession('handling_type') == 1)
	{
		$filter.="";
		if( $db ->havepost('agent_tms')) 
		{
			$filter.= " and e.AssignMgr = '".$db ->escPost('agent_tms')."'";
		}
	}
	else if($db->getSession('handling_type') == 2)
	{
		$filter.=" and e.AssignMgr = '".$db ->getSession('UserId')."'";
		if( $db ->havepost('agent_tms')) 
		{
			$filter.= " and e.AssignSpv = '".$db ->escPost('agent_tms')."'";
		}
	}
	else if($db->getSession('handling_type') == 3)
	{
		$filter.=" and e.AssignSpv = '".$db ->getSession('UserId')."'";
		if( $db ->havepost('agent_tms')) 
		{
			$filter.= " and e.AssignSelerId = '".$db ->escPost('agent_tms')."'";
		}
	}
	
	if( $db ->havepost('campaign')) 
		$filter.= " and a.CampaignId = '".$db ->escPost('campaign')."'";
		
	if( $db ->havepost('cust_name')) 
		$filter.= " and a.CustomerFirstName LIKE '%".$db ->escPost('cust_name')."%'";
				 
	$NavPages -> query($sql);
	$NavPages -> setWhere($filter);
	$NavPages -> GroupBy('a.CustomerId');
	$NavPages -> Result();
	
?>	
	
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript">
	var datas  	= {}
		 
	extendsJQuery.totalPage   = <?php echo $NavPages ->getTotPages(); ?>;
	extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
	
	datas = 
	{
		agent_tms 	: '<?php echo $db -> escPost('agent_tms');?>',
		cust_name 	: '<?php echo $db -> escPost('cust_name');?>',
		campaign	: '<?php echo $db -> escPost('campaign');?>'
	}
	
	var navigation = {
		custnav:'src_data_nav.php',
		custlist:'src_data_list.php'
	}
	
	extendsJQuery.construct(navigation,datas)
	extendsJQuery.postContentList();
		
	var defaultPanel = function()
	{
		if( doJava.destroy() )
		{
			doJava.File = '../class/class.src.data.php'
			doJava.Method = 'POST',
			doJava.Params = 
			{
				action		:'tpl_onready',
				campaign 	: datas.campaign,
				agent_tms 	: datas.agent_tms,
				cust_name 	: datas.cust_name
			}
			doJava.Load('span_top_nav');	
		}
	}
	
	doJava.onReady(
		evt=function(){ 
		 defaultPanel();
		},
	  evt()
	)
	
/* handler enter ***/	
	// doJava.dom('span_top_nav').addEventListener("keyup",function(e){
		// if(e.keyCode==13)
		// {
			// searchCustomer();
		// }	
	// });
	
/* set render ****************************/
	// var SetUerData = function(userid,username)
	// {
		// doJava.dom('agent_tms').value= username;
		// doJava.dom('agent_tms').focus();
		// doJava.dom('user_list_content').innerHTML='';
	// }
	
/* set render ****************************/	
	// doJava.dom('agent_tms').addEventListener("keyup",function(e)
	// {
		// var keyword = e.currentTarget.value
			// doJava.File = '../class/class.src.data.php';
			// doJava.Params = {
				// action :'get_list_user',
				// keyword : keyword	
			// }
		// var user_list = doJava.eJson();
			// var html = '';
				// html ="<table width=\"90%\" padding=\"0px;\" cellspacing=\"0px\" style=\"border-bottom:1px solid #000;\">";
				
				// for ( var i in user_list.UserId)
				// {
					// var color= (i%2!=0?"#FFFCCC":"#FFFEEE");
						// html +=" <tr bgcolor=\""+color+"\">"+
							   // " <td style=\"border-top:1px solid #DDD000;height:22px;border-right:1px solid #DDD000;border-left:1px solid #DDD000;\">"+
							   // "&nbsp;<a href=\"javascript:void(0);\" onclick=\"SetUerData('"+user_list.UserId[i]+"','"+user_list.Username[i]+"');\" style=\"cursor:pointer;color:blue;text-decoration:none;\">"+user_list.Username[i]+"</a>"+
							   // " </td><tr>";
					
				// }
			// html +="</table>";	
			// doJava.dom('user_list_content').innerHTML=html;
	// });
	
	//onkeyup="get_autocemplete();"
	
	var searchCustomer = function(){
		var campaign  	 = doJava.dom('campaign').value; 
		var agent_tms  	 = doJava.dom('agent_tms').value;
		var cust_name 	 = doJava.dom('cust_name').value;
				
		doJava.File = '../class/class.src.data.php'
		datas = {
			campaign	: campaign,
			agent_tms 	: agent_tms,
			cust_name 	: cust_name
		}
		//alert(campaign);
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContent()
	}
	
	var resetSeacrh = function(){
		if( doJava.destroy() ){
			doJava.init = [
							['campaign'],
							['agent_tms'], 
							['cust_name']
						  ]
			doJava.setValue('');
			searchCustomer();
		}
	}
	
	$(function(){
		
		$('#toolbars').extToolbars({
			extUrl   :'../gambar/icon',
			extTitle :[['Search'],['Go To Detail'],['Clear']],
			extMenu  :[['searchCustomer'],['goDetail'],['resetSeacrh']],
			extIcon  :[['zoom.png'], ['table_go.png'], ['cancel.png']],
			extText  :true,
			extInput :false,
		});
		
		$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	});
	
	var goDetail = function()
	{
		var customer_data_id = doJava.checkedValue('chk_cust_call');
		if( customer_data_id!=''){
			doJava.File = 'src_data_detail.php';
			doJava.Params = { 
				action:'show_data_detail',
				CustomerId : customer_data_id 
			}
			extendsJQuery.Content()
		}
		else
			alert('Please select a row!');
	}
	
	var stop = function(){
		doJava.File = '../class/class.src.data.php'
		doJava.Params = { action:'quick_time',mode:'stop' }
		doJava.Load('play_panel');
	}
	
/* set focus **/
	// new(function(){
		// doJava.dom('cust_name').focus();
	// });
		
		
		
</script>

<fieldset class="corner">
	<legend class="icon-customers">&nbsp;&nbsp;Search Data </legend>	
	
	<div id="span_top_nav"></div>
	<div id="toolbars"></div>
	<div id="recording_panel" class="box-shadow">
		<div class="content_table" ></div>
		<div id="pager"></div>
	</div>
				
</fieldset>	