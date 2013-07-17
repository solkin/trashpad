<?php
	include_once './connect_db.php';
	include_once './utils.php';
	
	$name = $_POST['name'];
	$feedback = $_POST['feedback'];
	$geo = $_POST['geo'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$message = $_POST['message'];
	$thread_id = generateRandomString();
	
	$sql = "INSERT INTO threads (name, feedback, geo, ip, thread_id, message)
		VALUES ('$name', '$feedback', '$geo', '$ip', '$thread_id', '$message')";
		
	mysqli_query($link, $sql) or die('Couldn\'t insert row.' . mysqli_error($link));

	mysqli_close($link);
	
	echo '{"thread_id": "'.$thread_id.'"}';
?>
