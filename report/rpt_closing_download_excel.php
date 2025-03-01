<?php 



require("../fungsi/global.php");

require("../class/MYSQLConnect.php");

require("../class/class.list.table.php");

require 'Export/PHPExcel.php';

require 'Export/PHPExcel/IOFactory.php';

$connect = new mysql();



$objPHPExcel = new PHPExcel();



$start_date = $_REQUEST['start_date'];

$end_date = $_REQUEST['end_date'];

$prodid = $_REQUEST['prodid'];

$Campaign = $_REQUEST['Campaign'];

$exported = $_REQUEST['exported'];

$query = $_REQUEST['query'];

$query_check = $_REQUEST['query_check'];

$qcstatus = explode (',',$_REQUEST['QCStat']);

$qcstatusname = $db->Entity->ReasonLabelQuality();

$SelectQCStatus = array();

/*

if ($prodid) {

		$productName = getProductName($prodid, new mysql());

	}

	else{

		$productName = 'All active product';

	}	



function getProductName($prodid, $dbase)

{

	$sql = "select pr.ProductName from t_gn_product pr

	where pr.ProductCode='$prodid' ";

	$te = $dbase->query($sql);

	$te2 = $fte->result_object();



	return $te2[0]->ProductName;

}



*/



function getSPVname($spvid){

	$sql = "select id from tms_agent

	where id ='$spvid' ";

	$te = $connect->query($sql);

	$te2 = $fte->result_object();

	

	return $te2[0]->id;

	

}



$sql = "SELECT DISTINCT

				pa.PolicyNumber AS policy_id,

				'' as policy_ref,

				cst.NumberCIF AS prospect_id,

				prd.ProductCode AS product_id,

				cmp.CampaignNumber AS campaign_id,

				cmp.CampaignNumber as campaign_TBSS,

				DATE_FORMAT(cst.CustomerUpdatedTs, '%Y-%m-%d 00:00:00') as input,

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

				agt.id as sellerid,

				spv.id as spv_id,

				am.id as atm_id,

				'' as tsm_id,

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



				left join t_lk_salutation s on s.SalutationId=py.SalutationId

				left join t_lk_gender g on g.GenderId=py.GenderId

				left join t_lk_province pv on pv.ProvinceId=py.ProvinceId

				left join t_lk_paymenttype pt on pt.PaymentTypeId=py.PaymentTypeId

				left join t_lk_creditcardtype ct on ct.CreditCardTypeId=py.CreditCardTypeId

				left join t_lk_bank bk on bk.BankId=py.PayersBankId

				left join t_lk_paymode pm on pm.PayModeId=prp.PayModeId

				left join t_lk_identificationtype id on id.IdentificationTypeId=py.IdentificationTypeId

				inner join t_lk_producttype prt on prt.ProductTypeId=prd.ProductTypeId

				WHERE 1=1 ";

/****

 --- inner JOIN tms_agent AS tsm ON tsm.handling_type=1

 --- tsm.init_name

 ***/

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



if(count($qcstatus)>0){

	//kondisi yang memperhatikan status insured

	/*if(count($qcstatus)==1 and $qcstatus[0]==1)

	 {

	 $filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."

	 and ins.QCStatus=1";

	 }

	 elseif(count($qcstatus)==1 and $qcstatus[0]==2)

	 {

	 $filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."

	 or (cst.CallReasonQue =1 and ins.QCStatus=2)";

	 }

	 else

	 {

	 if()

	 {

	 }

	 }*/

		

	//kondisi hnya memperhatikan status insured yang approve

	if(count($qcstatus)==1)

	{

		if($qcstatus[0]==1)

		{

			$filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."

					and ins.QCStatus=1";

		}

		else

		{

			$filterQC = "AND cst.CallReasonQue =".$qcstatus[0];

		}

	}

	else

	{

		$filterQC = " AND (";

		foreach($qcstatus as $index=>$value){

				

			if ($value==1)

			{

				$array_filter[$value] = " (cst.CallReasonQue =".$value."

									 and ins.QCStatus=1) ";

			}

			else

			{

				$array_filter[$value] .= " cst.CallReasonQue =".$value." ";

			}

		}

		$filterQC .= implode(" OR ",$array_filter);

		$filterQC .=" ) ";

	}

}



