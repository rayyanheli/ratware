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
        <small>View/Search Students</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?=$page_title;?></li>
      </ol>
    </section>

    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">
    <section class="content">
      <div class="row">
        <?php include"comman/code_flashdata.php"; ?>
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <?php if($CI->permissions('students_add')) { ?>
              <div class="box-tools">
                <a class="btn btn-block btn-info" href="<?php echo $base_url; ?>students/add">
                <i class="fa fa-plus"></i> New Student</a>
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
                  <th>Father Name</th>
                  <th>Roll No.</th>
                  <th>Class</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Monthly Fee</th>
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
<script type="text/javascript">
function load_datatable(){
   var table = $('#example2').DataTable({
        "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
        dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
        buttons: {
          buttons: [
              { className: 'btn bg-red color-palette btn-flat hidden delete_btn pull-left', text: 'Delete', action: function(e,dt,node,config){ multi_delete(); } },
              { extend: 'copy', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'excel', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'print', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'csv', className: 'btn bg-teal color-palette btn-flat' },
              { extend: 'colvis', className: 'btn bg-teal color-palette btn-flat', text:'Columns' },
          ]
        },
        "processing": true,
        "serverSide": true,
        "order": [],
        "responsive": true,
        language: { processing: '<div class="text-primary bg-primary" style="position: relative;z-index:100;overflow: visible;">Processing...</div>' },
        "ajax": {
            "url": "<?php echo site_url('students/ajax_list')?>",
            "type": "POST",
            complete: function (data) {
             $('.column_checkbox').iCheck({
                checkboxClass: 'icheckbox_square-orange',
                radioClass: 'iradio_square-orange',
                increaseArea: '10%'
              });
             call_code();
             },
        },
        "columnDefs": [
          { "targets": [0,10], "orderable": false },
          { "targets": [0], "className": "text-center" },
        ],
    });
    new $.fn.dataTable.FixedHeader( table );
}
$(document).ready(function() {
   load_datatable();
});
function update_status(id,status){
	var base_url=$("#base_url").val();
	$.post(base_url+"students/update_status",{id:id,status:status},function(result){
		if(result=="success"){
          toastr["success"]("Record Updated Successfully!");
          if(status==0){ var status_txt="Inactive"; var span_class="label label-danger"; $("#span_"+id).attr('onclick','update_status('+id+',1)'); }
          else{ var status_txt="Active"; var span_class="label label-success"; $("#span_"+id).attr('onclick','update_status('+id+',0)'); }
          $("#span_"+id).attr('class',span_class);
          $("#span_"+id).html(status_txt);
        }
        else{ toastr["error"](result); }
	});
}
function delete_students(q_id){
	var base_url=$("#base_url").val();
   if(confirm("Do You Wants to Delete Record ?")){
   	$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   	$.post(base_url+"students/delete_students",{q_id:q_id},function(result){
	   if(result=="success"){ toastr["success"]("Record Deleted Successfully!"); $('#example2').DataTable().ajax.reload(); }
	   else{ toastr["error"]("Failed to Delete. Try again!"); }
	   $(".overlay").remove();
   	});
   }
}
function multi_delete(){
	var base_url=$("#base_url").val();
	if(confirm("Are you sure?")){
		$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
		data = new FormData($('#table_form')[0]);
		$.ajax({ type:'POST', url: base_url+'students/multi_delete', data:data, cache:false, contentType:false, processData:false,
			success: function(result){
				if(result=="success"){ toastr["success"]("Record Deleted Successfully!"); $('#example2').DataTable().ajax.reload(); $(".group_check").prop("checked",false).iCheck('update'); }
				else{ toastr["error"]("Failed to Delete. Try again!"); }
				$(".overlay").remove();
		   }});
	}
}
</script>
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
</body>
</html>
