<?php

require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
require("../class/class.nav.table.php");
require("../class/class.application.php");
require('../sisipan/parameters.php');
require('../class/lib.form.php');	
	
	$sql = "select * from t_gn_ftp_customers a ";
	
	
	$filter = "";
	
	if( $db -> havepost('work_branch') ) $filter .= " AND a.CustomerZipCode IN ('".IMPLODE("','",EXPLODE(',',$_REQUEST['work_branch']))."')";
	if( $db -> havepost('city') ) 		 $filter .= " AND a.CustomerCity LIKE '%".$_REQUEST['city']."%'";
	if( $db -> havepost('card_type') )	 $filter .= " AND a.CustomerCardType LIKE '%".$_REQUEST['card_type']."%'";
	if( $db -> havepost('start_date') )  $filter .= " AND date(a.CustomerUploadedTs)>='".$db-> Date -> english($_REQUEST['start_date'],'-')."' 
													     AND date(a.CustomerUploadedTs)<='".$db-> Date -> english($_REQUEST['end_date'],'-')."'";
													  
	
	$NavPages -> setPage(25);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere($filter); 
	//echo $NavPages ->query;
	
/* get branch is active ***/
	
	function getBranchCode()
	{
		global $db;
		
		$datas = array();
		$sql = " select a.BranchCode, a.BranchName from t_lk_branch a where a.BranchFlags=1 ";
		$qry = $db -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['BranchCode']] = $rows['BranchCode']." - ".$rows['BranchName'];
		}
		
		return $datas;
	}	
?>
<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app -> basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app -> basePath();?>js/javaclass.js"></script>
<script type="text/javascript"> 

var getListCampaign = function()
		{
			doJava.File ="../class/class.bucket.ftp.php";
			doJava.Params = {
				action:'get_campaign'
			}
			return doJava.eJson();	
		} 


	
$(function(){
	$('#toolbars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Back'],['Find'],[],['Process']],
		extMenu  :[['backtohome'],['FindBucketFTP'],[],['Process']],
		extIcon  :[['house.png'],['find.png'],[],['drive_disk.png']],
		extText  :true,
		extInput :true,
		extOption:[{
						render	: 2,
						header	: 'Campaign Name ',
						type	: 'combo',
						id		: 'combo_filter_campaign', 	
						name	: 'combo_filter_campaign',
						value	: '',
						store	: [getListCampaign()],
						triger	: '',
						width	: 200
					}]
			});
			
	$('#start_date,#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy'});	
});

/* ******************** */

var Process = function()
{
	var ftp_list_id = doJava.checkedValue('ftp_list_id');
	var result_ftp_id = ftp_list_id.split(',');
	var campaign_id = doJava.dom('combo_filter_campaign').value;
	if( ftp_list_id =='' ) { alert('Please select a rows !'); return false; }
	else if( result_ftp_id.length<1 ){ alert('Please select a rows !'); return false; }
	else if( campaign_id=='') { alert('Please select a campaign !'); return false; }
	else{
		doJava.File ="../class/class.bucket.ftp.php";
		doJava.Params = {
			action : 'save_to_campaign',
			ftp_list_id : ftp_list_id,
			campaign_id : campaign_id
		}
		
		var error = doJava.eJson();
		if( error.result )
		{
			alert("Succes, Setup data to campaign with : \nSuccess Data("+error.totals_success+") ...\nDuplicate Data ("+error.totals_duplicate+")");
			if( confirm('Do you want to back campaign setup ?'))
			{
				$('#main_content').load('set_cmpupload_nav.php');
			}
		}
		else{
		
		}
		
	}
}
			
var backtohome = function(){
	if( confirm('Do you want to back campaign setup ?'))
	{
		$('#main_content').load('set_cmpupload_nav.php');
	}
	else
		return false;
}
	
