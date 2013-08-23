<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
    <meta charset="utf-8">
    <title>TrashPad</title>
    <link href="./bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="./bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }
        form {
            margin-bottom: 0;
        }
	/* Custom container */
	.container-narrow {
		margin: 0 auto;
		max-width: 1000px;
	}
	.facebook {float:left;margin-right:5px;padding:5px 0;}
	.facebook a {opacity: 0.5;display:block;width:19px;height:19px;background:url("./bootstrap/img/share_icons.png") no-repeat scroll 0 0 transparent;}
	.facebook a:hover {opacity:1;background:url("./bootstrap/img/share_icons.png") no-repeat 0px -21px;}
	.facebook a:active {opacity:1;background:url("./bootstrap/img/share_icons.png") no-repeat 0px -42px;}
	
	.twitter {float:left;margin-right:5px;padding:5px 0;}
	.twitter a {opacity: 0.5;display:block;width:19px;height:19px;background:url("./bootstrap/img/share_icons.png") no-repeat scroll -42px 0 transparent;}
	.twitter a:hover {opacity:1;background:url("./bootstrap/img/share_icons.png") no-repeat -42px -21px;}
	.twitter a:active {opacity:1;background:url("./bootstrap/img/share_icons.png") no-repeat -42px -42px;}
	
	.vkontakte {float:left;margin-right:5px;padding:5px 0;}
	.vkontakte a {opacity: 0.5;display:block;width:19px;height:19px;background:url("./bootstrap/img/share_icons.png") no-repeat scroll -21px 0 transparent;}
	.vkontakte a:hover {opacity:1;background:url("./bootstrap/img/share_icons.png") no-repeat -21px -21px;}
	.vkontakte a:active {opacity:1;background:url("./bootstrap/img/share_icons.png") no-repeat -21px -42px;}

	.googleplus {float:left;padding:5px 0;margin-right:0;}
	.googleplus a {opacity: 0.5;display:block;width:19px;height:19px;background:url("./bootstrap/img/share_icons.png") no-repeat scroll -63px 0 transparent;}
	.googleplus a:hover {opacity:1;background:url("./bootstrap/img/share_icons.png") no-repeat -63px -21px;}
	.googleplus a:active {opacity:1;background:url("./bootstrap/img/share_icons.png") no-repeat -63px -42px;}
    </style>
</head>
<body>
<?php
include_once './service/settings.php';
include_once './service/connect_db.php';
include_once './service/utils.php';

$page_id = $_GET['page_id'];
$thread_id = $_GET['thread_id'];
$query = $_GET['query'];
$rated = $_GET['rated'];
$admin_key = $_GET['admin'];

echo '<input type="hidden" id="generation_time" value="' . get_time_millis() . '">';
?>

<div class="navbar navbar-fixed-top navbar-inverse" style="margin: -1px -1px 0;">
    <div class="navbar-inner">
        <div class="container" style="width: auto; padding: 0 20px;">
            <a class="brand" href="./">TrashPad</a>
            <ul class="nav">
                <li <?if(!$rated) echo 'class="active"'; ?>><a href="./"><i class="icon-home icon-white"></i> Home <span id="fresh_counter" class="label label-info" style="display:none;">0</span></a>
                </li>
                <li <?if($rated) echo 'class="active"'; ?>><a href="./?rated=true"><i class="icon-star icon-white"></i> Top rated</a></li>
                <li class="divider-vertical"></li>
                <li><a href="#"><i class="icon-info-sign icon-white"></i> About</a></li>
                <li><a href="#myModal" data-toggle="modal"><i class="icon-pencil icon-white"></i> Post</a></li>
            </ul>
            <form class="navbar-search pull-right form-search" action="./">
                <div class="input-append">
                    <input type="text" class="span2 search-query" placeholder="Search" name="query">
                    <button type="submit" class="btn btn-inverse">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Post thread</h3>
    </div>
    <form onsubmit="post_thread(name, feedback, message, post_button, success_alert, warning_alert, error_alert); return false;"
          method="post" class="form-horizontal">
        <div class="modal-body">
            <div class="alert alert-success hide" id="success_alert">
                <strong>Congratulations!</strong> New thread successfully posted! Redirecting...
            </div>
            <div class="alert alert-warning hide" id="warning_alert">
                <strong>Heads up!</strong> You must fill at least message field.
            </div>
            <div class="alert alert-error hide" id="error_alert">
                <strong>Errrm.</strong> Something was wrong on the host during post this thread. 
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
                    <textarea id="inputMessage" name="message" id="message" placeholder="Your message here"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="submit" name="post_button">Post thread</button>
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </form>
</div>

