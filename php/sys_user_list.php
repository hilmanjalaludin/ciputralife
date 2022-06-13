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
		
		$sql = " SELECT 
					a.UserId as UserId, a.id, full_name, init_name, d.name as profile_id,
					agency_id, spv_id, mgr_id, a.password,
					a.logged_state as isLogin,if(a.telphone=1,'Yes','No') as telphone,
					ip_address, 
						IF(a.user_state=1,'Active','UnActive') as user_state, 
						IF(logged_state=1,'Sign In','Sign Out') as logged_state, login_count, update_password,
						IF( (SELECT concat(f.id,'- ',f.full_name)  from tms_agent f where f.UserId=a.mgr_id) is null,'-',(SELECT concat(f.id,' - ',f.full_name)  from tms_agent f where f.UserId=a.mgr_id)) as Manager,
						IF( (SELECT concat(f.id,'-',f.full_name)  from tms_agent f where f.UserId=a.spv_id) is null,'-',(SELECT concat(f.id,' - ',f.full_name)  from tms_agent f where f.UserId=a.spv_id)) as Spv,
					e.description as cc_group,
					h.description as SkillType, g.score as SkillScore		
				 FROM tms_agent a 
				 	LEFT JOIN cc_agent b on a.id=b.userid
					LEFT JOIN tms_agent_profile d on a.profile_id=d.id  
					LEFT JOIN cc_agent_group e on e.id=b.agent_group
					LEFT JOIN cc_agent_skill g on b.id=g.agent
					LEFT JOIN cc_skill h on g.skill=h.id ";
		
		$ListPages -> query($sql);
		
		if( $db->havepost('UserId')) $filter = " AND ( a.UserId LIKE '%".$_REQUEST['UserId']."%'   OR a.full_name LIKE '%".$_REQUEST['UserId']."%'  OR a.id LIKE '".$_REQUEST['UserId']."')";
		$ListPages -> setWhere($filter);
	/* order by ****/
		
		if( $db -> havepost('order_by'))
		{ 
			$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
		}
		
	/** set liimit 	***/
	
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();


?>
<!-- tet --->

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_menu');">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No.</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" id ="a.UserId" onclick="extendsJQuery.orderBy(this.id);">User ID</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" id ="a.full_name" onclick="extendsJQuery.orderBy(this.id);">User Name.</span></th>  
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" id ="a.init_name" onclick="extendsJQuery.orderBy(this.id);">Code Agent.</span></th>        
        <th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" id ="d.name" onclick="extendsJQuery.orderBy(this.id);">Previleges.</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;Supervisor.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Manager.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;CC Group.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Telephone.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;User State.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;User Status.</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;IP Location.</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color= ($no%2!=0?'#FAFFF9':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> UserId; ?>" name="chk_menu" name="chk_menu"></td>
				<td class="content-middle"><?php echo $no; ?></td>
				<td class="content-middle"><?php echo $row -> id; ?></td>
				<td class="content-middle"><?php echo $row -> full_name; ?></td>
				<td class="content-middle"><?php echo $row -> init_name; ?></td>
				<td class="content-middle"><?php echo $row -> profile_id; ?></td>
				<td class="content-middle"><?php echo $row -> Spv; ?></td>
				<td class="content-middle"><?php echo $row -> Manager; ?></td>
				<td class="content-middle"><?php echo $row -> cc_group; ?></td>
				<td class="content-middle" align='center'><?php echo $row -> telphone; ?></td>
				<td class="content-middle"><?php echo $row -> user_state; ?></td>
				<td class="content-middle"><span style="font-weight:normal;color:<?php echo ($row->isLogin==1?'green':'red');?>;" > <?php echo $row -> logged_state; ?></span></td>
				<td class="content-lasted" style="color:red;"><?php echo ($row -> ip_address?$row -> ip_address:'-'); ?></td>
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>