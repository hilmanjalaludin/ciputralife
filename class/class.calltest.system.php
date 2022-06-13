<?php
	
	require("../fungsi/global.php");
	require("../sisipan/sessions.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.telnet.socket.php");
	require("../class/lib.form.php");
	
	
	class TestCallSystem extends mysql{
		var $action;
		var $extensionId;
		var $extSystem;
		var $Forms;
		var $PBX;
		
		
		function __construct(){
			parent::__construct();
			if( $this->havepost('action')):
				$this -> action 		= $this->escPost('action');
				$this -> extensionId  	= $this->escPost('extension');
				$this -> Forms 			= new jpForm(true);
	
				
			endif;
		}
		
		
		/* function set style css **/
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:190px;font-size:11px;height:20px;background-color:#fffccc;}
					.input_text { border:1px solid #dddddd;width:190px;font-size:11px;height:16px;background-color:#fffccc;}
					.input_date { border:1px solid #dddddd;width:70px;font-size:11px;height:16px;background-color:#fffccc;}
					.text_header { text-align:right;color:red;font-size:12px;}
					.select_multiple { border:1px solid #dddddd;height:100px;width:180px;font-size:11px;background-color:#fffccc;}
					.textarea{border:1px solid #dddddd;width:300px;background-color:#fffccc;height:100px;}
				</style>
				
			<!-- stop: css -->
		<?php }
		
		function InitExtSystem(){
			
			if( $this->havepost('action')){
				switch($this -> action){
					case 'isdn_type_tpl' 	 :  $this -> IsdnTypeTpl(); 			break;
					case 'call_option_tpl' 	 :  $this -> UploadTplExtension();   	break;
					case 'save_activity_test':  $this -> SaveActivityCallTest(); 	break;
					case 'tpl_report'		 :  $this -> TplReport(); 				break;
					case 'export_excel_exe'	 :  $this -> ExportExcelExe(); 			break;
 				}
			}
		
		}
		
		function ExportExcelExe(){
			require("../class/class_export_excel.php");
			$excel = new excel(true);
			
			$excel -> xlsWriteHeader("test_call_report".date('Ymd'));
			$excel -> xlsWriteLabel(0, 0, "Call date ");
			$excel -> xlsWriteLabel(0, 1, "Call Number");
			$excel -> xlsWriteLabel(0, 2, "Provider Code");
			$excel -> xlsWriteLabel(0, 3, "Provider Name");
			$excel -> xlsWriteLabel(0, 4, "Test By user ");
			$excel -> xlsWriteLabel(0, 5, "Notes");
			
			$filter ='';
			
			if( $this->havepost('ProviderType') ):
				$filter .= " AND a.ProviderId IN(".$this -> escPost('ProviderType').")";
			endif;
			
			if( $this->havepost('start_date') && $this->havepost('end_date') ):
				$filter .= " AND date(a.CallDate)>='".$this->formatDateEng($_REQUEST['start_date'])."' 
							 AND date(a.CallDate)<='".$this->formatDateEng($_REQUEST['end_date'])."'";		
			endif;	
			
			$sql = " select 
					a.*, b.ProviderName, b.ProviderCode , c.full_name 
				from tms_misdn_report a 
				left join tms_misdn_provider b on a.ProviderId=b.ProviderId
				left join tms_agent c on a.CallByUser=c.UserId 
				WHERE 1=1 ";
			
			
			$sql .= $filter;
			$qry  = $this ->execute($sql,__FILE__,__LINE__);
			
			$i = 1;
			while( $row = $this ->fetchassoc($qry)){
				$excel -> xlsWriteLabel($i, 0, $row['CallDate']);
				$excel -> xlsWriteLabel($i, 1, $row['CallNumber']);
				$excel -> xlsWriteLabel($i, 2, $row['ProviderCode']);
				$excel -> xlsWriteLabel($i, 3, $row['ProviderName']);
				$excel -> xlsWriteLabel($i, 4, $row['full_name']);
				$excel -> xlsWriteLabel($i, 5, $row['CallRemarks']);
				$i++;
			}	
			
			$excel -> xlsClose();
		}
		
		function TplReport(){
			$this->setCss();
		?>
			<script>
				$(function(){
					$('#start_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
					$('#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
				});
			</script>
			<fieldset style="border:1px solid #dddddd;margin-top:10px;margin-left:10px;">
				<legend > <b>Call Report </b></legend>
					<div class="box-shadow">
						<form action="javascript:void(0);"  id="uploadform" method="POST" >
							<table cellpadding="12px;">
							<tr>
								<td class="text_header"> Interval  </td>
								<td style="color:#bbb000;"> 
									<?php $this -> Forms->jpInput('start_date', 'input_date');?> &nbsp; - &nbsp;
									<?php $this -> Forms->jpInput('end_date', 'input_date');?>
								</td>
							</tr>	
							
							<tr>
								<td class="text_header"> Provider   </td>
								<td style="color:#bbb000;"> 
									<?php $this -> Forms->jpMultiple('provider_type', 'select_multiple', $this->getCallProvider(),null); ?>
								</td>
							</tr>
							<tr>
								<td class="text_header">&nbsp;</td>
								<td><a href="javascript:void(0);" class="sbutton" onclick="ExcelReport();"><span>&nbsp;Excel</span></a></td>
							</tr>
							
							</table>
						</form>
					</div>
			</fieldset>		
		<?php
		}
		
		
		function SaveActivityCallTest(){
			$sql =array(
					'CallDate' => date('Y-m-d H:i:s'), 
					'CallNumber' => $_REQUEST['CallNumber'], 
					'CallByUser' => $this->getSession('UserId'), 
					'CallRemarks' => mysql_escape_string($_REQUEST['Notes']), 
					'CallMISDNType' => $_REQUEST['CallTestNumber'],
					'ProviderId' => $_REQUEST['ProviderType']
			);
					
			$res = $this -> set_mysql_insert('tms_misdn_report',$sql);
			//echo $this -> sqlText;
				if( $res ){
					echo 1;
				}
				else 
					echo 0;
		
		}
		
		function IsdnTypeTpl(){
			$sql = " select concat(a.MISDNPrefix,'', a.MISDNNumber) as Value,  
					 concat(a.MISDNName, ' - ', a.MISDNPrefix,'', a.MISDNNumber) as Number 
					 from tms_misdn_type a 
					 where a.MISDNProvider='".$this->escPost('provider')."' ";
			
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			while( $row = $this ->fetchassoc($qry)){
				$datas[$row['Value']] =  $row['Number'];
			}
			
			
			$this -> Forms->jpCombo('isdn_type_call', 'select',$datas,null,'onchange=SetNumberCall(this.value);'); 
			
		}
		
		
		function getCallProvider(){
			$sql = " select a.ProviderId, a.ProviderName from tms_misdn_provider a where a.ProviderStatus=1 ";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			while( $row = $this ->fetchassoc($qry)){
				$datas[$row['ProviderId']] =  $row['ProviderName'];
			}
			return $datas;
		}
		
		
		function UploadTplExtension(){
			$this->setCss();
		?>
			<fieldset style="border:1px solid #dddddd;margin-top:10px;margin-left:10px;">
				<legend > <b>Call Option </b></legend>
					<div class="box-shadow">
						<form action="javascript:void(0);"  id="uploadform" method="POST" >
							<table cellpadding="12px;" border=0>
							<tr>
								<td class="text_header"> Provider  </td>
								<td style="color:#bbb000;"> 
									<?php $this -> Forms->jpCombo('provider_type', 'select', $this->getCallProvider(),null,'onchange=getCallType(this.value);'); ?>
								</td>
								<td class="text_header"> Quality </td>
								<td style="color:#bbb000;"> 
									<?php $this -> Forms->jpCombo('call_quality', 'select',array(1=>'GOOD',2=>'MIDDLE',3=>'AVERAGE', 4=>'POOR'),null,0); ?>
								</td>
								
							</tr>
							<tr>
								<td class="text_header"> MISDN Type </td>
								<td style="color:#bbb000;"> 
									<div id="isdn_type">
										<?php $this -> Forms->jpCombo('isdn_type_call', 'select',null,'onchange=SetNumberCall(this.value);'); ?>
									</div>
									
								</td>
								<td class="text_header"> Notes  </td>
								<td style="color:#bbb000;" rowspan="2" valign="top"> 
									<?php $this -> Forms->jpTextarea('notes', 'textarea'); ?>
								</td>
								
							</tr>
							<tr>
								<td class="text_header"> Call Number </td>
								<td style="color:#bbb000;"> 
									<?php $this -> Forms->jpInput('call_to_number', 'input_text',null,'onkeyup="doJava.NumericOnly(this);"',0); ?>
								</td>
							</tr>	
							
							<tr>
								<td class="text_header">&nbsp;</td>
								<td style="color:#bbb000;"> 
									<img src="../gambar/PhoneCall.png" width="44px" height="44px" style="cursor:pointer;" title="Dial..." onclick="DialTest();"> 
									&nbsp;&nbsp;&nbsp;
									<img src="../gambar/HangUp.png" width="44px" height="44px" style="cursor:pointer;" title="Hangup..." onclick="HangupTest();">
								</td>
								<td>&nbsp;</td>
								<td><a href="javascript:void(0);" class="sbutton" onclick="SaveActivityTest();"><span>&nbsp;Save Activity</span></a></td>
							</tr>	
							
							</table>
						</form>
					</div>
			</fieldset>		
		<?php
		}
	
	}
	
	$TestCallSystem = new TestCallSystem();
	$TestCallSystem -> InitExtSystem();

?>