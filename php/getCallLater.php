<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__).'/../sisipan/parameters.php');

/*
 * getcalllater file
 * clas object for attribut run ajax session
 * running on the background only 
 * subproject : AJMI
 * author : omens 
 */
 
class getCallLater extends mysql
{
	var $CustomerId; 
	var $Tryagain;
	
	public function getCallLater()
	{
		parent::__construct();
		if( $this -> havepost('act') )
		{
			$this -> CustomerId = $this -> escPost('CustomerId');
			$this -> Tryagain	= $this -> escPost('calllaterdate');
			
			switch( $_REQUEST['act'] )
			{
				case 'select' 				: $this -> getAppoinmentCall(); 		break;
				case 'update'  				: $this -> updateCallLater(); 			break;
				case 'update-messages'  	: $this -> updateMessageBroadcast(); 	break;
				case 'get-broadcast-mesage' : $this -> getBroadCastMessage();		break;
				case 'get_verified'			: $this -> getVerifiedReminder(); 		break;
				case 'update_verifed'		: $this -> UpdateVerfied(); 			break;
			}
		}	
	}
	
/** updjate call later ****/
	
	function updateCallLater()
	{	
		$sql = " UPDATE t_gn_appoinment SET ApoinmentFlag = 1 
				 WHERE CustomerId = '".$this -> CustomerId."' 
				 and ApoinmentDate ='".$this -> Tryagain."' ";
				 
		if( $this -> execute($sql,__FILE__,__LINE__) ){
			return true;
		}
		else
			return false;
	}

/** get broadcast message *****/

	function getBroadCastMessage()
	{
		$datas= array();
		$sql = " select a.id as MsgId,  b.full_name as Username, a.message ,date_format(a.sent,'%d-%m-%Y %H:%i') as datetime
				 from tms_agent_msgbox a 
				 left join tms_agent b on a.`from`=b.UserId
				 where a.`to`='".$this -> getSession('UserId')."' and recd=0
				 ORDER BY a.id DESC";
		
		$qry = $this -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			$i =0;
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas['result'] = 1;
				$datas[$i]['msgid']	   = $rows['MsgId'];
				$datas[$i]['from']	   = $rows['Username'];
				$datas[$i]['message']  = $rows['message'];
				$datas[$i]['datetime'] = $rows['datetime'];
				$i++;
			}
		} 
		$total = $qry -> result_num_rows();
		if( $total > 0 ){
			$pesan['pesan'] = $datas;
		}
		else{
			$pesan['pesan'] = array('result'=>0);
		}	
		
		echo json_encode($pesan);	
	}
	
	
/** function update brodacast msg ***/

	function updateMessageBroadcast()
	{
		$sql =" Update tms_agent_msgbox SET recd=1 where id='".$_REQUEST['messageid']."'";
		if( $this -> execute($sql,__FILE__,__LINE__) )
		{
			return true;
		}
		else
			return false;
	}
	
	
/** UpdateVerfied ******************/

	function UpdateVerfied()
	{
		$sql = " UPDATE t_gn_verified_remider a SET a.VerifiedFlags =1 
				 WHERE a.VerifiedId = '".$_REQUEST['VerifiedId']."'";
		if( $this -> execute($sql,__FILE__,__LINE__) ) 
			return true;
		else
			return false;
	}

	
/** aqppoinment OR call back later ***/
	
	function getVerifiedReminder()
	{
		$result = array('CustomerId'=>'', 'CampaignId' => '', 'CustomerFirstName' => '','show' => 0,'query'=>'');
		$sql = "SELECT 
					a.VerifiedStatus, a.VerifiedId, b.CustomerId, 
					b.CustomerFirstName,b.CampaignId  
				FROM t_gn_verified_remider a 
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				LEFT JOIN t_gn_assignment c on a.CustomerId=c.CustomerId
				WHERE 1=1
				AND c.AssignSelerId='".$_SESSION['UserId']."'
				AND a.UserLevelId=10
				AND a.VerifiedFlags=0 ";
				
				
		$qry  = $this -> query($sql);
		$tot  = $qry -> result_num_rows();
		if( $tot > 0 )
		{
			$data  = $qry -> result_first_assoc();
			if( is_array($data) )
			{
				$result['VerifiedId'] 		 = $data['VerifiedId'];
				$result['CustomerId'] 		 = $data['CustomerId'];
				$result['CampaignId'] 		 = $data['CampaignId'];
				$result['VerifiedStatus'] 	 = $data['VerifiedStatus']; 
				$result['CustomerFirstName'] = $data['CustomerFirstName'];
				$result['show']	= $tot; 
				$result['query']	= $sql; 
			}
		}

		echo json_encode($result);	
	}	
	
/** aqppoinment OR call back later ***/
	
	function getAppoinmentCall()
	{
		$result = array('customer'=>'', 'campaignid' => '', 'customername' => '', 'show' => 0, 'tryagain' => '');
		
		$sql = "SELECT a.CustomerId, a.ApoinmentDate, b.CampaignId, b.CustomerFirstName
				FROM t_gn_appoinment a inner join t_gn_customer  b on a.CustomerId=b.CustomerId
				WHERE a.UserId = '".$this -> getSession('UserId')."' 
				AND (DATE_ADD(now(), INTERVAL '05:00' minute)>=a.ApoinmentDate) 
				AND NOW()<=a.ApoinmentDate 
				AND YEAR(a.ApoinmentDate)>0  
				AND a.ApoinmentFlag=0
				AND b.CallReasonId<>15";
				
				
		$qry  = $this -> query($sql);
		$tot  = $qry -> result_num_rows();
		if( $tot > 0 )
		{
			$data  = $qry -> result_first_array();
			if( is_array($data) )
			{
				$result['customer'] 	= $data[0];
				$result['campaignid'] 	= $data[2];
				$result['customername'] = $data[3];
				$result['show'] 		= $tot;
				$result['tryagain'] 	= $data[1];
				$result['status'] 	= 'Call Back later';
			}
		}

		echo json_encode($result);	
	}
}	

/** create object get callLater ****/
new getCallLater();

?>