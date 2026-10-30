function pay_now(fee_id){
  $.post($("#base_url").val()+'fee_management/show_pay_modal', {id: fee_id}, function(result) {
    $(".pay_now_modal").html('').html(result);
    $('.datepicker').datepicker({ autoclose: true, format: 'dd-mm-yyyy', todayHighlight: true });
    $('#pay_now').modal('toggle');
  });
}

function save_fee_payment(){
  var base_url = $("#base_url").val();
  var fee_id = $("#fee_id").val();
  var amount = $("#amount").val();
  var payment_date = $("#payment_date").val();
  var payment_type = $("#payment_type").val();
  var note = $("#note").val();

  if(!amount || parseFloat(amount) <= 0){
    toastr["error"]("Please Enter Valid Amount!");
    return false;
  }
  if(!payment_date){
    toastr["error"]("Please Select Payment Date!");
    return false;
  }
  if(parseFloat(amount) > parseFloat($("#amount").attr('data-due-amt'))){
    toastr["error"]("Entered Amount Should not be Greater than Balance!");
    return false;
  }

  $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
  $(".payment_save").attr('disabled', true);
  $.post(base_url + 'fee_management/save_payment', {fee_id: fee_id, amount: amount, payment_date: payment_date, payment_type: payment_type, note: note}, function(result){
    if(result == "success"){
      $('#pay_now').modal('toggle');
      toastr["success"]("Payment Recorded Successfully!");
      $('#example2').DataTable().ajax.reload();
    } else if(result == "failed"){
      toastr["error"]("Sorry! Failed to save Record. Try again!");
    } else {
      toastr["error"](result);
    }
    $(".payment_save").attr('disabled', false);
    $(".overlay").remove();
  });
}

function delete_fee(q_id){
  var base_url = $("#base_url").val();
  if(confirm("Do You Wants to Delete Record ?")){
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $.post(base_url + "fee_management/delete_fee", {q_id: q_id}, function(result){
      if(result == "success"){
        toastr["success"]("Record Deleted Successfully!");
        $('#example2').DataTable().ajax.reload();
      } else {
        toastr["error"]("Failed to Delete. Try again!");
      }
      $(".overlay").remove();
    });
  }
}

function multi_delete_fee(){
  var base_url = $("#base_url").val();
  if(confirm("Are you sure?")){
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    data = new FormData($('#table_form')[0]);
    $.ajax({ type:'POST', url: base_url+'fee_management/multi_delete', data:data, cache:false, contentType:false, processData:false,
      success: function(result){
        if(result == "success"){
          toastr["success"]("Record Deleted Successfully!");
          $('#example2').DataTable().ajax.reload();
          $(".group_check").prop("checked", false).iCheck('update');
        } else {
          toastr["error"]("Failed to Delete. Try again!");
        }
        $(".overlay").remove();
      }});
  }
}
