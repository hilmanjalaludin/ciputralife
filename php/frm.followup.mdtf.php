<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../sisipan/parameters.php");
	require(dirname(__FILE__)."/../class/lib.form.php");

/**
 ** window open for new add follow up fa
 ** every customer with other 
 ** other status
 ** author didi ganteng
 **/
	
class FollowUp_fa extends mysql
{
 
 	var $_url;
	var $_tem;
 
	/** 
	  ** aksesor 
	  ** on the main content
	**/

    //define variable
    var $_CustomerId;
    var $_CampaignId;
    var $_FuType;
    var $_pkField;

	function FollowUp_fa()
	{
		parent::__construct();
		
		$this -> _url = new application();
		$this -> _tem = new Themes();
		//parameters
		$this -> _CampaignId = $this -> escPost('CampaignId');
		$this -> _CustomerId = $this -> escPost('CustomerId');
		$this -> _FuType     = $this -> escPost('FuType');
		$this -> _pkField 	 = $this -> escPost('FuId');
		//convert all function statics
		self::_F_up();
	}
    
    /**	
    * Get Data Customer
    * return array
    * author didi ganteng
    */
 	function getCustomer(){

	 	// initials
	 	$data = array();
	 	$id = $this->getDataExist();

	 	$sql = "
		 	SELECT  
			 	tgc.CustomerFirstName, 
			 	CustomerDOB, 
			 	CustomerHomePhoneNum, 
			 	CustomerAddressLine1, 
			 	CustomerMobilePhoneNum,
			 	CustomerId,
			 	tgf.*,
			 	tlb.BankName,
				taps.AproveName
		 	FROM t_gn_customer tgc 
		 	LEFT JOIN t_gn_followup tgf ON tgf.FuCustId = tgc.CustomerId
		 	LEFT JOIN t_lk_bank tlb ON tlb.BankId = tgf.FuBank
			LEFT JOIN t_lk_aprove_status taps ON taps.Approveid = tgf.FuQAStatus
		 	WHERE 
			 	tgc.CustomerId = '{$this->_CustomerId}'
	 	";

	 	if($this->_FuType != "") {
	 		$sql .= "AND tgf.FuType = '{$this->_FuType}'";
	 	}

		// echo "<pre>".$sql."</pre>";
		$qry = $this -> query($sql);

		foreach($qry -> result_assoc() as $rows )
		{
			$data['id']			=   $rows['FuId'];
			$data['note1']		=   $rows['FuNotes1'];
			$data['note2']		=   $rows['FuNotes2'];
			$data['note3']		=   $rows['FuNotes3'];
			$data['note4']		=   $rows['FuNotes4'];
			$data['name_fa']	=   $rows['FuName'];
			$data['hpFa']		=   $rows['FuMobile'];
			$data['telpFa']     =   $rows['FuPhone'];
			$data['alamatFa']   =   $rows['FuAddress'];
			$data['dobfa']		=   $rows['FuDOB'];
			$data['id_bank']    =   $rows['FuBank'];
			$data['BankName']   =   $rows['BankName'];
			$data['status']     =   $rows['FuQAStatus'];
			$data['statusname'] =  	$rows['AproveName'];
			$data['type']		=   $rows['FuType'];
		}
		
		return $data;
	}
    
    /**
    *  Get default value form input
    *  table on t_gn_customer
    *  return array object
    *  author : didi ganteng
    */
	function getCustomers(){

	 	// initials
	 	$data = array();
	 	$id = $this->getDataExist();

	 	$sql = "
		 	SELECT  
			 	tgc.CustomerFirstName, 
			 	CustomerDOB, 
			 	CustomerHomePhoneNum, 
			 	CustomerAddressLine1, 
			 	CustomerMobilePhoneNum,
			 	CustomerId
			 	#tgf.*
		 	FROM t_gn_customer tgc 
		 	#LEFT JOIN t_gn_followup tgf ON tgf.FuCustId = tgc.CustomerId
		 	WHERE 
			 	tgc.CustomerId = '{$this->_CustomerId}'
	 	";
		// echo "<pre>".$sql."</pre>";
		$qry = $this -> query($sql);

		foreach($qry -> result_assoc() as $rows )
		{
			$data['name']   	=	$rows['CustomerFirstName']; 
			$data['dob']		=	$rows['CustomerDOB']; 
			$data['hp'] 		=	$rows['CustomerMobilePhoneNum']; 
			$data['alamat'] 	=	$rows['CustomerAddressLine1']; 
			$data['telephone'] 	=	$rows['CustomerHomePhoneNum'];	
		}
		
		return $data;
	}
    
