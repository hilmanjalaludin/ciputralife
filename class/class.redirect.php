<?php 
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

class RedirectEnigma extends mysql
{
	private $server_ip="127.0.0.1";
	private $param_allow;
	
	function __construct()
	{
		$this->param_allow = array(
			'CustomerId',
			'start_date_callmon',
			'end_date_callmon',
			'start_date_sale',
			'end_date_sale',
			'product_group',
			'action'
		);
		// echo "tes";
		$this->redirect_page();
	}
	
	private function redirect_page()
	{
		if( $this->havepost('page') && $this -> getSession('UserId'))
		{
			$halaman=strtolower($this->escPost('page'));
			$param = array(
				'UserId'=>$this -> getSession('UserId'),
				'username'=>$this -> getSession('username'),
				'user_profile'=>$this -> getSession('user_profile'),
				'mgr_id'=>($this -> getSession('mgr_id')?$this -> getSession('mgr_id'):0),
				'spv_id'=>($this -> getSession('spv_id')?$this -> getSession('spv_id'):0),
				'user_agency'=>$this -> getSession('user_agency'),
				'handling_type'=>$this -> getSession('handling_type')
			);
			$url_string="";
			$url_string.="page/".$halaman."/";
			foreach($param as $index=>$value)
			{
				$url_string.= $index."/".$value."/";
			}
			
			foreach($this->param_allow as $idx=>$val)
			{
				if($this->havepost($val))
				{
					$url_string.= $val."/".$this->escPost($val)."/";
				}
			}
			
			
			$url_string = substr($url_string,0,(strlen($url_string)-1));
			$uri='axa_collmon/index.php/manageredirect/login_enigma/'.$url_string;
			echo $uri;
			// header('Location: ../../'.$this->page[$halaman].'main/'.$url_string);
			
			header("Location: ../".$uri, TRUE, 302);
			exit();
		}
		else
		{
			header('Location: ../');
		}
		
	}
}

new RedirectEnigma();