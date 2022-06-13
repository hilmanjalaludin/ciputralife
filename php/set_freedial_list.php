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
		<th nowrap class="custom-grid th-first" align="center">&nbsp;No.</th>	
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;Call Number</th>       
        <th nowrap class="custom-grid th-middle" align="left">&nbsp;Call Date </th>
		<th nowrap class="custom-grid th-lasted" align="left">&nbsp;End Call Date</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color= ($no%2!=0?'#FFFDDD':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><?php echo $no; ?></td>
				<td class="content-middle" style="color:blue;cursor:pointer;"><span onclick="javascript:getPhoneNumber('<? echo $row->CallNumber;?>');"><?php echo $row->CallNumber;?></span></td>
				<td class="content-middle"><?php echo $db->Date->date_time_indonesia($row -> CallDate); ?></td>
				<td class="content-lasted"><?php echo $db->Date->date_time_indonesia($row ->CallEndDate); ?></td>
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>