<?php
// Thread to show calculation.

if($admin_key == $secret_key) {
    $admin = true;
} else {
    $admin = false;
}

if (!$page_id || $page_id == 0) {
    $page_id = 1;
}
$thread_from = ($page_id - 1) * $threads_per_page;

if ($thread_id) {
    $threads_list = get_thread($link, true, $thread_id);
} else if ($query) {
    $threads_list = get_threads_by_query($link, true, $query, $threads_per_page, $thread_from);
    $pages_total = ceil(get_query_threads_count($link, $query) / $threads_per_page);
} else {
    // Obtain required threads.
    $threads_list = get_thread_list($link, true, $threads_per_page, $thread_from, $rated);
    $pages_total = ceil(get_threads_count($link) / $threads_per_page);
}

echo '<div class="container-narrow">';

if($thread_id) {
  echo '<form class="form-inline" action="./" method="post">';
  echo '<button class="span3 offset3 btn btn-large btn-success" type="submit" id="big_green_button">To other threads</button> ';
  echo '</form>';
}

$threads_iterator = 0;
$threads_array = array();
// Page generation.
foreach ($threads_list as $thread) {
    $name = $thread['name'];
    $ip = $thread['ip'];
    $user_agent = $thread['user_agent'];
    $feedback = $thread['feedback'];
    $thread_id = $thread['thread_id'];
    $message = $thread['message'];
    $karma = $thread['karma'];
    $reply_list = array_reverse($thread['reply']);

    $threads_array[$threads_iterator++] = $thread_id;
    echo '<div class="row">';
    echo '  <div class="span8 offset1">';
    echo '    <p>';
    echo '    <br>';
    if (!empty($name) || !empty($feedback)) {
        if (!empty($name)) {
            echo '		<strong><i class="icon-user"></i> ' . $name . '</strong><br>';
        }
        if (!empty($feedback)) {
            echo '		<a href="mailto:' . $feedback . '"><i class="icon-envelope"></i> ' . $feedback . '</a><br>';
        }
    }
    echo '		<i class="icon-globe"></i> ' . $user_agent;
    echo '      <form class="form-inline" method="post">';
    if($admin) {
        echo '		<button class="btn btn-mini btn-danger" type="submit" id="remove_button_'.$thread_id.'" name="remove_button" onclick="remove_thread(\'' . $thread_id . '\', \'' . $admin_key . '\'); return false;"><i class="icon-trash icon-white"></i></button> ';
        echo '		<button class="btn btn-mini" type="submit" id="reset_button_'.$thread_id.'" name="karma_reset_button" onclick="karma_reset(\'' . $thread_id . '\', \'' . $admin_key . '\'); return false;"><i class="icon-thumbs-down"></i></button> ';
    }
    echo '		<div class="btn-group">';
    echo '			<button class="btn btn-mini btn-success" type="submit" id="like_button_'.$thread_id.'" onclick="karma_update(\'' . $thread_id . '\', 1); return false;"><i class="icon-heart icon-white"></i></button> ';
    echo '			<button class="btn btn-mini btn-warning" type="submit" id="fire_button_'.$thread_id.'" onclick="karma_update(\'' . $thread_id . '\', -1); return false;"><i class="icon-fire icon-white"></i></button>';
    echo '		</div>';
    echo '		<span class="label label-' . (intval($karma) >= 0 ? 'info' : 'warning') . '" id="karma_counter_' . $thread_id . '">' . $karma . '</span> ';
    echo $message;
    echo '      </form>';
    echo '    </p>';
    $direct_link  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $direct_link .= $_SERVER['SERVER_NAME'];
    $direct_link .= htmlspecialchars($_SERVER['REQUEST_URI']);
    $question_pos = strpos($direct_link, '?');
    if($question_pos) {
      $direct_link = substr($direct_link, 0, $question_pos);
    }
    $direct_link = $direct_link . "?thread_id=" . $thread_id;
    echo '<div class="share">';
    echo '  <div class="twitter">';
    echo '    <a href="http://twitter.com/intent/tweet?text=' . $direct_link . '" title="Опубликовать ссылку в Twitter" target="_blank"></a>';
    echo '  </div>';
    echo '  <div class="vkontakte">';
    echo '    <a href="http://vk.com/share.php?url=' . $direct_link . '" title="Опубликовать ссылку во ВКонтакте" onclick="window.open(this.href, \'Опубликовать ссылку во Вконтакте\', \'width=800,height=300\'); return false"></a>';
    echo '  </div>';
    echo '  <div class="facebook">';
    echo '    <a href="https://www.facebook.com/sharer/sharer.php?u=' . $direct_link . '" title="Опубликовать ссылку в Facebook" onclick="window.open(this.href, \'Опубликовать ссылку в Facebook\', \'width=640,height=436,toolbar=0,status=0\'); return false"></a>';
    echo '  </div>';
    /*echo '  <div class="googleplus">';
    echo '    <a href="https://plus.google.com/share?url=' . $direct_link . '" title="Опубликовать ссылку в Google Plus" onclick="window.open(this.href, \'Опубликовать ссылку в Google Plus\', \'width=800,height=300\'); return false"></a>';
    echo '  </div>';*/
    echo '</div>';
    echo '	<div class="row">';
    echo '		<form class="form-inline" onsubmit="post_reply(thread_id, message, reply_button); return false;" method="post">';
    echo '			<input type="hidden" name="thread_id" value="' . $thread_id . '">';
    echo '			<div class="span5 offset0 input-append">';
    echo '				<input class="input-block-level" type="text" id="reply_message_'.$thread_id.'" name="message" placeholder="Your reply here">';
    echo '				<button type="submit" class="btn btn-primary" id="reply_button_'.$thread_id.'" name="reply_button">Reply</button>';
    echo '			</div>';
    echo '		</form>';
    echo '	</div>';
    echo '	<p></p>';
    echo '	<div id="' . $thread_id . '">';
    foreach ($reply_list as $reply) {
        echo '<div class="row" id="' . $thread_id . '_' . $reply['reply_id'] . '">';
        echo '	<div class="span6 offset1">';
        echo '	<p><i class="icon-comment"></i> ' . $reply['message'] . '</p>';
        echo '	</div>';
        echo '</div>';
    }
    echo '	</div>';
    echo '	</div>';
    echo '</div>';
}
$href_page_prev = '"?page_id=' . ($page_id - 1) . ($query ? "&query=" . $query : "") . ($rated ? "&rated=" . $rated : "") . '"';
$href_page_next = '"?page_id=' . ($page_id + 1) . ($query ? "&query=" . $query : "") . ($rated ? "&rated=" . $rated : "") . '"';