$tail= $filterQC." group by pa.PolicyNumber

				order by pa.PolicyNumber";

$sql = $sql . $tail;

// echo $sql;

$policyRestObj = $connect->query($sql);









// query for data insured

$insured ="

				SELECT DISTINCT

				concat(date_format(plc.PolicySalesDate,'%d/%m/%y'), '-', pa.PolicyNumber)

			 	AS FormNumber,

				concat(ins.InsuredFirstName, ins.InsuredLastName) as InsuredName,

				date_format(ins.InsuredDOB,'%d/%m/%Y') as InsuredDOB,

				g.GenderShortCode as sex,

				py.PayerAddressLine1 as Address1,

				py.PayerAddressLine2 as Address2,

				py.PayerCity as City,

				cst.pros_Refnumber AS Refnumber,

				py.PayerZipCode as ZipCode,

				py.PayerHomePhoneNum as Phone1,

				py.PayerOfficePhoneNum as Phone2,

				'' as EXTPhone2,

				py.PayerMobilePhoneNum as MobilePhone,

				'' as MaritalStatus,

				'' as Children,

				'' as FACode,

				date_format(plc.PolicySalesDate, '%m/%d/%Y') as Activation_Date,

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

				agt.spv_id as spv_id,

				agt.mgr_id as am_id,

				'' as tsm_id,

				DATE_FORMAT(DATE_ADD(plc.PolicySalesDate,INTERVAL + 3 MONTH), '%m/%d/%Y %h:%i:%s') AS EXPDate,

				'' as EXPCard,

				prd.ProductCode AS product_id,

				'3' as Periode,

				round(plc.Premi,0) as PremiumAmount,

				cst.Remark_1,

				cst.Remark_2,

				cst.Remark_3,

				cst.Remark_4,

				cst.Remark_5

				FROM t_gn_customer AS cst

				inner join t_gn_insured ins on ins.customerid=cst.customerid

				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId

				inner join t_gn_policyautogen pa on pa.PolicyNumber=plc.PolicyNumber

				inner join t_gn_payer py on py.CustomerId=cst.CustomerId

				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId

				inner JOIN tms_agent AS agt ON agt.UserId = cst.SellerId





				left join t_lk_salutation s on s.SalutationId=ins.SalutationId

				left join t_lk_gender g on g.GenderId=ins.GenderId

				left join t_lk_premiumgroup pg on pg.PremiumGroupId=ins.PremiumGroupId

				WHERE 1 = 1

		";

/* inner JOIN tms_agent AS spv ON agt.spv_id = spv.UserId

 -- inner JOIN tms_agent AS am ON spv.mgr_id = am.UserId

 -- inner JOIN tms_agent AS tsm ON tsm.UserId=430

 */

$insured_tail =	"ORDER BY pa.PolicyNumber, pg.PremiumGroupOrder ";



// query for data beneficiary

$benf ="

		SELECT DISTINCT

				concat(date_format(plc.PolicySalesDate,'%d/%m/%y'), '-', pa.PolicyNumber)

			 	AS FormNumber,

				plc.PolicyId AS PolicyId,

				concat(bnf.BeneficiaryFirstName, bnf.BeneficiaryLastName) as Name,

				date_format(bnf.BeneficiaryDOB,'%d/%m/%Y') as DOB,

				g.GenderShortCode as Gender,

				r.BeneficiaryAliasCode as Relation,

				bnf.BeneficieryPercentage as Percentage



				FROM t_gn_customer AS cst

				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId

				inner join t_gn_beneficiary bnf on bnf.CustomerId=cst.CustomerId

				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId

				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId

				left join t_lk_gender g on g.GenderId=ins.GenderId

				left join t_lk_relationshiptype r on r.RelationshipTypeId=bnf.RelationshipTypeId

				WHERE 1 = 1

				";



//ins.GenderId as Gender

$tail_benf= " group by bnf.BeneficiaryId

				ORDER BY pa.PolicyNumber, bnf.BeneficiaryFirstName ";







$objPHPExcel->setActiveSheetIndex(0);

