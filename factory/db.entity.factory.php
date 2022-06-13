<?php
/**
	class untuk get properties 
	yang di perlukan system dalam 
	database  
*/

class Entity extends mysql
{
	function Entity()
	{
		//
	}
	
	function LevelReason($ReasonId)
	{
		$sql = " select a.CallReasonLevel from t_lk_callreason a  where a.CallReasonId='$ReasonId'";
		$qry = $this -> query($sql);
		return $qry -> result_get_value('CallReasonLevel');
	}
	
/** function getCall Reason id ** category **/

	function getCustomerReason($CustomerId=0)
	{
		$sql = "SELECT b.CallReasonCategoryId, b.CallReasonId,
						concat(b.CallReasonDesc,' - ', b.CallReasonCode) as ReasonName
				FROM t_gn_customer a 
				INNER JOIN t_lk_callreason b on a.CallReasonId=b.CallReasonId
				WHERE a.CustomerId= '$CustomerId' ";
		$qry = $this -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			$datas['CategoryId'] = $qry -> result_get_value('CallReasonCategoryId');
			$datas['ReasonName'] = $qry -> result_get_value('ReasonName');
			$datas['ReasonId']   = $qry -> result_get_value('CallReasonId');
		}
		
		return $datas;	
	}
	
	function getWAReason($CustomerId=0)
	{
		$sql = "SELECT b.Id, b.`Desc`, concat(b.Desc,' - ',b.`code`) AS ReasonName
				FROM t_gn_customer a
				INNER JOIN t_lk_wa_email  b ON a.wa_email_status=b.Id
				WHERE a.CustomerId= '$CustomerId' ";
		$qry = $this -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			$datas['CategoryId'] = $qry -> result_get_value('Id');
			$datas['ReasonName'] = $qry -> result_get_value('ReasonName');
			$datas['ReasonId']   = $qry -> result_get_value('Id');
		}
		
		return $datas;	
	}	
	
/** ReasonLabelQuality **/
	
	function VerifiedConfirm()
	{
		$sql = " SELECT a.ApproveId FROM t_lk_aprove_status a WHERE a.AproveFlags=1 and a.ConfirmFlags=1";
		$qry = $this -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			return $qry -> result_singgle_value();
		}
	}
	
/** ReasonLabelQuality **/
	
	function VerifiedNotEskalasi()
	{
		$sql = " SELECT a.ApproveId, a.AproveName FROM t_lk_aprove_status a WHERE a.ApproveEskalasi=0 
				 and a.AproveFlags=1
				 and a.ConfirmFlags=0";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{	
			$datas[$rows['ApproveId']] = $rows['AproveName'];  	
		}
		
		return $datas;
	}
		
	
	
	/** function get verfied Eskalasi **/
	
	function getEskalasiData()
	{
		$sql = " SELECT a.UserLevelEskalasi, a.ApproveId , a.AproveName, a.UserLevelEskalasi, b.name
				 FROM t_lk_aprove_status a 
				 LEFT JOIN tms_agent_profile b on a.UserLevelEskalasi=b.id
				 WHERE a.ApproveEskalasi=1";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{	
			$datas[][$rows['UserLevelEskalasi']] = $rows['ApproveId'];  	
		}
		return $datas;
	}	
	
/** suspend confirm ***/
	
	function SuspendConfirm()
	{
		$sql = " SELECT a.ApproveId FROM t_lk_aprove_status a WHERE a.AproveFlags=1 and a.SuspendFlags=1";
		$qry = $this -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			return $qry -> result_singgle_value();
		}
	}
	
/** get aupload nav **/
	function getTemplate()
	{ 
		$datas = array();
		
		$sql = " select a.TemplateId, a.TemplateName from tms_tempalate_upload a where a.TemplateFlags=1 ";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['TemplateId']] = $rows['TemplateName'];
		}
		return $datas;
	}
	
