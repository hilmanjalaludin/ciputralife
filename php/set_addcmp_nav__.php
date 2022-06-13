<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	/** 
	 * add master campaign 
	 * sifatnyan local doang 
	**/
	
	class NavCampaign extends mysql{
		
		function construct(){
			parent::__construct();
		}
		
		function getCoreCampaign(){
			$datas = array();
			if( $this->getSession('handling_type')==1 ) : 
				$sql = "select  
								a.CampaignGroupId as cmpGroup, a.CampaignGroupCode as cmpCode, 
								a.CampaignGroupName as cmpName, a.CampaignGroupStatusFlag as cmpStatus
						 from t_gn_campaigngroup a where a.CampaignGroupStatusFlag=1";
				
				$qry = $this -> execute($sql,__FILE__,__LINE__);
				while($row = $this -> fetchrow($qry)):
					$datas[] = $row;
				endwhile;
				
			endif;
			
			return $datas;
		}
		
		function getStatus(){
			$datas = array(0=>'Not Active',1=>'Active');
			return $datas;
		}  
		
	/** pay mode **/
	
		function getPayMode(){
			$datas=array();
			$sql = "select a.PayModeId, a.PayMode from t_lk_paymode a order by a.PayModeId ASC ";
			$qry = $this->execute($sql,__file__,__line__);
			while( $row = $this->fetchrow($qry)):
				$datas[$row->PayModeId] = $row->PayMode;
			endwhile;
			
			return $datas;
			
		}	
		
	}
	
	if(!is_object($aInit ) ): 
		$aInit = new NavCampaign();
	endif;	
	
	$datasCmp = $aInit -> getCoreCampaign();
	
?> 
	
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		// $(function(){
			// $('.corner').corner();
				
		// });
	/* start : globals init **/
		
		var html; 
		var prod_id; 
		var payMode;
		var cmp_core;
		var cmp_name;
		var cmp_status;
		var pDatas;
		
		var $__construct = function (){
			html		= '';
			prod_id  	= doJava.dom('text_product_id').value
			payMode  	= doJava.checkedValue('paymode');
			cmp_core  	= doJava.dom('select_cmp_core').value
			cmp_name  	= doJava.dom('text_product_name').value
			cmp_status 	= doJava.dom('status').value
		} 
		
		
		var getProduct = function(){
			pDatas = 'prod_id='+prod_id+'&payMode='+payMode
					+'&cmp_core='+cmp_core+'&prod_name='+cmp_name
					+'&cmp_status='+cmp_status;
					
			return pDatas;
		} 
	/* stop : globals init **/
	
		var resultPlan = function(int_cnt,object){
			$__construct();
			
			if( prod_id!='' && payMode!='' && cmp_core!='' && cmp_name!='' && cmp_status!='' ){
			
				if(isNaN(int_cnt)){
					doJava.dom(object).value='';
					doJava.dom(object).style.borderColor='1px solid red';
					return false;
				} 
				else{
					html += "<select id='select_plan_result' name='select_plan_result' style='width:120px;height:100px;' multiple='true'>";
					for (var i=1; i<=int_cnt; i++) {
						html+="<option value='"+i+"'>Plan "+i+"</option>";
					}	
					html += "</select>";
					doJava.dom('html_result_plan').innerHTML = html; 
				}
				if(int_cnt!='' || int_cnt!=0){
					var rows = doJava.dom('text_product_age').value;
					var cols = int_cnt;
						getLineAge(rows,cols)
				}
			}	
			
		}	
		
		var getLineAge = function(rows,cols){
			$__construct();
			
			if( (prod_id!='') && (payMode!='') && (cmp_core!='') && (cmp_name!='') && (cmp_status!='')){
				
				if( isNaN(cols) && isNaN(rows)){
					doJava.dom('html_div_agerange').innerHTML='';	
					return false;
				}
				else if( cols!='' && rows!=''){
					if( cols!=0 && rows!=0){
						doJava.destroy();
						
						doJava.File = '../class/class.product.core.php';
							doJava.Params = {
								action:'get_line_age',
								cols:cols,
								rows:rows,
								payMode:payMode
							}	
						doJava.Load('html_div_agerange');	
					}
					else
						doJava.dom('html_div_agerange').innerHTML='';	
				}
		   }
		}
		
		
		/* *******************************
		 * MATRIX
		/* *******************************/
		
			var max_periode =0;
			var max_plan =0;
			
