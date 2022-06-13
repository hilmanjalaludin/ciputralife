<?php
	
	class txtFile{
		
		public  $file;
		private $fp;
		private $content;
		private $str;
		
		
		function logConsole($write_line_text,$access_dir){
	
			//$dir_report_file = "../closing/".$access_dir."_".date('Y-m-d').".txt"; 
			$dir_report_file = "../DownLoadReport/closing/".$access_dir; 
			$dir_handle_file = fopen($dir_report_file, 'w');
			$dir_time_file	 = gmdate("D, d M Y H:i:s");
			$write_line_text = $write_line_text;
			fwrite($dir_handle_file,$write_line_text ); 
			fclose($dir_handle_file);
		}
	
		public function myBiller($val){
		
		
		/* PLN */
		
		$data['PLN'] = array(
							'currency'=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>9,
							'account'=>12,
							'name'=>64,
							'biller'=>3,
							'blank2'=>14,
							'jenis_jasa'=>	0,
							'blank3'=>0,
							'prefix'=>2,
							'no_pelanggan'=>20,
							'nama_pelanggan'=>64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'=>2,
							'tanggal_efektif'=>8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10
							);
		
		/* TELKOM */
		
		$data['TKOM'] = array(
							'currency'=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>9,
							'account'=>12,
							'name'=>64,
							'biller'=>4,
							'blank2'=>1,
							'jenis_jasa'=>6,
							'blank3'=>2,
							'prefix'=>4,
							'no_pelanggan'=>32,
							'nama_pelanggan'=>64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'=>2,
							'tanggal_efektif'=>	8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10
							);
		
		/* INDOSAT */
		$data['ISAT'] =array('currency'=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>16,
							'account'=>12,
							'name'=>64,
							'biller'=>4,
							'blank2'=>1,
							'jenis_jasa'=>3,
							'blank3'=>5,
							'prefix'=>4,
							'no_pelanggan'=>32,
							'nama_pelanggan'=>64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'=>2,
							'tanggal_efektif'=>8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10);
		/* TELKOM SEL */
		$data['TSEL'] =array(
							'currency'	=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>16,
							'account'=>12,
							'name'=>64,
							'biller'=>4,
							'blank2'=>1,
							'jenis_jasa'=>6,
							'blank3'=>6,
							'prefix'=>0,
							'no_pelanggan'=>32,
							'nama_pelanggan'=>64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'=>2,
							'tanggal_efektif'=>8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10);
		/* indovision*/
		$data['OFFL1']=array(
							'currency'=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>16,
							'account'=>12,
							'name'=>64,
							'biller'=>4,
							'blank2'=>1,
							'jenis_jasa'=>6,
							'blank3'=>6,
							'prefix'=>0,
							'no_pelanggan'=>32,
							'nama_pelanggan'=>64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'=>2,
							'tanggal_efektif'=>8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10
							);
		/* playja */
		$data['OFFL2']=array(
							'currency'=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>16,
							'account'=>	12,
							'name'=>64,
							'biller'=>4,
							'blank2'=>1,
							'jenis_jasa'=>6,
							'blank3'=>6,
							'prefix'=>0,
							'no_pelanggan'=>32,
							'nama_pelanggan'=>64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'=>2,
							'tanggal_efektif'=>8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10
							);

		/* mobile8*/
		
		$data['OFFL3']=array(
							'currency'=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>16,
							'account'=>12,
							'name'=>64,
							'biller'=>4,
							'blank2'=>1,
							'jenis_jasa'=>6,
							'blank3'=>6,
							'prefix'=>0,
							'no_pelanggan'=>32,
							'nama_pelanggan'=>	64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'	=>2,
							'tanggal_efektif'=>8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10);
							
			/* excel */						
			$data['EXCL']=array(
							'currency'=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>16,
							'account'=>12,
							'name'=>64,
							'biller'=>4,
							'blank2'=>1,
							'jenis_jasa'=>3,
							'blank3'=>5,
							'prefix'=>4,
							'no_pelanggan'=>32,
							'nama_pelanggan'=>64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'=>2,
							'tanggal_efektif'=>	8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10
							);
			
			/* MIS Prosp*/
			$data['prosp'] = array(
				'Prospect_Id' => 20,
				'Campaign_Name' => 80,
				'Campaign_Id' => 6,
				'ACC_Number' => 25,
				'Cif_Number' => 20,
				'ref_Number' => 20,
				'Name' => 50,
				'DOB' => 35,
				'Product_Id' => 8,
				'Product_Name' => 35,
				'Call_Result' => 3,
				'Call_Type' => 3,
				'seller_Id' => 16,
				'Call_Date' => 19,
				'Upload_date' => 19,
				'SPV_Id' => 0,
				'Agent_Id' => 16,
				'Remarks' => 16,
				'sex' => 1,
				'remarks_1' => 16,
				'Remarks_2' => 35,
				'Remarks_3' => 16,
				'Policy_No' => 20,
				'Policy_ref' => 20,
				'policy_date' => 20,
				'Campaign_Cigna' => 50,
				'Description' => 34,
				'Premium' => 8,
				'Payment_Frequent' => 2,
				'Camp_Type' => 3,
				'Campaign_Initial_Date' => 19,
				'Campaign_Start_Date' => 19,
				'Campaign_End_Date' => 19,
				'Extend_Date' => 19,
				'Total_Prospect' => 5,
				'Sponsor_Id' => 3,
				'Month_Of_Campaign' => 2,
				'year_Of_campaign' => 2,
				'Camp_Type1' => 2,
				'Build_Type' => 2,
				'Source_Campaign_ID' => 100,
				'Reupload_Reason' => 255,
				'Source_Campaign_ID' => 100
			);
			
			// MIS CTrack //
			$data['ctrack'] = array(
				'prospect_id' => 20,
				'product_Id' => 8,
				'Campaign_ID' => 6,
				'SPV_Name' => 16,
				'Agent_Id' => 16,
				'Call_Id' => 3,
				'Call_Date' => 19,
				'Remark' => 16,
				'Call_Type' => 3,
				'Description' => 34
			);
			
			/* MIS dulltxt */
			$data['dull'] = array(
				'CustomerId' =>20,
				'dtfr' =>0,
				'dtto' =>0,
				'system' =>2,
				'policy_id' =>20,
				'policy_ref' =>0,
				'prospect_id' =>20,
				'product_id' =>50,
				'campaign_id' =>50,
				'campaign_tbbs' =>0,
				'input' =>8,
				'effdt' =>8,
				'payer_title' =>10,
				'payer_fname' =>30,
				'payer_lname' =>30,
				'payer_sex' =>2,
				'payer_dob' =>8,
				'addr1' =>60,
				'addr2' =>60,
				'addr3' =>60,
				'addr4' =>60,
				'city' =>60,
				'post' =>10,
				'province' =>20,
				'phone' =>30,
				'faxphone' =>30,
				'email' =>50,
				'bnf1_lname' =>50,
				'bnf1_fname' =>50,
				'bnf1_sex' =>2,
				'bnf1_ssn' =>20,
				'bnf1_bene_ind' =>1,
				'bnf1_client_type' =>1,
				'bnf1_percent' =>9,
				'bnf1_coverage' =>10,
				'bnf1_relation' =>30,
				'bnf2_lname' =>50,
				'bnf2_fname' =>50,
				'bnf2_sex' =>2,
				'bnf2_ssn' =>20,
				'bnf2_bene_ind' =>1,
				'bnf2_client_type' =>1,
				'bnf2_percent' =>9,
				'bnf2_coverage' =>10,
				'bnf2_relation' =>30,
				'bnf3_lname' =>50,
				'bnf3_fname' =>50,
				'bnf3_sex' =>2,
				'bnf3_ssn' =>20,
				'bnf3_bene_ind' =>1,
				'bnf3_client_type' =>1,
				'bnf3_percent' =>9,
				'bnf3_coverage' =>10,
				'bnf3_relation' =>30,
				'bnf4_lname' =>50,
				'bnf4_fname' =>50,
				'bnf4_sex' =>2,
				'bnf4_ssn' =>20,
				'bnf4_bene_ind' =>1,
				'bnf4_client_type' =>1,
				'bnf4_percent' =>9,
				'bnf4_coverage' =>10,
				'bnf4_relation' =>30,
				'bnf5_lname' =>50,
				'bnf5_fname' =>50,
				'bnf5_sex' =>2,
				'bnf5_ssn' =>20,
				'bnf5_bene_ind' =>1,
				'bnf5_client_type' =>1,
				'bnf5_percent' =>9,
				'bnf5_coverage' =>10,
				'bnf5_relation' =>30,
				'pay_type' =>2,
				'card_type' =>10,
				'bank' =>50,
				'branch' =>50,
				'acctnum' =>16,
				'ccexpdate' =>10,
				'bill_freq' =>10,
				'question1' =>0,
				'question2' =>0,
				'question3' =>0,
				'benefit_level' =>0,
				'premium' =>0,
				'operid' =>0,
				'sellerid' =>10,
				'spv_id' =>2,
				'export' =>0,
				'exportdate' =>0,
				'canceldate' =>0,
				'callDate2' =>0,
				'paystatus' =>0,
				'paynotes' =>0,
				'payauthcode' =>0,
				'paytransdate' =>0,
				'payorderno' =>0,
				'payccnum' =>0,
				'paycvv' =>0,
				'payexpdate' =>0,
				'paycurency' =>0,
				'paycardtype' =>0,
				'payer_idtype' =>0,
				'payer_personalid' =>0,
				'payer_mobilephone' =>30,
				'payer_officephone' =>30,
				'delivery_date' =>0,
				'payer_age' =>0,
				'currency' =>0,
				'class' =>0,
				'ratingfactors' =>0,
				'mi_min' =>0,
				'mi_max' =>0,
				'mi_ren' =>0,
				'sp_min' =>0,
				'sp_max' =>0,
				'sp_ren' =>0,
				'dp_min' =>0,
				'dp_max' =>0,
				'dp_ren' =>0,
				'ratingoptions' =>0,
				'beneficiary' =>0,
				'policyprefix' =>0,
				'ei_mi' =>0,
				'ei_sp' =>0,
				'ei_dp' =>0,
				'cc_mi' =>0,
				'cc_mi_sp' =>0,
				'cc_mi_fam' =>0,
				'cc_mi_dp' =>0,
				'cc_sp_dp' =>0,
				'cc_sp' =>0,
				'cc_dp' =>0,
				'ben_level' =>0,
				'htype' =>0,
				'holder_title' =>0,
				'holder_age' =>0,
				'holder_type' =>2,
				'h_title' =>25,
				'holder_fname' =>50,
				'holder_lname' =>50,
				'holder_sex' =>2,
				'holder_dob' =>4,
				'relation' =>30,
				'premi' =>13,
				'holder_ssn' =>20,
				'benefit_level' =>1,
				'holder_race' =>25,
				'holder_idtype' =>3,
				'holder_issmoker' =>1,
				'holder_nationality' =>25,
				'holder_maritalstatus' =>2,
				'holder_occupation' =>25,
				'holder_jobtype' =>150,
				'holder_position' =>25,
				'holder_height' =>1,
				'holder_weight' =>1,
				'uwstatus' =>1,
				'uwlastupdate' =>8,
				'uwapprovedate' =>8,
				'uwprintdate' =>8,
				'holder_id' =>4,
				'question_id' =>10,
				'answer' =>4,
				'remark' =>300,
				'seq_no' =>4,
				'call_id' =>4,
				'bmimax' =>9,
				'bmimin' =>9,
				'camptype' =>5

			);
			
			$data['OFFL4']=array(
							'currency'=>3,
							'type'=>5,
							'card_no'=>16,
							'blank1'=>16,
							'account'=>12,
							'name'=>64,
							'biller'=>4,
							'blank2'=>1,
							'jenis_jasa'=>3,
							'blank3'=>9,
							'prefix'=>0,
							'no_pelanggan'=>32,
							'nama_pelanggan'=>64,
							'telp_ybs'=>20,
							'bulan_expired'=>2,
							'tahun_expired'=>2,
							'tanggal_efektif'=>8,
							'kelompok'=>2,
							'blank4'=>3,
							'custid'=>30,
							'sfid'=>10);

		  return $data[$val];
		}
		
		public function txtChekfile(){
			
		}
		
		public function txtWriteLabel($value){
		    $this->content =$value;
			$this -> logConsole($this->content,$this->file);
		}

		
		
		public function txtClose(){
			fclose($this->fp);
		}
		
		public function split($rowData,$biller,$column,$s){
			$blank="";
			$RowBill = $this->myBiller($biller);
			
			return $rowData.$s;
		}
		
	/** create line of the text **/
	
		public function splitHead($spcContent='',$spcCol=0,$spcPos=''){
				$xl = ($spcCol-strlen($spcContent));
		  return $spcContent.$this->txtBlank($xl).$spcPos;
		}
	
	  /** blank text area **/
	  
		public function txtBlank($count){
			$blank='';
			for($i=0; $i<$count; $i++){
				$blank.=" ";				
			}
			
		  return $blank;
		}
	
	}
?>
