<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.nav.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");


SetNoCache();
/** setup sql syntax *******/

  $sql = " SELECT * FROM t_lk_branch a ";
  
 /** setup navigation page ****/
 
  $NavPages -> setPage(20);			 
  $NavPages -> query($sql);

 /** set filter ***************/
 $filter = '';
 if( $db -> havepost('keywords'))
 {
	$filter.= " AND 
				(
					a.BranchCode LIKE '%".$_REQUEST['keywords']."%' OR  
					a.BranchName LIKE '%".$_REQUEST['keywords']."%' OR 
					a.BranchManager LIKE '%".$_REQUEST['keywords']."%' OR 
					a.BranchContact LIKE '%".$_REQUEST['keywords']."%' OR 
					a.BranchAddress LIKE '%".$_REQUEST['keywords']."%' OR 
					a.BranchEmail LIKE '%".$_REQUEST['keywords']."%' 
				) ";
 }
 
 $NavPages -> setWhere($filter);	
?>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript">
	
/* assign show list content **/
	
	var datas = 
	{
		keywords : '<?php echo $_REQUEST['keywords'];?>',
		order_by : '<?php echo $_REQUEST['order_by'];?>',
		type 	 : '<?php echo $_REQUEST['type'];?>'
	}
	
/* exttollbars **************/

	$('#toolbars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Enable'],['Disable'] ,['Add'],['Edit'],['Delete'],['Cancel'],[],['Search']],
		extMenu  :[['enableWork'],['disableWork'],['addWork'],['editWork'],['deleteWork'],['cancelWork'],[],['searchWork']],
		extIcon  :[['accept.png'],['cancel.png'], ['add.png'],['calendar_edit.png'],['delete.png'],['cancel.png'],[],['zoom.png']],
		extText  :true,
		extInput :true,
		extOption: [{
					 render:6,
					 type:'text',
					 id:'v_result', 	
					 name:'v_result',
					 value: datas.keywords,
					 width:200
					}]
	});
	
/* assign show list content **/
	
	var navigation = 
	{
		custnav	 : 'set_work_nav.php',
		custlist : 'set_work_list.php'
	}
	
	extendsJQuery.totalPage   = <?php echo $NavPages -> getTotPages(); ?>;
	extendsJQuery.totalRecord = <?php echo $NavPages -> getTotRows(); ?>;

/* assign show list content **/
	
	var searchWork = function(){
		var keywords = doJava.dom('v_result').value;
		var datas = {
			keywords : keywords
		}
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContent();
	}

/* assign show list content **/

	extendsJQuery.construct(navigation,datas)
	extendsJQuery.postContentList();

/* UpdateData save result ***/

 var UpdateData = function()
	{
		var BranchId =  doJava.dom('BranchId').value;
		var BranchCode =  doJava.dom('BranchCode').value;
		var BranchName =  doJava.dom('BranchName').value;
		var BranchContact = doJava.dom('BranchContact').value; 
		var BranchAddress =  doJava.dom('BranchAddress').value;
		var BranchManager = doJava.dom('BranchManager').value;
		var BranchEmail = doJava.dom('BranchEmail').value;
		if( BranchCode =='' ) { alert('Please input BranchCode..!'); return false;}
		else if( BranchName =='' ) { alert('Please input BranchName..!'); return false;}
		else
		{
			doJava.File = '../class/class.work.area.php';
			doJava.Params = { 
				action : 'update_work_area',
				BranchId : BranchId,
				BranchCode : BranchCode,
				BranchName : BranchName,
				BranchContact : BranchContact, 
				BranchAddress : BranchAddress,
				BranchManager : BranchManager,
				BranchEmail : BranchEmail
			}
			
			var error = doJava.eJson();
			if( error.result ){
				alert('Success, Update Branch Data..!');
				extendsJQuery.postContent();	
			}
			else{
				alert('Failed, Update Branch Data..!');
			}
		}
	}		