$newer_title = $rated ? "Higher" : "Newer";
$older_title = $rated ? "Lower" : "Older";
echo '<ul class="pager">';
echo '<li class="previous' . (($page_id <= 1) ? " disabled" : " ") . '">';
echo '<a' . (($page_id <= 1) ? "" : (' href=' . $href_page_prev)) . '>&larr; ' . $newer_title . '</a>';
echo '</li>';
echo '<li class="next' . (($page_id >= $pages_total) ? " disabled" : " ") . '">';
echo '<a' . (($page_id >= $pages_total) ? "" : (' href=' . $href_page_next)) . '>' . $older_title . ' &rarr;</a>';
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
    function remove_thread(thread_id, admin_key) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: './service/remove_thread.php',
            data: {'thread_id': thread_id, 'admin_key': admin_key},
            success: function (data) {
                if(data['status'] == 'ok') {
                    // Page refresh.
                    update_karma(thread_id, '<?echo $unrated_value;?>');
                } else {
                    this.error(data);
                }
            },
            error: function (data) {
                alert('Thread remove failed: \nstatus: ' + data['status'] + '\nreason: ' + data['reason']);
            }
        });
    }

    function karma_reset(thread_id, admin_key) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: './service/karma_update.php',
            data: {'thread_id': thread_id, 'karma': 'reset', 'admin_key': admin_key},
            success: function (data) {
                if(data['status'] == 'ok') {
                    var thread_id = data['thread_id'];
                    var karma = parseInt(data['karma']);
                    var karma_counter = document.getElementById('karma_counter_' + thread_id);
                    if (karma_counter) {
                        karma_counter.innerHTML = karma.toString();
                        if (karma >= 0) {
                            karma_counter.className = "label label-info";
                        } else {
                            karma_counter.className = "label label-warning";
                        }
                    }
                } else {
                    this.error(data);
                }
            },
            error: function (data) {
                alert('Karma reset failed: \nstatus: ' + data['status'] + '\nreason: ' + data['reason']);
            }
        });
    }

    function karma_update(thread_id, karma) {
        // Find buttons.
        var like_button = document.getElementById("like_button_" + thread_id);
        var fire_button = document.getElementById("fire_button_" + thread_id);
        <?
        if(!$admin) {
            echo "like_button.setAttribute('disabled', 'true'); ";
            echo "fire_button.setAttribute('disabled', 'true');\n";
        }
        ?>
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: './service/karma_update.php',
            data: {'thread_id': thread_id, 'karma': karma},
            success: function (data) {
                var thread_id = data['thread_id'];
                var karma = parseInt(data['karma']);
                var karma_counter = document.getElementById('karma_counter_' + thread_id);
                if (karma_counter) {
                    karma_counter.innerHTML = karma.toString();
                    if (karma >= 0) {
                        karma_counter.className = "label label-info";
                    } else {
                        karma_counter.className = "label label-warning";
                    }
                }
            },
            error: function (data) {
                like_button.removeAttribute('disabled');
                fire_button.removeAttribute('disabled');
            }
        });
    }

    function post_thread(name, feedback, message, post_button, success_alert, warning_alert, error_alert) {
        error_alert.style.display = 'none';
        success_alert.style.display = 'none';
        if (message.value) {
            name.setAttribute('readOnly', 'true');
            feedback.setAttribute('readOnly', 'true');
            message.setAttribute('readOnly', 'true');
            post_button.setAttribute('disabled', 'true');
            $.ajax({
                type: 'POST',
                dataType: "json",
                url: './service/post_thread.php',
                data: {'name': name.value, 'feedback': feedback.value, 'message': message.value},
                success: function (data) {
                    var status = data['status'];
                    if(status == 'ok') {
						var thread_id = data['thread_id'];
						success_alert.style.display = 'block';
						// Current page path.
						var path_array = location.pathname.split('/');
						var path_new = "";
						for (i = 1; i < path_array.length; i++) {
							path_new += "/";
							path_new += path_array[i];
						}
						// Redirect.
						location.href = location.protocol + '//' + location.host + path_new + '?thread_id=' + thread_id;
					} else {
						name.removeAttribute('readOnly');
						feedback.removeAttribute('readOnly');
						message.removeAttribute('readOnly');
						post_button.removeAttribute('disabled');
						error_alert.style.display = 'block';
					}
                },
                error: function (data) {
                    name.removeAttribute('readOnly');
                    feedback.removeAttribute('readOnly');
                    message.removeAttribute('readOnly');
                    post_button.removeAttribute('disabled');
					error_alert.style.display = 'block';
                }
            });
        } else {
            warning_alert.style.display = 'block';
        }
    }

    String.prototype.escape = function() {
        var tagsToReplace = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;'
        };
        return this.replace(/[&<>]/g, function(tag) {
            return tagsToReplace[tag] || tag;
        });
    };

    function post_reply(thread_id, message, reply_button) {
        var message_text = message.value;
        if (message_text) {
            message.setAttribute('readOnly', 'true');
            reply_button.setAttribute('disabled', 'true');
            $.ajax({
                type: 'POST',
                dataType: "json",
                url: './service/post_reply.php',
                data: {'thread_id': thread_id.value, 'message': message_text},
                success: function (data) {
                    display_reply(prepare_reply(data['thread_id'], data['reply_id'], message_text.escape()));
                    message.value = "";
                    message.removeAttribute('readOnly');
                    reply_button.removeAttribute('disabled');
                },
                error: function (data) {
                    message.removeAttribute('readOnly');
                    reply_button.removeAttribute('disabled');
                }
            });
        }
    }

    function fetch_events(one_time) {
        var generation_time = parseInt(document.getElementById('generation_time').value);
        var fetch_array = {};
        var threads_array = <?
					echo json_encode($threads_array);
				?>;
        threads_array.forEach(function (element, index, array) {
            var thread_div = document.getElementById(element);
            var reply_id = "";
            if (typeof thread_div.childNodes[0].getAttribute == 'function') {
                reply_id = thread_div.childNodes[0].getAttribute('id');
                if (reply_id != null) {
                    reply_id = reply_id.substring(reply_id.indexOf('_') + 1);
                }
            }
            var karma_counter = document.getElementById('karma_counter_' + element).innerHTML;
            var thread_data = {};
            thread_data.reply = reply_id;
            thread_data.karma = karma_counter;
            fetch_array[element] = thread_data;
        });
        console.log("threads: " + JSON.stringify(fetch_array) + ", generation_time: " + generation_time);
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: './service/fetch_events.php',
            data: {'threads': JSON.stringify(fetch_array), 'generation_time': generation_time},
            success: function (data) {
                console.log("data: " + JSON.stringify(data));
                var reply_array = data['reply_array'];
                var karma_array = data['karma_array'];
                var fresh_threads_count = parseInt(data['fresh_threads_count']);
                var fresh_time = parseInt(data['fresh_time']);
                for (var i = 0; i < reply_array.length; i++) {
                    var reply = reply_array[i];
                    display_reply(prepare_reply(reply['thread_id'], reply['reply_id'], reply['message']));
                }
                for (var i = 0; i < karma_array.length; i++) {
                    var karma = karma_array[i];
                    update_karma(karma['thread_id'], karma['karma']);
                }
                if(fresh_threads_count > 0 && fresh_time > 0) {
                    update_fresh_threads_count(fresh_threads_count, fresh_time);
                }
                if (!one_time) {
                    fetch_events(false);
                }
            },
            error: function (data) {
                if (!one_time) {
                    setTimeout("fetch_events(false)", 5000);
                }
            }
        });
    }

    function update_fresh_threads_count(fresh_threads_count, fresh_time) {
        var fresh_counter = document.getElementById('fresh_counter');

        if (fresh_counter) {
            $('#fresh_counter').hide('fast', function () {
				fresh_counter.innerHTML = (parseInt(fresh_counter.innerHTML) + fresh_threads_count).toString();
				$('#fresh_counter').show('fast', function () {});
			});
        }

        document.getElementById('generation_time').value = fresh_time;
    }

    function update_karma(thread_id, karma) {
        var karma_counter = document.getElementById('karma_counter_' + thread_id);

        if (karma_counter) {
            karma_counter.innerHTML = karma;
            if (karma == '<?echo $unrated_value;?>') {
                karma_counter.className = "label label-important";
                // Disable buttons.
                var like_button = document.getElementById("like_button_" + thread_id);
                var fire_button = document.getElementById("fire_button_" + thread_id);
                var reply_message = document.getElementById("reply_message_" + thread_id);
                var reply_button = document.getElementById("reply_button_" + thread_id);
                like_button.disabled = true;
                fire_button.disabled = true;
                reply_message.disabled = true;
                reply_button.disabled = true;
                <?
                if($admin) {
                    echo 'var remove_button = document.getElementById("remove_button_" + thread_id);';
                    echo 'var reset_button = document.getElementById("reset_button_" + thread_id);';
                    echo 'remove_button.disabled = true;';
                    echo 'reset_button.disabled = true;';
                }
                ?>
            } else {
                if (parseInt(karma) >= 0) {
                    karma_counter.className = "label label-info";
                } else {
                    karma_counter.className = "label label-warning";
                }
            }
        }
    }

    function prepare_reply(thread_id, reply_id, message) {
        var thread_reply_id = thread_id + '_' + reply_id;
        if (!document.getElementById(thread_reply_id)) {
            $('#' + thread_id).prepend(
                '<div class="row" id="' + thread_reply_id + '" style="display:none;">' +
                    '	<div class="span6 offset1">' +
                    '	<p><i class="icon-comment"></i> ' + message + '</p>' +
                    '	</div>' +
                    '</div>'
            );
            return thread_reply_id;
        }
        return "";
    }

    function display_reply(reply_id) {
        if (reply_id) {
            $('#' + reply_id).show('fast', function () {
            });
        }
    }

    fetch_events(false);
</script>
</body>
</html>
