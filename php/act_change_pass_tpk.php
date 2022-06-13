<?
	//last edited by wanthook at 8 Februari 2010
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	//require('../sisipan/parameters.php');
	
	$ipAddr 		= $db -> getRealIpAddr();
	$username       = $db -> getSession("username");
	$user_agency    = $db -> getSession("user_agency");
	$handling_type  = $db -> getSession("handling_type");
	
	//Get Parameters
	
   $curr_password   = $db -> escPost("curr_password");
   $new_password    = $db -> escPost("new_password");
   $re_new_password = $db -> escPost("re_new_password");
  
	
	$ada = $db->valueSQL("SELECT COUNT(id) FROM tms_agent WHERE id='".$username."' AND password=MD5('".$curr_password."')");
	
	if($ada==0) {
	 	echo "Wrong current password ";
	} else {
		if($new_password==$re_new_password)
		{
			if(($new_password!="1234") && ($new_password != $curr_password))
			{
				$db ->execute("UPDATE tms_agent SET password=MD5('".$new_password."'), update_password=now() WHERE id='".$username."'");
				$db ->execute("INSERT INTO tms_agent_log (agent_id,ip_address, password, memo, log_date) VALUES ('".$username."','".$ipAddr."', MD5('".$new_password."'), 'Ganti Password', now())");
				echo"Password has been changed";
			}
			else
			{
				echo "Do not use the default and old password again!!!";
			}
		}
		else
		{
			echo "Wrong current password";
		}
	}  
	
?>

