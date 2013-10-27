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

  if ($admin_key === $secret_key) {
    $admin = true;
  } else {
    $admin = false;
  }