<?php
	include_once './connect_db.php';
	include_once './utils.php';
	
	$thread_id = $_POST['thread_id'];
	$karma = $_POST['karma'];
	
	// Karma fetching.
	$sql = "SELECT thread_id, karma FROM threads WHERE thread_id='" . $thread_id . "'";
	$karma_result = mysqli_query ($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
	$karma_reply = mysqli_fetch_array ($karma_result, MYSQLI_ASSOC);
	$karma_value = $karma_reply['karma'];
	mysqli_free_result ($karma_result);
	
	if($karma > 0) {
		$karma_value++;
	} else {
		$karma_value--;
	}
	
	$sql = "UPDATE threads SET karma='".$karma_value."' WHERE thread_id='" . $thread_id . "'";
		
	mysqli_query ($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');

	mysqli_close ($link);
	
	echo '{"status": "ok", "thread_id": "' . $thread_id . '", "karma": "'
				. $karma_value . '"}';
?>
