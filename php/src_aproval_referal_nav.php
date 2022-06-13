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
	
	$sql = " SELECT 
				e.CampaignId, e.CampaignName, a.CustomerId AS CustomerId,
				a.CustomerId AS CustId,a.CustomerFirstName, COUNT(b.ReferalId) AS jml, 
					(SELECT COUNT(x.ReferalId)
						FROM t_gn_referal x
						WHERE 
							x.ReferalCustomerId=CustId 
							AND x.ReferalQAStatus=1) AS approve, 
					(SELECT COUNT(x.ReferalId)
						FROM t_gn_referal x
						WHERE 
							x.ReferalCustomerId=CustId 
							AND x.ReferalQAStatus=0) AS reject, 
				c.init_name AS SellerId, 
				DATE(b.ReferalCreateTs) AS CreateDate, 
				d.init_name AS QAId, 
				DATE(b.ReferalUpdatedTs) AS QAUpdate,
				b.ReferalPhone1,
				b.ReferalPhone2,
				b.ReferalPhone3,
				b.ReferalSellerId
			FROM t_gn_customer a
				RIGHT JOIN t_gn_referal b ON a.CustomerId=b.ReferalCustomerId
				LEFT JOIN tms_agent c ON a.SellerId = c.UserId
				LEFT JOIN tms_agent d ON b.ReferalUpdateQAUid = d.UserId
				LEFT JOIN t_gn_campaign e ON a.CampaignId=e.CampaignId ";
			
		
/** not valid page if not serachc **/

	$NavPages -> setPage(10);
	$NavPages -> query($sql);
	
 /** set filter **/
	
	if( $db->havepost('cust_number')) 
		$filter.=" AND b.ReferalName LIKE '%".$db->escPost('cust_number')."%'";  
		
	if( $db->havepost('user_id')) 
		$filter.=" AND b.ReferalSellerId LIKE '%".$db->escPost('user_id')."%'"; 
	
	if( $db->havepost('home_phone') )
		$filter.=" AND b.ReferalPhone1 =".$db->escPost('home_phone');
	
	if( $db->havepost('office_phone') )
		$filter.=" AND b.ReferalPhone2 =".$db->escPost('office_phone');
		
	if( $db->havepost('mobile_phone') )
		$filter.=" AND b.ReferalPhone3 =".$db->escPost('mobile_phone');
	
	if( $db->havepost('start_date') && $db->havepost('end_date') ){
		$filter .= " AND date(b.ReferalCreateTs) >= '".$db->formatDateEng($_REQUEST['start_date'])."' 
					 AND date(b.ReferalCreateTs) <= '".$db->formatDateEng($_REQUEST['end_date'])."' "; 
	}
	
	if( $db->havepost('call_result') ){
		if($db->escPost('call_result')=='null'){
			$filter.= "AND b.ReferalQAStatus is null";
		}
		else{
			$filter.= "AND b.ReferalQAStatus = ".$db->escPost('call_result');
		}
	}
	
	if( $db->havepost('cust_name')) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND e.CampaignId =".$db->escPost('campaign_id');
	
	$filter.=" group by CustomerId";
	
	$NavPages -> setWhere($filter);
	//echo 	$NavPages -> query;
	
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
	
	function getReferalProses()
	{
		
	}
?>

	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
  	<script type="text/javascript">
	
	
	/* create object **/
	 // var Reason = <?php echo getCallStatus(); ?>;
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
			call_result : '<?php echo $db -> escPost('call_result');?>', 
			user_id 	: '<?php echo $db -> escPost('user_id');?>',
			start_date  : '<?php echo $db -> escPost('start_date');?>',
			end_date    : '<?php echo $db -> escPost('end_date');?>',
			order_by 	: '<?php echo $db -> escPost('order_by');?>',
			type	 	: '<?php echo $db -> escPost('type');?>'
		}
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'src_aproval_referal_nav.php',
			custlist:'src_aproval_referal_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		doJava.File = '../class/class.src.referal.php'; 
		
		var defaultPanel = function(){
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action		 :'tpl_onready', cust_number : datas.cust_number,
					cust_name 	 : datas.cust_name, cust_dob 	: datas.cust_dob, 
					home_phone   : datas.home_phone, office_phone: datas.office_phone,
					mobile_phone : datas.mobile_phone,  campaign_id : datas.campaign_id, 
					call_result  : datas.call_result,  user_id 	: datas.user_id, 
					start_date   : datas.start_date,
					end_date	 : datas.end_date
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
				doJava.File = '../class/class.src.referal.php'; 
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
			var cust_number  = doJava.dom('cust_number').value; 
			var cust_name 	 = doJava.dom('cust_name').value;
			var cust_dob 	 = doJava.dom('cust_dob').value;
			var home_phone   = doJava.dom('home_phone').value;
			var office_phone = doJava.dom('office_phone').value;
			var mobile_phone = doJava.dom('mobile_phone').value;
			var campaign_id  = doJava.dom('campaign_id').value;
			var call_result  = doJava.dom('call_result').value;
			var user_id 	 = doJava.dom('user_id').value;
	
			
				datas = {
					cust_number : cust_number,
					cust_name 	: cust_name,
					cust_dob 	: cust_dob, 
					home_phone  : home_phone,
					office_phone: office_phone,
					mobile_phone: mobile_phone, 
					campaign_id : campaign_id, 
					call_result : call_result, 
					user_id 	: user_id,
					start_date 	: start_date,
					end_date 	: end_date
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
					doJava.File = '../class/class.src.referal.php'; 
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
								['user_id'],['start_date'],['end_date']
							  ]
				doJava.setValue('');	
			}
		}
  
 /* go to detail Referals **/
 
		var showReferal = function()
		{
			var arrCallRows  = doJava.checkedValue('chk_cust_call');
			//alert(arrCallRows)
			//alert(arrCallRows)
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!='')
				{	
					if( arrCountRows.length == 1 )
					{
						var error_code = validation_check(arrCountRows[0]);
						if( error_code.result==0)
						{
							doJava.File = 'dta_referal_detail.php';
							doJava.Params = { CustomerId : arrCountRows[0] }
							extendsJQuery.Content();
						}
						else{ 
							alert('Sorry , Referal in Qa Process. Please select other Referals !');
							return false 
						}
					}
					else{
						alert("Select One Referals !")
						return false;
					}
					
				}else{
					alert("No Referals Selected !");
					return false;
				}	
		}
		
	
	/* memanggil Jquery plug in */
	
		$(function(){
			//$('.corner').corner();
			//$('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['GoTo Detail'],['Clear']],
				extMenu  :[['searchCustomer'],['showReferal'],['resetSeacrh']],
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
						store  : ''
					}]
			});
			
			//$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
			$('#start_date,#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		});
		
		
	</script>
	
	
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-customers">&nbsp;&nbsp;Referal Approval</legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="customer_panel" class="box-shadow" style="background-color:#FFFFFF;">
					<div class="content_table" ></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	