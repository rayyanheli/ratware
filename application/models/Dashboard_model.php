<?php

class Dashboard_model extends CI_Model
{

	//Datatable start
		var $table = 'db_items a';
		var $column_order = array(
									'a.item_code',
									'a.item_name',
									'b.category_name',
									'c.brand_name',
									'a.stock'
									); //set column field database for datatable orderable
		var $column_search = array( 
									'a.item_code',
									'a.item_name',
									'b.category_name',
									'c.brand_name',
									'a.stock'
									); //set column field database for datatable searchable 
		var $order = array('a.id' => 'desc'); // default order 

	
		
		private function _get_datatables_query()
		{	
			$this->db->select($this->column_order);
			$this->db->from($this->table);
			$this->db->where('a.store_id',get_current_store_id());
	        $this->db->where('(a.stock<=a.alert_qty or a.stock is null)');
	        $this->db->where('a.service_bit',0);
	        
	        $this->db->where('a.status=1');
	        $this->db->join('db_category b','b.id=a.category_id','left');
	        $this->db->join('db_brands c','c.id=a.brand_id','left');

	     

			//	echo $this->db->get_compiled_select();exit();
			$i = 0;
		
			foreach ($this->column_search as $item) // loop column 
			{
				if($_POST['search']['value']) // if datatable send POST for search
				{
					
					if($i===0) // first loop
					{
						$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						$this->db->like($item, $_POST['search']['value']);
					}
					else
					{
						$this->db->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->db->group_end(); //close bracket
				}
				$i++;
			}
			
			if(isset($_POST['order'])) // here order processing
			{
				$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			} 
			else if(isset($this->order))
			{
				$order = $this->order;
				$this->db->order_by(key($order), $order[key($order)]);
			}
		}

		function get_datatables()
		{
			$this->_get_datatables_query();
			if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
			$query = $this->db->get();
			return $query->result();
		}

		function count_filtered()
		{
			$this->_get_datatables_query();
			$query = $this->db->get();
			return $query->num_rows();
		}

		public function count_all()
		{
			$this->db->where("store_id",get_current_store_id());
			$this->db->from($this->table);
			return $this->db->count_all_results();
		}
		//Datatable end

	public function get_subscription_chart()
	{
		$sub_chart = array();
		for ($i=6; $i >= 0; $i--) {
			//Date
            $sub_chart['date'][$i] = date("Y-m-d",strtotime("-".$i." months"));
            $sub_chart['sub_year'][$i] = date("Y",strtotime($sub_chart['date'][$i]));
            $sub_chart['sub_month'][$i] = date("M",strtotime($sub_chart['date'][$i]));

            $this->db->select("count(*) as tot_subscribes");
            $this->db->from("db_subscription");
            $this->db->where("month(subscription_date)",date("m",strtotime($sub_chart['date'][$i])));
            $q3=$this->db->get();
            $sub_chart['tot_subscribes'][$i] = $q3->row()->tot_subscribes;
        }
        return $sub_chart;
	}

	public function get_pie_chart($value='')
	{
		
            $this->db->where("c.store_id",get_current_store_id());
            /*if(!is_admin() && !is_store_admin()){
              $this->db->where("c.created_by",$this->session->userdata('inv_username'));  
            }*/
            $this->db->select("COALESCE(SUM(b.sales_qty),0) AS sales_qty, a.item_name");
            $this->db->from("db_items AS a, db_salesitems AS b ,db_sales AS c");
            $this->db->where("a.id=b.`item_id` AND b.sales_id=c.`id` AND c.`sales_status`='Final'");
            $this->db->group_by("a.id");
            $this->db->limit("10");
            $this->db->order_by("sales_qty","asc");

            $q3=$this->db->get();
            $pie_chart=array();
            $i=0;
            if($q3->num_rows() >0){
              foreach($q3->result() as $res3){
                  if($res3->sales_qty>0){
                  	++$i;
                  	$pie_chart['tranding_item'][$i]['name'] = $res3->item_name;
                  	$pie_chart['tranding_item'][$i]['sales_qty'] = $res3->sales_qty;
                  }
              }
            }
            $pie_chart['tranding_item']['tot_rec'] = $i;
            return $pie_chart;
	}
	public function get_bar_chart(){
		$bar_chart=array();
          for ($i=6; $i >= 0; $i--) { 

              //Date
              $bar_chart['date'][$i] = date("Y-m-d",strtotime("-".$i." months"));
              $bar_chart['month'][$i] = date("M",strtotime($bar_chart['date'][$i])).",".date("Y",strtotime($bar_chart['date'][$i]));

              //Find purchase total
              $this->db->where("store_id",get_current_store_id());
              if(!is_admin() && !is_store_admin()){
                $this->db->where("created_by",$this->session->userdata('inv_username'));  
              }
              $this->db->select("COALESCE(SUM(grand_total),0) AS pur_total");
              $this->db->from("db_purchase");
              $this->db->where("purchase_status='Received'");
              $this->db->where("month(purchase_date)",date("m",strtotime($bar_chart['date'][$i])));
              $this->db->where("year(purchase_date)",date("Y",strtotime($bar_chart['date'][$i])));
              $q1=$this->db->get()->row();
              $this->db->get_compiled_select();
              $bar_chart['purchase'][$i]=$q1->pur_total;
              
              //Find sales total
              $this->db->where("store_id",get_current_store_id());
              if(!is_admin() && !is_store_admin()){
                $this->db->where("created_by",$this->session->userdata('inv_username'));  
              }
              $this->db->select("COALESCE(SUM(grand_total),0) AS sal_total");
              $this->db->from("db_sales");
              $this->db->where("sales_status='Final'");
              $this->db->where("month(sales_date)",date("m",strtotime($bar_chart['date'][$i])));
              $this->db->where("year(sales_date)",date("Y",strtotime($bar_chart['date'][$i])));
              $q1=$this->db->get()->row();
              $bar_chart['sales'][$i]=$q1->sal_total;

              //Find expense total
              $this->db->where("store_id",get_current_store_id());
              if(!is_admin() && !is_store_admin()){
                $this->db->where("created_by",$this->session->userdata('inv_username'));  
              }
              $this->db->select("COALESCE(SUM(expense_amt),0) AS expense_amt");
              $this->db->from("db_expense");
              $this->db->where("month(expense_date)",date("m",strtotime($bar_chart['date'][$i])));
              $this->db->where("year(expense_date)",date("Y",strtotime($bar_chart['date'][$i])));
              $q1=$this->db->get()->row();
              $bar_chart['expense'][$i]=$q1->expense_amt;
          }
          return $bar_chart;
	}
	public function get_by_date($table_date)
	{
		$dates = $this->input->post('dates');
		if($dates=='Today'){
      		//$this->db->where("$table_date > DATE_SUB(NOW(), INTERVAL 1 DAY)");
      		$this->db->where("$table_date",date("Y-m-d"));
      	}
      	if($dates=='Weekly'){
      		$this->db->where("$table_date > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
      	}
      	if($dates=='Monthly'){
      		$this->db->where("$table_date > DATE_SUB(NOW(), INTERVAL 1 MONTH)");
      	}
      	if($dates=='Yearly'){
      		$this->db->where("$table_date > DATE_SUB(NOW(), INTERVAL 1 YEAR)");
      	}
	}
	public function breadboard_values()
	{	
		$dates = $this->input->post('dates');
		//$store_id=$this->input->post('store_id');
		$CI =& get_instance();
		$info=array();

		///Find total suppliers
		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		$this->db->select("coalesce(count(*),0) as tot_sup");
		$this->db->from("db_suppliers");
		$this->db->where("status=1");
		
		$tot_sup=$this->db->get()->row()->tot_sup;	
		$info['tot_sup']=$tot_sup;

		///Find total Products
		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		$this->db->select("coalesce(count(*),0) as tot_pro");
		$this->db->from("db_items");
		$this->db->where("status=1");
		$tot_pro=$this->db->get()->row()->tot_pro;	
		$info['tot_pro']=$tot_pro;

		//Total Customers
		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		$this->db->select("coalesce(count(*),0) as tot_cust");
		$this->db->from("db_customers");
		$this->db->where("status=1");
		$tot_cust=$this->db->get()->row()->tot_cust;	
		$info['tot_cust']=$tot_cust;

  		//Total Purchases Active
  		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		if(!is_admin() && !is_store_admin()){
			$this->db->where("created_by",$this->session->userdata('inv_username'));	
		}
      	$this->get_by_date('purchase_date');//DATES FUNCTION
		$this->db->select("coalesce(count(*),0) as tot_pur");
		$this->db->from("db_purchase");
		$this->db->where("purchase_status='Received'");
		//echo $this->db->get_compiled_select();exit();
		$tot_pur=$this->db->get()->row()->tot_pur;	
		$info['tot_pur']=$tot_pur;

  		//Total SAles Active
  		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		if(!is_admin() && !is_store_admin()){
			$this->db->where("created_by",$this->session->userdata('inv_username'));	
		}
		$this->get_by_date('sales_date');//DATES FUNCTION
		$this->db->select("coalesce(count(*),0) as tot_sal");
		$this->db->from("db_sales");
		$this->db->where("`sales_status`= 'Final'");
		$tot_sal=$this->db->get()->row()->tot_sal;
		$info['tot_sal']=$tot_sal;


		//Total SAles return amount
		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		if(!is_admin() && !is_store_admin()){
			$this->db->where("created_by",$this->session->userdata('inv_username'));	
		}
		$this->get_by_date('return_date');//DATES FUNCTION
		$this->db->select("COALESCE(sum(grand_total),0) AS tot_sal_ret_grand_total");
		$this->db->from("db_salesreturn");
		$tot_sal_ret_grand_total=$this->db->get()->row()->tot_sal_ret_grand_total;
		$info['tot_sal_ret_grand_total']=$CI->currency(kmb($tot_sal_ret_grand_total));

		//Total SAles amount
		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		if(!is_admin() && !is_store_admin()){
			$this->db->where("created_by",$this->session->userdata('inv_username'));	
		}
		$this->get_by_date('sales_date');//DATES FUNCTION
		$this->db->select("COALESCE(sum(grand_total),0) AS tot_sal_grand_total");
		$this->db->from("db_sales");
		$this->db->where("`sales_status`= 'Final'");
		$tot_sal_grand_total=$this->db->get()->row()->tot_sal_grand_total;
		$info['tot_sal_grand_total']=$CI->currency(kmb($tot_sal_grand_total-$tot_sal_ret_grand_total));

		


		//Total expense amount
		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		if(!is_admin() && !is_store_admin()){
			$this->db->where("created_by",$this->session->userdata('inv_username'));	
		}
		$this->get_by_date('expense_date');//DATES FUNCTION
		$this->db->select("COALESCE(sum(expense_amt),0) AS tot_exp");
		$this->db->from("db_expense");
		$tot_exp=$this->db->get()->row()->tot_exp;
		$info['tot_exp']=$CI->currency(kmb($tot_exp,2));

		//Total SAles Due
		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		if(!is_admin() && !is_store_admin()){
			$this->db->where("created_by",$this->session->userdata('inv_username'));	
		}
		$this->get_by_date('sales_date');//DATES FUNCTION
		$this->db->select("(COALESCE(sum(grand_total),0)-COALESCE(sum(paid_amount),0)) as sales_due");
		$this->db->from("db_sales");
		$this->db->where("`sales_status`= 'Final'");
		$sales_due=$this->db->get()->row()->sales_due;
		$info['sales_due']=$CI->currency(kmb($sales_due));

		//Total Purchase  Due
		/*if(store_module() && is_admin()){if(!empty($store_id)){ 
					$this->db->where("store_id",$store_id);}
				}else{ */
					$this->db->where("store_id",get_current_store_id());	
			/*}*/
		if(!is_admin() && !is_store_admin()){
			$this->db->where("created_by",$this->session->userdata('inv_username'));	
		}
		$this->get_by_date('purchase_date');//DATES FUNCTION
		$this->db->select("(COALESCE(sum(grand_total),0)-COALESCE(sum(paid_amount),0)) as purchase_due");
		$this->db->from("db_purchase");
		$this->db->where("`purchase_status`= 'Received'");
		$purchase_due=$this->db->get()->row()->purchase_due;
		$info['purchase_due']=$CI->currency(kmb($purchase_due));

		return $info;
	}

	/** Data for the retail command center. All figures come from the current store. */
	public function command_center_data($range = 'today')
	{
		$windows = $this->command_center_windows($range);
		$start = $windows['start'];
		$end = $windows['end'];
		$previous_start = $windows['previous_start'];
		$previous_end = $windows['previous_end'];

		$sales = $this->command_center_sales_totals($start, $end);
		$previous_sales = $this->command_center_sales_totals($previous_start, $previous_end);
		$refunds = $this->command_center_refund_total($start, $end);
		$previous_refunds = $this->command_center_refund_total($previous_start, $previous_end);
		$orders = (int)$sales->orders;
		$previous_orders = (int)$previous_sales->orders;

		$metrics = array(
			'revenue' => (float)$sales->gross - $refunds,
			'revenuePrevious' => (float)$previous_sales->gross - $previous_refunds,
			'orders' => $orders,
			'ordersPrevious' => $previous_orders,
			'aov' => $orders ? ((float)$sales->gross / $orders) : 0,
			'aovPrevious' => $previous_orders ? ((float)$previous_sales->gross / $previous_orders) : 0,
			'refunds' => $refunds,
			'refundsPrevious' => $previous_refunds,
			'paid' => (float)$sales->paid,
			'due' => max(0, (float)$sales->gross - (float)$sales->paid),
			'expenses' => $this->command_center_expense_total($start, $end),
		);

		$inventory = $this->command_center_inventory();
		$metrics['lowStockCount'] = $inventory['count'];

		return array(
			'range' => $windows['range'],
			'rangeLabel' => $windows['label'],
			'start' => $start,
			'end' => $end,
			'generatedAt' => date('c'),
			'metrics' => $metrics,
			'trend' => $this->command_center_trend($windows),
			'products' => $this->command_center_products($start, $end),
			'categories' => $this->command_center_categories($start, $end),
			'inventory' => $inventory['items'],
			'transactions' => $this->command_center_transactions($start, $end),
			'payments' => $this->command_center_payments($start, $end),
		);
	}

	private function command_center_windows($range)
	{
		$today = new DateTimeImmutable('today');
		$range = in_array($range, array('today', '7d', '30d', '12m'), true) ? $range : 'today';
		if ($range === '7d') {
			$start = $today->modify('-6 days');
			$previous_start = $start->modify('-7 days');
			$label = 'Last 7 days';
			$unit = 'day';
			$count = 7;
		} elseif ($range === '30d') {
			$start = $today->modify('-29 days');
			$previous_start = $start->modify('-30 days');
			$label = 'Last 30 days';
			$unit = 'day';
			$count = 30;
		} elseif ($range === '12m') {
			$start = $today->modify('first day of this month')->modify('-11 months');
			$previous_start = $start->modify('-12 months');
			$label = 'Last 12 months';
			$unit = 'month';
			$count = 12;
		} else {
			$start = $today;
			$previous_start = $today->modify('-1 day');
			$label = 'Today';
			$unit = 'hour';
			$count = (int)date('G') + 1;
		}
		return array(
			'range' => $range,
			'label' => $label,
			'unit' => $unit,
			'count' => $count,
			'start' => $start->format('Y-m-d'),
			'end' => $today->format('Y-m-d'),
			'previous_start' => $previous_start->format('Y-m-d'),
			'previous_end' => $start->modify('-1 day')->format('Y-m-d'),
		);
	}

	private function command_center_scope($alias, $date_column, $start, $end, $final_sales = false)
	{
		$this->db->where($alias.'.store_id', get_current_store_id());
		$this->db->where($alias.'.'.$date_column.' >=', $start);
		$this->db->where($alias.'.'.$date_column.' <=', $end);
		if ($final_sales) {
			$this->db->where($alias.'.sales_status', 'Final');
		}
		if (!is_admin() && !is_store_admin()) {
			$this->db->where($alias.'.created_by', $this->session->userdata('inv_username'));
		}
	}

	private function command_center_sales_totals($start, $end)
	{
		$this->db->select('COUNT(*) AS orders, COALESCE(SUM(s.grand_total),0) AS gross, COALESCE(SUM(s.paid_amount),0) AS paid', false);
		$this->db->from('db_sales s');
		$this->command_center_scope('s', 'sales_date', $start, $end, true);
		return $this->db->get()->row();
	}

	private function command_center_refund_total($start, $end)
	{
		$this->db->select('COALESCE(SUM(r.grand_total),0) AS amount', false);
		$this->db->from('db_salesreturn r');
		$this->command_center_scope('r', 'return_date', $start, $end);
		return (float)$this->db->get()->row()->amount;
	}

	private function command_center_expense_total($start, $end)
	{
		$this->db->select('COALESCE(SUM(e.expense_amt),0) AS amount', false);
		$this->db->from('db_expense e');
		$this->command_center_scope('e', 'expense_date', $start, $end);
		return (float)$this->db->get()->row()->amount;
	}

	private function command_center_trend_rows($start, $end, $unit)
	{
		$expression = $unit === 'hour' ? 'HOUR(s.created_time)' : ($unit === 'month' ? "DATE_FORMAT(s.sales_date, '%Y-%m')" : 's.sales_date');
		$this->db->select($expression.' AS bucket, COALESCE(SUM(s.grand_total),0) AS amount', false);
		$this->db->from('db_sales s');
		$this->command_center_scope('s', 'sales_date', $start, $end, true);
		$this->db->group_by($expression, false);
		$rows = $this->db->get()->result();
		$indexed = array();
		foreach ($rows as $row) {
			$indexed[(string)$row->bucket] = (float)$row->amount;
		}
		return $indexed;
	}

	private function command_center_trend($windows)
	{
		$unit = $windows['unit'];
		$current = $this->command_center_trend_rows($windows['start'], $windows['end'], $unit);
		$previous = $this->command_center_trend_rows($windows['previous_start'], $windows['previous_end'], $unit);
		$labels = array();
		$current_values = array();
		$previous_values = array();
		$start = new DateTimeImmutable($windows['start']);
		$previous_start = new DateTimeImmutable($windows['previous_start']);
		for ($i = 0; $i < $windows['count']; $i++) {
			if ($unit === 'hour') {
				$key = (string)$i;
				$previous_key = $key;
				$labels[] = sprintf('%02d:00', $i);
			} else {
				$step = $unit === 'month' ? '+'.$i.' months' : '+'.$i.' days';
				$date = $start->modify($step);
				$previous_date = $previous_start->modify($step);
				$key = $date->format($unit === 'month' ? 'Y-m' : 'Y-m-d');
				$previous_key = $previous_date->format($unit === 'month' ? 'Y-m' : 'Y-m-d');
				$labels[] = $date->format($unit === 'month' ? 'M y' : 'M j');
			}
			$current_values[] = isset($current[$key]) ? $current[$key] : 0;
			$previous_values[] = isset($previous[$previous_key]) ? $previous[$previous_key] : 0;
		}
		return array('labels' => $labels, 'current' => $current_values, 'previous' => $previous_values, 'unit' => $unit);
	}

	private function command_center_products($start, $end)
	{
		$this->db->select('i.id, i.item_name AS name, COALESCE(SUM(si.sales_qty),0) AS quantity, COALESCE(SUM(si.total_cost),0) AS revenue', false);
		$this->db->from('db_salesitems si');
		$this->db->join('db_sales s', 's.id = si.sales_id');
		$this->db->join('db_items i', 'i.id = si.item_id');
		$this->command_center_scope('s', 'sales_date', $start, $end, true);
		$this->db->group_by('i.id, i.item_name');
		$this->db->order_by('revenue', 'DESC');
		$this->db->limit(5);
		return $this->db->get()->result_array();
	}

	private function command_center_categories($start, $end)
	{
		$this->db->select('c.category_name AS name, COALESCE(SUM(si.total_cost),0) AS revenue', false);
		$this->db->from('db_salesitems si');
		$this->db->join('db_sales s', 's.id = si.sales_id');
		$this->db->join('db_items i', 'i.id = si.item_id');
		$this->db->join('db_category c', 'c.id = i.category_id', 'left');
		$this->command_center_scope('s', 'sales_date', $start, $end, true);
		$this->db->group_by('c.id, c.category_name');
		$this->db->order_by('revenue', 'DESC');
		$this->db->limit(5);
		return $this->db->get()->result_array();
	}

	private function command_center_inventory()
	{
		$store_id = get_current_store_id();
		$this->db->from('db_items i');
		$this->db->where('i.store_id', $store_id);
		$this->db->where('i.status', 1);
		$this->db->where('i.service_bit', 0);
		$this->db->where('(i.stock <= i.alert_qty OR i.stock IS NULL)', null, false);
		$count = (int)$this->db->count_all_results();
		$this->db->select('i.id, i.item_name AS name, i.item_code AS code, i.stock, i.alert_qty AS threshold');
		$this->db->from('db_items i');
		$this->db->where('i.store_id', $store_id);
		$this->db->where('i.status', 1);
		$this->db->where('i.service_bit', 0);
		$this->db->where('(i.stock <= i.alert_qty OR i.stock IS NULL)', null, false);
		$this->db->order_by('i.stock', 'ASC');
		$this->db->limit(5);
		return array('count' => $count, 'items' => $this->db->get()->result_array());
	}

	private function command_center_transactions($start, $end)
	{
		$this->db->select('s.id, s.sales_code AS code, s.sales_date AS date, s.created_time AS time, s.grand_total AS amount, s.payment_status AS status, c.customer_name AS customer');
		$this->db->from('db_sales s');
		$this->db->join('db_customers c', 'c.id = s.customer_id', 'left');
		$this->command_center_scope('s', 'sales_date', $start, $end, true);
		$this->db->order_by('s.sales_date', 'DESC');
		$this->db->order_by('s.id', 'DESC');
		$this->db->limit(8);
		return $this->db->get()->result_array();
	}

	private function command_center_payments($start, $end)
	{
		$this->db->select('p.payment_type AS method, COALESCE(SUM(p.payment),0) AS amount', false);
		$this->db->from('db_salespayments p');
		$this->db->join('db_sales s', 's.id = p.sales_id');
		$this->db->where('s.store_id', get_current_store_id());
		$this->db->where('s.sales_status', 'Final');
		$this->db->where('p.payment_date >=', $start);
		$this->db->where('p.payment_date <=', $end);
		if (!is_admin() && !is_store_admin()) {
			$this->db->where('s.created_by', $this->session->userdata('inv_username'));
		}
		$this->db->group_by('p.payment_type');
		$this->db->order_by('amount', 'DESC');
		return $this->db->get()->result_array();
	}
}
