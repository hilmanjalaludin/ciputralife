<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/lib.form.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../class/class.query.parameter.php");
	require(dirname(__FILE__).'/../sisipan/parameters.php');
	
	
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
		
		function __entity()
		{
			$sql = "select * from t_gn_campaign a where a.CampaignNumber ='".$this -> CampaignNumber."'";
			return $sql;
		}
		
		
		function getCampaignName()
		{
			if( $this -> __entity() )
			{
				$qry = $this-> execute($this -> __entity(),__FILE__,__LINE__);
				if( $qry && ($row = $this -> fetchassoc($qry) )){
					return $row['CampaignName'];
				}
			}
		}
		
		
		function getCampaignId()
		{
			if( $this -> __entity() )
			{
				$qry = $this-> execute($this -> __entity(),__FILE__,__LINE__);
			
				if( $qry && ($row = $this -> fetchassoc($qry)))
				{
					return $row['CampaignNumber'];
				}
			}
		}
		
		function getLevelUser()
		{
			$datas = array();
			
			if( $this -> getSession('handling_type')==1 ) $NOT_IN = '1,3,4,8,5,9';
			if( $this -> getSession('handling_type')==9 ) $NOT_IN = '1,5,9';
			if( $this -> getSession('handling_type')==8 ) $NOT_IN = '8,5,9,4,3,2';
			if( $this -> getSession('handling_type')==2 ) $NOT_IN = '1,2,4,5,8,9';
			if( $this -> getSession('handling_type')==3 ) $NOT_IN = '1,2,3,5,9,8';
			
			$sql   = "select a.id, a.name from tms_agent_profile a where a.id NOT IN($NOT_IN)";
			$qry   = $this -> execute($sql,__FILE__,__LINE__);
			if( $qry ){
				while( $row = $this -> fetchassoc($qry))
				{
					$datas[$row[id]] = $row[name];
				}
			}	
			return $datas;
		}
		
		function DistribusiType()
		{
			
			$datas = array(1=>'Manual',2=>'Automatic');
			if( is_array($datas) )
			{
				return $datas;
			}
		}
		
		function JumlahData()
		{
			$sql = $this -> Parameter -> getParameter();
			if( $sql )
			{
				return $this -> valueSQL($sql);
			}
		}
		
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
		$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	});
	
//////////////////////////////////////////////////////////
	
	var GoBack = function()
	{
		doJava.File= 'dta_distribute_nav.php';
		doJava.Params={}
		extendsJQuery.Content();
	}
	
//////////////////////////////////////////////////////////
	
	var getUserByLevel = function(combo)
	{
		var UserLevel = combo.value;
		var DistribusiType = doJava.dom('distribusi_type').value;
		var CampaignNumber = doJava.dom('campaign_number').value;
		
		doJava.File = '../class/class.distribusi.php';
		doJava.Params = {
			action :'show_user_by_level',
			UserLevel : UserLevel,
			CampaignNumber : CampaignNumber,
			DistribusiType : DistribusiType	
		}
		doJava.Load('show_user_by_level');
	}
	
