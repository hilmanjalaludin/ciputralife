<?php 

require(dirname(__FILE__).'/../sisipan/sessions.php');
require(dirname(__FILE__).'/../class/MYSQLConnect.php');
require(dirname(__FILE__).'/../class/lib.form.php');

class Deletedata extends mysql {
	
	function Deletedata(){
		parent::__construct();
			
		$this -> action = $this->escPost('action');
		$this -> Form   = new jpForm();
		//$this -> setCss();
	}
	
	function index(){
		$action = $_REQUEST['action'];
		switch ($action) {
			case 'Delete':
				$this->delete();
				break;
			case 'tpl_onready':
				$this->tplOnReady();
				break;
		}
	
	
	}
	
	function delete(){
		$campaignid = $_REQUEST['CampaingId'];
		$sql = "insert into t_gn_customer_db (select * from t_gn_customer where CampaignId = '".$campaignid."')";
		$this->execute($sql);
		$sqldelete = "delete from t_gn_customer where CampaignId = '".$campaignid."'";
		//$this->query($sqldelete);
		$qry = $this -> execute($sqldelete,__FILE__,__LINE__);
		if( $qry )
		{
			$result = array('result'=>1);
		}
		//$result = array('result'=>1,'id'=>$sql);
		echo json_encode($result);
	}
	
	function setCss(){ ?>
				
				<!-- start: css -->
					<style>
						.select { border:1px solid #dddddd;width:160px;font-size:11px;height:22px;background-color:#fffccc;}
						.input_text {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:160px;font-size:11px;height:20px;background-color:#fffccc;}
						.text_header { text-align:right;color:#746b6a;font-size:12px;}
						.select_multiple { border:1px solid #dddddd;width:250px;font-size:11px;background-color:#fffccc;}
					</style>
					
				<!-- stop: css -->
			<?php }
	
	
	
	function getCampaing(){
		$sql = "SELECT `CampaignId`,`CampaignName` FROM t_gn_campaign";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows)
		{
			$datas[$rows['CampaignId']] = $rows['CampaignName'];
		}
			
		return $datas;
	} 
	
	



function tplOnReady(){
	$this->setCss();
	?>
			
	<div id="result_content_add" class="box-shadow" style="padding-bottom:4px;margin-top:2px;margin-bottom:8px;padding-top:4px;">
				<table cellpadding="8px;">
					<tr>
						
						<td class="text_header"> Campaign</td>
						<td><?php $this -> Form -> jpCombo('IdCampaing', 'select', $this -> getCampaing(),$this->escPost('IdCampaing')) ?></td>
						
					</tr>
					
					
					
				</table>
			</div>
<?php }

}// end class
$Deletedata = new Deletedata();
$Deletedata->index();

?>