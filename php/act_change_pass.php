<?
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../fungsi/db_connect.php");
	require("../class/MYSQLConnect.php");
	//require('../sisipan/parameters.php');
	
	$ipAddr = getRealIpAddr();
	//Get Sessions
	$username       = getSession("username");
	$user_agency    = getSession("user_agency");
	$handling_type  = getSession("handling_type");
	
	//Get Parameters
	$curr_password    = getParam("curr_password");
    $new_password     = getParam("new_password");
    $re_new_password  = getParam("re_new_password");
  
	$warn = "";
	
	$ada = valueSQL("SELECT COUNT(id) FROM tms_agent WHERE id='".$username."' AND password=MD5('".$curr_password."')");
	
	if($ada==0) {
	  $warn = "Wrong current password";
	} else {
		if($new_password!="1234"){
		  execSQL("UPDATE tms_agent SET password=MD5('".$new_password."'), update_password=now() WHERE id='".$username."'");
		  execSQL("INSERT INTO tms_agent_log (agent_id,ip_address, password, memo, log_date) VALUES ('".$username."','".$ipAddr."', MD5('".$new_password."'), 'Ganti Password', now())");
		  $warn = "Password has been changed";
		}
	}  
	
	              
	              
	echo $warn;
?>

