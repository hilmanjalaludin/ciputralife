<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	class GroupMenu extends mysql{
		var $action;
		var $GroupId;
		function __construct(){
			parent::__construct();
			$this->action  = $this -> escPost('action');
			$this->GroupId = explode(',',$this -> escPost('GroupId'));
		}
		
		
		function initGroupMenu(){
			switch($this->action){
				case 'enable_group' 	: $this -> enableGroupMenu(); 	break;
				case 'disable_group'	: $this -> disableGroupMenu(); 	break;
				case 'remove_group' 	: $this -> removeGroupMenu(); 	break;
				case 'show_form'		: $this -> tplGroupMenu(); 		break;
				case 'save_menu_group'	: $this -> saveGroupMenu(); 	break;
				case 'add_menu_user'	: $this -> addGroupMenuUser(); 	break;
					
			}
		}
		
		function addGroupMenuUser(){
			$datas['menu_group'] = $this->escPost('GroupId');
			$where['id'] = $this->escPost('ComboGroup');
			$result = $this ->set_mysql_update('tms_agent_profile',$datas,$where);
			if( $result ) echo 1;
			else echo 0;
		}
		
		function saveGroupMenu(){
			$GroupName = $this -> escPost('groupname'); 
			$GroupDesc = $this -> escPost('groupdesc');
			
				$datas = array(
					'GroupName'=> $GroupName,
					'GroupShow'=> 0,
					'GroupDesc'=> $GroupDesc,
					'CreateDate'=> date('Y-m-d H:i:s'),
					'UserCreate'=> $this->getSession('username')
				);
				
				if( $this->set_mysql_replace('tms_group_menu',$datas)){
					echo 1;
				}
				else{ echo 0; }
		}
		
		function tplGroupMenu(){
			?>
				<table width="99%" cellpadding="9px;">
					<tr>
						<td width="20%">Group Name.</td>
						<td width="60%"><input type="text" id="groupIdText" value="" style="width:80%; height:18px;border:1px solid #dddddd;"></td>
					</tr>
					<tr>
						<td>Group Desc.</td>
						<td><input type="text" id="groupDescText" value="" style="width:80%; height:18px;border:1px solid #dddddd;"></td>
					</tr>
				</table>
			<?php
		}
		
		function enableGroupMenu(){
			$i=0;
			foreach($this->GroupId as $key=>$val){
				$where = array('GroupId'=>$val);
				$datas = array('GroupShow'=>1);
					if( $this->set_mysql_update('tms_group_menu',$datas,$where)):
						$i++;
					endif;
			}
			if( $i>0 ) : echo 1; 
			else : echo 0; endif;
		}
		
		function removeGroupMenu(){
			$i=0;
			foreach($this->GroupId as $key=>$val){
				$sql = "DELETE FROM tms_group_menu WHERE GroupId='$val'";
				$res = $this -> execute($sql,__FILE__,__LINE__); 
					if( $res ):
						$i++;
					endif;
			}
			if( $i>0 ) : echo 1; 
			else : echo 0; endif;
		}
		
		function disableGroupMenu(){
			$i=0;
			foreach($this->GroupId as $key=>$val){
				$where = array('GroupId'=>$val);
				$datas = array('GroupShow'=>0);
					if( $this->set_mysql_update('tms_group_menu',$datas,$where)):
						$i++;
					endif;
			}
			if( $i>0 ) : echo 1; 
			else : echo 0; endif;
		}
	}
	
	$GroupMenu = new GroupMenu();
	$GroupMenu->initGroupMenu();

?>