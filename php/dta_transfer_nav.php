<?php

	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.nav.table.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__).'/../sisipan/parameters.php');
	require(dirname(__FILE__)."/../class/lib.form.php");
	
	
	
	$sql = " select a.CustomerId,
				IF(a.GenderId=1,'M',IF(a.GenderId=2,'F','-')) as gnd
				FROM t_gn_customer a 
				INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId
				LEFT JOIN t_lk_callreason d on a.CallReasonId=d.CallReasonId";
	
	$NavPages -> setPage(15);			 
	$NavPages -> query($sql);
	
	
	//print_r($_SESSION);

/* if found filter label **/
	
		if( $db->getSession('handling_type')==1){
				
			$filter  = " AND b.AssignMgr is not null
						 AND b.AssignSpv is not null
						 AND b.AssignSelerId is not null
						 AND (d.CallReasonId NOT IN(".getShowFollowup().") OR d.CallReasonId is null)
						 AND b.AssignBlock=0 
						 AND c.CampaignStatusFlag=1 ";
		}		 
		else if( $db->getSession('handling_type')==2){
		
			$filter  = " AND b.AssignAdmin is not null
						 AND b.AssignMgr='".$db->getSession('UserId')."'
						 AND (d.CallReasonId NOT IN(".getShowFollowup().") OR d.CallReasonId is null)
						 AND b.AssignSpv is not null
						 AND b.AssignSelerId is not null 
						 AND b.AssignBlock=0 
						 AND c.CampaignStatusFlag=1";
		}
		else if( $db->getSession('handling_type')==3){
		
			$filter  = " AND b.AssignAdmin is not null
						 AND b.AssignMgr is not null
						 AND b.AssignSpv ='".$db->getSession('UserId')."'
						 AND (d.CallReasonId NOT IN(".getShowFollowup().") OR d.CallReasonId is null)
						 AND b.AssignSelerId is not null 
						 AND b.AssignBlock=0  
						 AND c.CampaignStatusFlag=1 ";
		}
			
		if( $db->havepost('campaignId') ):
			$filter.=" AND a.CampaignId='".$db->escPost('campaignId')."' ";
		endif;
		
		if( $db->havepost('filteruser') ):
			$filter.=" AND b.AssignSelerId='".$db->escPost('filteruser')."' ";
		endif;
		
		if( $db->havepost('callresult') ){
			if ($db->escPost('callresult') != "new")
				$filter.=" AND a.CallReasonId ='".$db->escPost('callresult')."' ";
			else
			$filter.=" AND a.CallReasonId is null ";
		}
		
		if( $db->havepost('CustomerId') ):
			$filter.=" AND a.CustomerNumber='".$db->escPost('CustomerId')."' ";
		endif;
		
		if( $db->havepost('CustomerName') ):
			$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('CustomerName')."%' ";
		endif;
				
		if( $db->havepost('city') ):
			$filter.=" AND a.CustomerCity LIKE '%".$db->escPost('city')."%' ";
		endif;
		
		if( $db->havepost('age') ):
			$filter.=" AND DATE_FORMAT(FROM_DAYS(DATEDIFF(CURRENT_DATE,a.CustomerDOB)),'%y')  ='".$db->escPost('age')."' ";
		endif;
			
		if( $db->havepost('gnd') ):
			$filter.=" AND GenderId  ='".$db->escPost('gnd')."' ";
		endif;
		
	$NavPages -> setWhere($filter);
	
	//echo $NavPages -> query;
