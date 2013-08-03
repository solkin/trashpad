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
			
			form {
				margin-bottom: 0px;
			}

			/* Custom container */
			.container-narrow {
				margin: 0 auto;
				max-width: 1000px;
			}
			
			.author-info {
			  position: relative;
			  margin: 14px 0;
			  padding: 10px 15px 5px;
			  *padding-top: 10px;
			  background-color: #fff;
			  border: 1px solid #ddd;
			  -webkit-border-radius: 4px;
				 -moz-border-radius: 4px;
					  border-radius: 4px;
			}
		</style>
	</head>
	<body>
		
		<div class="navbar navbar-fixed-top navbar-inverse" style="margin: -1px -1px 0;">
		  <div class="navbar-inner">
			<div class="container" style="width: auto; padding: 0 20px;">
			  <a class="brand" href="#">hlamogram</a>
			  <ul class="nav">
				<li class="active"><a href="#"><i class="icon-home icon-white"></i> Home</a></li>
				<li><a href="#"><i class="icon-info-sign icon-white"></i> About</a></li>
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
		    <form onsubmit="post_thread(name, feedback, message, post_button, success_alert, error_alert); return false;" method="post" class="form-horizontal">
			  <div class="modal-body">
					<div class="alert alert-success hide" id="success_alert">
					  <strong>Congratulations!</strong> New thread successfully posted!
					</div>
					<div class="alert alert-error hide" id="error_alert">
					  <strong>Heads up!</strong> You must fill at least message field.
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Name</label>
						<div class="controls">
							<div class="input-prepend">
								<span class="add-on"><i class="icon-user"></i></span>
								<input type="text" id="inputName" name="name" placeholder="Name">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputFeedback">Feedback</label>
						<div class="controls">
							<div class="input-prepend">
								<span class="add-on"><i class="icon-envelope"></i></span>
								<input type="text" id="inputFeedback" name="feedback" placeholder="Feedback">
							</div>
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
				<button class="btn btn-primary" type="submit" name="post_button">Post thread</button>
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
			
			// Obtain required threads.
			$threads_list = get_thread_list($link, true, $threads_per_page, $thread_from);
			
			echo '<div class="container-narrow">';
			
			$threads_iterator = 0;
			// Page generation.
			foreach($threads_list as $thread) {
				$name = $thread['name'];
				$ip = $thread['ip'];
				$user_agent = $thread['user_agent'];
				$feedback = $thread['feedback'];
				$thread_id = $thread['thread_id'];
				$message = $thread['message'];
				$reply_list = array_reverse($thread['reply']);
				
				$threads_array[$threads_iterator++] = $thread_id;
				echo '<div class="row">';
				echo '	<div class="span8 offset1">';
				echo '  <p>';
				echo '	<br>';
				if(!empty($name) || !empty($feedback)) {
					if(!empty($name)) { 
						echo '		<strong><i class="icon-user"></i> '.$name.'</strong><br>';
					}
					if(!empty($feedback)) {
						echo '		<a href="mailto:'.$feedback.'"><i class="icon-envelope"></i> '.$feedback.'</a><br>';
					}
				}
				echo '		<i class="icon-globe"></i> '.$user_agent;
				echo '	<div class="btn-group">';
				echo '		<button class="btn btn-mini btn-success" type="button"><i class="icon-heart icon-white"></i></button> ';
				echo '		<button class="btn btn-mini btn-warning" type="button"><i class="icon-fire icon-white"></i></button>';
				echo '	</div> ';
				echo $message;
				echo '</p>';
				echo '	<div class="row">';
				echo '		<form class="form-inline" onsubmit="post_reply(thread_id, message, reply_button); return false;" method="post">';
				echo '			<input type="hidden" name="thread_id" value="'.$thread_id.'">';
				echo '			<div class="span5 offset1 input-append">';
				echo '				<input class="input-block-level" type="text" name="message" placeholder="Your reply here">';
				echo '				<button type="submit" class="btn btn-primary" name="reply_button">Reply</button>';
				echo '			</div>';
				echo '		</form>';
				echo '	</div>';
				echo '	<p></p>';
				echo '	<div id="'.$thread_id.'">';
				foreach($reply_list as $reply) {
					echo '<div class="row" id="'.$thread_id.'_'.$reply['reply_id'].'">';
					echo '	<div class="span6 offset1">';
					echo '	<p><i class="icon-comment"></i> '.$reply['message'].'</p>';
					echo '	</div>';
					echo '</div>';
				}
				echo '	</div>';
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
		<script>
			function post_thread(name, feedback, message, post_button, success_alert, error_alert) {
				error_alert.style.display = 'none';
				success_alert.style.display = 'none';
				if(message.value) {
					name.setAttribute('readOnly', true);
					feedback.setAttribute('readOnly', true);
					message.setAttribute('readOnly', true);
					post_button.setAttribute('disabled', true);
					$.ajax( {
						type: 'POST',
						dataType: "json",
						url: './service/post_thread.php',
						data: {'name': name.value, 'feedback': feedback.value, 'message': message.value},
						success: function(data) {
							success_alert.style.display = 'block';
							setTimeout("location.reload(true);",2500);
						},
						error: function(data) {
							name.removeAttribute('readOnly');
							feedback.removeAttribute('readOnly');
							message.removeAttribute('readOnly');
							post_button.removeAttribute('disabled');
						}
					});
				} else {
					error_alert.style.display = 'block';
				}
			}
			
			function post_reply(thread_id, message, reply_button) {
				if(message.value) {
					message.setAttribute('readOnly', true);
					reply_button.setAttribute('disabled', true);
					$.ajax( {
						type: 'POST',
						dataType: "json",
						url: './service/post_reply.php',
						data: {'thread_id': thread_id.value, 'message': message.value},
						success: function(data) {
							display_reply(prepare_reply(data['thread_id'], data['reply_id'], message.value));
							message.value = "";
							message.removeAttribute('readOnly');
							reply_button.removeAttribute('disabled');
						},
						error: function(data) {
							message.removeAttribute('readOnly');
							reply_button.removeAttribute('disabled');
						}
					});
				}
			}
			
			function load_reply(one_time) {
				var fetch_array = {};
				var threads_array = <?
					echo json_encode($threads_array);
				?>;
				threads_array.forEach(function(element, index, array) {
					var thread_div = document.getElementById(element);
					var reply_id = "";
					if(typeof thread_div.childNodes[0].getAttribute == 'function') {
						var reply_id = thread_div.childNodes[0].getAttribute('id');
						if(reply_id != null) {
							reply_id = reply_id.substring(reply_id.indexOf('_') + 1);
						}
					}
					fetch_array[element] = reply_id;
				});
				console.log("request: " + JSON.stringify(fetch_array));
				$.ajax( {
					type: 'POST',
					dataType: "json",
					url: './service/fetch_reply.php',
					data: {'threads': JSON.stringify(fetch_array)},
					success: function(data) {
						var reply_array = data['reply_array'];
						console.log("reply: " + JSON.stringify(reply_array));
						for(var i=0; i<reply_array.length; i++) {
							reply = reply_array[i];
							display_reply(prepare_reply(reply['thread_id'], reply['reply_id'], reply['message']));
						}
						if(!one_time) {
							setTimeout("load_reply(false)", 5000);
						}
					},
					error: function(data) {
						if(!one_time) {
							setTimeout("load_reply(false)", 5000);
						}
					}
				});
			}
			
			function prepare_reply(thread_id, reply_id, message) {
				var reply_id = thread_id + '_' + reply_id;
				if(!document.getElementById(reply_id)) {
					$('#' + thread_id).prepend (
						'<div class="row" id="' + reply_id + '" style="display:none;">'+
						'	<div class="span6 offset1">'+
						'	<p><i class="icon-comment"></i> '+message+'</p>'+
						'	</div>'+
						'</div>'
					);
					return reply_id;
				}
				return "";
			}

			function display_reply(reply_id) {
				if(reply_id) {
					$('#' + reply_id).show('fast', function() {});
				}
			}

			load_reply(false);
		</script>
	</body>
</html>
