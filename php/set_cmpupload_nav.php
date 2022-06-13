<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " SELECT distinct(a.CampaignId) FROM t_gn_campaign a 
					LEFT JOIN t_lk_campaigntype c on a.CampaignTypeId=c.CampaignTypeId 
					LEFT JOIN t_lk_cignasystem d on a.CignaSystemId=d.CignaSystemId 
					LEFT JOIN t_lk_reuploadreason e on a.ReUploadReasonId=e.ReUploadReasonId ";
					
	$NavPages -> setPage(20);			 
	$NavPages -> query($sql);
	
	$filter = '';
	if( $db -> havepost('status_campaign') )
	{
		if( $_REQUEST['status_campaign']==0) $filter = " and  a.CampaignStatusFlag=0 "; 
		if( $_REQUEST['status_campaign']==1) $filter = " and  a.CampaignStatusFlag=1 ";
	}
	
    $NavPages -> setWhere($filter);
	
?>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/javaclass.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/upload.js"></script>
	<script type="text/javascript">
	
	var status_campaign = '<?php echo $_REQUEST['status_campaign']; ?>';
 /* get toolbar and pagging **/
 
	    $(function(){
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Add Campaign'],['Edit Data'],['Upload Data'],['FTP Bucket '],['Cancel'],['Show'],['Detail Data']],
				extMenu  :[['addCampaign'],['EditData'],['uploadData'],['FTPBucket'],['cancel'],['viewCampaign'],['ShowRowData']],
				extIcon  :[['cog_add.png'],['application_form_edit.png'],['database_go.png'],['database_go.png'],['cancel.png'],['table_gear.png'],['table_go.png']],
				extText  :true,
				extInput :true,
				extOption:[{
						render	: 5,
						header	: 'Filter ',
						type	: 'combo',
						id		: 'combo_filter_campaign', 	
						name	: 'combo_filter_campaign',
						value	: status_campaign,
						store	: [{'2':'All'},{'1':'Active'},{'0':'Not Active'}],
						triger	: '',
						width	: 200
					}]
			});	
	    });
		
	var FTPBucket = function(){
		$('#main_content').load('act_ftp_bucket_nav.php');
	}	
		
	var datas={ 
		status_campaign	: status_campaign,
		order_by 		: '<?php echo $db->escPost('order_by');?>',
		type	 		: '<?php echo $db->escPost('type');?>'
	}
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	/* assign navigation filter **/
		
	var navigation = {
			custnav:'set_cmpupload_nav.php',
			custlist:'set_cmpupload_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
		
		
	var cbMoveOn = function(){
			var selfrom = doJava.dom('cmp_upload_product');
			var selto = doJava.dom('move_on_product');
			doJava.moveOptions(selfrom,selto)
		}
		
	//	
	
	var ShowRowData = function()
	{
		var array_campaign_data = doJava.checkedValue('check_list_cmp');
		if( array_campaign_data!='' )
		{
			var list_campaign_data  = array_campaign_data.split(',');
			if( list_campaign_data.length==1)
			{
				window.open('act_show_datail_campaign.php?list_campaign='+list_campaign_data);
			}
			else{
				alert('Please select a campaign!')
			}	
		}
		else{
			alert('Please Select Campaign !')
		}	
	}

	
	var cbRemoveOn = function(){
			var selfrom = doJava.dom('move_on_product');
			var selto = doJava.dom('cmp_upload_product');
			doJava.moveOptions(selfrom,selto)
		}
		
	var cbEvent = function(opt){
			if( opt!='' && opt==1){
				doJava.dom('cmp_upload_reason').disabled=false;
			}
			else{
				doJava.dom('cmp_upload_reason').disabled=true;
			}
		}
		
	var viewCampaign = function(){
			var status_campaign = doJava.dom('combo_filter_campaign').value;
			if( status_campaign )
			{
				datas = { status_campaign: status_campaign }
				extendsJQuery.construct(navigation,datas)
				extendsJQuery.postContent();
			}	
		}
		
	var cancel = function(){
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		
	var uploadData = function()
	{
		doJava.File = '../class/class.campaign.upload.php' 	
			$(function(){
				$('#span_top_nav').load(doJava.File+'?action=tpl_upload');
			});
		}
	
	var getProductCode = function(coreValue)
	{
		doJava.File = '../class/class.campaign.upload.php' 
		doJava.Params={
			action:'getProductByCore',
			cmp_core: coreValue
		}
		doJava.Load('html_product_code');
	}	
	
	var addCampaign = function(){
		doJava.File = '../class/class.campaign.upload.php' 
			$(function(){
				$('#span_top_nav').load(doJava.File+'?action=tpl_campaign');
			});
		}
		
	var EditData = function(){
	
		var ListCmp = doJava.checkedValue('check_list_cmp');
		var ArrCmp  = ListCmp.split(','); 
		if( ListCmp!=''){
			
			if( ArrCmp.length==1) {
				if( ArrCmp[0]!='')
				{
					doJava.File = '../class/class.campaign.upload.php' 
					$(function(){
							$('#span_top_nav').load(doJava.File+'?action=tpl_edit&CampaignNumber='+ArrCmp[0]);
						});
				}
			}
			else
				alert('Please Select One Rows !');
		}
		else 
			alert('Please Select Rows !')
	}	
	
/* extends date **/
 	
	var UpdateCmpUpload =function()
	{
		var CampaignNumber  = doJava.dom('cmp_upload_id').value;
		var CampaignExtends =  doJava.dom('extends_date').value;
		var expired_date =  doJava.dom('expired_date').value;
		var upload_cmp_type =  doJava.dom('cmp_camptype_id').value;
		var upload_cmp_built_type = doJava.dom('cmp_upload_builtype').value; 
		var upload_cmp_reupload = doJava.dom('cmp_upload_reupload').value;
		var upload_cmp_category = doJava.dom('cmp_upload_category').value;
		var upload_cmp_move_product =  doJava.getSelectValue('move_on_product');
		var upload_cmp_name = doJava.dom('upload_cmp_name').value;
		// var upload_cmp_camtype = doJava.dom('cmp_upload_camptype').value;
		var upload_cmp_system =  doJava.dom('cmp_upload_cignasystem').value;
		var upload_cmp_status = doJava.dom('cmp_upload_status').value;
		var upload_cmp_reason = doJava.dom('cmp_upload_reason').value;		
		
		if( (CampaignExtends!='') || (CampaignExtends=='')){
				doJava.File = '../class/class.campaign.upload.php' 
				doJava.Params = {
					action:'extends_date',
					CampaignNumber:CampaignNumber,
					CampaignExtends:CampaignExtends,
					upload_cmp_type : upload_cmp_type,
					upload_cmp_built_type : upload_cmp_built_type,
					upload_cmp_reupload : upload_cmp_reupload,
					upload_cmp_category : upload_cmp_category,
					upload_cmp_move_product : upload_cmp_move_product,
					upload_cmp_name : upload_cmp_name,
					expired_date : expired_date,
					upload_cmp_system : upload_cmp_system,
					upload_cmp_status : upload_cmp_status,
					upload_cmp_reason : upload_cmp_reason
				}
				
			var error = doJava.Post();
			alert(error);
			if( error==1)
			{
				if(CampaignExtends!='' ){
					alert("Suceeded, Update extends date !");
				}	
				else{
					alert("Suceeded, Update Campaign Information !");
				}
				extendsJQuery.postContent();
			}
			else
				alert("Failed, Update extends date !");
		}
		else{
			alert('Please Insert extends date!');
		}	
	}	

/* save on campaign to upload **/
	
	var saveCmpUpload = function()
	{	
			var upload_cmp_id = doJava.dom('cmp_upload_id').value;
			var upload_cmp_type =  doJava.dom('cmp_camptype_id').value;
			var upload_cmp_built_type = doJava.dom('cmp_upload_builtype').value; 
			var upload_cmp_date_expired = doJava.dom('expired_date').value; 
			var upload_cmp_reupload = doJava.dom('cmp_upload_reupload').value;
			var upload_cmp_category = doJava.dom('cmp_upload_category').value;
			var upload_cmp_move_product =  doJava.getSelectValue('move_on_product');
			var upload_cmp_name = encodeURIComponent(doJava.dom('upload_cmp_name').value);
			var upload_cmp_camtype = doJava.dom('cmp_upload_camptype').value;
			var upload_cmp_system =  doJava.dom('cmp_upload_cignasystem').value;
			var upload_cmp_status = doJava.dom('cmp_upload_status').value;
			var upload_cmp_reason = doJava.dom('cmp_upload_reason').value;
			var upload_cmp_extends_date = doJava.dom('extends_date').value;
			
			doJava.File = '../class/class.campaign.upload.php' 
			doJava.Params ={
				action : 'save_cmp_upload',
				upload_cmp_id : upload_cmp_id,
				upload_cmp_type : upload_cmp_type,
				upload_cmp_built_type : upload_cmp_built_type,
				upload_cmp_date_expired : upload_cmp_date_expired,
				upload_cmp_extends_date : upload_cmp_extends_date,
				upload_cmp_reupload : upload_cmp_reupload,
				upload_cmp_category : upload_cmp_category,
				upload_cmp_move_product : upload_cmp_move_product,
				upload_cmp_name : upload_cmp_name,
				upload_cmp_camtype : upload_cmp_camtype,
				upload_cmp_system : upload_cmp_system,
				upload_cmp_status : upload_cmp_status,
				upload_cmp_reason : upload_cmp_reason
			}
			
/* if to look parameter to send : doJava.MsgBox(); **/		

		var error = doJava.Post(); 
		//alert(error);
			if(error==1) { alert("Suceeded, Adding Campaign"); 
				extendsJQuery.postContent();
			}
			else{
				alert("Failed, Adding Campaign");
				return false;
			}
		}
		
/* if click aggree confirmation before **/
	
	var actionUpload =function(){
		AjaxUploads.UploadsConfig = { 
				actToUploads   : '../class/class.app.upload.php?'+doJava.ArrVal(),
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
		
		AjaxUploads.UploadsFile(); 
	}		
	
 /* if click prosess button by user **/
 
	var proses=function()
	{
		var act_file_name = doJava.dom('fileToupload').value
		var act_cmp_id  = doJava.dom('act_cmp_core').value
		var act_template_id = doJava.dom('template_name').value;
			if( act_file_name.length==0){ 
				alert('Filename is empty !'); 
				return false;
			}
			else if( act_template_id==''){ 
				alert('Template Name is empty !'); 
				return false;
			}
			else if( act_cmp_core.length ==0 ){ 
				alert('Campaign Core is empty !'); 
				return false;
			}
			else{
				if( confirm('Do you want to upload this file ?')){
					doJava.dom('loadings_gambar').style.display="block";
					doJava.Params ={
						action : 'upload',
						act_cmp_id : act_cmp_id,
						act_file_name:act_file_name,
						act_template_id: act_template_id
					}
					actionUpload();
				}
				else{ return false; }
		}	
	}	
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-campaign">&nbsp;&nbsp;Campaign Setup </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav" ></div>
					<div class="box-shadow" style="background-color:#FFFFFF;margin-top:10px;">	
						<div class="content_table"></div>
						<div id="pager"></div>
						<div id="ViewCmp"></div>
					</div>	
		</fieldset>	
		
	<!-- stop : content -->
	
	
	