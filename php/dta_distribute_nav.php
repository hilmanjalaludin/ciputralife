<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select a.CampaignId from t_gn_campaign a where a.CampaignStatusFlag=1";
	
	$NavPages -> setPage(15);			 
	$NavPages -> query($sql);
   // $NavPages -> setWhere();
	// echo $NavPages ->query;
	/** user group **/
	
	
?>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
  	<script type="text/javascript">
	
	/* create object **/
	
	 var datas={}
			extendsJQuery.showrecord=false;
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'dta_distribute_nav.php',
			custlist:'dta_distribute_list.php'
		}
		
	/* assign show list content **/
	
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		doJava.File = '../class/class.distribusi.php' 
		
		var distributeAmount = function()
		{
			var arr_chk_cmpId = doJava.checkedValue('chk_cmp');
			var vrr_chk_cmpId = arr_chk_cmpId.split(',');
			if( arr_chk_cmpId!='' )
			{
				if( vrr_chk_cmpId.length==1 )
				{
					doJava.File = 'dta_content_distribusi.php';
					doJava.Params ={
						action :'show_list_agent',
						campaignId : arr_chk_cmpId
					}
					extendsJQuery.Content();
				}
				else {
					alert('please select one rows !'); 
					return false;
				}
			}
			else{
				alert('Please select a rows !'); 
				return false;
			}	
			
			
		}
	
		
	/* creete object javaclass **/	
		
		var byGroups = function(){
			
		}
		
	/* creete object javaclass **/	
		
		var argsDisable = function(num,post){
			if(num){ 
				doJava.dom(post).disabled=false;
				doJava.dom(post).style.borderColor="red";
			}
			else{ 
				doJava.dom(post).style.borderColor="#dddddd";
				doJava.dom(post).value='';
				doJava.dom(post).disabled=true; 
			}	
		}
		
		
		
	/* creete object javaclass **/	
		
		var countRows = function (a,b){
			if(isNaN(a)){
				doJava.dom(b).value=0;
			}
			else
				doJava.dom(b).value=a;
		}
		
		var clearDistribute = function(){
				doJava.Params = {action:'ss'}
				doJava.Load('user_panel');
		}
	
	/* get agent data function **/
	
		var getAgentData = function()
		{
			doJava.File = 'act_agent_data_nav.php';
			doJava.Params ={
				action :'show_list_agent'
			}
			extendsJQuery.Content();
		}
		
	/* save assign **/
		var saveAssign = function(totals,campaign_id){
			var assign_true 	= false;
			var assign_total	= 0;
			var assign_list 	= '';
			var get_value		= '';
			var agent_list 		= doJava.checkedValue('chk_user');
			var arr_agent_list  = agent_list.split(',')
			
		if( totals!=0 && totals!=''){
				if( arr_agent_list!=''){
					for(var i in arr_agent_list){
							var get_value = doJava.dom('count_'+arr_agent_list[i]).value
							if( arr_agent_list!='' && get_value.length <1 ){
								assign_true = false;
							}else{
								assign_true = true;
								assign_total += parseInt(get_value); 	
								assign_list   = assign_list+"|"+arr_agent_list[i]+"~"+get_value
							}
					}
					
					if( assign_true )
					{
						if( assign_total <= parseInt(totals) )
						{
							var list_data = assign_list.substring(1,assign_list.length);
								doJava.File   = '../class/class.customer.distribusi.php';
								doJava.Params = {
									action		 : 'save_dist_bymount',
									list_data	 : list_data,
									assign_total : assign_total,
									assign_true  : assign_true,
									campaign_id  : campaign_id
								} 
								
								var error = doJava.Post();
								if( error==1)
								{
									alert("Success assigning the customers..!");
									extendsJQuery.construct(navigation,datas)
									extendsJQuery.postContent();
								}
								else{
									alert("Failed assigning the customers..!");return;
								}
						}
						else{
							alert("Error, Assignment exceeds the total capacity of the data..!");
							return false;
						}
					}
					else
					{
						alert("Error, Uncheck if the input is empty..!");
						return false;
					}
				}
				else
				{
					alert("Please select the user assigment!");
					return;
				}	
		}
		else {
			alert("Error, No data on this campaign..!");
		}		
	} 
		
	/* memanggil Jquery plug in */
	
		$(function(){
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Distribute'],['Get Agent Data'],['Clear']],
				// ,['Get Agent Data']
				extMenu  :[['distributeAmount'],['getAgentData'],['clearDistribute']],
				// ['getAgentData'],
				extIcon  :[['door_in.png'],['group.png'],['cancel.png']],
				// ,['group.png']
				extText  :true,
				extInput :false,
				extOption:[{
							  render:3, type:'text',
							  id:'v_result', name:'v_result',
							  value:'', width:200
							},{
								render:0, type:'text',
								id:'v_result', name:'v_result',
								value:'', width:200
							 }
						   ]
			});
			
			$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		});
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-customers">&nbsp;&nbsp;Assignment Summary Data </legend>	
				<div id="toolbars"></div>
					<table>
						<tr>
						  <td valign="top">
							<div id="customer_panel" class="box-shadow" style="float:left;width:500px;">
								<div class="content_table"></div>
								<div id="pager"></div>
							</div>
						   </td>
						 <td valign="top">	
							<div id="user_panel" class="box-shadow" style="background-color:#fff;margin-top:10px;float:left;width:850px;display:none;"> </div>
						  </td>
						</tr>
					</table>	
				<div id="span_top_nav"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	