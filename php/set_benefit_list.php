<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	SetNoCache();
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(15);
		
		$sql = "select 
					a.ProductPlanBenefitId,
					b.ProductCode, b.ProductName, a.ProductPlanBenefitDesc, 
					a.ProductPlanBenefit, d.ProductPlanName,
					e.ProductType,
					IF(a.ProductPlanBenefitStatusFlag=0, 'Not Active','Active')	as status
					
				from t_gn_productplanbenefit a 
				left join t_gn_product b on a.ProductId=b.ProductId 
				LEFT JOIN t_gn_campaignproduct f on b.ProductId=f.ProductId
				left join t_gn_campaign c on f.CampaignId=c.CampaignId
				left join t_gn_productplan d on a.ProductPlan=d.ProductPlan 
				left join t_lk_producttype e on b.ProductTypeId=e.ProductTypeId";
				
				
		
	/** harus berusritan **/
	
		$ListPages -> query($sql);
		$ListPages -> setWhere();
		$ListPages -> GroupBy("a.ProductPlanBenefitId");
		$ListPages -> OrderBy("a.ProductPlanBenefitId","DESC");
		$ListPages -> setLimit();
		$ListPages -> result();
	
	

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Product ID</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Product Name</th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Description</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Benefit</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Plan</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Status</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color; ?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> ProductPlanBenefitId; ?>" name="chk_benfit" id="chk_benfit"></td>
				<td class="content-middle"><?php echo $no; ?></td>	
				<td class="content-middle"><?php echo $row -> ProductCode; ?></td>
				<td class="content-middle"><?php echo $row -> ProductName; ?></td>
				<td class="content-middle"><?php echo $row -> ProductPlanBenefitDesc; ?></td>
				<td class="content-middle"><?php echo ($row -> ProductPlanBenefit?$row -> ProductPlanBenefit:'-'); ?></td>
				<td class="content-middle"><?php echo ($row -> ProductPlanName?$row -> ProductPlanName:'-'); ?></td>
				<td class="content-lasted"><?php echo $row->status;?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



