<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	function constructDate(){
		$date  = explode("-",$_REQUEST['filter_close']);
		$year  = substr($date[2],2,2);
		$month =  $date[1];
		$days  =  $date[0];
		$string = $days.$month.$year;
		if( $string!='') return $string;
	}
	
	$sql = " select  a.RecDate, a.RecFileName, a.RecStatusDownload,a.RecSumaryFile, a.RecUserDownload from t_gn_recording a ";
					
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
    $filter ='';
	$my_dates = constructDate();
	if($db->havepost('filter_close')) $filter = " and a.RecFileName REGEXP('".$my_dates."') ";
	
	//$filter = " and a.ScriptFlagStatus=1 ";
	
	$NavPages -> setWhere($filter);
	$NavPages -> OrderBy("date(a.RecDate)","DESC");
	
	/** user group **/
	
	
?>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/upload.js"></script>
  	
	<script type="text/javascript">
		
		$(function(){
		
			// $('.corner').corner();
			// $('#toolbars').corner();
			
			
			
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Download'],['Filter Closing'],['Re Generate'],['']],
				extMenu  :[['DownloadRecording'],['filterClose'],['reGenerate'],['']],
				extIcon  :[['disk.png'],['zoom.png'],['cog_add.png'],['']],
				extText  :true,
				extInput :true,
				extOption: [{
						render:1,
						type:'text',
						id:'filter_close', 	
						name:'filter_close',
						value:'<?php echo $db->escPost('filter_close');?>',
						width:200
					}]
			});
			
			$('#filter_close').datepicker({dateFormat:'dd-mm-yy'});
			
		});
		
		var datas={
			filter_close:'<?php echo $db->escPost('filter_close');?>'
		}
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'mon_dwnrecording_nav.php',
			custlist:'mon_dwnrecording_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
		doJava.File = '../class/class.download.recording.php' 
		
		var reGenerate = function(){
			var filter_close = doJava.dom('filter_close').value;
			doJava.dom('loading-text').innerHTML="<span style='z-index:999;color:red;'><img src='../gambar/loading.gif'> Please Wait..</span>";
			doJava.Params ={
				action:'regenerate',
				filter_close:filter_close
			}
			var error = doJava.Post();
			doJava.dom('loading-text').innerHTML=""
			extendsJQuery.postContent();		
		}
		
		var DownloadRecording = function()
		{
			var rec_chk = doJava.checkedValue('chk_rec');
			var rec_no = rec_chk.split(',');
			
			if( rec_chk =='') { alert('Please select rows !'); return false; }
			else if( rec_no.length>1){ alert('Please select one rows!'); return false; }
			else
			{
				doJava.File = '../class/class.download.recording.php' 
				doJava.Params={
					action : 'get_file_recording',
					onrowsid : rec_chk
				}
				window.open(doJava.File+'?'+doJava.ArrVal());
				extendsJQuery.postContent();
			}
		}
		
		var filterClose = function(){
			var filter_close = doJava.dom('filter_close').value;
			//alert(filter_close);
			datas={ filter_close:filter_close }
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
			
		}
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;Download Recording </legend>	
				<div id="toolbars"></div>
				<div id="loading-text"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	