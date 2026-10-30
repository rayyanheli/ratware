<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
<?php include "comman/code_css.php"; ?>
<style type="text/css">
  /* ===== LABEL DESIGNER STYLES ===== */

  .inner-div-2 { margin: 0.5in !important; }

  /* Designer Canvas */
  #designer_canvas {
    position: relative;
    background: #fff;
    border: 2px dashed #ccc;
    margin: 0 auto;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .canvas-element {
    position: absolute;
    padding: 2px 5px;
    border: 1px dashed #007bff;
    background: rgba(255,255,255,0.95);
    cursor: move;
    user-select: none;
    min-height: 20px;
    font-size: 12px;
    border-radius: 2px;
    box-sizing: border-box;
  }

  .canvas-element.selected {
    border: 2px solid #28a745;
    z-index: 100;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
  }

  .canvas-element .delete-btn {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 20px;
    height: 20px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 12px;
    line-height: 20px;
    text-align: center;
    cursor: pointer;
    display: none;
    z-index: 101;
    padding: 0;
  }

  .canvas-element:hover .delete-btn,
  .canvas-element.selected .delete-btn {
    display: block;
  }

  .resize-handle {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #007bff;
    border: 2px solid white;
    border-radius: 50%;
    display: none;
    z-index: 102;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
  }

  .canvas-element.selected .resize-handle { display: block; }
  .resize-se { bottom: -5px; right: -5px; cursor: se-resize; }
  .resize-sw { bottom: -5px; left: -5px; cursor: sw-resize; }
  .resize-ne { top: -5px; right: -5px; cursor: ne-resize; }
  .resize-nw { top: -5px; left: -5px; cursor: nw-resize; }

  /* Design Elements Sidebar */
  .design-element {
    padding: 12px;
    margin: 6px 0;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: grab;
    text-align: center;
    transition: all 0.2s ease;
    font-size: 13px;
    font-weight: 500;
  }

  .design-element:hover {
    background: #e9ecef;
    border-color: #007bff;
    transform: translateX(3px);
  }

  .design-element:active {
    cursor: grabbing;
  }

  .design-element i {
    display: block;
    font-size: 18px;
    margin-bottom: 4px;
    color: #007bff;
  }

  /* Preview */
  .label-sheet {
    background: white;
    border: 1px solid #ddd;
    padding: 20px;
    min-height: 200px;
    overflow: auto;
  }

  .label-item {
    border: 1px solid #333;
    background: white;
    display: inline-block;
    margin: 5px;
    vertical-align: top;
    overflow: hidden;
    position: relative;
    box-sizing: border-box;
  }

  .label-item-content {
    position: relative;
    width: 100%;
    height: 100%;
  }

  /* Items Table */
  #items_table tbody tr:hover {
    background-color: #f5f5f5;
  }

  .qty-input {
    width: 70px;
    text-align: center;
  }

  /* Template Modal */
  .template-item {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 8px;
    background: #fafafa;
    transition: all 0.2s;
  }

  .template-item:hover {
    background: #f0f0f0;
    border-color: #007bff;
  }

  /* Print styles */
  @media print {
    body * { visibility: hidden !important; }
    #print_area, #print_area * { visibility: visible !important; }
    #print_area { 
      position: absolute; 
      left: 0; 
      top: 0; 
      width: 100%; 
    }
    .no-print { display: none !important; }
  }

  /* Barcode in preview */
  .barcode-svg {
    max-width: 100%;
    height: auto;
  }

  /* Helper text */
  .help-text {
    color: #6c757d;
    font-size: 12px;
    margin-top: 8px;
  }

  /* Canvas wrapper */
  .canvas-wrapper {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    border: 1px solid #e9ecef;
  }
