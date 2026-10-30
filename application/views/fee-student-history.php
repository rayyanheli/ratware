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
      <h1><?= $page_title; ?> <small><?= (!empty($student)) ? $student->registration_no.' - '.$student->student_name.' ('.$student->class.')' : ''; ?></small></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo $base_url; ?>students">Students List</a></li>
        <li class="active"><?= $page_title; ?></li>
      </ol>
    </section>
    <section class="content">
      <div class="row">
        <?php include"comman/code_flashdata.php"; ?>
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-body table-responsive no-padding">
              <table class="table table-bordered table-hover custom_hover">
                <thead class="bg-gray">
                  <tr>
                    <th>#</th>
                    <th>Fee Month</th>
                    <th>Fee Amount</th>
                    <th>Discount</th>
                    <th>Paid Amount</th>
                    <th>Balance</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($history)){ $i=1; foreach($history as $h){ ?>
                  <tr>
                    <td><?= $i++; ?></td>
                    <td><?= $h->fee_month.' '.$h->fee_year; ?></td>
                    <td class="text-right"><?= $CI->currency($h->fee_amount); ?></td>
                    <td class="text-right"><?= $CI->currency($h->discount); ?></td>
                    <td class="text-right"><?= $CI->currency($h->paid_amount); ?></td>
                    <td class="text-right"><?= $CI->currency($h->balance); ?></td>
                    <td><?= show_date($h->due_date); ?></td>
                    <td>
                      <?php if($h->status=='Paid'){ ?><span class="label label-success">Paid</span>
                      <?php } elseif($h->status=='Partial'){ ?><span class="label label-warning">Partial</span>
                      <?php } else { ?><span class="label label-danger">Due</span><?php } ?>
                    </td>
                    <td><a target="_blank" href="<?= base_url(); ?>fee_management/print_challan/<?= $h->id; ?>"><i class="fa fa-print"></i> Challan</a></td>
                  </tr>
                  <?php } } else { ?>
                  <tr><td colspan="9" class="text-center text-bold">No Fee Records Found for This Student!</td></tr>
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
</body>
</html>
