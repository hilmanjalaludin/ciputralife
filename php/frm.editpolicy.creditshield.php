<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../class/class.getfunction.php");
	require(dirname(__FILE__)."/../sisipan/parameters.php");
	require(dirname(__FILE__)."/../class/lib.form.php");
	
	
	$Customers = array();
	
	$sql = " select * from t_gn_customer a 
				left join t_gn_policyautogen b on a.CustomerId=b.CustomerId
				left join t_gn_policy c on b.PolicyNumber=c.PolicyNumber 
				left join t_gn_insured d on c.PolicyId=d.PolicyId
				left join tms_agent e on a.SellerId=e.UserId
				where a.CustomerId='".$_REQUEST['customerid']."' ";
				
				
	$qry = $db -> execute($sql,__FILE__,__LINE__);
	if( $qry && ( $rows = $db -> fetchassoc($qry) ) )
	{
		$Customers = $rows;
	}
	
	//print_r($Customers);
	
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content="Microsoft Excel 12">
<link rel=File-List href="Book1_files/filelist.xml">

<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>    
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script>

	var CustomerId = '<?php echo $_REQUEST['customerid'];?>';
	var CampaignId = '<?php echo $_REQUEST['campaignid'];?>';
</script>

<style>
	div#pages{
		width:750px;
		border:1px solid #ddd;
		margin: 2px 2px 2px 2px;
		padding: 5px 5px 5px 5px;
	}
	
	table.form td{
		font-family:Trebuchet MS;
		padding-left:4px;	
	}
	
	.header{
		font-family:Trebuchet MS;
		padding-left:4px;
		font-size:12px;	
		color:red;
		font-weight:bold;
	}
	.inputtext{
		border:1px solid #FF4321;
		height:23px;
		font-size:11px;
		background-color:#FFFCCC;
		width:190px;
	}
</style>
</head>

<body>
	<div id="pages">
		<table border=0 cellpadding=4 cellspacing=0 width="90%" align="center" class="form">
			<tr>
				<td colspan=4 style='font-size:12px;height:27px;color:#1a427d;background-color:#FFFEEE;font-weight:bold;border-bottom:1px solid #ddd;'>REGISTER CUSTOMER</td>
			</tr>
			<tr>
				<td colspan=4>&nbsp;</td>
			</tr>
			
			<tr height=20>
			  <td class="header" >Customer Number </td>
			  <td class="content"><?php echo $jpForm-> jpField('customer_name','inputtext',$Customers['CustomerNumber'],NULL,1);?></td>
			  <td class="header">Closing TM</td>
			  <td class="content"><?php echo $jpForm-> jpField('customer_dob','inputtext',$Customers['full_name'],NULL,1);?></td>
			</tr>
			
			<tr height=20>
			  <td class="header" >Closing ID</td>
			  <td class="content"><?php echo $jpForm-> jpField('customer_name','inputtext',$Customers['PolicyNumber'],NULL,1);?></td>
			  <td class="header">Closing Date</td>
			  <td class="content"><?php echo $jpForm-> jpField('customer_dob','inputtext',$db -> Date->indonesia($Customers['PolicySalesDate']),NULL,1);?></span></td>
			</tr>
			
			<tr height=20 >
			  <td class="header" >Customer Name</td>
			  <td class="content"><?php echo $jpForm-> jpField('customer_name','inputtext',$Customers['CustomerFirstName'],NULL,1);?></td>
			  <td class="header">Customer DOB</td>
			  <td class="content"><?php echo $jpForm-> jpField('customer_dob','inputtext',$db -> Date->indonesia($Customers['CustomerDOB']),NULL,1,10);?></td>
			</tr>
			<!--
			<tr height=20>
			  <td class="header" >Customer Name</td>
			  <td class="content"><?php echo $jpForm-> jpField('customer_name','inputtext',$getFunction->CustData->CustomerFirstName,NULL,1);?></td>
			  <td class="header">Customer DOB</td>
			  <td class="content"><?php echo $jpForm-> jpField('customer_dob','inputtext',NULL,'onkeyup="verifikasiDOB(this);";',0);?></td>
			</tr>
			-->
			<tr>
				<td colspan=4 style="border-bottom:1px solid #eee;">&nbsp;</td>
			</tr>
			
		</table>
	</div>
</body>
</html>