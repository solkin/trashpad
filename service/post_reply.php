<?php
	include_once './connect_db.php';
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$message = $_POST['message'];
	$thread_id = $_POST['thread_id'];
	
	$sql = "INSERT INTO reply (ip, thread_id, message)
		VALUES ('$ip', '$thread_id', '$message')";
		
	mysqli_query($link, $sql) or die('Couldn\'t insert row.' . mysqli_error($link));

	mysqli_close($link);
	
	echo '{"status": "ok"}';
?>
