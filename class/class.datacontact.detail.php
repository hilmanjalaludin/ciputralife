

<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	/**
		class for detail QC approval data 
		
	**/
	
	class ContactDetail extends mysql
	{
		var $Action; 
		var $CustomerId; 
		var $ServerIP;
		
		function ContactDetail()
		{
			parent::__construct();
			
			$this -> Action		= $this -> escPost('action');	
			$this -> ServerIP 	= 'http://'.$this -> getPBX().'/recording/';
			$this -> CustomerId	= $this -> escPost('customerid');
			$this -> Masking	= new application(); 
				
		}
	
	/** *************************/
	/** *************************/	
	
		function index()
		{	
			if( $this->havepost('action') )
			{
				switch($this -> Action )
				{
					case 'history_contact'   : $this -> HistoryContactTpl(); 	break;
					case 'get_recording'	 : $this -> getRecording(); 		break;	
					case 'play_recording'    : $this -> playRecording(); 		break;
					case 'get_information'	 : $this -> getInformasi(); 		break;
				}
			}
		}
		
	/* getInformasi **/

		function getInformasi()
		{
			$sql = " SELECT a.*, b.CampaignName, 
							DATE_FORMAT(b.CampaignEndDate,'%d/%m/%Y') as CampaignEndDate, 
							DATE_FORMAT(b.CampaignStartDate,'%d/%m/%Y') as CampaignStartDate, 
							d.ProductName 
						FROM t_gn_customer a 
						LEFT JOIN t_gn_campaign b on a.CampaignId=b.CampaignId 
						LEFT JOIN t_gn_campaignproduct c on b.CampaignId=c.CampaignId
						LEFT JOIN t_gn_product d on c.ProductId=d.ProductId
						WHERE a.CustomerId='".$this->CustomerId."'";
				
			$qry = $this -> query($sql);
			if( !$qry ->EOF() )
			{
				$customer_dob = str_replace('-','/',$this -> formatDateId($qry->result_get_value('CustomerDOB')));
				
				echo "<table border=0 width=\"99%\" align=\"center\" cellpadding=\"4px\" cellspacing=\"4px\">
						<tr>
							<td class=\"txt_header\">Fine Code</td>
							<td class=\"txt_input\"><b style=\"color:#233BBB;\">{$qry->result_get_value('CustomerNumber')}</b></td>
							<td class=\"txt_header\">Customer Name</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerFirstName')}</td>
							<td class=\"txt_header\">Product Name</td>
							<td class=\"txt_input\">{$qry->result_get_value('ProductName')}</td>
						</tr>
						<tr>
							<td class=\"txt_header\">Home Address</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerAddressLine1')}</td>
							<td class=\"txt_header\">Customer DOB</td>
							<td class=\"txt_input\">{$customer_dob}</td>
							<td class=\"txt_header\">Campaign Name</td>
							<td class=\"txt_input\">{$qry->result_get_value('CampaignName')}</td>
						</tr>
						<tr>
							<td class=\"txt_header\">&nbsp;</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerAddressLine2')}</td>
							<td class=\"txt_header\">Home Phone</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerHomePhoneNum')}</td>
							<td class=\"txt_header\">Campaign Start Date</td>
							<td class=\"txt_input\">{$qry->result_get_value('CampaignStartDate')}</td>
						</tr>
						<tr>
							<td class=\"txt_header\">&nbsp;</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerAddressLine3')}</td>
							<td class=\"txt_header\">Mobile Phone</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerMobilePhoneNum')}</td>
							<td class=\"txt_header\">Campaign Expired</td>
							<td class=\"txt_input\">{$qry->result_get_value('CampaignEndDate')}</td>
						</tr>
						<tr>
							<td class=\"txt_header\">City</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerCity')}</td>
							<td class=\"txt_header\">Office Phone</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerWorkPhoneNum')}</td>
							<td class=\"txt_header\">&nbsp;</td>
							<td class=\"txt_header\">&nbsp;</td>
						</tr>
						<tr>
							<td class=\"txt_header\">Zip Code</td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerZipCode')}</td>
							<td class=\"txt_header\">Card Type </td>
							<td class=\"txt_input\">{$qry->result_get_value('CustomerCardType')}</td>
							<td class=\"txt_header\">&nbsp;</td>
							<td class=\"txt_header\">&nbsp;</td>
						</tr>
					 </table>";
			
			}
			// foreach($qry ->result_assoc() as $rows )
			// {
				// echo "<pre>";
				// print_r($rows);
				// echo "</pre>";
			// }
		
		}
		
	/** *************************/
	/** *************************/	
	
		private function getInfoRec()
		{
			$sql ="select * from cc_recording where id='".$_REQUEST['rec_id']."'";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$row = $this -> fetchrow($qry);
			if( $row ) : 
				return $row;
			endif;
		}
		
		
	/*** class ****/
	
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

		
		
	/** *************************/
	/** *************************/	
	
		private function fileVoc($str)
		{
			if($str!=''):
				$file_voc_loc = explode("/", $str);
				return $file_voc_loc[5].'/'.$file_voc_loc[6].'/'.$file_voc_loc[7].'/';				
			endif;
		}
	
	/** *************************/
	/** *************************/	
	
		private function getQueryRec()
		{
			$sql = "select a.*, date_format(a.start_time,'%H:%i:%s %d/%m/%Y') as start_date   from cc_recording a  left join t_gn_customer b on a.assignment_data=b.CustomerId
					where a.assignment_data='".$this -> CustomerId."' order by a.id DESC";
							
			return $sql;
		}
		
	/** *************************/
	/** *************************/	
	
		function playRecording(){
			$rec = $this -> getInfoRec();
			$RecFileVoice = trim($this -> ServerIP.$this -> fileVoc($rec->file_voc_loc).$rec->file_voc_name);
		?>
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
		<?php		
		}
		
		
	/** //////////////////////////////////////////// */
	
		function getRecording()
		{ ?>
			<ul class="ul-recording">
					<?php
						$qry = $this -> query($this->getQueryRec());
						if( $qry -> EOF() ) echo "<span style='color:red;'>No Recording File..!</span>";
						else
						{
							foreach( $qry -> result_assoc() as $rows )
							{
								$exp = explode("_",$rows['file_voc_name']);
								$new = $exp[1]."_".$exp[2]."_".$exp[3];
								echo "<li class=\"ul-recording\"><a class=\"a-recording\" href='javascript:void(0);' onclick=\"playRecording('".$rows['id']."');\">".$new." - ".toDuration($rows['duration']) ."  - ".$rows['start_date']."</a></li>";
							}
						}
					?>
				</ul>	
			<?php
		}
		
	/** get hilstroy calll **********************/
	
		function HistoryContactTpl(){
		?>
			<fieldset style="border:1px solid #dddddd;">
				<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;"> Contact History </legend>
					
					<div style="height:220px;overflow:auto;border:1px solid #dddddd;">
						<table border=0 align="left" cellspacing=0 style="width:99%;" cellspacing="1px">
							<tr>
								<th style="border:1px solid #dddddd;height:35px;background-color:#878382;color:#FFF;" WIDTH="15%">Last Call Date</th>
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
									<td style="border-left:1px solid #dddddd;border-top:1px solid #dddddd;height:24px;padding-left:4px;font-size:12px;color:green;"><?php echo $row->Calldate; ?></td>
									<td style="border-left:1px solid #dddddd;border-top:1px solid #dddddd;height:24px;padding:4px;font-size:12px;color:green;" nowrap><?php echo $row -> full_name; ?></td>
									<td style="border-left:1px solid #dddddd;border-top:1px solid #dddddd;height:24px;padding-left:4px;">
										<span style="line-height:20px;font-size:12px;font-family:Arial;color:green;">
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
	
	$ContactDetail = new ContactDetail(true);
	$ContactDetail -> index();
	
?>