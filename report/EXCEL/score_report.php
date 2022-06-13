<?php
class score_report extends IndexExcel
{
	private $IdScore= Array();
	function score_report()
	{
		$xlsName = "full_report_".date('Ymd')."_".date('His').".xls";
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
		
		$this->start_date_mon = $this -> formatDateEng($_REQUEST['start_callmon']);
		$this->end_date_mon = $this -> formatDateEng($_REQUEST['end_callmon']);
		
		$this->start_date_sale = $this -> formatDateEng($_REQUEST['start_sale']);
		$this->end_date_sale = $this -> formatDateEng($_REQUEST['end_sale']);
		ini_set("memory_limit","1024M");
	}
	
	function write_footer()
	{
		$this -> _Excel_Footer();
		
	}
	
	private function getHeaderScore(){
		$sql = "SELECT a.CollCategoryName, b.SubCategoryDesc, a.CollCategoryId, b.SubCategoryId
				FROM coll_category_collmon a 
				LEFT JOIN coll_subcategory_collmon b ON a.CollCategoryId=b.CategoryId
				ORDER BY a.CollCategoryId, b.SubCategoryId";
		$qry = $this ->query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			//$data['category'][]= $rows['CollCategoryName'];
			//$data['subcategory'][]= $rows['SubCategoryDesc'];
			$category= self::ChangeToUS($rows['CollCategoryName']);
			$subcategory= self::ChangeToUS($rows['SubCategoryDesc']);
			$data[$category][$subcategory]['score']= "SCORE";
			$data[$category][$subcategory]['time']= "MINUTE";
			$this->IdScore[$rows['CollCategoryId']][$rows['SubCategoryId']]=$rows['SubCategoryDesc'];
		}
		return $data;
	}
	function ChangeToUS($str){
		return $data=str_replace(" ","_",$str);
	}
	function ChangeToSpace($str)
	{
		return $data=str_replace("_"," ",$str);
	}
	
	private function write_header_closed()
	{ 
		$this -> _Excel_Header();
		echo "Call Monitoring (Score) Report<br />";
		echo "Call Monitoring Date : ".$_REQUEST['start_callmon']." - ".$_REQUEST['end_callmon']."<br />";
		echo "Selling Date : ".$_REQUEST['start_sale']." - ".$_REQUEST['end_sale']."<br />";
		echo "Report Date : ".date("d/m/Y")."<br />";
		
		$header=self::getHeaderScore();
		
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">No</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">PROSPECT ID</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">PROSPECT</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">POLICY</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">CLOSING DATE</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">PAYER NAME</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">INSURED NAME</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">INSURED DOB</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">PREMIUM</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">APE</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">PRODUCT</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">CAMPAIGN</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">CASE STATUS</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">DB STATUS</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">AGENT</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">TM ID</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">SPV</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">AM</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">APPROVE / CANCEL QA DATE</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">QC</td>
					<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">NO TELPON</td>";
					
		foreach($header as $category=>$ArrValue){
				$colspan = count($header[$category])*2;
				echo "<td colspan=\"".$colspan."\"  class=\"xl602508\" nowrap align=\"center\">".self::ChangeToSpace($category)."</td>";	
		}
			echo "<td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">Remarks</td>
				  <td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">Total Score</td>
				  <td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">Rating</td>
				  <td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">HASIL DC</td>
				  <td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">DC By DATE</td>
				  <td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">DC OLEH</td>
				  <td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">NO. CC & EXP. CARD</td>
				  <td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">NOTE</td>
				  <td rowspan=\"3\" class=\"xl602508\" nowrap align=\"center\">Remark 1</td>
				</tr>";
		echo "<tr>";
		foreach($header as $category=>$ArrValue){
			foreach($ArrValue as $subcategory=>$arrval){
				echo "<td colspan=\"2\"  class=\"xl602508\" nowrap align=\"center\">".self::ChangeToSpace($subcategory)."</td>";
			}
		}
		echo "</tr>";
		echo "<tr>";
		foreach($header  as $category=>$ArrValue){
			foreach($ArrValue as $subcategory=>$arrval){
				foreach($arrval as $k=>$val){
					echo "<td class=\"xl602508\" nowrap align=\"center\">".$val."</td>";
				}
			}
		}
		echo "</tr>";				
	}
	

	/* main content EXCEL **/
	function show_content_excel()
	{
		mysql::__construct();
		self::write_header_closed();
		self::write_content();
		self::write_footer();
	}
	
	/*** Navigasi Penyaring **/
	function geFilter()
	{
		$filter = "";
		if ($this->start_date_mon!="" and $this->end_date_mon !="")
		{
			$filter .= "AND a.CustomerRejectedDate  >= '".$this->start_date_mon." 00:00:00'
					 AND a.CustomerRejectedDate <= '".$this->end_date_mon." 23:00:00'";
		}
		if ($this->start_date_sale!="" and $this->end_date_sale !="")
		{
			$filter .= "AND e.PolicySalesDate  >= '".$this->start_date_sale." 00:00:00'
					 AND e.PolicySalesDate <= '".$this->end_date_sale." 23:00:00'";
		}
		if (($this->start_date_mon=="" or $this->end_date_mon =="") and ($this->start_date_sale=="" or $this->end_date_sale ==""))
		{
			$filter .= "AND a.CustomerRejectedDate >= ''
					AND a.CustomerRejectedDate <= ''";
			$filter .= "AND DATE (e.PolicySalesDate)  >= ''
					AND DATE (e.PolicySalesDate) <= '' ";
		}
		return $filter;
		
	}
	
	function getInfoCustomer(){
		$sql="	SELECT a.CustomerId,c.InsuredId,c.PremiumGroupId,e.PolicyNumber,DATE_FORMAT(e.PolicySalesDate, '%d-%m-%Y') AS EFFDate ,a.CustomerNumber, c.InsuredFirstName, 
				DATE_FORMAT(c.InsuredDOB, '%d-%m-%Y') AS InsuredDOB, d.Gender, o.PremiumGroupDesc,
				e.Premi,f.CampaignName, h.ProductName,'untukprospect', 'exportdate','untukstatus',
				CONCAT(i.id,'-',i.full_name)AS TM,
				i.init_name as TM_ID,'UntukEffDateComplete', j.full_name AS SPV, 
				k.full_name AS mgr,
				if(n.handling_type=5,n.full_name,null) as QC,
				#IF(l.PayerHomePhoneNum IS NULL,
				#	IF(l.PayerMobilePhoneNum IS NULL,l.PayerOfficePhoneNum,
				#		l.PayerMobilePhoneNum),
				#l.PayerHomePhoneNum) AS PhoneNum,
				Test.CallNumber AS PhoneNum,
				m.BankName, 
				#CONCAT(cc.CreditCardNo,' (', cc.ExpireDate,')') AS CC,
				b.Overall_Score, b.Rating,
				IFNULL(q.AproveName,'NEW') as aprove_score,
				r.full_name as qa_score,
				s.AproveName as aprove_dc,
				if(t.handling_type=10,t.full_name,null) as qa_dc,
				l.PayerFirstName,
				IFNULL(qq.AproveName,'NEW') db_status,
				a.CustomerFirstName,
				e.Premi* IF(g.PayModeId=2,12,1) APE,
				a.Remark_1
				FROM t_gn_customer a
				#left join t_gn_customer_cc cc on cc.CustomerId=a.CustomerId
				LEFT JOIN coll_report_collmon b ON a.CustomerId = b.CustomerId
				LEFT JOIN t_gn_insured c ON a.CustomerId=c.CustomerId
				LEFT JOIN t_lk_gender d ON c.GenderId = d.GenderId
				LEFT JOIN t_gn_policy e ON c.PolicyId = e.PolicyId
				LEFT JOIN t_gn_campaign f ON a.CampaignId =f.CampaignId
				LEFT JOIN t_gn_productplan g ON e.ProductPlanId = g.ProductPlanId
				LEFT JOIN t_gn_product h ON g.ProductId = h.ProductId
				LEFT JOIN tms_agent i ON a.SellerId=i.UserId
				LEFT JOIN tms_agent j ON i.spv_id= j.UserId
				LEFT JOIN tms_agent k ON j.mgr_id = k.UserId
				LEFT JOIN tms_agent n ON a.QueueId = n.UserId
				LEFT JOIN t_gn_payer l ON a.CustomerId = l.CustomerId 
				LEFT JOIN t_lk_bank m ON l.PayersBankId = m.BankId
				LEFT JOIN t_lk_premiumgroup o ON c.PremiumGroupId = o.PremiumGroupId
				LEFT JOIN t_gn_approval_score_qa p ON a.CustomerId = p.CustomerId
				LEFT JOIN t_lk_aprove_status q ON c.QCStatus = q.ApproveId
				LEFT JOIN t_lk_aprove_status qq ON a.CallReasonQue = qq.ApproveId
				LEFT JOIN tms_agent r ON p.approval_qa_id = r.UserId
				LEFT JOIN t_lk_aprove_status s ON p.approval_id_update = s.ApproveId
				LEFT JOIN tms_agent t ON p.approval_qa_id = t.UserId
				LEFT JOIN ( SELECT
					MAX(a.CallHistoryId) AS CallID, a.CallNumber, a.CustomerId
				FROM t_gn_callhistory a
				WHERE 1=1 #AND a.CallHistoryCallDate BETWEEN '".$this->start_date_mon." 06:00:00' and '".$this->end_date_mon." 23:00:00'
				AND a.CallNumber != ''
				GROUP BY a.CustomerId ) Test on a.CustomerId = Test.CustomerId
				WHERE true 
				/* b.ReportId IS NOT NULL*/ 
				 ".self::geFilter(); //-- AND c.PremiumGroupId = 2
			// echo "<pre>".$sql."</pre>";
			// var_dump($sql);
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$data[$rows['CustomerId']][$rows['InsuredId']]['PolicyNumber']= $rows['PolicyNumber'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['EFFDate']= $rows['EFFDate'];
				//$data[$rows['CustomerId']][$rows['InsuredId']]['CustomerNumber']= $rows['CustomerNumber'];
				//$data[$rows['CustomerId']][$rows['InsuredId']]['PremiumGroup']= $rows['PremiumGroupDesc'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['InsuredFirstName']= $rows['InsuredFirstName'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['InsuredDOB']= $rows['InsuredDOB'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['Gender']= $rows['Gender'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['Premi']= $rows['Premi'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['CampaignName']= $rows['CampaignName'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['ProductName']= $rows['ProductName'];
				//$data[$rows['CustomerId']][$rows['InsuredId']]['untukprospect']= $rows['untukprospect'];
				//$data[$rows['CustomerId']][$rows['InsuredId']]['exportdate']= $rows['exportdate'];
				//$data[$rows['CustomerId']][$rows['InsuredId']]['untukstatus']= $rows['untukstatus'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['TM']= $rows['TM'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['TM_ID']= $rows['TM_ID'];
				//$data[$rows['CustomerId']][$rows['InsuredId']]['UntukEffDateComplete']= $rows['UntukEffDateComplete'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['SPV']= $rows['SPV'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['mgr']= $rows['mgr'];
				// $data[$rows['CustomerId']][$rows['InsuredId']]['QC']= ( $rows['qa_score']?$rows['qa_score']:$rows['QC'] );
				$data[$rows['CustomerId']][$rows['InsuredId']]['PhoneNum']= $rows['PhoneNum'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['BankName']= $rows['BankName'];
				// $data[$rows['CustomerId']][$rows['InsuredId']]['CC']= $rows['CC'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['Overall_Score']= $rows['Overall_Score'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['Rating']= $rows['Rating'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['hasilanalis']= $rows['aprove_score'];
				// $data[$rows['CustomerId']][$rows['InsuredId']]['hasilDC']= $rows['aprove_dc'];
				// $data[$rows['CustomerId']][$rows['InsuredId']]['DC_Oleh']= $rows['qa_dc'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['PayerFirstName']= $rows['PayerFirstName'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['db_status']= $rows['db_status'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['CustomerFirstName']= $rows['CustomerFirstName'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['CustomerId']= $rows['CustomerId'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['APE']= $rows['APE'];
				$data[$rows['CustomerId']][$rows['InsuredId']]['Remark_1']= $rows['Remark_1'];
			}
			
			$sql2 = "select a.CustomerId, c.InsuredId, p.approval_qa_id as Apprv_QAID,
						(select max(QC.CallHistoryId) from t_gn_callhistory QC
						 left join tms_agent ta1 ON QC.CreatedById = ta1.UserId
						 where ta1.handling_type = 5 and QC.CustomerId = a.CustomerId
						) as QCA,
						(select max(DC.CallHistoryId) from t_gn_callhistory DC
						 left join tms_agent ta1 ON DC.CreatedById = ta1.UserId
						 where ta1.handling_type = 10 and DC.CustomerId = a.CustomerId
						) as DCA,
						(select max(CC.CreditCardId) from t_gn_customer_cc CC
						 where CC.CustomerId = a.CustomerId
						) as CCA
					FROM t_gn_customer a
									LEFT JOIN coll_report_collmon b ON a.CustomerId = b.CustomerId
									LEFT JOIN t_gn_insured c ON a.CustomerId=c.CustomerId
									LEFT JOIN t_gn_policy e ON c.PolicyId = e.PolicyId
									LEFT JOIN t_gn_campaign f ON a.CampaignId =f.CampaignId
									LEFT JOIN t_gn_productplan g ON e.ProductPlanId = g.ProductPlanId
									LEFT JOIN t_gn_product h ON g.ProductId = h.ProductId
									LEFT JOIN tms_agent i ON a.SellerId=i.UserId
									LEFT JOIN tms_agent j ON i.spv_id= j.UserId
									LEFT JOIN tms_agent k ON j.mgr_id = k.UserId
									LEFT JOIN tms_agent n ON a.QueueId = n.UserId
									LEFT JOIN t_gn_payer l ON a.CustomerId = l.CustomerId
									LEFT JOIN t_lk_gender d ON c.GenderId = d.GenderId 
									LEFT JOIN t_lk_bank m ON l.PayersBankId = m.BankId
									LEFT JOIN t_lk_premiumgroup o ON c.PremiumGroupId = o.PremiumGroupId
									LEFT JOIN t_gn_approval_score_qa p ON a.CustomerId = p.CustomerId
									LEFT JOIN t_lk_aprove_status q ON c.QCStatus = q.ApproveId
									LEFT JOIN t_lk_aprove_status qq ON a.CallReasonQue = qq.ApproveId
									LEFT JOIN t_lk_aprove_status s ON p.approval_id_update = s.ApproveId
									LEFT JOIN tms_agent r ON p.approval_qa_id = r.UserId
									LEFT JOIN tms_agent t ON p.approval_qa_id = t.UserId
									WHERE true
									".self::geFilter().";";
			$qry2 = $this -> query($sql2);
			foreach($qry2 -> result_assoc() as $rows2 ){
					$dataaas[$rows2['InsuredId']]['CustomerId']= $rows2['CustomerId'];
					$dataaas[$rows2['InsuredId']]['InsuredId']= $rows2['InsuredId'];
					$dataaas[$rows2['InsuredId']]['Apprv_QAID']= $rows2['Apprv_QAID'];
					$dataaas[$rows2['InsuredId']]['QCA']= $rows2['QCA'];
					$dataaas[$rows2['InsuredId']]['DCA']= $rows2['DCA'];
					$dataaas[$rows2['InsuredId']]['CCA']= $rows2['CCA'];
			}
			
			foreach($dataaas as $key => $Insured){
				foreach($Insured as $kei => $Datanya){
					if($kei == "Apprv_QAID"){
							if($Datanya){
								$qryApprv_QAID = $this -> query("select a.full_name from tms_agent a where a.UserId = ".$Datanya);
								foreach($qryApprv_QAID -> result_assoc() as $rowsApprv_QAID ){
									// $data[$Datanya['CustomerId']][$Datanya['InsuredId']]['qa_score']= $rowsApprv_QAID['full_name'];
								}
							}
						}else if($kei == "QCA"){
							if($Datanya){
								$QCA = "select b.full_name,
								IF(i.QCStatus=2,'-',DATE_FORMAT(a.CallHistoryUpdatedTs, '%d-%m-%Y')) as CallHistoryUpdatedTs
									 from t_gn_callhistory a
										left join tms_agent b on a.CreatedById = b.UserId
										left join t_gn_insured i on i.CustomerId=a.CustomerId
										where a.CallHistoryId = ".$Datanya;
								$qryQCA = $this -> query($QCA);
								foreach($qryQCA -> result_assoc() as $rowsQCA ){
									$data[$Insured['CustomerId']][$Insured['InsuredId']]['QC']= $rowsQCA['full_name'];
									$data[$Insured['CustomerId']][$Insured['InsuredId']]['QC_Date']= $rowsQCA['CallHistoryUpdatedTs'];
								}
							}
						}else if($kei == "DCA"){
							if($Datanya){
								$DCA = "select b.full_name,DATE_FORMAT(a.CallHistoryUpdatedTs, '%d-%m-%Y') as CallHistoryUpdatedTs
									 from t_gn_callhistory a
										left join tms_agent b on a.CreatedById = b.UserId
										where a.CallHistoryId = ".$Datanya;
								$qryDCA = $this -> query($DCA);
								foreach($qryDCA -> result_assoc() as $rowsDCA ){
									$data[$Insured['CustomerId']][$Insured['InsuredId']]['DC_Oleh']= $rowsDCA['full_name'];
									$data[$Insured['CustomerId']][$Insured['InsuredId']]['DC_Date']= $rowsDCA['CallHistoryUpdatedTs'];
								}
							}
						}else if($kei == "CCA"){
							if($Datanya){
								$CCA = "select CONCAT(cc.CreditCardNo,' (', cc.ExpireDate,')') AS CC from t_gn_customer_cc as cc
										where cc.CreditCardId = ".$Datanya;
								$qryCCA = $this -> query($CCA);
								foreach($qryCCA -> result_assoc() as $rowsCCA ){
									$data[$Insured['CustomerId']][$Insured['InsuredId']]['CC']= $rowsCCA['CC'];
								}
							}
						}
						else if($kei == "CustomerId"){
							if($Datanya){
								$hasilDC = "select s.AproveName from t_gn_approval_score_qa a 
											LEFT JOIN t_lk_aprove_status s ON a.approval_id = s.ApproveId
											where a.CustomerId = ".$Datanya;
								$qryhasilDC = $this -> query($hasilDC);
								foreach($qryhasilDC -> result_assoc() as $rowshasilDC ){
									$data[$Insured['CustomerId']][$Insured['InsuredId']]['hasilDC']= $rowshasilDC['AproveName'];
								}
							}
						}
				}
			}

				// echo "<pre>";
				// print_r($dataaap);
				// echo "</pre>";
			return $data;
	}
	/* get answer per customer***/
	function getAnswer ()
	{
		$sql = "SELECT a.CustomerId,c.CategoryId, c.SubCategoryId, g.CallTypeNum 
				FROM t_gn_customer a 
				INNER JOIN coll_report_collmon b ON a.CustomerId = b.CustomerId
				INNER JOIN coll_transaction_collmon c ON b.ReportId = c.ReportId
				INNER JOIN coll_link_point d ON c.LinkPointId = d.LinkPointId
				INNER JOIN coll_calltype_collmon g ON d.CallTypeId = g.CallTypeId
				LEFT JOIN t_gn_insured f ON a.CustomerId=f.CustomerId
				LEFT JOIN t_gn_policy e ON f.PolicyId = e.PolicyId
				WHERE 1=1 
				".self::geFilter()."
				GROUP BY a.CustomerId, c.CategoryId, c.SubCategoryId
				ORDER BY a.CustomerId, c.CategoryId, c.SubCategoryId";
		// echo "<pre>".$sql."</pre>";
		$qry = $this ->query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$data[$rows['CustomerId']][$rows['CategoryId']][$rows['SubCategoryId']] = $rows['CallTypeNum'];
		}
		return $data;
	}
	/** get Remark and Note **/
	function getRemark ()
	{
		$sql = "SELECT a.CustomerId, c.RemaksFields, c.RemarksText AS xRemark
				FROM t_gn_customer a
				LEFT JOIN coll_report_collmon b ON a.CustomerId = b.CustomerId
				LEFT JOIN coll_remarks_collmon c ON c.RemarksCustomerId = a.CustomerId
				LEFT JOIN t_gn_insured f ON a.CustomerId=f.CustomerId
				LEFT JOIN t_gn_policy e ON f.PolicyId = e.PolicyId
				WHERE 1=1
				".self::geFilter()." GROUP BY a.CustomerId, c.RemaksFields";
		// echo "<pre>".$sql."</pre>";
		$qry = $this ->query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$data[$rows['CustomerId']][$rows['RemaksFields']] = $rows['xRemark'];
		}
		return $data;
	}
	
	/** cretae d write_content **/
	function write_content(){
		$cusinfo= self::getInfoCustomer();
		$answer = self::getAnswer ();
		$remark = self::getRemark();
		// echo "<pre>";
		// print_r($remark);
		// echo "</pre>";
		
		// echo "<pre>";
		// print_r($answer);
		// echo "</pre>";
		$no=0;
		
		foreach ($cusinfo as $cusid => $arrValue){
			$no++;
			$samecustid= $no;
			$rowspan= count($cusinfo[$cusid]);
			foreach($arrValue as $insuredid => $arrval){
				echo "<tr>";
				if($samecustid==$no){
					echo	"<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$no."</td>";
				}
				echo   "<td class=\"xl582508\" nowrap>".$arrval['CustomerId']."</td>
						<td class=\"xl582508\" nowrap>".$arrval['CustomerFirstName']."</td>
						<td class=\"xl582508\" nowrap>".$arrval['PolicyNumber']."</td>
						<td class=\"xl582508\" nowrap>".$arrval['EFFDate']."</td>
						";
				/*if($samecustid==$no){
				echo "  <td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrValue[2]['InsuredFirstName']."</td>
						<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrValue[2]['InsuredDOB']."</td>";
						//$arrValue[2]['InsuredFirstName'] cetak hanya data insure
				}*/		
				echo  "<td class=\"xl582508\" nowrap>".$arrval['PayerFirstName']."</td>
						<td class=\"xl582508\" nowrap>".$arrval['InsuredFirstName']."</td>
						<td class=\"xl582508\" nowrap>".$arrval['InsuredDOB']."</td>
						<td class=\"xl582508\" nowrap>".(INT) $arrval['Premi']."</td>
						<td class=\"xl582508\" nowrap>".(INT) $arrval['APE']."</td>
						<td class=\"xl582508\" nowrap>".$arrval['ProductName']."</td>
						<td class=\"xl582508\" nowrap>".$arrval['CampaignName']."</td>
						<td class=\"xl582508\" nowrap>".$arrval['hasilanalis']."</td>";
				if($samecustid==$no){		
					echo	"<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['db_status']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['TM']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['TM_ID']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['SPV']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['mgr']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['QC_Date']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['QC']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['PhoneNum']."</td>";
					foreach($this->IdScore as $category =>$arrVal){
						foreach($arrVal as $subcat => $val){
							echo "<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$answer[$cusid][$category][$subcat]['score']."</td>";
							echo "<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>&nbsp;</td>";
						}
					}
					echo   "<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".($remark[$cusid][1]?$remark[$cusid][1]:"-")."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['Overall_Score']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['Rating']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['hasilDC']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['DC_Date']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['DC_Oleh']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['CC']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".($remark[$cusid][2]?$remark[$cusid][2]:"-")."</td>
							<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arrval['Remark_1']."</td>";
				}				
				
				echo "</tr>";
				$samecustid++;
			}
			
		}
	}
	
}
?>