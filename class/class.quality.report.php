<?php
require(dirname(__FILE__).'/../sisipan/sessions.php');
require(dirname(__FILE__).'/../class/MYSQLConnect.php');
require(dirname(__FILE__).'/../class/lib.form.php');

/*
 * class filename class.work.area.php
 * subject product application
 * version v.6
 * author : omens
 */	
 class QualityReport extends mysql
 {
	private $JP_plugin; 
 /* aksesor of class ***/
 
	function QualityReport()
	{
		self::index();
	}
	
/** main index class ****/
	function index()
	{
		$this -> JP_plugin = new jpForm();
		
		if( $this -> havepost('action'))
		{
			switch($_REQUEST['action'])
			{
				case 'get_user_agent'	   : $this -> getUserAgent(); 		break;	
				case 'by_quality_campaign' : $this -> getCampaignName(); 	break;
				case 'by_quality_agent'	   : $this -> getQualityAgent(); 	break;	
				case 'by_quality_status'   : $this -> getQualityStatus(); 	break;
				case 'by_quality_date'	   : $this -> getQualityDates(); 	break;	
				case 'by_quality_qa'	   : $this -> getQualityQA(); 		break;	
			}
		}
	}
	
/** getQualityQA **/
 function getQualityQA()
 {
	$sql = "SELECT a.UserId, a.full_name FROM tms_agent a WHERE a.profile_id = 5 and a.user_state=1";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows)
	{
		$datas[$rows['UserId']] = $rows['full_name'];
	}
		
	if( $_REQUEST['type_form']=='check')  $this -> JP_plugin -> jpListcombo('group_filter_value', $label = 'User QA',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);
	if( $_REQUEST['type_form']=='combo')  $this -> JP_plugin -> jpCombo('group_filter_value','xx002',$datas, NULL, 'onchange="doJava.getUserBySpv(this);"',$attr = false, $dis=0);	
	
 }
 	
/** getUserAgent **/

  function getUserAgent()
  {
	if( $this -> havepost('spvid'))
	{
		$sql = " SELECT a.UserId, a.full_name FROM tms_agent a 
				 WHERE a.profile_id = 4  AND a.user_state=1
				 AND a.spv_id='".$_REQUEST['spvid']."'";

		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows)
		{
			$datas[$rows['UserId']] = $rows['full_name'];
		}
		
		$this -> JP_plugin -> jpListcombo('group_user_tm', 'User TM', $datas,$values = NULL, $js = NULL,$attr = false, $dis=0);
	}
	else{	
		$this -> JP_plugin -> jpCombo('group_user_tm','xx002',array(), NULL, NULL,true,true);
	}	
  }	
  
/** getQulityDates **/

 function getQualityDates()
 {	
	$this -> JP_plugin -> jpCombo('group_filter_value','xx002',array(), NULL, NULL, TRUE);
 }
	
	
/** getQulityStatus **/
 function getQualityStatus()
 {
	$sql = " select a.ApproveId, a.AproveName from t_lk_aprove_status a ";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows)
	{
		$datas[$rows['ApproveId']] = $rows['AproveName'];
	}
		
	
	if( $_REQUEST['type_form']=='check') $this -> JP_plugin -> jpListcombo('group_filter_value', $label = 'Select Status',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);
	if( $_REQUEST['type_form']=='combo') $this -> JP_plugin -> jpCombo('group_filter_value','xx002',$datas, NULL, 'onchange="doJava.getUserBySpv(this);"',$attr = false, $dis=0);
	
 }

/** function getQulityAgent **/

 function getQualityAgent()
	{
		$sql = "SELECT a.UserId, a.full_name FROM tms_agent a WHERE a.profile_id = 3  and a.user_state=1";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows)
		{
			$datas[$rows['UserId']] = $rows['full_name'];
		}
		
		if( $_REQUEST['type_form']=='check')  $this -> JP_plugin -> jpListcombo('group_filter_value', $label = 'Select Campaign',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);
		if( $_REQUEST['type_form']=='combo')  $this -> JP_plugin -> jpCombo('group_filter_value','xx002',$datas, NULL, 'onchange="doJava.getUserBySpv(this);"',$attr = false, $dis=0);	
	}	
	
/** getCampaignName ***/

 function getCampaignName()
	{
		$sql = "select a.CampaignId, a.CampaignName from t_gn_campaign a where a.CampaignStatusFlag=1 ";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows)
		{
			$datas[$rows['CampaignId']] = $rows['CampaignName'];
		}
		
		if( $_REQUEST['type_form']=='check') $this -> JP_plugin -> jpListcombo('group_filter_value', $label = 'Select Campaign',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);
		if( $_REQUEST['type_form']=='combo') $this -> JP_plugin -> jpCombo('group_filter_value','xx002',$datas, $values = NULL, $js = NULL,$attr = false, $dis=0); 
	}	
 }
 
 new QualityReport();