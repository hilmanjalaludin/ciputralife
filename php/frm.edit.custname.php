<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	require('../class/lib.form.php');
	
	
	$sql = " select * from t_gn_customer a left join t_gn_payer b on a.CustomerId=b.CustomerId 
				where a.CustomerId = '".$_REQUEST['CustomerId']."'";
				
	$qry = $db ->execute($sql,__FILE__,__LINE__);
	if( $qry && ( $row = $db -> fetchassoc($qry))){
		$AssCustomer = $row;
	}
	//print_r($AssCustomer);
	
	
?>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
	<script>
		var CustomerId = '<?php echo $AssCustomer['CustomerId']; ?>';
		var closeWin = function(){
			if(confirm('Do you want to close ?')){ 
				window.close(); 
			}
			else{
				return false;
			}
		}
		
		var UpdateName =function(){
			var policy_qc = window.opener.winFrame;
			var iframeUrl = policy_qc.location;
			var old_cust_name = doJava.dom('old_customer_name').value;
			var new_cust_name = doJava.dom('name_customer_name').value;
			
			if( old_cust_name.length < 3) { alert("Old Name is empty!"); return false; }
			else if( new_cust_name.length < 3){ alert("New Name is empty!"); return false; }
			else{
				doJava.File = "../class/class.approval.data.php";
				doJava.Params = {
					action : 'update_name',
					customerid : CustomerId, 
					oldcustomername : old_cust_name,
					newcustomername : new_cust_name
				}
				
				
				var error = doJava.Post();
					if( error ==1){
						alert("Success Update Name!");
						window.location = window.location;
						policy_qc.location= window.opener.winFrame.location;
						
					}
					else{
						alert("Failed Update Name!"); 
						return false;
					}
				
			}
		}
	</script>
	
	<style>
		#page_info_header { color:#3c3c36;background-color:#eeeeee;height:20px;padding:3px;font-size:14px;font-weight:bold;border-bottom:2px solid #dddfff;margin-bottom:4px;}
		 table td{ font-size:11px; text-align:left;}
		 table p{font-size:12px;color:blue;}
		 table td .input{ border:1px solid #b4d2d4;background-color:#f4f5e6;width:200px;height:22px;font-size:12px;}
		 table td .input:hover{ border:1px solid red;background-color:#f9fae3}
		 table td select{ border:1px solid #b4d2d4;background-color:#f2f2e9;}
		.header-text {text-align:right;font-weight:normal;font-size:14px;font-weight:bold;}
		.sunah {color:#4c4c47;font-size:12px;font-family:Arial;}
		.wajib {color:red;font-size:12px;font-family:Arial;}
		 h4{background-color:#8da0cf;color:#FFFFFF;padding:4px;cursor:pointer;width:120px;font-size:14px;width:auto;font-family:Arial;}
		 h4:hover{color:#f04a1d;background-color:#d8f7f9;}
		 .age{width:60px;}
		 .button{width:98px;border:1px solid #ddd;color:blue;height:24px;}
	</style>
	
	<fieldset style="border:1px solid #ddd;margin-top:-20px;">
		<legend> <h4>Edit Customer Name </h4></legend>
		<div style="margin-top:-10px;">
			<table cellpadding="8px;">
				<tr>
					<td class="header-text wajib"> Old Customer Name</td>
					<td><?php $jpForm->jpInput('old_customer_name','input',$AssCustomer['CustomerFirstName'],null,1 )?></td>
				</tr>

				<tr>
					<td class="header-text wajib"> New Customer Name</td>
					<td> <?php $jpForm->jpInput('name_customer_name','input')?></td>
				</tr>

				<tr>
					<td class="header-text"> &nbsp;</td>
					<td> 
						<input type="button" class="button" name="UpdateName" onclick="UpdateName();" value="Update">
						<input type="button" class="button" name="exitForm"  onclick="closeWin();" value="Cancel">
					</td>
				</tr>
				
			</table>
			
		</div>
	</fieldset>