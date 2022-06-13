<?
require("../fungsi/db_connect.php");
require("../sisipan/sessions.php");
require("../fungsi/global.php");
SetNoCache();

//get session
$username     	= getSession("username");
$user_group   	= getSession("user_group");

$act 			= getParam('act');
$param 			= getParam('param');

//get param value
$tsr_id			= getParam('tsr_id');
$campaign_id	= getParam('campaign_id');
$project_id		= getParam('project_id');
$amount			= getParam('amount');

					
switch($act){
	case 'lock':
		$dataLock = valueSQL("select IS_LOCK from tms_upload_history where ID = '".$param."'");
		if($dataLock == 0){
			echo  1;
			$res = execSQL("update tms_upload_history set IS_LOCK = 1, LOCK_BY = '".$username."' where ID = '".$param."'");
		}else{
			$lockBy = valueSQL("select LOCK_BY from tms_upload_history where ID = '".$param."'");
			echo $lockBy;
		}
	break;
	
	case 'unlock':	
		execSQL("update tms_upload_history set IS_LOCK = 0, LOCK_BY = null where ID = '".$param."'");		
	break;
	
	case 'cekFileUpload':	
		$q = "select FILE_NAME FROM tms_upload_history where FILE_NAME = '".trim($param)."'";
		$v = mysql_num_rows(mysql_query($q));
		
		if($v > 0)
			echo false;
		else
			echo true;
	break;
	
}


?>
