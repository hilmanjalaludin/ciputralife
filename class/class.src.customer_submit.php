<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/lib.form.php");
	

	/*
	 *	class untuk action call result
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	
	
	class CallResult extends mysql{
		var $action;
		var $Form;
		
		function __construct()
		{
			parent::__construct();
			
			$this -> action = $this->escPost('action');
			$this -> Form   = new jpForm();
			$this -> setCss();
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
			if( $this->havepost('action'))
			{
				switch( $this->action)
				{
					case 'tpl_onready' : $this->tplOnReady();    	break;
					case 'tpl_delete'  : $this->tplResultRemove(); 	break;
				}
			}
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
			else if( $this->getSession('handling_type')==9 ){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 ";
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
		
		private function HitungUmur($tgllhr) { 
			list($tgl,$bln,$thn) = explode('-',$tgllhr); 
			$lahir = mktime(0, 0, 0, (int)$bln, (int)$tgl, $thn); 
			$t = time(); 
			$umur = ($lahir < 0) ? ( $t + ($lahir * -1) ) : $t - $lahir; $tahun = 60 * 60 * 24 * 365; 
			$tahunlahir = $umur / $tahun; 
			$umursekarang=floor($tahunlahir) ; 
			
			return $umursekarang;
		}
		
		private function getGender()
		{
			$sql = "select a.GenderId, a.Gender from t_lk_gender a order by a.GenderId ASC";
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['GenderId']] = $rows['Gender'];
			}
			return $datas;	
		}
		
		private function getCardType()
		{
			$sql = "select a.CardType, a.CardTypeDesc from t_lk_cardtype a order by a.CardTypeId asc";
					
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['CardType']] = $rows['CardTypeDesc'];
			}
			
			return $datas;
		}
		
		private function getCampaign($cond='')
		{
			$sql = "
				select a.CampaignId, a.CampaignNumber,a.CampaignName from t_gn_campaign a
					order by a.CampaignId DESC
				";
			// $sql = "
			//         select a.CampaignId, c.CampaignNumber, c.CampaignName from t_gn_customer a
			// 		left join t_gn_assignment b on a.CustomerId = b.CustomerId
			// 		left join t_gn_campaign c on a.CampaignId = c.CampaignId
			// 		where b.AssignSelerId = ".$this->getSession('UserId')."
			// 		group by a.CampaignId";
			//echo $sql;
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows)
			{
				$datas[$rows['CampaignId']] = $rows['CampaignNumber']." - ".$rows['CampaignName'];
			}
			
			return $datas;
		}
		
		private  function getResultStatus(){
			$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a 
					where a.CallReasonStatusFlag=1 and a.CallReasonId = 23
					order by a.CallReasonId asc";
					
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows)
			{
				$datas[$rows['CallReasonId']] = $rows['CallReasonCode']." - ".$rows['CallReasonDesc'];
			}
			
			return $datas;
			
		}
	
		function tplOnReady(){ 
		 ?>
			<!-- start : jQuqery -->
			
			<script>
				$(document).ready(function(){
					alert("hello");
				});
				
			</script>
			<!-- stop : jQuqery -->
			<div id="result_content_add" class="box-shadow" style="padding-bottom:4px;margin-top:2px;margin-bottom:8px;padding-top:4px;">
				<table cellpadding="8px;">
					<tr>
						<td class="text_header"> Campaign Name</td>
						<td><?php $this -> Form -> jpCombo('campaign_id', 'select', $this -> getCampaign(),$this->escPost('campaign_id')) ?> </td>
						<td class="text_header"> Customer Number</td>
						<td><?php $this -> Form -> jpInput('cust_number','input_text', $this->escPost('cust_number'));?></td>
					</tr>
					<tr>
						<td class="text_header"> Customer Name</td>
						<td><?php $this -> Form -> jpInput('cust_name','input_text', $this->escPost('cust_name'));?></td>
						<td class="text_header"> Call Status </td>
						<td><?php $this -> Form -> jpCombo('call_status', 'select', $this -> getResultStatus(),$this->escPost('call_status')) ?> </td>
					</tr>
				</table>
			</div>
		<?php
		}	
		function tplResultRemove(){ ?>
			<div id="result_content_delete" class="box-shadow" style="margin-top:10px;"></div>
		<?php
		}
	}
	$CallResult= new CallResult();
	$CallResult -> initClass();