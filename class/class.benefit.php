<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	

	/*
	 *	class untuk action product
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	
	
	class Benefit extends mysql{
		var $action;
		
		function __construct(){
			parent::__construct();
			$this->action = $this->escPost('action');
			
		}
		
	/* function set style css **/
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
					.input_text {border:1px solid #dddddd;width:160px;font-size:12px;height:20px;background-color:#fffccc;}
					.text_header { text-align:right;color:red;}
					.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
					.textarea { font-family:Arial;color:blue;height:100px;border:1px solid #dddddd;width:250px;font-size:12px;background-color:#fffccc; }
				</style>
				
			<!-- stop: css -->
		<?php }
		
		
		function initClass(){
			if( $this->havepost('action'))
			{
				switch( $this->action)
				{
					case 'tpl_add'  			: $this -> tplBenefitAdd();    		break;
					case 'tpl_edit' 			: $this -> tplBenefitEdit();   		break;
					case 'tpl_delete'			: $this -> tplBenefitRemove(); 		break;
					case 'get_cb_product_plan'  : $this -> getBenefitPlan(); 		break;
					case 'save_benefit' 		: $this -> saveBenefitProduct(); 	break;
					case 'update_benefit' 		: $this -> updateBenefitProduct(); 	break;
					case 'delete_benefit' 		: $this -> DeleteBenefitProduct(); 	break;
				}
			}
		}
		
	/** delete product benefit ***/
		
		function DeleteBenefitProduct()
		{
			$BenfitId = EXPLODE(",", $this -> escPost('benefit_id'));
			
			$totals = 0;
			foreach( $BenfitId as $k => $BenefitNameId )
			{
				$sql = "DELETE FROM t_gn_productplanbenefit WHERE ProductPlanBenefitId='$BenefitNameId'";
				
				if( $this -> execute($sql,__FILE__,__LINE__) )
				{
					$totals++;
				}
			}
			
			if( $totals > 0 )
				echo json_encode(array('result'=>1));
			else
				echo json_encode(array('result'=>1));
		}
		
	/** funtion get Benfit edit interface **/
	
		private function getBenfitEdit()
		{
			$sql = " select * from t_gn_productplanbenefit a where a.ProductPlanBenefitId=".$this -> escPost('rowsid')."";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$rows = $this -> fetchrow($qry);
			return $rows;
		}
	/** update **/
		
		private function updateBenefitProduct()
		{
			$valueArray = array(
				'ProductId' => $this -> escPost('benefit_item_product'), 
				'ProductPlan' => $this -> escPost('benefit_item_plan'), 
				'ProductPlanBenefit' => $this -> escPost('benefit_item_value'), 
				'ProductPlanBenefitDesc' => $this -> escPost('benefit_item_desc'), 
				'ProductPlanBenefitStatusFlag' => $this -> escPost('benefit_status')
			);
			
			$where['ProductPlanBenefitId'] = $_REQUEST['rowsid'];
				
			if( $this -> havepost('benefit_item_product')):
				$query = $this -> set_mysql_update("t_gn_productplanbenefit",$valueArray,$where);
				
					if( $query ) : echo 1;
					else : 
						echo 0; 
					endif;
			endif;	
		}
		
	/** save benfit **/
		private function saveBenefitProduct()
		{
			$valueArray = array
			(
				'ProductId' => $this -> escPost('benefit_item_product'), 
				'ProductPlan' => $this -> escPost('benefit_item_plan'), 
				'ProductPlanBenefit' => $this -> escPost('benefit_item_value'), 
				'ProductPlanBenefitDesc' => $this -> escPost('benefit_item_desc'), 
				'ProductPlanBenefitStatusFlag' => $this -> escPost('benefit_status')
			);
			
			if( $this -> havepost('benefit_item_product'))
			{
				$query = $this -> set_mysql_insert("t_gn_productplanbenefit",$valueArray);
				if( $query ) : echo 1;
				else : 
					echo 0; 
				endif;
			}
		
		}
		
	/** function getCategory **/	
	
		private function getProductId($opt='')
		{ 
			$sql = " select a.ProductId,  a.ProductCode, a.ProductName from t_gn_product a
					 where a.ProductStatusFlag=1";
			
		?>
			<select class="select" name="cb_benefit_product_id" id="cb_benefit_product_id" style="width:auto;" onchange="getPlanByProduct(this.value);">
				<option value="">-- Choose --</option>
				<?php
				
					$qry = $this->execute($sql,__FILE__,__LINE__);
					while( $row = $this ->fetchrow($qry) ){
						if( $opt==$row->ProductId){
							echo "<option value=\"{$row->ProductId}\" selected>{$row->ProductCode} - {$row->ProductName}</option>";
						}
						else{
							echo "<option value=\"{$row->ProductId}\">{$row->ProductCode} - {$row->ProductName}</option>";
						}
					}
				?>
			</select>
		<?php }		

	/** function getCategory **/	
	
		private function getBenefitPlan($opt='',$plan){ ?>
			<select class="select" name="cb_benefit_plan" id="cb_benefit_plan">
				<option value="">-- Choose --</option>
				<?php
					$sql ="select a.ProductPlan, a.ProductPlanName from t_gn_productplan a
							where a.ProductId='".($opt!=''?$opt:$this -> escPost('productid'))."'
							group by a.ProductPlanName";
						
					$qry = $this->execute($sql,__FILE__,__LINE__);
					while( $row = $this ->fetchrow($qry) ){
						if( $plan==$row->ProductPlan){
							echo "<option value=\"{$row->ProductPlan}\" selected>{$row->ProductPlanName}</option>";
						}
						else{	
							echo "<option value=\"{$row->ProductPlan}\">{$row->ProductPlanName}</option>";
						}	
					
					}	
					
				?>
			</select>
		<?php }			
	
	/** function getCategory **/	
	
		private function getCategory($str=''){ ?>
			<select class="select">
				<option value="">-- Choose --</option>
			</select>
		<?php }	

	/** function getCategory **/	
	
		private function getActivated($active=''){ ?>
			<select class="select" id="benefit_status" name="benefit_status">
				<option value="">-- Choose --</option>
				<?php if($active==1){ ?>	
					<option value="0">Not Active</option>
					<option value="1" selected>Active</option>
				<?php } else if($active==0 && $active!='') { ?>
					<option value="0" selected>Not Active</option>
					<option value="1">Active</option>
				<?php } else { ?>
					<option value="0">Not Active</option>
					<option value="1">Active</option>
				<?php } ?>
			</select>
		<?php }			
		
		
		function tplBenefitAdd(){ 
			$this->setCss(); 
			?>
			<div id="result_content_add" class="box-shadow" style="margin-top:10px;">
				<h3 class="box-shadow h3"> Add  Benefit Product</h3>
				<table cellpadding="6px;">
					
					<tr>
						<td class="text_header">* Product ID</td>
						<td><?php $this -> getProductId(); ?></td>
					</tr>
					
					<tr>
						<td class="text_header">* Benefit Description </td>
						<td>
							<textarea name="benefit_description" id="benefit_description" class="textarea"></textarea>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Plan</td>
						<td id="div_product_plan">
							<select class="select" name="cb_benefit_plan" id="cb_benefit_plan">
								<option value=""> -- Choose --</option>
							</select>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Benefit Product</td>
						<td>
							<textarea name="benefit_product" id="benefit_product" class="textarea" style="height:60px;"></textarea>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Result Active</td>
						<td><?php $this -> getActivated(); ?></td>
					</tr>
					
					<tr>
						<td class="text_header">&nbsp;</td>
						<td><a href="javascript:void(0);" class="sbutton" onclick="saveBenefit();"><span>&nbsp;Save </span></a></td>
					</tr>
				</table>
			</div>
		<?php
		}
		
		function tplBenefitEdit(){ 
		
			$this->setCss();
			$getValue = $this -> getBenfitEdit();
		
		?>
		<div id="result_content_edit" class="box-shadow" style="margin-top:10px;">
			<input type="hidden" name="rows_id" id="rows_id" value="<?php echo $_REQUEST['rowsid']; ?>"
			<h3 class="box-shadow h3"> Edit  Benefit Product</h3>
				<table cellpadding="6px;">
					
					<tr>
						<td class="text_header">* Product ID</td>
						<td><?php $this -> getProductId($getValue->ProductId); ?></td>
					</tr>
					
					<tr>
						<td class="text_header">* Benefit Description </td>
						<td>
							<textarea name="benefit_description" id="benefit_description" class="textarea"><?php echo $getValue->ProductPlanBenefitDesc; ?></textarea>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Plan</td>
						<td id="div_product_plan">
							<?php $this -> getBenefitPlan($getValue->ProductId, $getValue->ProductPlan); ?>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Benefit Product</td>
						<td>
							<textarea name="benefit_product" id="benefit_product" class="textarea" style="height:60px;"><?php echo $getValue->ProductPlanBenefit; ?></textarea>
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Result Active</td>
						<td><?php $this -> getActivated($getValue->ProductPlanBenefitStatusFlag); ?></td>
					</tr>
					
					<tr>
						<td class="text_header">&nbsp;</td>
						<td><a href="javascript:void(0);" class="sbutton" onclick="updateBenfit();"><span>&nbsp;Save </span></a></td>
					</tr>
				</table>
			</div>
		<?php
		}
		
		function tplResultRemove(){ ?>
			<div id="result_content_delete" class="box-shadow" style="margin-top:10px;">
				
			</div>
		<?php
		}
	}
	
	$Benefit = new Benefit();
	$Benefit -> initClass();