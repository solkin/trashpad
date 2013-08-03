<?php
	include_once './connect_db.php';
	include_once './utils.php';
	
	$time = round(microtime(true) * 1000);
	$name = $_POST['name'];
	$feedback = $_POST['feedback'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$message = $_POST['message'];
	$thread_id = generate_random_string();
	
	$sql = "INSERT INTO threads (time, name, feedback, ip, user_agent, thread_id, message)
		VALUES ('$time', '$name', '$feedback', '$ip', '$user_agent', '$thread_id', '$message')";
		
	mysqli_query($link, $sql) or die(json_encode('Couldn\'t insert row.' . mysqli_error($link)));

	mysqli_close($link);
	
	echo '{"thread_id": "'.$thread_id.'", "user_agent": '.json_encode($user_agent).', "time": "'.$time.'"}';
?>
