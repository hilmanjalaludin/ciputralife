<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/lib.form.php");
	
	class MenuAssign extends mysql{
		
		var $action;
		var $Form;
		
		function __construct(){
			parent::__construct();
			$this->action = $this -> escPost('action');
			$this -> Form = new jpForm(true);
		}
		
		function initMenu(){
			if( $this->havepost('action')):
				switch($this->action):
					case 'assign_menu'	: $this -> MenuAssignTo(); 		break;
					case 'disabled_menu': $this -> MenuDisabled(); 		break;
					case 'enabled_menu'	: $this -> MenuEnabled(); 		break;
					case 'show_menu'	: $this -> MenuShow(); 			break;
					case 'remove_menu'	: $this -> removeMenu(); 		break;
					case 'group_menu'	: $this -> groupMenu(); 		break;
					case 'update_group' : $this -> updateMenuGroup(); 	break;
					case 'menu_edit_tpl': $this -> EditMenuTpl(); 		break;
					case 'update_menu'  : $this -> actionUpdate(); 		break;
					case 'menu_add_tpl' : $this -> AddMenu(); 			break;
					case 'act_add_menu' : $this -> ExecuteMenu(); 			break;
					
				endswitch;
			endif;
		}
		
		function ExecuteMenu()
		{
			$sql = array
				(
					'menu'=> $_REQUEST['menu_name_add'], 
					'file_name'=> $_REQUEST['menu_filename_add'],
					'group_menu' => $_REQUEST['menu_group']
				);
			
			$qry = $this -> set_mysql_insert('tms_application_menu',$sql);
			if( $qry ) echo 1;
			else echo 0;
		}
		
		function ScandirSystem()
		{
			$datas = array();
			foreach(scandir('../php/', 1) as $key => $name )
			{
				$datas[$name] = $name;
			}
			
			return 	$datas;
		}
		
	/** get group menu Aktive **/

		function getGroupMenuActive()
		{
			$sql = "select a.GroupId, a.GroupName from tms_group_menu a where a.GroupShow=1 ORDER BY a.GroupOrder ASC ";
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['GroupId']] = $rows['GroupName']; 
			}
			
			return $datas;
		}
		
		function AddMenu(){
			$this ->setCss(); 
			
			?>
			<div class="box-shadow">
				<table border=0 cellpadding="12px;" >
					<tr>
						<td class="text_header"> Menu Name </td> 
						<td><?php $this -> Form->jpInput('menu_name_add','input_text'); ?></td>
					</tr>	
					<tr>
						<td class="text_header">File Name</td> 
						<td><?php $this -> Form->jpCombo('menu_filename_add','select',$this->ScandirSystem()); ?></td>
					</tr>	
					
					<tr>
						<td class="text_header">Group Menu</td> 
						<td><?php $this -> Form->jpCombo('menu_group','select',$this->getGroupMenuActive() ); ?></td>
					</tr>	
					
					<tr>
						<td>&nbsp;</td>
						<td><?php $this -> Form->jpLink('saveMenu','sbutton','javascript:void(0);','<span>Save Menu</span>','onClick="SaveMenu();"');?></td>
					</tr>
				</table>
			</div>	
			
			<?php
		}
		
		private function getMenu($user){
			if($user!=''){
				$menu  = $this->valueSQL("select a.menu from tms_agent_profile a where a.id='".$user."'");
				return explode(",",$menu);
			}
		}
		
		
		
		
		
		function updateMenuGroup(){
			$group = $this->escPost('group');
			$menu  = $this->escPost('menu');
			
			
			$sql  = " Update tms_application_menu SET group_menu ='$group'  where id='$menu' ";
		
			$qry  =	 $this->execute($sql,__FILE__,__LINE__);	
			if( $qry ):
				echo $this->valueSQL("select GroupName from tms_group_menu where GroupId='$group'");
			endif;	
						
		}
		
		function groupMenu(){
			$sql = "select * from tms_group_menu a order by a.GroupId asc";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			while( $row = $this->fetchrow($qry)){
				$datas[$row->GroupId] = $row->GroupName;
			}
			
			if(is_array($datas)):
				echo "<select name='group_name' onchange=\"updateMenu(this.value,'".$this->escPost('menuid')."');\" id='group_name' style='border:1px solid #dddddd;color:red;font-size:11px;'>".
					 "<option value=''> ( Choice ) </option>";
					foreach( $datas as $key=>$value){
						echo "<option value=\"{$key}\">{$value}</option>";
					}
				echo "</select>";	
					
			endif;
		}
		
		function removeMenu(){
			$menu = $this -> escPost('menuid');
			$user = $this -> escPost('user');
			
			if(!empty($menu) && !empty($user) ):
				$menus ='';
				$init_menu =  $this->getMenu($user);
				
				$i=0; 
				foreach($init_menu as $key=>$value):
					if($menu <> $value):
						$menus[$i] = $value;
						$i++;
					endif;
				endforeach;
				
				$datas = array('menu' => $this->arrayToText($menus));
				$where = array('id' => $user);
				
				$res   = $this->set_mysql_update('tms_agent_profile',$datas, $where);
				if( $res ) 
					echo 1;
				else 
					echo 0;
				
			else :
				echo 0;
			endif;	
			
		}
			
	
	function actionUpdate()
	{		
			$sql =array
				(
					'menu'=> $this->escPost('menu_name_edit'),
					'file_name' => $this->escPost('menu_filename_edit'),
					'group_menu' => $this->escPost('menu_group_edit')
				);
			
			$where= array('id'=>$this->escPost('menu_edit_id'));
			
			$qry = $this ->set_mysql_update('tms_application_menu',$sql,$where);
			if( $qry ) echo 1;
			else echo 0;

	}
	
		private function getMenuDetail(){
			$sql = "Select * from tms_application_menu a where a.id='".$this->escPost('menu_id')."'";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			if( $qry && $row=$this->fetchrow($qry)){
				return $row;
			}
		}
		
	/* function set style css **/
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:190px;font-size:11px;height:20px;background-color:#fffccc;}
					.input_text { border:1px solid #dddddd;width:190px;font-size:11px;height:16px;background-color:#fffccc;}
					.text_header { text-align:right;color:red;font-size:12px;}
					.select_multiple { border:1px solid #dddddd;height:100px;font-size:11px;background-color:#fffccc;}
				</style>
				
			<!-- stop: css -->
		<?php }
		
		function EditMenuTpl(){ 
			$this ->setCss(); 
			$EditMenu = $this -> getMenuDetail();
		?>
			<div class="box-shadow">
				<input type="hidden" name="menu_edit_id" id="menu_edit_id" value="<?php echo $this->escPost('menu_id'); ?>">
				<table border=0 cellpadding="12px;" >
						<tr>
							<td class="text_header"> Menu Name </td> 
							<td><input type="text" name="menu_name_edit" id="menu_name_edit" class="input_text" value="<?php echo $EditMenu->menu; ?>"></td>
						</tr>	
						<tr>
							<td class="text_header">File Name</td> 
							<td><?php $this -> Form->jpCombo('menu_filename_edit','select',$this->ScandirSystem(),$EditMenu->file_name); ?></td>
						</tr>
						<tr>
							<td class="text_header">Group Menu</td> 
							<td><?php $this -> Form->jpCombo('menu_group_edit','select',$this->getGroupMenuActive(),$EditMenu->group_menu); ?></td>
						</tr>		
						<tr>
							<td>&nbsp;</td>
							<td><a href="javascript:void(0);" class="sbutton" onclick="UpdateMenu();"><span>&nbsp;Update Menu</span></a></td>
						</tr>
					</table>
			</div>	
			
		 
		<?php }
		
		function MenuShow(){
				$menu = $this->getMenu($this->escPost('user'));
				echo "<select name='avail_menu' id='avail_menu' style=\"height:180px;width:200px;font-size:12px;COLOR:red;\" multiple>";
				foreach($menu as $key=>$value): 
						echo "<option value=\"{$value}\">".$this->MenuName($value)."</option>";
				endforeach;
				echo "</select>";
		}
		
		
		private function MenuName($menu){
			$ret = $this->valueSQL("select menu from tms_application_menu where id=$menu");
			if($ret) return $ret;
			else return null;
		}
		
		function arrayToText($array){
			$str='';
			if(is_array($array)){
				foreach($array as $key=>$value){
					$str.=",".$value;
				}
			}
			
			return substr($str,1,strlen($str));
		}
		
		function MenuAssignTo(){
		
			$assignto = explode(',',$this -> escPost('assignto'));
			$menuid = explode(',',$this -> escPost('menuid'));
	
			$i=0;
			foreach($assignto as $key => $user ){
			
				$aArray = $this->getMenu($user);
				$result = array_merge((array)$aArray, (array)$menuid);
				$Totext = $this->arrayToText($result);
				$datas  = array('menu'=>$Totext);
				$where  = array('id'=>$user);
				$res    = $this->set_mysql_update('tms_agent_profile',$datas, $where);
				if( $res ) $i+=1;
			}
			
			if( $i >0 ) echo 1;
			else echo 0;
		}
		
		function MenuDisabled(){
			$menu_id = explode(",",$this->escPost('menuid'));
			$i=0;
			foreach($menu_id as $key=>$menu_id){
				$where = array('id'=>$menu_id );
				$datas = array('flag'=>0);
				$res   = $this->set_mysql_update('tms_application_menu',$datas, $where);
				if( $res ) $i++;	
			}
			
			if( $i >0 ) echo 1;
			else echo 0;
		}
		
		function MenuEnabled(){
			$menu_id = explode(",",$this->escPost('menuid'));
			$i=0;
			foreach($menu_id as $key=>$menu_id){
				$where = array('id'=>$menu_id);
				$datas = array('flag'=>1);
				$res = $this->set_mysql_update('tms_application_menu',$datas, $where);
				if( $res ) $i++;	
			}
			
			if( $i>0 ) echo 1;
			else echo 0;
		}
		
		
	}
	
	$Menu = new MenuAssign();
	$Menu -> initMenu();
?>