<?php
class quality_activity extends IndexExcel{
	function quality_activity()
	{
		mysql::__construct();
	}
	
/* content HTML */
	
function show_content_excel()
	{
		if( $this -> havepost('group_by'))
		{
				$xlsName = 'Quality_report_'.date('Ymd').'_'.date('His').'.xls';
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Cache-Control: private");
				header("Pragma: no-cache");
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$xlsName");
				
			switch( $this -> escPost('group_by'))
			{
				case 'by_quality_campaign' 	: $this -> QualityByCampaign(); 	break; 
				case 'by_quality_status' 	: $this -> QualityByStatus(); 		break; 
				case 'by_quality_agent' 	: $this -> QualityByAgent(); 		break; 
				case 'by_quality_qa' 		: $this -> QualityByQA(); 			break; 
				case 'by_quality_date' 		: $this -> QualityByDates(); 		break; 
			}
		}
	}
	
/** get agnet name **/

private function getAgentName($SellerId=0 )
	{
		$sql = " SELECT CONCAT(a.id,' - ',a.full_name) as agent_name 
				 FROM tms_agent a 
				 WHERE a.UserId ='$SellerId' ";
				 
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			return $qry -> result_singgle_value();
		}
		else
			return NULL;
	}
	
	
/** get campaign name **/

private function getCampaignName($CampaignId=0 )
	{
		$sql = "SELECT a.CampaignName FROM t_gn_campaign a WHERE a.CampaignId='$CampaignId'";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			return $qry -> result_singgle_value();
		}
		else
			return NULL;
	}
	
	
/** get campaign name **/

private function getResultName($ResultId=0 )
	{
		$sql = "select a.AproveName from t_lk_aprove_status a where a.ApproveId='$ResultId'";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			return $qry -> result_singgle_value();
		}
		else
			return NULL;
	}	
	
/** QualityByDates() ****/
function QualityByDates()
{
	$start_date = $this -> Date -> english( $this -> escPost('start_date'),'-' );
	$end_date   = $this -> Date -> english( $this -> escPost('end_date'),'-' );
	
	/** start header content table ***/	
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				  <td class=\"header first\" nowrap align=\"center\">Interval</td>
				  <td class=\"header middle\" nowrap>Data Size</td>
				  <td class=\"header middle\" nowrap>Verified By QA</td>
				  <td class=\"header middle\" nowrap>Pending By QA</td>
				  <td class=\"header middle\" nowrap>Reject By QA</td>
				  <td class=\"header middle\" nowrap>Reconfirm By TM</td>
				  <td class=\"header lasted\" nowrap>AVG Score</td>
			</tr> "; 
	
	/** query sintax ***/
		
		$sql = " SELECT date(a.CollmonCreateTs) as tgl,
				  COUNT(a.CustomerId) as jumlah,
				  SUM(IF(a.CollmonResultId=1,1,0)) as Verified_By_QA,
				  SUM(IF(a.CollmonResultId=2,1,0)) as Pending_By_QA,
				  SUM(IF(a.CollmonResultId=3,1,0)) as Reject_By_QA,
				  SUM(IF(a.CollmonResultId=4,1,0)) as Reconfirm_By_QA,
				  SUM(a.CollmonPoint) as ScorePoint
				FROM coll_report_collmon a 
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId
				LEFT JOIN t_gn_assignment d on b.CustomerId=d.CustomerId
				GROUP BY tgl ";
	
	/* definer totals data array ***/
	
			$totals_jumlah	  = array();
			$totals_verified  = array();
			$totals_pending   = array();
			$totals_reject    = array();
			$totals_reconfirm = array();
			$totals_point     = array();	
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$totals_jumlah[$rows['tgl']]+= $rows['jumlah'];
				$totals_verified[$rows['tgl']]+= $rows['Verified_By_QA'];
				$totals_pending[$rows['tgl']]+= $rows['Pending_By_QA'];
				$totals_reject[$rows['tgl']]+= $rows['Reject_By_QA'];
				$totals_reconfirm[$rows['tgl']]+= $rows['Reconfirm_By_QA'];
				$totals_point[$rows['tgl']]+= $rows['ScorePoint'];
			}
			
	/** testing ***/
		
		while( true )
		{
			$estart_date = $start_date;
			$avg_score_point = number_format(($totals_point[$estart_date]?($totals_point[$estart_date]/$totals_jumlah[$estart_date]):0),2,'.',','); 
			echo "<tr>
					<td class=\"content first\" nowrap style=\"padding-left:8px;\"><b>".$estart_date."</b></td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_jumlah[$estart_date]?$totals_jumlah[$estart_date]:0)."</td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_verified[$estart_date]?$totals_verified[$estart_date]:0)."</td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_pending[$estart_date]?$totals_pending[$estart_date]:0)."</td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_reject[$estart_date]?$totals_reject[$estart_date]:0)."</td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_reconfirm[$estart_date]?$totals_reconfirm[$estart_date]:0)."</td>
					<td class=\"content lasted\" align=\"right\" nowrap>".($avg_score_point?$avg_score_point:0)."</td>
				</tr> "; 
				
			$totals_sub_jumlah += $totals_jumlah[$estart_date];
			$totals_sub_verified += $totals_verified[$estart_date];
			$totals_sub_pending += $totals_pending[$estart_date];
			$totals_sub_reject += $totals_reject[$estart_date];
			$totals_sub_reconfirm += $totals_reconfirm[$estart_date];
			$totals_sub_point += $totals_point[$estart_date];
				
			if( $estart_date == $end_date)	break;
				$start_date  = $this -> Date -> nextDate($start_date);
				
		}
		
		$avg_grand_point = number_format(($totals_sub_jumlah?($totals_sub_point/$totals_sub_jumlah):0),2,'.',','); 
		echo "<tr>
				<td class=\"total middle\" nowrap>Summary</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_jumlah."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_verified."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_pending."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_reject."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_reconfirm."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$avg_grand_point."</td>
			</tr> </table>"; 

}
	
