<?php
	include_once './connect_db.php';
	include_once './utils.php';
	
	$name = $_POST['name'];
	$feedback = $_POST['feedback'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$message = $_POST['message'];
	$thread_id = generate_random_string();
	
	$sql = "INSERT INTO threads (name, feedback, ip, user_agent, thread_id, message)
		VALUES ('$name', '$feedback', '$ip', '$user_agent', '$thread_id', '$message')";
		
	mysqli_query($link, $sql) or die('Couldn\'t insert row.' . mysqli_error($link));

	mysqli_close($link);
	
	echo '{"thread_id": "'.$thread_id.'"}';
?>
