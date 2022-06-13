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
	
	$sql = " select 
				a.CustomerId as CustomerId, a.CampaignId, a.CustomerNumber, 
				a.CustomerFirstName, a.CustomerLastName, a.QaProsess, a.QaProsessId, i.id, i.full_name,
				IF(a.CustomerCity is null,'-',a.CustomerCity) as CustomerCity, 
				a.CustomerUploadedTs, a.CustomerOfficeName, c.CampaignNumber ,
				IF(f.AproveName is null,'Request Confirm', f.AproveName) as  AproveName
			FROM t_gn_customer a 
			LEFT JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
			LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
			LEFT JOIN t_gn_policyautogen d on a.CustomerId=d.CustomerId
			LEFT JOIN t_gn_policy e ON d.PolicyNumber=e.PolicyNumber
			LEFT JOIN t_lk_aprove_status f on a.CallReasonQue=f.ApproveId 
			LEFT JOIN tms_agent i ON a.QaProsessId = i.UserId
			LEFT JOIN t_gn_productplan j ON e.ProductPlanId=j.ProductPlanId
			LEFT JOIN t_gn_product k ON j.ProductId=k.ProductId";
	
/** not valid page if not serachc **/

	$NavPages -> setPage(10);
	$NavPages -> query($sql);
	
 /** set filter **/
	
	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null
				 AND a.CallReasonQue IN(7, 2, 13,".$db -> Entity -> VerifiedConfirm().")";
				 //AND b.AssignBlock=0
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' ";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 
	if( $db->havepost('cust_name')) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('cust_number')) 
		$filter.=" AND a.CustomerNumber LIKE '%".$db->escPost('cust_number')."%'"; 
		
	if( $db->havepost('call_result')){ 
		$filter .=" AND a.CallReasonId ='".$db->escPost('call_result')."'"; 
		//$filter .=" AND a.CallReasonId IN(".get_value_status().") ";
	}
	
	if( $db->havepost('start_date') && $db->havepost('end_date') ){
		$filter .= " AND date(a.CustomerUpdatedTs) >= '".$db->formatDateEng($_REQUEST['start_date'])."' 
					 AND date(a.CustomerUpdatedTs) <= '".$db->formatDateEng($_REQUEST['end_date'])."' "; 
	}
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND a.CampaignId =".$db->escPost('campaign_id');

	if( $db->havepost('user_id') )
		$filter.=" AND a.SellerId =".$db->escPost('user_id');
	
	if( $db->havepost('user_spv') )
		$filter.=" AND b.AssignSpv =".$db->escPost('user_spv');
	
	if( $db->havepost('user_am') )
		$filter.=" AND b.AssignMgr =".$db->escPost('user_am');
		
	if( $db->havepost('ProductGroup') ){
		$filter.=" AND k.product_category_id =".$db->escPost('ProductGroup');
	}
	if( $db->havepost('cmp_status') )
	{
		$filter.=" AND c.CampaignStatusFlag =".$db->escPost('cmp_status');
	}
	else
	{
		$filter.=" AND c.CampaignStatusFlag =1";
	}
	
	if(!$db->havepost('call_result')){
		$filter.=" AND a.CallReasonId IN(".get_value_status().") ";
	}
	$NavPages -> setWhere($filter);
	$NavPages -> GroupBy('a.CustomerId');
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
	
	
	/* create object **/
	 var Reason = <?php echo getCallStatus(); ?>;
	 var datas  = {}
	 
		extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	
	/* catch of requeet accep browser **/
	
		datas = {
			//cust_id		: '<?php echo $db -> escPost('CustomerId');?>',
			cust_number : '<?php echo $db -> escPost('cust_number');?>',
			cust_name 	: '<?php echo $db -> escPost('cust_name');?>',
			cust_dob 	: '<?php echo $db -> escPost('cust_dob');?>', 
			home_phone  : '<?php echo $db -> escPost('home_phone');?>',
			office_phone: '<?php echo $db -> escPost('office_phone');?>',
			mobile_phone: '<?php echo $db -> escPost('mobile_phone');?>', 
			campaign_id : '<?php echo $db -> escPost('campaign_id');?>',
			cmp_status : '<?php echo ($db->havepost('cmp_status')?$db -> escPost('cmp_status'):'1');?>',
			call_result : '<?php echo $db -> escPost('call_result');?>', 
			user_id 	: '<?php echo $db -> escPost('user_id');?>',
			user_spv 	: '<?php echo $db -> escPost('user_spv');?>',
			user_am	 	: '<?php echo $db -> escPost('user_am');?>',
			start_date  : '<?php echo $db -> escPost('start_date');?>',
			end_date    : '<?php echo $db -> escPost('end_date');?>',
			ProductGroup: '<?php echo $db -> escPost('ProductGroup');?>',
			order_by 	: '<?php echo $db -> escPost('order_by');?>',
			type	 	: '<?php echo $db -> escPost('type');?>'
		}
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'dta_qc_nav.php',
			custlist:'dta_qc_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		doJava.File = '../class/class.src.qualitycontrol.php'; 
		
		var defaultPanel = function(){
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action		 :'tpl_onready', cust_number : datas.cust_number,
					cust_name 	 : datas.cust_name, cust_dob 	: datas.cust_dob, 
					home_phone   : datas.home_phone, office_phone: datas.office_phone,
					mobile_phone : datas.mobile_phone,  campaign_id : datas.campaign_id, cmp_status : datas.cmp_status, 
					call_result  : datas.call_result,  user_id 	: datas.user_id, 
					start_date   : datas.start_date, user_spv : datas.user_spv, user_am : datas.user_am,
					end_date	 : datas.end_date, ProductGroup :datas.ProductGroup
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
			var start_date   = doJava.dom('start_date').value;
			var end_date     = doJava.dom('end_date').value;
			var ProductGroup = doJava.dom('ProductGroup').value;
			var cust_number  = doJava.dom('cust_number').value; 
			var cust_name 	 = doJava.dom('cust_name').value;
			var cust_dob 	 = doJava.dom('cust_dob').value;
			var home_phone   = doJava.dom('home_phone').value;
			var office_phone = doJava.dom('office_phone').value;
			var mobile_phone = doJava.dom('mobile_phone').value;
			var campaign_id  = doJava.dom('campaign_id').value;
			var cmp_status  = doJava.dom('cmp_status').value;
			var call_result  = doJava.dom('call_result').value;
			var user_id 	 = doJava.dom('user_id').value;
			var user_spv 	 = doJava.dom('user_spv').value;
			var user_am 	 = doJava.dom('user_am').value;
	
			
				datas = {
					cust_number : cust_number,
					cust_name 	: cust_name,
					cust_dob 	: cust_dob, 
					home_phone  : home_phone,
					office_phone: office_phone,
					mobile_phone: mobile_phone, 
					campaign_id : campaign_id, 
					cmp_status : cmp_status,
					call_result : call_result, 
					user_id 	: user_id,
					user_spv 	: user_spv,
					user_am 	: user_am,
					start_date 	: start_date,
					end_date 	: end_date,
					ProductGroup 	: ProductGroup
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
				doJava.init = [
								['cust_number'], ['cust_name'],
								['cust_dob'], ['home_phone'],
								['office_phone'], ['mobile_phone'],
								['campaign_id'], ['call_result'],
								['user_id'],['start_date'],['end_date'],['user_spv'],['user_am'],['ProductGroup']
							  ]
				doJava.setValue('');
				extendsJQuery.construct(navigation,'')
				extendsJQuery.postContent()
			}
		}
  
 /* go to call contact detail customers **/
 
		var showPolicy = function()
		{
			var arrCallRows  = doJava.checkedValue('chk_cust_call');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!='')
				{	
					if( arrCountRows.length == 1 )
					{
						var error_code = validation_check(arrCountRows[0]);
						if( error_code.result==0) 
						{
							doJava.File = 'dta_qc_detail.php';
							doJava.Params = { CustomerId : arrCountRows[0] }
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
				extTitle :[['Search'],['Show Policy '],['Clear']],
				extMenu  :[['searchCustomer'],['showPolicy'],['resetSeacrh']],
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
			<legend class="icon-customers">&nbsp;&nbsp;Approval Interest </legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="customer_panel" class="box-shadow" style="background-color:#FFFFFF;">
					<div class="content_table" ></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	