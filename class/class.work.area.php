<?php

require(dirname(__FILE__).'/../sisipan/sessions.php');
require(dirname(__FILE__).'/../class/MYSQLConnect.php');
require(dirname(__FILE__).'/../class/lib.form.php');

/*
 * class filename class.work.area.php
 * subject product application
 * version v.6
 * author : omens
 */	
	
class WorkArea extends mysql
{
	function WorkArea(){
		parent::__construct();
		$this -> setForm();
	}
	
  
/* eidt tpl work Area ****************************/
		
	private function setForm()
	{
		$this -> setForm = new jpForm();
	}
  
/* eidt tpl work Area ****************************/
	
	private function getJson( $opt = 0 )
	{
		echo json_encode( array('success' => $opt) );	
	}

  
/* eidt tpl work Area ****************************/

	private function WorkAreaId()
	{
		if( $this -> havepost('BranchId'))
		{
			$arr_result = explode(',',$this -> escPost('BranchId'));
			if( is_array($arr_result) )
			{
				return $arr_result;
			}
		}
	}
	
  
/* eidt tpl work Area ****************************/

	function index()
	{
		switch($_REQUEST['action'])
		{
			case 'enable_work_area'  : $this -> EnableWorkArea(); 	break;
			case 'disable_work_area' : $this -> DisableWorkArea(); 	break;
			case 'delete_work_area'  : $this -> DeleteWorkArea(); 	break;
			case 'edit_work_area' 	 : $this -> EditWorkArea(); 	break;
			case 'update_work_area'  : $this -> UpdateWorkArea(); 	break;
			case 'add_work_area'	 : $this -> AddWorkArea(); 		break;	
			case 'insert_work_area'	 : $this -> InsertWorkArea(); 	break;
			
		}
	}
	
/** function ineternal factory **/

	function BranchData()
	{
		return new FactoryBranch($_REQUEST['BranchId']);	
	}
	
/* eidt tpl work Area ****************************/

	function get_catgory_area()
	{
		$sql ="select * from tms_catgory_area a ";
		$qry = $this -> query($sql);
		foreach($qry-> result_assoc() as $rows )
		{
			$datas[$rows['AreaCatgoryId']] = $rows['AreaCategotyName'];
		}
		return $datas;
	}		
	
/* function set style css **/

	function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:180px;font-size:11px;height:22px;background-color:#fffccc;}
					.input_text {border:1px solid #dddddd;width:250px;font-size:12px;height:18px;background-image:url('../gambar/input_bg.png');}
					.text_header { text-align:right;color:#24385c;font-weight:bold;font-size:12px;}
					.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
					.text_area{border:1px solid #dddddd;width:250px;height:100px;background-color:#fffccc;}
				</style>
				
			<!-- stop: css -->
	<?php }
		
  
/* eidt tpl work Area ****************************/

  function EnableWorkArea()
  {
		$i = 0; 
		
		if(	is_array($this -> WorkAreaId()) )
		{
			foreach($this -> WorkAreaId() as $k=>$rows )
			{
				$sql = " UPDATE t_lk_branch a SET a.BranchFlags=1 WHERE a.BranchId='$rows'";
				$qry = $this -> execute($sql,__FILE__,__LINE__);
				if( $qry )
				{
					$i++;
				}
			}
		}
		
		if( $i > 0 ) $this -> getJson(1);
  }
  
  
/* eidt tpl work Area ****************************/

  function DisableWorkArea()
  {
		$i = 0; 
		if(	is_array($this -> WorkAreaId()) )
		{
			foreach($this -> WorkAreaId() as $k=>$rows )
			{
				$sql = " UPDATE t_lk_branch a SET a.BranchFlags=0 WHERE a.BranchId='$rows'";
				$qry = $this -> execute($sql,__FILE__,__LINE__);
				if( $qry )
				{
					$i++;
				}
			}
		}
		
		if( $i > 0 ) $this -> getJson(1);
  }
  
/* eidt tpl work Area ****************************/

  function DeleteWorkArea()
  {
		$i = 0; 
		if(	is_array($this -> WorkAreaId()) )
		{
			foreach($this -> WorkAreaId() as $k=>$rows )
			{
				$sql = " DELETE FROM t_lk_branch WHERE BranchId='$rows'";
				$qry = $this -> execute($sql,__FILE__,__LINE__);
				if( $qry )
				{
					$i++;
				}
			}
		}
	 if( $i > 0 ) $this -> getJson(1);
  }
  
/* eidt tpl work Area ****************************/

  function EditWorkArea()
  {
	$Branch = $this -> BranchData();
	$this -> setCss(); 
	?>
		<fieldset style='border:1px solid #dddddd;' class="corner">
			<input type="hidden" name="BranchId" id="BranchId" value="<?php echo $Branch -> getBranchId();?>"/>
			<legend> Edit Work Area </legend>
			<div class="box-shadow">
				<table border='0' width='99%' align='center' cellpadding='6px;'>
					<tr> 
						<td class='text_header'>* ) Branch Code : </td>
						<td ><?php $this -> setForm ->jpInput('BranchCode','input_text',$Branch -> getBranchCode());?></td>
						<td class='text_header'>Contact Phone : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('BranchContact','input_text',$Branch -> getBranchContact());?></td>
					</tr>
					<tr> 
						<td class='text_header'>* ) Branch Name : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('BranchName','input_text',$Branch -> getBranchName());?></td>
						<td class='text_header'>Branch Manager : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('BranchManager','input_text',$Branch -> getBranchManager());?></td>
					</tr>
					
					<tr> 
						<td class='text_header'>Branch Mail : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('BranchEmail','input_text',$Branch -> getBranchEmail());?></td>
						<td class='text_header' rowspan="2">Branch Address : </td>
						<td class='text_content' rowspan="2"><?php $this -> setForm ->jpTextArea('BranchAddress','text_area',$Branch -> getBranchAddress());?></td>
					</tr>
					
					<tr> 
						<td class='text_header'></td>
						<td class='text_content'></td>
					</tr>
					<tr>
						<td class="text_header">&nbsp;</td>
						<td colspan="3"><a href="javascript:void(0);" class="sbutton" onclick="UpdateData();"><span>&nbsp;Update</span></a></td>
					</tr>
				</table>
			</div>	
		</fieldset>
	<?php 	
  }
  
/* Udpate work Area ****************************/

