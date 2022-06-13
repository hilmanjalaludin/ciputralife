<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../plugin/class.exportplevel.txt.php");
	
	$start_date  = $_REQUEST['start_date'];
	$end_date  	 = $_REQUEST['end_date'];
	$cignasystem = $_REQUEST['cignasystem'];
	$stgl		 = explode("-", $start_date);
	$tgl_start	 =$stgl[2]."".$stgl[1]."".$stgl[0];
	$ntgl		 = explode("-", $end_date);
	$tgl_end	 =$ntgl[2]."".$ntgl[1]."".$ntgl[0];
	
	set_time_limit(750000);
	ini_set('memory_limit', '2048M');
	
	function Totalcmp(){
		global $db,$start_date,$end_date;
		
		$sql =" select count(a.CampaignId) as cnt, ca.CampaignNumber as CmpId,DATE_FORMAT((ca.CampaignStartDate),'%Y-%m-%d %H:%i') AS cmpstartdate, ca.CampaignName AS cmpname,
				DATE_FORMAT((ca.CampaignEndDate),'%Y-%m-%d %H:%i') as cmpenddate, DATE_FORMAT((ca.CampaignExtendedDate),'%Y-%m-%d %H:%i') as cmpextdate, rur.ReUploadReason as reuploadreason, ca.CampaignDataFileName as cmpfilename,
				DATE_FORMAT(ca.CampaignStartDate, '%m') AS Month_Of_Campaign,DATE_FORMAT(ca.CampaignStartDate, '%Y') AS Year_Of_Campaign,ct.CampaignTypeCode AS Camp_Type1
				from t_gn_customer a 
				left join t_gn_campaign ca on ca.CampaignId=a.CampaignId
				left join t_gn_assignment asg on asg.customerid = a.customerid
				left join t_lk_reuploadreason rur on rur.ReUploadReasonId = ca.ReUploadReasonId
				LEFT JOIN t_lk_campaigntype AS ct ON ca.CampaignTypeId = ct.CampaignTypeId
				where asg.AssignBlock = 0 
				AND ca.CampaignStatusFlag = 1 
				GROUP BY ca.CampaignNumber ";
		/*echo "<pre>";
		echo $sql;
		echo "</pre>";*/
		$qry = mysql_query($sql);		
		while( $row = mysql_fetch_assoc($qry)){
			$jumlah[$row['CmpId']]['jml'] = $row['cnt'];
			$jumlah[$row['CmpId']]['cmpsd'] = $row['cmpstartdate'];
			$jumlah[$row['CmpId']]['cmped'] = $row['cmpenddate'];
			$jumlah[$row['CmpId']]['cmpexd'] = $row['cmpextdate'];
			$jumlah[$row['CmpId']]['cmpnm'] = $row['cmpname'];
			$jumlah[$row['CmpId']]['cmpfln'] = $row['cmpfilename'];
			$jumlah[$row['CmpId']]['cmptp'] = $row['Camp_Type1'];
			$jumlah[$row['CmpId']]['cmpno'] = $row['CmpId'];
			$jumlah[$row['CmpId']]['cmpyear'] = $row['Year_Of_Campaign'];
			$jumlah[$row['CmpId']]['cmpmonth'] = $row['Month_Of_Campaign'];
			$jumlah[$row['CmpId']]['rur'] = $row['reuploadreason'];
		
		}
		return $jumlah;		
	}
	
	function note(){
	global $db;
		$sql = "select clh.CallHistoryNotes AS notes, cst.CustomerNumber AS cstnum 
			from t_gn_callhistory clh
			left join t_gn_customer cst on cst.CustomerId = clh.CustomerId
			left join t_gn_assignment asg on asg.CustomerId = cst.CustomerId 
			left join t_gn_campaign cmp on cmp.CampaignId = cst.CampaignId
			where cst.CustomerUpdatedTs = clh.CallHistoryCallDate
			AND cst.CallReasonId = clh.CallReasonId
			AND cmp.CampaignStatusFlag = 1
			AND asg.AssignBlock = 0
			GROUP BY cst.CustomerNumber";
		$qry = mysql_query($sql);
		while( $row = mysql_fetch_assoc($qry)){
			$remark[$row['cstnum']] = $row['notes'];
		
		}
		return $remark;
	
	}
	
	$total_cmp = 0;
	$total_cmp = Totalcmp();
	$description = note();
	
	
	
	
	function getRowFile($page=0,$perpage=0){
		global $db,$start_date,$end_date,$total_cmp,$tgl_start,$tgl_end;
			
			
			$textFile = new txtFile();
			//var_dump($textFile);
			//$textFile ->file = ($page+1).'_Report_DownLoad_Prospect_level_'.$_REQUEST['start_date'].'To'.$_REQUEST['end_date'];
			$textFile ->file = ($page+1).'_Prospect_level_'.$tgl_start.'To'.$tgl_end;
				
			if($page < 1):
				$start = 0;
			else:
				$start = ($page) * $perpage;
			endif;
			
			
				

				$sql = " SELECT DISTINCT c.CustomerNumber AS prospect_id,
							ca.CampaignNumber AS campaign_id,
							pa.PayerCreditCardNum AS acc_number,
							'' AS cif_number,
							pa.PayerCreditCardExpDate AS ref_number,
							c.CustomerFirstName AS `name`,
							c.CustomerDOB AS dob,
							pr.ProductCode AS product_id,
							pr.ProductName AS product_name,
							cr.CallReasonCode AS call_id,
							crc.CallReasonCategoryCode AS call_type,
							ta.id AS seller_id,
							c.CustomerUploadedTs AS Upload_date,
							spv.full_name AS SPV_Id,
							ta.id AS Agent_Id,
							DATE_FORMAT((p.PolicySalesDate),'%Y-%m-%d %H:%i') AS call_date,
							g.GenderShortCode AS sex,
							'' AS Remarks_2,
							'' AS Remarks_3,
							p.PolicyNumber AS Policy_No,
							'' AS Policy_ref,
							p.PolicyEffectiveDate AS policy_date,
							cg.CampaignGroupName AS Campaign_Cigna,
							cr.CallReasonDesc AS Description,
							pp.ProductPlanPremium AS Premium,
							pm.PayModeCode AS Payment_Frequent,
							'B41' AS Sponsor_Id,
							'N' AS Camp_Type,
							1 AS Build_Type,
							cs.CignaSystemCode
							FROM
							t_gn_customer AS c
							LEFT JOIN t_gn_assignment AS asg ON asg.CustomerId = c.CustomerId
							LEFT JOIN t_gn_campaign AS ca ON c.CampaignId = ca.CampaignId
							LEFT JOIN t_lk_cignasystem AS cs ON ca.CignaSystemId = cs.CignaSystemId
							LEFT JOIN t_gn_insured AS i ON c.CustomerId = i.CustomerId AND i.PremiumGroupId = 2
							LEFT JOIN t_gn_policy AS p ON i.PolicyId = p.PolicyId
							LEFT JOIN t_gn_productplan AS pp ON p.ProductPlanId = pp.ProductPlanId
							LEFT JOIN t_gn_campaignproduct AS cpr ON cpr.CampaignId = ca.CampaignId 
							LEFT JOIN t_gn_product AS pr ON cpr.ProductId = pr.ProductId 
							LEFT JOIN t_gn_campaigngroup AS cg ON pr.CampaignGroupId = cg.CampaignGroupId 
							LEFT JOIN t_gn_payer AS pa ON c.CustomerId = pa.CustomerId
							LEFT JOIN t_lk_salutation AS pas ON pa.SalutationId = pas.SalutationId
							LEFT JOIN t_lk_gender AS pag ON pa.GenderId = pag.GenderId
							LEFT JOIN t_lk_province AS pap ON pa.ProvinceId = pap.ProvinceId
							LEFT JOIN t_lk_paymenttype AS pt ON pa.PaymentTypeId = pt.PaymentTypeId
							LEFT JOIN t_lk_creditcardtype AS cct ON pa.CreditCardTypeId = cct.CreditCardTypeId
							LEFT JOIN t_lk_paymode AS pm ON pp.PayModeId = pm.PayModeId
							LEFT JOIN tms_agent AS ta ON c.SellerId = ta.UserId
							LEFT JOIN t_lk_premiumgroup AS pg ON i.PremiumGroupId = pg.PremiumGroupId
							LEFT JOIN t_lk_salutation AS s ON i.SalutationId = s.SalutationId
							LEFT JOIN t_lk_gender AS g ON i.GenderId = g.GenderId
							LEFT JOIN t_lk_relationshiptype AS rt ON i.RelationshipTypeId = rt.RelationshipTypeId
							LEFT JOIN t_lk_callreason AS cr ON c.CallReasonId = cr.CallReasonId
							LEFT JOIN t_lk_campaigntype AS ct ON ca.CampaignTypeId = ct.CampaignTypeId
							LEFT JOIN t_lk_callreasoncategory AS crc ON cr.CallReasonCategoryId = crc.CallReasonCategoryId
							LEFT JOIN tms_agent AS spv ON spv.UserId = ta.spv_id
							WHERE ca.CampaignStatusFlag = 1 AND asg.AssignBlock = 0
							GROUP BY c.CustomerNumber LIMIT $start, $perpage";
				
				//echo $sql;
				
				$query 	   = $db->execute($sql,__FILE__,__LINE__);
				
				while($row = $db -> fetchrow($query) ){
					$datas .=$textFile->split($row->prospect_id,"prosp","prospect_id","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpnm'],"prosp","campaign_name","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpno'],"prosp","campaign_id","|");
					$datas .=$textFile->split($row->acc_number,"prosp","acc_Number","|");
					$datas .=$textFile->split($row->cif_number,"prosp","cif_number","|");
					$datas .=$textFile->split($row->ref_number,"prosp","ref_number","|");
					$datas .=$textFile->split($row->name,"prosp","name","|");
					$datas .=$textFile->split($row->dob,"prosp","dob","|");
					$datas .=$textFile->split($row->product_id,"prosp","product_id","|");
					$datas .=$textFile->split($row->product_name,"prosp","product_name","|");
					$datas .=$textFile->split($row->call_id,"prosp","call_id","|");
					$datas .=$textFile->split($row->call_type,"prosp","call_type","|");
					$datas .=$textFile->split($row->seller_id,"prosp","seller_id","|");
					$datas .=$textFile->split($row->call_date,"prosp","call_date","|");
					$datas .=$textFile->split($row->Upload_date,"prosp","Upload_date","|");
					$datas .=$textFile->split($row->SPV_Id,"prosp","SPV_Id","|");
					$datas .=$textFile->split($row->Agent_Id,"prosp","Agent_Id","|");
					$datas .=$textFile->split($desription[$row->prospect_id],"prosp","Remarks","|");
					$datas .=$textFile->split($row->sex,"prosp","sex","|");
					$datas .=$textFile->split($desription[$row->prospect_id],"prosp","Remarks_1","|");
					$datas .=$textFile->split($row->Remarks_2,"prosp","Remarks_2","|");
					$datas .=$textFile->split($row->Remarks_3,"prosp","Remarks_3","|");
					$datas .=$textFile->split($row->Policy_No,"prosp","Policy_No","|");
					$datas .=$textFile->split($row->Policy_ref,"prosp","Policy_ref","|");
					$datas .=$textFile->split($row->policy_date,"prosp","policy_date","|");
					$datas .=$textFile->split($row->Campaign_Cigna,"prosp","Campaign_Cigna","|");
					$datas .=$textFile->split($row->Description,"prosp","Description","|");
					$datas .=$textFile->split($row->Premium,"prosp","Premium","|");
					$datas .=$textFile->split($row->Payment_Frequent,"prosp","Payment_Frequent","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmptp'],"prosp","Camp_Type1","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpsd'],"prosp","Campaign_Initial_Date","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpsd'],"prosp","Campaign_Start_Date","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmped'],"prosp","Campaign_End_Date","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpexd'],"prosp","Extend_Date","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['jml'],"prosp","Total_Prospect","|");
					$datas .=$textFile->split($row->Sponsor_Id,"prosp","Sponsor_Id","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpmonth'],"prosp","Month_Of_Campaign","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpyear'],"prosp","year_Of_campaign","|");
					$datas .=$textFile->split($row->Camp_Type,"prosp","Camp_Type","|");
					$datas .=$textFile->split($row->Build_Type,"prosp","Build_Type","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpfln'],"prosp","Source_Campaign_ID","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['rur'],"prosp","Reupload_Reason","|");
					$datas .=$textFile->split($total_cmp[$row->campaign_id]['cmpfln'],"prosp","Source_Campaign_ID","")."\r\n";
				}
				
			$textFile -> txtWriteLabel($datas);	
			//var_dump($textFile);
			return true;
		
		}
		
		
		
	
		$sql = " SELECT DISTINCT c.CustomerNumber AS prospect_id,
				ca.CampaignNumber AS campaign_id,
				pa.PayerCreditCardNum AS acc_number,
				'' AS cif_number,
				pa.PayerCreditCardExpDate AS ref_number,
							c.CustomerFirstName AS `name`,
							c.CustomerDOB AS dob,
							pr.ProductCode AS product_id,
							pr.ProductName AS product_name,
							cr.CallReasonCode AS call_id,
							crc.CallReasonCategoryCode AS call_type,
							ta.id AS seller_id,
							c.CustomerUploadedTs AS Upload_date,
							spv.id AS SPV_Id,
							ta.id AS Agent_Id,
							DATE_FORMAT((c.CustomerUpdatedTs),'%Y-%m-%d %H:%i') AS call_date,
							g.GenderShortCode AS sex,
							'' AS Remarks_2,
							'' AS Remarks_3,
							p.PolicyNumber AS Policy_No,
							'' AS Policy_ref,
							p.PolicyEffectiveDate AS policy_date,
							cg.CampaignGroupName AS Campaign_Cigna,
							cr.CallReasonDesc AS Description,
							pp.ProductPlanPremium AS Premium,
							pm.PayModeCode AS Payment_Frequent,
							'N' AS Camp_Type,
							1 AS Build_Type,
							cs.CignaSystemCode
							FROM
							t_gn_customer AS c
							LEFT JOIN t_gn_assignment AS asg ON asg.CustomerId = c.CustomerId
							LEFT JOIN t_gn_campaign AS ca ON c.CampaignId = ca.CampaignId
							LEFT JOIN t_lk_cignasystem AS cs ON ca.CignaSystemId = cs.CignaSystemId
							LEFT JOIN t_gn_insured AS i ON c.CustomerId = i.CustomerId AND i.PremiumGroupId = 2
							LEFT JOIN t_gn_policy AS p ON i.PolicyId = p.PolicyId
							LEFT JOIN t_gn_productplan AS pp ON p.ProductPlanId = pp.ProductPlanId
							LEFT JOIN t_gn_campaignproduct AS cpr ON cpr.CampaignId = ca.CampaignId 
							LEFT JOIN t_gn_product AS pr ON cpr.ProductId = pr.ProductId 
							LEFT JOIN t_gn_campaigngroup AS cg ON pr.CampaignGroupId = cg.CampaignGroupId 
							LEFT JOIN t_gn_payer AS pa ON c.CustomerId = pa.CustomerId
							LEFT JOIN t_lk_salutation AS pas ON pa.SalutationId = pas.SalutationId
							LEFT JOIN t_lk_gender AS pag ON pa.GenderId = pag.GenderId
							LEFT JOIN t_lk_province AS pap ON pa.ProvinceId = pap.ProvinceId
							LEFT JOIN t_lk_paymenttype AS pt ON pa.PaymentTypeId = pt.PaymentTypeId
							LEFT JOIN t_lk_creditcardtype AS cct ON pa.CreditCardTypeId = cct.CreditCardTypeId
							LEFT JOIN t_lk_paymode AS pm ON pp.PayModeId = pm.PayModeId
							LEFT JOIN tms_agent AS ta ON c.SellerId = ta.UserId
							LEFT JOIN t_lk_premiumgroup AS pg ON i.PremiumGroupId = pg.PremiumGroupId
							LEFT JOIN t_lk_salutation AS s ON i.SalutationId = s.SalutationId
							LEFT JOIN t_lk_gender AS g ON i.GenderId = g.GenderId
							LEFT JOIN t_lk_relationshiptype AS rt ON i.RelationshipTypeId = rt.RelationshipTypeId
							LEFT JOIN t_lk_callreason AS cr ON c.CallReasonId = cr.CallReasonId
							LEFT JOIN t_lk_campaigntype AS ct ON ca.CampaignTypeId = ct.CampaignTypeId
							LEFT JOIN t_lk_callreasoncategory AS crc ON cr.CallReasonCategoryId = crc.CallReasonCategoryId
							LEFT JOIN tms_agent AS spv ON spv.UserId = ta.spv_id
							WHERE ca.CampaignStatusFlag = 1 AND asg.AssignBlock = 0
							GROUP BY c.CustomerNumber";
							
							
							
		$query 	   = $db->execute($sql,__FILE__,__LINE__);
		$perpages  = 3000;
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