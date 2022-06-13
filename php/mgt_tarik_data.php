<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../class/lib.form.php");
	require(dirname(__FILE__)."/../sisipan/parameters.php");
 
?>
<style>
	a.legend_label{text-decoration:none;}
	.legend:hover{color:blue;}
	div.content{padding-top:5px; padding-left:5px; padding-bottom:5px; color:red; }
</style>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>	
<script type="text/javascript">

/////////////////////////////////
////////////////////////////////
	$(function(){
		$('#toolbars').extToolbars({
			extUrl  :'../gambar/icon',
			extTitle:[['Show Data'],['Save to Temporary'],['Download'],['Clear'],['Delete'],[],[]],
			extMenu :[['showData'],['SaveToTmp'],['Download'],['Clear'],['Delete'],[],[]],
			extIcon :[['zoom.png'],['drive_disk.png'],['page_excel.png'],['cancel.png'],['cross.png'],[],[]],
			extText :true,
			extInput :true,
			extOption:[
				{
					type   : 'text',
					name   : 'jumlah_data',
					id     : 'jumlah_data',
					value  : '',
					render : 6
				},{
					type   : 'label',
					label  : 'Size Data :',
					name   : 'label_jumlah_data',
					id     : 'label_jumlah_data',
					render : 5
				}]
		});
	});
 
 doJava.dom('label_jumlah_data').style.color='red';	
 doJava.dom('jumlah_data').readOnly=true;	
 
 // doJava.dom("jumlah_data").addEventListener("click", function(e) {
		// alert(e.currentTarget.value)
 // });
 
 var Clear = function()
 {
	doJava.uncheckedAll('campaign_name');
	doJava.uncheckedAll('reason_name');
	doJava.uncheckedAll('first_level_user');
	doJava.uncheckedAll('second_level_user'); 
	doJava.uncheckedAll('three_level_user');
	showData();
 }
 /* 888 ***/
 
 var Download = function()
 {
	var tmp_list_id = doJava.checkedValue('tmp_list_id');
	if( tmp_list_id !='' )
	{
		doJava.File   = '../class/class.tarik.data.php';
		doJava.Params = 
		{
			action : 'download_data',
			tmp_session_id : doJava.Base64.encode(tmp_list_id)
		}
		
	//	var InterValId = setInterval('getContentTable();',1000);
			doJava.winew.opener();
			getContentTable();
			//window.clearInterval(InterValId);
		
	}
	else{
		alert('Please select a rows !')
	}
 }
 
 /* *** **/
 
 var Delete = function()
 {
	var tmp_list_id = doJava.checkedValue('tmp_list_id');
	doJava.File = "../class/class.tarik.data.php";
	doJava.Params = {
		action :'delete_temp_id',
		temp_list_id : tmp_list_id
	}
		var error_message = doJava.eJson();
		if( error_message.result )
		{
			alert("Success, Delete Temporary Session !");
			getContentTable();	
		}
		else{
			alert("Failed, Delete Temporary Session !");
			return false;
		}
			
 }
 
/* save to tmp before to distribute again ***/
 
 var SaveToTmp = function()
 {
	var sql_str_id = doJava.dom('sql_code_id').value;
		if( sql_str_id!='')
		{
			doJava.File = "../class/class.tarik.data.php";
			doJava.Params = {
				action :'save_data_tmp',
				sql_code_id : sql_str_id
			}
			var error_datas = doJava.eJson();
			if( error_datas.result ){
				alert('Success, Save to Temporary data ( '+error_datas.total_tmp_save+' ) Rows !');
				getContentTable();	
			}
			else{
					alert('Failed, Save to Temporary data !');
			}	
		}
		else{
			alert('No Session Filtering Data !'); 
		}
		
 }
/* get value data **/
	
var getLevelTop = function(Handle)
{
	var tarik_level = doJava.dom('first_level_user');
		doJava.checkedAll('first_level_user');
		FirstLevelUser(tarik_level,Handle)
	
	
}

/* get value data **/
	
var getLevelMiddle = function(Handle,levelUser)
{
	var tarik_level = doJava.dom(levelUser);
		doJava.checkedAll(levelUser);
		if( Handle!=4)
		{
			ThreeLevelUser(tarik_level,Handle)
		}
}
 
 
/* get content table **/
 
 var getContentTable = function()
 {
	doJava.File = "../class/class.tarik.data.php";
	doJava.Params = {
		action:'get_content_table' }
	
	var content = doJava.eJson();
	doJava.dom('content_tables').innerHTML = content.tables;	
 }	
 
 /* show data ***/
 
 var showData = function()
 {
	var campaign_name_id = doJava.checkedValue('campaign_name');
	var reason_name_id = doJava.checkedValue('reason_name');
	var top_level_user = doJava.checkedValue('first_level_user');
	var second_level_user = doJava.checkedValue('second_level_user'); 
	var three_level_user = doJava.checkedValue('three_level_user');
		doJava.File = "../class/class.tarik.data.php";
		doJava.Params = {
			action : 'get_size_data',
			campaign_name_id : campaign_name_id,
			reason_name_id : reason_name_id,
			top_level_user : top_level_user,
			second_level_user : second_level_user, 
			three_level_user : three_level_user
		}
		
		var error_message = doJava.eJson();
		
			if( error_message.result==1)
			{
				doJava.dom('jumlah_data').value = error_message.total_rows;
				if( error_message.total_rows > 0 )
				{
					doJava.dom('sql_code_id').value = error_message.code;
				}
				else{
					doJava.dom('sql_code_id').value = '';
				}
			} 
	}	
