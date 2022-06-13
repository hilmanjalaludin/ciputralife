<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.export.txt.php");
	
	$start_date = $_REQUEST['start_date'];
	$end_date  	= $_REQUEST['end_date'];
	$cignasystem		= $_REQUEST['cignasystem'];
	

	
	$textFile = new txtFile();
	$textFile -> file ='Report_Call_Tracking.txt';

	 
		$sql = " SELECT cst.CustomerNumber AS prospect_id,
						pr.ProductCode AS Product_Id,
						cmp.CampaignNumber AS Campaign_ID,
						ag2.full_name AS SPV_Name,
						ag1.id AS Agent_Id,
						crs.CallReasonCode AS Call_Id,
						ch.CallHistoryCallDate AS Call_Date,
						ch.CallHistoryNotes AS Remark,
						crc.CallReasonCategoryCode AS Call_Type,
						crs.CallReasonDesc AS Description
						FROM
						t_gn_callhistory AS ch
						LEFT JOIN t_gn_customer AS cst ON cst.CustomerId = ch.CustomerId
						LEFT JOIN t_gn_product AS pr ON pr.CampaignId = cst.CampaignId
						LEFT JOIN t_gn_campaign AS cmp ON cmp.CampaignId = cst.CampaignId
						LEFT JOIN t_lk_callreason AS crs ON crs.CallReasonId = ch.CallReasonId
						LEFT JOIN t_lk_callreasoncategory AS crc ON crc.CallReasonCategoryId = crs.CallReasonCategoryId
						LEFT JOIN tms_agent AS ag1 ON ag1.UserId = ch.CreatedById
						LEFT JOIN tms_agent AS ag2 ON ag2.UserId = ag1.spv_id ";
						// WHERE date( ca.CampaignStartDate) >='$start_date' AND date( ca.CampaignEndDate) <='$end_date'
						 //AND (cs.cignasystemcode) ='$cignasystem'";
						 
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
	 
	 $query = $db->execute($sql,__FILE__,__LINE__);
		
		
		while($row = $db -> fetchrow($query) ){
			$datas .=$textFile->split($row-> Prospect_Id, "prosp","Prospect_Id","|");
			$datas .=$textFile->split($row-> Product_Id, "prosp","Product_Id","|");
			$datas .=$textFile->split($row-> Campaign_ID, "prosp","Campaign_ID","|");
			$datas .=$textFile->split($row-> SPV_Name, "A","SPV_Name","|");
			$datas .=$textFile->split($row-> Agent_Id, "A","Agent_Id","|");
			$datas .=$textFile->split($row-> Call_Id, "A","Call_Id","|");
			$datas .=$textFile->split($row-> Call_Date, "A","Call_Date","|");
			$datas .=$textFile->split($row-> Remark, "A","Remark","|");
			$datas .=$textFile->split($row-> Call_Type, "A","Call_Type","|");
			$datas .=$textFile->split($row-> Description, "A","Description","")."\r\n";

		}
		
		$textFile -> txtWriteLabel($datas);	
		
?>	

	


