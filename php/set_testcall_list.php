<?php
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(20);
		
		$sql = " select a.*, b.ProviderName, b.ProviderCode , c.full_name 
			from tms_misdn_report a 
			left join tms_misdn_provider b on a.ProviderId=b.ProviderId
			left join tms_agent c on a.CallByUser=c.UserId";
					
		
		$ListPages -> query($sql);
		
	
		$ListPages -> setWhere();
		$ListPages -> OrderBy('a.CallId','DESC');
		$ListPages -> setLimit();
		$ListPages -> result();
		
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;No.</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Call date </th>
		<th nowrap class="custom-grid th-middle">&nbsp;Call Number</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Provider Code </th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Provider Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Test By user </th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Notes </th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color= ($no%2!=0?'#FAFFF9':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><?php echo $no; ?></td>
				<td class="content-middle"><?php echo $row -> CallDate; ?></td>
				<td class="content-middle" style="color:blue;"><?php echo $row->CallNumber;?></td>
				<td class="content-middle"><?php echo $row -> ProviderCode; ?></td>
				<td class="content-middle"><?php echo $row ->ProviderName; ?></td>
				<td class="content-middle"><?php echo $row -> full_name; ?></td>
				<td class="content-lasted">
					<div style="width:200px;word-wrap:break-word;padding:4px;">
						<?php echo $row -> CallRemarks; ?>
					</div>
				</td>
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>