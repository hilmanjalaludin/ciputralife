<?php
	require ("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("class_export_excel.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$campaign		= $_REQUEST['Campaign'];
	$agt			= $_REQUEST['Agent'];
	$excel			= new excel();
	$excel -> xlsWriteHeader('DownlodCTR(From'.$start_date.'To'.$end_date.')');
	
		$size_data103='';
		$size_data201='';
		$size_data203='';
		$size_data204='';
		$size_data205='';
		$size_data206='';
		$size_data207='';
		$size_data301='';
		$size_data302='';
		$size_data305='';
		$size_data306='';
		$size_data307='';
		$size_data310='';
		$size_data401='';
		$size_data402='';
		$size_data504='';
		$size_data505='';
		$size_data506='';
		$size_data507='';
		$size_data508='';
		$size_data512='';
		$size_data513='';
		$size_data514='';
		$size_data515='';
		$size_data516='';
		$size_data517='';
		$size_data518='';
		$size_data519='';
		$size_data102='';
			
		$TotalSupply=0;
		$TotalSolicited=0;
		$TotalCallAttempted=0;
		$TotalRatio=0;
		$TotalFax=0;
		$TotalInvalidPhone=0;
		$TotalBusyLine=0;
		$TotalNPU=0;
		$TotalMissCust=0;
		$Total=0;
		$TotalRUF=0;
		$TotalCBL=0;
		$TotalOverage=0;
		$TotalAlreadyMoved=0;
		$TotalSupplement=0;
		$TotalDoubleCall=0;
		$TotalCountrySide=0;
		$TotalThinking=0;
		$TotalThinkingNewNumber=0;
		$TotalInterested_PIF=0;
		$TotalNotInterested=0;
		$TotalAlreadyhadCIGNA=0;
		$TotalNoNeedInsurance=0;
		$TotalAccountClosed=0;
		$TotalOwnerChanged=0;
		$TotalAlreadyInsuredWith=0;
		$TotalPremiumTooHigh=0;
		$TotalNoDependant=0;
		$TotalNeedHighBenefit=0;
		$TotalPremiumIncludeInstallment=0;
		$TotalAskingInvesmentProduct=0;
		$TotalInterestButNoCreditCard=0;
		$TotalInterestButPaymentMechanismIssue=0;
		$TotalInterestButCreditCardHasBeenExpired=0;
		$TotalInterestButCardNotReceivedYet=0;
		$TotalNotInterestedOther=0;
	
	
	$xlsRows = 0;
	$excel ->xlsWriteLabel($xlsRows,9,"Solicited");
	$excel ->xlsWriteLabel($xlsRows,21,"Contacted Result");
	$excel ->xlsWriteLabel($xlsRows,35,"Presentation Result");
	$excel ->xlsWriteLabel($xlsRows,79,"Sales Result");
	$xlsRows = 1;
	$excel ->xlsWriteLabel($xlsRows,4,"Database");
	$excel ->xlsWriteLabel($xlsRows,9,"Not Connect");
	$excel ->xlsWriteLabel($xlsRows,13,"Connected Total");
	$excel ->xlsWriteLabel($xlsRows,16,"Not Contacted");
	$excel ->xlsWriteLabel($xlsRows,19,"Contacted Total");
	$excel ->xlsWriteLabel($xlsRows,21,"Unable To do Presentation");
	$excel ->xlsWriteLabel($xlsRows,32,"Done Presentation");
	$excel ->xlsWriteLabel($xlsRows,35,"Customer Response");
	$excel ->xlsWriteLabel($xlsRows,45,"Not Interested Reasons");
	
	
	
	
	$xlsRows = 2;
	$excel ->xlsWriteLabel($xlsRows,39,"Interested ( PIF )");
		
	
	$xlsRows = 3;
	$excel ->xlsWriteLabel($xlsRows,4,"Supply");
	$excel ->xlsWriteLabel($xlsRows,5,"Solicited");
	$excel ->xlsWriteLabel($xlsRows,7,"Call Attemp");
	$excel ->xlsWriteLabel($xlsRows,9,"Fax");
	$excel ->xlsWriteLabel($xlsRows,11,"Invalid Phone");
	$excel ->xlsWriteLabel($xlsRows,13,"Busy Line");
	$excel ->xlsWriteLabel($xlsRows,15,"NPU");
	$excel ->xlsWriteLabel($xlsRows,17,"Miss Cust");
	$excel ->xlsWriteLabel($xlsRows,19,"RUF");
	$excel ->xlsWriteLabel($xlsRows,21,"CBL");
	$excel ->xlsWriteLabel($xlsRows,23,"Overage");
	$excel ->xlsWriteLabel($xlsRows,25,"Already Moved");
	$excel ->xlsWriteLabel($xlsRows,27,"Supplement");
	$excel ->xlsWriteLabel($xlsRows,29,"Double Call");
	$excel ->xlsWriteLabel($xlsRows,31,"Country Side");
	$excel ->xlsWriteLabel($xlsRows,33,"(Total)");
	$excel ->xlsWriteLabel($xlsRows,35,"Thinking");
	$excel ->xlsWriteLabel($xlsRows,37,"Thinking Add New Number");
	$excel ->xlsWriteLabel($xlsRows,39,"Holder");
	$excel ->xlsWriteLabel($xlsRows,41,"Spouse");
	$excel ->xlsWriteLabel($xlsRows,42,"Dependant");
	$excel ->xlsWriteLabel($xlsRows,43,"Not Interested");
	$excel ->xlsWriteLabel($xlsRows,45,"Already had CIGNA");
	$excel ->xlsWriteLabel($xlsRows,47,"No Need Insurance");
	$excel ->xlsWriteLabel($xlsRows,49,"Account Closed");
	$excel ->xlsWriteLabel($xlsRows,51,"Owner Changed");
	$excel ->xlsWriteLabel($xlsRows,53,"Already Insured With");
	$excel ->xlsWriteLabel($xlsRows,55,"Premium Too High");
	$excel ->xlsWriteLabel($xlsRows,57,"No Dependant");
	$excel ->xlsWriteLabel($xlsRows,59,"Need High Benefit");
	$excel ->xlsWriteLabel($xlsRows,61,"Premium Include Installment");
	$excel ->xlsWriteLabel($xlsRows,63,"Asking Invesment Product");
	$excel ->xlsWriteLabel($xlsRows,65,"Interest But No Credit Card");
	$excel ->xlsWriteLabel($xlsRows,67,"Interest But Payment Mechanism Issue");
	$excel ->xlsWriteLabel($xlsRows,69,"Interest But Credit Card Has Been Expired");
	$excel ->xlsWriteLabel($xlsRows,71,"Interest But Card Not Received Yet");
	$excel ->xlsWriteLabel($xlsRows,73,"Not Interested Other");
			
			
	
	$xlsRows = 4;
	$excel ->xlsWriteLabel($xlsRows,0,"No.");	
	$excel ->xlsWriteLabel($xlsRows,1,"Campaign ID");
	$excel ->xlsWriteLabel($xlsRows,2,"Campaign Name");
	$excel ->xlsWriteLabel($xlsRows,3,"Date Upload");
	$excel ->xlsWriteLabel($xlsRows,4,"Total1");
	$excel ->xlsWriteLabel($xlsRows,5,"Total");
	$excel ->xlsWriteLabel($xlsRows,6,"%");
	$excel ->xlsWriteLabel($xlsRows,7,"Total");
	$excel ->xlsWriteLabel($xlsRows,8,"Ratio");
	$excel ->xlsWriteLabel($xlsRows,9,"Total");
	$excel ->xlsWriteLabel($xlsRows,10,"%");
	$excel ->xlsWriteLabel($xlsRows,11,"Total");
	$excel ->xlsWriteLabel($xlsRows,12,"%");
	$excel ->xlsWriteLabel($xlsRows,13,"Total");
	$excel ->xlsWriteLabel($xlsRows,14,"%");
	$excel ->xlsWriteLabel($xlsRows,15,"Total");
	$excel ->xlsWriteLabel($xlsRows,16,"%");
	
	$excel ->xlsWriteLabel($xlsRows,17,"Total");
	$excel ->xlsWriteLabel($xlsRows,18,"%");
	$excel ->xlsWriteLabel($xlsRows,19,"Total");
	$excel ->xlsWriteLabel($xlsRows,20,"%");
	$excel ->xlsWriteLabel($xlsRows,21,"Total");
	$excel ->xlsWriteLabel($xlsRows,22,"%");
	$excel ->xlsWriteLabel($xlsRows,23,"Total");
	$excel ->xlsWriteLabel($xlsRows,24,"%");
	$excel ->xlsWriteLabel($xlsRows,25,"Total");
	
	$excel ->xlsWriteLabel($xlsRows,26,"%");
	$excel ->xlsWriteLabel($xlsRows,27,"Total");
	$excel ->xlsWriteLabel($xlsRows,28,"%");
	$excel ->xlsWriteLabel($xlsRows,29,"Total");
	$excel ->xlsWriteLabel($xlsRows,30,"%");
	$excel ->xlsWriteLabel($xlsRows,31,"Total");
	$excel ->xlsWriteLabel($xlsRows,32,"%");
	$excel ->xlsWriteLabel($xlsRows,33,"Total");
	$excel ->xlsWriteLabel($xlsRows,34,"%");
	$excel ->xlsWriteLabel($xlsRows,35,"Total");
	
	$excel ->xlsWriteLabel($xlsRows,36,"%");
	$excel ->xlsWriteLabel($xlsRows,37,"Total");
	$excel ->xlsWriteLabel($xlsRows,38,"%");
	$excel ->xlsWriteLabel($xlsRows,39,"Total");
	$excel ->xlsWriteLabel($xlsRows,40,"%");
	$excel ->xlsWriteLabel($xlsRows,41,"Total");
	$excel ->xlsWriteLabel($xlsRows,42,"%");
	$excel ->xlsWriteLabel($xlsRows,43,"Total");
	$excel ->xlsWriteLabel($xlsRows,44,"%");
	$excel ->xlsWriteLabel($xlsRows,45,"Total");
	
	$excel ->xlsWriteLabel($xlsRows,46,"Total");
	$excel ->xlsWriteLabel($xlsRows,47,"Total");
	$excel ->xlsWriteLabel($xlsRows,48,"%");
	$excel ->xlsWriteLabel($xlsRows,49,"Total");
	$excel ->xlsWriteLabel($xlsRows,50,"%");
	$excel ->xlsWriteLabel($xlsRows,51,"Total");
	$excel ->xlsWriteLabel($xlsRows,52,"%");
	$excel ->xlsWriteLabel($xlsRows,53,"Total");
	$excel ->xlsWriteLabel($xlsRows,54,"%");
	$excel ->xlsWriteLabel($xlsRows,55,"Total");
	
	$excel ->xlsWriteLabel($xlsRows,56,"%");
	$excel ->xlsWriteLabel($xlsRows,57,"Total");
	$excel ->xlsWriteLabel($xlsRows,58,"%");
	$excel ->xlsWriteLabel($xlsRows,59,"Total");
	$excel ->xlsWriteLabel($xlsRows,60,"%");
	$excel ->xlsWriteLabel($xlsRows,61,"Total");
	$excel ->xlsWriteLabel($xlsRows,62,"%");
	$excel ->xlsWriteLabel($xlsRows,63,"Total");
	$excel ->xlsWriteLabel($xlsRows,64,"%");
	$excel ->xlsWriteLabel($xlsRows,65,"Total");
	
	$excel ->xlsWriteLabel($xlsRows,66,"%");
	$excel ->xlsWriteLabel($xlsRows,67,"Total");
	$excel ->xlsWriteLabel($xlsRows,68,"%");
	$excel ->xlsWriteLabel($xlsRows,69,"Total");
	$excel ->xlsWriteLabel($xlsRows,70,"%");
	$excel ->xlsWriteLabel($xlsRows,71,"Total");
	$excel ->xlsWriteLabel($xlsRows,72,"%");
	$excel ->xlsWriteLabel($xlsRows,73,"Total");
	$excel ->xlsWriteLabel($xlsRows,74,"%");
	$excel ->xlsWriteLabel($xlsRows,75,"Total");
	
	$excel ->xlsWriteLabel($xlsRows,76,"%");
	$excel ->xlsWriteLabel($xlsRows,77,"Total");
	$excel ->xlsWriteLabel($xlsRows,78,"%");
	$excel ->xlsWriteLabel($xlsRows,79,"RR %");
	$excel ->xlsWriteLabel($xlsRows,80,"SCR %");
	$excel ->xlsWriteLabel($xlsRows,81,"ANP");
	$excel ->xlsWriteLabel($xlsRows,82,"AVG");
	
	
		

	

	
			$sql = "  SELECT cmp.campaignnumber AS campaignnumber,
				cmp.campaignname AS campaignname ,
				DATE_FORMAT((cmp.campaignstartdate),'%Y-%m-%d') AS uploaddate,
				count(cst.CustomerId) AS supply,
				sum(if(cst.CustomerUpdatedTs>0,1,0)) as solicited,
				sum(if(cst.CustomerUpdatedTs>0,0,1)) as notsolicited
				
				FROM t_gn_customer cst
				LEFT JOIN t_gn_campaign cmp ON cmp.CampaignId = cst.CampaignId
				WHERE date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >='.$start_date.'
				OR date(if(cmp.CampaignExtendedDate is null,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) <='.$end_date.'GROUP BY cmp.CampaignNumber ";
 
			
		$query		= $db ->execute($sql,__FILE__,__LINE__); 
		
		
		$xlsRows = 5;
		$number=1;
		while($row = $db ->fetchrow($query)){
		
				$excel->xlsWriteNumber($xlsRows,0,$number);
				$excel->xlsWriteLabel($xlsRows,1,$row -> campaignnumber );
				$excel->xlsWriteLabel($xlsRows,2,$row -> campaignname );
				$excel->xlsWriteLabel($xlsRows,3,$row -> uploaddate );
				$excel->xlsWriteLabel($xlsRows,4,$row -> supply );
				$excel->xlsWriteLabel($xlsRows,5,$row -> solicited );
				$excel ->xlsWriteLabel($xlsRows,6,"%");
				$excel ->xlsWriteLabel($xlsRows,7,"Total");
				$excel ->xlsWriteLabel($xlsRows,8,"Ratio");
				$excel ->xlsWriteLabel($xlsRows,9,"Total");
				$excel ->xlsWriteLabel($xlsRows,10,"%");
				$excel ->xlsWriteLabel($xlsRows,11,"Total");
				$excel ->xlsWriteLabel($xlsRows,12,"%");
				$excel ->xlsWriteLabel($xlsRows,13,"Total");
				$excel ->xlsWriteLabel($xlsRows,14,"%");
				$excel ->xlsWriteLabel($xlsRows,15,"Total");
				$excel ->xlsWriteLabel($xlsRows,16,"%");
				$excel ->xlsWriteLabel($xlsRows,17,"Total");
				$excel ->xlsWriteLabel($xlsRows,18,"%");
				$excel ->xlsWriteLabel($xlsRows,19,"Total");
				$excel ->xlsWriteLabel($xlsRows,20,"%");
				$excel ->xlsWriteLabel($xlsRows,21,"Total");
				$excel ->xlsWriteLabel($xlsRows,22,"%");
				$excel ->xlsWriteLabel($xlsRows,23,"Total");
				$excel ->xlsWriteLabel($xlsRows,24,"%");
				$excel ->xlsWriteLabel($xlsRows,25,"Total");
				$excel ->xlsWriteLabel($xlsRows,26,"%");
				$excel ->xlsWriteLabel($xlsRows,27,"Total");
				$excel ->xlsWriteLabel($xlsRows,28,"%");
				$excel ->xlsWriteLabel($xlsRows,29,"Total");
				$excel ->xlsWriteLabel($xlsRows,30,"%");
				$excel ->xlsWriteLabel($xlsRows,31,"Total");
				$excel ->xlsWriteLabel($xlsRows,32,"%");
				$excel ->xlsWriteLabel($xlsRows,33,"Total");
				$excel ->xlsWriteLabel($xlsRows,34,"%");
				$excel ->xlsWriteLabel($xlsRows,35,"Total");
				$excel ->xlsWriteLabel($xlsRows,36,"%");
				$excel ->xlsWriteLabel($xlsRows,37,"Total");
				$excel ->xlsWriteLabel($xlsRows,38,"%");
				$excel ->xlsWriteLabel($xlsRows,39,"Total");
				$excel ->xlsWriteLabel($xlsRows,40,"%");
				$excel ->xlsWriteLabel($xlsRows,41,"Total");
				$excel ->xlsWriteLabel($xlsRows,42,"%");
				$excel ->xlsWriteLabel($xlsRows,43,"Total");
				$excel ->xlsWriteLabel($xlsRows,44,"%");
				$excel ->xlsWriteLabel($xlsRows,45,"Total");
				$excel ->xlsWriteLabel($xlsRows,46,"Total");
				$excel ->xlsWriteLabel($xlsRows,47,"Total");
				$excel ->xlsWriteLabel($xlsRows,48,"%");
				$excel ->xlsWriteLabel($xlsRows,49,"Total");
				$excel ->xlsWriteLabel($xlsRows,50,"%");
				$excel ->xlsWriteLabel($xlsRows,51,"Total");
				$excel ->xlsWriteLabel($xlsRows,52,"%");
				$excel ->xlsWriteLabel($xlsRows,53,"Total");
				$excel ->xlsWriteLabel($xlsRows,54,"%");
				$excel ->xlsWriteLabel($xlsRows,55,"Total");
				$excel ->xlsWriteLabel($xlsRows,56,"%");
				$excel ->xlsWriteLabel($xlsRows,57,"Total");
				$excel ->xlsWriteLabel($xlsRows,58,"%");
				$excel ->xlsWriteLabel($xlsRows,59,"Total");
				$excel ->xlsWriteLabel($xlsRows,60,"%");
				$excel ->xlsWriteLabel($xlsRows,61,"Total");
				$excel ->xlsWriteLabel($xlsRows,62,"%");
				$excel ->xlsWriteLabel($xlsRows,63,"Total");
				$excel ->xlsWriteLabel($xlsRows,64,"%");
				$excel ->xlsWriteLabel($xlsRows,65,"Total");
				$excel ->xlsWriteLabel($xlsRows,66,"%");
				$excel ->xlsWriteLabel($xlsRows,67,"Total");
				$excel ->xlsWriteLabel($xlsRows,68,"%");
				$excel ->xlsWriteLabel($xlsRows,69,"Total");
				$excel ->xlsWriteLabel($xlsRows,70,"%");
				$excel ->xlsWriteLabel($xlsRows,71,"Total");
				$excel ->xlsWriteLabel($xlsRows,72,"%");
				$excel ->xlsWriteLabel($xlsRows,73,"Total");
				$excel ->xlsWriteLabel($xlsRows,74,"%");
				$excel ->xlsWriteLabel($xlsRows,75,"Total");
				$excel ->xlsWriteLabel($xlsRows,76,"%");
				$excel ->xlsWriteLabel($xlsRows,77,"Total");
				$excel ->xlsWriteLabel($xlsRows,78,"%");
				$excel ->xlsWriteLabel($xlsRows,79,"RR %");
				$excel ->xlsWriteLabel($xlsRows,80,"SCR %");
				$excel ->xlsWriteLabel($xlsRows,81,"ANP");
				$excel ->xlsWriteLabel($xlsRows,82,"AVG");
			
			
		$xlsRows+=1;
		$number++;
		
		
		
	};	
				$xlsRows+=1;
				$excel->xlsWriteLabel($xlsRows,3,TOTAL );
				$excel->xlsWriteLabel($xlsRows,4,$row -> supply );
				$excel->xlsWriteLabel($xlsRows,5,$row -> solicited );
				$excel ->xlsWriteLabel($xlsRows,6,"%");
				$excel ->xlsWriteLabel($xlsRows,7,"Total");
				$excel ->xlsWriteLabel($xlsRows,8,"Ratio");
				$excel ->xlsWriteLabel($xlsRows,9,"Total");
				$excel ->xlsWriteLabel($xlsRows,10,"%");
				$excel ->xlsWriteLabel($xlsRows,11,"Total");
				$excel ->xlsWriteLabel($xlsRows,12,"%");
				$excel ->xlsWriteLabel($xlsRows,13,"Total");
				$excel ->xlsWriteLabel($xlsRows,14,"%");
				$excel ->xlsWriteLabel($xlsRows,15,"Total");
				$excel ->xlsWriteLabel($xlsRows,16,"%");
				$excel ->xlsWriteLabel($xlsRows,17,"Total");
				$excel ->xlsWriteLabel($xlsRows,18,"%");
				$excel ->xlsWriteLabel($xlsRows,19,"Total");
				$excel ->xlsWriteLabel($xlsRows,20,"%");
				$excel ->xlsWriteLabel($xlsRows,21,"Total");
				$excel ->xlsWriteLabel($xlsRows,22,"%");
				$excel ->xlsWriteLabel($xlsRows,23,"Total");
				$excel ->xlsWriteLabel($xlsRows,24,"%");
				$excel ->xlsWriteLabel($xlsRows,25,"Total");
				$excel ->xlsWriteLabel($xlsRows,26,"%");
				$excel ->xlsWriteLabel($xlsRows,27,"Total");
				$excel ->xlsWriteLabel($xlsRows,28,"%");
				$excel ->xlsWriteLabel($xlsRows,29,"Total");
				$excel ->xlsWriteLabel($xlsRows,30,"%");
				$excel ->xlsWriteLabel($xlsRows,31,"Total");
				$excel ->xlsWriteLabel($xlsRows,32,"%");
				$excel ->xlsWriteLabel($xlsRows,33,"Total");
				$excel ->xlsWriteLabel($xlsRows,34,"%");
				$excel ->xlsWriteLabel($xlsRows,35,"Total");
				$excel ->xlsWriteLabel($xlsRows,36,"%");
				$excel ->xlsWriteLabel($xlsRows,37,"Total");
				$excel ->xlsWriteLabel($xlsRows,38,"%");
				$excel ->xlsWriteLabel($xlsRows,39,"Total");
				$excel ->xlsWriteLabel($xlsRows,40,"%");
				$excel ->xlsWriteLabel($xlsRows,41,"Total");
				$excel ->xlsWriteLabel($xlsRows,42,"%");
				$excel ->xlsWriteLabel($xlsRows,43,"Total");
				$excel ->xlsWriteLabel($xlsRows,44,"%");
				$excel ->xlsWriteLabel($xlsRows,45,"Total");
				$excel ->xlsWriteLabel($xlsRows,46,"Total");
				$excel ->xlsWriteLabel($xlsRows,47,"Total");
				$excel ->xlsWriteLabel($xlsRows,48,"%");
				$excel ->xlsWriteLabel($xlsRows,49,"Total");
				$excel ->xlsWriteLabel($xlsRows,50,"%");
				$excel ->xlsWriteLabel($xlsRows,51,"Total");
				$excel ->xlsWriteLabel($xlsRows,52,"%");
				$excel ->xlsWriteLabel($xlsRows,53,"Total");
				$excel ->xlsWriteLabel($xlsRows,54,"%");
				$excel ->xlsWriteLabel($xlsRows,55,"Total");
				$excel ->xlsWriteLabel($xlsRows,56,"%");
				$excel ->xlsWriteLabel($xlsRows,57,"Total");
				$excel ->xlsWriteLabel($xlsRows,58,"%");
				$excel ->xlsWriteLabel($xlsRows,59,"Total");
				$excel ->xlsWriteLabel($xlsRows,60,"%");
				$excel ->xlsWriteLabel($xlsRows,61,"Total");
				$excel ->xlsWriteLabel($xlsRows,62,"%");
				$excel ->xlsWriteLabel($xlsRows,63,"Total");
				$excel ->xlsWriteLabel($xlsRows,64,"%");
				$excel ->xlsWriteLabel($xlsRows,65,"Total");
				$excel ->xlsWriteLabel($xlsRows,66,"%");
				$excel ->xlsWriteLabel($xlsRows,67,"Total");
				$excel ->xlsWriteLabel($xlsRows,68,"%");
				$excel ->xlsWriteLabel($xlsRows,69,"Total");
				$excel ->xlsWriteLabel($xlsRows,70,"%");
				$excel ->xlsWriteLabel($xlsRows,71,"Total");
				$excel ->xlsWriteLabel($xlsRows,72,"%");
				$excel ->xlsWriteLabel($xlsRows,73,"Total");
				$excel ->xlsWriteLabel($xlsRows,74,"%");
				$excel ->xlsWriteLabel($xlsRows,75,"Total");
				$excel ->xlsWriteLabel($xlsRows,76,"%");
				$excel ->xlsWriteLabel($xlsRows,77,"Total");
				$excel ->xlsWriteLabel($xlsRows,78,"%");
				$excel ->xlsWriteLabel($xlsRows,79,"RR %");
				$excel ->xlsWriteLabel($xlsRows,80,"SCR %");
				$excel ->xlsWriteLabel($xlsRows,81,"ANP");
				$excel ->xlsWriteLabel($xlsRows,82,"AVG");
	

	$excel -> xlsClose();
	
	
	?>
