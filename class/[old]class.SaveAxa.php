<?php

require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");

/*
 * @ package 	: Save Policy For AXA 
 * 
 * @ param	 	: Over all request paramter
 * @ method		: Class aksess
 * @ return 	: JSON parameter 
 * @ revision	: 2
 */
 
define('HOLDER',2); 
define('SPOUSE',3); 
define('DEPEND',1); 
define('UW',2); 
define('SURVEY',1); 
 
 
class AXA_Save extends mysql
{

	private $PolicyNo;
	private $PolicyLast;
	private $LastInsured;
	private $Sensor;
/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 public function __construct()
 {
	parent::__construct();
	$this->Sensor = new application();
	if( $this -> havepost('action') ){
		self::index();
	}
	
 }
 
/* @ def 	: getPolicyId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */
 
private function _set_save_insured( $Insured = null )
{
	if( !is_null( $Insured ) AND is_array($Insured) )
	{
		$this -> set_mysql_insert('t_gn_insured', $Insured);
	}
}
/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

function _get_class_policy()
{
	$_CLASS_APPLICATION = dirname(__FILE__) ."/class.generator.policy.php";
	if( file_exists( $_CLASS_APPLICATION ) )
	{
		require_once( $_CLASS_APPLICATION );	
	}	
	
	$_instance =& Polis::get_instance();
	return $_instance;
}  
 
/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
public function index()
{
	
	switch( $this -> escPost('action')) 
	{
		case '_get_age'			: self::_get_personal_age(); 	break;
		case '_get_age_payer'	: self::_get_payer_age(); 		break;
		case '_get_premi' 		: self::_get_personal_premi();  break;
		case '_get_payer_data'  : self::_get_payer_data(); 		break;
		case '_get_split'		: self::_get_splite_data(); 	break;
		case '_savePolis'		: self::_set_save_data(); 		break;
		case '_get_policy'		: self::_get_policy(); 			break;
		case '_get_detail'		: self::_get_Detail(); 			break;
		case '_get_transaction' : self::_get_transaction();     break;
		case '_get_pay_mode' 	: self::_get_pay_mode();     	break;
		case '_get_group_premi' : self::_get_group_premi();    	break;
		case '_get_plan_type' 	: self::_get_plan_type();     	break;
		case '_get_same_plan' 	: self::getExistingPlan();     	break;
		case '_get_valid_ins' 	: self::validInsured();     	break;
		case '_get_benefit' 	: self::_get_benefit();     	break;
		case '_check_benef' 	: self::checkBenef();     		break;
		case '_get_product_benef': self::_get_product_benef();  break;
		case '_update_qc'		 : self::_update_qc();  		break;
		case '_get_benefInsured' : self::_get_benefInsured();	break;
		case '_get_valid_prefix' : self::_get_valid_prefix();	break;
		case '_get_valid_expire' : self::_get_valid_expire();	break;
		case '_get_questioner' 		 : self::_get_questioner();			break;
		case '_get_mandat_quest' : self::_mandatory_question();	break;
		case '_get_mandat_ans'	 : self::_mandatory_answer();	break;
		case '_getValidAnswer'	 : self::_getValidAnswer();		break;
		case '_getExceptQuestion'	 : self::_getExceptQuestion();		break;
		case '_getUWQuestion'	 : self::_getUWQuestion();		break;
		case '_json_pay_method'	 : self::json_pay_method();		break;
		case '_card_type_pay'	 : self::_card_type_pay();		break;
		case 'CmbBankByCardType'	 : self::CmbBankByCardType();		break;
		case 'show_card_numbercc' : self::show_card_numbercc(); break;
        case 'show_card_numbersaving' : self::show_card_numbersaving(); break;
        case '_load_ivr_bank' : self::load_ivr_bank(); break;
        // case '_check_digit' : self::_check_digit(); break;
		// case 'move_cc' : self::move_cc();break;
		// case 'move_saving' : self::move_saving();break;
	}
}

function check_cc_type($card_number)
{
	$cc_type = 0;
	$card = $this -> Customer -> cc_type_pattern();
	foreach($card as $cardtypeid => $array_obj){
		if(preg_match($array_obj['pattern'],$card_number) >= 1)
		{
			$cc_type = $cardtypeid;
			break;
		}
	}
	return $cc_type;
}

// function move_cc()
// {
	// $cc = $this->get_card_numbercc();
	// echo json_encode($cc);
// }

// function _check_digit(){
	// $_conds = array('result'=>0, 'img'=>'<img src="../gambar/icon/delete.png">');
	// $digit_ivr = array();
	// if($this -> havepost('CustomerId') && $this -> havepost('PayMethodId'))
	// {
		// $CustomerId = $this -> escPost('CustomerId');
		// $pay_methode_id = $this -> escPost('PayMethodId');
		// $cc = 1;
		// $saving = 2;
		
		// if($pay_methode_id == $cc)
		// {
			// $sql = "select a.CreditCardNo as digit_ivr,a.ExpireDate from t_gn_customer_cc a where a.CustomerId = ".$CustomerId;
		// }
		// elseif($pay_methode_id == $saving)
		// {
			// $sql = "select a.AccountNo as digit_ivr  from t_gn_customer_bank_account a where a.CustomerId = ".$CustomerId;
		// }
		// $qry = $this->query($sql);
		// foreach($qry -> result_assoc() as $rows ) 
		// {
			// $digit_ivr[] = $rows['digit_ivr'];
 		// }
		
		// echo "<table border=\"0\" align=\"left\" width=\"50%\" cellpadding=\"2\" cellspacing=\"0\">
				// <tr>
					// <td class=\"header-first\" align=\"center\">#</td>
					// <td class=\"header-first\">Digit</td>
					// <td class=\"header-last\">Status</td>
				// </tr>";
		// $i=1;
		// foreach($digit_ivr as $key => $value ) 
		// {
			// echo "<tr>
					// <td class=\"rows-first\" align=\"center\"><input type=\"checkbox\" value=\"\" onclick=\"InsuredWindow(this)\"".(  ( count($digit_ivr)==$i )?'checked':'')." ></td>
					// <td class=\"rows-first\" align=\"left\">".$value."</td>
					// <td class=\"rows-last\" align=\"left\">".(  ( count($digit_ivr)==$i )?'Selected':'Forgeted')."</td>
				// </tr>";
			// $i++;
		// }
		// echo "</table>";
	// }
	
// }
private function getBankByIvrPay( $ivr_payment = null )
{
	$bank = array();
	if(!is_null($ivr_payment))
	{
		$sql = "SELECT a.BankId,b.BankName FROM t_ivr_register_bank a 
				INNER JOIN t_lk_bank b ON a.BankId=b.BankId
				WHERE b.BankStatusFlag  = 1
				AND a.ivr_payment_id = " .$ivr_payment ;
				// echo $sql;
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$bank[$rows['BankId']] = $rows['BankName'];
		}
	}
	return $bank;
}
private function load_ivr_bank()
{
	$bank_list = array();
	// var_dump($this->havepost('IvrPayMethode'));
	if($this->havepost('IvrPayMethode') and ($this->escPost('IvrPayMethode') != "undefined") )
	{
		$IvrPayMethode = $this->escPost('IvrPayMethode');
		$bank_list = $this->getBankByIvrPay( $IvrPayMethode );
		if(count( $bank_list ) > 0)
		{
			$this -> DBForm -> jpCombo("IvrBankId", 'select long',$bank_list);
		}
		else
		{
			$this -> DBForm -> jpCombo("IvrBankId", 'select long',array());
		}
	}
	else
	{
		$this -> DBForm -> jpCombo("IvrBankId", 'select long',array());
	}
	
	
	
}
private function getXSellDetail()
{
	$Xselldetail = array();
	$sql = "SELECT a.CustomerCreditCardNum,
			a.CustomerCreditCardExpDate,
			a.CustomerEmail,
			a.accountnumber,
			a.Xsellbank
			FROM t_gn_customer a WHERE a.CustomerId = '". $this -> escPost('CustomerId') ."'";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$Xselldetail = $qry -> result_first_assoc();
	}
	return $Xselldetail;
}
private function get_card_numbercc()
{
	$digit_ivr = array();
	$sql = "	SELECT a.CreditCardId,a.CreditCardNo,a.ExpireDate,
				DATE_FORMAT(a.CreatedOn,'%d-%m-%Y %H:%i:%s') as CreatedOn 
				FROM t_gn_customer_cc a where 1=1 ";
	if($this -> havepost('CustomerId'))
	{
		$sql .= " AND a.CustomerId = ".$this -> escPost('CustomerId');
	}
	
	if($this -> havepost('DigitId'))
	{
		$sql .= " AND a.CreditCardId = ".$this -> escPost('DigitId');
	}
	$sql .= " ORDER BY a.CreditCardId ASC";
	$qry = $this->query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$digit_ivr[$rows["CreditCardId"]]["card_number"] = $rows["CreditCardNo"];
		$digit_ivr[$rows["CreditCardId"]]["CreatedOn"] = $rows["CreatedOn"];
		$digit_ivr[$rows["CreditCardId"]]["Expire"] = substr($rows["ExpireDate"],0,2) . "/" . substr($rows["ExpireDate"], 2, 2);
	}
	return $digit_ivr;
}

private function get_card_numbersaving()
{
	$digit_ivr = array();
	$sql = "	SELECT a.AccountId,a.AccountNo,
				DATE_FORMAT(a.CreatedOn,'%d-%m-%Y %H:%i:%s') as CreatedOn 
				FROM t_gn_customer_bank_account a where 1=1 ";
	if($this -> havepost('CustomerId'))
	{
		$sql .= " AND a.CustomerId = ".$this -> escPost('CustomerId');
	}
	$sql .= " ORDER BY a.AccountId ASC";
	$qry = $this->query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$digit_ivr[$rows["AccountId"]]["AccountNo"] = $rows["AccountNo"];
		$digit_ivr[$rows["AccountId"]]["CreatedOn"] = $rows["CreatedOn"];
	}
	return $digit_ivr;
}

