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
	function countreferal($customerid){
		return 0;
	}
 /** set properties pages records **/
	$ListPages -> setPage(10);
	$ListPages -> pages = $db -> escPost('v_page'); 
	////$ListPages -> IFpage('campaign_id');
	
	
 /** set  genral query SQL  **/
 
$sql = " SELECT 
				e.CampaignId, e.CampaignName, a.CustomerId AS CustomerId,
				a.CustomerId AS CustId,a.CustomerFirstName, COUNT(b.ReferalId) AS jml, 
					(SELECT COUNT(x.ReferalId)
						FROM t_gn_referal x
						WHERE 
							x.ReferalCustomerId=CustId 
							AND x.ReferalQAStatus=1) AS approve, 
					(SELECT COUNT(x.ReferalId)
						FROM t_gn_referal x
						WHERE 
							x.ReferalCustomerId=CustId 
							AND x.ReferalQAStatus=0) AS reject, 
				c.init_name AS SellerId, 
				DATE(b.ReferalCreateTs) AS CreateDate, 
				d.init_name AS QAId, 
				DATE(b.ReferalUpdatedTs) AS QAUpdate,
				b.ReferalPhone1,
				b.ReferalPhone2,
				b.ReferalPhone3,
				b.ReferalSellerId
			FROM t_gn_customer a
				RIGHT JOIN t_gn_referal b ON a.CustomerId=b.ReferalCustomerId
				LEFT JOIN tms_agent c ON a.SellerId = c.UserId
				LEFT JOIN tms_agent d ON b.ReferalUpdateQAUid = d.UserId
				LEFT JOIN t_gn_campaign e ON a.CampaignId=e.CampaignId ";
			
	
	//$ListPages -> setPage(10);			 
	$ListPages -> query($sql);
		
	if( $db->havepost('cust_number')) 
		$filter.=" AND b.ReferalName LIKE '%".$db->escPost('cust_number')."%'";  
		
	if( $db->havepost('user_id')) 
		$filter.=" AND b.ReferalSellerId LIKE '%".$db->escPost('user_id')."%'"; 
	
	if( $db->havepost('home_phone') )
		$filter.=" AND b.ReferalPhone1 =".$db->escPost('home_phone');
	
	if( $db->havepost('office_phone') )
		$filter.=" AND b.ReferalPhone2 =".$db->escPost('office_phone');
		
	if( $db->havepost('mobile_phone') )
		$filter.=" AND b.ReferalPhone3 =".$db->escPost('mobile_phone');
	
	if( $db->havepost('start_date') && $db->havepost('end_date') ){
		$filter .= " AND date(b.ReferalCreateTs) >= '".$db->formatDateEng($_REQUEST['start_date'])."' 
					 AND date(b.ReferalCreateTs) <= '".$db->formatDateEng($_REQUEST['end_date'])."' "; 
	}
	
	if( $db->havepost('call_result') ){
		if($db->escPost('call_result')=='null'){
			$filter.= "AND b.ReferalQAStatus is null";
		}
		else{
			$filter.= "AND b.ReferalQAStatus = ".$db->escPost('call_result');
		}
	}
	
	if( $db->havepost('cust_name')) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND e.CampaignId =".$db->escPost('campaign_id');
	
	$filter.=" group by CustomerId";
	$ListPages -> setWhere($filter);
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
	$ListPages -> setLimit();
	
	$ListPages -> result();
	//echo 	$ListPages -> query;
	SetNoCache();
	
	

?>			
<style>
	.wraptext{color:green;font-size:11px;padding:3px;width:120px;}
	.wraptext:hover{color:blue;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="15"> 
		<th rowspan = "2" nowrap class="custom-grid th-first" >&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_cust_call');">#</a></th>	
		<th rowspan = "2" nowrap class="custom-grid th-middle">&nbsp;No</th>			
		<th rowspan = "2" nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.CampaignName');">Campaign</span></th>
		<th rowspan = "2" nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerFirstName');">Customer Name</span></th>     
		<th colspan = "3" nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.full_name');">Referal</span></th>
		<th colspan = "2" nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.full_name');">Seller</span></th>
		<th colspan = "2" nowrap class="custom-grid th-lasted" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.full_name');">QA</span></th>
	</tr>
	<tr height="15"> 			    
		<th nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('jml');">Count</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('approve');">Approve</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('reject');">Reject</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('SellerId');">Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('CreateDate');">Created</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('QAId');">Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:center;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('QAUpdate');">Last Update</span></th>
	</tr>
</thead>	
<tbody>
	<?php
		//print_r($ListPages);
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$diff_days = $db -> Date -> get_date_diff(date('Y-m-d'),$row -> PolicySalesDate);
			$color= ($no%2!=0?'#FAFFF9':'#FFFFFF');
			$creferal = countreferal($row -> CustomerId);
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first">
				<input type="checkbox" value="<?php echo $row -> CustomerId; ?>" name="chk_cust_call" name="chk_cust_call"></td>
				<td class="content-middle"><?php  echo $no; ?></td>
				<td class="content-middle"><div style="width:100px;"><?php  echo $row -> CampaignName; ?></div></td>
				<td class="content-middle" nowrap><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> jml; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> approve; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> reject; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> SellerId; ?></td>
				<td class="content-middle" nowrap><?php echo date('d/m/Y',strtotime($row -> CreateDate)); ?></td>
				<td class="content-middle" nowrap><?php echo $row -> QAId; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> QAUpdate; ?></td>
			</tr>
</tbody>
	<?php
		//echo $row -> CustomerId;
		$no++;
		};
		
	?>
</table>


