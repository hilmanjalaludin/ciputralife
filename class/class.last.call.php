<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	

	/*
	 *	class untuk action Last Call Modul
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	
	
	class LastCall extends mysql{
		var $action;
		
		function __construct(){
			parent::__construct();
			$this->action = $this->escPost('action');
		
		}
		
	/* function set style css **/
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
					.input_text {border:1px solid #dddddd;width:160px;font-size:12px;height:20px;background-color:#fffccc;}
					.text_header { text-align:right;color:red;}
					.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
					.date{ width:80px;}
					.time{ width:37px;}
				</style>
				
			<!-- stop: css -->
		<?php }
		
		
		function initClass(){
			if( $this->havepost('action')):
				switch( $this->action){
					case 'tpl_add'  : $this->tplResultAdd();    break;
					case 'tpl_delete': $this->tplResultRemove(); break;
					case 'save_last_call': $this -> saveLastCall(); break;
					case 'delete_last_call' : $this -> removeLastCall(); break;
					case 'enable_last_call' : $this -> enableLastCall(); break;
					case 'disable_last_call' : $this -> disableLastCall(); break;
					case 'tpl_edit' : $this->tplResultEdit(); break;	
					case 'update_last_call' : $this-> updateLastCall(); break;	
					case 'get_last_call' : $this-> getLastCall(); break;	
				}
			endif;
		}
		
	# cs : get Lastcall periode 

		function getLastCall(){
			$datas = '';
			
			$sql = " SELECT
						(TIME(NOW())>=TIME(a.LastCallStartTime)) as start_last_time,
						(TIME(NOW())<=TIME(a.LastCallEndTime)) as end_last_time
					FROM t_gn_lastcall a 
						WHERE a.LastCallStatus = 1
								AND date(a.LastCallEndDate)>=DATE(NOW()) ";
			$qry = $this->execute($sql,__FILE__,__LINE__);
			
			if( ($qry) && ($row = $this->fetchrow($qry)) ){
				$start_last_time = $row -> start_last_time;
				$end_last_time	 = $row -> end_last_time;
			}	
			
			if( ($start_last_time==1) && ($end_last_time==1) ) echo 1;
			else echo 0;
		}
		
	# ce : 	get Lastcall periode
	
	/* delete **/
		function removeLastCall(){
			$resultid = explode(",",$this -> escPost('resultid'));
			
			$i=0;
			foreach( $resultid as $key=> $LastCallId)
			{
				$sql = " delete from t_gn_lastcall where LastCallId ='".$LastCallId."'"; 
				$qry = $this ->execute($sql,__FILE__,__LINE__);
			
				if ( $qry ) $i++;
			}
			
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
		}
		
	/* enable **/
		function enableLastCall(){
			$resultid = explode(",",$this -> escPost('resultid'));
			
			$i=0;
			foreach( $resultid as $key=> $LastCallId)
			{
				$sql = " Update t_gn_lastcall SET LastCallStatus=1  where LastCallId ='".$LastCallId."'"; 
				$qry = $this ->execute($sql,__FILE__,__LINE__);
			
				if ( $qry )  $i++;
			}
			
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
		}
		
		
		function getEditLastCall(){
			$sql = " select * from t_gn_lastcall a where a.LastCallId =".$this -> escPost('resultid')." ";
			//echo $sql;
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$res = $this -> fetchrow($qry);
			if( $res )  return $res;
			else return null; 
		}

