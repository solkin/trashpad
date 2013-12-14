<?php

include_once 'settings.php';
include_once 'connect_db.php';
include_once 'utils.php';

$time = get_time_millis();
$from = intval(encode($_GET['from']));
$count = intval(encode($_GET['count']));
$rated = encode($_GET['rated']) === 'true';
$reply = encode($_GET['reply']) === 'true';

$threads_list = get_thread_list($link, $reply, $count, $from, $rated, 
        "time,name,feedback,thread_id,message,karma,type,polls", 
        "reply_id,time,message");

mysqli_close($link);

echo '{"status": "ok", "threads": ' . json_encode($threads_list) . ', "time": "' . $time . '"}';