<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require('../sisipan/parameters.php');
	
	define("ASTMAN_HOST", IP_PBX()); 
	require('../include/astlib.php');

	
/** get paramater ***/

	$username     = $db -> getSession("username");
	$user_group   = $db -> getSession("user_group");
	$user_profile = $db -> getSession("handling_type");
	$mgr_id		  = $db -> getSession("mgr_id");
	$spv_id		  = $db -> getSession("spv_id");
	
/** get ip PBX ***/
	
	function IP_PBX()
	{
		global $db;
		$sql = "select a.set_value from cc_pbx_settings a where a.set_name='host' ";
		$qry = $db -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			return $qry -> result_singgle_value();
		}
	}


/** get ip PBX ***/
	
	function actionSpy()
	{
		global $db;
		$src 	= $db -> escPost("src");
		$target	= $db -> escPost("target");
		astChanSpy("SIP/".$src, "SIP/".$target, "centerback", "");
	}
	
/** get ip PBX ***/	
	
	function actionSpyW()
	{
		global $db;
		$src = $db -> escPost("src");
        $target = $db -> escPost("target");
        astChanSpyWhisper("SIP/".$src, "SIP/".$target, "centerback", "");
    }
	

/** cek is valid post ****/
	
if( $db -> havepost('action'))
{	
	switch($db -> escPost('action') )
	{
		case "spy":  actionSpy(); break;
		case "spyw": actionSpyW(); break;
	}
}
	
?>
