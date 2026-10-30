<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_model extends CI_Model {
	var $table = 'db_fee_records as a LEFT JOIN db_students as b ON a.student_id = b.id';
	var $column_order = array('a.id','b.registration_no','b.student_name','a.class','a.fee_month','a.fee_year','a.fee_amount','a.due_date','a.paid_amount','a.balance','a.status');
	var $column_search = array('b.registration_no','b.student_name','a.class','a.fee_month');
	var $order = array('a.due_date' => 'desc');

	public function __construct(){
		parent::__construct();
	}

	private function _get_datatables_query(){
		$this->db->select('a.id,b.registration_no,b.student_name,a.class,a.fee_month,a.fee_year,a.fee_amount,a.due_date,a.paid_amount,a.balance,a.status,a.discount');
		$this->db->from('db_fee_records as a');
		$this->db->join('db_students as b', 'a.student_id = b.id', 'left');
		$this->db->where('a.store_id', get_current_store_id());
		$this->db->where('a.delete_bit', 0);

		if(!empty($_POST['class_filter'])) $this->db->where('a.class', $_POST['class_filter']);
		if(!empty($_POST['month_filter'])) $this->db->where('a.fee_month', $_POST['month_filter']);
		if(!empty($_POST['status_filter'])) $this->db->where('a.status', $_POST['status_filter']);
		if(!empty($_POST['student_filter'])) $this->db->where('a.student_id', $_POST['student_filter']);

		$i = 0;
		foreach ($this->column_search as $item){
			if($_POST['search']['value']){
				if($i===0){ $this->db->group_start(); $this->db->like($item, $_POST['search']['value']); }
				else { $this->db->or_like($item, $_POST['search']['value']); }
				if(count($this->column_search) - 1 == $i) $this->db->group_end();
			}
			$i++;
		}
		if(isset($_POST['order'])){
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables(){
		$this->_get_datatables_query();
		if($_POST['length'] != -1) $this->db->limit($_POST['length'], $_POST['start']);
		return $this->db->get()->result();
	}

	public function count_filtered(){
		$this->_get_datatables_query();
		return $this->db->get()->num_rows();
	}

	public function count_all(){
		$this->db->from('db_fee_records');
		$this->db->where('store_id', get_current_store_id());
		$this->db->where('delete_bit', 0);
		return $this->db->count_all_results();
	}

	/* Generate a single fee record for one student */
	public function generate_single($student_id, $fee_month, $fee_year, $fee_amount, $due_date, $discount = 0, $note = ''){
		$student = $this->db->where('id', $student_id)->get('db_students')->row();
		if(empty($student)) return 'Student not found!';

		$dup = $this->db->where('student_id', $student_id)->where('fee_month', $fee_month)->where('fee_year', $fee_year)->where('delete_bit', 0)->get('db_fee_records')->num_rows();
		if($dup > 0) return 'Fee record for this student & month already exists!';

		// Clean up any old soft-deleted record for the same student/month so a
		// leftover unique key (on older DB copies) never blocks a fresh insert.
		$this->db->where('student_id', $student_id)->where('fee_month', $fee_month)->where('fee_year', $fee_year)->where('delete_bit', 1)->delete('db_fee_records');

		$data = array(
			'store_id'     => get_current_store_id(),
			'student_id'   => $student_id,
			'fee_month'    => $fee_month,
			'fee_year'     => $fee_year,
			'class'        => $student->class,
			'fee_amount'   => $fee_amount,
			'discount'     => $discount,
			'paid_amount'  => 0,
			'balance'      => $fee_amount - $discount,
			'due_date'     => $due_date,
			'status'       => 'Due',
			'note'         => $note,
			'delete_bit'   => 0,
			'created_by'   => $this->session->userdata('inv_username'),
			'created_date' => date('Y-m-d'),
			'created_time' => date('h:i:s a'),
		);
	if($this->db->insert('db_fee_records', $data)){
			return 'success:'.$this->db->insert_id();
		}
		$db_error = $this->db->error();
		return 'DB Error: '.$db_error['message'];
	}

	/* Bulk-generate fee records for every active student in a class for a given month */
	public function generate_for_class($class, $fee_month, $fee_year, $due_date){
		$this->db->where('store_id', get_current_store_id());
		$this->db->where('status', 1);
		$this->db->where('delete_bit', 0);
		if(!empty($class)) $this->db->where('class', $class);
		$students = $this->db->get('db_students')->result();

		if(empty($students)) return 'No active students found for the selected class!';

		$created = 0; $skipped = 0; $failed = 0;
		foreach($students as $st){
			$dup = $this->db->where('student_id', $st->id)->where('fee_month', $fee_month)->where('fee_year', $fee_year)->where('delete_bit', 0)->get('db_fee_records')->num_rows();
			if($dup > 0){ $skipped++; continue; }

			// Clean up any old soft-deleted record for the same student/month so a
			// leftover unique key (on older DB copies) never blocks a fresh insert.
			$this->db->where('student_id', $st->id)->where('fee_month', $fee_month)->where('fee_year', $fee_year)->where('delete_bit', 1)->delete('db_fee_records');

			$data = array(
				'store_id'     => get_current_store_id(),
				'student_id'   => $st->id,
				'fee_month'    => $fee_month,
				'fee_year'     => $fee_year,
				'class'        => $st->class,
				'fee_amount'   => $st->default_fee_amount,
				'discount'     => 0,
				'paid_amount'  => 0,
				'balance'      => $st->default_fee_amount,
				'due_date'     => $due_date,
				'status'       => 'Due',
				'delete_bit'   => 0,
				'created_by'   => $this->session->userdata('inv_username'),
				'created_date' => date('Y-m-d'),
				'created_time' => date('h:i:s a'),
			);
			if($this->db->insert('db_fee_records', $data)){
				$created++;
			} else {
				$failed++;
			}
		}
		$msg = "Generated: $created, Skipped (already existed): $skipped";
		if($failed > 0) $msg .= ", Failed: $failed (check that every student has a Class and Monthly Fee Amount set)";
		return $msg;
	}

	public function get_fee_details($id){
		$this->db->select('a.*, b.registration_no, b.student_name, b.class as student_class');
		$this->db->from('db_fee_records as a');
		$this->db->join('db_students as b', 'a.student_id = b.id', 'left');
		$this->db->where('a.id', $id);
		return $this->db->get()->row();
	}

	public function record_payment($fee_id, $amount, $payment_date, $payment_type = 'Cash', $note = ''){
		$fee = $this->db->where('id', $fee_id)->get('db_fee_records')->row();
		if(empty($fee)) return 'failed';

		$new_paid = $fee->paid_amount + $amount;
		$balance = $fee->fee_amount - $fee->discount - $new_paid;
		$status = ($balance <= 0) ? 'Paid' : (($new_paid > 0) ? 'Partial' : 'Due');

		$this->db->trans_start();

		$this->db->where('id', $fee_id)->update('db_fee_records', array(
			'paid_amount'   => $new_paid,
			'balance'       => $balance,
			'status'        => $status,
			'payment_date'  => $payment_date,
		));

		$this->db->insert('db_fee_payments', array(
			'store_id'      => get_current_store_id(),
			'fee_record_id' => $fee_id,
			'student_id'    => $fee->student_id,
			'amount'        => $amount,
			'payment_date'  => $payment_date,
			'payment_type'  => $payment_type,
			'note'          => $note,
			'created_by'    => $this->session->userdata('inv_username'),
			'created_date'  => date('Y-m-d'),
			'created_time'  => date('h:i:s a'),
		));

		$this->db->trans_complete();
		return ($this->db->trans_status()) ? 'success' : 'failed';
	}

	public function delete_fee($ids){
		$this->db->where_in('id', explode(',', $ids));
		return ($this->db->update('db_fee_records', array('delete_bit' => 1))) ? 'success' : 'failed';
	}

	public function get_student_history($student_id){
		$this->db->where('student_id', $student_id);
		$this->db->where('delete_bit', 0);
		$this->db->order_by('fee_year', 'desc');
		$this->db->order_by('id', 'desc');
		return $this->db->get('db_fee_records')->result();
	}

	public function get_payment_history($fee_id){
		return $this->db->where('fee_record_id', $fee_id)->order_by('id', 'desc')->get('db_fee_payments')->result();
	}

	/* Summary for the Fee Report screen */
	public function get_report_summary($filters = array()){
		$this->db->select("
			coalesce(sum(fee_amount - discount),0) as total_billed,
			coalesce(sum(paid_amount),0) as total_collected,
			coalesce(sum(balance),0) as total_outstanding,
			sum(case when status='Paid' then 1 else 0 end) as paid_count,
			sum(case when status='Partial' then 1 else 0 end) as partial_count,
			sum(case when status='Due' then 1 else 0 end) as due_count
		");
		$this->db->from('db_fee_records');
		$this->db->where('store_id', get_current_store_id());
		$this->db->where('delete_bit', 0);
		if(!empty($filters['class'])) $this->db->where('class', $filters['class']);
		if(!empty($filters['fee_month'])) $this->db->where('fee_month', $filters['fee_month']);
		if(!empty($filters['fee_year'])) $this->db->where('fee_year', $filters['fee_year']);
		if(!empty($filters['status'])) $this->db->where('status', $filters['status']);
		return $this->db->get()->row();
	}

	public function get_report_rows($filters = array()){
		$this->db->select('a.*, b.registration_no, b.student_name, b.mobile');
		$this->db->from('db_fee_records as a');
		$this->db->join('db_students as b', 'a.student_id = b.id', 'left');
		$this->db->where('a.store_id', get_current_store_id());
		$this->db->where('a.delete_bit', 0);
		if(!empty($filters['class'])) $this->db->where('a.class', $filters['class']);
		if(!empty($filters['fee_month'])) $this->db->where('a.fee_month', $filters['fee_month']);
		if(!empty($filters['fee_year'])) $this->db->where('a.fee_year', $filters['fee_year']);
		if(!empty($filters['status'])) $this->db->where('a.status', $filters['status']);
		$this->db->order_by('b.student_name', 'asc');
		return $this->db->get()->result();
	}
}
