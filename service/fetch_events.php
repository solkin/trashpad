<?php

include_once 'settings.php';
include_once 'connect_db.php';
include_once 'utils.php';

$time = get_time_millis();
$fresh_threads_time = 0;
$reply_count = 0;
$threads_count = 0;
$reply_array = array();
$threads_array = array();
$threads = json_decode($_POST['threads']);
$generation_time = encode($_POST['generation_time']);

/*if(!$thread) {
  $threads = json_decode($_GET['threads']);
  $generation_time = encode($_GET['generation_time']);
}*/

$threads_query = "";
foreach ($threads as $key => $value) {
  $shown = $value->{'shown'};
  if($shown) {
    $threads_query .= "thread_id='" . $key . "' OR ";
  }
}
if(strlen($threads_query) > 0) {
  $threads_query = substr($threads_query, 0, $threads_query - 4);
  $sql = "UPDATE threads SET polls=polls+1 WHERE " . $threads_query;
  mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
}

do {
  foreach ($threads as $key => $value) {
    $reply = $value->{'reply'};
    $karma = $value->{'karma'};
    $polls = $value->{'polls'};
    $shown = $value->{'shown'};

    // Karma fetching.
    $sql = "SELECT karma, polls FROM threads WHERE thread_id='" . $key . "'";
    $threads_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
    $threads_object = mysqli_fetch_array($threads_result, MYSQLI_ASSOC);
    mysqli_free_result($threads_result);
    if ($threads_object) {
      if ($karma != null && $karma != $threads_object['karma']) {
        $threads_array[$key] = $threads_object;
      } else if ($polls != null && $polls != $threads_object['polls']) {
        $threads_array[$key] = $threads_object;
      }
      if($shown) {
        $id = NULL;
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
        $sql = "SELECT thread_id, reply_id, message FROM reply WHERE thread_id='" . $key . "' AND id>" . $id . " AND type!=-1";
        $reply_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

        $reply_object = mysqli_fetch_array($reply_result, MYSQLI_ASSOC);
        mysqli_free_result($reply_result);

        if ($reply_object != null) {
          $reply_array[$reply_count++] = $reply_object;
        }
      }
    } else {
      // No such thread anymore.
      if ($karma != "unrated") {
        $empty_karma['karma'] = "unrated";
        $threads_array[$key] = $empty_karma;
      }
    }
  }

  // Fresh threads fetching.
  $fresh_threads_time = get_time_millis();
  $sql = "SELECT thread_id, time FROM threads WHERE time>" . $generation_time;
  $threads_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
  $fresh_threads_count = mysqli_num_rows($threads_result);
  mysqli_free_result($threads_result);

  if (empty($reply_array) && empty($threads_array) && $fresh_threads_count == 0) {
    usleep($events_poll_time * 1000);
  } else {
    break;
  }
} while ((get_time_millis() - $time) * 10 < $fetch_events_timeout);

if(strlen($threads_query) > 0) {
  $sql = "UPDATE threads SET polls=polls-1 WHERE " . $threads_query;
  mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
}

mysqli_close($link);

echo '{"status": "ok", "reply_array":' . json_encode($reply_array)
 . ', "threads_array":' . json_encode($threads_array) . ', "fresh_threads_count": ' . $fresh_threads_count . ', "fresh_time": ' . $fresh_threads_time . '}';