/*event triger save **/
			
			var saveProduct = function(){
				var credit_shield_name = doJava.dom('credit_shield_name').value;
				if( credit_shield_name == 0 )
				{
					$__construct()
					var str_post = '';
					var el_o = document.getElementsByTagName('input');
					var el_f = el_o.length
					
					for(i in el_o){
						str_post+= "&"+el_o[i].name+"="+el_o[i].value
					}	
					
					str_post = str_post+'&'+getProduct();
					doJava.File = '../class/class.product.core.php';
					
						doJava.Params = {
							action:'save_age_plan',
							datas:str_post
						}
					
					doJava.dom('loadings_gambar').style.display="block";
					var error = doJava.Post();
					
					if( error ==1 ){
						
						alert('Success saving the product!');
						doJava.dom('loadings_gambar').style.display="none";
					}else{
						alert('Failed saving the product!');
						doJava.dom('loadings_gambar').style.display="none";
					}
				}
				else{
						$__construct();
						doJava.File = '../class/class.product.core.php';
						doJava.Params =
						{
							action:'save_credit_shield',
							credit_shield : credit_shield_name,
							prod_id : prod_id, payMode :  payMode,
							cmp_core : cmp_core, prod_name : cmp_name,
							cmp_status:  cmp_status 
						}	
						
						var error = doJava.eJson();
						if( error.result==1){
							alert('Success saving the product!');
						}
						else{
							alert('Failed saving the product!');
						}
			
				}	
			}	
	
 /* onkeyup iput age periode **/
	
			var replaceHTML= function (pos,opt,num){
			
				var inPayMode  	= doJava.checkedValue('paymode');
			
			/* main **/
			
				var age_main_max_anu = "ma_anu_max_"+num+"";
				var age_main_max_mon = "ma_mon_max_"+num+"";
				var age_main_min_anu = "ma_anu_min_"+num+"";
				var age_main_min_mon = "ma_mon_min_"+num+"";
				
			/* spouse */ 
			
				var age_spos_min_anu = "sp_anu_min_"+num+"";
				var age_spos_min_mon = "sp_mon_min_"+num+"";
				var age_spos_max_anu = "sp_anu_max_"+num+"";
				var age_spos_max_mon = "sp_mon_max_"+num+"";
				
				var i = 0;
				if(pos=='max'){ 
					for (i=0; i<inPayMode.length; i++){
						switch(inPayMode[i]){
							case '1':
								doJava.dom(age_main_max_anu).value=opt;
								doJava.dom(age_spos_max_anu).value=opt;
							break;

							case '2':	
								doJava.dom(age_main_max_mon).value=opt;
								doJava.dom(age_spos_max_mon).value=opt;
							break;
						}
					}
					
				}
				
				if(pos=='min'){ 
					for(i=0; i<inPayMode.length; i++){
						switch(inPayMode[i]){
							case '1':
								doJava.dom(age_main_min_anu).value=opt;
								doJava.dom(age_spos_min_anu).value=opt;
							break;

							case '2':	
								doJava.dom(age_main_min_mon).value=opt;
								doJava.dom(age_spos_min_mon).value=opt;
							break;
						}
					}
				}
			}
		
		
		var setProduct = function(options)
		{
			var tt = doJava.name('paymode');
			if( options.value==1)
			{
				doJava.dom('text_product_plan').disabled=true
				doJava.dom('text_product_plan').style.borderColor='#dddddd'
				doJava.dom('text_product_age').style.borderColor='#dddddd'
				doJava.dom('text_product_age').disabled=true
				for( i in tt ) tt[i].disabled=true;
				
			}
			else{
				doJava.dom('text_product_plan').disabled=false
				doJava.dom('text_product_age').disabled=false
				doJava.dom('text_product_plan').style.borderColor='red'
				doJava.dom('text_product_age').style.borderColor='red'
				for( i in tt ) tt[i].disabled=false;
			}
		}
		
	/* *********************************/
		
		doJava.onReady(
			evt = function(){
				
			},
			evt()
		);
		
	</script>

	<fieldset class="corner">
		<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Add Product</legend>
			<div id="cmp_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
				<table width="99%" border="0" align="center">
					<tr>
						<td valign="top">	
						
				<!-- start : left tpl -->	
				
					<div class="sub_main_content">
						<table cellpadding="5px" align="left">
							<tr style="display:none">
								<th class="content_th_top" style="color:red;font-weight:normal;"> Credit Shield</th>
								<td>
									<select id="credit_shield_name" name="credit_shield_name" onchange="setProduct(this);">
									<option> -- Choose -- </option>
									<option value="1"> YES </option>
									<option value="0"> NO</option>
									</select>
								</td>
							</tr>
							
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Product ID</th>
								<td> <input type="text" id="text_product_id" name="text_product_id" value="" maxlength> ( 30 )</td>
							</tr>
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Product Paymode</th>
								<td> 
									<?php 
									$paymode = $aInit -> getPayMode();
									foreach( $paymode as $value => $text): ?>	
										<input type="checkbox" id="paymode" name="paymode" value="<?php echo $value; ?>">&nbsp; <?php echo $text; ?>&nbsp;  
									<?php endforeach; ?>
								</td>
							</tr>
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Campaign Core</th>
								<td>
									<select name="select_cmp_core" id="select_cmp_core">
											<option value=""> -- Choose -- </option>
										<?php foreach($datasCmp as $value ): ?>
											<option value="<?php echo $value ->cmpGroup; ?>"><?php echo $value ->cmpName;  ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Product Plan </th>
								<td> <input type="text" onkeyup="resultPlan(this.value,this.name);" id="text_product_plan" name="text_product_plan" value="" style="width:85px;"> ( Plan ) </td>
							</tr>
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Age Period</th>
								<td> <input type="text" onkeyup="getLineAge(this.value,doJava.Value('text_product_plan'));" id="text_product_age" value="" name="text_product_age" style="width:85px;"> </td>
							</tr>
						</table>
					</div>
				<!-- stop : left tpl -->	
						</td>
						<td valign="top">
						
					<!-- start : right tpl -->	
					<div class="sub_main_content">
						<table cellpadding="5px" align="left">
							<tr>
								<th style="color:red;font-weight:normal;" colspan=2> &nbsp;</th>
							</tr>
							<tr>
								<th style="color:red;font-weight:normal;"> * Product Name</th>
								<td> <input type="text" id="text_product_name" name="text_product_name" value="" maxlength> ( 30 )</td>
							</tr>
							<tr>
								<th style="color:red;font-weight:normal;"> * Product Status</th>
								<td> 
									<select name="status" id="status">
									<?php foreach($aInit -> getStatus() as $key => $val) : ?>
										<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
									<?php endforeach; ?>
									</select>
									
								</td>
							</tr>
							
							<tr>
								<th style="display:none;"> Result Plan</th>
								<td> 
									<span id="html_result_plan" style="display:none;">
										<select id="select_plan_result" name="select_plan_result" multiple="true"> </select>
									</span>
								</td>
							</tr>
							<tr>
								<th></th>
								<td>
									
								</td>
							</tr>
							
						</table>
					</div>
					</td>	
					<tr>
						<!-- start:line content age ajax -->
							<td colspan="2" id="html_div_agerange">&nbsp;</td>
						<!-- stop:line content age ajax -->
					</tr>
				</table>
				<a href="javascript:void(0);" style="float:right;margin-right:50px;margin-bottom:20px;" class="sbutton" onclick="saveProduct();"><span>&nbsp;Save</span></a>	
			</div>
			
			
				
	</fieldset>