<!DOCTYPE html>
<html>

<head>
<!-- TABLES CSS CODE -->
<?php include"comman/code_css.php"; ?>

<!-- Lightbox -->
<link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/lightbox/ekko-lightbox.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include"sidebar.php"; ?>
  <style>
    @media(max-width: 480px){
      .box-header>.box-tools {
          position: absolute;
          right: -13px;
          top: -106px;
      }
    }
  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$page_title;?>
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?=$page_title;?></li>
      </ol>
    </section>
    
    <!-- Warehouse wise stock view -->
    <div class="view_warehouse_wise_stock_item">
    </div>
    <!-- Warehouse wise stock view end-->

    <!-- Main content -->
    <?= form_open('#', array('class' => '', 'id' => 'table_form')); ?>
    <input type="hidden" id='base_url' value="<?=$base_url;?>">

    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
        <?php include"comman/code_flashdata.php"; ?>
        <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <!-- <h3 class="box-title"><?=$page_title;?></h3> -->
              

                <div class="row">
                    <div class="col-md-12">                                  
                      <!-- Warehouse Code -->
                      <?php if(warehouse_module()){ ?>
                        <div class="col-md-3">
                    <?php $this->load->view('warehouse/warehouse_code',array('show_warehouse_select_box'=>true,'div_length'=>'',
                      'label_length'=>'','show_all'=>'true','show_all_option'=>true,'remove_star'=>true)); ?>
                    <!-- Warehouse Code end -->
                    </div>
                    <?php } ?>
                    <?php if(service_module() && $CI->permissions('services_view')){ ?>
                    <div class="col-md-3">
                        <label for="item_type" class=" control-label">Item Type</label>
                          <select class="form-control select2" id="item_type" name="item_type"  style="width: 100%;">
                            <?php if($CI->permissions('items_view') && $CI->permissions('services_view')){?>
                              <option value=''>All</option>
                            <?php } ?>  
                            <?php if($CI->permissions('items_view')){?>
                              <option value='Items'>Items</option>
                            <?php } ?>
                            <?php if($CI->permissions('services_view')){?>
                              <option value='Services'>Services</option>
                            <?php } ?>
                          </select>
                    </div>
                  <?php }else{ ?>
                    <input type="hidden" id="item_type" value="Items">
                    <?php } ?>
                    
                  </div>
                </div>

              <?php if($CI->permissions('items_add') || $CI->permissions('services_add')) { ?>
              <div class="box-tools">      
                <?php if($CI->permissions('items_add')){ ?>          
                <a class="btn btn-info margin" href="<?php echo $base_url; ?>items/add">
                <i class="fa fa-plus " ></i> <?= $this->lang->line('create_item'); ?></a>
                <?php } ?>
                <?php if(service_module() && $CI->permissions('services_add')){ ?>
                <a class="btn btn-success margin" href="<?php echo $base_url; ?>services/add">
                <i class="fa fa-plus " ></i> <?= $this->lang->line('create_service'); ?></a>
              <?php } ?>
              </div>
             <?php } ?>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example2" class="table table-bordered custom_hover" width="100%">
                <thead class="bg-gray ">
                <tr>
                  <th class="text-center">
                    <input type="checkbox" class="group_check checkbox" >
                  </th>
                  <th><?= $this->lang->line('image'); ?></th>
                  <!-- <th><?= $this->lang->line('store_name'); ?></th> -->
                  <th><?= $this->lang->line('item_code'); ?></th>
                  <th><?= $this->lang->line('item_name'); ?></th>
                  <th><?= $this->lang->line('brand'); ?></th>
                  <th><?= $this->lang->line('category'); ?>/<br><?= $this->lang->line('item_type'); ?></th>
                  <th><?= $this->lang->line('unit'); ?></th>
                  <th><?= $this->lang->line('stock'); ?></th>
                  <th><?= $this->lang->line('alert_quantity'); ?></th>
                  <th><?= $this->lang->line('sales_price'); ?></th>
                  <th><?= $this->lang->line('tax'); ?></th>
	         	  	  <th><?= $this->lang->line('status'); ?></th>
                  <th><?= $this->lang->line('action'); ?></th>
                </tr>
                </thead>
                <tbody>
				
                </tbody>
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
     <?= form_close();?>
  </div>
  <!-- /.content-wrapper -->
  <?php include"footer.php"; ?>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- SOUND CODE -->
