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
		
		$sql = " select a.GroupId, a.GroupName, 
				 if(a.GroupShow=1,'Active','Unactive') as status,
				 a.GroupDesc,
				 a.CreateDate,
				 a.UserCreate
				 from tms_group_menu a ";
			
		$ListPages -> query($sql);
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();
	
	function getOnListMenu($group_id){
		global $db;
		$string ='';
		$sql =" select a.menu_group, a.name from tms_agent_profile a ";
		$qry = $db->execute($sql,__FILE__,__LINE__);
		$i=0;
		while( $row  = $db->fetchrow($qry)){
			$list = explode(',',$row->menu_group);
				if(in_array($group_id,$list)){
					$menu_group[] = $row->name; 
				}
			$i++;
		} 
		
		foreach($menu_group as $key=>$menu){
			$string.= $menu." , ";
		}
		return "<span style='color:red;'>".substr($string,0,strlen($string)-2)."</span>";
	}
?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_menu');">#</a></th>		
		<th nowrap class="custom-grid th-middle">&nbsp;Group ID</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Group Name.</th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Group Desc.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Active On Privileges.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Create date.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Create By.</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Group status.</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> GroupId; ?>" name="chk_menu" name="chk_menu"></td>
				<td class="content-middle"><?php echo $row -> GroupId; ?></td>
				<td class="content-middle"><?php echo $row -> GroupName; ?></td>
				<td class="content-middle"><?php echo $row -> GroupDesc; ?></td>
				<td class="content-middle"><?php echo getOnListMenu($row -> GroupId); ?></td>
				<td class="content-middle"><?php echo $db->Date->date_time_indonesia($row -> CreateDate); ?></td>
				<td class="content-middle"><?php echo $row -> UserCreate; ?></td>
				<td class="content-lasted"><?php echo $row -> status;?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



