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
		
		$sql = "select * from t_lk_aprove_status a left join tms_agent_profile b on a.UserLevelEskalasi=b.id ";
	
	/** list pages ***/
	
		$ListPages -> query($sql);
		$ListPages -> setWhere();
	
		if( $db -> havepost('order_by')) 
			$ListPages -> OrderBy($db -> escPost('order_by'),$db -> escPost('type'));
		else 
			$ListPages -> OrderBy("a.ApproveId","ASC");
		
		$ListPages -> setLimit();
		$ListPages -> result();
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_quality');">#</a></th>	
		<th nowrap class="custom-grid th-middle" align="center">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;<span class="header_order" id="a.ApproveId" onclick="extendsJQuery.orderBy(this.id);">Result ID</span></th>        
        <th nowrap class="custom-grid th-middle" align="left">&nbsp;<span class="header_order" id="a.AproveName" onclick="extendsJQuery.orderBy(this.id);">Result Name</span></th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;<span class="header_order" id="a.ApproveEskalasi" onclick="extendsJQuery.orderBy(this.id);">Eskalasi</span></th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;<span class="header_order" id="a.ApproveEskalasi" onclick="extendsJQuery.orderBy(this.id);">From User Level</span></th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;<span class="header_order" id="b.name" onclick="extendsJQuery.orderBy(this.id);">To User Level</span></th>
		<th nowrap width="15%" align="center" class="custom-grid th-lasted">&nbsp;<span class="header_order" id="a.AproveFlags"  onclick="extendsJQuery.orderBy(this.id);"></span>Status</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
		
	?>
			<tr CLASS="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> ApproveId; ?>" name="chk_quality" id="chk_quality"></td>
				<td class="content-middle" align="center"><?php echo $no ?></td>
				<td class="content-middle"><?php echo $row -> AproveCode; ?></td>
				<td class="content-middle"><?php echo $row -> AproveName; ?></td>
				
				<td class="content-middle"><?php echo ($row -> ApproveEskalasi?'Yes':'No'); ?></td>
					<td class="content-middle"><?php echo ($row -> name?$row -> name:'-'); ?></td>
				<td class="content-middle"><?php echo ($row -> name?$row -> name:'-'); ?></td>
				<td align="center" class="content-lasted"><?php echo ($row -> AproveFlags?'Active':'Not Active');?></td>
				
				
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



