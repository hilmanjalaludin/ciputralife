<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	$sql = "select ScriptId, ProductId, ScriptFileName, ScriptFlagStatus, ScriptUpload, UploadDate, UploadBy  from 
			t_gn_productscript where ScriptId='".$_REQUEST['scriptid']."' AND  ScriptFlagStatus=1";
	$qry = $db ->execute($sql,__FILE__,__LINE__);
	$row = $db -> fetchrow($qry);
	
	if( $row->ScriptFileName!='' ){
		header("location:../script/".$row->ScriptFileName);
	}
	
	
?>