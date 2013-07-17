<?php
	include_once './connect_db.php';

	$include_reply = $_GET['include_reply'] == 'true' ? true : false;

	$sql = "SELECT * FROM threads";
	$result = mysqli_query($link, $sql);
	if (!$result) {
	    die('Incorrect query: ' . mysqli_error($link));
	}
	
	$threads_count = 0;
	$threads = [];
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	  if($include_reply) {
		  $sql = "SELECT * FROM reply WHERE thread_id='" . $row['thread_id'] . "'";
		  $reply_result = mysqli_query($link, $sql);
		  
		  $count = 0;
		  $reply = [];
		  while ($reply_row = mysqli_fetch_array($reply_result, MYSQLI_ASSOC)) {
		    $reply[$count++] = $reply_row;
		  }
		  mysqli_free_result($reply_result);
		  $row['reply'] = $reply;
	  }
	  $threads[$threads_count++] = $row;
	}
	
	mysqli_free_result($result);
	
	echo '{"threads": '.json_encode($threads).'}';

	mysqli_close($link);
?>
