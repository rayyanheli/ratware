<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('students_model','students');
	}

	public function index(){
		$this->permission_check('students_view');
		$data = $this->data;
		$data['page_title'] = $this->lang->line('students_list');
		$this->load->view('students-view', $data);
	}

	public function add(){
		$this->permission_check('students_add');
		$data = $this->data;
		$data['page_title'] = $this->lang->line('students');
		$this->load->view('students', $data);
	}

	public function new_student(){
		$this->form_validation->set_rules('student_name', 'Student Name', 'trim|required');
		$this->form_validation->set_rules('class', 'Class', 'trim|required');
		if($this->form_validation->run() == TRUE){
			$result = $this->students->verify_and_save();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function update($id){
		$this->belong_to('db_students', $id);
		$this->permission_check('students_edit');
		$data = $this->data;
		$result = $this->students->get_details($id, $data);
		$data = array_merge($data, $result);
		$data['page_title'] = $this->lang->line('students');
		$this->load->view('students', $data);
	}

	public function update_student(){
		$this->form_validation->set_rules('student_name', 'Student Name', 'trim|required');
		$this->form_validation->set_rules('class', 'Class', 'trim|required');
		if($this->form_validation->run() == TRUE){
			$result = $this->students->update_students();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function ajax_list(){
		$list = $this->students->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach($list as $st){
			$no++;
			$row = array();
			$disable = ($st->delete_bit == 1) ? 'disabled' : '';
			$row[] = ($st->delete_bit == 1) ? '<span class="text-danger fa fa-fw fa-ban" title="Restricted"></span>' : '<input type="checkbox" name="checkbox[]" '.$disable.' value='.$st->id.' class="checkbox column_checkbox">';
			$row[] = $st->registration_no;
			$row[] = $st->student_name;
			$row[] = $st->father_name;
			$row[] = $st->roll_no;
			$row[] = $st->class;
			$row[] = $st->mobile;
			$row[] = $st->email;
			$row[] = store_number_format($st->default_fee_amount);

			if($st->status == 1){
				$str = "<span onclick='update_status(".$st->id.",0)' id='span_".$st->id."' class='label label-success' style='cursor:pointer'>Active</span>";
			} else {
				$str = "<span onclick='update_status(".$st->id.",1)' id='span_".$st->id."' class='label label-danger' style='cursor:pointer'>Inactive</span>";
			}
			$row[] = $str;

			$str2 = '<div class="btn-group" title="Action">
						<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
							Action <span class="caret"></span>
						</a>
						<ul role="menu" class="dropdown-menu dropdown-light pull-right">';
			if($this->permissions('students_edit') && $st->delete_bit != 1){
				$str2 .= '<li><a title="Edit Record" href="'.base_url().'students/update/'.$st->id.'"><i class="fa fa-fw fa-edit text-blue"></i>Edit</a></li>';
			}
			if($this->permissions('fee_view')){
				$str2 .= '<li><a title="Fee History" href="'.base_url().'fee_management/student_history/'.$st->id.'"><i class="fa fa-fw fa-money text-blue"></i>Fee History</a></li>';
			}
			if($this->permissions('fee_add')){
				$str2 .= '<li><a title="Generate Fee" href="'.base_url().'fee_management/add?student_id='.$st->id.'"><i class="fa fa-fw fa-plus text-blue"></i>Generate Fee</a></li>';
			}
			if($this->permissions('students_delete') && $st->delete_bit != 1){
				$str2 .= '<li><a style="cursor:pointer" title="Delete Record?" onclick="delete_students('.$st->id.')"><i class="fa fa-fw fa-trash text-red"></i>Delete</a></li>';
			}
			$str2 .= '</ul></div>';
			$row[] = $str2;

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->students->count_all(),
			"recordsFiltered" => $this->students->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function update_status(){
		$this->permission_check_with_msg('students_edit');
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		echo $this->students->update_status($id, $status);
	}

	public function delete_students(){
		$this->permission_check_with_msg('students_delete');
		$id = $this->input->post('q_id');
		echo $this->students->delete_students_from_table($id);
	}

	public function multi_delete(){
		$this->permission_check_with_msg('students_delete');
		$ids = implode(",", $_POST['checkbox']);
		echo $this->students->delete_students_from_table($ids);
	}
}
