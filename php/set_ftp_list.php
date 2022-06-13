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
		
		$sql = " SELECT * FROM tms_ftp_config a ";
	
	/** list pages ***/
	
		$ListPages -> query($sql);
		$ListPages -> setWhere();
		$ListPages -> OrderBy("a.ftp_id","ASC");
		$ListPages -> setLimit();
		$ListPages -> result();
	SetNoCache();
//	(ftp_id, ftp_port, ftp_user, ftp_pasword, ftp_host, ftp_get_file, ftp_put_file, ftp_history_file, ftp_flags)
	
?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;#</th>	
		<th nowrap class="custom-grid th-middle" align="center">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;FTP Port</th>        
        <th nowrap class="custom-grid th-middle" align="left">&nbsp;FTP User</th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;FTP Password</th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;FTP Server</th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;FTP Get Directory</th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;FTP Put Directory</th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;FTP History Directory</th>
		<th nowrap class="custom-grid th-lasted" align="left">&nbsp;Status</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
	?>
			<tr CLASS="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> ftp_id; ?>" name="ftp_id" id="ftp_id"></td>
				<td class="content-middle"><?php echo $no ?></td>
				<td class="content-middle"><?php echo $row -> ftp_port; ?></td>
				<td class="content-middle"><?php echo $row -> ftp_user; ?></td>
				<td class="content-middle"><?php echo $row -> ftp_pasword; ?></td>
				<td class="content-middle"><?php echo $row -> ftp_host; ?></td>
				<td class="content-middle"><?php echo $row -> ftp_get_file; ?></td>
				<td class="content-middle"><?php echo $row -> ftp_put_file; ?></td>
				<td class="content-middle"><?php echo $row -> ftp_history_file; ?></td>
				<td class="content-lasted"><?php echo ($row -> ftp_flags?'Active':'Not Active');?></td>
				
				
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



