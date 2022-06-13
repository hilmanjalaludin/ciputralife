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
	//echo $db->escPost('aksi');
   if($db->escPost('aksi')=="deletion"){
	$sql = "SELECT 
				distinct(a.CustomerId) as CustomerId,  a.CallReasonId, c.CampaignName, a.CustomerFirstName, 
				a.CustomerLastName, a.QaProsess,
				e.id as agentName, 
				DATE(g.PolicySalesDate) as PolicySalesDate,
				DATE(a.CustomerRejectedDate) as CustomerRejectedDate,
				h.AproveName
			FROM t_gn_customer_db a 
				INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
				LEFT JOIN t_lk_callreason d on a.CallReasonId =d.CallReasonId
				LEFT JOIN tms_agent e on a.SellerId=e.UserId 
				LEFT JOIN t_gn_policyautogen f ON a.CustomerId=f.CustomerId
				LEFT JOIN t_gn_policy g ON f.PolicyLastNumber=g.PolicyId
				LEFT JOIN t_lk_aprove_status h ON h.ApproveId=a.CallReasonQue
				LEFT JOIN t_gn_productplan pl ON g.ProductPlanId=pl.ProductPlanId
				LEFT JOIN t_gn_product pr ON pl.ProductId=pr.ProductId";
   }else{
   	$sql = "SELECT
				distinct(a.CustomerId) as CustomerId,  a.CallReasonId, c.CampaignName, a.CustomerFirstName,
				a.CustomerLastName, a.QaProsess,
				e.id as agentName,
				DATE(g.PolicySalesDate) as PolicySalesDate,
				DATE(a.CustomerRejectedDate) as CustomerRejectedDate,
				h.AproveName
			FROM t_gn_customer a
				INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId
				LEFT JOIN t_lk_callreason d on a.CallReasonId =d.CallReasonId
				LEFT JOIN tms_agent e on a.SellerId=e.UserId
				LEFT JOIN t_gn_policyautogen f ON a.CustomerId=f.CustomerId
				LEFT JOIN t_gn_policy g ON f.PolicyNumber=g.PolicyNumber
				LEFT JOIN t_lk_aprove_status h ON h.ApproveId=a.CallReasonQue
				LEFT JOIN t_gn_productplan pl ON g.ProductPlanId=pl.ProductPlanId
				LEFT JOIN t_gn_product pr ON pl.ProductId=pr.ProductId";
   }
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	

	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null  
				 AND b.AssignBlock=0
				 AND a.CallReasonQue IN('".$db -> Entity ->VerifiedNotBack()."')";
				 // AND c.CampaignStatusFlag=1 ";
				 
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' ";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 	
	if( $db->havepost('cust_name') ) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('cust_number') ) 
		$filter.=" AND a.CustomerNumber LIKE '%".$db->escPost('cust_number')."%'"; 
		
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND a.CampaignId =".$db->escPost('campaign_id');	
	

	if( $db->havepost('start_date') && $db->havepost('end_date') )
		$filter .= " AND date(a.CustomerUpdatedTs) >= '".$db->formatDateEng($_REQUEST['start_date'])."' 
					 AND date(a.CustomerUpdatedTs) <= '".$db->formatDateEng($_REQUEST['end_date'])."' "; 
	
	if( $db->havepost('ProductGroup') ){
		$filter.=" AND pr.product_category_id =".$db->escPost('ProductGroup');
	}
    if( $db->havepost('call_result')){ 
		$filter .=" AND a.CallReasonId ='".$db->escPost('call_result')."'"; 
		$filter .=" AND a.CallReasonId IN(".get_value_status().") ";
	}
	else{
		$filter.=" AND a.CallReasonId IN(".get_value_status().") ";
	}
	
		$filter.="Group By a.CustomerId ";
	
	$ListPages -> setWhere($filter);
	
 /* order by ****/
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
	$ListPages -> setLimit();
	$ListPages -> result();
	// if( $db->getSession('handling_type')==9 ) 
		// echo $ListPages ->query;
	SetNoCache();
	
	

?>			
<style>
	.wraptext{color:green;font-size:11px;padding:3px;width:120px;}
	.wraptext:hover{color:blue;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" >&nbsp;#</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>			
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('c.CampaignName');">Campaign Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerFirstName');">Customer Name</span></th>     
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.id');">Agent Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.full_name');">Verified Status</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('g.PolicySalesDate');">Policy Sales Date</span></th>
		<th nowrap class="custom-grid th-lasted" style="text-align:left;">&nbsp;Days</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$diff_days = $db -> Date -> get_date_diff($row-> CustomerRejectedDate, $row -> PolicySalesDate);
			$color= ($no%2!=0?'#FAFFF9':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first">
				<input type="checkbox" value="<?php echo $row -> CustomerId; ?>" name="chk_cust_call" name="chk_cust_call" disabled="disabled" ></td>
				<td class="content-middle"><?php  echo $no; ?></td>
				<td class="content-middle"><div style="width:100px;"><?php  echo $row -> CampaignName; ?></div></td>
				<td class="content-middle" nowrap><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> agentName; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> AproveName; ?></td>
				<td class="content-middle" nowrap><?php echo $db -> Date -> indonesia($row -> PolicySalesDate); ?></td>
				<td class="content-lasted" style="color:red;">+<?php echo $diff_days -> days();?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>