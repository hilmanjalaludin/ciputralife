<?php
    //include file
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/lib.form.php");

	class saveFollowUpFa extends mysql
	{
	    //variable initials
		var $_CustomerId;
		var $action;
		var $_FuTYpe;
        var $_pkField;

	    function __construct(){
			parent::__construct();
			$this -> action 	 = $this->escPost('action');
			$this -> _CustomerId = $this -> escPost('CustomerId');
			$this -> _FuTYpe     = $this -> escPost('FuType');	
			$this -> _pkField 	 = $this -> escPost('FuId');

		}


	    /**	
	    * DEFINE Post is action
	    */
		function initClass()
		{
			if( $this->havepost('action'))
			{
				switch( $this->action)
				{
					case 'save_fol'	: 
						$this -> SaveFollow(); 
						break;
					case 'save_bank': 
						$this-> insert_other_bank();
					break;
				}
			}
		}   

		/**	
	    * Get Data Customer
	    * return array
	    * author didi ganteng
	    */
	 	function getCustomer(){

		 	// initials
		 	$data = array();

		 	$sql = "
			 	SELECT  
				 	tgc.CustomerFirstName, 
				 	CustomerDOB, 
				 	CustomerHomePhoneNum, 
				 	CustomerAddressLine1, 
				 	CustomerMobilePhoneNum,
				 	CustomerId,
				 	tgf.*
			 	FROM t_gn_customer tgc 
			 	LEFT JOIN t_gn_followup tgf ON tgf.FuCustId = tgc.CustomerId
			 	WHERE 
				 	tgc.CustomerId = '{$this->_CustomerId}'
		 	";
			// echo "<pre>".$sql."</pre>";
			$qry = $this -> query($sql);

			foreach($qry -> result_assoc() as $rows )
			{
				$data['id']			=   $rows['CustomerId'];
			}
			
			return $data;
		}
		
		/**
		* function for insert new bank
		* author : didi ganteng
		**/
		function insert_other_bank() 
		{
			$results = array('sukses' =>0);
			
			$arrayToDb = array(
				"BankName" => $this -> escPost('BankName'),
			);
			$query = $this -> set_mysql_insert("t_lk_bank",$arrayToDb);

			if( $query ) {
				$results = array('sukses' => 1);
			}
			
			printf("%s", json_encode($results) );
			return false;
		}
		
		/**	
	    *  function for save or update table tgn_followup
	    *  author : didi ganteng
	    */
		function SaveFollow() 
		{
			$result = array('success' =>0, 'FuId' => '' );
			
			$id = $this->getCustomer();

			$arrayToDb = array(
				'FuCustId'   => $this -> _CustomerId,
	         	'FuName'     => $this -> escPost('FuName'),
	         	'FuDOB'	     => $this -> escPost('FuDOB'),
	         	'FuMobile'   => $this -> escPost('FuMobile'),
	         	'FuPhone'    => $this -> escPost('FuPhone'),
	            'FuAddress'  => $this -> escPost('FuAddress'),
	            'FuBank'	 => $this -> escPost('FuBank'),
	            'FuNotes1'   => $this -> escPost('FuNotes1'),
	            'FuNotes2'   => $this -> escPost('FuNotes2'),
	            'FuNotes3'   => $this -> escPost('FuNotes3'),
	            'FuNotes4'   => $this -> escPost('FuNotes4'),
	            'FuAgentId'  => $this -> getSession('UserId'),
	            'FuType'	 => $this -> _FuTYpe,
	            'FuCreateTs' => date('Y-m-d H:i:s'),
				'IsForm'	 => 1
	        );
		 
            // first check data in database;
            if( $this->_pkField !="") {
				//conditions for update data
				$conditions = array("FuId" => $this->_pkField ); 
				// if session QUA have update status
				if( $this-> getSession('handling_type') == 5 || $this-> getSession('handling_type') == 10) {
					$arrayToDb = array(
							'FuName'     => $this -> escPost('FuName'),
							'FuDOB'	     => $this -> escPost('FuDOB'),
							'FuMobile'   => $this -> escPost('FuMobile'),
							'FuPhone'    => $this -> escPost('FuPhone'),
							'FuAddress'  => $this -> escPost('FuAddress'),
							'FuBank'	 => $this -> escPost('FuBank'),
							'FuNotes1'   => $this -> escPost('FuNotes1'),
							'FuNotes2'   => $this -> escPost('FuNotes2'),
							'FuNotes3'   => $this -> escPost('FuNotes3'),
							'FuNotes4'   => $this -> escPost('FuNotes4'),
							'FuQaId'	 => $this -> getSession('UserId'),
							'FuQAStatus' => $this -> escPost('FuQAStatus'),
							'FuUpdateTs' => date('Y-m-d H:i:s'),
							'IsForm'	 => 1
					);
				}
				$query = $this -> set_mysql_update("t_gn_followup", $arrayToDb, $conditions);
				if( $query ) { 
					$result = array('success' =>1, 'FuId' => $this->_pkField);
				}
			} 
			else {
				$query = $this -> set_mysql_insert("t_gn_followup",$arrayToDb);
				if( $query ) {
					$result = array('success' =>1, 'FuId' => mysql_insert_id() );
				}
			}
			printf("%s", json_encode($result) );
			return false;
		}    
	}
	
	$saveFollowUpFa = new saveFollowUpFa();
	$saveFollowUpFa -> initClass();
?>