<?php include"comman/code_js_sound.php"; ?>
<!-- TABLES CODE -->
<?php include"comman/code_js.php"; ?>
<!-- Lightbox -->
<script src="<?php echo $theme_link; ?>plugins/lightbox/ekko-lightbox.js"></script>
<script type="text/javascript">
  $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });
</script>
<script type="text/javascript">
  function load_datatable(){

    

    var table = $('#example2').DataTable({ 
        "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
      /* FOR EXPORT BUTTONS START*/
  dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr><"pull-right margin-left-10 "B>>>tip',
 /* dom:'<"row"<"col-sm-12"<"pull-left"B><"pull-right">>> <"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',*/
      buttons: {
        buttons: [
            {
                className: 'btn bg-red color-palette btn-flat hidden delete_btn pull-left',
                text: 'Delete',
                action: function ( e, dt, node, config ) {
                    multi_delete();
                }
            },
            { extend: 'copy', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'excel', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'pdf', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'print', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'csv', className: 'btn bg-teal color-palette btn-flat',exportOptions: { columns: [2,3,4,5,6,7,8,9,10,11]} },
            { extend: 'colvis', className: 'btn bg-teal color-palette btn-flat',text:'Columns' },  

            ]
        },
        /* FOR EXPORT BUTTONS END */

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "responsive": true,
        language: {
            processing: '<div class="text-primary bg-primary" style="position: relative;z-index:100;overflow: visible;">Processing...</div>'
        },
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('items/ajax_list')?>",
            "type": "POST",
            "data": {
                      warehouse_id: $("#warehouse_id").val(),
                      item_type: $("#item_type").val(),
                    },
            complete: function (data) {
             $('.column_checkbox').iCheck({
                checkboxClass: 'icheckbox_square-orange',
                /*uncheckedClass: 'bg-white',*/
                radioClass: 'iradio_square-orange',
                increaseArea: '10%' // optional
              });
             call_code();
              //$(".delete_btn").hide();
             },

        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ 0,12 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        {
            "targets" :[0],
            "className": "text-center",
        },
        
        ],
    });
    new $.fn.dataTable.FixedHeader( table );
  }
$(document).ready(function() {
    //datatables
   load_datatable();
});
$("#warehouse_id,#item_type").on("change",function(){
    $('#example2').DataTable().destroy();
    load_datatable();
});
</script>


<script src="<?php echo $theme_link; ?>js/items.js"></script>

<!-- Make sidebar menu hughlighter/selector -->
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>

<!-- Shades/Sizes Modal -->
<div class="modal fade" id="shades_sizes_modal" tabindex="-1" role="dialog" aria-labelledby="shades_sizes_modal_label">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-orange">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="shades_sizes_modal_label"><i class="fa fa-tags"></i> Add Sizes or Shades - <span id="modal_item_name"></span></h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="modal_item_id" value="">
        
        <div class="row margin-bottom-10">
          <div class="col-md-12 text-right">
            <button type="button" class="btn btn-success btn-sm" onclick="add_new_shade_size_row()">
              <i class="fa fa-plus"></i> Add New Row
            </button>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="shades_sizes_table">
            <thead class="bg-gray">
              <tr>
                <th style="width:15%">Type</th>
                <th style="width:20%">Value</th>
                <th style="width:15%">Additional Price</th>
                <th style="width:15%">Stock Qty</th>
                <th style="width:15%">Barcode</th>
                <th style="width:15%">SKU</th>
                <th style="width:5%">Action</th>
              </tr>
            </thead>
            <tbody id="shades_sizes_tbody">
              <!-- Dynamic rows -->
            </tbody>
          </table>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="save_shades_sizes_btn" onclick="save_shades_sizes()">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Shades/Sizes Modal Functions
var shade_size_row_count = 0;

function open_shades_sizes_modal(item_id, item_name) {
    $('#modal_item_id').val(item_id);
    $('#modal_item_name').text(item_name);
    $('#shades_sizes_tbody').html('');
    shade_size_row_count = 0;
    
    // Load existing data
    $.ajax({
        url: "<?php echo site_url('items/get_item_shades_sizes')?>",
        type: "POST",
        data: {item_id: item_id},
        dataType: 'json',
        success: function(data) {
            if(data && data.length > 0) {
                $.each(data, function(index, row) {
                    add_new_shade_size_row(row);
                });
            } else {
                add_new_shade_size_row(); // Add one empty row
            }
            $('#shades_sizes_modal').modal('show');
        },
        error: function() {
            add_new_shade_size_row(); // Add one empty row on error
            $('#shades_sizes_modal').modal('show');
        }
    });
}

