<?php
require_once(dirname(__FILE__)."/../sisipan/sessions.php");
require_once(dirname(__FILE__)."/../fungsi/global.php");
require_once(dirname(__FILE__)."/../class/MYSQLConnect.php");
require_once(dirname(__FILE__)."/../class/class.nav.table.php");
require_once(dirname(__FILE__)."/../class/class.application.php");
require_once(dirname(__FILE__)."/../sisipan/parameters.php");
require_once(dirname(__FILE__)."/../class/lib.form.php");

/** set page navigator */

///////////////////////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////////////////////////
 function getListTable()
 {
	global $db;
	$arr_result = array();
	$query = $db -> query('show tables');
	$i = 0;
	
	foreach( $query -> result_rows() as $key => $rows )
	{
		foreach( $rows as $a => $result )
		{ 
			$arr_result[$result] = $result;
		}
		$i++;
	}
	return $arr_result;	
 }	
 
 //print_r(getListTable());
 ///////////////////////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////////////////////////
 
 function modeInput()
 {
	 return array('insert'=>'Insert Rows ','Update'=>'Update Rows');
 }
 
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
 
 function fileTypeMode()
 {
	return array('xls'=>' *.xls ','csv'=>' *.csv ','txt'=>' *.txt ');
 }
 
 ///////////////////////////////////////////////////////////////////////////
 /*** settup query **/
 
$sql = "select * from tms_tempalate_upload ";

$NavPages -> setPage(10);
$NavPages -> query($sql);
 
?>	
<!--#cs : javscript -->

<script type="text/javascript"  src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/aqPaging.js?t=<?php echo time(); ?>"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js?t=<?php echo time(); ?>"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>js/extendsJQuery.js?t=<?php echo time(); ?>"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>js/javaclass.js?t=<?php echo time(); ?>"></script>
<script type="text/javascript">

<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->

	var filename = '<?php echo basename(__FILE__);?>';
	$(function(){
		$('#toolbars').extToolbars({
			extUrl   :'../gambar/icon',
			extTitle :[['Clear'],['Show Datas'],['Save Template'],['Download Template'],['Enable'],['Disable']],
			extMenu  :[['Clear'],['ShowDatas'],['SaveTemplate'],['DownloadTemplate'],['Enable'],['Disable']],
			extIcon  :[['find.png'],['zoom.png'],['disk.png'],['application_add.png'],['accept.png'],['cancel.png']],
			extText  :true,
			extInput :true,
			extOption:[{
				render : 2,
				}]
		});
		
		// $('#start_date,#end_date').datepicker({ dateFormat:'dd-mm-yy'});
	});
	
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->

	extendsJQuery.totalPage = <?php echo $NavPages -> getTotPages(); ?>;
	extendsJQuery.totalRecord = <?php echo $NavPages -> getTotRows(); ?>;
	
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->

	var datas = 
	{
	
	}	
	
	
/* deleted data from template data rows **/
	
	var Delete = function()
	{
		var array_list_check = doJava.checkedValue('TemplateId');
		var geted_list_check = array_list_check.split(',');
		
		if( geted_list_check.length > 0 )
		{
			if( confirm('Do you want to deleted this template ?'))
			{
				doJava.File = '../class/class.settemplate.upload.php';
				doJava.Params = 
				{
					action:'delete_template',
					check_list_id : array_list_check
				}
				var error_data = doJava.eJson();
				if( error_data.result )
				{
					alert("Success, Delete template !");
					$('#main_content').load('set_temp_upload_nav.php');
				}	
				else{
					alert("Failed, Delete template !"); 
					return false;
				}
			}	
		}
	}
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->

	var navigation = 
	{
		custnav	 : 'set_temp_upload_nav.php',
		custlist : 'set_temp_upload_list.php'
	}
	
		
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
	
	extendsJQuery.construct(navigation,datas)
	extendsJQuery.postContentList();	
		
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
	var Enable = function()
	{
		var TemplateId = doJava.checkedValue('TemplateId');
		//alert(TemplateId);
		if( (TemplateId!='') )
		{
			doJava.File = '../class/class.settemplate.upload.php';
			doJava.Params = 
			{
				action :'enable_template',
				TemplateId : TemplateId
			}
			
			var err = doJava.Post();
			alert(err);
		}
	}
	
	var Disable = function()
	{
		
	}
	
	var SaveTemplate = function()
	{
		var list_check = doJava.get_list_checkdata();
		var table_name = doJava.dom('table_name').value;
		var mode_input = doJava.dom('mode_input').value;
		var templ_name = doJava.dom('templ_name').value;
		var file_type  = doJava.dom('file_type').value;
		
		if(confirm('Do you want to save this template ?'))
		{	
			doJava.File = '../class/class.settemplate.upload.php';
			doJava.Params = {
				action :'save_tempalate', table_name : table_name,
				mode_input : mode_input, file_type  : file_type,
				templ_name : templ_name, list_check : list_check
			}
			
			var result = doJava.eJson();
			if( result.result)
			{
				alert('Success Save Template Upload!');
				$('#main_content').load('set_temp_upload_nav.php');
			}
			else{
				alert('Success Save Template Upload!');
				return false;
			}
		}
	}
	
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
	doJava.get_list_checkdata= function()
	{
		var list_check_name = this.checkedValue('chk_columns');
		var split_check_name = list_check_name.split(',');
		var string_check_data = '';	
		for( var s_i in split_check_name )
		{
			var alias_name = this.dom('alias_name_'+split_check_name[s_i]).value;
			var order_name = this.dom('order_name_'+split_check_name[s_i]).value;
			
			string_check_data = split_check_name[s_i]+"~"+alias_name+"~"+order_name+"|"+string_check_data;
		}	
		string_check_data = string_check_data.substring(0,(string_check_data.length-1));
		return string_check_data;
	}

