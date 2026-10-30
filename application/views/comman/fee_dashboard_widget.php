<?php
$CI =& get_instance();
if($CI->permissions('fee_view') || $CI->permissions('students_view')){
	$store_id = get_current_store_id();

	$total_students = $CI->db->where('store_id', $store_id)->where('delete_bit', 0)->from('db_students')->count_all_results();
	$total_classes   = $CI->db->distinct()->select('class')->where('store_id', $store_id)->where('class !=', '')->from('db_students')->get()->num_rows();

	$fee_totals = $CI->db->select("coalesce(sum(paid_amount),0) as total_collected, coalesce(sum(balance),0) as total_outstanding, coalesce(sum(fee_amount-discount),0) as total_billed")
		->where('store_id', $store_id)->where('delete_bit', 0)->from('db_fee_records')->get()->row();

	$status_counts = $CI->db->select("status, count(*) as cnt")
		->where('store_id', $store_id)->where('delete_bit', 0)->from('db_fee_records')->group_by('status')->get()->result();
	$paid_cnt = 0; $partial_cnt = 0; $due_cnt = 0;
	foreach($status_counts as $sc){
		if($sc->status == 'Paid') $paid_cnt = $sc->cnt;
		elseif($sc->status == 'Partial') $partial_cnt = $sc->cnt;
		else $due_cnt = $sc->cnt;
	}

	// Monthly trend (last 6 available months, chronological)
	$raw_trend = $CI->db->select("fee_month, fee_year, coalesce(sum(paid_amount),0) as collected, coalesce(sum(balance),0) as outstanding")
		->where('store_id', $store_id)->where('delete_bit', 0)->from('db_fee_records')->group_by('fee_month, fee_year')->get()->result();
	$month_num = array('January'=>1,'February'=>2,'March'=>3,'April'=>4,'May'=>5,'June'=>6,'July'=>7,'August'=>8,'September'=>9,'October'=>10,'November'=>11,'December'=>12);
	usort($raw_trend, function($a,$b) use ($month_num){
		$ay=(int)$a->fee_year; $by=(int)$b->fee_year;
		if($ay!=$by) return $ay <=> $by;
		return ($month_num[$a->fee_month] ?? 0) <=> ($month_num[$b->fee_month] ?? 0);
	});
	$raw_trend = array_slice($raw_trend, -6);
?>
<div class="row">
  <div class="col-md-12">
    <div style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%); border-radius:8px; padding:18px 24px; margin-bottom:15px; color:#fff; box-shadow:0 4px 14px rgba(30,60,114,0.35);">
      <h2 style="margin:0; font-weight:600;"><i class="fa fa-graduation-cap"></i> School Fee Overview</h2>
      <p style="margin:2px 0 0; opacity:0.85;">Live snapshot of student enrollment & fee collection</p>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box bg-aqua">
      <div class="inner"><h3><?= $total_students; ?></h3><p><i class="fa fa-users"></i> Total Students</p></div>
      <div class="icon"><i class="fa fa-graduation-cap"></i></div>
      <a href="<?= $base_url; ?>students" class="small-box-footer">View Students <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box bg-green">
      <div class="inner"><h3><?= $CI->currency($fee_totals->total_collected); ?></h3><p><i class="fa fa-check-circle"></i> Fee Collected</p></div>
      <div class="icon"><i class="fa fa-money"></i></div>
      <a href="<?= $base_url; ?>fee_management/report" class="small-box-footer">View Report <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box bg-red">
      <div class="inner"><h3><?= $CI->currency($fee_totals->total_outstanding); ?></h3><p><i class="fa fa-exclamation-triangle"></i> Outstanding / Due</p></div>
      <div class="icon"><i class="fa fa-warning"></i></div>
      <a href="<?= $base_url; ?>reports/fee_defaulters" class="small-box-footer">View Defaulters <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box bg-yellow">
      <div class="inner"><h3><?= $total_classes; ?></h3><p><i class="fa fa-list"></i> Active Classes</p></div>
      <div class="icon"><i class="fa fa-book"></i></div>
      <a href="<?= $base_url; ?>reports/fee_classwise" class="small-box-footer">Class Report <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-8 animated">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-line-chart"></i> Fee Collection Trend (Last 6 Months)</h3>
      </div>
      <div class="box-body">
        <canvas id="fee_trend_widget_chart" height="90"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4 animated">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Payment Status</h3>
      </div>
      <div class="box-body">
        <canvas id="fee_status_widget_chart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $theme_link; ?>plugins/chartjs/Chart.min.js"></script>
<script>
$(document).ready(function(){
  var trendLabels = [<?php foreach($raw_trend as $t){ echo "'".addslashes($t->fee_month.' '.$t->fee_year)."',"; } ?>];
  var trendCollected = [<?php foreach($raw_trend as $t){ echo $t->collected.','; } ?>];
  var trendOutstanding = [<?php foreach($raw_trend as $t){ echo $t->outstanding.','; } ?>];

  if(document.getElementById('fee_trend_widget_chart')){
    new Chart(document.getElementById('fee_trend_widget_chart'), {
      type: 'line',
      data: {
        labels: trendLabels,
        datasets: [
          { label: 'Collected', borderColor:'#00a65a', backgroundColor:'rgba(0,166,90,0.15)', data: trendCollected, fill:true, tension:0.3 },
          { label: 'Outstanding', borderColor:'#dd4b39', backgroundColor:'rgba(221,75,57,0.12)', data: trendOutstanding, fill:true, tension:0.3 }
        ]
      },
      options: { responsive:true, scales:{ yAxes:[{ticks:{min:0}}] }, legend:{display:true,position:'top'} }
    });
  }

  if(document.getElementById('fee_status_widget_chart')){
    new Chart(document.getElementById('fee_status_widget_chart'), {
      type: 'doughnut',
      data: {
        labels: ['Paid', 'Partial', 'Due'],
        datasets: [{
          data: [<?= $paid_cnt; ?>, <?= $partial_cnt; ?>, <?= $due_cnt; ?>],
          backgroundColor: ['#00a65a', '#f39c12', '#dd4b39']
        }]
      },
      options: { responsive:true, legend:{display:true,position:'bottom'} }
    });
  }
});
</script>
<?php } ?>
