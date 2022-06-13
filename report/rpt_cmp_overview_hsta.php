<?php
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$cignasystem	= $_REQUEST['cignasystem'];
	$campaign		= explode(",",$_REQUEST['cmp']);
	$campaign1		= implode("','",$campaign);
	$today = date("Y-m-d");
	// echo $_REQUEST['cmp']."=>".$campaign1;
	
	set_time_limit(500000);

		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		//Fungsi Hours
		function jaammm()
		{
			global $db;	
			$jaammm=array();
			$sql ="  SELECT
						ca.CampaignId, ca.CampaignNumber, 
						SUM((ac.EndCallTs - ac.StartCallTs)/3600) AS Hours
					FROM t_gn_activitycall AS ac
						LEFT JOIN t_gn_customer c ON c.CustomerId=ac.CustomerId
						LEFT JOIN t_gn_campaign ca ON ca.CampaignId=c.CampaignId
					WHERE 1=1
						AND c.CustomerUpdatedTs >= '".$_REQUEST['start_date']." 00:00:00'
						AND c.CustomerUpdatedTs <= '".$_REQUEST['end_date']." 23:00:00'
					GROUP BY ca.CampaignNumber";
			
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$jaammm[$row['CampaignNumber']] = $row['Hours'];
			}	
			return $jaammm;
		}
		
		//Fungsi Leads
		function leads()
		{
			global $db;	
			$leads=array();
			$sql =" SELECT
						ca.CampaignNumber, ca.CampaignId, 
						COUNT(DISTINCT c.CustomerId) AS Leads
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
					WHERE 1=1
						AND ca.CampaignNumber IS NOT NULL
						AND ca.CampaignId IS NOT NULL
						AND DATE(c.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(c.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY ca.CampaignNumber;";
			
			// echo "<pre>".$sql."</pre>";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$leads[$row['CampaignNumber']] = $row['Leads'];
			}	
			return $leads;
		}
		
		//Fungsi Solicited
		function solicited()
		{
			global $db;	
			$solicited=array();
			$sql =" SELECT 
						b.CampaignNumber, 
						SUM(IF(a.CustomerUpdatedTs IS NOT NULL, 1,0)) AS Solicited
					FROM t_gn_customer a
						LEFT JOIN t_gn_campaign b ON a.CampaignId = b.CampaignId
					WHERE 1=1 
						AND DATE(a.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(a.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY b.CampaignNumber;";
			// echo "<pre>".$sql."</pre>";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$solicited[$row['CampaignNumber']] = $row['Solicited'];
			}	
			return $solicited;
		}
		
		//Fungsi Contact
		function contact()
		{
			global $db;	
			$contact=array();
			$sql =" SELECT
						#ca.CampaignNumber,
						up.UploadId,
						ca.CampaignId, 
						COUNT(DISTINCT IF(a.CallReasonContactedFlag = 1,c.CustomerId,0)) AS Contact
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
						LEFT JOIN t_lk_callreason a ON c.CallReasonId = a.CallReasonId
						LEFT JOIN t_gn_uploadreport up ON c.UploadId = up.UploadId
					WHERE 1=1 
						AND DATE(c.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(c.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY ca.CampaignNumber ";
			// echo "<pre>".$sql."</pre>";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				// $contact[$row['CampaignNumber']] = $row['Contact'];
				$contact[$row['UploadId']] = $row['Contact'];
			}
			return $contact;
		}
		
		
		//Fungsi Terminated Leads
		function terminated()
		{
			global $db;	
			$terminated=array();
			$sql = "SELECT 
						ca.CampaignNumber, ca.CampaignId, 
						COUNT(DISTINCT IF(a.CallReasonTerminate = 1,c.CustomerId,0)) AS Terminate
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
						LEFT JOIN t_lk_callreason a ON c.CallReasonId = a.CallReasonId
					WHERE 1=1
						AND DATE(c.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(c.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY ca.CampaignNumber";
			// echo "<pre>".$sql."</pre>";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$terminated[$row['CampaignNumber']] = $row['Terminate'];
			}
			return $terminated;
		}
		
		//Fungsi TMR
		function tmr()
		{
			global $db;	
			$tmr = array();
			$sql = "SELECT 
						c.CampaignNumber AS CampaignNumber, 
						c.CampaignId AS CampaignId, 
						COUNT(DISTINCT a.AssignSelerId) AS tmr
					FROM t_gn_assignment a
						LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
						LEFT JOIN t_gn_campaign c ON b.CampaignId = c.CampaignId
					WHERE 
						a.AssignSelerId IS NOT NULL 
						AND DATE(b.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(b.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY c.CampaignNumber";
			//echo $sql;
			$qry=mysql_query($sql);
			while($row = mysql_fetch_array($qry))
			{
				$tmr[$row['CampaignNumber']] = $row['tmr'];
			}
			return $tmr;
		}
		
		//Fungsi Attempt d.CampaignNumber,
		function attempt()
		{
			global $db;	
			$attempt = array();
			$sql = "SELECT DISTINCT 
						
						up.UploadId,
						COUNT(a.CallHistoryId) AS Attempt
					FROM t_gn_callhistory a
						LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
						LEFT JOIN t_gn_uploadreport up ON b.UploadId=up.UploadId
						LEFT JOIN tms_agent c on a.CreatedById=c.UserId
						LEFT JOIN t_gn_campaign d ON b.CampaignId = d.CampaignId
					WHERE 1=1
						AND DATE(a.CallHistoryCreatedTs)>= '".$_REQUEST['start_date']."' 
						AND DATE(a.CallHistoryCreatedTs)<= '".$_REQUEST['end_date']."'
					GROUP BY up.UploadId";
			//echo $sql;
			$qry=mysql_query($sql);
			while($row = mysql_fetch_array($qry))
			{
				$attempt[$row['UploadId']] = $row['Attempt'];
			}
			return $attempt;
		}
		
		//Fungsi Sales
		function sales()
		{
			global $db;	
			$sales = array();
			$sql = " SELECT 
						f.CampaignId,
						f.CampaignNumber, COUNT(DISTINCT a.CustomerId) AS Sales
					FROM t_gn_policyautogen a
						LEFT JOIN t_gn_policy b ON a.PolicyNumber=b.PolicyNumber
						LEFT JOIN t_gn_customer c ON a.CustomerId=c.CustomerId
						LEFT JOIN t_gn_assignment d ON c.CustomerId=d.CustomerId
						LEFT JOIN t_gn_productplan e ON b.ProductPlanId=e.ProductPlanId
						LEFT JOIN t_gn_campaign f ON c.CampaignId = f.CampaignId
					WHERE 
						DATE(b.PolicySalesDate)>='".$_REQUEST['start_date']."' AND 
						DATE(b.PolicySalesDate)<='".$_REQUEST['end_date']."' AND 
						c.CallReasonId IN(15,16)
					GROUP BY f.CampaignNumber ";
			
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$sales[$row['CampaignNumber']] = $row['Sales'];
			}	
			return $sales;
		}
		
		//Fungsi ANP
		function anp()
		{
			global $db;	
			$anp = array();
			$sql = "SELECT 
						f.CampaignId, f.CampaignNumber,
						SUM(b.Premi) AS tmp, SUM(IF(e.PayModeId=2,(b.Premi*12), b.Premi)) AS ANP
					FROM t_gn_policyautogen a
						LEFT JOIN t_gn_policy b ON a.PolicyNumber=b.PolicyNumber
						LEFT JOIN t_gn_customer c ON a.CustomerId=c.CustomerId
						LEFT JOIN t_gn_assignment d ON c.CustomerId=d.CustomerId
						LEFT JOIN t_gn_productplan e ON b.ProductPlanId=e.ProductPlanId
						LEFT JOIN t_gn_product tgpd on e.ProductId = tgpd.ProductId
						LEFT JOIN t_gn_campaign f ON c.CampaignId = f.CampaignId
					WHERE 
						DATE(b.PolicySalesDate)>='".$_REQUEST['start_date']."' AND 
						DATE(b.PolicySalesDate)<='".$_REQUEST['end_date']."' AND 
						c.CallReasonId IN(15) AND c.CallReasonQue = 1
					GROUP BY f.CampaignNumber ";
			
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$anp[$row['CampaignNumber']] = $row['ANP'];
			}	
			return $anp;
		}
		
		//New Function added per 2nd Mei 2014
		function getProductCode($i)
		{
			$sql = "select a.ProductCode from t_gn_product a where ProductStatusFlag = $i";
			$qry=mysql_query($sql);
			$k=0;
			while ($row = mysql_fetch_array($qry)){
				$ProductCode[$k] = $row['ProductCode'];
				$k++;
			}
			return $ProductCode;
		}
		
		function getProductCategory()
		{
			$sql = "SELECT a.product_category_id,a.product_category_code,
					b.ProductId,b.ProductCode
					FROM t_gn_product_category a 
					INNER JOIN t_gn_product b ON a.product_category_id=b.product_category_id
					WHERE b.ProductStatusFlag = 1";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry)){
				$ProductCategory['category'][$row['product_category_id']] = $row['product_category_code'];
				$ProductCategory['product'][$row['product_category_id']][$row['ProductId']] = $row['ProductCode'];
				
			}
			return $ProductCategory;
		}
		
		function getCases($CampaignId,$UploadId,$cat=""){
			switch($cat){
				case "cases":
					$sql = "SELECT COUNT(DISTINCT tcst.CustomerId) as Cases FROM t_gn_campaign tcmp 
					LEFT JOIN t_gn_customer tcst ON tcmp.CampaignId = tcst.CampaignId 
					LEFT JOIN t_lk_callreason tcll ON tcll.CallReasonId = tcst.CallReasonId 
					
					left join t_gn_insured tgin on tcst.CustomerId = tgin.CustomerId 
					left join t_gn_policy tgpc on tgin.PolicyId = tgpc.PolicyId 
					left join t_gn_productplan tgpl on tgpc.ProductPlanId = tgpl.ProductPlanId 
					left join t_gn_product tgpd on tgpl.ProductId = tgpd.ProductId 
					left join t_gn_uploadreport tgup on tgup.UploadId = tcst.UploadId 
					
					WHERE 1=1 
					AND tgup.UploadId = ".$UploadId." 
					AND tcst.CampaignId = ".$CampaignId."
					AND tcst.CallReasonId = 15 AND tcst.CallReasonQue = 1 
					AND tcst.CustomerUpdatedTs >= '".$_REQUEST['start_date']." 00:00:00' 
					AND tcst.CustomerUpdatedTs <= '".$_REQUEST['end_date']." 23:00:00'";
					break;
				case "contacted":
					// $sql = "SELECT COUNT(DISTINCT tcst.CustomerId) as Cases FROM t_gn_campaign tcmp 
					// LEFT JOIN t_gn_customer tcst ON tcmp.CampaignId = tcst.CampaignId 
					// LEFT JOIN t_lk_callreason tcll ON tcll.CallReasonId = tcst.CallReasonId 
					
					// left join t_gn_insured tgin on tcst.CustomerId = tgin.CustomerId 
					// left join t_gn_policy tgpc on tgin.PolicyId = tgpc.PolicyId 
					// left join t_gn_productplan tgpl on tgpc.ProductPlanId = tgpl.ProductPlanId 
					// left join t_gn_product tgpd on tgpl.ProductId = tgpd.ProductId 
					// left join t_gn_uploadreport tgup on tgup.UploadId = tcst.UploadId 
					
					// WHERE 1=1 
					// AND tgup.UploadId = ".$UploadId." 
					// AND tcst.CampaignId = ".$CampaignId."
					// AND tcll.CallReasonContactedFlag = 1 AND DATE(tcst.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
					// AND DATE(tcst.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'";
					$sql = "select count(distinct ch.CustomerId) as Cases
							from t_gn_callhistory ch
							inner join t_gn_customer cs on cs.CustomerId=ch.CustomerId
							inner join t_lk_callreason ca on ca.CallReasonId=ch.CallReasonId
							where ch.CallHistoryId =
								(select max(subch.CallHistoryId) from t_gn_callhistory subch where
									subch.CallHistoryCallDate >= '".$_REQUEST['start_date']." 00:00:00' 
									and subch.CallHistoryCallDate <= '".$_REQUEST['end_date']." 23:50:00'
									and subch.CustomerId = ch.CustomerId
									and cs.UploadId = ".$UploadId."
									AND cs.CampaignId = ".$CampaignId."
									and ca.CallReasonContactedFlag = 1
								);
							#ch.CallHistoryCallDate >= '".$_REQUEST['start_date']." 00:00:00'
							#and ch.CallHistoryCallDate <= '".$_REQUEST['end_date']." 23:00:00'
							#and ca.CallReasonContactedFlag = 1
							#AND cs.UploadId = ".$UploadId."
							#AND cs.CampaignId = ".$CampaignId.";
							";
					break;
				case "nottouch":
					$sql = "SELECT COUNT(DISTINCT tcst.CustomerId) as Cases FROM t_gn_campaign tcmp 
					LEFT JOIN t_gn_customer tcst ON tcmp.CampaignId = tcst.CampaignId 
					LEFT JOIN t_lk_callreason tcll ON tcll.CallReasonId = tcst.CallReasonId 
					
					left join t_gn_insured tgin on tcst.CustomerId = tgin.CustomerId 
					left join t_gn_policy tgpc on tgin.PolicyId = tgpc.PolicyId 
					left join t_gn_productplan tgpl on tgpc.ProductPlanId = tgpl.ProductPlanId 
					left join t_gn_product tgpd on tgpl.ProductId = tgpd.ProductId 
					left join t_gn_uploadreport tgup on tgup.UploadId = tcst.UploadId 
					
					WHERE 1=1 
					AND tgup.UploadId = ".$UploadId." 
					AND tcst.CampaignId = ".$CampaignId."
					AND tcst.CallReasonId IS NULL";
					break;
				case "touch":
					// $sql = "SELECT COUNT(DISTINCT tcst.CustomerId) as Cases FROM t_gn_campaign tcmp 
					// LEFT JOIN t_gn_customer tcst ON tcmp.CampaignId = tcst.CampaignId 
					// LEFT JOIN t_lk_callreason tcll ON tcll.CallReasonId = tcst.CallReasonId 
					
					// left join t_gn_insured tgin on tcst.CustomerId = tgin.CustomerId 
					// left join t_gn_policy tgpc on tgin.PolicyId = tgpc.PolicyId 
					// left join t_gn_productplan tgpl on tgpc.ProductPlanId = tgpl.ProductPlanId 
					// left join t_gn_product tgpd on tgpl.ProductId = tgpd.ProductId 
					// left join t_gn_uploadreport tgup on tgup.UploadId = tcst.UploadId 
					
					// WHERE 1=1 
					// AND tgup.UploadId = ".$UploadId." 
					// AND tcst.CampaignId = ".$CampaignId."
					// AND tcst.CallReasonId IS NOT NULL AND DATE(tcst.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
					// AND DATE(tcst.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'";

					$sql = "select count(distinct ch.CustomerId) as Cases 
						from t_gn_callhistory ch
						inner join t_gn_customer cs on cs.CustomerId=ch.CustomerId
						where ch.CallHistoryCallDate >= '".$_REQUEST['start_date']." 00:00:00'
						and ch.CallHistoryCallDate <= '".$_REQUEST['end_date']." 23:00:00'
						and cs.UploadId = ".$UploadId."
						AND cs.CampaignId = ".$CampaignId."
						";
					break;
			}
			// if($cat="contacted"){
				// echo "<pre>".$sql."</pre>";
			// }
			$qry = mysql_query($sql);
			$row = mysql_fetch_array($qry);
			return $row[0];
			// return $sql;
		}
		
		function getCasesPerProduct($CampaignId,$prodId){
			$sql = "Select tgpd.ProductCode,count(tgcs.CustomerId) as jumlah,
					tgcs.CustomerFirstName,tgin.InsuredId,tgin.PolicyId,tgpc.ProductPlanId,tgpd.ProductCode,tgpd.product_category_id
					from t_gn_customer tgcs
					left join t_gn_insured tgin on tgcs.CustomerId = tgin.CustomerId
					left join t_gn_policy tgpc on tgin.PolicyId = tgpc.PolicyId
					left join t_gn_productplan tgpl on tgpc.ProductPlanId = tgpl.ProductPlanId
					left join t_gn_product tgpd on tgpl.ProductId = tgpd.ProductId
					
					LEFT JOIN t_gn_uploadreport tgur ON tgur.UploadId = tgcs.UploadId
					
					where tgcs.CallReasonId in (20,21) And tgin.InsuredId is not null
					
					AND DATE(tgcs.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
					AND DATE(tgcs.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					and tgcs.CampaignId = ".$CampaignId."
					and tgcs.UploadId = ".$prodId."
					group by tgpd.ProductCode";
			$qry = mysql_query($sql);
			while ($row = mysql_fetch_array($qry)){
				$oPerProduct[$row['product_category_id']][$row['ProductCode']] = $row['jumlah'];
			}
			return $oPerProduct;
		}
		
		//Query Index

		$sql = "SELECT DISTINCT(tgur.UploadDateTs) AS upload_date,\"\" AS source_db,\"\" AS campaig_type,
				tlct.Category AS Segmentasi,tgcm.CampaignId,tgcm.CampaignNumber,tgcm.CampaignName,
				COUNT(DISTINCT tgcs.CustomerId) AS `total_data`,tgur.UploadId
				FROM t_gn_campaign tgcm
				LEFT JOIN t_gn_customer tgcs ON tgcs.CampaignId = tgcm.CampaignId
				LEFT JOIN t_gn_uploadreport tgur ON tgur.UploadId = tgcs.UploadId
				LEFT JOIN t_lk_category tlct ON tlct.CategoryId = tgcm.CategoryId
				WHERE 1=1 AND tgcs.UploadId is not null ";
				if($_REQUEST['cmp'])
				{
					$sql.=" AND tgcm.CampaignId In ('".$campaign1."')";
				}
					//$sql.=" group by CampaignNumber ";
					$sql.=" GROUP BY tgur.UploadId";
					//$sql.="order by pr.ProductCode asc";
		
		$ListPages -> query($sql,$sql1);
		$ListPages -> result();
		//echo $ListPages -> getSQL();
		
	SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Preview Campaign Overview by History &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Date Range  &nbsp;: $start_date To $end_date<br/></th>";
		echo "<th> &nbsp;&nbsp;&nbsp;Report Date : $today </th>";
?>
</legend>
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<table width="99%" border="0" align="center">
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;No.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Upload DATE</th>
		<!-- t h nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Product</t h -->
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Source DB</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Campaign Type</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Segmentasi</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Campaign Name</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Campaign Number</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Total Data</th>
		<?php
			// $prodCode = getProductCode(1);
			// foreach($prodCode as $key => $val){
				// echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">&nbsp;".$val."</th>";
			// }
			$ProductCategory = getProductCategory();
			foreach($ProductCategory['category'] as $ProductCategoryId => $val){
				echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">&nbsp;Cases ".$val."</th>";
			}
		?>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Cases</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;APE</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Contact</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Attempt</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Not Touch</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Touch</th>
		
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;AVG APE/Cases</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Contact Rate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Response Rate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Conversion Rate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Attempt Ratio</th>
	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		
		$rowhours = 0;
		$no = (($ListPages -> start) + 1);
		
		$jam_getok = array();
		$jam_getok = jaammm();
		//var_dump($jam_getok);
		
		$leads1 = array();
		$leads1=leads();
		//var_dump($leads1);
		
		$solicited1 = array();
		$solicited1 = solicited();
		//var_dump($leads1);
		
		$contact1 = array();
		$contact1=contact();
		//var_dump($contact1);
		
		$termin = array();
		$termin=terminated();
		//var_dump($termin);
		
		$etem = array();
		$etem=attempt();
		//var_dump($etem);
		
		$tmr1 = array();
		$tmr1=tmr();
		//var_dump($tmr1);
		
		$sales12 = array();
		$sales12 = sales();
		//var_dump($sales12);
		
		$anp1 = array();
		$anp1 = anp();
		$Sumcategory = array();
		//var_dump($anp1);
		
		while($row = $db ->fetchrow($ListPages->result))
		{
			/** OUTPUT **/
			$oLeads			= ($leads1[$row ->CampaignNumber] ? $leads1[$row ->CampaignNumber] :0);
			$oSolicited		= ($solicited1[$row ->CampaignNumber] ? $solicited1[$row ->CampaignNumber] :0);
			// $oContact		= ($contact1[$row ->CampaignNumber] ? $contact1[$row ->CampaignNumber] :0);
			$oTerminLeads	= ($termin[$row ->CampaignNumber] ? $termin[$row ->CampaignNumber] :0);
			//$oAttempt		= ($etem[$row ->CampaignNumber] ? $etem[$row ->CampaignNumber] :0);
			$oHours			= ($jam_getok[$row ->CampaignNumber] ? $jam_getok[$row ->CampaignNumber] :0);
			$oTMR			= ($tmr1[$row->CampaignNumber] ? $tmr1[$row->CampaignNumber] :0);
			$oSales			= ($sales12[$row ->CampaignNumber] ? $sales12[$row ->CampaignNumber] :0);
			$oANP			= ($anp1[$row->CampaignNumber] ? $anp1[$row->CampaignNumber] :0);
			//new line
			$cases   	= getCases($row->CampaignId,$row->UploadId,"cases");
			$contact 	= getCases($row->CampaignId,$row->UploadId,"contacted");
			$nottouch	= getCases($row->CampaignId,$row->UploadId,"nottouch");
			$touch	 	= getCases($row->CampaignId,$row->UploadId,"touch");
			$perProd	= getCasesPerProduct($row->CampaignId,$row->UploadId);
			// echo getCases($row->CampaignId,$row->UploadId,"cases");
			$oCases			= ($cases ? $cases :0);
			$oContact		= ($contact ? $contact :0);
			$oTouch 		= ($touch ? $touch :0);
			// $oNotTouch		= ($nottouch ? $nottouch :0);
			$oNotTouch		= ($row->total_data-$oTouch);
			$oAttempt		= ($etem[$row ->UploadId] ? $etem[$row ->UploadId] :0);
			
			
			/** NGITUNG **/
			$LeadsRemain 	= ($leads1[$row ->CampaignNumber] ? ($leads1[$row ->CampaignNumber] - $termin[$row ->CampaignNumber]) :0);
			$LeadsAllocate	= ($leads1[$row ->CampaignNumber] ? ($leads1[$row ->CampaignNumber] / $tmr1[$row->CampaignNumber]) :0);
			$AvgPremium		= ($anp1[$row->CampaignNumber] ? (($anp1[$row->CampaignNumber] / $sales12[$row ->CampaignNumber]) / 12) :0);
			$ContactPersen	= ($contact1[$row ->CampaignNumber] ? (($contact1[$row ->CampaignNumber] / $solicited1[$row ->CampaignNumber]) * 100) :0);
			$CPH			= ($contact1[$row ->CampaignNumber] ? ($contact1[$row ->CampaignNumber] / $jam_getok[$row ->CampaignNumber]) :0);
			$SCR			= ($sales12[$row ->CampaignNumber] ? (($sales12[$row ->CampaignNumber] / $contact1[$row ->CampaignNumber]) * 100) :0);
			$SPH			= ($sales12[$row ->CampaignNumber] ? ($sales12[$row ->CampaignNumber] / $jam_getok[$row ->CampaignNumber]) :0);
			$AnpPh			= ($anp1[$row->CampaignNumber] ? ($anp1[$row->CampaignNumber] / $jam_getok[$row->CampaignNumber]) :0);
			$AnpPerTMR		= ($anp1[$row->CampaignNumber] ? ($anp1[$row->CampaignNumber] / $tmr1[$row->CampaignNumber]) :0);
			$SalesPerTMR	= ($sales12[$row ->CampaignNumber] ? ($sales12[$row ->CampaignNumber]/$tmr1[$row->CampaignNumber]) :0);
			$AARP			= ($anp1[$row->CampaignNumber] ? $anp1[$row->CampaignNumber] / $sales12[$row ->CampaignNumber] :0);
			
	?>
			<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->upload_date ; ?></td>
				<!-- t d nowrap style="text-align: center" class="content-middle"><?php //echo $row ->ProductCode; ?></t d -->
				<td nowrap style="text-align: center" class="content-middle">&nbsp;</td>
				<td nowrap style="text-align: center" class="content-middle">&nbsp;</td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Segmentasi ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignName ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignNumber ; ?></td>
				

				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->total_data ; ?></td>
				<?php
					// $prodCode = getProductCode(1);
					// foreach($prodCode as $key => $val){
						// echo "<td nowrap class=\"content-middle\" style=\"text-align: center;\">&nbsp;".($perProd[$val]==""?0:$perProd[$val])."</td>";
						// $sperProd[$val]+=$perProd[$val];
					// }
					
					// echo "<pre>";
					// print_r($perProd);
					// echo "</pre>";
					
					foreach($ProductCategory['product'] as $ProductCategoryId => $arrayval){
						$SumProduct[$ProductCategoryId] = 0;
						foreach($arrayval as $productid => $produccode){
							$SumProduct[$ProductCategoryId] += ($perProd[$ProductCategoryId][$produccode]==""?0:$perProd[$ProductCategoryId][$produccode]);
							
						}
						echo "<td nowrap class=\"content-middle\" style=\"text-align: center;\">&nbsp;".$SumProduct[$ProductCategoryId]."</td>";
						$Sumcategory[$row->CampaignId][$ProductCategoryId] += $SumProduct[$ProductCategoryId];
					}
					// echo "<pre>";
					// print_r($Sumcategory);
					// echo "</pre>";
				?>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oCases) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo formatRupiah($oANP) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oContact) ; ?></td>

				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oAttempt) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oNotTouch) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oTouch) ; ?></td>
				
				<td nowrap style="text-align: center" class="content-middle"><?php echo formatRupiah($oANP/$oCases);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND(($oContact/$row->total_data)*100,2). "%"; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND(($oCases/$row->total_data)*100,5). "%"; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND(($oCases/$oContact)*100,2). "%"; ?></td>
				<!--<td nowrap style="text-align: center" class="content-middle"><?php //echo ROUND(($oAttempt/$row->total_data)*100,2). "%"; ?></td>-->
				<!-- perubahan RUMUS 08-09-2017 attemtRatio -->
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND(($oAttempt/$oTouch),2); ?></td>
			</tr>	
	<?php
		/** KALKULASI **/
			$no++;
			$sTotalData		+= $row->total_data;
			$sCases			+= $oCases;
			$sANP 			+= $oANP;
			$sContact		+= $oContact;
			$sAttempt 		+= $oAttempt;
			$sNotTouch 		+= $oNotTouch;
			$sTouch			+= $oTouch;
			
			$tAVGNBI	   += ($oANP/$oCases);
			$tContactRate  += ($oContact/$row->total_data);
			$tResponseRate += ($oCases/$row->total_data);
			$tConversiRate += ($oCases/$oContact);
			// $tAttemptRatio += ($oAttempt/$row->total_data);
			//perubahan RUMUS 08-09-2017 attemtRatio
			$tAttemptRatio = ($sAttempt/$sTouch);
		};

		/* KALKULASI HITUNG */
		// $aLeadsRemain += ($aLeads -  $aTerminLeads);
		// $aLeadAllocate += ($aLeads / $aTMR);
		// $aAvgPremium += (($aANP / $aSales) / 12);
		// $aContactPersen += (($aContact / $aSolicited) * 100);
		// $aCPH += ($aContact / $aHours);
		// $aSCR += (($aSales / $aContact) * 100);
		// $aSPH += ($aSales / $aHours);
		// $aANPperPH += ($aANP / $aHours);
		// $aANPperTMR += ($aANP / $aTMR);
		// $aSalesperTMR += ($aSales / $aTMR);
		// $aAARP += ($aANP / $aSales);
	?>
		
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;" colspan="7">&nbsp;Subtotal</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($sTotalData) ; ?></th>
		<?php
					// $prodCode = getProductCode(1);
						// foreach($prodCode as $key => $val){
						// echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">&nbsp;".$sperProd[$val]."</th>";
					// }
				$totCategoryProduct = array();
				foreach($Sumcategory as $cmpid => $arr_val)
				{
					foreach($arr_val as $ProductCategoryId => $value)
					{
						$totCategoryProduct[$ProductCategoryId] += $value;
					}
				}
				foreach($ProductCategory['category'] as $ProductCategoryId => $productCategoryCode){
					echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">&nbsp;".$totCategoryProduct[$ProductCategoryId]."</th>";
				}
		?>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($sCases) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo formatRupiah($sANP) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($sContact) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($sAttempt) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($sNotTouch) ;  ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($sTouch) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo formatRupiah($tAVGNBI); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND(($tContactRate)*100,2)."%"; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND(($tResponseRate)*100,5)."%"; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND(($tConversiRate)*100,2)."%"; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND(($tAttemptRatio),2); ?></th>
	</tr>
	</div>
	</tbody>
</table>