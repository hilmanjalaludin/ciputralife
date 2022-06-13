<?php
	
	require("../fungsi/global.php");
	require("../sisipan/sessions.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.telnet.socket.php");
	require("../class/lib.form.php");
	
	
	class MISDNSytem extends mysql{
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
					case 'tpl_addISDN'		 :  $this -> tpl_addISDN(); 			break;
					case 'save_isdn'	 	 :  $this -> SaveMISDN(); 				break;
					case 'remove_isdn'	     :  $this -> RemoveMISDN(); 			break;
 				}
			}
		
		}
		
		
		function RemoveMISDN(){
			if( $this ->havepost('ISDN_ID') ):
				$sql = " DELETE FROM tms_misdn_type WHERE MISDNId IN(".$_REQUEST['ISDN_ID'].")";
				
				$res = $this -> execute($sql,__FILE__,__LINE__);
					if( $res ) 
						echo 1;
					else 
						echo 0;
			else:
				echo 0;
			endif;	
		}	
		
		
		function SaveMISDN(){
				
			$sql = array(
				'MISDNProvider'=> $_REQUEST['provider_type'],
				'MISDNPrefix'=> $_REQUEST['isdn_prefix'], 
				'MISDNName'=> $_REQUEST['isdn_name'],
				'MISDNNumber'=> $_REQUEST['isdn_number'],
				'MISDNStatus'=> 1
			);
			
			$rs = $this ->set_mysql_insert('tms_misdn_type',$sql);
		//	echo $this -> sqlText;
			if( $rs ) echo 1;
			else echo 0;
		}
		
		function tpl_addISDN(){
			$this->setCss();
		?>
			
			<fieldset style="border:1px solid #dddddd;margin-top:10px;margin-left:10px;">
				<legend > <b>Add MISDN </b></legend>
					<div class="box-shadow">
						<form action="javascript:void(0);"  id="uploadform" method="POST" >
							<table cellpadding="12px;">
							<tr>
								<td class="text_header"> Provider   </td>
								<td style="color:#bbb000;"> 
									<?php $this -> Forms->jpCombo('provider_type', 'select', $this->getCallProvider(),null); ?>
								</td>
							</tr>
							
							<tr>
								<td class="text_header"> MISDN Name  </td>
								<td style="color:#bbb000;"> 
									
									<?php $this -> Forms->jpInput('isdn_name', 'input_text');?>
								</td>
							</tr>	
							
							<tr>
								<td class="text_header"> MISDN Prefix  </td>
								<td style="color:#bbb000;"> 
									
									<?php $this -> Forms->jpInput('isdn_prefix', 'input_text');?>
								</td>
							</tr>

							<tr>
								<td class="text_header"> MISDN Number  </td>
								<td style="color:#bbb000;"> 
									
									<?php $this -> Forms->jpInput('isdn_number', 'input_text');?>
								</td>
							</tr>	
							
							
							
							<tr>
								<td class="text_header">&nbsp;</td>
								<td><a href="javascript:void(0);" class="sbutton" onclick="SaveISDN();"><span>&nbsp;Save MISDN</span></a></td>
							</tr>
							
							</table>
						</form>
					</div>
			</fieldset>		
		<?php
		}
		
		function getCallProvider(){
			$sql = " select a.ProviderId, a.ProviderName from tms_misdn_provider a where a.ProviderStatus=1 ";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			while( $row = $this ->fetchassoc($qry)){
				$datas[$row['ProviderId']] =  $row['ProviderName'];
			}
			return $datas;
		}
		
		
	}
	
	$MISDNSytem = new MISDNSytem();
	$MISDNSytem -> InitExtSystem();

?>