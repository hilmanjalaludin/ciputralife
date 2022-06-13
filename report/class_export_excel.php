<?
 class excel
 {	
	function __construct()
	{
	  $default;
	  //$this->xlsWriteHeader($default);
	}
	
	function __destruct()
	{
		$this->xlsEOF();
		$this->xlsClose();
	}
	
	function xlsBOF()
	{
	 echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
	 return;
	}

	function xlsEOF()
	{
		echo pack("ss", 0x0A, 0x00);
		return;
	}

	function xlsWriteNumber($Row, $Col, $Value)
	{
		echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
		echo pack("d", $Value);
		return;
	}

	function xlsWriteLabel($Row, $Col, $Value )
	{
		$L = strlen($Value);
		echo pack("s*", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
		echo $Value;
	return;
	} 
	
	
	function xlsWriteHeader($xlsFilename)
	{
		if(!empty($xlsFilename))
		{
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");;
			header("Content-Disposition: attachment;filename=".$xlsFilename.".xls"); 
			header("Content-Transfer-Encoding: binary ");
		}
		else
		{
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");;
			header("Content-Disposition: attachment;filename=PhpExporttoExcel.xls"); 
			header("Content-Transfer-Encoding: binary ");
		}
		
	  $this->xlsBOF();
	}
	
	function xlsClose()
	{
		exit;
	}
}
?>