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
      <h1><?= $page_title; ?> <small>Create Monthly Fee Records</small></h1>
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
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_single" data-toggle="tab">Single Student</a></li>
              <li><a href="#tab_class" data-toggle="tab">Whole Class (Bulk Generate)</a></li>
            </ul>
            <div class="tab-content">
              <!-- SINGLE STUDENT TAB -->
              <div class="tab-pane active" id="tab_single">
                <input type="hidden" id="base_url" value="<?php echo $base_url; ?>">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Student<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                          <select class="form-control select2" id="student_id" style="width:100%">
                            <?= $this->students->get_student_select_list($student_id); ?>
                          </select>
                          <span id="student_id_msg" style="display:none" class="text-danger"></span>
                        </div>
                        <label class="col-sm-2 control-label">Fee Month<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                          <select class="form-control" id="fee_month">
                            <?php foreach(array('January','February','March','April','May','June','July','August','September','October','November','December') as $m){ ?>
                            <option value="<?= $m; ?>" <?= ($m==date('F'))?'selected':''; ?>><?= $m; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Fee Year<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                          <input type="text" class="form-control" id="fee_year" value="<?= date('Y'); ?>">
                        </div>
                        <label class="col-sm-2 control-label">Fee Amount<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                          <input type="text" class="form-control only_currency" id="fee_amount">
                          <span id="fee_amount_msg" style="display:none" class="text-danger"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Discount</label>
                        <div class="col-sm-4">
                          <input type="text" class="form-control only_currency" id="discount" value="0">
                        </div>
                        <label class="col-sm-2 control-label">Due Date<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                          <input type="text" class="form-control datepicker" id="due_date">
                          <span id="due_date_msg" style="display:none" class="text-danger"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Note</label>
                        <div class="col-sm-4">
                          <textarea class="form-control" id="note"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-8 col-sm-offset-2 text-center">
                  <center>
                    <div class="col-md-3 col-md-offset-3">
                      <button type="button" id="save_single" class="btn btn-block btn-success">Generate Fee Record</button>
                    </div>
                    <div class="col-sm-3">
                      <a href="<?=base_url('fee_management');?>"><button type="button" class="col-sm-3 btn btn-block btn-warning">Close</button></a>
                    </div>
                  </center>
                </div>
              </div>
              <!-- WHOLE CLASS TAB -->
              <div class="tab-pane" id="tab_class">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Class</label>
                        <div class="col-sm-4">
                          <select class="form-control" id="bulk_class">
                            <option value="">All Classes</option>
                            <?php foreach($class_list as $c){ ?>
                            <option value="<?= $c->class; ?>"><?= $c->class; ?></option>
                            <?php } ?>
                          </select>
                          <span class="text-success">Leave blank to generate for every active student</span>
                        </div>
                        <label class="col-sm-2 control-label">Fee Month<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                          <select class="form-control" id="bulk_fee_month">
                            <?php foreach(array('January','February','March','April','May','June','July','August','September','October','November','December') as $m){ ?>
                            <option value="<?= $m; ?>" <?= ($m==date('F'))?'selected':''; ?>><?= $m; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Fee Year<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                          <input type="text" class="form-control" id="bulk_fee_year" value="<?= date('Y'); ?>">
                        </div>
                        <label class="col-sm-2 control-label">Due Date<span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                          <input type="text" class="form-control datepicker" id="bulk_due_date">
                          <span id="bulk_due_date_msg" style="display:none" class="text-danger"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                          <span class="text-info"><i class="fa fa-info-circle"></i> Fee amount is taken from each student's "Monthly Fee Amount" set on their profile. Students who already have a fee record for the selected month/year are skipped automatically.</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-8 col-sm-offset-2 text-center">
                  <center>
                    <div class="col-md-4 col-md-offset-2">
                      <button type="button" id="save_bulk" class="btn btn-block btn-success">Generate for Class</button>
                    </div>
                    <div class="col-sm-3">
                      <a href="<?=base_url('fee_management');?>"><button type="button" class="col-sm-3 btn btn-block btn-warning">Close</button></a>
                    </div>
                  </center>
                </div>
              </div>
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
<script src="<?php echo $theme_link; ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript">
  $('.datepicker').datepicker({ autoclose: true, format: 'dd-mm-yyyy', todayHighlight: true });
  $('#student_id').select2();
  $('#student_id').on('change', function(){
    var opt = $(this).find(':selected');
    if(opt.data('fee')){ $('#fee_amount').val(opt.data('fee')); }
  }).trigger('change');
</script>
<script type="text/javascript">
function check_field(id){
  if(!$("#"+id).val()){
    $('#'+id+'_msg').fadeIn(200).show().html('Required Field');
    return false;
  }
  $('#'+id+'_msg').fadeOut(200).hide();
  return true;
}

$('#save_single').on('click', function(){
  var flag = true;
  if(!check_field('student_id')) flag = false;
  if(!check_field('fee_amount')) flag = false;
  if(!check_field('due_date')) flag = false;
  if(!flag){ toastr["warning"]("You have Missed Something to Fillup!"); return; }

  var base_url = $("#base_url").val();
  $(".box-body").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
  $.post(base_url+'fee_management/generate_single', {
    student_id: $('#student_id').val(),
    fee_month: $('#fee_month').val(),
    fee_year: $('#fee_year').val(),
    fee_amount: $('#fee_amount').val(),
    discount: $('#discount').val(),
    due_date: $('#due_date').val(),
    note: $('#note').val()
  }, function(result){
    if(result.indexOf('success:') === 0){
      var new_id = result.split(':')[1];
      toastr["success"]("Fee Record Generated Successfully!");
      window.open(base_url + 'fee_management/print_challan/' + new_id, '_blank');
      window.location = base_url + 'fee_management';
    } else {
      toastr["error"](result);
    }
    $(".overlay").remove();
  });
});

$('#save_bulk').on('click', function(){
  var flag = true;
  if(!check_field('bulk_due_date')) flag = false;
  if(!flag){ toastr["warning"]("You have Missed Something to Fillup!"); return; }

  var base_url = $("#base_url").val();
  $(".box-body").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
  $.post(base_url+'fee_management/generate_for_class', {
    class: $('#bulk_class').val(),
    fee_month: $('#bulk_fee_month').val(),
    fee_year: $('#bulk_fee_year').val(),
    due_date: $('#bulk_due_date').val()
  }, function(result){
    toastr["info"](result);
    $(".overlay").remove();
  });
});
</script>
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
</body>
</html>
