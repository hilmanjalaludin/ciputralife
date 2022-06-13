<?php

 require_once("../sisipan/sessions.php");
 require_once("../fungsi/global.php");
 require_once("../class/MYSQLConnect.php");
	
/* 
 * class distribusi by customer extends mysql
 * untuk action yang berhubungan
 * dengan distribusi
 * author : omens
 */
 
 /*
	@ POST PARAMETER :
		action :'distribusi_by_customer', ((str))
		campaign_id : campaign_id,  ( ((str)))
		agent_id : agent_id, ((str))
		cust_id : cust_id,((str))
 */

 class 	CustomerDistribusi extends mysql{
	
	var $Action;
	var $CampaignId;
	var $AgentId;
	var $CustomerId;
	
	function __construct(){
		parent::__construct();
		$this -> Action 	= $this -> escPost('action');
		$this -> CampaignId = $this -> escPost('campaign_id');
		$this -> AgentId 	= $this -> escPost('agent_id');
		$this -> CustomerId = explode(',',$this -> escPost('cust_id'));
		
	}
	
	function index(){
		if( $this->havepost('action')){
			switch($this -> Action){
				case 'distribusi_by_customer':  $this -> distribusiByCustomer(); break;
				case 'save_dist_bymount'	 :  $this -> saveDistByMount(); 	 break;
			}
		}
		
	}
	
	private function getAssignColumns(){
		$array_field = array('1'=>'AssignMgr', '2'=>'AssignSpv','3'=>'AssignSelerId');
		
		if($this -> getSession('handling_type')!='' ):
			return $array_field[$this -> getSession('handling_type')];
		else:
			return false;
		endif;
	}
	
	
	
/** by customers **/
	
	function distribusiByCustomer(){
		
		$i=0;
		foreach($this -> CustomerId as $key => $customerid ){
			$datas[$this -> getAssignColumns()] = $this -> AgentId ;
			$datas['AssignDate'] = date('Y-m-d H:i:s');
			$where['CustomerId'] = $customerid;
			
				$Query = $this->set_mysql_update('t_gn_assignment',$datas,$where);
				if( $Query ) $i++;	
		}
		
		if( $i>0 ) echo 1;
		else echo 0;
	}
	
	
/** function assigment data **/
	
	private function AssignmentAction($userId, $Amount){
		if( ( $userId!=0 ) && ( $userId!='' ) && ( $Amount!='' ) && ( $Amount!=0) ):
			
			if( $this ->getSession('handling_type')==1){
				$sql = " SELECT a.CustomerId from t_gn_assignment a 
						 INNER JOIN t_gn_customer b on a.CustomerId=b.CustomerId 
						 LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId 
						 WHERE c.CampaignNumber = '".$this->CampaignId."'
							AND a.AssignAdmin =".$this ->getSession('UserId') ."
							AND a.AssignMgr is null 
							AND a.AssignSpv is null 
							AND a.AssignSelerId is null 
							AND c.CampaignStatusFlag =1
							LIMIT $Amount";
			}
			
			else if( $this ->getSession('handling_type')==2){
				$sql = " SELECT a.CustomerId from t_gn_assignment a 
						 INNER JOIN t_gn_customer b on a.CustomerId=b.CustomerId 
						 LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId 
						 WHERE c.CampaignNumber = '".$this->CampaignId."'
							AND a.AssignAdmin is not null
							AND a.AssignMgr =".$this ->getSession('UserId') ."
							AND a.AssignSpv is null 
							AND a.AssignSelerId is null 
							AND c.CampaignStatusFlag =1
							LIMIT $Amount";
			}
			
			else if( $this ->getSession('handling_type')==3){
				$sql = " SELECT a.CustomerId from t_gn_assignment a 
						 INNER JOIN t_gn_customer b on a.CustomerId=b.CustomerId 
						 LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId 
						 WHERE c.CampaignNumber = '".$this->CampaignId."'
							 AND a.AssignAdmin is not null
							 AND a.AssignMgr is not null 
							 AND a.AssignSpv =".$this ->getSession('UserId') ."
							 AND a.AssignSelerId is null 
							 AND c.CampaignStatusFlag =1
							 LIMIT $Amount";
			}				
			
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			$i   = 0;
			while( $result = $this ->fetchrow($qry) ){
				$datas[$this -> getAssignColumns()] = $userId;
				$datas['AssignDate'] = date('Y-m-d H:i:s');
				$where['CustomerId'] = $result -> CustomerId;
				$Query = $this->set_mysql_update('t_gn_assignment',$datas,$where);
				if( $Query ) $i++;
			}	
			if( $i >0 ) return true;
			else return false;
		else:
			return false;
		endif;	
	}
	
	
/** pisah amount dan agent **/
	
	private function AssignmentValue($list_data=''){
		if( $list_data!=''):
			$assigment_value = explode("~",$list_data);
			if(is_array($assigment_value)):
					$datas['userid'] = $assigment_value[0];
					$datas['amount']  = $assigment_value[1];
				return $datas;
			else:
				return false;
			endif;	
		else:
			return false;
		endif;
	}
		
 /* by amount **/
	
	function saveDistByMount(){
		$ListData	 = explode("|",$this -> escPost('list_data'));
		$AssignTotal = $this -> escPost('assign_total');
		$AssignTrue  = $this -> escPost('assign_true');
			
		$i=0;	
		foreach( $ListData as $key=>$val){
			$user_datas = $this -> AssignmentValue($val);
			if( $this -> AssignmentAction($user_datas['userid'],$user_datas['amount'])) :
				$i++;
			endif;	
		}
		
		if( $i > 0 ) echo 1;
		else echo 0;
	}
 }
 
 $CustomerDistribusi = new CustomerDistribusi();
 $CustomerDistribusi -> index();
 
?>
