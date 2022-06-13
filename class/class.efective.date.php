<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	

	/*
	 *	class untuk action efective date 
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	class EfectiveDate extends mysql{
		var $action;
		
		function __construct(){
			parent::__construct();
			$this->action = $this->escPost('action');
		
		}
		
		function index(){
			if( $this -> havepost('action')){
				switch( $this-> action){
					case 'current_efective'  : $this -> CurrentEfectiveDate(); break;
					case 'add_efective'		 : $this -> addEfectiveDate(); break;
					case 'save_cut_off_date' : $this -> saveEfectiveDate(); break; 
				}
			}
		}
		
		function saveEfectiveDate(){
			$datas = array('CutoffDate'=> $_REQUEST['cut_off_date'], 'CutOffMonth'=> date("m",strtotime($_REQUEST['cut_off_date'])) );
			$query = $this -> set_mysql_replace("t_lk_cutoffdate",$datas);
			if( $query) echo 1;
			else echo 0;
		}
		
		function getEfectiveDate($cut_off_date='',$src_off_date=''){
			if( ($cut_off_date!='') && ($src_off_date!='')) :
				$sql = "SELECT `F_getEfectiveDate`('".$cut_off_date."', '".$src_off_date."')";
				return $this -> valueSQL($sql);
			endif;
		}
		
		private function getYear(){
			$date = explode("-",$this ->escPost('date'));
			return $date[0];
		}
		
		private function getMonth(){
			$date = explode("-",$this ->escPost('date'));
			return $date[1];
		}
		
		private function currStart(){
			$date = explode("-",$this ->escPost('date'));
			$str  = $date[0].'-'.$this -> getMonth().'-01';
			return $str;	
		}
		
		private function currEnd(){
			$date = explode("-",$this ->escPost('date'));
			$max  = date('t',strtotime($this ->escPost('date')));
			$str  = $date[0].'-'.$this -> getMonth().'-'.$max;
			return $str;	
		}
		
		function addEfectiveDate(){
			?>
			<script>
				$('#cut_off_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'yy-mm-dd'});
			</script>
			<div class="box-shadow" style="margin-top:10px;padding:8px;overflow:auto;" >
				<table cellpadding="7px;">
					<tr>
						<td style="text-align:right;color:red;font-size:12px;">Cust Off Date </td>
						<td><input type="text" name="cut_off_date" Id="cut_off_date" style="height:22px;text-align:left;border:1px solid #dddddd;">
						( yyyy-mm-dd )</td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;</td>
						<td>
							<a href="javascript:void(0);" class="sbutton" onclick="saveCutOffDate();"><span>&nbsp;Save</span>
						
					</tr>
				</table>	
			</div>
			<?php	
		}
		
		function CurrentEfectiveDate(){
			$start_date = $this -> currStart();
			$end_date 	= $this -> currEnd(); 
			$cutoffdate = $this ->escPost('date');
			

			$i=0;
			
		   echo " <div class=\"box-shadow\" style=\"margin-top:10px;padding:8px;height:420px;overflow:auto;\" >".
					" <table class=\"custom-grid\" cellspacing=\"0\" width=\"40%\">".
						" <tr>".
							" <th class=\"custom-grid th-first\"><b>Production Date</b></th>".
							" <th class=\"custom-grid th-middle\" align=\"center\"><b>CutOff Date</b></th>".
							" <th class=\"custom-grid th-lasted\" align=\"center\"><b>Efective Date</b></th>".
						" </tr>"; 
			
			$i=0;	
			while(true){
				$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
				
					$start_date 	= $start_date; 
					$start_dateid   = $this -> formatDateId($start_date);
					$cut_off_date   = $this -> formatDateId($cutoffdate);
					$efective_date  = $this -> formatDateId($this->getEfectiveDate($cutoffdate,$start_date));
					$curr_row_color = ($start_dateid!=$efective_date?'style="background-color:green;color:#FFFFFF;font-weight:bold;"':'style="color:red;font-weight:bold;"');
					
					echo " <tr class=\"onselect\" bgcolor=\"{$color}\" > ".
								" <td nowrap class=\"content-first\" align=\"center\" width=\"30%\">".$start_dateid."</td>".
								" <td class=\"content-middle\" align=\"center\">".$cut_off_date."</td>".
								" <td class=\"content-lasted\" align=\"center\" ".$curr_row_color.">".$efective_date."</td>".
							" </tr>"; 
					
				if( $start_date == $end_date ) break;
				$start_date = $this -> nextDate($start_date);
				$i++;
			}
			
			echo "</table><br></div>";
		}
	}	
	
	$EfectiveDate = new EfectiveDate();
	$EfectiveDate -> index();

?>