var datas=
{ 
		order_by 	: '<?php echo $db->escPost('order_by');?>',
		type	 	: '<?php echo $db->escPost('type');?>',
		work_branch : '<?php echo $db->escPost('work_branch');?>',
		city 		: '<?php echo $db->escPost('city');?>',
		card_type 	: '<?php echo $db->escPost('card_type');?>',
		start_date 	: '<?php echo $db->escPost('start_date');?>',
		end_date 	: '<?php echo $db->escPost('end_date');?>'
	}
		
	extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
	extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
var navigation = {
			custnav:'act_ftp_bucket_nav.php',
			custlist:'act_ftp_bucket_list.php'
		}
		
	/* assign show list content **/
		
	extendsJQuery.construct(navigation,datas)
	extendsJQuery.postContentList();
		
		
var FindBucketFTP = function()
{
	var work_branch = doJava.checkedValue('work_branch');
	var city = doJava.dom('city').value;
	var card_type = doJava.dom('card_type').value;
	var start_date = doJava.dom('start_date').value;
	var end_date = doJava.dom('end_date').value;
	
	var datas = {
		work_branch : work_branch,
		city : city,
		card_type : card_type,
		start_date : start_date,
		end_date : end_date	
	}
	extendsJQuery.construct(navigation,datas)
	extendsJQuery.postContent();
	
}		
			
</script>	
<!-- start: css -->
	<style>
		.select { border:1px solid #bbbbbb;width:160px;font-size:11px;height:22px;background-image:url('../gambar/input_bg.png');}
		.input_text {border:1px solid #bbbbbb;width:250px;font-size:12px;height:18px;background-image:url('../gambar/input_bg.png');}
		.input_box {border:1px solid #bbbbbb;width:70px;font-size:12px;height:18px;background-image:url('../gambar/input_bg.png');}
		
		.text_header { text-align:right;color:#236777;font-weight:bold;}
		.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
		.textarea { font-family:Arial;color:blue;height:100px;border:1px solid #dddddd;width:250px;font-size:12px;background-color:#fffccc; }
	</style>
<!-- stop: css -->
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-campaign">&nbsp;&nbsp;FTP Bucket </legend>	
				<div id="span_top_nav" style="border:1px solid #ddd;margin-bottom:8px;margin-right:5px;margin-left:5px;">
				<table style="margin:3px;" cellpadding="8px;" border=0>
					<tr>
						<td style="font-family:Arial;font-size:12px;font-weight:bold;color:#000BBB;" valign="top" rowspan=2> Work Branch </td>
						<td rowspan=2 valign="top">:</td>
						<td rowspan=2 valign="top"><?php $jpForm -> jpListcombo('work_branch','Select Branch',getBranchCode(),EXPLODE(',',$_REQUEST['work_branch'])); ?></td>
						<td style="font-family:Arial;font-size:12px;font-weight:bold;color:#000BBB;" valign="top">City</td>
						<td valign="top">:</td>
						<td valign="top"><?php $jpForm -> jpInput('city','input_text',$db -> escPost('city'));?></td>
					</tr>	
					<tr>
						<td style="font-family:Arial;font-size:12px;font-weight:bold;color:#000BBB;">Upload Date</td>
						<td>:</td>
						<td>
							<?php $jpForm -> jpInput('start_date','input_box',$db -> escPost('start_date'));?> &nbsp; - &nbsp; 
							<?php $jpForm -> jpInput('end_date','input_box',$db -> escPost('end_date'));?> </td>
					</tr>
					
					<tr>
						<td style="font-family:Arial;font-size:12px;font-weight:bold;color:#000BBB;">Card Type</td>
						<td>:</td>
						<td> <?php $jpForm -> jpInput('card_type','input_text',$db -> escPost('card_type'));?></td>
						<td style="font-family:Arial;font-size:12px;font-weight:bold;color:#000BBB;"></td>
						<td></td>
						<td> </td>
					</tr>
				</table>
				</div>
				<div id="toolbars"></div>
					<div class="box-shadow" style="background-color:#FFFFFF;margin-top:10px;">	
						<div class="content_table"></div>
						<div id="pager"></div>
						<div id="ViewCmp"></div>
					</div>	
		</fieldset>	