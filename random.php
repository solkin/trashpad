<?php

  include_once 'service/settings.php';
  include_once 'service/connect_db.php';
  include_once 'service/utils.php';

  $threads_list = get_random_thread($link, true);

  $thread = $threads_list[0];
  $thread_id = $thread['thread_id'];

  header("Location: ./thread.php?rid=" . $thread_id);
  die();