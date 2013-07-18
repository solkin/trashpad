<?php
	include_once './connect_db.php';
	include_once './utils.php';

	$include_reply = $_GET['include_reply'] == 'true' ? true : false;
	
	$threads = get_thread_list($link, $include_reply);
	
	echo '{"threads": '.json_encode($threads).'}';
?>
