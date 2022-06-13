	<?php
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	class DownloadRecording extends mysql{
		var $action;
		var $server;
		
		function __construct($server=''){
			parent::__construct();
			$this -> action = $this ->escPost('action');
			
			if( $server!=''){
				$this -> server = 'http://'. $server.'/';
			}
			else{
				$this -> server = 'http://192.168.16.10/';
				}
			
		}
		
		function index(){
			if( $this -> havepost('action') )
			{
				switch($this->action)
				{
					case 'get_file_recording': $this -> getFileRecording(); break;
					case 'regenerate': $this->GenerateRecording(); break;
				}
			}
		}
		
		
		function GenerateRecording(){
			$filter_close = $_REQUEST['filter_close'];
			$url = "http://192.168.16.11/vrs/vrs.php?gen_date=".$this->formatDateEng($filter_close)."";
			$ch  = curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_HEADER,0);
			curl_exec($ch);
			curl_close($ch);
		}
		
		
		private function get_recording_wav()
		{
			$sql = " SELECT a.* FROM t_gn_recording a
					 LEFT JOIN tms_agent b on a.RecUserDownload=b.UserId 
					 WHERE a.RecId='".$this -> escPost('onrowsid')."'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() ){
				return $qry;
			}
		}
		
		function getFileRecording()
		{
			$query = $this -> get_recording_wav();
			foreach($query -> result_assoc() as $rows )
			{
				$SQL_Update['RecStatusDownload'] = 1;  
				$SQL_Update['RecUserDownload'] = $this -> getSession('UserId');
				$SQL_Update['RecDateDownload'] =  date('Y-m-d H:i:s'); 
				$SQL_Wheres['RecId'] = $this -> escPost('onrowsid');
				
				if( $this -> set_mysql_update('t_gn_recording',$SQL_Update,$SQL_Wheres) )
				{
					$FilePath = "http://$rows[RecServerPath]";
					header("location:$FilePath");
					header("Pragma: public");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: public");
					header("Content-Description: File Transfer");
					header("Content-Type: audio/x-gsm");
					header("Content-Disposition: attachment; filename=".$rows['RecFileName']);
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize($FilePath));
					readfile($FilePath); 
					echo "Recording Download";
				}	
			}
		}	
		
	} 
	
	$DownloadRecording = new DownloadRecording();
	$DownloadRecording -> index();
?>	