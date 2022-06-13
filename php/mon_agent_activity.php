<?
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require('../sisipan/parameters.php');
	
	//Get Sessions
	$username       = getSession("username");
	$handling_type  = getSession("handling_type");
	
?>

<iframe width="99%" height="1500px" frameborder=0 scrolling="yes" style="overflow:hidden;border:0px solid #ddd;"
	      SRC="mon_agent_activity_list.php"></iframe>

