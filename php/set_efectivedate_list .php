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
		
	
		$sql = "select a.CutoffDate as value, a.CutoffDate as text from  t_lk_cutoffdate a";
					
		$ListPages -> query($sql);
		$ListPages -> setWhere();
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_lastcall');">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Start Date</th>        
        <th nowrap class="custom-grid th-middle">&nbsp;End Date </th>
		<th nowrap class="custom-grid th-middle">&nbsp;Start Time </th>
		<th nowrap class="custom-grid th-middle">&nbsp;End Time </th>
		<th nowrap class="custom-grid th-middle">&nbsp;Create Date </th>
		<th nowrap class="custom-grid th-middle">&nbsp;Create By </th>
		<th nowrap class="custom-grid th-middle">&nbsp;Status </th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Reason</th>
	</tr>
</thead>	
<style>
	.hover:hover{color:blue;cursor:pointer;font-size:11px;}
</style>
<tbody>
	<?php
	//(CallReasonId, CallReasonCategoryId, CallReasonLevel, CallReasonCode, CallReasonDesc, CallReasonStatusFlag, CallReasonContactedFlag)
	
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
		
	?>
			<tr CLASS="onselect" bgcolor="<?php echo $color;?>">
				<td nowrap class="content-first"><input type="checkbox" value="<?php echo $row -> LastCallId; ?>" name="chk_lastcall" id="chk_lastcall"></td>
				<td nowrap class="content-middle" style="padding:3px;"><?php echo $no ?></td>
				<td nowrap class="content-middle" style="padding:3px;"><?php echo $row -> LastCallStartDate; ?></td>
				<td nowrap class="content-middle" style="padding:3px;"><?php echo $row -> LastCallEndDate; ?></td>
				<td nowrap class="content-middle" style="padding:3px;"><?php echo $row -> LastCallStartTime; ?></td>
				<td nowrap class="content-middle" style="padding:3px;"><?php echo $row -> LastCallEndTime; ?></td>
				<td nowrap class="content-middle" style="padding:3px;"><?php echo $row -> LastCallCreateDate; ?></td>
				<td nowrap class="content-middle" style="padding:3px;"><?php echo $row -> full_name; ?></td>
				<td nowrap  class="content-middle" style="padding:3px;"><?php echo $row -> LastCallStatus; ?></td>
				<td class="content-lasted hover"><div style="text-align:justify;width:160px;padding:2px;"><?php echo $row -> LastCallReason;?></div></td>
				
				
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



