<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	class ChangeNameCustomer extends mysql{
		var $action;
		
		function __construct(){
			parent::__construct();
			$this -> action = $this ->escPost('action');
		}
		
		function getCustomers(){
			$sql = " select * from t_gn_customer a right join t_gn_approvalhistory b on a.CustomerId=b.CustomerId
					 where b.ApprovalItemId=1
					 and b.ApprovalApprovedFlag <> 1
					 and b.ApprovalHistoryId=".$_REQUEST['rowsid'].""; 
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$row = $this -> fetchrow($qry);	
			return $row;
		}
		
		function index(){
			if( $this -> havepost('action')){
				switch( $this -> action ):
					case 'get_tpl_custname' : $this -> getTplCustomer(); break;
					case 'save_cust_name'   : $this -> setSaveCustomer(); break;
				endswitch;
			}
		}
		
		
		function setSaveCustomer(){
			$datas = $this -> getCustomers();
			$sql = " update t_gn_customer SET CustomerFirstName ='".$_REQUEST['cust_from_name']."' 
					 where CustomerId ='".$datas->CustomerId."'";
			$res = $this -> execute($sql,__FILE__,__LINE__);
			if( $res ){
				$sql2 = " Update t_gn_approvalhistory SET ApprovalApprovedFlag = 1, ApprovalUpdatedTs = now(),
						 UpdatedById = '".$this -> getSession('UserId')."' 
						 where ApprovalHistoryId='".$_REQUEST['rowsid']."'";
						 
				$res2 = $this -> execute($sql2,__FILE__,__LINE__);
					if( $res2 ): echo 1;
					else : echo 0;
					endif;
			}
		
		}
		
		function setCss(){
		?>
			<style>	
				.input { border:1px solid #ff4321;background-color:#fffccc;width:240px;height:22px;font-size:11px;color:#024284; }
				.input:hover{border:1px solid #B90900;}
				.input:active{border:1px solid #BBBDDD;}
				.select:hover{border:1px solid #B90900;}
				.select:active{border:1px solid #BBBDDD;}
				.select { font-family:arial;border:1px solid #ff4321;background-color:#fffccc;width:240px;height:24px;font-size:11px;color:#024284;} 
			</style>
		<?php
		}
		function getTplCustomer(){
			$datas = $this -> getCustomers();
			$this -> setCss();
			?>
				<div class="box-shadow" style="text-align:left;height:auto;">
					<input type="hidden" name="rowsid" id="rowsid" value="<?php echo $_REQUEST['rowsid']; ?>">
					
					<table  cellpadding="12px" style="text-align:left;" border=0>
						<tr>
							<td style="font-size:12px;text-align:right;color:red;" nowrap> Original Customer Name  </td>
							<td > <input type="text" class="input" name="cust_ori_name" id="cust_ori_name" value="<?php echo $datas -> CustomerFirstName; ?>" disabled ></td>
						</tr>	
						<tr>
							<td style="font-size:12px;text-align:right;color:red;" nowrap> Request Customer Name  </td>
							<td > <input type="text" class="input" name="cust_from_name" id="cust_from_name" value="<?php echo $datas -> ApprovalOldValue; ?>" disabled ></td>
						</tr>	
						<tr>
							<td style="font-size:12px;text-align:right;color:red;" nowrap> To Customer Name  </td>
							<td> <input type="text" class="input" name="cust_to_name" id="cust_to_name" value="<?php echo $datas -> ApprovalNewValue; ?>" ></td>
						</tr>	
						<tr>
							<td style="font-size:12px;text-align:right;color:red;"> Aggreement  </td>
							<td> 
								<select name="cb_aggreement" id="cb_aggreement" class="select">
									<option value=""> -- Choose -- </option>
									<option value="1"> Aggree </option>
									<option value="0"> Not Aggree </option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%" style="text-align:right;color:red;"></td>
							<td>
								
								<a href="javascript:void(0);" style="margin-left:0px;width:60px;text-align:center;" class="sbutton" onclick="UpdateName();"><span>&nbsp;Save </span></a> 
								
								</td>
						</tr>
					</table>	
				</div>
				
			<?php
		}
		
	}
		
	$ChangeNameCustomer = new ChangeNameCustomer();
	$ChangeNameCustomer -> index();
		