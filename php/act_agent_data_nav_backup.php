<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/lib.form.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../class/class.query.parameter.php");
	require(dirname(__FILE__).'/../sisipan/parameters.php');
	
	//echo $db -> getSession('handling_type');
	define('USER_LEVEL',$db -> getSession('handling_type'));
	
	class Distribusi extends mysql
	{
	
		private $CampaignNumber;
		
		function Distribusi($POST)
		{
			parent::__construct();
			if( isset($POST))
			{
				$this -> CampaignNumber = $POST['campaignId'];
				$this -> Parameter = new ParameterQuery();
			}
		}
		
// get call reaon 
	function getCallReason()
	{
		$datas['NULL'] = 'New Data';
		
		$sql = "select a.CallReasonId, a.CallReasonDesc from t_lk_callreason a 
				where a.CallReasonStatusFlag=1 
				AND a.CallReasonId NOT IN('".$this -> Entity-> SaleWithIn()."')";
		$qry = $this -> query($sql);
		
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows[CallReasonId]] = $rows[CallReasonDesc];
		}
		
		return $datas;
	}
		
		
// __entity_campaign	
	
	function __entity_campaign()
		{
			$sql = "select * from t_gn_campaign a where a.CampaignNumber ='".$this -> CampaignNumber."'";
			return $sql;
		}
	
// 	__entity

	function __entity_all_campaign()
		{
			$sql = "SELECT a.CampaignId, a.CampaignName FROM t_gn_campaign a where a.CampaignStatusFlag=1";
			return $sql;
		}
		
	/** get campaign filter nama **/
	
	function getCampaignName()
		{
			$datas = array();
			if( $this ->__entity_all_campaign() )
			{
				$qry = $this-> query($this -> __entity_all_campaign() );
				foreach($qry -> result_assoc() as $rows )
				{
					$datas[$rows['CampaignId']] = $rows['CampaignName'];
				}
			}
			
			return $datas;
		}
		
/** get campaign id **/
	function getCampaignId()
		{
			if( $this -> __entity_campaign() )
			{
				$qry = $this-> execute($this -> __entity_campaign(),__FILE__,__LINE__);
			
				if( $qry && ($row = $this -> fetchassoc($qry)))
				{
					return $row['CampaignNumber'];
				}
			}
		}
		
/** users ****/
	
	function getUserDatas()
		{
			$datas = array(); 
			$filter = "";
			//echo USER_LEVEL;
			switch( $this -> getSession('handling_type') )
			{
			
				case USER_ROOT: 
					$filter = " AND a.handling_type ='".USER_TELESALES."'"; 
					//echo $filter;
				break;
				
				case USER_ADMIN: 
					$filter = " AND a.handling_type ='".USER_TELESALES."'"; 
				break;
				
				case USER_MANAGER: 
					$filter = " AND a.handling_type ='".USER_TELESALES."'"; 
				break;
				
				case USER_SUPERVISOR:
					$filter = " AND a.handling_type ='".USER_TELESALES."' 
							    AND a.spv_id ='".$this -> getSession('UserId')."'"; 
				break;
				
				case USER_QUALITY: 
					$filter = " AND a.handling_type ='".USER_TELESALES."'"; 
				break;
			}
			
			$sql = "select a.UserId, a.id as username, a.full_name FROM tms_agent a where 1=1 ".$filter;
			
			//ECHO $sql;
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows ){
				$datas[$rows['UserId']] = $rows['username']." - ".$rows['full_name'];
			}
			return $datas;
		}
		
/** users level****/		
		
	function getLevelUser()
		{
			$datas = array();
			if( $this -> getSession('handling_type')==9 ) $NOT_IN = '1,5';
			if( $this -> getSession('handling_type')==1 ) $NOT_IN = '1,5';
			if( $this -> getSession('handling_type')==2 ) $NOT_IN = '1,2,5';
			if( $this -> getSession('handling_type')==3 ) $NOT_IN = '1,2,3,5';
			
			$sql   = "select a.id, a.name from tms_agent_profile a where a.id NOT IN($NOT_IN)";
			$qry   = $this -> execute($sql,__FILE__,__LINE__);
			if( $qry ){
				while( $row = $this -> fetchassoc($qry)){
					$datas[$row[id]] = $row[name];
				}
			}	
			return $datas;
		}
