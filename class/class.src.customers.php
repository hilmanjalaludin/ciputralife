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
					.xx002{width:75px;border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
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
		
		private function getCampaignAssigment($cond='')
		{
			$sql = "select a.CampaignId, a.CampaignNumber,a.CampaignName from t_gn_campaign a
					where CampaignStatusFlag=1
					order by a.CampaignId DESC";
					
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows)
			{
				$datas[$rows['CampaignId']] = $rows['CampaignNumber']." - ".$rows['CampaignName'];
			}
			
			return $datas;
		}
		
		private  function getResultStatus(){
			$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a 
					where a.CallReasonStatusFlag=1
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
				$(document).ready(function(){
					$("#tanggal").datepicker({
				})
			})
				
			</script>
			
			<!-- stop : jQuqery -->
			
			
			<div id="result_content_add" class="box-shadow" style="padding-bottom:4px;margin-top:2px;margin-bottom:8px;padding-top:4px;">
				<table cellpadding="8px;">
					<tr>
						<!-- t d class="text_header"> Customer CIF Code</td>
						<td><?#php $this -> Form -> jpInput('cust_fine_code','input_text', $this->escPost('cust_fine_code'));?></t d -->
						<td class="text_header"> Customer Name</td>
						<td><?php $this -> Form -> jpInput('cust_name','input_text', $this->escPost('cust_name'));?></td>
						<td class="text_header"> Gender</td>
						<td><?php $this -> Form -> jpCombo('gender', 'select', $this -> getGender(),$this->escPost('gender')) ?></td>
						<td class="text_header"> Campaign Name</td>
						<td><?php $this -> Form -> jpCombo('campaign_id', 'select', $this -> getCampaignAssigment(),$this->escPost('campaign_id')) ?> </td>
					</tr>
					
					<tr>
						<!-- t d class="text_header"> Customer Name</td>
						<td><?#php $this -> Form -> jpInput('cust_name','input_text', $this->escPost('cust_name'));?></t d -->
						<td class="text_header"> Call Status </td>
						<td><?php $this -> Form -> jpCombo('call_status', 'select', $this -> getResultStatus(),$this->escPost('call_status')) ?> </td>
						<td class="text_header"> Date Call</td>
						<td><input type="text" name="start_date" id="start_date"  value="<?php echo $this->escPost('start_date');?>"
								   class="input_text" style="width:70px;height:18px;"> &nbsp;-&nbsp;
						<input type="text" name="end_date" id="end_date"  value="<?php echo $this->escPost('end_date');?>"
								   class="input_text" style="width:70px;height:18px;"></td>
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
	
	$CallResult= new CallResult();
	$CallResult -> initClass();