/* add save result ***/
 var saveResult = function()
	{
		var BranchCode =  doJava.dom('BranchCode').value;
		var BranchName =  doJava.dom('BranchName').value;
		var BranchContact = doJava.dom('BranchContact').value; 
		var BranchAddress =  doJava.dom('BranchAddress').value;
		var BranchManager = doJava.dom('BranchManager').value;
		var BranchEmail = doJava.dom('BranchEmail').value;
		
		if( BranchCode =='' ) { alert('Please input BranchCode..!'); return false;}
		else if( BranchName =='' ) { alert('Please input BranchName..!'); return false;}
		else
		{
			doJava.File = '../class/class.work.area.php';
			doJava.Params = { 
				action : 'insert_work_area',
				BranchCode : BranchCode,
				BranchName : BranchName,
				BranchContact : BranchContact, 
				BranchAddress : BranchAddress,
				BranchManager : BranchManager,
				BranchEmail : BranchEmail
			}
			
			var error = doJava.eJson();
			if( error.result ){
				alert('Success, Add Branch Data..!');
				extendsJQuery.postContent();	
			}
			else{
				alert('Failed, Add Branch Data..!');
			}
		}
	}	
/* deleted work ********************/

var deleteWork = function()
	{
		doJava.File = '../class/class.work.area.php';
		var arr_cbx_list = doJava.checkedValue('BranchId');
		doJava.Params = {
			action :'delete_work_area',
			BranchId : arr_cbx_list 
		}
		
		var result = doJava.eJson();
		if( result.success ){
			alert('Success, Deleted Rows!');
			extendsJQuery.postContent();	

		}
		else
			alert('Failed, Deleted Rows!');
	}
	
/* enabel work ********************/

var enableWork = function()
	{
			doJava.File = '../class/class.work.area.php';
		var arr_cbx_list = doJava.checkedValue('BranchId');
		doJava.Params = {
			action :'enable_work_area',
			BranchId : arr_cbx_list 
		}
		
		var result = doJava.eJson();
		if( result.success ){
			alert('Success Enable Rows!');
			extendsJQuery.postContent();	

		}
		else
			alert('Failed Enable Rows!');
	}
	
/* editWork **/

	var editWork = function(){
	
		var arr_cbx_list = doJava.checkedValue('BranchId').split(',');
		if( arr_cbx_list.length==1){
			doJava.File = '../class/class.work.area.php';
			doJava.Params = 
			{
				action :'edit_work_area',
				BranchId : arr_cbx_list[0] 
			}
			doJava.Load('span_top_nav');
		}
		else{
			alert('please select a rows !')
		}
	}
	
/* disbaled work ***/
	
	var disableWork = function()
	{
		doJava.File = '../class/class.work.area.php';
		var arr_cbx_list = doJava.checkedValue('BranchId');
			doJava.Params = {
				action :'disable_work_area',
				BranchId : arr_cbx_list 
			}
			
			var result = doJava.eJson();
			if( result.success ){
				alert('Success Disable Rows!');
				extendsJQuery.postContent();	
			}
			else
				alert('Failed Disable Rows!');
	}
	
	var addWork = function()
	{
		doJava.File = '../class/class.work.area.php';
		doJava.Params ={ action: 'add_work_area' }
		doJava.Load('span_top_nav');
	}
	
/* cancelWork *************/	
	var cancelWork = function()
	{
		doJava.File = '../class/class.work.area.php';
		doJava.Params ={ action: 'clear_work_area' }
		doJava.Load('span_top_nav');
	}	
</script>

<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- start : content -->

	<fieldset class="corner">
		<legend class="icon-customers">&nbsp;&nbsp;Setting Work Branch </legend>	
			<div id="toolbars"></div>
			<div id="span_top_nav" style="margin:5px;"></div>
			<div id="customer_panel" class="box-shadow">
				<div class="content_table" style="background-color:#FFFFFF;"></div>
				<div id="pager"></div>
			</div>
	</fieldset>	
		
<!-- stop : content -->
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->