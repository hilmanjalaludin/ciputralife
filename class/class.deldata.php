<?php
	require_once("../sisipan/sessions.php");
	require_once("../fungsi/global.php");
	require_once("../class/MYSQLConnect.php");
	require_once("../class/lib.form.php");
	

	class MoveData extends mysql{
	
		function __construct(){
			parent::__construct();
		}
		
		
		function index()
		{
			if( $this -> havepost('action'))
			{
				switch($_REQUEST['action'])
				{
					case 'get_agent_byspv'	 : $this -> getAgentBySpv(); 	break;
					case 'get_data_null'  	 : $this -> getDataNull(); 		break;
					case 'get_fromspv_byam'	 : $this -> getSpvFromAM();		break;	
					case 'get_tospv_byam'	 : $this -> getSpvToAM(); 		break;
					case 'get_toagent_byspv' : $this -> getToAgentBySpv(); 	break;
					case 'move_to_spv'    	 : $this -> MoveToSPV(); 		break;
					case 'move_to_agent'	 : $this -> MoveToAgent(); 		break;
					case 'move_to_mgr'	 	 : $this -> MoveToMgr(); 		break;	
					case 'get_data_user'	 : $this -> getUserData();		break;
					case 'get_ajax_data'	 : $this -> getAjaxData();		break;
					case 'remove_customer'	 : $this -> DeleteCustomer();	break;
				}
			}
		}
		



/** delete customer ***/
		
		function DeleteCustomer()
		{
			
			$CustmerId = EXPLODE(",", $this -> escPost('customer_id'));
			
			$totals = 0;
			foreach( $CustmerId as $k => $CustomerNameId )
			{
			
				$sql = "DELETE a.*, b.* 
				FROM t_gn_customer as a, t_gn_assignment as b
				WHERE a.CustomerId = b.CustomerId
				AND a.CustomerId = '$CustomerNameId'";
				
				
				if( $this -> execute($sql,__FILE__,__LINE__) )
				{
					$totals++;
				}
			}
			
			if( $totals > 0 )
				echo json_encode(array('result'=>1));
			else
				echo json_encode(array('result'=>1));
		}


			
	/** get ajax data **/
		function getAjaxData()
		{
			$sql = " SELECT * from tms_agent a where a.full_name LIKE '%".$_REQUEST['letters']."%' OR id LIKE '%".$_REQUEST['letters']."%' ";
			$qry = $this -> query($sql);
			
			foreach($qry -> result_assoc() as $rows)
			{
				$datas[] = $rows['full_name']."|";
			}
			
			echo implode("1614###",$datas);
		}
		
	/** getUserData **/

		function getUserData()
		{
			$sql = "SELECT * from tms_agent a where a.UserId='".$_REQUEST['UserId']."' AND a.profile_id='".$_REQUEST['handle']."'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				echo json_encode( $qry -> result_first_assoc() );
			
			}
		}
		
				
	/** get list form am ***/

		function getSpvFromAM()
		{
			$sql = "select a.UserId, a.id, a.full_name 
					from tms_agent a 
					left join tms_agent_profile b on a.profile_id=b.id 
					where b.id=3 ";
					
			if( $this -> havepost('ManagerId')) $sql.=" and a.mgr_id='".$_REQUEST['ManagerId']."'" ;
			
			$qry = $this -> query($sql);
			foreach($qry -> result_array() as $rows )
			{
				$datas[$rows[0]] = $rows[1].' - '.$rows[2]; 
			}
			
			$this -> setForm() -> jpCombo('FromSupervisorId','select',$datas,NULL,'onChange="AgentBySpv(this);"');
		
		}
		
	/** get list form am ***/

		function getSpvToAM()
		{
			$sql = "select a.UserId, a.id, a.full_name 
					from tms_agent a 
					left join tms_agent_profile b on a.profile_id=b.id 
					where b.id=3";
					if( $this -> havepost('ManagerId')) $sql.=" and a.mgr_id='".$_REQUEST['ManagerId']."'" ;
			$qry = $this -> query($sql);
			foreach($qry -> result_array() as $rows )
			{
				$datas[$rows[0]] = $rows[1].' - '.$rows[2]; 
			}
			
			$this -> setForm() -> jpCombo('ToSupervisorId','select',$datas,NULL,'onChange="ToAgentBySpv(this);"');
		
		}	
		
	/** ************************/
	
		function MoveToSPV()
		{
			$Qty = 0;
			if( $this -> havepost('CustomerId') && $this -> havepost('ToSupervisorId')){
				$QtyCustomers = explode(',',$_REQUEST['CustomerId']);
				foreach($QtyCustomers as $k=>$v ){
					global $db;
					$sesi= $db->getSession('UserId');
					$trfby=$_REQUEST['ToSupervisorId'];
					$sql = " UPDATE t_gn_assignment a 
							SET a.AssignSpv ='".$_REQUEST['ToSupervisorId']."', 
								a.AssignMgr ='".$_REQUEST['ToManagerId']."', 
								a.AssignSelerId = NULL   
							WHERE a.CustomerId = '".$v."'";
					$result = $this -> execute($sql,__FILE__,__LINE__);
					
					$sqlx = "INSERT INTO t_gn_distribusi_log (LogAssignmentId, LogUserId, LogAssignUserId, LogCreatedDate, FromTrfMenu, LogTransferFrom)
						VALUES ($v, $trfby, $sesi, now(), '2', $sesi)";
					$this -> execute($sqlx,__FILE__,__LINE__);
					
					
					if( $result ) $Qty+=1;
				}
			}
			
			echo json_encode(array('total'=>$Qty, 'spv'=>'Move New Data'));
		}
		
	/** move to manager ****/
	
		function MoveToMgr()
		{
			$Qty = 0;
			if( $this -> havepost('CustomerId') && $this -> havepost('ToManagerId')){
				$QtyCustomers = explode(',',$_REQUEST['CustomerId']);
				foreach($QtyCustomers as $k=>$v ){
					global $db;
					$sesi= $db->getSession('UserId');
					$trfby=$_REQUEST['ToManagerId'];
					
					$sql = " UPDATE t_gn_assignment a 
							SET a.AssignMgr ='".$_REQUEST['ToManagerId']."', 
								a.AssignSpv = NULL,
								a.AssignSelerId = NULL   
							WHERE a.CustomerId = '".$v."'";
					$result = $this -> execute($sql,__FILE__,__LINE__);
					
					$sqlx = "INSERT INTO t_gn_distribusi_log (LogAssignmentId, LogUserId, LogAssignUserId, LogCreatedDate, FromTrfMenu, LogTransferFrom)
							VALUES ($v, $trfby, $sesi, now(), '2', $sesi)";
					$this -> execute($sqlx,__FILE__,__LINE__);
					
					if( $result ) $Qty+=1;
				}
			}
			
			echo json_encode(array('total'=>$Qty, 'spv'=>'Move New Data'));
		
		}
	
	/** &&&&&&&&&&&&&&&&&&& ***********/
	
		function MoveToAgent()
		{
			Global $db;
			$QtyCustomers = explode(',',$_REQUEST['CustomerId']);
			$QtyAgents 	  = explode(',',$_REQUEST['ToSellerId']);
			$QtnCustomers = (int)$_REQUEST['SizeData']; // count($QtyCustomers);
			$QtnAgents 	  = count($QtyAgents);
			$QtnPerAgents = 0;
			$dataSize 	  = array();
			
			if( $QtnCustomers> 0 && $QtnAgents >0 )
			{
				$QtnPerAgents = floor($QtnCustomers/$QtnAgents);
				$TotPerAgents = ($QtnCustomers-($QtnPerAgents*$QtnAgents));
				$start = 0;
				foreach($QtyAgents as $a => $b)
				{
					if( $start==($QtnAgents-1)){
						$no = ($start* $QtnPerAgents);
						$dataSize[$b]= $this -> AssignSize(($QtnPerAgents+$TotPerAgents),$no, $b);
					}
					else
					{
						$no = ($start* $QtnPerAgents);
						$dataSize[$b]= $this -> AssignSize($QtnPerAgents,$no, $b);
					}	
					
					$start++;
				}
				
			}
			if( is_array($dataSize))
			{
				foreach($dataSize as $agentId => $value)
				{
					foreach($value as $i=> $customerId)
					{
						Global $db;
						$trfby=$_REQUEST['ToSupervisorId'];
						$sesi= $db->getSession('UserId');
						$sql = " UPDATE t_gn_assignment a 
								 SET a.AssignSpv ='".$_REQUEST['ToSupervisorId']."',  
									 a.AssignMgr ='".$_REQUEST['ToManagerId']."', 
									 a.AssignSelerId ='".$agentId."',
								    a.AssignBlock=0,
									a.AssignMode='MOV'
								 WHERE a.CustomerId = '".$customerId."'";
						$this -> execute($sql,__FILE__,__LINE__);
						
						$sqlx = "INSERT INTO t_gn_distribusi_log (LogAssignmentId, LogUserId, LogAssignUserId, LogCreatedDate, FromTrfMenu, LogTransferFrom)
								VALUES ($customerId, $agentId, $sesi, now(), '2', $trfby)";
						$this -> execute($sqlx,__FILE__,__LINE__);
					}
					
				}
			}
			
			echo json_encode(array('total'=>$QtnCustomers,'spv'=>'Move New Data', 'agent'=>$QtnAgents ,'peragent'=>$QtnPerAgents ));
			
		}
	
	/** get assig Size ****/
		function AssignSize($QtyAllow=0, $start = 0,$v=0)
		{
			global $db;
			$QtyCustomers = explode(',',$_REQUEST['CustomerId']);
			return array_slice
				(
					$QtyCustomers,
					$start,
					$QtyAllow
				);
		}
		
		private function setForm()
		{
			return new jpForm(true);
		}
		
		public function getDataNull(){
		global $db;
		
		$sesi = $db->getSession('UserId');
			$QtySize = '<table width="100%" class="custom-grid" cellspacing="0">
						<thead>
							<tr height="20"> 
								<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="CheckListData();">#</a></th>	
								<th nowrap class="custom-grid th-middle">&nbsp;No.</th>
								<th nowrap class="custom-grid th-middle">&nbsp;Customer Number.</th>
								<th nowrap class="custom-grid th-middle">&nbsp;Customer Name.</th>
								<th nowrap class="custom-grid th-middle">&nbsp;Gender.</th>
								<th nowrap class="custom-grid th-middle">&nbsp;City.</th>
								<th nowrap class="custom-grid th-middle">&nbsp;DOB.</th>
								<th nowrap class="custom-grid th-lasted">&nbsp;Age.</th>
							</tr>
						</thead>';	
					
			$sql = "select *,IF(b.GenderId=1,'MALE',IF(b.GenderId=2,'FEMALE','-')) as Gender,
						DATE_FORMAT(FROM_DAYS(DATEDIFF(CURRENT_DATE,b.CustomerDOB)),'%y') AS Age
						from t_gn_assignment a 
						inner join t_gn_customer b on a.CustomerId=b.CustomerId
						left join t_gn_campaign c on b.CampaignId=c.CampaignId 
					where b.CallReasonId is null AND b.CampaignId IN(".$_REQUEST['CampaignId'].") ";
				
			if( $db -> getSession('handling_type')==9 ) :	
				$sql .=" and a.AssignAdmin IS NOT NULL and a.AssignMgr IS NULL and a.AssignSpv IS NULL and a.AssignSelerId IS NULL "; endif;
			if( $db -> getSession('handling_type')==1 ) :	
				$sql .=" and a.AssignAdmin IS NOT NULL and a.AssignMgr IS NULL and a.AssignSpv IS NULL and a.AssignSelerId IS NULL "; endif;
			if( $db -> getSession('handling_type')==2 ) :	
				$sql .=" and a.AssignMgr = ".$sesi." and a.AssignSpv IS NULL and a.AssignSelerId IS NULL "; endif;
			if( $db -> getSession('handling_type')==3 ) :	
				$sql .=" and a.AssignSpv = ".$sesi." and a.AssignSelerId IS NULL "; endif;
			if( $this ->havepost('city')) 				:	
				$sql .=" and b.CustomerCity LIKE '%".$_REQUEST['city']."%'"; endif;
			if( $this ->havepost('gender')) 			:	
				$sql .=" and b.GenderId = '".$_REQUEST['gender']."'"; endif;
			if( $this ->havepost('age')) 				:	
				$sql .=" and DATE_FORMAT(FROM_DAYS(DATEDIFF(CURRENT_DATE,b.CustomerDOB)),'%y') = '".$_REQUEST['age']."'"; endif;
			//echo $sql;
			//echo $db -> getSession('handling_type');
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			
			$dataSize  =0;
			$no = 1;
			while( $rows = $this ->fetchassoc($qry)){
				
				$QtySize.= '<tr class="onselect">'.
							'<td class="content-first"><input type="checkbox" value="'.$rows['CustomerId'].'" name="chk_cust_dist" name="chk_cust_dist" onclick="RandomClick();"></td>'.
							'<td class="content-middle">'.$no.'</td>'.
							'<td class="content-middle">'.$rows['CustomerNumber'].'</td>'.
							'<td class="content-middle">'.$rows['CustomerFirstName'].'</td>'.
							'<td class="content-middle"><b style="color:green;">'.$rows['Gender'].'<b></td>'.
							'<td class="content-middle"><b style="color:green;">'.$rows['CustomerCity'].'<b></td>'.
							'<td class="content-middle"><b style="color:green;">'.$rows['CustomerDOB'].'<b></td>'.
							'<td class="content-lasted"><b style="color:green;">'.$rows['Age'].'<b></td>'.
						'</tr>';
				$dataSize+=1; $no++;		
						
			}
			
			$QtySize.='</table>';
			
			echo json_encode(array('table'=>$QtySize, 'total'=>$dataSize));
						
		}
		
		public function getAgentBySpv()
		{
			global $db;
			$sql = " select a.UserId, a.id, a.full_name 
						from tms_agent a 
						left join tms_agent_profile b on a.profile_id=b.id 
					where b.id=4
					and a.spv_id='".$_REQUEST['SupervisorId']."'" ;
			//echo $sql;
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			while( $rows = $this ->fetcharray($qry)){
				$datas[$rows[0]] = $rows[1].' - '.$rows[2]; 
			}
			
			$this -> setForm() -> jpListCombo('SellerId','SelectAgent',$datas);
		}
		
		public function getToAgentBySpv()
		{
			$sql = "select a.UserId, a.id, a.full_name 
						from tms_agent a 
						left join tms_agent_profile b on a.profile_id=b.id 
					where b.id=4
					and a.spv_id='".$_REQUEST['SupervisorId']."'" ;
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			while( $rows = $this ->fetcharray($qry)){
				$datas[$rows[0]] = $rows[1].' - '.$rows[2]; 
			}
			
			$this -> setForm() -> jpListCombo('ToxSellerId','Select Agent',$datas);
		}
	}	
	
	
	$MoveData = new MoveData(true);
	$MoveData -> index();
 
?>