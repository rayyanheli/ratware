<!DOCTYPE html>
<html>
<head>
<?php include"comman/code_css.php"; ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php include"sidebar.php"; ?>
  <?php
    if(!isset($student_name)){
      $registration_no=$student_name=$father_name=$roll_no=$class=$mobile=$email=$address=$admission_date=$q_id='';
      $default_fee_amount = 0;
    }
  ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1><?= $page_title; ?> <small>Enter Student Information</small></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo $base_url; ?>students"><?= $this->lang->line('students_list'); ?></a></li>
        <li class="active"><?= $page_title; ?></li>
      </ol>
    </section>
    <section class="content">
      <div class="row">
        <?php include"comman/code_flashdata.php"; ?>
        <div class="col-md-12">
          <div class="nav-tabs-custom">
            <form class="form-horizontal" id="students-form" method="post">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
              <input type="hidden" id="base_url" value="<?php echo $base_url; ?>">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-10">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Registration No.</label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control" id="registration_no" name="registration_no" placeholder="Auto-generated if left blank" value="<?php print $registration_no; ?>">
                      </div>
                      <label class="col-sm-2 control-label">Student Name<label class="text-danger">*</label></label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control" id="student_name" name="student_name" value="<?php print $student_name; ?>">
                        <span id="student_name_msg" style="display:none" class="text-danger"></span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Father Name</label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control" id="father_name" name="father_name" value="<?php print $father_name; ?>">
                      </div>
                      <label class="col-sm-2 control-label">Roll No.</label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control" id="roll_no" name="roll_no" value="<?php print $roll_no; ?>">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Class<label class="text-danger">*</label></label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control" id="class" name="class" placeholder="e.g. Class 5, Grade 8, Nursery" value="<?php print $class; ?>">
                        <span id="class_msg" style="display:none" class="text-danger"></span>
                      </div>
                      <label class="col-sm-2 control-label">Monthly Fee Amount</label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control only_currency" id="default_fee_amount" name="default_fee_amount" value="<?php print store_number_format($default_fee_amount,0); ?>">
                        <span class="text-success">Used as default when generating monthly fee records</span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Mobile</label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control no_special_char_no_space" id="mobile" name="mobile" value="<?php print $mobile; ?>">
                      </div>
                      <label class="col-sm-2 control-label">Email</label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control" id="email" name="email" value="<?php print $email; ?>">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Admission Date</label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control datepicker" id="admission_date" name="admission_date" value="<?php print (!empty($admission_date)) ? show_date($admission_date) : ''; ?>">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Address</label>
                      <div class="col-sm-4">
                        <textarea class="form-control" id="address" name="address"><?php print $address; ?></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-8 col-sm-offset-2 text-center">
                <center>
                  <?php
                    if($student_name != ""){
                      $btn_name = "Update"; $btn_id = "update";
                      ?>
                      <input type="hidden" name="q_id" id="q_id" value="<?php echo $q_id;?>"/>
                      <?php
                    } else {
                      $btn_name = "Save"; $btn_id = "save";
                    }
                  ?>
                  <div class="col-md-3 col-md-offset-3">
                    <button type="button" id="<?php echo $btn_id;?>" class="btn btn-block btn-success" title="Save Data"><?php echo $btn_name;?></button>
                  </div>
                  <div class="col-sm-3">
                    <a href="<?=base_url('students');?>">
                      <button type="button" class="col-sm-3 btn btn-block btn-warning" title="Go Back">Close</button>
                    </a>
                  </div>
                </center>
              </div>
            </form>
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
</script>
<script src="<?php echo $theme_link; ?>js/students.js"></script>
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
</body>
</html>