    /**	
    * get data is exist in database
    * author : didi ganteng
    */
	function getDataExist()
	{
	 	// initials
	 	$data = array();

	 	$sql = "
		 	SELECT  
			 	tgc.CustomerFirstName, 
			 	CustomerDOB, 
			 	CustomerHomePhoneNum, 
			 	CustomerAddressLine1, 
			 	CustomerMobilePhoneNum,
			 	CustomerId,
			 	tgf.*
		 	FROM t_gn_customer tgc 
		 	LEFT JOIN t_gn_followup tgf ON tgf.FuCustId = tgc.CustomerId
		 	WHERE 
			 	tgc.CustomerId = '{$this->_CustomerId}'
	 	";

	 	if($this->dataFollowUp != "") {
	 		$sql .= "AND tgf.FuType= '{$this->_FuType}' AND tgf.FuId '{$this->_pkField}'";
	 	}

		// echo "<pre>".$sql."</pre>";
		$qry = $this -> query($sql);

		foreach($qry -> result_assoc() as $rows )
		{
			$data['id']  = $rows['FuId'];
		}
		
		return $data;
	}

    /**
    * first get data bank on database
    * author : didi ganteng
    */
 	private function getBankAccount($opt='')
	{
		//get bank is exist in databases
		$_bank = $this->getCustomer();
		//define flag is active
		$active = 1; 

			$sql = " 
				SELECT  
					BankId, 
					BankName, 
					BankStatusFlag 
				FROM t_lk_bank 
				WHERE 
					BankStatusFlag = $active";
		?>
		
		<select class="select graph_select" name="FuBank" id="FuBank" style="width:204px;">
			<option value="">-- Choose --</option>
			<option value='212'>Others...</option>
			<?php
				if( $_bank){
					echo "<option value=\"{$_bank['id_bank']}\" selected>{$_bank['BankName']}</option>";
				}
				$qry = $this->execute($sql,__FILE__,__LINE__);
				while ( $row = $this ->fetchrow($qry) ) {
					if( $opt==$row->BankId){
						echo "<option value=\"{$row->BankId}\" selected>{$bank}</option>";
					}
					else{
						echo "<option value=\"{$row->BankId}\">{$row->BankName}</option>";
					}
				}
			?>
		</select>
	<?php }

	/**
	 **
	 **/
	 
	function  _F_up()
	{
		// self::getRefaral();
		self::_F_Header();
		self::_F_Content();
		self::_F_Footer();
	}

/**
 **
 **/

function  _F_Header(){
?>		
<!-- template html -->
<!-- jangan di baca nikmatin aja  -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	 <!-- start Link : css --> 
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta content="utf-8" http-equiv="encoding">
	<title>
		<?php echo
			$this -> _tem -> V_WEB_TITLE; 
		?> 
		Follow MDTF
	</title>
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
	<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/EUI_1.0.2_dep.js?time=<?php echo time();?>"></script>
	<style>
		#page_info_header { 
			color:#3c3c36;\
			background-color:#eeeeee;
			height:20px;
			padding:3px;
			font-size:14px;
			font-weight:bold;
			border-bottom:2px solid #dddfff;
			margin-bottom:4px;
		}
		table td { 
			font-size:11px; 
			text-align:left;
		}
		table p{
			font-size:12px;
			color:blue;
		}
		.header-text{
			font-family:Arial;
			font-size:12px;
			text-align:right;
			color:#B00000;
		}	
		table td .input{ 
			border:1px solid #b4d2d4;
			width:204px;
			height:20px;
			background-image:url('../gambar/input_bg.png');
		}
		table td .input_box{ 
			border:1px solid #b4d2d4;
			background-color:#f4f5e6;
			width:40px;
			height:20px;
		}
		table td .input:hover{ 
			border:1px solid red;
			background-color:#f9fae3;
		}
		table td .textarea{ 
			border:1px solid #b4d2d4;
			background-color:#fffccc;
			width:200px;
		}
		table td .textarea:hover{ 
			border:1px solid red;
			background-color:#f9fae3;
		}
		.text_header { 
			text-align:right;
			color:red;
		}
		.select_multiple { 
			border:1px solid #dddddd;
			width:250px;
			font-size:11px;
			background-color:#fffccc;
		}
		.select { 
			border:1px solid #dddddd;
			width:160px;
			font-size:11px;
			height:22px;
			background-color:#fffccc;
		}
		.input_text {
			border:1px solid #dddddd;
			width:160px;
			font-size:12px;
			height:20px;
			background-color:#fffccc;
		}
		.text_header { 
			text-align:right;
			color:red;
		}
		.address { 
			width:200px;
		}		
		.combo { 
			height:21px;width:150px;
		}
		.date { 
			width:120px;
		}
		.txt_input:hover{
			border:1px solid red;
		}
	</style>
	</head>
<body>
<?php
}