<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
	
	var getTableColumns = function(select)
	{
		doJava.File = '../class/class.settemplate.upload.php';
		doJava.Params = {
			action :'get_columns',
			tables : select.value
		}
		doJava.Load('list_columns');
	}
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
		
	var DownloadTemplate = function()
	{
		var TemplateId = doJava.checkedValue('TemplateId');
		if( (TemplateId!='') )
		{
			doJava.File = '../class/class.settemplate.upload.php';
			doJava.Params = 
			{
				action :'download_template',
				TemplateId : TemplateId
			}
			
			doJava.windowOpen();
		}
	}	
	
	var getListCheck = function(object)
	{
		var alias_name = 'alias_name_'+object.value;
		var order_name = 'order_name_'+object.value;
		
		if( object.checked)
		{
			doJava.dom(alias_name).value = object.value;
			doJava.dom(order_name).value = 0;
		}
		else{
			doJava.dom(alias_name).value = '';
			doJava.dom(order_name).value = '';
		}	
	}	
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
	var ReturnNextForm = function(object)
	{
		doJava.checkedAll(object);
		var list_html_data = '';
		var list_check_data = doJava.checkedValue(object);
		// var split_list_data = list_check_data.split(',');
		
		// list_html_data = "<table>"+
								// "<tr> "+
									// "<td style='background-color:#FFFDDD;'><b>Column Names</b></td>"+
									// "<td style='background-color:#FFFDDD;'><b>Alias Name</b></td>"+
									// "<td style='background-color:#FFFDDD;'><b>Order</b></td>"+
								// "</tr>";	
		
		// for( var i in split_list_data )
		// {
			// list_html_data += "<tr>";
			// list_html_data += " <td><input type='text' class='input_text' name='value_name' value='"+split_list_data[i]+"'></td>"+
							  // " <td><input type='text' class='input_text' name='alias_name' value=''></td>"+
							  // " <td><input type='text' class='input_box' name='order_name' value=''></td>";
			// list_html_data += "</tr>";
		// }	
		// list_html_data +="</table>";
		// doJava.dom('test').style.class = 'box-shadow'; 
		// doJava.dom('test').innerHTML = list_html_data; 
	}
	
</script>
<!-- #cs: style css -->	
<style>
	.select { width:205px;border:1px solid #dddddd;font-size:11px;height:22px; background-color:#fffccc;background:url('../gambar/input_bg.png'); text-align:left; height:22px;}
	.input_text {font-family:Arial;color:#746b6a;font-weight:bold;border:1px solid red;width:200px;font-size:11px;height:20px;background:url('../gambar/input_bg.png'); text-align:left;}
	.input_box {font-family:Arial;color:#746b6a;font-weight:bold;border:1px solid red;width:30px;font-size:11px;height:20px;background:url('../gambar/input_bg.png'); text-align:left;}
	.input_alias {font-family:Arial;color:#746b6a;font-weight:bold;border:1px solid red;width:160px;font-size:11px;height:20px;background:url('../gambar/input_bg.png'); text-align:left;}
	
	.text_header { text-align:right;color:#746b6a;font-size:12px;vertical-text:top;font-weight:bold;}
	.select_multiple { border:1px solid #dddddd;height:120px;font-size:11px;background-color:#fffccc; }
</style>
<!-- #ce: style css -->

<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- start : content -->

	<fieldset class="corner">
		<legend class="icon-customers">&nbsp;&nbsp;Setting Template Upload </legend>	
			<div id="window_temp" class="box-shadow" style="border:1px solid #dddddd;padding:8px;">	
				<table cellpadding="8px" width="100%" border=0>
					<tr>
						<td valign="top" class='text_header' width="15%">&nbsp;Database Table </td>
						<td valign="top" width="10%"><?php echo $jpForm ->jpCombo('table_name','select', getListTable(),NULL,'onChange="javascript:getTableColumns(this);"');?></td>
						<td valign="top" rowspan=4>
							<div id="list_columns" style="background-color:#FFFFFF;border:1px solid #DDDDDD;position:absolute;overflow:auto;z-index:10000;"></div>
						</td>
					</tr>
					<tr>
						<td valign="top" class='text_header'>&nbsp;Mode Input </td>
						<td valign="top"><?php echo $jpForm ->jpCombo('mode_input','select',modeInput(),NULL,null);?></td>
					</tr>
					<tr>
						<td valign="top" class='text_header'>&nbsp;File Type </td>
						<td valign="top"><?php echo $jpForm ->jpCombo('file_type','select',fileTypeMode(),NULL,null);?></td>
					</tr>
					<tr>
						<td valign="top" class='text_header'>&nbsp;Template Name </td>
						<td valign="top"><?php echo $jpForm ->jpInput('templ_name','input_text',NULL);?></td>
					</tr>
				</table>
				
			</div>
			
			<div id="toolbars"></div>
			<div id="customer_panel" class="box-shadow"> 
					<div class="content_table" style="background-color:#FFFFFF;"></div>
				<div id="pager"></div>
			</div>
	</fieldset>	
		
<!-- stop : content -->
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
<div id='WinDialog'></div>