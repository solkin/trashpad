<?php
// Deprecated. Use fetch_events.php instead.
include_once './connect_db.php';
include_once './utils.php';
include_once './settings.php';

$count = 0;
$fetch_result = array();
$threads = json_decode($_POST['threads']);
foreach ($threads as $key => $value) {
    if ($value) {
        // Request id fro reply_id (value)
        $sql = "SELECT * FROM reply WHERE reply_id='" . $value . "'";
        $reply_result = mysqli_query($link, $sql) or die ('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
        $reply_reply = mysqli_fetch_array($reply_result, MYSQLI_ASSOC);
        $id = $reply_reply['id'];
        mysqli_free_result($reply_result);
    } else {
        // No one reply.
        $id = -1;
    }

    $sql = "SELECT * FROM reply WHERE thread_id='" . $key . "' AND id>" . $id;
    $reply_result = mysqli_query($link, $sql) or die ('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

    $thread_reply = mysqli_fetch_array($reply_result, MYSQLI_ASSOC);
    mysqli_free_result($reply_result);

    if ($thread_reply != null) {
        $fetch_result[$count++] = $thread_reply;
    }
}

echo '{"status": "ok", "reply_array":' . json_encode($fetch_result) . '}';
?>