function show_card_numbercc(){
    
    // $qry = $this->query("select a.CreditCardNo,a.ExpireDate from t_gn_customer_cc a where a.CustomerId = ".$CustomerId."
						// AND a.CreditCardId = (SELECT MAX(CreditCardId) FROM t_gn_customer_cc WHERE CustomerId =".$CustomerId.")");
    // foreach($qry -> result_assoc() as $rows ) 
    // {
        // $data["card_number"] = $rows["CreditCardNo"];
        // $data["Expire"] = substr($rows["ExpireDate"],0,2) . "/" . substr($rows["ExpireDate"], 2, 2);
    // }
	$digit_ivr = array();
	if($this -> havepost('CustomerId'))
	{
		// $CustomerId = $this -> escPost('CustomerId');
		// $qry = $this->query("	select a.CreditCardId,a.CreditCardNo,a.ExpireDate,
								// DATE_FORMAT(a.CreatedOn,'%d-%m-%Y %H:%i:%s') as CreatedOn 
								// from t_gn_customer_cc a where a.CustomerId = ".$CustomerId."
								// ORDER BY a.CreditCardId ASC"
							// );
		// foreach($qry -> result_assoc() as $rows ) 
		// {
			// $digit_ivr[$rows["CreditCardId"]]["card_number"] = $rows["CreditCardNo"];
			// $digit_ivr[$rows["CreditCardId"]]["CreatedOn"] = $rows["CreatedOn"];
			// $digit_ivr[$rows["CreditCardId"]]["Expire"] = substr($rows["ExpireDate"],0,2) . "/" . substr($rows["ExpireDate"], 2, 2);
		// }
		$digit_ivr = $this->get_card_numbercc();
		echo "<table border=\"0\" align=\"left\" width=\"50%\" cellpadding=\"2\" cellspacing=\"0\">
				<tr>
					<td class=\"header-first\" align=\"center\">#</td>
					<td class=\"header-first\">Digit</td> 
					<td class=\"header-first\">Inserted Date</td>
				</tr>";
		$i=1;
		foreach($digit_ivr as $id => $array_value ) 
		{
			$len_digit = strlen($array_value['card_number']);
			echo "<tr>
					<td class=\"rows-first\" align=\"center\"><input type=\"radio\" value=\"".$id."\" name=\"digit_arr\" id=\"digit_arr\" onclick=\"Ext.DOM.digitRB(this)\"".(  $i == 1 ?'checked':'')." ></td>
					
					<td class=\"rows-first\" align=\"left\">".$this->Sensor->setMaskText($array_value['card_number'],"",$len_digit)."</td>
					<td class=\"rows-first\" align=\"left\">".$array_value['CreatedOn']."</td>
				</tr>";
			$i++;		
		}
		echo "</table>";
		
		//<td class=\"rows-first\" align=\"left\">".$this->Masking->setMaskText($array_value['card_number'],"x",8)."</td>
	}
   
}

/* by japri */
function show_card_numbersaving(){
    // $CustomerId = $this -> escPost('CustomerId');
    // $qry = $this->query("select a.AccountNo from t_gn_customer_bank_account a where a.CustomerId = ".$CustomerId."
						// AND a.AccountId = (SELECT MAX(AccountId) FROM t_gn_customer_bank_account WHERE CustomerId = ".$CustomerId.")");
    // foreach($qry -> result_assoc() as $rows ) 
    // {
        // $data["card_number"] = $rows["AccountNo"];
        
    // }
    $digit_ivr = array();
	if($this -> havepost('CustomerId'))
	{
		// $CustomerId = $this -> escPost('CustomerId');
		// $qry = $this->query("	SELECT a.AccountId,a.AccountNo,
								// DATE_FORMAT(a.CreatedOn,'%d-%m-%Y %H:%i:%s') as CreatedOn 
								// FROM t_gn_customer_bank_account a where a.CustomerId = ".$CustomerId."
								// ORDER BY a.AccountId ASC"
							// );
		// foreach($qry -> result_assoc() as $rows ) 
		// {
			// $digit_ivr[$rows["AccountId"]]["AccountNo"] = $rows["AccountNo"];
			// $digit_ivr[$rows["AccountId"]]["CreatedOn"] = $rows["CreatedOn"];
		// }
		$digit_ivr = $this->get_card_numbersaving();
		echo "<table border=\"0\" align=\"left\" width=\"50%\" cellpadding=\"2\" cellspacing=\"0\">
				<tr>
					<td class=\"header-first\" align=\"center\">#</td>
					<td class=\"header-first\">Digit</td>
					<td class=\"header-first\">Inserted Date</td>
				</tr>";
		$i=1;
		foreach($digit_ivr as $id => $array_value ) 
		{
			$len_digit = strlen($array_value['AccountNo']);
			echo "<tr>
					<td class=\"rows-first\" align=\"center\"><input type=\"radio\" value=\"".$id."\" name=\"digit_arr\" id=\"digit_arr\" onclick=\"Ext.DOM.digitRB(this)\"".(  $i === 1 ?'checked':'')." ></td>
					<td class=\"rows-first\" align=\"left\">".$this->Sensor->setMaskText($array_value['AccountNo'],"",$len_digit)."</td>
					<td class=\"rows-first\" align=\"left\">".$array_value['CreatedOn']."</td>
				</tr>";
			$i++;
		}
		echo "</table>";
	}
}

private function _card_type_pay()
{
	$cardtype = array();
	if($this->havepost('Pay_Type'))
	{
		$cardtype = $this -> Customer -> CardType( $this->escPost('Pay_Type') );
	}
	else
	{
		$cardtype = $this -> Customer -> CardType();
	}
	$this -> DBForm -> jpCombo("CreditCardTypeId", 'select long',$cardtype,"",'onChange ="Ext.DOM.CardTypeChange();"');
}
private function _getBankByCardType( $cardType = null )
{
	$bank = array();
	if(!is_null($cardType))
	{
		$sql = "SELECT b.BankId,b.BankName FROM t_register_saving_bank a 
				INNER JOIN t_lk_bank b ON a.BankId = b.BankId
				WHERE a.CreditCardTypeId = ".$cardType."
				AND b.BankStatusFlag = 1";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$bank[$rows['BankId']] = $rows['BankName'];
		}
	}
	return $bank;
}

private function CmbBankByCardType()
{
	$bank_list = array();
	$auto_select = "";
	if($this->havepost('CardType'))
	{
		$cardtype = $this->escPost('CardType');
	}
	$bank_list = $this->_getBankByCardType( $cardtype );
	if(count( $bank_list ) == 0)
	{
		$bank_list = $this -> Customer -> Bank();
	}
	
	if(count( $bank_list ) == 1)
	{
		$auto_select = reset($bank_list);
	}
	$this -> DBForm -> jpCombo("PayersBankId", 'select long',$bank_list,$auto_select); 
}
private function json_pay_method()
{
	echo json_encode( $this->_get_pay_method() );
}
private function _get_pay_method()
{
	$data = array();
	$sql="SELECT a.PaymentTypeId,a.PaymentView,a.PaymentValidasi,a.PaymentResetForm,a.port_number,a.bool_payment_bank FROM t_lk_paymenttype a ";
			// echo $sql;
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		
		$data['form'][$rows['PaymentTypeId']]= $rows['PaymentView'];
		$data['validasi'][$rows['PaymentTypeId']]=$rows['PaymentValidasi'] ;
		$data['reset'][$rows['PaymentTypeId']]=$rows['PaymentResetForm'] ;
		$data['ivr'][$rows['port_number']]=$rows['PaymentTypeId'] ;
		$data['bool_bank'][$rows['port_number']]=$rows['bool_payment_bank'] ;
	}
	return $data;
}

private function _getUWQuestion()
{
	$data = array();
	if($this->havepost('ProductId'))
	{
		$product = $this->escPost('ProductId');
		$sql="	SELECT b.survey_quest_id FROM t_gn_questioner quest
				INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
				INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
				WHERE quest.product_id = ".$product."
				AND quest.questioner_flag = 1
				AND quest.questioner_type = 2
				GROUP BY b.survey_quest_id ";
				// echo $sql;
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ) 
		{
			
			$data[$rows['survey_quest_id']]=$rows['survey_quest_id'] ;
		}
	}
	echo json_encode($data);
}
private function _getExceptQuestion()
{
	$data = array();
	if($this->havepost('ProductId'))
	{
		$product = $this->escPost('ProductId');
		$sql="	SELECT b.survey_quest_id,a.prod_survey_id,d.ans_exeption_quest 
				
				FROM t_gn_questioner quest
				INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
				INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
				INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
				WHERE quest.product_id = ".$product."
				AND quest.questioner_flag = 1
				AND d.ans_rule_id = 3 ";
				// echo $sql;
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ) 
		{
			
			$data[$rows['survey_quest_id']]=$rows['ans_exeption_quest'] ;
		}
	}
	
	echo json_encode($data);
}
private function _getValidAnswer()
{
	$data = array();
	if($this->havepost('ProductId'))
	{
		$product = $this->escPost('ProductId');
		$sql="	SELECT b.survey_quest_id,a.prod_survey_id FROM t_gn_questioner quest
				INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
				INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
				INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
				WHERE quest.product_id = ".$product."
				AND quest.questioner_flag = 1
				AND d.ans_rule_id = 1 ";
				// echo $sql;
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ) 
		{
			
			$data['question'][$rows['survey_quest_id']]=$rows['survey_quest_id'] ;
			$data['answer'][$rows['survey_quest_id']][]=$rows['prod_survey_id'];
		}
	}
	
	echo json_encode($data);
}

private function _mandatory_answer()
{
	$data = array();
	if($this->havepost('ProductId'))
	{
		$product = $this->escPost('ProductId');
		$sql="	SELECT c.survey_quest_id,a.prod_survey_id,d.type_ans_id,d.ans_label,c.question_mandatory
				FROM t_gn_questioner quest 
				INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
				INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
				INNER JOIN t_lk_question_survey c ON b.survey_quest_id=c.survey_quest_id
				INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
				INNER JOIN t_lk_type_survey e ON d.type_survey_id=e.type_survey_id
				WHERE quest.product_id = ".$product."
				AND quest.questioner_flag = 1
				AND c.question_mandatory = 1
				AND e.type_from_user = 1
				ORDER BY c.survey_quest_order ASC ";
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ) 
		{
			
			$data[$rows['survey_quest_id']][$rows['prod_survey_id']]= $rows['prod_survey_id'];
		}
	}
	echo json_encode($data);
}

private function _mandatory_question()
{
	$data = array();
	if($this->havepost('ProductId'))
	{
		$product = $this->escPost('ProductId');
		$sql="	SELECT c.survey_quest_id,c.question_mandatory
				FROM t_gn_questioner quest 
				INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
				INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
				INNER JOIN t_lk_question_survey c ON b.survey_quest_id=c.survey_quest_id
				WHERE quest.product_id = ".$product."
				AND quest.questioner_flag = 1
				AND c.question_mandatory = 1
				GROUP BY c.survey_quest_id
				ORDER BY c.survey_quest_order ASC ";
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ) 
		{
			
			$data[$rows['survey_quest_id']]= $rows['survey_quest_id'];
		}
	}
	echo json_encode($data);
}


