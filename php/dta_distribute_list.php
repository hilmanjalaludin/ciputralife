<?php
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		
		
		$sql = "SELECT a.CampaignId, a.CampaignNumber, a.CampaignName FROM t_gn_campaign a 
				WHERE a.CampaignStatusFlag=1 ";
		$ListPages -> setPage(15);
		$ListPages -> query($sql);
		$ListPages -> setLimit();
		$ListPages -> result();
	
	
 /* get totals data is available to distribution to Mgr **/
	
	function getAvailableData($campaignId='')
	{
		global $db;
		if( $db->getSession('handling_type')==8){	
			$sql = " SELECT count(b.CustomerId) as jumlah
						FROM t_gn_campaign a 
							inner join t_gn_customer b on a.CampaignId=b.CampaignId 
							inner join t_gn_assignment  c on b.CustomerId=c.CustomerId
							WHERE a.CampaignStatusFlag=1
							and c.AssignManager is Null
							and c.AssignMgr is null
							and c.AssignSpv is null
							and c.AssignSelerId is null
							and b.CallReasonId is null
							and a.CampaignId=".$campaignId."";
		}
		else if( $db->getSession('handling_type')==1 ) {
			$sql = " SELECT count(b.CustomerId) as jumlah
						FROM t_gn_campaign a 
							inner join t_gn_customer b on a.CampaignId=b.CampaignId 
							inner join t_gn_assignment  c on b.CustomerId=c.CustomerId
							WHERE a.CampaignStatusFlag=1
							and c.AssignManager ='".$db->getSession('UserId')."'
							and c.AssignMgr is null
							and c.AssignSpv is null
							and c.AssignSelerId is null
							and b.CallReasonId is null
							and a.CampaignId=".$campaignId."";
		}
		else if( $db->getSession('handling_type')==2 ) {
			$sql = " SELECT count(b.CustomerId) as jumlah
						FROM t_gn_campaign a 
							inner join t_gn_customer b on a.CampaignId=b.CampaignId 
							inner join t_gn_assignment  c on b.CustomerId=c.CustomerId
							WHERE a.CampaignStatusFlag=1
							and c.AssignMgr = '".$db->getSession('UserId')."'
							and c.AssignSpv is null
							and c.AssignSelerId is null
							and b.CallReasonId is null
							and a.CampaignId=".$campaignId."";
		}
		else if( $db->getSession('handling_type')==3 ) {
			$sql = " SELECT count(b.CustomerId) as jumlah
						FROM t_gn_campaign a 
							inner join t_gn_customer b on a.CampaignId=b.CampaignId 
							inner join t_gn_assignment  c on b.CustomerId=c.CustomerId
							WHERE a.CampaignStatusFlag=1
							and c.AssignMgr is not null
							and c.AssignSpv = '".$db->getSession('UserId')."'
							and c.AssignSelerId is null
							and b.CallReasonId is null
							and a.CampaignId=".$campaignId."";
		}
		else{
		$sql = " SELECT count(b.CustomerId) as jumlah
						FROM t_gn_campaign a 
							inner join t_gn_customer b on a.CampaignId=b.CampaignId 
							inner join t_gn_assignment  c on b.CustomerId=c.CustomerId
							WHERE a.CampaignStatusFlag=1
							and c.AssignMgr is null
							and c.AssignSpv is null
							and c.AssignSelerId is null
							and b.CallReasonId is null
							and a.CampaignId=".$campaignId."";
		
		}
		
		
		$jumlah = $db -> valueSQL($sql);
		if( $jumlah >0 ) : return $jumlah;
		else : 
			return 0;
		endif;		
	}	
	
	SetNoCache();

?>

<table class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th width="2%"  nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_cmp');">#</a></th>		
		<th width="5%"  nowrap class="custom-grid th-middle">&nbsp;No</th>
		<th width="10%" nowrap class="custom-grid th-middle">&nbsp;Campaign ID.</th>     
		<th width="10%" nowrap class="custom-grid th-middle">&nbsp;Campaign Name.</th> 	
		<th width="20%" nowrap class="custom-grid th-lasted">&nbsp;Size Data.</th>        
        
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> CampaignNumber; ?>" di="chk_cmp" name="chk_cmp"></td>
				<td class="content-middle"><?php echo $no; ?></td>
				<td class="content-middle"><?php echo $row -> CampaignNumber;?></td>
				<td class="content-middle"><b style="color:green;"><?php echo $row -> CampaignName;?></b></td>
				<td class="content-lasted" align="center"><?php echo getAvailableData($row->CampaignId); ?></td>
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>