<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta charset="utf-8">
		<title>hlamogram</title>
		<link href="./bootstrap/css/bootstrap.css" rel="stylesheet">
		<link href="./bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<style type="text/css">
		  body {
			padding-top: 20px;
			padding-bottom: 40px;
		  }

		  /* Custom container */
		  .container-narrow {
			margin: 0 auto;
			max-width: 1000px;
		  }
      </style>
	</head>
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
		
		echo '<div class="container-narrow">';
		
		// Page generation.
		foreach($threads_list as $thread) {
			$name = $thread['name'];
			$feedback = $thread['feedback'];
			$geo = $thread['geo'];
			$thread_id = $thread['thread_id'];
			$message = $thread['message'];
			$reply_list = array_reverse($thread['reply']);
			echo '<div class="row">';
			echo '	<div class="span8">';
			echo '	<p>'.$message.'</p>';
			echo '	<div class="row">';
			echo '		<div class="span7 offset1">';
			echo '			<form class="form-inline" action="./service/post_reply.php" method="post">';
			echo '				<input type="hidden" name="thread_id" value="'.$thread_id.'">';
			echo '				<input type="text" name="message" placeholder="Your reply here">';
			echo '				<button type="submit" class="btn">Reply</button>';
			echo '			</form>';
			echo '		</div>';
			echo '	</div>';
			foreach($reply_list as $reply) {
				echo '<div class="row">';
				echo '	<div class="span7 offset1">';
				echo '	<p>'.$reply['message'].'</p>';
				echo '	</div>';
				echo '</div>';
			}
			echo '	</div>';
			echo '</div>';
		}
		echo '<hr class="soften">';
		echo '<div class="footer">';
		echo '<p>&copy; TomClaw Software 2013</p>';
		echo '</div>';
      
		echo '</div>';
	?>
  </body>
</html>
