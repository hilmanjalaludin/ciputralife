<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

class formEditQa extends mysql
{
	var $_url; 
	var $_tem;
	var $_data;	
	
	function formEditQa()
	{
		parent::__construct();
		
		$this -> _url  =& application::get_instance(); /// Application();
		$this -> _tem  =& Themes::get_instance();  // Themes
		$this -> _data =& self::_get_data_customer(); // customer;
		
		if(class_exists('Themes')) 
		{
			self::AXA_Content();
		}
	}
	
	function AXA_Content()
	{
	
	}
}

$formEditQa = new formEditQa();
?>