<?php
	$init = $_GET['init'];

	$link = mysqli_connect('localhost', 'root', '380349', 'hlamogram_db');

	if (mysqli_connect_errno()) {
		die("Failed to connect to MySQL: " . mysqli_connect_error());
	}

	if($init) {
		echo "Init mode. ";
		
		$sql = "DROP TABLE threads";
		if(!mysqli_query($link, $sql)){
			echo "Coudn\'t drop table " . mysqli_error($link);
		}
		
		$sql = "DROP TABLE reply";
		if(!mysqli_query($link, $sql)){
			echo "Coudn\'t drop table " . mysqli_error($link);
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS threads (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			time INT,
			name TEXT,
			feedback TEXT,
			ip TEXT,
			user_agent TEXT,
			thread_id TEXT NOT NULL default '',
			message TEXT NOT NULL default '',
			karma INT
			)";
		mysqli_query($link, $sql) or die('Couldn\'t create table.' . mysqli_error($link));

		$sql = "CREATE TABLE IF NOT EXISTS reply (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			reply_id TEXT NOT NULL default '',
			time INT,
			ip TEXT,
			user_agent TEXT,
			thread_id TEXT NOT NULL default '',
			message TEXT NOT NULL default ''
			)";
		mysqli_query($link, $sql) or die('Couldn\'t create table.' . mysqli_error($link));
		
		echo "Init completed.";
	}
?>
