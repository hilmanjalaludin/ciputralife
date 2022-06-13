<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$rpttype	= $_REQUEST['rpttype'];
	$campaign	= $_REQUEST['cmp'];
	$spv	= $_REQUEST['spv'];
	$tm	= $_REQUEST['agt'];
	
	header("Content-type: application/vnd-ms-excel");
	$name		="MIS_RCT(ByAgent-ClosingQA)";
	$file		=".xls";
	$sdate		=$start_date;
	$filename 	= $name.$sdate."To".$end_date.$file;
	
	header("Content-Disposition: attachment; filename=".($filename));
	
	
	
//	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
	
	function followup(){
		global $db;
		
		$sql =" select agt.id as agentid,count(cst.CustomerId) as slc
				from t_gn_customer cst
				left join t_gn_campaign cmp on cmp.campaignid = cst.campaignid
				left join tms_agent agt on agt.UserId = cst.SellerId
				WHERE date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
				AND cmp.campaignstatusflag = 1
				group by agt.UserId ";
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$flw[$row['agentid']] += $row['slc'];
		}
		
		return $flw;
		
	}	
	
	function anp(){
		global $db;
		
		$sql =" SELECT  cmp.CampaignId,
						cmp.CampaignNumber AS Campaign_Id,
						agt.UserId AS agtuid,
						agt.id AS agentid,
						agt.full_name AS Agent_Name,
						spv.id AS spvid,
						spv.UserId AS spvuid,
						spv.full_name AS spv_name,
						sum(if(prp.PremiumGroupId=2,1,0)) as main,
						sum(if(prp.PremiumGroupId=3,1,0)) as sp,
						sum(if(prp.PremiumGroupId=1,1,0)) as dp,
						sum(if(prp.PayModeId=2,prp.ProductPlanPremium*12,0)+if(prp.PayModeId=1,prp.ProductPlanPremium,0)) as allpremium
						FROM
						t_gn_customer AS cst
						LEFT JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
						LEFT JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
						LEFT JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
						LEFT JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
						LEFT JOIN t_gn_product AS prd ON prd.ProductId = prp.ProductId
						LEFT JOIN t_gn_callhistory AS clh ON clh.CustomerId = cst.CustomerId AND clh.CallHistoryCallDate = cst.CustomerUpdatedTs
						LEFT JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
						LEFT JOIN tms_agent AS spv ON spv.UserId = agt.spv_id
						LEFT JOIN t_lk_callreason crs ON crs.CallReasonId = cst.CallReasonId
						WHERE	cst.CallReasonId IN (37,38)
						AND date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
						AND spv.UserId like '%".$_REQUEST['spv']."%'
						AND agt.UserId like '%".$_REQUEST['agt']."%'
						AND cmp.CampaignNumber like '%".$_REQUEST['cmp']."%'
						AND cmp.campaignstatusflag = 1
						Group By agt.UserId
						ORDER by cst.CustomerId ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anp[$row['agentid']] += $row['allpremium'];
		}
		
		return $anp;
		
	}
	
	function sp(){
		global $db;
		
		$sql =" SELECT  cmp.CampaignId,
						cmp.CampaignNumber AS Campaign_Id,
						agt.UserId AS agtuid,
						agt.id AS agentid,
						agt.full_name AS Agent_Name,
						spv.id AS spvid,
						spv.UserId AS spvuid,
						spv.full_name AS spv_name,
						sum(if(prp.PremiumGroupId=2,1,0)) as main,
						sum(if(prp.PremiumGroupId=3,1,0)) as sp,
						sum(if(prp.PremiumGroupId=1,1,0)) as dp,
						sum(if(prp.PayModeId=2,prp.ProductPlanPremium*12,0)+if(prp.PayModeId=1,prp.ProductPlanPremium,0)) as allpremium
						FROM
						t_gn_customer AS cst
						LEFT JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
						LEFT JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
						LEFT JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
						LEFT JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
						LEFT JOIN t_gn_product AS prd ON prd.ProductId = prp.ProductId
						LEFT JOIN t_gn_callhistory AS clh ON clh.CustomerId = cst.CustomerId AND clh.CallHistoryCallDate = cst.CustomerUpdatedTs
						LEFT JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
						LEFT JOIN tms_agent AS spv ON spv.UserId = agt.spv_id
						LEFT JOIN t_lk_callreason crs ON crs.CallReasonId = cst.CallReasonId
						WHERE	cst.CallReasonId IN (37,38)
						AND date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
						AND spv.UserId like '%".$_REQUEST['spv']."%'
						AND agt.UserId like '%".$_REQUEST['agt']."%'
						AND cmp.CampaignNumber like '%".$_REQUEST['cmp']."%'
						AND cmp.campaignstatusflag = 1
						Group By agt.UserId
						ORDER by cst.CustomerId ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$sp[$row['agentid']] += $row['sp'];
		}
		
		return $sp;
		
	}
	
	
	function dp(){
		global $db;
		
		$sql =" SELECT  cmp.CampaignId,
						cmp.CampaignNumber AS Campaign_Id,
						agt.UserId AS agtuid,
						agt.id AS agentid,
						agt.full_name AS Agent_Name,
						spv.id AS spvid,
						spv.UserId AS spvuid,
						spv.full_name AS spv_name,
						sum(if(prp.PremiumGroupId=2,1,0)) as main,
						sum(if(prp.PremiumGroupId=3,1,0)) as sp,
						sum(if(prp.PremiumGroupId=1,1,0)) as dp,
						sum(if(prp.PayModeId=2,prp.ProductPlanPremium*12,0)+if(prp.PayModeId=1,prp.ProductPlanPremium,0)) as allpremium
						FROM
						t_gn_customer AS cst
						LEFT JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
						LEFT JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
						LEFT JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
						LEFT JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
						LEFT JOIN t_gn_product AS prd ON prd.ProductId = prp.ProductId
						LEFT JOIN t_gn_callhistory AS clh ON clh.CustomerId = cst.CustomerId AND clh.CallHistoryCallDate = cst.CustomerUpdatedTs
						LEFT JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
						LEFT JOIN tms_agent AS spv ON spv.UserId = agt.spv_id
						LEFT JOIN t_lk_callreason crs ON crs.CallReasonId = cst.CallReasonId
						WHERE	cst.CallReasonId IN (37,38)
						AND date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
						AND spv.UserId like '%".$_REQUEST['spv']."%'
						AND agt.UserId like '%".$_REQUEST['agt']."%'
						AND cmp.CampaignNumber like '%".$_REQUEST['cmp']."%'
						AND cmp.campaignstatusflag = 1
						Group By agt.UserId
						ORDER by cst.CustomerId ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$dp[$row['agentid']] += $row['dp'];
		}
		
		return $dp;
		
	}
	
	
	function callattempt(){
		global $db;
		
		$sql =" select agt.id as agentid, agt.UserId as ageninit,sum(if(clh.CallHistoryCallDate is not null,1,0)) as attempted
				from t_gn_callhistory clh
				left join tms_agent agt on agt.UserId = clh.CreatedById
				left join t_gn_customer cst on cst.CustomerId = clh.CustomerId
				where date(clh.CallHistoryCallDate) = date(cst.CustomerUpdatedTs)
				AND date(cst.CustomerUpdatedTs) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
				GROUP BY agt.id ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$attmp[$row['agentid']] += $row['attempted'];
		}
		
		return $attmp;
		
	}
	
	
	function asgn(){
		global $db;
		
		$sql =" select agt.id as agt,count(asg.AssignId) as supply,
				date(asg.AssignDate) as dateassign
				from t_gn_assignment asg
				left join t_gn_customer cst on cst.CustomerId = asg.CustomerId
				left join t_gn_campaign cmp on cmp.CampaignId = cst.CampaignId
				left join tms_agent agt on agt.UserId = asg.AssignSelerId
				WHERE cmp.CampaignStatusFlag = 1
				GROUP BY agt.id ";
				
				/*
				WHERE date(if(cmp.CampaignEndDate is not null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >= '".$_REQUEST['start_date']."'
				OR date(if(cmp.CampaignEndDate is not null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >='".$_REQUEST['end_date']."'
				*/
				
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_array($qry)){
			$followup[$row['agt']] += $row['supply'];
		}
		
		return $followup;
	
	}
		
		
	
		$sql = " select cmp.campaignnumber AS campaignnumber,
				cmp.campaignname AS campaignname ,
				DATE_FORMAT((cmp.campaignstartdate),'%Y-%m-%d') AS uploaddate,
				agt.UserId as agtUserId,
				spv.id as spvid,
				spv.full_name AS spv,
				agt.id as agentid,
				agt.full_name AS agent,
				sum(if(cst.CallReasonId =2,1,0)) as Fax,
				sum(if(cst.CallReasonId =4,1,0)) as Overage,
				sum(if(cst.CallReasonId =5,1,0)) as ChangeOwner,
				sum(if(cst.CallReasonId =6,1,0)) as AlreadyMoved,
				sum(if(cst.CallReasonId =7,1,0)) as Supplement,
				sum(if(cst.CallReasonId =8,1,0)) as DoubleCall,
				sum(if(cst.CallReasonId =9,1,0)) as CardClose,
				sum(if(cst.CallReasonId =10,1,0)) as NobodyPicksUp,
				sum(if(cst.CallReasonId =11,1,0)) as Countryside,
				sum(if(cst.CallReasonId =12,1,0)) as BusyLine,
				sum(if(cst.CallReasonId =13,1,0)) as MissCustomer,
				sum(if(cst.CallReasonId =14,1,0)) as Thinking,
				sum(if(cst.CallReasonId =15,1,0)) as CallBackLater,
				sum(if(cst.CallReasonId =37,1,0)) as Interested,
				sum(if(cst.CallReasonId =38,1,0)) as InterestedWithSpouse,
				sum(if(cst.CallReasonId =18,1,0)) as NotInterested,
				sum(if(cst.CallReasonId =19,1,0)) as AlreadyCIGNAProduct,
				sum(if(cst.CallReasonId =20,1,0)) as AlreadyInsuredCompatitor,
				sum(if(cst.CallReasonId =21,1,0)) as Premiumtoohigh,
				sum(if(cst.CallReasonId =22,1,0)) as NoDependant,
				sum(if(cst.CallReasonId =23,1,0)) as NeedHighBenefit,
				sum(if(cst.CallReasonId =24,1,0)) as PremiumIncludeInstallment,
				sum(if(cst.CallReasonId =25,1,0)) as AskingInvesmentProduct,
				sum(if(cst.CallReasonId =26,1,0)) as RejectUpFront,
				sum(if(cst.CallReasonId =27,1,0)) as Interestbutnocreditcard,
				sum(if(cst.CallReasonId =28,1,0)) as Intrestbutpaymentmechanismissue,
				sum(if(cst.CallReasonId =29,1,0)) as InterestedbutCChasbeenexpired,
				sum(if(cst.CallReasonId =30,1,0)) as InterestbutCardNotReceivedYet,
				sum(if(cst.CallReasonId =36,1,0)) as InvalidPhoneNumber,
				sum(if(cst.CallReasonId =43,1,0)) as NoNeedInsurance,
				sum(if(cst.CallReasonId =44,1,0)) as ThinkingAddNewNumber,
				sum(if(cst.CallReasonId =18,1,0)) as NotInterestedOther
				from t_gn_assignment asg
				left join t_gn_customer cst on cst.CustomerId = asg.CustomerId
				left join t_gn_campaign cmp on cmp.CampaignId = cst.CampaignId
				left join tms_agent agt on agt.UserId = asg.AssignSelerId
				left join tms_agent spv on spv.UserId = agt.spv_id
				WHERE date(cst.customerupdatedts) BETWEEN '$start_date' AND '$end_date'
				AND asg.AssignBlock = 0
				AND cmp.campaignnumber like '%$campaign%'
				AND cmp.CampaignStatusFlag = 1
				AND spv.UserId like '%$spv%'
				AND agt.UserId like '%$tm%'
				group by agt.UserId 
				ORDER BY cst.CustomerId";
 
			//print_r($_REQUEST);
			
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$query		= $db ->execute($sql,__FILE__,__LINE__); 
		
		$ListPages -> query($sql);
		$ListPages -> result();
	
	
	SetNoCache();

