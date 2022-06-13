<?
	
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.exportdownloadcalltracking.txt.php");
	
	
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$cignasystem	= $_REQUEST['cignasystem'];
	$camptype		= $_REQUEST['camptype'];
	

	function getRowFile($page=0,$perpage=0){
		global $db;
		
		$textFile = new txtFile();
		$textFile -> file ='Call_Tracking'.$_REQUEST['start_date'].'To'.$_REQUEST['start_date'].'.txt';
	
			if($page < 1):
				$start = 0;
			else:
				$start = ($page) * $perpage;
			endif;

	 
		$sql = " SELECT
						cst.CustomerNumber AS prospect_id,
						pr.ProductCode AS Product_Id,
						cmp.CampaignNumber AS Campaign_ID,
						ag2.full_name AS SPV_Name,
						ag1.id AS Agent_Id,
						crs.CallReasonCode AS Call_Id,
						ch.CallHistoryCallDate AS Call_Date,
						ch.CallHistoryNotes AS Remark,
						if(crc.CallReasonCategoryCode = 'PIC','INT',crc.CallReasonCategoryCode) AS Call_Type,
						if(crs.CallReasonCode = '401','Interested',if(crs.CallReasonCode='402','Interested With Spouse',crs.CallReasonDesc)) AS Description
						FROM
						t_gn_callhistory AS ch
						LEFT JOIN t_gn_customer AS cst ON cst.CustomerId = ch.CustomerId
						LEFT JOIN t_gn_campaignproduct cmpr ON cmpr.CampaignId = cst.CampaignId
						LEFT JOIN t_gn_campaign AS cmp ON cmp.CampaignId = cmpr.CampaignId
						LEFT JOIN t_gn_product AS pr ON pr.ProductId = cmpr.ProductId AND cmpr.CampaignId = cmp.CampaignId
						LEFT JOIN t_lk_callreason AS crs ON crs.CallReasonId = ch.CallReasonId
						LEFT JOIN t_lk_callreasoncategory AS crc ON crc.CallReasonCategoryId = crs.CallReasonCategoryId
						LEFT JOIN tms_agent AS ag1 ON ag1.UserId = cst.SellerId
						LEFT JOIN tms_agent AS ag2 ON ag2.UserId = ag1.spv_id
						WHERE date( ch.CallHistoryCallDate) >='".$_REQUEST['start_date']."' AND date( ch.CallHistoryCallDate) <='".$_REQUEST['end_date']."'
						GROUP BY ch.CallHistoryCallDate
						ORDER BY ch.CallHistoryCallDate ";
						 
						 /*
							LEFT JOIN t_lk_cignasystem sys ON sys.CignaSystemId = cmp.CignaSystemId
							 WHERE date( ch.CallHistoryCallDate) >='".$_REQUEST['start_date']."' AND date( ch.CallHistoryCallDate) <='".$_REQUEST['end_date']."'
							 AND (sys.CignaSystemCode)like '%".$camptype."%'
						*/
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
	 
	 $query = $db->execute($sql,__FILE__,__LINE__);
		
		
		while($row = $db -> fetchrow($query) ){
			$datas .=$textFile->split($row-> prospect_id, "ctrack","prospect_id","|");
			$datas .=$textFile->split($row-> Product_Id, "ctrack","product_Id","|");
			$datas .=$textFile->split($row-> Campaign_ID, "ctrack","Campaign_ID","|");
			$datas .=$textFile->split($row-> SPV_Name, "ctrack","SPV_Name","|");
			$datas .=$textFile->split($row-> Agent_Id, "ctrack","Agent_Id","|");
			$datas .=$textFile->split($row-> Call_Id, "ctrack","Call_Id","|");
			$datas .=$textFile->split($row-> Call_Date, "ctrack","Call_Date","|");
			$datas .=$textFile->split($row-> Remark, "ctrack","Remark","|");
			$datas .=$textFile->split($row-> Call_Type, "ctrack","Call_Type","|");
			$datas .=$textFile->split($row-> Description, "ctrack","Description","")."\r\n";

		}
		
		$textFile -> txtWriteLabel($datas);	
		return true;	
	}
	
	
		$sql = " SELECT
						cst.CustomerNumber AS prospect_id,
						pr.ProductCode AS Product_Id,
						cmp.CampaignNumber AS Campaign_ID,
						ag2.full_name AS SPV_Name,
						ag1.id AS Agent_Id,
						crs.CallReasonCode AS Call_Id,
						ch.CallHistoryCallDate AS Call_Date,
						ch.CallHistoryNotes AS Remark,
						if(crc.CallReasonCategoryCode = 'PIC','INT',crc.CallReasonCategoryCode) AS Call_Type,
						if(crs.CallReasonCode = '401','Interested',if(crs.CallReasonCode='402','Interested With Spouse',crs.CallReasonDesc)) AS Description
						FROM
						t_gn_callhistory AS ch
						LEFT JOIN t_gn_customer AS cst ON cst.CustomerId = ch.CustomerId
						LEFT JOIN t_gn_campaignproduct cmpr ON cmpr.CampaignId = cst.CampaignId
						LEFT JOIN t_gn_campaign AS cmp ON cmp.CampaignId = cmpr.CampaignId
						LEFT JOIN t_gn_product AS pr ON pr.ProductId = cmpr.ProductId AND cmpr.CampaignId = cmp.CampaignId
						LEFT JOIN t_lk_callreason AS crs ON crs.CallReasonId = ch.CallReasonId
						LEFT JOIN t_lk_callreasoncategory AS crc ON crc.CallReasonCategoryId = crs.CallReasonCategoryId
						LEFT JOIN tms_agent AS ag1 ON ag1.UserId = cst.SellerId
						LEFT JOIN tms_agent AS ag2 ON ag2.UserId = ag1.spv_id
						WHERE date( ch.CallHistoryCallDate) >='".$_REQUEST['start_date']."' AND date( ch.CallHistoryCallDate) <='".$_REQUEST['end_date']."'
						GROUP BY ch.CallHistoryCallDate
						ORDER BY ch.CallHistoryCallDate ";
						
						
		$query 	   = $db->execute($sql,__FILE__,__LINE__);
		$perpages  = 4000;
		$totalRows = $db->numrows($query);
		$totalPage = ceil($totalRows/$perpages);
		$sizePage=0;
		for( $i=0;  $i<$totalPage; $i++){
			
			$datas = getRowFile($i,$perpages);
			if( $datas ) $sizePage++;
		}
		
		if( $sizePage>0) echo 1;
		else echo 0;
?>	

	


