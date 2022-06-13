<?php

	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.nav.table.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../class/class.query.parameter.php");
	require(dirname(__FILE__).'/../sisipan/parameters.php');
	
	
	SetNoCache();
	
	
/** get all status ***/

	function get_value_status()
	{
		$query = new ParameterQuery();
		if( is_object($query))
		{
			return $query -> ImplodeStatus();
		}
	}
	
/** set general query SQL ****/
	
	$sql = " 
		SELECT  
		    b.CustomerId,
			c.CampaignName, 
			c.CampaignId,
			b.CustomerNumber, 
			b.CustomerFirstName, 
			d.CallReasonDesc, 
			b.CustomerUpdatedTs,
			e.AproveName,
			CONCAT(i.id, '-' ,i.full_name) as QA,
			b.QaProsessId
		FROM t_gn_followup a
		INNER JOIN t_gn_customer b ON a.FuCustId=b.CustomerId
		INNER JOIN t_gn_campaign c ON c.CampaignId=b.CampaignId
		LEFT JOIN t_lk_callreason d ON d.CallReasonId=b.CallReasonId
		LEFT JOIN t_lk_aprove_status e ON e.ApproveId = a.FuQAStatus
		LEFT JOIN tms_agent i ON b.QaProsessId = i.UserId
		";
	
	/** not valid page if not serachc **/
	$NavPages -> setPage(10);
	$NavPages -> query($sql);
 	/** set filter **/

    if( $db-> getSession('handling_type') == 5) 
        $filter .= " AND a.FuType ='2' AND a.FuQAStatus ='0' AND a.IsForm = '1'";
    if( $db->getSession('handling_type') == 10)
		$filter .= "AND e.ApproveId = '19' AND a.FuType ='2' AND a.IsForm = '1'";
	if( $db -> havepost('cust_name')) 
		$filter .= " AND a.FuName LIKE '%".$db->escPost('cust_name')."%'"; 
	if( $db -> havepost('cust_numb')) 
		$filter .= " AND b.CustomerNumber LIKE '%".$db->escPost('cust_numb')."%'"; 
	if( $db -> havepost('call_status'))
		$filter .= " AND e.ApproveId  =" .$db -> escPost('call_status');
	if( $db -> havepost('campaign_id'))
		$filter .= " AND c.CampaignId =" .$db->escPost('campaign_id');

	$NavPages -> setWhere($filter);
	$NavPages -> GroupBy('b.CustomerId');
	// echo $NavPages ->query;
    
    /** get Call status list **/
	function getCallStatus(){
		global $db;
		$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a 
				where a.CallReasonStatusFlag=1
				order by a.CallReasonId asc";
				
		$qry = $db->execute($sql,__file__,__line__);
		while( $res = $db->fetchrow($qry) ){
			$datas[$res -> CallReasonId] = $res -> CallReasonDesc; 
		}
	  return "[".json_encode($datas)."]";
	}