</style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include "sidebar.php"; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1><?= $page_title; ?> <small>Design & Print Item Labels</small></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?= $page_title; ?></li>
      </ol>
    </section>

    <section class="content">
      <div class="row">

        <!-- LEFT COLUMN: Items Selection & Templates -->
        <div class="col-md-4">

          <!-- Items Selection Panel -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Select Items</h3>
            </div>
            <div class="box-body">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-search"></i></span>
                  <input type="text" class="form-control" placeholder="Item name / Barcode / Item code" id="item_search" autocomplete="off">
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped" id="items_table">
                  <thead class="bg-primary">
                    <tr>
                      <th style="width:45%"><?= $this->lang->line('item_name'); ?></th>
                      <th style="width:20%"><?= $this->lang->line('quantity'); ?></th>
                      <th style="width:20%">Code</th>
                      <th style="width:15%"><?= $this->lang->line('action'); ?></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>

              <div class="text-center bg-success" style="padding: 10px; border-radius: 4px;">
                <h4 style="margin:0">Total Labels: <span class="text-bold" id="total_labels">0</span></h4>
              </div>
            </div>
          </div>

          <!-- Templates Panel -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-floppy-o"></i> Templates</h3>
            </div>
            <div class="box-body">
              <div class="form-group">
                <input type="text" class="form-control" id="template_name" placeholder="Template name (e.g. Standard Label)">
              </div>
              <button type="button" class="btn btn-success btn-block" id="save_template">
                <i class="fa fa-save"></i> Save Template
              </button>
              <button type="button" class="btn btn-info btn-block" id="load_template_btn" data-toggle="modal" data-target="#loadTemplateModal">
                <i class="fa fa-folder-open"></i> Load Template
              </button>
              <button type="button" class="btn btn-warning btn-block" id="export_template">
                <i class="fa fa-download"></i> Export Template
              </button>
              <div class="form-group" style="margin-top:10px;">
                <input type="file" class="form-control" id="import_template_file" accept=".json" style="display:none;">
                <button type="button" class="btn btn-default btn-block" id="import_template">
                  <i class="fa fa-upload"></i> Import Template
                </button>
              </div>
            </div>
          </div>

          <!-- Label Settings -->
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-cog"></i> Label Settings</h3>
            </div>
            <div class="box-body">
              <div class="form-group">
                <label>Label Size</label>
                <select class="form-control" id="label_size">
                  <option value="1x1.5">1" x 1.5" (Small)</option>
                  <option value="1.5x2.25">1.5" x 2.25" (Medium)</option>
                  <option value="1x2.625">1" x 2.625" (Avery 30/sheet)</option>
                  <option value="2x1">2" x 1" (Thermal)</option>
                  <option value="4x2">4" x 2" (Large)</option>
                  <option value="4x6">4" x 6" (Shipping)</option>
                  <option value="a4" selected>A4 Sheet (Custom)</option>
                </select>
              </div>
              <div class="form-group">
                <label>Barcode Format</label>
                <select class="form-control" id="barcode_format">
                  <option value="CODE128" selected>CODE128 (Auto)</option>
                  <option value="EAN13">EAN-13</option>
                  <option value="UPC">UPC</option>
                  <option value="CODE39">CODE39</option>
                  <option value="ITF14">ITF-14</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: Designer -->
        <div class="col-md-8">

          <!-- Designer Panel -->
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-paint-brush"></i> Label Designer</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-xs btn-default" id="undo_btn" disabled>
                  <i class="fa fa-undo"></i> Undo
                </button>
                <button type="button" class="btn btn-xs btn-danger" id="clear_canvas">
                  <i class="fa fa-trash"></i> Clear
                </button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <!-- Design Elements Sidebar -->
                <div class="col-md-3">
                  <label class="text-muted"><small>DRAG ELEMENTS TO CANVAS:</small></label>

                  <div class="design-element" data-type="item_name">
                    <i class="fa fa-tag"></i> Item Name
                  </div>
                  <div class="design-element" data-type="price">
                    <i class="fa fa-usd"></i> Price
                  </div>
                  <div class="design-element" data-type="sku">
                    <i class="fa fa-barcode"></i> SKU/Code
                  </div>
                  <div class="design-element" data-type="barcode">
                    <i class="fa fa-barcode"></i> Barcode Image
                  </div>
                  <div class="design-element" data-type="custom_text">
                    <i class="fa fa-font"></i> Custom Text
                  </div>
                  <div class="design-element" data-type="logo">
                    <i class="fa fa-image"></i> Store Logo
                  </div>
                  <div class="design-element" data-type="date">
                    <i class="fa fa-calendar"></i> Date
                  </div>
                  <div class="design-element" data-type="store_name">
                    <i class="fa fa-building"></i> Store Name
                  </div>

                  <hr style="margin: 15px 0; border-color: #ddd;">

                  <div class="bg-info" style="padding: 10px; border-radius: 4px; font-size: 12px;">
                    <i class="fa fa-info-circle"></i> <strong>Tips:</strong>
                    <ul style="padding-left: 15px; margin-bottom: 0; margin-top: 5px;">
                      <li>Drag elements to canvas</li>
                      <li>Click to select, drag to move</li>
                      <li>Drag corners to resize</li>
                      <li>Double-click text to edit</li>
                      <li>Press Delete key to remove</li>
                    </ul>
                  </div>
                </div>

                <!-- Canvas -->
                <div class="col-md-9 text-center">
                  <div class="canvas-wrapper">
                    <div id="designer_canvas" style="width: 400px; height: 200px;"></div>
                  </div>
                  <p class="help-text">
                    <i class="fa fa-arrows"></i> Canvas size adjusts based on label dimensions
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Preview Panel -->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-eye"></i> Preview</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-sm" id="generate_preview">
                  <i class="fa fa-refresh"></i> Generate Preview
                </button>
                <button type="button" class="btn btn-success btn-sm" id="print_labels">
                  <i class="fa fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-default btn-sm" id="download_pdf">
                  <i class="fa fa-file-pdf-o"></i> PDF
                </button>
              </div>
            </div>
            <div class="box-body">
              <div id="preview_container" class="label-sheet">
                <p class="text-center text-muted" style="padding: 40px 0;">
                  <i class="fa fa-picture-o fa-3x"></i><br><br>
                  Click "Generate Preview" to see your labels
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include "footer.php"; ?>
  <?php include "comman/code_js_sound.php"; ?>
  <?php include "comman/code_js.php"; ?>

  <!-- Load Template Modal -->
  <div class="modal fade" id="loadTemplateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title"><i class="fa fa-folder-open"></i> Load Saved Template</h4>
        </div>
        <div class="modal-body">
          <div id="templates_list">
            <p class="text-center text-muted">Loading templates...</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Hidden print area -->
  <div id="print_area" style="display:none;"></div>

  <!-- JsBarcode Library -->
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

  <script>
  // ============================================
  // LABEL DESIGNER - COMPLETE REBUILD v2.0
  // ============================================

  var base_url = "<?php echo $base_url; ?>";
  var store_id = "<?php echo get_current_store_id(); ?>";
  var warehouse_id = "<?php echo (isset($warehouse_id)) ? $warehouse_id : get_privileged_warehouses_ids(); ?>";

  // ===== STATE =====
  var canvasElements = [];
  var selectedElementId = null;
  var itemsList = [];
  var rowCounter = 0;
  var isDragging = false;
  var isResizing = false;
  var dragOffset = { x: 0, y: 0 };
  var resizeStart = { x: 0, y: 0, w: 0, h: 0 };
  var undoStack = [];
  var currentBarcodeFormat = 'CODE128';

  // Label sizes (pixels at 96 DPI)
  var labelSizes = {
    '1x1.5':     { w: 150,  h: 100, name: '1" x 1.5"' },
    '1.5x2.25':  { w: 216, h: 144, name: '1.5" x 2.25"' },
    '1x2.625':   { w: 96,  h: 252, name: '1" x 2.625"' },
    '2x1':       { w: 192, h: 96,  name: '2" x 1"' },
    '4x2':       { w: 384, h: 192, name: '4" x 2"' },
    '4x6':       { w: 384, h: 576, name: '4" x 6"' },
    'a4':        { w: 400, h: 200, name: 'A4 Sheet' }
  };

  // ============================================
  // INITIALIZATION
  // ============================================
  $(document).ready(function() {
      $("#item_search").off("focusout");
    initItemSearch();
    initDesigner();
    initTemplates();
    initKeyboardShortcuts();
    updateCanvasSize();

    // Barcode format change
    $("#barcode_format").on("change", function() {
      currentBarcodeFormat = $(this).val();
    });
  });

  // ============================================
  // ITEM SEARCH & TABLE
  // ============================================
