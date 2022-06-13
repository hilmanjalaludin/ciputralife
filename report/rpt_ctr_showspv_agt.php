<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$rpttype		= $_REQUEST['rpttype'];
	$campaign		= $_REQUEST['cmp'];
	$spv			= $_REQUEST['spv'];
	$tm				= $_REQUEST['agt'];
	
	
	set_time_limit(750000);
	ini_set('memory_limit', '2048M');
	
//
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
	
		
	function asgn(){
		global $db;
		
		$sql =" select spv.id as spv,count(asg.AssignId) as supply,
				date(asg.AssignDate) as dateassign
				from t_gn_assignment asg
				left join t_gn_customer cst on cst.CustomerId = asg.CustomerId
				left join t_gn_campaign cmp on cmp.CampaignId = cst.CampaignId
				left join tms_agent agt on agt.UserId = asg.AssignSelerId
				left join tms_agent spv on spv.UserId = asg.AssignSpv
				WHERE TRUE ";
		$whr ="";
				//if ($_REQUEST['start_date'] && $_REQUEST['end_date']) $whr.=" AND date(asg.AssignDate) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."' ";
				if ($_REQUEST['spv']) $whr.=" AND spv.UserId like '%".$_REQUEST['spv']."%' ";
				if ($_REQUEST['agt']) $whr.=" AND (agt.UserId like '%".$_REQUEST['agt']."%' or agt.UserId is null) ";
				if ($_REQUEST['cmp']) $whr.=" AND cmp.campaignnumber like '%".$_REQUEST['cmp']."%' ";
		$sql.=$whr. " GROUP BY spv.id ";
				
		/* echo "<pre>";
		echo $sql;
		echo "</pre>"; */
		
	 
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_array($qry)){
			$followup[$row['spv']] += $row['supply'];
		}
		
		return $followup;
	
	}
	
	function followup(){
		global $db;
		
		$sql =" select spv.id as spvid,cmp.campaignnumber as cmpnum,count(cst.CustomerId) as slc
				from t_gn_customer cst
				left join t_gn_campaign cmp ON cmp.campaignid = cst.campaignid
				left join t_gn_assignment asg ON cst.CustomerId = asg.CustomerId
				left join tms_agent agt on agt.UserId = cst.SellerId
				left join tms_agent spv on spv.UserId = agt.spv_id
				WHERE TRUE ";
		$whr ="";
				if ($_REQUEST['start_date'] && $_REQUEST['end_date']) 
				$whr.=" AND date(cst.CustomerUpdatedTs) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."' ";
				//$whr.=" AND date(asg.AssignDate) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."' ";
				if ($_REQUEST['spv']) 
				$whr.=" AND spv.UserId like '%".$_REQUEST['spv']."%' ";
				if ($_REQUEST['agt']) 
				$whr.=" AND (agt.UserId like '%".$_REQUEST['agt']."%' or agt.UserId is null) ";
				if ($_REQUEST['cmp']) 
				$whr.=" AND cmp.campaignnumber like '%".$_REQUEST['cmp']."%' ";
		$sql.=$whr." GROUP BY spv.id ";
				
			/*  echo "<pre>";
			 echo $sql;
			 echo "</pre>"; */
			 
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$flw[$row['spvid']] += $row['slc'];
		}
		
		return $flw;
		
	}	
	
	function callattempt(){
		global $db;
		
		$sql =" select spv.id as spvid, spv.UserId as spvinit,sum(if(clh.CallHistoryCallDate is not null,1,0)) as attempted
				from t_gn_callhistory clh
				left join t_gn_customer cst on cst.CustomerId = clh.CustomerId
				left join t_gn_assignment asg ON cst.CustomerId = asg.CustomerId
				left join t_gn_campaign cmp ON cst.CampaignId = cmp.CampaignId
				left join tms_agent agt on agt.UserId = cst.SellerId
				left join tms_agent spv on spv.UserId = agt.spv_id
				where date(clh.CallHistoryCallDate) = date(cst.CustomerUpdatedTs) ";
		$whr ="";
				if ($_REQUEST['start_date'] && $_REQUEST['end_date']) $whr.=" AND date(cst.CustomerUpdatedTs) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."' ";
				//if ($_REQUEST['start_date'] && $_REQUEST['end_date']) $whr.=" AND date(asg.AssignDate) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."' ";
				if ($_REQUEST['spv']) $whr.=" AND spv.UserId like '%".$_REQUEST['spv']."%' ";
				if ($_REQUEST['agt']) $whr.=" AND agt.UserId like '%".$_REQUEST['agt']."%' ";
				if ($_REQUEST['cmp']) $whr.=" AND cmp.campaignnumber like '%".$_REQUEST['cmp']."%' ";
		$sql.=$whr." GROUP BY spv.id ";
			
			/* echo "<pre>";
			echo $sql;
			echo "</pre>"; */
			
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$attmp[$row['spvid']] += $row['attempted'];
		}
		
		return $attmp;
		
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
						WHERE	cst.CallReasonId IN (16,17,37,38,39,40,41,42)
						AND date(plc.PolicySalesDate) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
						AND spv.UserId like '%".$_REQUEST['spv']."%'
						AND agt.UserId like '%".$_REQUEST['agt']."%'
						AND cmp.CampaignNumber like '%".$_REQUEST['cmp']."%'
						Group By spv.id
						ORDER by cst.CustomerId ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anp[$row['spvid']]['allpremi'] += $row['allpremium'];
			$anp[$row['spvid']]['sp'] += $row['sp'];
			$anp[$row['spvid']]['dp'] += $row['dp'];
			$anp[$row['spvid']]['mi'] += $row['main'];
		}
		
		return $anp;
		
	}
	
	function anpv(){
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
						Group By spv.id
						ORDER by cst.CustomerId ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anpv[$row['spvid']]['allpremiv'] += $row['allpremium'];
			$anpv[$row['spvid']]['spv'] += $row['sp'];
			$anpv[$row['spvid']]['dpv'] += $row['dp'];
			$anpv[$row['spvid']]['miv'] += $row['main'];
		}
		
		return $anpv;
		
	}
	function anpsd(){
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
						WHERE	cst.CallReasonId IN (46,47)
						AND date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
						AND spv.UserId like '%".$_REQUEST['spv']."%'
						AND agt.UserId like '%".$_REQUEST['agt']."%'
						AND cmp.CampaignNumber like '%".$_REQUEST['cmp']."%'
						Group By spv.id
						ORDER by cst.CustomerId ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anpsd[$row['spvid']]['allpremisd'] += $row['allpremium'];
			$anpsd[$row['spvid']]['spsd'] += $row['sp'];
			$anpsd[$row['spvid']]['dpsd'] += $row['dp'];
			$anpsd[$row['spvid']]['misd'] += $row['main'];
		}
		
		return $anpsd;
		
	}
	
	function anpss(){
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
						WHERE	cst.CallReasonId IN (48,49)
						AND date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
						AND spv.UserId like '%".$_REQUEST['spv']."%'
						AND agt.UserId like '%".$_REQUEST['agt']."%'
						AND cmp.CampaignNumber like '%".$_REQUEST['cmp']."%'
						Group By spv.id
						ORDER by cst.CustomerId ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anpss[$row['spvid']]['allpremiss'] += $row['allpremium'];
			$anpss[$row['spvid']]['spss'] += $row['sp'];
			$anpss[$row['spvid']]['dpss'] += $row['dp'];
			$anpss[$row['spvid']]['miss'] += $row['main'];
		}
		
		return $anpss;
		
	}
	
	function anpr(){
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
						WHERE	cst.CallReasonId IN (41,42)
						AND date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'
						AND spv.UserId like '%".$_REQUEST['spv']."%'
						AND agt.UserId like '%".$_REQUEST['agt']."%'
						AND cmp.CampaignNumber like '%".$_REQUEST['cmp']."%'
						Group By spv.id
						ORDER by cst.CustomerId ";
		
		/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anpr[$row['spvid']]['allpremir'] += $row['allpremium'];
			$anpr[$row['spvid']]['spr'] += $row['sp'];
			$anpr[$row['spvid']]['dpr'] += $row['dp'];
			$anpr[$row['spvid']]['mir'] += $row['main'];
		}
		
		return $anpr;
		
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
				sum(if(cst.CallReasonId in(16,37,39,40,41,42),1,0)) as Interested,
				sum(if(cst.CallReasonId in(17,38),1,0)) as InterestedWithSpouse,
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
				AND spv.UserId like '%$spv%'
				AND agt.UserId like '%$tm%'
				AND agt.handling_type = 4
				group by spv.UserId 
				ORDER BY cst.CustomerId";
 
			//AND date(asg.AssignDate) BETWEEN '$start_date' AND '$end_date'

			//print_r($_REQUEST);
			
			/* echo "<pre>";
			echo $sql;
			echo "</pre>"; */
			
			
		
		$query		= $db ->execute($sql,__FILE__,__LINE__); 
		
		$ListPages -> query($sql);
		$ListPages -> result();
	
	
	SetNoCache();

