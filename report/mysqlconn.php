<?
error_reporting(E_ALL ^E_NOTICE ^E_WARNING);

class mysql{
	
	var $host;
	var $user;
	var $passw;
    var $UseDB1;
	var $UseDB2;
    var $dbconn;
    var $svconn;
    var $executedSQL;
	
	
/* set response global message **/
 	
	public $response = 0;

/** set driver typ**/
	
	public $driver   = 'mysql';
	
	
	/**
		mysql_connect("10.5.52.3","enigma","enigma") or die (mysql_error());
		mysql_select_db("enigmasecuredb"); 
	
	**/
/** constructor class **/
	
	function __construct(){
		$this->host	  = 'localhost';
		$this->user	  = 'enigma';
		$this->passw  = 'enigma';
		$this->UseDB1 = 'CignaEnigmaDB';
		$this->connect();
	}
	
   
   /** global connect **/
   
	public function connect(){
		mysql_connect($this->host, $this->user, $this->passw);
		$this->selectdb();
		
	}
	
  /*** special selected db **/
  
	public function selectdb(){
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				mysqli_select_db($this->UseDB1); 
			else
				mysql_select_db($this->UseDB1); 
		}
	}

	
 /**>>! return data to array value **/
	
	public function fetcharray($res){
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				$resFetch = @mysqli_fetch_array($res);
			else
				$resFetch = @mysql_fetch_array($res);
		}
		
		if(!$resFetch){
			return false;
		} else {
			return $resFetch;
			}
	}
	
   
  /**!>> execute sql << **/
  
	 public function execute($stringSQL="",$file="",$line="") {
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				$sql_res1 = mysqli_query($stringSQL);
			else
				$sql_res1 = mysql_query($stringSQL);
		}
		
		if (!$sql_res1){
			$this->errorSQL($stringSQL, $file, $line);
		}
		
		return $sql_res1;
	 }
	
	
	/**!>> singgle value of execute sql **/
	
	public function fetchval($script,$file='',$line='') {
		$sql_res1 = $this->execute($script,$file,$line);
		if (!$sql_res1){
			$this->errorSQL($script,$file,$line);
		}
		
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				$sql_rec1 = @mysqli_fetch_row($sql_res1);
			else
				$sql_rec1 = @mysql_fetch_row($sql_res1);
		}
		return $sql_rec1[0];
	}
	
	
 /**>>! return data to object value **/

	public function fetchrow($res){
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				$resFetch = @mysqli_fetch_object($res);
			else
				$resFetch = @mysql_fetch_object($res);
		}
		
		if(!$resFetch){
			return false;
		} else {
			return $resFetch;
		}
	}
	
   /**>>! return number off rows tables selected  **/

   
	public function numrows($resExecute)  {
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				$intJmlBrs = @mysqli_num_rows($resExecute);
			else
				$intJmlBrs = @mysql_num_rows($resExecute);
		}
		
		if( is_int($intJmlBrs)) return $intJmlBrs;
		
	}

  /**>> singgle value of execute sql **/
	
	public function eof($resExecute){
		if ($this->numrows($resExecute) == 0) return true;
		else return false;
	}
	
	
  /**!>> error of sql data !>>**/

	public function errorSQL($script,$file='',$line=''){ 
		echo '<table border="0" cellpadding=1 cellspacing=1 bgcolor="silver" width="50%" align="center">'.
			 '<tr valign="top">'.
			 '	<th nowrap align="right" bgcolor="white">Error No :</th>'.
			 '	<td bgcolor="white">'.mysql_errno().'</td>'.
			 '</tr>'.
			 '<tr valign="top">'.
			 '	<th nowrap align="right" bgcolor="white">Error :</th>'.
			 '	<td bgcolor="white">'.mysql_error().'</td>'.
			 '</tr>'.
			 '<tr valign="top">'.
			 '	<th nowrap align="right" bgcolor="white">Script :</th>'.
			 '	<td bgcolor="white">'.$script.'</td>'.
			 '</tr>'.
			 '<tr valign="top">'.
			 '	<th nowrap align="right" bgcolor="white">File :</th>'.
			 '	<td bgcolor="white">'.($file==''?"There\'s no file information":$file).'</td>'.
			 '</tr>'.
			 '<tr valign="top">'.
			 '	<th nowrap align="right" bgcolor="white">Line :</th>'.
			 '	<td bgcolor="white">'.($line==""?"There's no line information":$line).'</td>'.
			 '</tr>'.
			 '</table>';
	}
	
  /**!>> implode sql update >>**/
   
	private function set_duplicate_key($sql=array()){
		$sqlStr = '';
		if(is_array($sql)){
			$update = implode("='".implode(' ', array_map('mysql_escape_string', $sql))."',",array_map('mysql_escape_string', array_keys($sql)));
			foreach($sql as $key=>$value){
					$sqlStr.= $key.'="'.$value.'",';
			}
		return "ON DUPLICATE KEY UPDATE ".substr($sqlStr,0,(strlen($sqlStr))-1);
	   }
	}
	
		
  /**!>> insert data to table selected >>!**/
   
	public function set_mysql_insert($key_tbl="",$data=array(),$key_dup=""){
		$sql  = sprintf('INSERT INTO %s (%s) VALUES ("%s")', $key_tbl, 
						implode(', ', array_map('mysql_escape_string', array_keys($data))), 
						implode('", "', array_map('mysql_escape_string',$data)));
						
		if(is_array($key_dup)) $sql.= $this->set_duplicate_key($key_dup);
		
	
		$qry = $this->execute($sql,__FILE__,__LINE__);
		
		if($qry){
			$this->executedSQL = true;
		}else {
			$this->executedSQL = false;
		}
	}

