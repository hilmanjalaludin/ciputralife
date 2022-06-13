<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	SetNoCache();
	$filter ='';
	
	$sql = " select
				a.id as extId,
				a.ext_number as extNumber, 
				b.set_value as extPbx,
				a.ext_desc as extDesc,
				a.ext_type as extType,
				a.ext_status as extStatus,
				a.ext_location as extLocation
			 from cc_extension_agent a left join cc_pbx_settings b on a.pbx=b.id ";
					
					
	
	$NavPages -> setPage(20);			 
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	
	
	/** user group **/
	
	function userGroup(){
		global $db;
		$datas= array();
			$sql = "select * from tms_agent_profile ";
			$qry = $db->execute($sql,__FILE__,__LINE__);
			while( $row = $db->fetchrow($qry)):
				$datas[$row->UserId] = $row->name; 
			endwhile;
			
		return $datas;	
	
	}	
	
	
	
	
?>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/upload.js"></script>
  	<script type="text/javascript">
	
		$(function(){
			// jQuery('.toolbars').corner();
			// jQuery('.corner').corner();
			$('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[['Upload'],['Edit'],['Add'],['Delete'],['Release Extension'],['Restart Service'],['Clear']],
				extMenu :[['UploadExtension'],['EditExtension'],['AddExtension'],['DeleteExtension'],['ReleaseExtension'],['RestartService'],['Clear']],
				extIcon :[['page_white_excel.png'],['page_edit.png'],['add.png'],['cross.png'],['phone_add.png'],['cog_go.png'],['cancel.png']],
				extText :true,
				extInput:true,
				extOption:[{
						render:8,
						type:'text',
						id:'v_cmp_user', 	
						name:'v_cmp_user',
						value:'<?php echo $db->escPost('UserId');?>',
						width:120
					}]
			});
		});
		
	var RestartService = function(){
	
		if( confirm('Do you want to restart this service ?')){
			doJava.File = '../class/class.extension.system.php';
			doJava.Params = { action : 'ctb_restart_exe'}
			var error = doJava.Post();
				alert(error);
				
		}		
	}
	
		
	var saveExtension = function()
	{
		var ext_number  = doJava.dom('ext_number').value;
		var ext_pbx = doJava.dom('ext_pbx').value;
		var ext_desc  = doJava.dom('ext_desc').value;
		var ext_type  = doJava.dom('ext_type').value;
		var ext_status  = doJava.dom('ext_status').value;
		var ext_location  = doJava.dom('ext_location').value;
		
		if( ext_pbx!='' && ext_number!='')
		{
			doJava.File = '../class/class.extension.system.php';
			doJava.Params = {
				action : 'add_extension_exe',
				ext_number : doJava.dom('ext_number').value,
				ext_pbx : doJava.dom('ext_pbx').value,
				ext_desc : doJava.dom('ext_desc').value,
				ext_type : doJava.dom('ext_type').value,
				ext_status : doJava.dom('ext_status').value,
				ext_location : doJava.dom('ext_location').value
			}
			
			var error= doJava.Post();
			
			if( error==1){
				alert("Success, Save Extension!");
				extendsJQuery.construct(navigation,'')
				extendsJQuery.postContentList();
			}
			else{
				alert("Failed, Save Extension!");
			}
		}
		
	}
	
	var UpdateExtension = function()
	{
		var extId  = doJava.dom('extId').value;
		var ext_number  = doJava.dom('ext_number').value;
		var ext_pbx = doJava.dom('ext_pbx').value;
		var ext_desc  = doJava.dom('ext_desc').value;
		var ext_type  = doJava.dom('ext_type').value;
		var ext_status  = doJava.dom('ext_status').value;
		var ext_location  = doJava.dom('ext_location').value;
		
		if( ext_pbx!='' && ext_number!='')
		{
			doJava.File = '../class/class.extension.system.php';
			doJava.Params = {
				action : 'upd_extension_exe',
				id : extId,
				ext_number : doJava.dom('ext_number').value,
				ext_pbx : doJava.dom('ext_pbx').value,
				ext_desc : doJava.dom('ext_desc').value,
				ext_type : doJava.dom('ext_type').value,
				ext_status : doJava.dom('ext_status').value,
				ext_location : doJava.dom('ext_location').value
			}
			
			var error= doJava.Post();
			
			if( error==1){
				alert("Success, Update Extension!");
				extendsJQuery.construct(navigation,'')
				extendsJQuery.postContentList();
			}
			else{
				alert("Failed, Update Extension!");
			}
		}
		
	}
	
	var DeleteExtension = function(){
		var extension  = doJava.checkedValue('chk_ext');
		if( extension!='')
		{
			doJava.File = '../class/class.extension.system.php';
			doJava.Params = {
				action : 'del_extension_exe',
				extension : extension
			}
			
			var error= doJava.Post();	
			if( parseInt(error)>0 ){
				alert("Success, Delete Extension!");
				extendsJQuery.construct(navigation,'')
				extendsJQuery.postContentList();
			}
			else{
				alert("Failed, Delete Extension!");
			}
		}
		else{
				alert("Please select rows !");
			}
	}	

