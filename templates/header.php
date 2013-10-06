<?php

function sanitize_output($buffer) {
  $search = array(
      '/\>[^\S ]+/s', // strip whitespaces after tags, except space
      '/[^\S ]+\</s', // strip whitespaces before tags, except space
      '/(\s)+/s'       // shorten multiple whitespace sequences
  );
  $replace = array(
      '>',
      '<',
      '\\1'
  );
  return preg_replace($search, $replace, $buffer);
}

ob_start("sanitize_output");
?>

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
    <script src="./bootstrap/js/modal.js"></script>
    <script src="./bootstrap/js/transition.js"></script>
    <script src="./bootstrap/js/collapse.js"></script>

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

    $page_id = $_GET['page_id'];
    $thread_id = $_GET['thread_id'];
    $query = $_GET['query'];
    $rated = $_GET['rated'];
    $random = $_GET['random'];
    $admin_key = $_GET['admin'];

    include_once 'service/settings.php';

    if ($admin_key === $secret_key) {
      $admin = true;
    } else {
      $admin = false;
    }

    include_once 'service/connect_db.php';
    include_once 'service/utils.php';

    echo '<input type="hidden" id="generation_time" value="' . get_time_millis() . '">';
    ?>

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="#write_modal" data-toggle="modal" class="navbar-toggle btn" style="color: white; padding-top: 6px; padding-bottom: 6px;">
            <span class="icon-pencil icon">
            </span>&nbsp;<?php echo _("Post") ?>
          </a>
          <a class="navbar-brand" href="./"><span class="icon-trash"></span>&nbsp;<?php echo _("TrashPad") ?></a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li <?php if (!$rated && !$random & !$about) echo 'class="active"'; ?>><a href="./">
                <span class="icon-home icon-white"></span> <?php echo _("Home") ?> 
                <span id="fresh_counter" class="badge" style="display:none;">0</span></a>
            </li>
            <li <?php if ($rated) echo 'class="active"'; ?>><a href="./?rated=true"><span class="icon-star icon-white"></span> <?php echo _("Top rated") ?></a></li>
            <li <?php if ($random) echo 'class="active"'; ?>><a href="./random.php"><span class="icon-random icon-white"></span> <?php echo _("Random") ?></a></li>
            <li class="divider-vertical"></li>
            <li <?php if ($about) echo 'class="active"'; ?>><a href="./about.php"><span class="icon-info-sign icon-white"></span> <?php echo _("About") ?></a></li>
            <li><a href="#write_modal" data-toggle="modal"><span class="icon-pencil icon-white"></span> <?php echo _("Post") ?></a></li>
          </ul>
          <form class="navbar-form navbar-right" role="search" action="./">
            <div class="form-group">
              <input type="text" placeholder="<?php echo _("Search") ?>" class="form-control" name="query"/>
            </div>
          </form>
        </div>
      </div>
    </nav>

    <div id="write_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _("Post thread") ?></h4>
          </div>
          <form onsubmit="post_thread(name, feedback, message, post_button);
              return false;" method="post" class="form-horizontal" role="form">
            <div class="modal-body">
              <div class="alert alert-success" id="success_alert" style="display:none;">
                <strong><?php echo _("Congratulations!") ?></strong> <?php echo _("New thread successfully posted! Redirecting...") ?>
              </div>
              <div class="alert alert-warning" id="warning_alert" style="display:none;">
                <strong><?php echo _("Heads up!") ?></strong> <?php echo _("You must fill at least message field.") ?>
              </div>
              <div class="alert alert-danger" id="error_alert" style="display:none;">
                <strong><?php echo _("Errrm.") ?></strong> <?php echo _("Something was wrong on the host during post this thread.") ?>
              </div>
              <div class="form-group">
                <label for="inputName" class="col-lg-3 control-label"><?php echo _("Name") ?></label>
                <div class="col-lg-9">
                  <input name="name" type="text" class="form-control" id="inputName" placeholder="<?php echo _("Name") ?>">
                </div>
              </div>
              <div class="form-group">
                <label for="inputFeedback" class="col-lg-3 control-label"><?php echo _("Feedback") ?></label>
                <div class="col-lg-9">
                  <input name="feedback" type="email" class="form-control" id="inputFeedback" placeholder="<?php echo _("Feedback") ?>">
                </div>
              </div>
              <div class="form-group">
                <label for="inputFeedback" class="col-lg-3 control-label"><?php echo _("Message") ?></label>
                <div class="col-lg-9">
                  <textarea name="message" rows="3" maxlength="<?php echo $thread_length ?>" class="form-control" id="inputMessage" placeholder="<?php echo _("Your message here") ?>"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _("Close") ?></button>
              <button type="submit" class="btn btn-primary" name="post_button"><?php echo _("Post thread") ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <div class="container" style="max-width: 850px;">
