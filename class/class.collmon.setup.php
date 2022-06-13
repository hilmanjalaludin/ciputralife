<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/lib.form.php");
	
	class CallResult extends mysql{
		var $action;
		
		function __construct(){
			parent::__construct();
			$this -> action = $this->escPost('action');
			$this -> form  = new jpForm();
		
		}
		
		function setCss(){?>
			
			<!-- start: css -->
				<style>
					.select { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
					.input_text {border:1px solid #dddddd;width:160px;font-size:12px;height:20px;background-color:#fffccc;}
					.input_text_score {border:1px solid #dddddd;width:60px;font-size:12px;height:20px;background-color:#fffccc;}
					.text_header { text-align:right;color:red;}
					.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
				</style>
				
			<!-- stop: css -->
		<?php }
		
		function initClass(){
			if( $this->havepost('action'))
			{
				switch( $this->action)
				{
					case 'tpl_add_collmon'  : $this->tplAddCollmon();    break;
					case 'tpl_edit_collmon' : $this->tplEditCollmon();    break;
					case 'save_collmon'  	: $this->saveCollmon();    break;
					case 'disable_collmon'  : $this->DisableCollmon();    break;
					case 'enable_collmon'  	: $this->EnableCollmon();    break;
					case 'update_collmon'  	: $this->UpdateCollmon();    break;
					case 'delete_collmon'  	: $this->DeleteCollmon();    break;
					
					
				}
			}
		}
		function EnableCollmon()
		{
			$resultid = explode(",",$_REQUEST['resultid']);
			$total = 0;
			foreach( $resultid as $k => $v )
			{
				$datas = array('SubCategoryFlags'=>1);
				$where = array('SubCategoryId'=>$v);
				$result = $this->set_mysql_update("coll_subcategory_collmon", $datas,$where);
				if( $result ) $total++;
				/*
				if( $this -> execute(" UPDATE t_lk_callreasoncategory a SET a.CallReasonCategoryFlags=1 
									   WHERE a.CallReasonCategoryId='$v'",__FILE__,__LINE__) )
				{
					$total++;
				}*/
			}
			
			if( $total> 0 ) 
				echo json_encode(array('result'=>1));
			else
				echo json_encode(array('result'=>0));
		}
		
	/* disable **/
	
		function DisableCollmon()
		{
			$resultid = explode(",",$_REQUEST['resultid']);
			$total = 0;
			foreach( $resultid as $k => $v )
			{
				$datas = array('SubCategoryFlags'=>0);
				$where = array('SubCategoryId'=>$v);
				$result = $this->set_mysql_update("coll_subcategory_collmon", $datas,$where);
				if( $result ) $total++;
				/*
				if( $this -> execute(" UPDATE t_lk_callreasoncategory a SET a.CallReasonCategoryFlags=1 
									   WHERE a.CallReasonCategoryId='$v'",__FILE__,__LINE__) )
				{
					$total++;
				}*/
			}
			
			if( $total> 0 ) 
				echo json_encode(array('result'=>1));
			else
				echo json_encode(array('result'=>0));
		}
		
		function getCategory()
		{
			$sql ="select * from coll_category_collmon a";
			$qry = $this -> query($sql);
			foreach( $qry -> result_rows() as $rows )
			{
				$datas[$rows[0]] = $rows[1];	
			}	
			return $datas;
		}
		
		function getCategory2(){?>
			<select class="select" id="result_head_level" name="result_head_level" onchange="javascript:doJava.dom('result_level').value=this.value;">
				<option value="">-- Choose --</option>
				<?php
					$sql = "select a.CallReasonLevel as idKey , a.CallReasonLevel 
							from t_lk_callreason a group by a.CallReasonLevel";
					$qry = $this -> execute($sql,__FILE__,__LINE__);
						
						while( $row = $this -> fetcharray($qry)){
							if( $option==$row[0]){
								echo "<option value=\"{$row[0]}\" selected>{$row[1]}</option>";
							}
							else{
								echo "<option value=\"{$row[0]}\">{$row[1]}</option>";
							}
						}	
				
				?>
			</select>
		<?php }
		
		function saveCollmon()
		{
			
			$sql = array(
					'CategoryId' 			=> $this -> escPost('category_collmon'),
					'SubCategoryParents'  	=> $this -> escPost('category_collmon'), 
					'SubCategory'  			=> $this -> escPost('sub_category'), 
					'SubCategoryDesc' 		=> $this -> escPost('sub_category'),
					'StartNumber'			=> $this->  escPost('min_number'),
					'EndNumber'				=> $this->  escPost('max_number'),
					'StepNumber'  			=> $this->  escPost('step_number'), 
					'SubCategoryFlags'  	=> 1 
					);
			
			$query = $this -> set_mysql_insert('coll_subcategory_collmon',$sql);
			if( $query ) : echo 1;
			else :
				echo 0;
			endif;
		}
		
		function tplAddCollmon()
		{
			$this->setCss();
			?>
			<div id="result_collmon_add" class="box-shadow" style="margin-top:10px;">
				<h3 class="box-shadow h3"> Add Collmon Setup </h3>
				<table cellpadding="6px;">
					<tr>
						<td class="text_header">* Call Category</td>
						<td  colspan="3">
							<?php $this->form->jpCombo('category_collmon','select',$this->getCategory());?>
						</td>
					</tr>
					<tr>
						<td class="text_header">* Call Sub Category</td>
						<td  colspan="3">
							<input type="text" name="sub_category" id="sub_category"  class="input_text" style="width:250px;height:18px;">
						</td>
					</tr>
					<tr>
						<td class="text_header">* Interval Score</td>
						<td>
							<input type="text" name="min_number" id="min_number"  class="input_text_score" style="width:50px;height:18px;">
							-
							<input type="text" name="max_number" id="max_number"  class="input_text_score" style="width:50px;height:18px;">
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Step Number</td>
						<td>
							<input type="text" name="step_number" id="step_number"  class="input_text" style="width:50px;height:18px;">
						</td>
					</tr>
					<tr>
						<td class="text_header">&nbsp;</td>
						<td><a href="javascript:void(0);" class="sbutton" onclick="saveCollmonSetup();"><span>&nbsp;Save</span></a></td>
					</tr>
				</table>
			</div>
			<?php
		}
		
		function getCollmonById(){
			$sql = " select * from coll_subcategory_collmon a where a.SubCategoryId='".$this->escPost('collmonid')."'";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			$datas = $this -> fetchrow($qry);
			//print_r($datas);
			if( $datas!=''):
				return $datas;
			endif;	
		}
		
		function tplEditCollmon()
		{
			$id = $this->getCollmonById();
			//print_r($id);
			$this->setCss();
			?>
			<input type="hidden" name="collmonid" id="collmonid" value="<?php echo $this->escPost('collmonid'); ?>">
			<div id="result_collmon_edit" class="box-shadow" style="margin-top:10px;">
				<h3 class="box-shadow h3"> Edit Collmon Setup </h3>
				<table cellpadding="6px;">
					<tr>
						<td class="text_header">* Call Category</td>
						<td  colspan="3">
							<?php $this->form->jpCombo('category_collmon','select',$this->getCategory(),$id->CategoryId);?>
						</td>
					</tr>
					<tr>
						<td class="text_header">* Call Sub Category</td>
						<td  colspan="3">
							<input type="text" name="sub_category" id="sub_category" value="<?php echo $id->SubCategory; ?>"  class="input_text" style="width:250px;height:18px;">
						</td>
					</tr>
					<tr>
						<td class="text_header">* Interval Score</td>
						<td>
							<input type="text" name="min_number" id="min_number" value="<?php echo $id->StartNumber ?>"  class="input_text_score" style="width:50px;height:18px;">
							-
							<input type="text" name="max_number" id="max_number" value="<?php echo $id->EndNumber ?>" class="input_text_score" style="width:50px;height:18px;">
						</td>
					</tr>
					
					<tr>
						<td class="text_header">* Step Number</td>
						<td>
							<input type="text" name="step_number" id="step_number" value="<?php echo $id->StepNumber ?>" class="input_text" style="width:50px;height:18px;">
						</td>
					</tr>
					<tr>
						<td class="text_header">&nbsp;</td>
						<td><a href="javascript:void(0);" class="sbutton" onclick="UpdateCollmon();"><span>&nbsp;Save</span></a></td>
					</tr>
				</table>
			</div>
			<?php
		}
		
		function UpdateCollmon()
		{
			$sql = array(
					'CategoryId' 			=> $this -> escPost('category_collmon'),
					'SubCategoryParents'  	=> $this -> escPost('category_collmon'), 
					'SubCategory'  			=> $this -> escPost('sub_category'), 
					'SubCategoryDesc' 		=> $this -> escPost('sub_category'),
					'StartNumber'			=> $this->  escPost('min_number'),
					'EndNumber'				=> $this->  escPost('max_number'),
					'StepNumber'  			=> $this->  escPost('step_number'), 
					'SubCategoryFlags'  	=> 1 
					);
				
			$where = array('SubCategoryId'=>$this->escPost('collmonid'));	
			
			$query = $this -> set_mysql_update('coll_subcategory_collmon',$sql,$where);
			//echo $this ->sqlText;
			if( $query ) : echo 1;
			else :
				echo 0;
			endif;
		}
		
		function DeleteCollmon()
		{
			$sql = " delete from coll_subcategory_collmon where SubCategoryId ='".$this -> escPost('collmonid')."'"; 
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			
			if ( $qry ) : echo 1;
			else : 
				echo 0; 
			endif;
		}
	}
	$CallResult= new CallResult();
	$CallResult -> initClass();
