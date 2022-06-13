<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
include(dirname(__FILE__)."/../class/MYSQLConnect.php");
include(dirname(__FILE__)."/../class/lib.form.php");
require(dirname(__FILE__)."/../fungsi/global.php");


/**
 ** class name < CallmonQA > 
 ** function < read , view of colmon qa modul >
 ** author < razaki & AIA Team Devlopment >
 ** project < QA Cigna >
 ** create < 2014-01-05 >
 ** modified by Fajar 
 ** 14-05-2014
 **/

 
 
class CallmonQA extends mysql
{

	/**
	 ** default object yang akan
	 ** digunakan dalam object class ini..
	 **/

	 var $action;
	 var $Form;
	 var $customer;
	 
	/**
	** default methode aksess < aksesor  >
	** return < @ void >
	**/

	function CallmonQA()
	{
		parent::__construct();
		$this -> action 	= $this->escPost('action');
		$this -> Form  		= new jpForm();
		$this -> customer 	= $this->escPost('customerid');

		self::index(); // direct langsung 
	}
	function index()
	{
		if( $this -> havepost('action'))
		{
			switch( $this->action )
			{
				case 'get_content_header'	: $this -> getContentHeader(); 	break;
				//case 'look_up_point'		: $this -> getLookup();
				case 'get_content_call'		: $this -> getCallContent(); 	break;
				case 'get_store_nilai'		: $this -> _getNilai(); 		break;
				case 'get_sub_category'		: $this -> SubCategoryNilai();  break;
				case 'get_category'			: $this -> getCategoryNilai();  break;
				case 'save_call_mon'		: $this -> SaveCallMon();		break;
				
				
			}
		}
	}
	
