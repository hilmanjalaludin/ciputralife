<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class_export_excel.php");

	/*
	 *	class untuk action  reload data
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	class ReloadData extends mysql{
		var $action;
		var $campaignId;
		var $callresultid;
		var $assignData;
		var $excel;
		
		
		
		public function __construct(){
			if( $this -> havepost('action')):
				$this -> action = $this -> escPost('action');
				$this -> campaignId  = $this -> escPost('campaignid');
				$this -> callresultid = $this -> escPost('resultid');
				$this -> assignData  = $this -> escPost('assign');
				$this -> excel = new excel();
			endif;
		}
		
		public function index(){
			switch($this ->action){
				case 'get_list_campaign'	: $this -> getCampaignList(); 	break;	
				case 'get_list_result'		: $this -> getResultList(); 	break;
				case 'get_list_table'		: $this -> getListTable(); 	break;
			}
		}
	
		
		
		function getCampaignList(){
			$sql = " select a.CampaignId, a.CampaignNumber, a.CampaignName from t_gn_campaign a 
						where a.CampaignStatusFlag=1
						and ( if(a.CampaignExtendedDate is null, date(a.CampaignEndDate) > date(NOW()),
						  date(a.CampaignExtendedDate) > date(NOW()))) ";
						  
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			?>
				<select name="campaign_list_id" id="campaign_list_id" onchange="getListResult(this);" class="select" style="height:200px;width:300px;" multiple="true">
				
			<?php
			while( $row = $this -> fetchrow($qry) ){
				echo "<option value=\"{$row->CampaignId}\">{$row->CampaignNumber} - {$row->CampaignName}</option>";
			}
			?>
				</select>	
			<?
		}
		
		function getResultList(){
			$sql =" select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from  t_lk_callreason a
					where a.CallReasonStatusFlag=1 and a.CallReasonId NOT IN(16,17) ";
			
			$qry  = $this ->execute($sql,__FILE__,__LINE__);
			?>
				<select name="result_list_id" id="result_list_id" multiple="true" class="select" style="height:200px;width:300px;" onclick="getListUser(this);">
			<?php
			while( $row = $this -> fetchrow($qry)){
				echo "<option value=\"{$row->CallReasonId}\">{$row->CallReasonCode} - {$row->CallReasonDesc} </option>";
			}	
			?>
				</select>	
			<?php
		}
		
		
		
		function getAssignData($string){
			if( $string!=''){
				$V_DATAS = explode("_",$string); 
				if( is_array($V_DATAS) ):
					$datas['UserId'] 	 = $V_DATAS[2];
					$datas['CampaignId'] = $V_DATAS[3];
					$datas['ResultId'] 	 = $V_DATAS[4];
					$datas['Amount'] 	 = ($V_DATAS[5]?$V_DATAS[5]:0);
				endif;
				
				return $datas;
			}	
		
		}
		
		private function emptyText($str){
			if($str!=''): return $str;
			else : return '-';
				endif;
		}
		
	
		function getListTable(){
			
			$sql = " select b.CallReasonDesc, b.CallReasonCode, c.CampaignNumber, c.CampaignName,
						a.*
			
						from t_gn_customer a
						inner join t_gn_assignment d on a.CustomerId=d.CustomerId
						left join t_lk_callreason b on a.CallReasonId=b.CallReasonId
						left join t_gn_campaign c on a.CampaignId=c.CampaignId
						
				where d.AssignBlock=0 
				and a.CampaignId IN(".$this ->escPost('CampaignId').") ";
				
				$status = explode(',',$this ->escPost('CallResult'));
				
				$stt = array();
					foreach($status as $k=>$v){ if($v!=1) $stt[] = $v; }
					
				$stt1 = implode("','",$stt);
				if(in_array(1,$status)) $sql.=" and ( a.CallReasonId IN('$stt1') OR a.CallReasonId IS NULL ) ";
				else $sql.=" and a.CallReasonId IN('$stt1') ";
				
		
				//echo $sql;
			?>
				<style>
					h2 { font-family:Arial;color:white;font-size:16px;background-color:green;padding:6px;width:200px;}
					h2:hover{ background-color:green;color:red;cursor:pointer;}
					.header-text{ color:#FFFFFF;padding:4px;height:30px;background-color:green;font-weight:bold;font-family:Arial;font-size:12px;border-left:1px solid #dddddd;
									border-top:1px solid #dddddd;}
					.content-text{ color:black;height:24px;background-color:#ffffff;font-weight:normal;
									font-family:Arial;font-size:12px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;}
					table{ border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;}					
				</style>
				<H2> Show Reload Data </h2>
				<table width="100%" cellpadding="2" cellspacing="0">
					<tr>
						<td nowrap class="header-text">CAMPAIGN</td>
						<td nowrap class="header-text">CALL RESULT</td>
						<td nowrap class="header-text">LAST CALL DATE </td>
						<td nowrap class="header-text">CUSTOMER NUMBER</td>
						<td nowrap class="header-text">CODE SOURCE</td>
						<td nowrap class="header-text">CODE DB </td>
						<td nowrap class="header-text">NAMA</td>
						<td nowrap class="header-text">DOB</td>
						<td nowrap class="header-text">TLP_RMH</td>
						<td nowrap class="header-text">TLP_KNTR</td>
						<td nowrap class="header-text">EXT</td>
						<td nowrap class="header-text">HP</td>
						<td nowrap class="header-text">ALAMAT_RMH1</td>
						<td nowrap class="header-text">ALAMAT_RMH2</td>
						<td nowrap class="header-text">ALAMAT_RMH3</td>
						<td nowrap class="header-text">ALAMAT_RMH4</td>
						<td nowrap class="header-text">KOTA_RMH</td>
						<td nowrap class="header-text">KODE_POS</td>
						<td nowrap class="header-text">NAMA_KANTOR</td>
						<td nowrap class="header-text">ALAMAT_KANTOR1</td>
						<td nowrap class="header-text">ALAMAT_KANTOR2</td>
						<td nowrap class="header-text">ALAMAT_KANTOR3</td>
						<td nowrap class="header-text">ALAMAT_KANTOR4</td>
						<td nowrap class="header-text">KOTA_KANTOR</td>
						<td nowrap class="header-text">KODE_POS</td>
						<td nowrap class="header-text">WILAYAH</td>
						
					</tr>
				
			<?php	
			
			if( $this -> havepost('CampaignId') && $this -> havepost('CallResult') ):
				$query = $this ->execute($sql,__FILE__,__LINE__);
				while( $rows = $this ->fetchrow($query)){
				
				?>
				<tr>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CampaignNumber); ?> - <?php echo $this->emptyText( $rows ->CampaignName); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CallReasonCode); ?> - <?php echo $this->emptyText( $rows ->CallReasonDesc); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerUpdatedTs); ?> </td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerNumber); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CDDB); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CDDB); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerFirstName); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerDOB); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerHomePhoneNum); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerWorkPhoneNum); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerWorkExtPhoneNum); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerMobilePhoneNum); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerAddressLine1); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerAddressLine2); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerAddressLine3); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerAddressLine4); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerCity); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerZipCode); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerOfficeName); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerOfficeLine1); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerOfficeLine2); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerOfficeLine3); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerOfficeLine4); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerOfficeCity); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerOfficeZipCode); ?></td>
						<td nowrap class="content-text"><?php echo $this->emptyText( $rows ->CustomerArea); ?></td>
						
					</tr>	
				<?	
				
				}
				?>  </table> <?php	
			endif;	
		}
		
	}
	
	
	$ReloadData = new ReloadData(true);
	$ReloadData -> index();
	
	