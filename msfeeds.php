<?php
  include_once 'settings.php';
  include_once 'connect_db.php';
  include_once 'utils.php';

  // This will be first page.
  $page_id = 1;
  
  // Thread index is id parameter from GET.
  $index = $_GET['id'];
  
  $thread_from = ($page_id - 1) * $threads_per_page;
  $threads_list = get_thread_list($link, true, $threads_per_page, $thread_from, $rated);
  $pages_total = ceil(get_threads_count($link) / $threads_per_page);

  if(sizeof($threads_list) == 0) {
	  die;
  }

  $threads_iterator = 0;
  $threads_array = array();
  // Page generation.
  if($index > sizeof($threads_list)) {
	  $index = sizeof($threads_list) - 1;
  }
  $thread = $threads_list[$index];

  // Thread info.
  $name = $thread['name'];
  $ip = $thread['ip'];
  $user_agent = $thread['user_agent'];
  $feedback = $thread['feedback'];
  $thread_id = $thread['thread_id'];
  $message = $thread['message'];
  $karma = $thread['karma'];
  $time = $thread['time'];
  $type = $thread['type'];
  $polls = $thread['polls'];
  $reply_list = array_reverse($thread['reply']);

  // Thread direct link.
  $direct_link = get_current_path() . "thread.php?id=" . $thread_id;

  $thread_date = date('d.m.Y', $time / 100);
  $thread_time = date('H:i', $time / 100);

  // Karma label.
  if(intval($karma) == 0) {
    $label_type = 'default';
  } elseif (intval($karma) > 0) {
    $label_type = 'info';
  } else {
    $label_type = 'warning';
  }

  // Browser and OS.
  $os = get_os_by_ua($user_agent);
  $browser = get_browser_by_ua($user_agent);

  include('./templates/msfeed.php');