/** ReasonLabelQuality **/
	
	function ReasonLabelQuality()
	{
		$sql = " SELECT a.ApproveId, a.AproveName FROM t_lk_aprove_status a WHERE a.AproveFlags=1 and a.ConfirmFlags=0 and a.CancelFlags != 1 and a.ApproveId IN(1,2,3)";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['AproveName']; 
		}
		
		return $datas;
	}
	
	function ReasonLabelQualityFa()
	{
		$sql = " SELECT a.ApproveId, a.AproveName FROM t_lk_aprove_status a WHERE a.AproveFlags=1 and a.ConfirmFlags=0 and a.CancelFlags != 1 and a.ApproveId IN(14,15,16)";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['AproveName']; 
		}
		
		return $datas;
	}

	function ReasonLabelQualityTna()
	{
		$sql = " SELECT a.ApproveId, a.AproveName FROM t_lk_aprove_status a WHERE a.AproveFlags=1 and a.ConfirmFlags=0 and a.CancelFlags != 1 and a.ApproveId IN(17,18,19)";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['AproveName']; 
		}
		
		return $datas;
	}

	function ReasonLabelQualityMtf()
	{
		$sql = " SELECT a.ApproveId, a.AproveName FROM t_lk_aprove_status a WHERE a.AproveFlags=1 and a.ConfirmFlags=0 and a.CancelFlags != 1 and a.ApproveId IN(20,21,22)";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['AproveName']; 
		}
		
		return $datas;
	}
	
	
	function ReasonLabelQualitySuspend()
	{
		$sql = " SELECT a.ApproveId, a.AproveName FROM t_lk_aprove_status a WHERE a.AproveFlags=1 and a.ConfirmFlags=0 and a.SuspendFlags = 2";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['AproveName']; 
		}
		
		return $datas;
	}
	
	function SuspendSelling()
	{
		$sql = " SELECT a.ApproveId, a.AproveName FROM t_lk_aprove_status a WHERE a.AproveFlags=1 
				 and a.FromUserLevelEskalasi='".USER_QUALITY."'
				 and a.ToUserLevelEskalasi='".USER_QUALITY."'";
					 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['AproveName']; 
		}
		
		return $datas;
	}
	
	function ReasonLabelQualityCancel()
	{
		$sql = " SELECT a.ApproveId, a.AproveName FROM t_lk_aprove_status a WHERE a.AproveFlags=1 and a.ConfirmFlags=0 and a.CancelFlags = 1";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['AproveName']; 
		}
		
		return $datas;
	}

/** status ReasonId on QA **/
	
	function ReasonQA()
	{
		$sql = " SELECT a.ApproveId FROM t_lk_aprove_status a WHERE a.AproveFlags=1";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['ApproveId']; 
		}
		
		return $datas;
	}
	


/** status ReasonId on QA **/
	
	function qulityBackAgent()
	{
		$sql = " SELECT a.ApproveId FROM t_lk_aprove_status a 
				 WHERE a.AproveFlags=1 and a.ApproveEskalasi=1
				 AND a.ConfirmFlags=0 ";
				 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['ApproveId']; 
		}
		
		return $datas;
	}
	
	
	
/** function result qa ***/

	public function getAproveName($StatusId=0)
		{
			$sql ="select a.AproveName from t_lk_aprove_status a where a.ApproveId='$StatusId'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				return $qry -> result_get_value('AproveName');
			}
		}
		
	
	
/** get tore campaign */

	function get_list_campaignId()
	{	
		$sql = " select * from t_gn_campaign a where 1=1 ";
				 
		$qry = $this -> query($sql);		 
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['CampaignId']] = array
			(
				'CampaignNumber' => $rows['CampaignNumber'],
				'CampaignName' => $rows['CampaignName'],
				'CampaignStartDate' => $rows['CampaignStartDate'],
				'CampaignEndDate'=> $rows['CampaignEndDate']
			); 
		}
		
		return $datas;
	}		
	
/** get reason id name **/

	function get_list_reasonid()
	{
		$sql =" select 
				a.CallReasonId, a.CallReasonCode,
				a.CallReasonDesc, 
				b.CallReasonCategoryId,
				b.CallReasonCategoryName,
				b.CallReasonCategoryCode
			from t_lk_callreason a 
			left join t_lk_callreasoncategory b on a.CallReasonCategoryId=b.CallReasonCategoryId
			where 1=1
			and a.CallReasonStatusFlag=1
			and b.CallReasonCategoryFlags=1 ";
		
		$qry = $this -> query($sql);		 
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = array
			(
				'CallReasonId' => $rows['CallReasonId'],
				'CallReasonCode' => $rows['CallReasonCode'],
				'CallReasonDesc' => $rows['CallReasonDesc'],
				'CallReasonCategoryId' => $rows['CallReasonCategoryId'],
				'CallReasonCategoryName'=> $rows['CallReasonCategoryName'],
				'CallReasonCategoryCode'=> $rows['CallReasonCategoryCode']
			); 
		}
		
		return $datas;	
	}
	
/** status ReasonId **/
	
	function ReasonId()
	{
		$sql = " select a.CallReasonId from t_lk_callreason a where a.CallReasonStatusFlag=1";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
		}
		
		return $datas;
	}

	
/** status call back **/
	
	function getCallBack()
	{
		$sql = " select a.CallReasonId from t_lk_callreason a where a.CallReasonLater=1 and a.CallReasonStatusFlag=1 ";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
		}
		
		return $datas;
	}
	
/** status sale */	
	
	function getSale()
	{
		$sql = " select a.CallReasonId from t_lk_callreason a where a.CallReasonEvent=1 and a.CallReasonStatusFlag=1";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
		}
		
		return $datas;
	}
	
/** status selain sale  */	

	function getExactlySale()
	{
		$sql = " select a.CallReasonId from t_lk_callreason a where a.CallReasonEvent NOT IN(1) and a.CallReasonStatusFlag=1";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
		}
		
		return $datas;
	}
	
/** status selain Call back dan && 	sale **/

	function getExactly()
	{
		$sql = " select a.CallReasonId from t_lk_callreason a where a.CallReasonLater!=1 AND  a.CallReasonEvent!=1 and a.CallReasonStatusFlag=1";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
		}
		
		return $datas;
	}
	
