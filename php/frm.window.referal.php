<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

/**
 ** window open for new add referal
 ** every customer with other 
 ** other status
 **/
	
class W_Referal extends mysql
{
 
 var $_url;
 var $_tem;
 
 /** 
  ** aksesor 
  ** on the main content
  **/
  
 var $_CustomerId;
 var $_CampaignId;
 
 function W_Referal()
 {
	parent::__construct();
	
	$this -> _url = new application();
	$this -> _tem = new Themes();
	$this -> _CampaignId = $this -> escPost('CampaignId');
	$this -> _CustomerId = $this -> escPost('CustomerId');
	//print_r(self::getRefaral());
	self::_W_Main();
 }

  function getRefaral(){
	$sql = " select * from t_gn_referal a where a.ReferalCustomerId='{$this ->_CustomerId}' and a.ReferalQAStatus is null";
	$qry = $this -> query($sql);
	$i=0;
	foreach($qry -> result_assoc() as $rows )
	{
		$data[$i]['data']['name']=$rows['ReferalName']; 
		$data[$i]['data']['phone1']=$rows['ReferalPhone1']; 
		$data[$i]['data']['phone2']=$rows['ReferalPhone2']; 
		$data[$i]['data']['phone3']=$rows['ReferalPhone3']; 
		$data[$i]['data']['dob']=$rows['ReferalDOB']; 
		$data[$i]['data']['relasi']=$rows['ReferalRelasi']; 
		$data[$i]['data']['address']=$rows['ReferalAddress']; 
		$i++;
	}
		
	return $data;
 }
/**
 **
 **/
 
function  _W_Main()
{
	
	
	self::_W_Header();
	self::_W_Content();
	self::_W_Footer();
}

/**
 **
 **/

function  _W_Header(){
?>		
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <!-- start Link : css --> 
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta content="utf-8" http-equiv="encoding">
	<title><?php echo $this -> _tem -> V_WEB_TITLE; ?> :: Add Referal </title>
	<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/gaya_utama.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/other.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css" />	
	<link type="text/css" rel="stylesheet" media="all" href="<?php echo $this -> _url -> basePath();?>gaya/chat.css" />
	<link type="text/css" rel="stylesheet" media="all" href="<?php echo $this -> _url -> basePath();?>gaya/screen.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/custom.css" />
	<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>    
	<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js"></script>
	<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/javaclass.js?time=<?php echo time();?>"></script>
	<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.WindowReferal.js?time=<?php echo time();?>"></script>
	<script>
	var _json_data = <?php echo json_encode($this -> getRefaral());?>;	
	var add_editcontent_rows = function(){
		
		try{ 
			if(_json_data){
				var z = 0
				for(var i in _json_data){
					Ext.Table("table_referal").addRows(i);
					Ext.Cmp('CustomerName_'+i).setValue(_json_data[i].data.name);
					Ext.Cmp('PhoneNumber1_'+i).setValue(_json_data[i].data.phone1);
					Ext.Cmp('PhoneNumber2_'+i).setValue(_json_data[i].data.phone2);
					Ext.Cmp('PhoneNumber3_'+i).setValue(_json_data[i].data.phone3);
					Ext.Cmp('DOB_'+i).setValue(_json_data[i].data.dob);
					Ext.Cmp('Relasi_'+i).setValue(_json_data[i].data.relasi);
					Ext.Cmp('CustomerAddress_'+i).setValue(_json_data[i].data.address);
					doJava.dom('chk_rows'+i).checked = false;
					doJava.dom('chk_rows'+i).disabled= true;
					doJava.dom('CustomerName_'+i).disabled=true;
					doJava.dom('PhoneNumber1_'+i).disabled=true;
					doJava.dom('PhoneNumber2_'+i).disabled=true;
					doJava.dom('PhoneNumber3_'+i).disabled=true;
					doJava.dom('DOB_'+i).disabled=true;
					doJava.dom('Relasi_'+i).disabled=true;
					doJava.dom('CustomerAddress_'+i).disabled=true;
					z++;
				}	
			}
			else{
				add_content_rows();
			}
						
		}	
		catch(e){ alert(e); }
	}
	</script>
	<style>
		#page_info_header { color:#3c3c36;background-color:#eeeeee;height:20px;padding:3px;font-size:14px;font-weight:bold;border-bottom:2px solid #dddfff;margin-bottom:4px;}
		table td{ font-size:11px; text-align:left;}
		table p{font-size:12px;color:blue;}
		.header-text{font-family:Arial;font-size:12px;text-align:right;color:#B00000;}	
		table td .input{ border:1px solid #b4d2d4;width:100px;height:20px;background-image:url('../gambar/input_bg.png');}
		table td .input_box{ border:1px solid #b4d2d4;background-color:#f4f5e6;width:40px;height:20px;}
		table td .input:hover{ border:1px solid red;background-color:#f9fae3;}
		table td .textarea{ border:1px solid #b4d2d4;background-color:#fffccc;width:200px;}
		table td .textarea:hover{ border:1px solid red;background-color:#f9fae3;}
	</style>
	</head>
	<body onload="add_editcontent_rows();">
<?php
}

/**
 **
 **/

function  _W_Content(){
?>
	
	<div style="border-bottom:1px solid #dddddd;padding:4px;height:450px;overflow:auto;">
		<fieldset style="border:1px solid #dddddd;">
			<legend> <h2>Add Referal </h2></legend>
			<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo $this -> _CustomerId;?>">
			<form name="frm_add_referal">
				<table border=0 width="90%" id="table_referal"></table>
				
			</form>
		</fieldset>	
	</div>
	<div style="padding-left:2px;border:0px solid #dddddd;margin-top:30px;height:70px;">
		<input type="button" name="add_rows" value="ADD" onclick="add_content_rows();">
		<!-- <input type="button" name="add_rows" value="REMOVE" onclick="remove_content_rows();"> -->
		<input type="button" name="add_rows" value="SAVE" onclick="saveReferal();">
	</div>			
<?php
}
	

/**
 **
 **/
 
function  _W_Footer(){ ?>
</body>
</HTML>
<?php
}	
	
}

new W_Referal();

?>