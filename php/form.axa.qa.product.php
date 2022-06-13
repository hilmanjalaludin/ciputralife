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
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.QA.AxaProduct_dep.js?time=<?php echo time();?>"></script>

</head>
<body>
 <?php }
 
function AXA_Transaction()
{ ?>
	<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo self::_getCustomerId(); ?>"/>
	<input type="hidden" name="CampaignId" id="CampaignId" value="<?php echo self::_getCampaignId(); ?>"/>
	<fieldset class="corner" style="margin-left:-5px;">
		<legend class="icon-application ">&nbsp;&nbsp;<b> Transaction </b></legend>
		<span id="Transaction"></span>
	</fieldset>
	</body>
	</html>	
<?php  
}
  

 }
 
 new AXA_Product();
 
 
?>

