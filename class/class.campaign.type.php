<?php

require(dirname(__FILE__).'/../sisipan/sessions.php');
require(dirname(__FILE__).'/../class/MYSQLConnect.php');
require(dirname(__FILE__).'/../class/lib.form.php');

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
		if( $this -> havepost('CampaignTypeId'))
		{
			$arr_result = explode(',',$this -> escPost('CampaignTypeId'));
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
			case 'update_campaign_type'  : $this -> UpdateWorkArea(); 	break;
			case 'add_campaign_type'	 : $this -> AddWorkArea(); 		break;	
			case 'insert_campaign_type'	 : $this -> InsertWorkArea(); 	break;
			case 'clear_work_area'	 : $this -> clear_work_area(); 	break;
			
		}
	}
	
/** function ineternal factory **/

	function clear_work_area()
	{
		echo '';
	}

	function BranchData()
	{
		return new FactoryBranch($_REQUEST['CampaignTypeid']);	
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
  		$CampaignTypeid=$_REQUEST['CampaignTypeid'];
  		$result = array('result'=>0);
		$sql = " UPDATE t_lk_campaigntype set CampaignTypeStatus=1 WHERE CampaignTypeid=$CampaignTypeid";
		$result = array('result'=>$sql);
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		if( $qry )
		{
			$result = array('result'=>1);
		}
		echo json_encode($result);

  }
  
  
/* eidt tpl work Area ****************************/

  function DisableWorkArea()
  {
		$CampaignTypeid=$_REQUEST['CampaignTypeid'];
  		$result = array('result'=>0);
		$sql = " UPDATE t_lk_campaigntype set CampaignTypeStatus=0 WHERE CampaignTypeid=$CampaignTypeid";
		$result = array('result'=>$sql);
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		if( $qry )
		{
			$result = array('result'=>1);
		}
		echo json_encode($result);
  }
  
/* eidt tpl work Area ****************************/

  function DeleteWorkArea()
  {
  		$CampaignTypeid=$_REQUEST['CampaignTypeid'];
  		$result = array('result'=>0);
		$sql = " DELETE FROM t_lk_campaigntype WHERE CampaignTypeid=$CampaignTypeid";
		$result = array('result'=>$sql);
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		if( $qry )
		{
			$result = array('result'=>1);
		}
		echo json_encode($result);
  }
  
/* eidt tpl work Area ****************************/

  function EditWorkArea()
  {
  	$CampaignTypeid=$_REQUEST['CampaignTypeid'];
	$Branch = $this -> BranchData();
	$this -> setCss(); 
	?>
		<fieldset style='border:1px solid #dddddd;' class="corner">
			<input type="hidden" name="CampaignTypeid" id="CampaignTypeid" value="<?php echo $CampaignTypeid; ?>"/>
			<legend> Edit Work Area </legend>
			<div class="box-shadow">
				<table border='0' width='99%' align='center' cellpadding='6px;'>
					<tr> 
						<td class='text_header'>Campaign Type Code : </td>
						<td ><?php $this -> setForm ->jpInput('CampaignTypeCode','input_text',$Branch -> getBranchCode());?></td>
					</tr>
					<tr> 
						<td class='text_header'>Campaign Type Desc : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('CampaignTypeDesc','input_text',$Branch -> getBranchName());?></td>
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
		
		$SQL_update['CampaignTypeCode'] 	 =  $this -> escPost('code');
		$SQL_update['CampaignTypeDesc'] 	 =  $this -> escPost('desc');

		$SQL_wheres['CampaignTypeId'] = $this -> escPost('CampaignTypeId');
		if( $this -> set_mysql_update('t_lk_campaigntype',$SQL_update, $SQL_wheres))
		{
			$result = array('result'=>1);
		}
		
	echo json_encode($result);	
  }
  
/* tpl  work Area ****************************/

  function AddWorkArea()
  {
	// $Branch = $this -> BranchData();
	// echo json_encode($Branch);
	// die();
	$this -> setCss(); 
	?>
		<fieldset style='border:1px solid #dddddd;' class="corner">
			<legend> Add Campaign Type</legend>
			<div class="box-shadow">
				<table border='0' width='99%' align='center' cellpadding='6px;'>
					<tr> 
						<td class='text_header'>Campaign Type Code : </td>
						<td ><?php $this -> setForm ->jpInput('CampaignTypeCode','input_text');?></td>
					</tr>
					<tr> 
						<td class='text_header'>Campaign Type Desc : </td>
						<td class='text_content'><?php $this -> setForm ->jpInput('CampaignTypeDesc','input_text');?></td>
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
	
		$SQL_insert['CampaignTypeCode'] 	 =  $this -> escPost('code');
		$SQL_insert['CampaignTypeDesc'] 	 =  $this -> escPost('desc');
		// $SQL_insert['BranchManager'] =  $this -> escPost('BranchManager');
		// $SQL_insert['BranchContact'] =  $this -> escPost('BranchContact');
		// $SQL_insert['BranchAddress'] =  $this -> escPost('BranchAddress');
		// $SQL_insert['BranchEmail']   =  $this -> escPost('BranchEmail');
		
		if( $this -> set_mysql_insert('t_lk_campaigntype',$SQL_insert))
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
		$sql = "SELECT * FROM t_lk_campaigntype t where t.CampaignTypeId = '$BranchId'";
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
			return $this -> BranchData['CampaignTypeId'];
		}
	}
	
/** return function BranchId ***/
	
	function getBranchCode()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['CampaignTypeCode'];
		}
	}
	
/** return function BranchId ***/
	
	function getBranchName()
	{
		if( count($this -> BranchData)> 0 )
		{
			return $this -> BranchData['CampaignTypeDesc'];
		}
	}
	
/** return function BranchId ***/
	
	// function getBranchManager()
	// {
	// 	if( count($this -> BranchData)> 0 )
	// 	{
	// 		return $this -> BranchData['BranchManager'];
	// 	}
	// }
	
/** return function BranchId ***/
	
	// function getBranchContact()
	// {
	// 	if( count($this -> BranchData)> 0 )
	// 	{
	// 		return $this -> BranchData['BranchContact'];
	// 	}
	// }
/** return function BranchId ***/
	
	// function getBranchAddress()
	// {
	// 	if( count($this -> BranchData)> 0 )
	// 	{
	// 		return $this -> BranchData['BranchAddress'];
	// 	}
	// }
	
/** return function BranchId ***/
	
	// function getBranchEmail()
	// {
	// 	if( count($this -> BranchData)> 0 )
	// 	{
	// 		return $this -> BranchData['BranchEmail'];
	// 	}
	// }
/** return function BranchId ***/
	
	// function getBranchFlags()
	// {
	// 	if( count($this -> BranchData)> 0 )
	// 	{
	// 		return $this -> BranchData['BranchFlags'];
	// 	}
	// }
}

/* end : factory **/


$WorkArea = new WorkArea();
$WorkArea -> index();
?>