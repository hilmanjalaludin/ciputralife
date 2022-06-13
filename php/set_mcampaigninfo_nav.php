<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select count(a.CustomerId), 
				SUM( if(b.AssignMgr is not null and b.AssignSpv is not null ,1,0)) as AssignToSpv,
				SUM( if(b.AssignMgr is not null and b.AssignSpv is null ,1,0)) as NotAssignToSpv,
				SUM( if(b.AssignMgr is not null and b.AssignSpv is not null and b.AssignSelerId is not null ,1,0)) as AssignToAgent,
				SUM( if(b.AssignMgr is not null and b.AssignSpv is not null and b.AssignSelerId is null ,1,0)) as NotAssignToAgent,
				SUM( IF(b.AssignSpv is not null and b.AssignSelerId is null,1,0) ) as NotAssignBySpv,
				a.CampaignId, c.CampaignName
			from t_gn_customer a inner join t_gn_assignment b on a.CustomerId=b.CustomerId
			left join t_gn_campaign c on a.CampaignId=c.CampaignId ";
			
					
	$NavPages -> setPage(10);
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	$NavPages -> GroupBy("a.CampaignId");
	
	
	/** user group **/
	
	
?>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/javaclass.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/upload.js"></script>
	<script type="text/javascript">
 /* get toolbar and pagging **/
 
	    $(function(){
			//$('.corner').corner();
			//$('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Calculation Data'],['Clear']],
				extMenu  :[['calculation'],['clearcontent']],
				extIcon  :[['zoom.png'],['cancel.png']],
				extText  :true,
				extInput :true,
				extOption:[{
						render:3,
						type:'text',
						id:'v_cmp_upload', 	
						name:'v_cmp_upload',
						value:'',
						width:200
					}]
			});	
	    });
		
	var datas={}
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	/* assign navigation filter **/
		
	var navigation = {
			custnav:'set_mcampaigninfo_nav.php',
			custlist:'set_mcampaigninfo_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		
		var clearcontent = function(){
			doJava.File = '../class/class.mcampaign.info.php' 
			doJava.Params = { action:'clear'}
			doJava.Load("span_top_nav");
		}
		
		
		var calculation =function(){
			doJava.File = '../class/class.mcampaign.info.php' 
			var CampaignNumber = doJava.checkedValue('check_list_cmp');
			var CampaignArray  = CampaignNumber.split(',');
			
			if( CampaignNumber!='')
			{
				if( CampaignArray.length==1)
				{
					doJava.Params = {
						action:'calculation',
						campaign_number:CampaignArray[0]
					}
					doJava.Load("span_top_nav");
				}
				else
					{
						alert('Please Select one rows!');
						return false;
					}
			}
			else {
				alert("Please Select Rows");
				return;
			}	
		}
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-campaign">&nbsp;&nbsp;Campaign Information </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
					<div class="box-shadow" style="background-color:#FFFFFF;margin-top:10px;">	
						<div class="content_table"></div>
						<div id="pager"></div>
						<div id="ViewCmp"></div>
					</div>	
		</fieldset>	
		
	<!-- stop : content -->
	
	
	