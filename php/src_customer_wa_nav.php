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
	
	$sql = "SELECT 
				a.CustomerId, a.CustomerFirstName,f.CallReasonId, f.CallReasonDesc, a.CustomerUpdatedTs, 
				h.full_name tm, a.CampaignId, 
				IF(a.wa_email_status=0,'-', c.`Desc`) wa_email_status, 
				IF(a.wa_email_status=0,'-', g.full_name) wa_email_qa, 
				IF(a.wa_email_status=0,'-', a.wa_email_updatets) wa_email_ts
		FROM t_gn_customer a
		INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
		LEFT JOIN t_lk_gender e ON a.GenderId=e.GenderId
		LEFT JOIN t_lk_wa_email c ON a.wa_email_status=c.Id
		LEFT JOIN t_gn_campaign d on a.CampaignId=d.CampaignId 
		LEFT JOIN t_lk_callreason f on a.CallReasonId = f.CallReasonId
		left join tms_agent g on a.wa_email_updatebyid=g.UserId 
		left join tms_agent h on a.SellerId=h.UserId ";
	
/** not valid page if not search **/
	$NavPages -> setPage(10);
	$NavPages -> query($sql);
	
 /** set filter **/
	
	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null
				 AND f.CallReasonId IN(".getClsoingStatus().")
				 AND b.AssignBlock=0 
				 and d.CampaignStatusFlag=1";
				 
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' 
				   AND a.CallReasonQue = 12";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 
	if( $db->havepost('cust_name')) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('gender')) 
		$filter.=" AND e.Gender = '".$db->escPost('gender')."'"; 
	
	
	if( $db->havepost('card_type')) 
		$filter.=" AND c.CardTypeDesc = '".$db->escPost('card_type')."'"; 
		
	
	if( isset($_SESSION['V_CMP']))
		$filter.=" AND d.CampaignId =".$_SESSION['V_CMP'];		
		
    $NavPages -> setWhere($filter);
	// $NavPages -> echo_query();
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
			custnav:'src_customer_wa_nav.php',
			custlist:'src_customer_wa_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		doJava.File = '../class/class.src.customer_wa.php' 
		
		var defaultPanel = function(){
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action		:'tpl_onready', 
					//cust_number : datas.cust_number,
					cust_name 	: datas.cust_name, 
					gender		: datas.gender,
					card_type	: datas.card_type
					//cust_dob 	: datas.cust_dob, 
					//home_phone  : datas.home_phone, 
					//office_phone: datas.office_phone,
					//mobile_phone: datas.mobile_phone,  
					//campaign_id : datas.campaign_id, 
					//call_result : datas.call_result,  
					//user_id 	: datas.user_id
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
			//var cust_number  = doJava.dom('cust_number').value; 
			var cust_name 	 = doJava.dom('cust_name').value;
			var gender	 	 = doJava.dom('gender').value;
			// var card_type 	 = doJava.dom('card_type').value;
			//var cust_dob 	 = doJava.dom('cust_dob').value;
			//var home_phone   = doJava.dom('home_phone').value;
			//var office_phone = doJava.dom('office_phone').value;
			//var mobile_phone = doJava.dom('mobile_phone').value;
			//var campaign_id  = doJava.dom('campaign_id').value;
			//var call_result  = doJava.dom('call_result').value;
			//var user_id 	 = doJava.dom('user_id').value;	
			
				datas = {
					//cust_number : cust_number,
					cust_name 	: cust_name,
					gender	 	: gender
					// card_type 	: card_type
					//cust_dob 	: cust_dob, 
					//home_phone  : home_phone,
					//office_phone: office_phone,
					//mobile_phone: mobile_phone, 
					//campaign_id : campaign_id, 
					//call_result : call_result, 
					//user_id 	: user_id
				}
				
		    //if( campaign_id!=''){
			//if( cust_name!=''){
				extendsJQuery.construct(navigation,datas)
				extendsJQuery.postContent()
			//}
			//else{
				//alert('Please Select Campaign!');
			//}
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
			<legend class="icon-customers">&nbsp;&nbsp;Customer WA - Email </legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="customer_panel" class="box-shadow" style="background-color:#FFFFFF;">
					<div class="content_table" ></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	