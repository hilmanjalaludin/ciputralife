<?php
	require("fungsi/global.php");
	require("class/MYSQLConnect.php");
	require("class/class.application.php");
	require("sisipan/parameters.php");
	
	// session 
	
	$app -> issetSession();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="author" content="<?php echo $V_WEB_AUTHOR; ?>" />
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <title><?php echo $Themes -> V_WEB_TITLE;?></title>
	
	<!-- CSS -->
	
    <link type="text/css" rel="stylesheet" href="<?php echo $app->base_app();?>gaya/gaya_utama.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->base_app();?>gaya/other.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->base_app();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $Themes->V_UI_THEMES; ?>/ui.all.css" rel="stylesheet" />	
	<link type="text/css" rel="stylesheet" href="<?php echo $app->base_app();?>gaya/chat.css" media="all"/>
    <link type="text/css" rel="stylesheet" href="<?php echo $app->base_app();?>gaya/screen.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->base_app();?>gaya/custom.css"/>
	
    <!--jQuery-->
	<script type="text/javascript" src="<?php echo $app->base_app();?>js/javaclass.js"></script>    	
	<script type="text/javascript" src="<?php echo $app->base_app();?>pustaka/jquery/jquery-1.3.2.js"></script>    
	<script type="text/javascript" src="<?php echo $app->base_app();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo $app->base_app();?>pustaka/jquery/plugins/jquery.form.js"></script>
    <script type="text/javascript" src="<?php echo $app->base_app();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
	
	<script>
	  var message_error = 0;
		var doLogin  = function()
		{
			doJava.File = 'class/class.user.login.php';
			doJava.Method = 'POST';
				var Username = doJava.dom('username').value;
				var Password = doJava.dom('password').value;
			
				if( (Username.length < 1) || ( Password.length < 1 ) ){
					alert('Error, Incorrect Username Or Password. Please try again..!');
					return false;
				}else{	
					doJava.Params ={
						action:'login',
						username:Username,
						password:Password
					}
					message_error = doJava.eJson();
					if( message_error.result==1){
						window.location ='include/main.php' 
					}
					else if( message_error.result==2){
						alert('Error ,Your Account Already Login On Other Location ');
						window.location= window.location.href
					}
					else
						alert('Error, Incorrect Username Or Password. Please try again..!');
				}
		}
	
/* modal dialog */
	
		var UserLogin = function(){	
			jQuery(document).ready(function(){
				jQuery("#loginUser").dialog({
					title :'<span style="padding-top:5px;border:0px solid #dddddd;"><img src="gambar/icon/group_key.png"></span> &nbsp; <span style="position:absolute;top:-2;">User Login</span>',
					bgiframe : false,
					width : 320, 
					height : 200,
					autoOpen : true, 
					cache : false,
					show : "drop",
					direction : 'up',
					modal : true, 
					closeOnEscape : false,
					resizable : false,
					buttons: {
						Login: function(){
							doLogin();
						},
						Cancel:function(){
							window.location= window.location.href
						}
					}
				}).dialog("open");
				
				setFocus();
			});
	 }
	 
/* set focus **/
	var setFocus = function()
	{	
		  doJava.onReady(
			evt = function(){
				window.menubar=function(){ return false; }
				document.oncontextmenu=new Function("return false")
				doJava.dom('username').focus()
			},
			evt()
		)
	}	

 /* init enter **/
 
	window.onkeypress=function(e){
		var winEvent = e;
		if( winEvent.keyCode ==13){
			doLogin();
		}
		else if( winEvent.keyCode==8 ){
			return 0;
		}
		else if( winEvent.keyCode==27 ){
			window.location= window.location.href
		}
		else
			return;
	}
	
	</script>
</head>
<body onload="UserLogin();">
  <div id="loginUser" style="border:0px solid #000;">
		<table align="center" cellpadding="4px;" width="99%">
			<tr>
				<td width="20%" style="height:24px;font-family:Arial;font-size:12px;">Username</td>
				<td width="69%" style="height:24px;font-family:Arial;font-size:12px;"> 
				<input name="username" id="username" type="text" value="" 
					style="width:200px;height:20px;border:1px solid #dddddd;background-color:#f5f8fa;"></td>
			</tr>
			<tr>
				<td style="height:24px;font-family:Arial;font-size:12px;">Password</td>
				<td style="height:24px;font-family:Arial;font-size:12px;"> 
				<input name="password" id="password" type="password" value="" 
					style="width:200px;height:20px;border:1px solid #dddddd;background-color:#f5f8fa;"></td>
			</tr>	
		</table>
  </div>    
</body>
</html>
