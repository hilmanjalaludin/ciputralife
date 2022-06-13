<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../sisipan/parameters.php");
	
	class ShowReferal extends mysql
	{
		var $cust_id;
		var $_tem;
		var $_url;
		
		function __construct()
		{
			parent::__construct();
			$this -> cust_id = $_REQUEST['customerid'];
			$this -> _tem	 = & new Themes();
			$this -> _url 	 = & new application();
			self::index();
		}
		
		function footer()
		{
			echo "</body>
				</html>";
		}
		function getCustomerNumber($cst)
		{
			$sql = "Select a.CustomerNumber From t_gn_customer a where a.CustomerId = ".$cst;
			$qry = $this->query($sql);
			
			return $qry->result_get_value();
		}
		
		function getCustomerName($cst)
		{
			$sql = "Select a.CustomerFirstName From t_gn_customer a where a.CustomerId = '".$cst."'";
			$qry = $this->query($sql);
			
			return $qry->result_get_value();
		}
		
		function getAgent($agt)
		{
			$sql = "select a.id from tms_agent a where a.UserId = '".$agt."'";
			$qry = $this->query($sql);
			
			return $qry->result_get_value();
		}
		
		function getReferal()
		{
			$sql = "select
						*
					from t_gn_referal a
					where a.ReferalCustomerId = '".$this -> cust_id."'
					AND a.ReferalQAStatus is null"; //$this->getCustomerNumber($this -> cust_id)
			$qry = $this->query($sql);
			
			$row = $qry->result_assoc();
			
			return $row;
		}
		
		function getCountReferal()
		{
			$sql = "select
						count(*)
					from t_gn_referal a
					where a.ReferalCustomerId = '".$this -> cust_id."'
					AND a.ReferalQAStatus is null";
			$qry = $this->query($sql);
			
			return $qry->result_get_value();
		}
		
		function index(){
			self::header();
			self::content();
			self::footer();
		}
		
		function ApproveReferal()
		{
			echo "1";
		}
		
		function header()
		{
?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<!-- start Link : css --> 
				<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<meta content="utf-8" http-equiv="encoding">
				<title><?php echo $this -> _tem -> V_WEB_TITLE; ?> :: Show Referal </title>
				<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/other.css" />
				<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css" />	
				<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>    
				<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js"></script>
				<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
				<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/javaclass.js"></script>
				<script>
					var count = <?php echo $this->getCountReferal(); ?>;
					
					var Approve = function()
					{
						var ref_id = new Array();
						if(count > 0){
							for(var i = 1;i<=count;i++){
								ref_id.push (doJava.dom('referal_id_'+i).value);
							}
							
							doJava.File = '../class/class.update.referal.php';
							doJava.Params = {
								action : 'approve_ref',
								ref_id : ref_id
							}
							var err = doJava.Post();
							if(err==1){
								alert('Success, Status Approved !');
							}
							else{
								alert('Failed, Status Not Approved !');
								return false;
							}
						}
						else{
							alert('Sorry, Approve Failed! Referal less than 3 account.');
							return false;
						}
					}
					
					var Reject = function()
					{
						var ref_id = new Array();
						if(count > 0){
							for(var i = 1;i<=count;i++){
								ref_id.push (doJava.dom('referal_id_'+i).value);
							}
							
							doJava.File = '../class/class.update.referal.php';
							doJava.Params = {
								action : 'reject_ref',
								ref_id : ref_id
							}
							
							var err = doJava.Post();
							if(err==1){
								alert('Success, Status Rejected !');
							}
							else{
								alert('Failed, Status Not Rejected !');
								return false;
							}
						}
						else{
							alert('Sorry, Reject Failed! Referal less than 3 account.');
							return false;
						}
					}
				</script>
				<style>
					 #page_info_header { color:#3c3c36;background-color:#eeeeee;height:20px;padding:0px;font-size:16px;font-weight:bold;border-bottom:2px solid #dddfff;margin-bottom:4px;}
					 table td{ font-size:11px; text-align:left;}
					 table p{font-size:14px;color:#4c4c47;}
					 table td .input{ border:1px solid #a5bb89;background-color:#fbfaf5;width:120px;height:18px;font-size:11px;}
					 table td .box{ border:1px solid #a5bb89;background-color:#fbfaf5;width:50px;height:18px;font-size:11px;}
					  
					 table td .input:hover{ border:1px solid #a5bb89;background-color:#e7d795;font-size:11px;}
					 table td .select{ border:1px solid #a5bb89;background-color:#fbfaf5;font-size:11px;height:20px;}
					.header-text {text-align:right;font-weight:normal;font-size:11px;}
					.sunah{color:#4c4c47;font-size:12px;font-family:Arial;}
					.wajib{color:#4c4c47;font-size:12px;font-family:Arial;}
					 h4{background-color:#61605e;color:white;padding:2px;cursor:pointer;width:120px;}
					 h4:hover{color:white;background-color:blue;}
					.age{width:60px;}
				</style>
			</head>
			<body style="overflow:auto;background-color:#eee;background-position:center;">
			<?php
		}
		
		function content()
		{
			$no = 0;
			$list = $this->getReferal();
			if (count($list) ==0){
				echo "konsumen ini tidak memiliki referal";
			}
			else{
			
			//print_r($list);
			?>
				<h1 align="center">SHOW REFERAL</h1>
				<table border=0 cellpadding=2 cellspacing=2 width='99%' style='border:1px solid #eeeeee;'>
					<tr bgcolor="#99FF00" bordercolor="#330000">
						<td>No</td>
						<td>Customer Name</td>
						<td>Referal Name</td>
						<td>Referal Address</td>
						<td>Phone 1</td>
						<td>Phone 2</td>
						<td>Phone 3</td>
						<td>Create Date</td>
						<td>Create User</td>
					</tr>
				<?php
				foreach($list as $key => $val)
				{
					$no++;
					echo"
					<tr>
						<td>".$no." ".$this -> DBForm ->jpHidden('referal_id_'.$no,$val['ReferalId'])."</td>
						<td>".($val['ReferalCustomerId']?$this->getCustomerName($val['ReferalCustomerId']):'-')."</td>
						<td>".($val['ReferalName']?$val['ReferalName']:'-')."</td>
						<td>".($val['ReferalAddress']?$val['ReferalAddress']:'-')."</td>
						<td>".($val['ReferalPhone1']?$val['ReferalPhone1']:'-')."</td>
						<td>".($val['ReferalPhone2']?$val['ReferalPhone2']:'-')."</td>
						<td>".($val['ReferalPhone3']?$val['ReferalPhone3']:'-')."</td>
						<td>".($val['ReferalCreateTs']?$val['ReferalCreateTs']:'-')."</td>
						<td>".($val['ReferalSellerId']?$this->getAgent($val['ReferalSellerId']):'-')."</td>
					</tr>";
				}
				?>
					<tr>
						<td colspan="9">
							<?php $this -> DBForm ->jpButton('btn_approve',NULL,'Approve','onclick="Approve();"'); ?>
							<?php $this -> DBForm ->jpButton('btn_reject',NULL,'Reject','onclick="Reject();"'); ?>
							<?php $this -> DBForm ->jpButton('btn_exit',NULL,'Exit','onclick="doJava.winew.winClose();"'); ?>
						</td>
					</tr>
				</table>
			<?php
			}
			
		}
	} //end class
	
	new ShowReferal();
	?>