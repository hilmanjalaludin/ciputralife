<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	
	$sql = " select a.*, b.* from tms_application_menu a 
				left join tms_group_menu b on b.GroupId=a.group_menu";
	
	$NavPages -> setPage(8);			 
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	
	
	/** user group **/
	
	function userGroup(){
		global $db;
		$datas= array();
			$sql = "select * from tms_agent_profile ";
			$qry = $db->execute($sql,__FILE__,__LINE__);
			while( $row = $db->fetchrow($qry)):
				$datas[$row->id] = $row->name; 
			endwhile;
			
		return $datas;	
	
	}	
	
	
	
	
?>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
	
		$(function(){
			$('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[['Enable Menu'],['Disable Menu'],['Edit Menu'],['Add Menu'],['Close']],
				extMenu :[['enableMenu'],['disableMenu'],['EditMenu'],['AddMenu'],['ClearMenu']],
				extIcon :[['accept.png'],['cancel.png'],['application_form_edit.png'],['add.png'],['cross.png']],
				extText :true
			});
		});
		
		var datas={}
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'sys_menu_nav.php',
			custlist:'sys_menu_list.php'
		}
		
	/* assign show list content **/
		
			extendsJQuery.construct(navigation,'')
			extendsJQuery.postContentList();
		
	/* start : enable menu if click current usergroup **/	
	
		var initClass = '../class/class.menu.assign.php';
		var initUser  = 0;
		
		var assignMenu = function(){
			var menu_id = doJava.checkedValue('chk_menu');
			var assign_id =  doJava.SelArrVal('userGroupText');
			if( menu_id!=''){
				doJava.File = initClass
				doJava.Params ={
					action:'assign_menu',
					menuid:menu_id,
					assignto:assign_id
				}
				
				//doJava.MsgBox();
				var error = doJava.Post();
					if( error==1) { 
						alert("Success,Assign Menu");
						(assign_id?showMenu(assign_id):null);
					}
					else{
						alert("Failed, Assign Menu");
					}
			}
		}
  /* update **/

	var UpdateMenu = function(){
		var menu_edit_id = doJava.dom('menu_edit_id').value;
		var menu_name_edit = doJava.dom('menu_name_edit').value;
		var menu_filename_edit = doJava.dom('menu_filename_edit').value;
		var menu_group_edit = doJava.dom('menu_group_edit').value;
		
		
			doJava.File = initClass;
			doJava.Params = {
				action:'update_menu',
				menu_edit_id : menu_edit_id,
				menu_name_edit : menu_name_edit,
				menu_filename_edit : menu_filename_edit,
				menu_group_edit : menu_group_edit
			}
			var error = doJava.Post();
			if( error==1){
				alert('Success, Update Menu!')
				extendsJQuery.postContent();
			}
			else
				alert('Failed, Update Menu!')
			
	}
	
  /* add menu **/
  
	var AddMenu  = function(){
		doJava.File=initClass;
		doJava.Params= {
			action:'menu_add_tpl'
		}	
		doJava.Load('top_header');
	}

	
	var SaveMenu = function(){
		var menu_name_add = doJava.dom('menu_name_add').value;
		var menu_filename_add = doJava.dom('menu_filename_add').value;
		var menu_group	= doJava.dom('menu_group').value;
		doJava.File = initClass;
			doJava.Params = {
				action:'act_add_menu',
				menu_group : menu_group,
				menu_name_add : menu_name_add,
				menu_filename_add : menu_filename_add
				
			}
			var error = doJava.Post();
			if( error==1){
				alert('Success, Save Menu!')
				extendsJQuery.postContent();
			}
			else
				alert('Failed, Save Menu!')
			
			
	}
	
	
  /* edit menu **/
  
	var EditMenu = function(){
		var menu_id = doJava.checkedValue('chk_menu');
		var is_menu =  menu_id.split(',');
		if( menu_id!='' ){
			if( is_menu.length==1){
				doJava.File=initClass;
				doJava.Params= {
					action:'menu_edit_tpl',
					menu_id:is_menu[0]
				}	
				doJava.Load('top_header');
			}
			else
				alert('Please select one rows!')
		}
		else
			alert("Please select rows!");
		
	}

	var ClearMenu = function(){
		doJava.dom('top_header').innerHTML='';
	}		
		
	/* start : enable menu if click current usergroup **/	
	
		var disableMenu = function(){
				var menuid = doJava.checkedValue('chk_menu')
				doJava.File = initClass
				
				doJava.Method='POST';
					doJava.Params ={
						action:'disabled_menu',
						menuid: menuid
					}
					
				if( menuid!=''){	
					var error = doJava.Post();
						if( error==1){ 
							alert("Success, Disabled Menu");
							extendsJQuery.construct(navigation,'')
							extendsJQuery.postContentList();
						}
						else{
							alert("Failed, Disabled Menu");
						}
				}
				else{
					alert("Selected Rows..!");
					return false;
				}	
				
		}
		
	/* start : enable menu if click current usergroup **/	
		
		var enableMenu = function(menuid){
			var menuid = doJava.checkedValue('chk_menu')
				doJava.File = initClass
					doJava.Params ={
						action:'enabled_menu',
						menuid:menuid
					}
			if( menuid!=''){		
				var error = doJava.Post();
					if( error==1){ 
						alert("Success, Enable Menu");
						extendsJQuery.construct(navigation,'')
						extendsJQuery.postContentList();
					}
					else{
						alert("Failed, Enabled Menu");
					}
			}
			else{
				alert("Selected Rows..!"); return false;
			}				
				
		}
	/* start : show menu if click current usergroup **/
	
		var showMenu = function(userGroup){
			initUser = userGroup
			doJava.File = initClass
			doJava.Params ={
					action:'show_menu',
					user:userGroup
				}
			doJava.Load('menu_available')	
		}
	
	/* start : remove menu if click current usergroup **/	
	
		var removeMenu = function(){
			
			var avail_menu = doJava.SelArrVal('avail_menu'); 
				doJava.File = initClass
					doJava.Params = {
						action:'remove_menu',
						user:initUser,
						menuid:avail_menu
					}
				if( confirm('Do you want to remove this menu..?')){
					var error = doJava.Post();
					if( error==1){
						alert("Success, Remove Menu");
							showMenu(initUser);
					}
					else
						alert("Failed, Remove Menu");
							showMenu(initUser);
				}
				else
					return false;
		}
		
	var choiceGroup = function(menu){
		doJava.File = initClass
			doJava.Params = {
				action:'group_menu',
				menuid:menu
			}
					
		doJava.Load('menu_'+menu);
	}	
	
	var updateMenu = function(group,menu){
		doJava.File = initClass
		doJava.Params ={
			action:'update_group',
			menu:menu,
			group:group
		} 
		doJava.Load('textm_'+menu);
		doJava.dom('menu_'+menu).innerHTML='';
	}
	
	</script>
