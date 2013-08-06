<?php
	include_once './connect_db.php';
	include_once './utils.php';
	
	$reply_id = generate_random_string (true);
	$time = round (microtime (true) * 1000);
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$message = $_POST['message'];
	$thread_id = $_POST['thread_id'];
	
	$sql = "INSERT INTO reply (reply_id, time, ip, user_agent, thread_id, message)
		VALUES ('$reply_id', '$time', '$ip', '$user_agent', '$thread_id', '$message')";
		
	mysqli_query ($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');

	mysqli_close ($link);
	
	echo '{"status": "ok", "thread_id": "' . $thread_id . '", "reply_id": "'
				. $reply_id . '", "time": "' . $time . '", "user_agent": '
				. json_encode ($user_agent) . '}';
?>
