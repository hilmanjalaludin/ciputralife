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
				distinct(a.CustomerId) as CustomerId, a.CampaignId, a.CustomerNumber, 
				a.CustomerFirstName, a.CustomerLastName, e.ApoinmentDate,
				IF(a.CustomerCity is null,'-',a.CustomerCity) as CustomerCity, 
				a.CustomerUploadedTs, a.CustomerOfficeName, c.CampaignNumber 
			FROM t_gn_customer a INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
			LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
			left join t_gn_appoinment d on a.CustomerId=d.CustomerId 
			LEFT JOIN t_gn_appoinment e on a.CustomerId=e.CustomerId";
			
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	
 /** set filter **/
	
	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null
				 AND a.CallReasonId IN('".$db -> Entity -> CallBackWithIn()."')
				 AND e.ApoinmentDate > now()";
				 
	
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
	// echo $NavPages -> query;
 /** get Call status list **/
 
	
	$ReasonId = json_encode($db -> Entity -> ReasonId());
?>
	
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
  	<script type="text/javascript">
	
	
	/* create object **/
	 var Reason = [<?php echo $ReasonId; ?>];
	 var datas  = {}
	 
		extendsJQuery.totalPage = <?php echo $NavPages -> getTotPages(); ?>;
		extendsJQuery.totalRecord = <?php echo $NavPages -> getTotRows(); ?>;
		
	
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
			custnav:'src_appoinment_nav.php',
			custlist:'src_appoinment_list.php'
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
 
		var gotoCallCustomer = function(){
			/*var arrCallRows  = doJava.checkedValue('chk_cust_appoinment');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!=''){	
					if( arrCountRows.length == 1 ){
						class_active.NotActive();
						arrCallRows = arrCountRows[0].split('_'); 
						extendsJQuery.contactDetail(arrCallRows[0],arrCallRows[1])
					}
					else{
						alert("Select One Customers !")
						return false;
					}
					
				}else{
					alert("No Customers Selected !");
					return false;
				}*/	
				
			var arrCallRows  = doJava.checkedValue('chk_cust_appoinment');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!=''){	
					if( arrCountRows.length == 1 ){
						arrCallRows = arrCountRows[0].split('_'); 
						//alert(arrCallRows[0]);
						//return false;
						class_active.NotActive();
						CustomerId = arrCallRows[0] // CustomerId
						CampaignId = arrCallRows[1];
						extendsJQuery.contactDetail(CustomerId,CampaignId)
					}
					else{
						alert("Select One Customers !")
						return false;
					}
					
				}else{
					alert("No Customers Selected !");
					return false;
				}
				/*doJava.File = "dta_contact_detail.php";
				doJava.Params = { 
					action:'show_detail_data',
					CustomerId : Customers.CustomerId,
					CampaignId : Customers.CampaignId,
					CallReasonId :	Customers.CallReasonId,
					CustomerName : Customers.CustomerName,
					CustomerAddress : Customers.CustomerAddress
				}
				
				extendsJQuery.Content()*/
		}
		
	
	/* memanggil Jquery plug in */
	
		$(function(){
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['Go to Call '],['Clear']],
				extMenu  :[['searchCustomer'],['gotoCallCustomer'],['resetSeacrh']],
				extIcon  :[['zoom.png'],['telephone_go.png'],['cancel.png']],
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
			<legend class="icon-customers">&nbsp;&nbsp;Appointment Search </legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="customer_panel" class="box-shadow">
					<div class="content_table" style="background-color:#FFFFFF;"></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	