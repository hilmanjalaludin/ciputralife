<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	
	$sql = " select * from tms_group_menu ";
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	
	
	
	function getGroupUser(){
		global $db;
		$sql =" select a.id,a.name from tms_agent_profile a ";
		$qry = $db -> execute($sql,__FILE__,__LINE__);
		while( $row = $db ->fetchrow($qry) ){
			$datas[$row -> id] = $row->name;
		}
		
		return "[".json_encode($datas)."]";
	
	}
	
	
	/** user group **/
	
	
?>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		
		var storeGroup = <?php echo getGroupUser(); ?>;
		$(function(){
			// jQuery('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[['Enable'],['Disable'],['Add Group Menu'],['Remove Group Menu'],[''],['User Group Menu']],
				extMenu :[['enabledGroup'],['disabledGroup'],['addGroup'],['removeGroup'],[''],['userMenuGroup']],
				extIcon :[['accept.png'],['cancel.png'],['add.png'],['cross.png'],[''],['group_edit.png']],
				extText :true,
				extInput:true,
				extOption:[{
						render:4,
						type:'combo',
						id:'combo_group', 	
						name:'combo_group',
						triger:'',
						header:'User Privileges ',
						store:storeGroup,
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
			custnav:'sys_mgroup_nav.php',
			custlist:'sys_mgroup_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		
		var userMenuGroup = function(){
		
			var GroupId 	= doJava.checkedValue('chk_menu')
			var ComboGroup  = doJava.dom('combo_group').value
			if( GroupId!='' && ComboGroup!=''){
			doJava.File = '../class/class.group.menu.php' 
		
				doJava.Params = {
						action:'add_menu_user',
						ComboGroup:ComboGroup,
						GroupId:GroupId
				}
			
				var error = doJava.Post();
					if( error ==1){
						alert('Success, Assign Group Menu');
						 extendsJQuery.postContent();
					}
					else{
						alert('Failed, Assign Group Menu')
						return;
					}
			}
			else {
				alert('Please select rows and User Privileges..!'); return;
			}	
		
		}
		
		var enabledGroup = function(){
			var GroupId = doJava.checkedValue('chk_menu')
			if( GroupId!=''){
			doJava.File = '../class/class.group.menu.php' 
		
				doJava.Params = {
						action:'enable_group',
						GroupId:GroupId
				}
			
				var error = doJava.Post();
					if( error ==1){
						alert('Success, Enabled Group Menu');
						 extendsJQuery.postContentList();
					}
					else{
						alert('Failed, Enabled Group Menu')
						return;
					}
			}
			else {
				alert('Please select rows..!'); return;
			}	
		}
		
		var disabledGroup = function(){
			var GroupId = doJava.checkedValue('chk_menu')
			if( GroupId!=''){
			doJava.File = '../class/class.group.menu.php' 
		
				doJava.Params = {
						action:'disable_group',
						GroupId:GroupId
				}
			
				var error = doJava.Post();
					if( error ==1){
						alert('Success, Disabled Group Menu');
						 extendsJQuery.postContentList();
					}
					else{
						alert('Failed, Disabled Group Menu')
						return;
					}
			}
			else {
				alert('Please select rows..!'); return;
			}	
		}
		
		var removeGroup = function(){
			var GroupId = doJava.checkedValue('chk_menu')
			if( GroupId!=''){
			doJava.File = '../class/class.group.menu.php' 
		
				doJava.Params = {
					action:'remove_group',
					GroupId:GroupId
				}
			
				if(confirm('Do you want to remove this rows ?')){
					var error = doJava.Post();
					if( error ==1){
						alert('Success, Remove Group Menu');
						$(this).dialog('close');
						$(this).empty()
						$(this).dialog('destroy')
						extendsJQuery.postContent();
					}
					else{
						alert('Failed, Remove Group Menu'); return;
					}
				}
			}
			else {
				alert('Please select rows..!'); return;
			}	
		}
		
		var addGroup = function(){
		  $(function(){
			$("#dialogMenu").dialog({
				title:'Add Group Menu',
				height:200,
				width:400,
				buttons: {
					Close: function() {
						$(this).dialog('close');
						$(this).empty()
						$(this).dialog('destroy')
					},
					Save: function() {
					doJava.File = '../class/class.group.menu.php' 
		
						doJava.Params={
							action:'save_menu_group',
							groupname: doJava.dom('groupIdText').value,
							groupdesc: doJava.dom('groupDescText').value,
						}
						doJava.MsgBox();
						var error = doJava.Post();
							if( error==1){
							alert('Success, Save Group Menu');
								$(this).dialog('close');
								$(this).empty()
								$(this).dialog('destroy')
								extendsJQuery.postContent();
							}
					}
				}
			}).load(doJava.File+'?action=show_form');
		  });	
		}
	</script>
	<div id="toolbars"></div>
	<div class="content_table"></div>
	<div id="pager"></div>
	<div id="dialogMenu"></div>
	
	