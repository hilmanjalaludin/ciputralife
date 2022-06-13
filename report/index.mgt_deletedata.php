<?php 
error_reporting(1);

include("../sisipan/sessions.php");
include("../fungsi/global.php");
include("../class/MYSQLConnect.php");
include("../class/class.application.php");
include("../class/lib.form.php");
include('../sisipan/parameters.php');
include("../class/class_export_excel.php");

class ExcelDeletion extends mysql {
	
	function ExcelDeletion(){
		$xlsName = "Customer_Deletion_".date('Ymd')."_".date('His').".xls";
		if( $xlsName )
		{
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Cache-Control: private");
			header("Pragma: no-cache");
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$xlsName");
		}
	}
		
	function index(){
			$this->excelHeader();
			$this->excelDetail();
			$this->excelFooter();
				
	}
	
	function excelFooter(){
		echo "</table>";
	}
	
	function excelHeader(){
	    echo "<table>";

	    echo "<tr>";
	    echo "<td>pros_prospect_Id</td>";
	    echo "<td>pros_campaign_Id</td>";
	    echo "<td>pros_name</td>";
	    echo "<td>pros_dob</td>";
	    echo "<td>pros_haddress1</td>";
	    echo "<td>pros_haddress2</td>";
	    echo "<td>pros_haddress3</td>";
	    echo "<td>pros_haddress4</td>";
	    echo "<td>pros_hcity</td>";
	    echo "<td>pros_hphone1</td>";
	    echo "<td>pros_hphone2</td>";
	    echo "<td>pros_mphone</td>";
	    echo "<td>pros_mphone1</td>";
	    echo "<td>pros_mphone2</td>";
	    echo "<td>pros_Product_Id_master</td>";
	    echo "<td>pros_call_Id</td>";
	    echo "<td>pros_Call_group</td>";
	    echo "<td>pros_Calldate</td>";
	    echo "<td>pros_agent_Id</td>";
	    echo "<td>pros_spv_Id</td>";
	    echo "<td>pros_remark1</td>";
	    echo "<td>pros_remark2</td>";
	    echo "<td>pros_remark3</td>";
	    echo "<td>pros_remark4</td>";
	    echo "<td>pros_remark5</td>";
	    echo "<td>pros_Accnumber</td>";
	    echo "<td>pros_cifnumber</td>";
	    echo "<td>pros_Refnumber</td>";
	    echo "<td>pros_camp_name</td>";
	    echo "<td>pros_Inititaldate</td>";
	    echo "<td>pros_uploaddate</td>";
	    echo "<td>pros_totalprosp</td>";
	    echo "<td>pros_policy_Id</td>";
	    echo "<td>pros_Product_Id</td>";
	    echo "<td>pros_input</td>";
	    echo "<td>pros_effdt</td>";
	    echo "<td>pros_premium</td>";
	    echo "<td>pros_nbi</td>";
	    echo "<td>acctnum</td>";
	    echo "<td>ccexpdate</td>";
	    echo "<td>posemail</td>";
	    echo "</tr>";
	}
	