/* ***************# Section #*************************************************************************/

				
		var datas={ UserId:'<?php echo $db->escPost('UserId');?>'}
		extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
		
/* ***************# Section #*************************************************************************/			
/* assign navigation filter **/
		
		var initClass  = '../class/class.extension.system.php'
		var navigation = {
			custnav:'set_extension_nav.php',
			custlist:'set_extension_list.php'
		}
		
		
/* ***************# Section #*************************************************************************/		
/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		

	
/* ***************# Section #*************************************************************************/
	
	var EditExtension =function(){
		
		var extArray  = doJava.checkedValue('chk_ext');
		var extlength = extArray.split(',');
			if( extArray!=''){
				if( extlength.length==1){
					doJava.File = '../class/class.extension.system.php';
					doJava.Params = {
						action:'edit_extension_tpl',
						extension:extlength[0]
					}
					doJava.Load('tpl_header');
					
				}
				else{
					alert('Please select one rows!')	
				}
			}
			else{
				alert('Please select rows!')
			}
	}
	
	
/* ***************# Section #*************************************************************************/
	
	var actionUpload =function(){
		AjaxUploads.UploadsConfig = { 
				actToUploads   : initClass+'?'+doJava.ArrVal(),
				methodUploads  : 'POST', // mthod action /post/get dflt:POST
				fileToUploads  : 'fileToupload', // nama id pada type file input dflt : fileToupload
				numberProgress : 'progressNumber', // progress bar id dalam percent  dflt ::  progressNumber
				innerProgress  : 'prog', // progress bar id   dflt ::  prog
				fileInfoUploads  : {
						fileName :'fileName', // nama id untuk informasi nama file   dflt ::  fileName
						fileType :'fileType', // nama id untuk informasi type file   dflt ::  fileType
						fileSize :'fileSize'  // nama id untuk informasi Ukuran file  dflt ::  fileType
				}
		}
		
		AjaxUploads.UploadsOther(); 
	}


/* ***************# Section #*************************************************************************/
	
	var Upload=function(){
		var ajaxLoad  = doJava.dom('loading_image'); 
		var act_file_name = doJava.dom('fileToupload').value
		var mode_action = doJava.checkedValue('modus_action');
		
		
		if( act_file_name!='')
		{
			if( confirm('Do you want to upload this file ?')){
				
				doJava.dom('loadings_gambar').style.display="block";
					doJava.File = '../class/class.extension.system.php';
					doJava.Params ={ 
						action : 'upl_extension_exe',
						filename : act_file_name,
						mode : mode_action
					}
					
				actionUpload();
				extendsJQuery.postContent();
			}
			else{ return false; }
		}
		else 
			alert('Please select file!');
		
	}	

/* ***************# Section #*************************************************************************/	
	
	var UploadExtension = function(){
		doJava.File = '../class/class.extension.system.php';
		doJava.Params = { action:'upl_extension_tpl'}
		doJava.Load('tpl_header');
	}
	
/* ***************# Section #*************************************************************************/
	
	var Clear =function(){
		doJava.Params = { action:'clear' }
		doJava.Load('tpl_header');
	}
	
/* ***************# Section #*************************************************************************/
	
	var ReleaseExt = function(){
		var ajaxLoad  = doJava.dom('loading_image'); 
		var ext_number = doJava.dom('ext_number').value;
		if( ext_number!='')
		{
			ajaxLoad.innerHTML = '<img src="../gambar/loading.gif"> Please Wait...'; 
			doJava.File = '../class/class.extension.system.php';
			doJava.Params = {
				action : 'rel_extension_exe',
				ext_number : ext_number
			}
			
			var error= doJava.Post();
			if(error==1)
				{
					ajaxLoad.innerHTML="";
					alert('Success, Release Extension');
				}
			else
				{
					ajaxLoad.innerHTML="";
					alert('Failed, Release Extension');
				}
		}
		else
		{
			alert("Please input Or selected Extension Number!");
		}	
	}
	
	var ReleaseExtension = function(){
		var extArray  = doJava.checkedValue('chk_ext');
		var extlength = extArray.split(',');
			doJava.File = '../class/class.extension.system.php';
				doJava.Params = {
					action:'rel_extension_tpl',
					extension:extlength[0]
				}
				doJava.Load('tpl_header');
	}
	
	var AddExtension =function(){
		doJava.File = '../class/class.extension.system.php';
		doJava.Params = { action:'add_extension_tpl'}
		doJava.Load('tpl_header');
	}
	
	
	/* search agent **/
	
	var searchAgent = function(){
		var UserId = doJava.dom('v_cmp_user').value;
		datas ={UserId:UserId}
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContent();
	}
		
	</script>
	<fieldset class="corner" style="background-color:white;">
		<legend class="icon-userapplication">&nbsp;&nbsp;Extension Management </legend>
			<div id="toolbars" class="toolbars"></div>
			
			<div id="tpl_header"></div>
			<div class="content_table"></div>
			<div id="pager"></div>
			<div id="UserTpl"></div>
	</fieldset>	
	