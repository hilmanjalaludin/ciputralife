<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../sisipan/parameters.php");
	require(dirname(__FILE__)."/../class/lib.form.php");
	
	
	$sql = " SELECT a.* FROM t_gn_tmp_tarik_id a where a.tmp_id='".$_REQUEST['tmp_session_id']."'";
	
	$qry = $db -> query($sql);
	if( $qry -> result_num_rows() > 0 )
	{
		$SessionTmpId = $qry -> result_first_assoc();
	}
		
	/** get user level **/
	
	function getUserLevel()
	{
		global $db;
		
		if( $_SESSION['handling_type']==1 ) $sql = "SELECT a.id, a.name from tms_agent_profile a where a.id IN(2,3,4) order by a.id ASC "; 
		if( $_SESSION['handling_type']==2 ) $sql = "SELECT a.id, a.name from tms_agent_profile a where a.id IN(3,4) order by a.id ASC ";
		if( $_SESSION['handling_type']==3 ) $sql = "SELECT a.id, a.name from tms_agent_profile a where a.id IN(4) order by a.id ASC ";
		
		$qry = $db -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['id']] = $rows['name'];
		}
		
		return $datas;
	}
	
	/** type **************************/ 
	
	function getDistType()
	{
		return array
		(
			1 => 'Automatic',
			2 => 'Manual'
		);
	}
	
	/** type ************************************/ 
	
	function getDistMode()
	{
		return array
		(
			1 => 'Urutan',
			2 => 'Acak'
		);
	}
	
	
?>

<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script type="text/javascript">

$(function(){
	$('#toolbars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Go Back Tarik Data'],['Distribute']],
		extMenu  :[['goBackTarikData'],['Distribute']],
		extIcon  :[['resultset_first.png'],['user_go.png']],
		extText  :true,
		extInput :false,
		extOption:[{
			render : 4,
			type   : 'combo',
			header : 'Call Reason ',
			id     : 'v_result_customers', 	
			name   : 'v_result_customers',
			triger : '',
			store  : []
			}]
	});	
});


var goBackTarikData = function()
{
	doJava.File = 'mgt_tarik_data.php';
	doJava.Params={action:''}
	extendsJQuery.Content();
}

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////


var createTable = function( content, labels )
{
	var content_html ="<fieldset style='border:1px solid #dddddd;padding:5px;'> "+
						"<legend>"+labels+"</legend>"+
						" <table class=\"custom-grid\" cellspacing=\"0\" border=\"0\" width=\"60%\" align=\"left\" > "+
							" <tr> "+ 
								" <th class=\"custom-grid th-first\" align=\"center\" width=\"5%\">&nbsp;<a href=\"javascript:void(0);\" onclick=\"doJava.checkedAll('user_list_id');\">#</a></th>"+
								" <th class=\"custom-grid th-middle\" align=\"left\">&nbsp;<b style='color:#8989b6;'>Username</b></th>"+
								" <th class=\"custom-grid th-lasted\" align=\"left\">&nbsp;<b style='color:#8989b6;'>Size Data</b></th>"+
							"</tr>"+content+"</table>"+
						"</filedset>";	
							
						
	doJava.dom('contents').innerHTML = content_html;					
}

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////

