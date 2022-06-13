<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../sisipan/parameters.php");
	
	class UpdateReferal extends mysql
	{
		var $action;
		
		function UpdateReferal()
		{
			parent::__construct();
			
			$this->action = $_REQUEST['action'];
			//print_r($_REQUEST['action']);
			self::index();
		}
		
		function index()
		{
			switch($this->action){
				case 'approve_ref' : $this->UpdateRef(); break;
				case 'reject_ref'  : $this->RejectRef(); break;
			}
			
		}
		
		function _ref_id()
		{
			$id = explode(',',$_REQUEST['ref_id']);
			
			return $id;
		}
		
		function UpdateRef()
		{
			$i = 0;
			$SQL_UPDATE = array();
			$datas = $this->_ref_id();
			
			$SQL_UPDATE['ReferalQAStatus'] 		= 1;
			$SQL_UPDATE['ReferalUpdateQAUid']	= $this->getSession('UserId');
			$SQL_UPDATE['ReferalUpdatedTs'] 	= date("Y-m-d H:i:s");
			
			foreach($datas as $key => $value)
			{
				$WHERE['ReferalId'] = $value;
				$query = $this -> set_mysql_update('t_gn_referal',$SQL_UPDATE,$WHERE);
				$this ->sqlText;
				if( $query ) : $i++;
				endif;
			}
			
			if( $i > 0 ) : echo 1;
			else :
				echo 0;
			endif;
		}
		
		function RejectRef()
		{
			$i = 0;
			$SQL_UPDATE = array();
			$datas = $this->_ref_id();
			
			$SQL_UPDATE['ReferalQAStatus'] 		= 0;
			$SQL_UPDATE['ReferalUpdateQAUid']	= $this->getSession('UserId');
			$SQL_UPDATE['ReferalUpdatedTs'] 	= date("Y-m-d H:i:s");
			
			foreach($datas as $key => $value)
			{
				$WHERE['ReferalId'] = $value;
				$query = $this -> set_mysql_update('t_gn_referal',$SQL_UPDATE,$WHERE);
				$this ->sqlText;
				if( $query ) : $i++;
				endif;
			}
			
			if( $i > 0 ) : echo 1;
			else :
				echo 0;
			endif;
		}
	}
	
	new UpdateReferal();
	
?>