// DistribusiType		

	function DistribusiType()
		{
			$datas = array(1=>'Manual',2=>'Automatic');
			if( is_array($datas) )
			{
				return $datas;
			}
		}
		
// JumlahData		

	function JumlahData()
		{
			$sql = $this -> Parameter -> getParameter();
			
			if( $sql )
			{
				return $this -> valueSQL($sql);
			}
		}
//DistribusiMode() 
		
	function DistribusiMode()
		{
			$datas = array(1=>'Urutan',2=>'Acak');
			if( is_array($datas) )
			{
				return $datas;
			}
		}
	}
	
	$Distribusi = new Distribusi($_REQUEST);
	
?>

<!--- CS: content js ----->

<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript">
	
	var totals_data_size = 0;
	var query_data_size = "";
	
	$(function(){
		
		$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Go Back'],['Distribusi'],['Clear']],
				extMenu  :[['GoBack'],['Distribusi'],['clearDistribute']],
				extIcon  :[['server_go.png'],['server_go.png'],['cancel.png']],
				extText  :true,
				extInput :false,
				extOption:[]
			});
			
		$('#MyBars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Show Data'],[],[],[],[],['Show Detail']], 
				extMenu  :[['ShowDatas'],[],[],[],[],['ShowDetail']],
				extIcon  :[['page_find.png'],[],[],[],[],['page_go.png']],
				extText  :true,
				extInput :true,
				extOption:[
					{
						type  	: 'label',
						name  	: 'label_text',
						id 	  	: 'label_text',
						label 	: '<psan style="color:#FF4321;"># Size Data</span>',
						render	: 1
					},{
						type  	: 'label',
						name  	: 'label_asign',
						id 	  	: 'label_asign',
						label 	: '<psan style="color:#FF4321;"># Assign Data</span>',
						render	: 3
					},{
						render : 2,
						type   : 'text',
						id 	   : 'size_data_show',
						name   : 'size_data_show',
						value  : 0,
							
					},{
						render : 4,
						type   : 'text',
						id 	   : 'size_asign_data',
						name   : 'size_asign_data',
						value  : 0,
							
					}
				]
			});
		doJava.dom('size_data_show').disabled = true;
		$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	});
	
/////////////////////////////////////////////////////////
	doJava.dom('size_asign_data').addEventListener('keyup',function(e){
		if( this.value!='')
		{
			if( !isNaN(this.value) ) 
				{
					if( totals_data_size >= parseInt(this.value) )
					{
						doJava.dom('distribusi_assign').value = this.value; 
						return false;
					}
					else
					{
						console.log("Error, Assign Data > Size Data..!");
						doJava.dom('distribusi_assign').value = 0; 
						this.value = 0; 
						return false;
					}
				}
				else{ 
					this.value = 0; 
					return false;
				}
		}	
	});
	
/* ** show data for assignment ****/
	
	var ShowDatas = function()
	{
		var CampaignId = doJava.checkedValue('campaign_find_id');
		var UserDatas	 = doJava.checkedValue('agent_name');
		var CallResult  = doJava.checkedValue('call_result');
			doJava.File = "../class/class.reassigmnet.data.php";
			doJava.Params = 
			{
				action : 'show_data_assignment',
				CampaignId : CampaignId,
				UserDatas : UserDatas,
				CallResult : CallResult 
			}
			var error_data = doJava.eJson();
			//alert(error_data.query_string);
			if(error_data)
			{
				doJava.dom('size_data_show').value = error_data.size_data;
				totals_data_size = error_data.size_data;
				query_data_size = error_data.query_string;
			}	
	}	
	
//////////////////////////////////////////////////////////
	
	var GoBack = function()
	{
		if( confirm('Do you want back to Campaign List ?'))
		{
			doJava.File= 'dta_distribute_nav.php';
			doJava.Params={}
			extendsJQuery.Content();
		}	
	}
	
/* ** ShowDetail **/

	var ShowDetail = function()
	{
		//alert(query_data_size);
		var encodeQuery = doJava.Base64.encode(query_data_size);
		
		window.open('act_detail_reassign.php?query='+encodeQuery);
	}	
		
