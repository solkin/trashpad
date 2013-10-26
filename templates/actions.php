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
        alert('<?= _("Thread remove failed:") ?>\nstatus: ' + data['status'] + '\nreason: ' + data['reason']);
      }
    });
  }

  function remove_reply(reply_id, admin_key) {
    $.ajax({
      type: 'POST',
      dataType: "json",
      url: './service/remove_reply.php',
      data: {'reply_id': reply_id, 'admin_key': admin_key},
      success: function(data) {
        if (data['status'] === 'ok') {
          hide_reply(data['reply_id']);
        } else {
          this.error(data);
        }
      },
      error: function(data) {
        alert('<?= _("Reply remove failed:") ?>\nstatus: ' + data['status'] + '\nreason: ' + data['reason']);
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
        alert('<?= _("Karma reset failed:") ?>\nstatus: ' + data['status'] + '\nreason: ' + data['reason']);
      }
    });
  }

  function karma_update(thread_id, karma) {
    var like_button = document.getElementById("like_button_" + thread_id);
    var fire_button = document.getElementById("fire_button_" + thread_id);
    <?php if (!$admin): ?>
      like_button.setAttribute('disabled', 'true');
      fire_button.setAttribute('disabled', 'true');
    <?php endif; ?>
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
            alert('<?= _("Unable to post a reply") ?>');
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
    var threads_array = <?= json_encode($threads_array); ?>;
    threads_array.forEach(function(element, index, array) {
      var thread_div = document.getElementById(element);
      var reply_id = "";
      if (thread_div.childNodes[0] !== undefined) {
        reply_id = thread_div.childNodes[0].getAttribute('id');
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
        karma_counter.innerHTML = '<span class="icon-fire"></span>';

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
          alert('<?= _("This thread was moderated, so you cannot share it anymore. Refresh page to get deleted threads gone forever.") ?>');
          return false;
        };
        twitter.onclick = moderated_action;
        vkontakte.onclick = moderated_action;
        facebook.onclick = moderated_action;
        <?php if ($admin): ?>
          var remove_button = document.getElementById("remove_button_" + thread_id);
          var reset_button = document.getElementById("reset_button_" + thread_id);
          remove_button.disabled = true;
          reset_button.disabled = true;
        <?php endif; ?>
      } else {
        var current = parseInt(karma_counter.innerHTML);
        var target = parseInt(karma);
        if (current < target) current++;
        if (current > target) current--;
        if (current === 0) {
          karma_counter.className = "label label-default";
        } else if (current > 0) {
          karma_counter.className = "label label-info";
        } else {
          karma_counter.className = "label label-warning";
        }
        karma_counter.innerHTML = current.toString();
        if (current !== target) setTimeout(function(){
          update_karma(thread_id, karma);
        }, 100);
      }
    }
  }

  function prepare_reply(thread_id, reply_id, message) {
    if (!document.getElementById(reply_id)) {
      $('#' + thread_id).prepend(
              '<div id="' + reply_id + '" style="display:none;">' +
              '<p><span class="icon-comment"></span>&nbsp;' + message + '</p>' +
              '</div>'
              );
      return reply_id;
    }
    return "";
  }

  function display_reply(reply_id) {
    if (reply_id) {
      $('#' + reply_id).show('fast', function() {
      });
    }
  }

  function hide_reply(reply_id) {
    if (reply_id) {
      $('#' + reply_id).hide('fast', function() {
      });
    }
  }

  function is_numeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
  }

  setTimeout("fetch_events(false)", 2000);
</script>