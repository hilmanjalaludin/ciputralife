<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

define('UW',2); 
define('SURVEY',1);

class AxaEditQAProduct extends mysql
{
	var $_url; 
	var $_tem;
	private static $InsuredId;
	private static $CampaignId;
	private static $Instance;
	private $product_survey;
	function AxaEditQAProduct()
	{
		parent::__construct();
		$this -> _url  =& application::get_instance(); /// Application();
		$this -> _tem  =& Themes::get_instance();  // Themes
		
		if( is_null(self::$InsuredId) ) 
		{
			self::$InsuredId = base64_decode( $this -> escPost('InsuredId') );
			self::$CampaignId = base64_decode( $this -> escPost('CampaignId') );
		}		
		self::index();
	}
	
	function _Payers()
{
	$_conds = array();
	if(class_exists('Customer') ) 
	{
		$CustomerId = self::getData() -> result_get_value('CustomerId');
		if( !is_null($CustomerId))
		{
			$_data = $this -> Customer -> DataPolicy($CustomerId);
			if( isset($_data['Payers']) )
			{
				$_conds = $_data['Payers'];
			}	
		}	
	}	
	
	return $_conds;
}
	
function _PayersPH()
{
	$_conds = '';
	if(class_exists('Customer') ) 
	{
		$CustomerId = self::getData() -> result_get_value('CustomerId');
		if( !is_null($CustomerId))
		{
			$_data = $this -> Customer -> _PayerPH($CustomerId);
			if( isset($_data) ) {
				$_conds = $_data;
			}	
		}	
	}	
	
	return $_conds;
}

	function _Holder(){
		$_conds = array();
		if(class_exists('Customer') ) {
			$CustomerId = self::getData() -> result_get_value('CustomerId');
			if( !is_null($CustomerId)){
				$_data = $this -> Customer -> DataPolicy($CustomerId);
				if( isset($_data['CiputHolder']) ){
					$_conds = $_data['CiputHolder'];
				}
			}
		}
		
		return $_conds;
	}
	
private function _getCustomer()
{
	$_conds = array();
	if(class_exists('Customer') ) 
	{
		$CustomerId = self::getData() -> result_get_value('CustomerId');
		if( !is_null($CustomerId))
		{
			$_data = $this -> Customer -> DataPolicy($CustomerId);
			if( isset($_data['Customer']) )
			{
				$_conds = $_data['Customer'];
			}	
		}	
	}	
	
	return $_conds;
}
 
 
/*
 * @ def 		: _getProductName on by insured  
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */ 
 
function _getProductName()
{
	$_conds = array();
	$sql = " select c.ProductId, d.ProductName  from t_gn_insured a 
			inner join t_gn_policy b on a.PolicyId=b.PolicyId
			inner join t_gn_productplan c on b.ProductPlanId=c.ProductPlanId
			inner join t_gn_product d on c.ProductId=d.ProductId
			where a.InsuredId='".self::$InsuredId."'";
		// echo $sql;
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows ) {
		$_conds[$rows['ProductId']]= $rows['ProductName'];
	}	
return $_conds;	
} 


