<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");

class ReportNav extends mysql
{
	private $action;
	
	function ReportNav()
	{
		parent::__construct();
		$this->action = $this->escPost('action');
	}
	
	function start()
	{
		switch($this->action)
		{
			case 'load_cmp' 		: $this -> getCMP(); break;
			case 'load_spv' 		: $this -> getSPV(); break;
			case 'load_tso' 		: $this -> getTSO(); break;
			case 'load_tso_by_spv'  : $this -> getContentTSO(); break;
		}
	}
	
	// HANDLING GROUP FILTER
	
	function getCMP() // load combonya
	{
		switch($this->escPost('mode'))
		{
			case 'cmp'  : // group filter
					$this->getContentCMP();
				break;
				
			case 'spv'  : // group filter
					$this->getContentCMP();
				break;
				
			case 'tso'  : // group filter
					$this -> DBForm -> jpCombo('group_filter_cmp','xx002',array(),NULL);
				break;
				
			default		: 
					$this -> DBForm -> jpCombo('group_filter_cmp','xx002',array(),NULL);
				break;
		}
	}
	
	function getSPV() // load combonya
	{
		switch($this->escPost('mode'))
		{
			case 'cmp'  : // group filter
					$this -> DBForm -> jpCombo('group_filter_spv','xx002',array(),NULL);
				break;
				
			case 'spv'  : // group filter
					$this -> getContentSPVbySPV();
				break;
				
			case 'tso'  : // group filter
					$this -> getContentSPVbyTSO();
				break;
				
			default		: 
					$this -> DBForm -> jpCombo('group_filter_spv','xx002',array(),NULL);
				break;
		}
	}
	
	function getTSO() // load combonya
	{
		switch($this->escPost('mode'))
		{
			case 'cmp'  : // group filter
					$this -> DBForm -> jpCombo('group_filter_tso','xx002',array(),NULL);
				break;
				
			case 'spv'  : // group filter
					$this -> DBForm -> jpCombo('group_filter_tso','xx002',array(),NULL);
				break;
				
			case 'tso'  : // group filter
					$this->getContentTSO();
				break;
				
			default		: 
					$this -> DBForm -> jpCombo('group_filter_tso','xx002',array(),NULL);
				break;
		}
	}
	
	// lookup
	
	function getContentCMP()
	{
		if ($this -> getSession('handling_type') > 0) :
			$sql = "SELECT
						cp.CampaignId as CampId,
						cp.CampaignName as CampName
					FROM t_gn_campaign cp
					where cp.CampaignStatusFlag = 1
					order by CampId";

			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['CampId']] = $rows['CampName'];
			
			}
			$this -> DBForm -> jpListcombo('group_filter_cmp', $label = 'Campaign',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);
		endif;
	}
	
	function getContentSPVbySPV()
	{
		if( $this -> getSession('handling_type')==1) {
			$sql = " select a.UserId, a.full_name from tms_agent a  where a.handling_type=3 and a.user_state=1";
		}
		
		if( $this -> getSession('handling_type')==9) {
			$sql = " select a.UserId, a.full_name from tms_agent a  where a.handling_type=3 and a.user_state=1";
		}
		
		if( $this -> getSession('handling_type')==2) {
			$sql = " select a.UserId, a.full_name from tms_agent a 
					 where a.handling_type=3 and a.user_state=1 
					 and a.mgr_id='".$_SESSION['UserId']."'";
		}
		
		if( $this -> getSession('handling_type')==3) {
			$sql = " select a.UserId, a.full_name from tms_agent a 
					 where a.handling_type=3 and a.user_state=1 
					 and a.UserId='".$_SESSION['UserId']."'";
		}
		//echo $sql;
		$qry = $this -> query($sql);
		
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['UserId']] = $rows['full_name'];
		
		}
		
		$this -> DBForm -> jpListcombo('group_filter_spv', $label = 'User SPV',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);
	}
	
	function getContentSPVbyTSO()
	{
		if( $this -> getSession('handling_type')==1) {
			$sql = " select a.UserId, a.full_name from tms_agent a  where a.handling_type=3 and a.user_state=1";
		}
		
		if( $this -> getSession('handling_type')==9) {
			$sql = " select a.UserId, a.full_name from tms_agent a  where a.handling_type=3 and a.user_state=1";
		}
		
		if( $this -> getSession('handling_type')==2) {
			$sql = " select a.UserId, a.full_name from tms_agent a 
					 where a.handling_type=3 and a.user_state=1 
					 and a.mgr_id='".$_SESSION['UserId']."'";
		}
		
		if( $this -> getSession('handling_type')==3) {
			$sql = " select a.UserId, a.full_name from tms_agent a 
					 where a.handling_type=3 and a.user_state=1 
					 and a.UserId='".$_SESSION['UserId']."'";
		}
		
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['UserId']] = $rows['full_name'];
		
		}
		$this -> DBForm -> jpCombo('group_filter_spv','xx004',$datas,NULL,'onchange="Ext.DOM.groupFilterContent();"');
	}
	
	function getContentTSO()
	{	
		$sql = " select a.UserId, a.full_name from tms_agent a 
				 where a.handling_type=4 and a.user_state=1
				 AND a.spv_id='".$_REQUEST['spv_id']."'";
		$qry = $this -> query($sql);
		
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['UserId']] = $rows['full_name'];
		
		}
		$this -> DBForm -> jpListcombo('group_filter_tso', $label = 'User TM',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);
	}
	
	// END OF HANDLING GROUP FILTER
}

$ReportNav = new ReportNav();
$ReportNav->start();
?>