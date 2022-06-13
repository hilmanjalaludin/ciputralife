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
 $ListPages -> setPage(15);
 
/** set sql settup **/
  $sql = "SELECT a.CampaignTypeStatus, a.CampaignTypeId, a.CampaignTypeCode as code, a.CampaignTypeDesc as des 
		  FROM t_lk_campaigntype a";
  
/** set query string **/
  $ListPages -> query($sql);
  
/** set filter **/
$filter = '';
 if( $db -> havepost('keywords'))
 {
	$filter.= " AND 
				(
					a.CampaignTypeCode LIKE '%".$_REQUEST['keywords']."%' OR  
					a.CampaignTypeDesc LIKE '%".$_REQUEST['keywords']."%'
				) ";
 }
 
 $ListPages -> setWhere($filter);
 $ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
 $ListPages -> setLimit();
 $ListPages -> result();
	// echo $ListPages -> query;
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
		<th class="custom-grid th-middle" width="8%"  align="center">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CampaignTypeCode');" title="Order ASC/DESC">Campaign Type Code</span></th>		
		<th class="custom-grid th-middle" width="12%" align="left">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CampaignTypeDesc');" title="Order ASC/DESC">Campaign Type Desc</span></th>    
		<th class="custom-grid th-lasted" width="20%" align="left" nowrap>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CampaignTypeStatus');" title="Order ASC/DESC">Status</span></th>
		
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
				<td class="content-first" ><?php $jpForm -> jpCheck('CampaignTypeId',NULL,$row ->CampaignTypeId, NULL, NULL,0);?></td>
				<td class="content-middle" align="center"><?php echo ($row -> code?$row -> code:'-'); ?></td>
				<td class="content-middle" ><?php echo ($row -> des?$row -> des:'-');?></td>
				<td class="content-lasted" align="justify" nowrap><?php echo ($row -> CampaignTypeStatus ?'Active':'Not Active');?></td>
			</tr>	
</tbody>
	<?php
		};
	?>
</table>