/*
 * @ def 		: getData detail all insured  
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
  
 function getData()
 {
	$sql = "SELECT a.*, d.*,b.Premi
			FROM t_gn_insured a
			LEFT JOIN t_gn_policy b ON a.PolicyId=b.PolicyId
			LEFT JOIN t_gn_policyautogen c ON c.PolicyNumber=b.PolicyNumber
			LEFT JOIN t_gn_productplan d ON b.ProductPlanId=d.ProductPlanId
			WHERE a.InsuredId = '".self::$InsuredId."'";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() ){
		return $qry;
	}
}
 

 /*
 * @ def 		: getBenef detail information  
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
  
 function getBenef() 
 {
	$_conds = array();
	
	$sql = "select * from t_gn_beneficiary a WHERE a.InsuredId = '".self::$InsuredId."'";
	$qry = $this -> query($sql);
	// echo $sql;
	$i = 1;
	foreach($qry -> result_assoc() as $rows ) {
		$_conds[$i] = $rows;
		$i++;
	}
	return $_conds;
 }

 function getBeneftotal() 
 {
	$_conds = array();
	// $CustomerId = self::getData() -> result_get_value('CustomerId');

	$sql = "select count(a.InsuredId) as Total 
			from t_gn_beneficiary a 
			inner join t_gn_insured ins on ins.InsuredId = a.InsuredId
			inner join t_gn_policy pc on pc.PolicyId = ins.PolicyId 
			where a.InsuredId = '".self::$InsuredId."'";
	$qry = $this -> query($sql);

	// echo $sql;
	foreach ($qry -> result_assoc() as $rows) {
		$_conds = $rows;
	}
	return $_conds;
 }
	
	function index()
	{
		self::head();
		self::body();
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
		<title>Edit Policy </title>
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/policy.screen.css?time=<?php echo time();?>" />
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.9.0/themes/base/jquery.ui.autocomplete.css?time=<?php echo time();?>" />
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.9.0/themes/base/jquery.ui.menu.css?time=<?php echo time();?>" />
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css?time=<?php echo time();?>" />	
		<!-- <script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js?time=<?php echo time();?>"></script> --> 
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.8.2.js?time=<?php echo time();?>"></script>

		<!-- <script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js?time=<?php echo time();?>"></script> -->
		<script src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.9.0/ui/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js?time=<?php echo time();?>"></script>
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/EUI_1.0.2.js?time=<?php echo time();?>"></script>
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/EUI_1.0.2_dep.js?time=<?php echo time();?>"></script>
		 <script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.AxaEditProduct_dep.js?time=<?php echo time();?>"></script>
		<script src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery.chained.min.js"></script>
		<script>
			function myFunction() {
			    var mylist = document.getElementById("kota");
			    document.getElementById("PayerCity").value = mylist.options[mylist.selectedIndex].text;
			}
		</script>
		</head>
	<?php
	}
	
	function body()
	{
		?>
		<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onunload="Ext.DOM.OnUnloadWindow()">
		<input type="hidden" id="InsuredId" name="InsuredId" value="<?php echo self::$InsuredId;?>">
			<table border=0 width="100%" align="center" cellpadding="5px">	
				<?php
				// if($this->getSession('handling_type') != 4)
				// {
					?><tr><td><?php self::header(); ?></td></tr><?php
				// }
				?>
				<tr><td><?php self::tabs(); ?></td></tr>	
				<!-- start : layout footer -->
				<tr><td><?php self::footer();?></td></tr>	
			</table>
		</body>
		</html>
		<?php 
	}
	
	function header()
	{
		$Insured= self::getData();
	?>
		<fieldset class="corner" style="background:url('../gambar/pager_bg.png') left top; width:15%;">
			<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Status Insured</legend>
			<table cellpadding="5px" width="10%" align="left">
				<tr>
					<td class="header_table">Status</td>
					<td><?php $this -> DBForm -> jpCombo("StatusInsured","select long",$this->Entity->getQCstatus(),($Insured->result_get_value('QCStatus')?$Insured->result_get_value('QCStatus'):null),null);?></td>
					<input type="hidden" name="QCStatus" id="QCStatus" value="<?php echo $Insured->result_get_value('QCStatus') ?>"/>
					<td><a href="javascript:void(0);" class="sbutton" onclick="javascript:Ext.DOM.UpdateStatusIns();" style="margin-left:-4px;
					"><span>&nbsp;Update</span></a></td>
				</tr>
			</table>
		</fieldset>
	 <?php
	}
	
	function tabs()
	{
		$Insured= self::getData();
		$produk = $Insured -> result_get_value('ProductId');
		$kategori = $this -> Customer ->getProductCategory();
	?>
	<!-- start : layout content -->	
	
		<div id="tabs" class="corener" style="margin:-10px;">
			<ul>
				<li><a href="#tabs-5" id="PAYER">PAYER AND ADDRESS INFO</a></li>
				<li><a href="#tabs-2" id="INSURED">INSURED</a></li>
				<li><a href="#tabs-3" id="HOLDER">HOLDER</a></li>
				<li><a href="#tabs-6" id="BENEFICIARY">EDIT BENEFICIARY</a></li>
				<li><a href="#tabs-7" id="BENEFICIARY">ADD BENEFICIARY</a></li>
				<li><a href="#tabs-9" id="SURVEY">SURVEY</a></li>
				<li><a href="#tabs-10" id="UNDERWRITING">UNDERWRITING</a></li>
				<li><a href="#tabs-11" id="PAYMENTINFO">PAYMENT INFO</a></li>
		<?php 	if( $kategori['category_name'][$produk] == "FPA") { 	?>		
				<li><a href="#tabs-12" id="SENDTOCORE">SEND PA TO CORE </a></li>
		<?php 	}	?>
				
			</ul>
			<div id="tabs-5"><?php self::AXA_Payers();?></div>
			<div id="tabs-2"><?php self::AXA_Insured();?></div>
			<div id="tabs-6"><?php self::AXA_Edit_Benefiecery();?></div>
			<div id="tabs-7"><?php self::AXA_Add_Benefiecery();?></div>
			<div id="tabs-3"><?php self::Ciputra_Holder();?></div>
			<div id="tabs-9"><?php self::AXA_Survey();?></div>
			<div id="tabs-10"><?php self::AXA_Underwriting();?></div>
			<div id="tabs-11"><?php self::AXA_Paymentinfo();?></div>
		<?php 	if( $kategori['category_name'][$produk] == "FPA") { 	?>	
			<div id="tabs-12"><?php self::AXA_Send2Core();?></div>
		<?php 	}	?>
		</div>
		
	<?php
	}
	
	function _get_ans_survey($questioner_type)
	{
		$CustomerId = self::getData() -> result_get_value('CustomerId');
		$data=array();
		if ( $questioner_type == 1 )
		{
			$insured = self::$InsuredId;
		}
		else
		{
			$insured = "0";
		}
		$sql = "SELECT a.question_id,b.prod_survey_id,b.answer_value FROM t_gn_ans_survey a 
				INNER JOIN t_gn_multians_survey b ON a.ans_survey_id=b.ans_survey_id
				WHERE a.customer_id = ". $CustomerId ." 
				AND a.product_id = ".$this->product_survey." 
				AND b.quest_have_ans = 1
				AND a.insured_id = ". $insured ."
				AND b.questioner_type = ".$questioner_type."
				ORDER BY a.question_id";
				
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ) 
		{
			
			$data[$rows['question_id']][$rows['prod_survey_id']]= $rows['answer_value'];
		}
		return $data;
	}
	
	function AXA_Send2Core()
	{
		$CustomerId = self::getData() -> result_get_value('CustomerId');
		$PAStat = $this -> Customer ->PAStatus($CustomerId);
		
		echo "<form name=\"form_edit_fpa\">";
		echo "	<fieldset class=\"corner\" style=\"margin-left:-5px;\">
					<legend class=\"icon-customers\">&nbsp;&nbsp;&nbsp;<b>Send PA to Core</b></legend>	";
		?>
		
		<table width="100%" align="left" cellpadding="5px" border="0px">
			<tr>
				<td class="header_table ">First Name</td>
				<td><?php $this -> DBForm -> jpInput('FPAFirstName','input long',self::_Payers() -> result_get_value('PayerFirstName'),null,false); ?></td>
				<td class="header_table ">Status di Core</td>
				<td><?php $this -> DBForm -> jpInput('pa_core_status','input long',( !empty($PAStat) ? 'Registered' : 'Not registered' ),null,true); ?></td>
			</tr>
			<tr>
				<td class="header_table ">Last Name</td>
				<td><?php $this -> DBForm -> jpInput('FPALastName','input long',self::_Payers() -> result_get_value('PayerLastName'),null,false); ?></td>
				<td class="header_table ">No Induk</td>
				<td><?php $this -> DBForm -> jpInput('pa_no_induk','input long',( !empty($PAStat) ? $PAStat['no_induk'] : '' ),null,true); ?></td>
			</tr>
			<tr>
				<td class="header_table ">DOB</td>
				<td><?php $this -> DBForm -> jpInput("FPADOB","input long",formatDateId(self::_Payers() -> result_get_value('PayerDOB')), null, false);?>
				<td class="header_table ">No Polis</td>
				<td><?php $this -> DBForm -> jpInput('pa_no_polis','input long', ( !empty($PAStat) ? $PAStat['no_polis'] : '' ) ,null,true); ?></td>
			</tr>
			<tr>
				<td class="header_table ">Email</td>
				<td><?php $this -> DBForm -> jpInput("FPAEmail", "input long",self::_Payers() -> result_get_value('PayerEmail'),null,false);?></td>
				<td class="header_table ">Expired</td>
				<td><?php $this -> DBForm -> jpInput("pa_expired","input long", ( !empty($PAStat) ? $PAStat['expired'] : '' ), null, true);?>
			</tr>
			<tr>
				<td class="header_table">Mobile Phone</td>
				<td><?php $this -> DBForm -> jpInput("FPAMobilePhoneNum","input long",self::_PayersPH(),null, false);?> </td>
				<td class="header_table">&nbsp;</td>
				<td><img id="loading_pa" src="../gambar/loading.gif" alt="some_text" style="position: flow; width:64px;height:64px; visibility: hidden;"></td>
			</tr>
		</table>
		
		<?
		echo "	</fieldset></form>";
		echo "<br><div> <a href=\"javascript:void(0);\" class=\"sbutton\" onclick=\"javascript:SendFPA();\" style=\"margin-left:-4px;
		margin-top:-10px;\" disabled><span>&nbsp;Send PA</span></a> &nbsp; </div>";
	}

	
	function AXA_Underwriting()
	{
		echo "<form name=\"form_edit_uw\">";
		echo "	<fieldset class=\"corner\" style=\"margin-left:-5px;\">
					<legend class=\"icon-customers\">&nbsp;&nbsp;&nbsp;<b>Underwriting</b></legend>	";
		
		$this->_get_questioner(UW);
		echo "	</fieldset>
				</form>";
		echo "<br><div> <a href=\"javascript:void(0);\" class=\"sbutton\" onclick=\"javascript:UpdateUW();\" style=\"margin-left:-4px;
		margin-top:-10px;\"><span>&nbsp;Update</span></a> &nbsp; </div>";
	}


	function AXA_Paymentinfo(){
		$CustomerId = self::getData() -> result_get_value('CustomerId');
	 ?>
	<fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;             
		<b>Payment Information</b>
	</legend>
	<form name="form_payment_ivr" >
	<table width="100%" align="left" cellpadding="5px" border="0px">
		<tr>
			<td class="header_table" width="20%" nowrap>IVR Payment Methode</td>
			<!--<td><?php $this -> DBForm -> jpHidden('hiddennocc','');?>&nbsp;&nbsp;<span id="error_messagecc_html"></span></td>-->
                        <!-- by japri -->
			<td width="20%">
				<?php $this -> DBForm -> jpCombo("IvrPayMethod", 'select long',$this -> Customer -> PaymentMethod(),"","onChange=Ext.DOM.loadIvrBank();");?> 	
				
			</td>
			<td width="20%">
				<div style="float:left;">
					<a href="javascript:void(0);" class="sbutton" onclick="Ext.DOM.splitintoivr();" style="margin:4px;"><span>&nbsp;Send To IVR</span></a>
					<a href="javascript:void(0);" class="sbutton" id="btn_sh" onclick="Ext.DOM.check_digit_valid();" style="margin:4px;"><span>&nbsp;Check Digit</span></a>
					<?php 
						if($this->getSession('handling_type') == 10) {
							$string = "btn_show";
						}
					?>
					<a href="javascript:void(0);" class="sbutton" id="<?php echo $string ?>" onclick="Ext.DOM.UpdateCC();" style="margin:4px; display: none;"><span>&nbsp;Update</span></a>
				</div>
			</td>
            <?php
             
            /*$this -> DBForm -> jpButton("splitintoivr",'IVR','Guide Customer','onclick="Ext.DOM.splitintoivr();"',0); */
            ?>
                        
			<!-- <td><?php //$this -> DBForm -> jpButton("getcustomercc",'IVR','Change to IVR','onclick="Ext.DOM.getcustomercc();"',0);?></td> -->
			<!-- <td><?php //$this -> DBForm -> jpButton("check_digit",'IVR','Check Digit','onclick="Ext.DOM.check_digit_valid();"');?></td> -->
		</tr>
		<tr>
			<td class="header_table" width="20%" nowrap>IVR Bank</td>
			<td width="20%"><div id="ivr_bank"><?php $this -> DBForm -> jpCombo("IvrBankId", 'select long',$this -> Customer -> SavingBank()); ?></div></td>
		</tr>
		<?php 
			if($this->getSession('handling_type') == 10) {
				$string = "myform";
			}
		?>
		<tr style="display: none;" id="<?php echo $string?>">
			<td class="header_table" width="20%" nowrap>CC Credit card</td>
			<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo $CustomerId ?>">
			<td width="20%"><input type="text" name="CreditCardNo" id="CreditCardNo"></td>
		</tr>
		<tr>
			<td colspan = "3">
				<div id="ivr_list" style="height:200px;overflow:auto;"></div>
			</td>
		</tr>
	</table>
	</form>
	</fieldset>
 <?php
 }
	
	function AXA_Survey()
	{
		echo "<form name=\"form_edit_survey\">";
		echo "	<fieldset class=\"corner\" style=\"margin-left:-5px;\">
					<legend class=\"icon-customers\">&nbsp;&nbsp;&nbsp;<b>Survey</b></legend>	";
		
		$this->_get_questioner(SURVEY);
		echo "	</fieldset>
				</form>";
		echo "<br><div> <a href=\"javascript:void(0);\" class=\"sbutton\" onclick=\"javascript:UpdateSurvey();\" style=\"margin-left:-4px;
		margin-top:-10px;\"><span>&nbsp;Update</span></a> &nbsp; </div>";
	}
	
	function _get_questioner($questioner_type)
	{
		if($this->product_survey!="" or !empty($this->product_survey))
		{
			$product = $this->product_survey;
			$survey_data = $this->_get_data_survey($product,$questioner_type);
			$question = $survey_data['question'];
			$answer_label = $survey_data['answer_label'];
			$setup_answer = $survey_data['setup_answer'];
			$answer_survey = $this->_get_ans_survey($questioner_type);
			
			
			/**generate view answer**/
			foreach($setup_answer as $index => $setup_decs)
			{
				if($setup_decs =="checkbox")
				{
					$setup[$index] = $this->DBForm->RTListcombo('survey_'.$index ,'CheckAll',$answer_label[$index],array_keys($answer_survey[$index]));
				}
				elseif($setup_decs=="combobox")
				{
					$cmb_select = "";
					if(is_array($answer_survey[$index]) && (count($answer_survey[$index]) > 0) )
					{
						foreach($answer_survey[$index] as $prod_sur_id => $cmb_ans)
						{
							if( ($cmb_ans !="") )
							{
								$cmb_select = $prod_sur_id;
							}
						}
					}
					$setup[$index] = $this->DBForm->RTCombo('survey_'.$index ,'select long', $answer_label[$index],$cmb_select);
				}
				elseif($setup_decs=="radiobutton")
				{
					$rb_checked = "";
					if(is_array($answer_survey[$index]) && (count($answer_survey[$index]) > 0) )
					{
						foreach($answer_survey[$index] as $prod_sur_id => $rb_ans)
						{
							if( ($rb_ans !="") )
							{
								$rb_checked = $prod_sur_id;
							}
						}
					}
					$setup[$index] = $this->DBForm->RTRadio('survey_'.$index ,'', $answer_label[$index],$rb_checked);
				}
				elseif($setup_decs=="textbox")
				{
					if(is_array($answer_survey[$index]) )
					{
						$setup[$index] = $this->DBForm->RTInput('survey_'.$index ,'input long',$answer_survey[$index],$answer_label[$index]);
					}
					else
					{
						$setup[$index] = $this->DBForm->RTInput('survey_'.$index ,'input long',array(),$answer_label[$index]);
					}
				}
			}
			
			/**** make view ***/
			$no=1;
			echo "<table width=\"100%\" border=\"1px\" cellspacing=\"1px\" cellpadding=\"6px\">";
			foreach ($question as $idx=>$qst)
			{
				echo "<tr >
						<td rowspan=\"2\" align=\"center\">$no</td>
						<td >$qst</td>
					  </tr>
					  <tr>
						<td><div id=\"ans_valid_{$idx}\">{$setup[$idx]}</div></td>
					  </tr>";
				$no++;
			}
			
			echo "</table>";
		}
	}

	private function _get_data_survey($product=0,$questioner_type=0)
	{

		/*$data=array();
		$sql = "SELECT a.prod_survey_id,c.survey_quest_id,c.survey_question, d.ans_label,e.type_survey
			FROM t_gn_questioner quest 
			INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
			INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
			INNER JOIN t_lk_question_survey c ON b.survey_quest_id=c.survey_quest_id
			INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
			INNER JOIN t_lk_type_survey e ON d.type_survey_id=e.type_survey_id
			WHERE quest.product_id = ".$product."
			AND quest.questioner_flag = 1
			AND quest.questioner_type = 1
			ORDER BY c.survey_quest_order,d.ans_order ASC";
				
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ) 
		{
			
			$data['question'][$rows['survey_quest_id']]= $rows['survey_question'];
			$data['answer_label'][$rows['survey_quest_id']][$rows['prod_survey_id']]= $rows['ans_label'];
			$data['setup_answer'][$rows['survey_quest_id']]= $rows['type_survey'];
		}
		return $data;*/
		
		$data=array();
		if($questioner_type!=0)
		{
			$sql = "SELECT a.prod_survey_id,c.survey_quest_id,c.survey_question, d.ans_label,e.type_survey,quest.questioner_type
				FROM t_gn_questioner quest 
				INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
				INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
				INNER JOIN t_lk_question_survey c ON b.survey_quest_id=c.survey_quest_id
				INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
				INNER JOIN t_lk_type_survey e ON d.type_survey_id=e.type_survey_id
				WHERE quest.product_id = ".$product."
				AND quest.questioner_flag = 1
				AND quest.questioner_type = ".$questioner_type."
				ORDER BY c.survey_quest_order,d.ans_order ASC";
		}
		else
		{
			$sql = "SELECT a.prod_survey_id,c.survey_quest_id,c.survey_question, d.ans_label,e.type_survey,quest.questioner_type
				FROM t_gn_questioner quest 
				INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
				INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
				INNER JOIN t_lk_question_survey c ON b.survey_quest_id=c.survey_quest_id
				INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
				INNER JOIN t_lk_type_survey e ON d.type_survey_id=e.type_survey_id
				WHERE quest.product_id = ".$product."
				AND quest.questioner_flag = 1
				ORDER BY c.survey_quest_order,d.ans_order ASC";
		}
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ) 
		{
			$data['question'][$rows['survey_quest_id']]= $rows['survey_question'];
			$data['questioner_type'][$rows['survey_quest_id']]= $rows['questioner_type'];
			$data['setup_answer'][$rows['survey_quest_id']]= $rows['type_survey'];
			$data['answer_label'][$rows['survey_quest_id']][$rows['prod_survey_id']]= $rows['ans_label'];
			// $data['answer_tree'][$rows['survey_quest_id']][$rows['prod_survey_id']] = $rows['ans_perent_id'];
		}
		return $data;
	}
	
 function AXA_Payers() { 
	$ProductCategory = $this -> Customer ->getProductCategory();
	$disable_pay = false;
	$Insured= self::getData();
	$CustomerDetil = $this->_getCustomer();
	$ProductId = $Insured -> result_get_value('ProductId');
	$PayerCreditCardNum = self::_Payers() -> result_get_value('PayerCreditCardNum');
	$PayerCreditCardExpDate = self::_Payers() -> result_get_value('PayerCreditCardExpDate');
	if( $ProductCategory['category_name'][$ProductId] == "APE")
	{
		$disable_pay = true;
		if( $this->getSession('handling_type')==USER_TELESALES )
		{
			$len_digit = strlen($PayerCreditCardNum);
			$PayerCreditCardNum = $this->_url->setMaskText($PayerCreditCardNum,"",$len_digit);
			$len_digit = strlen($PayerCreditCardExpDate);
			$PayerCreditCardExpDate = $this->_url->setMaskText($PayerCreditCardExpDate,"",$len_digit);
		}
		else
		{
			$disable_pay = false;
		}
	}
	
 ?>
	<form name="form_edit_payers">
	<input type="hidden" name="PayerId" id="PayerId" value="<?php echo self::_Payers() -> result_get_value('PayerId') ?>"/>
	<fieldset class="corner" style="margin-left:-5px;">
			<legend class="icon-customers">&nbsp;&nbsp;&nbsp;<b>Payers</b></legend>	
			<table width="100%" align="left" cellpadding="5px" >	
		<tr>
			<td class="header_table required">* Title</td>
			<td><?php $this -> DBForm -> jpCombo("PayerSalutationId",'select long', $this -> Customer -> Salutation(), self::_Payers() -> result_get_value('PayerSalutationId'));?></td>
			<td class="header_table required" nowrap>* First Name</td>
			<td><?php $this -> DBForm -> jpInput("PayerFirstName","input long",self::_Payers() -> result_get_value('PayerFirstName'));?></td>
			<td class="header_table" nowrap>Last Name</td>
			<td><?php $this -> DBForm -> jpInput("PayerLastName","input long",self::_Payers() -> result_get_value('PayerLastName'));?></td>
		</tr>
		<tr>
			<td class="header_table">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("PayerGenderId",'select long',  $this -> Customer -> Gender(), self::_Payers() -> result_get_value('pay_gender'));?></td>
			<td class="header_table">POB</td>
			<td><?php $this -> DBForm -> jpInput("PayerPOB","input long",self::_Payers() -> result_get_value('PayerPOB'));?></td>
			<td class="header_table">DOB</td>
			<td><?php $this -> DBForm -> jpInput("PayerDOB","input long date",formatDateId(self::_Payers() -> result_get_value('PayerDOB')));?>
			<input type="hidden" name="PayerAge" id="PayerAge" value=""/></td>
			
		</tr>
		<tr>
			<td class="header_table required">ID - Type </td>
			<td><?php $this -> DBForm -> jpCombo("PayerIdentificationTypeId","select long", $this -> Customer -> IndentificationId(), self::_Payers() -> result_get_value('payerIdentificationTypeId'));?></td>
			<td class="header_table required" >* ID No</td>
			<td><?php $this -> DBForm -> jpInput("PayerIdentificationNum","input long",self::_Payers() -> result_get_value('PayerIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
			<td class="header_table">Billing Address</td>
			<td><?php $this -> DBForm -> jpCombo("PayerAddrType",'select long',  $this->Customer->TypeAlamat(), self::_Payers() -> result_get_value('PayerAddrType'));?></td>
		</tr>
		<tr>
			<td class="header_table">Mobile Phone</td>
			<td><?php $this -> DBForm -> jpInput("PayerMobilePhoneNum","input long",self::_Payers() -> result_get_value('PayerMobilePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header_table">City</td>
			<td><?php //$this -> DBForm -> jpInput("PayerCity","input long",self::_Payers() -> result_get_value('PayerCity'),'onkeyup="Ext.Set(this.id).IsString();"');?>  
			<!-- Enhance QA Province City 13082018-->
			<select id="kota" name="PayerCity2" class="select long" onchange="myFunction()" width="200">
                <option value="" hidden="hidden">--Choose--</option>
                <option value="<?php echo self::_Payers()->result_get_value('PayerCity') ?>" selected="selected" hidden="hidden"><?php echo self::_Payers()->result_get_value('PayerCity') ?></option>
                <?php
                //echo "<option selected='selected' value='".self::_Payers()->result_get_value('PayerCity')."'>".self::_Payers()->result_get_value('PayerCity')."</option>";
                    $query = mysql_query("SELECT * FROM t_lk_city a
                                          INNER JOIN t_lk_province b ON b.ProvinceId = a.ProvinceId 
                                          where a.Flag = 1 ORDER BY a.CItyNames");
                    while ($row = mysql_fetch_array($query)) {
                    ?>
                        <option id="kota" class="<?php echo $row['ProvinceId']; ?>" value="<?php echo $row['CItyNames']; ?>">
                            <?php echo $row['CItyNames']; ?>
                        </option>
                <?php
                    }
                ?>
            </select>
            <td hidden="hidden"><?php $this -> DBForm -> jpInput("PayerCity","input",self::_Payers() -> result_get_value('PayerCity'),'onkeyup="Ext.Set(this.id).IsString();"');?>  </td>
            </td>
			</td>
			</td>
			<td class="header_table">Address</td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine1","input",self::_Payers() -> result_get_value('PayerAddressLine1'),null,null,40);?></td>
		</tr>	
		<tr>
			<td class="header_table">Home Phone </td>
			<td><?php $this -> DBForm -> jpInput("PayerHomePhoneNum","input long",self::_Payers() -> result_get_value('PayerHomePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header_table">Zip</td>
			<td><?php $this -> DBForm -> jpInput("PayerZipCode","input long",self::_Payers() -> result_get_value('PayerZipCode'),null,0,5);?></td>
			<td class="header_table"></td>
			<td> <?php $this -> DBForm -> jpInput("PayerAddressLine2","input",self::_Payers() -> result_get_value('PayerAddressLine2'),null,null,40);?></td>
		</tr>	
		<tr>
			<td class="header_table">Office Phone </td>
			<td><?php $this -> DBForm -> jpInput("PayerOfficePhoneNum", "input long",self::_Payers() -> result_get_value('PayerOfficePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header_table">Province</td>
			<td><?php $this -> DBForm -> jpCombo("PayerProvinceId", 'select long',$this -> Customer -> Province(),self::_Payers() -> result_get_value('PayerProvinceId'));?></td>
			<td class="header_table"></td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine3","input",self::_Payers() -> result_get_value('PayerAddressLine3'),null,null,40);?></td>
		</tr>	
		<tr>
			<td class="header_table">Certificate Type</td>
			<td><?php $this -> DBForm -> jpCombo("PayerCertificateStatus", 'select long',$this -> Customer -> getCertificateList(), self::_Payers() -> result_get_value('certificat_type') );?></td>
			<!--td class="header_table">Add. Mobile Phone</td>
			<td><?php //$this -> DBForm -> jpInput("AddMobilePhoneNum","input long",$CustomerDetil -> result_get_value('CustomerMobilePhoneNum2'),'onkeyup="Ext.Set(this.id).IsNumber();"',true);?> </td-->
			<td class="header_table">Payment Type</td>
			<td><?php $this -> DBForm -> jpCombo("PayerPaymentType", 'select long',$this -> Customer -> payment_method(),self::_Payers() -> result_get_value('PaymentTypeId'),"",$disable_pay );?></td>
			<td class="header_table"></td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine4", "input",self::_Payers() -> result_get_value('PayerAddressLine4'),null,null,40);?>  </td>
			
		</tr>	
		<tr>
			<!--td class="header_table">Add. Home Phone </td>
			<td><?php //$this -> DBForm -> jpInput("AddHomePhoneNum","input long",$CustomerDetil -> result_get_value('CustomerHomePhoneNum2'),'onkeyup="Ext.Set(this.id).IsNumber();"', true);?> </td-->
			
			<td class="header_table">Email</td>
			<td><?php $this -> DBForm -> jpInput("PayerEmail", "input long",self::_Payers() -> result_get_value('PayerEmail'));?></td>
			<td colspan="2" >
			<?php 
					
					if(self::_Payers() -> result_get_value('PaymentTypeId')=="1")
					{
						$display_saving = " style=\"display:none;\"";
						$display_cc = "";
						$val_saving = "";
						$val_cc = $PayerCreditCardNum;
						$disable_pay = true;
					}
					else if(self::_Payers() -> result_get_value('PaymentTypeId')=="2")
					{
						$display_saving = "";
						$display_cc = " style=\"display:none;\"";
						$val_saving = $PayerCreditCardNum;
						$val_cc = "";
					}
					
					echo "<table width=\"100%\" align=\"center\" cellpadding=\"5px\" id=\"payment_cc_form\"".$display_cc." >
						<tr>
							<td class=\"header_table\" valign=\"top\">Card Number</td>
							<td valign=\"top\">".$this -> DBForm -> RTInput("PayerCreditCardNum", "input long",$val_cc,null,'onkeyup="Ext.Set(this.id).IsNumber();"',$disable_pay,16)."</td>
							<td><span id=\"error_message_html\"></span></td>
						</tr>
						<tr>
							<td class=\"header_table\" nowrap>Expiration Date</td>
							<td >".$this -> DBForm -> RTInput("PayerCreditCardExpDate", "input small",$PayerCreditCardExpDate,null,"",$disable_pay)."
							<span class=\"wrap\">&nbsp;(mm/yy)&nbsp;</span><span id=\"error_message_exp\"></span>
							</td>
						</tr>
						</table>
						<table width=\"100%\" align=\"center\" cellpadding=\"5px\" id=\"payment_saving_form\"".$display_saving." >
						<tr>
							<td class=\"header_table\" valign=\"top\">Saving Account</td>
							<td valign=\"top\">".$this -> DBForm -> RTInput("SavingAccount", "input long",$val_saving,null,'',$disable_pay,16)."</td>
							<td><span id=\"error_message_html\"></span></td>
						</tr>
						</table>";
				?>
			</td>
			
			
			
		</tr>
		<tr>
			<td class="header_table">Add. Office Phone </td>
			<td><?php $this -> DBForm -> jpInput("AddOfficePhoneNum", "input long",$CustomerDetil -> result_get_value('CustomerWorkPhoneNum2'),'onkeyup="Ext.Set(this.id).IsNumber();"', true);?> </td>
			<td class="header_table">Card Type</td>
			<td><div id="dyn_card_type"><?php $this -> DBForm -> jpCombo("CreditCardTypeId", 'select long',$this -> Customer -> CardType(),self::_Payers() -> result_get_value('CreditCardTypeId'),"",$disable_pay );?></div></td>
			<td class="header_table">Bank</td>
			<td><?php $this -> DBForm -> jpCombo("PayersBankId", 'select long',$this -> Customer -> Bank(),self::_Payers() -> result_get_value('PayersBankId'),"",$disable_pay);?></td>
		</tr>	
	 </table>
	</fieldset>
	</form><br>
	<div> <a href="javascript:void(0);" class="sbutton" onclick="javascript:UpdatePayer();" style="margin-left:-4px;
		margin-top:-10px;"><span>&nbsp;Update</span></a> &nbsp; </div>
	<script>
		$("#kota").chained("#PayerProvinceId");
	</script>  

 <?php
 }
  
 
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
 function AXA_Insured()
 { 
	$Insured= self::getData();
	$this->product_survey = $Insured -> result_get_value('ProductId');
?>
	<form name="form_edit_insured">
	<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo $Insured -> result_get_value('CustomerId'); ?>"/> 
	<fieldset class="corner" style="margin-left:-5px;">
		<legend class="icon-application">&nbsp;&nbsp;&nbsp;<b>Insured</b>
		</legend>	
		
	<table cellpadding="2px">
	<tr>
			<td class="header_table required">* Product</td>
			<td><?php $this -> DBForm -> jpCombo('ProductId','select long', self::_getProductName(),$Insured -> result_get_value('ProductId') ,null); ?> </td>
			<td class="header_table">Payment Mode</td>
			<td><span id="pay_plan_h"><?php $this -> DBForm -> jpCombo('InsuredPayMode','select long', $this ->Customer -> Paymode( self::$CampaignId ), $Insured -> result_get_value('PayModeId'),"OnChange=getPremi(this);"); ?></span> </td>
		</tr>
		<tr>
			<td class="header_table required">* Group Premi</td>
			<td><?php $this -> DBForm -> jpCombo('InsuredGroupPremi','select long', $this->Customer->PremiumGroup(),$Insured -> result_get_value('PremiumGroupId'), null); ?> </td>
			<td class="header_table">Plan Type</td>
			<td><span id="plan_plan_h"><?php $this -> DBForm -> jpCombo('InsuredPlanType','select long', $this -> Customer -> ProductPlan(self::$CampaignId),$Insured -> result_get_value('ProductPlan'),"OnChange=getPremi(this);"); ?></span> </td>
		</tr>
		<tr>
			<td class="header_table required">* ID Type</td>
			<td><?php $this -> DBForm -> jpCombo('InsuredIdentificationTypeId','select long', $this->Customer->IndentificationId(),$Insured -> result_get_value('IdentificationTypeId')); ?> </td>
			<td class="header_table">Premi</td>
			<td><?php $this -> DBForm -> jpInput('InsuredPremi','input long', formatRupiah($Insured -> result_get_value('Premi')), null, 1); ?> <span class="wrap"> ( IDR ) </span></td>
			</tr>
		<tr>
			<td class="header_table required">* ID No</td>
			<td><?php $this -> DBForm -> jpInput('InsuredIdentificationNum','input long',$Insured -> result_get_value('InsuredIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?></td>
			
		</tr>
		<tr>
			<td class="header_table sunah">Relation</td>
			<td><?php $this -> DBForm -> jpCombo('InsuredRelationshipTypeId','select long', $this->Customer->RelationshipType(),$Insured -> result_get_value('RelationshipTypeId')); ?></td>
			
		</tr>
		<tr>
			<td class="header_table sunah">Title</td>
			<td><?php $this -> DBForm -> jpCombo('InsuredSalutationId','select long',$this->Customer->Salutation(),$Insured -> result_get_value('SalutationId')); ?></td>
		</tr>
		<tr>
			<td class="header_table sunah">First Name</td>
			<td><?php $this -> DBForm -> jpInput('InsuredFirstName','input long',$Insured -> result_get_value('InsuredFirstName'),null); ?></td>
		</tr>
		<tr>
			<td class="header_table sunah">Last Name</td>
			<td><?php $this -> DBForm -> jpInput('InsuredLastName','input long',$Insured -> result_get_value('InsuredLastName'),null); ?></td>
		</tr>
		<tr>
			<td class="header_table sunah">Gender</td>
			<td><?php $this -> DBForm -> jpCombo('InsuredGenderId','select long',$this -> Customer -> Gender(),$Insured -> result_get_value('GenderId')); ?></td>
		</tr>
		<tr>
			<td class="header_table sunah">POB</td>
			<td><?php $this -> DBForm -> jpInput('InsuredPOB','input long',$Insured -> result_get_value('InsuredPOB'),null); ?></td>
		</tr>
		<tr>
			<td class="header_table sunah">DOB</td>
			<td><?php $this -> DBForm -> jpInput('InsuredDOB','input long date',$this -> formatDateId($Insured -> result_get_value('InsuredDOB')), null, 0); ?></td>
		</tr>
		<tr>
			<td class="header_table sunah">Age</td>
			<td><?php $this -> DBForm -> jpInput('InsuredAge','input long',$Insured -> result_get_value('InsuredAge'), null, 1); ?></td>
		</tr>
	</table>	
	</fieldset><br>
	</form>
	<div> <a href="javascript:void(0);" class="sbutton" onclick="javascript:ExtInsured();" style="margin-left:-4px;
		margin-top:-10px;"><span>&nbsp;Update</span></a> &nbsp; </div>
<?php
 }
 
 
 private function Ciputra_Holder() {
	?>

	<?php //print_r(self::_Holder()); ?>
	<fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;<b>Polis Holder</b>
		<?php $this -> DBForm -> jpCheck("CopyDataPayer",'Holder = Payer ',1,"onchange=Ext.DOM.CopyDataPayer2EditHolder(this);",0,0);?>
	</legend>
	<input type="hidden" id="PayerXsellbank" name="PayerXsellbank" value="" />
	<input type="hidden" id="PayerValidXsell" name="PayerValidXsell" value="" />
	<form name="form_data_holder" >
	<input type="hidden" name="HolderId" id="HolderId" value="<?php echo self::_Holder() -> result_get_value('HolderId') ?>"/>
	<input type="hidden" id="isXsell" name="isXsell" value="" />
	<table width="100%" align="center" cellpadding="5px" border="0px">	
		<tr>
			<!-- t d class="header_table required">* Nomor SPAJ</td>
			<td><?#php $this -> DBForm -> jpInput("nomor_spaj","input long",null,'onkeyup="Ext.Set(this.id).IsString();"',1);?></t d -->
			<td class="header_table " nowrap>* First Name</td>
			<td><?php $this -> DBForm -> jpInput("HolderFirstName","input long",self::_Holder() -> result_get_value('HolderFirstName'),'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table" nowrap>Last Name</td>
			<td><?php $this -> DBForm -> jpInput("HolderLastName","input long",self::_Holder() -> result_get_value('HolderLastName'),'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table ">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("HolderGenderId",'select long',  $this -> Customer -> Gender(), self::_Holder() -> result_get_value('HolderGenderId'));?></td>
		</tr>
		<tr>
			<td class="header_table ">Hubungan dengan PH</td>
			<td><?php $this -> DBForm -> jpCombo("HolderRelationshipTypeId",'select long', $this->Customer->RelationshipType(),self::_Holder() -> result_get_value('HolderRelationshipTypeId'),79);?></td>
			<td class="header_table ">POB</td>
			<td><?php $this -> DBForm -> jpInput("HolderPOB","input long suggestcity",self::_Holder() -> result_get_value('HolderPOB'),'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table ">DOB</td>
			<td><?php $this -> DBForm -> jpInput("HolderDOB","input long date",self::_Holder() -> result_get_value('HolderDOB'));?><input type="hidden" name="HolderAge" id="HolderAge" value=""/></td>
		</tr>
		<tr>
			<td class="header_table ">Pekerjaan Client</td>
			<?php //print_r($this->Customer->getIncomeList());?>
			<td><?php $this -> DBForm -> jpCombo("HolderPosition",'select long', $this->Customer->getOcupationList(), self::_Holder() -> result_get_value('HolderPosition'), 1);?></td>
			<td class="header_table ">Jabatan Client</td>
			<td><?php $this -> DBForm -> jpCombo("HolderOccupation","input long",$this->Customer->getJobPosList(),self::_Holder() -> result_get_value('HolderOccupation'),'onkeyup="Ext.Set(this.id).IsString();"',1);?></td>
			<td class="header_table ">Income Setahun</td>
			<td><?php $this -> DBForm -> jpCombo("HolderIncome","input long", $this->Customer->getIncomeList(),self::_Holder() -> result_get_value('HolderIncome'),'onkeyup="Ext.Set(this.id).IsNumber();"',1);?></td>
		</tr>
		<tr>
			<td class="header_table ">Tempat Kerja</td>
			<td><?php $this -> DBForm -> jpInput("HolderCompany",'select long',  self::_Holder() -> result_get_value('HolderCompany'),null, 1);?></td>
			<td class="header_table ">Mobile Phone</td>
			<td><?php $this -> DBForm -> jpInput("HolderMobilePhoneNum","input long suggestcity",self::_Holder() -> result_get_value('HolderMobilePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
			<td class="header_table ">Marital status</td>
			<td><?php $this -> DBForm -> jpCombo("HolderMaritalStatus","input long date", $this->Customer->getMaritallist(),self::_Holder() -> result_get_value('HolderMaritalStatus'));?></td>
		</tr>
		<tr>
			<td class="header_table ">ID - Type </td>
			<td><?php $this -> DBForm -> jpCombo("HolderIdentificationTypeId","select long", $this -> Customer -> IndentificationId(),self::_Holder() -> result_get_value('HolderIdentificationTypeId'),NULL,1);?></td>
			<td class="header_table " >* ID No</td>
			<td><?php $this -> DBForm -> jpInput("HolderIdentificationNum","input long",self::_Holder() -> result_get_value('HolderIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"',1);?></td>
			<!-- td class="header_table ">Hubungan dengan PH</td -->
			<!-- td><?php //$this -> DBForm -> jpCombo("HolderRelationshipTypeId",'select long', $this->Customer->RelationshipType(),self::_Holder() -> result_get_value('HolderRelationshipTypeId'),79);?></td -->
		</tr>
		<tr>
			<td class="header_table ">Type Alamat</td>
			<td><?php $this -> DBForm -> jpCombo("HolderAddrType","input long",$this->Customer->TypeAlamat(), self::_Holder() -> result_get_value('HolderAddrType') );?> </td>
			<td class="header_table ">Alamat 1</td>
			<td><?php $this -> DBForm -> jpInput("HolderAddressLine1", "input long",self::_Holder() -> result_get_value('HolderAddressLine1'),null,null,40 );?></td>
			<td class="header_table">Alamat 2</td>
			<td><?php $this -> DBForm -> jpInput("HolderAddressLine2", "input long",self::_Holder() -> result_get_value('HolderAddressLine2'),null,null,40 );?></td>
			<!-- td class="header_table">Certificate status</td>
			<td><?php //$this -> DBForm -> jpCombo("PayerCertificateStatus", 'select long',$this -> Customer -> getCertificateList(), self::_Payers() -> result_get_value('certificat_type') );?></td -->
	
		</tr>	
		<tr>
			<td class="header_table ">Province</td>
			<td><?php $this -> DBForm -> jpCombo("HolderProvinceId","input long",$this -> Customer -> Province(),self::_Holder() -> result_get_value('HolderProvinceId'), true);?> </td>
			<td class="header_table ">Alamat 3</td>
			<td><?php $this -> DBForm -> jpInput("HolderAddressLine3", "input long",self::_Holder() -> result_get_value('HolderAddressLine3'),null,null,40 );?></td>
			<td class="header_table">Alamat 4</td>
			<td><?php $this -> DBForm -> jpInput("HolderAddressLine4", "input long",self::_Holder() -> result_get_value('HolderAddressLine4'),null,null,40 );?></td>
			<!-- td class="header_table">Email</td>
			<td><?php //$this -> DBForm -> jpInput("HolderEmail", "input long",self::_Holder() -> result_get_value('HolderEmail'));?></td -->
		</tr>
		
		<!-- tr>
			<td class="header_table required">Bank</td>
			<td><div id="dyn_bank"><?php //$this -> DBForm -> jpCombo('HoldersBankId', 'select long',$this -> Customer -> Bank(),self::_Holder() -> result_get_value('HoldersBankId'));?></div></td>
			<td class="header_table required">Cabang Bank</td>
			<td><?php //$this -> DBForm -> jpInput("HolderBankBranch", "input long",self::_Holder() -> result_get_value('HolderBankBranch'),'onkeyup="Ext.Set(this.id).IsString();"' );?></td>
			<td class="header_table required">Nomor Rekening</td>
			<td><?php //$this -> DBForm -> jpInput("HolderCreditCardNum","input",self::_Holder() -> result_get_value('HolderCreditCardNum'),null,0,100);?></td>
		</tr -->
		<tr>
			<td class="header_table">Office Phone</td>
			<td><?php $this -> DBForm -> jpInput("HolderOfficePhoneNum", "input long",self::_Holder() -> result_get_value('HolderOfficePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header_table ">Kota</td>
			<td><?php $this -> DBForm -> jpInput("HolderCity","input long",self::_Holder() -> result_get_value('HolderCity'),'onkeyup="Ext.Set(this.id).IsString();"');?>  </td>
			<!-- td class="header_table">Card Type</td>
			<td><div id="dyn_card_type"><?php //$this -> DBForm -> jpCombo("HolderCreditCardTypeId", 'select long',$this -> Customer -> CardType(),self::_Holder() -> result_get_value('HolderCreditCardTypeId') );?></div></td -->
			<td class="header_table ">Zip</td>
			<td><?php $this -> DBForm -> jpInput("HolderZipCode","input long",self::_Holder() -> result_get_value('HolderZipCode'),null,0,5);?></td>
		</tr>
	 </table>
	 </form>
	</fieldset> 
	<br>
	<div> <a href="javascript:void(0);" class="sbutton" onclick="javascript:UpdateHolder();" style="margin-left:-4px;
		margin-top:-10px;"><span>&nbsp;Update</span></a> &nbsp; </div>
	
	<?php 
}
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function AXA_Add_Benefiecery() { ?>

<form name="form_add_benefiecery">
<?php
 $total = implode(self::getBeneftotal());
 $frame = 4 - $total;
 // print_r($total);
 for( $_benefiecery=1; $_benefiecery<=$frame; $_benefiecery++)
 {  ?>


 <fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
		<b>Benefiecery <?php echo $_benefiecery; ?></b>
		<?php $this -> DBForm -> jpCheck("AddBenefeciery",null,$_benefiecery,"onclick=FormBenefieceryAdd(this,". $_benefiecery .");");?>
	</legend>	
	
	<table cellpadding="3px"> 
		<tr>
			<td class="header_table">Relations</td>
			<td><?php $this -> DBForm -> jpCombo("AddBenefRelationshipTypeId_{$_benefiecery}",'select long',  $this -> Customer -> RelationshipType()); ?></td>
		</tr>
		<tr>
			<td class="header_table">Title</td>
			<td><?php $this -> DBForm -> jpCombo("AddBenefSalutationId_{$_benefiecery}",'select long', $this -> Customer -> Salutation()); ?></td>
		</tr>
		<tr>
			<td class="header_table required">* First Name</td>
			<td> <?php $this -> DBForm -> jpInput("AddBenefFirstName_{$_benefiecery}","input long",null,null); ?></td>
		</tr>
		<tr>
			<td class="header_table ">Last Name</td>
			<td><?php $this -> DBForm -> jpInput("AddBenefLastName_{$_benefiecery}","input long",null,null); ?></td>
		</tr>
		<tr>
			<td class="header_table">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("AddBenefGenderId_{$_benefiecery}",'select long', $this -> Customer -> Gender()); ?></td>
		</tr>
		<tr>
			<td class="header_table">DOB</td>
			<td><?php $this -> DBForm -> jpInput("AddBenefDOB_{$_benefiecery}","input long date");?></td>
		</tr>
		<tr>
			<td class="header_table required">* Percentage</td>
			<td><?php $this -> DBForm -> jpInput("AddBenefPercentage_{$_benefiecery}","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"'); ?>&nbsp;<span class="wrap">( % )</span></td>
		</tr>
	</table>
  </fieldset><br>		
<?php }	 ?>		
</form>
<div> <a href="javascript:void(0);" class="sbutton" onclick="javascript:ExtAddBenefiecery();" style="margin-left:-4px;
		margin-top:-10px;"><span>&nbsp;Submit</span></a> &nbsp; </div>
<?php 
}				
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
function AXA_Edit_Benefiecery() {  ?>
 <form name="form_edit_benefiecery">
	<?php $num = 1;
	foreach( self::getBenef() as $rows )
	{  ?>
	
	<fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
		<b>Benefiecery <?php echo $num; ?></b>
		<?php $this -> DBForm -> jpCheck("EditBenefeciery",null,$rows['BeneficiaryId'],"onclick=FormBenefieceryEdit(this,{$rows['BeneficiaryId']});");?>
	</legend>	
	
	<table cellpadding="3px"> 
		<tr>
			<td class="header_table">Relation</td>
			<td><?php $this -> DBForm -> jpCombo("EditBenefRelationshipTypeId_{$rows['BeneficiaryId']}", 'select long',  $this -> Customer -> RelationshipType(), $rows['RelationshipTypeId'] ); ?></td>
		</tr>
		<tr>
			<td class="header_table">Title</td>
			<td><?php $this -> DBForm -> jpCombo("EditBenefSalutationId_{$rows['BeneficiaryId']}",'select long', $this -> Customer -> Salutation(),$rows['SalutationId']); ?></td>
		</tr>
		<tr>
			<td class="header_table required">* First Name</td>
			<td> <?php $this -> DBForm -> jpInput("EditBenefFirstName_{$rows['BeneficiaryId']}","input long", $rows['BeneficiaryFirstName'],null); ?></td>
		</tr>
		<tr>
			<td class="header_table ">Last Name</td>
			<td><?php $this -> DBForm -> jpInput("EditBenefLastName_{$rows['BeneficiaryId']}","input long", $rows['BeneficiaryLastName'],null ); ?></td>
		</tr>
		<tr>
			<td class="header_table">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("EditBenefGenderId_{$rows['BeneficiaryId']}",'select long', $this -> Customer -> Gender(),$rows['GenderId']); ?></td>
		</tr>
		<tr>
			<td class="header_table">DOB</td>
			<td><?php $this -> DBForm -> jpInput("EditBenefDOB_{$rows['BeneficiaryId']}","input long date", formatDateId($rows['BeneficiaryDOB']),null);?></td>
		</tr>
		<tr>
			<td class="header_table required">* Percentage</td>
			<td><?php $this -> DBForm -> jpInput("EditBenefPercentage_{$rows['BeneficiaryId']}","input long",$rows['BeneficieryPercentage'],'onkeyup="Ext.Set(this.id).IsNumber();"'); ?>&nbsp;<span class="wrap">( % )</span></td>
		</tr>
	</table>
  </fieldset><br>		
<?php  
$num++;
}  ?>		
</form>

<div> 
	<a href="javascript:void(0);" class="sbutton" onclick="javascript:ExtUpdateBenefiecery(<?php echo $rows['BeneficiaryId']; ?>);" style="margin-left:-4px;	margin-top:-10px;"><span>&nbsp;Update</span></a> 
&nbsp; </div>
<?php 
 }
	
	function footer()
	{
	
	}
	
	 public static function &get_instance() 
	 {	
		if(is_null(self::$Instance)) {
			self::$Instance = new self();
		}
		
		return self::$Instance;
	 }
}

$AxaEditQAProduct = new AxaEditQAProduct();
?>