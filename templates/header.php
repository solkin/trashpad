<?php require_once './include/sanitizer.php'; ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>TrashPad</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TrashPad">
    <meta name="author" content="TomClaw Software">
    <script src="./jquery/jquery-1.10.2.min.js"></script>

    <script src="./bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="./bootstrap/css/bootstrap-theme.min.css">

    <link rel="stylesheet" href="./font-awesome/css/font-awesome.min.css">

    <style type="text/css">
      body {
        padding-top: 65px;
        padding-bottom: 20px;
      }
      .container {
        max-width: 1024px;
      }
      .navbar-form {
        border-style: none;
        border-top-style: dashed;
      }
      .navbar-collapse {
        border-top-style: dashed;
        margin-bottom: -10px;
      }
      .wrap {
        margin-top: 70px;
        margin-left: 50px;
      }
      .facebook {float:right;margin-right:5px;padding:1px 0;}
      .facebook a {opacity: 0.7;display:block;width:19px;height:19px;background:url("./images/system/share_icons.png") no-repeat scroll 0 0 transparent;}
      .facebook a:hover {opacity:1;background:url("./images/system/share_icons.png") no-repeat 0px -21px;}
      .facebook a:active {opacity:1;background:url("./images/system/share_icons.png") no-repeat 0px -42px;}

      .twitter {float:right;margin-right:-4px;padding:1px 0;}
      .twitter a {opacity: 0.7;display:block;width:19px;height:19px;background:url("./images/system/share_icons.png") no-repeat scroll -42px 0 transparent;}
      .twitter a:hover {opacity:1;background:url("./images/system/share_icons.png") no-repeat -42px -21px;}
      .twitter a:active {opacity:1;background:url("./images/system/share_icons.png") no-repeat -42px -42px;}

      .vkontakte {float:right;margin-right:5px;padding:1px 0;}
      .vkontakte a {opacity: 0.7;display:block;width:19px;height:19px;background:url("./images/system/share_icons.png") no-repeat scroll -21px 0 transparent;}
      .vkontakte a:hover {opacity:1;background:url("./images/system/share_icons.png") no-repeat -21px -21px;}
      .vkontakte a:active {opacity:1;background:url("./images/system/share_icons.png") no-repeat -21px -42px;}

      .googleplus {float:right;padding:0px 0;margin-right:0;}
      .googleplus a {opacity: 0.7;display:block;width:19px;height:19px;background:url("./images/system/share_icons.png") no-repeat scroll -63px 0 transparent;}
      .googleplus a:hover {opacity:1;background:url("./images/system/share_icons.png") no-repeat -63px -21px;}
      .googleplus a:active {opacity:1;background:url("./images/system/share_icons.png") no-repeat -63px -42px;}
    </style>
  </head>
  <body data-spy="scroll" data-target=".bs-docs-sidebar">
    <?php
      setlocale(LC_ALL, "ru_RU.UTF-8");
      $domain = "trashpad";
      bindtextdomain($domain, "./locale");
      bind_textdomain_codeset($domain, 'UTF-8');
      textdomain($domain);

      $admin_key = $_GET['admin'];
      $init = $_GET['init'];

      require_once './service/settings.php';
      require_once './service/connect_db.php';
      require_once './service/utils.php';

      echo '<input type="hidden" id="generation_time" value="' . get_time_millis() . '">';
      if ($admin_key === $secret_key) {
        $admin = true;
      } else {
        $admin = false;
      }

      require_once './templates/navbar.php';
    ?>

    <div class="container" style="max-width: 850px;">