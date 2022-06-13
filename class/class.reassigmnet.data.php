<?php

 require_once(dirname(__FILE__)."/../sisipan/sessions.php");
 require_once(dirname(__FILE__)."/../fungsi/global.php");
 require_once(dirname(__FILE__)."/../class/MYSQLConnect.php");
 require_once(dirname(__FILE__)."/../class/lib.form.php");
 require_once(dirname(__FILE__)."/../class/class.query.parameter.php");	
/* 
 * class distribusi extends mysql
 * untuk action yang berhubungan
 * dengan distribusi
 * author : omens
 *
 */
 

 class ReassigmentData extends mysql{
	
	var $action;
	var $campaignId;
	var $userId;
	var $assignId;
	var $Form;
	var $spv_id;
	
/** distribusi type is manual data ****/
	
	function ReassigmentData()
	{
		parent::__construct();
		$this -> action 	= $this -> escPost('action');
		$this -> campaignId = $this -> escPost('campaignId');
		$this -> Form		= new jpForm(true);
		$this -> Query  	= new ParameterQuery();
		$this -> spv_id		= $this -> escPost('UserLevelSPV');
	}
	
	
/* function set style css **/
	
	function setCss(){?>
		<!-- start: css -->
			<style>
				.select { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
				.input_text {font-family:Arial;color:#bbb000;font-weight:bold;border:1px solid #dddbbb;width:50px;
				 font-size:11px;height:20px;background-color:#fffccc;}
				.text_header { text-align:right;color:#746b6a;font-size:12px;}
				.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
			</style>
		<!-- stop: css -->
	<?php 
	}	
	
	function getTitle(){
		return $this -> title;
	}

/* main class **/
	
	function index()
	{
		
		if( $this -> havepost('action') )
		{
			switch( $this->action )
			{
				case 'show_user_by_level'	: $this -> ShowUserByLevel(); 	break;
				case 'act_distribusi_data'	: $this -> ActDistribusiData(); break;
				case 'show_data_assignment' : $this -> ReassingData(); 		break;
				case 'get_Agent_BySPV'		: $this -> get_Agent_BySPV();	break;
			}
		}
	}
	
	
	function get_Agent_BySPV()
	{
		$sql = " select a.UserId, a.id, a.full_name 
			from tms_agent a 
			left join tms_agent_profile b on a.profile_id=b.id 
			where b.id=4 and a.spv_id='".$this -> spv_id."'";
		// echo $sql;
		// die();
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $row = $this ->fetcharray($qry)){
				$datas[$row[0]] =  $row[1].' - '.$row[2];
			}
		//print_r($datas);
		echo $this->Form->jpListcombo('agent_name','Select',$datas);
	}
/** printed call statua null ***/
	
	function get_call_status()
	{
		$callresult = EXPLODE(",",$this -> escPost('CallResult'));
		foreach( $callresult as $key => $rows )
		{
			if( $rows=='NULL') $NULL = 1;
			else
			{
				$datas[$rows] = $rows; 
			}
		}
		
		$filter = " ";
		if( $NULL ) $filter_null = " OR a.CallReasonId IS NULL ";
			$filter = " AND (  (a.CallReasonId NOT IN (13,14,15,21,22) AND a.CallReasonId  IN ('".IMPLODE("','",$datas)."') ) $filter_null ) ";
		
		return $filter;	
	}	
	
	
/** ReassingData() **/
	
	function ReassingData()
	{
		$result_size = array('size_data'=>0,'query_string'=>'');
		
		
		$CampaignId  = IMPLODE("','",EXPLODE(",",$this -> escPost('CampaignId')));
		$UserDatas   = IMPLODE("','",EXPLODE(",",$this -> escPost('UserDatas')));
		$CallResult  = IMPLODE("','",EXPLODE(",",$this -> escPost('CallResult')));
		// $Spv_Id		 = $this -> spv_id;
		
		$sql = " SELECT * from t_gn_customer a 
				 left join t_gn_assignment b on a.CustomerId=b.CustomerId
				 WHERE 1=1 ";
		
		//if( $this-> getSession('handling_type')==USER_ADMIN ) $sql .= " AND b.AssignAdmin='".$this-> getSession('UserId')."'";
		if( $this-> getSession('handling_type')==USER_ADMIN ) $sql .= " AND b.AssignAdmin='".$this-> getSession('UserId')."'";
		if( $this-> getSession('handling_type')==USER_MANAGER ) $sql .= " AND b.AssignMgr='".$this-> getSession('UserId')."'";
		if( $this-> getSession('handling_type')==USER_SUPERVISOR ) $sql .= " AND b.AssignSpv='".$this-> getSession('UserId')."'";
		if( $this-> getSession('handling_type')==USER_TELESALES ) $sql .= " AND b.AssignSelerId='".$this-> getSession('UserId')."'";
		
		if($this -> havepost('CampaignId')) $sql .= " AND a.CampaignId IN('$CampaignId') ";
		if($this -> havepost('UserDatas'))  $sql .= " AND b.AssignSelerId IN('$UserDatas') ";
		if($this -> havepost('CallResult')){ 
			$sql .= $this -> get_call_status();
		}
		else{
			$sql .= " AND (a.CallReasonId IS NULL OR a.CallReasonId NOT IN (13,14,15,21,22))";
		}
		
		
		if($this -> escPost('Spvname')!="")
			$sql.= " AND b.AssignSpv = '".$this -> escPost('Spvname')."'";
		// echo $sql;
		//print_r($this -> escPost('Spvname'));
		
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			$result_size['size_data'] = $qry -> result_num_rows();
			$result_size['query_string'] =	$qry -> result_str_query();
		}
		
		echo json_encode($result_size);
	}	
	
	
/** distribusi type is manual data ****/
	
	function ShowUserByLevel()
	{
		switch($_REQUEST['DistribusiType'])
		{
			case 1 : $this -> ShowDistribusiByManual(); 	break;
			case 2 : $this -> ShowDistribusiByAutomatic(); 	break;
		}
	}
	
/** distribusi type is manual data ****/
	
	function ActDistribusiData()
	{
		switch($_REQUEST['DistribusiType'])
		{
			case 1 : $this -> actDistribusiByManual();		break;
			case 2 : $this -> actDistribusiByAutomatic(); 	break;
		}
	}
	
/** function get query data **/
	function get_query_datas()
	{
		if( $this -> havepost('QueryDatas'))
		{
			return $_REQUEST['QueryDatas'];
		}
		else
			return false;
	}	
	
/** distribusi type is manual data ****/

	function agent_list_array()
	{
		if( $_REQUEST['UserSelectId']!='' )
		{
			return json_decode(str_replace("\\","", $_REQUEST['UserSelectId']),true);
		}	
	}
	
/** functio  get Mgr Id ****/
		
	function getMgrByUser($UserId)
	{
		$sql = " select mgr_id from tms_agent  where  UserId='$UserId'	";
		return $this -> valueSQL($sql);
	}
	
/** functio  get SpvId ****/
			
	function getSpvIdByUser($UserId)
	{
		$sql = " select spv_id from tms_agent  where UserId='$UserId'	";
		return $this -> valueSQL($sql);
	}
	
	
/** function get level ***/
	
	function getLevelUserByCols($UserLevel='')
	{
		$maving  = array(9=>'AssignMgr', 2=>'AssignMgr', 3=>'AssignSpv', 4=>'AssignSelerId' );
		if( $UserLevel!='' )
		{
			return " $maving[$UserLevel]";
		}
	}	
	
/** get save to array key ***/
	private function getAssignMentData()
	{
		if( $this -> get_query_datas() )
		{
			$sql = $this -> get_query_datas();	
			switch( $_REQUEST['DistribusiMode'])
			{ 
				case 2 : $sql.= " ORDER BY RAND() ";  break;
				case 1 : $sql.= " ORDER BY b.AssignId ASC "; break;
			}
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[] = $rows['AssignId']; 
			}
			return $datas;
		}
		else
			return 0;
	}
	
/** insert to log distribusi **/

	private function LogHistory($SQL_insert = array() ){
		if( is_array($SQL_insert)){
			$SQL_exec_insert['LogAssignmentId'] = $SQL_insert['AssignId'];
			$SQL_exec_insert['LogUserId']= $SQL_insert['UserId'];
			$SQL_exec_insert['LogCreatedDate'] = date('Y-m-d H:i:s');
			$SQL_exec_insert['LogAssignUserId'] = $this -> getSession('UserId');
			if( $this -> set_mysql_insert('t_gn_distribusi_log', $SQL_exec_insert ) ) return true;
			else
				return false;
		} 
		else
			return false;
	}	
		
/** distribusi type is manual data ****/
	
	private function getTotalByUser($UserId)
	{
		$sql = " SELECT COUNT(a.AssignId) FROM t_gn_assignment a 
				 left join t_gn_customer b on a.CustomerId=b.CustomerId
				 left join t_gn_campaign c on b.CampaignId=c.CampaignId
				 where 1=1
				 and ".$this->getLevelUserByCols($_REQUEST['UserLevel'])." ='$UserId'";
		return $this -> valueSQL($sql);		
	}
		
	
/** distribusi type is manual data ****/
	
	private function actDistribusiByManual()
	{
		$result 		= array('result'=>0,'agent'=> 0, 'count'=>0 );
		$JumlahData 	= $_REQUEST['JumlahData'];
		$AssignData 	= $_REQUEST['AssignData'];
		$UserLevel 		= $_REQUEST['UserLevel'];
		$DistribusiType = $_REQUEST['DistribusiType'];
		$UserSelectId	= $_REQUEST['UserSelectId'];
		$UserSelect		= explode(',',$_REQUEST['UserSelect']);
		
		
		/** define ******/
		$out_json = $this -> agent_list_array();
		// print_r($out_json);
			if( is_array( $out_json ))
			{
				foreach($out_json as $key => $rows )
				{
					$array_user_id[$key] = $rows['userid']; 
					$array_size_id[$key] = $rows['size'];	
				}
				
				///////////////////////////////////////////////////////////////////////////////////////////////////
				/** urutkan size_data secra ASC ex: 0,1,2,3,4  ***************************************************/
				
				$array_multisort = array_multisort($array_size_id, SORT_ASC, $array_user_id, SORT_ASC, $out_json); 
				if( $array_multisort )
				{
					$QtyDataAsg = $this -> getAssignMentData();
					$start_post = 0;
					foreach($out_json as $rows )
					{
						if( $start_post==0 ){
							$start =0;
							$post_size = ($rows['size']-1);
							while(true){
								$datas[$rows['userid']][] = $QtyDataAsg[$start]; 
								if( $start==$post_size ) BREAK;
									$start+=1;
							}
						}	
						else{
							$post_size = ($rows['size']+$start);
							$start = $start+1;
							while(true)
							{
								$datas[$rows['userid']][] = $QtyDataAsg[$start]; 
								if( $start==$post_size ) BREAK;
									$start+=1;
							}
						}
						
						$start_post+=1;
					}	
					
				/** return error status to client ****/
				
					$totals_data_assign += $this -> UpdateTgnAssignMent( $datas );
					$result = array('result'=>1,'agent'=> count($UserSelect), 'count' => $totals_data_assign);	
				}
				
				
			}
		echo json_encode($result);	
		
    }
	
/** distribusi type is manual data ****/
	
	private function actDistribusiByAutomatic()
	{
		$result 		= array('result'=>0,'agent'=> 0, 'count'=>0 );
		$JumlahData 	= $_REQUEST['JumlahData'];
		$AssignData 	= $_REQUEST['AssignData'];
		$UserLevel 		= $_REQUEST['UserLevel'];
		$DistribusiType = $_REQUEST['DistribusiType'];
		$UserSelectId	= $_REQUEST['UserSelectId'];
		$UserSelect		= explode(',',$_REQUEST['UserSelect']);
		$QtyUserSelect  = count($UserSelect );
		$QtyDataSelect  = $AssignData;
		$QtyUserResult  = 0;
		$QtyValidResult = 0;
		
		if( ($QtyUserSelect !=0) && ($QtyDataSelect!='') && ($QtyDataSelect<> 0) )
		{
			$QtyDataAsg = $this -> getAssignMentData();
			$QtyPerUser = (INT)($QtyDataSelect/$QtyUserSelect); 
				if( $QtyPerUser > 0 )
				{
					$start = 0;
					foreach( $UserSelect as $k => $vUser )
					{
						$start_data = (($start)*($QtyPerUser));
						$limit_data = (($QtyPerUser)-1);
						$post_data = $start_data;
						$limit_assign = 0;
						while(true)
						{
							$datas[$vUser][] = $QtyDataAsg[$post_data]; 
							if(($limit_assign==$limit_data)) break;
								$post_data+=1;	
								$limit_assign+=1;
						}
						$start++;	
					}
					
					$totals_data_assign += $this -> UpdateTgnAssignMent( $datas );
					$result = array('result'=>1,'agent'=> count($UserSelect), 'count' => $totals_data_assign);	
				}
		}
		
		echo json_encode($result);	
	}
	
/** UpdateTgnAssignMent ***/

	function UpdateTgnAssignMent($DataByUser)
	{
		$sub_totals = 0;
		
		if( is_array($DataByUser))
		{
			foreach($DataByUser as $UserId => $datas )
			{	
				foreach($datas as $key => $AssignId )
				{
					$Users = $this -> Users -> getUsers($UserId);
					if( $Users -> isAvailable() )
					{
						if( $this -> escPost('UserLevel')==2 ){
							$SQL_update['AssignMgr']  = $Users -> getManagerId();
							$SQL_update['AssignMode'] = 'RTV';
							$SQL_update['AssignDate'] = date('Y-m-d H:i:s');
							
						}
						
						if( $this -> escPost('UserLevel')==3 ){
							$SQL_update['AssignMgr']  = $Users -> getManagerId();
							$SQL_update['AssignSpv']  = $Users -> getSupervisorId();
							$SQL_update['AssignMode'] = 'RTV';
							$SQL_update['AssignDate'] = date('Y-m-d H:i:s');
							$SQL_update['AssignSelerId'] = 'AssignSelerId';
						}
						
						if( $this -> escPost('UserLevel')==4 ){
							$SQL_update['AssignMgr']   = $Users -> getManagerId();
							$SQL_update['AssignSpv']   = $Users -> getSupervisorId();
							$SQL_update['AssignSelerId'] = $Users -> getUserId();
							$SQL_update['AssignMode'] = 'RTV';
							$SQL_update['AssignDate']  = date('Y-m-d H:i:s');
						}
						
						$SQL_wheres['AssignId'] = $AssignId;
						$this -> LogHistory(array('AssignId'=>$AssignId,'UserId'=>$Users->getUserId()));
						// $test=$this -> set_mysql_update('t_gn_assignment',$SQL_update, $SQL_wheres;
						// echo $test;
						if( $this -> set_mysql_update('t_gn_assignment',$SQL_update, $SQL_wheres))
						{
							$sub_totals++;
						}		 
					}
				}
			}
		}
		
		return $sub_totals;
	}	

/** distribusi type is manual data ****/
	
	private function ShowDistribusiByAutomatic()
	{
		$this -> setCss();
		echo "<table  width=\"50%\" class=\"custom-grid\" cellspacing=\"0\">
				<tr>
					<th class=\"custom-grid th-first\"><a href=\"javascript:void(0);\"  onclick=\"doJava.checkedAll('chk_user_id');\">#</a></th>
					<th class=\"custom-grid th-middle\">User ID </th>
					<th class=\"custom-grid th-middle\">User Name </th>
					<th class=\"custom-grid th-middle\">Leader Name </th>
					<th class=\"custom-grid th-middle\">Size Data</th>
				</tr>";
				
		
		if( $this -> getSession('handling_type')==1 )
			$sql = " select a.*,b.full_name as full_name_spv 
					from tms_agent a 
					left join tms_agent b on a.spv_id = b.userid  where a.handling_type ='".$this -> escPost('UserLevel')."' and a.user_state=1 ";	
		if( $this -> getSession('handling_type')==2 ) 
			$sql = " select a.*,b.full_name as full_name_spv 
					from tms_agent a 
					left join tms_agent b on a.spv_id = b.userid 
					where a.handling_type ='".$this -> escPost('UserLevel')."' 
					and a.user_state=1 and a.mgr_id='".$_SESSION['UserId']."' ";	
		if( $this -> getSession('handling_type')==3 ) 
			$sql = " select a.*,b.full_name as full_name_spv 
					from tms_agent a 
					left join tms_agent b on a.spv_id = b.userid  
					where a.handling_type ='".$this -> escPost('UserLevel')."' and a.user_state=1 and a.spv_id='".$_SESSION['UserId']."' ";	
					
		
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $rows = $this -> fetchassoc($qry) )
		{	
			echo " <tr>
						<td class=\"content-first\"><input type='checkbox' name='chk_user_id' id='chk_user_id' onclick='UncheckSize(this);' value='".$rows['UserId']."'></td>
						<td class=\"content-middle\">{$rows[id]}</td>
						<td class=\"content-middle\">{$rows[full_name]}</td>
						<td class=\"content-middle\">{$rows[full_name_spv]}</td>
						<td class=\"content-middle\">".$this->getTotalByUser($rows['UserId'])."</td>
					</tr>";
		}
	
		echo "</table>";
		
				
	}
	
