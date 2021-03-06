<?php

// This class provide link to work with DB. Also this can init DB.

$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die('{"status": "failed", "reason": ' . json_encode(mysqli_connect_error($link)) . '}');

// Will be implemented in admin page.
if ($init && $debug) {
  $sql = "DROP TABLE IF EXISTS threads";
  mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  $sql = "DROP TABLE IF EXISTS reply";
  mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  $sql = "CREATE TABLE IF NOT EXISTS threads (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			time BIGINT,
			name TEXT,
			feedback TEXT,
			ip TEXT,
			user_agent TEXT,
			thread_id TEXT NOT NULL default '',
			message TEXT NOT NULL default '',
			karma INT NOT NULL default 0,
            type INT NOT NULL default 0,
            polls INT NOT NULL default 0
			)";
  mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  $sql = "CREATE TABLE IF NOT EXISTS reply (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			reply_id TEXT NOT NULL default '',
			time BIGINT,
			ip TEXT,
			user_agent TEXT,
			thread_id TEXT NOT NULL default '',
			message TEXT NOT NULL default '',
            type INT NOT NULL default 0
			)";
  mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
}