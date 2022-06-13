<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(8);
		
		$sql = " select if(a.flag=1,'Active','Unactive') as status, a.*, b.* from tms_application_menu a 
				left join tms_group_menu b on b.GroupId=a.group_menu";
			
		$ListPages -> query($sql);
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_menu');">#</a></th>		
		<th nowrap class="custom-grid th-middle">&nbsp;Menu ID</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Menu Name.</th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Group Menu.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Mobile Phone.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Status.</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr CLASS="onselect">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> id; ?>" name="chk_menu" name="chk_menu"></td>
				<td class="content-middle"><?php echo $row -> id; ?></td>
				<td class="content-middle"><?php echo $row -> menu; ?></td>
				<td class="content-middle"  >
					<span id="textm_<?php echo $row -> id;?>" onclick="choiceGroup('<?php echo $row -> id; ?>');"><?php echo ($row -> GroupName!=''?$row -> GroupName:'Uknown'); ?></span>
					<span id="menu_<?php echo $row -> id;?>"></span></td>
				<td class="content-middle"><?php echo $row -> file_name; ?></td>
				<td class="content-lasted"><?php echo $row -> status; ?></td>
				
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>



