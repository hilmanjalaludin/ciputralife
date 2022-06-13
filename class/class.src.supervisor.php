<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	

	/*
	 *	class untuk action call result
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	
	
	class Supervisor extends mysql{
		var $action;
		
		function __construct(){
			parent::__construct();
			$this->action = $this->escPost('action');
			$this->setCss();
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
		
		
		function initClass(){
			if( $this->havepost('action')):
				switch( $this->action){
					case 'tpl_onready' : $this->tplOnReady();    break;
					case 'tpl_delete'  : $this->tplResultRemove(); break;
				}
			endif;
		}
		
		function getUserList($cond=''){
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
		
		private function getCampaignAssigment($cond=''){
			$sql = "select a.CampaignId, a.CampaignNumber,a.CampaignName from t_gn_campaign a
					where a.CampaignStatusFlag=1 order by a.CampaignId DESC";
					
			$qry = $this ->execute($sql,__FILE__LINE__);
			while( $row = $this ->fetchrow($qry)){
				if( $row->CampaignId == $cond){
					echo "<option value=\"{$row->CampaignId}\" selected>{$row->CampaignNumber} - {$row->CampaignName} </option>";
				}
				else
					echo "<option value=\"{$row->CampaignId}\">{$row->CampaignNumber} - {$row->CampaignName}</option>";
			}	
		}
		
		private  function getResultStatus($cond=''){
			$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a 
					where a.CallReasonStatusFlag=1
					order by a.CallReasonId asc";
					
			$qry = $this->execute($sql,__file__,__line__);
			
			
			while( $res = $this->fetchrow($qry) )
			{
				if( $res -> CallReasonId ==$cond ):
					echo "<option value=\"{$res -> CallReasonId}\" selected>{$res -> CallReasonCode} - {$res -> CallReasonDesc}</option>"; // $datas[$res -> CallReasonId] = $res -> CallReasonDesc; 
				else:
					echo "<option value=\"{$res -> CallReasonId}\">{$res -> CallReasonCode} - {$res -> CallReasonDesc}</option>"; // $datas[$res -> CallReasonId] = $res -> CallReasonDesc;
				endif;
			}
		}
	
		function tplOnReady(){ ?>
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
						<td class="text_header"> Customer ID</td>
						<td>
							<input type="text" name="cust_number" id="cust_number" 
								   value="<?php echo ($this->havepost('cust_number')?$this->escPost('cust_number'):'');?>" 
								   class="input_text" style="width:180px;height:18px;margin-right:2px;">
						</td>
						<td class="text_header"> Home Phone</td>
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
						<td class="text_header"> Office Phone </td>
						<td>
							<input type="text" name="office_phone" id="office_phone" 
								   value="<?php echo ($this->havepost('office_phone')?$this->escPost('office_phone'):'');?>"
								   class="input_text" style="width:180px;height:18px;">
						</td>
						<td class="text_header"> Call Result </td>
						<td>
							<select name="call_result" id="call_result" class="select" style="width:300px;">
								<option value=""> -- Choose --</option>
								<?php $this->getResultStatus( $this->escPost('call_result') ); ?>
							</select>
						</td>
					</tr>
					
					<tr>
						<td class="text_header"> DOB </td>
						<td>
							<input type="text" name="cust_dob" id="cust_dob" 
								   value="<?php echo ($this->havepost('cust_dob')?$this->escPost('cust_dob'):'');?>"
								   class="input_text" style="width:160px;height:18px;">
						</td>
						<td class="text_header"> Mobile Phone </td>
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
		
		function tplResultRemove(){ ?>
			<div id="result_content_delete" class="box-shadow" style="margin-top:10px;">
				
			</div>
		<?php
		}
	}
	
	$Supervisor= new Supervisor();
	$Supervisor -> initClass();