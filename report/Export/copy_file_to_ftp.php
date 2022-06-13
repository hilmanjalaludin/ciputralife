<?php

ini_set('display_errors', 0);
// ini_set('display_errors', 1);
ini_set("error_reporting", 0);
// ini_set("error_reporting", E_ALL);

$ftp_server	= "192.168.16.252";
$ftp_port	= 22;
$ftp_username = "root";
$ftp_userpass = "root01";

$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
// if (!$ftp_conn) {
// 	echo "Could not connect to $ftp_server";
// } else {
// 	echo " connect to $ftp_server <br>";
// }
$login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);

// echo "\nDATE => ".date("h:i:s");
// echo 'tes';
// var_dump($ftp_conn);
$src_dir = "/var/www/html/development/ciputralife/report/Generated/";
$dest_dir = "/var/www/html/development/bni10092021/application/temp/";
// echo $src_dir . "*" . date('Ymd', strtotime('-1 day', strtotime(date('Y-m-d')))) . "*.xls \n";
// foreach (glob($src_dir."*".date('Ymd', strtotime(date('Y-m-d') . ' -1 day'))."*.xls") as $filename) {
foreach (glob($src_dir . "*" . date('Ymd', strtotime('-1 day', strtotime(date('Y-m-d')))) . "*.xls*") as $filename) {
	// echo "\nCOPYING $filename SIZE " . filesize($filename) . " TO FTP SERVER\n <br>";

	copy_to_ftp($ftp_conn, $filename, $dest_dir);
}

// close connection
// if($login){
// echo "login";
// }else{
// echo "asdfasdf";
// }

function copy_to_ftp($con = "", $long_file = "", $dest = "")
{
	$short_name = end(explode("/", $long_file));

	// upload file
	//  echo "\ndest short_file => " . $dest . $short_name . "\n <br>";

	
	// $result = ftp_put($con, $dest.$short_name, $long_file,  FTP_BINARY);
	// $result = ftp_put($con, $dest . $short_name, $long_file, FTP_ASCII);
	// var_dump(ftp_put($con, $data, $long_file, FTP_BINARY));


	$result = ftp_put($con, $dest.$short_name, $long_file, FTP_ASCII);
	// var_dump($result);
	// print_r($con);
	// echo $long_file."\n";
	if ($result)
		echo "\nSUCCESSFULLY UPLOADING $long_file";
	else echo "\nERROR UPLOADING $dest$short_name \n";

	
  
}



// close this connection and file handler
ftp_close($ftp_conn);
// fclose($fp);
