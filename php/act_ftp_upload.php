<?php

require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../plugin/class.ftp.php");
require(dirname(__FILE__)."/../class/class_fixedwidth.php");

define('destination','/var/www/html/AJMI/upload');

class FTP_Upload extends mysql
{

var $class_ftp;
	
function FTP_Upload()
	{
		parent::__construct();
		self::FTP_Action();
		self::getFileLocation();
	}
	
/** run of action FTP **/

function result_rows()
{
	$sql = " SELECT  
				ftp_id, ftp_port, ftp_user, ftp_pasword, ftp_host, 
				ftp_get_file, ftp_put_file, ftp_history_file 
			 FROM tms_ftp_config WHERE ftp_flags=1 ";
			 
	$qry = $this -> query($sql);
	if( is_object($qry) )
	{
		return $qry;
	}	
}	

/** get result setting FTP ID ***/

function FTP_get_resultId()
{
	$qry = $this -> result_rows();
	if(!$qry -> EOF() )
	{
		return $qry -> result_get_value('ftp_id');
	}
}


/** run of action FTP **/
	
function FTP_Action()
	{
		$qry = $this -> result_rows();
		
		if(!$qry -> EOF())
		{
			$this -> class_ftp = new FTP($qry -> result_get_value('ftp_host'), $qry -> result_get_value('ftp_user'), $qry -> result_get_value('ftp_pasword'), (INT)$qry -> result_get_value('ftp_port'));
			if( is_object($this -> class_ftp))
			{	
				$this -> class_ftp -> __FTP_Connect();
				$this -> class_ftp -> __FTP_Setup( array( 'patern' =>'txt', 'mode' => 'get', 'get_dir' => $qry -> result_get_value('ftp_get_file'),  'put_dir' => $qry -> result_get_value('ftp_put_file')));
			}			
		}
}

/** Move ***/

public function FTP_move_data( $from_dir='', $dest_dir='',  $original_file='' )
{
	$new_file_text = $original_file.'_'.date('Ymd').'_'.date('H:i').'.txt';
	if( rename("$from_dir/$original_file", "$from_dir/$new_file_text"))
	{
		if( copy("$from_dir/$new_file_text", "$dest_dir/$new_file_text"))
		{
			@unlink("$from_dir/$new_file_text");
			$result  = true;
		}
	}
	
	return $result;	
}	
	
/** location file **/
	
function getFileLocation()
{
	$qry = $this -> result_rows();
	if(!$qry -> EOF())
	{
		$file_location_dir = $qry -> result_get_value('ftp_put_file');
		$file_location_history = $qry -> result_get_value('ftp_history_file');
		
		if( !empty( $file_location_dir) )
		{
			$result_rows = scandir( $file_location_dir );
			foreach( $result_rows as $key => $file_name_txt )
			{
				$exponen = explode(".",$file_name_txt);
				if( strtolower($exponen[(count($exponen)-1)])=='txt')
				{
					$file_detected_rows = $file_location_dir.'/'.$file_name_txt;
					if( file_exists($file_detected_rows) )
					{
						$totals_data = $this -> FTP_process_data($file_detected_rows);
						if( $totals_data > 1 )
						{
							$this -> FTP_move_data( $file_location_dir, $file_location_history, $file_name_txt);
						}
					}
				}
			}
		}
	}
}	
	
/*** function clear number phone **/

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
		
/**	function get gender **/

function getCustomerGender($code)
	{
		$sql = " select a.GenderId from t_lk_gender a where a.GenderShortCode='".strtoupper($code)."' ";
		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();
		
	}	
	
/**** define of columns fixed width text ******/

function Definition()
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

/** start uploads **/
 	
function FTP_process_data($file_detected_name)
	{
		ini_set("memory_limit", "1024M");
		
		$total_data_uploads  = 0;
		
		$this -> FixedWidth = new FixedWidth($file_detected_name,$this -> Definition() );	
		if( $this -> FixedWidth -> CountColumns() > 0 )
		{
			$rows = 0;
			while(($rows < $this -> FixedWidth -> CountLines()) )
			{
				if( ($this -> FixedWidth -> value( $rows, 'customer_name')!='') && 
					(strlen($this -> FixedWidth -> value( $rows, 'customer_name'))> 2) )
				{
					$SQL_Ins['FTP_DataId']			  = $this -> FTP_get_resultId();	
					$SQL_Ins['CustomerNumber'] 		  = $this -> FixedWidth -> value( $rows, 'customer_number');
					$SQL_Ins['CustomerFirstName']     = $this -> FixedWidth -> value( $rows, 'customer_name');
					$SQL_Ins['CustomerDOB'] 		  = $this -> FixedWidth -> value( $rows, 'customer_dob');
					$SQL_Ins['CustomerAddressLine1']  = $this -> FixedWidth -> value( $rows, 'customer_address1');
					$SQL_Ins['CustomerAddressLine2']  = $this -> FixedWidth -> value( $rows, 'customer_address2');
					$SQL_Ins['CustomerAddressLine3']  = $this -> FixedWidth -> value( $rows, 'customer_address3');
					$SQL_Ins['CustomerAddressLine4']  = $this -> FixedWidth -> value( $rows, 'customer_address4');
					$SQL_Ins['CustomerCity'] 		  = $this -> FixedWidth -> value( $rows, 'customer_city');
					$SQL_Ins['CustomerZipCode'] 	  = $this -> FixedWidth -> value( $rows, 'customer_zipcode');
					$SQL_Ins['CustomerCardType']	  = $this -> FixedWidth -> value( $rows, 'customer_cardtype');
					$SQL_Ins['GenderId']		  	  = $this -> FixedWidth -> value( $rows, 'customer_gender');
					$SQL_Ins['CustomerHomePhoneNum']  = $this -> ClearPhoneNumber($this -> FixedWidth -> value( $rows, 'customer_home_phone'));
					$SQL_Ins['CustomerMobilePhoneNum']= $this -> ClearPhoneNumber($this -> FixedWidth -> value( $rows, 'customer_mobile_phone'));
					$SQL_Ins['CustomerWorkPhoneNum']  = $this -> ClearPhoneNumber($this -> FixedWidth -> value( $rows, 'customer_office_phone'));
					$SQL_Ins['CustomerUploadedTs']    = date('Y-m-d H:i:s');
					$SQL_Wheres['CustomerUpdatedTs']  = date('Y-m-d H:i:s');
					
					if( $this -> set_mysql_insert('t_gn_ftp_customers',$SQL_Ins,$SQL_Wheres) )
					{
						$total_data_uploads +=1;
					}
				}
				
				$rows++;
			}
		}

		return $total_data_uploads;
		
	}		
}

new FTP_Upload();


?>