<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	SetNoCache();

	   
 /** get Call status list **/
 
	function getCallStatus()
	{
		global $db;
		$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a  where a.CallReasonTerminate=1  
		order by a.CallReasonId asc";
		$qry = $db->execute($sql,__FILE__,__LINE__);
		while( $res = $db->fetchrow($qry))
		{
			$datas[$res -> CallReasonId] = $res -> CallReasonDesc; 
		}
	  
		return "[".json_encode($datas)."]";
	}
	
/** get status closing ****/
	
	function getClsoingStatus()
	{
		global $db;
		$sql = "select a.CallReasonId from t_lk_callreason a where a.CallReasonTerminate =1 ";
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
	
	/** set general query SQL ****/
	
	$sql = "
		SELECT DISTINCT 
			tgf.FuCustId, 
			tgf.*,
			tps.AproveName,
			tgc.CustomerUpdatedTs
		FROM t_gn_followup tgf
	    INNER JOIN t_gn_customer tgc    ON tgf.FuCustId = tgc.CustomerId
	    LEFT JOIN t_lk_aprove_status tps ON tgf.FuQAStatus = tps.ApproveId
 		";
	
	/** not valid page if not search **/
	$NavPages -> setPage(10);
	$NavPages -> query($sql);
	$filter .= "AND tgf.FuType = '3'";    
	
 	/** set filter **/
			
	// if( $db->getSession('handling_type')==4)
	// 	$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 
	if( $db->havepost('cust_name')) 
		$filter.=" AND tgf.FuName LIKE '%".$db->escPost('cust_name')."%'"; 
	
    $NavPages -> setWhere($filter);
    $NavPages -> GroupBy('tgc.CustomerId');
	//$NavPages -> echo_query();
?>

	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
  	<script type="text/javascript">
	
	
	/* create object **/
	 var Reason = <?php echo getCallStatus(); ?>;
	 var datas  = {}
	 
		extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	
	/* catch of requeet accep browser **/
	
		datas = {
			cust_fine_code	: '<?php echo $db -> escPost('cust_fine_code');?>',
			gender	 		: '<?php echo $db -> escPost('gender');?>',
			campaign_id 	: '<?php echo $db -> getSession('V_CMP');?>',
			cust_name 		: '<?php echo $db -> escPost('cust_name');?>',
			call_status		: '<?php echo $db -> escPost('call_status');?>',
			order_by 		: '<?php echo $db -> escPost('order_by');?>',
			type	 		: '<?php echo $db -> escPost('type');?>',
			user_id 		: '<?php echo $db -> escPost('user_id');?>'
			
		}
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'src_mtf_submit_nav.php',
			custlist:'src_mtf_submit_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		doJava.File = '../class/class.src.customer_submit.php' 
		
		var defaultPanel = function(){
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action		:'tpl_onready', 
					cust_name 	: datas.cust_name, 
					gender		: datas.gender,
					card_type	: datas.card_type
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
		
		
	
	/* function searching customers **/
	
		var searchCustomer = function(){
			var cust_name 	 = doJava.dom('cust_name').value;
			var gender	 	 = doJava.dom('gender').value;
			
				datas = {
					cust_name 	: cust_name,
					gender	 	: gender
				}
				
				extendsJQuery.construct(navigation,datas)
				extendsJQuery.postContent()
		}
		
	/* function clear searching form **/	
		
		var resetSeacrh = function(){
			if( doJava.destroy() ){
				doJava.init = [
								['cust_number'], ['cust_name'],
								['cust_dob'], ['home_phone'],
								['office_phone'], ['mobile_phone'],
								['campaign_id'], ['call_result'],
								['user_id']
							  ]
				doJava.setValue('');	
			}
		}
 /* go to call contact detail customers **/
 
		var gotoCallCustomer = function()
		{
			var arrCallRows  = doJava.checkedValue('chk_cust_call');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!='')
				{	
					if( arrCountRows.length == 1 )
					{
						
						arrCallRows = arrCountRows[0].split('_'); 
						 
						if( (arrCallRows.length==4))
						{
							class_active.NotActive();
							CustomerId = arrCallRows[0] // CustomerId
							CampaignId = arrCallRows[1]; // campaignid
							CallReasonId = arrCallRows[2]; // callreason
							VerifiedStatus = arrCallRows[3]; // approvalid
							//alert(CustomerId+" -- "+CampaignId+"--"+VerifiedStatus);
							extendsJQuery.verifiedContent(CustomerId, CampaignId, VerifiedStatus)
						}
						else{
							alert('Please Select other status!');
							return false
						}
					}
					else{
						alert("Select One Customers!")
						return false;
					}
					
				}else{
					alert("No Customers Selected!");
					return false;
				}	
		}
		
		var ShowPolicy = function()
		{
			alert("This Feature Isn't Yet Available. Be Patient Please !");
			return false;
		}
	/* memanggil Jquery plug in */
	
		$(function(){
			
			
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['Clear']],
				extMenu  :[['searchCustomer'],['resetSeacrh']],
				extIcon  :[['zoom.png'],['cancel.png']],
				extText  :true,
				extInput :true,
				extOption:[{
						render : 4,
						type   : 'combo',
						header : 'Call Reason ',
						id     : 'v_result_customers', 	
						name   : 'v_result_customers',
						triger : '',
						store  : Reason
					}]
			});
			
			$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		});
		
		
	</script>
	
	
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-customers">&nbsp;&nbsp;MDTF Submitted </legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="customer_panel" class="box-shadow" style="background-color:#FFFFFF;">
					<div class="content_table" ></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	