<?php
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/lib.form.php");
	/* 
	 * class Campaign core 
	 * update , insert, delete, tempelate 
	*/
	class CmpCore extends mysql{
		var $action;
		var $error;
		var $form;
		
		
		function __construct(){
			parent::__construct();
			$this -> action = $this->escPost('action');
			$this -> form	= new jpForm(true);
		}
		
		function setError($string){
			if( !empty($string)):
				$this -> error = $string; 
			endif;
			
			echo $this -> error;
		}
		
		function initCmp()
		{
		
			if( $this->havepost('action'))
			{
				switch($this -> action)
				{
					case 'add_campaign_core' 	: $this -> addCampaignCore(); 	 break;
					case 'tpl_campaign_core' 	: $this -> tplCampaignCore(); 	 break;
					case 'save_campaign_core'	: $this -> addCampaignCore(); 	 break;
					case 'edit_campaign_core'	: $this -> editCampaignCore(); 	 break;
					case 'update_campaign_core' : $this -> updateCampaignCore(); break;
					case 'enable_core' 			: $this -> EnableCore(); break;
					case 'disable_core' 		: $this -> DisableCore(); break;
				}
			}
		}
		
		function EnableCore()
		{
			$sql = "update t_gn_campaigngroup set CampaignGroupStatusFlag = 1 where CampaignGroupId =".$this->escPost('core');
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			
			if ( $qry ) : echo 1;
			else : 
				echo 0; 
			endif;
		}
		
		function DisableCore()
		{
			$sql = "update t_gn_campaigngroup set CampaignGroupStatusFlag = 0 where CampaignGroupId =".$this->escPost('core');
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			
			if ( $qry ) : echo 1;
			else : 
				echo 0; 
			endif;
		}
		
		function updateCampaignCore()
		{
			$sql = " UPDATE t_gn_campaigngroup
					 SET
						CampaignGroupCode='".$_REQUEST['text_cmp_name']."',
						CampaignGroupName='".$_REQUEST['text_cmp_id']."',
						CampaignGroupStatusFlag='".$_REQUEST['select_cmp_status']."'
					 WHERE CampaignGroupId='".$_REQUEST['campaign_core_id']."'";
					 
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			//echo $sql;
			if( $qry ){
				$result = array('result'=>1,'message'=>'Success, Update Campaign Core !');
			}	
			else{
				$result = array('result'=>0, 'message'=>'Failed, Update Campaign Core !');
			}
			
			echo json_encode($result);
		}
		
		function addCampaignCore()
		{
			$datas = array('CampaignGroupCode' => $this->escPost('text_cmp_name'),
							'CampaignGroupName' => $this->escPost('text_cmp_id'),
							'CampaignGroupStatusFlag' => $this->escPost('select_cmp_status')
							);
							
							
			
			$res = $this -> set_mysql_replace('t_gn_campaigngroup',$datas);
			
			if($res): 
				$this->setError('1');
			else: 
				$this -> setError('0'); 
			endif;
		}
		
		function editCampaignCore()
		{ 
			$id = base64_decode($_REQUEST['CampaignId']);
			$sql = "select * from t_gn_campaigngroup a where a.CampaignGroupId='".$id."' ";
			$qry = $this -> query($sql);
			//echo $sql;
			if( $qry -> result_num_rows() > 0 )
			{
				$CampaignEdit = $qry -> result_first_assoc();
			}
		?>
			<style>
				.text { height:18px;width:160px;color:#000000;border:1px solid red; }
				.text2 { height:18px;width:160px;color:GREY;border:1px solid GREY; }
				.text3 { height:18px;width:160px;color:GREY; }
				.selc { height:22px;width:160px;color:#000000;border:1px solid red; }
			</style>
			<table cellpadding="4px;">
				<input type="hidden" name="CampaignCoreId" id="CampaignCoreId" value="<?php echo $CampaignEdit['CampaignGroupId']; ?>" >
 				<tr>
					<td class="text3">* Campaign Core Code</td>
					<!--<td><?php $this -> form -> jpInput('text_cmp_id','text',$CampaignEdit['CampaignGroupCode'],'onkeyup=RenderValueCampaign(this);',0,50); ?>&nbsp;(50)</td>-->
					<!--<td><?php $this -> form -> jpInput('text_cmp_name','text',$CampaignEdit['CampaignGroupCode'],NULL,0,50); ?>&nbsp;(50)</td>-->
					<td><?php $this -> form -> jpInput ('text_cmp_name','text2',$CampaignEdit['CampaignGroupCode'],NULL,1,50);?></td>
				</tr>
				<tr>
					<td>* Campaign Core Name</td>
					<td><?php $this -> form -> jpInput('text_cmp_id','text',$CampaignEdit['CampaignGroupName'],NULL,0,50); ?>&nbsp;(50)</td>
					
				</tr>
				<tr>
					<td>* Status </td>
					<td><?php $this -> form -> jpCombo('select_cmp_status','selc', array(1=>'Active',0=>'Not Active'),$CampaignEdit['CampaignGroupStatusFlag']);?></td>
				</tr>				
			</table>
		<?php
		}
		
		function deleteCampaignCore(){
		
		}
		
		function tplCampaignCore(){ ?>
			<table cellpadding="4px;">
				<tr>
					<td>* Campaign Core Code</td>
					<td><input type="text" id="text_cmp_id" name="text_cmp_id"  value="" onkeyUp="javascript:doJava.dom('text_cmp_name').value=this.value;" style="height:18px;width:160px;color:#000000;border:1px solid red;" maxlength="10"> (10)</td>
				</tr>

				<tr>
					<td>* Campaign Core Name</td>
					<!--<td><input type="text" id="text_cmp_name" name="text_cmp_name" value="" style="height:18px;width:160px;color:#000000;border:1px solid red;" maxlength="10" disabled> (10)</td>-->
					<td><input type="text" id="text_cmp_name" name="text_cmp_name" value="" style="height:18px;width:160px;color:#000000;border:1px solid red;" maxlength="10" enabled> (10)</td>
				</tr>
				<tr>
					<td>* Status </td>
					<td>
						<select name="select_cmp_status" id="select_cmp_status" style="height:21px;width:100px;color:#000000;border:1px solid red;">
							<option value=""> -- Choose -- </option>
							<option value="1"> Active </option>
							<option value="0"> Not Active </option>
						</select>	
					</td>
				</tr>				
			</table>
		<?php }	
		
		
		function getError(){
			echo $this -> error = $string; 
		}
		
	}
	
	$CmpCore = new CmpCore();
	$CmpCore -> initCmp();
	$CmpCore -> getError();

?>