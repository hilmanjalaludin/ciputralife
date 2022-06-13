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
 
	$sql = " SELECT 
				a.*, d.id AS UserId, d.full_name, b.CustomerNumber, b.CustomerFirstName,
				a.start_time,
				cmp.CampaignName AS cmpnum, rs.CallReasonDesc, b.CampaignId,
				b.CallReasonId, a.anumber
			FROM cc_recording a
				LEFT JOIN t_gn_customer b ON a.assignment_data =b.CustomerId
				LEFT JOIN cc_agent c ON a.agent_id=c.id
				INNER JOIN tms_agent d ON c.userid=d.id
				LEFT JOIN t_gn_campaign cmp ON cmp.campaignid = b.campaignid
				LEFT JOIN t_lk_callreason rs ON b.CallReasonId=rs.CallReasonId ";
					
					
	
	
	$ListPages -> query($sql);
	
	$filter = " AND b.CustomerNumber is not null ";	
 /** create set filter SQL if found **/	
	
	if( $db ->havepost('cust_number')) 
		$filter.= " and b.CustomerNumber LIKE '%".$db ->escPost('cust_number')."%'";
	
	if( $db ->havepost('cust_name')) 
		$filter.= " and b.CustomerFirstName LIKE '%".$db ->escPost('cust_name')."%'";
	
	if( $db ->havepost('campaign_id') ) 
		$filter.= " and b.CampaignId LIKE '%".$db ->escPost('campaign_id')."%'";
	
	if( $db ->havepost('call_result')) 
		$filter.= " and b.CallReasonId ='".$db ->escPost('call_result')."'";
	
	if( $db ->havepost('user_id')) 
		$filter.= " and d.UserId ='".$db ->escPost('user_id')."'";
		
	if( $db ->havepost('destination')) 
		$filter.= " and a.anumber ='".$db ->escPost('destination')."'";
		
	if( $db ->havepost('start_date')){ 
		$filter.= " and a.start_time>='".$db->formatDateEng($db ->escPost('start_date'))." 00:00:00'";	
	}
	else{
		$filter.= " and a.start_time='0000-00-00 00:00:00'";
	}
	if( $db ->havepost('end_date')) 
		$filter.= " and a.start_time<='".$db->formatDateEng($db ->escPost('end_date'))." 23:59:59'";	
		
    $ListPages -> setWhere($filter);
	
 /** create set Limit record **/	
 
	$ListPages -> OrderBy("date(a.start_time)","DESC");
	$ListPages -> setLimit();
	$ListPages -> result();
	
	SetNoCache();

?>
<style>
		.call-hover:hover{color:red;}
	</style>			
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;#</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>			
		<th nowrap class="custom-grid th-middle">&nbsp;Campaign</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Cust Number</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Cust Name</th>    
		<th nowrap class="custom-grid th-middle">&nbsp;User ID</th>		
		<th nowrap class="custom-grid th-middle">&nbsp;Call Result</th>	
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
				<td class="content-middle"><?php echo $row -> cmpnum; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle"><?php echo $row -> UserId." - ".$row -> full_name; ?></td>
				<td class="content-middle"><div class="call-hover" style="width:100px;padding:3px;color:green;font-size:11px;word-wrap:true;"><?php echo $row ->CallReasonDesc ?></div></td>
				
				<td class="content-middle"><?php echo $row -> file_voc_name; ?></td>
				<td class="content-middle"><?php echo formatSize($row -> file_voc_size); ?></td>
				<td class="content-middle"><?php echo $db->Date->date_time_indonesia($row -> start_time); ?></td>
				<td class="content-lasted"><?php echo toDuration($row -> duration); ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


