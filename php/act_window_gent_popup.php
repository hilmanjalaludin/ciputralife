<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
?>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript">
	var getUserState = function(){
		doJava.File = "../class/class.userstate.phone.php";
		doJava.Params ={
			action:'user_state'
		}
		doJava.Load('UserSatate');
	}
	
	var ChatWithUser = function(box)
	{
		window.opener.chatWith(box.id);
	}
	setInterval("getUserState();",1000);

</script>
<div style="margin-top:-1px;">
	<fieldset style="border:1px solid #eee;margin-top:-1px;">
		<legend style="font-family:Arial;font-size:12px;color:green;"> Select Users </legend>
		<div id="UserSatate" style="height:200px;overflow:auto;border:0px solid #eee;margin-top:-1px;"> </div>
	</fieldset>
</div>