//$objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', 'No.');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', 'formnumber');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('B1', 'insuredname');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('C1', 'insureddob');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('D1', 'sex');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('E1', 'address1');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('F1', 'address2');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('G1', 'city');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('H1', 'rtandrw');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('I1', 'zipcode');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('J1', 'phone1');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('K1', 'phone2');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('L1', 'extphone2');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('M1', 'mphone');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('N1', 'maritalstatus');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('O1', 'children');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('P1', 'facode');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('Q1', 'activationdate');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('R1', 'branchcode');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('S1', 'csname');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('T1', 'accholdername');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('U1', 'accnumber');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('V1', 'accbranch');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('W1', 'program');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('X1', 'email');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('Y1', 'asken');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('Z1', 'region');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AA1', 'namalg');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AB1', 'idstaffbank');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AC1', 'operid');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AD1', 'sellerid');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AE1', 'SPVID');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AF1', 'AMID');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AG1', 'SMID');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AH1', 'productid');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AI1', 'period');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AJ1', 'expdate');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AK1', 'expcard');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('AL1', 'premiumamount');



$no = 1;

$policy_array = '';

foreach ($policyRestObj ->result_object() as $key => $row) {

	$policy_array = $policy_array .','. "'".$row ->policy_id."'";

}

$policy_array = substr($policy_array,1);

$insured_tail =	"ORDER BY pa.PolicyNumber, pg.PremiumGroupOrder ";

$policy_cond = $policy_array ? " and pa.PolicyNumber in (". $policy_array .") " : '  ';

$insured_query = $insured . $policy_cond . $insured_tail;

$insuredRestObj = $connect->query($insured_query);



$no = 1;

$pol_id='';

$holder_id=0;

$baris = 2;

