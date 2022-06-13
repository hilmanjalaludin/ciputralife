<?php

require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/lib.form.php");


class Convertion_Report extends mysql
{

	var $JPForm;

	public function Convertion_Report()
	{
			parent::__construct();
			session_start();
			
			self::index();
		}
		
	public function index()
	{
		if( $this -> havepost('action'))
		{
			$this -> JPForm = new jpForm();
			
			switch($_REQUEST['action'])
			{
				case 'get_filter_tm' : $this -> getFilterTelesales(); 	break;	
			}
		}
	}
	
	public function getFilterTelesales()
	{
		if($this -> getSession('handling_type')==USER_ADMIN)
			$sql = " select a.UserId, a.full_name from tms_agent a where a.handling_type=4 and a.user_state=1";
		else if($this -> getSession('handling_type')==USER_MANAGER)
			$sql = " select a.UserId, a.full_name from tms_agent a  where a.handling_type=4 and a.user_state=1 ";
		else if($this -> getSession('handling_type')==USER_SUPERVISOR) 
			$sql = " select a.UserId, a.full_name from tms_agent a where a.handling_type=4 and a.user_state=1 and a.spv_id='".$_SESSION['UserId']."'";
		else
			$sql = " select a.UserId, a.full_name from tms_agent a where a.handling_type=4 and a.user_state=1";
			
		// echo $sql;
		if($this -> havepost('values')){
			$sql.=" AND a.spv_id='".$_REQUEST['values']."'";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['UserId']] = $rows['full_name'];
			}
			
			switch($_REQUEST['Type']){
				case 'box' : $this -> JPForm -> jpListcombo('group_filter_tm', $label = 'Telesales',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0); break;
				case 'cmb' : $this -> JPForm -> jpCombo('group_filter_tm', 'xx004', $datas); break;
				case 'mtp' : $this -> JPForm -> jpMultiple('group_filter_tm','xx001',$datas); break;
			}
		}else{
			$this -> JPForm -> jpListcombo('group_filter_tm', $label = 'Telesales',NULL,$values = NULL, $js = NULL,$attr = false, $dis=0);
		}
	}

}

new Convertion_Report();