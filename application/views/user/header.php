<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])):?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SKINNER</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?= base_url('vendor/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/img/basic/favicon.ico') ?>" type="image/x-icon">
    <!-- MetisMenu CSS -->
    <link href="<?= base_url('assets/css/app.css')?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?= base_url('dist/css/sb-admin-2.css')?>" rel="stylesheet">
    <link href="<?= base_url('assets/img/basic/favicon.ico')?>" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="<?= base_url('vendor/morrisjs/morris.css')?>" rel="stylesheet">
<link href="<?= base_url('assets\css\style.css')?>" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="<?= base_url('vendor/font-awesome/css/font-awesome.min.css')?>" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
 <style>
        .loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #F5F8FA;
            z-index: 9998;
            text-align: center;
        }

        .plane-container {
            position: absolute;
            top: 50%;
            left: 50%;
        }.sidebar{width: 100%!important;}
    </style>
    <!-- Js -->
    <!--
    --- Head Part - Use Jquery anywhere at page.
    --- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
    -->
    <script>(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)
</script>
</head>
<body class="light">
<!-- Pre loader -->
<div id="loader" class="loader">
    <div class="plane-container">
        <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-blue">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-yellow">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-green">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>
        </div>
    </div>
</div>
<div id="app">
	<?php $this->load->view('siderbar')?>
	<div class="has-sidebar-left">
    <div class="pos-f-t">
    <div style="height:80px;background: #2979ff"; position: fixed;
  top: 0;>
  <span>
    	<a href="<?= base_url('index.php/logout')?> " style="color: white;float: right;
    padding: 23px;">LOGOUT</a>
    </span>
    </div>
    </div>
</div>
	<?php endif; ?>