foreach ($insuredRestObj ->result_object() as $key => $row) {

	if ($row ->policy_id != $pol_id) {

		$pol_id=$row ->policy_id;

		$holder_id=0;

	}

	

	if($row->EXTPhone2 ==''){

		$extphone2 = '?';

	}else {

		$extphone2 = $row->EXTPhone2;

	}

	

	if($row->Children =='' ){

		$children ='?'; 

	}else {

		$children=strtoupper($row->Children);

	}

	

	if($row->BranchCode ==''){

		$branchcode ='?';

	}else{

		$branchcode=strtoupper($row->BranchCode);

	}

	if($row->CSName==''){

		$csname ='?';

	}else{

		$csname = strtoupper($row->CSName);

	}

	if($row->Program==''){

		$program ='?';

	}else{

		$program =strtoupper($row->Program);

	}

	if($row->Asken==''){

		$asken ='?';

	}else{

		$asken=strtotupper($row->Asken);

	}

	if($row->Region==''){

		$region='?';

	}else{

		$region=strtoupper($row->Region);

	}

	if($row ->NamaLG==''){

		$namalg ='?';

	}else{

		$namalg = strtoupper($row->NamaLG);

	}

	if($row ->IDStaffBank ==''){

		$idstaffbank = '?';

	}else{

		$idstaffbank = strtoupper($row ->IDStaffBank);

	}

	if($row->sex =="M"){
            $sex = 'BAPAK';
        }else{
            $sex = 'IBU';
        }

	$sqlspvname = "select id from tms_agent where UserId = '".$row->spv_id."'";

	$rstspvname = $connect->query($sqlspvname);

	foreach ($rstspvname->result_object() as $key=>$rw){

		$spvid=  strtoupper($rw->id);

	}

	

	$sqlspvname = "select id from tms_agent where UserId = '".$row ->am_id."'";

	$rstamname = $connect->query($sqlspvname);

	foreach ($rstamname->result_object() as $key=>$rw){

		$amid=  strtoupper($rw->id);

	}

	//$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$baris, $no,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$baris, $row ->FormNumber,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$baris, strtoupper($row->InsuredName),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$baris, $row->InsuredDOB,PHPExcel_Cell_DataType::TYPE_STRING );

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$baris, strtoupper($sex),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$baris, strtoupper($row ->Address1),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$baris, strtoupper($row ->Address2),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$baris, strtoupper($row ->City),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$baris, $row ->Refnumber,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.$baris, $row ->ZipCode,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.$baris,$row ->Phone1  ,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('K'.$baris, $row ->Phone2,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('L'.$baris, $extphone2,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('M'.$baris, $row ->MobilePhone,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('N'.$baris, strtoupper($row ->MaritalStatus),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('O'.$baris, $children,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('P'.$baris, $row ->FACode,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('Q'.$baris, $row ->Activation_Date,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('R'.$baris, $branchcode,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('S'.$baris, $csname,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('T'.$baris, strtoupper($row ->ACCHolderName),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('U'.$baris, $row ->ACCNumber,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('V'.$baris, $row ->ACCBranch,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('W'.$baris, $program,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('X'.$baris, strtoupper($row ->Email),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('Y'.$baris, $asken,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('Z'.$baris,$region,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AA'.$baris, $namalg,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AB'.$baris, $idstaffbank,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AC'.$baris, $row ->operid,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AD'.$baris, $row ->sellerid,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AE'.$baris, $spvid,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AF'.$baris, $amid,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AG'.$baris, $row ->tsm_id,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AH'.$baris, $row ->product_id,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AI'.$baris, $row ->Periode,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AJ'.$baris, $row ->EXPDate,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AK'.$baris, $row ->EXPCard,PHPExcel_Cell_DataType::TYPE_STRING);	

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AL'.$baris, $row ->PremiumAmount,PHPExcel_Cell_DataType::TYPE_NUMERIC);

		

	$holder_id++;

	$baris++;

	$no++;

}





$objPHPExcel->getActiveSheet()->setTitle('Insured');



$objPHPExcel->createSheet();



// Add some data to the second sheet, resembling some different data types

$objPHPExcel->setActiveSheetIndex(1);



//$objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', 'No.');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', 'formnumber');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('B1', 'policyid');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('C1', 'name');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('D1', 'dob');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('E1', 'gender');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('F1', 'relation');

$objPHPExcel->getActiveSheet()->setCellValueExplicit('G1', 'percentage');



$benf_query = $benf . $policy_cond . $tail_benf;

// echo $benf_query;

$benfRestObj = $connect->query($benf_query);

$no = 1;

$pol_id='';

$holder_id=0;

$baris=2;

foreach ($benfRestObj ->result_object() as $key => $row) {

	if ($row ->policy_id != $pol_id) {

		$pol_id=$row ->policy_id;

		$holder_id=0;

	}



	//$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$baris, $no,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$baris, $row->FormNumber,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$baris, $row->PolicyId,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$baris, strtoupper($row->Name),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$baris, $row->DOB,PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$baris, strtoupper($row->Gender),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$baris, strtoupper($row->Relation),PHPExcel_Cell_DataType::TYPE_STRING);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$baris, $row->Percentage,PHPExcel_Cell_DataType::TYPE_STRING);



	$holder_id++;

	$baris++;

	$no++;

}

// Rename 2nd sheet



//$objPHPExcel->getActivesheet()->setCellValueExplicit('A20',$sql,PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->setTitle('Beneficiary');



$objPHPExcel->createSheet();

$objPHPExcel->setActiveSheetIndex(2);







$sqlsurvey = "SELECT  CONCAT(DATE_FORMAT(i.`PolicySalesDate`,'%d/%m/%y') , '-', pa.PolicyNumber) AS FormNumber,concat(h.InsuredFirstName, h.InsuredLastName) as InsuredName,i.PolicyId,e.`Pros_Refnumber` AS refNumber,e.`CampaignId`,a.`customer_id`,d.`survey_quest_id`, d.`survey_question`,a.`answer_value` FROM t_gn_multians_survey a

						INNER JOIN t_gn_customer e ON e.`CustomerId` = a.`customer_id`

						INNER JOIN `t_gn_campaign` f ON f.`CampaignId` = e.`CampaignId`

						INNER JOIN t_gn_insured h ON h.`CustomerId` = e.`CustomerId`

						INNER JOIN t_gn_policyautogen pa ON pa.CustomerId = e.CustomerId

						INNER JOIN t_gn_policy i ON i.`PolicyId` = h.`PolicyId`

						INNER JOIN t_gn_prod_survey b ON a.`prod_survey_id` = b.`prod_survey_id`

						inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId

						INNER JOIN t_lk_survey c ON c.`survey_id` = b.`survey_id`

						INNER JOIN t_lk_question_survey d ON d.`survey_quest_id` = c.`survey_quest_id`

						WHERE 1 = 1 and a.`quest_have_ans` <> 0 ";







if ($Campaign){

	$sqlsurvey .= " and f.`CampaignNumber` in (".$Campaign.")";

}



if(count($qcstatus)>0){

	//kondisi yang memperhatikan status insured

	/*if(count($qcstatus)==1 and $qcstatus[0]==1)

	 {

	 $filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."

	 and ins.QCStatus=1";

	 }

	 elseif(count($qcstatus)==1 and $qcstatus[0]==2)

	 {

	 $filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."

	 or (cst.CallReasonQue =1 and ins.QCStatus=2)";

	 }

	 else

	 {

	 if()

	 {

	 }

	 }*/



	//kondisi hnya memperhatikan status insured yang approve

	if(count($qcstatus)==1)

	{

		if($qcstatus[0]==1)

		{

			$filterQC = "AND e.CallReasonQue =".$qcstatus[0]."

					and h.QCStatus=1";

		}

		else

		{

			$filterQC = "AND e.CallReasonQue =".$qcstatus[0];

		}

	}

	else

	{

		$filterQC = " AND (";

		foreach($qcstatus as $index=>$value){



			if ($value==1)

			{

				$array_filter1[$value] = " (e.CallReasonQue =".$value."

									 and h.QCStatus=1) ";

			}

			else

			{

				$array_filter1[$value] .= " e.CallReasonQue =".$value." ";

			}

		}

		$filterQC .= implode(" OR ",$array_filter1);

		$filterQC .=" ) ";

	}

}



$ssurvey = $filterQC." order by pa.PolicyNumber";

$sqlsurvey .= $policy_cond. $ssurvey;





$policysurvey = $connect->query($sqlsurvey);



$nomor = 1;

$tampkey = '';

$col=6;

foreach ($policysurvey->result_object() as $key => $row){



	if($tampkey <> $row->customer_id){

		if($nomor > 1){

			break;

		}

		//$objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', 'No.');

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', 'formnumber');

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('B1', 'policyid');

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('C1', 'refnumber');

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('D1', 'insuredname');		

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('E1', 'question'.$nomor);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('F1', 'answer '.$nomor);

	}else{

		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,1,'question'.$nomor);

		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col+1,1,'Answer '.$nomor);

		$col = $col+2;

	}

	$tampkey =  $row->customer_id;

	$nomor++;

}



$no = 1;

$oldkey ='';

$baris =1;

$col=6;

foreach ($policysurvey->result_object() as $key => $row){

	if(is_numeric($row->answer_value)){

		$ansvalue=$row->answer_value;

	}else{

		$ansvalue=strtoupper($row->answer_value);

	}

	if($oldkey <> $row->customer_id){

		$col=6;

		$baris++;

		//$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$baris, $no,PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$baris, $row->FormNumber,PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$baris, $row->PolicyId,PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$baris, $row->refNumber,PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$baris, $row->InsuredName,PHPExcel_Cell_DataType::TYPE_STRING);	

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$baris, strtoupper($row->survey_question),PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$baris, strtoupper($row->answer_value),PHPExcel_Cell_DataType::TYPE_STRING);

		$no++;

	}else{

		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,$baris,strtoupper($row->survey_question),PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col+1,$baris,strtoupper($row->answer_value),PHPExcel_Cell_DataType::TYPE_STRING);

		$col=$col+2;

	}



	$oldkey =$row->customer_id;



}

//$objPHPExcel->getActivesheet()->setCellValueExplicit('A20',$sqlsurvey,PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->setTitle('Survey');

// Redirect output to a client�s web browser (Excel5)

$file_name = $prodid.'_'.date('Y-m-d-his').'.xls';

header('Content-Type: application/vnd.ms-excel');

header('Content-Disposition: attachment;filename='.$file_name);

header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

$objWriter->save('php://output');

?>