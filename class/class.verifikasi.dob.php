<?php
require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
	
class VerifikasiDOB	extends mysql
{
	function VerifikasiDOB()
	{
		parent::__construct();
	}
	
	function index()
	{
		switch($_REQUEST['action'])
		{
			case 'verifikasi_dob' : $this -> VerifikasiCustomerDOB(); break;
			
		}
	}
	
	function VerifikasiCustomerDOB()
	{
		$sql = "select count(a.CustomerId) as jumlah from t_gn_customer a 
				where a.CustomerId='".$_REQUEST['CustomerId']."'
				AND a.CustomerDOB ='".$this -> formatDateEng($_REQUEST['customer_dob'])."'";
				
		echo json_encode(array('result'=>$this -> valueSQL($sql)));
	}
}

$VerifikasiDOB = new VerifikasiDOB();
$VerifikasiDOB -> index();

?>