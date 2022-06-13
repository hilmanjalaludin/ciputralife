<?php

class Users extends mysql
{
	private $UserData = array();
	
	function Users(){}

/** get all TM **/
	
function getAllAgent()
{
	$sql = " SELECT * FROM tms_agent a WHERE a.handling_type = 4";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$datas[$rows['UserId']] = $rows['full_name'];
	}
	
	return $datas;
}

function getAgentByCampaign($cmp,$startDate,$endDate)
{
	$sql = "select a.CustomerFirstName, a.CampaignId, a.SellerId, b.full_name, count(a.SellerId)  
			from t_gn_customer a 
			left join tms_agent b on a.SellerId = b.UserId
			left join t_gn_callhistory c on a.CustomerId = c.CustomerId
			where a.CampaignId = ".$cmp."
			and a.SellerId is not null
			AND date(c.CallHistoryCreatedTs)>='".$startDate."'
			AND date(c.CallHistoryCreatedTs)<='".$endDate."'
			group by a.SellerId";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$datas[$rows['SellerId']] = $rows['full_name'];
	}
	
	return $datas;
}
	
/** get user level privileges **/
	
 function getUserLevel()
	{
		$sql = " select a.id, a.name from tms_agent_profile a order by a.id ASC ";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['id']] = $rows['name'];
		}
		
		return $datas;
	}

/** get getUsers obejct class **/
	
function getUsers($UserId = 0 )
	{
		return new UserDetail($UserId);
	} 

function getUserBySpv($spvid=0)
	{
		$sql = " select a.UserId, a.UserId from tms_agent a 
				 where a.spv_id='$spvid' 
				 and a.user_state=1 ";
				 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$datas[$rows['UserId']] = $rows['UserId'];
		}
		return $datas;	
		
	}	
}

/** new classs factory methode ****/

class UserDetail extends Users
{
	private $UserDetail = array(); 
	
/** get UserId **/
 function UserDetail($UserId=0)
	{
		$sql = " SELECT * FROM tms_agent a WHERE a.UserId='$UserId'";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			$this -> UserDetail = $qry -> result_first_assoc();		
		}
	}

/** function cek data ***/
function isAvailable()
{
	if(count($this -> UserDetail) > 0 ){
		return true;
	}
	else{
		return false;
	}
}	

/** get userid ******/

 function getUserId()
 {
	return $this -> UserDetail['UserId'];
 
 }
 
 /** get userid ******/
 function getUserName()
 {
	return $this -> UserDetail['id'];
 }
 
 /** get full_name ******/

 function getFullname()
 {
	return $this -> UserDetail['full_name'];
 }
  
 
 /** get profile_id ******/

 function getProfileId()
 {
	return $this -> UserDetail['profile_id'];
 }
  
 /** get init_name ******/

 function getInitName()
 {
	return $this -> UserDetail['init_name'];
 }
  
 /** get mgr_id ******/

 function getManagerId()
 {
	return $this -> UserDetail['mgr_id'];
 }
    
 /** get spv_id ******/

 function getSupervisorId()
 {
	return $this -> UserDetail['spv_id'];
 }
 
 /** get user_state ******/

 function getUserstate()
 {
	return $this -> UserDetail['user_state'];
 }
 
 /** get logged_state ******/

 function getLogedState()
 {
	return $this -> UserDetail['logged_state'];
 }
}	
?>