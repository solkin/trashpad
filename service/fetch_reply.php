<?php
	include_once './connect_db.php';
	include_once './utils.php';
	include_once './settings.php';
	
	$count = 0;
	$fetch_result = array();
	$threads = json_decode($_POST['threads']);
	foreach($threads as $key => $value) {
		$sql = "SELECT * FROM reply WHERE thread_id='" . $key . "' AND id>" . $value;
		$reply_result = mysqli_query($link, $sql);
		
		$thread_reply = mysqli_fetch_array($reply_result, MYSQLI_ASSOC);
		mysqli_free_result($reply_result);
		
		if($thread_reply != null) {
			$fetch_result[$count++] = $thread_reply;
		}
	}
	
	echo '{"reply_array":' . json_encode($fetch_result) . '}';
?>
