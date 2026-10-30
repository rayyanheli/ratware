$('#save,#update').on("click", function (e) {
	var base_url = $("#base_url").val();
	var flag = true;

	function check_field(id) {
		if (!$("#" + id).val()) {
			$('#' + id + '_msg').fadeIn(200).show().html('Required Field');
			flag = false;
		} else {
			$('#' + id + '_msg').fadeOut(200).hide();
		}
	}

	check_field("student_name");
	check_field("class");

	if (flag == false) {
		toastr["warning"]("You have Missed Something to Fillup!");
		return;
	}

	var this_id = this.id;

	if (this_id == "save") {
		e.preventDefault();
		data = new FormData($('#students-form')[0]);
		if (!xss_validation(data)) { return false; }
		$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
		$("#" + this_id).attr('disabled', true);
		$.ajax({
			type: 'POST', url: base_url + 'students/new_student', data: data, cache: false, contentType: false, processData: false,
			success: function (result) {
				if (result == "success") {
					window.location = base_url + "students";
					return;
				} else if (result == "failed") {
					toastr['error']("Sorry! Failed to save Record. Try again");
				} else {
					toastr['error'](result);
				}
				$("#" + this_id).attr('disabled', false);
				$(".overlay").remove();
			}
		});
	} else if (this_id == "update") {
		e.preventDefault();
		data = new FormData($('#students-form')[0]);
		if (!xss_validation(data)) { return false; }
		$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
		$("#" + this_id).attr('disabled', true);
		$.ajax({
			type: 'POST', url: base_url + 'students/update_student', data: data, cache: false, contentType: false, processData: false,
			success: function (result) {
				if (result == "success") {
					window.location = base_url + "students";
				} else if (result == "failed") {
					toastr["error"]("Sorry! Failed to save Record. Try again!");
				} else {
					toastr['error'](result);
				}
				$("#" + this_id).attr('disabled', false);
				$(".overlay").remove();
			}
		});
	}
});
