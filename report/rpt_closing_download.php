<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.exportclosing.txt.php");
	
	$start_date  = $_REQUEST['start_date'];
	$end_date  	 = $_REQUEST['end_date'];
	$cignasystem = $_REQUEST['cignasystem'];
	

	
	function getRowFile($page=0,$perpage=0){
		global $db;
	
		$textFile = new txtFile();
		$textFile -> file ='Report_Download_TXT(Closing-QA)'.$_REQUEST['start_date'].'To'.$_REQUEST['start_date'].'.txt';
	
			if($page < 1):
				$start = 0;
			else:
				$start = ($page) * $perpage;
			endif;

	 
		$sql = " SELECT Distinct c.CustomerNumber AS CustomerId,
							'' AS dtfr,
							'' AS dtto,
							cs.CignaSystemCode AS system,
							p.PolicyNumber AS policy_id,
							'' AS policy_ref,
							c.CustomerNumber AS prospect_id,
							pr.ProductCode AS product_id,
							cg.CampaignGroupCode AS campaign_id,
							ca.CampaignNumber AS campaign_tbbs,
							p.PolicySalesDate AS input,
							p.PolicyEffectiveDate AS effdt,
							pas.SalutationCode AS payer_title,
							pa.PayerFirstName AS payer_fname,
							pa.PayerLastName AS payer_lname,
							pag.GenderCode AS payer_sex,
							pa.PayerDOB AS payer_dob,
							pa.PayerAddressLine1 AS addr1,
							pa.PayerAddressLine2 AS addr2,
							pa.PayerAddressLine3 AS addr3,
							pa.PayerAddressLine4 AS addr4,
							pa.PayerCity AS city,
							pa.PayerZipCode AS post,
							pap.ProvinceCode AS province,
							pa.PayerHomePhoneNum AS phone,
							pa.PayerFaxNum AS faxphone,
							pa.PayerEmail AS email,
							(select distinct min(beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId ) as id1,
							(select distinct (beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId and bnf.BeneficiaryId > id1 limit 1 ) as id2,
							(select distinct (beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId and bnf.BeneficiaryId > id2 limit 1 ) as id3,
							(select distinct (beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId and bnf.BeneficiaryId > id3 limit 1 ) as id4,
							(select distinct (beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId and bnf.BeneficiaryId > id4 limit 1 ) as id5,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_lastname,
							(select bnf.BeneficiaryfirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_ssn,
							'' as bnf1_bene_ind,
							'' as bnf1_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_percent1,
							(select if(bnf1_percent1>0,bnf1_percent1,0) ) as bnf1_percent,
							'' as bnf1_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id1 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf1_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId limit 1) as bnf2_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId limit 1) as bnf2_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_ssn,
							'' as bnf2_bene_ind,
							'' as bnf2_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_percent1,
							(select if(bnf2_percent1>0,bnf2_percent1,0) ) as bnf2_percent,
							'' as bnf2_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id2 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf2_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId limit 1) as bnf3_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId limit 1) as bnf3_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_ssn,
							'' as bnf3_bene_ind,
							'' as bnf3_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_percent1,
							(select if(bnf3_percent1>0,bnf3_percent1,0) ) as bnf3_percent,
							'' as bnf3_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id3 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf3_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId limit 1) as bnf4_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId limit 1) as bnf4_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_ssn,
							'' as bnf4_bene_ind,
							'' as bnf4_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_percent1,
							(select if(bnf4_percent1>0,bnf4_percent1,0) ) as bnf4_percent,
							'' as bnf4_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id4 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf4_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId limit 1) as bnf5_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId limit 1) as bnf5_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_ssn,
							'' as bnf5_bene_ind,
							'' as bnf5_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_percent1,
							(select if(bnf5_percent1>0,bnf5_percent1,0) ) as bnf5_percent,
							'' as bnf5_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id5 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf5_relation,
							10 AS pay_type,
							cct.CreditCardTypeCode AS card_type,
							'NULL' AS bank,
							'' AS branch,
							pa.PayerCreditCardNum AS acctnum,
							pa.PayerCreditCardExpDate AS ccexpdate,
							pm.PayModeCode AS bill_freq,
							'' AS question1,
							'' AS question2,
							'' AS question3,
							'' AS benefit_level1,
							'' AS premium,
							'' AS operid,
							ta.id AS sellerid,
							'' AS spv_id,
							'' AS export,
							'' AS exportdate,
							'' AS canceldate,
							'' AS callDate2,
							'' AS paystatus,
							'' AS paynotes,
							'' AS payauthcode,
							'' AS paytransdate,
							'' AS payorderno,
							'' AS payccnum,
							'' AS paycvv,
							'' AS payexpdate,
							'' AS paycurency,
							'' AS paycardtype,
							'' AS payer_idtype,
							pa.PayerIdentificationNum AS payer_personalid,
							pa.PayerMobilePhoneNum AS payer_mobilephone,
							pa.PayerWorkPhoneNum AS payer_officephone,
							'' AS delivery_date,
							'' AS payer_age,
							'' AS currency,
							'' AS class,
							'' AS ratingfactors,
							'' AS mi_min,
							'' AS mi_max,
							'' AS mi_ren,
							'' AS sp_min,
							'' AS sp_max,
							'' AS sp_ren,
							'' AS dp_min,
							'' AS dp_max,
							'' AS dp_ren,
							'' AS ratingoptions,
							'' AS beneficiary,
							'' AS policyprefix,
							'' AS ei_mi,
							'' AS ei_sp,
							'' AS ei_dp,
							'' AS cc_mi,
							'' AS cc_mi_sp,
							'' AS cc_mi_fam,
							'' AS cc_mi_dp,
							'' AS cc_sp_dp,
							'' AS cc_sp,
							'' AS cc_dp,
							'' AS ben_level,
							'' AS htype,
							s.SalutationCode AS holder_title,
							i.InsuredAge AS holder_age,
							pg.PremiumGroupCode AS holder_type,
							s.SalutationCode AS h_title,
							i.InsuredFirstName AS holder_fname,
							i.InsuredLastName AS holder_lname,
							g.GenderCode AS holder_sex,
							i.InsuredDOB AS holder_dob,
							rt.RelationshipTypeCode AS relation,
							pp.ProductPlanPremium AS premi,
							i.InsuredIdentificationNum AS holder_ssn,
							pp.ProductPlan AS benefit_level,
							'' AS holder_race,
							'' AS holder_idtype,
							1 AS holder_issmoker,
							'' AS holder_nationality,
							'NULL' AS holder_maritalstatus,
							'' AS holder_occupation,
							'' AS holder_jobtype,
							'' AS holder_position,
							'' AS holder_height,
							'' AS holder_weight,
							'' AS uwstatus,
							'' AS uwlastupdate,
							'' AS uwapprovedate,
							'' AS uwprintdate,
							'NULL' AS holder_id,
							'NULL' AS question_id,
							'' AS answer,
							'' AS remark,
							'' AS seq_no,
							cr.CallReasonCode AS call_id,
							'NULL' AS bmimax,
							'NULL' AS bmimin,
							ct.CampaignTypeCode AS camptype
							FROM t_gn_customer c
							LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
							LEFT JOIN t_lk_cignasystem cs ON ca.CignaSystemId = cs.CignaSystemId
							LEFT JOIN t_gn_insured i ON c.CustomerId = i.CustomerId
							LEFT JOIN t_gn_policy p ON i.PolicyId = p.PolicyId
							LEFT JOIN t_gn_productplan pp ON p.ProductPlanId = pp.ProductPlanId
							LEFT JOIN t_gn_product pr ON pp.ProductId = pr.ProductId
							LEFT JOIN t_gn_campaigngroup cg ON pr.CampaignGroupId = cg.CampaignGroupId
							LEFT JOIN t_gn_payer pa ON c.CustomerId = pa.CustomerId
							LEFT JOIN t_lk_salutation pas ON pa.SalutationId = pas.SalutationId
							LEFT JOIN t_lk_gender pag ON pa.GenderId = pag.GenderId
							LEFT JOIN t_lk_province pap ON pa.ProvinceId = pap.ProvinceId
							LEFT JOIN t_lk_paymenttype pt ON pa.PaymentTypeId = pt.PaymentTypeId
							LEFT JOIN t_lk_creditcardtype cct ON pa.CreditCardTypeId = cct.CreditCardTypeId
							LEFT JOIN t_lk_validccprefix vcp ON pa.ValidCCPrefixId = vcp.ValidCCPrefixId
							LEFT JOIN t_lk_bank b ON vcp.BankId = b.BankId
							LEFT JOIN t_lk_paymode pm ON pp.PayModeId = pm.PayModeId
							LEFT JOIN tms_agent ta ON i.CreatedById = ta.UserId
							LEFT JOIN t_lk_premiumgroup pg ON i.PremiumGroupId = pg.PremiumGroupId
							LEFT JOIN t_lk_salutation s ON i.SalutationId = s.SalutationId
							LEFT JOIN t_lk_gender g ON i.GenderId = g.GenderId
							LEFT JOIN t_lk_relationshiptype rt ON i.RelationshipTypeId = rt.RelationshipTypeId
							LEFT JOIN t_gn_callhistory ch ON c.CustomerId = ch.CustomerId AND c.CallReasonId = ch.CallReasonId
							LEFT JOIN t_lk_callreason cr ON ch.CallReasonId = cr.CallReasonId
							LEFT JOIN t_lk_campaigntype ct ON ca.CampaignTypeId = ct.CampaignTypeId
							LEFT JOIN t_gn_beneficiary bnf ON bnf.CustomerId = c.customerid
							LEFT JOIN t_gn_assignment asg ON asg.CustomerId = c.customerid
							where c.CallReasonId in (16,17,37,38,39,40,41,42) AND asg.AssignBlock =0
							AND date( c.customerupdatedts) >='".$_REQUEST['start_date']."' AND date( c.customerupdatedts) <='".$_REQUEST['end_date']."'
							AND (cs.cignasystemcode)like '%".$cignasystem."%'
							AND ca.campaignstatusflag = 1
							ORDER BY c.CustomerNumber ";
							// echo $sql;
	 
	 $query = $db->execute($sql,__FILE__,__LINE__);
	
		
		while($row = $db -> fetchrow($query) ){
			$datas.=$textFile->split($row->CustomerId,"dull","CustomerId","|");
			$datas.=$textFile->split($row->dtfr,"dull","dtfr","|");
			$datas.=$textFile->split($row->dtto,"dull","dtto","|");
			$datas.=$textFile->split($row->system,"dull","system","|");
			$datas.=$textFile->split($row->policy_id,"dull","Policy_id","|");
			$datas.=$textFile->split($row->policy_ref,"dull","Policy_ref","|");
			$datas.=$textFile->split($row->CustomerId,"dull","CustomerId","|");
			$datas.=$textFile->split($row->product_id,"dull","product_id","|");
			$datas.=$textFile->split($row->campaign_id,"dull","campaign_id","|");
			$datas.=$textFile->split($row->campaign_tbbs,"dull","campaign_tbbs","|");
			$datas.=$textFile->split($row->input,"dull","input","|");
			$datas.=$textFile->split($row->effdt,"dull","effdt","|");
			$datas.=$textFile->split($row->payer_title,"dull","payer_title","|");
			$datas.=$textFile->split($row->payer_fname,"dull","payer_fname","|");
			$datas.=$textFile->split($row->payer_lname,"dull","payer_lname","|");
			$datas.=$textFile->split($row->payer_sex,"dull","payer_sex","|");
			$datas.=$textFile->split($row->payer_dob,"dull","payer_dob","|");
			$datas.=$textFile->split($row->addr1,"dull","addr1","|");
			$datas.=$textFile->split($row->addr2,"dull","addr2","|");
			$datas.=$textFile->split($row->addr3,"dull","addr3","|");
			$datas.=$textFile->split($row->addr4,"dull","addr4","|");
			$datas.=$textFile->split($row->city,"dull","city","|");
			$datas.=$textFile->split($row->post,"dull","post","|");
			$datas.=$textFile->split($row->province,"dull","province","|");
			$datas.=$textFile->split($row->phone,"dull","phone","|");
			$datas.=$textFile->split($row->faxphone,"dull","faxphone","|");
			$datas.=$textFile->split($row->email,"dull","email","|");
			$datas.=$textFile->split($row->Bnf1_lastname,"dull","Bnf1_lastname","|");
			$datas.=$textFile->split($row->bnf1_firstname,"dull","bnf1_firstname","|");
			$datas.=$textFile->split($row->bnf1_sex,"dull","bnf1_sex","|");
			$datas.=$textFile->split($row->bnf1_ssn,"dull","bnf1_ssn","|");
			$datas.=$textFile->split($row->bnf1_bene_ind,"dull","bnf1_bene_ind","|");
			$datas.=$textFile->split($row->bnf1_client_type,"dull","bnf1_client_type","|");
			$datas.=$textFile->split($row->bnf1_percent,"dull","bnf1_percent","|");
			$datas.=$textFile->split($row->bnf1_coverage,"dull","bnf1_coverage","|");
			$datas.=$textFile->split($row->bnf1_relation,"dull","bnf1_relation","|");
			$datas.=$textFile->split($row->bnf2_lastname,"dull","bnf2_lastname","|");
			$datas.=$textFile->split($row->bnf2_firstname,"dull","bnf2_firstname","|");
			$datas.=$textFile->split($row->bnf2_sex,"dull","bnf2_sex","|");
			$datas.=$textFile->split($row->bnf2_ssn,"dull","bnf2_ssn","|");
			$datas.=$textFile->split($row->bnf2_bene_ind,"dull","bnf2_bene_ind","|");
			$datas.=$textFile->split($row->bnf2_client_type,"dull","bnf2_client_type","|");
			$datas.=$textFile->split($row->bnf2_percent,"dull","bnf2_percent","|");
			$datas.=$textFile->split($row->bnf2_coverage,"dull","bnf2_coverage","|");
			$datas.=$textFile->split($row->bnf2_relation,"dull","bnf2_relation","|");
			$datas.=$textFile->split($row->bnf3_lastname,"dull","bnf3_lastname","|");
			$datas.=$textFile->split($row->bnf3_firstname,"dull","bnf3_firstname","|");
			$datas.=$textFile->split($row->bnf3_sex,"dull","bnf3_sex","|");
			$datas.=$textFile->split($row->bnf3_ssn,"dull","bnf3_ssn","|");
			$datas.=$textFile->split($row->bnf3_bene_ind,"dull","bnf3_bene_ind","|");
			$datas.=$textFile->split($row->bnf3_client_type,"dull","bnf3_client_type","|");
			$datas.=$textFile->split($row->bnf3_percent,"dull","bnf3_percent","|");
			$datas.=$textFile->split($row->bnf3_coverage,"dull","bnf3_coverage","|");
			$datas.=$textFile->split($row->bnf3_relation,"dull","bnf3_relation","|");
			$datas.=$textFile->split($row->bnf4_lastname,"dull","bnf4_lastname","|");
			$datas.=$textFile->split($row->bnf4_firstname,"dull","bnf4_firstname","|");
			$datas.=$textFile->split($row->bnf4_sex,"dull","bnf4_sex","|");
			$datas.=$textFile->split($row->bnf4_ssn,"dull","bnf4_ssn","|");
			$datas.=$textFile->split($row->bnf4_bene_ind,"dull","bnf4_bene_ind","|");
			$datas.=$textFile->split($row->bnf4_client_type,"dull","bnf4_client_type","|");
			$datas.=$textFile->split($row->bnf4_percent,"dull","bnf4_percent","|");
			$datas.=$textFile->split($row->bnf4_coverage,"dull","bnf4_coverage","|");
			$datas.=$textFile->split($row->bnf4_relation,"dull","bnf4_relation","|");
			$datas.=$textFile->split($row->bnf5_lastname,"dull","bnf5_lastname","|");
			$datas.=$textFile->split($row->bnf5_firstname,"dull","bnf5_firstname","|");
			$datas.=$textFile->split($row->bnf5_sex,"dull","bnf5_sex","|");
			$datas.=$textFile->split($row->bnf5_ssn,"dull","bnf5_ssn","|");
			$datas.=$textFile->split($row->bnf5_bene_ind,"dull","bnf5_bene_ind","|");
			$datas.=$textFile->split($row->bnf5_client_type,"dull","bnf5_client_type","|");
			$datas.=$textFile->split($row->bnf5_percent,"dull","bnf5_percent","|");
			$datas.=$textFile->split($row->bnf5_coverage,"dull","bnf5_coverage","|");
			$datas.=$textFile->split($row->bnf5_relation,"dull","bnf5_relation","|");
			$datas.=$textFile->split($row->pay_type,"dull","pay_type","|");
			$datas.=$textFile->split($row->card_type,"dull","card_type","|");
			$datas.=$textFile->split($row->bank,"dull","bank","|");
			$datas.=$textFile->split($row->branch,"dull","branch","|");
			$datas.=$textFile->split($row->acctnum,"dull","acctnum","|");
			$datas.=$textFile->split($row->ccexpdate,"dull","ccexpdate","|");
			$datas.=$textFile->split($row->bill_freq,"dull","bill_freq","|");
			$datas.=$textFile->split($row->question1,"dull","question1","|");
			$datas.=$textFile->split($row->question2,"dull","question2","|");
			$datas.=$textFile->split($row->question3,"dull","question3","|");
			$datas.=$textFile->split($row->benefit_level1,"dull","benefit_level1","|");
			$datas.=$textFile->split($row->premium,"dull","premium","|");
			$datas.=$textFile->split($row->operid,"dull","operid","|");
			$datas.=$textFile->split($row->sellerid,"dull","sellerid","|");
			$datas.=$textFile->split($row->spv_id,"dull","spv_id","|");
			$datas.=$textFile->split($row->export,"dull","export","|");
			$datas.=$textFile->split($row->exportdate,"dull","exportdate","|");
			$datas.=$textFile->split($row->canceldate,"dull","canceldate","|");
			$datas.=$textFile->split($row->callDate2,"dull","callDate2","|");
			$datas.=$textFile->split($row->paystatus,"dull","paystatus","|");
			$datas.=$textFile->split($row->paynotes,"dull","paynotes","|");
			$datas.=$textFile->split($row->payauthcode,"dull","payauthcode","|");
			$datas.=$textFile->split($row->paytransdate,"dull","paytransdate","|");
			$datas.=$textFile->split($row->payorderno,"dull","payorderno","|");
			$datas.=$textFile->split($row->payccnum,"dull","payccnum","|");
			$datas.=$textFile->split($row->paycvv,"dull","paycvv","|");
			$datas.=$textFile->split($row->payexpdate,"dull","payexpdate","|");
			$datas.=$textFile->split($row->paycurrency,"dull","paycurrency","|");
			$datas.=$textFile->split($row->paycardtype,"dull","paycardtype","|");
			$datas.=$textFile->split($row->payer_idtype,"dull","payer_idtype","|");
			$datas.=$textFile->split($row->payer_personalid,"dull","payer_personalid","|");
			$datas.=$textFile->split($row->payer_mobilephone,"dull","payer_mobilephone","|");
			$datas.=$textFile->split($row->payer_officephone,"dull","payer_officephone","|");
			$datas.=$textFile->split($row->delivery_date,"dull","delivery_date","|");
			$datas.=$textFile->split($row->payer_age,"dull","payer_age","|");
			$datas.=$textFile->split($row->currency,"dull","currency","|");
			$datas.=$textFile->split($row->class,"dull","class","|");
			$datas.=$textFile->split($row->ratingfactors,"dull","ratingfactors","|");
			$datas.=$textFile->split($row->mi_min,"dull","mi_min","|");
			$datas.=$textFile->split($row->mi_max,"dull","mi_max","|");
			$datas.=$textFile->split($row->mi_ren,"dull","mi_ren","|");
			$datas.=$textFile->split($row->sp_min,"dull","sp_min","|");
			$datas.=$textFile->split($row->sp_max,"dull","sp_max","|");
			$datas.=$textFile->split($row->sp_ren,"dull","sp_ren","|");
			$datas.=$textFile->split($row->dp_min,"dull","dp_min","|");
			$datas.=$textFile->split($row->dp_max,"dull","dp_max","|");
			$datas.=$textFile->split($row->dp_ren,"dull","dp_ren","|");
			$datas.=$textFile->split($row->ratingoptions,"dull","ratingoptions","|");
			$datas.=$textFile->split($row->beneficiary,"dull","beneficiary","|");
			$datas.=$textFile->split($row->policyprefix,"dull","policyprefix","|");
			$datas.=$textFile->split($row->ei_mi,"dull","ei_mi","|");
			$datas.=$textFile->split($row->ei_sp,"dull","ei_sp","|");
			$datas.=$textFile->split($row->ei_dp,"dull","ei_dp","|");
			$datas.=$textFile->split($row->cc_mi,"dull","cc_mi","|");
			$datas.=$textFile->split($row->cc_mi_sp,"dull","cc_mi_sp","|");
			$datas.=$textFile->split($row->cc_mi_fam,"dull","cc_mi_fam","|");
			$datas.=$textFile->split($row->cc_mi_dp,"dull","cc_mi_dp","|");
			$datas.=$textFile->split($row->cc_sp_dp,"dull","cc_sp_dp","|");
			$datas.=$textFile->split($row->cc_sp,"dull","cc_sp","|");
			$datas.=$textFile->split($row->cc_dp,"dull","cc_dp","|");
			$datas.=$textFile->split($row->ben_level,"dull","ben_level","|");
			$datas.=$textFile->split($row->htype,"dull","htype","|");
			$datas.=$textFile->split($row->holder_title,"dull","holder_tittle","|");
			$datas.=$textFile->split($row->holder_age,"dull","holder_age","|");
			$datas.=$textFile->split($row->holder_type,"dull","holder_type","|");
			$datas.=$textFile->split($row->h_title,"dull","h_title","|");
			$datas.=$textFile->split($row->holder_fname,"dull","holder_fname","|");
			$datas.=$textFile->split($row->holder_lname,"dull","holder_lname","|");
			$datas.=$textFile->split($row->holder_sex,"dull","holder_sex","|");
			$datas.=$textFile->split($row->holder_dob,"dull","holder_dob","|");
			$datas.=$textFile->split($row->relation,"dull","relation","|");
			$datas.=$textFile->split($row->premi,"dull","premi","|");
			$datas.=$textFile->split($row->holder_ssn,"dull","holder_ssn","|");
			$datas.=$textFile->split($row->benefit_level,"dull","benefit_level","|");
			$datas.=$textFile->split($row->holder_race,"dull","holder_race","|");
			$datas.=$textFile->split($row->holder_idtype,"dull","holder_idtype","|");
			$datas.=$textFile->split($row->holder_issmoker,"dull","holder_issmoker","|");
			$datas.=$textFile->split($row->holder_nationality,"dull","holder_nationality","|");
			$datas.=$textFile->split($row->holder_maritalstatus,"dull","holder_maritalstatus","|");
			$datas.=$textFile->split($row->holder_occupation,"dull","holder_occupation","|");
			$datas.=$textFile->split($row->holder_jobtype,"dull","holder_jobtype","|");
			$datas.=$textFile->split($row->holder_position,"dull","holder_position","|");
			$datas.=$textFile->split($row->holder_height,"dull","holder_height","|");
			$datas.=$textFile->split($row->holder_weight,"dull","holder_weight","|");
			$datas.=$textFile->split($row->uwstatus,"dull","uwstatus","|");
			$datas.=$textFile->split($row->uwlastupdated,"dull","uwlastupdated","|");
			$datas.=$textFile->split($row->uwapprovedate,"dull","uwapprovedate","|");
			$datas.=$textFile->split($row->uwprintdate,"dull","uwprintdate","|");
			$datas.=$textFile->split($row->holder_id,"dull","holder_id","|");
			$datas.=$textFile->split($row->question_id,"dull","question_id","|");
			$datas.=$textFile->split($row->answer,"dull","answer","|");
			$datas.=$textFile->split($row->remark,"dull","remark","|");
			$datas.=$textFile->split($row->seq_no,"dull","seq_no","|");
			$datas.=$textFile->split($row->call_id,"dull","call_id","|");
			$datas.=$textFile->split($row->bmimax,"dull","bmimax","|");
			$datas.=$textFile->split($row->bmimin,"dull","bmimin","|");
			$datas.=$textFile->split($row->camptype,"dull","camptype","")."\r\n";
		}
		
		//@mysql_free_result($query);
		$textFile -> txtWriteLabel($datas);	
	return true;	
	}

			$sql = " SELECT Distinct c.CustomerNumber AS CustomerId,
							'' AS dtfr,
							'' AS dtto,
							cs.CignaSystemCode AS system,
							p.PolicyNumber AS policy_id,
							'' AS policy_ref,
							c.CustomerNumber AS prospect_id,
							pr.ProductCode AS product_id,
							cg.CampaignGroupCode AS campaign_id,
							ca.CampaignNumber AS campaign_tbbs,
							p.PolicySalesDate AS input,
							p.PolicyEffectiveDate AS effdt,
							pas.SalutationCode AS payer_title,
							pa.PayerFirstName AS payer_fname,
							pa.PayerLastName AS payer_lname,
							pag.GenderCode AS payer_sex,
							pa.PayerDOB AS payer_dob,
							pa.PayerAddressLine1 AS addr1,
							pa.PayerAddressLine2 AS addr2,
							pa.PayerAddressLine3 AS addr3,
							pa.PayerAddressLine4 AS addr4,
							pa.PayerCity AS city,
							pa.PayerZipCode AS post,
							pap.ProvinceCode AS province,
							pa.PayerHomePhoneNum AS phone,
							pa.PayerFaxNum AS faxphone,
							pa.PayerEmail AS email,
							(select distinct min(beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId ) as id1,
							(select distinct (beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId and bnf.BeneficiaryId > id1 limit 1 ) as id2,
							(select distinct (beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId and bnf.BeneficiaryId > id2 limit 1 ) as id3,
							(select distinct (beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId and bnf.BeneficiaryId > id3 limit 1 ) as id4,
							(select distinct (beneficiaryid)from t_gn_beneficiary bnf where c.CustomerId = bnf.CustomerId and bnf.BeneficiaryId > id4 limit 1 ) as id5,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_lastname,
							(select bnf.BeneficiaryfirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_ssn,
							'' as bnf1_bene_ind,
							'' as bnf1_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_percent1,
							(select if(bnf1_percent1>0,bnf1_percent1,0) ) as bnf1_percent,
							'' as bnf1_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id1 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf1_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId limit 1) as bnf2_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId limit 1) as bnf2_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_ssn,
							'' as bnf2_bene_ind,
							'' as bnf2_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_percent1,
							(select if(bnf2_percent1>0,bnf2_percent1,0) ) as bnf2_percent,
							'' as bnf2_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id2 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf2_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId limit 1) as bnf3_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId limit 1) as bnf3_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_ssn,
							'' as bnf3_bene_ind,
							'' as bnf3_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_percent1,
							(select if(bnf3_percent1>0,bnf3_percent1,0) ) as bnf3_percent,
							'' as bnf3_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id3 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf3_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId limit 1) as bnf4_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId limit 1) as bnf4_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_ssn,
							'' as bnf4_bene_ind,
							'' as bnf4_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_percent1,
							(select if(bnf4_percent1>0,bnf4_percent1,0) ) as bnf4_percent,
							'' as bnf4_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id4 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf4_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId limit 1) as bnf5_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId limit 1) as bnf5_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_ssn,
							'' as bnf5_bene_ind,
							'' as bnf5_client_type,
							(select bnf.BeneficieryPercentage from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_percent1,
							(select if(bnf5_percent1>0,bnf5_percent1,0) ) as bnf5_percent,
							'' as bnf5_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id5 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf5_relation,
							10 AS pay_type,
							cct.CreditCardTypeCode AS card_type,
							'NULL' AS bank,
							'' AS branch,
							pa.PayerCreditCardNum AS acctnum,
							pa.PayerCreditCardExpDate AS ccexpdate,
							pm.PayModeCode AS bill_freq,
							'' AS question1,
							'' AS question2,
							'' AS question3,
							'' AS benefit_level1,
							'' AS premium,
							'' AS operid,
							ta.id AS sellerid,
							'' AS spv_id,
							'' AS export,
							'' AS exportdate,
							'' AS canceldate,
							'' AS callDate2,
							'' AS paystatus,
							'' AS paynotes,
							'' AS payauthcode,
							'' AS paytransdate,
							'' AS payorderno,
							'' AS payccnum,
							'' AS paycvv,
							'' AS payexpdate,
							'' AS paycurency,
							'' AS paycardtype,
							'' AS payer_idtype,
							pa.PayerIdentificationNum AS payer_personalid,
							pa.PayerMobilePhoneNum AS payer_mobilephone,
							pa.PayerWorkPhoneNum AS payer_officephone,
							'' AS delivery_date,
							'' AS payer_age,
							'' AS currency,
							'' AS class,
							'' AS ratingfactors,
							'' AS mi_min,
							'' AS mi_max,
							'' AS mi_ren,
							'' AS sp_min,
							'' AS sp_max,
							'' AS sp_ren,
							'' AS dp_min,
							'' AS dp_max,
							'' AS dp_ren,
							'' AS ratingoptions,
							'' AS beneficiary,
							'' AS policyprefix,
							'' AS ei_mi,
							'' AS ei_sp,
							'' AS ei_dp,
							'' AS cc_mi,
							'' AS cc_mi_sp,
							'' AS cc_mi_fam,
							'' AS cc_mi_dp,
							'' AS cc_sp_dp,
							'' AS cc_sp,
							'' AS cc_dp,
							'' AS ben_level,
							'' AS htype,
							s.SalutationCode AS holder_title,
							i.InsuredAge AS holder_age,
							pg.PremiumGroupCode AS holder_type,
							s.SalutationCode AS h_title,
							i.InsuredFirstName AS holder_fname,
							i.InsuredLastName AS holder_lname,
							g.GenderCode AS holder_sex,
							i.InsuredDOB AS holder_dob,
							rt.RelationshipTypeCode AS relation,
							pp.ProductPlanPremium AS premi,
							i.InsuredIdentificationNum AS holder_ssn,
							pp.ProductPlan AS benefit_level,
							'' AS holder_race,
							'' AS holder_idtype,
							1 AS holder_issmoker,
							'' AS holder_nationality,
							'NULL' AS holder_maritalstatus,
							'' AS holder_occupation,
							'' AS holder_jobtype,
							'' AS holder_position,
							'' AS holder_height,
							'' AS holder_weight,
							'' AS uwstatus,
							'' AS uwlastupdate,
							'' AS uwapprovedate,
							'' AS uwprintdate,
							'NULL' AS holder_id,
							'NULL' AS question_id,
							'' AS answer,
							'' AS remark,
							'' AS seq_no,
							cr.CallReasonCode AS call_id,
							'NULL' AS bmimax,
							'NULL' AS bmimin,
							ct.CampaignTypeCode AS camptype
							FROM t_gn_customer c
							LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
							LEFT JOIN t_lk_cignasystem cs ON ca.CignaSystemId = cs.CignaSystemId
							LEFT JOIN t_gn_insured i ON c.CustomerId = i.CustomerId
							LEFT JOIN t_gn_policy p ON i.PolicyId = p.PolicyId
							LEFT JOIN t_gn_productplan pp ON p.ProductPlanId = pp.ProductPlanId
							LEFT JOIN t_gn_product pr ON pp.ProductId = pr.ProductId
							LEFT JOIN t_gn_campaigngroup cg ON pr.CampaignGroupId = cg.CampaignGroupId
							LEFT JOIN t_gn_payer pa ON c.CustomerId = pa.CustomerId
							LEFT JOIN t_lk_salutation pas ON pa.SalutationId = pas.SalutationId
							LEFT JOIN t_lk_gender pag ON pa.GenderId = pag.GenderId
							LEFT JOIN t_lk_province pap ON pa.ProvinceId = pap.ProvinceId
							LEFT JOIN t_lk_paymenttype pt ON pa.PaymentTypeId = pt.PaymentTypeId
							LEFT JOIN t_lk_creditcardtype cct ON pa.CreditCardTypeId = cct.CreditCardTypeId
							LEFT JOIN t_lk_validccprefix vcp ON pa.ValidCCPrefixId = vcp.ValidCCPrefixId
							LEFT JOIN t_lk_bank b ON vcp.BankId = b.BankId
							LEFT JOIN t_lk_paymode pm ON pp.PayModeId = pm.PayModeId
							LEFT JOIN tms_agent ta ON i.CreatedById = ta.UserId
							LEFT JOIN t_lk_premiumgroup pg ON i.PremiumGroupId = pg.PremiumGroupId
							LEFT JOIN t_lk_salutation s ON i.SalutationId = s.SalutationId
							LEFT JOIN t_lk_gender g ON i.GenderId = g.GenderId
							LEFT JOIN t_lk_relationshiptype rt ON i.RelationshipTypeId = rt.RelationshipTypeId
							LEFT JOIN t_gn_callhistory ch ON c.CustomerId = ch.CustomerId AND c.CallReasonId = ch.CallReasonId
							LEFT JOIN t_lk_callreason cr ON ch.CallReasonId = cr.CallReasonId
							LEFT JOIN t_lk_campaigntype ct ON ca.CampaignTypeId = ct.CampaignTypeId
							LEFT JOIN t_gn_beneficiary bnf ON bnf.CustomerId = c.customerid
							LEFT JOIN t_gn_assignment asg ON asg.CustomerId = c.customerid
							where c.CallReasonId in (37,38) AND asg.AssignBlock =0
							AND date( c.customerupdatedts) >='".$_REQUEST['start_date']."' AND date( c.customerupdatedts) <='".$_REQUEST['end_date']."'
							AND (cs.cignasystemcode)like '%".$cignasystem."%'
							AND ca.campaignstatusflag = 1
							ORDER BY c.CustomerNumber ";
							
					$query 	   = $db->execute($sql,__FILE__,__LINE__);
		$perpages  = 3000;
		$totalRows = $db->numrows($query);
		$totalPage = ceil($totalRows/$perpages);
		$sizePage=0;
		for( $i=0;  $i<$totalPage; $i++){
			
			$datas = getRowFile($i,$perpages);
			if( $datas ) $sizePage++;
		}
		
		if( $sizePage>0) echo 1;
		else echo 0;
?>	

	


