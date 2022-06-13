<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	function getSizeOffData($CampaignId=''){
		global $db;
		if($CampaignId!=''){
			$sql = "select count(a.CustomerId) from t_gn_customer a where a.CampaignId='".$CampaignId."'";
			return '<span style="color:green;font-weight:bold;">'.$db->valueSQL($sql).'</span>';
		}
	}
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		$sql = " select count(a.CustomerId) as Total,
					count(a.CustomerId) as Total,
					SUM( if(b.AssignManager is not null ,1,0)) as ASSIGNTOMAGER,
					SUM( if(b.AssignManager is null ,1,0)) as NOTASSIGNTOMAGER,
					SUM( if(b.AssignMgr is not null ,1,0)) as ASSIGNTOAM,
					SUM( if(b.AssignMgr is null ,1,0)) as NOTASSIGNTOAM,
					SUM( if(b.AssignMgr is not null and b.AssignSpv is not null ,1,0)) as ASSIGNTOSPV,
					SUM( if(b.AssignMgr is not null and b.AssignSpv is null ,1,0)) as NOTASSIGNTOSPV,
					SUM( if(b.AssignMgr is not null and b.AssignSpv is not null and b.AssignSelerId is not null ,1,0)) as ASSIGNTOAGENT,
					SUM( if(b.AssignMgr is not null and b.AssignSpv is not null and b.AssignSelerId is null ,1,0)) as NOTASSIGNTOAGENT,
					SUM( IF(b.AssignSpv is not null and b.AssignSelerId is null,1,0) ) as NOTASSIGNBYSPV,
				a.CampaignId, c.CampaignName,c.CampaignNumber
			from t_gn_customer a inner join t_gn_assignment b on a.CustomerId=b.CustomerId
			left join t_gn_campaign c on a.CampaignId=c.CampaignId ";
			
		$ListPages -> query($sql);
		$ListPages -> GroupBy("a.CampaignId");
		$ListPages -> OrderBy("a.CampaignId","DESC");
		$ListPages -> setLimit();
		$ListPages -> result();
		
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		
		<th nowrap class="custom-grid th-first">&nbsp;#</th>
		
		<!--<th nowrap class="custom-grid th-middle">&nbsp;Campaign Number</th> -->  
		
        <!-- t h nowrap class="custom-grid th-middle">&nbsp;Campaign ID</t h -->
		<th nowrap class="custom-grid th-middle">&nbsp;Campaign Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Size Campaign</th>
		<th nowrap class="custom-grid th-middle">&nbsp; IN ADMIN</th>
		<th nowrap class="custom-grid th-middle">&nbsp; Assign to MGR</th>
		<th nowrap class="custom-grid th-middle">&nbsp; Assign to AM</th>
		<th nowrap class="custom-grid th-middle">&nbsp; Not Assign to AM</th>
		<th nowrap class="custom-grid th-middle">&nbsp; Assign to SPV</th>
		<th nowrap class="custom-grid th-middle">&nbsp; Not Assign to SPV</th>
		<th nowrap class="custom-grid th-middle">&nbsp; Assign to TM</th>
		<th nowrap class="custom-grid th-lasted">&nbsp; Not Assign to TM</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<td class="content-first"><input type="checkbox" name="check_list_cmp" id="check_list_cmp" value="<?php echo $row->CampaignNumber; ?>"></td>
				
				<!-- t d class="content-middle"><?#php echo $row -> CampaignNumber; ?></t d -->
				<!--<td align="center" class="content-middle"><#?php echo $row -> CampaignId; ?></td>-->
				<td align="center" class="content-middle"><b style="color:green;"><?php echo $row -> CampaignName; ?></b></td>
				<td align="center" class="content-middle"><?php echo $row -> Total; ?></td>
				<td align="center" class="content-middle"><?php echo $row -> NOTASSIGNTOMAGER; ?></td>
				<td align="center" class="content-middle"><?php echo $row -> ASSIGNTOMAGER; ?></td>
				<td align="center" class="content-middle"><?php echo $row -> ASSIGNTOAM; ?></td>
				<td align="center" class="content-middle"><?php echo $row -> NOTASSIGNTOAM; ?></td>
				<td align="center" class="content-middle"><?php echo $row -> ASSIGNTOSPV; ?></td>
				<td align="center" class="content-middle"><?php echo $row -> NOTASSIGNTOSPV; ?></td>
				<td align="center" class="content-middle"><?php echo $row -> ASSIGNTOAGENT; ?></td>
				<td align="center" class="content-lasted"><?php echo $row -> NOTASSIGNTOAGENT; ?></td>
				
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>



