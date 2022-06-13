<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();

/** set general query SQL ****/
	
	$sql = " select 
				a.CustomerId, a.CampaignId, a.CustomerNumber, 
				a.CustomerFirstName, a.CustomerLastName, 
				IF(a.CustomerCity is null,'-',a.CustomerCity) as CustomerCity, 
				a.CustomerUploadedTs, a.CustomerOfficeName, c.CampaignNumber 
			FROM t_gn_customer a INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
			LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId ";
			
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	
	
 /** set filter **/
	
		 
	if( $db ->havepost('campaign_id')) 
		$filter =" AND a.CampaignId='".$db->escPost('campaign_id')."' ";
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' ";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 
	if( $db->havepost('cust_name')) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('cust_number')) 
		$filter.=" AND a.CustomerNumber LIKE '%".$db->escPost('cust_number')."%'"; 
		
	if( $db->havepost('call_result') )
		$filter.=" AND a.CallReasonId =".$db->escPost('call_result');		
		
    $NavPages -> setWhere($filter);
	
	
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
			cust_number : '<?php echo $db->escPost('cust_number');?>',
			cust_name 	: '<?php echo $db->escPost('cust_name');?>',
			cust_dob 	: '<?php echo $db->escPost('cust_dob');?>', 
			home_phone  : '<?php echo $db->escPost('home_phone');?>',
			office_phone: '<?php echo $db->escPost('office_phone');?>',
			mobile_phone: '<?php echo $db->escPost('mobile_phone');?>', 
			campaign_id : '<?php echo $db->escPost('campaign_id');?>', 
			call_result : '<?php echo $db->escPost('call_result');?>', 
			user_id 	: '<?php echo $db->escPost('user_id');?>'
		}
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'dta_detail_nav.php',
			custlist:'dta_detail_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		doJava.File = '../class/class.src.customers.php' 
		
		var defaultPanel = function(){
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action		:'tpl_onready', cust_number : datas.cust_number,
					cust_name 	: datas.cust_name, cust_dob 	: datas.cust_dob, 
					home_phone  : datas.home_phone, office_phone: datas.office_phone,
					mobile_phone: datas.mobile_phone,  campaign_id : datas.campaign_id, 
					call_result : datas.call_result,  user_id 	: datas.user_id
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
					user_id 	: user_id
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
 
		var gotoDetailCustomer = function(){
			var arrCallRows  = doJava.checkedValue('chk_cust_call');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!=''){	
					if( arrCountRows.length == 1 ){
						extendsJQuery.__mainContact = 'dta_detail_data.php';
						arrCallRows = arrCountRows[0].split('_'); 
						extendsJQuery.contactDetail(arrCallRows[0],arrCallRows[1])
					}
					else{
						alert("Select a customer!")
						return false;
					}
					
				}else{
					alert("No customer has been selected!");
					return false;
				}	
		}
		
	var showWindow = function(){
		var arrCallRows  = doJava.checkedValue('chk_cust_call');
		
		var arrCountRows = arrCallRows.split(','); 
			if( arrCallRows!=''){	
				
				
				if( arrCountRows.length == 1 ){
					var xScreen = ($(window).width()-250);
					var yScreen = ($(window).height());
					
					var Customer = arrCountRows[0].split('_');
					doJava.Params = {
						CustomerId:Customer[0]
					}
					
					doJava.winew.winconfig={
							location	: '../coll_mon/coll.mon.index.php?'+doJava.ArrVal(),
							width		: xScreen,
							height		: yScreen,
							windowName	: 'windowName',
							resizable	: false, 
							menubar		: false, 
							scrollbars	: true, 
							status		: false, 
							toolbar		: false
					};
					
					if( !doJava.winew.winHwnd.closed) doJava.winew.winClose();
						doJava.winew.open();
				}
				else{
					alert("Select a customer!")
					return false;
				}	
			}
			else{
					alert("No customer has been selected!");
					return false;
				}		
	}
		
		
	
	/* memanggil Jquery plug in */
	
		$(function(){
			
			// $('.corner').corner();
			// $('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['Customer Detail '],['Scoring'],['Clear']],
				extMenu  :[['searchCustomer'],['gotoDetailCustomer'],['showWindow'],['resetSeacrh']],
				extIcon  :[['zoom.png'],['user_go.png'],['calendar_edit.png'], ['cancel.png']],
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
			<legend class="icon-customers">&nbsp;&nbsp;Detail Data </legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="customer_panel" class="box-shadow">
					<div class="content_table" ></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	