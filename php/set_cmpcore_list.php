<?php

	require_once(dirname(__FILE__)."/../sisipan/sessions.php");
	require_once(dirname(__FILE__)."/../fungsi/global.php");
	require_once(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require_once(dirname(__FILE__)."/../class/class.list.table.php");
	require_once(dirname(__FILE__)."/../class/class.application.php");
	require_once(dirname(__FILE__)."/../sisipan/parameters.php");
	require_once(dirname(__FILE__)."/../class/lib.form.php");
	
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		$sql = " select campaignGroupId, CampaignGroupCode, 
						CampaignGroupName,  
						if(CampaignGroupStatusFlag<>1,'Not Active','Active') as campaignStatusCore
				 from t_gn_campaigngroup ";
		
		
		$where = " AND ( CampaignGroupCode LIKE '%".$db->escPost('cbFilter')."%' ".
			     " OR CampaignGroupName LIKE '%".$db->escPost('cbFilter')."%' )";			
		
		$ListPages -> query($sql);
		$ListPages -> setWhere($where);
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		
		<th nowrap class="custom-grid th-first" >&nbsp;<a href="javascript:void(0);" onclick="javascript:doJava.checkedAll('cmp_check_list');">#</a> </th>	
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;No</th>  
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" id ="CampaignGroupCode" onclick="extendsJQuery.orderBy(this.id);">Campaign Core ID</span></th>        
        <th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" id ="CampaignGroupName" onclick="extendsJQuery.orderBy(this.id);">Campaign Core Name</span></th>
		<th nowrap class="custom-grid th-lasted" style="text-align:left;">&nbsp;Status</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr CLASS="onselect">
				
				<td class="content-first" width="5%"><?php $jpForm -> jpCheck('cmp_check_list',NULL,$row -> campaignGroupId); ?></td>
				<td class="content-middle" width="5%"><?php echo $no ?></td>
				<td class="content-middle" width="30%"><?php echo $row -> CampaignGroupCode; ?></td>
				<td class="content-middle" width="30%"><?php echo $row -> CampaignGroupName; ?></td>
				<td class="content-lasted" width="30%"><?php echo $row -> campaignStatusCore;?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



