<?php

	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../plugin/lib.form.php");
	

	/*
	 *	class untuk upload data campaign dan setup
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	class CampaignUpload extends mysql{
		var $action;
		var $JP_Plugin;
		
		function __construct(){
			
			parent::__construct();	
			$this -> action = $this->escPost('action'); 
			$this -> JP_Plugin = new jpForm(true);
		}
		
	/* function set style css **/
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:190px;font-size:11px;height:20px;background-color:#fffccc;}
					.input_text { border:1px solid #dddddd;width:190px;font-size:11px;height:16px;background-color:#fffccc;}
					.text_header { text-align:right;color:red;font-size:12px;}
					.select_multiple { border:1px solid #dddddd;height:100px;font-size:11px;background-color:#fffccc;}
				</style>
				
			<!-- stop: css -->
		<?php }
		
	/** function tpl dialog setup upload **/
	
		function initClass(){
			if( $this -> havepost('action')):
				switch( $this->action){
					case 'tpl_campaign'		: $this -> tplDialog(); 		break;
					case 'tpl_edit'			: $this -> tplEdit(); 			break;
					case 'tpl_upload'  		: $this -> tplUpload(); 		break;	
					case 'save_cmp_upload'	: $this -> saveCmpUpload();   	break;
					case 'getProductByCore' : $this -> getProductByCore(); 	break;
					case 'extends_date'		: $this -> extendsDates(); 		break;
				}
			endif;
		} 
		
	function next30date($date)
	{
		$next_date = $date;
		for($d=0; $d<=60; $d++)
		{
			$next_date = $this -> nextdate($next_date);
		}
		return $next_date." 00:00:00";
	}

	function convertDateSendiri($tgl)
	{
		print_r(explode(' ',$tgl));
	}
		
	/**
		extendsDates 
		action:'extends_date',
		CampaignNumber:CampaignNumber,
		CampaignExtends:CampaignExtends
	*/	
	
		function extendsDates(){
		
			$upload_cmp_type		 = $this -> escPost('upload_cmp_type');
			$upload_cmp_built_type	 = $this -> escPost('upload_cmp_built_type');
			$upload_cmp_reupload	 = $this -> escPost('upload_cmp_reupload');
			$upload_cmp_category	 = $this -> escPost('upload_cmp_category');
			$upload_cmp_move_product = explode(",",$this->escPost('upload_cmp_move_product'));
			$upload_cmp_name		 = $this -> escPost('upload_cmp_name');
			$upload_cmp_camtype		 = $this -> escPost('upload_cmp_camtype');
			$upload_cmp_system		 = $this -> escPost('upload_cmp_system');
			$upload_cmp_status		 = $this -> escPost('upload_cmp_status');
			$upload_cmp_reason		 = $this -> escPost('upload_cmp_reason');
			$expired_date		  	 = $this ->Date->exp_date_indo($this -> escPost('expired_date'));
			
			//$this->convertDateSendiri($this -> escPost('expired_date'));
			//echo $expired_date;
			
			if( $this -> havepost('CampaignNumber')):
			
				if( $this -> havepost('CampaignExtends') && $this -> escPost('CampaignExtends')!=''){ 
					$datas = array(
							'CampaignTypeId' => $upload_cmp_type,
							'BuildTypeId' => $upload_cmp_built_type,
							'CategoryId' => $upload_cmp_category,
							'CignaCampTypeId'=>$upload_cmp_camtype,
							'CignaSystemId' => $upload_cmp_system,
							'ReUploadReasonId' =>$upload_cmp_reason,
							'CampaignName' => $upload_cmp_name,
							//'CampaignStartDate' => date('Y-m-d h:i:s'),
							'CampaignExtendedDate' => $this ->formatDateEng($_REQUEST['CampaignExtends']),
							'CampaignReUploadFlag' => $upload_cmp_reupload,
							'CampaignEndDate' => $expired_date,
							'CampaignStatusFlag' => 1
					);
				}
				else{
					$datas = array(
							'CampaignTypeId' => $upload_cmp_type,
							'BuildTypeId' => $upload_cmp_built_type,
							'CategoryId' => $upload_cmp_category,
							'CignaCampTypeId'=>$upload_cmp_camtype,
							'CignaSystemId' => $upload_cmp_system,
							'ReUploadReasonId' =>$upload_cmp_reason,
							'CampaignName' => $upload_cmp_name,
							'CampaignStartDate' => date('Y-m-d h:i:s'),
							'CampaignReUploadFlag' => $upload_cmp_reupload,
							'CampaignStatusFlag' => $upload_cmp_status,
							'CampaignEndDate' => $expired_date
					);
				}
			
				$where = array('CampaignNumber'=> $this ->escPost('CampaignNumber'));
				$query = $this -> set_mysql_update('t_gn_campaign',$this -> SQLnull($datas),$where);
				
					if( $query ) : echo 1;
					else :
						echo 0;
					endif;	
			
			endif;
		}
	
	/**
		getCampaign
		On ajax handler 
		:)
	*/	
	
		private function getCampaign(){
			$sql = "select * from t_gn_campaign where CampaignNumber= '".$this -> escPost('CampaignNumber')."' ";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$row = $this -> fetchrow($qry);
			if( $row ){
				return $row;
			}
			
		}
		
	
	/**
		Mookup form Edit screen 
		On ajax handler 
		:)
	*/	
		function tplEdit(){
			$this -> setCss();
			$campaign = $this -> getCampaign();
			global $db;
		?>
			<script type="text/javascript">
				$(function() {
					doJava.dom('cmp_upload_reason').disabled=true;
					$("#expired_date").datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy'});
					$("#extends_date").datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy'});
					
				});
			</script>
			<div class="box-shadow" style="margin-top:10px;">
		
			<table border=0 width="99%" align="center" cellpadding="9px" style="margin-top:-1px;">
				<tr>
					<!-- start : left layout -->	
						<td valign="top">
							<table cellpadding="6px">
								<tr style="display:none;">
									<td class="text_header">* Campaign ID. </td>
									<td><input type="text" name="cmp_upload_id" id="cmp_upload_id" value="<?php echo $campaign->CampaignNumber; ?>" class="input_text" disabled></td>
								</tr>
								
								<tr style="display:none;">
									<td class="text_header">* Campaign Type</td>
									<td><?php $this -> JP_Plugin -> jpMultiple('cmp_camptype_id', 'select', $this -> getCampaignType(),$campaign->CampaignTypeId); ?></td>
								</tr>
								
								<tr style="display:none;">
									<td class="text_header" style="color:#000000;"> Built Type</td>
									<td><?php $this -> JP_Plugin -> jpCombo('cmp_upload_builtype', 'select', $this -> getBuiltType(), $campaign -> BuildTypeId);?></td>
								</tr>
								
								<tr style="display:none;">
									<td class="text_header">* Re-Upload Campaign</td>
									<td><?php $this -> getReupload($campaign->CampaignReUploadFlag); ?></td>
								</tr>
								<tr>
									<td class="text_header">* Campaign Name</td>
									<td><input type="text" name="upload_cmp_name" id="upload_cmp_name" class="input_text" value="<?php echo $campaign->CampaignName; ?>"></td>
								</tr>
								<tr>
									<td class="text_header">* Product</td>
									<td><?php $this -> JP_Plugin -> jpMultiple('cmp_upload_product', 'select_multiple',$this -> getProduct()); ?></td>
								</tr>
								<tr>
									<td class="text_header">* Status Active</td>
									<td><?php $this -> JP_Plugin -> jpCombo('cmp_upload_status','select',$this-> getStatusActive())?></td>
								</tr>
								<tr style="display:none;">
									<td class="text_header">* Date Extends</td>
									<td><input type="text" name="extends_date" id="extends_date" class="input_text" value=""></td>
								</tr>
								
							</table>
						</td>
					<!-- stop : left layout -->
					
					
					<!-- start : right layout -->
						<td valign="top">
							<table cellpadding="6px">
								<tr style="display:none;">
									<td class="text_header">* Category</td>
									<td><?php $this -> getCategory($campaign->CategoryId);?></td>
								</tr>
								<tr>
									<td class="text_header">* Date Expired</td>
									<td><input type="text" name="expired_date" id="expired_date" class="input_text" value="<?php echo $db -> Date -> date_time_indonesia($campaign->CampaignEndDate); ?>"></td>
								</tr>
								<tr style="display:none;">
									<td style="display:none;"></td>
									<td><?php $this-> getCampType($campaign->CignaCampTypeId); ?></td>
								</tr>
								<tr>
									<td class="text_header">
										<input type="button" value=">>" Onclick="cbMoveOn();" ><br>
										<input type="button" value="<<" Onclick="cbRemoveOn();">
									</td><td><?php $this->getMoveProduct($campaign->CampaignNumber); ?></td>
								</tr>
								
								<tr style="display:none;">
									<td class="text_header" style="display:none;">* System</td>
									<td><?php $this-> getSystem($campaign->CignaSystemId); ?></td>
								</tr>
								
								<tr style="display:none;">
									<td class="text_header">* Re-Upload Reason</td>
									<td><?php $this-> getReuploadReason($campaign->ReUploadReasonId); ?></td>
								</tr>
								
							</table>
						</td>
					<!-- stop : right layout -->
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="float:right;border:0px solid #000;">
						<a href="javascript:void(0);" class="sbutton" onclick="UpdateCmpUpload();"><span>&nbsp;Update</span></a>
					</td>
				</tr>
				
			</table>
			
			</div>
		
		
		<?php }
		
		
	/**
		Mookup form Edit screen 
		On ajax handler 
		:)
	*/	
	
		function getMaxLastCampaign()
		{
			$sql = " select IF( max(a.CampaignId) is null,1,max(a.CampaignId)+1) from t_gn_campaign a";
			$result = $this->genCmpNumber($this ->valueSQL($sql));
			return $result;
		}
	
	/**
		Mookup form Edit screen 
		On ajax handler 
		:)
	*/		
		function genCmpNumber($last_campaign_id='')
		{
			$cmp_id= $last_campaign_id;
			$years = date('Y');
			$result = '';
			$prefix = substr($years,2,2).'0000'; 
			$maxLength = 6;
			
			if($cmp_id!=''){
				$result = substr($prefix,0,(strlen($prefix)-strlen($cmp_id)));
				$result.= $cmp_id;	
			}
			$newstring = $result;
				if( strlen($newstring)== $maxLength) :
					return $newstring;
				else:
					return null;
				endif;	
			
		}	
	    
		
	
	
	/* 
	*  set to NULL (empty MYSQL ) 
	*/
		
		private function contextNull($datas)
		{
			$clearNull = array(); 
			foreach( $datas as $key=>$value){
				if(trim($value)!='' && !empty($value)):
					$clearNull[$key] = $value;	
				endif;
			}
			return $clearNull;
		}
	
	 /** insert tgnCampaignID **/	
	
	function TgnCampaignId($datas='')
	{
		if( is_array($datas))
		{
			$qry = $this -> set_mysql_insert('t_gn_campaign',$this->contextNull($datas) );
			//echo $this->sqlText;
			if( $qry )
			{
				return true; 
			}
			else{
				return false;
			}

		}
		else			
			return false;
	}
	
 /** insert tgnCampaignProduct **/
 
	function TgnCampaignProduct($V_VALUES='')
	{
		if( ( count($V_VALUES)>0 ) && (is_array($V_VALUES)) ) :
			$datas = array(
				'CampaignId' => $V_VALUES['CampaignId'], 
				'ProductId' => $V_VALUES['ProductId']
			);
			
			
			$query = $this ->set_mysql_insert('t_gn_campaignproduct',$datas);
				
			if( $query ) return true;
			else
				return false;
		else :	
				return false;
		endif;
		
	}

	
	/* upload campaign **/
	
		private function saveCmpUpload(){
		
			$upload_cmp_id			 = $this -> escPost('upload_cmp_id');
			$upload_cmp_type		 = $this -> escPost('upload_cmp_type');
			$upload_cmp_built_type	 = $this -> escPost('upload_cmp_built_type');
			$upload_cmp_date_expired = $this -> ubahLimiterTgl($this->escPost('upload_cmp_date_expired'),'/','-');
			$upload_cmp_reupload	 = $this -> escPost('upload_cmp_reupload');
			$upload_cmp_category	 = $this -> escPost('upload_cmp_category');
			$upload_cmp_move_product = explode(",",$this->escPost('upload_cmp_move_product'));
			$upload_cmp_name		 = $this -> escPost('upload_cmp_name');
			$upload_cmp_camtype		 = $this -> escPost('upload_cmp_camtype');
			$upload_cmp_system		 = $this -> escPost('upload_cmp_system');
			$upload_cmp_status		 = $this -> escPost('upload_cmp_status');
			$upload_cmp_reason		 = $this -> escPost('upload_cmp_reason');
			$upload_cmp_extdate	     = $this -> escPost('upload_cmp_extends_date'); 
			
			
		/** set data to insert to campaign **/
		
			$datas = array(
				'CampaignTypeId' => $upload_cmp_type,
				'BuildTypeId' => $upload_cmp_built_type,
				'CategoryId' => $upload_cmp_category,
				'CignaCampTypeId'=>$upload_cmp_camtype,
				'CignaSystemId' => $upload_cmp_system,
				'ReUploadReasonId' =>$upload_cmp_reason,
				'CampaignNumber' => $this -> getMaxLastCampaign(),
				'CampaignName' => $upload_cmp_name,
				'CampaignStartDate' => date('Y-m-d h:i:s'),
				'CampaignEndDate' => $upload_cmp_date_expired,
				'CampaignExtendedDate' => $upload_cmp_extdate,
				'CampaignReUploadFlag' => $upload_cmp_reupload ,
				'CampaignStatusFlag' => $upload_cmp_status
			);
			
			//print_r($datas);
			
			if( $this -> TgnCampaignId($datas) ){
				$LastCampaignId = $this -> get_insert_id();
			}
			
		
		/** set data to insert Assosiasi table  **/ 
			
			if( $LastCampaignId >0 )
			{
				$i = 0;
				foreach($upload_cmp_move_product as $aKey => $bKey )
				{
					$VALUES_DATA[$i]= array( 
						'CampaignId' => $LastCampaignId, 
						'ProductId' => $bKey
					);
						
					if( $this -> TgnCampaignProduct($VALUES_DATA[$i]) ) $i++;
				}
					
				if( $i>0 ) echo 1;
				else echo 0;
			}
			else{
				echo 0;
			}
			
		}
		
	/** get max number **/
		function getMaxNumber()
		{
			$sql =" select (max(a.CampaignNumber)+1)  from t_gn_campaign  a";
			return $this -> valueSQL($sql);
		}
		
	/** function getCampaignType**/
	
		private function getCampaignType($opt='')
		{ 
			$sql = " SELECT a.CampaignTypeId, a.CampaignTypeCode, a.CampaignTypeDesc 
					 FROM t_lk_campaigntype a  ORDER BY a.CampaignTypeId ASC ";
					 
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['CampaignTypeId']] = $rows['CampaignTypeCode']." - ".$rows['CampaignTypeDesc'];
			}
			return $datas;
			
		}
	/** function getBuiltType **/	
	
		private function getBuiltType($opt='')
		{ 
			$sql = " SELECT a.BuildTypeId, a.BuildType FROM t_lk_buildtype a ORDER BY a.BuildTypeId ASC";
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['BuildTypeId']] = $rows['BuildType'];	
			}

			return $datas;
		}
		
	/** function getReupload **/	
	
		private function getReupload($opt=''){ 
			$array_upload = array(0=>'NO',1=>'YES');  
		?>
			<select class="select" id="cmp_upload_reupload" name="cmp_upload_reupload" onChange="cbEvent(this.value);">
				<option value="">-- Choose --</option>
				<?php foreach($array_upload as $key=>$val): ?> 
					<?php
						if( $opt==$key){
							echo "<option value=\"{$key}\" selected>{$val}</option>";
						}
						else{
							echo "<option value=\"{$key}\">{$val}</option>";
						}
					
					?>
				<?php endforeach; ?>	
			</select>
		<?php }
		
	/** function getCategory **/	
	
		private function getCampType()
		{ 
			$datas = array();
			$sql = "select a.CignaCampTypeId, a.CignaCampType from t_lk_cignacamptype a";
			$qry = $this -> query($sql);
			foreach($qry  -> result_assoc() as $rows ){
				$datas[$rows['CignaCampTypeId']] = $rows['CignaCampType']; 
			}
			return $datas;
		}
		
	/** function getCategory **/	
	
		private function getSystem($opt=''){ 
		?>
			<select class="select" id="cmp_upload_cignasystem" name="cmp_upload_cignasystem">
				<option value="">-- Choose --</option>
				<?php
					
					$sql = "select a.CignaSystemId, a.CignaSystemCode, a.CignaSystem from t_lk_cignasystem a";
					$qry = $this -> execute($sql,__file__,__LINE__);
					while( $row = $this->fetchrow($qry)){
						if( $row->CignaSystemId==$opt){
							echo "<option value=\"{$row->CignaSystemId}\" selected>{$row->CignaSystemCode} - {$row->CignaSystem}</option>";
						}
						else{
							echo "<option value=\"{$row->CignaSystemId}\">{$row->CignaSystemCode} - {$row->CignaSystem}</option>";
						}
					}	
				
				?>
			</select>
		<?php }

	
	/** function getCategory **/	
	
		private function getStatusActive($opt='')
		{ 
			return array(0=>'Not Active',1=>'Active');
		}
		
	/** function getCategory **/	
	
		private function getReuploadReason($opt=''){ ?>
			<select class="select" id="cmp_upload_reason" name="cmp_upload_reason" >
				<option value="">-- Choose --</option>
				<?php
					$sql = "select a.ReUploadReasonId, a.ReUploadReason from t_lk_reuploadreason a ";
					$qry = $this -> execute($sql,__file__,__LINE__);
					while( $row = $this->fetchrow($qry)){
						echo "<option value=\"{$row->ReUploadReasonId}\">{$row->ReUploadReason}</option>";
					}
				?>
			</select>
		<?php }
	

	/** function getCategory **/	
	
		private function getCategory($opt=''){ ?>
			<select class="select" name="cmp_upload_category" id="cmp_upload_category">
				<option value="">-- Choose --</option>
				<?php
					$sql = "select a.CampaignTypeId, a.CampaignTypeDesc from t_lk_campaigntype a";
					$qry = $this -> execute($sql,__file__,__LINE__);
					while( $row = $this->fetchrow($qry)){
						if( $opt==$row->CampaignTypeId) {
							echo "<option value=\"{$row->CampaignTypeId}\" selected>{$row->CampaignTypeDesc}</option>";
						}
						else{
							echo "<option value=\"{$row->CampaignTypeId}\">{$row->CampaignTypeDesc}</option>";
						}
					}	
				?>
				
			</select>
		<?php }		
		
	/** function getCategory **/	
	
		private function getProduct()
		{ 
			$sql = "select  a.ProductId, a.ProductCode, a.ProductName from t_gn_product a where a.ProductStatusFlag=1";
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows)
			{
				$datas[$rows['ProductId']] = $rows['ProductCode']." - ".$rows['ProductName']; 
			}
			return $datas;
		}	
		
	
		
		function getProductByCore($opt=''){ 
			$sql = " select b.ProductId,  b.ProductCode, b.ProductName from t_gn_campaignproduct a
						left join t_gn_product b 
						on a.ProductId=b.ProductId
						where a.CampaignId='".$this->escPost('cmp_core')."' ";

			
	
		?>
			<select  class="select" name="act_upload_product" id="act_upload_product" style="width:auto;height:auto;" multiple disabled>
				<?php
					$qry = $this -> execute($sql,__file__,__LINE__);
					while( $row = $this->fetchrow($qry)){
						echo "<option value=\"{$row->ProductId}\">{$row->ProductCode} - {$row->ProductName} </option>";
					}	
				
				?>
			</select>
		<?php }	
		
		
	/** function getCategory **/	
	
		private function getMoveProduct($opt=''){ ?>
			<select  class="select_multiple" name="move_on_product" id="move_on_product" multiple="true">
			</select>
		<?php }
		
		
	/** get cmpcore *****/
	
		private function getCmpCore(){
		
			$sql = " select a.CampaignId, a.CampaignNumber, a.CampaignName from t_gn_campaign a 
					 where a.CampaignStatusFlag=1";
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['CampaignId']] = $rows['CampaignNumber']." - ".$rows['CampaignName'];
			}
			
			return $datas;
		}

		private function tplUpload()
		{ 
		$this -> setCss();
		?> 
			<div class="box-shadow" style="margin-top:10px; ">
			<form action="javascript:void(0);"  id="uploadform" method="POST" >	
				<table border="0" cellpadding="8px" align="cnter">
					
					<tr>
						<th nowrap class="text_header"> File Name </th>
						<td><input  type="file" name="fileToupload[]" id="fileToupload"  size="47" style="background-color:#eeeeee;font-size:11px;border:1px solid #dddddd;height:24px;width:300px;" onChange="JavaScript:AjaxUploads.UploadInfo();"/></td>
						<td rowspan="4">
							<fieldset class="box-shadow" style="display:none;">
								<div id="fileName" style="font-family:Trebuchet MS;color:red;line-height:20px;"></div>
								<div id="fileSize" style="font-family:Trebuchet MS;color:red;line-height:20px;"></div>
								<div id="fileType" style="font-family:Trebuchet MS;color:red;line-height:20px;"></div>
								<div id="progressNumber"></div>
								<progress id="prog" value="0" max="100.0"></progress>
							</fieldset>	
							<span id="loadings_gambar"  style="display:none;color:red;"> <img src="../gambar/loading.gif"> loading...</span>
						</td>
					</tr>
					<tr>
						<th nowrap class="text_header"> Template  </th>
						<td><?php echo $this -> JP_Plugin -> jpCombo('template_name','select', $this -> Entity-> getTemplate());?></td>
					</tr>
					<tr>
						<th nowrap class="text_header"> Campaign ID  </th>
						<td><?php echo $this -> JP_Plugin -> jpCombo('act_cmp_core','select', $this -> getCmpCore(),NULL,'onChange="getProductCode(this.value);"');?></td>
					</tr>
					<tr>
						<th class="text_header" nowrap> Product </th>
						<td><span id="html_product_code"> <?php echo $this -> JP_Plugin -> jpCombo('act_upload_product','select', $this -> getCmpCore(),NULL,'onChange="getProductCode(this.value);"');?></span></td>
					</tr>
					<tr>
						<th nowrap></th>
						<td>
							<a href="javascript:void(0);" class="sbutton" onclick="proses();"><span>&nbsp;Prosess</span></a>
						</td>
					</tr>
				</table>
            </form>
			</div>
			
		<? }	
			
		
	/** function tpl dialog setup upload **/
	
		function tplDialog(){ 
			global $db;
			$this -> setCss();
		?>
			<script>
				$(function() {
					doJava.dom('cmp_upload_reason').disabled=true;
					var date = new Date();
					$("#expired_date").datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy',changeMonth: true,changeYear: true,yearRange:date.getFullYear()+':3000'});
					$("#extends_date").datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy'});
					
				});
			</script>
			<div class="box-shadow" style="margin-top:10px;">
		
			<table border=0 width="99%" align="center" cellpadding="9px" style="margin-top:-1px;">
				<tr>
					<!-- start : left layout -->	
						<td valign="top">
							<table cellpadding="6px">
								<tr style="display:none;">
									<td class="text_header">* Campaign ID. </td>
									<td><input type="text" name="cmp_upload_id" id="cmp_upload_id" value="<?php echo $this->getMaxLastCampaign(); ?>" class="input_text" disabled></td>
								</tr>
								<tr style="display:none";>
									<td class="text_header" >* Campaign Type</td>
									<td><?php $this -> JP_Plugin -> jpCombo('cmp_camptype_id', 'select', $this -> getCampaignType() ); ?></td>
								</tr>
								
								<tr style="display:none">
									<td class="text_header" style="display:none;"> Built Type</td>
									<td style="display:none"><?php $this -> JP_Plugin -> jpCombo('cmp_upload_builtype', 'select', $this -> getBuiltType() ); ?></td>
								</tr>
								
								<tr style="display:none;">
									<td class="text_header" style="display:none;">* Re-Upload Campaign</td>
									<td style="display:none";><?php $this -> getReupload(); ?></td>
								</tr>
								<tr>
									<td class="text_header">* Campaign Name</td>
									<td><input type="text" name="upload_cmp_name" id="upload_cmp_name" class="input_text"></td>
								</tr>
								<tr>
									<td class="text_header">* Product</td>
									<td > <?php $this -> JP_Plugin -> jpMultiple('cmp_upload_product', 'select_multiple',$this -> getProduct()); ?></td>
								</tr>
								<tr>
									<td class="text_header">* Date Expired</td>
									<td><input type="text" name="expired_date" id="expired_date" class="input_text" value="<?php echo $db->Date->date_time_indonesia($this->next30date(date('Y-m-d')));?>"></td>
								</tr>
								<tr style="display:none;">
									<td class="text_header" >* Date Extends</td>
									<td><input type="text" name="extends_date" id="extends_date" class="input_text"></td>
								</tr>
							</table>
						</td>
					<!-- stop : left layout -->
					
					
					<!-- start : right layout -->
						<td valign="top">
							<table cellpadding="6px">
								<tr>
									<td class="text_header">* Category</td>
									<td><?php $this -> getCategory();?></td>
								</tr>
								
								<tr>
									<td class="text_header">
										<input type="button" value=">>" Onclick="cbMoveOn();" ><br>
										<input type="button" value="<<" Onclick="cbRemoveOn();">
									</td><td><?php $this->getMoveProduct(); ?></td>
								</tr>
								<tr>
									<td class="text_header">* status Active</td>
									<td><?php $this -> JP_Plugin ->jpCombo('cmp_upload_status','select',$this-> getStatusActive())?></td>
								</tr>
								<tr style="display:none;">
									<td class="text_header">* CampType</td>
									<td><?php $this -> JP_Plugin ->jpCombo('cmp_upload_camptype','select',$this-> getCampType());?></td>
								</tr>
								
								<tr style="display:none;">
									<td class="text_header" >* System</td>
									<td><?php $this-> getSystem(); ?></td>
								</tr>
								
								<tr style="display:none;">
									<td class="text_header">* Re-Upload Reason</td>
									<td><?php $this-> getReuploadReason(); ?></td>
								</tr>
								
							</table>
						</td>
					<!-- stop : right layout -->
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="float:right;border:0px solid #000;">
						<a href="javascript:void(0);" class="sbutton" onclick="saveCmpUpload();"><span>&nbsp;Save</span></a>
					</td>
				</tr>
				
			</table>
			
			</div>
		
		
		<?php }
	}
	
	$CampaignUpload= new CampaignUpload();
	$CampaignUpload -> initClass();
?>
