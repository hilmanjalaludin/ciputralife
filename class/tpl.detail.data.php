<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	class DetailData extends mysql{
		
		var $CustomerId;
		var $CampaignId;
		var $UserId;
		var $UserName;
		var $Action;
		var $getValue;
		var $Masking;
		var $LockStatus;
		
		function __construct(){
			
			parent::__construct();
			
			$this -> Action		= $this -> escPost('action');	
			$this -> CustomerId	= $this -> escPost('customerid');
			$this -> CampaignId = $this -> escPost('campaignid');
			$this -> UserId		= $this -> getSession('UserId');
			$this -> UserName   = $this -> getSession('username');
			$this -> Masking	= new application(); 
			$this -> LockStatus = array(16,17);
		}
		
		function index(){
		
			$this -> getCustomer();
			
			if( $this->havepost('action') ){
				switch($this -> Action ){
					case 'default_contact' 	 	: $this -> DefaultContactTpl(); 	break;
					case 'home_contact' 	 	: $this -> HomeContactTpl(); 		break;
					case 'office_contact' 	 	: $this -> OfficeContactTpl(); 		break;
					case 'history_contact'   	: $this -> HistoryContactTpl();	 	break;
					case 'reason_contact'	 	: $this -> ReasonContactTpl(); 		break; 
					case 'get_primary_phone'   	: $this -> PrimaryPhoneTpl(); 		break;
					case 'get_add_phone'        : $this -> getAdditionalPhone();    break;
					case 'card_info'			: $this -> cardInfo(); 				break;
				}
			}
		}
		
		function sendCangeRequest(){
			if( $this -> havepost('item_customer') ):
				$V_CHG = array
				(
					'CustomerId' => $this -> escPost('item_customer'), 
					'ApprovalItemId' => $this -> escPost('item_value'), 
					'CreatedById' => $this -> getSession('UserId'), 
					'ApprovalOldValue' => $this -> escPost('item_old_value'), 
					'ApprovalNewValue' => $this -> escPost('item_new_value'), 
					'ApprovePhoneType' => $this -> escPost('item_phone_type'),
					'ApprovalCreatedTs' => date('Y/m/d H:i:s')
				);
				
				$query = $this -> set_mysql_insert("t_gn_approvalhistory",$V_CHG);
					
					if( $query ) : echo 1;
					else :
						echo 0;
					endif;
			endif;
		}
		
		
		function getContactReasonText(){
			$datas = array();
			$sql = " select a.CallReasonCode, a.CallReasonDesc from t_lk_callreason  a where a.CallReasonContactedFlag='".$this->escPost('call_status')."' ";
			$query = $this -> execute($sql,__FILE__,__LINE__);
			?>	
				<select class="txt_input combo" id="call_result" name="call_result" onChange="getActionNext(this.value);">
					<option value=""> -- Choose --</option>
			<?php 
				while( $rows  = $this ->fetchrow($query) ){
					echo "<option value=\"{$rows->CallReasonCode}\" >{$rows->CallReasonDesc}</option>";	
				}
			?>
				</select>
			<?php
		}
		
		
		function PrimaryPhoneTpl(){
			$datas = array();
			$sql = " select a.CustomerHomePhoneNum, a.CustomerMobilePhoneNum, a.CustomerWorkPhoneNum, a.CallReasonId 
					from t_gn_customer a WHERE a.CustomerId=".$this -> CustomerId."";
					
				
			$query = $this -> execute($sql,__FILE__,__LINE__);
			$rows  = $this ->fetchrow($query);
			
			$datas[$rows->CustomerHomePhoneNum]   = 'Home'; 
			$datas[$rows->CustomerMobilePhoneNum] = 'Mobile';
			$datas[$rows->CustomerWorkPhoneNum]   = 'Office';	
		?>	
				<select class="txt_input combo" multiple="true" readonly style="height:70px;">
					
			<?php 
					foreach( $datas as $key => $val ):
						echo "<option value=\"{$key}\" > {$val} - {$key}</option>";
				   endforeach;	
			?> 				
				</select>
			<?php		
		}
		
		function setCss(){
			?>
				<style>	
					.txt_header { color:#703c04; text-align:right;font-weight:bold;}
					.txt_input { color:#000000;font-size:11px; 
								 background:url('../gambar/input_bg.png'); text-align:left; height:18px;
								 width:160px; border:1px solid #c9bb81;padding-left:2px;}
					.zip_code { width:40px;}			 
					.address { width:250px;}		
					.combo { height:21px;width:165px;}
					.date { width:140px;}
					.legend{ }
					.txt_input:hover{border:1px solid red;}
					
				</style>
			<?php
		}
		
		function getDebugError(){
			echo "<pre>";
					print_r($this->getValue);
			echo "</pre>";
		}
		
		
		function getAdditionalPhone(){
			$sql = "select c.PhoneDesc,
							a.AddPhoneNumber
							from t_gn_addphone a
						left join t_gn_approvalhistory b on a.AddPhoneApproveId=b.ApprovalHistoryId
						left join t_lk_phonetype c on a.AddPhoneType=c.PhoneTypeId
						where b.ApprovalApprovedFlag=1
						and a.CustomerId=".$this -> CustomerId."";
						
			$qry = $this -> execute($sql,__FILE__,__LINE__);	
			?>
				<select class="txt_input combo" multiple="true" readonly style="height:70px;">
					
			<?php	
			while( $row = $this -> fetcharray($qry)){
				echo "<option value=\"{$row[1]}\">{$row[0]} - {$row[1]}</option>";
			}			
			?>
				</select>
			<?
		}
		
		
		
		function getCustomer(){
			$sql =" SELECT 
						a.*,b.AssignSelerId,
						a.CustomerCardType,
						c.CampaignName, 
						c.CampaignStartDate, 
						c.CampaignEndDate 
					FROM t_gn_customer a
						INNER JOIN t_gn_assignment b on a.CustomerId= b.CustomerId
						LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId
						WHERE c.CampaignStatusFlag=1
						AND a.CustomerId = ".$this -> CustomerId."
						AND a.CampaignId = ".$this -> CampaignId." ";
			
			$query  = $this ->execute($sql,__FILE__,__LINE__);
			if( $query  ) :	 $this -> getValue = $this -> fetchrow($query); endif;
		}
		
		
		function getValueGender($GenderId=''){
			
			$sql = "select a.GenderId, a.GenderCode, a.Gender from t_lk_gender a ";
			$query = $this ->execute($sql,__FILE__,__LINE__);
			while( $rows = $this->fetchrow($query) ){
				if( $rows->ProvinceId == $GenderId ){
					echo "<option value=\"{$rows->GenderId}\" selected>{$rows->GenderCode} - {$rows->Gender}</option>";
				}else{
					echo "<option value=\"{$rows->GenderId}\">{$rows->GenderCode} - {$rows->Gender}</option>";
				}
			}	
		}	
		
		function getValueCard($CardTypeId=''){
			
			$sql = "select a.CreditCardTypeId, a.CreditCardTypeCode, a.CreditCardTypeDesc from t_lk_creditcardtype a";
			$query = $this ->execute($sql,__FILE__,__LINE__);
			while( $rows = $this->fetchrow($query) ){
				if( $rows -> CreditCardTypeId == $CardTypeId ){
					echo "<option value=\"{$rows->CreditCardTypeId}\" selected>{$rows->CreditCardTypeCode} - {$rows->CreditCardTypeDesc}</option>";
				}else{
					echo "<option value=\"{$rows->CreditCardTypeId}\">{$rows->CreditCardTypeCode} - {$rows->CreditCardTypeDesc}</option>";
				}
			}	
		}	
		
		function getItemApprove(){
			$sql = "select  a.ApprovalItemId,a.ApprovalItem  from t_lk_approvalitem a";
			
			$qry = $this -> execute($sql,__FILE__,__LINE__);
				while( $row = $this ->fetchrow($qry)){
					echo "<option value=\"{$row->ApprovalItemId}\">{$row->ApprovalItem}</option>";
				
				}
		
		}
		
		function getPhoneType(){
			$sql = "select a.PhoneType, a.PhoneDesc from t_lk_phonetype a";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			while($row= $this ->fetcharray($qry)){
				echo "<option value=\"{$row[0]}\">{$row[1]}</option>";
			}
		}
		
		function getValueTitle($SalutationId=''){
			
			$sql = "select a.SalutationId, a.SalutationId, a.Salutation from t_lk_salutation a order by a.SalutationId ASC";
			$query = $this ->execute($sql,__FILE__,__LINE__);
			while( $rows = $this->fetchrow($query) ){
				if( $rows->ProvinceId == $SalutationId ){
					echo "<option value=\"{$rows->SalutationId}\" selected>{$rows->SalutationId} - {$rows->Salutation}</option>";
				}else{
					echo "<option value=\"{$rows->SalutationId}\">{$rows->SalutationId} - {$rows->Salutation}</option>";
				}
			}	
		}	
		
		function getValuePropince($ProvinceId=''){
			
			$sql = "Select a.ProvinceId, a.ProvinceCode, a.Province from t_lk_province a order by a.Province ASC";
			$query = $this ->execute($sql,__FILE__,__LINE__);
			while( $rows = $this->fetchrow($query) ){
				if( $rows->ProvinceId == $ProvinceId ){
					echo "<option value=\"{$rows->ProvinceId}\" selected>{$rows->ProvinceCode} - {$rows->Province}</option>";
				}else{
					echo "<option value=\"{$rows->ProvinceId}\">{$rows->ProvinceCode} - {$rows->Province}</option>";
				}
			}	
		}	
	

		
	/** tpl default **/	
	
		function DefaultContactTpl(){
			global $db;
			$this -> setCss();
		?>
			<script>
				$(function(){
					$('#txt_customer_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy',readonly:true});
				});
			</script>
			<fieldset> 
				<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;"> Customer Information</legend> 
			<table class="content_contact_default" width="100%" align="center" cellpadding="4px">
					<tr>
						<td class="txt_header" nowrap>Customer ID</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this -> getValue -> CustomerNumber; ?>" disabled></td>
						<td nowrap class="txt_header">First Name</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this -> getValue -> CustomerFirstName; ?>" disabled></td>
						
						<td class="txt_header" nowrap>Last Name</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this -> getValue -> CustomerLastName; ?>" disabled></td>
					</tr>
					
					<tr>
						<td class="txt_header" nowrap>Title</td>
						<td>
							<select class="txt_input combo"  id="cmb_customer_salut" name="cmb_customer_salut" disabled>
								<option value=""> -- Choose -- </option>
								<?php $this -> getValueTitle( $this -> getValue -> SalutationId ); ?>
							</select>
						</td>
						<td class="txt_header" nowrap>Gender </td>
						<td>
							<select class="txt_input combo" id="cmb_customer_gender" name="cmb_customer_gender" disabled>
								<option value=""> -- Choose -- </option>
								<?php $this -> getValueGender( $this -> getValue -> GenderId ); ?>
							</select>
						</td>
						
						<td class="txt_header" nowrap>Date Of Birth </td>
						<td><input type="text" class="txt_input date" name="txt_customer_dob" id="txt_customer_dob" value="<?php echo $db->Date->date_time_indonesia($this -> getValue -> CustomerDOB); ?>" disabled></td>
						<tr>
							<td class="txt_header" >Primary Phone (s) </td>
							<td valign="top"><?php $this -> PrimaryPhoneTpl(); ?></td>
							
							<td class="txt_header" >Additional Phone (s)</td>
							<td valign="top"><?php $this -> getAdditionalPhone(); ?></td>
							
						</tr>
						
					</tr>
			</table>	
		 </fieldset>	
		
		<?	
		}
		
		/** home tpl default **/	
	
		function HomeContactTpl(){
			
		?>
			<fieldset> 
				<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;"> Home Information</legend> 
				<table class="content_contact_default" cellpadding="4px;" align="center">
						<tr>
							<td class="txt_header" nowrap>Address</td>
							<td><input type="text" class="txt_input address" name="txt_cust_home_address" id="txt_cust_home_address" value="<?php echo $this -> getValue -> CustomerAddressLine1; ?>" disabled></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="text" class="txt_input address" name="txt_cust_home_address1" id="txt_cust_home_address1" value="<?php echo $this -> getValue -> CustomerAddressLine2; ?>" disabled></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="text" class="txt_input address" name="txt_cust_home_address2" id="txt_cust_home_address2" value="<?php echo $this -> getValue -> CustomerAddressLine3; ?>" disabled></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="text" class="txt_input address" name="txt_cust_home_address3" id="txt_cust_home_address3" value="<?php echo $this -> getValue -> CustomerAddressLine4; ?>" disabled></td>
						</tr>
						<tr>
							<td class="txt_header" nowrap>City</td>
							<td><input type="text" class="txt_input" name="txt_cust_home_city" id="txt_cust_home_city" value="<?php echo $this -> getValue -> CustomerCity; ?>" disabled></td>
						</tr>
						<tr>
							<td class="txt_header">Province</td>
							<td>
								<select class="txt_input combo" id="cmb_cust_home_province" name="cmb_cust_home_province" disabled>
								<option value=""> -- Choose -- </option>
								<?php $this -> getValuePropince( $this -> getValue -> ProvinceId ); ?>	
								</select>
								&nbsp;  <b class="txt_header">Zip Code</b> &nbsp; <input type="text" class="txt_input zip_code" name="text_cust_home_zip" id="text_cust_home_zip" value="<?php echo $this -> getValue -> CustomerZipCode; ?>" disabled>
							</td>
						</tr>
						
					<tr>
							<td class="txt_header" nowrap style="height:24px;">&nbsp;</td>
							<td>
					</tr>
						
						
				</table>	
		 </fieldset>	
		
		<?	
		}
		
		/** Office tpl default **/	
	
		function OfficeContactTpl(){
		?>
			<script>
				$(function(){
					$('#text_cust_office_expired').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy',readonly:true});
				});
			</script>
			<fieldset > 
				<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;"> Office Information</legend> 
				<table class="content_contact_default" cellpadding="4px;">
						<tr>
							<td class="txt_header">Address</td>
							<td><input type="text" class="txt_input address" name="txt_cust_office_address" id="txt_cust_office_address" value="<?php echo $this -> getValue -> CustomerOfficeLine1; ?>" disabled></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="text" class="txt_input address"  name="txt_cust_office_address1" id="txt_cust_office_address1" value="<?php echo $this -> getValue -> CustomerOfficeLine2; ?>" disabled></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="text" class="txt_input address"  name="txt_cust_office_address2" id="txt_cust_office_address2" value="<?php echo $this -> getValue -> CustomerOfficeLine3; ?>" disabled></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="text" class="txt_input address"  name="txt_cust_office_address3" id="txt_cust_office_address3" value="<?php echo $this -> getValue -> CustomerOfficeLine4; ?>" disabled></td>
						</tr>
						<tr>
							<td class="txt_header">City</td>
							<td><input type="text" class="txt_input"  name="txt_cust_office_city" id="txt_cust_office_city" value="<?php echo $this -> getValue -> CustomerOfficeLine1; ?>" disabled></td>
						</tr>
						<tr>
							<td class="txt_header" nowrap>Province</td>
							<td>
								<select class="txt_input combo"  id="cmb_cust_office_province" id="cmb_cust_office_province" disabled>
									<option value=""> -- Choose -- </option>
									<?php $this -> getValuePropince( $this -> getValue -> ProvinceId ); ?>	
								</select>
								&nbsp;  <b class="txt_header">Zip Code</b> &nbsp; <input type="text" class="txt_input zip_code" name="text_cust_office_zip" id="text_cust_office_zip" value="<?php echo $this -> getValue -> CustomerOfficeZipCode; ?>" disabled>
							</td>
						</tr>
						<tr>
							<td class="txt_header" nowrap>Office Name</td>
							<td><input type="text" class="txt_input address"  name="text_cust_office_name" id="text_cust_office_name" value="<?php echo $this -> getValue -> CustomerOfficeName; ?>" disabled></td>
						</tr>
						
						
						
				</table>	
			 </fieldset>	
			
		
		<?	
		}
		
		function cardInfo(){
		?>
			<fieldset style="padding-bottom:10px;">
				<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;">Credit Card Info </legend>
					<div style="margin-left:20px;">
						<table class="content_contact_default" cellpadding="4px;" >
						<tr>
							<td class="txt_header" nowrap>Card Type</td>
							<td>
								<select class="txt_input combo" id="cm_cust_home_cardtype" name="cm_cust_home_cardtype" disabled>
									<option value=""> -- Choose -- </option>
									<?php $this -> getValueCard(''); ?>
								</select>
							</td>
						
							<td class="txt_header" nowrap>Expiration Date</td>
							<td>
								<input type="text" class="txt_input date"  name="text_cust_office_expired" id="text_cust_office_expired" value="<?php echo $this -> getValue -> xxx; ?>" disabled>
							</td>
						</tr>
					
					</table>
					</div>	
			</fieldset>	
			
		<?php
		}
		
		function HistoryContactTpl(){
		?>
			
			<fieldset>
				<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;"> Contact History </legend>
					
					<div style="height:200px;overflow:auto;">
						<table border=0 align="left" cellspacing=0 style="width:99%;" cellspacing="1px">
							<tr>
								<th style="border:1px solid #dddddd;height:35px;background-color:#878382;color:#FFF;" WIDTH="15%">Last Call</th>
								<th style="border:1px solid #dddddd;background-color:#878382;color:#FFF;" WIDTH="12%">Agent </th>
								<th style="border:1px solid #dddddd;background-color:#878382;color:#FFF;">Note</th>
							</tr>
						<?
							$sql = " SELECT 
										b.id, b.full_name, a.*, c.*, date_format(a.CallHistoryUpdatedTs,'%d-%m-%Y %H:%s') as Calldate
									FROM t_gn_callhistory a left join tms_agent b on a.UpdatedById= b.UserId
										left join t_lk_callreason c on a.CallReasonId=c.CallReasonId
									WHERE a.CustomerId ='".$this -> CustomerId."' 	
									ORDER BY CallHistoryId DESC ";
									
							$qry = $this ->execute($sql,__FILE__,__LINE__);
							$i= 0;
							while( $row = $this ->fetchrow($qry)){
								$color = ($i%2!=0?'#FFFFFF':'#f3f6f0');
								$i++;
							?>
								<tr class="onselect" bgColor="<?php echo $color; ?>">
									<td style="border-left:1px solid #dddddd;border-top:1px solid #dddddd;height:24px;padding-left:4px;font-size:12px;color:#787473;"><?php echo $row->Calldate; ?></td>
									<td style="border-left:1px solid #dddddd;border-top:1px solid #dddddd;height:24px;padding:4px;font-size:12px;color:#787473;" nowrap><?php echo $row -> full_name; ?></td>
									<td style="border-left:1px solid #dddddd;border-top:1px solid #dddddd;height:24px;padding-left:4px;">
										<span style="line-height:20px;font-size:12px;font-family:Arial;color:#6d706a">
											<span> Call Number </span>
												<b style="color:#e53b14"> ( <?php echo ($this -> Masking -> setMaskText($row->CallNumber)?$this -> Masking -> setMaskText($row->CallNumber):'-');?> ) </b> 
												<br><span> Last Call Result </span> <b style="color:#e53b14;"> 
											( <?php echo $row -> CallReasonDesc; ?> )</b> <br> 
											<p style="color:#787473;"><?php echo $row -> CallHistoryNotes; ?></p>
										<span>	
									</td>
								</tr>
							<?php	
							}	
						?>		
						</table>
					</div>	
			</fieldset>	
		<?php
		}
	}
	
	$DetailData = new DetailData();
	$DetailData -> index();

?>