<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$am  		= $_REQUEST['am'];
	$spv  		= $_REQUEST['spv'];
	$agt  		= $_REQUEST['agt'];
	$am_text  		= $_REQUEST['am_text'];
	$spv_text  		= $_REQUEST['spv_text'];
	$agt_text  		= $_REQUEST['agt_text'];
	// $cignasystem	= $_REQUEST['cignasystem'];
	// $campaign		= explode(",",$_REQUEST['cmp']);
	// $campaign1		= implode("','",$campaign);
	$today = date("Y-m-d");
	
	set_time_limit(500000);

		// $ListPages -> pages = $db -> escPost('v_page'); 
		// $ListPages -> setPage(10);
		
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
						AND DATE(c.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."'
						AND DATE(c.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
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
					GROUP BY ca.CampaignNumber ";
					
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
					GROUP BY b.CampaignNumber ";
					
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
						ca.CampaignNumber, ca.CampaignId, 
						COUNT(DISTINCT IF(a.CallReasonContactedFlag = 1,c.CustomerId,0)) AS Contact
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
						LEFT JOIN t_lk_callreason a ON c.CallReasonId = a.CallReasonId
					WHERE 1=1 
						AND DATE(c.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(c.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY ca.CampaignNumber ";
			//echo $sql;
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$contact[$row['CampaignNumber']] = $row['Contact'];
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
		
		//Fungsi Attempt
		function attempt()
		{
			global $db;	
			$attempt = array();
			$sql = "SELECT DISTINCT 
						d.CampaignNumber,
						COUNT(a.CallHistoryId) AS Attempt
					FROM t_gn_callhistory a
						LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
						LEFT JOIN tms_agent c on a.CreatedById=c.UserId
						LEFT JOIN t_gn_campaign d ON b.CampaignId = d.CampaignId
					WHERE 1=1
						AND DATE(a.CallHistoryCreatedTs)>= '".$_REQUEST['start_date']."' 
						AND DATE(a.CallHistoryCreatedTs)<= '".$_REQUEST['end_date']."'
					GROUP BY d.CampaignNumber";
			//echo $sql;
			$qry=mysql_query($sql);
			while($row = mysql_fetch_array($qry))
			{
				$attempt[$row['CampaignNumber']] = $row['Attempt'];
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
						LEFT JOIN t_gn_campaign f ON c.CampaignId = f.CampaignId
					WHERE 
						DATE(b.PolicySalesDate)>='".$_REQUEST['start_date']."' AND 
						DATE(b.PolicySalesDate)<='".$_REQUEST['end_date']."' AND 
						c.CallReasonId IN(15,16)
					GROUP BY f.CampaignNumber ";
			
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$anp[$row['CampaignNumber']] = $row['ANP'];
			}	
			return $anp;
		}
		
		//Query Index				
		$sql = "SELECT DISTINCT 
				agt.init_name as agent,
				pa.PolicyNumber AS policy_id,
				cst.CustomerUpdatedTs as effdt,
				py.PayerFirstName as payer_fname,
				date_format(py.PayerDOB, '%Y-%m-%d %H:%i:%s') as payer_dob,
				g.GenderShortCode as payer_sex,
				round((if(count(distinct ins.InsuredId)>1, 0.9, 1)*sum(plc.Premi)),0) as premium,
				round(if(prt.ProductType='PA',plc.Premi, if(pm.PayModeCode='M', if(count(distinct ins.InsuredId)>1, 0.9, 1)*12*sum(plc.Premi),if(count(distinct ins.InsuredId)>1, 0.9, 1)*sum(plc.Premi))),0) as nbi,
				pm.PayModeCode as bill_freq,
				prd.ProductCode AS product_id,
				cmp.CampaignNumber AS campaign_id,
				cst.NumberCIF AS prospect_id,
				agt.id as sellerid,
				spv.id as spv_id,
				am.id as atm_id,
				tsm.init_name as tsm_id,
				
				'' as policy_ref,
				cmp.CampaignNumber as campaign_TBSS,
				cst.CustomerUpdatedTs as input,
				
				'' as payer_cifno,
				s.Salutation as payer_title,
				
				py.PayerLastName as payer_lname,
				
				
				if(py.payeraddrtype=1,'HA', 'OA') as addrtype,
				py.PayerAddressLine1 AS addr1,
				py.PayerAddressLine2 AS addr2,
				py.PayerAddressLine3 AS addr3,
				py.PayerAddressLine4 AS addr4,
				py.PayerCity as city,
				py.PayerZipCode as post,
				pv.ProvinceCode as province,
				py.PayerHomePhoneNum as phone,
				py.PayerFaxNum as faxphone,
				py.PayerEmail as email,
				pt.PaymentTypeCode as pay_type,
				ct.CreditCardTypeCode as card_type,
				bk.BankName as bank,
				'' as branch,
				py.PayerCreditCardNum as acctnum,
				py.PayerCreditCardExpDate as ccexpdate,
				
				'' as question1,
				'' as question2,
				'' as question3,
				'' as question4,
				'' as question5,
				case prp.ProductPlan 
					when 1 then 'A'
					when 2 then 'B'
					when 3 then 'C'
					when 4 then 'D'
					when 5 then 'E'
					when 6 then 'F'
					when 7 then 'G'
					when 8 then 'H'
					when 9 then 'i'
					when 10 then 'J'
				end as benefit_level,
				
				'N' as export,
				now() AS exportdate,
				'' as canceldate,
				date_format(cst.CustomerRejectedDate,'%Y-%m-%d') as callDate2,
				0 as paystatus,
				'' as paynotes,
				'' as payauthcode,
				'' as paytransdate,
				'' as payorderno,
				'' as payccnum,
				'' as paycvv,
				'' as payexpdate,
				'IDR' as paycurency,
				'' as paycardtype,
				id.IdentificationType as payer_idtype,
				'' as payer_personalid,
				py.PayerMobilePhoneNum as payer_mobilephone,
				py.PayerOfficePhoneNum as payer_officephone,
				'' as deliverydate,
				'' as seperate_policy,
				1 as 'status',
				'' as payer_occupationid,
				'' as payer_birthplace,
				'' as payer_religionid,
				0 as payer_income,
				'' as payer_position,
				'' as payer_company,
				agt.init_name as operid,
				
				'' as pcifnumber,
				'' as pcardtype,
				'' as prefnumber,
				'' as paccnumber,
				py.PayerFirstName as paccname,
				'' as pcardnumber,
				'' as record_id,
				cst.CustomerUpdatedTs as callDate,
				cst.CustomerHomePhoneNum2 as phone2,
				cst.CustomerMobilePhoneNum2 as payer_mobilephone2,
				cst.CustomerWorkPhoneNum2 as payer_officephone2
				
				FROM t_gn_customer AS cst
				inner join t_gn_insured ins on ins.CustomerId = cst.CustomerId
				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId
				inner join t_gn_policyautogen pa on pa.PolicyNumber=plc.PolicyNumber
				inner join t_gn_payer py on py.CustomerId=cst.CustomerId
				inner JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
				inner JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
				inner JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
				inner JOIN tms_agent AS spv ON agt.spv_id = spv.UserId
				inner JOIN tms_agent AS am ON spv.spv_id = am.UserId
				inner JOIN tms_agent AS tsm ON tsm.handling_type=1
				left join t_lk_salutation s on s.SalutationId=py.SalutationId
				left join t_lk_gender g on g.GenderId=py.GenderId
				left join t_lk_province pv on pv.ProvinceId=py.ProvinceId
				left join t_lk_paymenttype pt on pt.PaymentTypeId=py.PaymentTypeId
				left join t_lk_creditcardtype ct on ct.CreditCardTypeId=py.CreditCardTypeId
				left join t_lk_bank bk on bk.BankId=py.PayersBankId
				left join t_lk_paymode pm on pm.PayModeId=prp.PayModeId
				left join t_lk_identificationtype id on id.IdentificationTypeId=py.IdentificationTypeId
				inner join t_lk_producttype prt on prt.ProductTypeId=prd.ProductTypeId
				WHERE cst.CallReasonQue =1
				AND agt.UserId = '".$agt."' 
				and date(cst.CustomerUpdatedTs) >= '".$start_date."'
				and date(cst.CustomerUpdatedTs) <= '".$end_date."'
				and ins.QCStatus=1
				group by pa.PolicyNumber
				order by pa.PolicyNumber";	

				// 	$sql.=" group by CampaignNumber ";
				// echo "$sql";
		
		$query = $db -> execute($sql,$sql1);
		// $db -> result();
			
	SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Agent Performance History &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Start Date  &nbsp;: $start_date<br/></th>";
		echo "<th> &nbsp;&nbsp;&nbsp;End Date  &nbsp;: $end_date<br/></th>";
		echo "<th> &nbsp;&nbsp;&nbsp;AM  &nbsp;: $am_text<br/></th>";
		echo "<th> &nbsp;&nbsp;&nbsp;SPV  &nbsp;: $spv_text<br/></th>";
		echo "<th> &nbsp;&nbsp;&nbsp;Agent  &nbsp;: $agt_text<br/></th>";
		echo "</br>";
?>

</legend>
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<table width="99%" border="0" align="center">
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;No.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Policy#</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Eff.Date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Holder Name</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Holder DOB</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Holder Sex</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Premium</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;APE</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Bill_freq</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Product#</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Multiple </th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Campaign#</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Prospect#</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;SellerId</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Agent</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;SPV</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;AM</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;TSM</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Status</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Remark</th>

	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		
		// $rowhours = 0;
		// $no = (($ListPages -> start) + 1);
		
		// $jam_getok = array();
		// $jam_getok = jaammm();
		// //var_dump($jam_getok);
		
		// $leads1 = array();
		// $leads1=leads();
		// //var_dump($leads1);
		
		// $solicited1 = array();
		// $solicited1 = solicited();
		// //var_dump($leads1);
		
		// $contact1 = array();
		// $contact1=contact();
		// //var_dump($contact1);
		
		// $termin = array();
		// $termin=terminated();
		// //var_dump($termin);
		
		// $etem = array();
		// $etem=attempt();
		// //var_dump($etem);
		
		// $tmr1 = array();
		// $tmr1=tmr();
		// //var_dump($tmr1);
		
		// $sales12 = array();
		// $sales12 = sales();
		// //var_dump($sales12);
		
		// $anp1 = array();
		// $anp1 = anp();
		// //var_dump($anp1);
		
		// //var_dump($end_date);
		
		// while($row = $db ->fetchrow($ListPages->result))
		// {
		// 	/** OUTPUT **/
		// 	$oLeads			= ($leads1[$row ->CampaignNumber] ? $leads1[$row ->CampaignNumber] :0);
		// 	$oSolicited		= ($solicited1[$row ->CampaignNumber] ? $solicited1[$row ->CampaignNumber] :0);
		// 	$oContact		= ($contact1[$row ->CampaignNumber] ? $contact1[$row ->CampaignNumber] :0);
		// 	$oTerminLeads	= ($termin[$row ->CampaignNumber] ? $termin[$row ->CampaignNumber] :0);
		// 	$oAttempt		= ($etem[$row ->CampaignNumber] ? $etem[$row ->CampaignNumber] :0);
		// 	$oHours			= ($jam_getok[$row ->CampaignNumber] ? $jam_getok[$row ->CampaignNumber] :0);
		// 	$oTMR			= ($tmr1[$row->CampaignNumber] ? $tmr1[$row->CampaignNumber] :0);
		// 	$oSales			= ($sales12[$row ->CampaignNumber] ? $sales12[$row ->CampaignNumber] :0);
		// 	$oANP			= ($anp1[$row->CampaignNumber] ? $anp1[$row->CampaignNumber] :0);
		
		// 	/** NGITUNG **/
		// 	$LeadsRemain 	= ($leads1[$row ->CampaignNumber] ? ($leads1[$row ->CampaignNumber] - $termin[$row ->CampaignNumber]) :0);
		// 	$LeadsAllocate	= ($leads1[$row ->CampaignNumber] ? ($leads1[$row ->CampaignNumber] / $tmr1[$row->CampaignNumber]) :0);
		// 	$AvgPremium		= ($anp1[$row->CampaignNumber] ? (($anp1[$row->CampaignNumber] / $sales12[$row ->CampaignNumber]) / 12) :0);
		// 	$ContactPersen	= ($contact1[$row ->CampaignNumber] ? (($contact1[$row ->CampaignNumber] / $solicited1[$row ->CampaignNumber]) * 100) :0);
		// 	$CPH			= ($contact1[$row ->CampaignNumber] ? ($contact1[$row ->CampaignNumber] / $jam_getok[$row ->CampaignNumber]) :0);
		// 	$SCR			= ($sales12[$row ->CampaignNumber] ? (($sales12[$row ->CampaignNumber] / $contact1[$row ->CampaignNumber]) * 100) :0);
		// 	$SPH			= ($sales12[$row ->CampaignNumber] ? ($sales12[$row ->CampaignNumber] / $jam_getok[$row ->CampaignNumber]) :0);
		// 	$AnpPh			= ($anp1[$row->CampaignNumber] ? ($anp1[$row->CampaignNumber] / $jam_getok[$row->CampaignNumber]) :0);
		// 	$AnpPerTMR		= ($anp1[$row->CampaignNumber] ? ($anp1[$row->CampaignNumber] / $tmr1[$row->CampaignNumber]) :0);
		// 	$SalesPerTMR	= ($sales12[$row ->CampaignNumber] ? ($sales12[$row ->CampaignNumber]/$tmr1[$row->CampaignNumber]) :0);
		// 	$AARP			= ($anp1[$row->CampaignNumber] ? $anp1[$row->CampaignNumber] / $sales12[$row ->CampaignNumber] :0);
			
	?>
			<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
			<?php 
				$no=1;
				while($row = $db -> fetchrow($query) ){ ?>
			
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->policy_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->effdt ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_fname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_dob ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->premium ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->nbi ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bill_freq ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->product_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ''; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->campaign_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->prospect_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sellerid ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->agent ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->spv_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->atm_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->tsm_id ; ?></td>
			</tr>
			<?php 
				$no++;
			} ?>	
	<?php
		/** KALKULASI **/
		// $no++;
		// $aLeads 		+= $oLeads;
		// $aSolicited 	+= $oSolicited;
		// $aContact 		+= $oContact;
		// $aTerminLeads 	+= $oTerminLeads;
		// $aAttempt 		+= $oAttempt;
		// $aHours 		+= $oHours;
		// $aTMR 			+= $oTMR;
		// $aSales 		+= $oSales;
		// $aANP 			+= $oANP;
		// };
		
		// /* KALKULASI HITUNG */
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
		
	
	</div>
	</tbody>
</table>


