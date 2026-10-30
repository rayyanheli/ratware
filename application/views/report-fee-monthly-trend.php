<!DOCTYPE html>
<html>
<head>
<?php include"comman/code_css.php"; ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php include"sidebar.php"; $CI =& get_instance(); ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1><?= $page_title; ?> <small>Fee billing & collection trend, month over month</small></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= $page_title; ?></li>
      </ol>
    </section>
    <section class="content">
      <div class="row">
        <?php include"comman/code_flashdata.php"; ?>
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-line-chart"></i> Billed vs Collected vs Outstanding</h3>
            </div>
            <div class="box-body">
              <canvas id="trend_chart" height="90"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-body table-responsive no-padding">
              <table class="table table-bordered table-hover custom_hover">
                <thead class="bg-gray">
                  <tr>
                    <th>Month</th>
                    <th>Records</th>
                    <th>Billed</th>
                    <th>Collected</th>
                    <th>Outstanding</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($rows)){ foreach($rows as $r){ ?>
                  <tr>
                    <td><b><?= $r->fee_month.' '.$r->fee_year; ?></b></td>
                    <td><?= $r->total_records; ?></td>
                    <td class="text-right"><?= $CI->currency($r->billed); ?></td>
                    <td class="text-right text-green"><?= $CI->currency($r->collected); ?></td>
                    <td class="text-right text-red"><?= $CI->currency($r->outstanding); ?></td>
                  </tr>
                  <?php } } else { ?>
                  <tr><td colspan="5" class="text-center text-bold">No Fee Records Found!</td></tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include"footer.php"; ?>
  <div class="control-sidebar-bg"></div>
</div>
<?php include"comman/code_js_sound.php"; ?>
<?php include"comman/code_js.php"; ?>
<script src="<?php echo $theme_link; ?>plugins/chartjs/Chart.min.js"></script>
<script>
$(document).ready(function(){
  var labels = [<?php foreach($rows as $r){ echo "'".addslashes($r->fee_month.' '.$r->fee_year)."',"; } ?>];
  var billed = [<?php foreach($rows as $r){ echo $r->billed.','; } ?>];
  var collected = [<?php foreach($rows as $r){ echo $r->collected.','; } ?>];
  var outstanding = [<?php foreach($rows as $r){ echo $r->outstanding.','; } ?>];

  new Chart(document.getElementById('trend_chart'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        { label: 'Billed', borderColor:'#3c8dbc', backgroundColor:'rgba(60,141,188,0.1)', data: billed, fill:true },
        { label: 'Collected', borderColor:'#00a65a', backgroundColor:'rgba(0,166,90,0.1)', data: collected, fill:true },
        { label: 'Outstanding', borderColor:'#dd4b39', backgroundColor:'rgba(221,75,57,0.1)', data: outstanding, fill:true }
      ]
    },
    options: {
      responsive: true,
      scales: { yAxes: [{ ticks: { min: 0 } }] },
      legend: { display: true, position: 'top' }
    }
  });
});
</script>
<script>$(".report-fee-monthly-trend-active-li").addClass("active");</script>
</body>
</html>
