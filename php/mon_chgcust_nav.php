<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select  
					b.CustomerNumber,
				from t_gn_approvalhistory a
				left join t_gn_customer b on a.CustomerId=b.CustomerId
				left join t_lk_approvalitem c on a.ApprovalItemId=c.ApprovalItemId
				left join tms_agent d on a.CreatedById=d.UserId ";

	$where = " AND c.ApprovalItemId = 1 AND a.ApprovalApprovedFlag<>1 ";
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere($where);
	
	
	/** user group **/
?>
	
	<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app -> basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
	
		
	var V_IMAGE_URL = "<?php echo trim($app->basePath());?>gambar/icon";
		
	var datas={
		cbFilter:'<?php echo $db->escPost('cbFilter');?>'
	}
		extendsJQuery.totalPage = '<?php echo $NavPages ->getTotPages(); ?>';
		extendsJQuery.totalRecord = '<?php echo $NavPages ->getTotRows(); ?>';
		
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'mon_chgcust_nav.php',
			custlist:'mon_chgcust_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		doJava.File = '../class/class.change.customer.php' 
		
	/* ((((((((((((((((((((((y))))))))))))))))))))))))))) */	
	
		var viewList = function(){
			datas = {
				cbFilter:doJava.dom('v_cmp').value
			}
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent();
		}
	
	/* ((((((((((((((((((((((y))))))))))))))))))))))))))) */	
	
		var UpdateName=function(){
			var cust_from_name 	= doJava.dom('cust_from_name').value;
			var cust_to_name 	= doJava.dom('cust_to_name').value;
			var cb_aggreement   = doJava.dom('cb_aggreement').value;
			var rowsid			= doJava.dom('rowsid').value;
			
			if( (cust_from_name!='') && (cust_to_name!='') && (cb_aggreement!=''))
			{
				doJava.Params = {
					action : 'save_cust_name',
					cust_from_name : cust_from_name, 
					cust_to_name : cust_to_name,
					cb_aggreement : cb_aggreement,
					rowsid : rowsid
				}

				doJava.MsgBox();
					var error = doJava.Post();
					alert(error)
					if(error==1)
					{
						alert('Success saving customer name changes!'); return;	
					}
			}
			else
				alert('Input is not complete!');
		}
		
	/* ((((((((((((((((((((((y))))))))))))))))))))))))))) */	
		
		var changeName =function(){
			var ListChk = doJava.checkedValue('chk_name');
			var ListLength = ListChk.split(',');
				if( ListChk!=''){
					if( ListLength.length <=1){
						doJava.Params= {
							action : 'get_tpl_custname',
							rowsid : ListLength[0]	
						}
						doJava.Load('top_panel');
					}
					else{ alert('Please select a row!'); }
				}
				else{ alert('Please select a row!'); }
		}	
		
	/* ((((((((((((((((((((((y))))))))))))))))))))))))))) */	
	
		var exitName = function(){
			$(function(){
				$('#top_panel').load(doJava.File+'?action=empty');
			});
		}
		
	
		
	
	/* load jquery **/
	
		$(function(){
			$('.corner').corner();
			$('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl   : V_IMAGE_URL,
				extTitle : [['Change Name '],['Close'],['Search']],
				extMenu  : [['changeName'],['exitName'],['viewList']],
				extIcon  : [['application_edit.png'],['cancel.png'],['zoom.png']],
				extText  : true,
				extInput : true,
				extOption: [{
						render:2,
						type:'text',
						id:'v_cust_name', 	
						name:'v_cust_name',
						value:'<?php echo $db->escPost('cbFilter');?>',
						width:200
					}]
			});
		});
		
	
			
	
	</script>
	<fieldset class="corner">
		<legend class="icon-customers">&nbsp;&nbsp;Change Customer  </legend>	
		
		<div id="toolbars"></div>
		<div id="top_panel"></div>
		<div class="content_table"></div>
		<div id="pager"></div>
	</fieldset>
	
	