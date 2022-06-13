<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " SELECT * FROM tms_ftp_config a ";
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere();
	$NavPages -> OrderBy("a.ftp_id","ASC");
	
	/** user group **/
	
	
?>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		
		$(function(){
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Enable'],['Disable'] ,['Add'],['Edit'],['Add Schedule'],['Cancel']],
				extMenu  :[['FTP_enable'],['FTP_disabled'],['FTP_adding'],['FTP_edit'],['FTP_add_schedule'],['cancelResult']],
				extIcon  :[['accept.png'],['cancel.png'], ['add.png'],['calendar_edit.png'],['clock_add.png'],['cancel.png']],
				extText  :true,
				extInput :false,
				extOption: [{
						render:6,
						type:'text',
						id:'v_result', 	
						name:'v_result',
						value:'',
						width:200
					}]
			});
			
		});
		
	var datas={}
	extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
	extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	/* assign navigation filter **/
		
	var navigation = {
		custnav : 'set_ftp_nav.php',
		custlist : 'set_ftp_list.php'
	}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		var cancelResult=function(){
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var FTP_adding = function(){
			doJava.File = '../class/class.ftp.upload.php' 
			doJava.Params ={ action:'add_ftp_setting' }	
			doJava.Load('span_top_nav');
		}
		
/* getFileSelection **/
	
	var getFileSelection = function(object)
	{	
	
		var object_input_file = doJava.dom('file_action');
		
		if( object.value!='')
		{
			object_input_file.value = object.value;
			object_input_file.style.width = "350px";
			object_input_file.style.border = "1px solid #dddddd";
			object_input_file.style.color = "blue";
			object_input_file.disabled = true;
		}
		else{
			object_input_file.disabled = true;
			object_input_file.style.width = "0px";
		}	
	}	
	
/* FTP_active_schedule ***/
	
	var FTP_schedule_save = function()
	{
	
		var minute = doJava.dom('minute').value;
		var hour   = doJava.dom('hour').value;
		var days   = doJava.dom('days').value;
		var month  = doJava.dom('month').value;
		var weeks  = doJava.dom('weeks').value;
		var file_action = doJava.dom('file_action').value;
		
		
	  if( file_action =='' ) { alert('Please select file action !'); return false; }
	  else
	  {	
		doJava.File = '../class/class.ftp.upload.php' 
		doJava.Params ={ 
			action:'atv_ftp_crontab',
			minute : minute, hour : hour,
			days : days, month : month,
			weeks : weeks, 
			file_action: file_action
		}	
		
		var error= doJava.eJson();
		if( error.result){
			alert("OK");
		}
	  }	 
	}
	
	
  var FTP_add_schedule = function()
  {
	doJava.File = '../class/class.ftp.upload.php' 
	doJava.Params ={ action:'add_ftp_schedule' }	
	doJava.Load('span_top_nav');
	
  }	
/* edit category ****/
	
	var FTP_edit = function()
	{
			var inResultCheck = doJava.checkedValue('ftp_id');
			var inArray = inResultCheck.split(',');
			
			if( inResultCheck!=''){	
			if( inArray.length>1){
				alert('Please Select One Rows');
			}
			else{
				doJava.File = '../class/class.ftp.upload.php' 
				doJava.Params ={ 
					action:'edt_ftp_setting',
					ftp_id:inArray
				}	
				doJava.Load('span_top_nav');
			}
		  }
		  else { alert('Please select rows !'); }
	}
		
/* ** delete **/	
	
	var FTP_delete = function()
		{
				var inResultCheck = doJava.checkedValue('ftp_id');
				if( inResultCheck!='')
				{
					if(confirm('Do you want to delete this FTP Config ?'))
					{
						doJava.File = '../class/class.ftp.upload.php' 
						doJava.Params = {
							action:'del_ftp_setting',
							ftp_id: inResultCheck
						}
						var error= doJava.eJson();
							if( error.result)
							{
								alert("Success, Deleting FTP Config!");
								extendsJQuery.postContent();
							}
							else{ 
								alert("Failed, Deleting FTP Config!"); 
								return false; 
							}
					}	
				}
				else{
					alert("Please select a row!")
				}
		}
	
 /* disabled **/
	
		var FTP_disabled=function()
		{
			doJava.File = '../class/class.ftp.upload.php' 
			var inResultCheck = doJava.checkedValue('ftp_id');
			if( inResultCheck!='')
			{
				doJava.Params = {
					action:'dsb_ftp_setting',
					ftp_id: inResultCheck
				}
				var error= doJava.eJson();
				if( error.result)
				{
					alert("Success disabling the FTP Config!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed disabling the FTP Config!"); 
					return false; 
				}
			}
			else{
				alert("Please select a row!")
			}
		}
		
 /* enable **/
	
		var FTP_enable=function()
		{
				doJava.File = '../class/class.ftp.upload.php' 
			var inResultCheck = doJava.checkedValue('ftp_id');
			if( inResultCheck!=''){
				doJava.Params = {
					action:'enb_ftp_settingenb_ftp_setting',
					ftp_id: inResultCheck
				}
				var error= doJava.eJson();
				if( error.result)
				{
					alert("Success Enabling the FTP Config!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed Enabling the FTP Config!"); 
					return false; 
				}
			}
			else{
				alert("Please select a row!")
			}
		}
		
/* update FTP ****/
		
		var FTP_update = function()
		{
			var ftp_host = doJava.dom('ftp_host').value; 
			var ftp_port = doJava.dom('ftp_port').value; 
			var ftp_user = doJava.dom('ftp_user').value;
			var ftp_pasword = doJava.dom('ftp_pasword').value;
			var ftp_get_file = doJava.dom('ftp_get_file').value;
			var ftp_put_file = doJava.dom('ftp_put_file').value;
			var ftp_history_file = doJava.dom('ftp_history_file').value;
			var ftp_id  = doJava.dom('ftp_id').value;
			
			
		/*  settup get varibel FTP ****/
		
			if( ftp_host=='' ) { alert("FTP Server is empty !"); return false; }
			else if( ftp_port=='' ) { alert("FTP Port is empty !"); return false; }
			else if( ftp_user=='' ) { alert("FTP User is empty !"); return false; }
			else if( ftp_pasword=='' ) { alert("FTP Password is empty !"); return false; }
			else if( ftp_get_file=='' ) { alert("FTP Get File Directory is empty!"); return false; }
			else if( ftp_put_file=='' ) { alert("FTP Put File Directory !"); return false; }
			else if( ftp_history_file=='' ) { alert("FTP History Directory !"); return false; }
			else
			{
				doJava.File = '../class/class.ftp.upload.php'; 
				doJava.Params ={
					action:'upd_ftp_setting',
					ftp_id : ftp_id,
					ftp_host : ftp_host, 
					ftp_port : ftp_port, 
					ftp_user : ftp_user,
					ftp_pasword : ftp_pasword,
					ftp_get_file : ftp_get_file,
					ftp_put_file : ftp_put_file,
					ftp_history_file : ftp_history_file
				}
					var error = doJava.eJson();
					if( error.result)
					{
						alert("Success, Update FTP Config !");
						extendsJQuery.postContent();
					}
					else{
						alert("Failed, Update FTP Config !");
						return false;
					}
			}
		}
		
/*  save activity ftp config ***/
	
	var FTP_save = function()
		{	
			var ftp_host = doJava.dom('ftp_host').value; 
			var ftp_port = doJava.dom('ftp_port').value; 
			var ftp_user = doJava.dom('ftp_user').value;
			var ftp_pasword = doJava.dom('ftp_pasword').value;
			var ftp_get_file = doJava.dom('ftp_get_file').value;
			var ftp_put_file = doJava.dom('ftp_put_file').value;
			var ftp_history_file = doJava.dom('ftp_history_file').value;
			
		/*  settup get varibel FTP ****/
		
			if( ftp_host=='' ) { alert("FTP Server is empty !"); return false; }
			else if( ftp_port=='' ) { alert("FTP Port is empty !"); return false; }
			else if( ftp_user=='' ) { alert("FTP User is empty !"); return false; }
			else if( ftp_pasword=='' ) { alert("FTP Password is empty !"); return false; }
			else if( ftp_get_file=='' ) { alert("FTP Get File Directory is empty!"); return false; }
			else if( ftp_put_file=='' ) { alert("FTP Put File Directory !"); return false; }
			else if( ftp_history_file=='' ) { alert("FTP History Directory !"); return false; }
			else
			{
				doJava.File = '../class/class.ftp.upload.php'; 
				doJava.Params ={
					action:'sav_ftp_setting',
					ftp_host : ftp_host, 
					ftp_port : ftp_port, 
					ftp_user : ftp_user,
					ftp_pasword : ftp_pasword,
					ftp_get_file : ftp_get_file,
					ftp_put_file : ftp_put_file,
					ftp_history_file : ftp_history_file
				}
					var error = doJava.eJson();
					if( error.result)
					{
						alert("Success, Save FTP Config !");
						extendsJQuery.postContent();
					}
					else{
						alert("Failed, Save FTP Config !");
						return false;
					}
			}
		}
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;FTP Upload Setting </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	