private function _get_input_key($product=0)
{
	$data = array();
	$sql="	SELECT a.prod_survey_id, b.survey_quest_id,d.type_from_user
			FROM t_gn_questioner quest
			INNER JOIN t_gn_prod_survey a ON quest.questioner_id=a.questioner_id
			INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
			INNER JOIN t_lk_type_ans_survey c ON b.type_ans_id = c.type_ans_id
			INNER JOIN t_lk_type_survey d ON c.type_survey_id=d.type_survey_id
			WHERE quest.product_id = ".$product."
			AND quest.questioner_flag = 1 ";
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		
		$data[$rows['type_from_user']][$rows['survey_quest_id']][]= $rows['prod_survey_id'];
	}
	return $data;
}
private function _save_survey()
{
	if($this->havepost('ProductId'))
	{
		$product = $this->escPost('ProductId');
		$customerid = $this -> escPost('CustomerId');
		$survey_data = $this->_get_data_survey($product);
		$question = $survey_data['question'];
		$answer_label = $survey_data['answer_label'];
		$questioner_type = $survey_data['questioner_type'];
		// $setup_answer = $survey_data['setup_answer'];
		
		/* if product have question of survey */
		if(count($question) >0)
		{
			/* get post from form survey */
			$post_survey = array();
			$input = $this->_get_input_key($product);
			$input_keyboard = $input[1];
			$input_choose = $input[0];
			// echo"<pre>";
			// print_r($input_choose);
			// echo"</pre>";
			foreach($question as $qst_id => $qst)
			{
				if( array_key_exists($qst_id,$input_keyboard) )
				{
					foreach($input_keyboard[$qst_id] as $idx => $prod_survey_id)
					{
						$param = "survey_".$qst_id."_".$prod_survey_id;
						if($this->havepost($param))
						{
							$post_survey[$qst_id][$prod_survey_id]['answer_value'] = strtoupper($this->escPost($param));
							$post_survey[$qst_id][$prod_survey_id]['quest_have_ans'] = "1";
						}
						else 
						{
							$post_survey[$qst_id][$prod_survey_id]['answer_value'] = "";
							$post_survey[$qst_id][$prod_survey_id]['quest_have_ans'] = "0";
						}
					}
				}
				else if ( array_key_exists($qst_id,$input_choose) )
				{
					$param = "survey_".$qst_id;
					if($this->havepost($param))
					{
						$post = $this->escPost($param);
						$answer = explode(",",$post);
					}
					// echo $param;
					// echo"<pre>";
					 // echo var_dump($answer);
					// echo"</pre>";
					// echo"<pre>";
					// print_r($input_choose);
					// echo"</pre>";
					
					foreach($input_choose[$qst_id] as $idx => $prod_survey_id)
					{
						
						// echo var_dump($input_choose[$qst_id]);
						// echo"<pre>";
						// echo var_dump(in_array($prod_survey_id,$answer));
						// echo"</pre>";
						if( in_array($prod_survey_id,$answer) )
						{
							// $post_survey[$qst_id][$prod_survey_id] = $prod_survey_id;
							$post_survey[$qst_id][$prod_survey_id]['answer_value'] = strtoupper($answer_label[$qst_id][$prod_survey_id]);
							$post_survey[$qst_id][$prod_survey_id]['quest_have_ans'] = "1";
						}
						else
						{
							// $post_survey[$qst_id][$prod_survey_id] = "0";
							$post_survey[$qst_id][$prod_survey_id]['answer_value'] = "";
							$post_survey[$qst_id][$prod_survey_id]['quest_have_ans'] = "0";
						}
					}
				}
			}
			
			
			/** Fail ---------
			foreach($question as $index => $qst_id)
			{
				if( array_key_exists($qst_id,$input_keyboard) )
				{
					foreach($input_keyboard[$qst_id] as $idx => $prod_survey_id)
					{
						$param = "survey_".$qst_id."_".$prod_survey_id;
						// echo "survey_".$qst_id."_".$prod_survey_id."<br />";
						if($this->havepost($param))
						{
							// $post_survey[$qst_id]['prod_survey_id'] = $prod_survey_id;
							// $post_survey[$qst_id]['answer_value'] = $this->escPost('param');
							$post_survey[$qst_id][$prod_survey_id] = $this->escPost($param);
						}
					}
					// echo "ada";
					
				}
				else 
				{
					// echo "survey_".$qst_id."<br />";
					$param = "survey_".$qst_id;
					if($this->havepost($param))
					{
						$post = $this->escPost($param);
						$answer = explode(",",$post);
						// $post_survey[$qst_id]['prod_survey_id'] = $prod_survey_id;
						// $post_survey[$qst_id]['answer_value'] = $this->escPost('param');
						foreach($answer as $ans_indx => $value)
						{
							$post_survey[$qst_id][$value] = $value;
						}
					}
				}
			}
			---------- END OF FAIL****/
			
			/*** Ok we get the answer, Save now **/
			
			foreach($post_survey as $qst_id => $array_value)
			{
				if($questioner_type[$qst_id]==UW)
				{
					$insured_id = $this->LastInsured;
				}
				else
				{
					$insured_id = "0";
				}
				$customer_survey=array(
					'customer_id'=> $customerid,
					'question_id'=> $qst_id,
					'product_id'=>$product,
					'insured_id'=>$insured_id
				);
				// echo "customer survey <br />";
				// echo "<pre>";
				// print_r($customer_survey);
				// echo "</pre>";
				$_conds = $this -> set_mysql_insert('t_gn_ans_survey', $customer_survey);
				if( $_conds )
				{
					$ans_id = $this -> get_insert_id();
					
					foreach($array_value as $prod_survey_id => $value)
					{
						$data = array (
							'ans_survey_id' => $ans_id,
							'customer_id' => $customerid,
							'insured_id' => $insured_id,
							'prod_survey_id' => $prod_survey_id,
							'quest_have_ans' => $value['quest_have_ans'],
							'answer_value' => $value['answer_value'],
							'questioner_type' => $questioner_type[$qst_id],
							'ins_datets' => date('Y-m-d H:i:s')
						);
						
						// echo "data <br />";
						// echo "<pre>";
						// print_r($data);
						// echo "</pre>";
						$_conds = $this -> set_mysql_insert('t_gn_multians_survey', $data);
					}
				}
				else
				{
					// foreach($array_value as $prod_survey_id => $value)
					// {
						// $this -> set_mysql_update('t_gn_multians_survey', 
							// array(
								// 'quest_have_ans' => $value['quest_have_ans'],
								// 'answer_value' => $value['answer_value'],
								// 'update_datets' => date('Y-m-d H:i:s')
							// ),
							// array(
								// 'customer_id'=>$customerid,
								// 'prod_survey_id'=>$prod_survey_id));
					// }
				}
			}
			
			/**Fail Save ----
			// foreach($post_survey as $qst_id => $array_value)
			// {
				// foreach($array_value as $prod_survey_id => $value)
				// {
					// $data = array (
						// 'prod_survey_id' => $prod_survey_id,
						// 'customer_id' => $this->escPost('CustomerId'),
						// 'answer_value' => $value
					// );
					// $_conds = $this -> set_mysql_insert('t_gn_answer_survey', $data);
				// }
			// }
			--- END OF FAIL SAVE ****/
			
		}
		// echo "<pre>";
		// print_r($question);
		// echo "</pre>";
		// echo "<pre>";
		// print_r($input_keyboard);
		// echo "</pre>";
		// echo "post survey <br />";
		// echo "<pre>";
		// print_r($post_survey);
		// echo "</pre>";
	}
}
function _get_ans_survey()
{
	$product = $this->escPost('ProductId');
	$customerid = $this -> escPost('CustomerId');
	
	$data = array();
	$sql = "SELECT a.question_id,b.prod_survey_id,b.answer_value FROM t_gn_ans_survey a 
			INNER JOIN t_gn_multians_survey b ON a.ans_survey_id=b.ans_survey_id
			WHERE a.customer_id = ". $customerid ." 
			AND a.product_id = ".$product." 
			AND b.quest_have_ans = 1
			ORDER BY a.question_id";
			
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		
		$data[$rows['question_id']][$rows['prod_survey_id']]= $rows['quest_have_ans'];
		$data[$rows['question_id']][$rows['prod_survey_id']]= $rows['answer_value'];
	}
	return $data;
}
function _get_questioner()
{
	if($this->havepost('ProductId') && $this->havepost('QuestinerType') )
	{
		$product = $this->escPost('ProductId');
		$questioner_type = $this->escPost('QuestinerType');
		$survey_data = $this->_get_data_survey($product,$questioner_type);
		$question = $survey_data['question'];
		$answer_label = $survey_data['answer_label'];
		$setup_answer = $survey_data['setup_answer'];
		
		// $answer_survey = $this->_get_ans_survey();
		$survey_data = array();
		/**generate view answer**/
		
		foreach($setup_answer as $index => $setup_decs)
		{
			if($setup_decs =="checkbox")
			{
				// $setup[$index] = $this->DBForm->RTListcombo( 'survey_'.$index ,'CheckAll',$answer_label[$index],array_keys($answer_survey[$index]) );
				$setup[$index] = $this->DBForm->RTListcombo( 'survey_'.$index ,'CheckAll',$answer_label[$index]);
			}
			elseif($setup_decs=="combobox")
			{
				
				$setup[$index] = $this->DBForm->RTCombo('survey_'.$index ,'select long', $answer_label[$index]);
			}
			elseif($setup_decs=="radiobutton")
			{
				
				$setup[$index] = $this->DBForm->RTRadio('survey_'.$index ,'', $answer_label[$index]);
			}
			elseif($setup_decs=="textbox")
			{
				// RTInput($name="",$css="",$value="",$label,$js="",$true=false,$maxLength=0)
				
					$setup[$index] = $this->DBForm->RTInput('survey_'.$index ,'input long',array(),$answer_label[$index]);
				
			}
			// echo"<pre>";
			// print_r($answer_label[$index]);
			// echo"</pre>";
		}
		// echo"<pre>";
		// print_r($answer_survey);
		// echo"</pre>";
		// echo"<pre>";
		// print_r($answer_label);
		// echo"</pre>";
		// RTListcombo
		// echo $tes;
		$no=1;
		echo "<table width=\"100%\" border=\"1px\" cellspacing=\"1px\" cellpadding=\"6px\">";
		foreach ($question as $idx=>$qst)
		{
			echo "<tr >
					<td rowspan=\"2\" align=\"center\">$no</td>
					<td >$qst</td>
				  </tr>
				  <tr>
					<td ><div id=\"ans_valid_{$idx}\">{$setup[$idx]}</div></td>
				  </tr>";
			$no++;
		}
		echo "</table>";
	}
}

