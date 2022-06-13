<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/**
** project date 10-02-2017
** Menentukan Nilai dari sebuah parameter
** Tambahkan methode untuk mengisi value dari parameter
** Created By : Fajar
**/
class ManageSegmentParam
{
	const IS_INT = 1;
	const IS_STR = 2;
	const IS_FUNC = 3;
	const IS_FLOAT = 4;
	private $_CI;
	
	private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
	
	public function __construct()
	{
		$this->_CI=&get_instance();
	}
	
	public function ParsingParameterSegment($config=array())
	{
		$param=array();
		foreach($config['param_value'] as $id_segment_desc=>$arrayparam)
		{
			foreach($arrayparam as $param_code=>$param_value)
			{
				switch($config['param_type'][$id_segment_desc][$param_code])
				{
					case self::IS_INT :
						$param[$id_segment_desc][$param_code] = (int) $param_value;
					break;
					
					case self::IS_STR :
						$param[$id_segment_desc][$param_code] = (string) $param_value;
					break;
					
					case self::IS_FLOAT :
						$param[$id_segment_desc][$param_code] = (float) $param_value;
					break;
					
					case self::IS_FUNC :
						$param[$id_segment_desc][$param_code] = $this->$param_value();
					break;
					
					default:
						$param[$id_segment_desc][$param_code] = 0;
				}
			}
		}
		return $param;
	}
	
	private function GetPayerMail()
	{
		$email="";
		$this->_CI->db->select("a.PayerEmail");
		$this->_CI->db->from("t_gn_payer a");
		$this->_CI->db->where("a.CustomerId", $this->_CI->session->userdata('CustomerId'));
		$query = $this->_CI->db->get();
		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$email=$row->PayerEmail; 
		}
		return $email;
	}
}