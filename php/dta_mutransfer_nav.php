<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	function returnArray($datas=''){
		$i_a = explode(',',$datas);
		return implode("','",$i_a);
	}
	
	function statusInAgent(){
		global $db;
		$sql = " select a.CallReasonId from t_lk_callreason a 
					left join t_lk_callreasoncategory b
					on a.CallReasonCategoryId=b.CallReasonCategoryId
					where a.CallReasonCategoryId IN (2,5,6)";
		$qry = $db->execute($sql,__FILE__,__LINE__);
		while($row = $db->fetcharray($qry)){
			if( $row[0]!=1){ 
			$datas[] = $row[0];
			}
		}
		$datas = implode(",",$datas);
		return $datas;
	}	
	

/* main of sql list count data **/
		
	$sql = " select a.CustomerId
				FROM t_gn_customer a 
				INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId
				LEFT JOIN t_lk_callreason d on a.CallReasonId=d.CallReasonId ";
	
	$NavPages -> setPage(15);			 
	$NavPages -> query($sql);

	if( $db->getSession('handling_type')==1){
		$filter  = " AND b.AssignMgr is not null
					 AND b.AssignSpv is not null
					 AND b.AssignSelerId is not null
					 AND ( a.CallReasonId NOT IN (".statusInAgent().") OR a.CallReasonId is null)
					 AND b.AssignBlock=0 
					 AND c.CampaignStatusFlag=1 ";
	}	
	else if( $db->getSession('handling_type')==6){
		$filter  = " AND b.AssignMgr is not null
					 AND b.AssignSpv is not null
					 AND b.AssignSelerId is not null
					 AND ( a.CallReasonId NOT IN (".statusInAgent().") OR a.CallReasonId is null)
					 AND b.AssignBlock=0 
					 AND c.CampaignStatusFlag=1 ";
	}		 	
	
	else if( $db->getSession('handling_type')==2){
		$filter  = " AND b.AssignAdmin is not null
					 AND b.AssignMgr='".$db->getSession('UserId')."'
				     AND b.AssignSpv is not null
					 AND b.AssignSelerId is not null 
					 AND ( a.CallReasonId NOT IN (".statusInAgent().") OR a.CallReasonId is null)
					  
					 AND b.AssignBlock=0 
					 AND c.CampaignStatusFlag=1";
	}
	
	else if( $db->getSession('handling_type')==3){
		$filter  = " AND b.AssignAdmin is not null
					 AND b.AssignMgr is not null
					 AND b.AssignSpv ='".$db->getSession('UserId')."'
					 AND b.AssignSelerId is not null 
					AND ( a.CallReasonId NOT IN (".statusInAgent().") OR a.CallReasonId is null)
					 
					 AND b.AssignBlock=0  
					 AND c.CampaignStatusFlag=1 ";
	}
			
		if( $db->havepost('campaign_list_id') ):
			$filter.=" AND a.CampaignId IN('".returnArray($db->escPost('campaign_list_id'))."')";
		endif;
		
		if( $db->havepost('campaign_onagent_id') ):
			if( $db -> getSession('handling_type')==1) $filter.=" AND b.AssignMgr IN('".returnArray($db->escPost('campaign_onagent_id'))."')";
			if( $db -> getSession('handling_type')==6) $filter.=" AND b.AssignMgr IN('".returnArray($db->escPost('campaign_onagent_id'))."')";
			if( $db -> getSession('handling_type')==2) $filter.=" AND b.AssignSpv IN('".returnArray($db->escPost('campaign_onagent_id'))."')";
			if( $db -> getSession('handling_type')==3) $filter.=" AND b.AssignSelerId IN('".returnArray($db->escPost('campaign_onagent_id'))."')";
		endif;
		
		if( $db->havepost('campaign_result_id') ):
			$status = explode(',', $_REQUEST['campaign_result_id']);
			if(in_array(1,$status)){
				$filter.=" AND ( a.CallReasonId IN('".returnArray($db->escPost('campaign_result_id'))."') OR a.CallReasonId is null ) "; 
			}
			else{
				$filter.=" AND a.CallReasonId IN('".returnArray($db->escPost('campaign_result_id'))."')";
			}	
		endif;
		
		$NavPages -> setWhere($filter);
		

