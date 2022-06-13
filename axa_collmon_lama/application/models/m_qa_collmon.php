<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class m_qa_collmon extends CI_Model
{	
	
	private $where = array();
	
	function __construct()
    {
    	parent::__construct();
    }
	public function ResetWhere()
	{
		$this->where=array();
	}
	public function SetWhere($where)
	{
		$this->where=$where;
		return $this;
	}
	public function _CollMonForm()
	{
		$coll_mon_form=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("b.id_form_collmon,
				a.id_coll_product,a.product_category_id,a.desc_coll_product,a.score_review_func,
				c.CollGroupId,d.CollGroupName,d.add_remarks,
				c.SubGroupId,c.SubGroupDesc,c.is_mandatory,c.is_session,
				e.id_answer_collmon,e.label_answer,e.is_default as default_answer,e.value_answer,
				f.input_type_func,f.input_from_user,f.is_readonly"
			);
			$this->db->from("coll_category_product a");
			$this->db->join("coll_form_collmon b","a.id_coll_product=b.id_coll_product","INNER");
			$this->db->join("coll_subgroup_collmon c","b.SubGroupId=c.SubGroupId","INNER");
			$this->db->join("coll_group_collmon d","c.CollGroupId=d.CollGroupId","INNER");
			$this->db->join("coll_answer_collmon e","b.id_answer_collmon=e.id_answer_collmon","INNER");
			$this->db->join("t_lk_input_type f","e.id_input_type=f.id_input_type","INNER");
			$this->db->where("a.product_category_id", $this->where['product_category_id'] );
			if(isset($this->where['subcategoryflag']))
			{
				$this->db->where("c.SubGroupFlags", $this->where['subcategoryflag'] );
			}
			$this->db->order_by("c.SubGroupId"); 
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$coll_mon_form['header']['name'] = $row->desc_coll_product;
					$coll_mon_form['header']['id'] = $row->id_coll_product;
					$coll_mon_form['header']['func'][$row->product_category_id] = $row->score_review_func;
					$coll_mon_form['category'][$row->CollGroupId] = $row->CollGroupName;
					$coll_mon_form['add_remark_category'][$row->CollGroupId] = $row->add_remarks;
					$coll_mon_form['sub_category'][$row->CollGroupId][$row->SubGroupId] = $row->SubGroupDesc;
					$coll_mon_form['sub_mandat'][$row->CollGroupId][$row->SubGroupId] = $row->is_mandatory;
					$coll_mon_form['sub_category_input'][$row->CollGroupId][$row->SubGroupId] = $row->input_from_user;
					$coll_mon_form['input_func'][$row->CollGroupId][$row->SubGroupId] = $row->input_type_func;
					$coll_mon_form['answer_label'][$row->CollGroupId][$row->SubGroupId][$row->id_form_collmon] = $row->label_answer;
					$coll_mon_form['answer_value'][$row->id_form_collmon] = $row->value_answer;
					$coll_mon_form['answer_is_input'][$row->SubGroupId] = $row->input_from_user;
					$coll_mon_form['answer_is_readonly'][$row->SubGroupId] = $row->is_readonly;
					$coll_mon_form['answer_is_session'][$row->SubGroupId] = $row->is_session;
					if($row->default_answer)
					{
						$coll_mon_form['answer_default'][$row->CollGroupId][$row->SubGroupId] = $row->id_form_collmon;
					}
				}
			}
		}
		// echo $this->db->last_query();
		$this->ResetWhere();
		return $coll_mon_form;
	}
	
	public function CountSavedDataScoring()
	{
		$count_row = 0;
		if(isset($this->where['CustomerId']))
		{
			$this->db->where('a.CustomerId', $this->where['CustomerId']);
			$this->db->from('coll_result_collmon a');
			$count_row =  $this->db->count_all_results();
		}
		return $count_row;
	}
	public function GetSavedData()
	{
		$saved=array();
		if(isset($this->where['CustomerId']))
		{
			$this->db->select("a.id_result_mon,c.SubGroupId,d.CollGroupId,
				b.id_result_form,b.id_form_collmon,
				e.id_fom_input,e.input_text,
				f.id_remarks_group,f.remarks_group"
			);
			$this->db->from("coll_result_collmon a");
			$this->db->join("coll_result_form b","a.id_result_mon=b.id_result_mon","INNER");
			$this->db->join("coll_form_collmon c","b.id_form_collmon=c.id_form_collmon","INNER");
			$this->db->join("coll_subgroup_collmon d","c.SubGroupId=d.SubGroupId","INNER");
			$this->db->join("coll_form_input e","b.id_result_form=e.id_result_form","LEFT");
			$this->db->join("coll_group_remarks f","a.id_result_mon=f.id_result_mon AND d.CollGroupId=f.CollGroupId","LEFT");
			$this->db->where("a.CustomerId", $this->where['CustomerId'] );
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					// var_dump($row->id_fom_input);
					$saved['IdMonitoring'] = $row->id_result_mon;
					$saved['coll_result_form'][$row->SubGroupId]['id']=$row->id_result_form;
					$saved['coll_result_form'][$row->SubGroupId]['value']=$row->id_form_collmon;
					if(!is_null($row->id_fom_input))
					{
						$saved['coll_form_input'][$row->SubGroupId]['id']=$row->id_fom_input;
						$saved['coll_form_input'][$row->SubGroupId]['value']=$row->input_text;
					}
					if(!is_null($row->id_remarks_group))
					{
						$saved['coll_group_remarks'][$row->CollGroupId]['id']=$row->id_remarks_group;
						$saved['coll_group_remarks'][$row->CollGroupId]['value']=$row->remarks_group;
					}
					
				}
				$this->db->select("a.id_form_remark,a.field_remark,a.form_remarks");
				$this->db->from("coll_form_remark a");
				$this->db->where("a.id_result_mon", $saved['IdMonitoring'] );
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					foreach ($query->result() as $row)
					{
						if(!is_null($row->id_form_remark))
						{
							$saved['coll_form_remark'][$row->field_remark]['id']=$row->id_form_remark;
							$saved['coll_form_remark'][$row->field_remark]['value']=$row->form_remarks;
						}
					}
				}
				
				$this->db->select("a.id_calculation_result,a.score,a.id_calculation");
				$this->db->from("coll_calculation_result a");
				$this->db->where("a.id_result_mon", $saved['IdMonitoring'] );
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					foreach ($query->result() as $row)
					{
						$saved['coll_calculation_result'][$row->id_calculation]['id']=$row->id_calculation_result;
						$saved['coll_calculation_result'][$row->id_calculation]['value']=$row->score;
					}
				}
			}
			
		}
		// echo $this->db->last_query();
		$this->ResetWhere();
		return $saved;
	}
	public function update_score()
	{
		$success=true;
		$delete_insert=array();
		$success_step=0;
		$form = $this->setWhere(
			array(
				'product_category_id'=>$this->session->userdata('ProductGrupId')
			)
		)->_CollMonForm();
		$SavedData=$this->setWhere(
			array(
				'CustomerId'=>$this->session->userdata('CustomerId')
			)
		)->GetSavedData();
		$customer_info = $this->setWhere(
			array(
				'CustomerId'=>$this->session->userdata('CustomerId'),
				'product_category_id'=>$this->session->userdata('ProductGrupId')
			)
		)->CustomerInfo();
		$recording=$this->setWhere(
			array(
				'CustomerId'=>$this->session->userdata('CustomerId')
			)
		)->GetDurationRecording();
		
		$this->db->set('rec_duration',$recording['sec']);
		$this->db->set('date_selling',$customer_info[0]['PolicySalesDate']);
		$this->db->where('id_result_mon', $SavedData['IdMonitoring'] );
		$this->db->update('coll_result_collmon');
		
		// echo "<pre>";
		// print_r($SavedData);
		// echo "</pre>";
		foreach($form['sub_category'] as $category_id=>$sub_category)
		{
			if($form['add_remark_category'][$category_id])
			{
				// $SavedData['coll_group_remarks'][]
				// $this->input->post('remark_'.$category_id)
				if(isset($SavedData['coll_group_remarks'][$category_id]['id']))
				{
					
					if(strcmp($SavedData['coll_group_remarks'][$category_id]['value'],
						$this->input->post('remark_'.$category_id))!==0)
					{
						$text = $this->input->post('remark_'.$category_id);
						$this->db->set('remarks_group',$text);
						$this->db->where('id_remarks_group', $SavedData['coll_group_remarks'][$category_id]['id']);
						$this->db->update('coll_group_remarks');
					}
					 
				}
				else
				{
					if($this->input->post('remark_'.$category_id)!="")
					{
						$this->db->insert('coll_group_remarks',
							array(
								'id_result_mon'=>$SavedData['IdMonitoring'],
								'CollGroupId'=>$category_id,
								'remarks_group'=>$this->input->post('remark_'.$category_id)
							)
						);
					}
					if ($this->db->affected_rows() > 0)
					{
						$delete_insert['coll_group_remarks'][$success_step]=$this->db->insert_id();
					}
					else
					{
						$success=false;
						break;
					}
				}
			}
			foreach($sub_category as $sub_category_id=>$sub_category_name)
			{
				$name="qa_quest_".$sub_category_id;
				$input_type = $form['sub_category_input'][$category_id][$sub_category_id];
				if($input_type)
				{
					$text = $this->input->post($name);
					if(isset($SavedData['coll_result_form'][$sub_category_id]['id']))
					{
						if(strcmp($SavedData['coll_form_input'][$sub_category_id]['value'],
							$this->input->post($name)!==0))
						{
							$this->db->set('input_text',$text);
							$this->db->where('id_fom_input', $SavedData['coll_form_input'][$sub_category_id]['id']);
							$this->db->update('coll_form_input');
						}
					}
					else
					{
						$form_id = $form['answer_label'][$category_id][$sub_category_id];
						foreach($form_id as $id_form_collmon => $text)
						{
							if($this->input->post($name) or $this->input->post($name)!="")
							{
								$this->db->insert('coll_result_form',
									array(
										'id_result_mon'=>$SavedData['IdMonitoring'],
										'id_form_collmon'=>$id_form_collmon
									)
								);
								if ($this->db->affected_rows() > 0)
								{
									$id_result_form = $this->db->insert_id();
									
									$this->db->insert('coll_form_input',
										array(
											'id_result_form'=>$id_result_form,
											'input_text'=>$this->input->post($name)
										)
									);
									if ($this->db->affected_rows() > 0)
									{
										$delete_insert['coll_form_input'][$success_step]=$this->db->insert_id();
									}
									else
									{
										$success=false;
										break 3;
									}
								}
							}
							$success_step++;
						}
					}
				}
				else
				{
					if(isset($SavedData['coll_result_form'][$sub_category_id]['id']))
					{
						$this->db->set('id_form_collmon',$this->input->post($name));
						$this->db->where('id_result_form', $SavedData['coll_result_form'][$sub_category_id]['id']);
						$this->db->update('coll_result_form');
					}
					else
					{
						$this->db->insert('coll_result_form',
							array(
								'id_result_mon'=>$SavedData['IdMonitoring'],
								'id_form_collmon'=>$this->input->post($name)
							)
						);
						if ($this->db->affected_rows() > 0)
						{
							$delete_insert['coll_result_form'][$success_step]=$this->db->insert_id();
						}
						else
						{
							$success=false;
							break 2;
						}
					}
					
				}
				$success_step++;
			}
			$success_step++;
		}
		if($success===false)
		{
			return $success;
		}
		$score_review = $this->$form['header']['func'][$this->session->userdata('ProductGrupId')]();
		$calculation_score = $this->setWhere(
			array('product_category_id'=>$this->session->userdata('ProductGrupId'))
		)->GetCalculationColomn();
		
		foreach( $calculation_score as $id_calculation=> $cal_desc)
		{
			if(isset($SavedData['coll_calculation_result'][$id_calculation]['id']))
			{
				if(strcmp($SavedData['coll_calculation_result'][$id_calculation]['value'],
					$score_review['score_'.$id_calculation]!==0))
				{
					$this->db->set('score',$score_review['score_'.$id_calculation]);
					$this->db->where('id_calculation_result', $SavedData['coll_calculation_result'][$id_calculation]['id']);
					$this->db->update('coll_calculation_result');
				}
			}
			else
			{
				$this->db->insert('coll_calculation_result',
					array(
						'id_result_mon'=>$SavedData['IdMonitoring'],
						'id_calculation'=>$id_calculation,
						'score'=>$score_review['score_'.$id_calculation]
					)
				);
				if ($this->db->affected_rows() > 0)
				{
					$delete_insert['coll_calculation_result'][$success_step]=$this->db->insert_id();
				}
				else
				{
					$success=false;
					break;
				}
			}
			$success_step++;
		}
		if($success===false)
		{
			return $success;
		}
		for($i=1;$i<2;$i++)
		{
			if(isset($SavedData['coll_form_remark'][$i]['id']))
			{
				if(strcmp($SavedData['coll_form_remark'][$i]['value'],
					$this->input->post("report_remaks_".$i)!==0))
				{
					$this->db->set('form_remarks',$this->input->post("report_remaks_".$i));
					$this->db->where('id_form_remark', $SavedData['coll_form_remark'][$i]['id']);
					$this->db->update('coll_form_remark');
				}
			}
			else
			{
				if($this->input->post("report_remaks_".$i) or $this->input->post("report_remaks_".$i)!="")
				{
					$this->db->insert('coll_form_remark',
						array(
							'id_result_mon'=>$SavedData['IdMonitoring'],
							'field_remark'=>$i,
							'form_remarks'=>$this->input->post("report_remaks_".$i)
						)
					);
					if ($this->db->affected_rows() > 0)
					{
						$delete_insert['coll_form_remark'][$success_step]=$this->db->insert_id();
					}
					else
					{
						$success=false;
						break;
					}
				}
			}
			$success_step++;
		}
		return $success;
		
	}
	
	public function insert_score()
	{
		$form = $this->setWhere(
			array(
				'product_category_id'=>$this->session->userdata('ProductGrupId'),
				'subcategoryflag'=>'1'
			)
		)->_CollMonForm();
		$customer_info = $this->setWhere(
			array(
				'CustomerId'=>$this->session->userdata('CustomerId'),
				'product_category_id'=>$this->session->userdata('ProductGrupId')
			)
		)->CustomerInfo();
		$recording=$this->setWhere(
			array(
				'CustomerId'=>$this->session->userdata('CustomerId')
			)
		)->GetDurationRecording();
		$delete=array();
		$success=true;
		$success_step=0;
		// echo "<pre>";
		// print_r($form);
		// echo "</pre>";
		$this->db->insert('coll_result_collmon',
			array(
				'CustomerId'=> $this->session->userdata('CustomerId'),
				'id_coll_product'=>$form['header']['id'],
				'score_by'=>$this->session->userdata('UserId'),
				'collmon_datets'=>date('Y-m-d H:i:s'),
				'date_selling'=>$customer_info[0]['PolicySalesDate'],
				'rec_duration'=>$recording['sec']
			)
		); 
		
		if ($this->db->affected_rows() > 0)
		{
			$id_result_collmon = $this->db->insert_id();
			$this->db->insert('coll_static_input',
				array(
					'id_result_mon'=> $id_result_collmon,
					'static_note'=>$this->input->post('note_static'),
					'static_status_system'=>$this->input->post('status_system_static'),
					'static_plan'=>$this->input->post('plan_static')					
				)
			); 
			$delete['coll_result_collmon'][$success_step]=$id_result_collmon;
			foreach($form['sub_category'] as $category_id=>$sub_category)
			{
				// var_dump($form['add_remark_category'][$category_id]);
				$success_step++;
				if($form['add_remark_category'][$category_id])
				{
					if($this->input->post('remark_'.$category_id)!="")
					{
						$this->db->insert('coll_group_remarks',
							array(
								'id_result_mon'=>$id_result_collmon,
								'CollGroupId'=>$category_id,
								'remarks_group'=>$this->input->post('remark_'.$category_id)
							)
						);
						if ($this->db->affected_rows() > 0)
						{
							$delete['coll_group_remarks'][$success_step]=$this->db->insert_id();
						}
						else
						{
							$success=false;
							break;
						}
					}
				}
				foreach($sub_category as $sub_category_id=>$sub_category_name)
				{
					$name="qa_quest_".$sub_category_id;
					$input_type = $form['sub_category_input'][$category_id][$sub_category_id];
					$success_step++;
					if($input_type)
					{
						$form_id = $form['answer_label'][$category_id][$sub_category_id];
						foreach($form_id as $id_form_collmon => $text)
						{
							if($this->input->post($name) or $this->input->post($name)!="")
							{
								$this->db->insert('coll_result_form',
									array(
										'id_result_mon'=>$id_result_collmon,
										'id_form_collmon'=>$id_form_collmon
									)
								);
								if ($this->db->affected_rows() > 0)
								{
									
									$id_result_form = $this->db->insert_id();
									$delete['coll_result_form'][$success_step]=$id_result_form;
									
										$this->db->insert('coll_form_input',
											array(
												'id_result_form'=>$id_result_form,
												'input_text'=>$this->input->post($name)
											)
										);
										if ($this->db->affected_rows() > 0)
										{
											$delete['coll_form_input'][$success_step]=$this->db->insert_id();
										}
										else
										{
											$success=false;
											break 3;
										}
									
								}
								else
								{
									$success=false;
									break 3;
								}
							}
							$success_step++;
						}
					}
					else
					{
						$this->db->insert('coll_result_form',
							array(
								'id_result_mon'=>$id_result_collmon,
								'id_form_collmon'=>$this->input->post($name)
							)
						);
						if ($this->db->affected_rows() > 0)
						{
							$delete['coll_result_form'][$success_step]=$this->db->insert_id();
						}
						else
						{
							$success=false;
							break 2;
						}
					}
				}
			}
			if($success===false)
			{
				return $success;
			}
			for($i=1;$i<2;$i++)
			{
				$success_step++;
				if($this->input->post("report_remaks_".$i) or $this->input->post("report_remaks_".$i)!="")
				{
					$this->db->insert('coll_form_remark',
						array(
							'id_result_mon'=>$id_result_collmon,
							'field_remark'=>$i,
							'form_remarks'=>$this->input->post("report_remaks_".$i)
						)
					);
					if ($this->db->affected_rows() > 0)
					{
						$delete['coll_form_remark'][$success_step]=$this->db->insert_id();
					}
					else
					{
						$success=false;
						break;
					}
				}
			}
			if($success===false)
			{
				return $success;
			}
			// echo $form['header']['func'][$this->session->userdata('ProductGrupId')];
			$score_review = $this->$form['header']['func'][$this->session->userdata('ProductGrupId')]();
			$calculation_score = $this->setWhere(
				array('product_category_id'=>$this->session->userdata('ProductGrupId'))
			)->GetCalculationColomn();
			foreach($calculation_score as $id_calculation=> $score_text)
			{
				$success_step++;
				$this->db->insert('coll_calculation_result',
					array(
						'id_result_mon'=>$id_result_collmon,
						'id_calculation'=>$id_calculation,
						'score'=>$score_review['score_'.$id_calculation]
					)
				);
				if ($this->db->affected_rows() > 0)
				{
					$delete['coll_calculation_result'][$success_step]=$this->db->insert_id();
				}
				else
				{
					$success=false;
					break;
				}
			}
		}
		else
		{
			$success=false;
		}
		return $success;
		// echo "<pre>";
		// print_r($delete);
		// echo "</pre>";
		
	}
	
	public function GetCalculationColomn()
	{
		$calculation_colomn=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.id_calculation,c.CollGroupName");
			$this->db->from("coll_calculation_collmon a");
			$this->db->join("coll_category_product b","a.id_coll_product=b.id_coll_product","INNER");
			$this->db->join("coll_group_collmon c","a.CollGroupId=c.CollGroupId","INNER");
			$this->db->where("b.product_category_id", $this->where['product_category_id'] );
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$calculation_colomn[$row->id_calculation]=$row->CollGroupName;
				}
			}
		}
		$this->ResetWhere();
		return $calculation_colomn;
	}
	
	public function CustomerInfo()
	{
		$Customer = array();
		if(isset($this->where['CustomerId']))
		{
		
			$this->db->select("a.CustomerId,e.product_category_id,a.CustomerFirstName,b.CampaignName,e.ProductCode, e.ProductName,
				DATE_FORMAT(d.PolicySalesDate,'%d-%m-%Y') AS SELLINGDATE, d.PolicySalesDate, f.id,
				a.CallReasonQue",FALSE);
			$this->db->from("t_gn_customer a");
			$this->db->join("t_gn_campaign b","a.CampaignId=b.CampaignId","INNER");
			$this->db->join("t_gn_policyautogen c","a.CustomerId=c.CustomerId","INNER");
			$this->db->join("t_gn_policy d","c.PolicyNumber=d.PolicyNumber","INNER");
			$this->db->join("t_gn_product e","e.ProductId=c.ProductId","INNER");
			$this->db->join("tms_agent f","a.SellerId = f.UserId","INNER");
			$this->db->where("a.CustomerId", $this->where['CustomerId']);
			if(isset($this->where['product_category_id']))
			{
				$this->db->where("e.product_category_id", $this->where['product_category_id']);
			}
			
			$this->db->group_by(array("a.CustomerId", "e.product_category_id")); 
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				$Customer = $query->result_array();
				// foreach ($query->result() as $row)
				// {
					// $group[$row->id_grup_calculate] = $row->SubGroupId;
				// }
			}
		}
		$this->ResetWhere();
		return $Customer;
	}
	
	public function GetDurationRecording()
	{
		$duration['sec']=0;
		$duration['time_format']="00:00:00";
		if(isset($this->where['CustomerId']))
		{
			$this->db->select("sum(a.duration) as durasi_sec, sec_to_time(sum(a.duration)) as duration_time");
			$this->db->from("cc_recording a");
			$this->db->where("a.assignment_data", $this->where['CustomerId']);
			$this->db->group_by(array("a.assignment_data")); 
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$duration['sec'] = $row->durasi_sec;
					$duration['time_format'] = $row->duration_time;
				}
				
			}
		}
		$this->ResetWhere();
		return $duration;
	}
	
	private function grup_calculate()
	{
		$formula=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.id_coll_product,a.product_category_id,
				b.id_calculation,f.CollGroupName,
				c.id_grup_calculat,c.SubGroupId,
				d.id_segment_desc,d.segment_code,
				e.case_func"
			);
			$this->db->from("coll_category_product a");
			$this->db->join("coll_calculation_collmon b","a.id_coll_product=b.id_coll_product","INNER");
			$this->db->join("coll_grup_calculat c","b.id_calculation=c.id_calculation","INNER");
			$this->db->join("coll_segment_desc d","c.id_segment_desc=d.id_segment_desc","INNER");
			$this->db->join("t_lk_calculate_method e","d.id_calculate_method=e.id_calculate_method","INNER");
			$this->db->join("coll_group_collmon f","b.CollGroupId=f.CollGroupId","INNER");
			$this->db->where("a.product_category_id", $this->where['product_category_id']);
			$this->db->group_by(array("b.id_calculation","d.id_segment_desc","c.SubGroupId")); 
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$formula['segment'][$row->id_calculation][$row->id_segment_desc][$row->id_grup_calculat] = $row->SubGroupId;
					$formula['case_func'][$row->id_segment_desc] = $row->case_func;
				}
				$this->db->select("*");
				$this->db->from("coll_segment_param a");
				$this->db->where_in("a.id_segment_desc",array_keys($formula['case_func']) );
				$query = $this->db->get();
				if ($query->num_rows() > 0)
				{
					foreach ($query->result() as $row)
					{
						$param['param_value'][$row->id_segment_desc][$row->param_code] = $row->param_value;
						$param['param_type'][$row->id_segment_desc][$row->param_code] = $row->id_param_type;
					}
					$formula['param_case']= $param;
				}
			}
		}
		$this->ResetWhere();
		return $formula;
		
	}
	public function score_ape()
	{
		return $this->score_fpa();
	}
	public function score_fpa()
	{
		/*
			SELECT * FROM coll_grup_calculate a 
			LEFT JOIN coll_segment_calculate b ON a.id_grup_calculate=b.id_grup_calculate
			LEFT JOIN t_lk_calculate_method c ON b.id_calculate_method=c.id_calculate_method
			INNER JOIN coll_subgroup_collmon d ON d.SubGroupId=a.SubGroupId
			INNER JOIN coll_calculation_collmon e ON a.id_calculation=e.id_calculation
			WHERE a.id_calculation = 1;
		*/
		// $this->where=array();
		// $group_score_tfc = array();
		// $group_score_tfc = array();
		// $this->where['id_calculation'] = "1";
		
		// $group_score_tfc = $this->get_group_calculate();
		
		// echo "<pre>";
		// print_r();
		// echo "</pre>";
		
		// $score['score_tfc']=0;
		// $score['score_sqc']=0;
		// $this->load->library('Scoring/CalculatorScoring');
		// $form = $this->setWhere(
			// array(
				// 'product_category_id'=>$this->session->userdata('ProductGrupId')
			// )
		// )->_CollMonForm();
		// $customer_info = $this->setWhere(
			// array(
				// 'CustomerId'=>$this->session->userdata('CustomerId'),
				// 'product_category_id'=>$this->session->userdata('ProductGrupId')
			// )
		// )->CustomerInfo();
		// $grup_criteria=array(11,12,13,14,15,16);
		// $yes_cc = 19;
		// $spelling_email= 20;
		// $score=0;
		// $score = CalculatorScoring::getInstance()->ResetCalculator();
		// foreach($grup_criteria as $index=>$val)
		// {
			// $input = $this->input->post(
				// "qa_quest_".$val
			// );
			// $score->SetPoin(
				// $form['answer_value'][$input]
			// );
		// }
		// $score->CalculateCriteriaPoin(6,4,1);
		// $input=$this->input->post("qa_quest_".$yes_cc);
		// $score->AddScore($form['answer_value'][$input]);
		// $input=$this->input->post("qa_quest_".$spelling_email);
		// $score->PoinEqualString($input,$customer_info[0]['PayerEmail']);
		// $score_review['score_tfc']= $score->GetScore();
		
		// $score->ResetCalculator();
		// $input=$this->input->post("qa_quest_".$yes_cc);
		// $score->AddScore($form['answer_value'][$input]);
		// $input=$this->input->post("qa_quest_".$spelling_email);
		// $score->PoinEqualString($input,$customer_info[0]['PayerEmail']);
		// $score_review['score_sqc']=$score->GetScore();
		$this->load->library('Scoring/CalculatorScoring');
		$this->load->library('Scoring/ManageSegmentParam');
		$param=array();
		$ManagerSegmentParam= ManageSegmentParam::getInstance();
		$score = CalculatorScoring::getInstance()->ResetCalculator();
		$point_score = $this->setWhere(
			array(
				'product_category_id'=>$this->session->userdata('ProductGrupId')
			)
		)->grup_calculate();
		$form = $this->setWhere(
			array(
				'product_category_id'=>$this->session->userdata('ProductGrupId'),
				'subcategoryflag'=>'1'
			)
		)->_CollMonForm();
		
		// echo "<pre>";
		// print_r($point_score);
		// echo "</pre>";
		$param=$ManagerSegmentParam->ParsingParameterSegment($point_score['param_case']);
		// echo "<pre>";
		// print_r($param);
		// echo "</pre>";
		foreach($point_score['segment'] as $id_calculation=>$segment)
		{
			$score_review['score_'.$id_calculation] =0;
			// echo 'score_'.$id_calculation."\n";
			foreach($segment as $id_segment_desc=>$group_segment)
			{
				// echo $point_score['case_func'][$id_segment_desc]."\n";
				
				// if(isset($point_score['param_case']['param_value'][$id_segment_desc]))
				// {
					// $param=$point_score['param_case']['param_value'][$id_segment_desc];
				// }
				// echo "jumlah grup = ".count($group_segment)."\n";
				//collect input
				$answer=array();
				foreach($group_segment as $id_grup_calculat=>$SubGroupId)
				{
					$input = $this->input->post(
						"qa_quest_".$SubGroupId
					);
					// echo $input."\n";
					if($form['answer_is_input'][$SubGroupId])
					{
						if(count($group_segment)>1)
						{
							$answer[$id_segment_desc]["qa_quest_".$SubGroupId]= $input;
						}
						else
						{	
							$answer[$id_segment_desc]= $input;
						}
					}
					else
					{
						if(count($group_segment)>1)
						{
							$answer[$id_segment_desc]["qa_quest_".$SubGroupId]=$form['answer_value'][$input];
						}
						else
						{	
							$answer[$id_segment_desc]=$form['answer_value'][$input];
						}
						
					}
				}
				
				
				// echo "<pre>";
				// echo($point_score['case_func'][$id_segment_desc]);
				// echo "</pre>";
				// echo "<pre>";
				// print_r($answer[$id_segment_desc]);
				// echo "</pre>";
				switch($point_score['case_func'][$id_segment_desc])
				{
					case 'CalculateCriteriaPoin':
						$score->SetPoin(
							$answer[$id_segment_desc]
						)
						->SetParameter($param[$id_segment_desc])
						->CalculateCriteriaPoin();
					break;
					case 'AddScore':
						$score->AddScore($answer[$id_segment_desc]);
					break;
					case 'PoinEqualString':
						$score->SetParameter($param[$id_segment_desc])->PoinEqualString($answer[$id_segment_desc]);
					break;
					default :
					$score_review['score_'.$id_calculation] = 0;
				}
				
			}
			$score_review['score_'.$id_calculation] = $score->GetScore();
			$score->ResetCalculator();
		}
		return $score_review;
	}
	
	public function GetHiddenGroupByStatusSQC()
	{
		$CategoryStatus=array();
		if(isset($this->where['CollGroupId']))
		{
			$this->db->select("a.id_group_qastatus,a.CollGroupId,a.ApproveId");
			$this->db->from("coll_group_qastatus a");
			if(is_array($this->where['CollGroupId']))
			{
				$this->db->where_in("a.CollGroupId",$this->where['CollGroupId']);
			}
			else
			{
				$this->db->where("a.CollGroupId",$this->where['CollGroupId']);
			}
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$CategoryStatus[$row->ApproveId][$row->id_group_qastatus]= $row->CollGroupId;
				}
			}
			
		}
		$this->ResetWhere();
		return $CategoryStatus;
	}
	
	public function GetPolicyForm()
	{
		$policy=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.CustomerId,d.InsuredId,
				d.PremiumGroupId,f.PolicyNumber,
				DATE_FORMAT(f.PolicySalesDate, '%d-%m-%Y') AS EFFDate,
				a.rec_duration,
				d.InsuredFirstName,
				DATE_FORMAT(d.InsuredDOB, '%d-%m-%Y') AS InsuredDOB,
				f.Premi,
				i.ProductName,
				g.CampaignName,
				'-' as prospect,
				c.PayerEmail,
				'-' as waktu_analisis,
				'-' as status_report,
				CONCAT(j.id,'-',j.full_name)AS TM,
				'-' as eff_date_comp,
				k.full_name as spv,
				l.full_name as mgr,
				m.full_name as qc,
				IF(c.PayerHomePhoneNum IS NULL,
					IF(c.PayerMobilePhoneNum IS NULL,c.PayerOfficePhoneNum,
						c.PayerMobilePhoneNum),
				c.PayerHomePhoneNum) AS PhoneNum,
				CONCAT(c.PayerCreditCardNum,'( ',c.PayerCreditCardExpDate,' )') as PayerCreditCardNum",
			FALSE);
			$this->db->from("coll_result_collmon a");
			$this->db->join("t_gn_customer b","a.CustomerId=b.CustomerId","INNER");
			$this->db->join("t_gn_payer c","a.CustomerId=c.CustomerId","INNER");
			$this->db->join("t_gn_insured d","b.CustomerId=d.CustomerId","INNER");
			$this->db->join("t_lk_gender e","d.GenderId = e.GenderId","INNER");
			$this->db->join("t_gn_policy f","d.PolicyId = f.PolicyId","INNER");
			$this->db->join("t_gn_campaign g","b.CampaignId = g.CampaignId","INNER");
			$this->db->join("t_gn_productplan h","f.ProductPlanId=h.ProductPlanId","INNER");
			$this->db->join("t_gn_product i","h.ProductId=i.ProductId","INNER");
			$this->db->join("tms_agent j","b.SellerId=j.UserId","INNER");
			$this->db->join("tms_agent k","j.spv_id= k.UserId","INNER");
			$this->db->join("tms_agent l","k.mgr_id = l.UserId","INNER");
			$this->db->join("tms_agent m","a.score_by=m.UserId","INNER");
			$this->db->where("i.product_category_id",$this->where['product_category_id']);
			if(isset($this->where['colmon_date']))
			{
				$this->db->where("a.collmon_datets >=",$this->where['colmon_date']['start']." 00:00:00");
				$this->db->where("a.collmon_datets <=",$this->where['colmon_date']['end']." 23:59:59");
			}
			
			if(isset($this->where['selling_date']))
			{
				$this->db->where("a.date_selling >=",$this->where['selling_date']['start']);
				$this->db->where("a.date_selling <=",$this->where['selling_date']['end']);
			}
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					/*
						$policy['PolicyNumber'][$row->CustomerId][$row->InsuredId]= $row->PolicyNumber;
						$policy['EFFDate'][$row->CustomerId][$row->InsuredId]= $row->EFFDate;
						$policy['rec_duration'][$row->CustomerId][$row->InsuredId]= $row->rec_duration;
						$policy['InsuredFirstName'][$row->CustomerId][$row->InsuredId]= $row->InsuredFirstName;
						$policy['InsuredDOB'][$row->CustomerId][$row->InsuredId]= $row->InsuredDOB;
						$policy['Premi'][$row->CustomerId][$row->InsuredId]= (int) $row->Premi;
						$policy['ProductName'][$row->CustomerId][$row->InsuredId]= $row->ProductName;
						$policy['CampaignName'][$row->CustomerId][$row->InsuredId]= $row->CampaignName;
						$policy['prospect'][$row->CustomerId][$row->InsuredId]= $row->prospect;
						$policy['PayerEmail'][$row->CustomerId][$row->InsuredId]= $row->PayerEmail;
						$policy['waktu_analisis'][$row->CustomerId][$row->InsuredId]= $row->waktu_analisis;
						$policy['status_report'][$row->CustomerId][$row->InsuredId]= $row->status_report;
						$policy['TM'][$row->CustomerId][$row->InsuredId]= $row->TM;
						$policy['eff_date_comp'][$row->CustomerId][$row->InsuredId]= $row->eff_date_comp;
						$policy['spv'][$row->CustomerId][$row->InsuredId]= $row->spv;
						$policy['mgr'][$row->CustomerId][$row->InsuredId]= $row->mgr;
						$policy['qc'][$row->CustomerId][$row->InsuredId]= $row->qc;
						$policy['PhoneNum'][$row->CustomerId][$row->InsuredId]= $row->PhoneNum;
					*/
					$policy[$row->CustomerId][$row->InsuredId]['PolicyNumber']= $row->PolicyNumber;
					$policy[$row->CustomerId][$row->InsuredId]['EFFDate']= $row->EFFDate;
					$policy[$row->CustomerId][$row->InsuredId]['rec_duration']= $row->rec_duration;
					$policy[$row->CustomerId][$row->InsuredId]['InsuredFirstName']= $row->InsuredFirstName;
					$policy[$row->CustomerId][$row->InsuredId]['InsuredDOB']= $row->InsuredDOB;
					$policy[$row->CustomerId][$row->InsuredId]['Premi']= (int) $row->Premi;
					$policy[$row->CustomerId][$row->InsuredId]['ProductName']= $row->ProductName;
					$policy[$row->CustomerId][$row->InsuredId]['CampaignName']= $row->CampaignName;
					$policy[$row->CustomerId][$row->InsuredId]['prospect']= $row->prospect;
					$policy[$row->CustomerId][$row->InsuredId]['PayerEmail']= $row->PayerEmail;
					$policy[$row->CustomerId][$row->InsuredId]['waktu_analisis']= $row->waktu_analisis;
					$policy[$row->CustomerId][$row->InsuredId]['status_report']= $row->status_report;
					$policy[$row->CustomerId][$row->InsuredId]['TM']= $row->TM;
					$policy[$row->CustomerId][$row->InsuredId]['eff_date_comp']= $row->eff_date_comp;
					$policy[$row->CustomerId][$row->InsuredId]['spv']= $row->spv;
					$policy[$row->CustomerId][$row->InsuredId]['mgr']= $row->mgr;
					$policy[$row->CustomerId][$row->InsuredId]['qc']= $row->qc;
					$policy[$row->CustomerId][$row->InsuredId]['PhoneNum']= $row->PhoneNum;
					$policy[$row->CustomerId][$row->InsuredId]['PayerCreditCardNum']= $row->PayerCreditCardNum;
				}
			}
			
		}
		// echo $this->db->last_query();
		$this->ResetWhere();
		return $policy;
	}
	public function GetCollmonResult()
	{
		$colmon_result=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.id_result_mon,a.CustomerId,
				a.id_coll_product,k.product_category_id,
				g.CollGroupId,
				f.SubGroupId,
				IF(e.input_from_user = 1,h.input_text,d.label_answer) as answer",
			FALSE);
			$this->db->from("coll_result_collmon a");
			$this->db->join("coll_result_form b","a.id_result_mon=b.id_result_mon","INNER");
			$this->db->join("coll_form_collmon c","b.id_form_collmon=c.id_form_collmon","INNER");
			$this->db->join("coll_answer_collmon d","c.id_answer_collmon=d.id_answer_collmon","INNER");
			$this->db->join("t_lk_input_type e","d.id_input_type=e.id_input_type","INNER");
			$this->db->join("coll_subgroup_collmon f","c.SubGroupId=f.SubGroupId","INNER");
			$this->db->join("coll_group_collmon g","f.CollGroupId=g.CollGroupId","INNER");
			$this->db->join("coll_form_input h","b.id_result_form=h.id_result_form","LEFT");
			$this->db->join("coll_category_product k","a.id_coll_product=k.id_coll_product","INNER");
			$this->db->where("k.product_category_id",$this->where['product_category_id']);
			if(isset($this->where['colmon_date']))
			{
				$this->db->where("a.collmon_datets >=",$this->where['colmon_date']['start']." 00:00:00");
				$this->db->where("a.collmon_datets <=",$this->where['colmon_date']['end']." 23:59:59");
			}
			
			if(isset($this->where['selling_date']))
			{
				$this->db->where("a.date_selling >=",$this->where['selling_date']['start']);
				$this->db->where("a.date_selling <=",$this->where['selling_date']['end']);
			}
			$query = $this->db->get();
			
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$colmon_result[$row->CustomerId][$row->CollGroupId][$row->SubGroupId]=$row->answer;
				}
			}
		}
		$this->ResetWhere();
		return $colmon_result;
	}
	
	public function GetScoreResult()
	{
		$score_result=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.CustomerId,d.CollGroupId,c.score");
			$this->db->from("coll_result_collmon a");
			$this->db->join("coll_category_product b","a.id_coll_product=b.id_coll_product","INNER");
			$this->db->join("coll_calculation_result c","a.id_result_mon=c.id_result_mon","INNER");
			$this->db->join("coll_calculation_collmon d","c.id_calculation=d.id_calculation","INNER");
			$this->db->where("b.product_category_id",$this->where['product_category_id']);
			if(isset($this->where['colmon_date']))
			{
				$this->db->where("a.collmon_datets >=",$this->where['colmon_date']['start']." 00:00:00");
				$this->db->where("a.collmon_datets <=",$this->where['colmon_date']['end']." 23:59:59");
			}
			
			if(isset($this->where['selling_date']))
			{
				$this->db->where("a.date_selling >=",$this->where['selling_date']['start']);
				$this->db->where("a.date_selling <=",$this->where['selling_date']['end']);
			}
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$score_result[$row->CustomerId][$row->CollGroupId]=$row->score;
				}
			}
		}
		$this->ResetWhere();
		return $score_result;
		
	}
	
	public function header_collmon()
	{
		$header=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.CollGroupId,c.CollGroupName");
			$this->db->from("coll_report_header a");
			$this->db->join("coll_category_product b","a.id_coll_product=b.id_coll_product","INNER");
			$this->db->join("coll_group_collmon c","a.CollGroupId=c.CollGroupId","INNER");
			$this->db->where("b.product_category_id",$this->where['product_category_id']);
			$this->db->order_by("a.order_header","ASC");
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$header[$row->CollGroupId]=$row->CollGroupName;
				}
			}
		}
		$this->ResetWhere();
		return $header;
	}
	public function GetRemarksResult()
	{
		$remarks=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.CustomerId,c.field_remark,c.form_remarks");
			$this->db->from("coll_result_collmon a");
			$this->db->join("coll_category_product b","a.id_coll_product=b.id_coll_product","INNER");
			$this->db->join("coll_form_remark c","a.id_result_mon=c.id_result_mon","INNER");
			$this->db->where("b.product_category_id",$this->where['product_category_id']);
			if(isset($this->where['colmon_date']))
			{
				$this->db->where("a.collmon_datets >=",$this->where['colmon_date']['start']." 00:00:00");
				$this->db->where("a.collmon_datets <=",$this->where['colmon_date']['end']." 23:59:59");
			}
			
			if(isset($this->where['selling_date']))
			{
				$this->db->where("a.date_selling >=",$this->where['selling_date']['start']);
				$this->db->where("a.date_selling <=",$this->where['selling_date']['end']);
			}
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$remarks[$row->CustomerId][$row->field_remark]=$row->form_remarks;
				}
			}
		}
		$this->ResetWhere();
		return $remarks;
	}
	
	public function GetGroupRemarks()
	{
		$remarks=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.CustomerId,c.CollGroupId,c.remarks_group");
			$this->db->from("coll_result_collmon a");
			$this->db->join("coll_category_product b","a.id_coll_product=b.id_coll_product","INNER");
			$this->db->join("coll_group_remarks c","a.id_result_mon=c.id_result_mon","INNER");
			$this->db->where("b.product_category_id",$this->where['product_category_id']);
			if(isset($this->where['colmon_date']))
			{
				$this->db->where("a.collmon_datets >=",$this->where['colmon_date']['start']." 00:00:00");
				$this->db->where("a.collmon_datets <=",$this->where['colmon_date']['end']." 23:59:59");
			}
			
			if(isset($this->where['selling_date']))
			{
				$this->db->where("a.date_selling >=",$this->where['selling_date']['start']);
				$this->db->where("a.date_selling <=",$this->where['selling_date']['end']);
			}
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$remarks[$row->CustomerId][$row->CollGroupId]=$row->remarks_group;
				}
			}
		}
		$this->ResetWhere();
		return $remarks;
	}
	
	public function GetStaticInput()
	{
		$remarks=array();
		if(isset($this->where['product_category_id']))
		{
			$this->db->select("a.CustomerId,c.*");
			$this->db->from("coll_result_collmon a");
			$this->db->join("coll_category_product b","a.id_coll_product=b.id_coll_product","INNER");
			$this->db->join("coll_static_input c","a.id_result_mon=c.id_result_mon","INNER");
			$this->db->where("b.product_category_id",$this->where['product_category_id']);
			if(isset($this->where['colmon_date']))
			{
				$this->db->where("a.collmon_datets >=",$this->where['colmon_date']['start']." 00:00:00");
				$this->db->where("a.collmon_datets <=",$this->where['colmon_date']['end']." 23:59:59");
			}
			
			if(isset($this->where['selling_date']))
			{
				$this->db->where("a.date_selling >=",$this->where['selling_date']['start']);
				$this->db->where("a.date_selling <=",$this->where['selling_date']['end']);
			}
			$query = $this->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$remarks[$row->CustomerId]['static_note']=$row->static_note;
					$remarks[$row->CustomerId]['static_status_system']=$row->static_status_system;
					$remarks[$row->CustomerId]['static_plan']=$row->static_plan;
				}
			}
		}
		$this->ResetWhere();
		return $remarks;
	}
	
	/*
		untuk report
#EXPLAIN
SELECT 
a.id_result_mon,a.CustomerId,
a.id_coll_product,k.product_category_id,
a.collmon_datets,
g.CollGroupName,
f.SubGroupDesc,
IF(e.input_from_user = 1,h.input_text,d.label_answer) as answer,
e.input_from_user,
g.add_remarks,
i.remarks_group,
j.form_remarks
FROM coll_result_collmon a 
INNER JOIN coll_result_form b ON a.id_result_mon=b.id_result_mon
INNER JOIN coll_form_collmon c ON b.id_form_collmon=c.id_form_collmon
INNER JOIN coll_answer_collmon d ON c.id_answer_collmon=d.id_answer_collmon
INNER JOIN t_lk_input_type e ON d.id_input_type=e.id_input_type
INNER JOIN coll_subgroup_collmon f ON c.SubGroupId=f.SubGroupId
INNER JOIN coll_group_collmon g ON f.CollGroupId=g.CollGroupId
LEFT JOIN coll_form_input h ON b.id_result_form=h.id_result_form
LEFT JOIN coll_group_remarks i ON a.id_result_mon=i.id_result_mon AND g.CollGroupId=i.CollGroupId
LEFT JOIN coll_form_remark j ON a.id_result_mon=j.id_result_mon
INNER JOIN coll_category_product k ON a.id_coll_product=k.id_coll_product
WHERE a.CustomerId =26

truncate coll_result_collmon;
truncate coll_form_remark;
truncate coll_group_remarks;
truncate coll_calculation_result;
truncate coll_form_input;
truncate coll_result_form;


segment calculate
#EXPLAIN
SELECT a.id_coll_product,a.product_category_id,
b.id_calculation,b.calculation_desc,
c.id_grup_calculat,c.SubGroupId,
d.id_segment_desc,d.segment_code,
e.case_func
FROM coll_category_product a
INNER JOIN coll_calculation_collmon b ON a.id_coll_product=b.id_coll_product
INNER JOIN coll_grup_calculat c ON b.id_calculation=c.id_calculation
INNER JOIN coll_segment_desc d ON c.id_segment_desc=d.id_segment_desc
INNER JOIN t_lk_calculate_method e ON d.id_calculate_method=e.id_calculate_method
#LEFT JOIN coll_segment_param f ON d.id_segment_desc=f.id_segment_desc
WHERE a.product_category_id =1
ORDER BY b.id_calculation,d.id_segment_desc,c.SubGroupId;

SELECT *
FROM coll_category_product a
INNER JOIN coll_calculation_collmon b ON a.id_coll_product=b.id_coll_product
INNER JOIN coll_grup_calculat c ON b.id_calculation=c.id_calculation
WHERE a.product_category_id =1;
	*/
}