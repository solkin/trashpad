<?php
	function get_thread_list($link, $include_reply, $threads_count = 0, $thread_from = 0) {
		$limit = $threads_count > 0 ? " ORDER BY id DESC LIMIT " . $thread_from . ", " . $threads_count : "";
		$sql = "SELECT * FROM threads" . $limit;
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
		
		return $threads;
	}
	
	function get_threads_count($link) {
		$sql = "SELECT * FROM threads";
		$result = mysqli_query($link, $sql);
		
		if (!$result) {
			die('Incorrect query: ' . mysqli_error($link));
		}
		
		$rows_count = mysqli_affected_rows($link);
		
		mysqli_free_result($result);
		
		return $rows_count;
	}

	function generate_random_string($length = 24) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
?>
