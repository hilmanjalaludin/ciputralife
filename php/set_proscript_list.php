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
		
		$sql = " select 
					a.ScriptId,
					b.ProductCode,
					b.ProductName,
					c.id,
					c.full_name,
					a.ScriptFileName,
					a.ScriptUpload,
					a.UploadDate,
					if( a.ScriptFlagStatus<>1,'Not Active','Active') as status
			from t_gn_productscript a
			left join t_gn_product b on a.ProductId=b.ProductId
			left join tms_agent c on a.UploadBy=c.UserId ";
					
					
		$ListPages -> query($sql);
		
		//$filter = " and a.ScriptFlagStatus=1 ";
		
		//$ListPages -> setWhere($filter);
		
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);" >#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Product Code </th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Product Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Script Title</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Script File</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Upload Date</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Upload By User</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Status</th>
	</tr>
</thead>	
<tbody>
	<?php
	//(CallReasonId, CallReasonCategoryId, CallReasonLevel, CallReasonCode, CallReasonDesc, CallReasonStatusFlag, CallReasonContactedFlag)
	//echo $ListPages ->query;
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
		
	?>
			<tr CLASS="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> ScriptId; ?>" name="chk_result" id="chk_result"></td>
				<td class="content-middle"><?php echo $no ?></td>
				<td class="content-middle"><?php echo $row -> ProductCode; ?></td>
				<td class="content-middle"><?php echo $row -> ProductName; ?></td>
				<td class="content-middle"><?php echo $row -> ScriptUpload; ?></td>
				<td class="content-middle"><?php echo $row -> ScriptFileName; ?></td>
				<td class="content-middle"><?php echo $db -> Date->date_time_indonesia($row -> UploadDate); ?></td>
				<td class="content-middle"><?php echo $row -> full_name; ?></td>
				<td class="content-lasted"><?php echo $row -> status;?></td>
				
				
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



