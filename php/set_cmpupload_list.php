<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	function getSizeOffData($CampaignId=''){
		global $db;
		if($CampaignId!=''){
			$sql = "select count(a.CustomerId) from t_gn_customer a where a.CampaignId='".$CampaignId."'";
			return '<span style="color:green;font-weight:bold;">'.$db->valueSQL($sql).'</span>';
			//return '<span style="color:green;font-weight:bold;">skip this for testing reason</span>';
		}
	}
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(20);
		
		$sql = " select distinct
					a.CampaignNumber,	a.CampaignName,a.CampaignId,
					c.CampaignTypeCode, d.CignaSystemCode,
					IF( (a.CampaignReUploadFlag is null OR a.CampaignReUploadFlag=0),'N','Y')  as ReUploadReasonId,
					IF( e.ReUploadReason is null, '-',e.ReUploadReason) as ReUploadReason,
					a.CampaignEndDate, a.CampaignExtendedDate,
					IF( a.CampaignStatusFlag=0,'Not Active','Active') as CmpStatus
				FROM t_gn_campaign a 
					
					LEFT JOIN t_lk_campaigntype c on a.CampaignTypeId=c.CampaignTypeId
					LEFT JOIN t_lk_cignasystem d on a.CignaSystemId=d.CignaSystemId
					LEFT JOIN t_lk_reuploadreason e on a.ReUploadReasonId=e.ReUploadReasonId
				 ";
			
		$ListPages -> query($sql);
		
	/** set filter *************************/
	
		$filter = '';
		if( $db -> havepost('status_campaign') )
		{
			if($_REQUEST['status_campaign']==0) $filter = " and  a.CampaignStatusFlag=0 "; 
			if($_REQUEST['status_campaign']==1) $filter = " and  a.CampaignStatusFlag=1 ";
		}
	
		$ListPages -> setWhere($filter);
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
		
		<th nowrap class="custom-grid th-first">&nbsp;#</th>
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>   
        <th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CampaignName');" title="Order ASC/DESC">Campaign Name</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CampaignEndDate');" title="Order ASC/DESC">Expiration Date</span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;Data Size</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Status Active</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<td class="content-first"><input type="checkbox" name="check_list_cmp" id="check_list_cmp" value="<?php echo $row->CampaignNumber; ?>"></td>
				<td class="content-middle"><?php echo $no; ?></td>
				<td class="content-middle"><?php echo $row -> CampaignName; ?></td>
				<td class="content-middle"><?php echo $db->Date->indonesia($row -> CampaignEndDate); ?></td>
				<td class="content-lasted" style="text-align:center;padding-right:8px;"><?php echo getSizeOffData( $row->CampaignId); ?></td>
				<td class="content-lasted"><?php echo $row -> CmpStatus; ?></td>
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>