	function excelDetail(){
		$campaingid = $_REQUEST['CampaingId'];
		//$sql = "call sp_deletion_toExcel(".$campaingid.")";
		$sql = "SELECT DISTINCT
	a.CustomerNumber AS 'pros_prospect_Id',
	d.CampaignNumber AS 'pros_campaign_Id',
	a.CustomerFirstName AS 'pros_name',
	DATE_FORMAT(a.CustomerDOB,  '%d-%m-%Y' ) AS 'pros_dob',
	r.PayerAddressLine1 AS 'pros_haddress1',
	'NULL' AS 'pros_haddress2',
	r.PayerAddressLine3 AS 'pros_haddress3',
	r.PayerAddressLine4 AS 'pros_haddress4',
	r.PayerCity AS 'pros_hcity',
	a.CustomerHomePhoneNum AS 'pros_hphone1',
	a.CustomerHomePhoneNum2 AS 'pros_hphone2',
	a.CustomerMobilePhoneNum AS 'pros_mphone',
	a.CustomerMobilePhoneNum2 AS 'pros_mphone1',
	a.CustomerWorkPhoneNum AS 'pros_mphone2',
	m.ProductCode AS 'pros_Product_Id_master',
	h.CallReasonCode AS 'pros_call_Id',
	i.CallReasonCategoryCode AS 'pros_Call_group',
	a.CustomerUpdatedTs AS 'pros_Calldate',
	n.id AS'pros_agent_Id',
	o.id AS 'pros_spv_Id',
	r.PayerAddressLine2 AS 'pros_remark1',
	(SELECT DISTINCT ss.CallHistoryNotes FROM t_gn_callhistory ss
	WHERE  a.CustomerId = ss.CustomerId
	ORDER BY ss.CallHistoryCallDate DESC
	LIMIT 1 ) AS 'pros_remark2',
	'NULL'AS 'pros_remark3',
	'NULL'AS 'pros_remark4',
	'NULL'AS 'pros_remark5',
	'NULL'AS 'pros_Accnumber',
	'NULL'AS 'pros_cifnumber',
	r.PayerAddressLine2 AS 'pros_Refnumber',
	d.CampaignName AS 'pros_camp_name',
	d.CampaignEndDate AS 'pros_Initialdate',
	d.CampaignStartDate AS 'pros_uploaddate',
	'null'AS 'pros_totalprosp',
	p.PolicyNumber AS 'pros_policy_Id',
	m.ProductCode AS 'pros_Product_Id',
	p.PolicyEffectiveDate AS 'pros_input',
	p.PolicyEffectiveDate AS 'pros_effdt',
	p.Premi AS 'pros_premium',
	IF(q.PayModeId= 2,12*p.Premi,p.Premi) AS 'pros_nbi',
	r.PayerCreditCardNum AS 'acctnum',
	r.PayerCreditCardExpDate AS 'ccexpdate',
	r.PayerEmail AS 'posemail'

	FROM t_gn_customer a
	LEFT JOIN t_gn_campaign d ON a.CampaignId = d.CampaignId
	LEFT JOIN t_gn_policyautogen f ON a.CustomerId = f.CustomerId
	LEFT JOIN t_gn_product g ON f.ProductId = g.ProductId
	LEFT JOIN t_lk_callreason h ON a.CallReasonId = h.CallReasonId
	LEFT JOIN t_lk_callreasoncategory i ON h.CallReasonCategoryId = i.CallReasonCategoryId
	LEFT JOIN tms_agent n ON a.SellerId = n.UserId
	LEFT JOIN tms_agent o ON n.spv_id = o.UserId
	LEFT JOIN t_gn_policy p ON f.PolicyNumber = p.PolicyNumber
	LEFT JOIN t_gn_productplan q ON p.ProductPlanId = q.ProductPlanId
	LEFT JOIN t_gn_product m ON q.ProductId = m.ProductId
	LEFT JOIN t_gn_payer r ON a.CustomerId = r.CustomerId
	WHERE d.CampaignId =".$campaingid;		
		$qry = $this->query($sql);
		foreach($qry-> result_assoc() as $rows )
		{
			echo "<tr>";
			echo "<td>".$rows["pros_prospect_Id"]."</td>";
			echo "<td>".$rows["pros_campaign_Id"]."</td>";
			echo "<td>".$rows["pros_name"]."</td>";
			echo "<td>".$rows["pros_dob"]."</td>";
			echo "<td>".$rows["pros_haddress1"]."</td>";
			echo "<td>".$rows["pros_haddress2"]."</td>";
			echo "<td>".$rows["pros_haddress3"]."</td>";
			echo "<td>".$rows["pros_haddress4"]."</td>";
			echo "<td>".$rows["pros_hcity"]."</td>";
			echo "<td>".$rows["pros_hphone1"]."</td>";
			echo "<td>".$rows["pros_hphone2"]."</td>";
			echo "<td>".$rows["pros_mphone"]."</td>";
			echo "<td>".$rows["pros_mphone1"]."</td>";
			echo "<td>".$rows["pros_mphone2"]."</td>";
			echo "<td>".$rows["pros_Product_Id_master"]."</td>";
			echo "<td>".$rows["pros_call_Id"]."</td>";
			echo "<td>".$rows["pros_Call_group"]."</td>";
			echo "<td>".$rows["pros_Calldate"]."</td>";
			echo "<td>".$rows["pros_agent_Id"]."</td>";
			echo "<td>".$rows["pros_spv_Id"]."</td>";
			echo "<td>".$rows["pros_remark1"]."</td>";
			echo "<td>".$rows["pros_remark2"]."</td>";
			echo "<td>".$rows["pros_remark3"]."</td>";
			echo "<td>".$rows["pros_remark4"]."</td>";
			echo "<td>".$rows["pros_remark5"]."</td>";
			echo "<td>".$rows["pros_Accnumber"]."</td>";
			echo "<td>".$rows["pros_cifnumber"]."</td>";
			echo "<td>".$rows["pros_Refnumber"]."</td>";
			echo "<td>".$rows["pros_camp_name"]."</td>";
			echo "<td>".$rows["pros_Initialdate"]."</td>";
			echo "<td>".$rows["pros_uploaddate"]."</td>";
			echo "<td>".$rows["pros_totalprosp"]."</td>";
			echo "<td>".$rows["pros_policy_id"]."</td>";
			echo "<td>".$rows["pros_Product_Id"]."</td>";
			echo "<td>".$rows["pros_input"]."</td>";
			echo "<td>".$rows["pros_effdt"]."</td>";
			echo "<td>".$rows["pros_premium"]."</td>";
			echo "<td>".$rows["pros_nbi"]."</td>";
			echo "<td>".$rows["acctnum"]."</td>";
			echo "<td>".$rows["ccexpdate"]."</td>";
			echo "<td>".$rows["posemail"]."</td>";
			echo "</tr>";
		}
	}

}

$xlsDeletion = new ExcelDeletion();
$xlsDeletion->index();
?>