/** function looping to array dimension **/
	
	function valueSqlLoop($script,$file='',$line='') {
		$sql_res1 = $this->execute($script,$file,$line);
		if (!$sql_res1){
			$this->errorSQL($script,$file,$line);
		}
			$d = array();
			$i = 0;
			while($sql_rec1 = $this->fetcharray($sql_res1)){
				$d[$i][0] = $sql_rec1[0];
				$d[$i][1] = $sql_rec1[1];
				$d[$i][2] = $sql_rec1[2];
				$d[$i][3] = $sql_rec1[3];
				$d[$i][4] = $sql_rec1[4];
				$i++;
			}
			
		return $d;
	}
	
 /* function set mysql update **/
	
	public function set_mysql_update($key_tbl="",$data=array(),$key_identify=""){
		$v_upt='';
		if(is_array($data)){
			foreach($data as $key=>$val){
				$v_upt .= $key."='".mysql_escape_string($val)."',";
			}
		}
		
		$v_upt = substr($v_upt,0,(strlen($v_upt))-1);
		
		
		$i_dfr = '';
		if(is_array($key_identify)){
			if(sizeof($key_identify)>1){
				$i_dfr = 'WHERE ';
				foreach($key_identify as $idf_key=>$idf_val){
					$i_dfr.=$idf_key."='".mysql_escape_string($idf_val)."' AND ";
				}
				$i_dfr = substr($i_dfr,0,(strlen($i_dfr))-5);
				
			}
			else {
				foreach($key_identify as $id_key=>$id_val){
				$i_dfr = " WHERE ".$id_key."='".mysql_escape_string($id_val)."'";
				}
			}
		}
		
		$sql = " UPDATE ".$key_tbl." SET ".$v_upt." ".$i_dfr;
		$qry = $this->execute($sql,__FILE__,__LINE__);
		if ($qry) $this->executedSQL = true;
		else $this->executedSQL = false;
	
	}
	
  /** function get error type **/
	
	public function showError(){
		if((is_string($this->response) || is_int($this->response)) 
			 && strlen ($this->response)>0){
			echo $this->response;
		}
		else
			return null;
	}
	
	
	/** loader php header **/
	
	 public function windowLocation($file='',$param=''){
		if($file!='' && $param!=''){
			if(is_array($param)){
				$str = '';
				foreach($param as $key=>$val){ $str .= $key.'='.$val.'&';}
				header('location:'.$file.'?'.$str);
			}
			else 
				header('location:'.$file.'?customerid='.$param);
		}else{
			header('location:'.$file);
		}
	}
	
 /* get Location IP User **/
	
	public function getRealIpAddr(){
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			$ip = $_SERVER['HTTP_CLIENT_IP'];
			
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			
		else
			$ip = $_SERVER['REMOTE_ADDR'];
		
		if ($ip) 
			return $ip;
		else
			return null;
	}

/* function set session **/
	
	function setSession($setSession=null, $nameSession=null){
		if(trim($setSession)!=null && trim($nameSession)!=null)
		 $_SESSION[$setSession] = $nameSession;
	}
	
	
  /* format tanggal return **/
  
	function formatDateEng($date=''){
		if($date!='') {
			$tanggal = explode('-',$date);	
			$tgl = $tanggal[2]."-".$tanggal[1]."-".$tanggal[0]; 
			
			if( is_string($tgl) ) return $tgl;
			else return null;
		}
	}
	
 /* function get session **/
 
	function getSession($sessionName=null){
		if(trim($sessionName)==null) return false;
		return  $_SESSION[$sessionName];
	}
	
/** load model file **/
	
	public function mLoad($file=''){
		if(!$file) echo "No Modul File to require";
		
			require("../modul/{$file}.php");
	}

 /** load model file **/
	
	public function cLoad($file=''){
		if(!$file) echo "No Control File to require";
			
			require("../control/{$file}.php");
	}
	
 /** load model file **/
	
	public function rLoad($file=''){
		if(!$file) echo "No Report File to require";
			
			require("../report/{$file}.php");
	}
	
 /** load model file **/
	
	public function escapeSQL($sql=''){
		if($sql!=''){
			return mysql_escape_string($sql);
		}	
	}
	
 /* get last insert id **/
 
	function get_insert_id(){
	  	
		$last_insert = mysql_insert_id();
		if ( $last_insert ) return $last_insert;
		else
			return null;
	}
	
 /* redirect file content **/
	
	function redirect($content=null){
		if($content!=null)
		header("Location:".$content);
	}
}
?>