<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** project date 10-02-2017
** rederict page from enigma
** parameter session enigma, page to open and others
** Created By : Fajar
**/
class manageredirect extends CI_Controller {
	
	private $page_available = array();
	function __construct()
    {
        parent::__construct();
		$this->page_available = array(
			'showscoring'=> 'qa_collmon/index',
			'dailysqcreport'=> 'qa_collmon/daily_sqc_report'
		);
	}
	public function index()
	{
		$body = array();
		$param = $this->uri->uri_to_assoc();
		// var_dump($this->input->get("UserId"));
		// $this->load->view('layout/sbadmin',$body);
	}
	
	public function login_enigma()
	{
		$param = $this->uri->uri_to_assoc();
		$link=null;
		// $BASE_ENIGMA ="../../../../../axalt5.2_ivr/";
		// var_dump($param);
		$set_session = array(
			'UserId',
			'username',
			'user_profile',
			'mgr_id',
			'spv_id',
			'user_agency',
			'handling_type'
		);
		if(count($param) < count($set_session))
		{
			exit("error : login");
		}
		foreach($set_session as $index => $val)
		{
			if(!isset($param[$val]))
			{
				exit("error : login");
			}
		}
		if(!isset($param['page']))
		{
			exit("error : page not available");
		}
		if(array_key_exists($param['page'],$this->page_available))
		{
			$link = $this->page_available[$param['page']];
			unset($param['page']);
		}
		else
		{
			exit("error : not allowed");
		}
		
		foreach($set_session as $index => $val)
		{
			$this->session->set_userdata($val,$param[$val]);
			unset($param[$val]);
		}
		$string_param="";
		// var_dump($param);
		if(count($param) >= 1)
		{
			$string_param="/".$this->uri->assoc_to_uri($param);
		}
		
		if(!is_null($link))
		{
			redirect($link.$string_param);
		}
		
	}
}