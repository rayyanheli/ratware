<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <?php include"comman/code_css.php"; ?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: 58mm auto;
            margin: 2mm 1mm;
        }

        @media print {
            .no-print { display: none !important; }
            body { width: 54mm; }
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: bold;
            width: 54mm;
            max-width: 54mm;
            margin: 0 auto;
            padding: 0 1px;
            color: #000;
            overflow: hidden;
        }

        .txt-center { text-align: center; }
        .txt-right  { text-align: right; }
        .bold       { font-weight: bold; }

        .store-name {
            font-size: 15px;
            font-weight: 900;
            text-align: center;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin: 2px 0;
        }
        .store-sub {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            line-height: 1.6;
        }

        .dashed { border: none; border-top: 2px dashed #000; margin: 4px 0; }
        .solid  { border: none; border-top: 2px solid #000;  margin: 4px 0; }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            font-weight: bold;
            margin: 2px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .items-table th {
            font-size: 10px;
            font-weight: 900;
            padding: 3px 1px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            text-transform: uppercase;
        }
        .items-table td {
            font-size: 10px;
            font-weight: bold;
            padding: 3px 1px;
            vertical-align: top;
            word-break: break-word;
        }
        .col-name { width: 34%; }
        .col-qty  { width: 10%; text-align: center; }
        .col-rate { width: 22%; text-align: right; white-space: nowrap; }
        .col-amt  { width: 34%; text-align: right; font-weight: 900; white-space: nowrap; }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            font-size: 10px;
            font-weight: bold;
            padding: 2px 1px;
        }
        .totals-table .lbl { text-align: left; }
        .totals-table .amt { text-align: right; width: 40%; white-space: nowrap; }

        .grand-row td {
            font-size: 14px;
            font-weight: 900;
            padding: 4px 1px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .paid-row td {
            font-size: 11px;
            font-weight: bold;
            padding: 2px 1px;
        }

        .change-row td {
            font-size: 12px;
            font-weight: 900;
            padding: 3px 1px;
            border-top: 2px dashed #000;
            text-transform: uppercase;
        }

        .footer-note {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            line-height: 1.5;
            margin: 2px 0;
        }
        .thankyou {
            font-size: 11px;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0px 0 0px;
            padding-bottom: 15px;
        }

        .no-print { margin-top: 10px; text-align: center; }
        .no-print button {
            padding: 5px 14px; margin: 3px;
            font-size: 12px; cursor: pointer;
            border: none; border-radius: 3px;
            font-weight: bold;
        }
        .btn-print { background: #28a745; color: #fff; }
        .btn-back  { background: #dc3545; color: #fff; }
    </style>
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
        window.addEventListener('afterprint', function () {
            window.close();
        });
    </script>
</head>
<body>
<?php
    $CI =& get_instance();

    $q3 = $this->db->query("
        SELECT b.coupon_id, b.coupon_amt, b.created_by,
               b.store_id, a.customer_name, a.mobile,
               a.gstin, a.tax_number, a.delete_bit,
               a.state_id, a.opening_balance, a.sales_due,
               b.sales_date, b.created_time, b.sales_code, b.sales_note,
               b.invoice_terms, b.discount_to_all_input, b.discount_to_all_type,
               COALESCE(b.grand_total,0)             AS grand_total,
               COALESCE(b.subtotal,0)                AS subtotal,
               COALESCE(b.paid_amount,0)             AS paid_amount,
               COALESCE(b.tot_discount_to_all_amt,0) AS tot_discount_to_all_amt,
               COALESCE(b.round_off,0)               AS round_off,
               b.payment_status
        FROM db_customers a, db_sales b
        WHERE a.id = b.customer_id AND b.id = '$sales_id'
    ");

    $res3 = $q3->row();

    $customer_name           = $res3->customer_name;
    $customer_state          = $res3->state_id;
    $customer_delete_bit     = $res3->delete_bit;
    $sales_date              = show_date($res3->sales_date);
    $created_time            = show_time($res3->created_time);
    $sales_code              = $res3->sales_code;
    $sales_note              = $res3->sales_note;
    $grand_total             = $res3->grand_total;
    $paid_amount             = $res3->paid_amount;
    $discount_to_all_input   = $res3->discount_to_all_input;
    $discount_to_all_type    = $res3->discount_to_all_type;
    $tot_discount_to_all_amt = $res3->tot_discount_to_all_amt;
    $coupon_id               = $res3->coupon_id;
    $coupon_amt              = $res3->coupon_amt;

    $previous_due = $res3->sales_due - ($res3->grand_total - $res3->paid_amount);
    $previous_due = ($previous_due > 0) ? $previous_due : 0;
    $total_due    = $res3->sales_due;

    $coupon_code  = '';
    $coupon_type  = '';
    $coupon_value = 0;
    if (!empty($coupon_id)) {
        $cd = get_customer_coupon_details($coupon_id);
        $coupon_code  = $cd->code;
        $coupon_value = $cd->value;
        $coupon_type  = $cd->type;
    }

    if (!empty($customer_state)) {
        $q6 = $this->db->query("SELECT state FROM db_states WHERE id='$customer_state'");
        if ($q6->num_rows() > 0) $customer_state = $q6->row()->state;
    }

    $q1 = $this->db->query("SELECT * FROM db_store WHERE id=" . $res3->store_id);
    $res1 = $q1->row();
    $store_name          = $res1->store_name;
    $company_mobile      = $res1->mobile;
    $company_phone       = $res1->phone;
    $company_email       = $res1->email;
    $company_state       = $res1->state;
    $company_city        = $res1->city;
    $company_address     = $res1->address;
    $company_postcode    = $res1->postcode;
    $company_gst_no      = $res1->gst_no;
    $company_vat_number  = $res1->vat_no;
    $store_logo          = (!empty($res1->store_logo)) ? $res1->store_logo : store_demo_logo();
    $store_website       = $res1->store_website;
    $mrp_column          = $res1->mrp_column;
    $previous_balance_bit      = $res1->previous_balance_bit;
    $t_and_c_status_pos        = $res1->t_and_c_status_pos;
?>

<!-- HEADER -->
<div class="txt-center" style="margin-bottom:2px;">
    <img src="<?= base_url($store_logo); ?>" style="max-width:38mm;max-height:14mm;height:auto;">
</div>
<div class="store-name"><?= $store_name; ?></div>
<div class="store-sub">
    <?php
        $parts = array_filter([trim($company_address), trim($company_city), trim($company_postcode)]);
        if ($parts) echo implode(', ', $parts) . '<br>';
        if (!empty(trim($company_mobile))) {
            echo 'Tel: ' . $company_mobile;
            if (!empty($company_phone)) echo ' / ' . $company_phone;
            echo '<br>';
        }
        if (!empty($company_email))  echo $company_email . '<br>';
        if (!empty($store_website))  echo $store_website . '<br>';
        if (!empty(trim($company_gst_no)) && gst_number())
            echo $this->lang->line('gst_number') . ': ' . $company_gst_no . '<br>';
        if (!empty(trim($company_vat_number)) && vat_number())
            echo $this->lang->line('vat_number') . ': ' . $company_vat_number . '<br>';
    ?>
</div>

<hr class="dashed">

<!-- META -->
<div class="meta-row">
    <span>Inv: <b>#<?= $sales_code; ?></b></span>
    <span><?= $sales_date; ?> <?= $created_time; ?></span>
</div>
<div class="meta-row">
    <span>Customer: <?= $customer_name; ?></span>
</div>
<div class="meta-row">
    <span>Seller: <?= ucfirst($res3->created_by); ?></span>
</div>

<!-- ITEMS -->
<table class="items-table">
    <thead>
        <tr>
            <th class="col-name txt-left">Item</th>
            <th class="col-qty">Qty</th>
            <th class="col-rate">Rate</th>
            <th class="col-amt">Amt</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $subtotal           = 0;
        $tax_amt            = 0;
        $overall_discounted = $tot_discount_to_all_amt + $coupon_amt;

        $this->db->select("c.item_name, c.mrp, a.sales_qty, a.tax_type,
                            a.price_per_unit, b.tax, b.tax_name, a.tax_amt,
                            a.discount_amt, a.unit_total_cost, a.total_cost");
        $this->db->where("a.sales_id", $sales_id);
        $this->db->from("db_salesitems a");
        $this->db->join("db_tax b",   "b.id=a.tax_id",  "left");
        $this->db->join("db_items c", "c.id=a.item_id", "left");
        $q2 = $this->db->get();

        foreach ($q2->result() as $res2):
            $subtotal           += $res2->total_cost;
            $tax_amt            += $res2->tax_amt;
            $overall_discounted += $res2->discount_amt;
    ?>
        <tr>
            <td class="col-name"><?= $res2->item_name; ?></td>
            <td class="col-qty"><?= (int)$res2->sales_qty; ?></td>
            <td class="col-rate"><?= store_number_format($res2->unit_total_cost); ?></td>
            <td class="col-amt"><?= store_number_format($res2->total_cost); ?></td>
        </tr>
    <?php endforeach;
        $before_tax = $subtotal - $tax_amt;
    ?>
    </tbody>
</table>

<!-- TOTALS -->
<hr class="dashed">
<table class="totals-table">
    <tr>
        <td class="lbl">Subtotal</td>
        <td class="amt"><?= store_number_format($before_tax); ?></td>
    </tr>

    <?php if($tax_amt > 0): ?>
    <tr>
        <td class="lbl">Tax</td>
        <td class="amt"><?= store_number_format($tax_amt); ?></td>
    </tr>
    <?php endif; ?>

    <?php if (!empty($coupon_code)): ?>
    <tr>
        <td class="lbl">Coupon (<?= ($coupon_type=='Percentage') ? $coupon_value.'%' : 'Fixed'; ?>)</td>
        <td class="amt">-<?= store_number_format($coupon_amt); ?></td>
    </tr>
    <?php endif; ?>

    <?php if (!empty($tot_discount_to_all_amt) && $tot_discount_to_all_amt != 0): ?>
    <tr>
        <td class="lbl">Discount <?= ($discount_to_all_type=='in_percentage') ? '('.$discount_to_all_input.'%)' : '(Fixed)'; ?></td>
        <td class="amt">-<?= store_number_format($tot_discount_to_all_amt); ?></td>
    </tr>
    <?php endif; ?>

    <?php if (!empty($overall_discounted) && $overall_discounted > 0): ?>
    <tr>
        <td class="lbl"><i>You Saved</i></td>
        <td class="amt"><i><?= store_number_format($overall_discounted); ?></i></td>
    </tr>
    <?php endif; ?>

    <!-- GRAND TOTAL -->
    <tr class="grand-row">
        <td class="lbl">TOTAL</td>
        <td class="amt txt-right"><?= store_number_format($grand_total); ?></td>
    </tr>

    <!-- PAYMENT METHODS -->
    <?php
        $q4 = $this->db->query("SELECT * FROM db_salespayments WHERE sales_id=$sales_id");
        $total_paid   = 0;
        $payment_rows = [];
        if ($q4->num_rows() > 0) {
            foreach ($q4->result() as $r) {
                $payment_rows[] = $r;
                $total_paid += $r->payment;
            }
        }
        foreach ($payment_rows as $r):
    ?>
    <tr class="paid-row">
        <td class="lbl"><?= $r->payment_type; ?></td>
        <td class="amt txt-right"><?= store_number_format($r->payment); ?></td>
    </tr>
    <?php endforeach; ?>

    <?php if (count($payment_rows) > 1): ?>
    <tr class="paid-row" style="border-top:1px dashed #000;">
        <td class="lbl">PAID</td>
        <td class="amt txt-right"><?= store_number_format($total_paid); ?></td>
    </tr>
    <?php elseif (count($payment_rows) == 0): ?>
    <tr class="paid-row">
        <td class="lbl">PAID</td>
        <td class="amt txt-right">UNPAID</td>
    </tr>
    <?php endif; ?>

    <!-- CHANGE -->
    <?php if (change_return_status()):
        $change_return_amount = get_change_return_amount($sales_id);
        if ($change_return_amount > 0): ?>
    <tr class="change-row">
        <td class="lbl">CHANGE</td>
        <td class="amt txt-right"><?= store_number_format($change_return_amount); ?></td>
    </tr>
    <?php endif; endif; ?>

    <!-- PREVIOUS DUE -->
    <?php if ($previous_balance_bit == 1 && $total_due > 0): ?>
    <tr><td colspan="2"><hr class="dashed" style="margin:2px 0;"></td></tr>
    <tr><td class="lbl">Prev Due</td><td class="amt"><?= store_number_format($previous_due); ?></td></tr>
    <tr class="bold"><td class="lbl">Total Due</td><td class="amt"><?= store_number_format($total_due); ?></td></tr>
    <?php endif; ?>

</table>

<hr class="dashed">

<?php if (!empty(trim($sales_note))): ?>
<div class="footer-note">Note: <?= $sales_note; ?></div>
<?php endif; ?>

<?php if ($t_and_c_status_pos): ?>
<div class="footer-note"><?= nl2br(get_invoice_terms_for_pos()); ?></div>
<hr class="dashed">
<?php endif; ?>

<tr>
						<td colspan="<?=$mrp_column+5?>" align="center">Thank you for visiting!</td>
					</tr>

<div class="no-print">
    <button class="btn-print" onclick="window.print();">Print</button>
    <?php if (isset($_GET['redirect'])): ?>
    <a href="<?= base_url() . $_GET['redirect']; ?>">
        <button class="btn-back">Back</button>
    </a>
    <?php endif; ?>
</div>

</body>
</html>
