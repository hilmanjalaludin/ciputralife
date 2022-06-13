<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.list.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
require(dirname(__FILE__)."/../class/lib.form.php");

/** setup pages ****/
 
 $ListPages -> pages = $db -> escPost('v_page'); 
 $ListPages -> setPage(20);
 
/** set sql settup **/
  $sql = " SELECT * FROM t_lk_branch a ";
  
/** set query string **/
  $ListPages -> query($sql);
  
/** set filter **/
$filter = '';
 if( $db -> havepost('keywords'))
 {
	$filter.= " AND 
				(
					a.BranchCode LIKE '%".$_REQUEST['keywords']."%' OR  
					a.BranchName LIKE '%".$_REQUEST['keywords']."%' OR 
					a.BranchManager LIKE '%".$_REQUEST['keywords']."%' OR 
					a.BranchContact LIKE '%".$_REQUEST['keywords']."%' OR 
					a.BranchAddress LIKE '%".$_REQUEST['keywords']."%' OR 
					a.BranchEmail LIKE '%".$_REQUEST['keywords']."%' 
				) ";
 }
 
 $ListPages -> setWhere($filter);
 $ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
 $ListPages -> setLimit();
 $ListPages -> result();
	
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
		<th class="custom-grid th-middle" width="5%"  align="center">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.BranchId');" title="Order ASC/DESC">No</span></th>	
		<th class="custom-grid th-middle" width="8%"  align="center">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.BranchCode');" title="Order ASC/DESC">Branch Code</span></th>		
		<th class="custom-grid th-middle" width="12%" align="left">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.BranchName');" title="Order ASC/DESC">Branch Name.</span></th>    
		<th class="custom-grid th-middle" width="15%" align="left">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.BranchManager');" title="Order ASC/DESC">Branch Manager.</span></th>
		<th class="custom-grid th-middle" width="15%" align="left">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.BranchContact');" title="Order ASC/DESC">Branch Contact Phone.</span></th>
		<th class="custom-grid th-middle" width="15%" align="left">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.BranchName');" title="Order ASC/DESC">Branch Address.</span></th>
		<th class="custom-grid th-middle" width="15%" align="left">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.BranchEmail');" title="Order ASC/DESC">Branch Mail.</span></th>
		<th class="custom-grid th-lasted" width="20%" align="left" nowrap>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.BranchFlags');" title="Order ASC/DESC">Branch Status.</span></th>
		
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
				<td class="content-first" ><?php $jpForm -> jpCheck('BranchId',NULL,$row ->BranchId, NULL, NULL,0);?></td>
				<td class="content-middle" align="center"><?php echo $no; ?></td>
				<td class="content-middle" align="center"><?php echo ($row -> BranchCode?$row -> BranchCode:'-'); ?></td>
				<td class="content-middle" ><?php echo ($row -> BranchName?$row -> BranchName:'-');?></td>
				<td class="content-middle" ><?php echo ($row -> BranchManager?$row -> BranchManager:'-');?></td>
				<td class="content-middle" ><?php echo ($row -> BranchContact?$row -> BranchContact:'-');?></td>
				<td class="content-middle" ><div class="wraptext"><?php echo ($row -> BranchAddress?$row -> BranchAddress:'-');?></div></td>
				<td class="content-middle" ><?php echo ($row -> BranchEmail?$row -> BranchEmail:'-');?></td>
				<td class="content-lasted" align="justify" nowrap><?php echo ($row -> BranchFlags?'Active':'Not Active');?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>