/**
 **
 **/

function  _F_Content(){
?>
	<script> 
	
	$(function () {
		$(".graph_select").change(function() {
			var val = $(this).val();
			if(val === "212") {
				$("#otherbank" ).dialog({
					height:100
				});
			} else {
				$("#otherbank" ).dialog('close');
			}
		});
	});
	
	function dialogClose() {
		$('#otherbank').dialog('close');
	}
	
    /**
    * Get datepicker
    */
	$(function(){
		$('#FuDOB').datepicker({
			showOn: 'button', 
			buttonImage: '../gambar/calendar.gif', 
			buttonImageOnly: true, 
			dateFormat:'yy-mm-dd',
			readonly:true,
			changeYear: true,
			changeMonth : true,
			yearRange: '1950:2050'
		});
	});
    
	var SaveOtherBank = function() 
	{
		var fuName_value = doJava.dom('BankName').value;
		
		if( fuName_value == "") {
			alert('info, input bank name please !!!');
			return false;
		} else {
			if( confirm('Do you want to save this new bank')) {
				$.ajax
				({
					url : '../class/class.save.followupfa.php',
					type : 'POST',
					data : {
						action 	   :'save_bank',
						BankName   : fuName_value
					},
					success : function(data) {
						var data = JSON.parse(data);
						if(data.sukses == 1) {
							alert('success add new bank');
							return true;
								// $('#otherbank').dialog('close');
								// window.top.location = window.top.location;
								location.reload(true);
						} else {
							alert('failed add new bank');
								$('#otherbank').dialog('open');
								location.reload(false);
						}
					}
				});
			}
		}
	}
	
    /**
    * Post data with dom html
    * insert or update to t_gn_followup
    * author : didi ganteng
    */
	var SaveFolowUpFa = function()
	{	
		var fuName_value 		= doJava.dom('FuName').value;
		var fuDOB_value 		= doJava.dom('FuDOB').value; 
		var fuMobile_value 		= doJava.dom('FuMobile').value;
		var fuPhone_value 		= doJava.dom('FuPhone').value;
		var fuNote1_value 		= doJava.dom('FuNotes1').value;
		var fuNote2_value 		= doJava.dom('FuNotes2').value;
		var fuNote3_value 		= doJava.dom('FuNotes3').value;
		var fuNote4_value 		= doJava.dom('FuNotes4').value;
		var fuAddress_value 	= doJava.dom('FuAddress').value;
		var fuBank_value 		= doJava.dom('FuBank').value;
		var CustomerId      	= doJava.dom('CustomerId').value;
		var FuId            	= doJava.dom('FuId').value;
		var FuType          	= doJava.dom('FuType').value;
		var number 				= /^[0-9]+$/;
		// var regex 				= /^\d{4}-\d{2}-\d{2}$/;
		
		if( fuName_value =='') { 
			alert('info, Input Name Please');
		} else if( !fuMobile_value.match(number)) {
			alert('info, phone is number only');
		} else if( fuMobile_value=='') {  
			alert('info, Input Hp Please'); 
		} else if( fuDOB_value == "0000-00-00") {
			alert('info, Date of birth cannot 0000-00-00 !');
		} 
		else{
			if( confirm('Do you want to save this Follow up MDTF ?')){
				
				doJava.dom('loadings_gambar').style.display="block";
				$.ajax
				({
					url : '../class/class.save.followupfa.php',
					type : 'POST',
					//datatype: 'json',
					data : {
						action 	   :'save_fol',
						CustomerId : CustomerId,
						FuName     : fuName_value,
						FuDOB      : fuDOB_value,
						FuMobile   : fuMobile_value,
						FuPhone    : fuPhone_value,
						FuAddress  : fuAddress_value,
						FuNotes1   : fuNote1_value,
						FuNotes2   : fuNote2_value,
						FuNotes3   : fuNote3_value,
						FuNotes4   : fuNote4_value,
						FuBank     : fuBank_value,
						FuType     : FuType,
						FuId       : FuId
					},
					success : function( data ){
						var data = JSON.parse(data); 
						//console.log( data);
						if( typeof(data) == 'object' && data.success == 1 ){
							alert("Success saving the Follow up MDTF ! ");
							if( data.FuId != '' ){
								$('#FuId').val(data.FuId);
								//var data = $('#FuId').val(data.FuId);
								doJava.dom('loadings_gambar').style.display="none";
							}
						}
						else{
							alert("Failed saving the Follow up MDTF!"); 
							doJava.dom('loadings_gambar').style.display="none";
						}
					}
				});
			}
		}
	}

	/**
    * Post data with dom html
    * insert or update to t_gn_followup
    * author : didi ganteng
    */
	var UpdateFolowUpFa = function()
	{	
		var fuName_value 		= doJava.dom('FuName').value;
		var fuDOB_value 		= doJava.dom('FuDOB').value; 
		var fuMobile_value 		= doJava.dom('FuMobile').value;
		var fuPhone_value 		= doJava.dom('FuPhone').value;
		var fuNote1_value 		= doJava.dom('FuNotes1').value;
		var fuNote2_value 		= doJava.dom('FuNotes2').value;
		var fuNote3_value 		= doJava.dom('FuNotes3').value;
		var fuNote4_value 		= doJava.dom('FuNotes4').value;
		var fuAddress_value 	= doJava.dom('FuAddress').value;
		var fuBank_value 		= doJava.dom('FuBank').value;
		var CustomerId      	= doJava.dom('CustomerId').value;
		var FuId            	= doJava.dom('FuId').value;
		var FuType          	= doJava.dom('FuType').value;
		var number 				= /^\s*\d*\s*$/;

		if( fuName_value =='') { 
			alert('info, Input Name Please');
		} else if( !fuMobile_value.match(number)) {
			alert('info, phone is number only');
		} else if( fuMobile_value=='') {  
			alert('info, Input Hp Please'); 
		} else if( fuDOB_value == "0000-00-00") {
			alert('info, Date of birth cannot 0000-00-00 !');
		} 
		else{
			if( confirm('Do you want to save this Follow up MDTF ?')){
				
				doJava.dom('loadings_gambar').style.display="block";
				$.ajax
				({
					url : '../class/class.save.followupfa.php',
					type : 'POST',
					//datatype: 'json',
					data : {
						action 	   :'save_fol',
						CustomerId : CustomerId,
						FuName     : fuName_value,
						FuDOB      : fuDOB_value,
						FuMobile   : fuMobile_value,
						FuPhone    : fuPhone_value,
						FuAddress  : fuAddress_value,
						FuNotes1   : fuNote1_value,
						FuNotes2   : fuNote2_value,
						FuNotes3   : fuNote3_value,
						FuNotes4   : fuNote4_value,
						FuBank     : fuBank_value,
						FuType     : FuType,
						FuId       : FuId,
						//FuQAStatus : FuQAStatus
					},
					success : function( data ){
						var data = JSON.parse(data); 
						//console.log( data);
						if( typeof(data) == 'object' && data.success == 1 ){
							alert("Success saving the Follow up MDTF ! ");
							if( data.FuId != '' ){
								$('#FuId').val(data.FuId);
								doJava.dom('loadings_gambar').style.display="none";
							}
						}
						else{
							alert("Failed saving the Follow up MDTF!"); 
							doJava.dom('loadings_gambar').style.display="none";
						}
					}
				});
			}
		}
	}
    
    /**
    * button click close window
    */
	var exit = function()
	{
		if( confirm('Do you want to exit from this session ?') ){
			doJava.winew.winClose();
			//Ext.getCmp('saveActivity').getView().refresh();
		}
		else{
			return false;
		}
	}
			
	</script>
		<div id="otherbank" style="display:none;">
			<div><i>please input the bank name correctly, because this input will automatically be stored in the database !!!</i></div>
			<div><br/></div>
			<div style="color:red;">Other Bank :</div>
			<div><input type="text" class="input" name="BankName" id="BankName"></div>
			<div><a href="javascript:void(0);" class="sbutton" onclick="SaveOtherBank();" style="margin:4px;"><span>&nbsp;Save</span></a></div>
			<div><a onclick="dialogClose();" href="javascript:void(0);" class="sbutton" style="margin:4px;"><span>&nbsp;Close</span></a></div>
		</div>
	<div style="border-bottom:1px solid #dddddd;padding:4px;height:450px;overflow:auto;">
		<fieldset style="border:1px solid #dddddd;">
			<legend> <h2>
			Follow MDTF
			</h2></legend>
			<form id="forms">
				<input type="hidden" name="FuType" id="FuType" value="<?php echo $this-> _FuType ?>">
				<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo $this -> _CustomerId;?>">
				<table border=0 width="90%" id="table_referal">
					<tr>
						<!-- Get data customer -->
						<?php 
						    // insert data to variable value & datas
                            $value = $this -> getCustomer();
                            $datas = $this -> getCustomers();
							
							//var_dump($datas);
                            // echo $value['id'];
                            $note1 = isset($value['note1']) ? $value['note1'] : "";
                            $note2 = isset($value['note2']) ? $value['note2'] : "";
                            $note3 = isset($value['note3']) ? $value['note3'] : "";
                            $note4 = isset($value['note4']) ? $value['note4'] : "";
						?>
						<input type="hidden" name="FuId" id="FuId" value="<?php echo $value['id'] ?>">
						<td class="header-text wajib">First Name</td>
						<td class="header-content">
							<input type="input"  class="input" name="FuName" value="<?php echo ($value['id'] != "") ? $value['name_fa'] : $datas['name']  ?>" id="FuName" >
						</td>

						<td class="header-text wajib">HP</td>
						<td class="header-content">
							<input type="input" class="input" name="FuMobile" onkeyup="Ext.Set(this.id).IsNumber();" value="<?php echo ($value['id'] != "") ? $value['hpFa'] : $datas['hp'] ?>" id="FuMobile">
						</td>
                        <td class="header-text wajib">Date Of Birth (YYYY-MM-DD) </td>
						<td>
							<input type="text" class="txt_input date" name="FuDOB" id="FuDOB" value="<?php echo ($value['id'] != "") ? $value['dobfa'] : $datas['dob']; ?>" disabled>
						</td>
					</tr>

					<tr>
						<td class="header-text">Telephone</td>
						<td class="header-content">
							<input type="text" class="input" name="FuPhone" id="FuPhone" onkeyup="Ext.Set(this.id).IsNumber();" value="<?php echo ($value['id'] != "") ? $value['telpFa'] : $datas['telephone']  ?>" >
						</td>

						<td class="header-text"> Bank Name</td>
						<td><?php $this -> getBankAccount(); ?></td>
						<td class="header-text">Address</td>
						<td class="header-content">
							<textarea class="textarea" name="FuAddress" id="FuAddress"><?php echo ($value['id'] != "") ? $value['alamatFa'] : $datas['alamat'] ?></textarea>
						</td>
                    </tr>

                    <tr>
						<td class="header-text">Note 1</td>
						<td class="header-content">
							<textarea class="textarea" name="FuNotes1" id="FuNotes1"><?php echo $note1 ?></textarea>
						</td>

						<td class="header-text">Note 2</td>
						<td class="header-content">
							<textarea class="textarea" name="FuNotes2" id="FuNotes2"><?php echo $note2 ?></textarea></td>
						<td class="header-text">Note 3</td>
						<td class="header-content">
							<textarea class="textarea" name="FuNotes3" id="FuNotes3"><?php echo $note3 ?></textarea>
						</td>
					</tr>

					<tr>
						<td class="header-text">Note 4</td>
						<td class="header-content">
							<textarea class="textarea" name="FuNotes4" id="FuNotes4"><?php echo $note4 ?></textarea>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>	
		<span id="loadings_gambar" style="display:none;color:red;text-align: left;"> <img src="../gambar/loading.gif"> loading...</span>
		<div style="padding-left:2px;border:0px solid #dddddd;margin-top:30px;height:70px;">
		<?php 
		    //insert data to varible define id for get id 
            $define_id = $this -> getCustomer();

            if( $this -> getSession('handling_type') !=5 && $this -> getSession('handling_type') !=10 && $define_id['id'] != "") { ?>
            	<a href='javascript:void(0);' class='sbutton' name='exit' onclick='exit();' style='margin:4px;'><span>&nbsp;exit</span></a>
            <?php } else if( $this -> getSession('handling_type')==5  || $this -> getSession('handling_type')==10 && $define_id['id'] != ""){ ?>
				<a href="javascript:void(0);" class="sbutton" onclick="UpdateFolowUpFa();" style="margin:4px;"><span>&nbsp;Update</span></a>
				<a href="javascript:void(0);" class="sbutton" name="exit" onclick="exit();" style="margin:4px;"><span>&nbsp;exit</span></a>
            <?php } else { ?>
                <a href="javascript:void(0);" class="sbutton" onclick="SaveFolowUpFa();" style="margin:4px;"><span>&nbsp;Save</span></a>
				<a href="javascript:void(0);" class="sbutton" name="exit" onclick="exit();" style="margin:4px;"><span>&nbsp;exit</span></a>
            <?php }?> 
	</div>	
	</div>
	<?php
	}
	/**
	 **
	 **/
	 
	function  _F_Footer(){ ?>
	</body>
</HTML>
	<?php
	}		
}
new FollowUp_fa();