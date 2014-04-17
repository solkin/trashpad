<?php
  include_once 'settings.php';
  include_once 'connect_db.php';
  include_once 'utils.php';

  $generation_time = encode($_GET['generation_time']); 

  $fresh_threads_time = get_time_millis();
  $sql = "SELECT thread_id, time FROM threads WHERE time>" . $generation_time;
  $threads_result = mysqli_query($link, $sql) or die();
  $fresh_threads_count = mysqli_num_rows($threads_result);
  mysqli_free_result($threads_result);

  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  echo "<badge value=\"" . $fresh_threads_count . "\"/>";
