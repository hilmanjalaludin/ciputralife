<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.exportclosing.txt.php");
	
	
	
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$prodid = $_REQUEST['prodid'];
	$Campaign = $_REQUEST['Campaign'];
	$exported = $_REQUEST['exported'];
	$query = $_REQUEST['query'];
	$query_check = $_REQUEST['query_check'];

	if ($prodid) {
		$productName = getProductName($prodid, new mysql());
	}
	else
		$productName = 'All active product';

	function getProductName($prodid, $dbase)
	{
		$sql = " select pr.ProductName from t_gn_product pr
				where pr.ProductCode='$prodid' ";
		$te = $dbase->query($sql);
		$te2 = $te->result_object();
		// print_r($_REQUEST);
		return $te2[0]->ProductName;
	}

	$export_list='';

	function getRowFilePolicy($textFile='',$page=0,$perpage=0){
		global $export_list;
		global $db;

		global $start_date;
		global $end_date;
		global $prodid;
		global $Campaign;
		global $exported;
		global $query;
		global $query_check;
		
		if($page < 1):
			$start = 0;
		else:
			$start = ($page) * $perpage;
		endif;
		// bk.BankName
	 	// query for data policys
		$sql = "SELECT DISTINCT 
				pa.PolicyNumber AS policy_id,
				'' as policy_ref,
				cst.NumberCIF AS prospect_id,
				prd.ProductCode AS product_id,
				cmp.CampaignNumber AS campaign_id,
				cmp.CampaignNumber as campaign_TBSS,
				date_format(cst.CustomerUpdatedTs, '%Y-%m-%d 00:00:00') as input,
				cst.CustomerUpdatedTs as effdt,
				'' as payer_cifno,
				s.Salutation as payer_title,
				py.PayerFirstName as payer_fname,
				py.PayerLastName as payer_lname,
				g.GenderShortCode as payer_sex,
				date_format(py.PayerDOB, '%Y-%m-%d %H:%i:%s') as payer_dob,
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
				ct.CPayment as pay_type,
				ct.CreditCardTypeCode as card_type,
				'' as bank,
				'' as branch,
				py.PayerCreditCardNum as acctnum,
				py.PayerCreditCardExpDate as ccexpdate,
				pm.PayModeCode as bill_freq,
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
				round((if(count(distinct ins.InsuredId)>1, 0.9, 1)*sum(plc.Premi)),0) as premium,
				round(if(prt.ProductType='PA',plc.Premi, if(pm.PayModeCode='M', if(count(distinct ins.InsuredId)>1, 0.9, 1)*12*sum(plc.Premi),if(count(distinct ins.InsuredId)>1, 0.9, 1)*sum(plc.Premi))),0) as nbi,
				'N' as export,
				'' AS exportdate,
				'' as canceldate,
				date_format(cst.CustomerRejectedDate,'%Y-%c-%e') as callDate2,
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
				now() as deliverydate,
				'' as seperate_policy,
				1 as 'status',
				'' as payer_occupationid,
				'' as payer_birthplace,
				'' as payer_religionid,
				0 as payer_income,
				'' as payer_position,
				'' as payer_company,
				agt.init_name as operid,
				agt.id as sellerid,
				spv.id as spv_id,
				am.id as atm_id,
				tsm.init_name as tsm_id,
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
				inner JOIN tms_agent AS am ON spv.mgr_id = am.UserId
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
				WHERE 1 = 1 ";

		if ($prodid) {
			$sql = $sql . " and prd.ProductCode = '$prodid' ";
		}
		if ($start_date) {
			$sql = $sql . " and date(cst.CustomerUpdatedTs) >= '$start_date' ";
		}
		if ($end_date) {
			$sql = $sql . " and date(cst.CustomerUpdatedTs) <= '$end_date' ";
		}
		if ($Campaign) {
			$sql = $sql . " and cmp.CampaignNumber in ($Campaign) ";
		}
		if ($exported) {
			$sql = $sql . " and pa.isExported = 1 ";
		}
		else
			$sql = $sql . " and pa.isExported = 0 ";

		if ($query_check) {
			$query = str_replace("\\", '', $query);
			$sql = $sql . " ". $query." ";
		}

		$tail=" AND cst.CallReasonQue =1
				and ins.QCStatus=1 
				group by pa.PolicyNumber
				order by pa.PolicyNumber";
		$sql = $sql . $tail;

		// date(cst.CustomerUpdatedTs) >= '$start_date'
		// and date(cst.CustomerUpdatedTs) <= '$end_date'
		// and prd.ProductCode='$prodid'
		// and cst.CallReasonQue =1
		// and ins.QCStatus=1
		// /*and pa.isexported=0
		// -- cst.CallReasonId IN (37,38) */
		// group by pa.PolicyNumber
		// order by pa.PolicyNumber
		// ";
		
	 	$query = $db->execute($sql,__FILE__,__LINE__);
	
		// write header for policy
	 	$datas.=$textFile->split("[Policy]","","","")."\r\n";

	 	$datas.=$textFile->split("policy_id","","","\t");
		$datas.=$textFile->split("policy_ref","","","\t");
		$datas.=$textFile->split("prospect_id","","","\t");
		$datas.=$textFile->split("product_id","","","\t");
		$datas.=$textFile->split("campaign_id","","","\t");
		$datas.=$textFile->split("campaign_TBSS","","","\t");
		$datas.=$textFile->split("input","","","\t");
		$datas.=$textFile->split("effdt","","","\t");
		$datas.=$textFile->split("payer_cifno","","","\t");
		$datas.=$textFile->split("payer_title","","","\t");
		$datas.=$textFile->split("payer_fname","","","\t");
		$datas.=$textFile->split("payer_lname","","","\t");
		$datas.=$textFile->split("payer_sex","","","\t");
		$datas.=$textFile->split("payer_dob","","","\t");
		$datas.=$textFile->split("addrtype","","","\t");
		$datas.=$textFile->split("addr1","","","\t");
		$datas.=$textFile->split("addr2","","","\t");
		$datas.=$textFile->split("addr3","","","\t");
		$datas.=$textFile->split("addr4","","","\t");
		$datas.=$textFile->split("city","","","\t");
		$datas.=$textFile->split("post","","","\t");
		$datas.=$textFile->split("province","","","\t");
		$datas.=$textFile->split("phone","","","\t");
		$datas.=$textFile->split("faxphone","","","\t");
		$datas.=$textFile->split("email","","","\t");
		$datas.=$textFile->split("pay_type","","","\t");
		$datas.=$textFile->split("card_type","","","\t");
		$datas.=$textFile->split("bank","","","\t");
		$datas.=$textFile->split("branch","","","\t");
		$datas.=$textFile->split("acctnum","","","\t");
		$datas.=$textFile->split("ccexpdate","","","\t");
		$datas.=$textFile->split("bill_freq","","","\t");
		$datas.=$textFile->split("question1","","","\t");
		$datas.=$textFile->split("question2","","","\t");
		$datas.=$textFile->split("question3","","","\t");
		$datas.=$textFile->split("question4","","","\t");
		$datas.=$textFile->split("question5","","","\t");
		$datas.=$textFile->split("benefit_level","","","\t");
		$datas.=$textFile->split("premium","","","\t");
		$datas.=$textFile->split("nbi","","","\t");
		$datas.=$textFile->split("export","","","\t");
		$datas.=$textFile->split("exportdate","","","\t");
		$datas.=$textFile->split("canceldate","","","\t");
		$datas.=$textFile->split("callDate2","","","\t");
		$datas.=$textFile->split("paystatus","","","\t");
		$datas.=$textFile->split("paynotes","","","\t");
		$datas.=$textFile->split("payauthcode","","","\t");
		$datas.=$textFile->split("paytransdate","","","\t");
		$datas.=$textFile->split("payorderno","","","\t");
		$datas.=$textFile->split("payccnum","","","\t");
		$datas.=$textFile->split("paycvv","","","\t");
		$datas.=$textFile->split("payexpdate","","","\t");
		$datas.=$textFile->split("paycurency","","","\t");
		$datas.=$textFile->split("paycardtype","","","\t");
		$datas.=$textFile->split("payer_idtype","","","\t");
		$datas.=$textFile->split("payer_personalid","","","\t");
		$datas.=$textFile->split("payer_mobilephone","","","\t");
		$datas.=$textFile->split("payer_officephone","","","\t");
		$datas.=$textFile->split("deliverydate","","","\t");
		$datas.=$textFile->split("seperate_policy","","","\t");
		$datas.=$textFile->split("status","","","\t");
		$datas.=$textFile->split("payer_occupationid","","","\t");
		$datas.=$textFile->split("payer_birthplace","","","\t");
		$datas.=$textFile->split("payer_religionid","","","\t");
		$datas.=$textFile->split("payer_income","","","\t");
		$datas.=$textFile->split("payer_position","","","\t");
		$datas.=$textFile->split("payer_company","","","\t");
		$datas.=$textFile->split("operid","","","\t");
		$datas.=$textFile->split("sellerid","","","\t");
		$datas.=$textFile->split("spv_id","","","\t");
		$datas.=$textFile->split("atm_id","","","\t");
		$datas.=$textFile->split("tsm_id","","","\t");
		$datas.=$textFile->split("pcifnumber","","","\t");
		$datas.=$textFile->split("pcardtype","","","\t");
		$datas.=$textFile->split("prefnumber","","","\t");
		$datas.=$textFile->split("paccnumber","","","\t");
		$datas.=$textFile->split("paccname","","","\t");
		$datas.=$textFile->split("pcardnumber","","","\t");
		$datas.=$textFile->split("record_id","","","\t");
		$datas.=$textFile->split("calldate","","","\t");
		$datas.=$textFile->split("phone2","","","\t");
		$datas.=$textFile->split("payer_mobilephone2","","","\t");
		$datas.=$textFile->split("payer_officephone2","","","\r\n");

		while($row = $db -> fetchrow($query) ){
			$export_list.=", '".$row->policy_id."' ";
			$datas.=$textFile->split($row->policy_id,"","","\t");
			$datas.=$textFile->split($row->policy_ref,"","","\t");
			$datas.=$textFile->split($row->prospect_id,"","","\t");
			$datas.=$textFile->split($row->product_id,"","","\t");
			$datas.=$textFile->split($row->product_id,"","","\t");
			$datas.=$textFile->split($row->campaign_TBSS,"","","\t");
			$datas.=$textFile->split($row->input,"","","\t");
			$datas.=$textFile->split($row->effdt,"","","\t");
			$datas.=$textFile->split($row->payer_cifno,"","","\t");
			$datas.=$textFile->split($row->payer_title,"","","\t");
			$datas.=$textFile->split($row->payer_fname,"","","\t");
			$datas.=$textFile->split($row->payer_lname,"","","\t");
			$datas.=$textFile->split($row->payer_sex,"","","\t");
			$datas.=$textFile->split($row->payer_dob,"","","\t");
			$datas.=$textFile->split($row->addrtype,"","","\t");
			$datas.=$textFile->split($row->addr1,"","","\t");
			$datas.=$textFile->split($row->addr2,"","","\t");
			$datas.=$textFile->split($row->addr3,"","","\t");
			$datas.=$textFile->split($row->addr4,"","","\t");
			$datas.=$textFile->split($row->city,"","","\t");
			$datas.=$textFile->split($row->post,"","","\t");
			$datas.=$textFile->split($row->province,"","","\t");
			$datas.=$textFile->split($row->phone,"","","\t");
			$datas.=$textFile->split($row->faxphone,"","","\t");
			$datas.=$textFile->split($row->email,"","","\t");
			$datas.=$textFile->split($row->pay_type,"","","\t");
			$datas.=$textFile->split($row->card_type,"","","\t");
			$datas.=$textFile->split($row->bank,"","","\t");
			$datas.=$textFile->split($row->branch,"","","\t");
			$datas.=$textFile->split($row->acctnum,"","","\t");
			$datas.=$textFile->split($row->ccexpdate,"","","\t");
			$datas.=$textFile->split($row->bill_freq,"","","\t");
			$datas.=$textFile->split($row->question1,"","","\t");
			$datas.=$textFile->split($row->question2,"","","\t");
			$datas.=$textFile->split($row->question3,"","","\t");
			$datas.=$textFile->split($row->question4,"","","\t");
			$datas.=$textFile->split($row->question5,"","","\t");
			$datas.=$textFile->split($row->benefit_level,"","","\t");
			$datas.=$textFile->split($row->premium,"","","\t");
			$datas.=$textFile->split($row->nbi,"","","\t");
			$datas.=$textFile->split($row->export,"","","\t");
			$datas.=$textFile->split($row->exportdate,"","","\t");
			$datas.=$textFile->split($row->canceldate,"","","\t");
			$datas.=$textFile->split($row->callDate2,"","","\t");
			$datas.=$textFile->split($row->paystatus,"","","\t");
			$datas.=$textFile->split($row->paynotes,"","","\t");
			$datas.=$textFile->split($row->payauthcode,"","","\t");
			$datas.=$textFile->split($row->paytransdate,"","","\t");
			$datas.=$textFile->split($row->payorderno,"","","\t");
			$datas.=$textFile->split($row->payccnum,"","","\t");
			$datas.=$textFile->split($row->paycvv,"","","\t");
			$datas.=$textFile->split($row->payexpdate,"","","\t");
			$datas.=$textFile->split($row->paycurency,"","","\t");
			$datas.=$textFile->split($row->paycardtype,"","","\t");
			$datas.=$textFile->split($row->payer_idtype,"","","\t");
			$datas.=$textFile->split($row->payer_personalid,"","","\t");
			$datas.=$textFile->split($row->payer_mobilephone,"","","\t");
			$datas.=$textFile->split($row->payer_officephone,"","","\t");
			$datas.=$textFile->split($row->deliverydate,"","","\t");
			$datas.=$textFile->split($row->seperate_policy,"","","\t");
			$datas.=$textFile->split($row->status,"","","\t");
			$datas.=$textFile->split($row->payer_occupationid,"","","\t");
			$datas.=$textFile->split($row->payer_birthplace,"","","\t");
			$datas.=$textFile->split($row->payer_religionid,"","","\t");
			$datas.=$textFile->split($row->payer_income,"","","\t");
			$datas.=$textFile->split($row->payer_position,"","","\t");
			$datas.=$textFile->split($row->payer_company,"","","\t");
			$datas.=$textFile->split($row->sellerid,"","","\t");
			$datas.=$textFile->split($row->operid,"","","\t");
			$datas.=$textFile->split($row->spv_id,"","","\t");
			$datas.=$textFile->split($row->atm_id,"","","\t");
			$datas.=$textFile->split($row->tsm_id,"","","\t");
			$datas.=$textFile->split($row->pcifnumber,"","","\t");
			$datas.=$textFile->split($row->pcardtype,"","","\t");
			$datas.=$textFile->split($row->prefnumber,"","","\t");
			$datas.=$textFile->split($row->paccnumber,"","","\t");
			$datas.=$textFile->split($row->paccname,"","","\t");
			$datas.=$textFile->split($row->pcardnumber,"","","\t");
			$datas.=$textFile->split($row->record_id,"","","\t");
			$datas.=$textFile->split($row->callDate,"","","\t");
			$datas.=$textFile->split($row->phone2,"","","\t");
			$datas.=$textFile->split($row->payer_mobilephone2,"","","\t");
			$datas.=$textFile->split($row->payer_officephone2,"","","\r\n");

		}
	return $datas;	
// return $sql;
	}

	function getRowFileInsured($textFile='',$datas=array(),$page=0,$perpage=0){
		global $export_list;
		global $db;

		global $start_date;
		global $end_date;
		global $prodid;
		global $Campaign;
		global $exported;
		global $query;
		global $query_check;
	
			if($page < 1):
				$start = 0;
			else:
				$start = ($page) * $perpage;
			endif;

	 
		$insured = "
		SELECT DISTINCT 
				pa.PolicyNumber AS FormNumber,
				pg.PremiumGroupOrder as holder_id,
				s.Salutation as title,
				ins.InsuredFirstName as InsuredName,
				date_format(ins.InsuredDOB,'%Y-%m-%d %H:%i:%s') as InsuredDOB,
				g.GenderShortCode as sex,
				py.PayerAddressLine1 as Address1,
				py.PayerAddressLine2 as Address2,
				py.PayerCity as City,
				'' as RTdanRW,
				py.PayerZipCode as ZipCode,
				py.PayerHomePhoneNum as Phone1,
				py.PayerWorkPhoneNum as Phone2,
				'' as EXTPhone2,
				py.PayerMobilePhoneNum as MobilePhone,
				'' as MaritalStatus,
				'' as Children,
				'' as FACode,
				date_format(cst.CustomerUpdatedTs, '%Y-%m-%d 00:00:00') as Activation_Date,
				'' as BranchCode,
				'' as CSName,
				ins.InsuredFirstName as ACCHolderName,
				'' as ACCNumber,				
				'' as ACCBranch,
				'' as Program,
				py.PayerEmail as Email,
				'' as Asken,
				'' as Region,
				'' as NamaLG,
				'' as IDStaffBank,
				agt.init_name as operid,
				agt.id as sellerid,
				spv.id as spv_id,
				am.id as am_id,
				tsm.init_name as tsm_id,
				py.PayerCreditCardExpDate as EXPDate,
				'' as EXPCard,
				prd.ProductCode AS product_id,
				cst.NumberCIF as Periode,
				round(plc.Premi,0) as PremiumAmount
												
				FROM t_gn_customer AS cst
				inner join t_gn_insured ins on ins.customerid=cst.customerid
				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId
				inner join t_gn_policyautogen pa on pa.PolicyNumber=plc.PolicyNumber
				inner join t_gn_payer py on py.CustomerId=cst.CustomerId
				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
				inner JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
				inner JOIN tms_agent AS spv ON agt.spv_id = spv.UserId
				inner JOIN tms_agent AS am ON spv.mgr_id = am.UserId
				inner JOIN tms_agent AS tsm ON tsm.handling_type=1
				left join t_lk_salutation s on s.SalutationId=ins.SalutationId
				left join t_lk_gender g on g.GenderId=ins.GenderId
				left join t_lk_premiumgroup pg on pg.PremiumGroupId=ins.PremiumGroupId
				WHERE 1 = 1
		";
		if ($export_list) {
			$policy_arr = substr($export_list, 1);
		}
		
		$policy_cond = $policy_arr ? " and pa.PolicyNumber in (".$policy_arr.") " : ' and 1 = -1 ';
		$insured_tail =	" ORDER BY pa.PolicyNumber, pg.PremiumGroupOrder ";
		$insured_query = $insured . $policy_cond  . $insured_tail;
		// echo $insured_query;
		// date(cst.CustomerUpdatedTs) >= '$start_date'
		// and date(cst.CustomerUpdatedTs) <= '$end_date'
		// and prd.ProductCode='$prodid'
		// and cst.CallReasonQue =1
		// and ins.QCStatus=1
		// ORDER BY pa.PolicyNumber, pg.PremiumGroupOrder ";
	 
	 	$query = $db->execute($insured_query,__FILE__,__LINE__);
	
		// write header for policy
	 	$datas.=$textFile->split("[Insured]","","","\r\n");

	 	$datas.=$textFile->split("FormNumber","","","\t");
		$datas.=$textFile->split("holder_id","","","\t");
		$datas.=$textFile->split("title","","","\t");
		$datas.=$textFile->split("InsuredName","","","\t");
		$datas.=$textFile->split("InsuredDOB","","","\t");
		$datas.=$textFile->split("sex","","","\t");
		$datas.=$textFile->split("Address1","","","\t");
		$datas.=$textFile->split("Address2","","","\t");
		$datas.=$textFile->split("City","","","\t");
		$datas.=$textFile->split("RTdanRW","","","\t");
		$datas.=$textFile->split("ZipCode","","","\t");
		$datas.=$textFile->split("Phone1","","","\t");
		$datas.=$textFile->split("Phone2","","","\t");
		$datas.=$textFile->split("EXTPhone2","","","\t");
		$datas.=$textFile->split("MobilePhone","","","\t");
		$datas.=$textFile->split("MaritalStatus","","","\t");
		$datas.=$textFile->split("Children","","","\t");
		$datas.=$textFile->split("FACode","","","\t");
		$datas.=$textFile->split("Activation_Date","","","\t");
		$datas.=$textFile->split("BranchCode","","","\t");
		$datas.=$textFile->split("CSName","","","\t");
		$datas.=$textFile->split("ACCHolderName","","","\t");
		$datas.=$textFile->split("ACCNumber","","","\t");
		$datas.=$textFile->split("ACCBranch","","","\t");
		$datas.=$textFile->split("Program","","","\t");
		$datas.=$textFile->split("Email","","","\t");
		$datas.=$textFile->split("Asken","","","\t");
		$datas.=$textFile->split("Region","","","\t");
		$datas.=$textFile->split("NamaLG","","","\t");
		$datas.=$textFile->split("IDStaffBank","","","\t");
		$datas.=$textFile->split("operid","","","\t");
		$datas.=$textFile->split("sellerid","","","\t");
		$datas.=$textFile->split("spv_id","","","\t");
		$datas.=$textFile->split("am_id","","","\t");
		$datas.=$textFile->split("tsm_id","","","\t");
		$datas.=$textFile->split("EXPDate","","","\t");
		$datas.=$textFile->split("EXPCard","","","\t");
		$datas.=$textFile->split("product_id","","","\t");
		$datas.=$textFile->split("Periode","","","\t");
		$datas.=$textFile->split("PremiumAmount","","","\r\n");

		$pol_id='';
		$holder_id=0;
		while($row = $db -> fetchrow($query) ){
			if ($row ->policy_id != $pol_id) {
				$pol_id=$row ->policy_id;
				$holder_id=0;
			}
			$holder_id++;

			$datas.=$textFile->split($row->FormNumber,"","","\t");
			$datas.=$textFile->split($holder_id,"","","\t");
			$datas.=$textFile->split($row->title,"","","\t");
			$datas.=$textFile->split($row->InsuredName,"","","\t");
			$datas.=$textFile->split($row->InsuredDOB,"","","\t");
			$datas.=$textFile->split($row->sex,"","","\t");
			$datas.=$textFile->split($row->Address1,"","","\t");
			$datas.=$textFile->split($row->Address2,"","","\t");
			$datas.=$textFile->split($row->City,"","","\t");
			$datas.=$textFile->split($row->RTdanRW,"","","\t");
			$datas.=$textFile->split($row->ZipCode,"","","\t");
			$datas.=$textFile->split($row->Phone1,"","","\t");
			$datas.=$textFile->split($row->Phone2,"","","\t");
			$datas.=$textFile->split($row->EXTPhone2,"","","\t");
			$datas.=$textFile->split($row->MobilePhone,"","","\t");
			$datas.=$textFile->split($row->MaritalStatus,"","","\t");
			$datas.=$textFile->split($row->Children,"","","\t");
			$datas.=$textFile->split($row->FACode,"","","\t");
			$datas.=$textFile->split($row->Activation_Date,"","","\t");
			$datas.=$textFile->split($row->BranchCode,"","","\t");
			$datas.=$textFile->split($row->CSName,"","","\t");
			$datas.=$textFile->split($row->ACCHolderName,"","","\t");
			$datas.=$textFile->split($row->ACCNumber,"","","\t");
			$datas.=$textFile->split($row->ACCBranch,"","","\t");
			$datas.=$textFile->split($row->Program,"","","\t");
			$datas.=$textFile->split($row->Email,"","","\t");
			$datas.=$textFile->split($row->Asken,"","","\t");
			$datas.=$textFile->split($row->Region,"","","\t");
			$datas.=$textFile->split($row->NamaLG,"","","\t");
			$datas.=$textFile->split($row->IDStaffBank,"","","\t");
			$datas.=$textFile->split($row->operid,"","","\t");
			$datas.=$textFile->split($row->sellerid,"","","\t");
			$datas.=$textFile->split($row->spv_id,"","","\t");
			$datas.=$textFile->split($row->am_id,"","","\t");
			$datas.=$textFile->split($row->tsm_id,"","","\t");
			$datas.=$textFile->split($row->EXPDate,"","","\t");
			$datas.=$textFile->split($row->EXPCard,"","","\t");
			$datas.=$textFile->split($row->product_id,"","","\t");
			$datas.=$textFile->split($row->Periode,"","","\t");
			$datas.=$textFile->split($row->PremiumAmount,"","","\r\n");
		}
		
	return $datas;	
		// return $insured_query;
	}
	
	
	function getRowFileBenf($textFile='',$datas=array(),$page=0,$perpage=0){
		global $export_list;
		global $db;

		global $start_date;
		global $end_date;
		global $prodid;
		global $Campaign;
		global $exported;
		global $query;
		global $query_check;
	
		if($page < 1):
			$start = 0;
		else:
			$start = ($page) * $perpage;
		endif;
	 
		// query for data beneficiary
		$benf ="
		SELECT DISTINCT 
				pa.PolicyNumber AS FormNumber,
				plc.PolicyId AS PolicyId,
				bnf.BeneficiaryFirstName as bnf_fname,
				bnf.BeneficiaryLastName as bnf_lname,
				bnf.BeneficiaryDOB as bnf_dob,
				g.GenderShortCode as bnf_sex,
				r.RelationshipTypeCode as bnf_relation,
				bnf.BeneficieryPercentage as bnf_percent

				FROM t_gn_customer AS cst
				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
				inner join t_gn_beneficiary bnf on bnf.CustomerId=cst.CustomerId
				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId
				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId
				left join t_lk_gender g on g.GenderId=bnf.GenderId
				left join t_lk_relationshiptype r on r.RelationshipTypeId=ins.RelationshipTypeId
				WHERE 1 = 1
		";

				
		if ($export_list) {
			$policy_arr = substr($export_list, 1);
		}
		
		$policy_cond = $policy_arr ? " and pa.PolicyNumber in (".$policy_arr.") " : ' and 1 = -1 ';
		$tail_benf= " group by bnf.BeneficiaryId
					  ORDER BY pa.PolicyNumber, bnf.BeneficiaryFirstName ";
		$benf_query = $benf . $policy_cond . $tail_benf;
	 
	 	$query = $db->execute($benf_query,__FILE__,__LINE__);
	
		// write header for policy
	 	$datas.=$textFile->split("[Beneficiary]","","","\r\n");

	 	$datas.=$textFile->split("FormNumber","","","\t");
		$datas.=$textFile->split("PolicyId","","","\t");
		$datas.=$textFile->split("bnf_fname","","","\t");
		$datas.=$textFile->split("bnf_lname","","","\t");
		$datas.=$textFile->split("bnf_dob","","","\t");
		$datas.=$textFile->split("bnf_sex","","","\t");
		$datas.=$textFile->split("bnf_relation","","","\t");		
		$datas.=$textFile->split("bnf_percent","","","\r\n");

		$pol_id='';
		$holder_id=0;
		while($row = $db -> fetchrow($query) ){
			if ($row ->policy_id != $pol_id) {
				$pol_id=$row ->policy_id;
				$holder_id=0;
			}
			$holder_id++;

			$datas.=$textFile->split($row->FormNumber,"","","\t");
			//$datas.=$textFile->split($holder_id,"","","\t");
			$datas.=$textFile->split($row->PolicyId,"","","\t");
			$datas.=$textFile->split($row->bnf_fname,"","","\t");
			$datas.=$textFile->split($row->bnf_lname,"","","\t");
			$datas.=$textFile->split($row->bnf_dob,"","","\t");
			$datas.=$textFile->split($row->bnf_sex,"","","\t");
			$datas.=$textFile->split($row->bnf_relation,"","","\t");
			$datas.=$textFile->split($row->bnf_percent,"","","\r\n");
		}
		
	return $datas;	
	// return $benf_query;	
	}
	// echo exit("why???!!!");
	$textFile = new txtFile();
	// var_dump($textFile);
	$file_name = $prodid.'_'.date('Y-m-d-his').'.xls';
	$textFile -> file = $file_name;
	$dataPolicy = getRowFilePolicy($textFile, $i,$perpages);
	$dataInsured = getRowFileInsured($textFile, $dataPolicy, $i,$perpages);
	$dataBenf = getRowFileBenf($textFile, $dataInsured, $i,$perpages);
	// print_r($_REQUEST);
	if ($export_list!='') {
		// $textFile -> txtWriteLabel($dataBenf);	
		$textFile -> txtWriteLabel($dataBenf);	
		$sql="update t_gn_policyautogen  
			set isexported=1
			where PolicyNumber in (".substr($export_list, 2).")";
		$query = $db->execute($sql,__FILE__,__LINE__);

		header('Content-Type:aplication/text');
		header("Content-Disposition:attachment;filename=$file_name");
		header('Pragma: no-cache');
		readfile('../DownLoadReport/closing/'.$file_name);
	}
	else {
		header('Content-Type:aplication/text');
		header("Content-Disposition:attachment;filename=no_policy.xls");
		header('Pragma: no-cache');
		echo getRowFileBenf($textFile, $dataPolicy, $i,$perpages);
	}
?>