?>

	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
  	<script type="text/javascript">
	
	var FuId   = '<?php echo $db -> escPost('FuId'); ?>';
	var FuType = '<?php echo $db -> escPost('FuType'); ?>';
	/* create object **/
	 var Reason = <?php echo getCallStatus(); ?>;
	 var datas  = {}
	 
		extendsJQuery.totalPage    = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord  = <?php echo $NavPages ->getTotRows(); ?>;
		
	
	/* catch of requeet accep browser **/
	
		datas = {
			cust_numb   : '<?php echo $db -> escPost('cust_numb');?>',
			cust_name 	: '<?php echo $db -> escPost('cust_name');?>',
			campaign_id : '<?php echo $db -> escPost('campaign_id');?>',
			order_by 	: '<?php echo $db -> escPost('order_by');?>',
			type	 	: '<?php echo $db -> escPost('type');?>',
			call_status : '<?php echo $db -> escPost('call_status'); ?>'
		}
			
	/* assign navigation filter **/
		var navigation = {
			custnav  : 'dta_qa_tna_nav.php',
			custlist : 'dta_qa_tna_list.php'
		}
	/* assign show list content **/
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
		doJava.File = '../class/class.src.customers_submit.php'; 
		
		var defaultPanel = function(){
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action		 :'tpl_onready', 
					action		 :'tpl_onready', 
					cust_numb    : datas.cust_numb,
					cust_name 	 : datas.cust_name,
					campaign_id  : datas.campaign_id, 
					call_status  : datas.call_status
				}
				doJava.Load('span_top_nav');	
			}
		}

		
	/* function searching customers **/
	
		doJava.onReady(
			evt=function(){ 
			  defaultPanel();
			},
		  evt()
		)
		
	/* function searching customers **/

		var validation_check =  function(CustomerId)
		{
			if( CustomerId )
			{
				doJava.File = '../class/class.src.qualitycontrol.php'; 
				doJava.Params = {
					action:'validation_check',
					CustomerId : CustomerId
				}	
				
				return doJava.eJson();	
			} 
		} 
	
	/* function searching customers **/
	
		var searchCustomer = function()
		{
			var cust_numb    = doJava.dom('cust_numb').value; 
			var cust_name 	 = doJava.dom('cust_name').value;
			var campaign_id  = doJava.dom('campaign_id').value;
			var call_status  = doJava.dom('call_status').value;
			
				datas = {
					cust_numb   : cust_numb,
					cust_name 	: cust_name,
					campaign_id : campaign_id, 
					call_status : call_status
				}
				
			extendsJQuery.construct(navigation,datas)
		    extendsJQuery.postContent()
		}
	
	/* function approve all customer*/
		var approveAll = function(){
			
			var cust_id  = doJava.checkedValue('chk_cust_call');
			var arr_cust_id = cust_id.split(',');
			if( cust_id!='')
			{
				if (arr_cust_id.length==1){
					doJava.File = '../class/class.src.qualitycontrol.php'; 
					doJava.Method = 'POST'
					doJava.Params = { 
						action		: 'approve_all',
						arr_cust_id	: cust_id
					}
					var error = doJava.Post();
					if (error==1){
						alert('Berhasil!');
						extendsJQuery.construct(navigation,'')
						extendsJQuery.postContentList();
					}else{
						alert('Berhasil!');
					}
				}else{
					alert("Select One Customers !")
					return false;
				}
				
			}
			else
			{
				alert("No Customers Selected !");
				return false;
			}
			
		}
		
	/* function clear searching form **/	
		
		var resetSeacrh = function(){
			if( doJava.destroy() ){
				doJava.init = 
					[
						['cust_numb'], 
						['cust_name'],
						['campaign_id'],
						['call_status'] 
					]
				doJava.setValue('');
				extendsJQuery.construct(navigation,'')
				extendsJQuery.postContent()
			}
		}
  
 /* go to call contact detail customers **/
 
		var showDetail = function()
		{
			var arrCallRows  = doJava.checkedValue('chk_cust_call');
			var _FuId_ = $("input[type='checkbox']:checked").attr('_data_');
			var type   = $("input[type='checkbox']:checked").attr('data');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!='')
				{	
					if( arrCountRows.length == 1 )
					{
						var error_code = validation_check(arrCountRows[0]);
						if( error_code.result==0) 
						{
							doJava.File = 'dta_qa_tna_detail.php';
							doJava.Params = { 
								CustomerId : arrCountRows[0],
								FuId : _FuId_,
								FuType : type
							}
							class_active.NotActive();
							extendsJQuery.Content();
						}
						else{ 
							alert('Sorry , Data in Qa Process. Please select other Customers !');
							return false 
						}
					}
					else{
						alert("Select One Customers !")
						return false;
					}
					
				}else{
					alert("No Customers Selected !");
					return false;
				}	
		}
		 
	
	/* memanggil Jquery plug in */
	
		$(function(){
			//$('.corner').corner();
			//$('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['Show Detail '],['Clear']],
				extMenu  :[['searchCustomer'],['showDetail'],['resetSeacrh']],
				extIcon  :[['zoom.png'],['pencil_go.png'],['cancel.png']],
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
			 
			//$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
			$('#start_date,#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		});
		
		
	</script>
	
	
	
	<!-- start : content -->
	
	<fieldset class="corner">
		<legend class="icon-customers">&nbsp;&nbsp;TNAS Submitted </legend>	
			<div id="span_top_nav"></div>
			<div id="toolbars"></div>
			<div id="customer_panel" class="box-shadow" style="background-color:#FFFFFF;">
				<div class="content_table" ></div>
				<div id="pager"></div>
			</div>
	</fieldset>	
	<!-- stop : content -->