<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select * from t_gn_campaigngroup ";
	$where = " AND ( CampaignGroupCode LIKE '%".$db->escPost('cbFilter')."%' ".
			 " OR CampaignGroupName LIKE '%".$db->escPost('cbFilter')."%' )";
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere($where);

	
	//echo $NavPages ->query;
	
	/** user group **/
?>
	
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
	
	
		var datas={
			cbFilter:'<?php echo $db->escPost('cbFilter');?>'
		}
			extendsJQuery.totalPage = '<?php echo $NavPages ->getTotPages(); ?>';
			extendsJQuery.totalRecord = '<?php echo $NavPages ->getTotRows(); ?>';
		
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'set_cmpcore_nav.php',
			custlist:'set_cmpcore_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
		
		var viewCampaign = function(){
			datas = {
				cbFilter:doJava.dom('v_cmp').value
			}
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
		}
		
	/* ADD NEW CAMPAIGN */
	
		var addCampaign = function(){
			doJava.File = '../class/class.cmp.core.php' 
			$(function(){
				 $("#dialogCmp").dialog({
					title:'Add Campaign Core ',
					height:200,
					width:400,
					modal:true,
					buttons: {
						Close: function() {
							$(this).dialog('close');
							$(this).empty()
							$(this).dialog('destroy')
						},
						Save: function() {
							var text_cmp_id = doJava.dom('text_cmp_id').value
							var text_cmp_name = encodeURIComponent(doJava.dom('text_cmp_name').value)
							var select_cmp_status = doJava.dom('select_cmp_status').value
							
						/* cek valid input */
							
							if( text_cmp_id.length < 2 ) { doJava.dom('text_cmp_id').focus(); return false; }
							else if( text_cmp_name.length < 2 ) { doJava.dom('text_cmp_name').focus(); return false; }	
							else if( select_cmp_status.length=='' ) { doJava.dom('select_cmp_status').focus(); return false; }
							else{
								doJava.File = '../class/class.cmp.core.php'; 
								doJava.Params={
									action:'save_campaign_core',
									text_cmp_id: text_cmp_id,
									text_cmp_name:text_cmp_name,
									select_cmp_status:select_cmp_status
								}
							}
							
							var error = doJava.Post();
							
							if( error==1){
								alert('Success, Add Campaign Core');
									extendsJQuery.postContent();
										$(this).dialog('close');
										$(this).empty()
										$(this).dialog('destroy')	
							}else 
								alert('Failed, Add Campaign Core');
						}
					}
				}).load(doJava.File+'?action=tpl_campaign_core');
			});
		}
		
		var RenderValueCampaign=function(InputText)
		{
			doJava.dom('text_cmp_name').value = InputText.value.toUpperCase();
			InputText.value = InputText.value.toUpperCase();
		}
		
		
		var EditCampaignCore = function()
		{
			var CampaignCoreId = doJava.checkedValue('cmp_check_list');
			var CampaignCoreList = CampaignCoreId.split(',');
			
			doJava.File = '../class/class.cmp.core.php' 
			
			if( CampaignCoreId !='' ){
				if( CampaignCoreList.length==1 ){
					doJava.File = '../class/class.cmp.core.php'; 
					$(function(){
						 $("#dialogCmp").dialog({
							title:'Edit Campaign Core ',
							height:200,
							width:400,
							modal:true,
							buttons: {
								Close: function() {
									$(this).dialog('close')
									$(this).empty()
									$(this).dialog('destroy')
								},
								Update:function()
								{
									var CampaignCoreId = doJava.dom('CampaignCoreId').value
									var text_cmp_id = doJava.dom('text_cmp_id').value
									var text_cmp_name = doJava.dom('text_cmp_name').value
									var select_cmp_status = doJava.dom('select_cmp_status').value
									
								/* cek valid input */
									
									if( text_cmp_id.length < 2 ) { doJava.dom('text_cmp_id').focus(); return false; }
									else if( text_cmp_name.length < 2 ) { doJava.dom('text_cmp_name').focus(); return false; }	
									else if( select_cmp_status.length=='' ) { doJava.dom('select_cmp_status').focus(); return false; }
									else{
										doJava.File = '../class/class.cmp.core.php'; 
										doJava.Params={
											action:'update_campaign_core',
											text_cmp_id: text_cmp_id,
											campaign_core_id : CampaignCoreId,
											text_cmp_name:text_cmp_name,
											select_cmp_status:select_cmp_status
										}
											var error = doJava.eJson();
											if( error.result==1){
												$(this).dialog('close');
												$(this).empty()
												$(this).dialog('destroy')
												alert(error.message);
												$('#main_content').load('set_cmpcore_nav.php');
												
											}else 
												alert(error.message);
										//
									}
								}
							}
						}).load(doJava.File+'?action=edit_campaign_core&CampaignId='+doJava.Base64.encode(CampaignCoreId));
					});
				}
				else{
					alert('Please select one rows ');
					return false;
				}
			}
			else{
				alert('Please select a rows !'); 
				return false;
			}	
			
		}
		
		
	
	/* load jquery **/
	
		$(function(){
			// $('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Enable'],['Disable'],['Add Campaign Core'],['Edit Campaign Core'],['Search']],
				extMenu  :[['enableCore'],['disableCore'],['addCampaign'],['EditCampaignCore'],['viewCampaign']],
				extIcon  :[['accept.png'],['cancel.png'],['add.png'],['table_edit.png'],['zoom.png']],
				extText  :true,
				extInput :true,
				extOption:[{
						render:4,
						type:'text',
						id:'v_cmp', 	
						name:'v_cmp',
						value:'<?php echo $db->escPost('cbFilter');?>',
						width:200
					}]
			});
		});
		
		var enableCore=function(){
			var core = doJava.checkedValue('cmp_check_list');
			if(core!=''){
				doJava.File = '../class/class.cmp.core.php';
				doJava.Params ={
					action 	: 'enable_core',
					core	: core
				}
				var error = doJava.Post();
				//alert(error);
				if( error==1)
				{
					alert("Succeeded, Enable Campaign Core!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed, Enable Campaign Core!"); 
					return false; 
				}
			}
			else{
				alert("Please select Rows !")
			}
		}
		
		var disableCore=function(){
			var core = doJava.checkedValue('cmp_check_list');
			if(core!=''){
				doJava.File = '../class/class.cmp.core.php';
				doJava.Params ={
					action 	: 'disable_core',
					core	: core
				}
				var error = doJava.Post();
				//alert(error);
				if( error==1)
				{
					alert("Succeeded, Disable Campaign Core!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed, Disable Campaign Core!"); 
					return false; 
				}
			}
			else{
				alert("Please select Rows !")
			}
		}
	
	</script>
	
	<div id="toolbars"></div>
	<div class="content_table"></div>
	<div id="pager"></div>
	<div id="dialogCmp"></div>
	
	
	