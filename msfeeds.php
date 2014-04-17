<?php
  require_once './include/initializer.php';

  // This will be first page.
  $page_id = 1;
  
  // Thread index is id parameter from GET.
  $index = $_GET['id'];
  
  $thread_from = ($page_id - 1) * $threads_per_page;
  $threads_list = get_thread_list($link, true, $threads_per_page, $thread_from, $rated);
  $pages_total = ceil(get_threads_count($link) / $threads_per_page);

  if(sizeof($threads_list) == 0) {
	  die;
  }

  $threads_iterator = 0;
  $threads_array = array();
  // Page generation.
  if($index > sizeof($threads_list)) {
	  $index = sizeof($threads_list) - 1;
  }
  $thread = $threads_list[$index];

  // Thread info.
  $name = $thread['name'];
  $ip = $thread['ip'];
  $user_agent = $thread['user_agent'];
  $feedback = $thread['feedback'];
  $thread_id = $thread['thread_id'];
  $message = $thread['message'];
  $karma = $thread['karma'];
  $time = $thread['time'];
  $type = $thread['type'];
  $polls = $thread['polls'];
  $reply_list = array_reverse($thread['reply']);

  // Thread direct link.
  $direct_link = get_current_path() . "thread.php?id=" . $thread_id;

  $thread_date = date('d.m.Y', $time / 100);
  $thread_time = date('H:i', $time / 100);

  // Karma label.
  if(intval($karma) == 0) {
    $label_type = 'default';
  } elseif (intval($karma) > 0) {
    $label_type = 'info';
  } else {
    $label_type = 'warning';
  }

  // Browser and OS.
  $os = get_os_by_ua($user_agent);
  $browser = get_browser_by_ua($user_agent);

  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  echo "<tile>\n";
  echo "<visual lang=\"ru-RU\" version=\"2\">\n";
  echo "  <binding template=\"TileSquare150x150Text04\" branding=\"logo\" fallback=\"TileSquareImage\" contentId=\"" . $direct_link . "\">\n";
  echo "    <text id=\"1\">" . $message . "</text>\n";
  echo "  </binding>\n";
  echo "  <binding template=\"TileWide310x150Text04\" branding=\"logo\" fallback=\"TileWideImage\" contentId=\"" . $direct_link . "\">\n";
  echo "    <text id=\"1\">" . $message . "</text>\n";
  echo "  </binding>\n";
  echo "  <binding template=\"TileWide310x150Text04\" branding=\"logo\" fallback=\"TileWideImage\" contentId=\"" . $direct_link . "\">\n";
  echo "    <text id=\"1\">" . $message . "</text>\n";
  echo "  </binding>\n";
  echo "  <binding template=\"TileSquare310x310TextList02\" branding=\"logo\" contentId=\"" . $direct_link . "\">";
  echo "    <text id=\"1\">" . $message . "</text>";
  echo "    <text id=\"2\">" . $message . "</text>";
  echo "    <text id=\"3\">" . $message . "</text>";
  echo "  </binding>";
  echo "</visual>\n";
  echo "</tile>";
