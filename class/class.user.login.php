<?php
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.cti.php");

/**
 * class user login attribut  
 * sub project AJMI
 * author Omens
 */

class UserLogin extends mysql
{
		var $CTI;
		var $Username;
		var $Password;
		var $IpAddress;
		
		function __construct(){
			parent::__construct();
		}
		
		function initClass()
		{
			
			if( $this->havepost('action')):
				$this -> Username =  $this -> escPost('username');
				$this -> Password =  $this -> escPost('password');
				$this -> IpAddress=  $this -> getRealIpAddr();
			endif;
			
			if( !empty( $this -> Username) ):
				return $this -> initLogin();
			else:
				return 0;
			endif;
		}
		
		function loginOtherIp()
		{
			$this -> IpAddress =  $this -> getRealIpAddr();
			
			$sql = " SELECT a.ip_address as IPAddress from tms_agent a 
						INNER JOIN cc_agent b on a.id=b.userid 
						LEFT JOIN tms_agent_profile d on a.profile_id =d.id
						WHERE a.password=MD5('".$this->escPost('password')."') 
						AND a.id ='".$this->escPost('username')."'"; 	
			
			$IP = $this -> valueSQL($sql);	
			if( $this -> IpAddress==$IP):
				return true;
			else:
				if( empty($IP)){
					return true;
				}
				else{
					return false;
				}
				
			endif;
						
		}
		
		function initLogin(){
			
			$sql = " SELECT 
						a.UserId, a. id, a.profile_id, 
						a.agency_id, a.mgr_id, a.spv_id,
						a.handling_type,  d.menu,d.menu_group,
						a.password, a.ip_address,
						a.logged_state, a.user_state 
					FROM tms_agent a inner join cc_agent b on a.id=b.userid 
					LEFT JOIN tms_agent_profile d on a.profile_id =d.id
					WHERE a.user_state=1 
					AND a.password=MD5('".$this->Password."') 
					AND a.id ='".$this->Username."'"; 	
			
			$qry = $this -> query($sql);
			
			if(!$qry -> EOF())
			{
				session_start();
				$this -> setSession('UserId',$qry -> result_get_value('UserId'));
				$this -> setSession('login_date', date('l, j F Y, H:i:s'));
				$this -> setSession('username', $qry -> result_get_value('id'));
				$this -> setSession('user_profile', $qry -> result_get_value('profile_id'));
				$this -> setSession('mgr_id', $qry -> result_get_value('mgr_id'));
				$this -> setSession('spv_id', $qry -> result_get_value('spv_id'));
				$this -> setSession('user_agency', $qry -> result_get_value('agency_id'));
				$this -> setSession('handling_type', $qry -> result_get_value('handling_type'));
				$this -> setSession('menu', $qry -> result_get_value('menu'));
				$this -> setSession('pass', $qry -> result_get_value('password'));
				$this -> setSession('menu_group', $qry -> result_get_value('menu_group'));
				
				if( $this -> getSession('username')!='' )
				{
					$this -> setLogin();
					$this -> setCountLogin();
					$this -> setLogHistory();
					$this -> ActiveCampaignId();
					$this -> CTI = new CTI($this -> Username); /** look in cc_agent **/
					$this -> CTI -> initExt();
					return true;
				}	
			}
			else
			{
				return false;
			}
		}
		
	/** get login session ****/
	
		private function setLogin()
		{
			$res = $this -> execute("UPDATE tms_agent SET logged_state='1', ip_address = '".$this->IpAddress."' WHERE id='".$this->Username."'");
			if( $res ) return true;
			else
				return false;
		}
		
	/** get login session ****/
		
		private function setCountLogin()
		{
			$res = $this -> execute("UPDATE tms_agent SET login_count=((login_count)+1) WHERE id='".$this->Username."'");
			if( $res ) return true;
			else
				return false;
		}
		
	/** get login session ****/
	
		private function setLogHistory($activity='LOGIN')
		{
			
			$SQL_Insert['UserId'] 		  	 = $this -> getSession('UserId');
			$SQL_Insert['ActivityLocation']  = $this -> getRealIpAddr();
			$SQL_Insert['ActivityAction'] 	 = $activity;
			$SQL_Insert['ActivitySessionId'] = session_id();
			$SQL_Insert['ActivityDateTs'] 	 = date('Y-m-d H:i:s');
			
			$qry = $this -> set_mysql_insert('tms_agent_activity',$SQL_Insert);
			if( $qry ) return true;
			else
				return false;
		}
		
		
	/** private function cek login on login Session **/
		
		private function ActiveCampaignId()
		{
			$sql = " SELECT 
						a.CampaignId,a.CampaignNumber,
						DATE(a.CampaignEndDate), date(NOW()) ,
						IF( datediff(date(a.CampaignEndDate),date(NOW()))<0,1,0) 
						as total_expired
						FROM t_gn_campaign a 
					WHERE a.CampaignStatusFlag=1";
						
			$qry = $this -> query($sql);				
			if( $qry -> result_num_rows() > 0 )
			{
				foreach( $qry -> result_assoc() as $rows )
				{
					if($rows['total_expired'])
					{
						$sql = " UPDATE t_gn_campaign a SET a.CampaignStatusFlag = 0
								 WHERE a.CampaignId='".$rows['CampaignId']."'";
								 
						if( $this -> execute($sql,__FILE__,__LINE__) )
						{
							return true;
						}
					}
				}
			}
			else{
				return false;
			}
		}
}
	
	
 /** required init login class **/
 
	$array_result = array('result'=>0);
	if(!is_object($UserLogin))
	{ 
		$UserLogin = new UserLogin();	
		if( $UserLogin -> loginOtherIp() )
		{
			if( $UserLogin -> initClass() ) 
				$array_result = array( 'result' => 1 );
			else
				$array_result = array( 'result' => 0 );
		}	
		else{
			$array_result = array( 'result' => 2 );
		}	
	}
	
	echo json_encode($array_result);
	
?>	