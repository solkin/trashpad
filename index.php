<?php

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
  
  require_once './templates/header.php';
  
  $thread_from = ($page_id - 1) * $threads_per_page;
  $threads_list = get_thread_list($link, true, $threads_per_page, $thread_from, $rated);
  $pages_total = ceil(get_threads_count($link) / $threads_per_page);
?>
<div class="row" style="padding-left: 15px; padding-right: 15px;">
<?php
  require_once './include/threads.php';
  if (empty($threads_list)) {
    include ('./templates/burova.php');
  } else if ($pages_total > 1) {
    include ('./include/navigator.php');
  }
  require_once './templates/actions.php';
?>
</div>
<?php
  require_once './templates/footer.php'; 