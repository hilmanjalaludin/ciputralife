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
		global $db,$tm,$spv,$campaign;
		
		/*
		::perbedaan data supply ini dengan yg ada di report team (spv), 
		dikarenakan ada data yg tdk di distribusi ke tm dari SPV::
		*/
		
		$sql =" select agt.id as agt,count(asg.AssignId) as supply,
				date(asg.AssignDate) as dateassign
				from t_gn_assignment asg
				left join t_gn_customer cst on cst.CustomerId = asg.CustomerId
				left join t_gn_campaign cmp on cmp.CampaignId = cst.CampaignId
				left join tms_agent agt on agt.UserId = asg.AssignSelerId
				left join tms_agent spv on spv.UserId = asg.AssignSpv WHERE TRUE";
		if ($spv)
			$sql.= " AND spv.UserId = '".$spv."'";
		if ($tm)
			$sql.= " AND (agt.UserId = '".$tm."' or agt.UserId is null)";
		if ($campaign)
			$sql.="	AND cmp.campaignnumber = '".$campaign."'";
			$sql.="	GROUP BY agt.id ";
		
				
				/*
				WHERE(date(if((cmp.CampaignExtendedDate is null or date(cmp.CampaignExtendedDate) < date(cmp.CampaignEndDate)) ,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >= '".$_REQUEST['start_date']."'
				OR date(if((cmp.CampaignExtendedDate is null or date(cmp.CampaignExtendedDate) < date(cmp.CampaignEndDate)) ,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >= '".$_REQUEST['end_date']."')
				*/
		
			echo "<pre>";
			echo $sql;
			echo "</pre>";
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_array($qry)){
			$followup[$row['agt']] += $row['supply'];
		}
		
		return $followup;
	
	}
	
	function followup(){
		global $db;
		
		$sql =" select agt.id as agentid,count(cst.CustomerId) as slc
				from t_gn_customer cst
				left join t_gn_campaign cmp on cmp.campaignid = cst.campaignid
				left join t_gn_assignment asg ON cst.CustomerId = asg.CustomerId
				left join tms_agent agt on agt.UserId = cst.SellerId
				left join tms_agent spv on spv.UserId = agt.spv_id 
				WHERE date(cst.customerupdatedts) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'";
		if ($_REQUEST['spv'])
			$sql.="	AND spv.UserId like '%".$_REQUEST['spv']."%'";
		if ($_REQUEST['agt'])
			$sql.="	AND agt.UserId like '%".$_REQUEST['agt']."%'";
		if ($_REQUEST['cmp'])	
			$sql.="	AND cmp.campaignnumber like '%".$_REQUEST['cmp']."%'";
		$sql.="	Group By agt.UserId ";
		
		/* echo "<pre>";
		echo $sql;
		echo "</pre>"; */
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$flw[$row['agentid']] += $row['slc'];
		}
		
		return $flw;
		
	}	
	
	//jika call attemp < dari jumlah status call, kemungkinan diakibatkan proses "transfer"
	//kasus -> call attemp = 0 atau (null), tetapi status call memiliki nilai, kemungkinan diakibatkan proses "transfer", cek tgn cst & tgn asg
	function callattempt(){
		global $db;
		
		$sql =" select agt.id as agentid, agt.UserId as ageninit,sum(if(clh.CallHistoryCallDate is not null,1,0)) as attempted
				from t_gn_callhistory clh
				left join tms_agent agt on agt.UserId = clh.CreatedById
				left join t_gn_customer cst on cst.CustomerId = clh.CustomerId
				left join t_gn_campaign cmp on cmp.CampaignId = cst.CampaignId
				where date(clh.CallHistoryCallDate) = date(cst.CustomerUpdatedTs) AND agt.handling_type = 4 ";
		
		if($_REQUEST['start_date'])	
			$sql.="	AND date(cst.CustomerUpdatedTs) BETWEEN '".$_REQUEST['start_date']."' AND '".$_REQUEST['end_date']."'";	
		if ($_REQUEST['cmp'])	
		$sql.="	AND cmp.campaignnumber like '%".$_REQUEST['cmp']."%'";
			$sql.="	GROUP BY agt.id ";
		
		/*echo "<pre>";
		echo $sql;
		echo "</pre>";*/
		
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$attmp[$row['agentid']] += $row['attempted'];
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
						Group By agt.UserId
						ORDER by cst.CustomerId ";
		
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anp[$row['agentid']]['allpremi'] += $row['allpremium'];
			$anp[$row['agentid']]['sp'] += $row['sp'];
			$anp[$row['agentid']]['dp'] += $row['dp'];
			$anp[$row['agentid']]['mi'] += $row['main'];
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
						Group By agt.UserId
						ORDER by cst.CustomerId ";
		
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anpv[$row['agentid']]['allpremiv'] += $row['allpremium'];
			$anpv[$row['agentid']]['spv'] += $row['sp'];
			$anpv[$row['agentid']]['dpv'] += $row['dp'];
			$anpv[$row['agentid']]['miv'] += $row['main'];
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
						Group By agt.UserId
						ORDER by cst.CustomerId ";
		
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anpsd[$row['agentid']]['allpremisd'] += $row['allpremium'];
			$anpsd[$row['agentid']]['spsd'] += $row['sp'];
			$anpsd[$row['agentid']]['dpsd'] += $row['dp'];
			$anpsd[$row['agentid']]['misd'] += $row['main'];
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
						Group By agt.UserId
						ORDER by cst.CustomerId ";
		
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anpss[$row['agentid']]['allpremiss'] += $row['allpremium'];
			$anpss[$row['agentid']]['spss'] += $row['sp'];
			$anpss[$row['agentid']]['dpss'] += $row['dp'];
			$anpss[$row['agentid']]['miss'] += $row['main'];
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
						Group By agt.UserId
						ORDER by cst.CustomerId ";
		
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
			$anpr[$row['agentid']]['allpremir'] += $row['allpremium'];
			$anpr[$row['agentid']]['spr'] += $row['sp'];
			$anpr[$row['agentid']]['dpr'] += $row['dp'];
			$anpr[$row['agentid']]['mir'] += $row['main'];
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
				AND agt.UserId like '%$tm%' AND agt.handling_type = 4
				group by agt.UserId 
				ORDER BY cst.CustomerId";
				
				/*
				AND (date(if((cmp.CampaignExtendedDate is null or date(cmp.CampaignExtendedDate) < date(cmp.CampaignEndDate)) ,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >= '$start_date'
				OR date(if((cmp.CampaignExtendedDate is null or date(cmp.CampaignExtendedDate) < date(cmp.CampaignEndDate)) ,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >= '$end_date')
				*/
 
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
<legend style="color: teal;"> &nbsp;&nbsp;&nbsp;Preview RCT (Per Agent)&nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Interval : $start_date To $end_date</th>";
		if ($tm	!=''){
			echo "<td> (TRM Id : $tm	) </td>";
		};
		if ($spv!=''){
			echo "<td> (Spv Id : $spv) </td>";
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
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>Agent ID</th>
					<th class="head" bgcolor="teal" style="color:#AFA;text-align:center;" align="center" rowspan="5" nowrap>Agent Name</th>
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
		$ratio [$row -> agentid]= 0;
		$persenspl [$row -> agentid]= 0;
		$persenFax[$row -> agentid]= 0;
		$persenOverage[$row -> agentid]= 0;
		$persenChangeOwner[$row -> agentid]= 0;
		$persenAlreadyMoved[$row -> agentid]= 0;
		$persenSupplement[$row -> agentid]= 0;
		$persenDoubleCall[$row -> agentid]= 0;
		$persenCardClose[$row -> agentid]= 0;
		$persenNobodyPicksUp[$row -> agentid]= 0;
		$persenCountryside[$row -> agentid]= 0;
		$persenBusyLine[$row -> agentid]= 0;
		$persenMissCustomer[$row -> agentid]= 0;
		$persenThinking[$row -> agentid]= 0;
		$persenThinkingAddNewNumber[$row -> agentid]= 0;
		$persenCallBackLater[$row -> agentid]= 0;
		$persenInterested[$row -> agentid]= 0;
		$persenInterestedv[$row -> agentid]= 0;
		$persenInterestedsd[$row -> agentid]= 0;
		$persenInterestedss[$row -> agentid]= 0;
		$persenInterestedr[$row -> agentid]= 0;
		
		$persenInterestedWithSpouse[$row -> agentid]= 0;
		$persenNotInterested[$row -> agentid]= 0;
		$persenNotInterestedOther[$row -> agentid]= 0;
		$persenAlreadyCIGNAProduct[$row -> agentid]= 0;
		$persenNoNeedInsurance[$row -> agentid]= 0;
		$persenAlreadyInsuredCompatitor[$row -> agentid]= 0;
		$persenPremiumtoohigh[$row -> agentid]= 0;
		$persenNoDependant[$row -> agentid]= 0;
		$persenNoNeedInsurance[$row -> agentid]= 0;
		$persenNeedHighBenefit[$row -> agentid]= 0;
		$persenPremiumIncludeInstallment[$row -> agentid]= 0;
		$persenAskingInvesmentProduct[$row -> agentid]= 0;
		$persenRejectUpFront[$row -> agentid]= 0;
		$persenInterestbutnocreditcard[$row -> agentid]= 0;
		$persenIntrestbutpaymentmechanismissue[$row -> agentid]= 0;
		$persenInterestedbutCChasbeenexpired[$row -> agentid]= 0;
		$persenInterestbutCardNotReceivedYet[$row -> agentid]= 0;
		$persenInvalidPhoneNumber[$row -> agentid]= 0;
		$totalconnected [$row -> agentid]=0;
		$totalpersenconnected [$row -> agentid] =0;
		$totalcontacted [$row -> agentid] =0;
		$totalNotInterested [$row -> agentid] =0;
		$totalInterested [$row -> agentid] =0;
		
		
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
			
			$totalInterested [$row -> agentid] =($anp_cnt[$row->agentid]['mi']?$anp_cnt[$row->agentid]['mi']:0);
			$totalInterestedv [$row -> agentid] =($anpv_cnt[$row->agentid]['miv']?$anpv_cnt[$row->agentid]['miv']:0);
			$totalInterestedsd [$row -> agentid] =($anpsd_cnt[$row->agentid]['misd']?$anpsd_cnt[$row->agentid]['misd']:0);
			$totalInterestedss [$row -> agentid] =($anpss_cnt[$row->agentid]['miss']?$anpss_cnt[$row->agentid]['miss']:0);
			$totalInterestedr [$row -> agentid] =($anpr_cnt[$row->agentid]['mir']?$anpr_cnt[$row->agentid]['mir']:0);
			$persenInterested [$row -> agentid]= (($totalInterested [$row -> agentid]) / ($totalDonePresentation [$row -> agentid]))*100;
			$persenInterestedv [$row -> agentid]= (($anpv_cnt[$row->agentid]['miv']) / ($totalInterested [$row -> agentid]))*100;
			$persenInterestedsd [$row -> agentid]= (($anpsd_cnt[$row->agentid]['misd']) / ($totalDonePresentation [$row -> agentid]))*100;
			$persenInterestedss [$row -> agentid]= (($anpss_cnt[$row->agentid]['miss']) / ($totalDonePresentation [$row -> agentid]))*100;
			$persenInterestedr [$row -> agentid]= (($anpr_cnt[$row->agentid]['mir']) / ($totalDonePresentation [$row -> agentid]))*100;
			$pif [$row -> agentid] = ($totalInterested [$row -> agentid]);
			$pifv [$row -> agentid] = ($totalInterestedv [$row -> agentid]);
			$pifsd [$row -> agentid] = ($totalInterestedsd [$row -> agentid]);
			$pifss [$row -> agentid] = ($totalInterestedss [$row -> agentid]);
			$pifr [$row -> agentid] = ($totalInterestedr [$row -> agentid]);
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
			$AVG [$row -> agentid] =  $anp_cnt[$row->agentid]['allpremi'] / $pif [$row -> agentid] / 12;
			$AVGv [$row -> agentid] =  $anpv_cnt[$row->agentid]['allpremiv'] / $pifv [$row -> agentid] / 12;
			$AVGsd [$row -> agentid] =  $anpsd_cnt[$row->agentid]['allpremisd'] / $pifss [$row -> agentid] / 12;
			$AVGss [$row -> agentid] =  $anpss_cnt[$row->agentid]['allpremiss'] / $pifsd [$row -> agentid] / 12;
			$AVGr [$row -> agentid] =  $anpr_cnt[$row->agentid]['allpremir'] / $pifr [$row -> agentid] / 12;
			
			
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
			$Tots_sp+=$anp_cnt[$row->agentid]['sp'];
			$Tots_dp+=$anp_cnt[$row->agentid]['dp'];
			$Tots_holderv+=$totalInterestedv [$row -> agentid];
			$Tots_spv+=$anpv_cnt[$row->agentid]['spv'];
			$Tots_dpv+=$anpv_cnt[$row->agentid]['dpv'];
			$Tots_holdersd+=$totalInterestedsd [$row -> agentid];
			$Tots_spsd+=$anpsd_cnt[$row->agentid]['spsd'];
			$Tots_dpsd+=$anpsd_cnt[$row->agentid]['dpsd'];
			$Tots_holderss+=$totalInterestedss [$row -> agentid];
			$Tots_spss+=$anpss_cnt[$row->agentid]['spss'];
			$Tots_dpss+=$anpss_cnt[$row->agentid]['dpss'];
			$Tots_holderr+=$totalInterestedr [$row -> agentid];
			$Tots_spr+=$anpr_cnt[$row->agentid]['spr'];
			$Tots_dpr+=$anpr_cnt[$row->agentid]['dpr'];
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
			$Tots_anp+=$anp_cnt[$row->agentid]['allpremi'];
			$Tots_anpv+=$anpv_cnt[$row->agentid]['allpremiv'];
			$Tots_anpsd+=$anpsd_cnt[$row->agentid]['allpremisd'];
			$Tots_anpss+=$anpss_cnt[$row->agentid]['allpremiss'];
			$Tots_anpr+=$anpr_cnt[$row->agentid]['allpremir'];
			
					
			
			
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
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($attempt_count[$row->agentid]?$attempt_count[$row->agentid]:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($ratio[$row->agentid]?$ratio[$row->agentid]:0,2); ?></td>
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
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anp_cnt[$row->agentid]['sp']?$anp_cnt[$row->agentid]['sp']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anp_cnt[$row->agentid]['dp']?$anp_cnt[$row->agentid]['dp']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $totalInterestedv [$row -> agentid];?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedv [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpv_cnt[$row->agentid]['spv']?$anp_cnt[$row->agentid]['spv']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpv_cnt[$row->agentid]['dpv']?$anp_cnt[$row->agentid]['dpv']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpsd_cnt[$row->agentid]['misd']?$anpsd_cnt[$row->agentid]['misd']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedsd [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpsd_cnt[$row->agentid]['spsd']?$anp_cnt[$row->agentid]['spsd']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpsd_cnt[$row->agentid]['dpsd']?$anp_cnt[$row->agentid]['dpsd']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpss_cnt[$row->agentid]['miss']?$anpss_cnt[$row->agentid]['miss']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedss [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpss_cnt[$row->agentid]['spss']?$anp_cnt[$row->agentid]['spss']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpss_cnt[$row->agentid]['dpss']?$anp_cnt[$row->agentid]['dpss']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpr_cnt[$row->agentid]['mir']?$anpr_cnt[$row->agentid]['mir']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($persenInterestedr [$row -> agentid],2);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpr_cnt[$row->agentid]['spr']?$anp_cnt[$row->agentid]['spr']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpr_cnt[$row->agentid]['dpr']?$anp_cnt[$row->agentid]['dpr']:0);?></td>
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
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anp_cnt[$row->agentid]['allpremi']?$anp_cnt[$row->agentid]['allpremi']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVG [$row -> agentid]);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpv_cnt[$row->agentid]['allpremiv']?$anpv_cnt[$row->agentid]['allpremiv']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVGv [$row -> agentid]);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpsd_cnt[$row->agentid]['allpremisd']?$anpsd_cnt[$row->agentid]['allpremisd']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVGsd [$row -> agentid]);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpss_cnt[$row->agentid]['allpremiss']?$anpss_cnt[$row->agentid]['allpremiss']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVGss [$row -> agentid]);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($anpr_cnt[$row->agentid]['allpremir']?$anpr_cnt[$row->agentid]['allpremir']:0);?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ($AVGr [$row -> agentid]);?></td>
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
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_slc_count/$Tots_cmprow_count),2);?></td>
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
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_holderss);?></td>
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
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo number_format(($Tots_noneedinsurance/($Tots_slc_count-$Tots_Fax-$Tots_invalid-$Tots_busy-$Tots_npu-$Tots_misscust-$Tots_ruf-$Tots_cbl-$Tots_ovg-$Tots_alreadymoved-$Tots_supplement-$Tots_doublecall-$Tots_countryside-$Tots_thingking-$Tots_thingkingaddnew-$Tots_holder))*100,2);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_noneedinsurance?$Tots_noneedinsurance:0);?></td>
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
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anp?$Tots_anp:0;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_anp/$Tots_holder/12);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anpv?$Tots_anpv:0;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_anpv/$Tots_holderv/12);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anpsd?$Tots_anpsd:0;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_anpsd/$Tots_holdersd/12);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anpss?$Tots_anpss:0;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_anpss/$Tots_holderss/12);?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo $Tots_anpr?$Tots_anpr:0;?></td>
	<td nowrap bgcolor="teal" style="color:#AFA;text-align:center;" class="content-middle"><?php echo ($Tots_anpr/$Tots_holderr/12);?></td>
	
	</tr>
	
</table>