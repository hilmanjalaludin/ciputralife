<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select  
					b.CustomerNumber
				from t_gn_approvalhistory a
				left join t_gn_customer b on a.CustomerId=b.CustomerId
				left join t_lk_approvalitem c on a.ApprovalItemId=c.ApprovalItemId
				left join tms_agent d on a.CreatedById=d.UserId ";

	$where = " AND c.ApprovalItemId <>1 AND a.ApprovalApprovedFlag<>1 AND d.spv_id = '".$db -> getSession('UserId')."'";
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere($where);
	//$NavPages -> echo_query();
	
	/** user group **/
?>
	
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
	
	
		var datas={
			cbFilter:'<?php echo $db->escPost('cbFilter');?>'
		}
			extendsJQuery.totalPage = '<?php echo $NavPages ->getTotPages(); ?>';
			extendsJQuery.totalRecord = '<?php echo $NavPages ->getTotRows(); ?>';
		
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'mon_approvephone_nav.php',
			custlist:'mon_approvephone_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		doJava.File = '../class/class.approval.data.php' 
		
		var viewAppList = function(){
			datas = {
				cbFilter:doJava.dom('v_cmp').value
			}
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
		}
		var ApproveList =function(){
			var ListChk = doJava.checkedValue('chk_apprv');
			var ListLength = ListChk.split(',');
				if( ListChk!=''){
					if( ListLength.length <=1){
						doJava.Params= {
							action : 'approve_list_phone',
							rowsid : ListLength[0]	
						}
						var error = doJava.Post();
						if( error==1)
						{
							alert("Success approving data list!");
							extendsJQuery.postContent();
						}
						else{ alert("Failed approving data list!"); }
					}
					else{ alert('Please select a row!'); }
				}
				else{ alert('Please select a row!'); }
		}	
		
	
		
	
	/* load jquery **/
	
		$(function(){
			// $('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Approve'],['Search']],
				extMenu  :[['ApproveList'],['viewAppList']],
				extIcon  :[['accept.png'],['zoom.png']],
				extText  :true,
				extInput :true,
				extOption:[{
						render:1,
						type:'text',
						id:'v_cmp', 	
						name:'v_cmp',
						value:'<?php echo $db->escPost('cbFilter');?>',
						width:200
					}]
			});
		});
		
	
			
	
	</script>
	
	<div id="toolbars"></div>
	<div class="content_table"></div>
	<div id="pager"></div>
	<div id="dialogCmp"></div>
	
	
	