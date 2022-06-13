<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require("../class/lib.form.php");
	
	class ContactDetail extends mysql{
		
		var $CustomerId;
		var $CampaignId;
		var $UserId;
		var $UserName;
		var $Action;
		var $getValue;
		var $Masking;
		var $LockStatus;
		var $JPForm;
		
		
		function __construct(){
			
			parent::__construct();
			
			$this -> Action		= $this -> escPost('action');	
			$this -> CustomerId	= $this -> escPost('customerid');
			$this -> CampaignId = $this -> escPost('campaignid');
			$this -> UserId		= $this -> getSession('UserId');
			$this -> UserName   = $this -> getSession('username');
			$this -> ServerIP 	= 'http://'.$this -> getPBX().'/recording/';
			$this -> Masking	= new application(); 
			$this -> LockStatus = array(22,17);
			$this -> JPForm 	= new jpForm();
			
		}
		
		function index(){
		
			$this -> getCustomer();
			
			if( $this->havepost('action') )
			{
				//echo $this->getDebugError();
				switch($this -> Action )
				{
					case 'default_contact' 	 	: $this -> DefaultContactTpl(); 	break;
					case 'home_contact' 	 	: $this -> HomeContactTpl(); 		break;
					case 'office_contact' 	 	: $this -> OfficeContactTpl(); 		break;
					case 'history_contact'   	: $this -> HistoryContactTpl();	 	break;
					case 'reason_contact'	 	: $this -> ReasonContactTpl(); 		break; 
					case 'get_primary_phone'   	: $this -> PrimaryPhoneTpl(); 		break;
					case 'call_reason_text' 	: $this -> getContactReasonText();  break;
					case 'change_request'		: $this -> getCangeRequest(); 		break;
					Case 'send_request_item'	: $this -> sendCangeRequest(); 		break;
					case 'get_add_phone'        : $this -> getAdditionalPhone();    break;
					case 'get_phone_type'		: $this -> getPhoneTypeNumber();    break;
					case 'get_recording'	 	: $this -> getRecording(); 			break;	
					case 'play_recording'    	: $this -> playRecording(); 		break;
					case 'add_referal'			: $this -> addReferal();			break;
					case 'add_grid'				: $this -> addGrid();				break;
					case 'remarks'				: 
					// $this -> Remarks();				
					break;
					case 'xsellinfo'			: $this -> XsellView();				break;
				}
			}
		}
		
		function addGrid(){
			$content = array(
				'grid_id'=>$this->escPost('grid_id'),
				'ref_name'=>$this->escPost('ref_name'),
				'ref_phone1'=>$this->escPost('ref_phone1'),
				'ref_phone2'=>$this->escPost('ref_phone2'),
				'ref_phone3'=>$this->escPost('ref_phone3')
			);
			
			//array_push($this->idx,$content);
			
			print_r($content);
			
			echo"<tr>
					<td>No</td>
					<td>Referal Name</td>
					<td>Phone 1</td>
					<td>Phone 2</td>
					<td>Phone 3</td>
				</tr>";
			
			echo"<tr>
					<td>".$this->escPost('grid_id')."</td>
					<td>".$this->escPost('ref_name')."</td>
					<td>".$this->escPost('ref_phone1')."</td>
					<td>".$this->escPost('ref_phone2')."</td>
					<td>".$this->escPost('ref_phone3')."</td>
				</tr>";
		}
		
		function addReferal(){
			$this -> setCss();
			
		?>
			
			<table cellpadding="6px;" width="90%" >
				<tr>
					<td width="10%" nowrap>Referal Name </td>
					<td width="10%"><?php $this -> JPForm -> jpInput('Ref_Name','txt_input',NULL,NULL); ?></td>
					<td width="10%" nowrap>Phone 1 </td>
					<td width="10%"><?php $this -> JPForm -> jpInput('Ref_phone1','txt_input',NULL,NULL); ?></td>
					<td width="10%" nowrap>Phone 2 </td>
					<td width="10%"><?php $this -> JPForm -> jpInput('Ref_phone2','txt_input',NULL,NULL); ?></td>
					<td width="10%" nowrap>Phone 3 </td>
					<td width="10%"><?php $this -> JPForm -> jpInput('Ref_phone3','txt_input',NULL,NULL); ?></td>
				</tr>
			</table>
				
			<table cellpadding="6px;" width="90%" id="content_grid">
				<tr>
					
				</tr>
			</table>
		<?php
		}
		
		private function fileVoc($str){
			if($str!=''):
				$file_voc_loc = explode("/", $str);
				return $file_voc_loc[5].'/'.$file_voc_loc[6].'/'.$file_voc_loc[7].'/';				
			endif;
		}
		
		private function getPBX()
		{
			$sql = "select a.set_value from cc_pbx_settings a where a.pbx=1 and a.set_name='host'";
			$qry = $this -> query($sql);
			if( $qry -> result_num_rows() > 0 )
			{
				return $qry -> result_singgle_value();
			}
			else
				return '127.0.0.1';
		}
		
		private function getInfoRec(){
			$sql ="select * from cc_recording where id='".$_REQUEST['rec_id']."'";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$row = $this -> fetchrow($qry);
			if( $row ) : 
				return $row;
			endif;
		}
		
		function playRecording(){
			$rec = $this -> getInfoRec();
			$RecFileVoice = trim($this -> ServerIP.$this -> fileVoc($rec->file_voc_loc).$rec->file_voc_name);
		?>
			<table>
				<tr>
					<td valign="top" >
						<fieldset style="border:1px solid #dddddd;"> 	
							<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;">Play Recording </legend> 
							<div class="xbox-shadow" style="margin-top:10px;margin-bottom:10px;padding:0px;">
								<div class="box-shadow" style="border:0px solid #000;height:20px;padding:10px;width:320px;"> 
									<object id="obj" width="20" height="20" attr1="attrValue1" attr2="attrValue2"  classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"> 
										<param name="src" value="<?php echo $RecFileVoice; ?>"> 
										<param name="autoplay" value="true"> 
										<param name="param1" value="paramValue1"> 
										<param name="param2" value="paramValue2"> 
										<embed width="99%" height="20" src="<?php echo $RecFileVoice; ?>" autoplay="true" attr1="attrValue1" attr2="attrValue2" param1="paramValue1" param2="paramValue2"> </embed> 
									</object> 
									
									
								</div>	
								<span style="color:green;padding:8px;margin-top:150px;">Duration : ( <?php echo toDuration($rec->duration); ?> )</span>
							</div>
						</fieldset>	
					</td>
				</tr>	
			</table>
		<?php		
		}
		
		private function getQueryRec(){
			$sql = "select a.*, date_format(a.start_time,'%H:%i:%s %d/%m/%Y') as start_date   from cc_recording a  left join t_gn_customer b on a.assignment_data=b.CustomerId
						where a.assignment_data='".$this -> CustomerId."' order by a.id DESC";
							
			return $sql;
		}
		
		function getRecording(){
			
			?>
			<table>
				<tr>
					<td valign="top" id="xx"> 
						<fieldset style="border:1px solid #dddddd;"> 	
							<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;">Recording List </legend>
							<ul class="ul-recording">
								<?php
									$qry = $this -> execute($this->getQueryRec(),__FILE__,__LINE__);
									while( $rows = $this -> fetchrow($qry)){
										$exp = explode("_",$rows->file_voc_name);
										$new = $exp[1]."_".$exp[2]."_".$exp[3];
										echo "<li class=\"ul-recording\"><a class=\"a-recording\" href='javascript:void(0);' onclick=\"playRecording('".$rows->id."');\">".$new." - ".toDuration($rows->duration) ."  - {$rows->start_date}</a></li>";
									}
								?>
							</ul>	
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			
		}
		
		function getPhoneTypeNumber(){
		
			if( $this->havepost('phone_type') ):
			
				$phoneArray  = array(
					2=>'CustomerHomePhoneNum',
					3=>'CustomerMobilePhoneNum',
					4=>'CustomerWorkPhoneNum',
					5=>'CustomerWorkExtPhoneNum'
					
				);
				
				if( ($phoneArray[$this->escPost('phone_type')]!='') ):
					$sql = " SELECT a.".$phoneArray[$this->escPost('phone_type')]." as value_phone 
							FROM t_gn_customer a WHERE a.CustomerId='".$this -> CustomerId."'";
							
					
					echo $this -> valueSQL($sql);
				endif;
			endif;
		}
		
		function getCallReason()
		{
			$sql = "SELECT a.CallReasonCategoryId, a.CallReasonCategoryName from t_lk_callreasoncategory a 
					where a.CallReasonCategoryFlags = 1
					order by CallReasonCategoryOrder ASC ";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			while($row = mysql_fetch_assoc($qry))
			{
				$datas[$row['CallReasonCategoryId']] = $row['CallReasonCategoryName']; 
			}
			return $datas;
		}
		
		function sendCangeRequest(){
			if( $this -> havepost('item_customer') ):
				$V_CHG = array
				(
					'CustomerId' => $this -> escPost('item_customer'), 
					'ApprovalItemId' => $this -> escPost('item_value'), 
					'CreatedById' => $this -> getSession('UserId'), 
					'ApprovalOldValue' => 0, 
					'ApprovalNewValue' => $this -> escPost('item_new_value'), 
					'ApprovePhoneType' => $this -> escPost('item_phone_type'),
					'ApprovalCreatedTs' => date('Y-m-d H:i:s')
				);
				
				$query = $this -> set_mysql_insert("t_gn_approvalhistory",$V_CHG);
				//echo $this->sqlText;	
					if( $query ) : echo 1;
					else :
						echo 0;
					endif;
			endif;
		}
		
		
		function getContactReasonText()
		{
			$datas = array();
			$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc, a.CallReasonEvent 
					from t_lk_callreason  a 
					where a.CallReasonStatusFlag=1
					order by a.CallReasonCode asc";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['CallReasonId']] = $rows['CallReasonCode']."-".$rows['CallReasonDesc'];
			}
			return $datas;
		}
		
		function getWAReasonText()
		{
			$datas = array();
			$sql = "SELECT a.Id, a.Code, a.Desc
					FROM t_lk_wa_email a
					WHERE a.`Status`=1
					order by a.Code asc";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['Id']] = $rows['Desc'];
			}
			return $datas;
		}
		
	/** get last number and last action data by suspends ***/
	
		function getLastSuspendData()
		{
			if ( $this -> havepost('VerifiedStatus'))
			{
				$sql = " SELECT a.CallReasonId, b.b_number from t_gn_customer a 
						 LEFT JOIN cc_call_session b on a.CustomerId=b.assign_data
						 WHERE a.CustomerId='".$this -> escPost('customerid')."'
						 ORDER BY b.start_time DESC LIMIT 1 ";
						 
				return $this -> query($sql);
			}
			else{
				$sql = " SELECT a.CallReasonId, b.b_number from t_gn_customer a 
						 LEFT JOIN cc_call_session b on a.CustomerId=b.assign_data
						 WHERE a.CustomerId='".$this -> escPost('customerid')."'
						 ORDER BY b.start_time DESC LIMIT 1 ";
						 
				return $this -> query($sql);
			}
		}
		
		
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
								<select class="txt_input combo" id="cmb_cust_home_province" name="cmb_cust_home_province">
								<option value=""> -- Choose -- </option>
								<?php $this -> getValuePropince( $this -> getValue -> ProvinceId ); ?>	
								</select>
							</td>
						</tr>
						<tr>
							<td class="txt_header">Zip Code</td>
							<td>
								<input type="text" class="txt_input zip_code" name="text_cust_home_zip" id="text_cust_home_zip" value="<?php echo $this -> getValue -> CustomerZipCode; ?>">
							</td>
						</tr>
				</table>	
		 </fieldset>	
		
		<?	
		}
		
		function PrimaryPhoneTpl()
		{
			$datas = array();
			$sql = " select a.CustomerHomePhoneNum, a.CustomerMobilePhoneNum, a.CustomerWorkPhoneNum, a.CallReasonId 
					from t_gn_customer a WHERE a.CustomerId=".$this -> CustomerId."";
					
			//echo $sql;	
			$query = $this -> execute($sql,__FILE__,__LINE__);
			$rows  = $this ->fetchrow($query);
			
			$datas[$rows->CustomerHomePhoneNum]   = 'Home'; 
			$datas[$rows->CustomerMobilePhoneNum] = 'Mobile';
			$datas[$rows->CustomerWorkPhoneNum]   = 'Office';	
		?>	
				<select class="txt_input combo" id="call_primary_phone" onchange="setCallNumber(this.value);" <?php echo (in_array($rows->CallReasonId, $this -> LockStatus)?'disabled':'');?> >
					<option value=""> -- Choose --</option>
			<?php 
					foreach( $datas as $key => $val ):
						$v = ($this -> Masking -> setMaskText($key)?$this -> Masking -> setMaskText($key):'-');
						echo "<option value=\"{$key}\" > {$val} - {$v} </option>";
						//$this -> Masking -> setMaskText($row->CallNumber)?$this -> Masking -> setMaskText($row->CallNumber):'-'
				   endforeach;	
			?> 				
				</select>
			<?php		
		}
		
		function getAdditionalPhone()
		{
			$sql = "select a.CustomerHomePhoneNum2, a.CustomerMobilePhoneNum2, a.CustomerWorkPhoneNum2 
					from t_gn_customer a where a.CustomerId = '".$this -> CustomerId."'";
			$qry = $this->query($sql);
			
			foreach($qry->result_assoc() as $rows)
			{
				$datas = array(
					$rows['CustomerHomePhoneNum2'] 	 => "Home   - ".$rows['CustomerHomePhoneNum2'],
					$rows['CustomerMobilePhoneNum2'] => "Mobile - ".$rows['CustomerMobilePhoneNum2'],
					$rows['CustomerWorkPhoneNum2'] 	 => "Office - ".$rows['CustomerWorkPhoneNum2']
				);
			}
			
			$this -> JPForm -> jpCombo('add_phone','txt_input combo',$datas,NULL,'onchange="setCallNumber(this.value);"',(in_array($rows->CallReasonId, $this -> LockStatus)?'1':'0'));
		}
		
		function setCss(){
			?>
				<style>
					.txt_note { color:#000000; text-align:center;}
					.txt_header { color:#703c04; text-align:right;font-weight:bold;}
					.txt_input { color:#000000;font-size:11px; 
								 background:url('../gambar/input_bg.png'); text-align:left; height:18px;
								 width:160px; border:1px solid #c9bb81;padding-left:2px;}
					.zip_code { width:40px;}			 
					.address { width:200px;}		
					.combo { height:21px;width:150px;}
					.date { width:120px;}
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
		
		function HitungUmur($tgllhr) { 
			list($tgl,$bln,$thn) = explode('-',$tgllhr); 
			$lahir = mktime(0, 0, 0, (int)$bln, (int)$tgl, $thn); 
			//jam,menit,detik,bulan,tanggal,tahun 
			$t = time(); 
			$umur = ($lahir < 0) ? ( $t + ($lahir * -1) ) : $t - $lahir; $tahun = 60 * 60 * 24 * 365; 
			$tahunlahir = $umur / $tahun; 
			$umursekarang=floor($tahunlahir) ; 
	
			return $umursekarang;
		}
		
		function getGenderCustomer()
		{
			$sql = "select b.Gender from t_gn_customer a
					left join t_lk_gender b on a.GenderId=b.GenderId 
					where a.CustomerId = ".$this -> CustomerId." ";
			//echo $sql;
			$qry  = $this ->query($sql);
			return $qry -> result_get_value('Gender');	
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
			$sql = "select  a.ApprovalItemId,a.ApprovalItem  from t_lk_approvalitem a where a.ApprovalItemId=6";
			
			$qry = $this -> execute($sql,__FILE__,__LINE__);
				while( $row = $this ->fetchrow($qry)){
					echo "<option value=\"{$row->ApprovalItemId}\">{$row->ApprovalItem}</option>";
				
				}
		
		}
		
		function getPhoneType(){
			$sql = "SELECT a.PhoneType, a.PhoneDesc from t_lk_phonetype a 
						where a.FlagStatusActive=1 ";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			while($row= $this ->fetcharray($qry)){
				echo "<option value=\"{$row[0]}\">{$row[1]}</option>";
			}
		}
		
		function getValueTitle($SalutationId=''){
			
			$sql = "select a.SalutationId, a.SalutationId, a.Salutation from t_lk_salutation a order by a.SalutationId ASC";
			$query = $this ->execute($sql,__FILE__,__LINE__);
			while( $rows = $this->fetchrow($query) ){
				if( $rows->SalutationId == $SalutationId ){
					echo "<option value=\"{$rows->SalutationId}\" selected>{$rows->SalutationId} - {$rows->Salutation}</option>";
				}else{
					echo "<option value=\"{$rows->SalutationId}\">{$rows->SalutationId} - {$rows->Salutation}</option>";
				}
			}	
		}	
		
		function getValueGender($GenderId=''){
			
				$sql = "select a.GenderId, a.GenderCode, a.Gender from t_lk_gender a ";
				//echo $sql; 
				$qry = $this -> query($sql);
				while( $rows = $this->fetchrow($qry) ){
					if( $rows->GenderId == $GenderId ){
						echo "<option value=\"{$rows->GenderId}\" selected>{$rows->GenderId} - {$rows->Gender}</option>";
					}else{
						echo "<option value=\"{$rows->GenderId}\">{$rows->GenderId} - {$rows->Gender}</option>";
					}
				}	
		}	
		
		/** get cvalue gender ***/
		/*					
		function getValueGender($GenderId=''){
				$sql = "select a.GenderId, a.GenderCode, a.Gender from t_lk_gender a ";
				$qry = $this -> query($sql);
					foreach($qry-> result_assoc() as $rows ){
							$datas[$rows['GenderId']] = $rows['GenderCode'].' - '.$rows['Gender'];
							}		
												
								return $datas;
		}													
		*/						
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
	

	/** change Request **/
		function getCangeRequest(){
		
			$this -> setCss();
		?>
			<div class="box-shadow">
				<table cellpadding="2" align="center" width="99%">
					<tr>
						<td class="txt_header" style="height:40px;">Change Type </td>
						<td>
							<!--<select class="txt_input combo" style="width:auto;color:red;" name="cb_request_type" id="cb_request_type" 
								onchange="
								if(this.value=='6'){ doJava.dom('cb_phone_type').disabled=false;}
								else{ doJava.dom('cb_phone_type').disabled=true;}; getPhoneNumber(this.value);">
								<option value=""></option>
							</select>-->
							<!--edit to add phone only-->
							<select class="txt_input combo" style="width:auto;color:red;" name="cb_request_type" id="cb_request_type" disabled> 
										<?php $this -> getItemApprove(); ?>
							</select>
							
						</td>
					</tr>
					<tr>
						<td class="txt_header" style="height:40px;">Add Phone Type </td>
						<td>
							<select class="txt_input combo" style="width:165px;color:red;" name="cb_phone_type" id="cb_phone_type">
								<option value=""></option>
								<?php $this -> getPhoneType(); ?>
							</select>
						</td>
					</tr>
					<!--<tr>
						<td class="txt_header" style="height:40px;"> Approval Old Value</td>
						<td>
							<input type="text" id="txt_old_value" name="txt_old_value" class="txt_input" style="width:250px;">
						</td>
					</tr>-->
					<tr>
						<td class="txt_header" style="height:40px;">Add Phone No</td>
						<td>
							<input type="text" id="txt_new_value" name="txt_new_value" class="txt_input" onkeyup="Ext.Set(this.id).IsNumber();" style="width:250px;">
						</td>
					</tr>
					
				</table>
			</div>
		
			<?php
		}
		
/** get umur customers *************/
	
		function getSelectAge()
		{
			$array_result = $this -> Date -> set_date_diff( $this -> getSelectDb(),date('Y-m-d'));
			return $array_result['years'];
		}
		
/** get tanggal lahir customers *************/
		
		function getSelectDb()
		{
			$sql = "select a.CustomerDOB from t_gn_customer a where a.CustomerId='".$this -> CustomerId."'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				return $qry -> result_get_value('CustomerDOB');
			}	
		}	
		
	/** tpl default **/	
		function getCustomer(){
			$sql =" SELECT 
						a.*, b.AssignSelerId,
						c.CampaignNumber,
						c.CampaignName, 
						c.CampaignStartDate, 
						c.CampaignEndDate,
						DATE_FORMAT(a.CustomerDOB,'%Y-%m-%d') as CustomerDOB,
						IF(a.GenderId=1,'MALE',IF(a.GenderId=2,'FEMALE','-')) as Gender
					FROM t_gn_customer a
						INNER JOIN t_gn_assignment b on a.CustomerId= b.CustomerId
						LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId
						WHERE c.CampaignStatusFlag=1
						AND a.CustomerId = ".$this -> CustomerId."
						AND a.CampaignId = ".$this -> CampaignId." ";
			
			$query  = $this ->execute($sql,__FILE__,__LINE__);
			if( $query  ) :	 $this -> getValue = $this -> fetchrow($query); endif;
		}
		
		
		
		function DefaultContactTpl(){
			$this -> setCss();
		?>
			<script>
				$(function(){
					$('#txt_customer_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
				});
			</script>
			<fieldset> 
				<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;"> Customer Information</legend> 
			<table class="content_contact_default" width="100%" align="center" cellpadding="4px" border=0>
					<tr>
						<td class="txt_header" nowrap>Campaign No</td>
						<td><input type="text" class="txt_input"  name="txt_campaign_id" id="txt_campaign_id" value="<?php echo $this -> getValue -> CampaignNumber; ?>" disabled></td>
						<td class="txt_header" nowrap>Campaign Name</td>
						<td><input type="text" class="txt_input"  name="txt_campaign_name" id="txt_campaign_name" value="<?php echo $this -> getValue -> CampaignName; ?>" disabled></td>
					</tr>
					<tr>	
						<td class="txt_header" nowrap>Customer ID</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this -> getValue -> CustomerNumber; ?>" disabled></td>
						<td nowrap class="txt_header">First Name</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this -> getValue -> CustomerFirstName; ?>" disabled></td>
						
					</tr>
					
					<tr>
						<td class="txt_header" nowrap>Last Name</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this -> getValue -> CustomerLastName; ?>" disabled></td>
						<td class="txt_header" nowrap>Date Of Birth </td>
						<td><input type="text" class="txt_input date" name="txt_customer_dob" id="txt_customer_dob" value="<?php echo $this -> getValue -> CustomerDOB; ?>" disabled></td>
					</tr>
					
					<tr>
						<td class="txt_header" nowrap>Title</td>
						<td>
							<select class="txt_input combo"  id="cmb_customer_salut" name="cmb_customer_salut" >
								<option value=""> -- Choose -- </option>
								<?php $this -> getValueTitle( $this -> getValue -> SalutationId ); ?>
							</select>
						</td>
						<td class="txt_header" nowrap>Gender </td>
						<td>
							<input type="text" class="txt_input"  name="txt_gender_id" id="txt_gender_id" value="<?php echo $this -> getValue -> Gender; ?>" disabled>
						</td>
					</tr>
					<tr>
						<td class="txt_header" nowrap>Code</td>
						<td><input type="text" class="txt_input"  name="txt_code" id="txt_code" value="<?php echo $this -> getValue -> Code; ?>" disabled></td>
						<td class="txt_header" nowrap>Vintage</td>
						<td><input type="text" class="txt_input"  name="txt_vintage" id="txt_vintage" value="<?php echo $this -> getValue -> Vintage; ?>" disabled></td>
					</tr>
					
			</table>	
		 </fieldset>	
		
		<?	
		}
		
		function OfficeContactTpl(){
		?>
			<script>
				$(function(){
					$('#text_cust_office_expired').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
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
								<select class="txt_input combo"  id="cmb_cust_office_province" id="cmb_cust_office_province">
									<option value=""> -- Choose -- </option>
									<?php $this -> getValuePropince( $this -> getValue -> ProvinceId ); ?>	
								</select>
							</td>
						</tr>
						<tr>
							<td class="txt_header" nowrap>Zip Code</td>
							<td>
								<input type="text" class="txt_input zip_code" name="text_cust_office_zip" id="text_cust_office_zip" value="<?php echo $this -> getValue -> CustomerOfficeZipCode; ?>">
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
												, <span> Last Call Result </span> <b style="color:#e53b14;"> 
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
		
		function Remarks(){
		
	    ?>
	    	<fieldset>
	    	<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;">Other Information</legend>
	    		<table>
	    		<tr>
	    			<td>Remark 1</td>
	    			<td><textarea rows="2" cols="20"><?php echo $this->getValue->Remark_1;?></textarea></td>
	    			<td>Remark 2</td>
	    			<td><textarea rows="2" cols="20"><?php echo $this->getValue->Remark_2;?></textarea></td>
	    			<td>Remark 3</td>
	    			<td><textarea rows="2" cols="20"><?php echo $this->getValue->Remark_3;?></textarea></td>
	    		</tr>
	    		<tr>
	    			
	    			<td>Remark 4</td>
	    			<td><textarea rows="2" cols="20"><?php echo $this->getValue->Remark_4;?></textarea></td>
	    			<td>Remark 5</td>
	    			<td><textarea rows="2" cols="20"><?php echo $this->getValue->Remark_5;?></textarea></td>
	    		</tr>
	    		
	    		</table>
	    	</fieldset>
	    <?php
		} //end remarks
		
		function XsellView(){
			$len_digit = strlen($this -> getValue -> CustomerCreditCardNum) - 4;
			$len_saving_digit = strlen($this -> getValue -> accountnumber) - 4;
	    ?>
	    	<fieldset>
	    	<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;">XSell Information</legend>
	    		<table class="content_contact_default" width="100%" align="center" cellpadding="4px" border=0>
					<tr>
						<td class="txt_header" nowrap>Credit Card Number</td>
						<td><input type="text" class="txt_input"  name="txt_campaign_id" id="txt_campaign_id" value="<?php echo $this->Masking->setMaskText($this -> getValue -> CustomerCreditCardNum,"",$len_digit) ?>" disabled></td>
						<td class="txt_header" nowrap>Credit Card Expired</td>
						<td><input type="text" class="txt_input"  name="txt_campaign_name" id="txt_campaign_name" value="<?php echo $this -> getValue -> CustomerCreditCardExpDate; ?>" disabled></td>
					</tr>
					<tr>	
						<td class="txt_header" nowrap>Account Number</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this->Masking->setMaskText($this -> getValue -> accountnumber,"",$len_saving_digit); ?>" disabled></td>
						<td nowrap class="txt_header">Bank</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this -> getValue -> Xsellbank; ?>" disabled></td>
					</tr>
					<tr>	
						<td class="txt_header" nowrap>Email</td>
						<td><input type="text" class="txt_input"  name="txt_customer_id" id="txt_customer_id" value="<?php echo $this -> getValue -> CustomerEmail; ?>" disabled></td>
					</tr>
			</table>
	    	</fieldset>
	    <?php
		} //end XSell
		
		
		function ReasonContactTpl()
		{
			if( $this -> havepost('VerifiedStatus') )
			{
				$result_rows = $this -> getLastSuspendData();
				// $CallReasonId = $result_rows -> result_get_value('CallReasonId');
			}	
		?>
			<script>
				var VERIFIED = '<?php echo $this -> escPost('VerifiedStatus');?>';
				
				var getprimaryPhone = function(){
					$(function(){
						$('#phone_primary_number').load(InitPhp+"action=get_primary_phone&customerid="+CustomerId+"&campaignid="+CampaignId);
					});
				}
				
				var getaddPhone = function(){
					$(function(){
						$('#phone_additional_number').load(InitPhp+"action=get_add_phone&customerid="+CustomerId+"&campaignid="+CampaignId);
					});
				}
				
				$(function(){
					getprimaryPhone();
					getaddPhone();
					$('#date_call_later').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
					if( VERIFIED!='' )
					{
						doJava.dom('create_policy').disabled = false;
					}
				});
				
			</script>
			<fieldset>
				
				<div style="overflow:auto;">
						<table cellpadding="6px;" border=0>
							<!--<tr>
								<td nowrap style="height:28px;" class="txt_header">Phone Number </td>
								<td nowrap style="height:28px;" id="phone_primary_number"> 
									<select class="txt_input combo">
										<option value=""> -- Choose --</option>
									</select>	
								</td>
							</tr>	
							<tr>
								<td nowrap style="height:28px;" class="txt_header">Add Phone Number </td>
								<td nowrap style="height:28px;" id="phone_additional_number"> 
									<select class="txt_input combo">
										<option value=""> -- Choose --</option>
										
									</select></td>
							</tr>	-->
							<tr>
								<td style="height:18px;">&nbsp;</td>
								<!--<td nowrap style="height:18px;">
									<img src="../gambar/PhoneCall.png" width="35px" height="35px" style="cursor:pointer;" title="Dial..." onclick="dialCustomer();">
									<img src="../gambar/HangUp.png" width="35px" height="35px" style="cursor:pointer;" title="Hangup..." onclick="hangupCustomer();">
									
								</td>-->
							</tr>	
							<tr style="Display:none;">
								<td style="height:28px;" class="txt_header" valign="top">Call Status </td>
								<td style="height:28px;"> 
									<?php  
									$CallReasonId = $this -> Entity -> getCustomerReason($this -> CustomerId);
									foreach( $this -> getCallReason() as $kode => $name )
									{
										$IsFlags = ($kode==$CallReasonId['CategoryId']?true:false);
										$this -> JPForm -> jpRadio("call_status", $css="", $kode, 'onchange="getCallReasontext(this.value,this.checked);"',$IsFlags,"<b>$name</b>", true);
										echo "<br>";
									} 
									?>
								</td>
							</tr>	
							<tr>
								<td style="height:28px;" class="txt_header">Call Result</td>
								<td style="height:28px;" id="contact_reason_text"> 
									<?php //echo $this -> JPForm -> jpCombo('call_result','txt_input combo',$this -> getContactReasonText(),$CallReasonId,'onChange="getActionNext(this.value);"',1);?> 
									<?php 
									$CallReasonIdX = $this -> Entity -> getCustomerReason($this -> CustomerId);
									//print_r($CallReasonIdX);
									$this -> JPForm -> jpCombo('call_result','txt_input combo',$this -> getContactReasonText(),$CallReasonId['ReasonId'],'onChange="getActionNext(this.value);"',1);
									
									?> 
								</td>
							</tr>
							<tr>
								<td style="height:28px;" class="txt_header">QA Result</td>
								<td style="height:28px;" id="contact_reason_text"> 
									<?php //echo $this -> JPForm -> jpCombo('call_result','txt_input combo',$this -> getContactReasonText(),$CallReasonId,'onChange="getActionNext(this.value);"',1);?> 
									<?php 
									$CallReasonIdWa = $this -> Entity -> getWAReason($this -> CustomerId);
									//print_r($CallReasonIdX);
									$this -> JPForm -> jpCombo('wa_result','txt_input combo',$this -> getWAReasonText(), $CallReasonIdWa['ReasonId'],'onChange="getActionNext(this.value);"');
									
									?> 
								</td>
							</tr>	
							
							<!--<tr>
								<td style="height:28px;" class="txt_header">Call Later (Insert For Reminder Call)</td>
								<td style="height:28px;"> 
									<input type="text" name="date_call_later" id="date_call_later" class="txt_input" style="width:120px;margin:2px;" disabled> <br>
									<input type="text" name="hour_call_later" id="hour_call_later" class="txt_input" style="margin:2px;width:20px;" disabled> :
									<input type="text" name="minute_call_later" id="minute_call_later" class="txt_input" style="margin:2px;width:20px;" disabled> 
								</td>
							</tr>-->
							<!--<tr>
								<td style="height:28px;" class="txt_header">&nbsp;</td>
								<td ><input type="checkbox" name="create_policy" id="create_policy" onchange="CreatePolicy(this.checked);" disabled> GoTo Closing</td>
							</tr>	-->
							 
							<tr><td style="height:28px;" colspan="2" align="center"> 
									<span style="padding:4px;" class="txt_header"> Note </span>
									 <textarea id="call_remarks" name="call_remarks" onkeyup="this.value=this.value.toUpperCase();" style="height:80px;background-color:#fbf9f7;color:#000000;font-family:Arial;font-size:11px;border:1px solid #dddddd;height:110px;margin-left:4px;width:250px;"></textarea>
									<!--
									 <div style="height:200px;overflow:auto;">
									 <span style="padding:4px;" class="txt_header"> Note </span>
									 <textarea id="call_remarks" name="call_remarks" onkeyup="this.value=this.value.toUpperCase();" style="height:80px;background-color:#fbf9f7;color:#000000;font-family:Arial;font-size:11px;border:1px solid #dddddd;height:110px;margin-left:4px;width:200px;"></textarea>
									 <span style="padding:4px;" class="txt_header"> Note2 </span>
									 <textarea id="call_remarks2" name="call_remarks" onkeyup="this.value=this.value.toUpperCase();" style="height:80px;background-color:#fbf9f7;color:#000000;font-family:Arial;font-size:11px;border:1px solid #dddddd;height:110px;margin-left:4px;width:200px;"></textarea>
									 <span style="padding:4px;" class="txt_header"> Note3 </span>
									 <textarea id="call_remarks3" name="call_remarks" onkeyup="this.value=this.value.toUpperCase();" style="height:80px;background-color:#fbf9f7;color:#000000;font-family:Arial;font-size:11px;border:1px solid #dddddd;height:110px;margin-left:4px;width:200px;"></textarea>
									 <span style="padding:4px;" class="txt_header"> Note4 </span>
									 <textarea id="call_remarks4" name="call_remarks" onkeyup="this.value=this.value.toUpperCase();" style="height:80px;background-color:#fbf9f7;color:#000000;font-family:Arial;font-size:11px;border:1px solid #dddddd;height:110px;margin-left:4px;width:200px;"></textarea>
									 <span style="padding:4px;" class="txt_header"> Note5 </span>
									 <textarea id="call_remarks5" name="call_remarks" onkeyup="this.value=this.value.toUpperCase();" style="height:80px;background-color:#fbf9f7;color:#000000;font-family:Arial;font-size:11px;border:1px solid #dddddd;height:110px;margin-left:4px;width:200px;"></textarea>
								</div>	 
									-->
								</td>
							</tr>	
							 
							<tr>
								<td colspan=2 align="center">
									 <a href="javascript:void(0);" id="buttonSave" style="float:right;margin-right:8px;margin-top:5px;" class="sbutton" onclick="saveActivity();"><span>&nbsp;Save</span></a>
									 <a href="javascript:void(0);" id="buttonSave" style="float:right;margin-right:8px;margin-top:5px;" class="sbutton" onclick="NextCustomers();"><span>&nbsp;Next</span></a>
									 <a href="javascript:void(0);" id="buttonCancel" style="float:right;margin-right:8px;margin-top:5px;" class="sbutton" onclick="CancelActivity();"><span>&nbsp;Exit</span></a>
								</td>
							</tr>
							
							
						</table>	
						
					</div>	
			</fieldset>	
		<?
		}
	}
	
	$ContactDetail = new ContactDetail();
	$ContactDetail -> index();

?>