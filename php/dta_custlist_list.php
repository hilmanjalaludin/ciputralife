<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	/** create object **/
	
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(15);
	
  /* create Sql Query for show list data **/
  
	$sql = " select a.CustomerId,
					a.CampaignId,
					a.CustomerNumber,
					a.CustomerFirstName,
					a.CustomerLastName,
					IF(a.CustomerCity is null,'-',a.CustomerCity) 
					as CustomerCity,
					a.CustomerUploadedTs,
					a.CustomerOfficeName,
					c.CampaignNumber
			FROM t_gn_customer a 
			INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId
			LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId";
			
	$ListPages -> query($sql);
	
	/* if found filter label **/	
	
	$filter = "";
		
		if( $db->getSession('handling_type')==1){
				
				$filter  = " AND b.AssignAdmin='".$db->getSession('UserId')."'
							 AND b.AssignMgr is null
							 AND b.AssignSpv is null
							 AND b.AssignSelerId is null ";
			}		 
			else if( $db->getSession('handling_type')==2){
				$filter  = " AND b.AssignAdmin is not null
							 AND b.AssignMgr='".$db->getSession('UserId')."'
							 AND b.AssignSpv is null
							 AND b.AssignSelerId is null ";
			}
			else if( $db->getSession('handling_type')==3){
				$filter  = " AND b.AssignAdmin is not null
							 AND b.AssignMgr is not null
							 AND b.AssignSpv ='".$db->getSession('UserId')."'
							 AND b.AssignSelerId is null ";
			}
			
		if( $db->havepost('campaignId')):
			$filter.=" AND a.CampaignId='".$db->escPost('campaignId')."' ";
		endif;
		
	$ListPages -> setWhere($filter);
		
 /** set of limit string **/
	
	$ListPages -> setLimit();
	$ListPages -> result();
	
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_cust_data');">#</a></th>	
		<th nowrap class="custom-grid th-middle" width="4%">&nbsp;No.</th>	
		<th nowrap class="custom-grid th-middle" width="7%">&nbsp;Campaign Id.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Number.</th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Customer Name.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer City.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Upload Date.</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> CustomerId; ?>" name="chk_cust_data" name="chk_cust_data"></td>
				<td class="content-middle"><?php echo $no; ?></td>	
				<td class="content-middle"><?php echo $row -> CampaignNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerCity; ?></td>
				<td class="content-lasted"><?php echo $row -> CustomerUploadedTs; ?></td>
				
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>



