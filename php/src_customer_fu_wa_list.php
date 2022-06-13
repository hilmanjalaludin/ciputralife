<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	require("../class/lib.form.php");
	
	
/** get last notes ***/

	function getWAEmailStatus()
	{
		global $db;
		$sql = "select a.CallReasonId from t_lk_callreason a where a.CallReasonTerminate =1 ";
		$qry = $db -> query($sql);
		$datas=array();
		if( $qry -> result_num_rows() > 0 )
		{
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[] = $rows['CallReasonId']; 
			}
		}
		return implode(',',$datas);
	}	
	
	function getLastHistory($customerId='')
	{
		global $db;
		$sql = " select a.CallHistoryNotes, a.CallHistoryCreatedTs from t_gn_callhistory a
				 where a.CustomerId =".$customerId." order by a.CallHistoryCreatedTs DESC LIMIT 1 ";
				 
		$notes = $db -> valueSQl($sql);
		if( $notes!='') return $notes;
		else 
			return '-';
	}
	
/** set properties pages records **/
	
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);
	
/** set  genral query SQL  **/
 
	$sql = "SELECT 
				a.CustomerId, a.CustomerFirstName,f.CallReasonId, f.CallReasonDesc, a.CustomerUpdatedTs, 
				h.full_name tm, a.CampaignId, a.wa_email_status,
				IF(a.wa_email_status=0,'-', c.`Desc`) wa_email_st, 
				IF(a.wa_email_status=0,'-', g.full_name) wa_email_qa, 
				IF(a.wa_email_status=0,'-', a.wa_email_updatets) wa_email_ts
		FROM t_gn_customer a
		INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
		LEFT JOIN t_lk_gender e ON a.GenderId=e.GenderId
		LEFT JOIN t_lk_wa_email c ON a.wa_email_status=c.Id
		LEFT JOIN t_gn_campaign d on a.CampaignId=d.CampaignId 
		LEFT JOIN t_lk_callreason f on a.CallReasonId = f.CallReasonId
		left join tms_agent g on a.wa_email_updatebyid=g.UserId 
		left join tms_agent h on a.SellerId=h.UserId ";
	
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	

	$filter =  " AND b.AssignAdmin IS NOT NULL 
				 AND b.AssignMgr IS NOT NULL 
				 AND b.AssignSpv IS NOT NULL
				 AND f.CallReasonId IN (". getWAEmailStatus(). ")
				 AND b.AssignBlock=0 
				 and d.CampaignStatusFlag=1";
				 
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' 
				   AND a.CallReasonQue = 12";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
	
	if( $db->getSession('handling_type')==5 )			 
		$filter.=" #AND b.AssignSpv ='".$db -> getSession('UserId')."' 
				   AND a.wa_email_status <> 12";
				 	
	if( $db->havepost('cust_name') ) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('cust_number') ) 
		$filter.=" AND a.CustomerNumber LIKE '%".$db->escPost('cust_number')."%'"; 
		
	if( $db->havepost('call_result') )
		$filter.=" AND a.CallReasonId =".$db->escPost('call_result');	
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND a.CampaignId =".$db->escPost('campaign_id');	
    
	
 /** create set Limit record **/	
	
	$ListPages -> setWhere($filter);
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
	$ListPages -> setLimit();
	$ListPages -> result();
	//$ListPages -> echo_query();
	SetNoCache();
	
?>			
<style>
	.wraptext{color:green;font-size:11px;padding:3px;width:200px;}
	.wraptext:hover{color:blue;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<b style='color:#608ba9;'>#</b></th>	
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<b style='color:#608ba9;'>No.</b></th>			
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerFirstName');"><b style='color:#608ba9;'>Customer Name</b></span></th>   
        <th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('g.AproveName');"><b style='color:#608ba9;'>Last TM</b></span></th>
        <th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('g.AproveName');"><b style='color:#608ba9;'>Last Call Status</b></span></th>
        <th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('g.AproveName');"><b style='color:#608ba9;'>Last Call Date</b></span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerUpdatedTs');"><b style='color:#608ba9;'>Last QA</b></span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerUpdatedTs');"><b style='color:#608ba9;'>Last QA Status</b></span></th>
		<th nowrap class="custom-grid th-lasted">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerUpdatedTs');"><b style='color:#608ba9;'>Last QA Date</b></span></th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$call_content = $row -> CustomerId."_".$row -> CampaignId."_".$row -> CallReasonId."_".$row -> wa_email_status;
			$QualityId = $row -> wa_email_qa;	
	?>
			<tr class="onselect">
				<td class="content-first" width='5%'><input type="checkbox" value="<?php echo $row -> CustomerId."_".$row -> CampaignId."_".$row -> CallReasonId."_".$row -> wa_email_status; ?>" name="chk_cust_call" name="chk_cust_call" <?php echo ($db->getSession('handling_type')==0?'disabled':'');?> ></td>
				<td class="content-middle" nowrap><?php echo $no; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle" style='color:green;text-align:center;'><?php echo ($row -> tm); ?></td>
				<td class="content-middle" style='color:green;text-align:center;'><?php echo ($row -> CallReasonDesc); ?></td>
				<td class="content-lasted" style='color:green;text-align:center;'><?php echo $row -> CustomerUpdatedTs; ?></td>
				<td class="content-lasted" style='color:green;text-align:center;'><?php echo $row -> wa_email_st; ?></td>
				<td class="content-lasted" style='color:green;text-align:center;'><?php echo $row -> wa_email_qa; ?></td>
				<td class="content-lasted" style='color:green;text-align:center;'><?php echo $row -> wa_email_ts; ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