/* disable **/
		function disableLastCall(){
			$resultid = explode(",",$this -> escPost('resultid'));
			
			$i=0;
			foreach( $resultid as $key=> $LastCallId)
			{
				$sql = " Update t_gn_lastcall SET LastCallStatus=0  where LastCallId ='".$LastCallId."'"; 
				$qry = $this ->execute($sql,__FILE__,__LINE__);
			
				if ( $qry )  $i++;
			}
			
			if( $i >0 ) 
				echo 1; 
			else 
				echo 0;
		}		
	/** update **/
		//last_call_editid
		
		function updateLastCall(){
		$start = explode("/",$this -> escPost('last_call_start_date'));
		$startcall = $start[2].'-'.$start[1].'-'.$start[0];
		$end = explode("/",$this -> escPost('last_call_end_date'));
		$endcall = $end[2].'-'.$end[1].'-'.$end[0];
		
		$datas = array(
					'LastCallStartDate'	 => $startcall, 
					'LastCallEndDate' 	 => $endcall, 
					'LastCallStartTime'  => ($this ->escPost('last_call_hour_start_time').":".$this ->escPost('last_call_minute_start_time').":00"), 
					'LastCallEndTime' 	 => ($this ->escPost('last_call_hour_end_time').":".$this ->escPost('last_call_minute_end_time').":00"), 
					'LastCallReason' 	 => $this ->escPost('last_call_reason'), 
					'LastCallStatus' 	 => $this ->escPost('last_call_status'), 
					'LasCallCreateBy' 	 => $this -> getSession('UserId'), 
					'LastCallCreateDate' => date('Y-m-d H:i:s'));
					
					
			$where['LastCallId'] = $_REQUEST['last_call_editid'];
			$query = $this -> set_mysql_update('t_gn_lastcall',$datas,$where);
			//echo $this->sqlText;
			if( $query ) : echo 1;
			else :
				echo 0;
			endif;
				
		}
		
	/* save */
	
		function saveLastCall(){
		$datas = array(
					'LastCallStartDate'	 => $this -> formatDateEng($this ->escPost('last_call_start_date')), 
					'LastCallEndDate' 	 => $this -> formatDateEng($this ->escPost('last_call_end_date')), 
					'LastCallStartTime'  => ($this ->escPost('last_call_hour_start_time').":".$this ->escPost('last_call_minute_start_time').":"), 
					'LastCallEndTime' 	 => ($this ->escPost('last_call_hour_end_time').":".$this ->escPost('last_call_minute_end_time').":"), 
					'LastCallReason' 	 => $this ->escPost('last_call_reason'), 
					'LastCallStatus' 	 => $this ->escPost('last_call_status'), 
					'LasCallCreateBy' 	 => $this -> getSession('UserId'), 
					'LastCallCreateDate' => date('Y-m-d H:i:s'));
			
			$query = $this -> set_mysql_insert('t_gn_lastcall',$datas);
			if( $query ) : echo 1;
			else :
				echo 0;
			endif;
				
		}
		
		
		function tplResultEdit(){
			global $db;
			$this->setCss();
			
			$EditLastCall = $this -> getEditLastCall();
			
		?>
			<script>
				$(function() {
					$("#last_call_start_date").datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy'});
					$("#last_call_end_date").datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy'});
				});
			</script>
			
			<div id="result_content_add" class="box-shadow" style="margin-top:10px;">
				<h3 class="box-shadow h3"> Edit Last Call </h3>
				<input type="hidden" name="edit_lastid" id="edit_lastid" value="<?php echo $EditLastCall -> LastCallId;?>"> 
				<table cellpadding="6px;">
					<tr>
						<td class="text_header">* Date Interval </td>
						<td>
							<input class="input_text date" type="text" id="last_call_start_date" name="last_call_start_date" value="<?php echo $db->Date->indonesia($EditLastCall->LastCallStartDate); ?>"> &nbsp;-&nbsp;  
							<input class="input_text date" type="text" id="last_call_end_date" name="last_call_end_date" value="<?php echo $db->Date->indonesia($EditLastCall->LastCallEndDate); ?>"> 
						</td>
					</tr>
					<tr>
						<td class="text_header" >* Time Interval </td>
						<td class="text_header" style="text-align:left;">
						<?php
							$start_time = explode(":",$EditLastCall->LastCallStartTime);
							$end_time = explode(":",$EditLastCall->LastCallEndTime);
						?>
							<input class="input_text time" type="text" id="last_call_hour_start_time" name="last_call_hour_start_time" value="<?php echo $start_time[0];?>">:  
							<input class="input_text time" type="text" id="last_call_minute_start_time" name="last_call_minute_start_time" value="<?php echo $start_time[1];?>" style="margin-right:10px;"> -  
							<input style="margin-left:9px;"  class="input_text time" type="text" id="last_call_hour_end_time" name="last_call_hour_end_time" value="<?php echo $end_time[0];?>">:
							<input class="input_text time" type="text" id="last_call_minute_end_time" name="last_call_minute_end_time" value="<?php echo $end_time[1];?>"> hh:mm 
						
						</td>
					</tr>
					<tr>
						<td class="text_header">* Reason </td>
						<td>
							<textarea id="last_call_reason" style="font-size:12px;width:200px;border:1px solid #dddddd;height:100px;font-family:Arial;color:blue;"><?php echo $EditLastCall->LastCallReason; ?></textarea>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Status</td>
						<td>
							<select class="select" name="last_call_status" id="last_call_status">
								<?php if( $EditLastCall->LastCallStatus==0 ) { ?>
									<option value=""> -- Choose -- </option>
									<option value="0" selected> Not Active</option>
									<option value="1"> Active </option>			
								<?php } else if( $EditLastCall->LastCallStatus==1){ ?>
									<option value=""> -- Choose -- </option>
									<option value="0"> Not Active</option>
									<option value="1" selected> Active </option>		
								<?php } else { ?>
									<option value="" selected> -- Choose -- </option>
									<option value="0"> Not Active</option>
									<option value="1"> Active </option>		
								<?php }?> 	
							</select>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">&nbsp;</td>
						<td><a href="javascript:void(0);" class="sbutton" onclick="UpdateLastCall();"><span>&nbsp;Update</span></a></td>
					</tr>
				</table>
			</div>
		<?php
		}
		
		function tplResultAdd(){
				global $db;
				$this->setCss();
				?>
				
			<script>
				$(function() {
					$("#last_call_start_date").datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy'});
					$("#last_call_end_date").datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd/mm/yy'});
				});
			</script>
			
			<div id="result_content_add" class="box-shadow" style="margin-top:10px;">
				<h3 class="box-shadow h3"> Add Last Call </h3>
				<table cellpadding="6px;">
					<tr>
						<td class="text_header">* Interval Date </td>
						<td>
							<input class="input_text date" type="text" id="last_call_start_date" name="last_call_start_date"> &nbsp;-&nbsp;  
							<input class="input_text date" type="text" id="last_call_end_date" name="last_call_end_date"> 
						</td>
					</tr>
					<tr>
						<td class="text_header" >* Interval Time </td>
						<td class="text_header" style="text-align:left;">
							<input  class="input_text time" type="text" id="last_call_hour_start_time" name="last_call_hour_start_time">:<input class="input_text time" type="text" id="last_call_minute_start_time" name="last_call_minute_start_time" style="margin-right:18px;" >&nbsp;-&nbsp; 
							<input style="margin-left:2px;" class="input_text time" type="text" id="last_call_hour_end_time" name="last_call_hour_end_time">:<input class="input_text time" type="text" id="last_call_minute_end_time" name="last_call_minute_end_time"> ( hh:mm ) 
							
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Reason </td>
						<td>
							<textarea id="last_call_reason" style="font-size:12px;width:200px;border:1px solid #dddddd;height:100px;font-family:Arial;color:blue;"></textarea>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Status</td>
						<td>
							<select class="select" name="last_call_status" id="last_call_status">
								<option value=""> -- Choose -- </option>
								<option value="0"> Not Active</option>
								<option value="1"> Active </option>								
							</select>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">&nbsp;</td>
						<td><a href="javascript:void(0);" class="sbutton" onclick="saveResult();"><span>&nbsp;Save</span></a></td>
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
	}
	
	$LastCall= new LastCall();
	$LastCall -> initClass();