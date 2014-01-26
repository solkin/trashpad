<?php

include_once 'settings.php';
include_once 'connect_db.php';
include_once 'utils.php';

$time = get_time_millis();
$name = encode($_POST['name']);
$feedback = encode($_POST['feedback']);
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$message = encode($_POST['message']);
$type = get_type_index($_POST['type']);
$thread_id = generate_random_string();

$sql = "SELECT * FROM threads WHERE message='" . $message . "'";
$result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

  $thread_id = $row['thread_id'];
  $user_agent = $row['user_agent'];
  $time = $row['time'];

  mysqli_free_result($result);
} else {
  $message_length = mb_strlen($_POST['message'], "UTF-8");
  if($message_length > $thread_length) {
    mysqli_close($link);
    die('{"status": "failed", "reason": "message too long (' . $message_length . ') - maximum thread length is ' . $thread_length . ' chars"}');
  } else if($message_length < $min_thread_length) {
    mysqli_close($link);
    die('{"status": "failed", "reason": "message too small (' . $message_length . ') - minimum thread length is ' . $min_thread_length . ' chars"}');
  } else {
    $sql = "INSERT INTO threads (time, name, feedback, ip, user_agent, thread_id, message, type) " .
            "VALUES ('$time', '$name', '$feedback', '$ip', '$user_agent', '$thread_id', '$message', '$type')";

    mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
  }
}

mysqli_close($link);

echo '{"status": "ok", "name": ' . json_encode($name) . ', "feedback": ' . json_encode($feedback) . ', "message": ' . json_encode($message) . ', "type": "' . $type . '", "thread_id": "' . $thread_id . '", "user_agent": ' . json_encode($user_agent) . ', "time": "' . $time . '"}';
