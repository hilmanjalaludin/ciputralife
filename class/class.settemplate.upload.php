<?php

require(dirname(__FILE__).'/../sisipan/sessions.php');
require(dirname(__FILE__).'/../class/MYSQLConnect.php');
require(dirname(__FILE__).'/../class/lib.form.php');
require(dirname(__FILE__).'/../plugin/class_export_excel.php');
		

/*
 * class filename class.work.area.php
 * subject product application
 * version v.6
 * author : omens
 */	
class SetleTemplate extends mysql
{
	var $ExcelClass;
	var $action;
	var $MyForm;
	
	function SetleTemplate()
	{
		parent::__construct();
		$this->action = $this->escPost('action');
		$this -> MyForm = new jpForm();
	}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

	
	private function setSelectTable()
	{
		if( $this -> havepost('tables') )
		{
			return $_REQUEST['tables'];
		}
	}
	
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
	
	function index ()
	{
		if( $this->havepost('action')):
			switch( $this->action )
			{
				case 'get_columns' 		 : $this -> getColumnsTable(); 	break;
				case 'save_tempalate' 	 : $this -> SaveTemplate();	  	break;
				case 'download_template' : $this -> DownloadTemplate();	break;
				case 'delete_template'	 : $this -> DeletedTemplate();	break;
				case 'enable_template'	 : $this -> EnableTemplate();	break;
			}
		endif;
	}
	
//////////////////////////////////////////////////////////
	function EnableTemplate()
	{
		if( $this -> havepost('check_list_id'))
		{
			$sql = "UPDATE tms_tempalate_upload SET TemplateFlags = 1 where TemplateId = '".$this -> escPost('TemplateId')."'";
			$q = $this -> execute($sql,__FILE__,__LINE__);
			if ($q) : echo 1;
			else :
				echo 0;
			endif;
				
		}
	}
//////////////////////////////////////////////////////////
	
	function DeletedTemplate()
	{
		$error = array('result'=> 0);
		if( $this -> havepost('check_list_id'))
		{
			$exp_list_data = EXPLODE(",", $this -> escPost('check_list_id'));
			if( is_array($exp_list_data))
			{
				$totals = 0;
				foreach( $exp_list_data as $keys => $TempId )
				{
					$sql = " DELETE FROM tms_tempalate_upload  WHERE TemplateId= '$TempId'";
					if( $this -> execute($sql,__FILE__,__LINE__))
					{
						$sql = " DELETE FROM tms_template_rows WHERE UploadTmpId ='$TempId'";
						if( $this -> execute($sql,__FILE__,__LINE__) )
						{
							$totals++;
						}
					}
				}
			}
		}
		
		if( $totals > 0 ) $error = array('result'=> 1); 
	
		echo json_encode($error);
	}
	
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
	
	function getColumnsTable()
	{
		$result = array();
		if( $this -> havepost('tables'))
		{
			$sql = 'DESC '.$this -> setSelectTable();
			$qry = $this -> query($sql);
			
			echo "<div style='padding:4px;border:1px solid #dddddd;overflow:auto;height:550px;background-color:#FFFCCC;'>";
			echo "<table border=0 width='90%' cellspacing=0 align='center' style='border-right:1px solid #ddddff;border-bottom:1px solid #ddddff;'>".
					"<tr>".
						" <td style='border-left:1px solid #ddddff;padding-left:2px;background-color:#BBDDDD;height:22px;'> ".
						" <a href='javascript:void(0);' onclick='ReturnNextForm(\"chk_columns\");return false;'>#</a></td> ".
						" <td style='border-left:1px solid #ddddff;padding-left:2px;background-color:#BBDDDD;height:22px;'>Cols Name</td>".
						" <td style='border-left:1px solid #ddddff;padding-left:2px;background-color:#BBDDDD;height:22px;'>Alias Name</td>".
						" <td style='border-left:1px solid #ddddff;padding-left:2px;background-color:#BBDDDD;height:22px;'>Order</td>".
					"</tr>";
					
			foreach( $qry -> result_array() as $rows )
			{
				echo " <tr>".
						 " <td style='border-top:1px solid #ddddff; border-left:1px solid #ddddff;padding-left:2px;height:22px;' valign='middle'>".$this -> MyForm -> jpResulCheck('chk_columns',null,$rows['Field'],'onchange="getListCheck(this);";')."</td>".
						 " <td style='border-top:1px solid #ddddff; border-left:1px solid #ddddff;padding-left:2px;height:22px;' nowrap><input type='text' class='input_alias' id='value_name_".$rows['Field']."' name='value_name_".$rows['Field']."' value='".$rows['Field']."'>&nbsp;".$rows['Type']."</td>".
						 " <td style='border-top:1px solid #ddddff; border-left:1px solid #ddddff;padding-left:2px;height:22px;' nowrap><input type='text' class='input_alias' id='alias_name_".$rows['Field']."' name='alias_name_".$rows['Field']."' value=''></td>".
						 " <td style='border-top:1px solid #ddddff; border-left:1px solid #ddddff;padding-left:2px;height:22px;' nowrap><input type='text' class='input_box' id='order_name_".$rows['Field']."' name='order_name_".$rows['Field']."' value=''></td>".
					  "</tr>";	 
			}
			echo "</table>";
			echo "</div>";
		}
		
	}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
	