private function _get_data_survey($product=0,$questioner_type=0)
{
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
		// echo"<pre>";
		// print_r($rows);
		// echo"</pre>";
		/*$data['question'][$rows['prod_survey_id']] = $rows['survey_question'];
		$data['label'][$rows['prod_survey_id']] = array($rows['type_ans_id']=>$rows['ans_label']);
		$data['setup'][$rows['prod_survey_id']] = array($rows['type_ans_id']=>$rows['type_survey_id']);*/
		$data['question'][$rows['survey_quest_id']]= $rows['survey_question'];
		$data['questioner_type'][$rows['survey_quest_id']]= $rows['questioner_type'];
		$data['answer_label'][$rows['survey_quest_id']][$rows['prod_survey_id']]= $rows['ans_label'];
		// $data['setup_answer'][$rows['survey_quest_id']]= $rows['type_survey_id'];
		$data['setup_answer'][$rows['survey_quest_id']]= $rows['type_survey'];
	}
	return $data;
}

function _get_valid_expire()
{
	$_conds = array('result'=>0, 'img'=>'<img src="../gambar/icon/delete.png">');
	
	if(strlen($this->escPost('expired_date')) >= 5)
	{
		$arr = explode('/',$this->escPost('expired_date'));
		
		if((int)$arr[1] >= (int)date("y"))
		{
			$_conds = array('result'=>1, 'img'=>'<img src="../gambar/icon/accept.png">');
		}
		else if((int)$arr[1] == (int)date("y"))
		{
			if((int)$arr[0] > (int)date("m")+1)
			{
				$_conds = array('result'=>1, 'img'=>'<img src="../gambar/icon/accept.png">');
			}
		}
	}
	
	echo json_encode($_conds);
}

function _get_valid_prefix()
{
	$_conds = array('result'=>0, 'img'=>'<img src="../gambar/icon/delete.png">');
	
	if(strlen($this->escPost('card_num')) >= 6)
	{
		$card_num = substr($this->escPost('card_num'),0,6); //$this->escPost('card_num')
		
		$sql = "select * from t_lk_validccprefix a where a.ValidCCPrefix = '".$card_num."'";
		$qry = $this->query($sql);
		
		if($qry->result_num_rows() > 0)
		{
			$_conds = array('result'=>1, 'img'=>'<img src="../gambar/icon/accept.png">');
		}
	}

	echo json_encode($_conds);
}

function _get_benefInsured(){
	if($this->havepost('ProductId') and $this->havepost('plan'))
	{
		$sql = "select a.ProductPlanBenefit, a.ProductPlanBenefitDesc from t_gn_productplanbenefit a
				where 1=1
				AND a.ProductId = '".$this->escPost('ProductId')."'
				AND a.ProductPlan = '".$this->escPost('plan')."'
				AND a.ProductPlanBenefitStatusFlag = 1";
		$qry = $this->query($sql);

		echo "<table width=\"100%\" border=\"1px\" cellspacing=\"1px\" cellpadding=\"6px\">
			 <tr>
					<td align=\"center\" width=\"15%\">Plan Benefit</td>
					<td align=\"center\" width=\"95%\">Plan Description</td>
			 </tr>";
		foreach($qry->result_rows() as $Qry2){
			echo "<tr>
					<td>".($Qry2[0])."</td>
					<td>".($Qry2[1])."</td>
				  </tr>
			";
		}
		echo "</table>";
	}
}

function _get_payer_age()
{
	$_conds = array('success' => 0, 'personal_age'=> 0 );
	$_DOB  = $this -> escPost('DOB');
	
	if( !is_null($_DOB) )
	{
		if( class_exists('DateFactory'))
		{
			$_date = $this -> Date -> set_date_diff( $_DOB, date('Y-m-d') );
			$_total_years = round(($_date['months_total']/12 ),1);
			if( $_total_years!='' AND $_total_years > 0 )
			{
				$Range =  self::_get_range_date( $this ->escPost('ProductId'),$this -> escPost('GroupPremi') );
				if( $_total_years >= $Range['MINIMUM'] AND  $_total_years <= $Range['MAXIMUM'] ){
					$_conds = array('success' =>1, 'personal_age'=> $_total_years );
				}
				else{
					$_conds = array('success' =>0, 'personal_age'=> 0,'Error' => 'Range Must Be  ' . $Range['MINIMUM'] .' && ' . $Range['MAXIMUM'] );
				}
			}
			else{
				$_conds = array('success' =>1, 'personal_age'=> 0,'Error' => 'Age not in range ');
			}
		}
	}
	
	echo json_encode($_conds);
}

function _update_qc()
{
	$_conds = array('result' => 0);
	
	$sql_upd['QCStatus'] = $this->escPost('StatusId');
	$sql_whe['InsuredId'] = $this->escPost('InsuredId');
	
	if( $this->set_mysql_update('t_gn_insured',$sql_upd,$sql_whe) )
	{
		$_conds = array('result' => 1);
	}
	
	echo json_encode($_conds);
}

function _get_product_benef()
{
	$_conds = array('result' => 0);
	
	$sql = "select a.ProductBenefFlag from t_gn_product a where a.ProductId = '".$this->escPost('ProductId')."'";
	$qry = $this->query($sql);
	
	if($qry->result_num_rows() > 0)
	{
		$_conds = array('result' => 1, 'value' => (INT)$qry->result_get_value('ProductBenefFlag'));
	}
	
	echo json_encode($_conds);
}

/* @ def 	: _get_member_polis()
 *
 * @ param  : jika dependent tidak berdiri sendiri && dan terikat oleh holder / spouse 
 * @ author : razaki team 
 */ 

 private function _single_policy()
 {
	$_conds = false;
	$polis = self::_get_singgle_polis();
	$data = array_values($polis);
	if( is_array($data) ) 
	{
		$_conds = $data[0];
	}
	
	return $_conds;
 }
 
/* @ def 	: _get_member_polis()
 *
 * @ param  : jika dependent tidak berdiri sendiri && dan terikat oleh holder / spouse 
 * @ author : razaki team 
 */ 
 
function _get_range_date($ProductId=0, $GroupPremi=0)
{
	$_conds = array('MINIMUM' =>0, 'MAXIMUM' => 0 );
	
	$sql = " SELECT MIN(a.ProductPlanAgeStart) as min_age, MAX(a.ProductPlanAgeEnd) as max_age  
			 FROM t_gn_productplan a 
			 WHERE a.ProductId='$ProductId'
			 AND a.PremiumGroupId='$GroupPremi'";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$_conds = array
			(
				'MINIMUM' => ( $qry -> result_get_value('min_age') ? $qry -> result_get_value('min_age') : 0 ), 
				'MAXIMUM' => ( $qry -> result_get_value('max_age') ? $qry -> result_get_value('max_age') : 0 ) 
			);
	}	
	
	return $_conds;
}

/*
 * @ def 		: _get_singgle_polis 
 *
 * @ params 	: if true Policy exist else no exist then you can create polis 
 * @ return 	: boolean
 */
 
function _get_policy()
{
	$_conds = array();
	$_polis = self::_get_singgle_polis();
	if( !is_null($_polis) AND is_array($_polis ) )
	{
		$_conds = $_polis;
	}	
	
	$this -> DBForm -> jpCombo('InsuredPolicyNumber','select long',$_conds,($this->escPost('SplitPolis')?'new':''),"onchange=Ext.DOM.LoadSamePlan(this)",($this->escPost('SplitPolis')?'0':'1'));
}

function _get_group_premi()
{
	$sql = "select distinct(a.PremiumGroupId) as PremiumGroupId, b.PremiumGroupCode, b.PremiumGroupDesc from t_gn_productplan a
			left join t_lk_premiumgroup b on a.PremiumGroupId = b.PremiumGroupId
			where a.ProductId = '".$this->escPost('ProductId')."'";
			
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$datas[$rows['PremiumGroupId']] = $rows['PremiumGroupDesc'];
	}
	
	$this -> DBForm -> jpCombo('InsuredGroupPremi','select long', $datas,null, "onchange=Ext.DOM.ClearInsured();" );
}

function _get_plan_type()
{
	$sql = "select distinct(a.ProductPlan) as ProductPlan, a.ProductPlanName, if(b.PlanNameAlias<>'',b.PlanNameAlias,a.ProductPlanName) as PlanNameAlias from t_gn_productplan a
			left join t_lk_plan_name b on a.ProductPlan = b.PlanSection
			where a.ProductId = '".$this->escPost('ProductId')."'";
			
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$datas[$rows['ProductPlan']] = $rows['PlanNameAlias'];
	}
	
	$this -> DBForm -> jpCombo('InsuredPlanType','select long', $datas, null,"OnChange=getPremi(this);");
}

function _get_pay_mode()
{
	$sql  = "select distinct(a.PayModeId) as PayModeId, b.PayMode, b.PayModeCode from t_gn_productplan a
			left join t_lk_paymode b on a.PayModeId = b.PayModeId
			where a.ProductId = '".$this->escPost('ProductId')."'";
	
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$datas[$rows['PayModeId']] = $rows['PayMode'];
	}
	
	$this -> DBForm -> jpCombo('InsuredPayMode','select long', $datas, null,"OnChange=getPremi(this);");
}

function getExistingPlan()
{
	$_conds = array('result'=>0);
		
	$sql = "select c.PayModeId, c.ProductPlan from t_gn_policyautogen a
			left join t_gn_policy b on a.PolicyNumber = b.PolicyNumber
			left join t_gn_productplan c on b.ProductPlanId = c.ProductPlanId
			where a.PolicyNumber = '".$this->escPost('PolicyNum')."'";
			
	$qry = $this->query($sql);
	
	if($qry->result_num_rows() > 0)
	{
		$_conds = array('result'=>1,'paymode'=>$qry->result_get_value('PayModeId'),'plan'=>$qry->result_get_value('ProductPlan'));
	}
	
	echo json_encode($_conds);
}

/*
 * @ def 		: _get_singgle_polis 
 *
 * @ params 	: if true Policy exist else no exist then you can create polis 
 * @ return 	: boolean
 */
 
private function _get_singgle_polis()
{
	$_conds = false;
	
	$ProductId  = ( $this -> havepost('ProductId') ? $this -> escPost('ProductId') : null );
	$CustomerId = ( $this -> havepost('CustomerId') ? $this -> escPost('CustomerId') : null );
	
	if( !is_null($ProductId) AND !is_null($CustomerId) )
	{
		$sql = " SELECT a.PolicyNumber FROM t_gn_policyautogen a WHERE a.ProductId='$ProductId' AND a.CustomerId = '$CustomerId'";
		$qry = $this -> query($sql);
		
		$_conds['new'] = 'New Policy';
		if( !$qry -> EOF() )
		{	
			foreach( $qry -> result_assoc() as $rows )
			{
				$_conds[$rows['PolicyNumber']] = $rows['PolicyNumber'];
			}
		}
	}
	
	return $_conds;
}
 
