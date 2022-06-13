<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");

	/** create object **/
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(25);
		
		$sql = " select * from t_gn_ftp_customers a ";
		
		$filter = "";
		if( $db -> havepost('work_branch') ) $filter .= " AND a.CustomerZipCode IN ('".IMPLODE("','",EXPLODE(',',$_REQUEST['work_branch']))."')";
		if( $db -> havepost('city') ) 		 $filter .= " AND a.CustomerCity LIKE '%".$_REQUEST['city']."%'";
		if( $db -> havepost('card_type') )	 $filter .= " AND a.CustomerCardType LIKE '%".$_REQUEST['card_type']."%'";
		if( $db -> havepost('start_date') )  $filter .= " AND date(a.CustomerUploadedTs)>='".$db-> Date -> english($_REQUEST['start_date'],'-')."' 
														  AND date(a.CustomerUploadedTs)<='".$db-> Date -> english($_REQUEST['end_date'],'-')."'";
				
		$ListPages -> query($sql);
		$ListPages -> setWhere($filter);
			
		if( $db -> havepost('order_by') ){ 
			$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
		}
		
	/** set filter *************************/
		$ListPages -> setLimit();
		$ListPages -> result();
		
		// echo $ListPages ->query;
	//SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('ftp_list_id');">#</a></th>
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>   
        <th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('CustomerNumber');" title="Order ASC/DESC">Customer Fine Code</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('CustomerFirstName');" title="Order ASC/DESC">Customer Name</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('GenderId');" title="Order ASC/DESC">Gender</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('CustomerCardType');" title="Order ASC/DESC">Card Type</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('CustomerAddressLine1');" title="Order ASC/DESC">Addresss</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('CustomerCity');" title="Order ASC/DESC">City</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('CustomerZipCode');" title="Order ASC/DESC">Zip Code</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('CustomerUploadedTs');" title="Order ASC/DESC">Upload date</span></th>
		<th nowrap class="custom-grid th-lasted">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('AssignCampign');" title="Order ASC/DESC">Assign</span></th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
		
			$color =($no%2!=0?'#FFFddd':'#FFFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>" style="color:#234777;">
				<td class="content-first"><input type="checkbox" name="ftp_list_id" id="ftp_list_id" value="<?php echo $row->CustomerId; ?>"></td>
				<td class="content-middle">&nbsp;<?php echo $no; ?></td>
				<td class="content-middle">&nbsp;<?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle">&nbsp;<?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle">&nbsp;<?php echo $row -> GenderId; ?></td>
				<td class="content-middle">&nbsp;<?php echo $row -> CustomerCardType; ?></td>
				<td class="content-middle">&nbsp;
					<?php echo $row -> CustomerAddressLine1; ?>
					<?php echo $row -> CustomerAddressLine2; ?>
					<?php echo $row -> CustomerAddressLine3; ?>
					<?php echo $row -> CustomerAddressLine4; ?></td>
				<td class="content-middle">&nbsp;<?php echo $row -> CustomerCity; ?></td>
				<td class="content-middle" nowrap>&nbsp;<?php echo $row -> CustomerZipCode; ?></td>
				<td class="content-middle" nowrap>&nbsp;<?php echo $db -> Date -> date_time_indonesia($row -> CustomerUploadedTs);?></td>
				<td class="content-lasted" align="center">&nbsp;<?php echo ($row -> AssignCampign?'YES':'NO'); ?></td>
			</tr>	
			
			
</tbody>
	<?php
		
		$no++;
		}
	?>
</table>



