<?php
require_once(dirname(__FILE__)."/../sisipan/sessions.php");
require_once(dirname(__FILE__)."/../fungsi/global.php");
require_once(dirname(__FILE__)."/../class/MYSQLConnect.php");
require_once(dirname(__FILE__)."/../class/class.list.table.php");
require_once(dirname(__FILE__)."/../class/class.application.php");
require_once(dirname(__FILE__)."/../sisipan/parameters.php");
require_once(dirname(__FILE__)."/../class/lib.form.php");

/** set page navigator */
	
	$sql = "select *
			from tms_tempalate_upload a ";
	
/** settup by class object ***/
	
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);
	$ListPages -> query($sql);
	$ListPages -> setWhere();
	$ListPages -> OrderBy('TemplateCreateTs','DESC');
	$ListPages -> setLimit();
	
/** test factory **/

	$query = $db -> query( $ListPages ->getSQL() );
?>
<style>
	.wraptext{color:#000;font-size:11px;padding:3px;width:200px;line-height:18px;}
	.wraptext:hover{color:blue;}
	.bold{font-weight:bold;color:#434152;}
	.number{text-align:right;padding-right:3px;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first bold" width="5%">&nbsp;#</th>	
		<th class="custom-grid th-middle bold" width="5%" align="center">&nbsp;No</th>	
		<th class="custom-grid th-middle bold" width="8%" align="center">&nbsp;Table Name</th>		
		<th class="custom-grid th-middle bold" width="15%">&nbsp;Tempalate Name.</th>    
		<th class="custom-grid th-middle bold" width="15%">&nbsp;Mode Query.</th>
		<th class="custom-grid th-middle bold" width="15%">&nbsp;File Type.</th>
		<th class="custom-grid th-middle bold" width="15%">&nbsp;Created Ts.</th>
		<th class="custom-grid th-lasted bold" width="15%">&nbsp;Status.</th>
		
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		foreach( $query -> result_assoc() as $rows )
		{
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color; ?>">
				<td class="content-first" ><?php $jpForm -> jpCheck('TemplateId',NULL,$rows['TemplateId'], NULL, NULL,0);?></td>
				<td class="content-middle" ><?php echo $no; ?></td>
				<td class="content-middle" ><b><?php echo ($rows['TemplateTableName']?$rows['TemplateTableName']:'-'); ?></b></td>
				<td class="content-middle" ><?php echo ($rows['TemplateName']?$rows['TemplateName']:'-');?></td>
				<td class="content-middle" ><?php echo ($rows['TemplateMode']?$rows['TemplateMode']:'-');?></td>
				<td class="content-middle" ><?php echo ($rows['TemplateFileType']?$rows['TemplateFileType']:'-');?></td>
				<td class="content-middle" ><?php echo ($rows['TemplateCreateTs']?$rows['TemplateCreateTs']:'-');?></td>
				<td class="content-lasted" align="justify"><?php echo ($rows['TemplateFlags']?'Active':'Not Active');?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>