/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function _get_personal_age()
{
	$_conds = array('success' => 0, 'personal_age'=> 0 );
	$_DOB  = $this -> escPost('DOB');
	
	if( !is_null($_DOB) )
	{
		if( class_exists('DateFactory'))
		{
			$_date = $this -> Date -> set_date_diff( $_DOB, date('Y-m-d') );
			
			$_total_years = round(($_date['months_total']/12 ),1);
			if( $_total_years!='' AND $_total_years > 0 )
			{
				$Range =  self::_get_range_date( $this ->escPost('ProductId'),$this -> escPost('GroupPremi') );
				if( $_total_years >= $Range['MINIMUM'] AND  $_total_years <= $Range['MAXIMUM'] ){
					$_conds = array('success' =>1, 'personal_age'=> $_total_years );
				}
				else{
					$_conds = array('success' =>0, 'personal_age'=> 0,'Error' => 'Range Must Be  ' . $Range['MINIMUM'] .' && ' . $Range['MAXIMUM'] );
				}
			}
			else{
				$_conds = array('success' =>1, 'personal_age'=> 0,'Error' => 'Age not in range ');
			}
		}	
	}
	echo json_encode($_conds);
}

/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function _get_personal_premi()
{
 
 $totals_premi_rupiah = 0;
 $_argument = array();
 $_conds  = array('success'=> 0, 'personal_premi'=> 0 );
 
 if( class_exists('Customer'))
 {
		$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
		if( $_exist_group_premi ) {
			$_argument['PremiumGroupId'] = (INT)$this -> escPost('GroupPremi'); 
		}
		
		$_argument['PayModeId']	  = ( $this -> havepost('PayModeId')?$this -> escPost('PayModeId'):false ); 
		$_argument['ProductPlan'] = ( $this -> havepost('PlanTypeId')?$this -> escPost('PlanTypeId'):false ); 
		$_argument['start_age']	  = ( $this -> havepost('PersonalAge')?(int)$this -> escPost('PersonalAge'):false );
		$_argument['GenderId'] = $this->escPost('InsuredGenderId');
		// print_r($_argument);
		
		// return by json data 
		$totals_premi_rupiah = self::_self_personal_premi( $this -> escPost('ProductId'),$_argument);
		$_conds  = array
		(
			'success'=> 1, 
			'personal_premi' => formatRupiah($totals_premi_rupiah['ProductPlanPremium']) 
		);
 }
			
 echo json_encode( $_conds );

}

/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

function _get_payer_data()
{
	$_data = array();
	$validXsell = "FALSE";
	$sql = "select * from t_gn_customer a where a.CustomerId='". $this -> escPost('CustomerId') ."'";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$rows = $qry -> result_first_assoc();
		// var_dump($rows);
		if( count($rows) > 1)
		{
			if($rows['CustomerDOB']=='0000-00-00'){
				$set_diff['years']="0";
			}
			else{
				// $set_diff = $this -> Date -> set_date_diff( $this -> formatDateId($rows['CustomerDOB']), date('Y-m-d') );
				$_date = $this -> Date -> set_date_diff( $this -> formatDateId($rows['CustomerDOB']), date('Y-m-d') );
				$set_diff['years'] = round(($_date['months_total']/12 ),1);
			}
			if(!is_null($rows['CustomerCreditCardNum']) && !empty($rows['CustomerCreditCardNum']) && $rows['CustomerCreditCardNum'] != "0" )
			{
				$validXsell = "TRUE";
			}
			else if( !is_null($rows['accountnumber']) && !empty($rows['accountnumber']) && $rows['accountnumber'] != "0" )
			{
				$validXsell = "TRUE";
			}
			$data = array
					( 
						'PayerSalutationId' 		=> $rows['SalutationId'],
						'PayerFirstName' 			=> $rows['CustomerFirstName'],
						'PayerLastName' 			=> $rows['CustomerLastName'],
						'PayerGenderId' 			=> $rows['GenderId'],
						'PayerAddrType' 			=> '0',
						'PayerDOB'					=> ($rows['CustomerDOB']=='0000-00-00'?'00-00-0000':$this -> formatDateId($rows['CustomerDOB'])), 
						'PayerAge'					=> $set_diff['years'],
						'PayerAddressLine1'			=> $rows['CustomerAddressLine1'],
						'PayerAddressLine2'			=> $rows['CustomerAddressLine2'],
						'PayerAddressLine3'			=> $rows['CustomerAddressLine3'],
						'PayerAddressLine4'			=> $rows['CustomerAddressLine4'],
						'PayerIdentificationTypeId'	=> $rows['IdentificationTypeId'],
						'PayerIdentificationNum' 	=> $rows['CustomerIdentificationNum'],
						'PayerMobilePhoneNum'		=> $rows['CustomerMobilePhoneNum'],
						'PayerMobilePhoneNum2'		=> $rows['CustomerMobilePhoneNum2'],
						'PayerCity'					=> $rows['CustomerCity'],
						'PayerHomePhoneNum'			=> $rows['CustomerHomePhoneNum'],
						'PayerHomePhoneNum2'		=> $rows['CustomerHomePhoneNum2'],
						'PayerZipCode'				=> $rows['CustomerZipCode'],
						'PayerOfficePhoneNum'		=> $rows['CustomerWorkPhoneNum'],
						'PayerOfficePhoneNum2'		=> $rows['CustomerWorkPhoneNum2'],
						'PayerProvinceId'			=> $rows['ProvinceId'],
						'CreditCardTypeId'			=> $rows['CardTypeId'],
						'PayerEmail' 				=> $rows['CustomerEmail'],
						'PayerCreditCardNum'		=> ($rows['CustomerCreditCardNum'] ? $rows['CustomerCreditCardNum'] : ''),
						'PayerFaxNum'				=> '',
						'PayerCreditCardExpDate'	=> $rows['CustomerCreditCardExpDate'],
						'PayersBankId'				=> '0',
						'PayerXsellbank'			=> $rows['Xsellbank'],
						'PayerValidXsell'			=> $validXsell
				);
		}
	}
	echo json_encode($data);
}

function checkBenef()
{
	$_conds = array('result'=>0);
	
	$sql = "select a.ProductBenefFlag from t_gn_product a where a.ProductId = '".$this->escPost('ProductId')."'";
	$qry = $this->query($sql);
	
	if($qry->result_get_value('ProductBenefFlag'))
	{
		$_conds = array('result'=>1);
	}
	
	echo json_encode($_conds);
}

 function validInsured()
 {
	$_conds = array('result'=>0);
		
	$sql = "select a.PolicyNumber, a.MemberGroup from t_gn_policyautogen a
			where 1=1
			and a.CustomerId = '".$this->escPost('CustomerId')."'
			and a.ProductId = '".$this->escPost('ProductId')."'
			and a.MemberGroup = '".$this->escPost('MemberGroup')."'";
			
	$qry = $this->query($sql);
	
	if($qry->result_num_rows() > 0)
	{
		$_conds = array("result"=>1,"PolicyNumber"=>$qry->result_get_value('PolicyNumber'),"MemberGroup"=>(INT)$qry->result_get_value('MemberGroup'));
	}
	
	echo json_encode($_conds);
 }
 
/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
function _get_splite_data()
{
	$_conds = array('success'=> 0, 'error'=> '');
	
	$_ProductId = $this -> escPost('ProductId');
	
	$qry = $this -> query("select a.PrefixMethod from t_gn_productprefixnumber a where a.ProductId='$_ProductId'");
	//echo "select a.PrefixMethod from t_gn_productprefixnumber a where a.ProductId='$_ProductId'";
	if( !$qry -> EOF() )
	{
		$qrycategory = $this->query("SELECT b.`product_category_code` FROM t_gn_product a INNER JOIN t_gn_product_category b ON a.`product_category_id` = b.`product_category_id` WHERE a.`ProductId` = '$_ProductId'");
            
		if(!$qrycategory->EOF()){
			$category = $qrycategory->result_singgle_value();
		}
		$_conds = array
		(
			'success'=> 1, 
			'pecah'=> $qry -> result_singgle_value(),
			'category' => $category
		);
	}
	
	echo json_encode($_conds);
	
}

/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 function _self_personal_premi( $ProductId=null, $_argument =null )
 {
	$_personal_premi = 0;
	
	if( !is_null($_argument) AND is_array($_argument) AND !is_null($ProductId) )
	{
		//$sql = " SELECT a.ProductPlanPremium, a.ProductPlanId FROM t_gn_productplan a  WHERE 1=1 ";
		if($_argument['GenderId'] == 1){
                    $sql = " SELECT (CASE a.`ProductPlanGender` WHEN 1 THEN a.ProductPlanPremiumMale ELSE a.ProductPlanPremium END) AS ProductPlanPremium, a.ProductPlanId FROM t_gn_productplan a  WHERE 1=1 ";
        }else{
                    $sql = " SELECT (CASE a.`ProductPlanGender` WHEN 1 THEN a.ProductPlanPremiumFemale ELSE a.ProductPlanPremium END) AS ProductPlanPremium, a.ProductPlanId FROM t_gn_productplan a  WHERE 1=1 ";
        }
		if( !is_null($ProductId) AND $ProductId!=FALSE ){
			$sql.= " AND a.ProductId= '". $ProductId ."'";	
		}
		
		if( isset($_argument['PayModeId']) 
			AND !is_null($_argument['PayModeId']) 
			AND $_argument['PayModeId']!=FALSE )
		{
			$sql.= " AND a.PayModeId= '". $_argument['PayModeId'] ."'";	
		}
		
		if( isset($_argument['ProductPlan']) 
			AND !is_null($_argument['ProductPlan']) 
			AND $_argument['ProductPlan']!=FALSE )
		{
			$sql.= " AND a.ProductPlan= '". $_argument['ProductPlan'] ."'";	
		}
		
		if( isset($_argument['PremiumGroupId']) 
			AND !is_null($_argument['PremiumGroupId']) 
			AND $_argument['PremiumGroupId']!=FALSE )
		{
			$sql.= " AND a.PremiumGroupId= '". $_argument['PremiumGroupId'] ."'";	
		}
		
		if( $_argument['start_age']!='' )
		{
			$sql.= " AND {$_argument['start_age']} BETWEEN a.ProductPlanAgeStart and a.ProductPlanAgeEnd ";
		}
		
		$qry = $this -> query($sql);
		
		if( $qry -> result_num_rows()==1 )
		{
			$_personal_premi = array
			(
				'ProductPlanPremium' => (INT)$qry -> result_get_value('ProductPlanPremium'),
				'ProductPlanId' => (INT)$qry -> result_get_value('ProductPlanId')
			);
		}	
	}
	// echo $sql;
	// print_r($_personal_premi);
	
	return $_personal_premi;
 }