function initItemSearch() {
    $("#item_search").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: base_url + 'items/get_json_items_details',
                dataType: "json",
                type: "GET",
                data: { 
                    name: request.term,
                    store_id: store_id,
                    warehouse_id: warehouse_id,
                    search_for: 'labels'
                },
                success: function(result) {
                    var items = [];
                    if (result && $.isArray(result)) {
                        $.each(result, function(index, value) {
                            var label = value.item_code + " - " + value.label;
                            if (value.barcode) label += " (" + value.barcode + ")";
                            items.push({
                                label: label,
                                value: value.id,
                                item: value
                            });
                        });
                    }
                    response(items);
                },
                error: function() {
                    response([]);
                }
            });
        },
        minLength: 1,
        autoFocus: true,
        select: function(event, ui) {
            if (ui.item && ui.item.item) {
                addItemToTable(ui.item.item);
            }
            $("#item_search").val('');
            return false;
        }
    });
}

function addItemToTable(item) {
    rowCounter++;
    var rowId = 'row_' + rowCounter;
    
    // Prevent duplicates - check if item already in table
    var exists = false;
    $("#items_table tbody tr").each(function() {
        if ($(this).data('item-id') == item.id) {
            var qtyInput = $(this).find('.qty-input');
            qtyInput.val(parseInt(qtyInput.val()) + 1);
            exists = true;
            updateTotalLabels();
            $(this).addClass('success').delay(300).queue(function(){
                $(this).removeClass('success').dequeue();
            });
            return false;
        }
    });
    
    if (exists) return;

    var html = '<tr id="' + rowId + '" ' +
               'data-item-id="' + item.id + '" ' +
               'data-item-name="' + escapeHtml(item.label || item.item_name || '') + '" ' +
               'data-price="' + (item.purchase_price || '0') + '" ' +
               'data-sales-price="' + (item.sales_price || item.purchase_price || '0') + '" ' +
               'data-sku="' + escapeHtml(item.item_code || '') + '" ' +
               'data-barcode="' + escapeHtml(item.barcode || item.item_code || '') + '">';
    html += '<td>' + escapeHtml(item.label || item.item_name || '') + '</td>';
    html += '<td><input type="number" class="form-control qty-input" value="1" min="1" max="999"></td>';
    html += '<td><small class="text-muted">' + escapeHtml(item.item_code || '') + '</small></td>';
    html += '<td><button type="button" class="btn btn-danger btn-xs btn-block" onclick="removeItemRow(\'' + rowId + '\')">' +
            '<i class="fa fa-trash"></i></button></td>';
    html += '</tr>';

    $("#items_table tbody").append(html);
    updateTotalLabels();

    if (typeof success !== 'undefined' && success.play) {
        success.currentTime = 0;
        success.play();
    }
}

  function removeItemRow(rowId) {
    $("#" + rowId).fadeOut(200, function() {
      $(this).remove();
      updateTotalLabels();
    });

    if (typeof failed !== 'undefined' && failed.play) {
      failed.currentTime = 0;
      failed.play();
    }
  }

  function updateTotalLabels() {
    var total = 0;
    $("#items_table tbody tr").each(function() {
      var qty = parseInt($(this).find('.qty-input').val()) || 0;
      total += qty;
    });
    $("#total_labels").text(total).hide().fadeIn(100);
  }

  $(document).on('change', '.qty-input', function() {
    var val = parseInt($(this).val()) || 1;
    if (val < 1) val = 1;
    if (val > 999) val = 999;
    $(this).val(val);
    updateTotalLabels();
  });

  // ============================================
  // LABEL DESIGNER CANVAS
  // ============================================
  function initDesigner() {
    // Drag from sidebar
    $(".design-element").on("mousedown", function(e) {
      var type = $(this).data("type");
      var ghost = $(this).clone()
        .css({
          position: 'fixed',
          left: e.clientX - 50,
          top: e.clientY - 20,
          width: '120px',
          zIndex: 9999,
          opacity: 0.85,
          pointerEvents: 'none',
          boxShadow: '0 4px 12px rgba(0,0,0,0.3)'
        })
        .appendTo('body');

      $(document).on("mousemove.drag", function(e) {
        ghost.css({ left: e.clientX - 50, top: e.clientY - 20 });
      });

      $(document).on("mouseup.drag", function(e) {
        $(document).off("mousemove.drag mouseup.drag");
        ghost.remove();

        var canvas = $("#designer_canvas");
        var offset = canvas.offset();
        var cw = canvas.outerWidth();
        var ch = canvas.outerHeight();

        if (e.pageX >= offset.left && e.pageX <= offset.left + cw &&
            e.pageY >= offset.top && e.pageY <= offset.top + ch) {
          var x = e.pageX - offset.left - 50;
          var y = e.pageY - offset.top - 15;
          saveState();
          createCanvasElement(type, x, y);
        }
      });

      e.preventDefault();
    });

    // Canvas click to deselect
    $("#designer_canvas").on("click", function(e) {
      if (e.target.id === "designer_canvas") {
        deselectAll();
      }
    });

    // Label size change
    $("#label_size").on("change", function() {
      saveState();
      updateCanvasSize();
    });

    // Clear canvas
    $("#clear_canvas").on("click", function() {
      if (canvasElements.length === 0) return;
      if (confirm("Clear all elements from canvas? This cannot be undone.")) {
        saveState();
        canvasElements = [];
        selectedElementId = null;
        renderCanvas();
      }
    });

    // Undo
    $("#undo_btn").on("click", function() {
      undo();
    });

    // Generate preview
    $("#generate_preview").on("click", generatePreview);

    // Print
    $("#print_labels").on("click", printLabels);

    // Download PDF (placeholder - uses print to PDF)
    $("#download_pdf").on("click", function() {
      alert("Use the Print button and select 'Save as PDF' as the printer.");
    });
  }

  function updateCanvasSize() {
    var size = labelSizes[$("#label_size").val()] || labelSizes['a4'];
    $("#designer_canvas").css({
      width: size.w + 'px',
      height: size.h + 'px'
    });
  }

  function createCanvasElement(type, x, y, loadedData) {
    var id = loadedData ? loadedData.id : 'el_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
    var content = '';
    var isTextEditable = false;
    var defaultWidth = 120;
    var defaultHeight = 25;

    if (loadedData) {
      content = loadedData.content;
      type = loadedData.type;
      isTextEditable = loadedData.editable;
      defaultWidth = loadedData.width || 120;
      defaultHeight = loadedData.height || 25;
    } else {
      switch(type) {
        case 'item_name': 
          content = '{ITEM_NAME}'; 
          defaultWidth = 150; 
          defaultHeight = 20;
          break;
        case 'price': 
          content = '{PRICE}'; 
          defaultWidth = 80; 
          defaultHeight = 20;
          break;
        case 'sku': 
          content = '{SKU}'; 
          defaultWidth = 100; 
          defaultHeight = 20;
          break;
        case 'barcode': 
          content = '[BARCODE]'; 
          defaultWidth = 150; 
          defaultHeight = 50;
          break;
        case 'custom_text': 
          content = 'Custom Text'; 
          isTextEditable = true; 
          defaultWidth = 120; 
          defaultHeight = 25;
          break;
        case 'logo': 
          content = '[LOGO]'; 
          defaultWidth = 80; 
          defaultHeight = 40;
          break;
        case 'date': 
          content = '{DATE}'; 
          defaultWidth = 100; 
          defaultHeight = 20;
          break;
        case 'store_name': 
          content = '{STORE_NAME}'; 
          defaultWidth = 150; 
          defaultHeight = 20;
          break;
      }
    }

    var el = {
      id: id,
      type: type,
      x: loadedData ? loadedData.x : Math.max(0, Math.min(x, $("#designer_canvas").width() - defaultWidth)),
      y: loadedData ? loadedData.y : Math.max(0, Math.min(y, $("#designer_canvas").height() - defaultHeight)),
      width: defaultWidth,
      height: defaultHeight,
      content: content,
      editable: isTextEditable,
      fontSize: loadedData ? (loadedData.fontSize || 12) : 12,
      fontWeight: loadedData ? (loadedData.fontWeight || 'normal') : 'normal',
      textAlign: loadedData ? (loadedData.textAlign || 'left') : 'left'
    };

    canvasElements.push(el);
    renderCanvas();
    selectElement(id);

    return el;
  }

  function renderCanvas() {
    var canvas = $("#designer_canvas").empty();

    $.each(canvasElements, function(i, el) {
      var div = $('<div class="canvas-element" id="' + el.id + '">')
        .css({
          left: el.x + 'px',
          top: el.y + 'px',
          width: el.width + 'px',
          height: el.height + 'px',
          lineHeight: (el.height - 4) + 'px',
          fontSize: (el.fontSize || 12) + 'px',
          fontWeight: el.fontWeight || 'normal',
          textAlign: el.textAlign || 'left'
        })
        .attr('data-type', el.type)
        .html(
          '<span class="el-content">' + escapeHtml(el.content) + '</span>' +
          '<button class="delete-btn" title="Remove">&times;</button>' +
          '<div class="resize-handle resize-se" title="Resize"></div>'
        );

      if (el.id === selectedElementId) {
        div.addClass('selected');
      }

      canvas.append(div);
      attachElementEvents(div, el);
    });
  }

  function attachElementEvents(div, el) {
    // Click to select
    div.on("mousedown", function(e) {
      if ($(e.target).hasClass('delete-btn')) {
        e.stopPropagation();
        saveState();
        deleteElement(el.id);
        return;
      }

      selectElement(el.id);

      if ($(e.target).hasClass('resize-handle')) {
        startResize(e, el);
      } else {
        startDrag(e, el);
      }
      e.stopPropagation();
    });

    // Double-click to edit text
    if (el.editable) {
      div.on("dblclick", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var contentSpan = div.find('.el-content');
        var currentText = contentSpan.text();
        var input = $('<input type="text" style="width:100%;height:100%;padding:2px 4px;border:1px solid #007bff;background:#fff;box-sizing:border-box;">')
          .val(currentText);

        contentSpan.empty().append(input);
        input.focus().select();

        function finishEdit() {
          var newText = input.val() || 'Custom Text';
          el.content = newText;
          contentSpan.text(newText);
          saveState();
        }

        input.on("blur", finishEdit);
        input.on("keydown", function(e) {
          if (e.which === 13) { // Enter
            finishEdit();
          } else if (e.which === 27) { // Escape
            contentSpan.text(currentText);
          }
        });
      });
    }
  }

  function selectElement(id) {
    selectedElementId = id;
    $(".canvas-element").removeClass("selected");
    $("#" + id).addClass("selected");
  }

  function deselectAll() {
    selectedElementId = null;
    $(".canvas-element").removeClass("selected");
  }

  function deleteElement(id) {
    canvasElements = canvasElements.filter(function(el) { return el.id !== id; });
    if (selectedElementId === id) selectedElementId = null;
    renderCanvas();
  }

  function startDrag(e, el) {
    isDragging = true;
    var div = $("#" + el.id);
    var offset = div.offset();
    dragOffset.x = e.pageX - offset.left;
    dragOffset.y = e.pageY - offset.top;

    $(document).on("mousemove.dragEl", function(e) {
      if (!isDragging) return;
      var canvas = $("#designer_canvas");
      var cOffset = canvas.offset();
      var newX = e.pageX - cOffset.left - dragOffset.x;
      var newY = e.pageY - cOffset.top - dragOffset.y;

      // Constrain to canvas bounds
      newX = Math.max(0, Math.min(newX, canvas.width() - el.width));
      newY = Math.max(0, Math.min(newY, canvas.height() - el.height));

      el.x = newX;
      el.y = newY;
      div.css({ left: newX + 'px', top: newY + 'px' });
    });

    $(document).on("mouseup.dragEl", function() {
      if (isDragging) {
        saveState();
      }
      isDragging = false;
      $(document).off("mousemove.dragEl mouseup.dragEl");
    });
  }

  function startResize(e, el) {
    isResizing = true;
    resizeStart.x = e.pageX;
    resizeStart.y = e.pageY;
    resizeStart.w = el.width;
    resizeStart.h = el.height;

    var div = $("#" + el.id);

    $(document).on("mousemove.resizeEl", function(e) {
      if (!isResizing) return;
      var dx = e.pageX - resizeStart.x;
      var dy = e.pageY - resizeStart.y;

      el.width = Math.max(30, resizeStart.w + dx);
      el.height = Math.max(15, resizeStart.h + dy);

      div.css({ width: el.width + 'px', height: el.height + 'px', lineHeight: (el.height - 4) + 'px' });
    });

    $(document).on("mouseup.resizeEl", function() {
      if (isResizing) {
        saveState();
      }
      isResizing = false;
      $(document).off("mousemove.resizeEl mouseup.resizeEl");
    });
  }

  // ============================================
  // KEYBOARD SHORTCUTS
  // ============================================
  function initKeyboardShortcuts() {
    $(document).on("keydown", function(e) {
      if (e.which === 46 && selectedElementId) { // Delete key
        saveState();
        deleteElement(selectedElementId);
      }
      if ((e.ctrlKey || e.metaKey) && e.which === 90) { // Ctrl+Z
        e.preventDefault();
        undo();
      }
    });
  }

  // ============================================
  // UNDO SYSTEM
  // ============================================
  function saveState() {
    if (undoStack.length >= 20) undoStack.shift();
    undoStack.push(JSON.parse(JSON.stringify(canvasElements)));
    $("#undo_btn").prop("disabled", false);
  }

  function undo() {
    if (undoStack.length === 0) return;
    canvasElements = undoStack.pop();
    selectedElementId = null;
    renderCanvas();
    if (undoStack.length === 0) {
      $("#undo_btn").prop("disabled", true);
    }
  }

  // ============================================
  // TEMPLATES (localStorage + Import/Export)
  // ============================================
  function initTemplates() {
    // Save template
    $("#save_template").on("click", function() {
      var name = $("#template_name").val().trim();
      if (!name) {
        alert("Please enter a template name");
        return;
      }
      if (canvasElements.length === 0) {
        alert("Canvas is empty! Add some elements first.");
        return;
      }

      var templates = getTemplates();
      var existing = templates.findIndex(function(t) { return t.name === name; });

      var template = {
        name: name,
        elements: JSON.parse(JSON.stringify(canvasElements)),
        labelSize: $("#label_size").val(),
        barcodeFormat: $("#barcode_format").val(),
        created: new Date().toISOString(),
        version: '2.0'
      };

      if (existing >= 0) {
        if (confirm('Template "' + name + '" exists. Overwrite?')) {
          templates[existing] = template;
        } else {
          return;
        }
      } else {
        templates.push(template);
      }

      saveTemplates(templates);
      alert('Template "' + name + '" saved successfully!');
      $("#template_name").val('');
    });

    // Load template modal
    $("#loadTemplateModal").on("show.bs.modal", function() {
      var templates = getTemplates();
      var html = '';

      if (templates.length === 0) {
        html = '<p class="text-center text-muted" style="padding:20px;"><i class="fa fa-inbox fa-2x"></i><br><br>No saved templates</p>';
      } else {
        html = '<div class="list-group">';
        // Sort by newest first
        templates.sort(function(a, b) { return new Date(b.created) - new Date(a.created); });

        $.each(templates, function(i, t) {
          var date = new Date(t.created).toLocaleDateString();
          html += '<div class="template-item">';
          html += '<div class="row">';
          html += '<div class="col-xs-7">';
          html += '<h5 class="list-group-item-heading" style="margin:0 0 5px 0;"><i class="fa fa-file-text-o"></i> ' + escapeHtml(t.name) + '</h5>';
          html += '<small class="text-muted">' + (t.elements ? t.elements.length : 0) + ' elements | ' + (labelSizes[t.labelSize] ? labelSizes[t.labelSize].name : t.labelSize) + ' | ' + date + '</small>';
          html += '</div>';
          html += '<div class="col-xs-5 text-right">';
          html += '<button class="btn btn-success btn-sm" onclick="loadTemplate(' + i + ')"><i class="fa fa-check"></i> Load</button> ';
          html += '<button class="btn btn-danger btn-sm" onclick="deleteTemplate(' + i + ')"><i class="fa fa-trash"></i></button>';
          html += '</div></div></div>';
        });
        html += '</div>';
      }

      $("#templates_list").html(html);
    });

    // Export template
    $("#export_template").on("click", function() {
      if (canvasElements.length === 0) {
        alert("Canvas is empty! Nothing to export.");
        return;
      }
      var exportData = {
        name: $("#template_name").val() || 'template_' + new Date().getTime(),
        elements: canvasElements,
        labelSize: $("#label_size").val(),
        barcodeFormat: $("#barcode_format").val(),
        exported: new Date().toISOString(),
        version: '2.0'
      };

      var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportData, null, 2));
      var downloadAnchorNode = document.createElement('a');
      downloadAnchorNode.setAttribute("href", dataStr);
      downloadAnchorNode.setAttribute("download", exportData.name + ".json");
      document.body.appendChild(downloadAnchorNode);
      downloadAnchorNode.click();
      downloadAnchorNode.remove();
    });

    // Import template
    $("#import_template").on("click", function() {
      $("#import_template_file").trigger('click');
    });

    $("#import_template_file").on("change", function(e) {
      var file = e.target.files[0];
      if (!file) return;

      var reader = new FileReader();
      reader.onload = function(e) {
        try {
          var imported = JSON.parse(e.target.result);
          if (imported.elements && Array.isArray(imported.elements)) {
            if (confirm('Import template "' + (imported.name || 'Unnamed') + '"? This will replace current canvas.')) {
              canvasElements = imported.elements;
              if (imported.labelSize) $("#label_size").val(imported.labelSize).trigger('change');
              if (imported.barcodeFormat) $("#barcode_format").val(imported.barcodeFormat).trigger('change');
              if (imported.name) $("#template_name").val(imported.name);
              saveState();
              renderCanvas();
              alert('Template imported successfully!');
            }
          } else {
            alert('Invalid template file format.');
          }
        } catch(err) {
          alert('Error reading file: ' + err.message);
        }
      };
      reader.readAsText(file);
      $(this).val(''); // Reset
    });
  }

  function getTemplates() {
    try {
      return JSON.parse(localStorage.getItem('pos_label_templates_v2') || '[]');
    } catch(e) {
      return [];
    }
  }

  function saveTemplates(templates) {
    localStorage.setItem('pos_label_templates_v2', JSON.stringify(templates));
  }

  function loadTemplate(index) {
    var templates = getTemplates();
    if (!templates[index]) return;

    var t = templates[index];
    canvasElements = JSON.parse(JSON.stringify(t.elements || []));
    if (t.labelSize) $("#label_size").val(t.labelSize);
    if (t.barcodeFormat) $("#barcode_format").val(t.barcodeFormat);
    $("#template_name").val(t.name);
    updateCanvasSize();
    renderCanvas();
    $("#loadTemplateModal").modal("hide");
    saveState();
  }

  function deleteTemplate(index) {
    var templates = getTemplates();
    if (!templates[index]) return;
    if (confirm('Delete template "' + templates[index].name + '"?')) {
      templates.splice(index, 1);
      saveTemplates(templates);
      $("#loadTemplateModal").modal("hide");
    }
  }

  // ============================================
  // PREVIEW & PRINT
  // ============================================
  function generatePreview() {
    var items = getSelectedItems();
    if (items.length === 0) {
      $("#preview_container").html(
        '<p class="text-center text-warning" style="padding:40px 0;">' +
        '<i class="fa fa-exclamation-triangle fa-2x"></i><br><br>' +
        'Please add items to the table first</p>'
      );
      return;
    }
    if (canvasElements.length === 0) {
      $("#preview_container").html(
        '<p class="text-center text-warning" style="padding:40px 0;">' +
        '<i class="fa fa-exclamation-triangle fa-2x"></i><br><br>' +
        'Please design your label first. Drag elements from the left panel.</p>'
      );
      return;
    }

    var size = labelSizes[$("#label_size").val()] || labelSizes['a4'];
    var currentDate = new Date().toLocaleDateString();
    var storeName = "<?php echo get_store_name(); ?>" || 'Store';

    var html = '<div style="display:flex;flex-wrap:wrap;justify-content:center;align-items:flex-start;">';

    $.each(items, function(idx, item) {
      for (var i = 0; i < item.qty; i++) {
        html += '<div class="label-item" style="width:' + size.w + 'px;height:' + size.h + 'px;position:relative;overflow:hidden;">';
        html += '<div class="label-item-content">';

        $.each(canvasElements, function(j, el) {
          var content = el.content;

          // Replace placeholders with actual data
          content = content.replace(/{ITEM_NAME}/g, escapeHtml(item.name));
          content = content.replace(/{PRICE}/g, item.price ? parseFloat(item.price).toFixed(2) : '0.00');
          content = content.replace(/{SALES_PRICE}/g, item.salesPrice ? parseFloat(item.salesPrice).toFixed(2) : (item.price ? parseFloat(item.price).toFixed(2) : '0.00'));
          content = content.replace(/{SKU}/g, escapeHtml(item.sku));
          content = content.replace(/{DATE}/g, currentDate);
          content = content.replace(/{STORE_NAME}/g, escapeHtml(storeName));

          // Generate actual barcode SVG
          if (el.type === 'barcode') {
            var barcodeValue = escapeHtml(item.barcode || item.sku || '000000');
            var barcodeId = 'bc_' + idx + '_' + i + '_' + j;
            content = '<svg id="' + barcodeId + '" class="barcode-svg"></svg>';
            // We'll render this after inserting HTML
          } else if (el.type === 'logo') {
            content = '<div style="text-align:center;font-size:10px;color:#999;height:100%;display:flex;align-items:center;justify-content:center;">' +
                     '<i class="fa fa-image fa-2x"></i></div>';
          }

         html += '<div style="position:absolute;left:' + el.x + 'px;top:' + el.y + 'px;' +
        'width:' + el.width + 'px;height:' + el.height + 'px;overflow:hidden;' +
        'font-size:' + (el.fontSize || 12) + 'px;font-weight:bold;' +
        'font-family:Helvetica;' +
        'text-align:' + (el.textAlign || 'left') + ';line-height:1.2;">' + content + '</div>';
        });

        html += '</div></div>';
      }
    });

    html += '</div>';
    $("#preview_container").html(html);

    // Render barcodes using JsBarcode
    if (typeof JsBarcode !== 'undefined') {
      $.each(items, function(idx, item) {
        for (var i = 0; i < item.qty; i++) {
          $.each(canvasElements, function(j, el) {
            if (el.type === 'barcode') {
              var barcodeId = '#bc_' + idx + '_' + i + '_' + j;
              var barcodeValue = item.barcode || item.sku || '000000';
              try {
                JsBarcode(barcodeId, barcodeValue, {
                  format: currentBarcodeFormat,
                  width: 2,
                  height: el.height - 10,
                  displayValue: true,
                  fontSize: 18,
                  margin: 0,
                  fontOptions: "bold",
                  font: "Helvetica"
                });
              } catch(e) {
                // Fallback if barcode format invalid for value
                $(barcodeId).replaceWith('<div style="text-align:center;font-size:10px;color:red;">Invalid Barcode</div>');
              }
            }
          });
        }
      });
    }
  }

  function printLabels() {
    var previewHtml = $("#preview_container").html();
    if (!previewHtml || previewHtml.indexOf('label-item') === -1) {
      alert("Please generate preview first!");
      return;
    }

    // Clone preview for print area and ensure barcodes are rendered
    $("#print_area").html(previewHtml).show();

    // Re-render barcodes in print area if needed
    if (typeof JsBarcode !== 'undefined') {
      $("#print_area svg").each(function() {
        var svg = $(this);
        if (!svg.attr('data-rendered')) {
          // Barcode already rendered as SVG, should be fine
        }
      });
    }

    window.print();
    setTimeout(function() {
      $("#print_area").hide().empty();
    }, 100);
  }

  function getSelectedItems() {
    var items = [];
    $("#items_table tbody tr").each(function() {
      var row = $(this);
      items.push({
        id: row.data('item-id'),
        name: row.data('item-name'),
        price: row.data('price'),
        salesPrice: row.data('sales-price'),
        sku: row.data('sku'),
        barcode: row.data('barcode'),
        qty: parseInt(row.find('.qty-input').val()) || 1
      });
    });
    return items;
  }

  // ============================================
  // UTILITIES
  // ============================================
  function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(String(text)));
    return div.innerHTML;
  }
  </script>

  <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
</body>
</html>