<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta charset="utf-8">
		<title>hlamogram</title>
		<link href="./bootstrap/css/bootstrap.css" rel="stylesheet">
		<link href="./bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-top: 60px;
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
		
		<div class="navbar navbar-fixed-top navbar-inverse" style="margin: -1px -1px 0;">
		  <div class="navbar-inner">
			<div class="container" style="width: auto; padding: 0 20px;">
			  <a class="brand" href="#">hlamogram</a>
			  <ul class="nav">
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#">About</a></li>
				<li><a href="#myModal" data-toggle="modal"><i class="icon-pencil icon-white"></i> Post</a></li>
			  </ul>
			</div>
		  </div>
		</div>
		 
		<!-- Modal -->
		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel">Post thread</h3>
		  </div>
		  <form action="./service/post_thread.php" method="post" class="form-horizontal">
			  <div class="modal-body">
					<div class="control-group">
						<label class="control-label" for="inputName">Name</label>
						<div class="controls">
							<input type="text" id="inputName" name="name" placeholder="Name">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputFeedback">Feedback</label>
						<div class="controls">
							<input type="text" id="inputFeedback" name="feedback" placeholder="Feedback">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputMessage">Message</label>
						<div class="controls">
							<textarea id="inputMessage" name="message" placeholder="Your message here"></textarea>
						</div>
					</div>
			  </div>
			  <div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" type="submit">Post thread</button>
			  </div>
		  </form>
		</div>

		<?php
			include_once './service/connect_db.php';
			include_once './service/utils.php';
			include_once './service/settings.php';
			
			// Thread to show calculation.
			$page_id = $_GET['page_id'];
			$pages_total = ceil(get_threads_count($link) / $threads_per_page);
			
			if($page_id == 0) {
				$page_id = 1;
			} 
			$thread_from = ($page_id - 1) * $threads_per_page;
			
			
			echo $page_id.'/'.$pages_total;
			
			// Obtain required threads.
			$threads_list = get_thread_list($link, true, $threads_per_page, $thread_from);
			
			echo '<div class="container-narrow">';
			
			// Page generation.
			foreach($threads_list as $thread) {
				$name = $thread['name'];
				$feedback = $thread['feedback'];
				$thread_id = $thread['thread_id'];
				$message = $thread['message'];
				$reply_list = array_reverse($thread['reply']);
				echo '<div class="row">';
				echo '	<div class="span8">';
				echo '	<p><button class="btn btn-mini btn-warning" type="button"><i class="icon-fire icon-white"></i></button> '.$message.'</p>';
				if(!empty($name) || !empty($feedback)) {
					echo '	<address>';
					if(!empty($name)) { 
						echo '		<strong><i class="icon-user"></i> '.$name.'</strong><br>';
					}
					if(!empty($feedback)) {
						echo '		<a href="mailto:'.$feedback.'"><i class="icon-envelope"></i> '.$feedback.'</a>';
					}
					echo '	</address>';
				}
				echo '	<div class="row">';
				echo '		<form class="form-inline" action="./service/post_reply.php" method="post">';
				echo '			<input type="hidden" name="thread_id" value="'.$thread_id.'">';
				echo '			<div class="span4 offset1 input-append">';
				echo '				<input class="input-block-level" type="text" name="message" placeholder="Your reply here">';
				echo '				<button type="submit" class="btn btn-primary">Reply</button>';
				echo '			</div>';
				echo '		</form>';
				echo '	</div>';
				echo '	<p></p>';
				foreach($reply_list as $reply) {
					echo '<div class="row">';
					echo '	<div class="span7 offset1">';
					echo '	<p><i class="icon-comment"></i> '.$reply['message'].'</p>';
					echo '	</div>';
					echo '</div>';
				}
				echo '	</div>';
				echo '</div>';
			}
			
			echo '<ul class="pager">';
			echo '<li class="previous'.(($page_id <= 1) ? " disabled" : " ").'">';
			echo '<a'.(($page_id <= 1) ? "" : (' href="?page_id='.($page_id - 1).'"')).'>&larr; Newer</a>';
			echo '</li>';
			echo '<li class="next'.(($page_id >= $pages_total) ? " disabled" : " ").'">';
			echo '<a'.(($page_id >= $pages_total) ? "" : (' href="?page_id='.($page_id + 1).'"')).'>Older &rarr;</a>';
			echo '</li>';
			echo '</ul>';
			
			echo '<hr class="soften">';
			echo '<div class="footer">';
			echo '<p>&copy; TomClaw Software 2013</p>';
			echo '</div>';
		  
			echo '</div>';
		?>
		
		<script src="./bootstrap/js/jquery.js"></script>
		<script src="./bootstrap/js/bootstrap-modal.js"></script>	
		<script src="./bootstrap/js/bootstrap-transition.js"></script>	
	</body>
</html>