/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 function _set_save_data()
 {
	$_conds = false;
	if( $this -> havepost('CustomerId') ) 
	{
		if( !(INT)$this -> escPost('PecahPolicy') ) 
		{
			$_conds = self::_set_save_one_to_one();
			if( is_array($_conds) )
			{
				self::_set_save_data_payer();
			}	
		}
		else
		{
			$_conds = self::_set_save_one_to_many();
			if( is_array($_conds) )
			{
				self::_set_save_data_payer();	
			}	
		}
		
		$this->_save_survey();
	}
	
	echo json_encode($_conds);
 }
 
/*
 * @ def 		: _set_save_one_to_many 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

private function _set_save_one_to_one()
{
	$conds = array("success"=>0,"polis" => '');
	$Polis = self::_get_class_policy();
	
	if( class_exists('Polis')!=FALSE )
	{
		if( $this -> havepost('InsuredPolicyNumber')!=TRUE or $this->escPost('InsuredPolicyNumber')=='new')
		{
			$Polis -> _set_polis_number( $this -> escPost('ProductId'),null );
			//$Polis -> _set_polis_number( $this -> escPost('ProductId'), 'N' ); with 'N' sufix policynumber
			$PolicyNumber = $Polis -> _get_polis_number();
			$PolicyLastId = $Polis -> _get_last_number();
			
			$Data = array 
			(
				'ProductId' => $this -> escPost('ProductId'),
				'CustomerId' => $this -> escPost('CustomerId'),
				'MemberGroup' => $this -> escPost('InsuredGroupPremi'), 
				'PolicyNumber' => $PolicyNumber, 	
				'PolicyLastNumber' => $PolicyLastId				
			);
			
			$_conds = $this -> set_mysql_insert('t_gn_policyautogen', $Data);	
			if( $_conds ) 
			{
				$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
				$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
				$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
				$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?(int)$this -> escPost('InsuredAge'):false );
				$_argument['GenderId'] = $this->escPost('InsuredGenderId');
				$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
				if( $this -> set_mysql_insert('t_gn_policy',array
				(
					'ProductPlanId'=> $_totals['ProductPlanId'],
					'Premi' => (INT) $_totals['ProductPlanPremium'],
					'PolicyNumber'=> $PolicyNumber,
					'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
					'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
				)))
				{
					self::_set_save_insured ( array (
						'PremiumGroupId' => $this -> escPost('InsuredGroupPremi'),
						'CustomerId' => $this -> escPost('CustomerId'),
						'PolicyId' => $this -> get_insert_id(),
						'SalutationId' => $this -> escPost('InsuredSalutationId'),
						'GenderId' => $this -> escPost('InsuredGenderId'),
						'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
						'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
						'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
						'CreatedById' => $this -> getSession('UserId'),
						'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
						'InsuredLastName' => $this -> escPost('InsuredLastName'),
						'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
						'InsuredAge' => $this -> escPost('InsuredAge'),
						'InsuredCreatedTs' => date('Y-m-d H:i:s')
					));
					
					$InsuredId =  $this -> get_insert_id();
					if( $InsuredId ){
						$this->LastInsured = $InsuredId;
						self::_set_save_benefiecery($InsuredId);
					}
					else
					{
						$this->LastInsured = "0";
					}
					
					$conds = array("success" => 1, "polis" => $PolicyNumber );
				}
			}	
			else
				$conds = array("success" => 2, "polis" => $PolicyNumber );
		}
		else // jika mau di ikutkan 
		{
			$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
			$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
			$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
			$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
			$_argument['GenderId'] = $this->escPost('InsuredGenderId');
			$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			
			if( $this -> set_mysql_insert('t_gn_policy',array
			(
				'ProductPlanId'=> $_totals['ProductPlanId'],
				'Premi' => (INT) $_totals['ProductPlanPremium'],
				'PolicyNumber'=> $this -> escPost('InsuredPolicyNumber'),
				'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
				'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
			)))
			{
				self::_set_save_insured ( array (
					'PremiumGroupId' => $this -> escPost('InsuredGroupPremi'),
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('InsuredSalutationId'),
					'GenderId' => $this -> escPost('InsuredGenderId'),
					'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
					'InsuredLastName' => $this -> escPost('InsuredLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
					'InsuredAge' => $this -> escPost('InsuredAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
				
				$InsuredId =  $this -> get_insert_id();
				if( $InsuredId )
				{
					$this->LastInsured = $InsuredId;
					self::_set_save_benefiecery($InsuredId);
				}
				else
				{
					$this->LastInsured = "0";
				}
				$conds = array("success" => 1, "polis" => $this -> escPost('InsuredPolicyNumber') );
			}
		}
	}
	
	return $conds;
}
  
 private function validExistPolicy( $param = array() )
 {
	foreach($param as $key => $value)
	{
		if($key != "new")
		{
			$datas[] = $value;
		}
	}
	
	return $datas;
 }
 
/*
 * @ def 		: _set_save_one_to_many 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function checkPolicy()
{
	$sql = "select * from t_gn_policyautogen a where a.PolicyNumber = '".$this -> PolicyNo."'";
	$qry = $this->query($sql);
	
	if($qry->result_num_rows() > 0)
	{
		return true;
	}
	else{
		return false;
	}
}
 
private function _set_save_one_to_many()
{
	$conds = array("success"=>0,"polis" => '');
	// print_r($_REQUEST);
	$Polis = self::_get_class_policy();
  
	if( class_exists('Polis')!=FALSE ) 
	{
		if($this->havepost('InsuredPolicyNumber') && $this->escPost('InsuredPolicyNumber')!= 'new')
		{
			$conds = $this->proses_save($this->escPost('InsuredPolicyNumber'));
		}
		else{
			$Polis -> _set_polis_number( $this -> escPost('ProductId'),null);
			$PolicyNumber = $Polis -> _get_polis_number();
			$PolicyLastId = $Polis -> _get_last_number();
			
			$conds = $this->proses_save($PolicyNumber,$PolicyLastId);
		}
	}
	
	return $conds;
}

private function proses_save($polNum,$LastId = 0)
{
	$this -> PolicyNo = $polNum;
	$this -> PolicyLast = $LastId;
	
	switch( $this -> escPost('InsuredGroupPremi')) 
	{
		case HOLDER : return $this->saveHolder(); 				break;
		case SPOUSE : return $this->saveSpouse(); 				break;
		case DEPEND : return $this->saveDepend(); 				break;
		default 	: return array("success"=>0,"polis" => ''); break;
	}
}

function saveHolder()
{
	$kondisi = array("success"=>0,"polis" => '');
	
	if( !$this->checkPolicy() )
	{
		// baru, tidak ikut policy siapapun
		$Data = array 
		(
			'ProductId' => $this -> escPost('ProductId'),
			'CustomerId' => $this -> escPost('CustomerId'),
			'MemberGroup' => $this -> escPost('InsuredGroupPremi'), 
			'PolicyNumber' => $this -> PolicyNo, 	
			'PolicyLastNumber' => $this -> PolicyLast			
		);
		
		$_conds = $this -> set_mysql_insert('t_gn_policyautogen', $Data);
		
		if( $_conds )
		{
			
			$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
			$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
			$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
			$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?(int)$this -> escPost('InsuredAge'):false );
			$_argument['GenderId'] = $this->escPost('InsuredGenderId');
			$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',array
			(
				'ProductPlanId'=> $_totals['ProductPlanId'],
				'Premi' => (INT) $_totals['ProductPlanPremium'],
				'PolicyNumber'=> $this -> PolicyNo,
				'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
				'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
			))){
				self::_set_save_insured ( array (
					'PremiumGroupId' => HOLDER,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('InsuredSalutationId'),
					'GenderId' => $this -> escPost('InsuredGenderId'),
					'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
					'InsuredLastName' => $this -> escPost('InsuredLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
					'InsuredAge' => $this -> escPost('InsuredAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
				
				$InsuredId =  $this -> get_insert_id();
				if( $InsuredId ){
					$this->LastInsured = $InsuredId;
					self::_set_save_benefiecery($InsuredId);
				}
				else
				{
					$this->LastInsured = "0";
				}
				$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
			}
		}
		else{
			$kondisi = array("success"=>2,"polis" => $this -> PolicyNo);
		}
	}
	
	return $kondisi;
}

function saveSpouse()
{
	$kondisi = array("success"=>0,"polis" => '');
	
	if( $this->checkPolicy() )
	{
		$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
		$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
		$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
		$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?(int)$this -> escPost('InsuredAge'):false );
		$_argument['GenderId'] = $this->escPost('InsuredGenderId');
		// print_r($_argument);
		$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
		if( $this -> set_mysql_insert('t_gn_policy',array
		(
			'ProductPlanId'=> $_totals['ProductPlanId'],
			'Premi' => (INT) $_totals['ProductPlanPremium'],
			'PolicyNumber'=> $this -> PolicyNo,
			'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
			'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
		))){
			self::_set_save_insured ( array (
					'PremiumGroupId' => SPOUSE,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('InsuredSalutationId'),
					'GenderId' => $this -> escPost('InsuredGenderId'),
					'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
					'InsuredLastName' => $this -> escPost('InsuredLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
					'InsuredAge' => $this -> escPost('InsuredAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
				
			$InsuredId =  $this -> get_insert_id();
			if( $InsuredId ){
				$this->LastInsured = $InsuredId;
				self::_set_save_benefiecery($InsuredId);
			}
			else
			{
				$this->LastInsured = "0";
			}
			
			$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
		}
	}
	else{
		$Data = array 
		(
			'ProductId' => $this -> escPost('ProductId'),
			'CustomerId' => $this -> escPost('CustomerId'),
			'MemberGroup' => $this -> escPost('InsuredGroupPremi'), 
			'PolicyNumber' => $this -> PolicyNo, 
			'PolicyLastNumber' => $this -> PolicyLast		
		);
		
		$_conds = $this -> set_mysql_insert('t_gn_policyautogen', $Data);
		
		if( $_conds )
		{
			$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
			$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
			$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
			$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?(int)$this -> escPost('InsuredAge'):false );
			$_argument['GenderId'] = $this->escPost('InsuredGenderId');
			$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',array
			(
				'ProductPlanId'=> $_totals['ProductPlanId'],
				'Premi' => (INT) $_totals['ProductPlanPremium'],
				'PolicyNumber'=> $this -> PolicyNo,
				'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
				'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
			))){
				self::_set_save_insured ( array (
						'PremiumGroupId' => SPOUSE,
						'CustomerId' => $this -> escPost('CustomerId'),
						'PolicyId' => $this -> get_insert_id(),
						'SalutationId' => $this -> escPost('InsuredSalutationId'),
						'GenderId' => $this -> escPost('InsuredGenderId'),
						'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
						'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
						'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
						'CreatedById' => $this -> getSession('UserId'),
						'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
						'InsuredLastName' => $this -> escPost('InsuredLastName'),
						'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
						'InsuredAge' => $this -> escPost('InsuredAge'),
						'InsuredCreatedTs' => date('Y-m-d H:i:s')
					));
					
				$InsuredId =  $this -> get_insert_id();
				if( $InsuredId ){
					$this->LastInsured = $InsuredId;
					self::_set_save_benefiecery($InsuredId);
				}
				else
				{
					$this->LastInsured = "0";
				}
				
				$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
			}
		}
		else{
			$kondisi = array("success"=>2,"polis" => $this -> PolicyNo);
		}
		
	}
	
	return $kondisi;
}

function saveDepend()
{
	$kondisi = array("success"=>0,"polis" => '');
	
	if( $this->checkPolicy() )
	{
		$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
		$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
		$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
		$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
		$_argument['GenderId'] = $this->escPost('InsuredGenderId');
		$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
		if( $this -> set_mysql_insert('t_gn_policy',array
		(
			'ProductPlanId'=> $_totals['ProductPlanId'],
			'Premi' => (INT) $_totals['ProductPlanPremium'],
			'PolicyNumber'=> $this -> PolicyNo,
			'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
			'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
		))){
			self::_set_save_insured ( array (
					'PremiumGroupId' => DEPEND,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('InsuredSalutationId'),
					'GenderId' => $this -> escPost('InsuredGenderId'),
					'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
					'InsuredLastName' => $this -> escPost('InsuredLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
					'InsuredAge' => $this -> escPost('InsuredAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
			
			$InsuredId =  $this -> get_insert_id();
			if( $InsuredId ){
				$this->LastInsured = $InsuredId;
				self::_set_save_benefiecery($InsuredId);
			}
			else
			{
				$this->LastInsured = "0";
			}
				
			$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
		}
	}
	else{
		$Data = array 
		(
			'ProductId' => $this -> escPost('ProductId'),
			'CustomerId' => $this -> escPost('CustomerId'),
			'MemberGroup' => $this -> escPost('InsuredGroupPremi'), 
			'PolicyNumber' => $this -> PolicyNo, 	
			'PolicyLastNumber' => $this -> PolicyLast			
		);
		
		$_conds = $this -> set_mysql_insert('t_gn_policyautogen', $Data);
		
		if( $_conds )
		{
			$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
			$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
			$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
			$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
			$_argument['GenderId'] = $this->escPost('InsuredGenderId');
			$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',array
			(
				'ProductPlanId'=> $_totals['ProductPlanId'],
				'Premi' => (INT) $_totals['ProductPlanPremium'],
				'PolicyNumber'=> $this -> PolicyNo,
				'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
				'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
			))){
				self::_set_save_insured ( array (
						'PremiumGroupId' => DEPEND,
						'CustomerId' => $this -> escPost('CustomerId'),
						'PolicyId' => $this -> get_insert_id(),
						'SalutationId' => $this -> escPost('InsuredSalutationId'),
						'GenderId' => $this -> escPost('InsuredGenderId'),
						'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
						'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
						'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
						'CreatedById' => $this -> getSession('UserId'),
						'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
						'InsuredLastName' => $this -> escPost('InsuredLastName'),
						'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
						'InsuredAge' => $this -> escPost('InsuredAge'),
						'InsuredCreatedTs' => date('Y-m-d H:i:s')
					));
				
				$InsuredId =  $this -> get_insert_id();
				if( $InsuredId ){
					$this->LastInsured = $InsuredId;
					self::_set_save_benefiecery($InsuredId);
				}
				else
				{
					$this->LastInsured = "0";
				}
					
				$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
			}
		}
		else{
			$kondisi = array("success"=>2,"polis" => $this -> PolicyNo);
		}
	}
	
	return $kondisi;
}

/*
 * @ def 		: _set_save_one_to_many 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
function _get_Detail()
{
	$_conds = array('success'=>0, 'data' => array());
	
	$sql = "select a.*, d.* from t_gn_insured a left join t_gn_policy b on a.PolicyId=b.PolicyId
			left join t_gn_policyautogen c on c.PolicyNumber=b.PolicyNumber
			left join t_gn_productplan d on b.ProductPlanId=d.ProductPlanId
			where c.PolicyNumber='".$this -> escPost('InsuredPolicyNumber') ."' 
			AND a.PremiumGroupId='" .$this -> escPost('GroupPremi'). "'";
	//echo $sql;		
	$qry = $this -> query($sql);
	$rows = $qry -> result_first_assoc();
	if( count($rows) > 0 )
	{
		$_data['IdentificationTypeId'] = $rows['IdentificationTypeId'];
		$_data['InsuredIdentificationNum'] = $rows['InsuredIdentificationNum'];
		$_data['RelationshipTypeId'] = $rows['RelationshipTypeId'];
		$_data['SalutationId'] = $rows['SalutationId'];
		$_data['InsuredFirstName'] = $rows['InsuredFirstName'];
		$_data['InsuredLastName'] = $rows['InsuredLastName'];
		$_data['GenderId'] = $rows['GenderId'];
		$_data['InsuredDOB'] = $this -> formatDateId($rows['InsuredDOB']);
		$_data['InsuredAge'] = $rows['InsuredAge'];
		$_data['PayModeId'] = $rows['PayModeId'];
		$_data['ProductPlan'] = $rows['ProductPlan'];
		$_data['ProductPlanPremium'] = formatRupiah($rows['ProductPlanPremium']);
		
		if( is_array($_data))
		{
			$_conds = array('success'=>1, 'data' => $_data );
		}	
	}
	
	echo json_encode($_conds);
}

/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
private function _set_save_benefiecery( $InsuredId = null )
{
 $BoxBenef = ( $this -> havepost('BenefBox') ? explode(",",$this -> escPost('BenefBox')) : false );
 if(($BoxBenef!=FALSE) AND (is_array($BoxBenef)) )
 {
	foreach( $BoxBenef as $k => $pos )
	{
		$this -> set_mysql_insert('t_gn_beneficiary',
			array
			(
				'InsuredId' => $InsuredId,
				'CustomerId' => $this -> escPost("CustomerId"), 
				'SalutationId' => $this -> escPost("BenefSalutationId_$pos"), 
				'RelationshipTypeId' => $this -> escPost("BenefRelationshipTypeId_$pos"),
				'BeneficiaryFirstName'=> $this -> escPost("BenefFirstName_$pos"),
				'BeneficiaryLastName' => $this -> escPost("BenefLastName_$pos"),
				'BeneficieryPercentage'=> $this -> escPost("BenefPercentage_$pos"),
				'GenderId'=> $this -> escPost("BenefGenderId_$pos"),
				'BeneficiaryDOB'=> $this -> formatDateEng($this -> escPost("BenefDOB_$pos")),
				'BeneficiaryCreatedTs'=> date('Y-m-d H:i:s')
			));	
	}
 }
}


/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 private function _set_save_data_payer()
 {
	$_conds = false;
	$payment_cc = "1";
	$payment_saving = "2";
	$paytypeid = NULL;
	$cc_saving_num = NULL;
	$expire_cc_saving = "/";
	$bank_id = null;
	$credit_card_type_id = null;
	if($this -> havepost('PayerPaymentType'))
	{
		$paytypeid = $this -> escPost('PayerPaymentType');
		if($paytypeid==$payment_cc)
		{
			$cc_saving_num = $this -> escPost('PayerCreditCardNum');
			$expire_cc_saving = $this -> escPost('PayerCreditCardExpDate');
		}
		else if($paytypeid==$payment_saving)
		{
			$cc_saving_num = $this -> escPost('SavingAccount');
			$expire_cc_saving = "/";
		}
		$bank_id = $this -> escPost('PayersBankId');
		$credit_card_type_id = $this -> escPost('CreditCardTypeId');
	}
	// var_dump($this -> havepost('digit_arr'));
	else if($this -> havepost('digit_arr'))
	{
		// var_dump($this -> havepost('IvrPayMethod'));
		if($this -> havepost('IvrPayMethod'))
		{
			$paymethod = $this-> _get_pay_method();
			// echo "<pre>";
			// print_r($paymethod);
			// echo "</pre>";
			$paytypeid = $paymethod['ivr'][$this -> escPost('IvrPayMethod')];
			if (  $paytypeid == $payment_cc )
			{
				$digit = $this->get_card_numbercc();
				$cc_saving_num = $digit[$this->escPost('digit_arr')]['card_number'];
				$expire_cc_saving = $digit[$this->escPost('digit_arr')]['Expire'];
				/*$first_num = substr($cc_saving_num,0,1);
				switch ($first_num) {
					case "4":
						//visa 
						$credit_card_type_id = 1;
						break;
					case "5":
						//master card
						$credit_card_type_id = 2;
						break;
					default:
						$credit_card_type_id = 0;
				}*/
				$credit_card_type_id = $this->check_cc_type($cc_saving_num);
			}
			elseif( $paytypeid == $payment_saving )
			{
				$digit = $this->get_card_numbersaving();
				$cc_saving_num = $digit[$this->escPost('digit_arr')]['AccountNo'];
				$expire_cc_saving = "/";
				$credit_card_type_id = 4;
			}
			$bank_id = $this -> escPost('IvrBankId');
		}
		
	}
	else if( $this -> havepost('isXsell') && $this -> escPost('isXsell') == "1" )
	{
		$XSell = $this->getXSellDetail();
		if(!is_null($XSell['CustomerCreditCardNum']) && !empty($XSell['CustomerCreditCardNum']) )
		{
			$cc_saving_num = $XSell['CustomerCreditCardNum'];
			$expire_cc_saving = $XSell['CustomerCreditCardExpDate'];
			$paytypeid = $payment_cc;
			/*$first_num = substr($cc_saving_num,0,1);
			switch ($first_num) {
				case "4":
					//visa 
					$credit_card_type_id = 1;
					break;
				case "5":
					//master card
					$credit_card_type_id = 2;
					break;
				default:
					$credit_card_type_id = 0;
			}*/
			$credit_card_type_id = $this->check_cc_type($cc_saving_num);
		}
		if(!is_null($XSell['accountnumber']) && !empty($XSell['accountnumber']) )
		{
			$cc_saving_num = $XSell['accountnumber'];
			$expire_cc_saving = "/";
			$paytypeid = $payment_saving;
			$bank_id = $this -> escPost('PayersBankId');
			$credit_card_type_id = $this -> escPost('CreditCardTypeId');
		}
	}
	// echo "<pre>";
	// print_r(array(
		// 'CustomerId' => $this -> escPost('CustomerId'),
		// 'SalutationId' => $this -> escPost('PayerSalutationId'),
		// 'PayerFirstName' => $this -> escPost('PayerFirstName'),
		// 'PayerLastName' => $this -> escPost('PayerLastName'),
		// 'GenderId' => $this -> escPost('PayerGenderId'),
		// 'PayerDOB' => $this -> formatDateEng($this -> escPost('PayerDOB')),
		// 'PayerAddrType' => $this -> escPost('PayerAddrType'),
		// 'PayerAddressLine1' => $this -> escPost('PayerAddressLine1'),
		// 'IdentificationTypeId' => $this -> escPost('PayerIdentificationTypeId'),
		// 'PayerIdentificationNum' => $this -> escPost('PayerIdentificationNum'),
		// 'PayerMobilePhoneNum' => $this -> escPost('PayerMobilePhoneNum'),
		// 'PayerCity' => $this -> escPost('PayerCity'),
		// 'PayerAddressLine2' => $this -> escPost('PayerAddressLine2'),
		// 'PayerHomePhoneNum' => $this -> escPost('PayerHomePhoneNum'),
		// 'PremiumGroupId' => ($this -> escPost('pyisholder')?$this -> escPost('HoldGroup'):4),
		// 'PayerZipCode' => $this -> escPost('PayerZipCode'),
		// 'PayerAddressLine3' => $this -> escPost('PayerAddressLine3'),
		// 'PayerOfficePhoneNum' => $this -> escPost('PayerOfficePhoneNum'),
		// 'ProvinceId' => $this -> escPost('PayerProvinceId'),
		// 'PayerAddressLine4' => $this -> escPost('PayerAddressLine4'),
		// 'PaymentTypeId' => $paytypeid,
		// 'PayerCreditCardNum' => $cc_saving_num,
		// 'PayersBankId' => $this -> escPost('PayersBankId'),
		// 'PayerFaxNum' => $this -> escPost('PayerFaxNum'),
		// 'PayerCreditCardExpDate' => $expire_cc_saving,
		// 'CreditCardTypeId' => $this -> escPost('CreditCardTypeId'),
		// 'PayerEmail' => $this -> escPost('PayerEmail'),
		// 'PayerCreatedTs' => date('Y-m-d H:i:s')
		
	// ));
	// echo "</pre>";
	if( $this -> set_mysql_insert('t_gn_payer', array(
		'CustomerId' => $this -> escPost('CustomerId'),
		'SalutationId' => $this -> escPost('PayerSalutationId'),
		'PayerFirstName' => $this -> escPost('PayerFirstName'),
		'PayerLastName' => $this -> escPost('PayerLastName'),
		'GenderId' => $this -> escPost('PayerGenderId'),
		'PayerDOB' => $this -> formatDateEng($this -> escPost('PayerDOB')),
		'PayerAddrType' => $this -> escPost('PayerAddrType'),
		'PayerAddressLine1' => $this -> escPost('PayerAddressLine1'),
		'IdentificationTypeId' => $this -> escPost('PayerIdentificationTypeId'),
		'PayerIdentificationNum' => $this -> escPost('PayerIdentificationNum'),
		'PayerMobilePhoneNum' => $this -> escPost('PayerMobilePhoneNum'),
		'PayerCity' => $this -> escPost('PayerCity'),
		'PayerAddressLine2' => $this -> escPost('PayerAddressLine2'),
		'PayerHomePhoneNum' => $this -> escPost('PayerHomePhoneNum'),
		'PremiumGroupId' => ($this -> escPost('pyisholder')?$this -> escPost('HoldGroup'):4),
		'PayerZipCode' => $this -> escPost('PayerZipCode'),
		'PayerAddressLine3' => $this -> escPost('PayerAddressLine3'),
		'PayerOfficePhoneNum' => $this -> escPost('PayerOfficePhoneNum'),
		'ProvinceId' => $this -> escPost('PayerProvinceId'),
		'PayerAddressLine4' => $this -> escPost('PayerAddressLine4'),
		'PaymentTypeId' => $paytypeid,
		'PayerCreditCardNum' => $cc_saving_num,
		'PayersBankId' => $bank_id,
		'PayerFaxNum' => $this -> escPost('PayerFaxNum'),
		'PayerCreditCardExpDate' => $expire_cc_saving,
		'CreditCardTypeId' => $credit_card_type_id,
		'PayerEmail' => $this -> escPost('PayerEmail'),
		'PayerCreatedTs' => date('Y-m-d H:i:s')
		
	))){ 
		$_conds = true; 
	} 
	else 
	{
		if( $this -> set_mysql_update('t_gn_payer', 
		array(
			'SalutationId' => $this -> escPost('PayerSalutationId'),
			'PayerFirstName' => $this -> escPost('PayerFirstName'),
			'PayerLastName' => $this -> escPost('PayerLastName'),
			'GenderId' => $this -> escPost('PayerGenderId'),
			'PayerAddrType' => $this -> escPost('PayerAddrType'),
			'PayerDOB' => $this -> formatDateEng( $this -> escPost('PayerDOB') ),
			'PayerAddressLine1' => $this -> escPost('PayerAddressLine1'),
			'IdentificationTypeId' => $this -> escPost('PayerIdentificationTypeId'),
			'PayerIdentificationNum' => $this -> escPost('PayerIdentificationNum'),
			'PayerMobilePhoneNum' => $this -> escPost('PayerMobilePhoneNum'),
			'PayerCity' => $this -> escPost('PayerCity'),
			'PayerAddressLine2' => $this -> escPost('PayerAddressLine2'),
			'PayerHomePhoneNum' => $this -> escPost('PayerHomePhoneNum'),
			'PayerZipCode' => $this -> escPost('PayerZipCode'),
			'PayerAddressLine3' => $this -> escPost('PayerAddressLine3'),
			'PayerOfficePhoneNum' => $this -> escPost('PayerOfficePhoneNum'),
			'ProvinceId' => $this -> escPost('PayerProvinceId'),
			'PayerAddressLine4' => $this -> escPost('PayerAddressLine4'),
			'PaymentTypeId' => $paytypeid,
			// 'PayerCreditCardNum' => $this -> escPost('PayerCreditCardNum'),
			'PayerCreditCardNum' => $cc_saving_num,
			'PayersBankId' => $bank_id,
			'PayerFaxNum' => $this -> escPost('PayerFaxNum'),
			// 'PayerCreditCardExpDate' => $this -> escPost('PayerCreditCardExpDate'),
			'PayerCreditCardExpDate' => $expire_cc_saving,
			'CreditCardTypeId' => $credit_card_type_id,
			'PayerEmail' => $this -> escPost('PayerEmail'),
			'PayerUpdatedTs' => date('Y-m-d H:i:s')
		),array('CustomerId'=>$this -> escPost('CustomerId')))){
			$_conds = true;
		}
	}
	
	return $_conds;
 }
