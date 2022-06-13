<?php
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$cignasystem	= $_REQUEST['cignasystem'];
//	$campaign		= explode(",",$_REQUEST['cmp']);
	$customer_ids	= $_REQUEST['cmp'];
//	$CustmerId = EXPLODE(",", $this -> escPost('customer_id'))
	
	// $campaign1		= implode("','",$campaign);
	$today = date("Y-m-d");
	
	/** FUNGSI EXCEL **/
	header("Content-type: application/vnd-ms-excel");
	$name		="CustomerOverview";
	$file		=".xls";
	$sdate		=$start_date;
	//	$filename 	= $name.$sdate."To".$end_date.$file;
	$filename 	= $name.$today.$file;
	
	header("Content-Disposition: attachment; filename=".($filename));
	
	
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(1000);
			
			$sql = "select 
				a.CustomerId, 
				a.CampaignId, 
				b.CampaignName,
				a.CustomerNumber, 
				a.CustomerFirstName,  
				a.CustomerDOB,
				d.GenderShortCode,
				a.CustomerCity, 				
				f.full_name as tso,
				a.CustomerAddressLine1

			from t_gn_backupcustomer a
			left join t_gn_campaign b on a.CampaignId = b.CampaignId
			left join t_lk_gender d on a.GenderId = d.GenderId
			left join t_gn_assignment e on a.CustomerId = e.CustomerId
			left join tms_agent f on e.AssignSelerId = f.UserId";
	
	
	$ListPages -> query($sql);
	
		
	$ListPages -> setWhere($filter);
	$ListPages -> GroupBy('a.CustomerId');
	$ListPages -> setLimit();
	//$ListPages -> echo_query();
	$ListPages -> result();
	
	SetNoCache();
?>
<style>
		.call-hover:hover{color:red;}
	</style>			
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-middle">&nbsp;NO</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Campaign</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;City</th>
		<th nowrap class="custom-grid th-middle">&nbsp;DOB</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Gender</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Agent</th>
		<th nowrap class="custom-grid th-middle">&nbsp;SPV</th>
		<th nowrap class="custom-grid th-middle">&nbsp;AM</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Last Call Status</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Last Call Date</th>
	</tr>
</thead>	
<tbody>
	
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
		$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
	?>
	<tr class="onselect" bgcolor="<?php echo $color; ?>">
		<td class="content-first">
		<td class="content-middle"><?php echo $no; ?></td>
		<td class="content-middle"><?php echo ($row->CampaignName?$row->CampaignName:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CustomerFirstName?$row->CustomerFirstName:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CustomerCity?$row->CustomerCity:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CustomerDOB?$row->CustomerDOB:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->GenderShortCode?$row->GenderShortCode:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->tso?$row->tso:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->spv?$row->spv:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->mgr?$row->mgr:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CallReasonDesc?$row->CallReasonDesc:'-'); ?></td>
		<td class="content-lasted"><?php echo ($row->CustomerUpdatedTs?$row->CustomerUpdatedTs:'-'); ?></td>
		
		<?php /*?></td>
		<!--<td class="content-middle"><?php echo ($row ->call_result?$row ->call_result:'-');?></td>
		<td class="content-middle"><?php echo ($row -> last_call?$row -> last_call:'-'); ?></td>
		<td class="content-lasted"><?php echo ($row -> cmp_name?$row -> cmp_name:'-'); ?></td>
		
			<td class="content-middle"><?php echo $row -> DOB; ?></td>
			<td class="content-middle"><?php echo $row -> recording; */?>
		
	</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>