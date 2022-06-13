<?php
error_reporting(E_ALL ^E_NOTICE ^E_WARNING);

/**
 * class mysql connect is main of class ,
 * connector mysql
 * author : omnes
**/

if(!define('Host','192.168.14.11')) define('Host','192.168.14.11');
if(!define('User','enigma')) define('User','enigma');
if(!define('Password','enigma')) define('Password','enigma');
if(!define('Database','enigmaaxajamsosdb')) define('Database','enigmaaxajamsosdb');
if(!define('Driver','mysql')) define('Driver','mysql');
	
/** define user level system **/
if(!define('USER_ROOT',9)) define('USER_ROOT',9);	
if(!define('USER_ADMIN',1)) define('USER_ADMIN',1);
if(!define('USER_MANAGER',2)) define('USER_MANAGER',2);
if(!define('USER_SUPERVISOR',3)) define('USER_SUPERVISOR',3);
if(!define('USER_TELESALES',4)) define('USER_TELESALES',4);
if(!define('USER_QUALITY',5)) define('USER_QUALITY',5);


/** recquire factory file ****/

// require(dirname(__FILE__).'/../factory/dbquery.factory.php');
// require(dirname(__FILE__).'/../factory/db.entity.factory.php');
// require(dirname(__FILE__).'/../factory/date.factory.php');
// require(dirname(__FILE__).'/../factory/agent.factory.php');

require(dirname(__FILE__).'/../factory/dbquery.factory.php');
require(dirname(__FILE__).'/../factory/db.entity.factory.php');
require(dirname(__FILE__).'/../factory/date.factory.php');
require(dirname(__FILE__).'/../factory/agent.factory.php');
require(dirname(__FILE__).'/../factory/db.form.factory.php');
require(dirname(__FILE__).'/../factory/db.customer.factory.php');
require(dirname(__FILE__).'/../factory/db.convert.factory.php');


class mysql
{
	public $host;
	public $user;
	public $passw;
    public $dbuse;
	public $dbconn;
    public $svconn;
    public $sqlText; 
	public $queryLimit;
	public $response;
	public $driver;
	public $fileName;
	
/** constructor class **/
	
	public function __construct()
	{
		$this -> response = 0;
		$this -> driver   = Driver;
		$this -> host	  = Host;
		$this -> user	  = User;
		$this -> passw    = Password;
		$this -> dbuse    = Database;
		
		if( !empty($this -> host) )
		{
			$this -> connect();
			$this -> factory();
		}
	}
	
   
   /** global connect **/
   
	public function connect()
	{
		$this -> dbconn = @mysql_connect($this->host, $this->user, $this->passw);
		if( $this -> dbconn )
		{
			$this->selectdb();
		}
	}
	
  /*** special selected db **/
  
	public function selectdb()
	{
		if(is_string($this->driver))
		{
			if( strtolower($this->driver) =='mysqli' )
				mysqli_select_db($this->dbuse); 
			else
				mysql_select_db($this->dbuse); 
		}
	}
	
	
	function logConsole($write_line_text,$access_dir)
	{
		$dir_report_file = "history/".$access_dir."_".date('Y-m-d').".log"; 
		$dir_handle_file = fopen($dir_report_file, 'a');
		$dir_time_file	 = gmdate("D, d M Y H:i:s");
		$write_line_text ="\n".$write_line_text ."\n";
		
		fwrite($dir_handle_file,$write_line_text ); 
		fclose($dir_handle_file);
	}
	
 /**>>! return data to array value **/
	
	public function fetcharray($res)
	{
		if(is_string($this->driver))
		{
			if( strtolower($this->driver) =='mysqli' )
				$resFetch = @mysqli_fetch_array($res);
			else
				$resFetch = @mysql_fetch_array($res);
		}
		
		if(!$resFetch)
		{
			return false;
		} else {
			return $resFetch;
			}
	}
	
	public function valueSQL($script)
	{
		$sql_res1 = $this->execute($script,__FILE__,__LINE__);
		if (!$sql_res1)
		{
			$this->errorSQL($script,$file,$line);
		}
		
		$sql_rec1 = mysql_fetch_row($sql_res1);
		return $sql_rec1[0];
	}
	
   
  /**!>> execute sql << **/
  
	 public function execute($stringSQL="",$file="",$line="")
	 {
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				$sql_res1 = mysqli_query($stringSQL);
			else
				$sql_res1 = mysql_query($stringSQL);
		}
		