/** QualityByQA() ******/
function QualityByQA()
{
		$QA_user    = EXPLODE( ',', $this -> escPost('group_select') );
		$start_date = $this -> Date -> english( $this -> escPost('start_date'),'-' );
		$end_date   = $this -> Date -> english( $this -> escPost('end_date'),'-' );
		
	/** start header content table ***/	
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				  <td class=\"header first\" nowrap align=\"center\">User QC <br> ( Quality Control )</td>
				  <td class=\"header middle\" nowrap>Data Size</td>
				  <td class=\"header middle\" nowrap>Verified By QA</td>
				  <td class=\"header middle\" nowrap>Pending By QA</td>
				  <td class=\"header middle\" nowrap>Reject By QA</td>
				  <td class=\"header middle\" nowrap>Reconfirm By TM</td>
				  <td class=\"header lasted\" nowrap>AVG Score</td>
			</tr> "; 
	
	/** query sintaxt ***/
	
		$sql = " SELECT a.CollmonUser,
					COUNT(a.CustomerId) as jumlah,
					SUM(IF(a.CollmonResultId=1,1,0)) as Verified_By_QA,
					SUM(IF(a.CollmonResultId=2,1,0)) as Pending_By_QA,
					SUM(IF(a.CollmonResultId=3,1,0)) as Reject_By_QA,
					SUM(IF(a.CollmonResultId=4,1,0)) as Reconfirm_By_QA,
					SUM(a.CollmonPoint) as ScorePoint
				FROM coll_report_collmon a 
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId
				LEFT JOIN t_gn_assignment d on b.CustomerId=d.CustomerId
				WHERE DATE(a.CollmonCreateTs)>='$start_date' 
				AND DATE(a.CollmonCreateTs)<='$end_date'
				GROUP BY a.CollmonUser ";
				
		/* definer totals data array ***/
	
			$totals_jumlah	  = array();
			$totals_verified  = array();
			$totals_pending   = array();
			$totals_reject    = array();
			$totals_reconfirm = array();
			$totals_point     = array();	
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$totals_jumlah[$rows['CollmonUser']]+= $rows['jumlah'];
				$totals_verified[$rows['CollmonUser']]+= $rows['Verified_By_QA'];
				$totals_pending[$rows['CollmonUser']]+= $rows['Pending_By_QA'];
				$totals_reject[$rows['CollmonUser']]+= $rows['Reject_By_QA'];
				$totals_reconfirm[$rows['CollmonUser']]+= $rows['Reconfirm_By_QA'];
				$totals_point[$rows['CollmonUser']]+= $rows['ScorePoint'];
			}
			
			
		/* definer totals data INT ***/	
	
			$totals_sub_jumlah = 0;
			$totals_sub_verified = 0;
			$totals_sub_pending = 0;
			$totals_sub_reject = 0;
			$totals_sub_reconfirm = 0;
			$totals_sub_point = 0;
			
			foreach( $QA_user as $key => $QualityId )
			{	
				$seller_value_name = $this -> getAgentName($QualityId);
				$avg_score_point = number_format(($totals_point[$QualityId]?($totals_point[$QualityId]/$totals_jumlah[$QualityId]):0),2,'.',','); 
				echo "<tr>
						<td class=\"content first\" nowrap><b>".$seller_value_name."</b></td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_jumlah[$QualityId]?$totals_jumlah[$QualityId]:0)."</td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_verified[$QualityId]?$totals_verified[$QualityId]:0)."</td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_pending[$QualityId]?$totals_pending[$QualityId]:0)."</td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_reject[$QualityId]?$totals_reject[$QualityId]:0)."</td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_reconfirm[$QualityId]?$totals_reconfirm[$QualityId]:0)."</td>
						<td class=\"content lasted\" align=\"right\" nowrap>".($avg_score_point?$avg_score_point:0)."</td>
					</tr> "; 
				
				$totals_sub_jumlah += $totals_jumlah[$QualityId];
				$totals_sub_verified += $totals_verified[$QualityId];
				$totals_sub_pending += $totals_pending[$QualityId];
				$totals_sub_reject += $totals_reject[$QualityId];
				$totals_sub_reconfirm += $totals_reconfirm[$QualityId];
				$totals_sub_point += $totals_point[$QualityId];
			}	
			
			$avg_grand_point = number_format(($totals_sub_jumlah?($totals_sub_point/$totals_sub_jumlah):0),2,'.',','); 
			echo "<tr>
					<td class=\"total middle\" nowrap>Summary</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_jumlah."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_verified."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_pending."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_reject."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_reconfirm."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$avg_grand_point."</td>
				</tr> </table>"; 
				
	
	} 