//////////////////////////////////////////////////////////
	
	var getUserByLevel = function(combo)
	{
		var UserLevel = combo.value;
		var DistribusiType = doJava.dom('distribusi_type').value;
		
		doJava.File = "../class/class.reassigmnet.data.php";
		doJava.Params = {
			action :'show_user_by_level',
			UserLevel : UserLevel,
			DistribusiType : DistribusiType	
		}
		doJava.Load('show_user_by_level');
	}
	
//////////////////////////////////////////////////////////	

	var getUserByType = function(combo)
	{
		var UserLevel = doJava.dom('distribusi_level').value;
		var DistribusiType = combo.value;
		
		doJava.File = "../class/class.reassigmnet.data.php";
		doJava.Params = {
			action :'show_user_by_level',
			UserLevel : UserLevel,
			//CampaignNumber : CampaignNumber,
			DistribusiType : DistribusiType	
		}
		doJava.Load('show_user_by_level');
	}
	
//////////////////////////////////////////////////////////	

	var getSizeByUser = function()
	{
		var UserId = doJava.checkedValue('chk_user_id').split(',');
		if( UserId !='' )
		{
			var SizeDatas  = new Array();
			for( var x in UserId )
			{
				var ByUserSize   = doJava.dom('amount_data_'+UserId[x]).value;
					SizeDatas[x] = {'userid': UserId[x],'size':ByUserSize};
					
			}
			return JSON.stringify(SizeDatas);
		}
		else
			return false;
	}
	
//////////////////////////////////////////////////////////	
	
	var BalanceUserSize = function(opt)
	{
		
		var array_size_datas  = 0;
		var QtyBalance   = 0;
		var array_result_user = doJava.checkedValue('chk_user_id').split(',');
		var AllocData  = doJava.dom('distribusi_assign').value;
			
			if( array_result_user!='' )
			{	
				for( var i in array_result_user )
				{
					array_size_datas = doJava.dom('amount_data_'+array_result_user[i]);
					if( (array_size_datas.value!=='') )
					{
						QtyBalance += parseInt(array_size_datas.value);
					}
				}
				
				if( parseInt(QtyBalance) > parseInt(AllocData) || AllocData=='' )
				{
					opt.value =0;
					opt.style.borderColor ='red';
				}
				else{
					opt.style.borderColor ='blue';
				}
			}	
			else
				opt.value =0;
	}
	
//////////////////////////////////////////////////////////		
	
	var valid_check_size = function()
	{
		var distribusi_jumlah = parseInt(totals_data_size);
		var distribusi_assign = parseInt(doJava.dom('distribusi_assign').value);
		if( distribusi_assign >  distribusi_jumlah )
		{
			doJava.dom('distribusi_assign').value = 0; 
			return false;
		}
		else{
			return true;
		}
	}
	
//////////////////////////////////////////////////////////		
	
	var UncheckSize = function(opts)
	{
		var DistributeType 	= doJava.dom('distribusi_type').value;
		var object_iud = doJava.dom('amount_data_'+opts.value);
		
		if( DistributeType==1 )
		{
			if( !opts.checked){
				object_iud.value=0;
				object_iud.style.borderColor='#dddbbb';
			}
			else{
				object_iud.style.borderColor='red';
			}
		}
	}
	
