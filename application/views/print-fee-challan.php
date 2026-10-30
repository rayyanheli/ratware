<!DOCTYPE html>
<html>
<head>
<?php include"comman/code_css.php"; ?>
<style type="text/css">
	@page { size: A4; margin: 10mm; }
	body{ font-family: arial; font-size: 13px; padding-top:15px; width:190mm; margin:0 auto; }
	.challan-box{ border:1px solid #333; padding:15px; margin-bottom:20px; page-break-inside: avoid; }
	table.table td { padding:6px 8px; }
	@media print {
		.no-print { display: none; }
		body{ width:100%; padding-top:0; }
	}
</style>
</head>
<body onload="window.print();">
<?php
$CI =& get_instance();
$store = get_store_details();
$balance = $fee->fee_amount - $fee->discount - $fee->paid_amount;
?>
<div class="container">
  <?php for($copy=1;$copy<=2;$copy++){ ?>
  <div class="challan-box">
    <div class="text-center">
      <h3><?= $store->store_name; ?></h3>
      <h4>Fee Challan / Payment Receipt</h4>
      <p><?= ($copy==1) ? '<b>Bank / Office Copy</b>' : '<b>Student Copy</b>'; ?></p>
    </div>
    <table class="table table-bordered">
      <tr>
        <td><b>Registration No.</b></td>
        <td><?= $fee->registration_no; ?></td>
        <td><b>Student Name</b></td>
        <td><?= $fee->student_name; ?></td>
      </tr>
      <tr>
        <td><b>Class</b></td>
        <td><?= $fee->student_class; ?></td>
        <td><b>Fee Month</b></td>
        <td><?= $fee->fee_month.' '.$fee->fee_year; ?></td>
      </tr>
      <tr>
        <td><b>Fee Amount</b></td>
        <td><?= $CI->currency($fee->fee_amount); ?></td>
        <td><b>Discount</b></td>
        <td><?= $CI->currency($fee->discount); ?></td>
      </tr>
      <tr>
        <td><b>Paid Amount</b></td>
        <td><?= $CI->currency($fee->paid_amount); ?></td>
        <td><b>Balance / Outstanding</b></td>
        <td><?= $CI->currency($balance); ?></td>
      </tr>
      <tr>
        <td><b>Due Date</b></td>
        <td><?= show_date($fee->due_date); ?></td>
        <td><b>Status</b></td>
        <td><?= $fee->status; ?></td>
      </tr>
    </table>
    <p style="margin-top:20px;">Signature: ______________________</p>
  </div>
  <?php } ?>
</div>
</body>
</html>