//////////////////////////////////////////////////////////	

	var getUserByType = function(combo)
	{
		var UserLevel = doJava.dom('distribusi_level').value;
		var DistribusiType = combo.value;
		var CampaignNumber = doJava.dom('campaign_number').value;
		
		doJava.File = '../class/class.distribusi.php';
		doJava.Params = {
			action :'show_user_by_level',
			UserLevel : UserLevel,
			CampaignNumber : CampaignNumber,
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
		var distribusi_jumlah = parseInt(doJava.dom('distribusi_jumlah').value);
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
		
		var CampaignNumber 	= doJava.dom('campaign_number').value;
		var JumlahData 		= doJava.dom('distribusi_jumlah').value;
		var UserLevel 		= doJava.dom('distribusi_level').value;
		var DistributeType 	= doJava.dom('distribusi_type').value;
		var DistributeMode 	= doJava.dom('distribusi_mode').value; 
		var AssignData 		= doJava.dom('distribusi_assign').value;
		var UserSelect 		= doJava.checkedValue('chk_user_id').split(',');
		var UserSelectId 	= (DistributeType==1?getSizeByUser():'');
		
		if( JumlahData==0){ alert('Data Size Is Zero!'); return false; }
		else if(UserLevel=='') { alert('Please select user level!'); return false;} 
		else if(DistributeType=='') { alert('Please distribute Type!'); return false; }
		else if(DistributeMode=='') { alert('Please distribute Mode!'); return false; }
		else if(AssignData==''|| AssignData==0) { alert('Please input Assign data !'); return false; }
		else if(UserSelect==''){ alert('Please Select User Id By Level !'); return false; }
		else if(!valid_check_size()) { alert('Assign Data > Data Size !'); return false; }
		else
		{
			doJava.File = '../class/class.distribusi.php';
			doJava.Params ={
				action :'act_distribusi_data', 
				CampaignNumber 	: CampaignNumber, JumlahData : JumlahData,
				AssignData 		: AssignData, UserLevel : UserLevel, 
				DistribusiType 	: DistributeType, DistribusiMode : DistributeMode, 
				UserSelectId 	: UserSelectId, UserSelect:UserSelect
			}
			
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
	.select2 { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
	.input_text2 {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-color:#fffccc;}
	.text_header2 { text-align:right;color:#746b6a;font-size:12px;}
	.select_multiple2 { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
</style>

<!-- CE : style --> 
<!-- CS: Content data distribui --->
		
<fieldset class="corner">
	<legend class="icon-customers">&nbsp;&nbsp;Content Distribusi </legend>	
		
		<fieldset  style="margin-top:6px;border:1px solid #ddd;">
			<legend class="icon-menulist">&nbsp;&nbsp;Distribusi Campaign  </legend>	
			<table cellpadding='7px'>
				<tr>
					<td class="text_header2">ID</td>
					<td ><?php echo $jpForm ->jpInput('campaign_number','input_text2',$Distribusi->getCampaignId(),NULL,1);?></td>
					<td class="text_header2">Level</td>
					<td><?php echo $jpForm ->jpCombo('distribusi_level', 'select2', $Distribusi->getLevelUser(),NULL,'onChange="getUserByLevel(this);"');?></td>
					<td class="text_header2">Assign Data</td>
					<td><?php echo $jpForm ->jpInput('distribusi_assign', 'input_text2','0','onkeyup="valid_check_size();"');?></td>
				</tr>
				<tr>
					<td class="text_header2">Name</td>
					<td><?php echo $jpForm ->jpInput('campaign_name','input_text2',$Distribusi->getCampaignName(),NULL,1);?></td>
					<td class="text_header2">Type</td>
					<td colspan="2"><?php echo $jpForm ->jpCombo('distribusi_type', 'select2', $Distribusi->DistribusiType(),NULL,'onChange="getUserByType(this);"');?></td>
				</tr>
				<tr>
					<td class="text_header2">Data Size</td>
					<td><?php echo $jpForm ->jpInput('distribusi_jumlah','input_text2',$Distribusi->JumlahData(),NULL,1);?></td>
					<td class="text_header2">Mode</td>
					<td colspan="2"><?php echo $jpForm ->jpCombo('distribusi_mode', 'select2', $Distribusi->DistribusiMode());?></td>
				</tr>
			</table>
		</fieldset>
		<div id="toolbars" style="margin-top:20px;margin-left:1px;"></div>
		<fieldset style="margin-top:20px;border:1px solid #ddd;">
			<legend class="icon-menulist">&nbsp;&nbsp; User By Level </legend>	
			<div id="show_user_by_level" style="background-color:#FFFFFF;"></div>
		</fieldset>
		
</fieldset>	