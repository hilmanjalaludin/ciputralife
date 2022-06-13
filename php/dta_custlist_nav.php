<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	
	$sql = " select a.CustomerId
				FROM t_gn_customer a 
				INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId";
	
	$NavPages -> setPage(15);			 
	$NavPages -> query($sql);
	

/* if found filter label **/
	
		if( $db->getSession('handling_type')==1){
				
			$filter  = " AND b.AssignAdmin='".$db->getSession('UserId')."'
						 AND b.AssignMgr is null
						 AND b.AssignSpv is null
						 AND b.AssignSelerId is null ";
		}		 
		else if( $db->getSession('handling_type')==2){
		
			$filter  = " AND b.AssignAdmin is not null
						 AND b.AssignMgr='".$db->getSession('UserId')."'
						 AND b.AssignSpv is null
						 AND b.AssignSelerId is null ";
		}
		else if( $db->getSession('handling_type')==3){
		
			$filter  = " AND b.AssignAdmin is not null
						 AND b.AssignMgr is not null
						 AND b.AssignSpv ='".$db->getSession('UserId')."'
						 AND b.AssignSelerId is null ";
		}
			
		if( $db->havepost('campaignId') ):
			$filter.=" AND a.CampaignId='".$db->escPost('campaignId')."' ";
		endif;
		
	$NavPages -> setWhere($filter);
	
	
/** get List Campaign by handling **/
	
	function ListCmp(){
		global $db;
		
		if( $db->getSession('handling_type')==1 || 
			$db->getSession('handling_type')==2 || 
			$db->getSession('handling_type')==3)
		{
			$sql = "SELECT a.CampaignId, a.CampaignNumber,a.CampaignName FROM t_gn_campaign a 
					WHERE a.CampaignStatusFlag=1";
				
		}		
		
		$qry = 	$db->execute($sql,__FILE__,__LINE__);
		while( $row = $db->fetchrow($qry) ){
			$datas[$row->CampaignId] = $row->CampaignNumber ." - ".$row->CampaignName; 
		}
		return "[".json_encode($datas)."]";
	}
	
 /** get List agent by handling type **/	
	
	function getListAgent(){
		global $db;
		
			if( $db -> getSession('handling_type')==1 )
				$filter = " AND handling_type=2";
				
			else if( $db -> getSession('handling_type')==2 )
				$filter = " AND handling_type=3 and mgr_id='".$db->getSession('UserId')."'";
				
			else if( $db -> getSession('handling_type')==3  )
				$filter = " AND handling_type=4 and spv_id='".$db->getSession('UserId')."'";
			
		$sql = "select * from tms_agent where 1=1";
		$sql.= $filter;
		$qry = $db->execute($sql,__FILE__,__LINE__);
		
		while( $row = $db->fetchrow($qry) ){
			$datas[$row->UserId] = $row->full_name; 
		}
		
		return "[".json_encode($datas)."]";
	}
	
?>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		
		var sAgentList  = <?php echo getListAgent();?>;
		var sCmpList 	= <?php echo ListCmp();?>;
		
		$(function(){
			$('#userGroup').corner();
			$('#menu_available').corner();
			$('.toolbars').corner();
			$('.corner').corner();
			
			
			$('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[[''],['Assign Data']],
				extMenu :[[''],['AssigData']],
				extIcon :[[''],['accept.png']],
				extText  :true,
				extInput :true,
				extOption:[{
								render	: 0,
								type	: 'combo',
								header	: 'Campaign ID ',
								id		: 'v_list_cmp', 	
								name	: 'v_list_cmp',
								value	: '<?php echo $db->escPost('campaignId');?>',
								width	: 300,
								triger	: 'CurrentCampaign',
								store 	: sCmpList
							},{
								render	: 1,
								type	: 'combo',
								header	: 'Agent List ',
								id		: 'v_list_agent', 	
								name	: 'v_list_agent',
								value	: '',
								width	: 200,
								triger	: '',
								store 	: sAgentList
							}]
			});
		});
		
	/* render of list table navigation **/	
		
		var datas={
			campaignId:	'<?php echo $db->escPost('campaignId');?>'
		}
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'dta_custlist_nav.php',
			custlist:'dta_custlist_list.php'
		}
		
	/* assign show list content **/
		
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContentList();
	
	
	/* assigment data by selected rows **/
	
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
						doJava.File = '../class/class.customer.distribusi.php';
						doJava.Params ={
							action :'distribusi_by_customer',
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
	
	/* get list by filter camapaign id **/
	
	var CurrentCampaign = function(opt){
		if( opt!=''){
			datas = { campaignId:opt } 
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
		}
	}	
</script>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;List Customers </legend>	
	<div id="toolbars" class="toolbars"></div>
	<div class="content_table"></div>
	<div id="pager" ></div>
</fieldset>