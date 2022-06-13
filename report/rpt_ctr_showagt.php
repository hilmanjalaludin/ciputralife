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
	
	
	
	
//
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
	
	function followup(){
		global $db;
		
		$sql =" select agt.id as agentid,count(cst.CustomerId) as slc
				from t_gn_customer cst
				left join tms_agent agt on agt.UserId = cst.SellerId
				WHERE date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
				group by agt.UserId ";
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$flw[$row['agentid']] += $row['slc'];
		}
		
		return $flw;
		
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
	
	/*function followup(){
		global $db;
		
		$sql =" select cmp.CampaignNumber as cmpnum,
				sum(if(cst.CustomerUpdatedTs is not null,1,0)) as slc
				from t_gn_customer cst
				LEFT JOIN t_gn_campaign cmp ON cmp.CampaignId = cst.CampaignId
				WHERE date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >='.$start_date.'
				OR date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) <='.$end_date.'
				GROUP BY cmp.CampaignNumber ";
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$flw[$row['cmpnum']] += $row['slc'];
		}
		
		return $flw;
		
	}
	
	function fanp(){
		global $db;
		
		$sql =" select cmp.CampaignNumber as cmpnum,
				sum(if(prp.PayModeId=1,prp.ProductPlanPremium,0)) as apremium,
				sum(if(prp.PayModeId=2,prp.ProductPlanPremium*12,0)) as mpremium,
				sum((if(prp.PayModeId=2,prp.ProductPlanPremium*12,0))+(if(prp.PayModeId=1,prp.ProductPlanPremium,0)))	as allpremium	
				from t_gn_insured ins
				LEFT JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
				LEFT JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
				LEFT JOIN t_gn_customer cst ON cst.CustomerId = ins.CustomerId
				LEFT JOIN t_gn_campaign cmp ON cmp.CampaignId = cst.CampaignId
				WHERE cst.callreasonid in (16,17) AND 
				date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >='.$start_date.'
				OR date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) <='.$end_date.'
				GROUP BY cmp.CampaignNumber";
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$premium[$row['cmpnum']] += $row['allpremium'];
		}
		
		return $premium;
		
	}
	
	

	function callattempt(){
		global $db;
		
		$sql =" SELECT cmp.campaignnumber AS cmpnum,
				sum(if(clh.CallReasonId is not null,1,0)) as attempted
				FROM t_gn_customer cst
				LEFT JOIN t_gn_campaign cmp ON cmp.CampaignId = cst.CampaignId
				LEFT JOIN t_gn_callhistory clh ON clh.CustomerId = cst.CustomerId
				WHERE date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >='.$start_date.'
				OR date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) <='.$end_date.'
				GROUP BY cmp.CampaignNumber ";
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$attmp[$row['cmpnum']] += $row['attempted'];
		}
		
		return $attmp;
		
	}
	
	
	function sp(){
		global $db;
		
		$sql =" select cmp.CampaignNumber as cmpnum,
				sum(if(ins.PremiumGroupId=3,1,0)) as sp
				from t_gn_insured ins
				LEFT JOIN t_gn_customer cst ON cst.CustomerId = ins.CustomerId
				LEFT JOIN t_gn_campaign cmp ON cmp.CampaignId = cst.CampaignId
				WHERE cst.callreasonid in (16,17)
				AND date(cst.customerupdatedts) BETWEEN '$start_date' AND '$end_date'
				GROUP BY cmp.CampaignNumber ";
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$sps[$row['cmpnum']] += $row['sp'];
		}
		
		return $sps;
		
	}
	function dp(){
		global $db;
		
		$sql =" select cmp.CampaignNumber as cmpnum,
				sum(if(ins.PremiumGroupId=1,1,0)) as dp
				from t_gn_insured ins
				LEFT JOIN t_gn_customer cst ON cst.CustomerId = ins.CustomerId
				LEFT JOIN t_gn_campaign cmp ON cmp.CampaignId = cst.CampaignId
				WHERE cst.callreasonid in (16,17) AND 
				date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >='.$start_date.'
				OR date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) <='.$end_date.'
				GROUP BY cmp.CampaignNumber ";
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$dpd[$row['cmpnum']] += $row['dp'];
		}
		
		return $dpd;
		
	}*/
	
	
	function asgn(){
		global $db;
		
		$sql =" select agt.id as agt,count(asg.AssignId) as supply,
				date(asg.AssignDate) as dateassign
				from t_gn_assignment asg
				left join t_gn_customer cst on cst.CustomerId = asg.CustomerId
				left join t_gn_campaign cmp on cmp.CampaignId = cst.CampaignId
				left join tms_agent agt on agt.UserId = asg.AssignSelerId
				WHERE date(if(cmp.CampaignEndDate is not null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >= '".$_REQUEST['start_date']."'
				OR date(if(cmp.CampaignEndDate is not null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >='".$_REQUEST['end_date']."'
				GROUP BY agt.id ";
		
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
				sum(if(cst.CallReasonId =45,1,0)) as NotInterestedOther,
				sum(if(ins3.PremiumGroupId =3,1,0)) as sp,
				sum(if(ins1.PremiumGroupId =1,1,0)) as dp,
				sum((if(prp1.PayModeId=2,prp1.ProductPlanPremium*12,0))+(if(prp1.PayModeId=1,prp1.ProductPlanPremium,0)))	as allpremium1,
				sum((if(prp2.PayModeId=2,prp2.ProductPlanPremium*12,0))+(if(prp2.PayModeId=1,prp2.ProductPlanPremium,0)))	as allpremium2,		
				sum((if(prp3.PayModeId=2,prp3.ProductPlanPremium*12,0))+(if(prp3.PayModeId=1,prp3.ProductPlanPremium,0)))	as allpremium3
				from t_gn_assignment asg
				left join t_gn_customer cst on cst.CustomerId = asg.CustomerId
				left join t_gn_campaign cmp on cmp.CampaignId = cst.CampaignId
				left join tms_agent agt on agt.UserId = asg.AssignSelerId
				left join tms_agent spv on spv.UserId = agt.spv_id
				LEFT JOIN t_gn_insured ins1 ON ins1.CustomerId = cst.CustomerId AND ins1.PremiumGroupId = 1
				LEFT JOIN t_gn_insured ins3 ON ins3.CustomerId = cst.CustomerId AND ins3.PremiumGroupId = 3
				LEFT JOIN t_gn_insured ins2 ON ins2.CustomerId = cst.CustomerId AND ins2.PremiumGroupId = 2
				
				LEFT JOIN t_gn_policy AS plc1 ON plc1.PolicyId = ins1.PolicyId
				LEFT JOIN t_gn_policy AS plc2 ON plc2.PolicyId = ins2.PolicyId
				LEFT JOIN t_gn_policy AS plc3 ON plc3.PolicyId = ins3.PolicyId
				LEFT JOIN t_gn_productplan AS prp1 ON prp1.ProductPlanId = plc1.ProductPlanId
				LEFT JOIN t_gn_productplan AS prp2 ON prp2.ProductPlanId = plc2.ProductPlanId
				LEFT JOIN t_gn_productplan AS prp3 ON prp3.ProductPlanId = plc3.ProductPlanId
				WHERE date(cst.customerupdatedts) BETWEEN '$start_date' AND '$end_date'
				
				AND cmp.campaignid like '%$campaign%'
				AND spv.UserId like '%$spv%'
				AND agt.UserId like '%$tm%'
				group by agt.UserId ";
 
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
<legend style="color: teal;"> &nbsp;&nbsp;&nbsp;Preview RCT Per Agent)&nbsp;&nbsp;&nbsp;</legend>
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
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total1</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total2</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total3</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Ratio</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total4</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total5</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total6</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total7</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total8</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total9</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total10</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total11</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total12</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total13</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total14</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total15</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total16</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total17</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total18</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total19</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total20</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total21</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total22</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total23</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total24</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total25</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total26</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total27</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total28</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total29</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total30</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total31</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total32</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total33</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total34</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total35</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total36</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total37</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total38</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total40</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		$slc_count=0;
		$attempt_count=0;
		$cmprow_count=0;
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
			
			//$solicited = followup();
			//$cnt_sp	= sp();
			$slc_count=followup();
			$cmprow_count = asgn();
			//$cnt_dp = dp();
			//$prm = fanp();
			//
			
			$persenflw [$row -> campaignnumber]= (($slc_count[$row->agentid])/($cmprow_count[$row->agentid]))*100;
			
			
			$persenInvalidPhoneNumber [$row -> campaignnumber]= (($row -> InvalidPhoneNumber) / ($slc_count[$row->agentid]))*100;
			$persenBusyLine [$row -> campaignnumber]= (($row -> BusyLine)/($slc_count[$row->agentid]))*100;
			$persenNobodyPicksUp [$row -> campaignnumber]= (($row -> NobodyPicksUp) / ($slc_count[$row->agentid]))*100;
			$persenMissCustomer [$row -> campaignnumber]= (($row -> MissCustomer) / ($slc_count[$row->agentid]))*100;
			$persenChangeOwner [$row -> campaignnumber]= ($slc_count[$row->agentid] / $row -> supply)*100;
			$persenInterestedWithSpouse [$row -> campaignnumber]= (($slc_count[$row->agentid])/($row -> supply))*100;
			//
			$ratio [$row -> campaignnumber]= (($attempt_count[$row->agentid]) /($slc_count[$row->agentid])) ;
			$persenslc[$row->campaignnumber]= (($row -> attmp)/($slc_count[$row->agentid]))*100;
			$persenFax [$row -> campaignnumber]= $row -> Fax /$slc_count[$row->agentid];
			//
			$totalconnected [$row -> campaignnumber]=$slc_count[$row->agentid] - ($row -> Fax) - ($row -> InvalidPhoneNumber);
			$totalpersenconnected [$row -> campaignnumber]=(($totalconnected [$row -> campaignnumber])/($slc_count[$row->agentid]))*100;
			$totalcontacted [$row -> campaignnumber]=(($totalconnected [$row -> campaignnumber]))-($row -> BusyLine)-($row -> NobodyPicksUp)-($row -> MissCustomer);
			$totalpersencontacted [$row -> campaignnumber]=(($totalcontacted [$row -> campaignnumber])/($slc_count[$row->agentid]))*100;
			$persenRejectUpFront [$row -> campaignnumber]= ($row -> RejectUpFront / $totalcontacted [$row -> campaignnumber])*100;
			$persenCallBackLater [$row -> campaignnumber]= ($row -> CallBackLater / $totalcontacted [$row -> campaignnumber])*100;
			$persenOverage [$row -> campaignnumber]= ($row -> Overage / $totalcontacted [$row -> campaignnumber])*100;
			$persenAlreadyMoved [$row -> campaignnumber]= ($row -> AlreadyMoved / $totalcontacted [$row -> campaignnumber])*100;
			$persenSupplement [$row -> campaignnumber]= ($row -> Supplement / $totalcontacted [$row -> campaignnumber])*100;
			$persenDoubleCall [$row -> campaignnumber]= ($row -> DoubleCall / $totalcontacted [$row -> campaignnumber])*100;
			$persenCountryside [$row -> campaignnumber]= ($row -> Countryside / $totalcontacted [$row -> campaignnumber])*100;
			//
			$totalRef1DonePresentastion [$row -> campaignnumber] = (($row ->RejectUpFront)+($row ->CallBackLater)+($row ->Overage));
			$totalRef2DonePresentastion [$row -> campaignnumber] = (($row ->AlreadyMoved)+($row ->Supplement)+($row ->DoubleCall)+($row ->Countryside));
			$totalRefDonePresentastion [$row -> campaignnumber] = ($totalRef1DonePresentastion [$row -> campaignnumber])+($totalRef2DonePresentastion [$row -> campaignnumber]);
			$totalDonePresentation [$row -> campaignnumber]= ($totalcontacted [$row -> campaignnumber])- ($totalRefDonePresentastion [$row -> campaignnumber]);
			$totalpersenDonePresentation [$row -> campaignnumber] = (($totalDonePresentation [$row -> campaignnumber])/($totalcontacted [$row -> campaignnumber]))*100;
			$persenThinking [$row -> campaignnumber]= (($row -> Thinking) / ($totalDonePresentation [$row -> campaignnumber]))*100;
			$persenThinkingAddNewNumber[$row -> campaignnumber]= (($row -> ThinkingAddNewNumber)/($totalDonePresentation [$row -> campaignnumber]))*100;
			
			$totalInterested [$row -> campaignnumber] =(($row -> Interested)+($row ->InterestedWithSpouse));
			$persenInterested [$row -> campaignnumber]= (($totalInterested [$row -> campaignnumber]) / ($totalDonePresentation [$row -> campaignnumber]))*100;
			$pif [$row -> campaignnumber] = ($totalInterested [$row -> campaignnumber]);
			//
			$totalNotInterested [$row -> campaignnumber] = (($totalDonePresentation [$row -> campaignnumber])-($row -> Thinking)-($pif [$row -> campaignnumber]));
			$persenNotInterested [$row -> campaignnumber]= (($totalNotInterested [$row -> campaignnumber]) / ($totalDonePresentation [$row -> campaignnumber]))*100;
			$persenAlreadyCIGNAProduct [$row -> campaignnumber]= (($row -> AlreadyCIGNAProduct) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenNoNeedInsurance [$row -> campaignnumber]= (($row -> NoNeedInsurance) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenCardClose [$row -> campaignnumber]= (($row -> CardClose) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenChangeOwner [$row -> campaignnumber]= (($row -> ChangeOwner) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenAlreadyInsuredCompatitor [$row -> campaignnumber]= (($row -> AlreadyInsuredCompatitor) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenPremiumtoohigh [$row -> campaignnumber]= (($row -> Premiumtoohigh) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenNoDependant [$row -> campaignnumber]= (($row -> NoDependant) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenNeedHighBenefit [$row -> campaignnumber]= (($row -> NeedHighBenefit) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenPremiumIncludeInstallment [$row -> campaignnumber]= (($row -> PremiumIncludeInstallment) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenAskingInvesmentProduct [$row -> campaignnumber]= (($row -> AskingInvesmentProduct) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenInterestbutnocreditcard [$row -> campaignnumber]= (($row -> Interestbutnocreditcard) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenIntrestbutpaymentmechanismissue [$row -> campaignnumber]= (($row -> Intrestbutpaymentmechanismissue) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenInterestedbutCChasbeenexpired [$row -> campaignnumber]= (($row -> InterestedbutCChasbeenexpired) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenInterestbutCardNotReceivedYet [$row -> campaignnumber]= (($row -> InterestbutCardNotReceivedYet) / ($totalNotInterested [$row -> campaignnumber]))*100;
			$persenNotInterestedOther [$row -> campaignnumber]= (($row -> NotInterestedOther)/($totalNotInterested [$row -> campaignnumber]))*100;
			$RR [$row -> campaignnumber] = ($pif [$row -> campaignnumber] / $slc_count[$row->agentid])*100;
			$SCR [$row -> campaignnumber] = ($pif [$row -> campaignnumber] / $totalcontacted [$row -> campaignnumber])*100;
			$ANP [$row -> campaignnumber] = $row -> allpremium1 + $row -> allpremium2 + $row -> allpremium3;
			$AVG [$row -> campaignnumber] =  $ANP [$row -> campaignnumber] / $pif [$row -> campaignnumber] / 12;
			
	?>
			<div class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> agentid ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> agent ; ?></td>
				<!--<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> uploaddate ; ?></td>-->
				<td nowrap style="text-align: center" class="content-middle"><?php echo $cmprow_count[$row->agentid]; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $slc_count[$row->agentid]; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenflw [$row -> campaignnumber],2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $attempt_count[$row->agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($ratio[$row->campaignnumber],2); ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Fax;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenFax [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InvalidPhoneNumber;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInvalidPhoneNumber [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalconnected [$row -> campaignnumber];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersenconnected [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->BusyLine;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenBusyLine [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NobodyPicksUp;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNobodyPicksUp [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->MissCustomer;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenMissCustomer [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalcontacted [$row -> campaignnumber];?></td> 
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersencontacted [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->RejectUpFront;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenRejectUpFront [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CallBackLater;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCallBackLater [$row -> campaignnumber],2)?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Overage;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenOverage [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyMoved;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyMoved [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Supplement;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenSupplement [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->DoubleCall;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenDoubleCall [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Countryside;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCountryside [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalDonePresentation [$row -> campaignnumber];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersenDonePresentation [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Thinking;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenThinking [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ThinkingAddNewNumber;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenThinkingAddNewNumber[$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalInterested [$row -> campaignnumber];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterested [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sp;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->dp;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalNotInterested [$row -> campaignnumber];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNotInterested [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyCIGNAProduct;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyCIGNAProduct [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NoNeedInsurance;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNoNeedInsurance[$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CardClose;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCardClose [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ChangeOwner;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenChangeOwner [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyInsuredCompatitor;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyInsuredCompatitor [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Premiumtoohigh;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenPremiumtoohigh [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NoDependant;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNoDependant [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NeedHighBenefit;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNeedHighBenefit [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->PremiumIncludeInstallment;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenPremiumIncludeInstallment [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AskingInvesmentProduct;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAskingInvesmentProduct [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Interestbutnocreditcard;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestbutnocreditcard [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Intrestbutpaymentmechanismissue;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenIntrestbutpaymentmechanismissue [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InterestedbutCChasbeenexpired;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedbutCChasbeenexpired [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InterestbutCardNotReceivedYet;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestbutCardNotReceivedYet [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NotInterestedOther;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNotInterestedOther [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($RR [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($SCR [$row -> campaignnumber],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $ANP [$row -> campaignnumber];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($AVG [$row -> campaignnumber],2);?></td>
			</tr>
			
		</div>
</tbody>
	<?php
		$no++;
		};
			
	?>
	<!--<tr>
	<td style="text-align: Right" class="content-first" colspan="4"><?php echo "T O T A L"; ?></td>
	<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Fax;?></td>
	</tr>-->
	
</table>


