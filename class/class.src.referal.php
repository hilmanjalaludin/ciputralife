<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require("../plugin/lib.form.php");

	/*
	 *	class untuk action call result
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	
	
	class Quality extends mysql
	{
		var $action;
		var $customer;
		var $userid;
		var $JPForm;
		
		function __construct()
		{
			parent::__construct();
			
			$this->action 	= $this->escPost('action');
			$this->customer	= $this->escPost('cust_id');
			
		}
		
	/* function set style css **/
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
					.input_text {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-color:#fffccc;}
					.text_header { text-align:right;color:#746b6a;font-size:12px;}
					.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
				</style>
				
			<!-- stop: css -->
		<?php }
		
		
		function initClass()
		{
			$this -> JPForm = new jpForm();
			if( $this->havepost('action')):
				switch( $this->action)
				{
					case 'tpl_onready' 		: $this -> tplOnReady();    break;
					case 'tpl_delete'  		: $this -> tplResultRemove(); break;
					case 'validation_check'	: $this -> ValidationCheck(); break;
					case 'approve_all'		: $this -> ApproveAll(); break;
				}
			endif;
		}
		
		function get_approval_status()
		{
			return $approve_status = array(
					'null' => 'New Referal',
					0 	   => 'Approved',
					1      => 'Rejected'
				);
		}
		
		function ValidationCheck()
		{
			$result = array('result'=>0);
			
			$sql = " select * from t_gn_customer a where a.CustomerId ='".$_REQUEST['CustomerId']."'";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			if( $qry && ( $rows = $this -> fetchassoc($qry) ) )
			{
				$result = array( 'result' => $rows['CustomerReferalProsess'] );
			}
			
			echo json_encode($result);
		}
		
		private function SaveReminderVerifed($cust = '')
		{	
			$SQL_insert['CustomerId'] = $cust; 
			$SQL_insert['VerifiedStatus'] = '1'; 
			$SQL_insert['UserLevelId'] = $_SESSION['handling_type'];
			$SQL_insert['VerfiedCreatedTs'] = date('Y-m-d H:i:s');
			if( $this -> set_mysql_insert('t_gn_verified_remider',$SQL_insert)) 
				return true;
			else
				return false;
				
		}
		
		private function QualitySaveScore($cust = ''){
		
			$SQL_Insert['CustomerId']		= $cust; 
			$SQL_Insert['CollmonUser']		= $this -> getSession('UserId'); 
			$SQL_Insert['CollmonResultId']	= ''; 
			$SQL_Insert['CollmonPoint']		= ''; 
			$SQL_Insert['CollmonNotes']		= '.:Hasil dari Approval All:.'; 
			$SQL_Insert['CollmonCreateTs']	= date('Y-m-d H:i:s'); 
			
			if( $this -> set_mysql_insert('coll_report_collmon',$SQL_Insert,$SQL_Insert) )
				return true;
			else
				return false;
		}
		
		function ApproveAll()
		{
			//status Approve
			//$userid		= $this -> getSession('UserId');
			$customer = explode(",",$this -> customer);
			print_r($customer);
			$time = date("Y-m-d H:i:s");
			$i = 0;
			foreach($customer as $a => $b){
				$data 	= array('CallReasonQue'=>1,'QueueId'=>$userid,'CustomerRejectedDate'=>date("Y-m-d H:i:s"));
				$where 	= array('CustomerId'=>$b);
				
				$result	= $this->set_mysql_update('t_gn_customer',$data,$where);
				//$this -> SaveReminderVerifed($b);
				$this -> QualitySaveScore($b);
				if( $result ) $i++;
			}
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
			/*$status		= '1';
			$customer	= $this->escPost('cust_id');
			$userid		= $this -> getSession('UserId');
			$sql = " UPDATE t_gn_customer 
						SET 
							CallReasonQue='$status', 
							QueueId='$userid', 
							CustomerRejectedDate=NOW()
					 WHERE CustomerId ='$customerid'";
			$this -> execute($sql,__FILE__,__LINE__);
			$this -> SaveReminderVerifed();*/
		}
		
		function getUserList($cond='')
		{
			if( $this->getSession('handling_type')==1 ){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 ";
			}
			else if( $this->getSession('handling_type')==2){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 
						and a.handling_type in('3','4') 
						and a.mgr_id='".$this->getSession('UserId')."'";
			}
			else if( $this->getSession('handling_type')==3){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 
						and a.spv_id='".$this->getSession('UserId')."' 
						and a.handling_type='4'";
			}
			else if( $this->getSession('handling_type')==4){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 
						and a.userid ='".$this->getSession('UserId')."' 
						and a.handling_type='4'";
			}
			
			if( $sql ){
				$query = $this ->execute($sql,__FILE__,__LINE__);
				while( $row = $this->fetchrow($query))
				{
					if( ($this->getSession('UserId')==$row->UserId) || ($cond==$row->UserId) ):
						echo "<option value=\"{$row->UserId}\" selected>{$row->id} - {$row->full_name}</option>";
					else :
						echo "<option value=\"{$row->UserId}\">{$row->id} - {$row->full_name}</option>";
					endif;	
				}
			
			}
		}
		
		private function getCampaignAssigment($cond='')
		{
			$sql = "select a.CampaignId, a.CampaignNumber,a.CampaignName from t_gn_campaign a
					where date(if(a.CampaignEndDate is null, a.CampaignExtendedDate,a.CampaignEndDate)) >= date(now())
					order by a.CampaignId DESC";
					
			$qry = $this ->execute($sql,__FILE__LINE__);
			while( $row = $this ->fetchrow($qry)){
				if( $row->CampaignId == $cond){
					echo "<option value=\"{$row->CampaignId}\" selected>{$row->CampaignNumber} - {$row->CampaignName} </option>";
				}
				else
					echo "<option value=\"{$row->CampaignId}\">{$row->CampaignNumber} - {$row->CampaignName}</option>";
			}	
		}
		
		private function getResultStatus($cond='')
		{
			$sql = "SELECT 
						a.ReferalId,
						a.ReferalQAStatus=1 AS approve, 
						a.ReferalQAStatus=0 AS reject
					FROM t_gn_referal a";
					
			$qry = $this->execute($sql,__file__,__line__);
			
			
			while( $res = $this->fetchrow($qry) )
			{
				if( $res -> ReferalId ==$cond ):
					echo "<option value=\"{$res -> ReferalId}\" selected>{$res -> ReferalQAStatus}</option>"; // $datas[$res -> CallReasonId] = $res -> CallReasonDesc; 
				else:
					echo "<option value=\"{$res -> ReferalId}\">{$res -> ReferalQAStatus}</option>"; // $datas[$res -> CallReasonId] = $res -> CallReasonDesc;
				endif;
			}
		}
	
		function tplOnReady()
		{ 
			global $db;
			$this->setCss();
		?>
			<!-- start : jQuqery -->
			
			<script>
				$(document).ready(function(){
					alert("hello");
				});
				
			</script>
			
			<!-- stop : jQuqery -->
			
			
			<div id="result_content_add" class="box-shadow" style="padding-bottom:4px;margin-top:2px;margin-bottom:8px;">
				<table cellpadding="3px;">
					<tr>
						<td class="text_header"> Referal Name</td>
						<td>
							<input type="text" name="cust_number" id="cust_number" 
								   value="<?php echo ($this->havepost('cust_number')?$this->escPost('cust_number'):'');?>" 
								   class="input_text" style="width:180px;height:18px;margin-right:2px;">
						</td>
						<td class="text_header"> Referal Phone Number 1</td>
						<td>
							<input type="text" name="home_phone" id="home_phone" 
								   value="<?php echo ($this->havepost('home_phone')?$this->escPost('home_phone'):'');?>"
								   class="input_text" style="width:180px;height:18px;margin-right:2px;">
						</td>
						
						<td class="text_header"> Campaign</td>
						<td>
							<select name="campaign_id" id="campaign_id" class="select" style="width:300px;">
								<option value=""> -- Choose --</option>
								<?php $this -> getCampaignAssigment( $this->escPost('campaign_id') ); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="text_header"> Customer Name </td>
						<td>
							<input type="text" name="cust_name" id="cust_name" 
								   value="<?php echo ($this->havepost('cust_name')?$this->escPost('cust_name'):'');?>"
								   class="input_text" style="width:180px;height:18px;">
						</td>
						<td class="text_header"> Referal Phone Number 2 </td>
						<td>
							<input type="text" name="office_phone" id="office_phone" 
								   value="<?php echo ($this->havepost('office_phone')?$this->escPost('office_phone'):'');?>"
								   class="input_text" style="width:180px;height:18px;">
						</td>
						<td class="text_header"> Approval Status </td>
						<td><?php $this -> JPForm -> jpCombo('call_result','select',$this->get_approval_status(),NULL,NULL);?></td>
					</tr>
					
					<tr>
						<td class="text_header"> Interval </td>
						<td>
							<input type="text" name="cust_dob" id="cust_dob" 
								   value="<?php echo ($this->havepost('cust_dob')?$db->Date->date_time_indonesia($this->escPost('cust_dob')):'');?>" class="input_text" style="width:160px;height:18px;display:none;">
								   
							<input type="text" name="start_date" id="start_date"  value="<?php echo $this->escPost('start_date');?>"
								   class="input_text" style="width:70px;height:18px;"> 
							<input type="text" name="end_date" id="end_date"  value="<?php echo $this->escPost('end_date');?>"
								   class="input_text" style="width:70px;height:18px;">	   
								   
						</td>
						<td class="text_header"> Referal Phone Number 3 </td>
						<td>
							<input type="text" name="mobile_phone" id="mobile_phone" 
								   value="<?php echo ($this->havepost('mobile_phone')?$this->escPost('mobile_phone'):'');?>"
								   class="input_text" style="width:180px;height:18px;">
						</td>
						<td class="text_header"> User ID </td>
						<td>
							<select name="user_id" id="user_id" class="select" style="width:300px;">
								<option value=""> -- Choose --</option>
								<?php $this->getUserList($this->escPost('user_id')); ?>
							</select>
						</td>
					</tr>
					
				</table>
			</div>
		<?php
		}
		
		function tplResultRemove(){ 
			$this->setCss();
		?>
			<div id="result_content_delete" class="box-shadow" style="margin-top:10px;">
				
			</div>
		<?php
		}
	}
	
	$Quality= new Quality();
	$Quality -> initClass();