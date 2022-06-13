<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(APPPATH.'../../report/Export/PHPExcel.php');
// echo APPPATH;
class appexcel extends PHPExcel
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function GetCellByColumnAndRow($start = NULL, $row1 = NULL, $end = NULL,  $row2 = null)
	{
		$cell = 'A1';
		
		if(is_null($start) && is_null($row1))
		{
			return $cell;
		}
		if(!is_null($start) && !is_null($row1))
		{
			$start = PHPExcel_Cell::stringFromColumnIndex($start);
			$cell = $start.$row1;
		}
		
		if(!is_null($end) && !is_null($row2))
		{
			$end = PHPExcel_Cell::stringFromColumnIndex($end);
			$cell .= ":".$end.$row2;
		}
		return $cell;
	}
}