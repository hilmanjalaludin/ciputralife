<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		$sql = "select  
					a.ApprovalHistoryId,
					b.CustomerNumber,
					b.CustomerFirstName,
					c.ApprovalItem,
					a.ApprovalOldValue,
					a.ApprovalNewValue,
					d.id,
					d.full_name,
					date_format(a.ApprovalCreatedTs,'%d-%m-%Y %H:%i:%s') as ApprovalCreatedTs,
						IF(e.PhoneDesc is null,'-',e.PhoneDesc) as PhoneDesc
				from t_gn_approvalhistory a
				left join t_gn_customer b on a.CustomerId=b.CustomerId
				left join t_lk_approvalitem c on a.ApprovalItemId=c.ApprovalItemId
				left join tms_agent d on a.CreatedById=d.UserId 
				left join t_lk_phonetype e on a.ApprovePhoneType=e.PhoneType ";

		$where = " AND c.ApprovalItemId = 1 
				   AND a.ApprovalApprovedFlag<>1 ";
		
		
		$ListPages -> query($sql);
		$ListPages -> setWhere($where);
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Number </th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Customer Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Approve Item</th>
		<th nowrap class="custom-grid th-middle">&nbsp;From Customer Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;To Customer Name</th>
		
		<th nowrap class="custom-grid th-middle">&nbsp;Create Date </th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Requet By </th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr CLASS="onselect">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> ApprovalHistoryId; ?>" name="chk_name" id="chk_name"></td>
				<td class="content-middle"><?php echo $no ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle"><?php echo $row -> ApprovalItem; ?></td>
				<td class="content-middle"><?php echo $row -> ApprovalOldValue; ?></td>
				<td class="content-middle"><?php echo $row -> ApprovalNewValue; ?></td>
				<td class="content-middle"><?php echo $row -> ApprovalCreatedTs; ?></td>
				<td class="content-lasted"><?php echo $row -> id." - ".$row -> full_name; ?></td>
					
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



