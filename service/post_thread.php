<?php
include_once 'connect_db.php';
include_once 'utils.php';

$time = get_time_millis();
$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
$feedback = htmlspecialchars($_POST['feedback'], ENT_QUOTES);
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$message = htmlspecialchars($_POST['message'], ENT_QUOTES);
$thread_id = generate_random_string();

$sql = "SELECT * FROM threads WHERE message='" . $message . "'";
$result = mysqli_query($link, $sql) or die ('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $thread_id = $row['thread_id'];
    $user_agent = $row['user_agent'];
    $time = $row['time'];

    mysqli_free_result($result);
} else {
    $sql = "INSERT INTO threads (time, name, feedback, ip, user_agent, thread_id, message) ".
        "VALUES ('$time', '$name', '$feedback', '$ip', '$user_agent', '$thread_id', '$message')";

    mysqli_query($link, $sql) or die ('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
}

mysqli_close($link);

echo '{"status": "ok", "thread_id": "' . $thread_id . '", "user_agent": ' . json_encode($user_agent) . ', "time": "' . $time . '"}';
?>