/** QualityByAgent() **/
	
function QualityByAgent()
	{
		$SellerId   = EXPLODE( ',', $this -> escPost('group_select') );
		$start_date = $this -> Date -> english( $this -> escPost('start_date'),'-' );
		$end_date   = $this -> Date -> english( $this -> escPost('end_date'),'-' );
		
	/** start header content table ***/	
	
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				  <td class=\"header first\" nowrap>Campaign Name</td>
				  <td class=\"header middle\" nowrap>Data Size</td>
				  <td class=\"header middle\" nowrap>Verified By QA</td>
				  <td class=\"header middle\" nowrap>Pending By QA</td>
				  <td class=\"header middle\" nowrap>Reject By QA</td>
				  <td class=\"header middle\" nowrap>Reconfirm By TM</td>
				  <td class=\"header lasted\" nowrap>AVG Score</td>
			</tr> "; 
			
		/* query syntaxk sql ****/
		
			$sql = " SELECT d.AssignSelerId,
						COUNT(a.CustomerId) as jumlah,
						SUM(IF(a.CollmonResultId=1,1,0)) as Verified_By_QA,
						SUM(IF(a.CollmonResultId=2,1,0)) as Pending_By_QA,
						SUM(IF(a.CollmonResultId=3,1,0)) as Reject_By_QA,
						SUM(IF(a.CollmonResultId=4,1,0)) as Reconfirm_By_QA,
						SUM(a.CollmonPoint) as ScorePoint
					FROM coll_report_collmon a 
					LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
					LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId
					LEFT JOIN t_gn_assignment d on b.CustomerId=d.CustomerId
					WHERE DATE(a.CollmonCreateTs)>='$start_date' 
					AND DATE(a.CollmonCreateTs)<='$end_date'
					GROUP BY d.AssignSelerId ";
		//echo "<pre>$sql</pre>";			
		/* definer totals data array ***/
	
			$totals_jumlah	  = array();
			$totals_verified  = array();
			$totals_pending   = array();
			$totals_reject    = array();
			$totals_reconfirm = array();
			$totals_point     = array();	
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$totals_jumlah[$rows['AssignSelerId']]+= $rows['jumlah'];
				$totals_verified[$rows['AssignSelerId']]+= $rows['Verified_By_QA'];
				$totals_pending[$rows['AssignSelerId']]+= $rows['Pending_By_QA'];
				$totals_reject[$rows['AssignSelerId']]+= $rows['Reject_By_QA'];
				$totals_reconfirm[$rows['AssignSelerId']]+= $rows['Reconfirm_By_QA'];
				$totals_point[$rows['AssignSelerId']]+= $rows['ScorePoint'];
			}
			
			/* definer totals data INT ***/	
	
			$totals_sub_jumlah = 0;
			$totals_sub_verified = 0;
			$totals_sub_pending = 0;
			$totals_sub_reject = 0;
			$totals_sub_reconfirm = 0;
			$totals_sub_point = 0;
			
			foreach( $SellerId as $key => $sellerid )
			{	
				$seller_value_name = $this -> getAgentName($sellerid);
				$avg_score_point = number_format(($totals_point[$sellerid]?($totals_point[$sellerid]/$totals_jumlah[$sellerid]):0),2,'.',','); 
				echo "<tr>
						<td class=\"content first\" nowrap><b>".$seller_value_name."</b></td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_jumlah[$sellerid]?$totals_jumlah[$sellerid]:0)."</td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_verified[$sellerid]?$totals_verified[$sellerid]:0)."</td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_pending[$sellerid]?$totals_pending[$sellerid]:0)."</td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_reject[$sellerid]?$totals_reject[$sellerid]:0)."</td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_reconfirm[$sellerid]?$totals_reconfirm[$sellerid]:0)."</td>
						<td class=\"content lasted\" align=\"right\" nowrap>".($avg_score_point?$avg_score_point:0)."</td>
					</tr> "; 
				
				$totals_sub_jumlah += $totals_jumlah[$sellerid];
				$totals_sub_verified += $totals_verified[$sellerid];
				$totals_sub_pending += $totals_pending[$sellerid];
				$totals_sub_reject += $totals_reject[$sellerid];
				$totals_sub_reconfirm += $totals_reconfirm[$sellerid];
				$totals_sub_point += $totals_point[$sellerid];
			}	
			
			$avg_grand_point = number_format(($totals_sub_jumlah?($totals_sub_point/$totals_sub_jumlah):0),2,'.',','); 
			echo "<tr>
					<td class=\"total middle\" nowrap>Summary</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_jumlah."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_verified."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_pending."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_reject."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_reconfirm."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$avg_grand_point."</td>
				</tr> </table>"; 
	}
	
