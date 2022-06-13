<?php
require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
require("../class/class.application.php");
require('../sisipan/parameters.php');



function getAgentName($AgentId)
{
	global $db;
	$sql = " select * from tms_agent a where a.UserId = '$AgentId'";
	$qry = $db -> query($sql);
	if( !$qry-> EOF() )
	{
		return $qry -> result_get_value('id')." - ".$qry -> result_get_value('full_name');
	}
}


function getCallReason($ReasonId)
{
	global $db;
	$sql = "select * from t_lk_callreason a where a.CallReasonId = '$ReasonId'";
	$qry = $db -> query($sql);
	if( !$qry-> EOF() )
	{
		return $qry -> result_get_value('CallReasonDesc');
	}
}


function getApprovalName($ReasonId)
{
	global $db;
	$sql = "select * from t_lk_aprove_status a where a.ApproveId = '$ReasonId'";
	$qry = $db -> query($sql);
	if( !$qry-> EOF() )
	{
		return $qry -> result_get_value('AproveName');
	}
}


function getCampaignName($CampaignId)
{
	global $db;
	$sql = " select a.CampaignName from t_gn_campaign a where a.CampaignId='$CampaignId'";
	$qry = $db->execute($sql,__FILE__,__LINE__);
	foreach($db->fetchrow($qry) as $rows){
		$datas = $rows[0];
	}
	
	return $datas[0];
}








if( $db -> havepost('query'))
{
	//global $db;
	$sql = base64_decode($_REQUEST['query']);
	$qry = $db -> execute($sql,__FILE__,__LINE__);
	
	echo "<html>
			<title>Show Detail Data Reassignment</title>
			<body>";
			
	echo "<h4 style=\"color:blue;font-family:arial;font-size:16px;\"> Detail Data </h4>";
	echo "<table style='border-bottom:1px solid #dddddd;' cellpadding=\"0\" cellspacing=\"0\">
			<tr> 
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>NO</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerId</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CampaignId</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>Code</th>
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>NumberCIF</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>Vintage</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerFirstName</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerDOB</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerAddressLine1</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerAddressLine2</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerAddressLine3</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerAddressLine4</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerCity</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerZipCode</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerHomePhoneNum</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerMobilePhoneNum</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerWorkPhoneNum</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerOfficeLine1</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerOfficeLine2</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerOfficeLine3</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>CustomerOfficeLine4</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>AssignAdmin</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>AssignMgr</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>AssignSpv</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>AssignSelerId</th>	
				<th style='background-color:#1e0ab2;color:#ffffff;font-family:arial;font-size:13px;padding:2px;height:35px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>AssignDate</th>
			</tr>";
			
			
	$no = 1;
	//while( $row = $this ->fetchrow($qry) )
	foreach($db ->fetchassoc($qry) as $rows )
	{
		$color=($no%2!=0?'#FFFDDD':'#FFFFFF');
		echo "<tr bgcolor=\"{$color}\">
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$no</td>
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerId]</td>
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;".getCampaignName($rows[CampaignId])."</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[Code]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[NumberCIF]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[Vintage]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerFirstName]</td>\n\r
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerDOB]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerAddressLine1]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerAddressLine2]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerAddressLine3]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerAddressLine4]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerCity]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerZipCode]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerHomePhoneNum]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerMobilePhoneNum]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerWorkPhoneNum]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerOfficeLine1]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerOfficeLine2]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerOfficeLine3]</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>&nbsp;$rows[CustomerOfficeLine4]</td>\n\r
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>".getAgentName($rows[AssignAdmin])."</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>".getAgentName($rows[AssignMgr])."</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>".getAgentName($rows[AssignSpv])."</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>".getAgentName($rows[AssignSelerId])."</td>\n\r	
			<td style='font-family:arial;font-size:12px;padding:2px;height:24px;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' nowrap>$rows[AssignDate]</td>
		</tr>";
		
	
		$no++;
	}
	
	echo "</table>";
	
	echo "</body>";
	echo "</html>";
}

?>