/** get List Campaign by handling **/
	
	function getShowFollowup()
	{
		global $db;
		$sql = "select a.CallReasonId from t_lk_callreason a where a.BucketFollowupShow =0 ";
		$qry = $db -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
			}
		}
		return implode(',',array_keys($datas));
	}
	
	function ListCmp(){
		global $db;
		
		if( $db->getSession('handling_type')==1 || 
			$db->getSession('handling_type')==2 || 
			$db->getSession('handling_type')==3)
		{
			$sql = "SELECT a.CampaignId, a.CampaignNumber,a.CampaignName FROM t_gn_campaign a 
					WHERE a.CampaignStatusFlag=1
					and date(if(a.CampaignEndDate is null, a.CampaignExtendedDate,a.CampaignEndDate)) >= date(now()) ";
				
		}		
		
		$qry = 	$db->execute($sql,__FILE__,__LINE__);
		while( $row = $db->fetchrow($qry) ){
			$datas[$row->CampaignId] = $row->CampaignNumber ." - ".$row->CampaignName; 
		}
		return $datas;
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
			$datas[$row->UserId] = $row->id; 
		}
		
		return $datas;
	}
	function gender(){
		global $db;
		
		$sql = "select * from t_lk_gender ";
		$qry = $db->execute($sql,__FILE__,__LINE__);
		
		while( $row = $db->fetchrow($qry) ){
			$datas[$row->GenderId] = $row->Gender; 
		}
		
		return $datas;
	}
		
	function city(){
		global $db;
		
		$sql = "select * from t_lk_branch ";
		$qry = $db->execute($sql,__FILE__,__LINE__);
		
		while( $row = $db->fetchrow($qry) ){
			$datas[$row->BranchName] = $row->BranchName; 
		}
		
		return $datas;
	}
	/** get List agent by handling type **/	
	
	function CallResult(){
		global $db;
		
		$datas['new'] = 'New Data';
		$sql = "select a.CallReasonId, concat(a.CallReasonCode,' - ',a.CallReasonDesc) as ReasonValue 
				from t_lk_callreason a where a.CallReasonStatusFlag=1
				and  a.CallReasonId NOT IN(".getShowFollowup().")";

		$qry = $db->execute($sql,__FILE__,__LINE__);
		
		while( $row = $db->fetchrow($qry) ){
			$datas[$row->CallReasonId] = $row->ReasonValue; 
		}
		
		return $datas;
	}
	
	/** get List agent by handling type **/	
	
	function filterListAgent(){
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
			$datas[$row->UserId] = $row->id; 
		}
		
		return $datas;
	}
	
