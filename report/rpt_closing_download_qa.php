<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.exportclosing.txt.php");
	
	$start_date  = $_REQUEST['start_date'];
	$end_date  	 = $_REQUEST['end_date'];
	$cignasystem = $_REQUEST['cignasystem'];
	$prodid = $_REQUEST['pprodid'];

	$sdates = explode("-", $start_date );
	$syyyy = $sdates[0];
	$smm   = $sdates[1];
	$sdd   = $sdates[2];
	$new_start_date = $sdates[2]."".$sdates[1]."".$sdates[0];
	$edates = explode("-", $start_date );
	$eyyyy = $edates[0];
	$emm   = $edates[1];
	$edd   = $edates[2];
	$new_end_date = $edates[2]."".$edates[1]."".$edates[0];

	function getRowFilePolicy($textFile='',$page=0,$perpage=0){
		global $db;
		global $yyyy;
		global $mm;
		global $dd;
		global $new_start_date;
		global $new_end_date;
		
		$start_date  = $_REQUEST['start_date'];
		$end_date  	 = $_REQUEST['end_date'];
		$cignasystem = $_REQUEST['cignasystem'];
		$prodid = $_REQUEST['pprodid'];
	
		// $textFile = new txtFile();
		// $textFile -> file ='AXA_'.$new_start_date.'_ClosingAgent.txt';
	
			if($page < 1):
				$start = 0;
			else:
				$start = ($page) * $perpage;
			endif;

	 
		$sql = "SELECT DISTINCT 
				pa.PolicyNumber AS policy_id,
				'' as policy_ref,
				cst.NumberCIF AS prospect_id,
				prd.ProductCode AS product_id,
				cmp.CampaignNumber AS campaign_id,
				cmp.CampaignNumber as campaign_TBSS,
				plc.PolicySalesDate as input,
				plc.PolicyEffectiveDate as effdt,
				'' as payer_cifno,
				s.Salutation as payer_title,
				py.PayerFirstName as payer_fname,
				py.PayerLastName as payer_lname,
				g.GenderShortCode as payer_sex,
				py.PayerDOB as payer_dob,
				'' as addrtype,
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
				pt.PaymentTypeDesc as pay_type,
				ct.CreditCardTypeDesc as card_type,
				bk.BankName as bank,
				'' as branch,
				py.PayerCreditCardNum as acctnum,
				py.PayerCreditCardExpDate as ccexpdate,
				pm.PayModeCode as bill_freq,
				'' as question1,
				'' as question2,
				'' as question3,
				'' as question4,
				'' as question5,
				0 as benefit_level,
				round(sum(plc.Premi),0) as premium,
				round(if(prt.ProductType='PA',plc.Premi, if(pm.PayModeCode='M', if(count(distinct ins.InsuredId)>1, 1, 0)*0.10*12*sum(plc.Premi),if(count(distinct ins.InsuredId)>1, 1, 0)*0.10*sum(plc.Premi))),0) as nbi,
				'N' as export,
				'' as exportdate,
				'' as canceldate,
				cst.CustomerUpdatedTs as callDate2,
				0 as paystatus,
				'' as paynotes,
				'' as payauthcode,
				'' as paytransdate,
				'' as payorderno,
				'' as payccnum,
				'' as paycvv,
				'' as payexpdate,
				'' as paycurency,
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
				'' as operid,
				agt.id as sellerid,
				spv.id as spv_id,
				am.id as atm_id,
				'' as tsm_id,
				'' as pcifnumber,
				'' as pcardtype,
				'' as prefnumber,
				'' as paccnumber,
				'' as paccname,
				'' as pcardnumber,
				'' as record_id,
				cst.CustomerUpdatedTs as callDate,
				cst.CustomerHomePhoneNum2 as phone2,
				cst.CustomerMobilePhoneNum2 as payer_mobilephone2,
				cst.CustomerWorkPhoneNum2 as payer_officephone2
				
				FROM t_gn_customer AS cst
				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId
				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
				inner JOIN t_gn_policy AS plc ON plc.PolicyNumber = pa.PolicyNumber
				
				inner join t_gn_payer py on py.CustomerId=cst.CustomerId
				inner JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
				inner JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
				inner JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
				inner JOIN tms_agent AS spv ON agt.spv_id = spv.UserId
				inner JOIN tms_agent AS am ON spv.mgr_id = am.UserId
				left join t_lk_salutation s on s.SalutationId=py.SalutationId
				left join t_lk_gender g on g.GenderId=py.GenderId
				left join t_lk_province pv on pv.ProvinceId=py.ProvinceId
				left join t_lk_paymenttype pt on pt.PaymentTypeId=py.PaymentTypeId
				left join t_lk_creditcardtype ct on ct.CreditCardTypeId=py.CreditCardTypeId
				left join t_lk_bank bk on bk.BankId=py.PayersBankId
				left join t_lk_paymode pm on pm.PayModeId=prp.PayModeId
				left join t_lk_identificationtype id on id.IdentificationTypeId=py.IdentificationTypeId
				inner join t_lk_producttype prt on prt.ProductTypeId=prd.ProductTypeId
				WHERE date(cst.CustomerUpdatedTs) >= '$start_date'
				and date(cst.CustomerUpdatedTs) <= '$end_date'
				and prd.ProductCode='$prodid'
				-- cst.CallReasonId IN (37,38) 
				group by pa.PolicyNumber
				order by pa.PolicyNumber
				";
	 
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
			$datas.=$textFile->split($row->policy_id,"","","\t");
			$datas.=$textFile->split($row->policy_ref,"","","\t");
			$datas.=$textFile->split($row->prospect_id,"","","\t");
			$datas.=$textFile->split($row->product_id,"","","\t");
			$datas.=$textFile->split($row->campaign_id,"","","\t");
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
			$datas.=$textFile->split($row->operid,"","","\t");
			$datas.=$textFile->split($row->sellerid,"","","\t");
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
			$datas.=$textFile->split($row->calldate,"","","\t");
			$datas.=$textFile->split($row->phone2,"","","\t");
			$datas.=$textFile->split($row->payer_mobilephone2,"","","\t");
			$datas.=$textFile->split($row->payer_officephone2,"","","\r\n");

		}
		
	return $datas;	
	}


	function getRowFileInsured($textFile='',$datas=array(),$page=0,$perpage=0){
		global $db;
		global $yyyy;
		global $mm;
		global $dd;
		global $new_start_date;
		global $new_end_date;
		
		$start_date  = $_REQUEST['start_date'];
		$end_date  	 = $_REQUEST['end_date'];
		$cignasystem = $_REQUEST['cignasystem'];
		$prodid = $_REQUEST['pprodid'];
	
			if($page < 1):
				$start = 0;
			else:
				$start = ($page) * $perpage;
			endif;

	 
		$sql = "SELECT DISTINCT 
				pa.PolicyNumber AS policy_id,
				pg.PremiumGroupOrder as holder_id,
				pg.PremiumGroupCode as holder_type,
				s.Salutation as holder_title,
				ins.InsuredFirstName as holder_fname,
				ins.InsuredLastName as holder_lname,
				g.GenderShortCode as holder_sex,
				ins.InsuredDOB as holder_dob,
				ins.InsuredIdentificationNum as holder_ssn,
				r.RelationshipTypeCode as relation,
				prp.ProductPlan as benefit_level,
				plc.Premi as premium,
				'' as holder_race,
				idt.IdentificationType as holder_idtype,
				0 as holder_issmoker,
				'' as holder_nationality,
				'' as holder_maritalstatus,
				'' as holder_occupation,
				'' as holder_jobtype,
				'' as holder_position,
				0 as holder_height,
				0 as holder_weight,
				'' as uwstatus,
				'' as uwlastupdate,
				'' as uwapprovedate,
				'' as uwprintdate,
				prd.ProductCode as product_id,
				'' as rating_factor1,
				'' as rating_factor2,
				'' as holder_birthplace

				FROM t_gn_customer AS cst
				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
				inner JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId
				inner JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
				left join t_lk_salutation s on s.SalutationId=ins.SalutationId
				left join t_lk_gender g on g.GenderId=ins.GenderId
				left join t_lk_identificationtype idt on idt.IdentificationTypeId=ins.IdentificationTypeId
				left join t_lk_premiumgroup pg on pg.PremiumGroupId=ins.PremiumGroupId
				left join t_lk_relationshiptype r on r.RelationshipTypeId=ins.RelationshipTypeId
				WHERE date(cst.CustomerUpdatedTs) >= '$start_date'
				and date(cst.CustomerUpdatedTs) <= '$end_date'
				and prd.ProductCode='$prodid'
				-- cst.CallReasonId IN (37,38) 
				ORDER BY pa.PolicyNumber, pg.PremiumGroupOrder
				";
	 
	 	$query = $db->execute($sql,__FILE__,__LINE__);
	
		// write header for policy
	 	$datas.=$textFile->split("[Insured]","","","\r\n");

	 	$datas.=$textFile->split("policy_id","","","\t");
		$datas.=$textFile->split("holder_id","","","\t");
		$datas.=$textFile->split("holder_type","","","\t");
		$datas.=$textFile->split("holder_title","","","\t");
		$datas.=$textFile->split("holder_fname","","","\t");
		$datas.=$textFile->split("holder_lname","","","\t");
		$datas.=$textFile->split("holder_sex","","","\t");
		$datas.=$textFile->split("holder_dob","","","\t");
		$datas.=$textFile->split("holder_ssn","","","\t");
		$datas.=$textFile->split("relation","","","\t");
		$datas.=$textFile->split("benefit_level","","","\t");
		$datas.=$textFile->split("premium","","","\t");
		$datas.=$textFile->split("holder_race","","","\t");
		$datas.=$textFile->split("holder_idtype","","","\t");
		$datas.=$textFile->split("holder_issmoker","","","\t");
		$datas.=$textFile->split("holder_nationality","","","\t");
		$datas.=$textFile->split("holder_maritalstatus","","","\t");
		$datas.=$textFile->split("holder_occupation","","","\t");
		$datas.=$textFile->split("holder_jobtype","","","\t");
		$datas.=$textFile->split("holder_position","","","\t");
		$datas.=$textFile->split("holder_height","","","\t");
		$datas.=$textFile->split("holder_weight","","","\t");
		$datas.=$textFile->split("uwstatus","","","\t");
		$datas.=$textFile->split("uwlastupdate","","","\t");
		$datas.=$textFile->split("uwapprovedate","","","\t");
		$datas.=$textFile->split("uwprintdate","","","\t");
		$datas.=$textFile->split("product_id","","","\t");
		$datas.=$textFile->split("rating_factor1","","","\t");
		$datas.=$textFile->split("rating_factor2","","","\t");
		$datas.=$textFile->split("holder_birthplace","","","\r\n");

		while($row = $db -> fetchrow($query) ){
			$datas.=$textFile->split($row->policy_id,"","","\t");
			$datas.=$textFile->split($row->holder_id,"","","\t");
			$datas.=$textFile->split($row->holder_type,"","","\t");
			$datas.=$textFile->split($row->holder_title,"","","\t");
			$datas.=$textFile->split($row->holder_fname,"","","\t");
			$datas.=$textFile->split($row->holder_lname,"","","\t");
			$datas.=$textFile->split($row->holder_sex,"","","\t");
			$datas.=$textFile->split($row->holder_dob,"","","\t");
			$datas.=$textFile->split($row->holder_ssn,"","","\t");
			$datas.=$textFile->split($row->relation,"","","\t");
			$datas.=$textFile->split($row->benefit_level,"","","\t");
			$datas.=$textFile->split($row->premium,"","","\t");
			$datas.=$textFile->split($row->holder_race,"","","\t");
			$datas.=$textFile->split($row->holder_idtype,"","","\t");
			$datas.=$textFile->split($row->holder_issmoker,"","","\t");
			$datas.=$textFile->split($row->holder_nationality,"","","\t");
			$datas.=$textFile->split($row->holder_maritalstatus,"","","\t");
			$datas.=$textFile->split($row->holder_occupation,"","","\t");
			$datas.=$textFile->split($row->holder_jobtype,"","","\t");
			$datas.=$textFile->split($row->holder_position,"","","\t");
			$datas.=$textFile->split($row->holder_height,"","","\t");
			$datas.=$textFile->split($row->holder_weight,"","","\t");
			$datas.=$textFile->split($row->uwstatus,"","","\t");
			$datas.=$textFile->split($row->uwlastupdate,"","","\t");
			$datas.=$textFile->split($row->uwapprovedate,"","","\t");
			$datas.=$textFile->split($row->uwprintdate,"","","\t");
			$datas.=$textFile->split($row->product_id,"","","\t");
			$datas.=$textFile->split($row->rating_factor1,"","","\t");
			$datas.=$textFile->split($row->rating_factor2,"","","\t");
			$datas.=$textFile->split($row->holder_birthplace,"","","\r\n");


		}
		
		//@mysql_free_result($query);
		// $textFile -> txtWriteLabel($datas);	
	return $datas;	
	}
	
	
	function getRowFileBenf($textFile='',$datas=array(),$page=0,$perpage=0){
		global $db;
		global $yyyy;
		global $mm;
		global $dd;
		global $new_start_date;
		global $new_end_date;
		
		$start_date  = $_REQUEST['start_date'];
		$end_date  	 = $_REQUEST['end_date'];
		$cignasystem = $_REQUEST['cignasystem'];
		$prodid = $_REQUEST['pprodid'];
	
			if($page < 1):
				$start = 0;
			else:
				$start = ($page) * $perpage;
			endif;

	 
		// query for data beneficiary
		$sql ="SELECT DISTINCT 
				pa.PolicyNumber AS policy_id,
				'' as holder_id,
				'' as bnf_id,
				bnf.BeneficiaryFirstName as bnf_fname,
				bnf.BeneficiaryLastName as bnf_lname,
				g.GenderShortCode as bnf_sex,
				bnf.BeneficiaryIdentificationNum as bnf_ssn,
				'' as bnf_bene_ind,
				'' as bnf_client_type,
				bnf.BeneficieryPercentage as bnf_percent,
				'' as bnf_coverage,
				r.RelationshipTypeCode as bnf_relation,
				bnf.BeneficiaryDOB as bnf_dob

				FROM t_gn_customer AS cst
				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
				inner join t_gn_beneficiary bnf on bnf.CustomerId=cst.CustomerId
				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId
				inner join t_gn_product prd on prd.ProductId=pa.ProductId
				left join t_lk_gender g on g.GenderId=bnf.GenderId
				left join t_lk_relationshiptype r on r.RelationshipTypeId=ins.RelationshipTypeId
				WHERE date(cst.CustomerUpdatedTs) >= '$start_date'
				and date(cst.CustomerUpdatedTs) <= '$end_date'
				and prd.ProductCode='$prodid'
				-- cst.CallReasonId IN (37,38) 
				ORDER BY pa.PolicyNumber
				";
	 
	 	$query = $db->execute($sql,__FILE__,__LINE__);
	
		// write header for policy
	 	$datas.=$textFile->split("[Beneficiary]","","","\r\n");

	 	$datas.=$textFile->split("policy_id","","","\t");
		$datas.=$textFile->split("holder_id","","","\t");
		$datas.=$textFile->split("bnf_id","","","\t");
		$datas.=$textFile->split("bnf_fname","","","\t");
		$datas.=$textFile->split("bnf_lname","","","\t");
		$datas.=$textFile->split("bnf_sex","","","\t");
		$datas.=$textFile->split("bnf_ssn","","","\t");
		$datas.=$textFile->split("bnf_bene_ind","","","\t");
		$datas.=$textFile->split("bnf_client_type","","","\t");
		$datas.=$textFile->split("bnf_percent","","","\t");
		$datas.=$textFile->split("bnf_coverage","","","\t");
		$datas.=$textFile->split("bnf_relation","","","\t");
		$datas.=$textFile->split("bnf_dob","","","\r\n");


		while($row = $db -> fetchrow($query) ){
			$datas.=$textFile->split($row->policy_id,"","","\t");
			$datas.=$textFile->split($row->holder_id,"","","\t");
			$datas.=$textFile->split($row->bnf_id,"","","\t");
			$datas.=$textFile->split($row->bnf_fname,"","","\t");
			$datas.=$textFile->split($row->bnf_lname,"","","\t");
			$datas.=$textFile->split($row->bnf_sex,"","","\t");
			$datas.=$textFile->split($row->bnf_ssn,"","","\t");
			$datas.=$textFile->split($row->bnf_bene_ind,"","","\t");
			$datas.=$textFile->split($row->bnf_client_type,"","","\t");
			$datas.=$textFile->split($row->bnf_percent,"","","\t");
			$datas.=$textFile->split($row->bnf_coverage,"","","\t");
			$datas.=$textFile->split($row->bnf_relation,"","","\t");
			$datas.=$textFile->split($row->bnf_dob,"","","\r\n");
		}
		
		//@mysql_free_result($query);
			
	return $datas;	
	}

	$textFile = new txtFile();
	$textFile -> file ='AXA_'.$new_start_date.'_VerifiedQA.txt';

	$dataPolicy = getRowFilePolicy($textFile, $i,$perpages);
	$dataInsured = getRowFileInsured($textFile, $dataPolicy, $i,$perpages);
	$dataBenf = getRowFileBenf($textFile, $dataInsured, $i,$perpages);

	$textFile -> txtWriteLabel($dataBenf);	

	if( $sizePage>0) echo 1;
	else echo 0;
?>	

	


