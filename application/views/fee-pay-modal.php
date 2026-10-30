<?php $CI =& get_instance(); $balance = $fee->fee_amount - $fee->discount - $fee->paid_amount; ?>
<div class="modal fade" id="pay_now" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Receive Fee Payment - <?= $fee->student_name; ?> (<?= $fee->fee_month.' '.$fee->fee_year; ?>)</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="fee_id" value="<?= $fee->id; ?>">
        <div class="form-group">
          <label>Total Fee</label>
          <input type="text" class="form-control" readonly value="<?= $CI->currency($fee->fee_amount); ?>">
        </div>
        <div class="form-group">
          <label>Already Paid</label>
          <input type="text" class="form-control" readonly value="<?= $CI->currency($fee->paid_amount); ?>">
        </div>
        <div class="form-group">
          <label>Outstanding Balance</label>
          <input type="text" class="form-control" readonly value="<?= $CI->currency($balance); ?>">
        </div>
        <div class="form-group">
          <label>Amount to Receive<span class="text-danger">*</span></label>
          <input type="text" class="form-control only_currency" id="amount" data-due-amt="<?= $balance; ?>" placeholder="Enter amount">
          <span id="amount_msg" style="display:none" class="text-danger"></span>
        </div>
        <div class="form-group">
          <label>Payment Date<span class="text-danger">*</span></label>
          <input type="text" class="form-control datepicker" id="payment_date" value="<?= date('d-m-Y'); ?>">
          <span id="payment_date_msg" style="display:none" class="text-danger"></span>
        </div>
        <div class="form-group">
          <label>Payment Type</label>
          <select class="form-control" id="payment_type">
            <option value="Cash">Cash</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cheque">Cheque</option>
            <option value="Online">Online</option>
          </select>
        </div>
        <div class="form-group">
          <label>Note</label>
          <textarea class="form-control" id="note"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success payment_save" onclick="save_fee_payment()">Save Payment</button>
      </div>
    </div>
  </div>
</div>