/////////////////////////////////
////////////////////////////////	
new (function(){
	doJava.File = "../class/class.tarik.data.php";
	doJava.Params={ action : 'get_list_campaign' }
	doJava.Load('content_campaign_list')
});	

/////////////////////////////////
////////////////////////////////	
	
new (function(){
	doJava.File = "../class/class.tarik.data.php";
	doJava.Params={ action : 'get_list_result' }
	doJava.Load('content_result_list')
});	

/////////////////////////////////
////////////////////////////////	

new (function(){
	doJava.File = "../class/class.tarik.data.php";
	doJava.Params={ action : 'get_list_user' }
	doJava.Load('content_top_user_list')
});		
	
////////////////////////////////////////////
///////////////////////////////////////////////

new (function(){
	getContentTable();
})	
	
var content_html = function(legend,content)
{
	var cv = " <div style='border:1px solid #dddddd;width:400px;height:150px;overflow:auto;'> "+
			 " <fieldset style='border:0px solid #dddddd;'> "+
				" <legend>"+legend+"</legend>"+
				" "+content+" "+
			 " </fieldset> "+
			 " </div>";
	return cv;
} 	

var FirstLevelUser = function(combo,position)
{
	var TopLevelUsser = doJava.checkedValue(combo.name);
		doJava.File = "../class/class.tarik.data.php";
		doJava.Params={ 
			action : 'get_list_low_user',
			UserId : TopLevelUsser,
			Handle : position
		}
		var content_datas = doJava.eJson();
		if( content_datas.result==1){
			doJava.dom('content_low_user_list').innerHTML = content_html(content_datas.legend_html,content_datas.content_html);
		}
		else{	
			doJava.dom('content_low_user_list').innerHTML = " ";		
		}		
															
}	

var ThreeLevelUser = function(combo,position)
{
	var TopLevelUsser = doJava.checkedValue(combo.name);
		doJava.File = "../class/class.tarik.data.php";
		doJava.Params={ 
			action : 'get_list_low_user',
			UserId : TopLevelUsser,
			Handle : position
		}
		var content_datas = doJava.eJson();
		if( content_datas.result==1)
		{
			doJava.dom('tree_level_user').innerHTML = content_html(content_datas.legend_html,content_datas.content_html);
		}
		else{	
			doJava.dom('tree_level_user').innerHTML = " ";		
		}												 
}
	
</script>
		
<fieldset class="corner" >
	<legend class="icon-menulist">&nbsp;&nbsp;Download Data </legend>
	<!-- content filter -->
	<div class="box-shadow"> 
		<input type="hidden" name="sql_code_id" id="sql_code_id" value="">
		<table border=0 cellpadding="6px;">
			<tr>
				<td valign="top">
					<fieldset style="border:1px solid #dddddd;">
						<legend> <a href="javascript:void(0);" onclick="doJava.checkedAll('campaign_name');"  style="text-decoration:none;"> # Campaign List </a> </legend>
						<div id="content_campaign_list" style='border:1px solid #dddddd;width:400px;height:150px;overflow:auto;' class="content"></div>
					</fieldset>	
				</td>
				<td valign="top">
					<fieldset style="border:1px solid #dddddd;">
						<legend> <a href="javascript:void(0);" onclick="doJava.checkedAll('reason_name');" style="text-decoration:none;"> # From Result </a> </legend>
						<div id="content_result_list" style='border:1px solid #dddddd;width:400px;height:150px;overflow:auto;' class="content"></div>
					</fieldset>	
				</td>
			</tr>
			<tr>
				<td valign="top"><div id="content_top_user_list" class="content" >&nbsp;</div></td>
				<td valign="top"><div id="content_low_user_list" class="content">&nbsp;</div></td>
			</tr>
			<tr>
				<td valign="top" colspan=2><div id='tree_level_user' class="content">&nbsp;</div></td>
			</tr>
		</table>
	</div>
	
	<!-- content toolbar -->
	<div id="toolbars" class="toolbars"></div>
	<div id="content_tables" class="content" style="margin-top:5px;"></div>
	<!-- content data mode --> 
	
</fieldset>	
