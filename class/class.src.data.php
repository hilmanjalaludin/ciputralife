<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/lib.form.php");
	
	class CallResult extends mysql{
		var $action;
		var $ServerIP;
		var $RecFileVoice;
	
		function __construct(){
			parent::__construct();
			$this->action = $this->escPost('action');
			$this -> form  = new jpForm();
			$this -> ServerIP = 'http://192.168.1.116/recording/';
		}
	
/* function set style css **/
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
					.input_text {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-color:#fffccc;}
					.text_header { text-align:right;color:#746b6a;font-size:12px;s}
					.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
				</style>
				
			<!-- stop: css -->
		<?php }
	
	
		function initClass()
		{
			if( $this->havepost('action'))
			{
				switch( $this->action)
				{
					case 'tpl_onready' 	 : $this -> tplOnReady();      	break;
					case 'tpl_delete'  	 : $this -> tplResultRemove(); 	break;
					case 'quick_time' 	 : $this -> QuickTime(); 	 	break;
					case 'download_rec'	 : $this -> QuickDownload();	break;
					case 'get_list_user' : $this -> getAjaxData();	 	break;
				}
			}
		}
		
		/** get ajax data **/
		function getAjaxData()
		{
			$sql = " SELECT * from tms_agent a 
						where a.profile_id =4
						AND ( a.full_name LIKE '%".$_REQUEST['keyword']."%' OR id LIKE '%".$_REQUEST['keyword']."%' ) ";
			$qry = $this -> query($sql);
			
			$totals = 0;
			foreach($qry -> result_assoc() as $rows)
			{
				$datas['Username'][$totals] = $rows['full_name'];
				$datas['UserId'][$totals]   = $rows['UserId'];
				$totals++;
			}
			
			if( $totals < 1 ){
				$datas['result'] = 0;
			}
			
			echo json_encode($datas);
			
			
		}
		
		function getListCampaign()
		{
			$sql = " SELECT 
						a.CampaignId, a.CampaignNumber, 
						a.CampaignName, a.CampaignStartDate, 
						a.CampaignEndDate, a.CampaignExtendedDate
					FROM t_gn_campaign a 
					WHERE (IF(( a.CampaignExtendedDate is null OR a.CampaignExtendedDate='0000-00-00 00:00:00'), 
						   date( a.CampaignEndDate)>=date(NOW()),
						   date( a.CampaignExtendedDate) >=date(NOW())))";
			$qry = $this -> query($sql);
			foreach( $qry -> result_rows() as $rows )
			{
				$datas[$rows[0]] = $rows[1].'-'.$rows[2];	
			}	
			return $datas;
		}
		
		function getInfoRec(){
			$sql ="select * from cc_recording where id='".$_REQUEST['rec_id']."'";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$row = $this -> fetchrow($qry);
			if( $row ) : 
				return $row;
			endif;
		}
		
		function getUserId()
		{
			$datas = array();
			
			if($this->getSession('handling_type') == 9 || $this->getSession('handling_type') == 1)
			{
				$sql = "select a.UserId, a.id, a.full_name, a.init_name from tms_agent a
						where a.user_state = 1 AND a.handling_type = 2";
			}
			else if($this->getSession('handling_type') == 2)
			{
				$sql = "select a.UserId, a.id, a.full_name, a.init_name from tms_agent a
						where a.user_state = 1 AND a.handling_type = 3
						AND a.mgr_id = '".$this->getSession('UserId')."'";
			}
			else if($this->getSession('handling_type') == 3)
			{
				$sql = "select a.UserId, a.id, a.full_name, a.init_name from tms_agent a
						where a.user_state = 1 AND a.handling_type = 4
						AND a.spv_id = '".$this->getSession('UserId')."'";
			}
			
			$qry = $this->query($sql);
			
			if($qry->result_num_rows() > 0)
			{
				foreach($qry->result_assoc() as $rows)
				{
					$datas[$rows['UserId']] = $rows['id']." - ".$rows['full_name'];
				}
			}
			
			return $datas;
		}
		
		function tplOnReady(){ 
			$this->setCss();
				
		?>
			<!-- start : jQuqery -->
			
			<script>
				$(document).ready(function(){
					alert("hello");
				});
				
			</script>
			
			<!-- stop : jQuqery -->
			
			
			<div id="result_content_add" class="box-shadow" style="border-radius:5px;padding-bottom:4px;margin-top:2px;margin-bottom:8px;">
				<table cellpadding="3px;"  width="80%" >
					<tr>
						<td class="text_header"> Customer Name</td>
						<td>
							<input type="text" name="cust_name" id="cust_name" 
								   value="<?php echo ($this->havepost('cust_name')?$this->escPost('cust_name'):'');?>" 
								   class="input_text" style="width:180px;height:18px;margin-right:2px;">
						</td>
						
						<td class="text_header"> User ID</td>
						<td><?php $this->form->jpCombo('agent_tms','select',$this->getUserId(),''); ?></td>
						
						<td class="text_header"> Campaign Name</td>
						<td><?php $this->form->jpCombo('campaign','select',$this->getListCampaign(),'');?></td>
					</tr>					
				</table>
			</div>
		<?php
		}
	
		function tplResultRemove(){ 
			$this->setCss();
			?>
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
				$this -> RecFileVoice = trim($this -> ServerIP.$this -> fileVoc($rec->file_voc_loc).$rec->file_voc_name);
				header("location:".$this -> RecFileVoice);
			
			}
		}
		
		function QuickTime(){
			$this->setCss();
			$rec = $this -> getInfoRec();
			$this -> RecFileVoice = trim($this -> ServerIP.$this -> fileVoc($rec->file_voc_loc).$rec->file_voc_name);
			
			if( $_REQUEST['mode']=='play')
			{ ?>
				<div class="box-shadow" style="margin-top:10px;margin-bottom:10px;padding:15px;">
					<div class="box-shadow" style="border:0px solid #000;height:20px;padding:10px;width:500px;"> 
						<object id="obj" width="100" height="20" attr1="attrValue1" attr2="attrValue2"  classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"> 
							<param name="src" value="<?php echo $this -> RecFileVoice; ?>"> 
							<param name="autoplay" value="true"> 
							<param name="param1" value="paramValue1"> 
							<param name="param2" value="paramValue2"> 
							<embed width="99%" height="20" src="<?php echo $this -> RecFileVoice; ?>" autoplay="true" attr1="attrValue1" attr2="attrValue2" param1="paramValue1" param2="paramValue2"> </embed> 
						</object> 
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
?>