doJava.dom("DistType").addEventListener("change", function(e)
{
		var distribute_type = e.currentTarget.value;
		var distribute_level = doJava.dom('DistUserLevel').value;
		doJava.File = "../class/class.tarik.data.php";
		doJava.Params = {
			action:'get_list_type',
			distribute_type : distribute_type,
			distribute_level : distribute_level
		}
		
		var message_error = doJava.eJson();
		if( message_error.result==1 )
		{
			createTable(message_error.content,message_error.label);
		}
 });
 
 ///////////////////////////////////////////////////////////////
 ///////////////// on change level /////////////////////////////
 
 doJava.dom("DistUserLevel").addEventListener("change", function(e){
		var distribute_type = doJava.dom('DistType').value;
		var distribute_level = e.currentTarget.value; 
			doJava.File = "../class/class.tarik.data.php";
			doJava.Params = {
				action:'get_list_type',
				distribute_type : distribute_type,
				distribute_level : distribute_level
			}
			
		var message_error = doJava.eJson();
			if( message_error.result==1 )
			{
				createTable(message_error.content,message_error.label);
			}
 });
 
 //////////////////////////////////////////////////////////	

	var getSizeByUser = function()
	{
		var type = doJava.dom('DistType').value;
		
		if( type==2 )
		{
			var UserId = doJava.checkedValue('user_list_id').split(',');
			if( UserId !='' )
			{
				var SizeDatas  = new Array();
				for( var x in UserId )
				{
					var ByUserSize   = doJava.dom('size_data_user'+UserId[x]).value;
						SizeDatas[x] = {'userid': UserId[x],'size':ByUserSize};
						
				}
				return JSON.stringify(SizeDatas);
			}
			else
				return false;
		}
		else
		{
			return doJava.checkedValue('user_list_id');
		}	
	}
	
 ///////////////////////////////////////////////////////////////
 ///////////////// on change level /////////////////////////////
 
 
 var Distribute = function()
 {
	var distribute_user_list   = getSizeByUser();
	var distribute_user_level  = doJava.dom('DistUserLevel').value;
	var distribute_type_level  = doJava.dom('DistType').value;
	var distribute_mode_level  = doJava.dom('DistMode').value;
	var distribute_size_level  = doJava.dom('DataSize').value;
	var distribute_alloc_level = doJava.dom('DataAlloc').value;
	var session_temp_id = doJava.dom('session_id').value;
	
	if( distribute_user_level=='' ) { alert('Please select user level ..!')}
	else if( distribute_type_level =='' ) { alert('Please select distribute type..!')}
	else if( distribute_mode_level =='' ) { alert('Please select distribute Mode..!')}
	else if( distribute_size_level =='' ) { alert('Size Data Can\'t be zero..!')}
	else if( distribute_alloc_level =='' ) { alert('Allocation Data Can\'t be zero..!')}
	else if( session_temp_id =='' ) { alert('No Session Temporary Data..!')}
	else if( distribute_user_list =='' ){ alert('No User Select !..!') }
	else{
		doJava.File = "../class/class.tarik.data.php";
		doJava.Params = 
		{
			action: 'retrive_data', 
			session_temp_id : session_temp_id,
			distribute_user_level : distribute_user_level,
			distribute_type_level : distribute_type_level,
			distribute_mode_level : distribute_mode_level,
			distribute_size_level : distribute_size_level,
			distribute_alloc_level : distribute_alloc_level,
			distribute_user_list : distribute_user_list
		}
		
		var message_error = doJava.eJson();
		if( message_error.result )
		{
			alert("Success, Total ("+message_error.count+") datas,  to ( "+message_error.agent+" ) agent ");
		}
		else{
			alert("Failed, Distribusi data !"); return false;
		}
		
	/* reload content ************************************/
	
		doJava.File   = 'act.tarik.data.php';
		doJava.Params = {
			action:'show_tmp_session',
			tmp_session_id : session_temp_id
		}
		extendsJQuery.Content();
	}	
 }
 
 
</script>

<style>
	.select { border:1px solid #dddddd;font-size:11px;background-color:#fffccc;height:22px;width:250px;}
	.input_text {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-color:#fffccc;}
	.input_box {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:90px;font-size:11px;height:20px;background-color:#fffccc;}
	.text_header { text-align:right;color:#000;font-size:12px;}
	.select_multiple { border:1px solid #dddddd;height:120px;font-size:11px;background-color:#fffccc;}
</style>

<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Temp SessionId ( <span style='color:red;'><?php echo $SessionTmpId[tmp_session_id];?> </span>) </legend>
	<input type="hidden" name="session_id" id="session_id" value="<?php echo $SessionTmpId['tmp_id']; ?>" >
	<div class="box-shadow">
		<fieldset class="corner">
			<legend > Option </legend>
			<table cellpadding="8px;" style="margin-top:5px;margin-bottom:5px;" border=0>
				<tr>
					<td class="text_header"> User Level </td>
					<td> &nbsp; <?php $jpForm -> jpCombo('DistUserLevel', 'select', getUserLevel());?>  </td>
				</tr>
				
				<tr>
					<td class="text_header"> Type </td>
					<td> &nbsp; <?php $jpForm -> jpCombo('DistType', 'select', getDistType());?> </td>
				</tr>
				
				<tr>
					<td class="text_header"> Mode </td>
					<td> &nbsp; <?php $jpForm -> jpCombo('DistMode', 'select', getDistMode());?> </td>
				</tr>
				<tr>
					<td> Data Size </td>
					<td> &nbsp; <?php $jpForm -> jpInput('DataSize', 'input_text',$SessionTmpId[tmp_size_data],NULL,1);?> </td>
				</tr>
				<tr>
					<td> Allocation Data </td>
					<td> &nbsp; <?php $jpForm -> jpInput('DataAlloc', 'input_text',$SessionTmpId[tmp_size_data]);?> </td>
				</tr>
			</table>
		</fieldset>	
	</div>
	<div id="toolbars"></div>
		<div id="contents" class="box-shadow" style="margin-top:5px;border:1px solid #dddddd;">
	</div>
</fieldset>	
	
	