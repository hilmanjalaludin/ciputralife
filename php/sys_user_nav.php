<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	SetNoCache();
	$filter ='';
	
	$sql = " SELECT  a.UserId as UserId
				FROM tms_agent a 
				 	left join cc_agent b on a.id=b.userid
					left join tms_agent_profile d on a.profile_id=d.id  ";
					
					
	
	$NavPages -> setPage(20);			 
	$NavPages -> query($sql);
    
	
	if( $db->havepost('UserId')) $filter = " AND ( a.UserId LIKE '%".$_REQUEST['UserId']."%' 
									            OR a.full_name LIKE '%".$_REQUEST['UserId']."%' 
												OR a.id LIKE '".$_REQUEST['UserId']."')";
	$NavPages -> setWhere($filter);
	
	
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
  	<script type="text/javascript">
	
		$(function(){
			$('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[['Enable'],['Disable'],['Add Agent'],['Remove'],['Edit Agent'],['Reset Password'],['Reset IP'],['PBX Register'],['Search'],[]],
				extMenu :[['enabledUser'],['disabledUser'],['addUser'],['disabledUser'],['changeGroup'],['resetPassword'],['resetIP'],['extRegiter'],['searchAgent'],[]],
				extIcon :[['accept.png'],['cancel.png'],['add.png'],['cross.png'],['group_edit.png'],['page_key.png'],['connect.png'],['phone_add.png'],['zoom.png'],[]],
				extText :true,
				extInput:true,
				extOption:[{
						render:8,
						type:'text',
						id:'v_cmp_user', 	
						name:'v_cmp_user',
						value:'<?php echo $db->escPost('UserId');?>',
						width:120
					},{
					  render:9,type:'label',label:'..',id:'load_images_id',name:'load_images_id'		
					}]
			});
		});
	
	var load_images_id = doJava.dom('load_images_id');
	
	/* ****** ***/	
		var datas = {
			UserId	 : '<?php echo $db->escPost('UserId');?>',
			order_by : '<?php echo $db->escPost('order_by');?>',
			type	 : '<?php echo $db->escPost('type');?>'
		}
	
		extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
			
	/* assign navigation filter **/
	
		var initClass  = '../class/class.user.system.php'
		var navigation = {
			custnav:'sys_user_nav.php',
			custlist:'sys_user_list.php'
		}
		
	/* assign show list content **/
		
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContentList();
			
			
	
		var UserByPrivileges = function(combo)
		{
			var user_mgr = doJava.dom('user_mgr');
			var user_spv = doJava.dom('user_spv');
			var cc_group = doJava.dom('cc_group');
			var uer_name = doJava.dom('textUserid'); 
			var user_fullname = doJava.dom('textFullname');
			
			if( combo.value==1 )
			{
				user_mgr.disabled=true;
				user_spv.disabled=true;
				user_mgr.selectedIndex=0;
				user_spv.selectedIndex=0;
				cc_group.selectedIndex=0
			}
			else if( combo.value==2 )
			{
				user_mgr.disabled=true;
				user_spv.disabled=true;
				user_mgr.selectedIndex=0;
				user_spv.selectedIndex=0;
			}
			else if( combo.value==3 )
			{
				user_mgr.disabled=false;
				user_spv.disabled=true;
				user_mgr.selectedIndex=0;
				user_spv.selectedIndex=0;
				
				
			}	
			else if( combo.value==4)
			{
				user_mgr.disabled=false;
				user_spv.disabled=false;			
				

			}
			else if( combo.value==5 ){
				user_mgr.disabled=1;
				user_spv.disabled=1;
				cc_group.disabled=1;
				user_mgr.selectedIndex=0;
				user_spv.selectedIndex=0;
				cc_group.selectedIndex=0;
			}
				
		}
		
	/* option ***/
	
		var getMGR = function(option)
		{
			var options = doJava.dom('user_mgr').options;
			var select  = doJava.dom('user_mgr');
			if( option.value!='' )
			{
				doJava.File = '../class/class.user.system.php';
				doJava.Params = { action:'get_manager', spv_id : option.value }
				var datas = doJava.eJson();
				 
					if( options.length > 0 )
					{
						for(var i = 0; i<options.length; i++)
						{	
							if( select.options[i].value == datas.mgr_id )
							{
								select.selectedIndex = i;
								select.disabled=true;
							}	
						}
					}	
			}
			else
			{
				select.selectedIndex = 0;
			}	
		}	
		
	/* enabled User */
	
		var enabledUser = function()
		{
			var userid = doJava.checkedValue('chk_menu');
			
				if( userid !=''){
					doJava.File = initClass
					doJava.Method = 'POST'
						doJava.Params ={
							action:'enable_user',
							userid:userid
						}
					var error = doJava.Post();
					if( error ==1){
						alert('Success, Enable User ');
						extendsJQuery.construct(navigation,'')
						extendsJQuery.postContentList();
					}
					else
						alert('Failed, Enable User ');
						
				}
				else{
					alert('Please select rows..!'); return false;
				}
		}
		
		var Skill = function()
		{
			$(function(){	
				$('#UserTpl').dialog({
					title:'User Skill',
					height:220,
					modal:true,
					position:['center','center'],
					width:350,
					buttons :{
						Save:function(){
							
							var user_skill_active 	= doJava.Value('user_skill_active');
							var user_skill_type 	= doJava.Value('user_skill_type'); 
							var user_skill_score 	= doJava.Value('user_skill_score');
				
								if( (user_skill_active!='') && (user_skill_type!='') && (user_skill_score!='') ){
									doJava.destroy()
									doJava.File = initClass
									doJava.Method ='POST'
										doJava.Params = {
											action : 'save_skill',
											user_skill_active : user_skill_active,
											user_skill_type : user_skill_type,
											user_skill_score : user_skill_score
										}
										
								
									var error = doJava.Post();
										if( error ==1){
											alert('Success, Add User Skill');
												$(this).dialog('close');
												$(this).empty()
												$(this).dialog('destroy')
													extendsJQuery.construct(navigation,'')
														extendsJQuery.postContent();
											
										}
										else{ alert('Failed, Add User Skill'); return false;}	
								}
								else{
									alert("Input Not Complete..!");
									return false;
								}	
						},
						
						Close:function(){
							$(this).dialog('close');
							$(this).dialog('destroy').remove();	
						}
					}
				}).load(initClass+'?action=user_skill');
		  });		
		}
	/* reset password */
	
		var resetIP = function()
		{
			var userid = doJava.checkedValue('chk_menu');
				if( userid !=''){
					doJava.destroy()
					doJava.File = initClass
					doJava.Method = 'POST'
						doJava.Params = {
							action:'reset_ip',
							userid:userid
						}
						
					if( confirm('Do you want to reset IP Location ?') ){			
						var error = doJava.Post();
						if( error ==1){
							alert('Success, Reset IP Location ');
							extendsJQuery.construct(navigation,'')
							extendsJQuery.postContentList();
						}
						else{
							alert('Failed, Reset IP Location ');
						}	
					}	
				}
				else{
					alert('Please select rows..!'); return false;
				}
		}
	/* register to PBX **/
	
		var extRegiter = function()
		{
			var userid = doJava.checkedValue('chk_menu');
				if( userid !=''){
					doJava.destroy()
					
					doJava.File = initClass
					doJava.Method = 'POST'
						doJava.Params = {
							action:'register_pbx',
							userid:userid
						}
						
					if( confirm('Do you want to User Register In PBX ?') )
					{
						load_images_id.innerHTML = "<span style='color:red;'><img src='../gambar/loading.gif' height='15'> Please wait...</span>";
							var error = doJava.eJson();
							if( error.datas.result!=0 )
							{
								var list_information = '';
								for( var i in error.datas )
								{
									list_information+=error.datas[i];
								}
								alert(list_information)
								load_images_id.innerHTML = "";
							}
							else{
								load_images_id.innerHTML = "";
							}	
					}	
				}
				else{
					alert('Please select rows..!'); return false;
				}
		}
		
		var resetPassword = function(){
			var userid = doJava.checkedValue('chk_menu');
				if( userid !=''){
					doJava.destroy()
					doJava.File = initClass
					doJava.Method = 'POST'
						doJava.Params = {
							action:'reset_password',
							userid:userid
						}
						
					if( confirm('Do you want to reset user password to ( 1234 ) ?') ){			
						var error = doJava.Post();
						if( error ==1){
							alert('Success, Reset Password User ');
							extendsJQuery.construct(navigation,'')
							extendsJQuery.postContentList();
						}
						else{
							alert('Failed, Reset Password User ');
						}	
					}	
				}
				else{
					alert('Please select rows..!'); return false;
				}
		}
		
	/* disabled User */
	
		var disabledUser = function(){
			var userid = doJava.checkedValue('chk_menu');
				if( userid !=''){
					doJava.destroy()
					doJava.File = initClass
					doJava.Method = 'POST'
						doJava.Params ={
							action:'disable_user',
							userid:userid
						}
							
					var error = doJava.Post();
					
					if( error ==1){
						
						extendsJQuery.construct(navigation,'')
						extendsJQuery.postContentList();
					}
					else
						alert('Failed,Disable User ');
						
				}
				else{
					alert('Please select rows..!'); return false;
				}
		}
	
	/* removal user */
		var removeUser = function(){
			var userid = doJava.checkedValue('chk_menu');
				if( userid !=''){
					doJava.destroy()
					doJava.File = initClass
					doJava.Method = 'POST'
						doJava.Params ={
							action:'remove_user',
							userid:userid
						}
					if( confirm('Do you want to remove this user ?') ){	
						var error = doJava.Post();
						if( error ==1){
							alert('Success, Remove User ');
							extendsJQuery.construct(navigation,'')
							extendsJQuery.postContentList();
						}
						else{ alert('Failed, Remove User '); }
					}	
				}
				else{
					alert('Please select rows..!'); return false;
				}
		}
		
	/* add users **/
	
		var addUser = function(){
		  $(function(){	
			
			$('#UserTpl').dialog({
				title:'Add User',
				height:380,
				width:350,
				modal:true,
				buttons :{
					Save:function(){
						var userid 	  = doJava.dom('textUserid').value;
						var fullname  = doJava.dom('textFullname').value;
						var user_mgr  = doJava.dom('user_mgr').value;
						var user_spv  = doJava.dom('user_spv').value;
						var profile   = doJava.dom('user_profile').value;
						var cc_group  = doJava.dom('cc_group').value;
						var user_telphone = doJava.dom('user_telphone').value;
						var textAgentcode = doJava.dom('textAgentcode').value;
						
						
							if( (userid!='') && (fullname!='') && (profile!='') ){
								doJava.destroy()
								doJava.File = initClass
								doJava.Method ='POST'
									doJava.Params = {
										action	 : 'add_user',
										userid	 : userid,
										fullname : fullname,
										user_mgr : user_mgr,
										user_spv : user_spv,
										profile  : profile,
										cc_group : cc_group,
										user_telphone : user_telphone,
										textAgentcode : textAgentcode
									}
									
							
								var error = doJava.Post();
									if( error ==1){
										alert('Success, Add User');
										    $(this).dialog('close');
											$(this).empty()
											$(this).dialog('destroy')
												extendsJQuery.construct(navigation,'')
												extendsJQuery.postContent();
										
									}
									else{ alert('Failed, Add User'); return false;}	
							}
							else{
								alert("Input Not Complete..!");
								return false;
							}	
					},
					close: function(){
					  $(this).empty();	
					   $(this).dialog("destroy");
					}
				}
			}).load(initClass+'?action=adduserTpl');
		  });		
		}
		
		var changeGroup = function(){
			var userid = doJava.checkedValue('chk_menu');
			if( userid!=''){
			  $(function(){
				$('#UserTpl').dialog({
					title:'Change User Group',
					height:380,
					width:350,
					modal:true,
					buttons : {
						Save:function(){
							var hiddenUserId = 	doJava.dom('UserId').value;
							var userid 	= doJava.Value('textUserid');
							var mgrid = doJava.Value('user_mgr');
							var spvid = doJava.Value('user_spv');
							var fullname = doJava.Value('textFullname');
							var privilges= doJava.Value('user_profile');
							var cc_group = doJava.Value('cc_group');
							var user_telphone = doJava.dom('user_telphone').value;
							var textAgentcode = doJava.dom('textAgentcode').value;
						
							if( (userid !='') && (fullname!='') ){
								 if( doJava.destroy()){
									doJava.File   = initClass;
									doJava.Method = 'POST';
									doJava.Params = {
											action: 'update_group',
											userid: userid,
											mgrid: mgrid,
											spvid: spvid,
											fullname: fullname,
											privilges: privilges,
											cc_group:cc_group,
											user_telphone : user_telphone,
											textAgentcode : textAgentcode,
											hiddenUserId : hiddenUserId	
										
										}
								 }
								 
								var error = doJava.Post();
								if( error ==1) {
									alert('Success, Edit User');
									$(this).dialog('close');
									$(this).empty()
									$(this).dialog('destroy')
									extendsJQuery.construct(navigation,'')
									extendsJQuery.postContent();
									//extendsJQuery.pageContent(2)
								}
								else
									alert('Failed, Edit User');	
							}
							else{
								alert('Input Not Complete..!'); 
								return;
							}	
								
						},
						close:function(){
							$(this).dialog('close');
							$(this).empty()
							$(this).dialog('destroy')	
						}
					}
				}).load(initClass+'?action=groupTpl&userid='+userid);	
			  });	
			}
			else
				alert("Please select Rows..!");
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
		<legend class="icon-userapplication">&nbsp;&nbsp;User Application </legend>
			<div id="toolbars" class="toolbars"></div>
			<div class="content_table"></div>
			<div id="pager"></div>
			<div id="UserTpl"></div>
	</fieldset>	
	