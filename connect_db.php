<?php
  $init = $_GET['init'];

  $link = mysqli_connect('localhost', 'root', '380349', 'hlamogram_db');

  if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
  }

  if($init) {
    $sql = "CREATE TABLE IF NOT EXISTS threads (
	    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	    name TEXT,
	    feedback TEXT,
	    geo TEXT,
	    ip TEXT,
	    thread_id TEXT NOT NULL default '',
	    message TEXT NOT NULL default ''
	    ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    mysqli_query($link, $sql) or die('Couldn\'t create table.' . mysql_error());
  }

?>