?>			
<fieldset class="corner">
<legend style="color: teal;"> &nbsp;&nbsp;&nbsp;Download RCT (Per Agent - Closing QA)&nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Interval : $start_date To $end_date</th>";
?>
</legend>
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div class="box-shadow" style="width:1115px;height:auto;overflow:auto; border:"black">
	<table width="99%" bordercolor="black" align="center">
	<tr>
					<tr>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>No</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>Agent ID</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>Agent Name</th>
					<!--<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>Date Upload</th>-->
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="3" colspan="5" nowrap>Database</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="14" nowrap >Solicited Result</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="14" nowrap >Contacted Result</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="42" nowrap >Presentation Result</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" rowspan="2" nowrap >Sales Result</th>
					</tr>
					<tr>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" rowspan="2" nowrap>Not Connect</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" rowspan="3" nowrap>Connected (Total)</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="6" rowspan="2" nowrap>Not Contact</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" rowspan="3" nowrap>Contacted (Total)</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="14" rowspan="2" nowrap>Unable To do Presentatation</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" rowspan="3" nowrap>Done Presentation (Total)</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="10" nowrap>Customer Response</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="30" rowspan="2" nowrap>Not Interested Reasons</th>
					</tr>
					<tr>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" nowrap ></th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="6" nowrap>Interested ( PIF )</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="3" nowrap >RR %</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="3" nowrap>SCR %</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="3" nowrap>ANP</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="3" nowrap>AVG</th>
					</tr>
					<tr>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" nowrap >Supply</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap>Solicited</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Call Attempted</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" colspan="2" nowrap >Fax</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Invalid Phone</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Busy Line</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >NPU</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Miss Cust</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >RUF</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >CBL</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Overage</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Already Moved</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Supplement</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Double Call</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Country Side</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Thinking</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Thinking Add New Number</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Holder</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Spouse</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Dependant</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Not Interested</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Already had CIGNA</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >No Need Insurance</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Account Closed</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Owner Changed</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Already Insured With Compatitor</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Premium Too High</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >No Dependant</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Need High Benefit</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Premium Include Installment</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Asking Invesment Product</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Interest But No Credit Card</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Interest But Payment Mechanism Issue</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Interest But Credit Card Has Been Expired</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Interest But Card Not Received Yet</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Not Interested Other</th>
					</tr>
					<tr>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Ratio</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		$Tots_cmprow_count=0;
		$Tots_slc_count=0;
		$Tots_persen_slc_count=0;
		$Tots_attempt_count=0;
		$Tots_fax=0;
		$Tots_invalid=0;
		$Tots_busy=0;
		$Tots_npu=0;
		$Tots_misscust=0;
		$Tots_ruf=0;
		$Tots_cbl=0;
		$Tots_ovg=0;
		$Tots_alreadymoved=0;
		$Tots_supplement=0;
		$Tots_doublecall=0;
		$Tots_countryside=0;
		$Tots_thinking=0;
		$Tots_thingkingaddnew=0;
		$Tots_holder=0;
		$Tots_sp=0;
		$Tots_dp=0;
		$Tots_alreadycigna=0;
		$Tots_noneedinsurance=0;
		$Tots_accountclose=0;
		$Tots_ownerchange=0;
		$Tots_alreadycompatitor=0;
		$Tots_premiumtoohigh=0;
		$Tots_nodependant=0;
		$Tots_needhighbenefit=0;
		$Tots_premiumincludeins=0;
		$Tots_askinginvestment=0;
		$Tots_intbutnocc=0;
		$Tots_intbutpayissue=0;
		$Tots_intbutccexp=0;
		$Tots_intbutccnotrcv=0;
		$Tots_notint=0;
		$Tots_anp=0;
		$Tots_avg=0;
		
		$slc_count=0;
		$attempt_count=0;
		$cmprow_count=0;
		$anp_cnt=0;
		$cnt_sp= 0;
		$cnt_dp = 0;
		$prm = 0;
		$solicited = 0;
		$ratio [$row -> campaignnumber]= 0;
		$persenspl [$row -> campaignnumber]= 0;
		$persenFax[$row -> campaignnumber]= 0;
		$persenOverage[$row -> campaignnumber]= 0;
		$persenChangeOwner[$row -> campaignnumber]= 0;
		$persenAlreadyMoved[$row -> campaignnumber]= 0;
		$persenSupplement[$row -> campaignnumber]= 0;
		$persenDoubleCall[$row -> campaignnumber]= 0;
		$persenCardClose[$row -> campaignnumber]= 0;
		$persenNobodyPicksUp[$row -> campaignnumber]= 0;
		$persenCountryside[$row -> campaignnumber]= 0;
		$persenBusyLine[$row -> campaignnumber]= 0;
		$persenMissCustomer[$row -> campaignnumber]= 0;
		$persenThinking[$row -> campaignnumber]= 0;
		$persenThinkingAddNewNumber[$row -> campaignnumber]= 0;
		$persenCallBackLater[$row -> campaignnumber]= 0;
		$persenInterested[$row -> campaignnumber]= 0;
		$persenInterestedWithSpouse[$row -> campaignnumber]= 0;
		$persenNotInterested[$row -> campaignnumber]= 0;
		$persenNotInterestedOther[$row -> campaignnumber]= 0;
		$persenAlreadyCIGNAProduct[$row -> campaignnumber]= 0;
		$persenNoNeedInsurance[$row -> campaignnumber]= 0;
		$persenAlreadyInsuredCompatitor[$row -> campaignnumber]= 0;
		$persenPremiumtoohigh[$row -> campaignnumber]= 0;
		$persenNoDependant[$row -> campaignnumber]= 0;
		$persenNoNeedInsurance[$row -> campaignnumber]= 0;
		$persenNeedHighBenefit[$row -> campaignnumber]= 0;
		$persenPremiumIncludeInstallment[$row -> campaignnumber]= 0;
		$persenAskingInvesmentProduct[$row -> campaignnumber]= 0;
		$persenRejectUpFront[$row -> campaignnumber]= 0;
		$persenInterestbutnocreditcard[$row -> campaignnumber]= 0;
		$persenIntrestbutpaymentmechanismissue[$row -> campaignnumber]= 0;
		$persenInterestedbutCChasbeenexpired[$row -> campaignnumber]= 0;
		$persenInterestbutCardNotReceivedYet[$row -> campaignnumber]= 0;
		$persenInvalidPhoneNumber[$row -> campaignnumber]= 0;
		$totalconnected [$row -> campaignnumber]=0;
		$totalpersenconnected [$row -> campaignnumber] =0;
		$totalcontacted [$row -> campaignnumber] =0;
		$totalNotInterested [$row -> campaignnumber] =0;
		$totalInterested [$row -> campaignnumber] =0;

		
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
				
			
			
			//
			$attempt_count= callattempt();
			$anp_cnt = anp();
			//$solicited = followup();
			$cnt_sp	= sp();
			$slc_count=followup();
			$cmprow_count = asgn();
			$cnt_dp = dp();
			//$prm = fanp();
			//
			
			$persenflw [$row -> agentid]= (($slc_count[$row->agentid])/($cmprow_count[$row->agentid]))*100;
			
			
			$persenInvalidPhoneNumber [$row -> agentid]= (($row -> InvalidPhoneNumber) / ($slc_count[$row->agentid]))*100;
			$persenBusyLine [$row -> agentid]= (($row -> BusyLine)/($slc_count[$row->agentid]))*100;
			$persenNobodyPicksUp [$row -> agentid]= (($row -> NobodyPicksUp) / ($slc_count[$row->agentid]))*100;
			$persenMissCustomer [$row -> agentid]= (($row -> MissCustomer) / ($slc_count[$row->agentid]))*100;
			$persenChangeOwner [$row -> agentid]= ($slc_count[$row->agentid] / $row -> supply)*100;
			$persenInterestedWithSpouse [$row -> agentid]= (($slc_count[$row->agentid])/($row -> supply))*100;
			//
			$ratio [$row -> agentid]= (($attempt_count[$row->agentid]) /($slc_count[$row->agentid])) ;
			$persenslc[$row->agentid]= (($row -> attmp)/($slc_count[$row->agentid]))*100;
			$persenFax [$row -> agentid]= $row -> Fax /$slc_count[$row->agentid];
			//
			$totalconnected [$row -> agentid]=$slc_count[$row->agentid] - ($row -> Fax) - ($row -> InvalidPhoneNumber);
			$totalpersenconnected [$row -> agentid]=(($totalconnected [$row -> agentid])/($slc_count[$row->agentid]))*100;
			$totalcontacted [$row -> agentid]=(($totalconnected [$row -> agentid]))-($row -> BusyLine)-($row -> NobodyPicksUp)-($row -> MissCustomer);
			$totalpersencontacted [$row -> agentid]=(($totalcontacted [$row -> agentid])/($slc_count[$row->agentid]))*100;
			$persenRejectUpFront [$row -> agentid]= ($row -> RejectUpFront / $totalcontacted [$row -> agentid])*100;
			$persenCallBackLater [$row -> agentid]= ($row -> CallBackLater / $totalcontacted [$row -> agentid])*100;
			$persenOverage [$row -> agentid]= ($row -> Overage / $totalcontacted [$row -> agentid])*100;
			$persenAlreadyMoved [$row -> agentid]= ($row -> AlreadyMoved / $totalcontacted [$row -> agentid])*100;
			$persenSupplement [$row -> agentid]= ($row -> Supplement / $totalcontacted [$row -> agentid])*100;
			$persenDoubleCall [$row -> agentid]= ($row -> DoubleCall / $totalcontacted [$row -> agentid])*100;
			$persenCountryside [$row -> agentid]= ($row -> Countryside / $totalcontacted [$row -> agentid])*100;
			//
			$totalRef1DonePresentastion [$row -> agentid] = (($row ->RejectUpFront)+($row ->CallBackLater)+($row ->Overage));
			$totalRef2DonePresentastion [$row -> agentid] = (($row ->AlreadyMoved)+($row ->Supplement)+($row ->DoubleCall)+($row ->Countryside));
			$totalRefDonePresentastion [$row -> agentid] = ($totalRef1DonePresentastion [$row -> agentid])+($totalRef2DonePresentastion [$row -> agentid]);
			$totalDonePresentation [$row -> agentid]= ($totalcontacted [$row -> agentid])- ($totalRefDonePresentastion [$row -> agentid]);
			$totalpersenDonePresentation [$row -> agentid] = (($totalDonePresentation [$row -> agentid])/($totalcontacted [$row -> agentid]))*100;
			$persenThinking [$row -> agentid]= (($row -> Thinking) / ($totalDonePresentation [$row -> agentid]))*100;
			$persenThinkingAddNewNumber[$row -> agentid]= (($row -> ThinkingAddNewNumber)/($totalDonePresentation [$row -> agentid]))*100;
			
			$totalInterested [$row -> agentid] =(($row -> Interested)+($row ->InterestedWithSpouse));
			$persenInterested [$row -> agentid]= (($totalInterested [$row -> agentid]) / ($totalDonePresentation [$row -> agentid]))*100;
			$pif [$row -> agentid] = ($totalInterested [$row -> agentid]);
			//
			$totalNotInterested [$row -> agentid] = (($totalDonePresentation [$row -> agentid])-($row -> Thinking)-($pif [$row -> agentid]));
			$persenNotInterested [$row -> agentid]= (($totalNotInterested [$row -> agentid]) / ($totalDonePresentation [$row -> agentid]))*100;
			$persenAlreadyCIGNAProduct [$row -> agentid]= (($row -> AlreadyCIGNAProduct) / ($totalNotInterested [$row -> agentid]))*100;
			$persenNoNeedInsurance [$row -> agentid]= (($row -> NoNeedInsurance) / ($totalNotInterested [$row -> agentid]))*100;
			$persenCardClose [$row -> agentid]= (($row -> CardClose) / ($totalNotInterested [$row -> agentid]))*100;
			$persenChangeOwner [$row -> agentid]= (($row -> ChangeOwner) / ($totalNotInterested [$row -> agentid]))*100;
			$persenAlreadyInsuredCompatitor [$row -> agentid]= (($row -> AlreadyInsuredCompatitor) / ($totalNotInterested [$row -> agentid]))*100;
			$persenPremiumtoohigh [$row -> agentid]= (($row -> Premiumtoohigh) / ($totalNotInterested [$row -> agentid]))*100;
			$persenNoDependant [$row -> agentid]= (($row -> NoDependant) / ($totalNotInterested [$row -> agentid]))*100;
			$persenNeedHighBenefit [$row -> agentid]= (($row -> NeedHighBenefit) / ($totalNotInterested [$row -> agentid]))*100;
			$persenPremiumIncludeInstallment [$row -> agentid]= (($row -> PremiumIncludeInstallment) / ($totalNotInterested [$row -> agentid]))*100;
			$persenAskingInvesmentProduct [$row -> agentid]= (($row -> AskingInvesmentProduct) / ($totalNotInterested [$row -> agentid]))*100;
			$persenInterestbutnocreditcard [$row -> agentid]= (($row -> Interestbutnocreditcard) / ($totalNotInterested [$row -> agentid]))*100;
			$persenIntrestbutpaymentmechanismissue [$row -> agentid]= (($row -> Intrestbutpaymentmechanismissue) / ($totalNotInterested [$row -> agentid]))*100;
			$persenInterestedbutCChasbeenexpired [$row -> agentid]= (($row -> InterestedbutCChasbeenexpired) / ($totalNotInterested [$row -> agentid]))*100;
			$persenInterestbutCardNotReceivedYet [$row -> agentid]= (($row -> InterestbutCardNotReceivedYet) / ($totalNotInterested [$row -> agentid]))*100;
			$persenNotInterestedOther [$row -> agentid]= (($row -> NotInterestedOther)/($totalNotInterested [$row -> agentid]))*100;
			$RR [$row -> agentid] = ($pif [$row -> agentid] / $slc_count[$row->agentid])*100;
			$SCR [$row -> agentid] = ($pif [$row -> agentid] / $totalcontacted [$row -> agentid])*100;
			$ANP [$row -> agentid] = $row -> allpremium1 + $row -> allpremium2 + $row -> allpremium3;
			$AVG [$row -> agentid] =  $anp_cnt[$row->agentid] / $pif [$row -> agentid] / 12;
			
			// totals
			$Tots_cmprow_count+=$cmprow_count[$row->agentid];
			$Tots_slc_count+=$slc_count[$row->agentid];
			$Tots_persen_slc_count+=($slc_count[$row->agentid]/$cmprow_count[$row->agentid]);
			$Tots_attempt_count+=$attempt_count[$row->agentid];
			$Tots_fax+= $row -> Fax;
			$Tots_invalid+=$row -> InvalidPhoneNumber;
			$Tots_busy+=$row -> BusyLine;
			$Tots_npu+=$row -> NobodyPicksUp;
			$Tots_misscust+=$row -> MissCustomer;
			$Tots_ruf+=$row -> RejectUpFront;
			$Tots_cbl+=$row -> CallBackLater;
			$Tots_ovg+=$row -> Overage;
			$Tots_alreadymoved+=$row ->AlreadyMoved;
			$Tots_supplement+=$row ->Supplement;
			$Tots_doublecall+=$row ->DoubleCall;
			$Tots_countryside+=$row ->Countryside;
			$Tots_thingking+=$row->Thinking;
			$Tots_thingkingaddnew+=$row->ThinkingAddNewNumber;
			$Tots_holder+=$totalInterested [$row -> agentid];
			$Tots_sp+=$cnt_sp[$row->agentid];
			$Tots_dp+=$cnt_dp[$row->agentid];
			$Tots_alreadycigna+=$row -> AlreadyCIGNAProduct;
			$Tots_noneedinsurance+=$row -> NoNeedInsurance;
			$Tots_accountclose+=$row ->CardClose;
			$Tots_ownerchange+=$row ->ChangeOwner;
			$Tots_alreadycompatitor+=$row ->AlreadyInsuredCompatitor;
			$Tots_premiumtoohigh+=$row ->Premiumtoohigh;
			$Tots_nodependant+=$row ->NoDependant;
			$Tots_needhighbenefit+=$row ->NeedHighBenefit;
			$Tots_premiumincludeins+=$row ->PremiumIncludeInstallment;
			$Tots_askinginvestment+=$row ->AskingInvesmentProduct;
			$Tots_intbutnocc+=$row ->Interestbutnocreditcard;
			$Tots_intbutpayissue+=$row ->Intrestbutpaymentmechanismissue;
			$Tots_intbutccexp+=$row ->InterestedbutCChasbeenexpired;
			$Tots_intbutccnotrcv+=$row ->InterestbutCardNotReceivedYet;
			$Tots_notint+=$row ->NotInterestedOther;
			$Tots_anp+=$anp_cnt[$row->agentid];
			
	?>
			<div class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> agentid ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> agent ; ?></td>
				<!--<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> uploaddate ; ?></td>-->
				<td nowrap style="text-align: center" class="content-middle"><?php echo $cmprow_count[$row->agentid]; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $slc_count[$row->agentid]; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenflw [$row -> agentid],2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $attempt_count[$row->agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($ratio[$row->agentid],2); ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Fax;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenFax [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InvalidPhoneNumber;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInvalidPhoneNumber [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalconnected [$row -> agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersenconnected [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->BusyLine;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenBusyLine [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NobodyPicksUp;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNobodyPicksUp [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->MissCustomer;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenMissCustomer [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalcontacted [$row -> agentid];?></td> 
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersencontacted [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->RejectUpFront;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenRejectUpFront [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CallBackLater;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCallBackLater [$row -> agentid],2)?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Overage;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenOverage [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyMoved;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyMoved [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Supplement;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenSupplement [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->DoubleCall;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenDoubleCall [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Countryside;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCountryside [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalDonePresentation [$row -> agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersenDonePresentation [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Thinking;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenThinking [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ThinkingAddNewNumber;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenThinkingAddNewNumber[$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalInterested [$row -> agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterested [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $cnt_sp[$row->agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $cnt_dp[$row->agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalNotInterested [$row -> agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNotInterested [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyCIGNAProduct;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyCIGNAProduct [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NoNeedInsurance;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNoNeedInsurance[$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CardClose;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCardClose [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ChangeOwner;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenChangeOwner [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyInsuredCompatitor;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyInsuredCompatitor [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Premiumtoohigh;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenPremiumtoohigh [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NoDependant;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNoDependant [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NeedHighBenefit;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNeedHighBenefit [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->PremiumIncludeInstallment;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenPremiumIncludeInstallment [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AskingInvesmentProduct;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAskingInvesmentProduct [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Interestbutnocreditcard;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestbutnocreditcard [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Intrestbutpaymentmechanismissue;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenIntrestbutpaymentmechanismissue [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InterestedbutCChasbeenexpired;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedbutCChasbeenexpired [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InterestbutCardNotReceivedYet;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestbutCardNotReceivedYet [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NotInterestedOther;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNotInterestedOther [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($RR [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($SCR [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $anp_cnt[$row->agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($AVG [$row -> agentid],2);?></td>
			</tr>
			
		</div>
</tbody>
	<?php
		$no++;
		};
			
	?>
	<tr>
	<td bgcolor="teal" style="color:#AFA;text-align:center;" class="content-first" colspan="3"><?php echo "T O T A L"; ?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_cmprow_count);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_slc_count);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_slc_count/$Tots_cmprow_count,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_attempt_count);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_attempt_count/$Tots_slc_count,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_Fax);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_Fax/$Tots_slc_count,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_invalid);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_invalid/$Tots_slc_count,2);?></td>
	<!--connected>-->
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_slc_count-$Tots_Fax-$Tots_invalid);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_slc_count-$Tots_Fax-$Tots_invalid)/$Tots_slc_count)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_busy);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_busy/$Tots_slc_count,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_npu);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_npu/$Tots_slc_count,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_misscust);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_misscust/$Tots_slc_count,2);?></td>
	<!--contacted>-->
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust)/$Tots_slc_count)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_ruf);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_ruf/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_cbl);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_cbl/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_ovg);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_ovg/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_alreadymoved);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_alreadymoved/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_supplement);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_supplement/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_doublecall);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_doublecall/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_countryside);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_countryside/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust),2);?></td>
	<!--done presentation>-->
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside)/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_thingking);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_thingking/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_thingkingaddnew);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_thingkingaddnew/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_holder);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_holder/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_sp);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_dp);?></td>
	<!--not int>-->
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder)/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside)*100),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_alreadycigna);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_alreadycigna/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_noneedinsurance);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_noneedinsurance/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_accountclose);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_accountclose/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_ownerchange);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_ownerchange/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_alreadycompatitor);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_alreadycompatitor/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_premiumtoohigh);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_premiumtoohigh/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_nodependant);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_nodependant/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_needhighbenefit);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_needhighbenefit/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_premiumincludeins);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_premiumincludeins/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_askinginvestment);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_askinginvestment/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_intbutnocc);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_intbutnocc/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_intbutpayissue);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_intbutpayissue/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_intbutccexp);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_intbutccexp/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_intbutccnotrcv);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_intbutccnotrcv/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_notint);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_notint/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_holder/$Tots_slc_count)*100),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_holder/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anp;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_anp/$Tots_holder/12),2);?></td>
	
	</tr>
	
</table>



