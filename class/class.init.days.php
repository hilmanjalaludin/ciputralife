#!/usr/bin/php
<?php

/* require attribut file reference **/
	
	require("/opt/enigma/webapps/MIS/fungsi/global.php");
	require("/opt/enigma/webapps/MIS/class/MYSQLConnect.php");
	

/*  **********************************************************
	* @ Class Name : InitDays. 
	* @ Run On Shell Comander Under Unix system.
	* @ FUNC :
	*	 -   For check expired data by QA approval
	*		 and Campaign Active
    *	
	*	 -   Restart Service centerback && release IP on 
	*	     tms_agent table
	*	   
	* @ Author : Omens <jombi_par@yahoo.com>
	***********************************************************/

	
#cs : start Object Class 

class InitDays extends mysql{

	var $start_date;
	var $end_date;
	var $max_date;
	
 /** __constructor class **/
 
	function __construct(){
		parent::__construct();
		$this-> max_date = 5;
	}
	
	
 /* starting main index class **/
		
	function index(){
		$D_CUSTOMER = array();
		$V_CUSTOMER = $this -> getCustomerIcoming();
		
		foreach( $V_CUSTOMER as $V_KEY=>$V_VAL){
			$D_CUSTOMER[$V_KEY] = $this -> getIntervalDate($V_VAL);	
		}
		
		/** get interger by customer Incoming **/
		
		foreach($D_CUSTOMER as $CustomerId =>$IntervalDays){
			$this -> UpdateExpired5($CustomerId,$IntervalDays);
		}
		
	}
	
  /** update customer If >=5 **/
	
	private function UpdateExpired5($CustomerId='', $Interval=''){
		if( $CustomerId!='' && $Interval!=''){
			if( $Interval <= $this-> max_date ){
				$sql = "UPDATE t_gn_customer a SET a.InitDays='".$Interval."' 
						WHERE a.CustomerId ='".$CustomerId."' ";
			}
			else{
				$sql = "UPDATE t_gn_customer a 
							SET a.InitDays='".$Interval."',
								a.CustomerRejectedDate=NOW() 
							WHERE a.CustomerId ='".$CustomerId."' ";
			}
			
			$query = $this -> execute($sql,__FILE__,__LINE__);
			if( $query ) return true;
			else return false;
		}
	}
	
	public function UpdateActiveCampaign(){
		$sql =" SELECT a.CampaignId, a.CampaignStartDate, a.CampaignEndDate, a.CampaignExtendedDate,
					date(IF(a.CampaignExtendedDate is null, a.CampaignEndDate, a.CampaignExtendedDate)) as valDate
				FROM t_gn_campaign a 
				WHERE a.CampaignStatusFlag <>0 
				HAVING date(valDate) < date(NOW()) ";
				
						
		$qry = $this ->execute($sql,__FILE__,__LINE__);
		while($rows = $this ->fetchrow($qry)){
			
			$sql = " update t_gn_campaign b SET b.CampaignStatusFlag = 0
						where b.CampaignId='".$rows->CampaignId."'";
			$this ->execute($sql,__FILE__,__LINE__);			
		} 	
		return true;
	}
	
	/** get to code incoming **/
	
	function getToCodec($callResultCode=''){
		$sql = " select a.CallReasonId from t_lk_callreason a 
				 where a.CallReasonCategoryId=5
				 and a.CallReasonCode='".$callResultCode."' 
				 and a.CallReasonLevel=3";
		return $this -> fetchval($sql,__FILE__,__LINE__);
	}
	
	
	/** get from code incoming **/
	
	function getFromCodec($customerId=''){
		$sql =" select b.CallReasonCode from t_gn_customer a left join t_lk_callreason b on a.CallReasonId=b.CallReasonId 
				where a.CallReasonId is not null 
				and a.CustomerId ='".$customerId."'";
				
		$call_result = $this -> fetchval($sql,__FILE__,__LINE__);	
		if( $call_result ):	
			return $this -> getToCodec($call_result);
		endif;	
	}
	
	/* 
		get data customer in status Interest and pending 
		on table t_gn_customer 
	**/
	
	function getCustomerIcoming(){
		$customer = '';
		
		$sql = " select a.CustomerId as init_data, date(a.CustomerUpdatedTs)  as end_date,
				 date(now()) as start_date 
				 from t_gn_customer a where a.CallReasonId in (16,17,39,40) ";
		
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $row = $this -> fetchrow($qry)){
			$customer[$row -> init_data]['start_date'] = $row -> start_date;
			$customer[$row -> init_data]['end_date']   = $row -> end_date; 
		}
		
		return $customer;
	} 
	
	/* 
		get Diff Date on two beetween date
		
	**/
	
	function datediff($d1, $d2){  
		$d1 = (is_string($d1) ? strtotime($d1) : $d1);  
		$d2 = (is_string($d2) ? strtotime($d2) : $d2);  
		$diff_secs = abs($d1 - $d2);  
		$base_year = min(date("Y", $d1), date("Y", $d2));  
		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);  
	  return array( "years" => date("Y", $diff) - $base_year,  "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,  "months" => date("n", $diff) - 1,  "days_total" => floor($diff_secs / (3600 * 24)),  "days" => date("j", $diff) - 1,  "hours_total" => floor($diff_secs / 3600),  "hours" => date("G", $diff),  "minutes_total" => floor($diff_secs / 60),  "minutes" => (int) date("i", $diff),  "seconds_total" => $diff_secs,  "seconds" => (int) date("s", $diff)  );  
	} 
	
	/* 
		Calculation day in Integer day by assumsion 
		wit maximum in bucket <=5 
	**/

	function getIntervalDate($datas=''){

		if( is_array($datas)){
			$interval = $this -> datediff($datas['end_date'],$datas['start_date']);
			
			if(is_array($interval)):
				return $interval['days']; 
			endif;
		}
	}
	
	function activityUpdate(){
		$sql = "update cc_agent_activity a SEt a.login_time=null, a.ext_status=null, a.ext_number=null,
				a.logout_time=NOW(), a.`status`=null ";
		$qry = $this->execute($sql,__FILE__,__LINE__);
	}
	
	public function ServiceCenterbackRestart(){
		
		$CenterBackScript = " service centerback restart ";
		if( !empty($CenterBackScript) ){
			
			$sql = " update tms_agent a SET a.ip_address=null , logged_state='0' ";
			$query = $this -> execute($sql,__FILE__,__LINE__);
			if( $query ) {
				$this -> activityUpdate();
				shell_exec($CenterBackScript);
				return true;
			}
		}
	
	}
	
}

# ce : end Object Class 
	

	$InitDays= new InitDays();
	
	 /** 
	   @ init days 
	     function with result Boolean
	 **/
	 
		$InitDays -> index();

	/** 
	  @ init campaign  
		function with result Boolean
	**/
		
		$InitDays -> UpdateActiveCampaign();
		
	/** 
	  @ restart centerback services 
	  function with result Boolean
	**/	
	
	//$InitDays -> ServiceCenterbackRestart();
?>