?>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		
	var YesOpen     = '<?php echo $db->escPost('opentabs'); ?>';
	var OpenTabs    = (YesOpen?true:false);
		
		$(function(){
			$('#userGroup').corner();
			$('#menu_available').corner();
			$('.toolbars').corner();
			$('.corner').corner();
			
			
			$('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[['Open Filter'],['Search'],['Transfer'],['Close']],
				extMenu :[['Filter'],['Search'],['Transfer'],['Close']],
				extIcon :[['text_align_justify.png'],['zoom.png'],['report_go.png'],['cancel.png']],
				extText  :true,
				extInput :false,
				extOption:[]
			});
		});
		
	
	
		
	/* render of list table navigation **/	
		
	var datas={
			opentabs:'<?php echo $db->escPost('opentabs'); ?>',
			campaign_list_id :'<?php echo $db->escPost('campaign_list_id'); ?>',
			campaign_result_id :'<?php echo $db->escPost('campaign_result_id');?>',
			campaign_onagent_id :'<?php echo $db->escPost('campaign_onagent_id');?>',
			campaign_toagent_id :'<?php echo $db->escPost('campaign_onagent_id');?>'
		}
		
		
		extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
			
	/* assign navigation filter **/
		
	var navigation = {
			custnav:'dta_mutransfer_nav.php',
			custlist:'dta_mutransfer_list.php'
			
		}
		
		
		
	/* assign show list content **/
		
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContentList();
	
	
	/* assigment data by selected rows **/
	
	var Search = function(){
		if( OpenTabs ){
			var campaign_list_id    = doJava.SelArrVal('campaign_list_id');
			var campaign_result_id  = doJava.SelArrVal('campaign_result_id');
			var campaign_onagent_id = doJava.SelArrVal('campaign_onagent_id');
			var campaign_toagent_id = doJava.SelArrVal('campaign_toagent_id');
					
					
					datas = {
						opentabs:(OpenTabs?1:0),
						campaign_list_id:campaign_list_id,
						campaign_result_id:campaign_result_id,
						campaign_onagent_id:campaign_onagent_id,
						campaign_toagent_id:campaign_onagent_id
					}
					
					extendsJQuery.construct(navigation,datas)
					extendsJQuery.postContent();	
				}
				else{ alert('Please Open Filter !'); }	
			
		}
	
	var Transfer = function(){
		var CampaignId    = doJava.SelArrVal('campaign_list_id');
		var ResultId  = doJava.SelArrVal('campaign_result_id');
		var OnAgentId = doJava.SelArrVal('campaign_onagent_id');
		var ToAgentId = doJava.SelArrVal('campaign_toagent_id');
		
		if( OpenTabs ){
			if( (OnAgentId!='') && (CampaignId !='') && (ResultId!='') && (ToAgentId!='') ){
					doJava.File = '../class/class.multiple.transfer.php';
					doJava.Params = { 
						action:'transfer_data', 
						CampaignId : CampaignId,
						ResultId: ResultId,
						OnAgentId: OnAgentId,
						ToAgentId: ToAgentId
					}
				var error = doJava.Post();
					if( error!='') { doJava.showMessage(error); }
					else{
						alert('Failed, Transefer !')
						return;
					}
			}
			else{
				alert('Info, Filter Not Complete!')
				return;
			}
		}
		else{
			alert('Please Open Filter !');
		}	
			
	}
	
	
	var Filter = function(){
			doJava.File = '../class/class.multiple.transfer.php';
			if( OpenTabs ){
				doJava.Params = {
					action:'tpl_header',
					campaign_list_id:datas.campaign_list_id,
					campaign_result_id:datas.campaign_result_id,
					campaign_onagent_id:datas.campaign_onagent_id,
					campaign_toagent_id:datas.campaign_onagent_id
				}
			}
			else{	
				doJava.Params = { action:'tpl_header' }
				OpenTabs = true;	
			}
			doJava.Load('header_content');	
		}
		
	
	var Close = function(){
			doJava.File = '../class/class.multiple.transfer.php';
			doJava.Params = {action:'tpl_clear'}
			OpenTabs = false;	
			doJava.Load('header_content');
		}
		
	var AssigData = function(){
			var campaign_id = doJava.dom('v_list_cmp').value
			var agent_id = doJava.dom('v_list_agent').value	
			var cust_id = doJava.checkedValue('chk_cust_data');
			
				if( campaign_id.length < 1 ) { alert('Please select the campaign!'); }
				else if( agent_id.length < 1 ) { alert('Please select the agent!'); }
				else if( cust_id.length < 1  ) { alert('Please select the customer ID!'); }
				else
				{
					if( doJava.destroy() ){
						doJava.File = '../class/class.customer.transfer.php';
						doJava.Params ={
							action :'transfer_by_customer',
							campaign_id : campaign_id, 
							agent_id : agent_id,
							cust_id : cust_id
						}	
					
					var error = doJava.Post();
					
						if( error==1) {
							alert('Success assigning the customer(s)!');
							extendsJQuery.construct(navigation,datas)
							extendsJQuery.postContent();	
						}
						else{
							alert('Failed assigning the customer(s)!');
							return false;
						}
					}
				}
		}
		
			if( OpenTabs) Filter();
	
</script>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Multiple Transfer </legend>	
	
	<div id="header_content" class="header_content"></div>
	<div id="toolbars" class="toolbars"></div>
	<div class="content_table"></div>
	<div id="pager" ></div>
</fieldset>