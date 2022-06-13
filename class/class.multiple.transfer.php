<?php

 require_once("../sisipan/sessions.php");
 require_once("../fungsi/global.php");
 require_once("../class/MYSQLConnect.php");
 require_once("../class/lib.form.php");
 

 class MultipleTransfer extends mysql{
	
	var $Forms;
	var $campaign_list_id;	
	var $campaign_onagent_id;	
	var $campaign_result_id;
	var $campaign_toagent_id;
	var $CampaignId;
	var $ResultId;
	var $OnAgentId;
	var $ToAgentId;
	var $SessionFilter;
	
						
	function __construct(){
		parent::__construct();
		$this -> Action = $this -> escPost('action');
		$this -> Forms = new jpForm(true);  
		
		if( $this->escPost('action')=='tpl_header'):
			$this -> campaign_list_id 		= explode(',',$this->escPost('campaign_list_id'));	
			$this -> campaign_onagent_id 	= explode(',',$this->escPost('campaign_onagent_id'));	
			$this -> campaign_result_id 	= explode(',',$this->escPost('campaign_result_id'));
			$this -> campaign_toagent_id 	= explode(',',$this->escPost('campaign_toagent_id'));
		endif;
		
		if( $this->havepost('action') ):
			$this -> CampaignId = $this -> ReturnSQL_IN(explode(',',$this->escPost('CampaignId')));
			$this -> ResultId	= $this -> ReturnSQL_IN(explode(',',$this->escPost('ResultId')));
			$this -> OnAgentId	= $this -> ReturnSQL_IN(explode(',',$this->escPost('OnAgentId')));
			$this -> ToAgentId	= explode(',',$this->escPost('ToAgentId'));
			$this -> setSessionFilter();
		endif;
	}
	
	
	function ReturnSQL_IN($array=''){
		$onInSQL = '';
		if( is_array($array)):
			$onInSQL = implode("','",$array);
		endif;
		
		return $onInSQL;	
	} 
	
	function setSessionFilter(){
		if( $this->getSession('handling_type')==1) $this-> SessionFilter = " AND b.AssignMgr IN('".$this -> OnAgentId."')";
		if( $this->getSession('handling_type')==2) $this-> SessionFilter = " AND b.AssignSpv IN('".$this -> OnAgentId."')";
		if( $this->getSession('handling_type')==3) $this-> SessionFilter = " AND b.AssignSelerId IN('".$this -> OnAgentId."')";
		if( $this->getSession('handling_type')==6) $this-> SessionFilter = " AND b.AssignMgr IN('".$this -> OnAgentId."')";
	}
	
	
	function CountSQL(){
		$sql = " SELECT count(a.CustomerId) as jumlah 
				 FROM t_gn_customer a 
					INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
					LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
					LEFT JOIN t_lk_callreason d on a.CallReasonId=d.CallReasonId 
				 WHERE 1=1 
					AND b.AssignMgr is not null 
					AND b.AssignSpv is not null 
					AND b.AssignSelerId is not null 
					AND a.CallReasonId is not null AND b.AssignBlock=0 
					AND c.CampaignStatusFlag=1 AND a.CampaignId IN('".$this -> CampaignId."') 
					AND a.CallReasonId IN('".$this -> ResultId."') ";
			
			$sql .= $this-> SessionFilter; 		 
			
			return $this->valueSQL($sql);		
	}
	
	
	function setSQL(){
		$sql = " SELECT a.CustomerId 
				 FROM t_gn_customer a 
					INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
					LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
					LEFT JOIN t_lk_callreason d on a.CallReasonId=d.CallReasonId 
				 WHERE 1=1 
					AND b.AssignMgr is not null 
					AND b.AssignSpv is not null 
					AND b.AssignSelerId is not null 
					AND a.CallReasonId is not null AND b.AssignBlock=0 
					AND c.CampaignStatusFlag=1 AND a.CampaignId IN('".$this -> CampaignId."') 
					AND a.CallReasonId IN('".$this -> ResultId."') ";
		
		$sql .= $this-> SessionFilter;
			
		return $sql;		
	}
	
	function index(){
		if( $this -> havepost('action')){
			switch($this -> Action)
			{
				case 'tpl_header'	 : $this -> HeaderTpl(); break;
				case 'transfer_data' : $this -> TransferData(); break;
			}
		}
	}
	
	
	function counterPage($QtyAllow=0, $start = 0,$v=0){
		$datas = array();
		//$sql = $this -> setSQL()."  LIMIT $start, $QtyAllow "; 
		$sql = $this -> setSQL()." AND b.AssignSelerId !='".$v."' ORDER BY RAND() LIMIT $start, $QtyAllow "; 
		
		$qry = $this ->execute($sql,__FILE__,__LINE__);
		
		$i=0;
		while( $row = $this->fetcharray($qry)){
			$datas[]= $row[0];	
			$i++;
		}
		return $datas;
	}
	
	
	
	function TransferData(){
		$valueDatas  = array();
		$AgentAssign = $this -> ToAgentId;
		$QtyCustomer = $this -> CountSQL();
		$QtyAgentId  = count($this -> ToAgentId);
		if( $QtyCustomer > 0)
		{
			$QtyAllow = ceil(($QtyCustomer)/($QtyAgentId));
			
			if( $QtyAllow > 0 ){
				$start = 0;
				foreach($AgentAssign as $k=>$v){
					$no = ($start* $QtyAllow);
					$valueDatas[$v] = $this -> counterPage($QtyAllow,$no,$v);
					$start++;
				}
			}
			
		}	
		
		
		
	/** set Update data With User && **/
	
		if( is_array($valueDatas) )
		{
			$TotalsUserAssign = 0;
			$TotalsDataAssign = 0;
			foreach($valueDatas as $UserId=>$datas){
				$TotalsDataAssign += $this->UpdateAssignMent($datas,$UserId);
				$TotalsUserAssign++;	
			}
		
		}
		
		echo 'To Count User :[ '.$TotalsUserAssign.' ] , With summary data [ '.$TotalsDataAssign.']';
	}
	
	
	
	
	function UpdateAssignMent($SizeData='',$UserId=''){
		if( is_array($SizeData) && ($UserId!='') ){
			
			$s_i = 0;
			foreach($SizeData as $key=> $CustomerId ){
				
				
				if( $this -> getSession('handling_type')==3 ){
					$sql['SellerId'] 	= $UserId;
					$whr['CustomerId']  = $CustomerId;
					
					$this -> set_mysql_update('t_gn_customer',$sql,$whr);
				}
				
				$datas['AssignMode'] = 'MOV';
				$datas['AssignDate'] = date('Y-m-d H:i:s');
				$datas[$this -> getColumns()] = $UserId;
				
				$where['CustomerId'] = $CustomerId;
				
				$this -> set_mysql_update('t_gn_assignment',$datas,$where);
				$this -> activityLog("Transfer to { ".$this->getFullname($UserId)." }, with Customer Id { {$CustomerId} }");
				
			  $s_i++;
			}
			
			return $s_i;
		}
	}
	
	
	function getFullname($UserId){
		$sql = " select a.full_name from tms_agent a where a.UserId=$UserId ";
		return $this->valueSQL($sql);
	}	
	
	
	/* function set style css **/
	
	function setCss(){?>
		<!-- start: css -->
			<style>
				.select { border:1px solid #dddddd;font-size:11px;background-color:#fffccc;height:100px;width:250px;}
				.input_text {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;
				 font-size:11px;height:20px;background-color:#fffccc;}
				.text_header { text-align:right;color:red;font-size:12px;}
				.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
			</style>
		<!-- stop: css -->
	<?php 
	}	
	
	
	function getColumns(){
		$s_a = array( 1=>'AssignMgr',2=>'AssignSpv',3=>'AssignSelerId',6=>'AssignMgr'); 
		if( $this->getSession('handling_type')!='' ):
			return $s_a[$this->getSession('handling_type')];
		endif;
	}
	
	function getListCampaign(){
		$sql = " SELECT 
					a.CampaignId, a.CampaignNumber, 
					a.CampaignName, a.CampaignStartDate, 
					a.CampaignEndDate, a.CampaignExtendedDate
				FROM t_gn_campaign a 
				WHERE (IF(( a.CampaignExtendedDate is null OR a.CampaignExtendedDate='0000-00-00 00:00:00'), 
					   date( a.CampaignEndDate)>=date(NOW()),
					   date( a.CampaignExtendedDate) >=date(NOW())))";
							
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $row = $this ->fetcharray($qry)){
			$datas[$row[0]] =  $row[1].' - '.$row[2];
		}
		
		return $datas;
	}
	
	
	function getLkReason(){
		
		$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a 
					left join t_lk_callreasoncategory b
					on a.CallReasonCategoryId=b.CallReasonCategoryId
					where a.CallReasonCategoryId NOT IN(2,5,6) ";
		
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $row = $this ->fetcharray($qry)){
			$datas[$row[0]] =  $row[1].' - '.$row[2];
		}
		
		return $datas;	
	}
	
	function getAgent(){
		if( $this->getSession('handling_type')==1)
			$sql = "select a.UserId, a.id, a.full_name from tms_agent a where a.user_state=1 and a.handling_type IN(2)";
			
		else if( $this->getSession('handling_type')==6) 
			$sql = "select a.UserId, a.id, a.full_name from tms_agent a where a.user_state=1 and a.handling_type IN(2)";
			
		else if( $this->getSession('handling_type')==2) 
			$sql = "select a.UserId, a.id, a.full_name from tms_agent a where a.user_state=1 and a.handling_type IN(3)";

		else if( $this->getSession('handling_type')==3) 
			$sql = "select a.UserId, a.id, a.full_name from tms_agent a where a.user_state=1 and a.handling_type IN(4)
					AND a.spv_id='".$this -> getSession('UserId')."'";		
		
		
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		while( $row = $this ->fetcharray($qry)){
			$datas[$row[0]] =  $row[1].' - '.$row[2];
		}
		
		return $datas;	
	}
	
	function HeaderTpl(){
		$this -> setCss();
		
		?>
			<fieldset style="border:0px">
				<div class="box-shadow" style="padding:4px;">
					<table cellpadding="5px;" width="75%" style="margin-top:5px;margin-bottom:5px;">
					
						<tr>
							<td class="text_header" valign="top"> Campaign ID</td>
							<td valign="top"><?php $this -> Forms -> jpMultiple('campaign_list_id','select',$this->getListCampaign(),$this -> campaign_list_id); ?> </td>
							<td class="text_header" valign="top">Call Result </td>
							<td valign="top"><?php $this -> Forms -> jpMultiple('campaign_result_id','select',$this->getLkReason(),$this -> campaign_result_id); ?></td>
						</tr>	

						<tr>
							<td class="text_header" valign="top">On Agent / SPV</td>
							<td valign="top"><?php $this -> Forms -> jpMultiple('campaign_onagent_id','select', $this->getAgent(),$this -> campaign_onagent_id); ?></td>
							<td class="text_header" valign="top"> To Agent / SPV </td>
							<td valign="top"><?php $this -> Forms -> jpMultiple('campaign_toagent_id','select',$this->getAgent()); ?></td>
						</tr>	
					</table>
					
				</div>
			</fieldset>
		
		<?php
		
	}
	
	
} 

	$MultipleTransfer = new MultipleTransfer();
	$MultipleTransfer -> index();	
?>