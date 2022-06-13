<?php
require(dirname(__FILE__).'/../sisipan/sessions.php');
require(dirname(__FILE__).'/../class/MYSQLConnect.php');
require(dirname(__FILE__).'/../class/lib.form.php');

class CampaignTracking extends mysql
{
	function CampaignTracking(){
		parent::__construct();
		$this -> setForm();
	}


/* eidt tpl work Area ****************************/

	private function setForm()
	{
		$this -> setForm = new jpForm();
	}

	function index()
	{
		switch($_REQUEST['action'])
		{
			case 'get_campaign_list'  : $this -> get_campaign_list(); 	break;
			case 'get_tmr_list'  : $this -> get_tmr_list(); 	break;
			case 'get_spv_list'  : $this -> get_spv_list(); 	break;
		}
	}
	function get_campaign()
	{
		$datas = array();
		if ($this->havepost('cmp_status'))
		{
			$status = $this->escPost('cmp_status');

			$sql ="SELECT a.CampaignId,a.CampaignName FROM t_gn_campaign a WHERE a.CampaignStatusFlag = ".$status;
			$qry = $this -> query($sql);
			foreach($qry-> result_assoc() as $rows )
			{
				$datas[$rows['CampaignId']] = $rows['CampaignName'];
			}
		}
		return $datas;
	}
	function get_tmr()
	{
		$datas = array();
		if ($this->havepost('group_filter_segment2'))
		{
			$spvid = $this->escPost('group_filter_segment2');

			$sql ="select a.UserId as agent,a.id as fullname from tms_agent a
					where a.user_state=1
					and a.handling_type=4
					and a.spv_id= ".$spvid."
					and a.id not like 'bucket%'";
			$qry = $this -> query($sql);
			// echo "<pre>".$sql."</pre>"; exit;
			foreach($qry-> result_assoc() as $rows )
			{
				$datas[$rows['agent']] = $rows['fullname'];
			}
		}
		return $datas;
	}
	function get_spv()
	{
		$datas = array();
		$spvid = $this->escPost('group_filter_segment2');
		$sql ="select a.UserId as agent,a.id as fullname from tms_agent a
					where a.user_state=1
					and a.handling_type=3
					and a.id not like 'bucket%'";
		$qry = $this -> query($sql);
			// echo "<pre>".$sql."</pre>"; exit;
			foreach($qry-> result_assoc() as $rows )
			{
				$datas[$rows['agent']] = $rows['fullname'];
			}
		return $datas;
	}
	function get_campaign_list()
	{
		$this -> setForm->jpListcombo('select_camp','All Campaign', $this->get_campaign());
	}
	function get_tmr_list()
	{
		$this -> setForm->jpListcombo('select_tele','All Telesales', $this->get_tmr());
	}
	function get_spv_list()
	{
		$this -> setForm->jpListcombo('group_filter_segment2','All Supervisor', $this->get_spv());
	}
}

$CampaignTracking = new CampaignTracking();
$CampaignTracking -> index();
?>
