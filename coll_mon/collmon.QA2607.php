<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
?>
<HTML>
	<head>
		<title><?php echo $Themes->V_WEB_TITLE; ?> :: Coll Monitoring </title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta http-equiv="Content-Style-Type" content="text/css">
		<meta http-equiv="Content-Script-Type" content="text/javascript">
		<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>
		<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?rev=ext.v.0.2"></script>
		<script language="javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>	
		<script type="text/javascript" src="<?php echo $app->basePath();?>js/EUI_1.0.2.js?time=<?php echo time();?>"></script>
		<link rel="shortcut icon" href="<?php echo $app->basePath();?>gambar/enigma.ico" />
		<link rel="stylesheet" type="text/css" href="styles.css" />
		<style>
			.select{height:23px;border:0px solid silver;overflow:hidden;color:green;font-family:Tahoma;font-weight:normal;}
			td{font-family:Tahoma;font-size:12px;}
			legend {font-family:Tahoma;font-size:12px;color:#6b6a6d;font-weight:bold;}
			.select-rows{background-color:#fffffd;} 
			.select-rows:hover{background-color:#fffccc;color:blue;font-weight:normal;}
			fieldset{border:1px solid #dddddd;}
			.input{border:1px solid red;width:160px;height:20px;}
			.textarea{border:1px solid red;width:250px;height:90px;}
			.inputtime{border:1px solid red;width:40px;height:20px;}
			.select_point{width:90px;border:1px solid #dddddd;color:red;font-size:12px;height:22px;}
			.overall_point{text-align:center;width:35px;border:1px solid #ffffff;color:red;font-size:12px;height:18px;}
			.red_yellow{border:1px solid #000000;}
			table{border:1px solid #dddddd;}
			.text{text-align:right;padding-right:3px;height:20px;border:1px solid #FFFFFF;}
			.box{text-align:right;padding-right:3px;height:20px;border:1px solid #dddddd;width:30px;color:red;}
			.copetency_g{font-weight:bold;border:1px solid #FFFFFF;color:white;background-color:green;text-align:center;}
			.copetency_a{font-weight:bold;border:1px solid #FFFFFF;color:blue;background-color:yellow;text-align:center;}
			.copetency_p{font-weight:bold;border:1px solid #FFFFFF;color:white;background-color:red;text-align:center;}
			.info {text-align:left;}
		</style>
		<script language="javascript">
		var CustomerId  = '<?php echo $_REQUEST['CustomerId']; ?>';
		
		/**
		 ** store data nilai
		 **/
		var _get_store = (function(){
		 return( 
				Ext.Ajax({
					url		: 'class.callmon.QA.php',
					method 	: 'POST',
					param 	: {
						action:'get_store_nilai'
					}	
				
				}).json()
			);
		})();
		/**
		 ** get content header
		 **/ 
		var getContentHeader = function()
		{
			try{
				Ext.Ajax({
					url		: 'class.callmon.QA.php',
					method	: 'POST',
					param 	: {
						action:'get_content_header',
						customerid:CustomerId
					}
				}).load("coll_header");
				
				//getLookup();	
			}
			 catch(e){
				alert(e)
			}	
		}
		// var getLookup = function()
		// {
			// Ext.Ajax({
				// url		: 'class.callmon.QA.php',
				// method	: 'POST',
				// param 	: {
					// action:'look_up_point'
				// }
			// }).load("coll_content_information");
		// }
		var getCallContent = function()
		{
			Ext.Ajax({
				url		: 'class.callmon.QA.php',
				method	: 'POST',
				param 	: {
					action:'get_content_call',
					customerid:CustomerId
				}
			}).load("coll_content");
		}
		/**
		 ** store data subcategory
		 **/
		var DataSub = (function(){
			doJava.File = "class.callmon.QA.php";
			doJava.Params = {
				action:'get_sub_category'
			}
			try{
				return doJava.eJson();
			}
			catch(e){
				console.log(e);;
			}	
		})();
		
		/**
		 ** store data category
		 **/
		 var DataCategory = function(){
			doJava.File = "class.callmon.QA.php";
			doJava.Params = {
				action:'get_category'
			}
			try{
				return doJava.eJson();
			}
			catch(e){
				console.log(e);
			}	
		}();
		
		/**
		 ** get sum per category
		 **/ 
		 
		var Call =  function(CategoryId)
		{
			var totals = 0;
			for( var i in DataSub[CategoryId] )
			{
				var _count = Ext.Cmp("nilai_"+CategoryId+"_"+DataSub[CategoryId][i]).getValue();	
				if( _count!='' ){
					
					
					totals+=parseInt(_count);
				}	
			}
			Ext.Cmp("cust_totals_"+CategoryId).setValue(totals) ;
		}
		
		/**
		***menghitung total score percategory
		***
		**/
		var categoryscore = function(CategoryId){
			var xscore=0;
			var overall=0;
			if(CategoryId=='1'){
				xscore = parseInt(Ext.Cmp('cust_totals_'+CategoryId).getValue())*10;
				Ext.Cmp('call_acc_1').setValue(xscore);
				if(xscore=='800'){Ext.Cmp('call_acc_2').setValue('80');}
				else{Ext.Cmp('call_acc_2').setValue('0');}
			}
			else if(CategoryId=='2'){
				xscore = parseInt(Ext.Cmp('cust_totals_'+CategoryId).getValue())*10;
				Ext.Cmp('courtesy_1').setValue(xscore);
				if(xscore=='200'){Ext.Cmp('courtesy_2').setValue('20');}
				else{Ext.Cmp('courtesy_2').setValue(xscore/10);}
			}
			overall=parseInt(Ext.Cmp('call_acc_2').getValue())+parseInt(Ext.Cmp('courtesy_2').getValue());
			Ext.Cmp('overall').setValue(overall);
		}
		
		var rating= function(){
			var allscore=parseInt(Ext.Cmp('overall').getValue());
			if(allscore>=86){
				Ext.Cmp('rating').setValue('A');
			}
			else if(allscore>=70 && allscore<86){
				Ext.Cmp('rating').setValue('B');
			}
			else{
				Ext.Cmp('rating').setValue('C');
			}
		}
		/**
		 ** Menjumlah Semua Score saat load
		 **/ 
		function sumoverall ()
		{
			var overall = 0;
			
			for( var i in DataCategory)
			{
				var xscore=0;
				if(DataCategory[i]=='1'){
					xscore = parseInt(Ext.Cmp('cust_totals_'+DataCategory[i]).getValue())*10;
					Ext.Cmp('call_acc_1').setValue(xscore);
					if(xscore=='800'){Ext.Cmp('call_acc_2').setValue('80');}
					else{Ext.Cmp('call_acc_2').setValue('0');}
				}
				else if(DataCategory[i]=='2'){
					xscore = parseInt(Ext.Cmp('cust_totals_'+DataCategory[i]).getValue())*10;
					Ext.Cmp('courtesy_1').setValue(xscore);
					if(xscore=='200'){Ext.Cmp('courtesy_2').setValue('20');}
					else{Ext.Cmp('courtesy_2').setValue(xscore/10);}
				}
			}
			overall=parseInt(Ext.Cmp('call_acc_2').getValue())+parseInt(Ext.Cmp('courtesy_2').getValue());
			Ext.Cmp('overall').setValue(overall);
		}
		
		/**
		 ** get nilai data 
		 ** per category -> subcategory
		 **/
		 
		 var getNilai = function(CategoryId, SubCategoryId, optional ){
			// try
			// {
				var _element_id = "nilai_"+CategoryId+"_"+SubCategoryId;
				// var nilai = doJava.dom(_element_id).value;
				// alert(nilai);
				var nilaibaru = 0;
				//try{
					if (optional !=''){
						nilaibaru = _get_store['nilai'][CategoryId][SubCategoryId][optional];	
					}
					else{
						nilaibaru ='';
					}
					doJava.dom("nilai_"+CategoryId+"_"+SubCategoryId).value = nilaibaru;
					Call(CategoryId);	
					categoryscore(CategoryId);
					rating();
				
				// }
				// catch(e){
					// alert(e);
				// }
			// }
			// catch(e){
			  // console.log(e)	
			// }
		 }
		
		var windowReady = function(){
			getContentHeader();
			getCallContent();
			sumoverall();
			rating();
			// doJava.dom('rating').style.color="#FFFFFF";
			// Ext.Cmp('rating').getElementId().style.backgroundColor="RED";
		}
		var validasi = function(){
			var success = 0;
			for( var i in DataCategory)
			{
				for( var j in DataSub[DataCategory[i]])
				{
					if (Ext.Cmp("nilai_"+DataCategory[i]+"_"+DataSub[DataCategory[i]][j]).getValue()==''){
						alert("Please select score");
						Ext.Cmp("nilai_"+DataCategory[i]+"_"+DataSub[DataCategory[i]][j]).setFocus();
						return false;
					}
					else{success++;}
				}
			}
			if( success > 0) {return true;}
			else {return false;}
		}
		var savescore = function(){
		try
		{
			if (validasi()){
				//if(validasiUW()){
					if( confirm('Do you want to save ?')){	
						var Error = 
						( 
							Ext.Ajax({
								url 	: 'class.callmon.QA.php',
								method 	: 'POST',
								param 	: {
									action : 'save_call_mon&'+Ext.Join( new Array(Ext.Serialize('frm1').getElement())).http(),
									customerid : CustomerId
								}
							}).responseText() 
						);
						if( Error!=''){ alert("Success, save scoring data !"); }//window.close("WNScoring");}
						else{ alert("Failed, save scoring data !"); }
					}
				//}
			}
		}
		catch(e){
			console.log(e);
		}
	}
		var Exit = function (){
			if (confirm("Close Window?")) {
				close();
			}
		}
		
		$(document).ready(function(){
			$('#toolbars2').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Save'],[],['Exit']],
				extMenu  :[['savescore'],[],['Exit']],
				extIcon  :[['disk.png'],[],['door_out.png']],
				extText  :true,
				extInput :true,
				extOption:[]
			});
		});
		</script>
	</head>
	<body onload="javascript:windowReady();">
		
		<form name="frm1" id="frm1">
		<table align="center">
			<tr>
				<td>
					<table border=0 width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" class="extToolbars">
								<h3 style="color:blue;margin-left:4px;">QA SCORING CALL MONITORING / SALES QUALITY </h3>
							</td>
						</tr>
						<tr>
							<td><div id="coll_header" style="padding:5px;border:0px solid #000;"></div></td>
						</tr>	
						
						<tr>
							<td></td>
						</tr>

						<tr>
							<td>
								<fieldset style="margin-bottom:2px;">
									<legend> Score Content</legend>
									<div id="coll_content"> </div>
								</fieldset>
							</td>
						</tr>
						
						<tr style = "display:none">
							<td>
								<fieldset style="margin-bottom:2px;">
									<legend> Underwriting</legend>
									<div id="coll_underwriting"> </div>
								</fieldset>
							</td>
						</tr>
						
					</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:10px;">	
					<!-- cs : start toolbars -->
						<div id="toolbars2"></div>
					<!-- ce : start toolbars -->
					
				</td>
			</tr>
		</table>
		</form>
	</body>
</HTML>