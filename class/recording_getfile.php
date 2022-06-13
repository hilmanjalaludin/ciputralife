<?php

//require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
require('../sisipan/parameters.php');

global $db;

//Get Sessions
$username     = getSession("username");
$user_agency  = getSession("user_agency");

//Get Parameters
$rec_id = getParam("rec_id");

//get file information
$sql = "SELECT * FROM cc_recording rec WHERE id=$rec_id";
$res = $db->execute($sql);



if($row = $db->fetchassoc($res)) {
	$rec_filename = $row["file_voc_name"];
	$rec_fileloc  = $row["file_voc_loc"];
	$rec_filesize = $row["file_voc_size"];
	$rec_ext	  = $row['agent_ext'];
	
	$ext_prefix = substr($rec_ext, 0, 2);
	//untuk penanganan ngambil dari server lain
	$location = "http://192.168.16.6/recording/";
	//$location = $locList[$ext_prefix];
	
	
	$name = $rec_fileloc."/".$rec_filename;
	//remove /opt/enigma/log/voice/
	$name = substr($name, 22);
	$name = $location.$name;		
	$wav_filename = $rec_filename.".wav";
	
	//get file
	exec("wget -O tmpvoice/$rec_filename ".$name);
	//convert
	exec("nice sox tmpvoice/$rec_filename tmpvoice/$wav_filename && rm -f tmpvoice/$rec_filename");
	// exec("nice sox tmpvoice/$rec_filename tmpvoice/$wav_filename ");
	
	
	$mtype = "audio/x-wav";
	$file_path = "tmpvoice/$wav_filename";
	// set headers
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Type: $mtype");
	header("Content-Disposition: attachment; filename=\"$wav_filename\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($file_path));
	
	@readfile($file_path);
	unlink($file_path);
	exit;
	
}

?>
