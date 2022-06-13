<?php
	
	require("../fungsi/global.php");
	require("../sisipan/sessions.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.telnet.socket.php");
	require("../class/lib.form.php");
	
	class UserSystem extends mysql{
		var $action;
		var $userid;
		var $PBX;
		var $Form;
		
		function __construct(){
			parent::__construct();
			
			if( $this->havepost('action')):
				$this -> action 	= $this->escPost('action');
				$this -> userid   	= $this->escPost('userid');	
				$this -> Form 		= new jpForm(true);
				$this -> PBX	 	= new socketTelnet();
			endif;
		}
/*
 * secctin set class style CSS 
 * return @procedure
 */		
		private function css()
		{
			?>
				<style>
				.select_text{ border:1px solid red;width:220px;height:22px;color:red;}
				.input_text{ border:1px solid red;width:220px;height:18px;color:red;}
				.input_box{ border:1px solid red;width:220px;height:18px;color:red;}
				</style>
			<?php
		}
		
		
	/** main of classs ***/
	
		function initUserSystem()
		{
			if( !empty($this -> action))
			{
				switch($this -> action)
				{
					case 'enable_user'   : $this -> enableUser();    break;
					case 'disable_user'  : $this -> disableUser();   break;
					case 'remove_user'   : $this -> removeUser();    break;
					case 'change_user'   : $this -> changeUser();    break;
					case 'add_user'	     : $this -> addUser();       break;	
					case 'adduserTpl'    : $this -> addUserTpl();    break;	
					case 'groupTpl'	     : $this -> chgGroupTpl();   break;	
					case 'update_group'  : $this -> updateGroup();   break;
					case 'reset_password': $this -> resetPassword(); break;
					case 'reset_ip'		 : $this -> resetUserIP(); 	 break;
					case 'register_pbx'  : $this -> PBXRegister(); 	 break;
					case 'user_skill'	 : $this -> UserSkill(); 	 break;
					case 'save_skill'	 : $this -> SaveSkill(); 	 break;
					case 'get_manager'   : $this -> getManagerId();  break;
				}
			}
		}
		
	/** get getManagerId **/

		function getManagerId()
		{
			$sql = "select * from tms_agent a where a.UserId='".$_REQUEST['spv_id']."' ";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				echo json_encode( $qry -> result_first_assoc() );
			} 
		}
		
	/** function get id **/
		
		function getId($username)
		{
			$sql = " select a.UserId from tms_agent a where a.id='$username' ";
			$qry = $this -> query($sql);
			
			if( $qry -> result_num_rows() > 0 )
			{
				return $qry -> result_singgle_value();
			}
		}
		
	/* manager settings */
		
		function ManagerHostPBX()
		{
			$sql = "SELECT set_name, set_value FROM cc_settings WHERE set_modul = 'agent' and instance_id=0";
			$qry = $this -> query($sql);
			if( $qry -> result_num_rows() > 0 )
			{
				foreach( $qry ->result_array() as $rows)
				{
					if($rows[0] == "server.host"){
						$managerHost = $rows[1];
					}
					else if ($rows[0] == "server.port"){
						$managerPort = $rows[1];
					}
				}
				
				$datas['managerHost'] = $managerHost;
				$datas['managerPort'] =	$managerPort;
			}
			
			return $datas;
		}	
	
	/** save Skill **/
	
		function SaveSkill(){
		
			$sql = array
			(
				'agent' => $this->escPost('user_skill_active'), 
				'skill'=> $this->escPost('user_skill_type'), 
				'score'=> $this->escPost('user_skill_score')
			);
			
			$rs = $this->set_mysql_insert('cc_agent_skill',$sql);
			if( $rs ) echo 1;
			else echo 0;
		}
		
	/** get System Skill  **/	
		
		private function getSkillType()
		{
			$sql = "select a.id, a.description from cc_skill a order by a.id ASC";
			$qry = $this ->execute($sql,__file__,__LINE__);
			while( $row= $this ->fetchassoc($qry)){
				$datas[$row['id']] = $row['description']; 
			}
			return $datas;
		}
		
		
		private function getUserFullname($UserId){
		
			$sql = " select a.full_name from tms_agent a where a.UserId='$UserId'";
			$qry = $this -> query($sql);
			if( $qry -> result_num_rows() > 0 )
			{
				return $qry -> result_singgle_value();
			}
			else
				return false;
		}
		

	/** get User Active **/
		
		private function ActiveUser()
		{
			$sql = "select  b.id, concat(a.id,' - ', a.full_name) as name
					 from tms_agent a left join cc_agent b on a.id=b.userid
					where a.user_state=1";
					
			$qry = $this ->execute($sql,__file__,__LINE__);
			while( $row= $this ->fetchassoc($qry)){
				$datas[$row['id']] = $row['name']; 
			}
			return $datas;		
		}
	
		/** tpl Add skill user **/
	
		function UserSkill(){
			$this -> css();
		?>
			<table cellpadding="8px;" width="90%">
				<tr>
					<td width="20%" nowrap>User Active </td>
					<td width="70%">
						<?php $this->Form->jpCombo('user_skill_active','select_text',$this->ActiveUser());?>
					</td>
				</tr>
				<tr>
					<td nowrap>Skill Type </td>
					<td><?php $this->Form->jpCombo('user_skill_type','select_text',$this->getSkillType());?></td>
				</tr>
				<tr>
					<td nowrap>Score </td>
					<td><?php $this->Form->jpInput('user_skill_score','input_text',100);?></td>
				</tr>
			</table>
			<?php
		}
		
		private function ccAgentId($UserId)
		{
			$sql = " select b.id as cc_agentid, c.agent as skill_agent, c.score as skillScore from tms_agent a left join cc_agent b on a.id=b.userid
					  left join cc_agent_skill c on b.id=c.agent
						where a.UserId='$UserId'";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$agent = $this -> fetchassoc($qry);	
			
			return $agent;
		}
		
	/** returning pbxsyntax **********************/
	
		private function PBXSyntax( $CUserId='' )
		{
			if( $CUserId!='')
			{
				$ManagerSetting = $this -> ManagerHostPBX(); //get cc_agent.id by tms_agent Userid 
				$agentId = $this -> ccAgentId($CUserId);
				if( is_array( $ManagerSetting ) )
				{
					if( is_array( $agentId ) )
					{
						if( $agentId['skillScore']!='' && $agentId['skillScore']!=0)
						{
							$this -> PBX -> set_fp_server($ManagerSetting['managerHost'], 9800); //$ManagerSetting['managerPort']); // settle manager setting
							$this -> PBX -> set_fp_command("load-agent\r\n"."agent-id: ".$agentId['cc_agentid']."\r\n\r\n"); // rows
							if( $this -> PBX -> send_fp_comand() )
							{
								$datas[$CUserId]['OK'] = $this -> PBX -> get_fp_response();
							} 
						}
						else
							$datas[$CUserId]['SE'] = 1;
					}	
					else
						$datas[$CUserId]['AE'] = 1;
				}
			}
			else
			{
				$datas[$CUserId]['NU'] = 1;
			}
			
			
			return $datas;
		}
		
	/*** register pb return json string ***/
	
		function PBXRegister()
		{
			$total_register_success  = 0;
			$total_register_empty_skill = 0;
			$total_register_cc_agent = 0;
			$total_register_not_user = 0;
			
			$userid = explode(",",$this -> userid);
			
			$i = 0;
			foreach( $userid as $a => $b)
			{
				$rows = $this -> PBXSyntax($b);
				if( $rows[$b]['OK'])
				{ 
					$result[$i] = " Agent ( ".$this -> getUserFullname( $b )." ) Success register..!\n"; 
				}	
				else if( $rows[$b]['SE'])
				{
					$result[$i] = " Agent ( ".$this -> getUserFullname( $b )." ) not have skill agent..!\n";
				}	
				else if( $rows[$b]['AE'])
				{
					$result[$i] = " Agent ( ".$this -> getUserFullname( $b )." )  not in cc_agent table..!\n";
				}
				else{
					$result[$i] = " Agent ( ".$this -> getUserFullname( $b )." ) PBX Server Error Connection..!\n";
				}
				
				$i++;
			}
			
			if( $i > 0 ) {
				$json['datas'] = $result;
			}
			else{
				$json['datas']['result'] = 0;
			}	
			
			echo json_encode($json);
		}
		
	/////////////////////////////////////////////////////////////////////
	/*** register pbx return json string ***/
	
		function resetUserIP()
		{
			$userid = explode(",",$this -> userid);
			$i = 0;
			foreach( $userid as $a => $b)
			{
				$sql = "update tms_agent a SET a.logged_state='0', a.ip_address=null  where a.UserId ='$b'";
				if( $this->execute($sql,__file__,__line__)) $i++;	
			}
			
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
		}
	
	/** ############### **/
	
		function getUser()
		{
			$sql = "select *, a.id as nama_ol from tms_agent a 
						left join cc_agent b on a.id=b.userid
						left join cc_agent_group c on b.agent_group=c.id
						where a.UserId='".$this->escPost('userid')."'";
			//echo $sql;			
			$qry = $this -> query($sql);
			if( $qry -> result_num_rows() > 0 )
			{
				return $qry ->result_first_assoc();
			}	
		}
		
	/** get User MGR ***/
	
		function getMgr($user)
		{
			$sql = "SELECT a.UserId, a.full_name FROM tms_agent a
						left join tms_agent_group b on a.group_id=b.id 
						where 1=1 and a.handling_type ='2'";
			
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['UserId']] = $rows['full_name'];
			}	
			return $datas;
		}
		
	/** get User spv ***/
	
		function getSpv($user)
		{
			$sql = "SELECT a.UserId, a.full_name FROM tms_agent a left join tms_agent_group b on a.group_id=b.id 
					where 1=1 and a.handling_type ='3'";
						
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['UserId']] = $rows['full_name'];
			}	
			return $datas;
		}
		
		
	/* add user */
	
		function addUser()
		{
			$userid		= $this->escPost('userid');
			$fullname	= $this->escPost('fullname');
			$user_mgr	= $this->escPost('user_mgr');
			$user_spv	= $this->escPost('user_spv');
			$profile  	= $this->escPost('profile');
			$cc_group   = $this->escPost('cc_group');
			$telphone   = $this->escPost('user_telphone'); 
			$init_name  = $this->escPost('textAgentcode');
			
			$datas['id']		= $userid;
			$datas['full_name']	= $fullname; 
			$datas['init_name']	= $init_name;
			$datas['profile_id']= $profile; 
			$datas['handling_type']= $profile;
			$datas['mgr_id'] 	= $user_mgr;
			$datas['agency_id'] = 'AJMI';
			$datas['spv_id']	= $user_spv; 
			$datas['telphone']	= $telphone;
			$datas['password']	= md5('1234');
			
			if( $this->set_mysql_insert('tms_agent',$this -> contextENull($datas)) )
			{
				if( $profile==2 ) {
					$mgr_id = $this -> get_insert_id();
					$this -> execute("UPDATE tms_agent a SET a.mgr_id='$mgr_id' WHERE a.UserId='$mgr_id' LIMIT 1",__FILE__,__LINE__);
				}
				
				if( $profile==3 ) {
					$spv_id = $this -> get_insert_id();
					$this -> execute("UPDATE tms_agent a SET a.spv_id='$spv_id' WHERE a.UserId='$spv_id' LIMIT 1",__FILE__,__LINE__);
				}	
				
				$data['userid'] = $userid; 
				$data['name'] = $fullname;
				$data['password'] = md5('1234');
				$data['agent_group'] = $cc_group;
				$result = $this->set_mysql_insert('cc_agent',$data);	
				
				if( $result )
				{
					$Skill['agent'] = $this -> get_insert_id();
					$Skill['skill'] = 1;
					$Skill['score'] = 100;
					$this->set_mysql_insert('cc_agent_skill',$Skill,$Skill); 
				}
			}
			
			if( $result ) 
				echo 1;
			else 
				echo 0;	
		}
	
	/* enabled user on tms_agent **/
	
		function enableUser()
		{
			$userid = explode(",",$this -> userid);
			
			$i = 0;
			foreach( $userid as $a => $b){
				$datas = array('user_state'=>1);
				$where = array('UserId'=>$b);
				
				$result = $this->set_mysql_update("tms_agent", $datas,$where);
				if( $result ) $i++;	
			}
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
		}
		
	/* disabled user on tms_agent **/	
	
		function disableUser()
		{
			$userid = explode(",",$this -> userid);
			$i = 0;
			foreach( $userid as $a => $b){
				$datas = array('user_state'=>0);
				$where = array('UserId'=>$b);
				
				$result = $this->set_mysql_update("tms_agent", $datas,$where);
				if( $result ) $i++;	
			}
			
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
		}
	/* remove user on tms_agent and cc_agent **/
	
		function resetPassword()
		{
			$userid = explode(",",$this -> userid);
			$i = 0;
			foreach( $userid as $a => $b){
				$sql = "update tms_agent SET password=md5('1234') where UserId ='$b'";
				if( $this->execute($sql,__file__,__line__)) $i++;	
			}
			
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
		}	
		
	/* remove user on tms_agent and cc_agent **/
	
		function removeUser()
		{
			$userid = explode(",",$this -> userid);
			
			$i = 0;
			foreach( $userid as $a => $b){
				$tms_agent  = $this->valueSQL("select id from tms_agent where UserId ='$b'");
				$cc_user  = $this->valueSQL("select id from cc_agent where userid ='$tms_agent'");
				if( $cc_user!='' ):
					$result = $this -> execute("delete from tms_agent where UserId = '$b'",__FILE__,__LINE__);
					$result = $this -> execute("delete from cc_agent where id = '$cc_user'",__FILE__,__LINE__);
							  $this -> execute("delete from cc_agent_skill where agent='$cc_user'",__FILE__,__LINE__);	
				else :
					$r = "delete from tms_agent where UserId = '$b'";
					$result = $this->execute($r,__FILE__,__LINE__);
				endif;
				
				if( $result ) $i++;	
			}
			
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
		}
		
	/** private function  profile user */
		
		private function userProfile()
		{
			$sql = "Select * from tms_agent_profile order by id ASC ";
			$qry = $this -> query($sql);
			
			foreach($qry ->result_assoc() as $rows )
			{
				$datas[$rows['id']] = $rows['name']; 
			}
			return $datas;
		}

	/**** get cc group ******/
	
		private function ccGroup()
		{
			$sql = "select * from cc_agent_group order by id ASC";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['id']] = $rows['description'];
			}
			return $datas;
		}
		
		
	/* tpl add **/
		function addUserTpl()
		{ 
			$this -> css(); ?>
			
			<table cellpadding="6px;" width="90%">
				<tr>
					<td width="20%" nowrap>UserId </td>
					<td width="70%">
						<?php $this -> Form -> jpInput('textUserid','input_text',NULL);?>
					</td>
				</tr>
				<tr>
					<td nowrap>Fullname </td>
					<td width="70%"><?php $this -> Form->jpInput('textFullname','input_text',NULL);?></td>
				</tr>
				<tr>
					<td nowrap>Agent Code </td>
					<td width="70%"><?php $this -> Form->jpInput('textAgentcode','input_box',NULL);?></td>
				</tr>
				<tr>
					<td nowrap>Previleges </td>
					<td><?php $this -> Form->jpCombo('user_profile','select_text',$this -> userProfile(),NULL,'onchange="UserByPrivileges(this);"');?></td>
				</tr>
				<tr>
					<td nowrap>User Spv</td>
					<td>
						<?php $this -> Form->jpCombo('user_spv','select_text',$this -> getSpv(),NULL,'onchange="getMGR(this);"');?></td>
				</tr>
				<tr>
					<td nowrap>User Manager</td>
					<td><?php $this -> Form->jpCombo('user_mgr','select_text',$this -> getMgr(),NULL,NULL);?></td>
				</tr>
				<tr>
					<td nowrap>CC Group </td>
					<td><?php $this -> Form->jpCombo('cc_group','select_text',$this -> ccGroup(),NULL,NULL);?></td>
				</tr>
				<tr>
					<td nowrap>Telphone</td>
					<td><?php $this -> Form->jpCombo('user_telphone','select_text',array(1=>'Yes',0=>'No'),NULL,NULL);?></td>
				</tr>
				
			</table>
		<?php
		}
		
		
		
	/** tpl group */	
		function chgGroupTpl()
		{ 
			$aClass = $this -> getUser();
			$this -> css();
		?>
		
			<table cellpadding="6px;" width="90%">
				<tr>
					<td width="20%" nowrap>UserId </td>
					<td width="70%">
						<?php $this -> Form->jpInput('textUserid','input_text',$aClass['nama_ol']);?>	
						<?php $this -> Form -> jpHidden('UserId',$aClass['UserId']);?>
					</td>
				</tr>
				<tr>
					<td nowrap>Fullname </td>
					<td width="70%"><?php $this -> Form->jpInput('textFullname','input_text',$aClass['full_name']);?></td>
				</tr>
				<tr>
					<td nowrap>Agent Code </td>
					<td width="70%"><?php $this -> Form->jpInput('textAgentcode','input_box',$aClass['init_name']);?></td>
				</tr>
				<tr>
					<td nowrap>Previleges </td>
					<td><?php $this -> Form->jpCombo('user_profile','select_text',$this -> userProfile(), $aClass['profile_id'],'onchange="UserByPrivileges(this);"');?></td>
				</tr>
				
				<tr>
					<td nowrap>User Spv</td>
					<td>
					<?php $this -> Form->jpCombo('user_spv','select_text',$this -> getSpv(),$aClass['spv_id'],'onchange="getMGR(this);"');?></td>
				</tr>
				<tr>
					<td nowrap>User Manager</td>
					<td><?php $this -> Form->jpCombo('user_mgr','select_text',$this -> getMgr(),$aClass['mgr_id'],NULL);?></td>
				</tr>
				<tr>
					<td nowrap>CC Group </td>
					<td><?php $this -> Form->jpCombo('cc_group','select_text',$this -> ccGroup(),$aClass['agent_group'],NULL);?></td>
				</tr>
				<tr>
					<td nowrap>Telphone</td>
					<td><?php $this -> Form->jpCombo('user_telphone','select_text',array(1=>'Yes',0=>'No'),$aClass['telphone'],NULL);?></td>
				</tr>
				
			</table>
			
		
		<? }
		
	function getUserTMP($UserId=0)
	{
		$sql = " select a.id from tms_agent a where a.UserId='$UserId'";
		$qry =  $this -> query($sql);
		if(!$qry -> EOF() ){
			return $qry -> result_get_value('id');
		}
		else
			return NULL;
	}	
		
		
	/** group update **/
	
		function updateGroup()
		{
			$userid	 	= $this -> escPost('userid');
			$mgrid	 	= $this -> escPost('mgrid');
			$spvid	 	= $this -> escPost('spvid');
			$fullname 	= $this -> escPost('fullname');
			$privilges	= $this -> escPost('privilges');
			$cc_group	= $this -> escPost('cc_group');
			$telphone   = $this -> escPost('user_telphone'); 
			$init_name  = $this -> escPost('textAgentcode');
			$UserId 	= $this -> escPost('hiddenUserId');
			
			
			if( !empty($userid) )
			{	
				if( $privilges==2) $mgrid = $this ->getId($userid);
				if( $privilges==3) $spvid = $this ->getId($userid);
				
				$getUserTMP = $this -> getUserTMP($UserId);
				$sql = "UPDATE tms_agent a SET
							a.id = '$userid',
							a.mgr_id=".($mgrid?$mgrid:'NULL').", 
							a.spv_id=".($spvid?$spvid:'NULL').",
							a.handling_type='$privilges', 
							a.profile_id='$privilges', 
							a.full_name='$fullname',
							a.init_name=".($init_name?"'$init_name'":"NULL").",
							a.telphone ='$telphone'
							WHERE a.UserId='$UserId'";
				$result = $this->execute($sql,__FILE__,__LINE__);
				if ( $result ){
					$this -> UpdateCcGroup($userid,$cc_group,$getUserTMP,$fullname);
					echo 1;
				}	
				else echo 0;
			}		
		}
		
	 /** update cc agent group **/
	 
		private function UpdateCcGroup($userId='', $cc_group='', $old_name='', $fullname=''){
			if( $userId!=''):
				$sql = "UPDATE cc_agent a SET 
						a.agent_group='$cc_group', 
						a.name = '$fullname',
						a.userid='$userId' 
						WHERE a.userid='$old_name'"; 
						
				$res = $this ->execute($sql,__FILE__,__LINE__);
					if( $res ) return true;
					else return false;
			endif;
		}
	
	}
	
	$UserSystem = new UserSystem();
	$UserSystem -> initUserSystem();

?>