//////////////////////////////////////////////////////////	

	var Distribusi = function()
	{
		
		var JumlahData 		= doJava.dom('size_data_show').value;
		var UserLevel 		= doJava.dom('distribusi_level').value;
		var DistributeType 	= doJava.dom('distribusi_type').value;
		var DistributeMode 	= doJava.dom('distribusi_mode').value; 
		var AssignData 		= doJava.dom('distribusi_assign').value;
		var UserSelect 		= doJava.checkedValue('chk_user_id').split(',');
		var UserSelectId 	= (DistributeType==1?getSizeByUser():'');
		var QueryDatas      = query_data_size;
		
		if( JumlahData==0){ alert('Data Size Is Zero!'); return false; }
		else if(UserLevel=='') { alert('Please select user level!'); return false;} 
		else if(DistributeType=='') { alert('Please distribute Type!'); return false; }
		else if(DistributeMode=='') { alert('Please distribute Mode!'); return false; }
		else if(AssignData==''|| AssignData==0) { alert('Please input Assign data !'); return false; }
		else if(UserSelect==''){ alert('Please Select User Id By Level !'); return false; }
		else if(!valid_check_size()) { alert('Assign Data > Data Size !'); return false; }
		else
		{
			doJava.File = "../class/class.reassigmnet.data.php";
			doJava.Params =
			{
				action :'act_distribusi_data', 
				JumlahData : JumlahData,
				AssignData  : AssignData, UserLevel : UserLevel, 
				DistribusiType : DistributeType, DistribusiMode : DistributeMode, 
				UserSelectId : UserSelectId, UserSelect:UserSelect,
				QueryDatas : QueryDatas
			}
			
			//console.log(doJava.ArrVal())
			var error_msg = doJava.eJson();
			if( error_msg.result==1 ) {
					alert("Success, Total ("+error_msg.count+") datas,  to ( "+error_msg.agent+" ) agent ");
					doJava.File = 'dta_content_distribusi.php';
					doJava.Params ={
						action :'show_list_agent',
						campaignId : doJava.dom('campaign_number').value
					}
					extendsJQuery.Content();
			}
			else{ alert("Failed!"); return false; }
		}
	}
	
</script>
<!-- CE: content js ----->
<!-- CS : style --->
<style>
	.select2 { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;background-image:url('../gambar/input_bg.png')}
	.input_text2 {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-image:url('../gambar/input_bg.png')}
	.text_header2 { text-align:right;color:#746b6a;font-size:12px;}
	.select_multiple2 { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
</style>

<!-- CE : style --> 
<!-- CS: Content data distribui --->

<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-customers">&nbsp;&nbsp;Get Agent Data </legend>	
		<fieldset  style="margin-top:6px;border:1px solid #ddd;">
			<legend class="icon-menulist">&nbsp;&nbsp;Options </legend>	
			<table cellpadding='7px'>
				<tr>
					<td class="text_header2" valign="top">Campaign Name</td>
					<td><?php echo $jpForm ->jpListcombo('campaign_find_id','Select',$Distribusi->getCampaignName(),NULL,1);?></td>
					<td class="text_header2" valign="top">Agent Name</td>
					<td><?php echo $jpForm ->jpListcombo('agent_name','Select',$Distribusi ->getUserDatas(),NULL,1);?></td>
					<td class="text_header2" valign="top">Call Result</td>
					<td><?php echo $jpForm ->jpListcombo('call_result','Select',$Distribusi ->getCallReason(),NULL,1);?></td>
				</tr>
			</table>
		</fieldset>
		<!-- button data --->
		<div id="MyBars" class="toolbars" style="margin-left:3px;margin-top:5px;"></div>
		<!-- content data reasign -->
		
		<fieldset  style="margin-top:6px;border:1px solid #ddd;">
			<table cellpadding='7px'>
				<tr>
					<td class="text_header2">Assign Data</td>
					<td><?php echo $jpForm ->jpInput('distribusi_assign', 'input_text2','0','onkeyup="valid_check_size();"');?></td>
					<td class="text_header2">Mode</td>
					<td colspan="2"><?php echo $jpForm ->jpCombo('distribusi_mode', 'select2', $Distribusi->DistribusiMode());?></td>	
				</tr>
				<tr>
					<td class="text_header2">Level</td>
					<td><?php echo $jpForm ->jpCombo('distribusi_level', 'select2', $Distribusi -> getLevelUser(),NULL, 'onChange="getUserByLevel(this);"');?></td>
					
				</tr>
				<tr>
					<td class="text_header2">Type</td>
					<td colspan="2"><?php echo $jpForm ->jpCombo('distribusi_type', 'select2', $Distribusi->DistribusiType(),NULL,'onChange="getUserByType(this);"');?></td>
					
				</tr>
			</table>
		</fieldset>
		<div id="toolbars" style="margin-top:20px;margin-left:1px;"></div>
		<fieldset style="margin-top:20px;border:1px solid #dddddd;">
			<legend class="icon-menulist">&nbsp;&nbsp; User By Level </legend>	
			<div id="show_user_by_level" style="background-color:#FFFFFF;"></div>
		</fieldset>
		
</fieldset>	