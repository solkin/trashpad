<?php

include_once 'connect_db.php';
include_once 'utils.php';
include_once 'settings.php';

$time = get_time_millis();
$fresh_threads_time = 0;
$reply_count = 0;
$karma_count = 0;
$reply_array = array();
$karma_array = array();
$threads = json_decode($_POST['threads']);
$generation_time = $_POST['generation_time'];
do {
  foreach ($threads as $key => $value) {
    $reply = $value->{'reply'};
    $karma = $value->{'karma'};

    // Karma fetching.
    $sql = "SELECT thread_id, karma FROM threads WHERE thread_id='" . $key . "'";
    $karma_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
    $karma_object = mysqli_fetch_array($karma_result, MYSQLI_ASSOC);
    mysqli_free_result($karma_result);
    if ($karma_object) {
      if ($karma != $karma_object['karma']) {
        $karma_array[$karma_count++] = $karma_object;
      }
      // Obtain reply db id.
      if ($reply) {
        // Request DB id from reply_id
        $sql = "SELECT id FROM reply WHERE reply_id='" . $reply . "'";
        $reply_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
        $reply_object = mysqli_fetch_array($reply_result, MYSQLI_ASSOC);
        mysqli_free_result($reply_result);
        $id = $reply_object['id'];
      }
      if (!$id) {
        // No one reply.
        $id = -1;
      }

      // Reply fetching.
      $sql = "SELECT * FROM reply WHERE thread_id='" . $key . "' AND id>" . $id;
      $reply_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

      $reply_object = mysqli_fetch_array($reply_result, MYSQLI_ASSOC);
      mysqli_free_result($reply_result);

      if ($reply_object != null) {
        $reply_array[$reply_count++] = $reply_object;
      }
    } else {
      // No such thread anymore.
      if ($karma != "unrated") {
        $empty_karma['thread_id'] = $key;
        $empty_karma['karma'] = "unrated";
        $karma_array[$karma_count++] = $empty_karma;
      }
    }
  }

  // Fresh threads fetching.
  $fresh_threads_time = get_time_millis();
  $sql = "SELECT thread_id, time FROM threads WHERE time>" . $generation_time;
  $threads_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
  $fresh_threads_count = mysqli_num_rows($threads_result);
  mysqli_free_result($threads_result);

  if (empty($reply_array) && empty($karma_array) && $fresh_threads_count == 0) {
    usleep($events_poll_time * 1000);
  } else {
    break;
  }
} while ((get_time_millis() - $time) < $fetch_events_timeout);

mysqli_close($link);

echo '{"status": "ok", "reply_array":' . json_encode($reply_array)
 . ', "karma_array":' . json_encode($karma_array) . ', "fresh_threads_count": ' . $fresh_threads_count . ', "fresh_time": ' . $fresh_threads_time . '}';
?>
