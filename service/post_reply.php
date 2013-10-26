<?php

include_once 'settings.php';
include_once 'connect_db.php';
include_once 'utils.php';

$reply_id = generate_random_string(true);
$time = get_time_millis();
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$message = htmlspecialchars($_POST['message'], ENT_QUOTES);
$thread_id = $_POST['thread_id'];

$message_length = mb_strlen($_POST['message'], "UTF-8");
if($message_length > $reply_length) {
  mysqli_close($link);
  die('{"status": "failed", "reason": "message too long (' . $message_length . ') - maximum reply length is ' . $reply_length . ' chars"}');
} else {
  $sql = "INSERT INTO reply (reply_id, time, ip, user_agent, thread_id, message) " .
          "VALUES ('$reply_id', '$time', '$ip', '$user_agent', '$thread_id', '$message')";

  mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
}

mysqli_close($link);

echo '{"status": "ok", "thread_id": "' . $thread_id . '", "reply_id": "'
 . $reply_id . '", "time": "' . $time . '", "user_agent": '
 . json_encode($user_agent) . '}';