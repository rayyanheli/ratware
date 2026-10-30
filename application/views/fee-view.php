<!DOCTYPE html>
<html>
<head>
<?php include"comman/code_css.php"; ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php include"sidebar.php"; ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        <?=$page_title;?>
        <small>View/Search Fee Records</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?=$page_title;?></li>
      </ol>
    </section>

    <div class="pay_now_modal"></div>

    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      <div class="row">
        <?php include"comman/code_flashdata.php"; ?>
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <div class="col-lg-2 col-xs-6">
                <select class="form-control" id="class_filter">
                  <option value="">All Classes</option>
                  <?php foreach($class_list as $c){ ?>
                  <option value="<?= $c->class; ?>"><?= $c->class; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-lg-2 col-xs-6">
                <select class="form-control" id="month_filter">
                  <option value="">All Months</option>
                  <?php foreach(array('January','February','March','April','May','June','July','August','September','October','November','December') as $m){ ?>
                  <option value="<?= $m; ?>"><?= $m; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-lg-2 col-xs-6">
                <select class="form-control" id="status_filter">
                  <option value="">All Status</option>
                  <option value="Paid">Paid</option>
                  <option value="Partial">Partial</option>
                  <option value="Due">Due</option>
                </select>
              </div>
              <?php if($CI->permissions('fee_add')) { ?>
              <div class="box-tools">
                <a class="btn btn-block btn-info" href="<?php echo $base_url; ?>fee_management/add">
                <i class="fa fa-plus"></i> Generate Fee Record</a>
              </div>
              <?php } ?>
              <?php if($CI->permissions('fee_report')) { ?>
              <div class="box-tools">
                <a class="btn btn-block btn-primary" href="<?php echo $base_url; ?>fee_management/report">
                <i class="fa fa-bar-chart"></i> Fee Report</a>
              </div>
              <?php } ?>
            </div>
            <div class="box-body">
              <table id="example2" class="table table-bordered custom_hover" width="100%">
                <thead class="bg-gray">
                <tr>
                  <th class="text-center"><input type="checkbox" class="group_check checkbox"></th>
                  <th>Registration No.</th>
                  <th>Student Name</th>
                  <th>Class</th>
                  <th>Fee Month</th>
                  <th>Fee Amount</th>
                  <th>Due Date</th>
                  <th>Balance / Outstanding</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?= form_close();?>
  </div>
  <?php include"footer.php"; ?>
  <div class="control-sidebar-bg"></div>
</div>

<?php include"comman/code_js_sound.php"; ?>
<?php include"comman/code_js.php"; ?>
<script src="<?php echo $theme_link; ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo $theme_link; ?>js/fee_management.js"></script>
<script type="text/javascript">
function load_datatable(){
   var table = $('#example2').DataTable({
        "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
        dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
        buttons: {
          buttons: [
              { className: 'btn bg-red color-palette btn-flat hidden delete_btn pull-left', text: 'Delete', action: function(e,dt,node,config){ multi_delete_fee(); } },
              { extend: 'copy', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'excel', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'print', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'csv', className: 'btn bg-teal color-palette btn-flat' },
          ]
        },
        "processing": true,
        "serverSide": true,
        "order": [],
        "responsive": true,
        language: { processing: '<div class="text-primary bg-primary" style="position: relative;z-index:100;overflow: visible;">Processing...</div>' },
        "ajax": {
            "url": "<?php echo site_url('fee_management/ajax_list')?>",
            "type": "POST",
            "data": function(d){
              d.class_filter = $('#class_filter').val();
              d.month_filter = $('#month_filter').val();
              d.status_filter = $('#status_filter').val();
            },
            complete: function (data) {
             $('.column_checkbox').iCheck({ checkboxClass: 'icheckbox_square-orange', radioClass: 'iradio_square-orange', increaseArea: '10%' });
             call_code();
             },
        },
        "columnDefs": [
          { "targets": [0,9], "orderable": false },
          { "targets": [0], "className": "text-center" },
        ],
    });
    new $.fn.dataTable.FixedHeader( table );
}
$(document).ready(function() {
   load_datatable();
});
$('#class_filter,#month_filter,#status_filter').on('change', function(){
  $('#example2').DataTable().ajax.reload();
});
</script>
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
</body>
</html>
