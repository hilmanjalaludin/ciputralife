<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
 /** set properties pages records **/
 
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);

 /** set  genral query SQL  **/
 
	$sql = "select a.*, d.id as UserId, d.full_name, b.CustomerNumber, b.CustomerFirstName,
					date_format(a.start_time,'%d-%m-%Y %H:%i:%s') as start_time
					from cc_recording a
						left join t_gn_customer b  on a.assignment_data =b.CustomerId
						left join cc_agent c on a.agent_id=c.id
						inner join tms_agent d on c.userid=d.id";
					
					
	//id, agent_id, agent_group, agent_ext, anumber, start_time,
	//end_time, duration, direction, session_key, 
	//file_voc_type, file_voc_size, file_voc_loc, file_voc_name, 
	//file_scr_type, file_scr_size, file_scr_loc, file_scr_name, memo, agent_note, assignment_data)
	
	
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	
	
	if( $db ->havepost('cust_number')) 
		$filter.= " and b.CustomerNumber LIKE '%".$db ->escPost('cust_number')."%'";
	
	if( $db ->havepost('cust_name')) 
		$filter.= " and b.CustomerFirstName LIKE '%".$db ->escPost('cust_number')."%'";
	
	if( $db ->havepost('campaign_id') ) 
		$filter.= " and b.CampaignId LIKE '%".$db ->escPost('campaign_id')."%'";
	
	if( $db ->havepost('call_result')) 
		$filter.= " and b.CallReasonId ='".$db ->escPost('call_result')."'";
	
	if( $db ->havepost('user_id')) 
		$filter.= " and b.SellerId ='".$db ->escPost('user_id')."'";
		
	if( $db ->havepost('destination')) 
		$filter.= " and a.anumber ='".$db ->escPost('destination')."'";
		
	if( $db ->havepost('start_date')) 
		$filter.= " and date(a.start_time)>='".$db->formatDateEng($db ->escPost('start_date'))."'";	
		
	if( $db ->havepost('end_date')) 
		$filter.= " and date(a.start_time)<='".$db->formatDateEng($db ->escPost('end_date'))."'";	
		
    $ListPages -> setWhere($filter);
	
 /** create set Limit record **/	

	$ListPages -> setLimit();
	$ListPages -> result();
	
	SetNoCache();

?>			
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;#</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>			
		<th nowrap class="custom-grid th-middle">&nbsp;Cust Number</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Cust Name</th>    
		<th nowrap class="custom-grid th-middle">&nbsp;User ID</th>		
        <th nowrap class="custom-grid th-middle">&nbsp;File Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;File Size</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Date</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Duration</th>
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
				<input type="checkbox"  name="chk_cust_call" name="chk_cust_call"  value="<?php echo $row -> id; ?>"></td>
				
				<td class="content-middle"><?php  echo $no; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle"><?php echo $row -> UserId." - ".$row -> full_name; ?></td>
				<td class="content-middle"><?php echo $row -> file_voc_name; ?></td>
				<td class="content-middle"><?php echo formatSize($row -> file_voc_size); ?></td>
				<td class="content-middle"><?php echo $row -> start_time; ?></td>
				<td class="content-lasted"><?php echo toDuration($row -> duration); ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


