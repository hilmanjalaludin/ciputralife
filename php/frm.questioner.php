<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

class Questioner extends mysql
{
	var $_url; 
	var $_tem;
	private static $InsuredId;
	private static $Instance;
	
	function Questioner()
	{
		parent::__construct();
		$this -> _url  =& application::get_instance(); /// Application();
		$this -> _tem  =& Themes::get_instance();  // Themes
		
		// if( is_null(self::$InsuredId) ) 
		// {
			// self::$InsuredId = base64_decode( $this -> escPost('InsuredId') );
		// }		
		self::index();
		// echo "coba cons";
	}
	
	function index()
	{
		// echo "coba index";
		self::head();
		self::body();
	}
	function get_product()
	{
		$data = array();
		$sql = "	SELECT a.ProductId,a.ProductName FROM t_gn_product a 
					LEFT JOIN t_gn_questioner b ON a.ProductId = b.product_id
					WHERE a.ProductStatusFlag = 1";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ) {
			$data[$rows['ProductId']]= $rows['ProductName'];
		}	
		return $data;
	}
	function get_questioner_type()
	{
		$data = array();
		$sql = "SELECT * FROM t_lk_questioner_type a WHERE a.quest_flag =1";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ) {
			$data[$rows['quest_type_id']]= $rows['quest_type_desc'];
		}	
		return $data;
	}
	
	function head()
	{
		?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta content="utf-8" http-equiv="encoding">
			<title>Questioner</title>
			<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/policy.screen.css?time=<?php echo time();?>" />
			<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css?time=<?php echo time();?>" />	
			<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js?time=<?php echo time();?>"></script>    
			<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js?time=<?php echo time();?>"></script>
			<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js?time=<?php echo time();?>"></script>
			<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/EUI_1.0.2.js?time=<?php echo time();?>"></script>
			<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.Questioner_dep.js?time=<?php echo time();?>"></script>
			</head>
		<?php
	}
	
	function body()
	{
		?>
		<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
			<table border="0" width="100%" cellpadding="5px">	
				<tr><td><?php self::header(); ?></td></tr>
				<tr><td><?php self::content(); ?></td></tr>	
				<!-- start : layout footer -->
				<tr><td><?php self::footer();?></td></tr>	
			</table>
		</body>
		</html>
		<?php 
	}
	
	function header()
	{
	?>
		<fieldset class="corner" style="background:url('../gambar/pager_bg.png') left top; width:70%;">
			<legend class="icon-product"> &nbsp;&nbsp;&nbsp;</legend>
			<table cellpadding="5px" width="100%" align="left" border="0">
				<tr>
					<td class="header_table">Product</td>
					<td><?php $this -> DBForm -> jpCombo("Product","select long",$this->get_product());?></td>
					<td class="header_table">Question type</td>
					<td><?php $this -> DBForm -> jpCombo("Quest_type","select long",$this->get_questioner_type());?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td align="left"><button type="button" onclick="Ext.DOM.AddQuestion();">Add Question</button> </td>
					<td class="header_table">Question Desc</td>
					<td><?php echo $this -> DBForm -> RTInput('Question_decs','input long','','','','',200);?></td>
				</tr>
			</table>
		</fieldset>
	 <?php
	}
	
	function content()
	{
		?>
		<div id="container_question">
			<form name="form_setup_questioner">
			<input type="hidden" id="count_pertanyaan" name="count_pertanyaan" value="0">
			<ul id="pertanyaan">
			</ul>
			</form>
		</div>
		<?php
	}
	function footer()
	{ ?>
		<table cellpadding="5px" width="100%" align="center">
		<tr>
			<td  align="center"><button type="button" onclick="Ext.DOM.SaveSetupQuestioner();">Save Questioner</button> </td>
		</tr>
		</table>
	  <?php
	}
	public static function &get_instance() 
	{	
		if(is_null(self::$Instance)) {
			self::$Instance = new self();
		}

		return self::$Instance;
	}
}
$Questioner = new Questioner();
?>