	function SaveTemplate()
	{
		$success = array('result'=>0);
		if( $this -> havepost('table_name') && $this -> havepost('list_check'))
		{
			$SQL_insert['TemplateTableName'] = $this -> escPost('table_name'); 
			$SQL_insert['TemplateName'] 	 = $this -> escPost('templ_name');
			$SQL_insert['TemplateMode'] 	 = $this -> escPost('mode_input');
			$SQL_insert['TemplateFileType']  = $this -> escPost('file_type');
			$SQL_insert['TemplateCreateTs']  = date('Y-m-d H:i:s');
			
			if( $this -> set_mysql_insert('tms_tempalate_upload', $SQL_insert))
			{
				$get_insert_id = $this -> get_insert_id();
				$ArrayCheckData = explode("|",$this -> escPost('list_check'));
				foreach( $ArrayCheckData as $key => $Datas )
				{
					$Columns = explode("~",$Datas);
					if( is_array($Columns) )
					{
						$SQL_raws[$key]['UploadTmpId'] = $get_insert_id;
						$SQL_raws[$key]['UploadColsName'] = $Columns[0];
						$SQL_raws[$key]['UploadColsAlias'] = $Columns[1];
						$SQL_raws[$key]['UploadColsOrder'] = $Columns[2];
					}	
				}
				
				$tots = 0;
				foreach( $SQL_raws as $SQL_string_keys )
				{
					if( $this -> set_mysql_insert('tms_template_rows', $SQL_string_keys ) )
					{
						$tots++; 
					}
				}
				
				if( $tots > 0 ) $success = array('result'=>1);
			}
		}
		
		echo json_encode($success);
	}
	
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
	
	
	function DownloadTemplate()
	{
		$this -> ExcelClass = new Excel();
		if( $this ->havepost('TemplateId') )
		{
			$TemplateId = $this ->escPost('TemplateId');
			if( $TemplateId )
			{
				$sql = " select a.TemplateFileType, a.TemplateName,b.UploadColsAlias from tms_tempalate_upload a 
						 left join tms_template_rows b on a.TemplateId=b.UploadTmpId
						 where a.TemplateId='$TemplateId' ORDER BY b.UploadColsOrder ASC "; 
				
				$qry = $this -> query($sql);
				if( $qry -> result_num_rows() > 0 )
				{
					$first_rows = $qry -> result_first_assoc();
					
					foreach( $qry -> result_assoc() as $rows )
					{
						$datas[] = $rows;
					}
					 
					if( is_array($first_rows) )
					{
						switch($first_rows['TemplateFileType'])
						{
							case 'xls' : $this -> getTemplateExcel($datas); break;	
							case 'csv' : $this -> getTemplateCSV($datas);   break;
							case 'txt' : $this -> getTemplateTXT($datas);   break;
						}
					}
				}
			}	
		}	
	}
	
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
	
	function getTemplateExcel( $rows = NULL )
	{
	
		foreach($rows as $result )
		{
			$TemplateName = $result['TemplateName'];
			$datas[] = $result['UploadColsAlias'];
		}
		
		if( $rows!=NULL )
		{
			if( is_array( $datas) )
			{	
				$this -> ExcelClass -> xlsWriteHeader(str_replace(" ","_",$TemplateName)."_".time());
				foreach($datas as $key => $result_raws )
				{
					$this -> ExcelClass -> xlsWriteLabel(0,$key,strtoupper($result_raws));
				}	
				$this -> ExcelClass -> xlsClose();
			}
		}	
	}
	
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
		
	function getTemplateTXT( $rows = NULL )
	{
		
	}
}	

$SetleTemplate = new SetleTemplate();
$SetleTemplate -> index();

?>