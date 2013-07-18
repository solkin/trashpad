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
		echo '<table width="100%"  border="0" cellspacing="0" cellpadding="4">';
		foreach($threads_list as $thread) {
			$name = $thread['name'];
			$feedback = $thread['feedback'];
			$geo = $thread['geo'];
			$thread_id = $thread['thread_id'];
			$message = $thread['message'];
			$reply_list = $thread['reply'];
			echo '<tr>';
			echo '	<td>';
			echo $message;
			echo '	</td>';
			echo '</tr>';
			if(sizeof($reply_list) > 0) {
				echo '<tr>';
				echo '	<td>';
				foreach($reply_list as $reply) {
					echo '<table width="100%" border="0" cellspacing="0" cellpadding="4">';
					echo '<tr>';
					echo '	<td width="5%">';
					echo '	</td>';
					echo '	<td  width="95%">';
					echo $reply['message'];
					echo '	</td>';
					echo '</tr>';
					echo "</table>";
				}
				echo '</tr>';
				echo '<tr>';
			}
			echo '<tr>';
			echo '	<td>';
			echo '		<form action="./service/post_reply.php" method="post">';
			echo '			<input size=30 type="hidden" name="thread_id" value="'.$thread_id.'"/>';
			echo '			<textarea rows=2 name="message" style="width:100%;"></textarea><br/>';
			echo '			<input type="submit" value="Reply"/>';
			echo '		</form>';
			echo '	</td>';
			echo '</tr>';
		}
		echo "</table>";
	?>
  </body>
</html>
