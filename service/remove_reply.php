<?php

include_once 'settings.php';
include_once 'connect_db.php';
include_once 'utils.php';

$reply_id = $_POST['reply_id'];
$admin_key = $_POST['admin_key'];


if ($admin_key == $secret_key) {
  // Query for this reply.
  $sql = "SELECT * FROM reply WHERE reply_id='" . $reply_id . "'";
  $reply_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
  $reply_reply = mysqli_fetch_array($reply_result, MYSQLI_ASSOC);
  // Checking for reply successfully fetched.
  if(sizeof($reply_reply) > 0) {
    $id = $reply_reply['id'];
    $thread_id = $reply_reply['thread_id'];
    $type = $reply_reply['type'];
    mysqli_free_result($reply_result);
    
    // Checking for reply is already marked as removed.
    if($type != -1) {
      // Marking reply as removed.
      $sql = "UPDATE reply SET type='-1' WHERE id='" . $id . "'";
      mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '"}');
      mysqli_close($link);
      
      echo '{"status": "ok", "thread_id": "' . $thread_id . '", "reply_id": "' . $reply_id . '"}';
    } else {
      echo '{"status": "ok", "flag": "already removed", "thread_id": "' . $thread_id . '", "reply_id": "' . $reply_id . '"}';
    }
  } else {
    die('{"status": "failed", "reason": "no such reply", "reply_id": "' . $reply_id . '"}');
  }
} else {
  die('{"status": "failed", "reason": "access denied"}');
}