function add_new_shade_size_row(existing_data) {
    shade_size_row_count++;
    var row_id = 'ss_row_' + shade_size_row_count;
    
    var type_select = '<select class="form-control ss_type" style="width:100%">' +
        '<option value="size" ' + (existing_data && existing_data.type == 'size' ? 'selected' : '') + '>Size</option>' +
        '<option value="shade" ' + (existing_data && existing_data.type == 'shade' ? 'selected' : '') + '>Shade</option>' +
        '<option value="color" ' + (existing_data && existing_data.type == 'color' ? 'selected' : '') + '>Color</option>' +
        '<option value="variant" ' + (existing_data && existing_data.type == 'variant' ? 'selected' : '') + '>Variant</option>' +
        '</select>';
    
    var html = '<tr id="' + row_id + '" data-id="' + (existing_data ? existing_data.id : '') + '">' +
        '<td>' + type_select + '</td>' +
        '<td><input type="text" class="form-control ss_value" value="' + (existing_data ? existing_data.value : '') + '" placeholder="e.g., Small, Red"></td>' +
        '<td><input type="number" class="form-control ss_additional_price" value="' + (existing_data ? existing_data.additional_price : '0') + '" step="0.01" min="0"></td>' +
        '<td><input type="number" class="form-control ss_stock_qty" value="' + (existing_data ? existing_data.stock_qty : '0') + '" step="0.01" min="0"></td>' +
        '<td><input type="text" class="form-control ss_barcode" value="' + (existing_data ? existing_data.barcode : '') + '" placeholder="Barcode"></td>' +
        '<td><input type="text" class="form-control ss_sku" value="' + (existing_data ? existing_data.sku : '') + '" placeholder="SKU"></td>' +
        '<td class="text-center">' +
        '<button type="button" class="btn btn-danger btn-xs" onclick="remove_shade_size_row(\'' + row_id + '\', ' + (existing_data ? existing_data.id : 0) + ')">' +
        '<i class="fa fa-trash"></i>' +
        '</button>' +
        '</td>' +
        '</tr>';
    
    $('#shades_sizes_tbody').append(html);
    
    // Initialize select2 for the new select
    $('#' + row_id + ' .ss_type').select2({
        minimumResultsForSearch: Infinity,
        width: '100%'
    });
}

function remove_shade_size_row(row_id, db_id) {
    if(db_id > 0) {
        // Delete from database
        if(confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: "<?php echo site_url('items/delete_item_shade_size')?>",
                type: "POST",
                data: {id: db_id},
                success: function(response) {
                    if(response.trim() == 'success') {
                        $('#' + row_id).remove();
                    } else {
                        alert('Failed to delete!');
                    }
                }
            });
        }
    } else {
        // Just remove from DOM
        $('#' + row_id).remove();
    }
}

function save_shades_sizes() {
    var item_id = $('#modal_item_id').val();
    var rows = [];
    
    $('#shades_sizes_tbody tr').each(function() {
        var row = {
            id: $(this).data('id') || '',
            type: $(this).find('.ss_type').val(),
            value: $(this).find('.ss_value').val(),
            additional_price: $(this).find('.ss_additional_price').val(),
            stock_qty: $(this).find('.ss_stock_qty').val(),
            barcode: $(this).find('.ss_barcode').val(),
            sku: $(this).find('.ss_sku').val()
        };
        
        if(row.value.trim() != '') {
            rows.push(row);
        }
    });
    
    if(rows.length == 0) {
        alert('Please add at least one valid size/shade!');
        return;
    }
    
    $('#save_shades_sizes_btn').html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: "<?php echo site_url('items/save_item_shades_sizes')?>",
        type: "POST",
        data: {
            item_id: item_id,
            rows: rows
        },
        success: function(response) {
            if(response.trim() == 'success') {
                $('#shades_sizes_modal').modal('hide');
                toastr.success('Sizes/Shades saved successfully!');
            } else {
                alert('Failed to save! Please try again.');
            }
            $('#save_shades_sizes_btn').html('<i class="fa fa-save"></i> Save Changes').prop('disabled', false);
        },
        error: function() {
            alert('Error occurred while saving!');
            $('#save_shades_sizes_btn').html('<i class="fa fa-save"></i> Save Changes').prop('disabled', false);
        }
    });
}
</script>
		
</body>
</html>
