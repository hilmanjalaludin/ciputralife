<?php
class ParameterQuery extends mysql
{
	function ParameterQuery(){ 
		parent::__construct();
	}
	
	function getParameter()
	{
		if(isset($_SESSION))
		{
			$sql = " SELECT a.`query` as str 
					 FROM t_gn_query a 
					 where a.query_level=".$this -> getSession('handling_type')." 
					 AND a.query_section='distribusi'";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			
			if( $qry && ($row = $this -> fetchassoc($qry)) )
			{
				$datas1 = str_replace('{SESSION_USER}',$this -> getSession('UserId'),$row['str']);
				$datas2 = str_replace('{CAMPAIGN_ID}',$_REQUEST['campaignId'],$datas1);
				return $datas2;
			}
		}
		
		
	}
	
	function getQueryList()
	{
		if(isset($_SESSION))
		{
			$sql = " SELECT a.`query` as str 
					 FROM t_gn_query a where a.query_level=".$this -> getSession('handling_type')." 
					 AND a.query_section='querylist'";
					 
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			if( $qry && ($row = $this -> fetchassoc($qry)) )
			{
				$datas1 = str_replace('{SESSION_USER}',$this -> getSession('UserId'),$row['str']);
				$datas2 = str_replace('{CAMPAIGN_ID}',$_REQUEST['CampaignNumber'],$datas1);
				return $datas2;
			}
		}
	}
	
	function getStatusQA()
	{
		$sql = " select * from t_lk_callreason a where a.CallReasonEvent=1";
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $rows = $this -> fetchassoc($qry))
		{
			$datas[] = $rows['CallReasonId']; 
		}
		return $datas;
	}
	
	function getStatusQR()
	{
		$sql = " select * from t_lk_aprove_status a where a.ApproveId in (2,13)";
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $rows = $this -> fetchassoc($qry))
		{
			$datas[] = $rows['ApproveId']; 
		}
		return $datas;
	}
	
	function getStatusQA2()
	{
		$sql = " select * from t_lk_callreason a where a.CallReasonEvent=1";
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $rows = $this -> fetchassoc($qry))
		{
			$datas[] = $rows['CallReasonId']; 
		}
		return $datas;
	}
	
	function ImplodeStatus()
	{
		$status = self::getStatusQA();
		if( is_array($status) )
		{
			return implode(",",array_values($status));
		}
	}
	
	function ImplodeStatusQR()
	{
		$status = self::getStatusQR();
		if( is_array($status) )
		{
			return implode(",",array_values($status));
		}
	}
	
	function ImplodeStatus2()
	{
		$status = self::getStatusQA2();
		if( is_array($status) )
		{
			return implode("','",array_values($status));
		}
	}
}	
?>