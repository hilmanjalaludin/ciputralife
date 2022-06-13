<?php

	require_once("../sisipan/sessions.php");
	require_once("../fungsi/global.php");
	require_once("../class/MYSQLConnect.php");
	require_once("../class/lib.form.php");
	

	/*
	 *	class untuk action call result
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	
	
	class CallResult extends mysql{
		var $action;
		var $ServerIP;
		var $RecFileVoice;
		
		function __construct()
		{
			parent::__construct();
			$this->action = $this->escPost('action');
			$this->setCss();
			$this -> ServerIP = 'http://'.$this -> getPbxHost().'/recording/';
		}
		
		
		function getPbxHost()
		{
			$sql = " SELECT a.set_value FROM cc_pbx_settings a WHERE a.set_name='host'
					 AND a.pbx=1 ";
					 
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				return $qry -> result_get_value('set_value');
			}	
		}
		
	/* function set style css **/
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
					.input_text {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-color:#fffccc;}
					.text_header { text-align:right;color:#746b6a;font-size:12px;}
					.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
				</style>
				
			<!-- stop: css -->
		<?php }
		
		function initClass(){
			if( $this->havepost('action')):
				switch( $this->action){
					case 'tpl_onready' 	: $this->tplOnReady();    break;
					case 'tpl_delete'  	: $this->tplResultRemove(); break;
					case 'quick_time' 	: $this -> QuickTime(); break;
					case 'download_rec'	: $this -> QuickDownload(); break;
					case 'list_record'	: $this -> list_recording(); break;
					
					
					//http://192.168.5.11/MIS/class/class.mon.recording.php?action=download_rec&rec_id=22
				}
			endif;
		}
		
		function getInfoRec(){
			$sql ="select * from cc_recording where id='".$_REQUEST['rec_id']."'";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$row = $this -> fetchrow($qry);
			if( $row ) : 
				return $row;
			endif;
		}
		
		function getUserList($cond=''){
			if( $this->getSession('handling_type')==1 ){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 ";
			}
			else if( $this->getSession('handling_type')==2){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 
						and a.handling_type in('3','4') 
						and a.mgr_id='".$this->getSession('UserId')."'";
			}
			else if( $this->getSession('handling_type')==3){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 
						and a.spv_id='".$this->getSession('UserId')."' 
						and a.handling_type='4'";
			}
			else if( $this->getSession('handling_type')==4){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 
						and a.userid ='".$this->getSession('UserId')."' 
						and a.handling_type='4'";
			}
			else if( $this->getSession('handling_type')==5){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1
				and a.handling_type='4'";
			}
			else if( $this->getSession('handling_type')==9){
				$sql = "Select a.* from tms_agent a inner join cc_agent b on a.id=b.userid  where a.user_state =1 ";
			}
			
			if( $sql ){
				$query = $this ->execute($sql,__FILE__,__LINE__);
				while( $row = $this->fetchrow($query))
				{
					if( ($cond==$row->UserId) ):
						echo "<option value=\"{$row->UserId}\" selected>{$row->id} - {$row->full_name}</option>";
					else :
						echo "<option value=\"{$row->UserId}\">{$row->id} - {$row->full_name}</option>";
					endif;	
				}
			
			}
		}
				
		private function getCampaignAssigment($cond=''){
			$sql = "select a.CampaignId, a.CampaignNumber, a.CampaignName
						from t_gn_campaign a
					order by a.CampaignId DESC";
			$qry = $this ->execute($sql,__FILE__LINE__);
			while( $row = $this ->fetchrow($qry)){
				if( $row->CampaignId == $cond){
					echo "<option value=\"{$row->CampaignId}\" selected>{$row->CampaignNumber} - {$row->CampaignName} </option>";
				}
				else
					echo "<option value=\"{$row->CampaignId}\">{$row->CampaignNumber} - {$row->CampaignName} </option>";
			}	
		}
		
		private  function getResultStatus($cond=''){
			$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a 
					where a.CallReasonStatusFlag=1
					order by a.CallReasonId asc";
					
			$qry = $this->execute($sql,__file__,__line__);
			
			
			while( $res = $this->fetchrow($qry) )
			{
				if( $res -> CallReasonId ==$cond ):
					echo "<option value=\"{$res -> CallReasonId}\" selected>{$res -> CallReasonDesc}</option>"; // $datas[$res -> CallReasonId] = $res -> CallReasonDesc; 
				else:
					echo "<option value=\"{$res -> CallReasonId}\">{$res -> CallReasonDesc}</option>"; // $datas[$res -> CallReasonId] = $res -> CallReasonDesc;
				endif;
			}
		}
		
		function list_recording(){
			$qty = "<table width=\"100%\" class=\"custom-grid\" cellspacing=\"0\">
						<thead>
							<tr height=\"20\"> 
								<th nowrap class=\"custom-grid th-first\">&nbsp;#</th>	
								<th nowrap class=\"custom-grid th-middle\">&nbsp;No</th>			
								<th nowrap class=\"custom-grid th-middle\">&nbsp;Campaign</th>
								<th nowrap class=\"custom-grid th-middle\">&nbsp;Cust Number</th>
								<th nowrap class=\"custom-grid th-middle\">&nbsp;Cust Name</th>    
								<th nowrap class=\"custom-grid th-middle\">&nbsp;User ID</th>		
								<th nowrap class=\"custom-grid th-middle\">&nbsp;Call Result</th>	
								<th nowrap class=\"custom-grid th-middle\">&nbsp;File Name</th>
								<th nowrap class=\"custom-grid th-middle\">&nbsp;File Size</th>
								<th nowrap class=\"custom-grid th-middle\">&nbsp;Date</th>
								<th nowrap class=\"custom-grid th-lasted\">&nbsp;Duration</th>
							</tr>
						</thead>";
						
			$sql = "select a.*, d.id as UserId, d.full_name, b.CustomerNumber, b.CustomerFirstName,
					a.start_time,
					cmp.CampaignName as cmpnum, rs.CallReasonDesc
					from cc_recording a
						left join t_gn_customer b  on a.assignment_data =b.CustomerId
						left join cc_agent c on a.agent_id=c.id
						inner join tms_agent d on c.userid=d.id
						left join t_gn_campaign cmp on cmp.campaignid = b.campaignid
						left join  t_lk_callreason rs on b.CallReasonId=rs.CallReasonId";
			
			if( $this ->havepost('cust_number')): $sql .= " and b.CustomerNumber LIKE '%".$this ->escPost('cust_number')."%'"; endif;
			if( $this ->havepost('cust_name')) 	: $sql .= " and b.CustomerFirstName LIKE '%".$this ->escPost('cust_name')."%'"; endif;
			if( $this ->havepost('campaign_id')): $sql .= " and b.CampaignId LIKE '%".$this ->escPost('campaign_id')."%'"; endif;
			if( $this ->havepost('call_result')): $sql .= " and b.CallReasonId ='".$this ->escPost('call_result')."'"; endif;
			if( $this ->havepost('user_id')) 	: $sql .= " and b.SellerId ='".$this ->escPost('user_id')."'"; endif;
			if( $this ->havepost('destination')): $sql .= " and a.anumber ='".$this ->escPost('destination')."'"; endif;
			if( $this ->havepost('start_date')) : $sql .= " and date(a.start_time)>='".$this->formatDateEng($this ->escPost('start_date'))."'"; endif;
			if( $this ->havepost('end_date')) 	: $sql .= " and date(a.start_time)<='".$this->formatDateEng($this ->escPost('end_date'))."'"; endif;
			
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			
			$dataSize  =0;
			$no = 1;
			while( $rows = $this ->fetchassoc($qry)){
				
				$qty.= '<tr class="onselect">'.
						   '<td nowrap class=\"custom-grid td-first\"><input type="checkbox"  name="chk_cust_call" name="chk_cust_call"  value="'.$row['id'].'"></td>	
							<td nowrap class=\"custom-grid td-middle\">&nbsp;No</td>			
							<td nowrap class=\"custom-grid td-middle\">&nbsp;Campaign</td>
							<td nowrap class=\"custom-grid td-middle\">&nbsp;Cust Number</td>
							<td nowrap class=\"custom-grid td-middle\">&nbsp;Cust Name</td>    
							<td nowrap class=\"custom-grid td-middle\">&nbsp;User ID</td>		
							<td nowrap class=\"custom-grid td-middle\">&nbsp;Call Result</td>	
							<td nowrap class=\"custom-grid td-middle\">&nbsp;File Name</td>
							<td nowrap class=\"custom-grid td-middle\">&nbsp;File Size</td>
							<td nowrap class=\"custom-grid td-middle\">&nbsp;Date</td>
							<td nowrap class=\"custom-grid td-lasted\">&nbsp;Duration</td>'.
							/*'<td class="content-first"><input type="checkbox" value="'.$rows['CustomerId'].'" name="chk_cust_dist" name="chk_cust_dist" onclick="RandomClick();"></td>'.
							'<td class="content-middle">'.$no.'</td>'.
							'<td class="content-middle">'.$rows['CustomerNumber'].'</td>'.
							'<td class="content-middle">'.$rows['CustomerFirstName'].'</td>'.
							'<td class="content-middle"><b style="color:green;">'.$rows['CampaignName'].'<b></td>'.
							'<td class="content-middle"><b style="color:green;">'.$rows['full_name'].'<b></td>'.
							'<td class="content-lasted">'.$rows['AssignDate'].'</td>'.*/
						'</tr>';
				$dataSize+=1; $no++;		
						
			}
			
			$qty.='</table>';
		}
		
		function tplOnReady(){ ?>
			<!-- start : jQuqery -->
			
			<script>
				$(document).ready(function(){
					alert("hello");
				});
				
			</script>
			
			<!-- stop : jQuqery -->
			
			
			<div id="result_content_add" class="box-shadow" style="padding-bottom:4px;margin-top:2px;margin-bottom:8px;">
				<table cellpadding="3px;"  width="70%" border=0>
					<tr>
						<td class="text_header"> Customer ID</td>
						<td>
							<input type="text" name="cust_number" id="cust_number" 
								   value="<?php echo ($this->havepost('cust_number')?$this->escPost('cust_number'):'');?>" 
								   class="input_text" style="width:180px;height:18px;margin-right:2px;">
						</td>
						
						
						<td class="text_header"> Campaign</td>
						<td>
							<select name="campaign_id" id="campaign_id" class="select">
								<option value=""> -- Choose --</option>
								<?php $this -> getCampaignAssigment( $this->escPost('campaign_id') ); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="text_header"> Customer Name</td>
						<td>
							<input type="text" name="cust_name" id="cust_name" 
								   value="<?php echo ($this->havepost('cust_name')?$this->escPost('cust_name'):'');?>" 
								   class="input_text" style="width:180px;height:18px;margin-right:2px;">
						</td>
						<td class="text_header"> Call Result </td>
						<td>
							<select name="call_result" id="call_result" class="select">
								<option value=""> -- Choose --</option>
								<?php $this->getResultStatus( $this->escPost('call_result') ); ?>
							</select>
						</td>
					</tr>
					
					<tr>
						<td class="text_header"> Destination </td>
						<td>
							<input type="text" name="destination" id="destination" 
								   value="<?php echo ($this->havepost('destination')?$this->escPost('destination'):'');?>"
								   class="input_text" style="width:180px;height:18px;">
						</td>
						
						<td class="text_header"> User ID </td>
						<td>
							<select name="user_id" id="user_id" class="select">
								<option value=""> -- Choose --</option>
								<?php $this->getUserList($this->escPost('user_id')); ?>
							</select>
						</td>
					</tr>
					
					<tr>
						<td class="text_header"> Interval </td>
						<td>
							<input type="text" name="start_date" id="start_date" 
								   value="<?php echo ($this->havepost('start_date')?$this->escPost('start_date'):'blank');?>" class="input_text" style="width:70px;height:18px;">
							&nbsp; -&nbsp;	   
							<input type="text" name="end_date" id="end_date" 
								   value="<?php echo ($this->havepost('end_date')?$this->escPost('end_date'):'blank');?>" class="input_text" style="width:70px;height:18px;">	   
						</td>
					</tr>
					<tr>
						<td style="color:green" colspan="3">Click "Search" to retrieve data</td>
					</tr>
					<tr>
						<td style="color:red" colspan="3">* The Interval date of recordings can't blank</td>
					</tr>
				</table>
			</div>
		<?php
		}
		
		function tplResultRemove(){ ?>
			<div id="result_content_delete" class="box-shadow" style="margin-top:10px;">
				
			</div>
		<?php
		}
				
		function fileVoc($str){
			if($str!=''):
				$file_voc_loc = explode("/", $str);
				return $file_voc_loc[5].'/'.$file_voc_loc[6].'/'.$file_voc_loc[7].'/';				
			endif;
		}
				
		function QuickDownload(){
			$rec = $this -> getInfoRec();
			if( $this ->havepost('rec_id')){
				// $this -> RecFileVoice = trim($this -> ServerIP.$this -> fileVoc($rec->file_voc_loc).$rec->file_voc_name);
				// header("location:".$this -> RecFileVoice);
				header("location:recording_getfile.php?rec_id=".$_REQUEST['rec_id']);
			
			}
		}
		
		function QuickTime(){
			
			$rec = $this -> getInfoRec();
			$this -> RecFileVoice = trim($this -> ServerIP.$this -> fileVoc($rec->file_voc_loc).$rec->file_voc_name);
			if( $_REQUEST['mode']=='play')
			{ ?>
				<!-- <div class="box-shadow" style="margin-top:10px;margin-bottom:10px;padding:15px;">
					<div class="box-shadow" style="border:0px solid #000;height:20px;padding:10px;width:500px;"> 
						<object id="obj" width="100" height="20" attr1="attrValue1" attr2="attrValue2"  classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"> 
							<param name="src" value="<?php //echo $this -> RecFileVoice; ?>"> 
							<param name="autoplay" value="true"> 
							<param name="param1" value="paramValue1"> 
							<param name="param2" value="paramValue2"> 
							<embed width="99%" height="20" src="<?php //echo $this -> RecFileVoice; ?>" autoplay="true" attr1="attrValue1" attr2="attrValue2" param1="paramValue1" param2="paramValue2"> </embed> 
						</object> 
					</div>	
				</div> -->

				<!-- <div class="box-shadow" style="margin-top:10px;margin-bottom:10px;padding:15px;">
					<div class="box-shadow" style="border:0px solid #000;height:200px;padding:10px;width:500px;"> 
						<object id="obj" width="100" height="200" attr1="attrValue1" attr2="attrValue2"  classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"> 
							<param name="src" value="../class/recording_getfile.php?<?php echo $_REQUEST[rec_id]; ?>"> 
							<param name="autoplay" value="true"> 
							<param name="param1" value="paramValue1"> 
							<param name="param2" value="paramValue2"> 
							<embed width="99%" height="100" src="../class/recording_getfile.php?<?php echo $_REQUEST[rec_id]; ?>" autoplay="true" attr1="attrValue1" attr2="attrValue2" param1="paramValue1" param2="paramValue2"> </embed> 
						</object> 
					</div>	
				</div> -->

				<div class="box-shadow" style="margin-top:10px;margin-bottom:10px;padding:15px;">
					<OBJECT ID="MediaPlayer" WIDTH="100%" HEIGHT="69" 
							CLASSID="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95"
							STANDBY="Loading Windows Media Player components..." TYPE="application/x-oleobject">
							
							<PARAM NAME="FileName" VALUE="../class/recording_getfile.php?rec_id=<?php echo $_REQUEST[rec_id]; ?>">
							<PARAM name="autostart" VALUE="true">
							<PARAM name="ShowControls" VALUE="true">
							<param name="ShowStatusBar" value="true">
							<PARAM name="ShowDisplay" VALUE="false">
							<EMBED TYPE="application/x-mplayer2" SRC="../class/recording_getfile.php?rec_id=<?php echo $_REQUEST[rec_id]; ?>" NAME="MediaPlayer"
							WIDTH="320" HEIGHT="80" ShowControls="1" ShowStatusBar="0" ShowDisplay="1" autostart="1"> </EMBED>
					</OBJECT>
					</div>	
				</div>
			<?php  }
			
			else if($_REQUEST['mode']=='stop'){  ?>
				<div class="box-shadow" style="margin-top:10px;margin-bottom:10px;padding:15px;" >
					<div class="box-shadow" style="border:0px solid #000;height:20px;padding:10px;width:500px;"> 
						<object id="obj" width="100" height="20" attr1="attrValue1" attr2="attrValue2"  classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"> 
							<param name="src" value="<?php echo $this -> RecFileVoice; ?>"> 
							<param name="autoplay" value="true"> 
							<param name="param1" value="paramValue1"> 
							<param name="param2" value="paramValue2"> 
							<embed width="99%" height="20" src="<?php echo $this -> RecFileVoice; ?>" autoplay="false" attr1="attrValue1" attr2="attrValue2" param1="paramValue1" param2="paramValue2"> </embed> 
						</object> 
					</div>	
				</div>
			<?php
			}	
		}
	}
	
	$CallResult= new CallResult();
	$CallResult -> initClass();