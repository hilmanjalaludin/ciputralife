<?
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	$pass   = $db -> getSession("pass");
	

	
	?>
			<html>
				<head>
					<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta content="utf-8" http-equiv="encoding">
					<title><?php echo $Themes -> V_WEB_TITLE; ?></title>
					<link rel="shortcut icon" href="/axa/gambar/<?php echo $Themes -> V_SYS_ICON;?>" />
				</head>
				<frameset rows="100%,*" cols="*" border="1">
					<FRAME SRC="../php/index.php"  >
				<frame src=""></frameset><noframes></noframes>
			</html>
		<?php

