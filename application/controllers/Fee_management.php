<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_management extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('fee_model','fee');
		$this->load->model('students_model','students');
	}

	public function index(){
		$this->permission_check('fee_view');
		$data = $this->data;
		$data['page_title'] = 'Fee Records List';
		$data['class_list'] = $this->students->get_class_list();
		$this->load->view('fee-view', $data);
	}

	public function add(){
		$this->permission_check('fee_add');
		$data = $this->data;
		$data['page_title'] = 'Generate Fee Record';
		$data['student_id'] = $this->input->get('student_id');
		$data['class_list'] = $this->students->get_class_list();
		$this->load->view('fee-add', $data);
	}

	public function get_students_select(){
		echo $this->students->get_student_select_list($this->input->post('student_id'));
	}

	public function generate_single(){
		$this->permission_check_with_msg('fee_add');
		$this->form_validation->set_rules('student_id', 'Student', 'trim|required');
		$this->form_validation->set_rules('fee_month', 'Fee Month', 'trim|required');
		$this->form_validation->set_rules('fee_year', 'Fee Year', 'trim|required');
		$this->form_validation->set_rules('fee_amount', 'Fee Amount', 'trim|required');
		$this->form_validation->set_rules('due_date', 'Due Date', 'trim|required');

		if($this->form_validation->run() == TRUE){
			$due_date = system_fromatted_date($this->input->post('due_date'));
			$result = $this->fee->generate_single(
				$this->input->post('student_id'),
				$this->input->post('fee_month'),
				$this->input->post('fee_year'),
				str_replace(',', '', $this->input->post('fee_amount')),
				$due_date,
				(!empty($this->input->post('discount'))) ? str_replace(',', '', $this->input->post('discount')) : 0,
				$this->input->post('note')
			);
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function generate_for_class(){
		$this->permission_check_with_msg('fee_add');
		$this->form_validation->set_rules('fee_month', 'Fee Month', 'trim|required');
		$this->form_validation->set_rules('fee_year', 'Fee Year', 'trim|required');
		$this->form_validation->set_rules('due_date', 'Due Date', 'trim|required');

		if($this->form_validation->run() == TRUE){
			$due_date = system_fromatted_date($this->input->post('due_date'));
			$result = $this->fee->generate_for_class(
				$this->input->post('class'),
				$this->input->post('fee_month'),
				$this->input->post('fee_year'),
				$due_date
			);
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function ajax_list(){
		$list = $this->fee->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach($list as $f){
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$f->id.' class="checkbox column_checkbox">';
			$row[] = $f->registration_no;
			$row[] = $f->student_name;
			$row[] = $f->class;
			$row[] = $f->fee_month.' '.$f->fee_year;
			$row[] = store_number_format($f->fee_amount);
			$row[] = show_date($f->due_date);
			$row[] = store_number_format($f->balance);

			if($f->status == 'Paid') $badge = '<span class="label label-success">Paid</span>';
			elseif($f->status == 'Partial') $badge = '<span class="label label-warning">Partial</span>';
			else $badge = '<span class="label label-danger">Due</span>';
			$row[] = $badge;

			$str2 = '<div class="btn-group">
						<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">Action <span class="caret"></span></a>
						<ul role="menu" class="dropdown-menu dropdown-light pull-right">';
			if($this->permissions('fee_edit') && $f->status != 'Paid'){
				$str2 .= '<li><a class="pointer" onclick="pay_now('.$f->id.')"><i class="fa fa-fw fa-money text-blue"></i>Receive Payment</a></li>';
			}
			$str2 .= '<li><a target="_blank" href="'.base_url().'fee_management/print_challan/'.$f->id.'"><i class="fa fa-fw fa-print text-blue"></i>Print Challan</a></li>';
			if($this->permissions('fee_delete')){
				$str2 .= '<li><a style="cursor:pointer" onclick="delete_fee('.$f->id.')"><i class="fa fa-fw fa-trash text-red"></i>Delete</a></li>';
			}
			$str2 .= '</ul></div>';
			$row[] = $str2;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->fee->count_all(),
			"recordsFiltered" => $this->fee->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function show_pay_modal(){
		$this->permission_check_with_msg('fee_edit');
		$id = $this->input->post('id');
		$fee = $this->fee->get_fee_details($id);
		$data = array('fee' => $fee);
		$this->load->view('fee-pay-modal', $data);
	}

	public function save_payment(){
		$this->permission_check_with_msg('fee_edit');
		$fee_id = $this->input->post('fee_id');
		$amount = str_replace(',', '', $this->input->post('amount'));
		$payment_date = system_fromatted_date($this->input->post('payment_date'));
		$payment_type = $this->input->post('payment_type');
		$note = $this->input->post('note');
		echo $this->fee->record_payment($fee_id, $amount, $payment_date, $payment_type, $note);
	}

	public function delete_fee(){
		$this->permission_check_with_msg('fee_delete');
		$id = $this->input->post('q_id');
		echo $this->fee->delete_fee($id);
	}

	public function multi_delete(){
		$this->permission_check_with_msg('fee_delete');
		$ids = implode(",", $_POST['checkbox']);
		echo $this->fee->delete_fee($ids);
	}

	public function student_history($student_id){
		$this->permission_check('fee_view');
		$data = $this->data;
		$data['page_title'] = 'Student Fee History';
		$data['student'] = $this->db->where('id', $student_id)->get('db_students')->row();
		$data['history'] = $this->fee->get_student_history($student_id);
		$this->load->view('fee-student-history', $data);
	}

	public function print_challan($id){
		if(!$this->permissions('fee_view')) $this->show_access_denied_page();
		$data = $this->data;
		$data['fee'] = $this->fee->get_fee_details($id);
		$data['page_title'] = 'Fee Challan';
		$this->load->view('print-fee-challan', $data);
	}

	public function report(){
		$this->permission_check('fee_report');
		$data = $this->data;
		$data['page_title'] = 'Fee Collection Report';
		$data['class_list'] = $this->students->get_class_list();

		$filters = array(
			'class'     => $this->input->post('class'),
			'fee_month' => $this->input->post('fee_month'),
			'fee_year'  => $this->input->post('fee_year'),
			'status'    => $this->input->post('status'),
		);

		$data['summary'] = $this->fee->get_report_summary($filters);
		$data['rows'] = $this->fee->get_report_rows($filters);
		$data['filters'] = $filters;
		$this->load->view('fee-report', $data);
	}
}
