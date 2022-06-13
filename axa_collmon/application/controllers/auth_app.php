<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_app extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
		
	}
	public function index()
	{
		// echo "tes";
		$this->load->view('form_login');
	}
}