/*
 * @ def 		: get_size_insured 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 
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
 
/*
 * @ def 		: get_size_policy 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 
 function get_size_policy()
 {
	$_conds = array();
	$sql = " SELECT b.PolicyNumber, UCASE(( SELECT d.InsuredFirstName FROM t_gn_insured d WHERE d.PolicyId = b.PolicyId ORDER BY d.InsuredId ASC  LIMIT 1 )) as NamaPemegangPolicy FROM t_gn_policyautogen a 
			 INNER JOIN t_gn_policy b on a.PolicyNumber=b.PolicyNumber
		     WHERE a.CustomerId = '".$this -> escPost('CustomerId')."'
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
 
/*
 * @ def 		: _get_transaction 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 function _get_benefit()
 {
	if($this->havepost('ProductId'))
	{
		$sql = "select distinct(a.ProductPlan) as ProductPlan, a.ProductPlanName, concat(b.ProductCode,' - ',b.ProductName) as Product
				from t_gn_productplan a
				left join t_gn_product b on a.ProductId = b.ProductId
				where a.ProductId = '".$this->escPost('ProductId')."'";
		$qry = $this->query($sql);
		?>
		<table border="1" width="99%" cellpadding="2" cellspacing="0">
			<tr>
				<td colspan="<?php echo ($qry->result_num_rows()*2); ?>"><?php echo $qry->result_get_value('Product'); ?></td>
			</tr>
			<tr>
				<?php
				foreach( $qry->result_assoc() as $rows )
				{
					echo "<td colspan=\"2\">".$rows['ProductPlanName']."</td>";
					$content[$rows['ProductPlan']] = $this->getContentBenefit($rows['ProductPlan']);
				}
				?>
			</tr>
			<tr>
				<?php
				for($idx = 1;$idx<=$qry->result_num_rows();$idx++)
				{
					?>
					<td>Benefit</td>
					<td>Description</td>
					<?php
				}
				?>
			</tr>
			<tr>
				<?php
				for($idz = 1;$idz<=$qry->result_num_rows();$idz++)
				{
					?>
					<td><?php echo $content[$idz]['ProductPlanBenefit']; ?></td>
					<td><?php echo $content[$idz]['ProductPlanBenefitDesc']; ?></td>
					<?php
				}
				?>
			</tr>
		</table>
		<?php
	}
 }

 function getContentBenefit($planId=0)
 {
	$sql = "select a.ProductPlanBenefit, a.ProductPlanBenefitDesc from t_gn_productplanbenefit a
			where 1=1
			AND a.ProductId = '".$this->escPost('ProductId')."'
			AND a.ProductPlan = '".$planId."'
			AND a.ProductPlanBenefitStatusFlag = 1";
	$qry = $this->query($sql);
	
	$html['ProductPlanBenefit'] = "<table>";
	$html['ProductPlanBenefitDesc'] = "<table>";
	foreach($qry->result_assoc() as $rows)
	{
		$html['ProductPlanBenefit'] .="<tr>
											<td>".$rows['ProductPlanBenefit']."</td>
										</tr>";
		$html['ProductPlanBenefitDesc'] .="<tr>
											<td>".$rows['ProductPlanBenefitDesc']."</td>
										</tr>";
	}
	$html['ProductPlanBenefit'] .= "</table>";
	$html['ProductPlanBenefitDesc'] .= "</table>";
	
	return $html;
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
  
function _get_transaction()
{ 
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
					// echo "cek premi" .$rows['Premi'];
					echo "  <tr>
								<td class=\"rows-first\" align=\"center\"><input type=\"checkbox\" value=\"{$rows['InsuredId']}\" onclick=\"InsuredWindow(this)\"></td>
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
}

}
new AXA_Save();