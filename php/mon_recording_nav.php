<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();

/** set general query SQL ****/
	
	$sql = " SELECT 
				a.*, d.id AS UserId, d.full_name, b.CustomerNumber, b.CustomerFirstName,
				a.start_time,
				cmp.CampaignName AS cmpnum, rs.CallReasonDesc, b.CampaignId,
				b.CallReasonId, a.anumber
			FROM cc_recording a
				LEFT JOIN t_gn_customer b ON a.assignment_data =b.CustomerId
				LEFT JOIN cc_agent c ON a.agent_id=c.id
				INNER JOIN tms_agent d ON c.userid=d.id
				LEFT JOIN t_gn_campaign cmp ON cmp.campaignid = b.campaignid
				LEFT JOIN t_lk_callreason rs ON b.CallReasonId=rs.CallReasonId ";
			
	
	$filter = " AND b.CustomerNumber is not null ";	
	if( $db ->havepost('cust_number')) 
		$filter.= " and b.CustomerNumber LIKE '%".$db ->escPost('cust_number')."%'";
	
	if( $db ->havepost('cust_name')) 
		$filter.= " and b.CustomerFirstName LIKE '%".$db ->escPost('cust_name')."%'";
	
	if( $db ->havepost('campaign_id')) 
		$filter.= " and b.CampaignId LIKE '%".$db ->escPost('campaign_id')."%'";
	
	if( $db ->havepost('call_result')) 
		$filter.= " and b.CallReasonId ='".$db ->escPost('call_result')."'";
	
	if( $db ->havepost('user_id')) 
		$filter.= " and d.UserId ='".$db ->escPost('user_id')."'";
	
	if( $db ->havepost('destination')) 
		$filter.= " and a.anumber ='".$db ->escPost('destination')."'";
		
	if( $db ->havepost('start_date')){ 
		$filter.= " and a.start_time >='".$db->formatDateEng($db ->escPost('start_date'))." 00:00:00'";	
	}
	else{
		$filter.= " and a.start_time='0000-00-00 00:00:00'";
	}	
	if( $db ->havepost('end_date')) 
		$filter.= " and a.start_time <='".$db->formatDateEng($db ->escPost('end_date'))." 23:59:59'";	
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	
	
 /** set filter **/
	
	//$filter =" AND b.CustomerId not is null "; 
    $NavPages -> setWhere($filter);
	
	//echo $NavPages ->query;
	
	
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
			home_phone  : '<?php echo $db->escPost('home_phone');?>',
			office_phone: '<?php echo $db->escPost('office_phone');?>',
			mobile_phone: '<?php echo $db->escPost('mobile_phone');?>', 
			campaign_id : '<?php echo $db->escPost('campaign_id');?>', 
			call_result : '<?php echo $db->escPost('call_result');?>', 
			user_id 	: '<?php echo $db->escPost('user_id');?>',
			destination : '<?php echo $db->escPost('destination');?>',
			start_date  : '<?php echo $db->escPost('start_date');?>',
			end_date	: '<?php echo $db->escPost('end_date');?>'
		}
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'mon_recording_nav.php',
			custlist:'mon_recording_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		
		var defaultPanel = function(){
			doJava.File = '../class/class.mon.recording.php' 
		
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action		:'tpl_onready', cust_number : datas.cust_number,
					cust_name 	: datas.cust_name, 
					home_phone  : datas.home_phone, office_phone: datas.office_phone,
					mobile_phone: datas.mobile_phone,  campaign_id : datas.campaign_id, 
					call_result : datas.call_result,  user_id 	: datas.user_id,
					start_date  : datas.start_date, end_date	: datas.end_date, destination	: datas.destination
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
			
			var campaign_id  = doJava.dom('campaign_id').value;
			var call_result  = doJava.dom('call_result').value;
			var user_id 	 = doJava.dom('user_id').value;	
			var destination  = doJava.dom('destination').value;	
			var start_date 	 = doJava.dom('start_date').value;	
			var end_date 	 = doJava.dom('end_date').value;	
				doJava.File = '../class/class.mon.recording.php' 
		
				datas = {
					//action		: 'list_record',
					cust_number : cust_number,
					cust_name 	: cust_name,
					campaign_id : campaign_id, 
					call_result : call_result, 
					user_id 	: user_id,
					destination : destination,	
					start_date 	: start_date,	
					end_date 	: end_date
			
				}
				
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent()
		}
		
	/* function clear searching form **/	
		
		var resetSeacrh = function(){
			if( doJava.destroy() ){
				doJava.init = [
								['cust_number'], ['cust_name'],
								['destination'],['start_date'],['end_date'],
								['campaign_id'], ['call_result'],
								['user_id']
							  ]
				doJava.setValue('');	
			}
			extendsJQuery.postContent()
		}
		
 /* go to call contact detail customers **/
 
		var gotoCallCustomer = function(){
			var arrCallRows  = doJava.checkedValue('chk_cust_call');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!=''){	
					if( arrCountRows.length == 1 ){
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
		
	
	/* memanggil Jquery plug in */
	
		$(function(){
			
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['Clear'],['Play'],['Download']],
				//['Download']],
				extMenu  :[['searchCustomer'],['resetSeacrh'],['play'],['downloadx']],
				//['downloadx']],
				extIcon  :[['zoom.png'], ['cancel.png'],['control_play_blue.png'],['disk.png']],
				//['disk.png']],
				extText  :true,
				extInput :false,
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
			
			$('#start_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
			$('#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		});
		
	var play = function(){
		var rec_id = doJava.checkedValue('chk_cust_call');
		// alert(rec_id);
		// return false;
		if( rec_id!=''){
			doJava.File = '../class/class.mon.recording.php' 
			doJava.Params = { action:'quick_time',mode:'play',rec_id:rec_id }
			doJava.Load('play_panel');
		}
		else
			alert('Please select a row!');
	}

	var downloadx = function(){
		var rec_id = doJava.checkedValue('chk_cust_call');
		if( rec_id!=''){
			doJava.File = '../class/class.mon.recording.php' 
			doJava.Params = { 
				action:'download_rec',
				rec_id:rec_id 
			}
			window.open(doJava.File+'?'+doJava.ArrVal());
		}
	}
 	
	
	var stop = function(){
		doJava.File = '../class/class.mon.recording.php' 
		doJava.Params = { action:'quick_time',mode:'stop' }
		doJava.Load('play_panel');
	}
		
		
	</script>
	
	
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-customers">&nbsp;&nbsp;Search Recording </legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="play_panel"></div>
				<div id="recording_panel" class="box-shadow">
				<div class="content_table" ></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	
