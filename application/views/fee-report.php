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
      <h1><?= $page_title; ?></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo $base_url; ?>fee_management">Fee Records List</a></li>
        <li class="active"><?= $page_title; ?></li>
      </ol>
    </section>
    <section class="content">
      <div class="row">
        <?php include"comman/code_flashdata.php"; ?>
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-body">
              <?= form_open('fee_management/report', array('method'=>'post')); ?>
              <div class="row">
                <div class="col-md-3">
                  <label>Class</label>
                  <select class="form-control" name="class">
                    <option value="">All Classes</option>
                    <?php foreach($class_list as $c){ ?>
                    <option value="<?= $c->class; ?>" <?= ($filters['class']==$c->class)?'selected':''; ?>><?= $c->class; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Fee Month</label>
                  <select class="form-control" name="fee_month">
                    <option value="">All Months</option>
                    <?php foreach(array('January','February','March','April','May','June','July','August','September','October','November','December') as $m){ ?>
                    <option value="<?= $m; ?>" <?= ($filters['fee_month']==$m)?'selected':''; ?>><?= $m; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-2">
                  <label>Fee Year</label>
                  <input type="text" class="form-control" name="fee_year" value="<?= (!empty($filters['fee_year'])) ? $filters['fee_year'] : ''; ?>" placeholder="e.g. <?= date('Y'); ?>">
                </div>
                <div class="col-md-2">
                  <label>Status</label>
                  <select class="form-control" name="status">
                    <option value="">All</option>
                    <option value="Paid" <?= ($filters['status']=='Paid')?'selected':''; ?>>Paid</option>
                    <option value="Partial" <?= ($filters['status']=='Partial')?'selected':''; ?>>Partial</option>
                    <option value="Due" <?= ($filters['status']=='Due')?'selected':''; ?>>Due</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label>&nbsp;</label>
                  <button type="submit" class="btn btn-block btn-primary"><i class="fa fa-filter"></i> Filter</button>
                </div>
              </div>
              <?= form_close(); ?>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="small-box bg-green">
                <div class="inner">
                  <h3><?= $CI->currency($summary->total_collected); ?></h3>
                  <p>Total Fee Collected</p>
                </div>
                <div class="icon"><i class="fa fa-money"></i></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="small-box bg-red">
                <div class="inner">
                  <h3><?= $CI->currency($summary->total_outstanding); ?></h3>
                  <p>Total Outstanding / Pending</p>
                </div>
                <div class="icon"><i class="fa fa-exclamation-circle"></i></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="small-box bg-blue">
                <div class="inner">
                  <h3><?= $CI->currency($summary->total_billed); ?></h3>
                  <p>Total Fee Billed (Net of Discount)</p>
                </div>
                <div class="icon"><i class="fa fa-file-text"></i></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4"><p class="text-center"><span class="label label-success">Paid: <?= $summary->paid_count; ?></span></p></div>
            <div class="col-md-4"><p class="text-center"><span class="label label-warning">Partial: <?= $summary->partial_count; ?></span></p></div>
            <div class="col-md-4"><p class="text-center"><span class="label label-danger">Due: <?= $summary->due_count; ?></span></p></div>
          </div>

          <div class="box box-primary">
            <div class="box-body table-responsive no-padding">
              <table class="table table-bordered table-hover custom_hover">
                <thead class="bg-gray">
                  <tr>
                    <th>#</th>
                    <th>Registration No.</th>
                    <th>Student Name</th>
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
                  <?php if(!empty($rows)){ $i=1; foreach($rows as $r){ ?>
                  <tr>
                    <td><?= $i++; ?></td>
                    <td><?= $r->registration_no; ?></td>
                    <td><?= $r->student_name; ?></td>
                    <td><?= $r->class; ?></td>
                    <td><?= $r->fee_month.' '.$r->fee_year; ?></td>
                    <td class="text-right"><?= $CI->currency($r->fee_amount); ?></td>
                    <td class="text-right"><?= $CI->currency($r->paid_amount); ?></td>
                    <td class="text-right"><?= $CI->currency($r->balance); ?></td>
                    <td><?= show_date($r->due_date); ?></td>
                    <td>
                      <?php if($r->status=='Paid'){ ?><span class="label label-success">Paid</span>
                      <?php } elseif($r->status=='Partial'){ ?><span class="label label-warning">Partial</span>
                      <?php } else { ?><span class="label label-danger">Due</span><?php } ?>
                    </td>
                  </tr>
                  <?php } } else { ?>
                  <tr><td colspan="10" class="text-center text-bold">No Records Found for the Selected Filters!</td></tr>
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
<script>$(".report-fee-active-li").addClass("active");</script>
</body>
</html>
