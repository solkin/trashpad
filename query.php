<?php

  $query = $_GET['query'];
  $page_id = $_GET['page_id'];
  // Thread to show calculation.
  if (!$page_id || $page_id == 0) {
    $page_id = 1;
  }
  
  require_once './templates/header.php';
  
  $thread_from = ($page_id - 1) * $threads_per_page;
  $threads_list = get_threads_by_query($link, true, $query, $threads_per_page, $thread_from);
  $pages_total = ceil(get_query_threads_count($link, $query) / $threads_per_page);
  include ('./templates/bigbutton.php');
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