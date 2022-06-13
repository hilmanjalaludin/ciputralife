<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

 
/*
||/\/\/\/\/\/\/\/\/-----------------------------------------------
||/\/\/\/\/\/\/\/\/-----------------------------------------------
*/
 
/* 
 * @ package 	: clas AXA_Product
 * 
 * @ params		: extends mysql
 * @ render		: object
 */
 
 // NOTES : js diganti dulu sama js/Ext.AxaProduct_dep.js (abie)
 
class AXA_Product extends mysql
{

  var $_url; 
  var $_tem;
  var $_data;	
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

private function _get_data_customer()
{
	$datas = $this -> Customer -> DataPolicy( $this -> escPost('customerid') ); // data customer 
	if( !is_array($datas) ) return null;
	else
	{
		return $datas['Customer'];
	}
} 


/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

 private function _getCampaignId()
 {
	$_conds = 0;
	if($this -> havepost('campaignid')){
		$_conds = (int)$this -> escPost('campaignid');
	}
	
	return $_conds;
 }

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

 private function _getCustomerId()
 {
	$_conds = 0;
	if($this -> havepost('customerid')){
		$_conds = (int)$this -> escPost('customerid');
	}
	
	return $_conds;
 }
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

 
  
public function AXA_Product()
{
	parent::__construct();
	
	$this -> _url  =& application::get_instance(); /// Application();
	$this -> _tem  =& Themes::get_instance();  // Themes
	$this -> _data =& self::_get_data_customer(); // customer;
	
	if(class_exists('Themes')) 
	{
		self::AXA_Header();
		//self::AXA_Body();
		self::AXA_Transaction();
	}
 }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
  
function _get_policy_number()
{
	$_CustomerId = $this -> escPost('customerid');
	
	$_conds = array();
	$_conds['new'] = 'New Policy';
	$sql = " select a.PolicyNumber, a.PolicyNumber 
				from t_gn_policy a 
				left join t_gn_insured b on a.PolicyId=b.InsuredId
				left join t_gn_policyautogen c on a.PolicyNumber=c.PolicyNumber
				where c.CustomerId='$_CustomerId'";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows ){
		$_conds[$rows['PolicyNumber']] = $rows['PolicyNumber'];
	}
	
	return $_conds;
}

 
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function _get_member_of()
{
	return array
	(
		'1'=>'Self', /// dependent berdiri sendiri tidak terikat 
		'2'=>'Holder', // terikat dgn holder 
		'3'=>'Spouse' // terikat dgn spouse
	);
}	

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
 public function AXA_Header()
 { 
   ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="utf-8" http-equiv="encoding">
<title>Show Policy </title>
<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/policy.screen.css?time=<?php echo time();?>" />
<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css?time=<?php echo time();?>" />	
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js?time=<?php echo time();?>"></script>    
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js?time=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js?time=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/EUI_1.0.2_dep.js?time=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.AxaProduct_dep.js?time=<?php echo time();?>"></script>

</head>
 <?php }
 
 function get_size_policy()
 {
	$_conds = array();
	$sql = " SELECT b.PolicyNumber, UCASE(( SELECT d.InsuredFirstName FROM t_gn_insured d WHERE d.PolicyId = b.PolicyId ORDER BY d.InsuredId ASC  LIMIT 1 )) as NamaPemegangPolicy FROM t_gn_policyautogen a 
			 INNER JOIN t_gn_policy b on a.PolicyNumber=b.PolicyNumber
		     WHERE a.CustomerId = '".$this -> escPost('customerid')."'
		     GROUP BY b.PolicyNumber ";
	// echo $sql;
	$qry = $this -> query($sql);
	
	$i=0;
	foreach($qry -> result_assoc() as $rows ) 
	{
		$_conds[$i]['PolicyNumber']= $rows['PolicyNumber'];
		$_conds[$i]['NamaPemegangPolicy']= $rows['NamaPemegangPolicy'];	
		$i++;	
	}
	return $_conds;	
 }
 
 function get_size_insured($PolicyNumber)
 {
	$_conds = array();
	
	$sql = "SELECT b.InsuredId, e.PremiumGroupDesc,a.Premi,
			d.PayMode, c.ProductPlanName, h.PlanName, h.PlanNameAlias,
			c.ProductPlanPremium, b.InsuredFirstName, b.InsuredDOB, b.InsuredAge, b.PremiumGroupId,
			f.ProductCode, f.ProductName, g.StatusQCcode, b.QCStatus
			FROM t_gn_policy a 
			inner join t_gn_insured b on a.PolicyId=b.PolicyId
			LEFT JOIN t_gn_productplan c on a.ProductPlanId=c.ProductPlanId
			LEFT JOIN t_lk_paymode d on c.PayModeId=d.PayModeId
			LEFT JOIN t_lk_premiumgroup e on c.PremiumGroupId=e.PremiumGroupId
			left join t_gn_product f on c.ProductId = f.ProductId
			left join t_lk_qcstatus g on b.QCStatus = g.StatusQCid
			left join t_lk_plan_name h on c.ProductPlan = h.PlanSection
			WHERE a.PolicyNumber='$PolicyNumber'";
	
	$qry = $this -> query($sql);
	$i=0;
	foreach($qry -> result_assoc() as $rows ) 
	{
		$_conds[$i]['InsuredId']= $rows['InsuredId'];
		$_conds[$i]['PremiumGroupDesc']= $rows['PremiumGroupDesc'];	
		$_conds[$i]['PayMode']= $rows['PayMode'];
		$_conds[$i]['ProductPlanName']= $rows['PlanNameAlias'];
		$_conds[$i]['Premi']= $rows['Premi'];
		$_conds[$i]['InsuredFirstName']= $rows['InsuredFirstName'];
		$_conds[$i]['InsuredDOB']= $rows['InsuredDOB'];
		$_conds[$i]['InsuredAge']= $rows['InsuredAge'];
		$_conds[$i]['PremiumGroupId']= $rows['PremiumGroupId'];
		$_conds[$i]['ProductName']= $rows['ProductCode'].' - '.$rows['ProductName'];
		$_conds[$i]['StatusQCcode']= $rows['StatusQCcode'];
		$_conds[$i]['QCStatus']= $rows['QCStatus'];
		$i++;	
	}
	return $_conds;	
 } 
 
 function getStyle($id)
  {
	switch($id)
	{
		case 1 : $value = "style='font-weight:bold;color:#009900;'"; break;
		case 2 : $value = "style='font-weight:bold;color:#FFCC00;'"; break;
		case 3 : $value = "style='font-weight:bold;color:#FF0000;'"; break;
		case 4 : $value = "style='font-weight:bold;color:#FF0000;'"; break;
		
		default : $value = ""; break;
	}
	
	return $value;
  }
 
