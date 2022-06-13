<?php
	
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../plugin/class_fixedwidth.php");
	require(dirname(__FILE__)."/../plugin/class.import.excel.php");
	
/* 
 * class app.upload 
 * sebagai controller untuk upload data excel
 * ke dalam applikasi databaseme
 * Author : omens
*/
			 
	class Uploads extends mysql{ 
	
		var $Files;
		var $FileName;
		var $Paramater;
		var $FileDir;
		var $Action;
		var $excel;
		var $FixedWidth;
		var $RowsInt;
		
	/* 
	 * iniated call back 
	 *
	 */
	
		var $SuccesRows;
		var $FailedRows;
		var $TotalRows;
		var $CampaignId;
		var $FileFrom;
		
		function __construct()
		{
			parent::__construct();	
			$this -> Action 	 = $this -> escPost('action');
			$this -> CampaignId  = $this -> escPost('act_cmp_id'); 
			$this -> FileFrom    = $this -> escPost('act_file_name');
		}
		
	/** get campaign id by customer number *****/

		private function getCampaignIdCustomers($CustomerNumber=0)
		{
			$sql = " SELECT a.CampaignId FROM t_gn_customer a where a.CustomerNumber='$CustomerNumber'";
			$qry = $this ->query($sql);
			if( $qry -> result_num_rows() > 0 ) { 
				return $qry -> result_singgle_value(); 
			}
			else
				return null;
		}	
		
	/*** private function cek other campaign custoomer id *****/

		private function getSameCustomer($CampaignId = 0, $CustomerNumber =0 )
		{ 
			$sql = " SELECT COUNT(*) as jumlah FROM t_gn_customer a 
					 WHERE a.CampaignId IN('$CampaignId') AND a.CustomerNumber='$CustomerNumber' ";
					 
			$qry = $this ->query($sql);
			if( $qry -> result_singgle_value() > 0 ) 
				return true;
			else
				return false;
		}

	/** private function getupload template **/
	
		private function getTemplateName()
		{
			if( $this -> havepost('act_template_id'))
			{ 
				$sql = " select a.TemplateFileType from tms_tempalate_upload a 
							where a.TemplateId='".$this -> escPost('act_template_id')."' ";
				$qry = $this -> query($sql);
				if( !$qry ->EOF())
				{
					return strtoupper($qry -> result_get_value('TemplateFileType'));
				}	
			}
			else
				return null;
		}
		
	/*** private function cek other campaign custoomer id *****/

		private function checkCustomer($CampaignId = 0, $CustomerNumber =0 )
		{ 
			$sql = " SELECT COUNT(*) as jumlah FROM t_gn_customer a 
					 WHERE a.CampaignId NOT IN('$CampaignId') AND a.CustomerNumber='$CustomerNumber' ";
					 
			$qry = $this ->query($sql);
			if( $qry -> result_singgle_value() > 0 ) 
				return true;
			else
				return false;
		}				
	
	/**** Main of Class  ******************************/
	 
		function initClass()
		{
			if( $this -> havepost('action'))
			{
				if( $this -> Action=='upload' )
				{
					if( $this -> CampaignId!='')
					{
						if( $this -> FileByCampaignId() )
						{
							$this -> Files = $_FILES['fileToupload'];
							
							/** 
							 * cek space dalam filename jika ada space langsung tak exit 
							 * update 20140925
							 * author : omens <jombi_par@yahoo.com>
							 */
							
							if(preg_match('/[\\s]/i', $this->Files['tmp_name'])!=TRUE)
							{
								switch($this->getTemplateName()) 
								{
									case 'XLS' : $this -> startUploadsExcel(); 	break;
									case 'TXT' : $this -> startUploadsText(); 	break;
									case 'CSV' : $this -> startUploadsCSV(); 	break;
									default	: 
										echo json_encode(array('result'=>0));
									break;	
								}
								
								$this -> moveFileName();
							}
							else{
								echo "Error, white space detectted !";
							}
						}
						else{
							echo "Error, File Name Not Valid !";
						}	
					}
					else{
						echo "Error, Campaign ID Not valid !";
					}
				}
			}
			else
				exit(0);
		}
		
	/*
	 * insert into t_gn_assignment by last insert ID
	 * for distribution function modul 
	 */	
		private function FKassig($custid)
		{
			if($custid!='')
			{
				$sql =" INSERT INTO t_gn_assignment (CustomerId, AssignAdmin) VALUES (".$custid.", ".$this->getSession('UserId').")";
				$this -> execute($sql,__FILE__,__LINE__);		
			}	
		}
		
	/** ################################################################ ****/
	
		private function moveFileName()
		{
			if( !copy( $this -> Files['tmp_name'], '../upload/'.$this -> Files['name'])){
				return false;
			}
			else {  return true; }
		}
		
	/////////////////////////
	
		private function getLocationFile()
		{
			$var_location = str_replace('class/','',dirname(__FILE__).'/upload/'.$this -> Files['name']);
			if( !empty($var_location))
			{
				return $var_location;
			}
		}
		
	
   /***** set to NULL (empty MYSQL ) *******************/
		
		private function contextNull($datas)
		{
			$clearNull = array(); 
			foreach( $datas as $key=>$value)
			{
				if(trim($value)!='' && !empty($value))
				{
					$clearNull[$key] = $value;	
				}
			}
			return $clearNull;
		}
								
		
   /* get CampaignID BY result function */
	
		private function getCampaignId()
		{
			return $this -> CampaignId;
		}
	
   /* 
	*  Update t_gn_campaign on Field CampaignDataFileName 
	*  By CamapaignId on reference from t_gn_campaigngroup 
	*  table 
	*/
	
		private function FileByCampaignId()
		{
			if( $this -> CampaignId!='' )
			{
				if( $this->havepost('act_file_name') )
				{
					$datas['CampaignDataFileName'] = $this->escPost('act_file_name');
					$datas['CampaignIsFollowup']  = 1;
					$where['CampaignId'] = $this -> CampaignId;
					$Query = $this -> set_mysql_update('t_gn_campaign',$datas,$where);
					
					if( $Query ) return true;
					else 
						return false;	
					
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
	   } 
	
   /* set CampaignId from t_gn_campaign  by parameter campaign_core   on table t_gn_campaigngroup */
   
	 private function setCampaignId()
		{
			if( $this->havepost('act_cmp_core') )
			{
				$sql = " SELECT c.CampaignId from t_gn_campaigngroup a
							LEFT JOIN t_gn_product b on a.CampaignGroupId=b.CampaignGroupId
							LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId
							WHERE a.CampaignGroupCode = '".trim($this->escPost('act_cmp_core'))."'
							AND b.ProductId=".trim($this->escPost('act_upload_product'))."
							AND c.CampaignId is not null
							AND a.CampaignGroupStatusFlag=1";		
				$Query = $this -> fetchval($sql,__FILE__,__LINE__);
				
				if(!empty($Query)){
					$this -> CampaignId = $Query;
				} 	
				else{
					$this -> CampaignId = false; 
				}
			}
		}	
		
	/** *********************************/
	
		private function ClearPhoneNumber( $string='' )
		{
			if( $string!='')
			{
				$phone = preg_replace('/[^\da-z]/i','',$string);
				if( $phone  )
				{
					$phone_62 = substr($phone,0,2);
					if( $phone_62==62)
					{
						return '0'.substr($phone,2,strlen($phone));
					}
					else
						return $phone;
				}
				else
					return NULL;
			}
			else
				return 0;
		}
		
   /* 
	* generate Customer number from prefix 2 digit curent Year,
	* and last insert ID from t_gn_customer Table
	*/
	
		function getGenerateNumber($custid='')
		{
		
			$year    = substr(date('Y-m-d'),1,3);
			$maxSize = 20;
			$textVal = $year.'0000000000000000';
			if( $custid !='')
			{
				$value = substr($textVal,1,(strlen($textVal)-strlen($custid)));	
				$value.= $custid;
			}
			
			 return $value;
		}
		
/**	function get gender **/

	function getCustomerGender($code)
	{
		$sql = " select a.GenderId from t_lk_gender a where a.GenderShortCode='".strtoupper($code)."' ";
		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();
	}
	

/**** define of columns fixed width text ******/
		
	private function __get_excel_header()
	{
		$set_header_index = array();
		
		if( $this -> havepost('act_template_id'))
		{
			$sql = " select a.UploadColsName, a.UploadColsAlias, a.UploadColsOrder  from tms_template_rows a 
					 where a.UploadTmpId='".$this -> escPost('act_template_id')."' 
					 order by a.UploadColsOrder ASC ";
					 
			$qry = 	$this -> query($sql);
			$i= 1;
			foreach( $qry -> result_assoc() as $rows )
			{
				$set_header_index[$i] = $rows['UploadColsName'];
				$i++;
			}
		}
		
		return $set_header_index;	
	}		
		
		
/**** define of columns fixed width text ******/
		
	private function __get_text_header()
	{
		$row = array
			(
				0  => array('cols' => 'customer_number' 	  , 'limit' => 20),
				1  => array('cols' => 'customer_name' 		  , 'limit' => 40),
				2  => array('cols' => 'customer_mobile_phone' , 'limit' => 15),
				3  => array('cols' => 'customer_office_phone' , 'limit' => 15),
				4  => array('cols' => 'customer_home_phone'   , 'limit' => 15),
				5  => array('cols' => 'customer_dob'   		  , 'limit' => 8),
				6  => array('cols' => 'customer_gender' 	  , 'limit' => 1),
				7  => array('cols' => 'customer_office_name'  , 'limit' => 40),
				8  => array('cols' => 'customer_address1' 	  , 'limit' => 40),
				9  => array('cols' => 'customer_address2' 	  , 'limit' => 40),
				10 => array('cols' => 'customer_address3' 	  , 'limit' => 40),
				11 => array('cols' => 'customer_address4' 	  , 'limit' => 40),
				12 => array('cols' => 'customer_city' 		  , 'limit' => 40),
				13 => array('cols' => 'customer_zipcode' 	  , 'limit' => 10),
				14 => array('cols' => 'customer_cardtype' 	  , 'limit' => 20)
			);
			
		return $row;	
	}		

			
	/* 
	* start import data from excel mode , to t_gn_customers 
	*/	
	
	function startUploadsExcel()
	{
		ini_set("memory_limit", "1024M");
		$sheet_raws_excel = array();			
		$this -> excel = new Spreadsheet_Excel_Reader( $this->Files['tmp_name'],true,'mb');
		$this -> RowsInt = $this -> excel -> rowcount($sheet_index=0);
		$UploadId = 0;
		
	/** definer data ***/
	
	 	$SQL_report['UploadDateTs']  = date('Y-m-d H:i:s');
		$SQL_report['UploadFileName'] = $this -> Files['name'];
		$SQL_report['TotalSuccessRows'] = 0;
		$SQL_report['TotalFailedRows'] = 0;
		$SQL_report['UploadTemporaryLocation']  = $this -> getLocationFile();
		$SQL_report['UploadUerId'] = $_SESSION['UserId'];
		$SQL_report['CampaignId'] = $this -> CampaignId;
		if( $this -> set_mysql_insert('t_gn_uploadreport',$SQL_report) ){
			$UploadId = $this->get_insert_id();
		}	
	 
	 //
		$totals_rows_sizedata = 0;
		$totals_rows_success  = 0;
		$totals_rows_failed   = 0;
		$totals_rows_updates  = 0;
		$totals_rows_campaign = 0;
		$totals_rows_samescmp = 0;
		
		$valid_rows = true;
		$rows = 2;
		
		//echo $this -> RowsInt;
		while(($rows<=$this -> RowsInt))
		{
			if( trim( $this -> excel -> val($rows,1) ) )
			{
				$totals_rows_sizedata +=1;
				foreach( $this -> __get_excel_header() as $sheet_index => $sheet_columns )
				{
					$sheet_raws_excel[$rows]['CampaignId'] = $this -> getCampaignId();
					
					if( $sheet_columns=='SalutationId' ) 
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> excel -> val($rows,$sheet_index);
					
					else if( $sheet_columns=='GenderId' )  
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> excel -> val($rows,$sheet_index);
					
					else if( $sheet_columns=='CardTypeId' )  
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> excel -> val($rows,$sheet_index);
					
					else if( $sheet_columns=='IdentificationTypeId' )  
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> excel -> val($rows,$sheet_index);
					
					else if( $sheet_columns=='ProvinceId' )  
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> excel -> val($rows,$sheet_index);
					
					else if( $sheet_columns=='SponsorId' )  
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> excel -> val($rows,$sheet_index);
				
					else if( $sheet_columns=='CustomerDOB' ) 
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> Date -> exp_date_english($this -> excel -> val($rows,$sheet_index));
						
					else if( $sheet_columns =='CustomerHomePhoneNum') 
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> ClearPhoneNumber($this -> excel -> val($rows,$sheet_index));
					
					else if( $sheet_columns =='CustomerMobilePhoneNum') 
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> ClearPhoneNumber($this -> excel -> val($rows,$sheet_index));
					
					else if( $sheet_columns =='CustomerWorkPhoneNum') 
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> ClearPhoneNumber($this -> excel -> val($rows,$sheet_index));
					
					else if( $sheet_columns =='CustomerFaxNum') 
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> ClearPhoneNumber($this -> excel -> val($rows,$sheet_index));
					
					else if( $sheet_columns =='CustomerOfficeZipCode') 
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> excel -> val($rows,$sheet_index);
						
					else{
						$sheet_raws_excel[$rows][$sheet_columns] = $this -> excel -> val($rows,$sheet_index);
					}
					$sheet_raws_excel[$rows]['CustomerUploadedTs'] = date('Y-m-d H:i:s');
					$sheet_raws_excel[$rows]['UploadId'] =$UploadId;
					
				}
				
			/* insert to t_gn_customers ****/
			
				if( $this -> set_mysql_insert('t_gn_customer',$this -> contextNull($sheet_raws_excel[$rows]) ) )
				// if( $valid_rows )
				{
					$CustomerNumberId = $this -> get_insert_id();
					$SQL_Update['CustomerNumber'] = $this -> getGenerateNumber($CustomerNumberId);
					$SQL_Wheres['CustomerId'] = $CustomerNumberId;
					
					/**** update to generate number t_gn_customers ****/
					
					if( $this -> set_mysql_update('t_gn_customer',$SQL_Update,$SQL_Wheres))
					// if( $valid_rows )
					{
						$totals_rows_success+=1;
						$this -> FKassig($CustomerNumberId); 
					}
				}
				else{
					$totals_rows_failed+=1;
				}
			}
			
			$rows++;	
		}
		
	/* save data to log system ****/
	
	
		$SQL_update_report['TotalDataRows']  = $totals_rows_sizedata;
		$SQL_update_report['TotalFailedRows'] = $totals_rows_failed;
		$SQL_update_report['TotalSuccessRows'] = $totals_rows_success;
		$SQL_update_report['TotalDuplicateSameCampaign'] = $totals_rows_samescmp;
		$SQL_update_report['TotalDuplicateOtherCampaign'] = $totals_rows_campaign;
	
		
		if( $this -> set_mysql_update('t_gn_uploadreport',$SQL_update_report, array('UploadId'=>$UploadId)))
		// if( $valid_rows )
		{
			
			$result['result']  = 1;
			$result['tot_rows'] = $totals_rows_sizedata;
			$result['tot_success_rows'] =  $totals_rows_success;
			$result['tot_update_rows'] = $totals_rows_updates;
			$result['tot_other_campaign_rows'] = $totals_rows_campaign;
			$result['tot_same_campaign_rows'] =  $totals_rows_samescmp;
			$result['campaign_upload_id'] = $this -> getCampaignId();
			$result['tot_failed_rows'] = $totals_rows_failed;
			
			echo json_encode($result);	
		}
		
	}
	
	/* 
	* start import data from text mode fixedwidth,
	* to t_gn_customers 
	*/	
		
		private function startUploadsText()
		{
			ini_set("memory_limit", "1024M");
			$this -> FixedWidth = new FixedWidth($this -> Files['tmp_name'],$this ->__get_text_header());	
			if( $this -> FixedWidth -> CountColumns() > 0 )
			{
				$tot_success_rows   = 0;
				$tot_failed_rows    = 0;
				$tot_updates_rows   = 0;
				$tot_other_campaign = 0;
				
				$rows = 0;
				while(($rows < $this -> FixedWidth -> CountLines()) )
				{
					if( ($this -> FixedWidth -> value( $rows, 'customer_name')!='') && (strlen($this -> FixedWidth -> value( $rows, 'customer_name'))> 2) )
					{
						
						$Contents['CampaignId']			  	= $this -> getCampaignId();
						$Contents['CustomerNumber'] 		= $this -> FixedWidth -> value( $rows, 'customer_number');
						$Contents['CustomerFirstName'] 	 	= $this -> FixedWidth -> value( $rows, 'customer_name');
						$Contents['CustomerDOB'] 			= $this -> FixedWidth -> value( $rows, 'customer_dob');
						$Contents['CustomerAddressLine1']   = $this -> FixedWidth -> value( $rows, 'customer_address1');
						$Contents['CustomerAddressLine2']   = $this -> FixedWidth -> value( $rows, 'customer_address2');
						$Contents['CustomerAddressLine3']   = $this -> FixedWidth -> value( $rows, 'customer_address3');
						$Contents['CustomerAddressLine4']   = $this -> FixedWidth -> value( $rows, 'customer_address4');
						$Contents['CustomerCity'] 		  	= $this -> FixedWidth -> value( $rows, 'customer_city');
						$Contents['CustomerZipCode'] 		= $this -> FixedWidth -> value( $rows, 'customer_zipcode');
						$Contents['CustomerCardType']		= $this -> FixedWidth -> value( $rows, 'customer_cardtype');
						$Contents['GenderId']		  		= $this -> getCustomerGender($this -> FixedWidth -> value( $rows, 'customer_gender'));
						$Contents['CustomerHomePhoneNum']   = $this -> ClearPhoneNumber($this -> FixedWidth -> value( $rows, 'customer_home_phone'));
						$Contents['CustomerMobilePhoneNum'] = $this -> ClearPhoneNumber($this -> FixedWidth -> value( $rows, 'customer_mobile_phone'));
						$Contents['CustomerWorkPhoneNum']   = $this -> ClearPhoneNumber($this -> FixedWidth -> value( $rows, 'customer_office_phone'));
						$Contents['UploadedById']			= $this -> getSession('UserId');
						$Contents['CustomerUploadedTs']     = date('Y-m-d H:i:s');
						
						$SQL_Wheres['UploadedById']			= $this -> getSession('UserId');
						$SQL_Wheres['CustomerUploadedTs']   = date('Y-m-d H:i:s');
						
					/** cek cutomer other campaign ***/
						
						if( $this -> checkCustomer($Contents['CampaignId'], $Contents['CustomerNumber']) )
						{
							$SQL_tmp['CampaignId']				= $Contents['CampaignId'];
							$SQL_tmp['CustomerNumber']			= $Contents['CustomerNumber'];
							$SQL_tmp['CustomerFirstName']		= $Contents['CustomerFirstName'];
							$SQL_tmp['Gender']					= $Contents['GenderId'];
							$SQL_tmp['CardTypeDesc']			= $Contents['CustomerCardType'];
							$SQL_tmp['CustomerDOB']				= $Contents['CustomerDOB'];
							$SQL_tmp['CustomerAddressLine1']	= $Contents['CustomerAddressLine1'];
							$SQL_tmp['CustomerAddressLine2']	= $Contents['CustomerAddressLine2'];
							$SQL_tmp['CustomerAddressLine3']	= $Contents['CustomerAddressLine3'];
							$SQL_tmp['CustomerAddressLine4']	= $Contents['CustomerAddressLine4'];
							$SQL_tmp['CustomerCity']			= $Contents['CustomerCity'];
							$SQL_tmp['CustomerZipCode']			= $Contents['CustomerZipCode'];
							$SQL_tmp['CustomerHomePhoneNum']	= $Contents['CustomerHomePhoneNum'];
							$SQL_tmp['CustomerMobilePhoneNum']	= $Contents['CustomerMobilePhoneNum'];
							$SQL_tmp['CustomerWorkPhoneNum']	= $Contents['CustomerWorkPhoneNum'];
							$SQL_tmp['CustomerUploadedTs']		= $Contents['CustomerUploadedTs'];
							$SQL_tmp['UploadedById']			= $Contents['UploadedById'];
							$SQL_tmp['UploadFileName']			= $this -> Files['name'];
							$SQL_tmp['UpoadDuplicateCampaign']	= $this -> getCampaignIdCustomers($Contents['CustomerNumber']);
							$SQL_whr['UpoadDuplicateCampaign']	= $this -> getCampaignIdCustomers($Contents['CustomerNumber']);
							
							if( $this -> set_mysql_insert('t_gn_tmpupload',$SQL_tmp,$SQL_whr)){
								$tot_other_campaign+=1;
							}
						}
						else
						{
							if( !$this -> getSameCustomer( $Contents['CampaignId'], $Contents['CustomerNumber']) )
							{	
								$SQL_Ins['CampaignId']				= $Contents['CampaignId'];
								$SQL_Ins['CustomerNumber']			= $Contents['CustomerNumber'];
								$SQL_Ins['CustomerFirstName']		= $Contents['CustomerFirstName'];
								$SQL_Ins['GenderId']				= $Contents['GenderId'];
								$SQL_Ins['CustomerCardType']		= $Contents['CustomerCardType'];
								$SQL_Ins['CustomerDOB']				= $Contents['CustomerDOB'];
								$SQL_Ins['CustomerAddressLine1']	= $Contents['CustomerAddressLine1'];
								$SQL_Ins['CustomerAddressLine2']	= $Contents['CustomerAddressLine2'];
								$SQL_Ins['CustomerAddressLine3']	= $Contents['CustomerAddressLine3'];
								$SQL_Ins['CustomerAddressLine4']	= $Contents['CustomerAddressLine4'];
								$SQL_Ins['CustomerCity']			= $Contents['CustomerCity'];
								$SQL_Ins['CustomerZipCode']			= $Contents['CustomerZipCode'];
								$SQL_Ins['CustomerHomePhoneNum']	= $Contents['CustomerHomePhoneNum'];
								$SQL_Ins['CustomerMobilePhoneNum']	= $Contents['CustomerMobilePhoneNum'];
								$SQL_Ins['CustomerWorkPhoneNum']	= $Contents['CustomerWorkPhoneNum'];
								$SQL_Ins['CustomerUploadedTs']		= $Contents['CustomerUploadedTs'];
								$SQL_Ins['UploadedById']			= $Contents['UploadedById'];
								$SQL_Ins['CustomerUploadedTs']		= $Contents['CustomerUploadedTs'];
								
								
								$res = $this -> set_mysql_insert('t_gn_customer',$SQL_Ins);
								if( $res)
								{
									$tot_success_rows +=1; 
									$MYQL_last_insertId = $this -> get_insert_id();
									$this -> FKassig($MYQL_last_insertId); 
								}
							}
							else
							{
								$tot_updates_rows+= 1;
							}	
						}
					}
					else{
						$tot_failed_rows +=1; 
					}	
					
					$rows++;
				}
				
				$SQL_report['UploadDateTs']  = date('Y-m-d H:i:s');
				$SQL_report['UploadFileName'] = $this -> Files['name'];
				$SQL_report['UploadTemporaryLocation']  = $this -> getLocationFile();
				$SQL_report['TotalDataRows']  = $rows;
				$SQL_report['TotalFailedRows'] = $tot_failed_rows;
				$SQL_report['TotalSuccessRows'] = $tot_success_rows;
				$SQL_report['TotalDuplicateSameCampaign'] = $tot_updates_rows;
				$SQL_report['TotalDuplicateOtherCampaign'] = $tot_other_campaign;
				$SQL_report['UploadUerId'] = $_SESSION['UserId'];
				
				if( $this -> set_mysql_insert('t_gn_uploadreport',$SQL_report) )
				{
					$result = array
					(
						'result' => 1,
						'tot_rows' => $rows,
						'tot_success_rows' => $tot_success_rows,
						'tot_update_rows' => $tot_updates_rows,
						'tot_other_campaign_rows' => $tot_other_campaign,
						'tot_same_campaign_rows' => $tot_updates_rows,
						'campaign_upload_id' => $this -> getCampaignId(),
						'tot_failed_rows' => $tot_failed_rows	
					);
						
					echo json_encode($result);	
				}
			}
		}
	}
	
	$Uploads = new Uploads();
	$Uploads -> initClass();
?>