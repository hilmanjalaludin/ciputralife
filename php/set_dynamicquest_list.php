<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.list.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
require(dirname(__FILE__)."/../class/lib.form.php");

$ListPages -> pages = $db -> escPost('v_page');
$ListPages -> setPage(15);

$sql = "SELECT a.questioner_id, a.product_id ,c.quest_type_desc,a.questioner_desc, b.ProductName, IF(a.questioner_flag=1,'Active','Not Active') AS flag_quest,
		DATE_FORMAT(a.questioner_createts,'%d-%m-%Y') AS quest_create
		FROM t_gn_questioner a 
		INNER JOIN t_gn_product b ON a.product_id=b.ProductId
		INNER JOIN t_lk_questioner_type c ON a.questioner_type = c.quest_type_id";

$filter = '';
if( $db->havepost('product_filter')){
	$filter =" AND b.ProductId = ".$db->escPost('product_filter');
}

$ListPages -> query($sql);
$ListPages -> setWhere($filter);
$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
// $ListPages -> GroupBy('a.CampaignId');
$ListPages -> setLimit();
$ListPages -> result();
//$ListPages -> echo_query();

?>
<style>
	.wraptext{color:#000;text-align:justify;font-size:11px;width:200px;line-height:18px;border:0px solid #000;padding:2px;overflow:auto;}
	.wraptext:hover{color:blue;}
	.bold{font-weight:bold;color:#434152;}
	.number{text-align:right;padding-right:3px;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first " width="5%">&nbsp;#</th>	
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<b style='color:#608ba9;'>No</b></th>			
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('b.ProductName');"><b style='color:#608ba9;'>Product Name</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('c.quest_type_desc');"><b style='color:#608ba9;'>Questioner Type</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.questioner_desc');"><b style='color:#608ba9;'>Questioner Description</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.questioner_flag');"><b style='color:#608ba9;'>Questioner Status</b></span></th>
		<th nowrap class="custom-grid th-lasted" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.questioner_createts');"><b style='color:#608ba9;'>Questioner Create</b></span></th>
		
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color; ?>">
				<td class="content-first" ><?php $jpForm -> jpCheck('questioner',NULL,$row ->questioner_id, NULL, NULL,0);?></td>
				<td class="content-middle" width='10'><?php  echo $no; ?></td>
				<td class="content-middle" style="color:green;font-weight:bold;"><?php echo $row -> ProductName; ?></td>
				<td class="content-middle" style="color:green;font-weight:bold;"><?php echo $row -> quest_type_desc; ?></td>
				<td class="content-middle" style="color:green;font-weight:bold;"><?php echo $row -> questioner_desc; ?></td>
				<td class="content-middle" style="color:green;font-weight:bold;"><?php echo $row -> flag_quest; ?></td>
				<td class="content-middle" style="color:green;font-weight:bold;"><?php echo $row -> quest_create; ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		}
	?>
</table>