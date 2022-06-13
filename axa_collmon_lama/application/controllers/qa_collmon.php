<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** project date 10-02-2017
** Scoring axa 
** parameter customer id and product group
** Created By : Fajar
**/
class qa_collmon extends CI_Controller {

	function __construct()
    {
        parent::__construct();
		$this->load->model(array("m_qa_collmon"));
		
	}
	
	public function index()
	{
		$param = $this->uri->uri_to_assoc();
		$data = array();
		
		$this->session
		->unset_userdata('CustomerId');
		
		$this->session
		->set_userdata(
			'CustomerId',
			$param['CustomerId']
		);
		$data['CustomerInfo'] = $this->m_qa_collmon
		->SetWhere(
			array('CustomerId'=> $param['CustomerId'])
		)
		->CustomerInfo();		
		if(count($data['CustomerInfo']) == 1)
		{
			$this->session
			->unset_userdata('ProductGrupId');
			$this->session
			->set_userdata(
				'ProductGrupId',
				$data['CustomerInfo'][0]['product_category_id']
			);
			// var_dump($data['CustomerInfo'][0]['product_category_id']);
			
			$data['Form'] = $this->m_qa_collmon
			->SetWhere(
				array(
					'product_category_id'=> $data['CustomerInfo'][0]['product_category_id']
			))
			->_CollMonForm();
			$data['score_place'] = $this->m_qa_collmon
			->setWhere(
				array(
					'product_category_id'=>$this->session->userdata('ProductGrupId')
				)
			)
			->GetCalculationColomn();
			$data['saved'] = $this->m_qa_collmon
			->setWhere(
				array(
					'CustomerId'=>$this->session->userdata('CustomerId')
				)
			)->GetSavedData();
			$data['recording'] = $this->m_qa_collmon
			->setWhere(
				array(
					'CustomerId'=>$this->session->userdata('CustomerId')
				)
			)->GetDurationRecording();
			$GroupHidden = $this->m_qa_collmon
			->setWhere(
				array(
					'CollGroupId'=>array_keys($data['Form']['category'])
				)
			)->GetHiddenGroupByStatusSQC();
			
			if(count($GroupHidden)>0)
			{
				foreach ($GroupHidden as $qa_status => $group_range)
				{
					if($qa_status != $data['CustomerInfo'][0]['CallReasonQue'] )
					{
						foreach($group_range as $id=>$groupid)
						{
							unset($data['Form']['category'][$groupid]);
							unset($data['Form']['add_remark_category'][$groupid]);
							unset($data['Form']['sub_category'][$groupid]);
							unset($data['Form']['sub_mandat'][$groupid]);
							unset($data['Form']['sub_category_input'][$groupid]);
							unset($data['Form']['input_func'][$groupid]);
							unset($data['Form']['answer_label'][$groupid]);
						}
					}
				}
			}
			// echo "<pre>";
			// print_r($data);
			// echo "</pre>";
			
			$this->load->view('qa_collmon/form_qa_collmon',$data);
		}
		else
		{
			echo "Not support ( agent closing more than one product group )";
		}
	}
	
	public function json_tools()
	{
		$data = array();
		// $remove = array(
			// 'header',
			// 'category',
			// 'add_remark_category',
			// 'sub_category',
			// 'sub_category_mandat',
			// 'sub_category_input',
			// 'input_func',
			// 'answer_label',
			// 'answer_default'
		// );
		$remove = array(
			'header',
			'add_remark_category',
			'sub_category_input',
			'input_func',
			'answer_label',
			'answer_default',
			'answer_value',
			'answer_is_input',
			'answer_is_readonly',
			'answer_is_session'
		);
		
		
		
		$data = $this->m_qa_collmon
		->setWhere(
			array(
				'product_category_id'=>$this->session->userdata('ProductGrupId')
			)
		)->_CollMonForm();
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		foreach($remove as $items)
		{
			unset($data[$items]);
		}
		$data['score_place'] = $this->m_qa_collmon
		->setWhere(
			array(
				'product_category_id'=>$this->session->userdata('ProductGrupId')
			)
		)
		->GetCalculationColomn();
		echo json_encode($data);
	}
	
