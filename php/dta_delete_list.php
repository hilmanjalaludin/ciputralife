<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
 /** set properties pages records **/
 
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> IFpage('campaign');
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

			from t_gn_customer a
			left join t_gn_campaign b on a.CampaignId = b.CampaignId
			left join t_lk_gender d on a.GenderId = d.GenderId
			left join t_gn_assignment e on a.CustomerId = e.CustomerId
			left join tms_agent f on e.AssignSelerId = f.UserId";
			

			

	$ListPages -> query($sql);
	
		
	if($db->getSession('handling_type') == 9 || $db->getSession('handling_type') == 1)
	{
		if( $db ->havepost('agent_tms')) 
		{
			$filter.= " and e.AssignMgr = '".$db ->escPost('agent_tms')."'";
		}
	}
	else if($db->getSession('handling_type') == 2)
	{
		$filter.=" and e.AssignMgr = '".$db ->getSession('UserId')."'";
		if( $db ->havepost('agent_tms')) 
		{
			$filter.= " and e.AssignSpv = '".$db ->escPost('agent_tms')."'";
		}
	}
	else if($db->getSession('handling_type') == 3)
	{
		$filter.=" and e.AssignSpv = '".$db ->getSession('UserId')."'";
		if( $db ->havepost('agent_tms')) 
		{
			$filter.= " and e.AssignSelerId = '".$db ->escPost('agent_tms')."'";
		}
	}
		
	
	if( $db ->havepost('campaign')) 
		$filter.= " and a.CampaignId = '".$db ->escPost('campaign')."'";
		
	if( $db ->havepost('cust_name')) 
		$filter.= " and a.CustomerFirstName LIKE '%".$db ->escPost('cust_name')."%'";
		
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
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_menu');">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;NO</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;CampaignId</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Campaign</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Number</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;DOB</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Gender</th>
		<th nowrap class="custom-grid th-middle">&nbsp;City</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Address</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Agent</th>
		
	</tr>
</thead>	
<tbody>
	
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
		$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
	?>
	<tr class="onselect" bgcolor="<?php echo $color; ?>">
		
		<td class="content-first"><input type="checkbox" value="<?php echo $row -> CustomerId; ?>" name="chk_menu" name="chk_menu"></td>
		<td class="content-middle"><?php echo $no; ?></td>
		<td class="content-middle"><?php echo ($row->CampaignId?$row->CampaignId:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CampaignName?$row->CampaignName:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CustomerNumber?$row->CustomerNumber:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CustomerFirstName?$row->CustomerFirstName:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CustomerDOB?$row->CustomerDOB:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->GenderShortCode?$row->GenderShortCode:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->CustomerCity?$row->CustomerCity:'-'); ?></td>
		<td class="content-lasted"><?php echo ($row->CustomerAddressLine1?$row->CustomerAddressLine1:'-'); ?></td>
		<td class="content-middle"><?php echo ($row->tso?$row->tso:'-'); ?></td>
		
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