/** QualityByStatus() **/
function QualityByStatus()
	{
		$QualityStatus = EXPLODE( ',', $this -> escPost('group_select') );
		$start_date = $this -> Date -> english( $this -> escPost('start_date'),'-' );
		$end_date = $this -> Date -> english( $this -> escPost('end_date'),'-' );
		  
		/** start header content table ***/	
		
			echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
					<tr>
					  <td class=\"header first\" nowrap>Quality Status </td>
					  <td class=\"header middle\" nowrap>Data Size</td>
					  <td class=\"header lasted\" nowrap>AVG Score</td>
				</tr> "; 
				
				
		/** query sintax ********/
			
			$totals_result  = array();
			$totals_jumlah	= array();
			$totals_point   = array();
			
			$sql = " SELECT 
						a.CollmonResultId, COUNT(a.CustomerId) as jumlah, SUM(a.CollmonPoint) as ScorePoint
					 FROM coll_report_collmon a 
					 LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
					 LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId
					 WHERE DATE(a.CollmonCreateTs)>='$start_date' 
					 AND DATE(a.CollmonCreateTs)<='$end_date'
					 GROUP BY a.CollmonResultId ";
					 
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$totals_jumlah[$rows['CollmonResultId']] = $rows['jumlah'];
				$totals_point[$rows['CollmonResultId']] += $rows['ScorePoint'];
			}
			
		/** content table ****/
			
			$totals_sub_jumlah = 0;
			$totals_sub_point = 0;
			
			foreach($QualityStatus as $key => $ResultId )
			{
				$avg_score_point = number_format(($totals_point[$ResultId]?($totals_point[$ResultId]/$totals_jumlah[$ResultId]):0),2,'.',','); 
				$result_value_name = $this -> getResultName($ResultId);
				echo "<tr>
						<td class=\"content first\" nowrap><b>".$result_value_name."</b></td>
						<td class=\"content middle\" align=\"right\" nowrap>".($totals_jumlah[$ResultId]?$totals_jumlah[$ResultId]:0)."</td>
						<td class=\"content lasted\" align=\"right\" nowrap>".($avg_score_point?$avg_score_point:0)."</td>
					</tr> "; 
					
				$totals_sub_jumlah += $totals_jumlah[$ResultId];
				$totals_sub_point += $totals_point[$ResultId];
			}
			
			$avg_grand_point = number_format(($totals_sub_jumlah?($totals_sub_point/$totals_sub_jumlah):0),2,'.',','); 
			echo "<tr>
					<td class=\"total middle\" nowrap>Summary</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_jumlah."</td>
					<td class=\"total middle\" align=\"right\" nowrap>".$avg_grand_point."</td>
				</tr> "; 
	}
	
