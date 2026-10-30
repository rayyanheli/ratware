<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php print $SITE_TITLE; ?> | Log in</title>
  <link rel='shortcut icon' href='<?php echo $theme_link; ?>images/favicon.ico' />
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>plugins/iCheck/square/blue.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/royal-blue-theme.css">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');

    body.login-page {
      background: #ffffff !important;
      font-family: 'Inter', -apple-system, sans-serif !important;
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow: hidden;
    }

    /* Split Main Structural Grid */
    .saas-split-container {
      display: flex;
      width: 100%;
      height: 100vh;
    }

    /* Left Side Showcase Panel */
    .saas-showcase-panel {
      flex: 1.1;
      position: relative;
      background: #002db3;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 80px;
      overflow: hidden;
    }

    /* High-Fidelity Premium POS Terminal Background */
    .saas-showcase-panel::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: 
        linear-gradient(180deg, rgba(0, 70, 254, 0.15) 0%, rgba(0, 45, 179, 0.92) 85%),
        url('https://images.unsplash.com/photo-1556742044-3c52d6e88c62?auto=format&fit=crop&w=1400&q=90');
      background-size: cover;
      background-position: center 30%;
      z-index: 1;
    }

    /* Elegant SaaS Branding Typography & Micro-copy */
    .saas-showcase-content {
      position: relative;
      z-index: 2;
      color: #ffffff;
      max-width: 580px;
      animation: saasFadeInLeft 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes saasFadeInLeft {
      from { opacity: 0; transform: translateX(-30px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .saas-showcase-content h1 {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
      font-weight: 800 !important;
      font-size: 38px;
      line-height: 1.2;
      letter-spacing: -0.03em;
      margin-bottom: 16px;
      color: #ffffff !important;
    }

    .saas-showcase-content p {
      font-size: 16px;
      line-height: 1.6;
      color: rgba(255, 255, 255, 0.85);
      margin-bottom: 32px;
      font-weight: 400;
    }

    /* Minimalist Feature Badges */
    .saas-feature-badges {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .saas-badge {
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      padding: 8px 16px;
      border-radius: 9999px;
      font-size: 13px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .saas-badge i { color: #336bff; }

    /* Right Side Clean Canvas Panel */
    .saas-interaction-panel {
      flex: 0.9;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px;
      overflow-y: auto;
    }

    .login-box {
      width: 100%;
      max-width: 420px;
      margin: 0 auto !important;
      animation: saasFadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes saasFadeUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-logo {
      margin-bottom: 32px;
      text-align: center;
    }

    .login-logo img {
      max-height: 100px;
      object-fit: contain;
    }

    .login-box-body {
      background: transparent !important;
      padding: 0 !important;
      box-shadow: none !important;
      border: none !important;
    }

    .login-box-msg {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
      font-size: 24px;
      font-weight: 800;
      color: #0f172a;
      text-align: left;
      padding: 0 0 8px 0;
      letter-spacing: -0.03em;
    }

    .login-sub-msg {
      font-size: 14px;
      color: #64748b;
      margin-bottom: 32px;
    }

    /* Premium Input Elements */
    .login-box-body .form-group {
      position: relative;
      margin-bottom: 22px;
    }

    .login-box-body .form-control {
      background: #f8fafc !important;
      border: 1.5px solid #e2e8f0 !important;
      border-radius: 12px !important;
      height: 52px;
      padding: 10px 16px !important;
      font-size: 14px;
      color: #0f172a !important;
      box-shadow: none !important;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .login-box-body .form-control:focus {
      border-color: #0046fe !important;
      background: #ffffff !important;
      box-shadow: 0 0 0 4px rgba(0, 70, 254, 0.1) !important;
    }

    .login-box-body .form-control-feedback {
      height: 52px;
      line-height: 52px;
      color: #94a3b8;
      right: 6px;
      font-size: 16px;
    }

    /* High Contrast Interactive Quick Buttons */
    .quick-login-buttons {
      margin: 0 -6px 28px -6px;
    }
    .quick-login-buttons .col-xs-6 {
      padding: 0 6px;
    }

    .quick-login-buttons .btn {
      height: 46px;
      border-radius: 12px !important;
      font-weight: 700 !important;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      border: none !important;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #quickAdminBtn {
      background: #0046fe !important;
      color: #ffffff !important;
      box-shadow: 0 4px 14px rgba(0, 70, 254, 0.15);
    }
    #quickAdminBtn:hover {
      background: #002db3 !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 70, 254, 0.3);
    }

    #quickCashierBtn {
      background: #f4f7ff !important;
      color: #0046fe !important;
    }
    #quickCashierBtn:hover {
      background: #e5edff !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 70, 254, 0.1);
    }

    /* Absolute Clean Main Submit Button */
    .login-page .btn-primary.btn-block {
      height: 52px;
      border-radius: 12px !important;
      font-size: 15px;
      font-weight: 700 !important;
      background: #0046fe !important;
      border: none !important;
      box-shadow: 0 8px 24px rgba(0, 70, 254, 0.2);
      transition: all 0.2s ease;
    }
    .login-page .btn-primary.btn-block:hover {
      background: #002db3 !important;
      box-shadow: 0 12px 28px rgba(0, 70, 254, 0.35);
      transform: translateY(-1px);
    }
    .login-page .btn-primary.btn-block:active {
      transform: scale(0.98);
    }

    /* Navigation Utilities */
    .login-utility-links {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }
    .login-utility-links a {
      color: #64748b;
      font-weight: 600;
      font-size: 13px;
      transition: color 0.15s ease;
    }
    .login-utility-links a:hover {
      color: #0046fe;
    }

    .version-stamp {
      font-style: normal !important;
      color: #94a3b8;
      font-size: 12px;
      margin-top: 32px;
      font-weight: 500;
    }

    /* Premium Modernized Demo Dashboard Widget */
    .demo-widget-card {
      background: #f8fafc !important;
      border-radius: 16px !important;
      margin-top: 32px;
      padding: 20px 24px !important;
      border: 1px solid #e2e8f0;
    }

    .demo-widget-card label {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px;
      font-weight: 700 !important;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      display: block;
      margin-bottom: 12px;
    }

    .demo-widget-card .table {
      margin-bottom: 0;
      background: transparent;
    }

    .demo-widget-card .table tr {
      background: transparent;
    }

    .demo-widget-card .table td {
      border-color: #e2e8f0 !important;
      padding: 8px 0 !important;
      font-size: 13px;
      color: #475569;
      vertical-align: middle !important;
    }

    .demo-widget-card .table tr:last-child td {
      border-bottom: none !important;
    }

    .demo-widget-card .table td:first-child {
      font-weight: 600;
      color: #0f172a;
    }

    .demo-widget-card .btn-info {
      background: #ffffff !important;
      border: 1.5px solid #cbd5e1 !important;
      color: #0046fe !important;
      font-weight: 700 !important;
      font-size: 11px !important;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      padding: 4px 10px !important;
      border-radius: 8px !important;
      box-shadow: none !important;
      transition: all 0.15s ease;
    }

    .demo-widget-card .btn-info:hover {
      background: #0046fe !important;
      border-color: #0046fe !important;
      color: #ffffff !important;
    }

    .demo-disclaimer {
      font-size: 11px;
      color: #64748b;
      line-height: 1.4;
      display: block;
      margin-top: 12px;
    }
    .demo-disclaimer i { color: #d97706 !important; margin-right: 4px; }

    /* Responsive scaling adjustments for small displays */
    @media (max-width: 991px) {
      .saas-showcase-panel { display: none; }
      .saas-interaction-panel { flex: 1; padding: 30px; }
    }
  </style>
  
  <?php 
      $lang = trim(strtoupper($this->session->userdata('language')));
      if($lang==strtoupper('arabic') || $lang==strtoupper('urdu')) {?>
  <link rel="stylesheet" href="<?php echo $theme_link; ?>bootstrap/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/AdminLTE.rtl.min.css">
  <?php } ?>
  
</head>
<body class="hold-transition login-page">

  <input type="hidden" id="base_url" value="<?=base_url()?>">
  <?php $this->load->view('comman/language.php');?>

  <div class="saas-split-container">
    
    <div class="saas-showcase-panel">
      <div class="saas-showcase-content">
        <h1>The Next Generation POS Architecture.</h1>
        <p>Blazing fast processing, intelligent hardware orchestration, and absolute omni-channel terminal control engineered seamlessly into a plain beautiful interface.</p>
        <div class="saas-feature-badges">
          <div class="saas-badge"><i class="fa fa-bolt"></i> Realtime Cloud Sync</div>
          <div class="saas-badge"><i class="fa fa-shield"></i> Advanced Encryption</div>
          <div class="saas-badge"><i class="fa fa-pie-chart"></i> Unified Analytics</div>
        </div>
      </div>
    </div>

    <div class="saas-interaction-panel">
      <div class="login-box">

        <div class="login-logo">
          <a href="#">
            <img src="<?php echo base_url(get_site_logo());?>" >
          </a>
        </div>

        <div class="login-box-body">
          <h2 class="login-box-msg"><?= $this->lang->line('sign_in_message'); ?></h2>
          <p class="login-sub-msg">Welcome back. Please enter your terminal credentials below to start your session.</p>

          <div class="text-danger text-center"><?php echo $this->session->flashdata('failed'); ?></div>
          <div class="text-success text-center"><?php echo $this->session->flashdata('success'); ?></div>
          
          <div class="row quick-login-buttons">
            <div class="col-xs-6">
              <button type="button" class="btn btn-primary btn-block btn-flat" id="quickAdminBtn">
                <i class="fa fa-user-secret"></i> Admin Suite
              </button>
            </div>
            <div class="col-xs-6">
              <button type="button" class="btn btn-success btn-block btn-flat" id="quickCashierBtn">
                <i class="fa fa-money"></i> Terminal POS
              </button>
            </div>
          </div>
          
          <form id="login-form" action="<?php echo $base_url; ?>login/verify" method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
            
            <div class="form-group has-feedback">
              <input type="text" class="form-control" placeholder="Email / Terminal Username" id="email" name="email" autofocus>
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            
            <div class="form-group has-feedback">
              <input type="password" class="form-control" placeholder="Secure Password" id="pass" name="pass">
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            
            <div class="row">
              <div class="col-xs-12">
                <button type="submit" class="btn btn-primary btn-block btn-flat"><?= $this->lang->line('sign_in'); ?></button>
              </div>
            </div>

            <div class="login-utility-links">
              <div>
                <?php if(store_module()){?>
                  <a href="<?=base_url('register')?>"><?= $this->lang->line('register'); ?></a>
                <?php } ?>
              </div>
              <div>
                <a href="<?=base_url('login/forgot_password')?>"><?= $this->lang->line('forgot_password'); ?></a>
              </div>
            </div>
          </form>

          <div class="row">
            <div class="col-md-12 text-center">
              <p class="version-stamp">System Engine Version <?=app_version();?></p>   
            </div>
          </div>
        </div>

        <?php if(demo_app()){ ?>
        <div class="box-body demo-widget-card">
          <label>Sandbox Accounts Matrix</label>
          <div class="row">
           <div class="col-md-12">
             <table class="table">         
                 <tr>
                   <td>admin@example.com</td>
                   <td>123456</td>
                   <td class="text-right"><button type="button" class="btn btn-info btn-flat admin">Use</button></td>
                 </tr>
                 <tr>
                   <td>accounts@example.com</td>
                   <td>123456</td>
                   <td class="text-right"><button type="button" class="btn btn-info btn-flat accounts">Use</button></td>
                 </tr>
                 <tr>
                   <td>seller@example.com</td>
                   <td>123456</td>
                   <td class="text-right"><button type="button" class="btn btn-info btn-flat seller">Use</button></td>
                 </tr>
                 <tr>
                   <td>purchase@example.com</td>
                   <td>123456</td>
                   <td class="text-right"><button type="button" class="btn btn-info btn-flat purchase">Use</button></td>
                 </tr>
               </table>
           </div>
          </div>
          <span class="demo-disclaimer"><i class="fa fa-fw fa-info-circle text-warning"></i>Demonstration environment assets are restricted and undergo automated session wipes every hour.</span>
        </div>
        <?php } ?>
               
      </div>
    </div>

  </div>

<script src="<?php echo $theme_link; ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?php echo $theme_link; ?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $theme_link; ?>plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $theme_link; ?>js/language.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%'
    });
  });
</script>
<script type="text/javascript" >
$(function($) {
    $.ajaxSetup({ data: {'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }  }); });
</script>

<script type="text/javascript">
  $("#quickAdminBtn").on("click", function(event) {
    $("input[name='email']").val("owner");
    $("input[name='pass']").val("owner123");
    $("#login-form").submit();
  });

  $("#quickCashierBtn").on("click", function(event) {
    $("input[name='email']").val("cashier");
    $("input[name='pass']").val("cashier123");
    $("#login-form").submit();
  });

  $(".admin").on("click",function(event) {
    $("input[name='email']").val("admin@example.com");
    $("input[name='pass']").val("123456");
    $("#login-form").submit();
  });

  $(".accounts").on("click",function(event) {
    $("input[name='email']").val("accounts@example.com");
    $("input[name='pass']").val("123456");
    $("#login-form").submit();
  });

  $(".seller").on("click",function(event) {
    $("input[name='email']").val("seller@example.com");
    $("input[name='pass']").val("123456");
    $("#login-form").submit();
  });

  $(".purchase").on("click",function(event) {
      $("input[name='email']").val("purchase@example.com");
      $("input[name='pass']").val("123456");
      $("#login-form").submit();
    });
</script>
</body>
</html>