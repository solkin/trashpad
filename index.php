<?php
  require_once './include/initializer.php';
  
  // Short links.
  $thread_id = $_GET['thread_id'];
  if($thread_id) {
    header("Location: ./thread.php?id=" . $thread_id);
    die();
  }
  
  $page_id = $_GET['page_id'];
  // Thread to show calculation.
  if (!$page_id || $page_id == 0) {
    $page_id = 1;
  }
  
  $thread_from = ($page_id - 1) * $threads_per_page;
  $threads_list = get_thread_list($link, true, $threads_per_page, $thread_from, $rated);
  $pages_total = ceil(get_threads_count($link) / $threads_per_page);
  
  include './templates/header.php';
  require_once './include/threads.php';
  if (empty($threads_list)) {
    include ('./templates/burova.php');
  } else if ($pages_total > 1) {
    require_once ('./include/navigator.php');
  }
  require_once './include/actions.php';
  include './templates/footer.php';