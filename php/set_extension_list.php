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
		
		$sql = " select
					a.id as extId,
					a.ext_number as extNumber, 
					b.set_value as extPbx,
					a.ext_desc as extDesc,
					a.ext_type as extType,
					a.ext_status as extStatus,
					a.ext_location as extLocation,
					c.full_name as userName
			 from cc_extension_agent a left join cc_pbx_settings b on a.pbx=b.id 
				left join tms_agent c on a.ext_location=c.ip_address ";
					
		
		$ListPages -> query($sql);
		
	
		$ListPages -> setWhere();
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_menu');">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No.</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Ext. Number</th>
		<th nowrap class="custom-grid th-middle">&nbsp;User State</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Ext. PBX </th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Ext. Description </th>
		<th nowrap class="custom-grid th-middle">&nbsp;Ext. Type </th>
		<th nowrap class="custom-grid th-middle">&nbsp;Ext. Status</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Ext. Location</th>
		
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color= ($no%2!=0?'#FAFFF9':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> extId; ?>" name="chk_ext" name="chk_ext"></td>
				<td class="content-middle"><?php echo $no; ?></td>
				<td class="content-middle"><?php echo $row -> extNumber; ?></td>
				<td class="content-middle" style="color:blue;"><?php echo ($row -> userName?$row -> userName:'-'); ?></td>
				<td class="content-middle"><?php echo $row -> extPbx; ?></td>
				<td class="content-middle"><?php echo ($row -> extDesc?$row -> extDesc:'-'); ?></td>
				<td class="content-middle"><?php echo $row -> extType; ?></td>
				<td class="content-middle"><?php echo $row -> extStatus; ?></td>
				<td class="content-lasted"><?php echo $row -> extLocation; ?></td>
				
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>