  function UpdateWorkArea()
  {
		$result = array('result'=>0);
		
		$SQL_update['BranchCode'] 	 =  $this -> escPost('BranchCode');
		$SQL_update['BranchName'] 	 =  $this -> escPost('BranchName');
		$SQL_update['BranchManager'] =  $this -> escPost('BranchManager');
		$SQL_update['BranchContact'] =  $this -> escPost('BranchContact');
		$SQL_update['BranchAddress'] =  $this -> escPost('BranchAddress');
		$SQL_update['BranchEmail']   =  $this -> escPost('BranchEmail');
		
		$SQL_wheres['BranchId'] = $this -> escPost('BranchId');
		if( $this -> set_mysql_update('t_lk_branch',$SQL_update, $SQL_wheres))
		{
			$result = array('result'=>1);
		}
		
	echo json_encode($result);	
  }
  
/* tpl  work Area ****************************/

  function AddWorkArea()
  {
	$Branch = $this -> BranchData();
	$this -> setCss(); 
	?>
		<fieldset style='border:1px solid #dddddd;' class="corner">
			<legend> Add Work Area </legend>
			<div class="box-shadow">
				<table border='0' width='99%' align='center' cellpadding='6px;'>
					<tr> 
						<td class='text_header'>* ) Branch Code : </td>
						<td ><?php $this -> setForm ->jpInput('BranchCode','input_text',$Branch -> getBranchCode());?></td>
						<td class='text_header'>Contact Phone : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('BranchContact','input_text',$Branch -> getBranchContact());?></td>
					</tr>
					<tr> 
						<td class='text_header'>* ) Branch Name : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('BranchName','input_text',$Branch -> getBranchName());?></td>
						<td class='text_header' >Branch Address : </td>
						<td class='text_content'><?php $this -> setForm ->jpTextArea('BranchAddress','text_area',$Branch -> getBranchAddress());?></td>
					</tr>
					<tr> 
						<td class='text_header'>Branch Mail : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('BranchEmail','input_text',$Branch -> getBranchEmail());?></td>
						<td class='text_header'>Branch Manager : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('BranchManager','input_text',$Branch -> getBranchManager());?></td>
					</tr>
					<tr>
						<td class="text_header">&nbsp;</td>
						<td colspan="3"><a href="javascript:void(0);" class="sbutton" onclick="saveResult();"><span>&nbsp;Save</span></a></td>
					</tr>
				</table>
			</div>	
		</fieldset>
	<?php 	
  }

/* insert work area **/
 
  function InsertWorkArea()
  {
	$result = array('result'=>0);
	
		$SQL_insert['BranchCode'] 	 =  $this -> escPost('BranchCode');
		$SQL_insert['BranchName'] 	 =  $this -> escPost('BranchName');
		$SQL_insert['BranchManager'] =  $this -> escPost('BranchManager');
		$SQL_insert['BranchContact'] =  $this -> escPost('BranchContact');
		$SQL_insert['BranchAddress'] =  $this -> escPost('BranchAddress');
		$SQL_insert['BranchEmail']   =  $this -> escPost('BranchEmail');
		
		if( $this -> set_mysql_insert('t_lk_branch',$SQL_insert))
		{
			$result = array('result'=>1);
		}
		
	echo json_encode($result);	
  }
  
}

/** start : factory internal ***/
class FactoryBranch extends WorkArea
{
	private  $BranchData = array();
	function FactoryBranch($BranchId)
	{
		$sql = " SELECT * FROM t_lk_branch where BranchId = '$BranchId'";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			$this -> BranchData = $qry -> result_first_assoc();
		}	
	}
	
/** return function BranchId ***/
	
	function getBranchId()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['BranchId'];
		}
	}
	
/** return function BranchId ***/
	
	function getBranchCode()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['BranchCode'];
		}
	}
	
/** return function BranchId ***/
	
	function getBranchName()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['BranchName'];
		}
	}
	
/** return function BranchId ***/
	
	function getBranchManager()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['BranchManager'];
		}
	}
	
/** return function BranchId ***/
	
	function getBranchContact()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['BranchContact'];
		}
	}
/** return function BranchId ***/
	
	function getBranchAddress()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['BranchAddress'];
		}
	}
	
/** return function BranchId ***/
	
	function getBranchEmail()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['BranchEmail'];
		}
	}
/** return function BranchId ***/
	
	function getBranchFlags()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['BranchFlags'];
		}
	}
}

/* end : factory **/


$WorkArea = new WorkArea();
$WorkArea -> index();
?>