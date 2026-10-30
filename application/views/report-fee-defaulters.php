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
      <h1><?= $page_title; ?> <small>Students with pending or partial fee payments</small></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= $page_title; ?></li>
      </ol>
    </section>
    <section class="content">
      <div class="row">
        <?php include"comman/code_flashdata.php"; ?>
        <div class="col-md-12">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?= $CI->currency($total_outstanding); ?></h3>
              <p><i class="fa fa-exclamation-triangle"></i> Total Outstanding from <?= count($rows); ?> Unpaid/Partial Records</p>
            </div>
            <div class="icon"><i class="fa fa-money"></i></div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="box box-danger">
            <div class="box-body table-responsive no-padding">
              <table id="example2" class="table table-bordered table-hover custom_hover">
                <thead class="bg-gray">
                  <tr>
                    <th>#</th>
                    <th>Registration No.</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th>Mobile</th>
                    <th>Class</th>
                    <th>Fee Month</th>
                    <th>Fee Amount</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Due Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($rows)){ $i=1; foreach($rows as $r){
                    $overdue = (strtotime($r->due_date) < strtotime(date('Y-m-d')));
                  ?>
                  <tr class="<?= $overdue ? 'bg-danger-light' : ''; ?>" style="<?= $overdue ? 'background:#fff0f0;' : ''; ?>">
                    <td><?= $i++; ?></td>
                    <td><?= $r->registration_no; ?></td>
                    <td><?= $r->student_name; ?></td>
                    <td><?= $r->father_name; ?></td>
                    <td><?= $r->mobile; ?></td>
                    <td><?= $r->class; ?></td>
                    <td><?= $r->fee_month.' '.$r->fee_year; ?></td>
                    <td class="text-right"><?= $CI->currency($r->fee_amount); ?></td>
                    <td class="text-right"><?= $CI->currency($r->paid_amount); ?></td>
                    <td class="text-right text-red text-bold"><?= $CI->currency($r->balance); ?></td>
                    <td><?= show_date($r->due_date); ?> <?= $overdue ? '<span class="label label-danger">Overdue</span>' : ''; ?></td>
                    <td>
                      <?php if($r->status=='Partial'){ ?><span class="label label-warning">Partial</span>
                      <?php } else { ?><span class="label label-danger">Due</span><?php } ?>
                    </td>
                  </tr>
                  <?php } } else { ?>
                  <tr><td colspan="12" class="text-center text-bold text-green"><i class="fa fa-check-circle"></i> No Defaulters! All Fees Are Collected.</td></tr>
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
<script>
$(document).ready(function(){
  $('#example2').DataTable({
    dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
    buttons: [
      { extend: 'excel', className: 'btn bg-teal color-palette btn-flat' },
      { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat' },
      { extend: 'print', className: 'btn bg-teal color-palette btn-flat' },
    ],
    "order": [],
    "responsive": true
  });
});
</script>
<script>$(".report-fee-defaulters-active-li").addClass("active");</script>
</body>
</html>