	public function save_score_fpa()
	{
		$status=array();
		$count_saved = $this->m_qa_collmon
		->setWhere(
			array(
				'CustomerId'=>$this->session->userdata('CustomerId')
			)
		)
		->CountSavedDataScoring();
		if($count_saved===0)
		{
			if($this->m_qa_collmon->insert_score())
			{
				$status['message']="Success save score";
			}
			else
			{
				$status['message']="Fail save score";
			}
		}
		else
		{
			if($this->m_qa_collmon->update_score())
			{
				$status['message']="Success update score";
			}
			else
			{
				$status['message']="Fail update score";
			}
		}
		echo json_encode($status);
		// $this->m_qa_collmon->test_break();
	}
	
	public function json_score_review_fpa()
	{
		
		$score =$this->m_qa_collmon->score_fpa();
		echo json_encode($score);
	}
	
	public function daily_sqc_report()
	{
		$param = $this->uri->uri_to_assoc();
		$this->load->helper('convert');
		$filter=array();
		if(!isset($param['product_group']))
		{
			exit("Filter : Product group is empty");
		}
		if( !(( isset($param['start_date_callmon']) and isset($param['end_date_callmon']) ) or
			( isset($param['start_date_sale']) and isset($param['end_date_sale']) ))
		)
		{
			exit("Filter : please input some interval");
		}
		
		if( isset($param['product_group']) )
		{
			$filter['product_category_id']=$param['product_group'];
		}
		
		if( isset($param['start_date_callmon']) and isset($param['end_date_callmon']) )
		{
			$filter['colmon_date']['start']= ConvertDateToEnglish($param['start_date_callmon']);
			$filter['colmon_date']['end']= ConvertDateToEnglish($param['end_date_callmon']);
			$data['filter']['colmon_date']['start']= $param['start_date_callmon'];
			$data['filter']['colmon_date']['end']= $param['end_date_callmon'];
		}
		
		if( isset($param['start_date_sale']) and isset($param['end_date_sale']) )
		{
			$filter['selling_date']['start']= ConvertDateToEnglish($param['start_date_sale']);
			$filter['selling_date']['end']= ConvertDateToEnglish($param['end_date_sale']);
			$data['filter']['selling_date']['start']= $param['start_date_sale'];
			$data['filter']['selling_date']['end']= $param['end_date_sale'];
		}
		
		$data['form'] = $this->m_qa_collmon
		->setWhere(
			array(
				'product_category_id'=>$param['product_group']
			)
		)
		->_CollMonForm();
		
		$remove = array(
			'sub_mandat',
			'sub_category_input',
			'input_func',
			'answer_label',
			'answer_value',
			'answer_is_input',
			'answer_default',
			'answer_is_readonly',
			'answer_is_session'
		);
		foreach($remove as $items)
		{
			unset($data['form'][$items]);
		}
		
		$data['header_collmon'] = $this->m_qa_collmon
		->setWhere(
			array(
				'product_category_id'=>$param['product_group']
			)
		)
		->header_collmon();
		
		$data['policy'] = $this->m_qa_collmon
		->setWhere($filter)
		->GetPolicyForm();
		
		$data['col_result'] = $this->m_qa_collmon
		->setWhere($filter)
		->GetCollmonResult();
		
		$data['score_result'] = $this->m_qa_collmon
		->setWhere($filter)
		->GetScoreResult();
		
		$data['remarks_form'] = $this->m_qa_collmon
		->setWhere($filter)
		->GetRemarksResult();
		
		$data['remarks_group'] = $this->m_qa_collmon
		->setWhere($filter)
		->GetGroupRemarks();
		
		$data['static_input'] = $this->m_qa_collmon
		->setWhere($filter)
		->GetStaticInput();
		
		
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// echo 'ini report';
		switch($param['action'])
		{
			case 'showhtml' :
				$this->load->view('qa_collmon/daily_sqc_report_html',$data);
			break;
			
			case 'showexcel':
				$this->load->library('appexcel');
				$this->load->view('qa_collmon/daily_sqc_report_excel',$data);
			break;
			default:
			exit("Not available");
		}
		
	}
	
	
}