/** distribusi type is manual data ****/
	
	function ShowDistribusiByManual()
	{
		$this -> setCss();
		echo "<table  width=\"50%\" class=\"custom-grid\" cellspacing=\"0\">
				<tr>
					<th class=\"custom-grid th-first\"><a href=\"javascript:void(0);\"  onclick=\"doJava.checkedAll('chk_user_id');\">#</a></th>
					<th class=\"custom-grid th-middle\">User ID </th>
					<th class=\"custom-grid th-middle\">User Name </th>
					<th class=\"custom-grid th-middle\">Leader Name </th>
					<th class=\"custom-grid th-middle\">Size Data</th>
					<th class=\"custom-grid th-lasted\">Amount </th>
				</tr>";
				
		if( $this -> getSession('handling_type')==USER_ROOT )
			$sql = " select a.*,b.full_name as full_name_spv 
					from tms_agent a 
					left join tms_agent b on a.spv_id = b.userid  where a.handling_type ='".$this -> escPost('UserLevel')."' and a.user_state=1 ";			
		else if( $this -> getSession('handling_type')==USER_ADMIN )
			$sql = " select a.*,b.full_name as full_name_spv 
					from tms_agent a 
					left join tms_agent b on a.spv_id = b.userid  where a.handling_type ='".$this -> escPost('UserLevel')."' and a.user_state=1 ";	
		if( $this -> getSession('handling_type')==USER_MANAGER ) 
			$sql = " select a.*,b.full_name as full_name_spv 
					from tms_agent a 
					left join tms_agent b on a.spv_id = b.userid 
					where a.handling_type ='".$this -> escPost('UserLevel')."' 
					and a.user_state=1 and a.mgr_id='".$_SESSION['UserId']."' ";	
		if( $this -> getSession('handling_type')==USER_SUPERVISOR ) 
			$sql = " select a.*,b.full_name as full_name_spv 
					from tms_agent a 
					left join tms_agent b on a.spv_id = b.userid  
					where a.handling_type ='".$this -> escPost('UserLevel')."' and a.user_state=1 and a.spv_id='".$_SESSION['UserId']."' ";	
		
		
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $rows = $this -> fetchassoc($qry) )
		{	
			echo " <tr>
						<td class=\"content-first\"><input type='checkbox' name='chk_user_id' id='chk_user_id' onclick='UncheckSize(this);' value='".$rows['UserId']."'></td>
						<td class=\"content-middle\">{$rows[id]}</td>
						<td class=\"content-middle\">{$rows[full_name]}</td>
						<td class=\"content-middle\">{$rows[full_name_spv]}</td>
						<td class=\"content-middle\">".$this->getTotalByUser($rows['UserId'])."</td>
						<td class=\"content-lasted\" align=\"center\">".$this -> Form -> jpField('amount_data_'.$rows['UserId'],'input_text',NULL,'onkeyup="BalanceUserSize(this);"')."</td>
					</tr>";
		}
	
		echo "</table>";
	}
	
 }
 
 $distribusi = new ReassigmentData();
 $distribusi -> index();
 
?>
