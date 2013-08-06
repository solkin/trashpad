<?php
	include_once './connect_db.php';
	include_once './utils.php';
	include_once './settings.php';
	
	$reply_count = 0;
	$karma_count = 0;
	$reply_array = array();
	$karma_array = array();
	$threads = json_decode ($_POST['threads']);
	foreach ($threads as $key => $value) {
		$reply = $value->{'reply'};
		$karma = $value->{'karma'};
		
		// Reply fetching.
		if ($reply) {
			// Request id fro reply_id (value)
			$sql = "SELECT * FROM reply WHERE reply_id='" . $reply . "'";
			$reply_result = mysqli_query ($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
			$reply_reply = mysqli_fetch_array ($reply_result, MYSQLI_ASSOC);
			$id = $reply_reply['id'];
			mysqli_free_result ($reply_result);
		} else {
			// No one reply.
			$id = -1;
		}
		
		$sql = "SELECT * FROM reply WHERE thread_id='" . $key . "' AND id>" . $id;
		$reply_result = mysqli_query ($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
		
		$thread_reply = mysqli_fetch_array ($reply_result, MYSQLI_ASSOC);
		mysqli_free_result ($reply_result);
		
		if ($thread_reply != null) {
			$reply_array[$reply_count++] = $thread_reply;
		}
		
		// Karma fetching.
		$sql = "SELECT thread_id, karma FROM threads WHERE thread_id='" . $key . "'";
		$karma_result = mysqli_query ($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
		$karma_reply = mysqli_fetch_array ($karma_result, MYSQLI_ASSOC);
		$karma_value = $karma_reply['karma'];
		if($karma != $karma_value) {
			$karma_array[$karma_count++] = $karma_reply;
		}
		mysqli_free_result ($karma_result);
	}
	
	echo '{"status": "ok", "reply_array":' . json_encode ($reply_array) 
		. ', "karma_array":' . json_encode ($karma_array) . '}';
?>
