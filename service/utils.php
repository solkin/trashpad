<?php
	function get_threads_by_query ($link, $include_reply, $query, $threads_count = 0, $thread_from = 0) {
		$query_parts = explode(' ', $query);
		
		$sql = "SELECT * FROM threads WHERE (";
		
		$index = 0;
		foreach ($query_parts as $query_piece) {
			if ($index > 0) {
				$sql .= " OR";
			}
			$sql .= " message LIKE '%" . $query_piece . "%'";
			$index ++;
		}
		$sql .= ")";
		
		$limit = $threads_count > 0 ? " ORDER BY id DESC LIMIT " . $thread_from . ", " . $threads_count : "";
		$sql .= $limit;
		
		$result = mysqli_query($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
		
		return list_threads ($link, $result, $include_reply);
	}

	function get_thread ($link, $include_reply, $thread_id) {
		$sql = "SELECT * FROM threads WHERE thread_id='" . $thread_id . "'";
		$result = mysqli_query($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
		
		return list_threads ($link, $result, $include_reply);
	}

	function get_thread_list($link, $include_reply, $threads_count = 0, $thread_from = 0) {
		$limit = $threads_count > 0 ? " ORDER BY id DESC LIMIT " . $thread_from . ", " . $threads_count : "";
		$sql = "SELECT * FROM threads" . $limit;
		$result = mysqli_query($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
		
		return list_threads ($link, $result, $include_reply);
	}
	
	function list_threads ($link, $result, $include_reply) {
		$threads_count = 0;
		$threads = array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		  if($include_reply) {
			  $sql = "SELECT * FROM reply WHERE thread_id='" . $row['thread_id'] . "'";
			  $reply_result = mysqli_query($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
			  
			  $count = 0;
			  $reply = array();
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
	
	function get_query_threads_count($link, $query) {
		
		$query_parts = explode(' ', $query);
		
		$sql = "SELECT * FROM threads WHERE (";
		
		$index = 0;
		foreach ($query_parts as $query_piece) {
			if ($index > 0) {
				$sql .= " OR";
			}
			$sql .= " message LIKE '%" . $query_piece . "%'";
			$index ++;
		}
		$sql .= ")";
		
		$result = mysqli_query($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
		
		$rows_count = mysqli_affected_rows($link);
		
		mysqli_free_result($result);
		
		return $rows_count;
	}
	
	function get_threads_count($link) {
		$sql = "SELECT * FROM threads";
		$result = mysqli_query($link, $sql) or die ( '{"status": "failed", "reason": ' . json_encode (mysqli_error ($link)) . '}');
		
		$rows_count = mysqli_affected_rows($link);
		
		mysqli_free_result($result);
		
		return $rows_count;
	}

	function generate_random_string($lower_case = false, $length = 24) {
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $lower_case ? strtolower($randomString) : $randomString;
	}
?>
