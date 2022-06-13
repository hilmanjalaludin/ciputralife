<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
/** get Call status list **/
 
	function getWAEmailStatus()
	{
		global $db;
		$sql = "select a.id from t_lk_wa_email a where a.FuShow =1 ";
		$qry = $db -> query($sql);
		$datas=array();
		if( $qry -> result_num_rows() > 0 )
		{
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[] = $rows['id']; 
			}
		}
		return implode(',',$datas);
	}

	function getWAEmailStatusExclude()
	{
		global $db;
		$sql = "select a.id from t_lk_wa_email a where a.FuShow =0 ";
		$qry = $db -> query($sql);
		$datas=array();
		if( $qry -> result_num_rows() > 0 )
		{
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[] = $rows['id']; 
			}
		}
		return implode(',',$datas);
	}
	
	function getCallStatus()
	{
		global $db;
		$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a 
				where a.CallReasonStatusFlag=1
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
		$sql = "select a.CallReasonId from t_lk_callreason a where a.CallReasonEvent =1 ";
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
	
	
/** set general query SQL ****/

	$sql = "SELECT 
			a.CustomerId, a.CustomerFirstName, a.GenderId, a.CustomerDOB
			FROM t_gn_customer a
			INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
			LEFT JOIN tms_agent c ON b.AssignSelerId = c.UserId
			LEFT JOIN tms_agent g ON b.AssignSpv = g.UserId
			LEFT JOIN tms_agent h ON b.AssignMgr = h.UserId
			LEFT JOIN t_gn_campaign d on a.CampaignId=d.CampaignId 
			LEFT JOIN t_lk_callreason f on a.CallReasonId = f.CallReasonId
			LEFT JOIN t_lk_wa_email i ON a.wa_email_status = i.id
			";
	
/** not valid page if not search **/

	$NavPages -> setPage(15);
	//$NavPages -> IFpage('campaign_id');
	$NavPages -> query($sql);
	
 /** set filter **/
 
	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null
				 AND b.AssignBlock=0 
				 and d.CampaignStatusFlag=1
				 AND a.wa_email_status NOT IN (". getWAEmailStatusExclude(). ")
				 AND (f.CallReasonId NOT IN(".getShowFollowup().") OR f.CallReasonId is null)
				 AND a.IsForm in (0,1) AND a.IsForm !=2";
				 // AND a.CallAgainAttempt<6";
				//AND (f.CallReasonId NOT IN (20,21) OR f.CallReasonId is null)";
				// AND (f.CallReasonId NOT IN(".getClsoingStatus().") OR f.CallReasonId is null)

				 
/** custom filtering data **/
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' ";
	
	if( $db->havepost('user_id') ) 
	{
		if($db->escPost('user_id') == 'new')
		{
			$filter.=" AND b.AssignSelerId is null"; 
		}
		else{
			$filter.=" AND c.UserId = '".$db->escPost('user_id')."'"; 
		}
	}
	
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 
	if( $db->havepost('cust_name')) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
		
	if( $db->havepost('gender')) 
		$filter.=" AND a.GenderId = '".$db->escPost('gender')."'"; 
	
	if( $db->havepost('card_type')) 
		$filter.=" AND c.CardTypeDesc = '".$db->escPost('card_type')."'"; 
	
	if( $db -> havepost('call_status'))
		$filter.=" AND a.CallReasonId LIKE '%".$db->escPost('call_status')."%'"; 
	
	if( $db -> havepost('cust_fine_code'))
		$filter.=" AND a.NumberCIF LIKE '%".$db->escPost('cust_fine_code')."%'";

	if( $db -> havepost('cust_dob'))
		$filter.=" AND a.CustomerDOB LIKE '%".$db->escPost('cust_dob')."%'"; 
		
	if( $db->havepost('city')) 
		$filter.=" AND a.CustomerCity LIKE '%".$db->escPost('city')."%'"; 
	
	if( $db->havepost('campaign_id')) 
	$filter.=" AND d.CampaignId = '".$db->escPost('campaign_id')."'"; 
	
	/*
	if( isset($_SESSION['V_CMP']))
		$filter.=" AND d.CampaignId =".$_SESSION['V_CMP'];		
	*/
    $NavPages -> setWhere($filter);
	 // echo $NavPages -> query;
	
?>

	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
  	<script type="text/javascript">
	
	
	/* create object **/
	 var Reason = <?php echo getCallStatus(); ?>;
	 var kelas	= '../class/class.src.customers.bucket.php'
	 var datas  = {}
	 
		extendsJQuery.totalPage		 = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord 	 = <?php echo $NavPages ->getTotRows(); ?>;
		
	
	/* catch of requeet accep browser **/
	
		datas = 
		{
			cust_name 		: '<?php echo $db -> escPost('cust_name');?>',
			gender	 		: '<?php echo $db -> escPost('gender');?>',
			city	 		: '<?php echo $db -> escPost('city');?>',
			cust_dob 		: '<?php echo $db -> escPost('cust_dob');?>',
			user_id 		: '<?php echo $db -> escPost('user_id');?>',
			//campaign_id 	: '<?php echo $db -> getSession('V_CMP');?>', 
			campaign_id 	: '<?php echo $db->escPost('campaign_id');?>', 
			user_id 		: '<?php echo $db -> escPost('user_id');?>',
			cust_fine_code	: '<?php echo $db -> escPost('cust_fine_code');?>', //datas.cust_fine_code,
			call_status 	: '<?php echo $db -> escPost('call_status');?>', //datas.call_status
			order_by 		: '<?php echo $db -> escPost('order_by');?>',
			type	 		: '<?php echo $db -> escPost('type');?>'
		}
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'src_customer_bucket_nav.php',
			custlist:'src_customer_bucket_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		
		
		var defaultPanel = function()
		{
			doJava.File = kelas
			
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action :'tpl_onready', 
					cust_name : datas.cust_name, 
					gender : datas.gender,
					city : datas.city,
					campaign_id : datas.campaign_id,
					cust_fine_code: datas.cust_fine_code,
					call_status : datas.call_status,
					cust_dob : datas.cust_dob,
					user_id : datas.user_id
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
	
		var searchCustomer = function()
		{
			var cust_name 	 	= doJava.dom('cust_name').value;
			var gender	 	 	= doJava.dom('gender').value;
			var campaign_id  	= doJava.dom('campaign_id').value; 
			var cust_fine_code  = doJava.dom('cust_fine_code').value;
			var call_status   	= doJava.dom('call_status').value;	
			var city		   	= doJava.dom('city').value;	
			var dob				= doJava.dom('cust_dob').value;	
			var user_id			= doJava.dom('user_id').value;	
				datas = {
					cust_name 	: cust_name,
					gender	 	: gender,
					campaign_id : campaign_id,
					cust_fine_code : cust_fine_code,
					call_status : call_status,
					city : city,
					cust_dob : dob,
					user_id : user_id
				}
				
		    extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent()
		}
		
	/* function clear searching form **/	
		
		var resetSeacrh = function()
		{
			if( doJava.destroy() ){
				doJava.init = [['cust_name'],['gender'],['card_type'],['call_status'],['campaign_id'],['dob']]
				doJava.setValue('');	
			}
		}
		
  
		var gotoDetail = function()
		{
			var arrCallRows  = doJava.checkedValue('chk_cust_call');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!='')
				{	
					if( arrCountRows.length == 1 )
					{
						arrCallRows = arrCountRows[0].split('_'); 
						
						if( (arrCallRows[2]!='15') &&  (arrCallRows[2]!='16'))
						{
							class_active.NotActive(); 
							extendsJQuery.showDetail(arrCallRows[0],arrCallRows[1])
						}
						else{
							alert('Please Select other status!');
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
						
						if( (arrCallRows[2]!='16') &&  (arrCallRows[2]!='17'))
						{
							class_active.NotActive(); 
							extendsJQuery.contactDetail(arrCallRows[0],arrCallRows[1],arrCallRows[2])
						}
						else{
							alert('Please Select other status!');
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
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['Go To Call'],['Clear']],
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
			
			$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'yy-mm-dd',readonly:true});
		});
		
		
	</script>
	
	
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-customers">&nbsp;&nbsp;Customer Bucket List </legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="customer_panel" class="box-shadow" style="background-color:#FFFFFF;">
					<div class="content_table" ></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	