<?php

class Type {
  const TYPE_THREAD = 0;
  const TYPE_KIOSK = 1;
}

function get_threads_by_query($link, $include_reply, $query, $threads_count = 0, $thread_from = 0) {
  $query_parts = explode(' ', $query);

  $sql = "SELECT * FROM threads WHERE (";

  $index = 0;
  foreach ($query_parts as $query_piece) {
    if ($index > 0) {
      $sql .= " OR";
    }
    $sql .= " message LIKE '%" . $query_piece . "%'";
    $sql .= " OR";
    $sql .= " name LIKE '%" . $query_piece . "%'";
    $sql .= " OR";
    $sql .= " feedback LIKE '%" . $query_piece . "%'";
    $index++;
  }
  $sql .= ")";

  $limit = $threads_count > 0 ? " ORDER BY id DESC LIMIT " . $thread_from . ", " . $threads_count : "";
  $sql .= $limit;

  $result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  return list_threads($link, $result, $include_reply);
}

function get_random_thread($link, $include_reply) {
  $sql = "SELECT * FROM threads ORDER BY RAND() LIMIT 1";
  $result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  return list_threads($link, $result, $include_reply);
}

function get_thread($link, $include_reply, $thread_id) {
  $sql = "SELECT * FROM threads WHERE thread_id='" . $thread_id . "'";
  $result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  return list_threads($link, $result, $include_reply);
}

function get_thread_list($link, $include_reply, $threads_count = 0, $thread_from = 0, $rated = false) {
  if ($rated == true) {
    $order = 'karma';
  } else {
    $order = 'id';
  }
  $limit = $threads_count > 0 ? " ORDER BY " . $order . " DESC LIMIT " . $thread_from . ", " . $threads_count : "";
  $sql = "SELECT * FROM threads" . $limit;
  $result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  return list_threads($link, $result, $include_reply);
}

function list_threads($link, $result, $include_reply) {
  $threads_count = 0;
  $threads = array();
  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    if ($include_reply) {
      $sql = "SELECT * FROM reply WHERE thread_id='" . $row['thread_id'] . "' AND type!=-1";
      $reply_result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

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
    $index++;
  }
  $sql .= ")";

  $result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  $rows_count = mysqli_num_rows($result);

  mysqli_free_result($result);

  return $rows_count;
}

function get_threads_count($link) {
  $sql = "SELECT * FROM threads";
  $result = mysqli_query($link, $sql) or die('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

  $rows_count = mysqli_num_rows($result);

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

function get_time_millis() {
  return round(microtime(true) * 100);
}

function get_os_by_ua($user_agent) {
  $os_platform = null;

  $os_array = array(
      '/windows phone/i' => array('Windows Phone', 'windows-3'),
      '/windows nt 6.2/i' => array('Windows 8', 'windows-3'),
      '/windows nt 6.1/i' => array('Windows 7', 'windows-2'),
      '/windows nt 6.0/i' => array('Windows Vista', 'windows-2'),
      '/windows nt 5.2/i' => array('Windows Server 2003/XP x64', 'windows-2'),
      '/windows nt 5.1/i' => array('Windows XP', 'windows-2'),
      '/windows xp/i' => array('Windows XP', 'windows-2'),
      '/windows nt 5.0/i' => array('Windows 2000', 'windows-1'),
      '/windows me/i' => array('Windows ME', 'windows-1'),
      '/win98/i' => array('Windows 98', 'windows-1'),
      '/win95/i' => array('Windows 95', 'windows-1'),
      '/win16/i' => array('Windows 3.11', 'windows-1'),
      '/android/i' => array('Android', 'android'),
      '/ubuntu/i' => array('Ubuntu', 'ubuntu'),
      '/linux/i' => array('Linux', 'linux'),
      '/iphone/i' => array('iPhone', 'apple'),
      '/ipod/i' => array('iPod', 'apple'),
      '/ipad/i' => array('iPad', 'apple'),
      '/macintosh|mac os x/i' => array('Mac OS X', 'apple'),
      '/mac_powerpc/i' => array('Mac OS 9', 'apple')
  );

  foreach ($os_array as $regex => $value) {
    if (preg_match($regex, $user_agent)) {
      $os_platform = $value;
      break;
    }
  }

  return $os_platform;
}

function get_browser_by_ua($user_agent) {
  $browser = null;

  $browser_array = array(
      '/msie/i' => array('Internet Explorer','explorer'),
      '/firefox/i' => array('Firefox','firefox'),
      '/safari/i' => array('Safari','safari'),
      '/OPR/i' => array('Opera','opera'),
      '/opera/i' => array('Opera','opera'),
      '/chrome/i' => array('Chrome','chrome')
  );

  foreach ($browser_array as $regex => $value) {
    if (preg_match($regex, $user_agent)) {
      $browser = $value;
    }
  }

  return $browser;
}

function encode($in) {
    return htmlspecialchars($in, ENT_QUOTES);
}

function get_current_path() {
  $direct_link = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $direct_link .= $_SERVER['SERVER_NAME'];
  $direct_link .= htmlspecialchars($_SERVER['REQUEST_URI']);
  $question_pos = strpos($direct_link, '?');
  if ($question_pos) {
    $direct_link = substr($direct_link, 0, $question_pos);
  }
  if(substr($direct_link, -4) === ".php") {
    $direct_link = substr($direct_link, 0, strrpos($direct_link, "/") + 1);
  }
  return $direct_link;
}

function get_type_index($type) {
  switch ($type) {
    case 'thread':
      return Type::TYPE_THREAD;
    case 'kiosk':
      return Type::TYPE_KIOSK;
    default:
      return Type::TYPE_THREAD;
  }
}