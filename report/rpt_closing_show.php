<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	
	$start_date = $_REQUEST['start_date'];
	$end_date  = $_REQUEST['end_date'];
	$cignasystem		= $_REQUEST['cignasystem'];
	
	
	
	//
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		
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
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id1 and bnf.CustomerId = c.CustomerId ) as bnf1_percent1,
							(select if(bnf1_percent1>0,bnf1_percent1,0) ) as bnf1_percent,
							'' as bnf1_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id1 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf1_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId limit 1) as bnf2_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId limit 1) as bnf2_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_ssn,
							'' as bnf2_bene_ind,
							'' as bnf2_client_type,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id2 and bnf.CustomerId = c.CustomerId ) as bnf2_percent1,
							(select if(bnf2_percent1>0,bnf2_percent1,0) ) as bnf2_percent,
							'' as bnf2_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id2 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf2_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId limit 1) as bnf3_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId limit 1) as bnf3_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_ssn,
							'' as bnf3_bene_ind,
							'' as bnf3_client_type,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id3 and bnf.CustomerId = c.CustomerId ) as bnf3_percent1,
							(select if(bnf3_percent1>0,bnf3_percent1,0) ) as bnf3_percent,
							'' as bnf3_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id3 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf3_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId limit 1) as bnf4_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId limit 1) as bnf4_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_ssn,
							'' as bnf4_bene_ind,
							'' as bnf4_client_type,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id4 and bnf.CustomerId = c.CustomerId ) as bnf4_percent1,
							(select if(bnf4_percent1>0,bnf4_percent1,0) ) as bnf4_percent,
							'' as bnf4_coverage,
							(select rlt.RelationshipTypeCode from t_gn_beneficiary bnf, t_lk_relationshiptype rlt where bnf.BeneficiaryId = id4 and bnf.RelationshipTypeId = rlt.RelationshipTypeId and bnf.CustomerId = c.CustomerId ) as bnf4_relation,
							(select bnf.BeneficiaryLastName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId limit 1) as bnf5_lastname,
							(select bnf.BeneficiaryFirstName from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId limit 1) as bnf5_firstname,
							(select gnd.GenderCode from t_gn_beneficiary bnf, t_lk_gender gnd where bnf.GenderId = gnd.GenderId and bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_sex,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_ssn,
							'' as bnf5_bene_ind,
							'' as bnf5_client_type,
							(select bnf.beneficiaryidentificationnum from t_gn_beneficiary bnf where bnf.BeneficiaryId = id5 and bnf.CustomerId = c.CustomerId ) as bnf5_percent1,
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
							AND date( c.customerupdatedts) >='$start_date' AND date( c.customerupdatedts) <='$end_date'
							AND ca.campaignstatusflag = 1
							AND (cs.cignasystemcode)like '%".$cignasystem."%'
							ORDER BY c.CustomerNumber ";
							//AND (cs.cignasystemcode) ='$cignasystem'";
 
			//print_r($_REQUEST);
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$ListPages -> query($sql);
		$ListPages -> result();
	
	SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Preview Report TXT (Closing-QA) &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Interval : $start_date To $end_date</th>";
?>
</legend>
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<table width="99%" border="0" align="center">
	<tr height="30">
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;no.</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;cifnumber</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;dtfr</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;dtto</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;system</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;policy_id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;policy_ref</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;prospect_id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;product_id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;campaign_id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;campaign_TBSS</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;input</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;effdt</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_title</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_fname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_lname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_sex</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_dob</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;addr1</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;addr2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;addr3</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;addr4</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;city</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;post</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;province</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;phone</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;faxphone</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;email</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_lname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_fname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_sex</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_ssn</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_bene_ind</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_client_type</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_percent</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_coverage</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf1_relation</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_lname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_fname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_sex</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_ssn</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_bene_ind</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_client_type</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_percent</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_coverage</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf2_relation</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_lname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_fname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_sex</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_ssn</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_bene_ind</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_client_type</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_percent</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_coverage</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf3_relation</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_lname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_fname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_sex</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_ssn</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_bene_ind</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_client_type</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_percent</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_coverage</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf4_relation</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_lname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_fname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_sex</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_ssn</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_bene_ind</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_client_type</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_percent</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_coverage</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bnf5_relation</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;pay_type</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;card_type</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bank</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;branch</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;acctnum</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;ccexpdate</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bill_freq</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;question1</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;question2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;question3</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;benefit_level</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;premium</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;operid</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;sellerid</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;spv_id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;export</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;exportdate</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;canceldate</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;callDate2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;paystatus</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;paynotes</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payauthcode</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;paytransdate</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payorderno</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payccnum</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;paycvv</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payexpdate</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;paycurency</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;paycardtype</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_idtype</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_personalid</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_mobilephone</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_officephone</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;delivery_date</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;payer_age</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;currency</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;class</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;ratingfactors</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;mi_min</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;mi_max</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;mi_ren</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;sp_min</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;sp_max</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;sp_ren</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;dp_min</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;dp_max</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;dp_ren</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;ratingoptions</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;beneficiary</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;policyprefix</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;ei_mi</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;ei_sp</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;ei_dp</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;cc_mi</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;cc_mi_sp</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;cc_mi_fam</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;cc_mi_dp</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;cc_sp_dp</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;cc_sp</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;cc_dp</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;ben_level</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;htype</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_title</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_age</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_type</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;h_title</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_fname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_lname</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_sex</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_dob</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;relation</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;premi</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_ssn</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;benefit_level</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_race</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_idtype</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_issmoker</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_nationality</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_maritalstatus</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_occupation</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_jobtype</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_position</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_height</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_weight</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;uwstatus</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;uwlastupdate</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;uwapprovedate</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;uwprintdate</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;holder_id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;question_id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;answer</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;remark</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;seq_no</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;call_id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bmimax</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;bmimin</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;camptype</th>
	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CustomerId ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->dtfr ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->dtto ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->system ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->policy_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->policy_ref ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CustomerId ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->product_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->campaign_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->campaign_tbbs ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->input ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->effdt ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_title ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_fname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_lname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_dob ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addr1 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addr2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addr3 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addr4 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->city ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->post ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->province ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->phone ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->faxphone ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->email ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_lastname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_firstname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_ssn ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_bene_ind ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_client_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_percent ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_coverage ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf1_relation ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_lastname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_firstname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_ssn ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_bene_ind ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_client_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_percent ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_coverage ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf2_relation ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_lastname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_firstname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_ssn ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_bene_ind ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_client_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_percent ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_coverage ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf3_relation ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_lastname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_firstname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_ssn ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_bene_ind ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_client_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_percent ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_coverage ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf4_relation ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_lastname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_firstname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_ssn ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_bene_ind ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_client_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_percent ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_coverage ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bnf5_relation ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->pay_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->card_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bank ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->branch ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->acctnum ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ccexpdate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bill_freq ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question1 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question3 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->benefit_level1 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->premium ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->operid ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sellerid ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->spv_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->export ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->exportdate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->canceldate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->callDate2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paystatus ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paynotes ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payauthcode ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paytransdate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payorderno ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payccnum ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paycvv ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payexpdate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paycurrency ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paycardtype ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_idtype ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_personalid ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_mobilephone ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_officephone ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->delivery_date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_age ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->currency ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->class ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ratingfactors ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->mi_min ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->mi_max ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->mi_ren ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sp_min ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sp_max ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sp_ren ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->dp_min ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->dp_max ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->dp_ren ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ratingoptions ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->beneficiary ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->policyprefix ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ei_mi ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ei_sp ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ei_dp ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->cc_mi ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->cc_mi_sp ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->cc_mi_fam ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->cc_mi_dp ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->cc_sp_dp ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->cc_sp ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->cc_dp ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ben_level ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->htype ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_title ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_age ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->h_title ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_fname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_lname ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_dob ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->relation ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->premi ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_ssn ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->benefit_level ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_race ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_idtype ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_issmoker ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_nationality ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_maritalstatus ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_occupation ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_jobtype ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_position ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_height ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_weight ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->uwstatus ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->uwlastupdated ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->uwapprovedate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->uwprintdate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->answer ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->remark ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->seq_no ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->call_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bmimax ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bmimin ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->camptype ; ?></td>

			</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>


