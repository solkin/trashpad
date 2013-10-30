<script>
  function remove_thread(thread_id, admin_key) {
    $.ajax({
      type: 'POST',
      dataType: "json",
      url: './service/remove_thread.php',
      data: {'thread_id': thread_id, 'admin_key': admin_key},
      success: function(data) {
        if (data['status'] === 'ok') {
          update_state(thread_id, '<?=State::STATE_REMOVED?>');
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

  function post_thread(name, feedback, message, post_button, thread_button, kiosk_button) {
    $('#error_alert').hide('fast');
    $('#success_alert').hide('fast');
    $('#warning_alert').hide('fast');
    if (message.value) {
      name.setAttribute('readOnly', 'true');
      feedback.setAttribute('readOnly', 'true');
      message.setAttribute('readOnly', 'true');
      post_button.setAttribute('disabled', 'true');
      thread_button.setAttribute('disabled', 'true');
      kiosk_button.setAttribute('disabled', 'true');
      var type = get_type(thread_button, kiosk_button);
      $.ajax({
        type: 'POST',
        dataType: "json",
        url: './service/post_thread.php',
        data: {'name': name.value, 'feedback': feedback.value, 'message': message.value, 'type': type},
        success: function(data) {
          var status = data['status'];
          if (status === 'ok') {
            var thread_id = data['thread_id'];
            $('#success_alert').show('fast');
            location.href = '<?=get_current_path()?>' + 'thread.php?id=' + thread_id;
          } else {
            error(data);
          }
        },
        error: function(data) {
          name.removeAttribute('readOnly');
          feedback.removeAttribute('readOnly');
          message.removeAttribute('readOnly');
          post_button.removeAttribute('disabled');
          thread_button.removeAttribute('disabled');
          kiosk_button.removeAttribute('disabled');
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
            error(data);
          }
          message.removeAttribute('readOnly');
          reply_button.removeAttribute('disabled');
        },
        error: function(data) {
          message.removeAttribute('readOnly');
          reply_button.removeAttribute('disabled');
          alert('<?= _("Unable to post a reply") ?>');
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
      var type = document.getElementById("type_" + element).innerHTML;
      
      var karma_counter = null;
      var reply_id = null;
      var shown = true;
      var state = null;
      var polls_counter = null;
      
      if(thread_div !== null) {
        var first_child = thread_div.getElementsByTagName("div")[0];
        if (first_child !== undefined) {
          reply_id = first_child.getAttribute('id');
        } else {
          reply_id = "";
        }
      } else {
        shown = false;
      }
      
      if(type === '<?=Type::TYPE_THREAD?>') {
        var karma_span = document.getElementById('karma_counter_' + element);
        if(karma_span !== null) {
          karma_counter = karma_span.innerHTML;
          if(!is_numeric(karma_counter)) {
            /** Thread removed **/
            karma_counter = null;
            shown = false;
            reply_id = null;
            state = '<?=State::STATE_REMOVED?>';
          }
        }
      } else if(type === '<?=Type::TYPE_KIOSK?>') {
        var polls_span = document.getElementById('polls_counter_' + element);
        if(polls_span !== null) {
          polls_counter = polls_span.innerHTML;
          if(!is_numeric(polls_counter)) {
            /** Thread removed **/
            polls_counter = null;
            shown = false;
            reply_id = null;
            state = '<?=State::STATE_REMOVED?>';
          }
        }
      }
      
      var thread_data = {};
      if(state !== null) {
        thread_data.state = state;
      }
      if(reply_id !== null) {
        thread_data.reply = reply_id;
      }
      if(karma_counter !== null) {
        thread_data.karma = karma_counter;
      }
      if(polls_counter !== null) {
        thread_data.polls = polls_counter;
      }
      thread_data.shown = shown;
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
        var threads_array = data['threads_array'];
        var fresh_threads_count = parseInt(data['fresh_threads_count']);
        var fresh_time = parseInt(data['fresh_time']);
        for (var i = 0; i < reply_array.length; i++) {
          var reply = reply_array[i];
          display_reply(prepare_reply(reply['thread_id'], reply['reply_id'], reply['message']));
        }
        for(var thread_id in threads_array) {
          var thread_data = threads_array[thread_id];
          if(thread_data['state'] !== undefined) {
            update_state(thread_id, thread_data['state']);
          }
          if(thread_data['karma'] !== undefined) {
            update_karma(thread_id, thread_data['karma']);
          }
          if(thread_data['polls'] !== undefined) {
            update_polls(thread_id, thread_data['polls']);
          }
        }
        if (fresh_threads_count > 0 && fresh_time > 0) {
          update_fresh_threads_count(fresh_threads_count, fresh_time);
        }
        if (!one_time) {
          setTimeout("fetch_events(false)", 1000);
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
  
  function update_state(thread_id, state) {
    switch(state) {
      case '<?=State::STATE_REMOVED?>': {
          var type = document.getElementById("type_" + thread_id).innerHTML;
          disable_thread(thread_id, type);
          return false;
      }
      default: {
          return true;
      }
    }
  }
  
  function update_polls(thread_id, polls) {
    var polls_counter = document.getElementById('polls_counter_' + thread_id);
    if (polls_counter) {
      polls_counter.innerHTML = polls;
    }
  }

  function update_karma(thread_id, karma) {
    var karma_counter = document.getElementById('karma_counter_' + thread_id);

    if (karma_counter) {
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
  
  function get_type(thread_button, kiosk_button) {
    var thread_class = thread_button.className;
    var kiosk_class = kiosk_button.className;
    if(thread_class.indexOf("active") >= 0) {
      return 'thread';
    } else if(kiosk_class.indexOf("active") >= 0) {
      return 'kiosk';
    }
    thread_button.className = thread_class;
    kiosk_button.className = kiosk_class;
  }
  
  function toggle_type(thread_button, kiosk_button, active_button) {
    var thread_class = thread_button.className;
    var kiosk_class = kiosk_button.className;
    var active_class = active_button.className;
    
    thread_button.className = thread_class.replace('active', '');
    kiosk_button.className = kiosk_class.replace('active', '');
    active_button.className = active_class.replace('active', '') + ' active';
  }
  
  function disable_thread(thread_id, type) {
    var counter;
    var icon;
    var lock_reply;
    if(type === '<?=Type::TYPE_THREAD?>') {
      var like_button = document.getElementById("like_button_" + thread_id);
      var fire_button = document.getElementById("fire_button_" + thread_id);
      like_button.disabled = true;
      fire_button.disabled = true;
      
      counter = document.getElementById('karma_counter_' + thread_id);
      icon = "icon-fire";
      
      lock_reply = true;
    } else if(type === '<?=Type::TYPE_KIOSK?>') {
      counter = document.getElementById('polls_counter_' + thread_id);
      icon = "icon-lock";
      
      var enter_button = document.getElementById('enter_' + thread_id);
      if(enter_button) {
        enter_button.disabled = true;
      }
      
      var thread_direct = document.getElementById('direct_' + thread_id);
      if(thread_direct) {
        lock_reply = true;
      } else {
        lock_reply = false;
      }
    }
    
    if (counter) {
      counter.className = "label label-danger";
      counter.innerHTML = '<span class="' + icon + '"></span>';
    }

    if(lock_reply) {
      var reply_message = document.getElementById("reply_message_" + thread_id);
      var reply_button = document.getElementById("reply_button_" + thread_id);
      reply_message.disabled = true;
      reply_button.disabled = true;
    }

    var twitter = document.getElementById("twitter_" + thread_id);
    var vkontakte = document.getElementById("vkontakte_" + thread_id);
    var facebook = document.getElementById("facebook_" + thread_id);

    var moderated_action = function() {
      alert('<?= _("This thread was moderated, so you cannot share it anymore. Refresh page to get deleted threads gone forever.") ?>');
      return false;
    };
    twitter.onclick = moderated_action;
    vkontakte.onclick = moderated_action;
    facebook.onclick = moderated_action;
    <?php if ($admin): ?>
      var remove_button = document.getElementById("remove_button_" + thread_id);
      remove_button.disabled = true;
      if(type === '<?=Type::TYPE_THREAD?>') {
        var reset_button = document.getElementById("reset_button_" + thread_id);
        reset_button.disabled = true;
      }
    <?php endif; ?>
  }

  function prepare_reply(thread_id, reply_id, message) {
    if (!document.getElementById(reply_id)) {
      $('#' + thread_id).prepend(
              '<div id="' + reply_id + '" style="display:none;">' +
              '<p style="margin: -2px; margin-top: 12px;"><span class="icon-comment"></span>&nbsp;' + message + '</p>' +
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