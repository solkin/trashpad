<?php
  $threads_iterator = 0;
  $threads_array = array();
  // Page generation.
  foreach ($threads_list as $thread) {

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

    include('./templates/thread.php');

    // Threads array for JS.
    $threads_array[$threads_iterator++] = $thread_id;
  }