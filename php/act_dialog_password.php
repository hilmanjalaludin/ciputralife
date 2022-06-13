<?php
   
   /**! 
	* attribut chage password by User
    * this compare old file to classes
	* on php OOP
    * createdby@omens
   */
	
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	class UserPassword extends mysql{
		var $ipAddr; 
		var $username;
		var $user_agency;
		var $handling_type;
		var $curr_password;
		var $new_password;
		var $renew_password;
		var $errString;
		var $userTable;
	
	/**! constructor for this class **/
	
		function __construct()
		{
			parent::__construct();
			
			$this->userTable	 = 'tms_agent';
			$this->ipAddr 	     = $this->getRealIpAddr();
			$this->username      = $_SESSION['username'];
			$this->user_agency   = $_SESSION['user_agency'];
			$this->handling_type = $_SESSION['handling_type'];			
			$this->curr_password = $_REQUEST['curr_password'];
			$this->new_password  = $_REQUEST['new_password'];
			$this->renew_password= $_REQUEST['re_new_password'];
	
		}
		
	/**! executed Query from user Requet **/
	
		public function executeUpdatePassword(){
		  if(!empty($this->curr_password) && ($this->new_password ==$this->renew_password)){
			
			/** Sql value of columns **/
				$sql['password']		= md5($this->new_password);
				$sql['update_password']	= date('Y-m-d H:i:s');
				$sql['last_update']		= date('Y-m-d H:i:s');
			
			
			/** where string **/
				$con['id'] = $this->username; 
			
			$result = $this -> set_mysql_update($this->userTable,$sql, $con);
			if($result){ 
				 $this->errString = '';
			}else{ 
				 $this->errString = 'Password fail to change.Pleas try again..!';
			}
		  } else $this->errString = 'Password not match. Re-type New Pass !';
		} 
	}
	
	if(!is_object($objPass)){
		$objPass = new UserPassword();
		$objPass->executeUpdatePassword();
	}
	
	
	/**! call back error on execute sql 
		return for ajax request !
		@omens
	**/
	
	  echo $objPass->errString;

?>