<fieldset class="corner">
	<legend class="icon-menulist">&nbsp;&nbsp;Menu List </legend>
	<div id="toolbars" class="toolbars"></div>
	<div id="top_header" style="margin-top:10px;"></div>	
	<div class="content_table"></div>
	<div id="pager"></div>
	
	<fieldset id="userGroup" style="margin-left:6px;border:1px solid #ddd;margin-top:8px;">
		<legend style="color:red;font-size:12px;"> Action Option</legend>
		<table border=0>
			<tr>
				<td valign="top" ><p style="font-size:13px;">User Group</p></td>
				<td valign="top" > 
					<select name="userGroupText" id="userGroupText" onclick="showMenu(this.value);"
					style="width:300px;color:red;overflow:'hidden';
					font-size:12px;height:180px;border:1px solid #ddd;" multiple>
					
					<?php foreach(userGroup() as $menuId=>$menuName): ?>
						<option value="<?php echo $menuId;?>"> <?php echo $menuName;?></option>
					<?php endforeach; ?>	
					</select>
				</td>
				<td valign="top"><p style="font-size:13px;">&nbsp; Menu &nbsp;</p></td>
				<td valign="top"> 
				
					<div id="menu_available" style="padding:5px;border:1px solid #dddddd;"></div>
				</td>
				
				
			</tr>

			<tr>
				<td valign="top">&nbsp;</td>
				<td colspan="2">
				<a href="javascript:void(0);" class="sbutton" onclick="assignMenu();"><span>&nbsp;Assign</span></a> </td>
				<td>
				<a href="javascript:void(0);" class="sbutton" onclick="removeMenu();"><span>&nbsp;Remove</span></a> </td>
			</tr>	
		</table>
	</fieldset>	
	</fieldset>