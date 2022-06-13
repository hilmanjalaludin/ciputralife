<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select a.CutoffDate as value, a.CutoffDate as text from  t_lk_cutoffdate a";
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	
	
	/** user group **/
	
	/** get Call status list **/
 
	function getEfectivetatus(){
		global $db;
		$sql = "select a.CutoffDate as value, a.CutoffDate as text from  t_lk_cutoffdate a";
				
		$qry = $db->execute($sql,__file__,__line__);
		while( $res = $db->fetchrow($qry) ){
			$datas[ $res -> value] = $db->formatDateId($res -> text); 
		}
	  
	  return "[".json_encode($datas)."]";
		
	}
	
	
?>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		
		var efectiveList =<?php echo getEfectivetatus(); ?> 
		$(function(){
		
			$('.corner').corner();
			$('#toolbars').corner();
			
			
			
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Add'],['Cancel'],['']],
				extMenu  :[['addEfective'],['cancelResult'],['']],
				extIcon  :[['add.png'],['cancel.png'],['']],
				extText  :true,
				extInput :true,
				extOption: [{
						render:2,
						header:'Cutoff Date',	
						type:'combo',
						triger:'showEfective',
						id:'v_efective', 	
						name:'v_efective',
						store:efectiveList,
						value:'',
						width:200
					}]
			});
			
		});
		
		var datas={}
			//extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			//extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'set_efectivedate_nav.php',
			custlist:''
		}
		
	/* assign show list content **/
		
		//extendsJQuery.construct(navigation,'')
		//extendsJQuery.postContentList();
		
		
		
		
		var searchResult = function(){
			doJava.File = '../class/class.efective.date.php' 
			alert(doJava.dom('v_result').value);
			
		}
		var cancelResult=function(){
			doJava.File = '../class/class.efective.date.php' 
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var addResult = function(){
			doJava.File = '../class/class.efective.date.php' 
			$('#span_top_nav').load(doJava.File+'?action=tpl_add');
		}
		
		var showEfective = function(text){
			doJava.File = '../class/class.efective.date.php' 
			$('#span_top_nav').load(doJava.File+'?action=current_efective&date='+text);
		}
		
		var addEfective = function(){
			doJava.File = '../class/class.efective.date.php' 
			$('#span_top_nav').load(doJava.File+'?action=add_efective');
		}
		
		var saveCutOffDate = function(){
			doJava.File = '../class/class.efective.date.php' 
			var cut_off_date = doJava.dom('cut_off_date').value;
				if( cut_off_date!='')
				{
					doJava.Params = {
						action:'save_cut_off_date',
						cut_off_date: cut_off_date
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Success, Save Cut Off Date !");
							extendsJQuery.construct(navigation,'')	
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Save Cut Off Date !"); 
							return false; 
						}
				}
		}
/* *************************************** */
/* *************************************** */	
		
		var enableLastCall = function(){
			doJava.File = '../class/class.efective.date.php' 
			var inResultCheck = doJava.checkedValue('chk_lastcall');
				if( inResultCheck!=''){
					doJava.Params = {
						action:'enable_last_call',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Success, Enable Last Call !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Enable Last Call !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		
/* *************************************** */
/* *************************************** */
	
		var disableLastCall = function(){
			doJava.File = '../class/class.efective.date.php' 
			var inResultCheck = doJava.checkedValue('chk_lastcall');
				if( inResultCheck!=''){
					doJava.Params = {
						action:'disable_last_call',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Success, Disable Last Call !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Disable Last Call !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		
/* *************************************** */
/* *************************************** */
	
		var deleteResult = function(){
			doJava.File = '../class/class.efective.date.php' 
				var inResultCheck = doJava.checkedValue('chk_lastcall');
				if( inResultCheck!=''){
					doJava.Params = {
						action:'delete_last_call',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Success, Delete Last Call !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Last Call !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		
/* *************************************** */
/* *************************************** */

		var saveResult=function(){
			doJava.File = '../class/class.efective.date.php' 
			
			var last_call_start_date 		= doJava.dom('last_call_start_date').value;
			var last_call_end_date 			= doJava.dom('last_call_end_date').value; 
			var last_call_hour_start_time 	= doJava.dom('last_call_hour_start_time').value;
			var last_call_minute_start_time = doJava.dom('last_call_minute_start_time').value;
			var last_call_hour_end_time 	= doJava.dom('last_call_hour_end_time').value;
			var last_call_minute_end_time 	= doJava.dom('last_call_minute_end_time').value;
			var last_call_reason 			= doJava.dom('last_call_reason').value;
			var last_call_status			= doJava.dom('last_call_status').value;
			
			if( (last_call_start_date!='') 
				&& (last_call_end_date!='')
				&& (last_call_hour_start_time!='') 
				&& (last_call_minute_start_time!='')
				&& (last_call_hour_end_time!='')
				&& (last_call_minute_end_time!='')
				&& (last_call_reason!='')
				&& (last_call_status!='') )
			{
				doJava.Params = {
					action:'save_last_call',
					last_call_start_date:last_call_start_date,	
					last_call_end_date : last_call_end_date,
					last_call_hour_start_time: last_call_hour_start_time,
					last_call_minute_start_time: last_call_minute_start_time,
					last_call_hour_end_time: last_call_hour_end_time,
					last_call_minute_end_time: last_call_minute_end_time,
					last_call_reason: last_call_reason,
					last_call_status: last_call_status
				}
				
				if(confirm('Do you want to save this Last Call?'))
				{
					var error = doJava.Post();
						if( error ==1)
						{
							alert("Success, Save Last Call");
							extendsJQuery.postContent();	
						}
						else { alert("Failed, Save Last Call");}
				}
				else { return false; }	
				
			}else { alert('Input Not Complete!') }
		}
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;Cut Off Date </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<!--<div class="content_table"></div>
				<div id="pager"></div>-->
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	