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
					a.PrefixNumberId,
					b.ProductCode, 
					b.ProductName,
					a.PrefixChar,
					a.PrefixLength,
					c.FormLayout,
				if( a.PrefixFlagStatus<>1, 'Not Active','Active') as Status
				 from t_gn_productprefixnumber a
				left join t_gn_product b on a.ProductId=b.ProductId
				LEFT JOIN t_gn_formlayout c on b.ProductId=c.ProductId ";
					
					
		$ListPages -> query($sql);
		$ListPages -> setWhere();
		if( $db -> havepost('order_by'))
		{ 
			$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
		}
		$ListPages -> setLimit();
		$ListPages -> result();
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_result');">#</a></th>	
		<th nowrap class="custom-grid th-middle" align="center">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;<span class="header_order" id ="b.ProductCode" onclick="extendsJQuery.orderBy(this.id);">Product Code </th>        
        <th nowrap class="custom-grid th-middle" align="left">&nbsp;Product Name</th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;Prefix</th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;Max Length</th>
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;Form Product</th>
		<th nowrap class="custom-grid th-lasted" align="left">&nbsp;Prefix Status</th>
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
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> PrefixNumberId; ?>" name="chk_result" id="chk_result"></td>
				<td class="content-middle"><?php echo $no ?></td>
				<td class="content-middle"><?php echo $row -> ProductCode; ?></td>
				<td class="content-middle"><?php echo $row -> ProductName; ?></td>
				<td class="content-middle"><?php echo $row -> PrefixChar; ?></td>
				<td class="content-middle"><?php echo $row -> PrefixLength; ?></td>
				<td class="content-middle"><b><?php echo $row -> FormLayout; ?></b></td>
				<td class="content-lasted"><?php echo $row -> Status;?></td>
				
				
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



