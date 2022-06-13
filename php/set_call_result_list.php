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
		
		$sql = " select a.*,if(a.CallReasonEvent=0,'No','YES') as triger,if(a.CallReasonLater=0,'No','YES') as calllater, if(a.CallReasonFollowUp=0,'No','YES') as followup, b.*, IF(CallReasonStatusFlag=0,'Not Active','Active') as statusResult from t_lk_callreason a
					left join t_lk_callreasoncategory b
					on a.CallReasonCategoryId=b.CallReasonCategoryId";
			
		$ListPages -> query($sql);
		$ListPages -> setWhere();
		
		$ListPages -> setLimit();
		
		
		$ListPages -> result();
		//echo $ListPages -> query;
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_result');">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Result ID </th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Result Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Result Category</th>
		<th nowrap width="10%" align="center" class="custom-grid th-middle">&nbsp;Apply as Trigger Form</th>
		<th nowrap width="10%" class="custom-grid th-middle">&nbsp;Apply as Call Later</th>
		<th nowrap width="10%" class="custom-grid th-middle">&nbsp;Apply as Follow Up</th>
		<th nowrap width="10%" class="custom-grid th-middle">&nbsp;Result Status </th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Order</th>
	</tr>
</thead>	
<tbody>
	<?php
	//(CallReasonId, CallReasonCategoryId, CallReasonLevel, CallReasonCode, CallReasonDesc, CallReasonStatusFlag, CallReasonContactedFlag)
	
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
		
	?>
			<tr CLASS="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> CallReasonId; ?>" name="chk_result" id="chk_result"></td>
				<td class="content-middle"><?php echo $no ?></td>
				<td class="content-middle"><?php echo $row -> CallReasonCode; ?></td>
				<td class="content-middle"><?php echo $row -> CallReasonDesc; ?></td>
				<td class="content-middle"><?php echo $row -> CallReasonCategoryCode; ?></td>
				<!--<td class="content-middle"><?php //echo $row -> CallReasonLevel; ?></td>-->
				<td align="center" class="content-middle"><?php echo $row -> triger;?></td>
				<td align="center" class="content-middle"><?php echo $row -> calllater;?></td>
				<td align="center" class="content-middle"><?php echo $row -> followup;?></td>
				<td align="center" class="content-middle"><?php echo $row -> statusResult;?></td>
				<td align="center" class="content-lasted"><?php echo $row -> CallReasonOrder;?></td>
				
				
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



