<?php

	require("../class/MYSQLConnect.php");
	require("../class/class_export_excel.php");

	/*
	 *	class untuk action  reload data
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	class ReloadExcel extends mysql{
		var $action;
		var $campaignId;
		var $callresultid;
		var $assignData;
		var $excel;
		
		
		public function __construct(){
			if( $this -> havepost('action')):
				$this -> action = $this -> escPost('action');
				$this -> excel = new excel();
			endif;
		}
		
		private function emptyText($str){
			if($str!=''): return $str;
			else : return 'NULL';
				endif;
		}
		
	/** update data **/
	
		private function BlockAssigment($CustomerId){
		
			
			$sql = "UPDATE t_gn_assignment a 
						SET a.AssignBlock=1 ,
						a.AssignMode='RLD' 
					where a.CustomerId='".$CustomerId."'";
			if( $CustomerId!=''){
				$qry = $this -> execute($sql,__FILE__,__LINE__);
					if($qry) return true;
				else 
					return false;	
			}
		}
	

	/* set query **/	
	
		private function getQuery(){
			$sql = " select b.CallReasonDesc, b.CallReasonCode, c.CampaignNumber, c.CampaignName,
						a.* 
					from t_gn_customer a
						inner join t_gn_assignment d on a.CustomerId=d.CustomerId
						left join t_lk_callreason b on a.CallReasonId=b.CallReasonId
						left join t_gn_campaign c on a.CampaignId=c.CampaignId
				where d.AssignBlock=0  
					and a.CampaignId IN(".$this ->escPost('CampaignId').")"; 
					
					
					$status = explode(',',$this ->escPost('CallResult'));
					$stt = array();
					foreach($status as $k=>$v){ if($v!=1) $stt[] = $v; }
					
				$stt1 = implode("','",$stt);
				if(in_array(1,$status)) $sql.=" and ( a.CallReasonId IN('$stt1') OR a.CallReasonId IS NULL ) ";
				else $sql.=" and a.CallReasonId IN('$stt1') ";
				
				
			return $sql;	
		}
		
		function index(){
			if( $this -> havepost('action') && 
				$this -> havepost('CampaignId') && 
				$this -> havepost('CallResult') ){
					
						$this -> excel->xlsWriteHeader("Reload_excel_".date('Ymd'));
						
						$xlsRows = 0;
						
						$this->excel->xlsWriteLabel($xlsRows,0,'CAMPAIGN');
						$this->excel->xlsWriteLabel($xlsRows,1,'CALL RESULT');
						$this->excel->xlsWriteLabel($xlsRows,2,'LAST CALL DATE ');
						$this->excel->xlsWriteLabel($xlsRows,3,'CUSTOMER NUMBER');
						$this->excel->xlsWriteLabel($xlsRows,4,'CODE SOURCE');
						$this->excel->xlsWriteLabel($xlsRows,5,'CODE DB ');
						$this->excel->xlsWriteLabel($xlsRows,6,'NAMA');
						$this->excel->xlsWriteLabel($xlsRows,7,'DOB');
						$this->excel->xlsWriteLabel($xlsRows,8,'TLP_RMH');
						$this->excel->xlsWriteLabel($xlsRows,9,'TLP_KNTR');
						$this->excel->xlsWriteLabel($xlsRows,10,'EXT');
						$this->excel->xlsWriteLabel($xlsRows,11,'HP');
						$this->excel->xlsWriteLabel($xlsRows,12,'ALAMAT_RMH1');
						$this->excel->xlsWriteLabel($xlsRows,13,'ALAMAT_RMH2');
						$this->excel->xlsWriteLabel($xlsRows,14,'ALAMAT_RMH3');
						$this->excel->xlsWriteLabel($xlsRows,15,'ALAMAT_RMH4');
						$this->excel->xlsWriteLabel($xlsRows,16,'KOTA_RMH');
						$this->excel->xlsWriteLabel($xlsRows,17,'KODE_POS');
						$this->excel->xlsWriteLabel($xlsRows,18,'NAMA_KANTOR');
						$this->excel->xlsWriteLabel($xlsRows,19,'ALAMAT_KANTOR1');
						$this->excel->xlsWriteLabel($xlsRows,20,'ALAMAT_KANTOR2');
						$this->excel->xlsWriteLabel($xlsRows,21,'ALAMAT_KANTOR3');
						$this->excel->xlsWriteLabel($xlsRows,22,'ALAMAT_KANTOR4');
						$this->excel->xlsWriteLabel($xlsRows,23,'KOTA_KANTOR');
						$this->excel->xlsWriteLabel($xlsRows,24,'KODE_POS');
						$this->excel->xlsWriteLabel($xlsRows,25,'WILAYAH');
						
						$xlsRows = $xlsRows+1;
						
						$query = $this ->execute($this->getQuery(),__FILE__,__LINE__);
						while( $rows = $this ->fetchrow($query)){
							$this->excel->xlsWriteLabel($xlsRows,0,$this->emptyText( $rows ->CampaignNumber).'- '.$this->emptyText( $rows ->CampaignName)); 
							$this->excel->xlsWriteLabel($xlsRows,1,$this->emptyText( $rows ->CallReasonCode).'- '.$this->emptyText( $rows ->CallReasonDesc));
							$this->excel->xlsWriteLabel($xlsRows,2,$this->emptyText( $rows ->CustomerUpdatedTs));
							$this->excel->xlsWriteLabel($xlsRows,3,$this->emptyText( $rows ->CustomerNumber)); 
							$this->excel->xlsWriteLabel($xlsRows,4,$this->emptyText( $rows ->CDDB));
							$this->excel->xlsWriteLabel($xlsRows,5,$this->emptyText( $rows ->CDDB));
							$this->excel->xlsWriteLabel($xlsRows,6,$this->emptyText( $rows ->CustomerFirstName));
							$this->excel->xlsWriteLabel($xlsRows,7,$this->emptyText( $rows ->CustomerDOB));
							$this->excel->xlsWriteLabel($xlsRows,8,$this->emptyText( $rows ->CustomerHomePhoneNum));
							$this->excel->xlsWriteLabel($xlsRows,9,$this->emptyText( $rows ->CustomerWorkPhoneNum));
							$this->excel->xlsWriteLabel($xlsRows,10,$this->emptyText( $rows ->CustomerWorkExtPhoneNum));
							$this->excel->xlsWriteLabel($xlsRows,11,$this->emptyText( $rows ->CustomerMobilePhoneNum));
							$this->excel->xlsWriteLabel($xlsRows,12,$this->emptyText( $rows ->CustomerAddressLine1));
							$this->excel->xlsWriteLabel($xlsRows,13,$this->emptyText( $rows ->CustomerAddressLine2));
							$this->excel->xlsWriteLabel($xlsRows,14,$this->emptyText( $rows ->CustomerAddressLine3));
							$this->excel->xlsWriteLabel($xlsRows,15,$this->emptyText( $rows ->CustomerAddressLine4));
							$this->excel->xlsWriteLabel($xlsRows,16,$this->emptyText( $rows ->CustomerCity));
							$this->excel->xlsWriteLabel($xlsRows,17,$this->emptyText( $rows ->CustomerZipCode));
							$this->excel->xlsWriteLabel($xlsRows,18,$this->emptyText( $rows ->CustomerOfficeName));
							$this->excel->xlsWriteLabel($xlsRows,19,$this->emptyText( $rows ->CustomerOfficeLine1));
							$this->excel->xlsWriteLabel($xlsRows,20,$this->emptyText( $rows ->CustomerOfficeLine2));
							$this->excel->xlsWriteLabel($xlsRows,21,$this->emptyText( $rows ->CustomerOfficeLine3));
							$this->excel->xlsWriteLabel($xlsRows,22,$this->emptyText( $rows ->CustomerOfficeLine4));
							$this->excel->xlsWriteLabel($xlsRows,23,$this->emptyText( $rows ->CustomerOfficeCity));
							$this->excel->xlsWriteLabel($xlsRows,24,$this->emptyText( $rows ->CustomerOfficeZipCode));
							$this->excel->xlsWriteLabel($xlsRows,25,$this->emptyText( $rows ->CustomerArea));
							
							$this -> BlockAssigment($rows ->CustomerId); 
							$xlsRows += 1;
						}
						
				$this->excel->xlsClose();		
			}		
		}
		
	}
	
	
	$ReloadExcel = new ReloadExcel(true);
	$ReloadExcel -> index();	