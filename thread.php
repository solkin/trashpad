<?php
  require_once './include/initializer.php';
  
  $thread_id = $_GET['id'];
  if(!$thread_id) {
    $thread_id = $_GET['rid'];
    $random = true;
  }
  
  $threads_list = get_thread($link, true, $thread_id);
  
  require_once './templates/header.php';
  include ('./templates/bigbutton.php');
  require_once './include/threads.php';
  if (empty($threads_list)) {
    include ('./templates/burova.php');
  }
  require_once './include/actions.php';
  require_once './templates/footer.php';