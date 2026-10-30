<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students_model extends CI_Model {
	// Datatable settings
	var $table = 'db_students as a';
	var $column_order = array('a.id','a.registration_no','a.student_name','a.father_name','a.roll_no','a.class','a.mobile','a.email','a.default_fee_amount','a.status','a.delete_bit');
	var $column_search = array('a.registration_no','a.student_name','a.father_name','a.roll_no','a.class','a.mobile','a.email');
	var $order = array('a.id' => 'desc');

	public function __construct(){
		parent::__construct();
	}

	private function _get_datatables_query(){
		$this->db->select($this->column_order);
		$this->db->from($this->table);
		$this->db->where('a.store_id', get_current_store_id());
		if(!empty($_POST['class_filter'])){
			$this->db->where('a.class', $_POST['class_filter']);
		}
		$i = 0;
		foreach ($this->column_search as $item){
			if($_POST['search']['value']){
				if($i===0){
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}
				if(count($this->column_search) - 1 == $i) $this->db->group_end();
			}
			$i++;
		}
		if(isset($_POST['order'])){
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} elseif(isset($this->order)){
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables(){
		$this->_get_datatables_query();
		if($_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered(){
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all(){
		$this->db->from($this->table);
		$this->db->where('a.store_id', get_current_store_id());
		return $this->db->count_all_results();
	}

	public function verify_and_save(){
		$data = array(
			'store_id'            => get_current_store_id(),
			'registration_no'     => $this->input->post('registration_no'),
			'student_name'        => $this->input->post('student_name'),
			'father_name'         => $this->input->post('father_name'),
			'roll_no'             => $this->input->post('roll_no'),
			'class'               => $this->input->post('class'),
			'mobile'              => $this->input->post('mobile'),
			'email'               => $this->input->post('email'),
			'address'             => $this->input->post('address'),
			'admission_date'      => (!empty($this->input->post('admission_date'))) ? system_fromatted_date($this->input->post('admission_date')) : date('Y-m-d'),
			'default_fee_amount'  => (!empty($this->input->post('default_fee_amount'))) ? str_replace(',','',$this->input->post('default_fee_amount')) : 0,
			'status'              => 1,
			'delete_bit'          => 0,
			'system_ip'           => $this->input->ip_address(),
			'system_name'         => gethostbyaddr($this->input->ip_address()),
			'created_by'          => $this->session->userdata('inv_username'),
			'created_date'        => date('Y-m-d'),
			'created_time'        => date('h:i:s a'),
		);

		if(empty($data['registration_no'])){
			$next_id = (int)$this->db->select('coalesce(max(id),0) as max_id')->get('db_students')->row()->max_id + 1;
			$data['registration_no'] = 'REG-'.str_pad($next_id, 4, '0', STR_PAD_LEFT);
		}

		// Prevent duplicate registration number within the same store
		$exists = $this->db->where('store_id', get_current_store_id())->where('registration_no', $data['registration_no'])->get('db_students')->num_rows();
		if($exists > 0){
			return 'Registration No. already exists for this store!';
		}

		if($this->db->insert('db_students', $data)){
			return "success";
		}
		return "failed";
	}

	public function get_details($id, $data){
		$rec = $this->db->where('id', $id)->get('db_students')->row();
		if(empty($rec)){
			return array();
		}
		return array(
			'q_id'                => $rec->id,
			'registration_no'     => $rec->registration_no,
			'student_name'        => $rec->student_name,
			'father_name'         => $rec->father_name,
			'roll_no'             => $rec->roll_no,
			'class'               => $rec->class,
			'mobile'              => $rec->mobile,
			'email'               => $rec->email,
			'address'             => $rec->address,
			'admission_date'      => $rec->admission_date,
			'default_fee_amount'  => $rec->default_fee_amount,
		);
	}

	public function update_students(){
		$id = $this->input->post('q_id');
		$data = array(
			'registration_no'     => $this->input->post('registration_no'),
			'student_name'        => $this->input->post('student_name'),
			'father_name'         => $this->input->post('father_name'),
			'roll_no'             => $this->input->post('roll_no'),
			'class'               => $this->input->post('class'),
			'mobile'              => $this->input->post('mobile'),
			'email'               => $this->input->post('email'),
			'address'             => $this->input->post('address'),
			'admission_date'      => (!empty($this->input->post('admission_date'))) ? system_fromatted_date($this->input->post('admission_date')) : date('Y-m-d'),
			'default_fee_amount'  => (!empty($this->input->post('default_fee_amount'))) ? str_replace(',','',$this->input->post('default_fee_amount')) : 0,
		);
		$this->db->where('id', $id);
		if($this->db->update('db_students', $data)){
			return "success";
		}
		return "failed";
	}

	public function update_status($id, $status){
		$this->db->where('id', $id);
		if($this->db->update('db_students', array('status' => $status))){
			return "success";
		}
		return "failed";
	}

	public function delete_students_from_table($ids){
		// Soft delete to protect fee history
		$this->db->where_in('id', explode(',', $ids));
		if($this->db->update('db_students', array('delete_bit' => 1))){
			return "success";
		}
		return "failed";
	}

	public function get_class_list(){
		$this->db->distinct();
		$this->db->select('class');
		$this->db->where('store_id', get_current_store_id());
		$this->db->where('class !=', '');
		$q = $this->db->get('db_students');
		return $q->result();
	}

	public function get_student_select_list($selected_id = ''){
		$q = $this->db->where('store_id', get_current_store_id())->where('delete_bit', 0)->order_by('student_name', 'asc')->get('db_students');
		$html = '<option value="">Select Student</option>';
		foreach($q->result() as $row){
			$sel = ($row->id == $selected_id) ? 'selected' : '';
			$html .= '<option '.$sel.' data-fee="'.$row->default_fee_amount.'" data-class="'.htmlspecialchars($row->class).'" value="'.$row->id.'">'.$row->registration_no.' - '.$row->student_name.' ('.$row->class.')</option>';
		}
		return $html;
	}
}