function AXA_Transaction()
{ ?>
	<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo self::_getCustomerId(); ?>"/>
	<fieldset class="corner" style="margin-left:-5px;">
		<legend class="icon-application ">&nbsp;&nbsp;<b> Transaction View Only</b></legend>
	<?php
		$GrandTotals = 0;
		$GrandDiscount = 0;
	
	echo "<table border=\"0\" align=\"center\" width=\"99%\" cellpadding=\"2\" cellspacing=\"0\">
				<tr>
					<td class=\"header-first\" align=\"center\">#</td>
					<td class=\"header-first\">Group Premi</td>
					<td class=\"header-first\">First Name </td>
					<td class=\"header-first\">DOB</td>
					<td class=\"header-first\">Age</td>
					<td class=\"header-first\">Plan</td>
					<td class=\"header-first\">Product</td>
					<td class=\"header-first\">Payment Type</td>
					<td class=\"header-first\">Premi</td>
					<td class=\"header-last\">Status</td>
				</tr> ";
				
	foreach( self::get_size_policy() as $rows )
	{
		echo "  <tr>
					<td class=\"rows-first\" style=\"background-color:#FFFCCC;height:24px;padding-left:4px;font-weight:bold;color:green;font-size:12px;\" align=\"left\" colspan=\"10\">". $rows['PolicyNumber']. "  / " . $rows['NamaPemegangPolicy'] ." </td>
				</tr>";
							
				
			/** calculation evry Number policy **/
			
				$SubTotals = 0;  $dis=0; $discount = 0;
				foreach( self::get_size_insured($rows['PolicyNumber']) as $rows )
				{
					echo "  <tr>
								<td class=\"rows-first\" align=\"center\"><input type=\"checkbox\" value=\"{$rows['InsuredId']}\" onclick=\"InsuredWindowCustom(this)\"></td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['PremiumGroupDesc'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['InsuredFirstName'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['InsuredDOB'])."</td>
								<td class=\"rows-first\" align=\"center\">".strtoupper($rows['InsuredAge'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['ProductPlanName'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['ProductName'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['PayMode'])."</td>
								<td class=\"rows-first\" align=\"right\">".formatRupiah($rows['Premi'])."</td>
								<td class=\"rows-last\" ".$this->getStyle($rows['QCStatus'])." align=\"right\">".($rows['StatusQCcode']?strtoupper($rows['StatusQCcode']):'-')."</td>
							</tr>";
							
					$SubTotals+= (int)$rows['Premi']; 	
					$dis++;	
				} 
				
		$discount = ( $dis > 1 ? ($SubTotals*10/100):0 );
		$adiscount = $SubTotals-( $dis > 1 ? ($SubTotals*10/100):0 );
		$GrandDiscount+= $discount;
		$GrandaDiscount+= $adiscount;
		$GrandTotals+= $SubTotals;
		
		
		echo "  <tr>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"2\"><b style=\"color:red;\"> * Discount</b></td>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"right\">".formatRupiah($discount)."</td>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"4\"></td>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"left\"><b style=\"color:red;\"> Sub Total</b></td>
					<td class=\"rows-last\"  style=\"border-top:1px solid red;\" align=\"right\" colspan=\"2\">".formatRupiah($SubTotals)."</td>
				</tr>";	
		echo "  <tr>
				<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"2\"><b style=\"color:red;\"> After Discount</b></td>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"right\">".formatRupiah($adiscount)."</td>
				</tr>";	
		
	}
	
	
	echo " <tr>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"2\"><b style=\"color:white;\"> Discount Total</b></td>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"right\">".formatRupiah($GrandDiscount)."</td>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"4\">&nbsp;</td>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"left\"><b style=\"color:white;\"> Grand Total</b></td>
				<td class=\"header-last\" style=\"border-top:1px solid red;\" align=\"right\" colspan=\"2\">".formatRupiah($GrandTotals)."</td>
			</tr>";

	echo " <tr>
			<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"2\"><b style=\"color:white;\"> After Discount Total</b></td>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"right\">".formatRupiah($GrandaDiscount)."</td>
			</tr>";	
				
	echo "</table><br>";
	echo "<span class=\"wrap\" style=\"font-size:11px;\"> <i> * ) Discount 10 % IF Insured > 1 , Payment Premi : ".formatRupiah( ($GrandTotals-$GrandDiscount) )." </i></span>";
	?></fieldset><?php
}

 }
 
 new AXA_Product();
 
 
?>

