<?php
  $thread_id = $_GET['id'];
  if(!$thread_id) {
    $thread_id = $_GET['rid'];
    $random = true;
  }
  
  require_once './templates/header.php';
  $threads_list = get_thread($link, true, $thread_id);
  include ('./templates/bigbutton.php');
?>
<div class="row" style="padding-left: 15px; padding-right: 15px;">
<?php
  require_once './include/threads.php';
  if (empty($threads_list)) {
    include ('./templates/burova.php');
  }
?>
</div>
<?php
  require_once './templates/actions.php';
  require_once './templates/footer.php'; 