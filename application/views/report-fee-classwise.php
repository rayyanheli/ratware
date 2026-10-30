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
      <h1><?= $page_title; ?> <small>Fee performance broken down by class</small></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= $page_title; ?></li>
      </ol>
    </section>
    <section class="content">
      <div class="row">
        <?php include"comman/code_flashdata.php"; ?>
        <div class="col-md-5">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-pie-chart"></i> Collected vs Outstanding by Class</h3>
            </div>
            <div class="box-body">
              <canvas id="classwise_chart" height="260"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title table-responsive no-padding"><i class="fa fa-table"></i> Class Breakdown</h3>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-bordered table-hover custom_hover">
                <thead class="bg-gray">
                  <tr>
                    <th>Class</th>
                    <th>Records</th>
                    <th>Billed</th>
                    <th>Collected</th>
                    <th>Outstanding</th>
                    <th>Collection %</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($rows)){ foreach($rows as $r){
                    $pct = ($r->billed > 0) ? round(($r->collected / $r->billed) * 100) : 0;
                  ?>
                  <tr>
                    <td><b><?= $r->class; ?></b></td>
                    <td><?= $r->total_records; ?></td>
                    <td class="text-right"><?= $CI->currency($r->billed); ?></td>
                    <td class="text-right text-green"><?= $CI->currency($r->collected); ?></td>
                    <td class="text-right text-red"><?= $CI->currency($r->outstanding); ?></td>
                    <td>
                      <div class="progress" style="margin-bottom:0;">
                        <div class="progress-bar <?= ($pct>=80)?'progress-bar-success':(($pct>=40)?'progress-bar-warning':'progress-bar-danger'); ?>" style="width: <?= $pct; ?>%">
                          <?= $pct; ?>%
                        </div>
                      </div>
                    </td>
                  </tr>
                  <?php } } else { ?>
                  <tr><td colspan="6" class="text-center text-bold">No Fee Records Found!</td></tr>
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
  var labels = [<?php foreach($rows as $r){ echo "'".addslashes($r->class)."',"; } ?>];
  var collected = [<?php foreach($rows as $r){ echo $r->collected.','; } ?>];
  var outstanding = [<?php foreach($rows as $r){ echo $r->outstanding.','; } ?>];

  new Chart(document.getElementById('classwise_chart'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        { label: 'Collected', backgroundColor: '#00a65a', data: collected },
        { label: 'Outstanding', backgroundColor: '#dd4b39', data: outstanding }
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
<script>$(".report-fee-classwise-active-li").addClass("active");</script>
</body>
</html>
