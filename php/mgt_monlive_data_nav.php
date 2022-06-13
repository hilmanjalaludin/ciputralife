<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/lib.form.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../class/class.query.parameter.php");
require(dirname(__FILE__).'/../sisipan/parameters.php');
	
?>

<!-- CE : style --> 
<!-- CS: Content data distribui --->

<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jschart/highcharts.js"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/mydasbordchart.js"></script>
<script>

/** 
 ** render autoload self function
 ** update by product id 
 **/

var CategoryId = (function(){
	doJava.File = "../class/class.monlive.data.php";
	doJava.Params ={
		action:'get_category'
	}
	return doJava.eJson();
})();

/** 
 ** render autoload jquery fuck 
 ** update by product id 
 **/

$(function(){
	$('#nav_bars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Send Message'],[],['Refresh'],[]],
		extMenu  :[['SendMessage'],[],['Refresh'],[]],
		extIcon  :[['email_go.png'],[],['cancel.png'],[]],
		extText  :true,
		extInput :true,
		extOption:[{
					render:3,
					type:'label',
					label:'<span style="color:#dddddd;">-</span>',
					id:'loading_jpg'
				  },{
					render:1,
					header:'Product Name&nbsp;',
					type:'combo',
					id:'CategoryId',
					width:180,
					name :'CategoryId',
					value :1,
					triger :'',
					store:[CategoryId]
				  }]
	});
});

new (function(){
var CategoryId = doJava.dom('CategoryId').value;
	var ChartView = new JP_Chart();
		ChartView.Create(CategoryId);
});

var Refresh = function(){
	var CategoryId = doJava.dom('CategoryId').value;
	var F5Chart = new JP_Chart();
		F5Chart.Create(CategoryId);
}

var SendMessage = function(){
	try
	{
		var FRecord  = new JP_Chart();
			FRecord.setCategoryId(doJava.dom('CategoryId').value);
		
		var UserId   = FRecord.arrayStr(FRecord.getSource().source_userid);
		var SizeData = FRecord.arrayStr(FRecord.getSource().source_size);
			doJava.File = "../class/class.monlive.data.php";
			doJava.Params = {
				action	 : 'send_broadcast_msg',			
				UserId	 : UserId,
				SizeData : SizeData
			}
			
			var error = doJava.eJson();
			if( error.result){
				alert("Success,Send Broadcast Messages!"); return false;
			}
			else{ alert("Failed,Send Broadcast Messages!"); return false; }
	}
	catch(e){
		console.log();
	}	
}

</script>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-customers">&nbsp;&nbsp;Live Size Closing</legend>	
	<div id="monitoring_dashboard" style="margin-top:3px;margin-left:0px;margin: 0 auto ;border:0px solid #000;"></div>	
	<div id="nav_bars" class="toolbars"></div>	
	
</fieldset>	