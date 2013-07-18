<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
  <body>
	<?php
		include_once './service/connect_db.php';
		include_once './service/utils.php';
		include_once './service/settings.php';
		
		// Thread to show calculation.
		$page_id = $_GET['page_id'];
		
		if($page_id == 0) {
			$page_id = 1;
		}
		$thread_from = ($page_id - 1) * $threads_per_page;
		
		// Obtain required threads.
		$threads_list = get_thread_list($link, true, $threads_per_page, $thread_from);
		
		// Page generation.
		echo '<table width="100%" border="1">';
		foreach($threads_list as $thread) {
			$name = $thread['name'];
			$feedback = $thread['feedback'];
			$geo = $thread['geo'];
			$message = $thread['message'];
			echo '<tr>';
			echo '<td>';
			echo $message;
			echo '</td>';
			echo '</tr>';
		}
		echo "</table>";
	?>
  </body>
</html>
