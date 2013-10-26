<?php
$ini_array = parse_ini_file("data/config.ini", true) or die('{"status": "failed", "reason": "No /service/data/config.ini file."}');

$main_page = $ini_array['main_page'];
$threads_per_page = $main_page['threads_per_page'];

$database = $ini_array['database'];
$db_host = $database['db_host'];
$db_user = $database['db_user'];
$db_pass = $database['db_pass'];
$db_name = $database['db_name'];

$administration = $ini_array['administration'];
$secret_key = $administration['secret_key'];
$debug = $administration['debug'];

$service = $ini_array['service'];
$events_poll_time = $service['events_poll_time'];
$fetch_events_timeout = $service['fetch_events_timeout'];
$thread_length = $service['thread_length'];
$reply_length = $service['reply_length'];