/** QualityByCampaign **/
function QualityByCampaign()
	{
	
	  $CampignId  = EXPLODE( ',', $this -> escPost('group_select') );
	  $start_date = $this -> Date -> english( $this -> escPost('start_date'),'-' );
	  $end_date   = $this -> Date -> english( $this -> escPost('end_date'),'-' );
	  
	/** start header content table ***/	
	
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				  <td class=\"header first\" nowrap>Campaign Name</td>
				  <td class=\"header middle\" nowrap>Data Size</td>
				  <td class=\"header middle\" nowrap>Verified By QA</td>
				  <td class=\"header middle\" nowrap>Pending By QA</td>
				  <td class=\"header middle\" nowrap>Reject By QA</td>
				  <td class=\"header middle\" nowrap>Reconfirm By TM</td>
				  <td class=\"header lasted\" nowrap>AVG Score</td>
			</tr> "; 
			
	/* query syntaxk sql ****/
	
		$sql = " SELECT b.CampaignId,
					COUNT(a.CustomerId) as jumlah,
					SUM(IF(a.CollmonResultId=1,1,0)) as Verified_By_QA,
					SUM(IF(a.CollmonResultId=2,1,0)) as Pending_By_QA,
					SUM(IF(a.CollmonResultId=3,1,0)) as Reject_By_QA,
					SUM(IF(a.CollmonResultId=4,1,0)) as Reconfirm_By_QA,
					SUM(a.CollmonPoint) as ScorePoint
				FROM coll_report_collmon a 
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId
				WHERE DATE(a.CollmonCreateTs)>='$start_date' 
				AND DATE(a.CollmonCreateTs)<='$end_date'
				
				GROUP BY b.CampaignId ";
				
		//echo "<pre>".$sql."</pre>";
		
	/* definer totals data array ***/
	
		$totals_campaign  = array();
		$totals_jumlah	  = array();
		$totals_verified  = array();
		$totals_pending   = array();
		$totals_reject    = array();
		$totals_reconfirm = array();
		$totals_point     = array();
		
	/* definer totals data INT ***/
	
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$totals_campaign[$rows['CampaignId']] = $rows['CampaignName'];
			$totals_jumlah[$rows['CampaignId']] = $rows['jumlah'];
			$totals_verified[$rows['CampaignId']]+= $rows['Verified_By_QA'];
			$totals_pending[$rows['CampaignId']] += $rows['Pending_By_QA'];
			$totals_reject[$rows['CampaignId']] += $rows['Reject_By_QA'];
			$totals_reconfirm[$rows['CampaignId']] += $rows['Reconfirm_By_QA'];
			$totals_point[$rows['CampaignId']] += $rows['ScorePoint'];
		}
		
	/* definer totals data INT ***/	
	
		$totals_sub_jumlah = 0;
		$totals_sub_verified = 0;
		$totals_sub_pending = 0;
		$totals_sub_reject = 0;
		$totals_sub_reconfirm = 0;
		$totals_sub_point = 0;
		
		foreach( $CampignId as $ValueKeys =>  $CampaignId )
		{	
			$campaign_value_name = $this -> getCampaignName($CampaignId);
			$avg_score_point = number_format(($totals_point[$CampaignId]?($totals_point[$CampaignId]/$totals_jumlah[$CampaignId]):0),2,'.',','); 
			echo "<tr>
					<td class=\"content first\" nowrap><b>".$campaign_value_name."</b></td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_jumlah[$CampaignId]?$totals_jumlah[$CampaignId]:0)."</td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_verified[$CampaignId]?$totals_verified[$CampaignId]:0)."</td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_pending[$CampaignId]?$totals_pending[$CampaignId]:0)."</td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_reject[$CampaignId]?$totals_reject[$CampaignId]:0)."</td>
					<td class=\"content middle\" align=\"right\" nowrap>".($totals_reconfirm[$CampaignId]?$totals_reconfirm[$CampaignId]:0)."</td>
					<td class=\"content lasted\" align=\"right\" nowrap>".($avg_score_point?$avg_score_point:0)."</td>
				</tr> "; 
			
			$totals_sub_jumlah += $totals_jumlah[$CampaignId];
			$totals_sub_verified += $totals_verified[$CampaignId];
			$totals_sub_pending += $totals_pending[$CampaignId];
			$totals_sub_reject += $totals_reject[$CampaignId];
			$totals_sub_reconfirm += $totals_reconfirm[$CampaignId];
			$totals_sub_point += $totals_point[$CampaignId];
		}	
		
		$avg_grand_point = number_format(($totals_sub_jumlah?($totals_sub_point/$totals_sub_jumlah):0),2,'.',','); 
		
		echo "<tr>
				<td class=\"total middle\" nowrap>Summary</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_jumlah."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_verified."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_pending."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_reject."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$totals_sub_reconfirm."</td>
				<td class=\"total middle\" align=\"right\" nowrap>".$avg_grand_point."</td>
			</tr> </table>"; 
	}	
}

?>