?>			
<fieldset class="corner">
<legend style="color: teal;"> &nbsp;&nbsp;&nbsp;Preview RCT (Per Team)&nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Interval : $start_date To $end_date</th>";
		if ($spv!=''){
			echo "<td> (Spv ID : $spv) </td>";
		};
		if ($campaign!=''){
			echo "<td> (Campaign No : $campaign) </td>";
		};
?>
</legend>
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div class="box-shadow" style="width:1115px;height:auto;overflow:auto; border:"black">
	<table width="99%" bordercolor="black" align="center">
	<tr>
					<tr>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>No</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>SPV ID</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>SPV Name</th>
					<!--<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>Date Upload</th>-->
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="3" colspan="5" nowrap>Database</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="14" nowrap >Solicited Result</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="14" nowrap >Contacted Result</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="58" nowrap >Presentation Result</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="12" rowspan="2" nowrap >Sales Result</th>
					</tr>
					<tr>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" rowspan="2" nowrap>Not Connect</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" rowspan="3" nowrap>Connected (Total)</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="6" rowspan="2" nowrap>Not Contact</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" rowspan="3" nowrap>Contacted (Total)</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="14" rowspan="2" nowrap>Unable To do Presentatation</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" rowspan="3" nowrap>Done Presentation (Total)</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="26" nowrap>Customer Response</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="30" rowspan="2" nowrap>Not Interested Reasons</th>
					</tr>
					<tr>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" nowrap ></th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" nowrap>Interested ( PIF )Agent</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" nowrap>Interested ( PIF )Verified QA</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" nowrap>Interested ( PIF )Suspend Data QA</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" nowrap>Interested ( PIF )Suspend Selling QA</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="4" nowrap>Interested ( PIF )Rejected QA</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="2" colspan="2" nowrap>Not Interested</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="3" nowrap >RR %</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="3" nowrap>SCR %</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="2" colspan="2" nowrap>Agent</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="2" colspan="2" nowrap>Verified</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="2" colspan="2" nowrap>Suspend Data</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="2" colspan="2" nowrap>Suspend Selling</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="2" colspan="2" nowrap>Rejected</th>
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
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Holder</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Spouse</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Dependant</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Holder</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Spouse</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Dependant</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Holder</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Spouse</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Dependant</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Holder</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Spouse</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="1" nowrap >Dependant</th>
					<!--<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" colspan="2" nowrap >Not Interested</th>-->
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
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>%</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap>Total</th>
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
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >ANP</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >Av Premium</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >ANP</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >Av Premium</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >ANP</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >Av Premium</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >ANP</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >Av Premium</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >ANP</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" width="5%" nowrap >Av Premium</th>
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
		$anpv_cnt=0;
		$cnt_spv= 0;
		$cnt_dpv = 0;
		$anpsd_cnt=0;
		$cnt_spsd= 0;
		$cnt_dpsd = 0;
		$anpss_cnt=0;
		$cnt_spss= 0;
		$cnt_dpss = 0;
		$anpr_cnt=0;
		$cnt_spr= 0;
		$cnt_dpr = 0;
		$solicited = 0;
		$ratio [$row -> spvid]= 0;
		$persenspl [$row -> spvid]= 0;
		$persenFax[$row -> spvid]= 0;
		$persenOverage[$row -> spvid]= 0;
		$persenChangeOwner[$row -> spvid]= 0;
		$persenAlreadyMoved[$row -> spvid]= 0;
		$persenSupplement[$row -> spvid]= 0;
		$persenDoubleCall[$row -> spvid]= 0;
		$persenCardClose[$row -> spvid]= 0;
		$persenNobodyPicksUp[$row -> spvid]= 0;
		$persenCountryside[$row -> spvid]= 0;
		$persenBusyLine[$row -> spvid]= 0;
		$persenMissCustomer[$row -> spvid]= 0;
		$persenThinking[$row -> spvid]= 0;
		$persenThinkingAddNewNumber[$row -> spvid]= 0;
		$persenCallBackLater[$row -> spvid]= 0;
		$persenInterested[$row -> spvid]= 0;
		$persenInterestedv[$row -> spvid]= 0;
		$persenInterestedsd[$row -> spvid]= 0;
		$persenInterestedss[$row -> spvid]= 0;
		$persenInterestedr[$row -> spvid]= 0;
		
		$persenInterestedWithSpouse[$row -> spvid]= 0;
		$persenNotInterested[$row -> spvid]= 0;
		$persenNotInterestedOther[$row -> spvid]= 0;
		$persenAlreadyCIGNAProduct[$row -> spvid]= 0;
		$persenNoNeedInsurance[$row -> spvid]= 0;
		$persenAlreadyInsuredCompatitor[$row -> spvid]= 0;
		$persenPremiumtoohigh[$row -> spvid]= 0;
		$persenNoDependant[$row -> spvid]= 0;
		$persenNoNeedInsurance[$row -> spvid]= 0;
		$persenNeedHighBenefit[$row -> spvid]= 0;
		$persenPremiumIncludeInstallment[$row -> spvid]= 0;
		$persenAskingInvesmentProduct[$row -> spvid]= 0;
		$persenRejectUpFront[$row -> spvid]= 0;
		$persenInterestbutnocreditcard[$row -> spvid]= 0;
		$persenIntrestbutpaymentmechanismissue[$row -> spvid]= 0;
		$persenInterestedbutCChasbeenexpired[$row -> spvid]= 0;
		$persenInterestbutCardNotReceivedYet[$row -> spvid]= 0;
		$persenInvalidPhoneNumber[$row -> spvid]= 0;
		$totalconnected [$row -> spvid]=0;
		$totalpersenconnected [$row -> spvid] =0;
		$totalcontacted [$row -> spvid] =0;
		$totalNotInterested [$row -> spvid] =0;
		$totalInterested [$row -> spvid] =0;
		
		//
			$attempt_count= callattempt();
			$anp_cnt = anp();
			$anpv_cnt = anpv();
			$anpsd_cnt = anpsd();
			$anpss_cnt = anpss();
			$anpr_cnt = anpr();
			//$solicited = followup();
			//$cnt_sp= anp();
			$slc_count=followup();
			$cmprow_count = asgn();
			//$cnt_dp = dp();
			//$prm = fanp();
			//
			

		
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
				
			
			
			
			
			$persenflw [$row -> spvid]= (($slc_count[$row->spvid])/($cmprow_count[$row->spvid]))*100;
			
			
			$persenInvalidPhoneNumber [$row -> spvid]= (($row -> InvalidPhoneNumber) / ($slc_count[$row->spvid]))*100;
			$persenBusyLine [$row -> spvid]= (($row -> BusyLine)/($slc_count[$row->spvid]))*100;
			$persenNobodyPicksUp [$row -> spvid]= (($row -> NobodyPicksUp) / ($slc_count[$row->spvid]))*100;
			$persenMissCustomer [$row -> spvid]= (($row -> MissCustomer) / ($slc_count[$row->spvid]))*100;
			$persenChangeOwner [$row -> spvid]= ($slc_count[$row->spvid] / $row -> supply)*100;
			$persenInterestedWithSpouse [$row -> spvid]= (($slc_count[$row->spvid])/($row -> supply))*100;
			//
			$ratio [$row -> spvid]= (($attempt_count[$row->spvid]) /($slc_count[$row->spvid])) ;
			$persenslc[$row->spvid]= (($row -> attmp)/($slc_count[$row->spvid]))*100;
			$persenFax [$row -> spvid]= $row -> Fax /$slc_count[$row->spvid];
			//
			$totalconnected [$row -> spvid]=$slc_count[$row->spvid] - ($row -> Fax) - ($row -> InvalidPhoneNumber);
			$totalpersenconnected [$row -> spvid]=(($totalconnected [$row -> spvid])/($slc_count[$row->spvid]))*100;
			$totalcontacted [$row -> spvid]=(($totalconnected [$row -> spvid]))-($row -> BusyLine)-($row -> NobodyPicksUp)-($row -> MissCustomer);
			$totalpersencontacted [$row -> spvid]=(($totalcontacted [$row -> spvid])/($slc_count[$row->spvid]))*100;
			$persenRejectUpFront [$row -> spvid]= ($row -> RejectUpFront / $totalcontacted [$row -> spvid])*100;
			$persenCallBackLater [$row -> spvid]= ($row -> CallBackLater / $totalcontacted [$row -> spvid])*100;
			$persenOverage [$row -> spvid]= ($row -> Overage / $totalcontacted [$row -> spvid])*100;
			$persenAlreadyMoved [$row -> spvid]= ($row -> AlreadyMoved / $totalcontacted [$row -> spvid])*100;
			$persenSupplement [$row -> spvid]= ($row -> Supplement / $totalcontacted [$row -> spvid])*100;
			$persenDoubleCall [$row -> spvid]= ($row -> DoubleCall / $totalcontacted [$row -> spvid])*100;
			$persenCountryside [$row -> spvid]= ($row -> Countryside / $totalcontacted [$row -> spvid])*100;
			//
			$totalRef1DonePresentastion [$row -> spvid] = (($row ->RejectUpFront)+($row ->CallBackLater)+($row ->Overage));
			$totalRef2DonePresentastion [$row -> spvid] = (($row ->AlreadyMoved)+($row ->Supplement)+($row ->DoubleCall)+($row ->Countryside));
			$totalRefDonePresentastion [$row -> spvid] = ($totalRef1DonePresentastion [$row -> spvid])+($totalRef2DonePresentastion [$row -> spvid]);
			$totalDonePresentation [$row -> spvid]= ($totalcontacted [$row -> spvid])- ($totalRefDonePresentastion [$row -> spvid]);
			$totalpersenDonePresentation [$row -> spvid] = (($totalDonePresentation [$row -> spvid])/($totalcontacted [$row -> spvid]))*100;
			$persenThinking [$row -> spvid]= (($row -> Thinking) / ($totalDonePresentation [$row -> spvid]))*100;
			$persenThinkingAddNewNumber[$row -> spvid]= (($row -> ThinkingAddNewNumber)/($totalDonePresentation [$row -> spvid]))*100;
			
			$totalInterested [$row -> spvid] =($anp_cnt[$row->spvid]['mi']?$anp_cnt[$row->spvid]['mi']:0);
			$totalInterestedv [$row -> spvid] =($anpv_cnt[$row->spvid]['miv']?$anpv_cnt[$row->spvid]['miv']:0);
			$totalInterestedsd [$row -> spvid] =($anpsd_cnt[$row->spvid]['misd']?$anpsd_cnt[$row->spvid]['misd']:0);
			$totalInterestedss [$row -> spvid] =($anpss_cnt[$row->spvid]['miss']?$anpss_cnt[$row->spvid]['miss']:0);
			$totalInterestedr [$row -> spvid] =($anpr_cnt[$row->spvid]['mir']?$anpr_cnt[$row->spvid]['mir']:0);
			$persenInterested [$row -> spvid]= (($totalInterested [$row -> spvid]) / ($totalDonePresentation [$row -> spvid]))*100;
			$persenInterestedv [$row -> spvid]= (($anpv_cnt[$row->spvid]['miv']) / ($totalInterested [$row -> spvid]))*100;
			$persenInterestedsd [$row -> spvid]= (($anpsd_cnt[$row->spvid]['misd']) / ($totalDonePresentation [$row -> spvid]))*100;
			$persenInterestedss [$row -> spvid]= (($anpss_cnt[$row->spvid]['miss']) / ($totalDonePresentation [$row -> spvid]))*100;
			$persenInterestedr [$row -> spvid]= (($anpr_cnt[$row->spvid]['mir']) / ($totalDonePresentation [$row -> spvid]))*100;
			$pif [$row -> spvid] = ($totalInterested [$row -> spvid]);
			$pifv [$row -> spvid] = ($totalInterestedv [$row -> spvid]);
			$pifsd [$row -> spvid] = ($totalInterestedsd [$row -> spvid]);
			$pifss [$row -> spvid] = ($totalInterestedss [$row -> spvid]);
			$pifr [$row -> spvid] = ($totalInterestedr [$row -> spvid]);
			//
			$totalNotInterested [$row -> spvid] = (($totalDonePresentation [$row -> spvid])-($row -> Thinking)-($pif [$row -> spvid]));
			$persenNotInterested [$row -> spvid]= (($totalNotInterested [$row -> spvid]) / ($totalDonePresentation [$row -> spvid]))*100;
			$persenAlreadyCIGNAProduct [$row -> spvid]= (($row -> AlreadyCIGNAProduct) / ($totalNotInterested [$row -> spvid]))*100;
			$persenNoNeedInsurance [$row -> spvid]= (($row -> NoNeedInsurance) / ($totalNotInterested [$row -> spvid]))*100;
			$persenCardClose [$row -> spvid]= (($row -> CardClose) / ($totalNotInterested [$row -> spvid]))*100;
			$persenChangeOwner [$row -> spvid]= (($row -> ChangeOwner) / ($totalNotInterested [$row -> spvid]))*100;
			$persenAlreadyInsuredCompatitor [$row -> spvid]= (($row -> AlreadyInsuredCompatitor) / ($totalNotInterested [$row -> spvid]))*100;
			$persenPremiumtoohigh [$row -> spvid]= (($row -> Premiumtoohigh) / ($totalNotInterested [$row -> spvid]))*100;
			$persenNoDependant [$row -> spvid]= (($row -> NoDependant) / ($totalNotInterested [$row -> spvid]))*100;
			$persenNeedHighBenefit [$row -> spvid]= (($row -> NeedHighBenefit) / ($totalNotInterested [$row -> spvid]))*100;
			$persenPremiumIncludeInstallment [$row -> spvid]= (($row -> PremiumIncludeInstallment) / ($totalNotInterested [$row -> spvid]))*100;
			$persenAskingInvesmentProduct [$row -> spvid]= (($row -> AskingInvesmentProduct) / ($totalNotInterested [$row -> spvid]))*100;
			$persenInterestbutnocreditcard [$row -> spvid]= (($row -> Interestbutnocreditcard) / ($totalNotInterested [$row -> spvid]))*100;
			$persenIntrestbutpaymentmechanismissue [$row -> spvid]= (($row -> Intrestbutpaymentmechanismissue) / ($totalNotInterested [$row -> spvid]))*100;
			$persenInterestedbutCChasbeenexpired [$row -> spvid]= (($row -> InterestedbutCChasbeenexpired) / ($totalNotInterested [$row -> spvid]))*100;
			$persenInterestbutCardNotReceivedYet [$row -> spvid]= (($row -> InterestbutCardNotReceivedYet) / ($totalNotInterested [$row -> spvid]))*100;
			$persenNotInterestedOther [$row -> spvid]= (($row -> NotInterestedOther)/($totalNotInterested [$row -> spvid]))*100;
			$RR [$row -> spvid] = ($pif [$row -> spvid] / $slc_count[$row->spvid])*100;
			$SCR [$row -> spvid] = ($pif [$row -> spvid] / $totalcontacted [$row -> spvid])*100;
			$ANP [$row -> spvid] = $row -> allpremium1 + $row -> allpremium2 + $row -> allpremium3;
			$AVG [$row -> spvid] =  number_format(($anp_cnt[$row->spvid]['allpremi'] / $pif [$row -> spvid] / 12),0,'.','');
			$AVGv [$row -> spvid] =  number_format(($anpv_cnt[$row->spvid]['allpremiv'] / $pifv [$row -> spvid] / 12),0,'.','');
			$AVGsd [$row -> spvid] =  number_format(($anpsd_cnt[$row->spvid]['allpremisd'] / $pifsd [$row -> spvid] / 12),0,'.','');
			$AVGss [$row -> spvid] =  number_format(($anpss_cnt[$row->spvid]['allpremiss'] / $pifss [$row -> spvid] / 12),0,'.','');
			$AVGr [$row -> spvid] =  number_format(($anpr_cnt[$row->spvid]['allpremir'] / $pifr [$row -> spvid] / 12),0,'.','');
			
			
			// totals
			$Tots_cmprow_count+=$cmprow_count[$row->spvid];
			$Tots_slc_count+=$slc_count[$row->spvid];
			$Tots_persen_slc_count+=($slc_count[$row->spvid]/$cmprow_count[$row->spvid]);
			$Tots_attempt_count+=$attempt_count[$row->spvid];
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
			$Tots_holder+=$totalInterested [$row -> spvid];
			$Tots_sp+=$anp_cnt[$row->spvid]['sp'];
			$Tots_dp+=$anp_cnt[$row->spvid]['dp'];
			$Tots_holderv+=$totalInterestedv [$row -> spvid];
			$Tots_holdersd+=$totalInterestedsd [$row -> spvid];
			$Tots_holderss+=$totalInterestedss [$row -> spvid];
			$Tots_holderr+=$totalInterestedr [$row -> spvid];
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
			$Tots_anp+=$anp_cnt[$row->spvid]['allpremi'];
			$Tots_anpv+=$anpv_cnt[$row->spvid]['allpremiv'];
			$Tots_anpsd+=$anpsd_cnt[$row->spvid]['allpremisd'];
			$Tots_anpss+=$anpss_cnt[$row->spvid]['allpremiss'];
			$Tots_anpr+=$anpr_cnt[$row->spvid]['allpremir'];
			
					
			
			
	?>
			<div class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> spvid ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> spv ; ?></td>
				<!--<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> uploaddate ; ?></td>-->
				<td nowrap style="text-align: center" class="content-middle"><?php echo $cmprow_count[$row->spvid]; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $slc_count[$row->spvid]; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenflw [$row -> spvid],2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $attempt_count[$row->spvid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($ratio[$row->spvid],2); ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Fax;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenFax [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InvalidPhoneNumber;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInvalidPhoneNumber [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalconnected [$row -> spvid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersenconnected [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->BusyLine;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenBusyLine [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NobodyPicksUp;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNobodyPicksUp [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->MissCustomer;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenMissCustomer [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalcontacted [$row -> spvid];?></td> 
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersencontacted [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->RejectUpFront;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenRejectUpFront [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CallBackLater;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCallBackLater [$row -> spvid],2)?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Overage;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenOverage [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyMoved;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyMoved [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Supplement;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenSupplement [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->DoubleCall;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenDoubleCall [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Countryside;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCountryside [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalDonePresentation [$row -> spvid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($totalpersenDonePresentation [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Thinking;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenThinking [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ThinkingAddNewNumber;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenThinkingAddNewNumber[$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalInterested [$row -> spvid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterested [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anp_cnt[$row->spvid]['sp']?$anp_cnt[$row->spvid]['sp']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anp_cnt[$row->spvid]['dp']?$anp_cnt[$row->spvid]['dp']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalInterestedv [$row -> spvid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedv [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpv_cnt[$row->spvid]['spv']?$anpv_cnt[$row->spvid]['spv']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpv_cnt[$row->spvid]['dpv']?$anpv_cnt[$row->spvid]['dpv']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpsd_cnt[$row->spvid]['misd']?$anpsd_cnt[$row->spvid]['misd']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedsd [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpsd_cnt[$row->spvid]['spsd']?$anpsd_cnt[$row->spvid]['spsd']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpsd_cnt[$row->spvid]['dpsd']?$anpsd_cnt[$row->spvid]['dpsd']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpss_cnt[$row->spvid]['miss']?$anpss_cnt[$row->spvid]['miss']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedss [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpss_cnt[$row->spvid]['spss']?$anpss_cnt[$row->spvid]['spss']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpss_cnt[$row->spvid]['dpss']?$anpss_cnt[$row->spvid]['dpss']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpr_cnt[$row->spvid]['mir']?$anpr_cnt[$row->spvid]['mir']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedr [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpr_cnt[$row->spvid]['spr']?$anpr_cnt[$row->spvid]['spr']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpr_cnt[$row->spvid]['dpr']?$anpr_cnt[$row->spvid]['dpr']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalNotInterested [$row -> spvid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNotInterested [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyCIGNAProduct;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyCIGNAProduct [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NoNeedInsurance;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNoNeedInsurance[$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CardClose;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenCardClose [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ChangeOwner;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenChangeOwner [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AlreadyInsuredCompatitor;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAlreadyInsuredCompatitor [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Premiumtoohigh;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenPremiumtoohigh [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NoDependant;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNoDependant [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NeedHighBenefit;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNeedHighBenefit [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->PremiumIncludeInstallment;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenPremiumIncludeInstallment [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AskingInvesmentProduct;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenAskingInvesmentProduct [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Interestbutnocreditcard;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestbutnocreditcard [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Intrestbutpaymentmechanismissue;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenIntrestbutpaymentmechanismissue [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InterestedbutCChasbeenexpired;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedbutCChasbeenexpired [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InterestbutCardNotReceivedYet;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestbutCardNotReceivedYet [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NotInterestedOther;?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenNotInterestedOther [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($RR [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($SCR [$row -> spvid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anp_cnt[$row->spvid]['allpremi']?$anp_cnt[$row->spvid]['allpremi']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVG [$row -> spvid]);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpv_cnt[$row->spvid]['allpremiv']?$anpv_cnt[$row->spvid]['allpremiv']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVGv [$row -> spvid]);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpsd_cnt[$row->spvid]['allpremisd']?$anpsd_cnt[$row->spvid]['allpremisd']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVGsd [$row -> spvid]);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpss_cnt[$row->spvid]['allpremiss']?$anpss_cnt[$row->spvid]['allpremiss']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVGss [$row -> spvid]);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpr_cnt[$row->spvid]['allpremir']?$anpr_cnt[$row->spvid]['allpremir']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVGr [$row -> spvid]);?></td>
			</tr>
			
		</div>
</tbody>
	<?php
		$no++;
		};
			
	?>
	<tr>
	<td bgcolor="teal" style="color:#AFA;text-align:center;" class="content-first" colspan="3"><?php echo "T O T A L"; ?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_cmprow_count);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_slc_count);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_slc_count/$Tots_cmprow_count)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_attempt_count);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_attempt_count/$Tots_slc_count),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_Fax?$Tots_Fax:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_Fax/$Tots_slc_count)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_invalid?$Tots_invalid:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_invalid/$Tots_slc_count)*100,2);?></td>
	<!--connected>-->
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_slc_count-$Tots_Fax-$Tots_invalid);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_slc_count-$Tots_Fax-$Tots_invalid)/$Tots_slc_count)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_busy?$Tots_busy:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_busy/$Tots_slc_count)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_npu?$Tots_npu:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_npu/$Tots_slc_count)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_misscust?$Tots_misscust:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_misscust/$Tots_slc_count)*100,2);?></td>
	<!--contacted>-->
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust)/$Tots_slc_count)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_ruf?$Tots_ruf:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_ruf/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_cbl?$Tots_cbl:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_cbl/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_ovg?$Tots_ovg:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format($Tots_ovg/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust)*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_alreadymoved?$Tots_alreadymoved:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_alreadymoved/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_supplement?$Tots_supplement:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_supplement/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_doublecall?$Tots_doublecall:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_doublecall/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_countryside?$Tots_countryside:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_countryside/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100,2);?></td>
	<!--done presentation>-->
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside)/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_thingking?$Tots_thingking:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_thingking/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_thingkingaddnew?$Tots_thingkingaddnew:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_thingkingaddnew/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_holder?$Tots_holder:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_holder/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_sp?$Tots_sp:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_dp?$Tots_dp:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_holderv?$Tots_holderv:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_holderv/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_spv?$Tots_spv:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_dpv?$Tots_dpv:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_holdersd?$Tots_holdersd:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_holdersd/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_spsd?$Tots_spsd:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_dpsd?$Tots_dpsd:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_holderss?$Tots_holderss:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_holderss/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_spss?$Tots_spss:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_dpss?$Tots_dpss:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_holderr?$Tots_holderr:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_holderr/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_spr?$Tots_spr:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_dpr?$Tots_dpr:0);?></td>
	<!--not int>-->
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder)/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside)*100),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_alreadycigna?$Tots_alreadycigna:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_alreadycigna/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_noneedinsurance?$Tots_noneedinsurance:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_noneedinsurance/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_accountclose?$Tots_accountclose:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_accountclose/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_ownerchange?$Tots_ownerchange:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_ownerchange/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_alreadycompatitor?$Tots_alreadycompatitor:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_alreadycompatitor/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_premiumtoohigh?$Tots_premiumtoohigh:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_premiumtoohigh/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_nodependant?$Tots_nodependant:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_nodependant/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_needhighbenefit?$Tots_needhighbenefit:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_needhighbenefit/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_premiumincludeins?$Tots_premiumincludeins:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_premiumincludeins/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_askinginvestment?$Tots_askinginvestment:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_askinginvestment/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_intbutnocc?$Tots_intbutnocc:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_intbutnocc/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_intbutpayissue?$Tots_intbutpayissue:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_intbutpayissue/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_intbutccexp?$Tots_intbutccexp:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_intbutccexp/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_intbutccnotrcv?$Tots_intbutccnotrcv:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_intbutccnotrcv/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_notint?$Tots_notint:0);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_notint/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_holder/$Tots_slc_count)*100),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format((($Tots_holder/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust))*100),2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anp;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_anp/$Tots_holder/12),0,'.','');?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anpv;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_anpv/$Tots_holderv/12),0,'.','');?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anpsd;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_anpsd/$Tots_holdersd/12),0,'.','');?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anpss;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_anpss/$Tots_holderss/12),0,'.','');?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anpr;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_anpr/$Tots_holderr/12),0,'.','');?></td>
	
	</tr>
	
</table>