?>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		
		
		$(function(){
			// $('#userGroup').corner();
			// $('#menu_available').corner();
			// $('.toolbars').corner();
			// $('.corner').corner();
			
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Transfer'],['Search'],['Clear']],
				extMenu  :[['AssigData'],['searchCustomer'],['resetSeacrh']],
				extIcon  :[['page_white_go.png'],['zoom.png'], ['cancel.png']],
				extText  :true,
				extInput :false,
				extOption:[{
						render	: 0,
						type	: 'combo',
						id		: 'combo_transfer_type', 	
						name	: 'combo_transfer_type',
						triger	: '',
						header	: 'Transfer Type ',
						store	: [{1:'Level Spv',2:'Level Agent'}],
						value	: '',
						width	: 100
					
				}]
			});
		});
		
	/* render of list table navigation **/	
		
		var datas={
			campaignId : '<?php echo $db->escPost('campaignId');?>',
			filteruser : '<?php echo $db->escPost('filteruser');?>',
			callresult : '<?php echo $db->escPost('callresult');?>',
			CustomerId : '<?php echo $db->escPost('CustomerId');?>',
			CustomerName : '<?php echo $db->escPost('CustomerName');?>',
			city : '<?php echo $db->escPost('city');?>',
			age : '<?php echo $db->escPost('age');?>',
			gender : '<?php echo $db->escPost('gender');?>'
		}
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'dta_transfer_nav.php',
			custlist:'dta_transfer_list.php'
			
		}
		
	/* assign show list content **/
		
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContentList();
	
	
	/* assigment data by selected rows **/
	
		var AssigData = function()
		{
			var CustomerId = doJava.dom('CustomerId').value;
			var CustomerName = doJava.dom('CustomerName').value;
			var campaign_id = doJava.dom('v_list_cmp').value
			var agent_id = doJava.dom('v_list_agent').value	
			var user_id = doJava.dom('v_filter_user').value	
			var cust_id = doJava.checkedValue('chk_cust_data');
			var city = doJava.dom('v_city');
			var age = doJava.dom('age');
			var gnd = doJava.dom('v_gender');
			//var transfer_type = doJava.dom('combo_transfer_type').value;
			
			
				//if( user_id.length < 1 ) { alert('Please Define "Transfer From User"!');  return false; }
				if( agent_id.length < 1 ) { alert('Please Define "Transfer To User"!');   return false; }
				else if( cust_id.length < 1  ) { alert('Please select the customer ID!'); return false; }
				//else if( transfer_type=='' ){ alert('Please select Transfer type !'); return false; }
				else
				{
					if( doJava.destroy() ){
						doJava.File = '../class/class.customer.transfer.php';
						doJava.Params ={
							action :'transfer_by_customer',
							campaign_id : campaign_id, 
							//transfer_type : transfer_type,
							agent_id : agent_id,
							cust_id : cust_id,
							user_id : user_id
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
		//if( opt!=''){
			datas = { 
				
				campaignId:opt,
				filteruser:doJava.dom('v_filter_user').value,
				CustomerId:doJava.dom('CustomerId').value,
				CustomerName:doJava.dom('CustomerName').value,
				callresult:doJava.dom('v_call_result').value
			} 
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
		//	}
	}
	
	var searchCustomer = function(){
			datas = { 
				campaignId : doJava.dom('v_list_cmp').value,
				CustomerId : doJava.dom('CustomerId').value,
				CustomerName : doJava.dom('CustomerName').value,
				filteruser : doJava.dom('v_filter_user').value,
				callresult:doJava.dom('v_call_result').value,
				city:doJava.dom('v_city').value,
				age:doJava.dom('age').value,
				gender:doJava.dom('v_gender').value
			} 
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
			
	}
	
	var CallResult =function(opt){
		datas = { 
				campaignId:doJava.dom('v_list_cmp').value,
				CustomerId:doJava.dom('CustomerId').value,
				CustomerName:doJava.dom('CustomerName').value,
				filteruser:doJava.dom('v_filter_user').value,
				callresult:opt	
			} 
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
	}
	
	var filterByUser = function(opt){
			datas = { 
				filteruser : opt,
				CustomerId : doJava.dom('CustomerId').value,	
				CustomerName : doJava.dom('CustomerName').value,
				campaignId : doJava.dom('v_list_cmp').value,
				callresult : doJava.dom('v_call_result').value,	
				city : doJava.dom('v_city').value,	
				age : doJava.dom('age').value,	
				gender : doJava.dom('v_gender').value	
			} 
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
	}	
	
	var resetSeacrh = function(){
		if( doJava.destroy() ){
				doJava.init = [
								['CustomerId'], ['CustomerName'],
								['v_list_cmp'], ['v_call_result'],['v_city'],['age'],['v_gender']
							  ]
				doJava.setValue('');	
			}
	}
	
</script>
<style>
	.select { border:1px solid #dddddd;font-size:11px;height:22px;background-color:#fffccc;}
	.input_text {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-color:#fffccc;}
	.text_header { text-align:right;color:#746b6a;font-size:12px;}
	.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
	.drop_dwn { border:1px solid #dddddd;font-size:11px;height:22px;background-color:#fffccc;width:225px;}
</style>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Customers Transfer </legend>	
	<div id="topnav" class="box-shadow">
		<table cellpadding="8px">
			<tr>
				<td class="text_header">Customer Age : </td>
				<td><?php $jpForm->jpInput('age','input_text',$_REQUEST['age']);?></td>
				<td class="text_header">Customer Gender : </td>
				<td><?php $jpForm->jpCombo('v_gender','drop_dwn', gender(), $_REQUEST['gender'],NULL);?></td>
				<td class="text_header">Customer City :</td>
				<td><?php $jpForm->jpCombo('v_city','drop_dwn', city(), $_REQUEST['city'], NULL);?></td>
			</tr>
			
			<tr>
				<td class="text_header">Customer Name : </td>
				<td><?php $jpForm->jpInput('CustomerName','input_text',$_REQUEST['CustomerName']);?></td>
				<td class="text_header">Call Result : </td>
				<td><?php $jpForm->jpCombo('v_call_result','drop_dwn', CallResult(), $_REQUEST['callresult'], 'onChange="CallResult(this.value);"');?></td>
				<td class="text_header">Transfer Data From : </td>
				<td><?php $jpForm->jpCombo('v_filter_user','drop_dwn', filterListAgent(), $_REQUEST['filteruser'],'onChange="filterByUser(this.value);"');?></td>
			</tr>
			
			<tr>
				<td class="text_header">CustomerId : </td>
				<td><?php $jpForm->jpInput('CustomerId','input_text',$_REQUEST['CustomerId']);?></td>
				<td class="text_header">CampaignId : </td>
				<td><?php $jpForm->jpCombo('v_list_cmp','drop_dwn', ListCmp(), $_REQUEST['campaignId'],'onChange="CurrentCampaign(this.value);"');?></td>
				<td class="text_header">Transfer Data To :</td>
				<td><?php $jpForm->jpCombo('v_list_agent','drop_dwn', getListAgent(), $_REQUEST['v_list_agent'], NULL);?></td>
			</tr>
		</table>
	</div>
	<div id="toolbars"></div>
	<div class="content_table"></div>
	<div id="pager" ></div>
</fieldset>