		return $sql_res1;
	 }
	
	
	/**!>> singgle value of execute sql **/
	
	public function fetchval($script,$file='',$line='')
	{
		$sql_res1 = $this->execute($script,$file,$line);
		if (!$sql_res1){
			return false;
		}
		
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				$sql_rec1 = @mysqli_fetch_row($sql_res1);
			else
				$sql_rec1 = @mysql_fetch_row($sql_res1);
		}
		return $sql_rec1[0];
	}
	
	
	function disconnectDB()
	{
		if( $this -> dbconn )
		{
			mysql_close( $this -> dbconn );
		}
	}
	
 /**>>! return data to object value **/

	public function fetchrow($res)
	{
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
	
	public function fetchassoc($res)
	{
		if(is_string($this->driver)){
			if( strtolower($this->driver) =='mysqli' )
				$resFetch = @mysqli_fetch_assoc($res);
			else
				$resFetch = @mysql_fetch_assoc($res);
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
	
	// public function eof($resExecute)
	// {
		// if ($this->numrows($resExecute) == 0) return true;
		// else return false;
	// }
	
	
  /**!>> error of sql data !>>**/

	public function errorSQL($script,$file='',$line='')
	{ 
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
   
	private function set_duplicate_key($sql=array())
	{
		$sqlStr = '';
		if(is_array($sql))
		{
			$update = implode("='".implode(' ', array_map('mysql_escape_string', $sql))."',",array_map('mysql_escape_string', array_keys($sql)));
			foreach($sql as $key=>$value)
			{
					$sqlStr.= $key.'="'.$value.'",';
			}
		return "ON DUPLICATE KEY UPDATE ".substr($sqlStr,0,(strlen($sqlStr))-1);
	   }
	}
	
		
  /**!>> insert data to table selected >>!**/
   
	public function set_mysql_insert($key_tbl="",$data=array(),$key_dup="")
	{
		$sql  = sprintf('INSERT INTO %s (%s) VALUES ("%s")', $key_tbl, 
						implode(', ', array_map('mysql_escape_string', array_keys($data))), 
						implode('", "', array_map('mysql_escape_string',$data)));
						
		if(is_array($key_dup)) $sql.= $this->set_duplicate_key($key_dup);
		
		
		$this -> sqlText = $sql;
		//echo "\n".$this->sqlText."\n";
		$qry = $this->execute($sql,__FILE__,__LINE__);
		
		if($qry) : return true;
		else: 
			return false;
		endif;
		
	}
	
	public function contextENull($datas)
	{
			$clearNull = array(); 
			foreach( $datas as $key=>$value){
				if(trim($value)!=''):
					$clearNull[$key] = $value;	
				endif;
			}
			return $clearNull;
	}
	
	/** replace **/
	
	public function set_mysql_replace($key_tbl="",$data=array())
	{
		$sql  = sprintf('REPLACE INTO %s (%s) VALUES ("%s")', $key_tbl, 
						implode(', ', array_map('mysql_escape_string', array_keys($data))), 
						implode('", "', array_map('mysql_escape_string',$data)));
	
		$this -> sqlText = $sql;	
		$qry = $this->execute($sql,__FILE__,__LINE__);
		
		if($qry): return true;
		else : return false;
		endif;
		
	}
	
	public function havepost($text)
	{
		$string = $this->escPost($text);
		if( strlen($string) >0 ) : return true;
		else : return false;
		endif;
	}
/** function looping to array dimension **/
	
	function valueSqlLoop($script,$file='',$line='') 
	{
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
	
	public function set_mysql_update($key_tbl="",$data=array(),$key_identify="")
	{
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
		$sql.= $this -> getLimitUpdate();
		
		$this -> sqlText = $sql;
		$qry = $this->execute($sql,__FILE__,__LINE__);
		
		if ($qry) : return true;
		else : return false; 
		endif;
	}
	
/**& enhancment for update **/
	
	function setLimitUpdate($string='')
	{
		if( $string!=''):
			$this -> queryLimit = $string;
		else:
			return false;
		endif;	
	}
	
/**& enhancment for update **/
	
	function getLimitUpdate(){
		if( $this -> queryLimit !=''):
			return $this -> queryLimit;
		else:
			return '';
		endif;
	}
	
  /** function get error type **/
	
	public function showError()
	{
		if((is_string($this->response) || is_int($this->response)) 
			 && strlen ($this->response)>0){
			echo $this->response;
		}
		else
			return null;
	}
	
	
	
 /* get Location IP User **/
	
	public function getRealIpAddr()
	{
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

	
//////////////////////////////////////////////////////////	

	
	function setSession($setSession=null, $nameSession=null)
	{
		if(trim($setSession)!=null && trim($nameSession)!=null)
		 $_SESSION[$setSession] = $nameSession;
	}
	
	
//////////////////////////////////////////////////////////	

	function formatDateEng($date='')
	{
		if($date!='') {
			$tanggal = explode('-',$date);	
			$tgl = $tanggal[2]."-".$tanggal[1]."-".$tanggal[0]; 
			
			if( is_string($tgl) ) return $tgl;
			else return null;
		}
	}
//////////////////////////////////////////////////////////		
	function ubahLimiterTgl($date='',$awal='',$akhir='')
	{
		if($date!='') {
			$dh = explode(' ',$date);
			$tanggal = explode($awal,$dh[0]);	
			$tgl = $tanggal[2].$akhir.$tanggal[1].$akhir.$tanggal[0].' '.$dh[1]; 
			
			if( is_string($tgl) ) return $tgl;
			else return null;
		}
	}
//////////////////////////////////////////////////////////	

	function optEng($date='',$limiter)
	{
		if($date!='') {
			$tanggal = explode($limiter,$date);	
			$tgl = $tanggal[2].$limiter.$tanggal[0].$limiter.$tanggal[1]; 
			
			if( is_string($tgl) ) return $tgl;
			else return null;
		}
	}
	
 	
//////////////////////////////////////////////////////////	

 
	function getSession($sessionName=null)
	{
		if(trim($sessionName)==null) return false;
		return  $_SESSION[$sessionName];
	}
	
//////////////////////////////////////////////////////////	

	function ReadClass($dir,$file)
	{
		if($dir!=''):
			include("".$dir."/".$file."php");
		else:
			include($file."php");
		endif;
	}
	
	
//////////////////////////////////////////////////////////	

	public function escapeSQL($sql='')
	{
		if($sql!=''){
			return mysql_escape_string($sql);
		}	
	}
	
//////////////////////////////////////////////////////////	

	function formatDateId($dDate)
	{
		$dNewDate = strtotime($dDate);
		if($dDate)
			return date('d-m-Y',$dNewDate);
	}
	
//////////////////////////////////////////////////////////	

	function get_insert_id()
	{
	  	
		$last_insert = mysql_insert_id();
		if ( $last_insert ) return $last_insert;
		else
			return null;
	}
	
//////////////////////////////////////////////////////////	
	
	function escPost($str='')
	{
		return mysql_real_escape_string($_REQUEST[$str]);
	}
	
/* 
 * function get decode base 64 data  <decrypt>  Or decryp data 
 * return @ string
**/

	function nextDate($date)
	{
			$dates = explode("-", $date);
			$yyyy = $dates[0];
			$mm   = $dates[1];
			$dd   = $dates[2];
			
			$currdate = mktime(0, 0, 0, $mm, $dd, $yyyy);
			
			$dd++;
			/* ambil jumlah hari utk bulan ini */
			$nd = date("t", $currdate);
			if($dd>$nd){
				$mm++;
				$dd = 1;
				if($mm>12){
					$mm = 1;
					$yyyy++;
				}
			}
			
			if (strlen($dd)==1)$dd="0".$dd;
			if (strlen($mm)==1)$mm="0".$mm;
			
			return $yyyy."-".$mm."-".$dd;
	}
	
//////////////////////////////////////////////////////////	
	
	public function SQLnull($datas)
	{
			$clearNull = array(); 
			foreach( $datas as $key=>$value){
				if(trim($value)!=''):
					$clearNull[$key] = $value;	
				endif;
			}
			return $clearNull;
	}
	
/* insert to activity log if found event by User , Return boolean, true or false **/
/* insert to activity log if found event by User , Return boolean, true or false **/
  
	public function activityLog($activityEvent='')
	{
		$datas= array(
				'ActivityUserId'=> $this-> getSession('UserId'), 
				'ActivityDate'=> date('Y-m-d H:i:s'), 
				'ActivityEvent'=> $activityEvent
			);
		$query = $this ->set_mysql_insert('t_gn_activitylog',$datas);	
		if( $query ) : return true;
		else : return false;
		endif;	
	}
	
/* 
 * function get decode base 64 data  <decrypt>  Or decryp data 
 * return @ string
**/

	public function decryptBase64($base64 = '' )
	{
		if( !empty($base64) && strlen($base64) > 0 )
		{
			return base64_decode($base64);
		}
	}
	
/* 
 * function get decode base 64 data  <decrypt>  Or decryp data 
 * return @ string
**/

	public function encryptBase64($base64 = '' )
	{
		if( !empty($base64) && strlen($base64) > 0 )
		{
			return base64_encode($base64);
		}
	}	
	
/*
 * calling dbfactory methode 
 * return @object class 
 */
	public function query( $SQL_string=NULL )
	{
		if( ($SQL_string!=NULL) )
			return new DBquery($SQL_string);
		else{
			return false;
		}	
	}
	
/** read all factory @ procedure *****/

public function factory()
	{
		//$factory_class = array('Entity'=>'Entity','Date'=>'DateFactory','Users'=> 'Users');
		$factory_class = array('Entity'=>'Entity','Date'=>'DateFactory','Users'=> 'Users','DBForm'=>'Form','Customer'=>'Customer','Convert'=>'Convert');
		
		foreach($factory_class as $class_id => $class_names )
		{
			$this -> $class_id = new $class_names();
		}
	}
}

	function connectDB(){
			$db = new mysql();
			if(!is_object($db)): Return $db; 
			else:
				 Return $db; 
			endif;
	}
	$db = connectDB();
	
	
?>