/** return implode $sql **/
	
	function CallBackWithIn()
	{
		return implode("','", array_values($this -> getCallBack()) );
	}
	
/** return implode $sql **/
	
	function SaleWithIn()
	{
		return implode("','", array_values($this -> getSale()) );
	}	
	
	
/** verified status not Backin agent **/
	function VerifiedNotBack()
	{
		return implode("','", array_keys( $this -> VerifiedNotEskalasi() ) ); 
	}	
	
/** return implode $sql **/
	
	function ExactlyWithIn()
	{
		return implode("','", array_values($this -> getExactly()) );
	}	
	
	
	
/** function getAllCampaign 
 ** date 2013-11-03
 ** author :omens
 **/
	
	function getActiveCampaign()
	{
		$sql = " SELECT * from t_gn_campaign a where a.CampaignStatusFlag=1 ";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() ){
			return $qry;
		}
		else
			return false;
	}	
	
/** function get spesific campaign 
 ** date 2013-11-03
 ** author :omens
**/
	
	function getSpesificCampaign($CampaignId=0)
	{
		$sql = " SELECT * FROM t_gn_campaign a WHERE a.CampaignId ='$CampaignId'";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() ){
			return $qry;
		}
		else
			return false;
	}

	
/** status selain sale  
 ** date 2013-11-03
 ** author :omens
*/	

	function FollowUp()
	{
		$sql = " select 
					a.CallReasonId, a.CallReasonDesc 
					from t_lk_callreason a where a.CallReasonFollowUp=1";
		$qry = $this -> query($sql);
		$datas['0']='New Data';
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = $rows['CallReasonDesc']; 
		}
		
		return $datas;
	}	
	
	function ArrayFollowUp()
	{
		$sql = " select 
		a.CallReasonId from t_lk_callreason a where a.CallReasonFollowUp=1 AND a.CallReasonId NOT IN (15,23)";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
		}
		
		return $datas;
	}	
	
	function ImplArrFollowUp()
	{
		return implode("','", array_values($this -> ArrayFollowUp()) );
	}
	
/** get assignCampaign 
 ** date 2013-11-03 
 ** author :omens
**/

 function getCampaignByAgentId($UserId)
 {
	$sql=" select a.CampaignId from t_gn_customer a 
			inner join t_gn_assignment b on a.CustomerId=b.CustomerId
			left join t_gn_campaign c on a.CampaignId = c.CampaignId
			where b.AssignSelerId='$UserId'
			and c.CampaignStatusFlag = 1
			group by a.CampaignId ";
	//echo $sql;		
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ){
		$datas[$rows['CampaignId']] = $rows['CampaignId']; 
	}
	return $datas;	
 }	
	
/** get assignCampaign 
 ** date 2013-11-03 
 ** author :omens
**/
	
	function getAssigmentCampaign( $UserId=0 )
	{
		$datas_result = array();
		foreach( $this -> getCampaignByAgentId($UserId) as $key => $CampaignId )
		{
			$sql = " SELECT COUNT(a.CampaignId) as cnt, a.CampaignId, a.CampaignMode 
					 FROM t_gn_assign_campaign a 
					 WHERE a.CampaignId ='$CampaignId'  
					 AND a.AssignSellerId='$UserId'";
					 
			$qry = $this -> query($sql);
			if( $qry -> result_get_value('cnt') > 0 )
			{
				if( $qry -> result_get_value('CampaignMode')=='Y')
				{
					$RowsData = $this -> getSpesificCampaign($CampaignId );
					$datas_result[$CampaignId] =  $RowsData -> result_get_value('CampaignName');
				}
			}
			else
			{
				$RowsData = $this -> getSpesificCampaign($CampaignId);
				$datas_result[$CampaignId] =  $RowsData -> result_get_value('CampaignName');
			}
		}
		//print_r($datas_result);
		return $datas_result;
	}	
	
	
/** getEskalasi data from QA to folloup by agent **/

	function getEskalasiStatus($FromUsers=0, $ToUsers=0)
	{
		$sql = "  SELECT a.ApproveId, a.AproveName from t_lk_aprove_status a  
				  where a.ApproveEskalasi=1
				  and a.FromUserLevelEskalasi='$FromUsers'
				  and a.ToUserLevelEskalasi='$ToUsers'";
		$qry = $this -> query($sql);		 
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['ApproveId']] = $rows['AproveName'];
		}
	return $datas;	
	}
	
	// get QC status for axa
	function getQCstatus()
	{
		$sql = "SELECT a.StatusQCid, a.StatusQCcode, a.StatusQCdesc FROM t_lk_qcstatus a
				WHERE a.StatusQCflags = 1 ";
		
		if( $this->getSession('handling_type') == 4 )
		{
			$sql .= "AND a.StatusQCcancel = 1";
		}
		
		$qry = $this -> query($sql);		 
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['StatusQCid']] = $rows['StatusQCdesc'];
		}
		return $datas;	
	}
}
?>