	/** 
	 ** ambil sub category active table : "coll_category_collmon " 
	 ** return < array >
	 **/ 
	function SubCategoryNilai()
	{
		$sql = " select a.SubCategoryId, a.CategoryId from coll_subcategory_collmon a ";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$datas[$rows['CategoryId']][]= $rows['SubCategoryId'];
		}
		echo json_encode($datas);
	}
	/**
	 ** get customer informasi dari yang akan 
	 ** di follow up
	 ** return < @array >
	 **/ 	
	function getCategoryNilai()
	{
		$sql="select * from coll_category_collmon a where a.CollCategoryFlags=1";
		$qry = $this -> query($sql);
		$i=0;
		foreach($qry -> result_assoc() as $rows )
		{
			$data[$i] = $rows['CollCategoryId'];
			$i++;
		}

		echo json_encode($data);
	}
	
	
	
	/**
	 ** Ambil nilai per pertanyaan 
	 ** 
	 ** return < array >
	 **/ 	
	 
	function _getNilai()
	{
		$store = array();
		
		$sql = " select b.CallTypeNum, a.LinkPointId, a.CategoryId, a.SubCategoryId from coll_link_point a
					left join coll_calltype_collmon b on a.CallTypeId=b.CallTypeId
					left join coll_category_collmon c on a.CategoryId=c.CollCategoryId
					left join coll_subcategory_collmon d on c.CollCategoryId=d.CategoryId
					WHERE d.SubCategoryFlags=1 ORDER BY a.LinkPointId ASC ";
					
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$store['nilai'][$rows['CategoryId']][$rows['SubCategoryId']][$rows['LinkPointId']] = $rows['CallTypeNum'];
		}	
		
		
		$sql =  " SELECT a.CategoryId, a.SubCategoryId, a.LinkPointId, b.CallTypeName from coll_link_point a
				  LEFT JOIN coll_calltype_collmon b ON a.CallTypeId = b.CallTypeId ";
					
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$store['answer'][$rows['CategoryId']][$rows['SubCategoryId']][$rows['LinkPointId']] = $rows['CallTypeName'];
		}	
					
		
		echo json_encode($store);
	}
	
	
	function getCategory(){
		$sql = " select * from coll_category_collmon a where a.CollCategoryFlags=1";
		$qry = $this ->query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$data[] = $rows;
		}
		return $data;
	}
	 /** 
	 ** ambil Simbol score dan nilai score dari table : "coll_link_point" 
	 ** return < array >
	 **/ 
	 
	function getCallType($CategoryId=0, $SubCategoryId=0)
	{
		$sql = "select a.CallTypeName, a.CallTypeNum,a.CallTypeId,b.LinkPointId from coll_calltype_collmon a
				left join coll_link_point b on a.CallTypeId=b.CallTypeId
				where b.CategoryId='".$CategoryId."' 
				AND b.SubCategoryId='".$SubCategoryId."' 
				order by a.CallTypeNum ASC";
				
		$qry = $this ->query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$data[$rows['LinkPointId']] = $rows['CallTypeName'];
		}
		return $data;
	}
	
	/** 
	 ** info customer
	 ** 
	 **/
		
	function getCustomerInfo(){
		$sql = "SELECT a.CustomerFirstName, b.CampaignName, d.ProductCode, d.ProductName,
				DATE_FORMAT(f.PolicySalesDate,'%d-%m-%Y') AS SELLINGDATE, g.id
				FROM t_gn_customer a
				LEFT JOIN t_gn_campaign b ON a.CampaignId = b.CampaignId
				LEFT JOIN t_gn_campaignproduct c ON b.CampaignId = c.CampaignId
				LEFT JOIN t_gn_product d ON c.ProductId = d.ProductId
				LEFT JOIN t_gn_policyautogen e ON a.CustomerId=e.CustomerId
				LEFT JOIN t_gn_policy f ON e.PolicyNumber=f.PolicyNumber
				LEFT JOIN tms_agent g ON a.SellerId = g.UserId
				WHERE a.CustomerId = '".$this->customer."'
				GROUP BY a.CustomerId";
				
		$qry = $this ->query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$data = $rows;
		}
		return $data;
	
	}
	function getUserName($id)
	{
		$sql = "select concat(a.id,' - ',a.full_name) as User from tms_agent a where a.UserId = ".$id;
		
		$qry = $this ->execute($sql,__FILE__,__LINE__);
		while( $row = $this->fetcharray($qry)){
			$datas = $row['User'];
		}
		
		return $datas;
	}
	
	/** 
	 ** ambil link id berdasrkan category & subcategory 
	 ** spesific data 
	 ** return < @int >
	 **/
	 
	function _get_link_nilai($_CategoryId=0, $_SubCategoryId=0 )
	{
		$sql = " select b.LinkPointId FROM  coll_report_collmon a 
				 left join coll_transaction_collmon b on a.ReportId=b.ReportId
				 where a.CustomerId = '{$this -> customer}'
				 AND b.CategoryId = '{$_CategoryId}'
				 and b.SubCategoryId= '{$_SubCategoryId}'";
				 
		$qry = $this -> query($sql);
		return $qry -> result_get_value("LinkPointId");
	}
	
	/**
	 ** get nilai by edit
	 **/
	 
	function _get_data_nilai($_link_point = 0 )
	{
		$sql = " select b.CallTypeNum from coll_link_point a  left join coll_calltype_collmon b on a.CallTypeId=b.CallTypeId
				where a.LinkPointId='$_link_point' ";
		$qry = $this -> query($sql);
		return $qry -> result_get_value("CallTypeNum");
	}
	/** 
	 ** ambil remark extra by edit
	 ** return <array remark extra>
	 **/ 
 function getRemarkExtra()
 {
	$sql = "SELECT a.RemaksFields,a.RemarksText FROM coll_remarks_collmon a WHERE a.RemarksCustomerId = ".$this -> customer;
	$qry = $this ->query($sql);
	foreach($qry -> result_assoc() as $rows ){
		$data[$rows['RemaksFields']] = $rows['RemarksText'];
	}
	return $data;
 }
	 /**
	*** GET Value Per SubCategory
	**/
	function getScore($CategoryId=0, $SubCategoryId=0)
	{
		$sql = "select a.CallTypeNum, b.LinkPointId from coll_calltype_collmon a
				left join coll_link_point b on a.CallTypeId=b.CallTypeId
				where b.CategoryId='".$CategoryId."' 
				AND b.SubCategoryId='".$SubCategoryId."' 
				order by a.CallTypeNum ASC";
				
		$qry = $this ->query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$data[$rows['LinkPointId']] = $rows['CallTypeNum'];
		}
		return $data;
	}
	 
	 /** 
	 **  end of info customer 
	 **/
	 
	 /*
	  ** Cek banyak customer
	 */
	function cekcustomer()
	{
		$sql ="SELECT COUNT(a.CustomerId) AS bnyk FROM coll_report_collmon a WHERE a.CustomerId =".$this -> customer; 
		$qry = $this -> query($sql);
				
		return $qry -> result_get_value("bnyk");
	}
	
	/**
	 ** save data ke dalam database 
	 ** di follow up
	 ** return < @array >
	 **/ 		
	 
	function SaveCallMon()
	{
		if( $this -> havepost('action'))
		{
			$_ReportId = self::SaveScore();
			$_CustomerId = $this->escPost('customerid');
			if( $_ReportId )
			{
				if( self::_delete_link_point($_CustomerId) )
				{
					$sql = " SELECT a.CollCategoryId, b.SubCategoryId, concat(a.CollCategoryId,'_',b.SubCategoryId) as NamePost from coll_category_collmon a left join coll_subcategory_collmon b on a.CollCategoryId=b.CategoryId";
					$qry = $this -> query($sql);
					$i = 0;
					foreach( $qry -> result_assoc() as $rows )
					{
						$V_POST = 'point_'.$rows['NamePost'];
						
						if( $this -> havepost($V_POST) )
						{
							$V_POINT['CategoryId'] = $rows['CollCategoryId'];
							$V_POINT['SubCategoryId'] = $rows['SubCategoryId']; 
							$V_POINT['LinkPointId'] = $this -> escPost($V_POST);
							$V_POINT['CustomerId'] = $_CustomerId;
							$V_POINT['ReportId'] = $_ReportId;
							
							if( $this ->set_mysql_insert('coll_transaction_collmon',$V_POINT) )
							 $i++;
						}
					}
				}
				if( self::_delete_coll_remark($_CustomerId))
				{
					for ($j=1; $j<3; $j++)
					{
						$AreaExtra = "ExtraRemark_".$j;
						
						if( $this -> havepost($AreaExtra))
						{
							$RowExtra['RemarksCustomerId'] = $_CustomerId;
							$RowExtra['RemaksFields'] = $j;
							$RowExtra['RemarksText'] = $this -> escPost($AreaExtra);
							
							if( $this ->set_mysql_insert('coll_remarks_collmon',$RowExtra))
							$i++;
						}
						
					}
				}
				echo $i;
			}
		}
	}
	
	/**
	 **  delete nilai2 yang ada pada customer 
	 ** di follow up
	 ** return < @void >
	 **/ 	
	 
	function _delete_link_point($_CustomerId){
		
		$sql = " DELETE FROM coll_transaction_collmon WHERE CustomerId='$_CustomerId'";
		//echo $sql;
		if( $this -> execute($sql,__FILE__,__LINE__))
			return true;
		else
			return false;
	}
	
	/**
	 **  delete jika terjadi update 
	 ** di follow up
	 ** return < @void >
	 **/ 	
	 
	function _delete_customers($_CustomerId){

		$sql = " DELETE FROM coll_report_collmon WHERE CustomerId='$_CustomerId'";
		if( $this -> execute($sql,__FILE__,__LINE__)){return true;}
		else{return false;}
	}
	
	/**
	 **  delete jika terjadi update 
	 ** di follow up
	 ** return < @void >
	 **/ 	
	function _delete_coll_remark($_CustomerId)
	{
		$sql = " DELETE FROM coll_remarks_collmon WHERE RemarksCustomerId='$_CustomerId'";
		if( $this -> execute($sql,__FILE__,__LINE__))
			return true;
		else
			return false;
	}
	
	function SaveScore()
	{
		$_CustomerId = $this->escPost('customerid');
		
		$InsReport['CustomerId'] = $_CustomerId ;
		$InsReport['Total_Acc'] = $this -> escPost('call_acc_1');
		$InsReport['Score_Acc'] = $this -> escPost('call_acc_2');
		$InsReport['Total_Courtesy'] = $this -> escPost('courtesy_1');
		$InsReport['Score_Courtesy'] = $this->escPost('courtesy_2'); 
		$InsReport['Overall_Score'] = $this->escPost('overall'); 
		$InsReport['Rating'] = $this->escPost('rating'); 
		$InsReport['CreateDateTimes']= date('Y-m-d H:i:s');
		
		if( self::_delete_customers($_CustomerId) )
		{			
			if( $this ->set_mysql_insert('coll_report_collmon',$InsReport ) )
				return $this -> get_insert_id();
			else
				return false;
		}
		else
			return false;
	}
	/** akhir proses save **/
	/** 
	 ** Informasi Hasil Score 
	 ** render by customer Id
	 ** return < @void >
	 **/
		
	function getContentHeader()
	{
		$_Customer = self::getCustomerInfo();
?>
		<table cellpadding="3px" style="margin-bottom:4px;" width="99%" border="0">
			<tr>
				<td nowrap>TM </td>
				<td>:</td>
				<td><?php $this -> Form -> jpInput('tm','input',$_Customer['id'],null,1); ?></td>
				<td nowrap>Campaign Name </td>
				<td>:</td>
				<td><?php $this -> Form -> jpInput('cmp_name','input',$_Customer['CampaignName'],null,1);?></td>
				<td nowrap>Date Selling </td>
				<td>:</td>
				<td><?php $this -> Form -> jpInput('date_selling','input',$_Customer['SELLINGDATE'],null,1); ?></td>
			</tr>
			<tr>
					<td nowrap>QA Callmon</td>
					<td>:</td>
					<td><?php $this -> Form -> jpInput('qa_callmon','input',$this->getUserName( $this->getSession('UserId')),null,1); ?></td>
					<td nowrap>Product Code</td>
					<td>:</td>
					<td><?php $this -> Form -> jpInput('product_code','input',$_Customer['ProductCode'],null,1); ?></td>
					<td nowrap>Date Callmon </td>
					<td>:</td>
					<td><?php $this -> Form -> jpInput('date_callmon','input',date('d-m-Y H:i:s'),null,1); ?></td>
				</tr>
				<tr>
					<td nowrap>Customer </td>
					<td>:</td>
					<td><?php $this -> Form -> jpInput('customer','input',$_Customer['CustomerFirstName'],null,1); ?></td>
					<td nowrap>Product Name</td>
					<td>:</td>
					<td><?php $this -> Form -> jpInput('product_name','input',$_Customer['ProductName'],null,1); ?></td>
				</tr>
				
				<tr>
					<td colspan="9" style="border-bottom:1px dotted #000000;"></td>
				</tr>
				<tr>
					<td colspan="9">
						<!-- div content -->
							<?php self::divHeader(); ?>
					</td>
				</tr>
			</table>
			<?php
		}
		function divHeader()
		{ ?>
			<table style="font-size:10px;" border="0" width="70%" cellpadding="4px" cellspacing="1">
				<!-- header -->
				<tr>
					<td style="font-size:11px;color:red;" colspan="4" class="content-text header">&nbsp;</td>
					<td style="font-size:11px;color:red;" colspan="3" class="content-text header">&nbsp;</td>
				</tr>
				<!-- end header -->
				
				<tr>
					<td rowspan="1" style="font-size:11px;color:red;" class="content-text" nowrap>Total Accuracy</td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap>:</td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap><?php $this -> Form->jpInput('call_acc_1','overall_point','0',NULL,1);?></td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap><?php $this -> Form->jpInput('call_acc_2','overall_point','0',NULL,1);?></td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap>Overall Score</td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap>:</td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap><?php $this -> Form->jpInput('overall','overall_point','0',NULL,1);?></td>
				</tr>
	
				<tr>
					<td style="font-size:11px;color:red;" class="content-text" nowrap>Total Courtesy</td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap>:</td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap><?php $this -> Form->jpInput('courtesy_1','overall_point','0',NULL,1);?></td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap><?php $this -> Form->jpInput('courtesy_2','overall_point','0',NULL,1);?></td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap>Rating</td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap>:</td>
					<td style="font-size:11px;color:red;" class="content-text" nowrap><?php $this -> Form->jpInput('rating','overall_point','A',NULL,1);?></td>
					
				</tr>
				
				<!-- footer -->
				<tr>
					<td style="font-size:11px;color:red;" colspan="4" class="content-text header">&nbsp;</td>
					<td style="font-size:11px;color:red;" colspan="3" class="content-text header">&nbsp;</td>
				</tr>
				<!-- end footer -->
			</table>
		<?php
		}
		
	
	function getCallContent()
	{
		$Category  = self::getCategory();
		$RowCustomer = self::cekcustomer();
		echo "<ul style=\"border:0px solid #000000;padding-left:20px;\">";
		$n =1;
		foreach($Category as $key => $value){
			$CategoryId = $value['CollCategoryId'];
			
			$sql = " select * from coll_subcategory_collmon a where a.CategoryId='".$CategoryId."'";
			$qry = $this ->query($sql);
				
			echo "<li style=\"margin-left:0px;margin-top:12px;margin-bottom:10px;color:blue;background-color:#eeeeee; border-bottom:1px solid #dddddd;border-top:1px solid #dddfff;height:22px;padding-left:8px;padding-top:4px;border-left:1px solid #dddddd;\"><b>{$value['CollCategoryDesc']}</b></li>
					<table width=\"99%\" border=0 style=\"border-bottom:0px solid #000;\" cellpadding=\"7px;\" cellspacing=\"0px;\">";
			$_totals = 0;
			foreach($qry -> result_assoc() as $rows ){
			if ($RowCustomer > 0){
				$_link_point_id = self::_get_link_nilai($rows['CategoryId'], $rows['SubCategoryId']);
				$_link_data_nilai = self::_get_data_nilai($_link_point_id);
				$_totals = $_totals + $_link_data_nilai;
			}
			else
			{
				$_link_data_nilai = 0;
				$score =  self::getScore($rows['CategoryId'], $rows['SubCategoryId']);
				foreach ($score as $index => $val )
				{
					//khusus Cheerfull subcategory id 8 dibuat jawabannya selalu N
					if($rows['SubCategoryId']==8){
						if ($_link_data_nilai == $val)
						{
							$_link_data_nilai = $val;
							$_link_point_id = $index;
						}
					}
					else {
						if ($_link_data_nilai < $val)
						{
							$_link_data_nilai = $val;
							$_link_point_id = $index;
						}
					}
				}
				$_totals = $_totals + $_link_data_nilai;
			}
			//disable Cheerfull subcategory id 8
			if ($rows['SubCategoryId']==8){
				$disable = 1;
			}
			else{
				$disable = 0;
			}
			?>
				<tr class="select-rows">
					<td width="1%" align="left" style="border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;font-weight:normal;"><?php echo $n;?></td>

					<td width="96%" align="left"  style="border-bottom:1px solid #dddddd;font-weight:normal;" id = "<?php echo 'question_'.$rows['SubCategoryId']; ?>"><?php echo $rows[SubCategoryDesc]; ?>  </td>
					
					<td align="center" width="2%" style="border-bottom:1px solid #dddddd;font-weight:normal;" nowrap ><?php $this -> Form->jpCombo("point_".$CategoryId[CategoryId]."_".$rows[SubCategoryId],'select_point', $this->getCallType($rows['CategoryId'], $rows['SubCategoryId']) ,$_link_point_id,"onChange=getNilai(".$rows[CategoryId].",".$rows[SubCategoryId].",this.value)",$disable); ?></td>
					<td style="border-bottom:1px solid #dddddd;font-weight:normal;" ><?php $this -> Form->jpInput("nilai_".$CategoryId[CategoryId]."_".$rows[SubCategoryId],'box',$_link_data_nilai,NULL,1);?></td>
				</tr>
			<?php
			
			$n++;
			}
			?>
			<tr class="select-rows">
				<td width="1%" align="left" style="border-right:1px solid #dddddd;border-bottom:1px solid #dddddd;font-weight:normal;">&nbsp;</td>
				<td width="80%" align="right" style="border-bottom:1px solid #dddddd;font-weight:normal;">&nbsp;</td>
				<td width="12%" align="left" style="border-bottom:1px solid #dddddd;font-weight:normal;text-align:right;color:red;"><b>Score : </b></td>
				<td align="center" width="1%" style="border-bottom:1px solid #dddddd;font-weight:normal;color:red;" nowrap>
				<?php $this -> Form->jpInput('cust_totals_'.$CategoryId,'box',$_totals." ",NULL,1);?>
				</td>
			</tr>
					
			<?php
			echo "</table>";
			
		}
		$ExtraRemark = self::getRemarkExtra();
		echo "<li style=\"margin-left:0px;margin-top:12px;margin-bottom:10px;color:blue;background-color:#eeeeee; border-bottom:1px solid #dddddd;border-top:1px solid #dddfff;height:22px;padding-left:8px;padding-top:4px;border-left:1px solid #dddddd;\"><b>Remark</b></li>
				<table width=\"99%\" border=0 style=\"border-bottom:0px solid #000;\" cellpadding=\"7px;\" cellspacing=\"0px;\">"; ?>
				<tr class="select-rows">
				<td align="center" style="border-bottom:1px solid #dddddd;font-weight:normal;">Remark </td>
				<td align="center" style="border-bottom:1px solid #dddddd;font-weight:normal;">Noted </td>
				</tr>
				<tr class="select-rows">
				<td align="center" style="border-bottom:1px solid #dddddd;font-weight:normal;"><?php $this -> Form-> jpTextarea('ExtraRemark_1','textarea',$ExtraRemark[1],'') ?></td>
				<td align="center" style="border-bottom:1px solid #dddddd;font-weight:normal;"><?php $this -> Form-> jpTextarea('ExtraRemark_2','textarea',$ExtraRemark[2],'') ?></td>
				</tr>
		<?php
		echo "</table>";
		echo "</ul>";
	}
}
new CallmonQA();
?>