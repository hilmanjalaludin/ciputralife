<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	
	class RefDetail extends mysql{
		var $Action; 
		var $CampaignId;
		var $CustomerId; 
		var $ServerIP;
		
	
	/** *************************/
	/** *************************/	
	
		function __construct(){
			parent::__construct();
				
				$this -> Action		= $this -> escPost('action');	
				$this -> ServerIP 	= 'http://'.$this -> getPBX().'/recording/';
				$this -> CustomerId	= $this -> escPost('customerid');
				$this -> CampaignId = $this -> escPost('campaignid');
				$this -> Masking	= new application(); 
				$this -> LockStatus = array(16,17);
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
					case 'save_qc'			 : $this -> saveVerified(); 		break;
					case 'save_qc_cancel'	 : $this -> saveVerifiedCancel(); 	break;
					case 'reset_prosess'	 : $this -> resetProses(); 			break;
				}
			}
		}
		
	/** stop prosess on QC **/
	
		function resetProses(){
			$sql = " Update t_gn_customer a SET a.CustomerReferalProsess=0 WHERE a.CustomerId='".$this->escPost('customerid')."'";
			if( $this -> execute($sql,__FILE__,__LINE__) ) echo 1;
			else echo 0;
		}
	
	/** *************************/
	/** *************************/	
	
		private function getInfoRec(){
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

		
		
	/** ************************************************
	 ** @get Collum status Result on table Call Result
	 ** 
	 ** ***********************************************/	
	
		private function setCols($code='',$callresult=''){
			$array_verified['401']= array(1=>'37',2=>'39',3=>'41');
			$array_verified['402']= array(1=>'38',2=>'40',3=>'42');
			if( ( $callresult!='') && ($code!='')) :
				return $array_verified[$code][$callresult];
			else:
				return false;	
			endif;
		}
	
	/** save remider if pending **/
		private function SaveReminderVerifed()
		{	
			if(in_array($_REQUEST['status'], array_keys($this->Entity->SuspendSelling()))) return true;
			else
			{
				if(in_array($_REQUEST['status'], array_keys($this ->Entity ->getEskalasiStatus(USER_QUALITY,USER_TELESALES))))
				{
					$SQL_insert['CustomerId'] = $_REQUEST['customerid']; 
					$SQL_insert['VerifiedStatus'] = $_REQUEST['status']; 
					$SQL_insert['UserLevelId'] = $_SESSION['handling_type'];
					$SQL_insert['VerfiedCreatedTs'] = date('Y-m-d H:i:s');
					if( $this -> set_mysql_insert('t_gn_verified_remider',$SQL_insert)) 
						return true;
					else
						return false;
				}		
			}	
		}
	
/** get save activity quality ***/
	
	private function QualitySaveScore(){
		
		$SQL_Insert['CustomerId']		= $this -> escPost('customerid'); 
		$SQL_Insert['CollmonUser']		= $this -> getSession('UserId'); 
		$SQL_Insert['CollmonResultId']	= $this -> escPost('status'); 
		$SQL_Insert['CollmonPoint']		= $this -> escPost('qulity_score'); 
		$SQL_Insert['CollmonNotes']		= $this -> escPost('notes'); 
		$SQL_Insert['CollmonCreateTs']	= date('Y-m-d H:i:s'); 
		
		if( $this -> set_mysql_insert('coll_report_collmon',$SQL_Insert,$SQL_Insert) )
			return true;
		else
			return false;
	} 
		
/** ************************************************
 ** @Save Verified by QC
 ** ************************************************/	
	
		function saveVerified()
		{
			$result		= array('result'=>0);
			$status 	= $this -> escPost('status');
			$codec  	= $this -> escPost('codec');
			$notes 	 	= $this -> escPost('notes');
			$customerid = $this -> escPost('customerid');
			$userid		= $this -> getSession('UserId');
			
			$sql = " UPDATE t_gn_customer 
						SET 
							CallReasonQue='$status', 
							QueueId='$userid', 
							CustomerRejectedDate=NOW()
					 WHERE CustomerId ='$customerid'";
			
			if( $this -> execute($sql,__FILE__,__LINE__))
			{
				$this -> SaveReminderVerifed();
				$this -> QualitySaveScore();
				
				if( $this -> HistoryCall()) 
					$result = array('result'=>1);	
				else
					$result = array('result'=>0);
			}
			else
				$result = array('result'=>0);
				
				
			echo json_encode($result);	
		}
		
		function saveVerifiedCancel()
		{
			$result		= array('result'=>0);
			$status 	= $this -> escPost('status');
			$codec  	= $this -> escPost('codec');
			$notes 	 	= $this -> escPost('notes');
			$customerid = $this -> escPost('customerid');
			$userid		= $this -> getSession('UserId');
			
			$sql = " UPDATE t_gn_customer 
						SET 
							CallReasonQue='$status', 
							QueueId='$userid', 
							CustomerRejectedDate=NOW()
					 WHERE CustomerId ='$customerid'";
			
			if( $this -> execute($sql,__FILE__,__LINE__))
			{
				$this -> SaveReminderVerifed();
				$this -> QualitySaveScore();
				
				if( $this -> HistoryCall()) 
					$result = array('result'=>1);	
				else
					$result = array('result'=>0);
			}
			else
				$result = array('result'=>0);
				
				
			echo json_encode($result);	
		}
	/** function result qa ***/

		function ReasonOnQA()
		{
			$sql ="select a.AproveName from t_lk_aprove_status a where a.ApproveId='".$_REQUEST['status']."'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				return $qry -> result_get_value('AproveName');
			}
		}
		
	/** ************************************************
	 ** Save Call Remark to call history table 
	 ** ************************************************/
	
		private function HistoryCall()
		{
			$status = $this -> escPost('status');
			$codec  = $this -> escPost('codec');
			$notes = $this -> escPost('notes');
			$notes.= " ( {$this -> ReasonOnQA()} ) ";
			$CallHistory = array(
					'CustomerId'=> $this -> escPost('customerid'), 
					'CallReasonId'=> $this -> getCallHistoryReason(),
					'CreatedById'=> $this -> getSession('UserId'), 
					'UpdatedById'=> $this -> getSession('UserId'), 
					'CallHistoryNotes'=> $notes, 
					'CallHistoryCreatedTs'=> date('Y/m/d H:i:s'), 
					'CallHistoryUpdatedTs'=> date('Y/m/d H:i:s') 
			);
			$query = $this ->set_mysql_insert('t_gn_callhistory',$CallHistory); 
			if( $query ) : return true;
			else : return false;
			endif;
	
		} 
		
	/** function notes dan reason ***/

		function getCallHistoryReason()
		{
			$sql = " SELECT a.CallReasonId FROM t_gn_callhistory a 
					 WHERE a.CustomerId='".$this -> escPost('customerid')."' 
					 ORDER by a.CallHistoryId DESC limit 1";
					 
			$qry = $this -> query($sql);
			if(!$qry -> EOF() )
			{
				return $qry -> result_get_value('CallReasonId');
			}
		}
		
	/** *************************/
	/** *************************/	
	
		private function fileVoc($str){
			if($str!=''):
				$file_voc_loc = explode("/", $str);
				return $file_voc_loc[5].'/'.$file_voc_loc[6].'/'.$file_voc_loc[7].'/';				
			endif;
		}
	
	/** *************************/
	/** *************************/	
	
		private function getQueryRec(){
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
		
		function getRecording(){
			
			?>
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
					
			<?php
			
		}
		
		
		function HistoryContactTpl(){
		global $db;
		?>
			<fieldset style="border:1px solid #dddddd;">
				<legend style="background:url('../gambar/pager_bg.png');color:blue;height:16px;font-size:12px;padding:4px;border:1px solid #dddddd;cursor:pointer;width:120px;"> Contact History </legend>
					
					<div style="height:220px;overflow:auto;border:1px solid #dddddd;">
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
									<td style="border-left:1px solid #dddddd;border-top:1px solid #dddddd;height:24px;padding-left:4px;font-size:12px;color:green;"><?php echo $db -> Date->date_time_indonesia($row->Calldate); ?></td>
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
	
	$RefDetail = new RefDetail(true);
	$RefDetail -> index();
	
?>