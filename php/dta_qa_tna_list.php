<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.list.table.php");
	require(dirname(__FILE__)."/../class/class.query.parameter.php");
	require(dirname(__FILE__).'/../sisipan/parameters.php');
	
	
	SetNoCache();
	
	
/** get all status ***/

	function get_value_status()
	{
		$query = new ParameterQuery();
		if( is_object($query))
		{
			return $query -> ImplodeStatus();
		}
	}
	
/** get info data **/
	
	
	function getLastBySpv($CustomerId='',$spv_id=''){
		global $db;
		
		$V_DATAS = '-';
		
		if( $CustomerId !=''):
			$sql = "SELECT a.CallHistoryNotes, b.id, b.full_name  FROM t_gn_callhistory a 
					left join tms_agent b on a.CreatedById=b.UserId
						where a.CustomerId='".$CustomerId."'
						and b.handling_type NOT IN(4,5,2)
						order by a.CallHistoryId DESC LIMIT 1 ";
						
			$qry = $db ->execute($sql,__FILE__,__LINE__);	
			if( $qry && ($row=$db ->fetchrow($qry))){
				$V_DATAS = 'Re-Confirm ( '.$row->id.' )'; 
			}
			else{
				$sql2 = " select concat(a.id,' - ',a.full_name) as name from tms_agent a where a.UserId='".$spv_id."'";
				$res  = $db->valueSQL($sql2);
			}	if($res!='') $V_DATAS = $res;
		endif;	
		
		return $V_DATAS;
	}

	function getSpvName($spv_id=''){
		global $db;
		if( $spv_id!=''){
			$sql = " select concat(a.id,' - ',a.full_name) as name from tms_agent a where a.UserId='".$spv_id."'";
			$name = $db->valueSQL($sql);
			if( $name !=''){
				return '<span style="color:red;">'.$name.'</span>';
			}
			else return '-'; 	
		}
	}
	
	function getLastHistory($customerId=''){
		global $db;
		$sql = " select a.CallHistoryNotes, a.CallHistoryCreatedTs from t_gn_callhistory a
			where a.CustomerId =".$customerId."
			order by a.CallHistoryCreatedTs DESC LIMIT 1 ";
		$notes = $db -> valueSQl($sql);
		if( $notes!='') return $notes;
		else 
			return '-';
	}
	
	$status = array(16,17);
	
 /** set properties pages records **/
	$ListPages -> setPage(10);
	$ListPages -> pages = $db -> escPost('v_page'); 
	//$ListPages -> IFpage('campaign_id');
	
	
 /** set  genral query SQL  **/
 
	$sql = "SELECT DISTINCT 
		    b.CustomerId,
			c.CampaignName, 
			c.CampaignId,
			b.CustomerNumber, 
			b.CustomerFirstName, 
			d.CallReasonDesc, 
			b.CustomerUpdatedTs,
			e.AproveName,
			b.QaProsess,
			b.QaProsessId,
			a.*,
			CONCAT(i.id,' - ',i.full_name) AS Qaproses
		FROM t_gn_followup a
		INNER JOIN t_gn_customer b ON a.FuCustId=b.CustomerId
		INNER JOIN t_gn_campaign c ON c.CampaignId=b.CampaignId
		LEFT JOIN t_lk_callreason d ON d.CallReasonId=b.CallReasonId
		LEFT JOIN t_lk_aprove_status e ON e.ApproveId = a.FuQAStatus
		LEFT JOIN tms_agent i ON b.QaProsessId = i.UserId
		";
	
	$ListPages -> query($sql);
		
 	/** create set filter SQL if found **/	
    if( $db -> getSession('handling_type') == 5) 
        $filter .= "AND a.FuType ='2' AND a.FuQAStatus ='0' AND a.IsForm = '1'";
    if( $db ->getSession('handling_type') == 10)
		$filter .= "AND e.ApproveId = '19' AND a.FuType ='2' AND a.IsForm = '1'";
	if( $db -> havepost('cust_name')) 
		$filter .= " AND a.FuName LIKE '%".$db->escPost('cust_name')."%'"; 
	if( $db -> havepost('cust_numb')) 
		$filter .= " AND b.CustomerNumber LIKE '%".$db->escPost('cust_numb')."%'"; 
	if( $db -> havepost('call_status'))
		$filter .= " AND e.ApproveId  =" .$db -> escPost('call_status');
	if( $db -> havepost('campaign_id'))
		$filter .= " AND c.CampaignId =" .$db->escPost('campaign_id');
	$ListPages -> setWhere($filter);
	
 	/* order by ****/
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
	$ListPages -> GroupBy('b.CustomerId');
	$ListPages -> setLimit();
	$ListPages -> result();
	
	SetNoCache();
	
	

?>			
<style>
	.wraptext{color:green;font-size:11px;padding:3px;width:120px;}
	.wraptext:hover{color:blue;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" >&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_cust_call');">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>			
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('c.CampaignName');">Campaign Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('b.CustomerNumber');">Customer Number</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.FuName');">Customer Name</span></th>     
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.AproveName');">Approve Status</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('b.Qaproses');"> Status</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('b.QaProsessId');">QA Processing</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('b.CustomerUpdatedTs');">Last Update</span></th>          
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$diff_days = $db -> Date -> get_date_diff(date('Y-m-d'),$row -> PolicySalesDate);
			//$color= ($no%2!=0?'#FAFFF9':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first">
				<input _data_="<?php echo $row -> FuId; ?>" type="checkbox" value="<?php echo $row -> CustomerId; ?>" name="chk_cust_call" name="chk_cust_call" data="<?php echo $row -> FuType?>"></td>
				<td class="content-middle"><?php  echo $no; ?></td>
				<td class="content-middle"><?php  echo $row -> CampaignName; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle"><?php echo $row -> FuName; ?></td>
				<?php
					if( $row -> FuQAStatus == 0 ) {
 						echo '<td class="content-middle wraptext"> New Closing</td>';
					} else {
						echo '<td class="content-middle">'. $row -> AproveName.'</td>';
					}
				?>
				<td class="content-middle" <?php echo($row -> QaProsess?'style="color:red;"':'style="color:green;"');?> nowrap><?php echo ($row -> QaProsess?'Processing ...':'IDLE'); ?></td>
				<td class="content-middle" <?php echo($row -> QaProsessId?'style="color:red;"':'style="color:green;"');?> nowrap><?php echo ($row -> QaProsessId?$row->Qaproses : " - "); ?></td>
				<td class="content-lasted"><?php echo $row -> CustomerUpdatedTs; ?></td>
			</tr>
</tbody>
	<?php
		//echo $row -> CustomerId;
		$no++;
		};
		// ::1
	?>
</table>


