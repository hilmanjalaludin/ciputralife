<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	class BroadCastMessage extends mysql{
		var $Action;
		var $UserId;
		var $MsgText;
			
		function __construct()
		{
			parent::__construct();	
			$this -> Action  = $this -> escPost('action');
			$this -> UserId  = explode(",",$this -> escPost('user_list')); 
			$this -> MsgText = $this -> escPost('text_message');
			
		}
		
		function index()
		{
			if( $this -> havepost('action'))
			{
				switch( $this ->Action)
				{
					case 'send_user_online'	 : $this -> sendToUserOnline();		break;
					case 'send_user_offline' : $this -> sendToUserOffline();	break;
					case 'send_user_all' 	 : $this -> sendToUserAll();		break;
				}
			}
		}
	
	/////////////////////////////////////
	/* send to online User **/
	
		function sendToUserOnline()
		{
			$send_total_mesages = 0;
			foreach($this -> UserId as $k => $UserId )
			{
				$sql = "SELECT count(a.UserId ) as jumlah FROM tms_agent a where a.logged_state=1 ";			
				$qry = $this -> query($sql);
				if( $qry -> result_singgle_value() > 0 )
				{
					$SQL_Insert['`from`'] = $this -> getSession('UserId'); 
					$SQL_Insert['`message`'] = $this -> MsgText; 
					$SQL_Insert['`sent`'] = date('Y-m-d H:i:s');
					$SQL_Insert['`to`'] = $UserId; 
					$SQL_Insert['`recd`'] = 0;
					
					if( $this -> set_mysql_insert('tms_agent_msgbox',$SQL_Insert) )
					{
						$send_total_mesages++;
					}
				}	
			}
			
			if( $send_total_mesages > 0 ) 
				$result = array('result'=>1, 'msg'=>'Success Send Message to ('.$send_total_mesages.') Users !');
			else
				$result = array('result'=>1, 'msg'=>'Failed Send Message !');
		
		
			echo json_encode($result);
		}
		
	/////////////////////////////////////
	/* send to offline User **/
	
		function sendToUserOffline()
		{
			$send_total_mesages = 0;
			foreach( $this -> UserId as $k => $UserId )
			{
				
				$sql = "SELECT count(a.UserId ) as jumlah FROM tms_agent a where a.logged_state=0 ";
							
				$qry = $this -> query($sql);
				if( $qry -> result_singgle_value() > 0 )
				{
					$SQL_Insert['`from`'] = $this -> getSession('UserId'); 
					$SQL_Insert['`message`'] = $this -> MsgText; 
					$SQL_Insert['`sent`'] = date('Y-m-d H:i:s');
					$SQL_Insert['`to`'] = $UserId; 
					$SQL_Insert['`recd`'] = 0;
					
					if( $this -> set_mysql_insert('tms_agent_msgbox',$SQL_Insert) )
					{
						$send_total_mesages++;
					}
				}	
			}
			
			if( $send_total_mesages > 0 ) 
				$result = array('result'=>1, 'msg'=>'Success Send Message to ('.$send_total_mesages.') Users !');
			else
				$result = array('result'=>1, 'msg'=>'Failed Send Message !');
		
		
			echo json_encode($result);
		}
		
	/////////////////////////////////////
	/* send to all User **/
	
		function sendToUserAll()
		{
			
			
			$send_total_mesages = 0;
			foreach( $this -> UserId as $k => $UserId )
			{
				$SQL_Insert['`from`'] = $this -> getSession('UserId'); 
				$SQL_Insert['`message`'] = $this -> MsgText; 
				$SQL_Insert['`sent`'] = date('Y-m-d H:i:s');
				$SQL_Insert['`to`'] = $UserId; 
				$SQL_Insert['`recd`'] = 0;
					
				if( $this -> set_mysql_insert('tms_agent_msgbox',$SQL_Insert) )
				{
					$send_total_mesages++;
				}
			}
			
			if( $send_total_mesages > 0 ) 
				$result = array('result'=>1, 'msg'=>'Success Send Message to ('.$send_total_mesages.') Users !');
			else
				$result = array('result'=>1, 'msg'=>'Failed Send Message !');
		
		
			echo json_encode($result);
		}
	}	
	
	$BroadCastMessage = new BroadCastMessage(true);
	$BroadCastMessage -> index();
?>	