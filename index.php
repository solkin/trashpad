    <?php
    require_once './templates/header.php';
    
    // Thread to show calculation.
    if (!$page_id || $page_id == 0) {
      $page_id = 1;
    }

    $thread_from = ($page_id - 1) * $threads_per_page;

    if ($random) {
      $threads_list = get_random_thread($link, true);
    } else if ($thread_id) {
      $threads_list = get_thread($link, true, $thread_id);
    } else if ($query) {
      $threads_list = get_threads_by_query($link, true, $query, $threads_per_page, $thread_from);
      $pages_total = ceil(get_query_threads_count($link, $query) / $threads_per_page);
    } else {
      // Obtain required threads.
      $threads_list = get_thread_list($link, true, $threads_per_page, $thread_from, $rated);
      $pages_total = ceil(get_threads_count($link) / $threads_per_page);
    }
    
    if ($random || $thread_id || empty($threads_list)) {
      echo '<div class="row" style="padding-bottom: 15px;">';
      echo '<div class="col-lg-3"></div>';
      echo '<div class="col-lg-6">';
      if ($random) {
        echo '<form class="form-inline" action="./?random=true" method="post">';
        echo '<button class="btn btn-lg btn-info btn-block" type="submit" id="big_green_button">' . _("One more random") . '</button> ';
        echo '</form>';
      } else if ($thread_id || empty($threads_list)) {
        echo '<form class="form-inline" action="./" method="post">';
        echo '<button class="btn btn-lg btn-success btn-block" type="submit" id="big_green_button">' . _("To other threads") . '</button> ';
        echo '</form>';
      }
      echo '</div>';
      echo '</div>';
    }
    
    echo '<div class="row" style="padding-left: 15px; padding-right: 15px;">';
    $threads_iterator = 0;
    $threads_array = array();
    // Page generation.
    foreach ($threads_list as $thread) {
      $name = $thread['name'];
      // $ip = $thread['ip'];
      $user_agent = $thread['user_agent'];
      $feedback = $thread['feedback'];
      $thread_id = $thread['thread_id'];
      $message = $thread['message'];
      $karma = $thread['karma'];
      $time = $thread['time'];
      $reply_list = array_reverse($thread['reply']);

      $threads_array[$threads_iterator++] = $thread_id;
      echo '<div class="panel panel-default">';
      echo '<div class="panel-heading">';
      if (!empty($name) || !empty($feedback)) {
        if (!empty($name)) {
          echo '<strong><span class="icon-user"></span> ' . $name . '</strong><br/>';
        }
        if (!empty($feedback)) {
          echo '<a href="mailto:' . $feedback . '"><span class="icon-envelope"></span> ' . $feedback . '</a><br/>';
        }
      }
      $direct_link = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
      $direct_link .= $_SERVER['SERVER_NAME'];
      $direct_link .= htmlspecialchars($_SERVER['REQUEST_URI']);
      $question_pos = strpos($direct_link, '?');
      if ($question_pos) {
        $direct_link = substr($direct_link, 0, $question_pos);
      }
      $direct_link = $direct_link . "?thread_id=" . $thread_id;
      echo '<span class="icon-calendar"></span> ' . date('d.m.Y', $time / 100) . '&nbsp;';
      echo '<span class="icon-time"></span> ' . date('H:i', $time / 100);
      echo '<div class="twitter">';
      echo '<a id="twitter_' . $thread_id . '" href="http://twitter.com/intent/tweet?text=' . $direct_link . '" title="' . _("Share via Twitter") . '" target="_blank"></a>';
      echo '</div>';
      echo '<div class="vkontakte">';
      echo '<a id="vkontakte_' . $thread_id . '" href="http://vk.com/share.php?url=' . $direct_link . '" title="' . _("Share via VK") . '" onclick="window.open(this.href, \'Опубликовать ссылку во Вконтакте\', \'width=800,height=300\'); return false"></a>';
      echo '</div>';
      echo '<div class="facebook">';
      echo '<a id="facebook_' . $thread_id . '" href="https://www.facebook.com/sharer/sharer.php?u=' . $direct_link . '" title="' . _("Share via Facebook") . '" onclick="window.open(this.href, \'Опубликовать ссылку в Facebook\', \'width=640,height=436,toolbar=0,status=0\'); return false"></a>';
      echo '</div>';
      echo '</div>';
      echo '<div class="panel-body">';
      echo '<form class="form-inline" method="post">';
      if ($admin) {
        echo '<button class="btn btn-xs btn-danger" type="button" id="remove_button_' . $thread_id . '" name="remove_button" onclick="remove_thread(\'' . $thread_id . '\', \'' . $admin_key . '\'); return false;"><span class="icon-trash icon-white"></span></button> ';
        echo '<button class="btn btn-xs btn-default" type="buton" id="reset_button_' . $thread_id . '" name="karma_reset_button" onclick="karma_reset(\'' . $thread_id . '\', \'' . $admin_key . '\'); return false;"><span class="icon-hand-down"></span></button> ';
      }
      echo '<div class="btn-group">';
      echo '<button class="btn btn-xs btn-success" type="submit" id="like_button_' . $thread_id . '" onclick="karma_update(\'' . $thread_id . '\', 1); return false;"><span class="icon-thumbs-up-alt"></span></button> ';
      echo '<button class="btn btn-xs btn-warning" type="submit" id="fire_button_' . $thread_id . '" onclick="karma_update(\'' . $thread_id . '\', -1); return false;"><span class="icon-thumbs-down-alt"></span></button>';
      echo '</div>';
      if(intval($karma) == 0) {
        $label_type = 'default';
      } elseif (intval($karma) > 0) {
        $label_type = 'info';
      } else {
        $label_type = 'warning';
      }
      echo '&nbsp;<span class="label label-' . $label_type . '" id="karma_counter_' . $thread_id . '">' . $karma . '</span>&nbsp;';
      echo $message;
      echo '</form>';
      echo '<hr/>';
      echo '<form class="form-inline" onsubmit="post_reply(thread_id, message, reply_button); return false;" method="post">';
      echo '<input type="hidden" name="thread_id" value="' . $thread_id . '"/>';
      echo '<div class="input-group">';
      echo '<input class="form-control" type="text" maxlength="' . $reply_length . '" id="reply_message_' . $thread_id . '" name="message" placeholder="' . _("Your reply here") . '"/>';
      echo '<span class="input-group-btn">';
      echo '<button type="submit" class="btn btn-primary" id="reply_button_' . $thread_id . '" name="reply_button">' . _("Reply") . '</button>';
      echo '</span>';
      echo '</div>';
      echo '</form>';
      echo '<br/>';
      echo '<div id="' . $thread_id . '" class="col-md-10">';
      foreach ($reply_list as $reply) {
        echo '<div id="' . $thread_id . '_' . $reply['reply_id'] . '">';
        echo '<p><span class="icon-comment"></span>&nbsp;' . $reply['message'] . '</p>';
        echo '</div>';
      }
      echo '</div>';
      echo '</div>';
      
      echo '<div class="panel-footer" style="text-align: right;">';
      $os = get_os_by_ua($user_agent);
      $browser = get_browser_by_ua($user_agent);
      if ($os) {
        $os_name = $os[0];
        $os_logo = $os[1];
        echo '<img src="./images/os/' . $os_logo . '.png" alt="' . $os_name . '"> ' . $os_name . '&nbsp;';
      }
      if ($browser) {
        $browser_name = $browser[0];
        $browser_logo = $browser[1];
        echo '<img src="./images/browser/' . $browser_logo . '.png" alt="' . $browser_name . '">&nbsp;' . $browser_name;
      }
      echo '</div>';
      echo '</div>';
    }

    if (empty($threads_list)) {
      echo '<div class="row" style="padding-bottom: 15px;" align="middle">';
      echo '<img src="./images/system/burova.jpg" class="img-circle"></img>';
      echo '</div>';
      echo '<p align="center">';
      echo _("Unfortunately, we have no threads here, but we have lots of others. Just press big green button!");
      echo '</p>';
    } elseif ($pages_total > 1) {
      $href_page_prev = '"?page_id=' . ($page_id - 1) . ($query ? "&query=" . $query : "") . ($rated ? "&rated=" . $rated : "") . '"';
      $href_page_next = '"?page_id=' . ($page_id + 1) . ($query ? "&query=" . $query : "") . ($rated ? "&rated=" . $rated : "") . '"';

      $newer_title = $rated ? _("Higher") : _("Newer");
      $older_title = $rated ? _("Lower") : _("Older");
      echo '<ul class="pager">';
      echo '<li class="previous' . (($page_id <= 1) ? " disabled" : " ") . '">';
      echo '<a' . (($page_id <= 1) ? "" : (' href=' . $href_page_prev)) . '>&larr; ' . $newer_title . '</a>';
      echo '</li>';
      echo '<li class="next' . (($page_id >= $pages_total) ? " disabled" : " ") . '">';
      echo '<a' . (($page_id >= $pages_total) ? "" : (' href=' . $href_page_next)) . '>' . $older_title . ' &rarr;</a>';
      echo '</li>';
      echo '</ul>';
    }
    
    echo '</div>';
    ?>

    <script>
            function remove_thread(thread_id, admin_key) {
              $.ajax({
                type: 'POST',
                dataType: "json",
                url: './service/remove_thread.php',
                data: {'thread_id': thread_id, 'admin_key': admin_key},
                success: function(data) {
                  if (data['status'] === 'ok') {
                    update_karma(thread_id, 'unrated');
                  } else {
                    this.error(data);
                  }
                },
                error: function(data) {
                  alert('<?php echo _("Thread remove failed:") ?>\nstatus: ' + data['status'] + '\nreason: ' + data['reason']);
                }
              });
            }

            function karma_reset(thread_id, admin_key) {
              $.ajax({
                type: 'POST',
                dataType: "json",
                url: './service/karma_update.php',
                data: {'thread_id': thread_id, 'karma': 'reset', 'admin_key': admin_key},
                success: function(data) {
                  if (data['status'] === 'ok') {
                    var thread_id = data['thread_id'];
                    var karma = parseInt(data['karma']);
                    var karma_counter = document.getElementById('karma_counter_' + thread_id);
                    if (karma_counter) {
                      karma_counter.innerHTML = karma.toString();
                      if (karma === 0) {
                        karma_counter.className = "label label-default";
                      } else if (karma > 0) {
                        karma_counter.className = "label label-info";
                      } else {
                        karma_counter.className = "label label-warning";
                      }
                    }
                  } else {
                    this.error(data);
                  }
                },
                error: function(data) {
                  alert('<?php echo _("Karma reset failed:") ?>\nstatus: ' + data['status'] + '\nreason: ' + data['reason']);
                }
              });
            }

            function karma_update(thread_id, karma) {
              var like_button = document.getElementById("like_button_" + thread_id);
              var fire_button = document.getElementById("fire_button_" + thread_id);
<?
if (!$admin) {
  echo "like_button.setAttribute('disabled', 'true'); ";
  echo "fire_button.setAttribute('disabled', 'true');\n";
}
?>
              $.ajax({
                type: 'POST',
                dataType: "json",
                url: './service/karma_update.php',
                data: {'thread_id': thread_id, 'karma': karma},
                success: function(data) {
                  var thread_id = data['thread_id'];
                  var karma = parseInt(data['karma']);
                  var karma_counter = document.getElementById('karma_counter_' + thread_id);
                  if (karma_counter) {
                    karma_counter.innerHTML = karma.toString();
                    if (karma === 0) {
                      karma_counter.className = "label label-default";
                    } else if (karma > 0) {
                      karma_counter.className = "label label-info";
                    } else {
                      karma_counter.className = "label label-warning";
                    }
                  }
                },
                error: function(data) {
                  like_button.removeAttribute('disabled');
                  fire_button.removeAttribute('disabled');
                }
              });
            }

            function post_thread(name, feedback, message, post_button) {
              $('#error_alert').hide('fast');
              $('#success_alert').hide('fast');
              $('#warning_alert').hide('fast');
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
                  success: function(data) {
                    var status = data['status'];
                    if (status === 'ok') {
                      var thread_id = data['thread_id'];
                      $('#success_alert').show('fast');
                      var path_array = location.pathname.split('/');
                      var path_new = "";
                      for (i = 1; i < path_array.length; i++) {
                        path_new += "/";
                        path_new += path_array[i];
                      }
                      location.href = location.protocol + '//' + location.host + path_new + '?thread_id=' + thread_id;
                    } else {
                      name.removeAttribute('readOnly');
                      feedback.removeAttribute('readOnly');
                      message.removeAttribute('readOnly');
                      post_button.removeAttribute('disabled');
                      $('#error_alert').show('fast');
                    }
                  },
                  error: function(data) {
                    name.removeAttribute('readOnly');
                    feedback.removeAttribute('readOnly');
                    message.removeAttribute('readOnly');
                    post_button.removeAttribute('disabled');
                    $('#error_alert').show('fast');
                  }
                });
              } else {
                $('#warning_alert').show('fast');
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
                  success: function(data) {
                    var status = data['status'];
                    if (status === 'ok') {
                      display_reply(prepare_reply(data['thread_id'], data['reply_id'], message_text.escape()));
                      message.value = "";
                    } else {
                      alert('<?php echo _("Unable to post a reply") ?>');
                    }
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

            function fetch_events(one_time) {
              var generation_time = parseInt(document.getElementById('generation_time').value);
              var fetch_array = {};
              var threads_array = <?
echo json_encode($threads_array);
?>;
              threads_array.forEach(function(element, index, array) {
                var thread_div = document.getElementById(element);
                var reply_id = "";
                if (thread_div.childNodes[0] !== undefined) {
                  reply_id = thread_div.childNodes[0].getAttribute('id');
                  if (reply_id !== null) {
                    reply_id = reply_id.substring(reply_id.indexOf('_') + 1);
                  }
                }
                var karma_counter = document.getElementById('karma_counter_' + element).innerHTML;
                if(!is_numeric(karma_counter)) {
                  karma_counter = "unrated";
                }
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
                success: function(data) {
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
                  if (fresh_threads_count > 0 && fresh_time > 0) {
                    update_fresh_threads_count(fresh_threads_count, fresh_time);
                  }
                  if (!one_time) {
                    fetch_events(false);
                  }
                },
                error: function(data) {
                  if (!one_time) {
                    setTimeout("fetch_events(false)", 5000);
                  }
                }
              });
            }

            function update_fresh_threads_count(fresh_threads_count, fresh_time) {
              var fresh_counter = document.getElementById('fresh_counter');

              if (fresh_counter) {
                $('#fresh_counter').hide('fast', function() {
                  fresh_counter.innerHTML = (parseInt(fresh_counter.innerHTML) + fresh_threads_count).toString();
                  $('#fresh_counter').show('fast', function() {
                  });
                });
              }

              document.getElementById('generation_time').value = fresh_time;
            }

            function update_karma(thread_id, karma) {
              var karma_counter = document.getElementById('karma_counter_' + thread_id);

              if (karma_counter) {
                if (karma === 'unrated') {
                  karma_counter.className = "label label-danger";
                  karma = '<span class="icon-fire"></span>';
                  
                  var like_button = document.getElementById("like_button_" + thread_id);
                  var fire_button = document.getElementById("fire_button_" + thread_id);
                  var reply_message = document.getElementById("reply_message_" + thread_id);
                  var reply_button = document.getElementById("reply_button_" + thread_id);

                  var twitter = document.getElementById("twitter_" + thread_id);
                  var vkontakte = document.getElementById("vkontakte_" + thread_id);
                  var facebook = document.getElementById("facebook_" + thread_id);

                  like_button.disabled = true;
                  fire_button.disabled = true;
                  reply_message.disabled = true;
                  reply_button.disabled = true;

                  var moderated_action = function() {
                    alert('<?php echo _("This thread was moderated, so you can't share it more. Refresh page to get deleted threads gone forever.") ?>');
                    return false;
                  };
                  twitter.onclick = moderated_action;
                  vkontakte.onclick = moderated_action;
                  facebook.onclick = moderated_action;
<?
if ($admin) {
  echo 'var remove_button = document.getElementById("remove_button_" + thread_id);';
  echo 'var reset_button = document.getElementById("reset_button_" + thread_id);';
  echo 'remove_button.disabled = true;';
  echo 'reset_button.disabled = true;';
}
?>
                } else {
                  if (parseInt(karma) === 0) {
                    karma_counter.className = "label label-default";
                  } else if (parseInt(karma) > 0) {
                    karma_counter.className = "label label-info";
                  } else {
                    karma_counter.className = "label label-warning";
                  }
                }
                karma_counter.innerHTML = karma;
              }
            }

            function prepare_reply(thread_id, reply_id, message) {
              var thread_reply_id = thread_id + '_' + reply_id;
              if (!document.getElementById(thread_reply_id)) {
                $('#' + thread_id).prepend(
                        '<div id="' + thread_reply_id + '" style="display:none;">' +
                        '<p><span class="icon-comment"></span>&nbsp;' + message + '</p>' +
                        '</div>'
                        );
                return thread_reply_id;
              }
              return "";
            }

            function display_reply(reply_id) {
              if (reply_id) {
                $('#' + reply_id).show('fast', function() {
                });
              }
            }
            
            function is_numeric(n) {
              return !isNaN(parseFloat(n)) && isFinite(n);
            }

            setTimeout("fetch_events(false)", 1000);
    </script>
<?
  require_once './templates/footer.php';
?>
