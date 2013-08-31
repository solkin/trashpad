<?php
include_once 'connect_db.php';
include_once 'utils.php';

$thread_id = $_POST['thread_id'];
$karma = $_POST['karma'];
$admin_key = $_POST['admin_key'];

// Karma fetching.
$sql = "SELECT thread_id, karma FROM threads WHERE thread_id='" . $thread_id . "'";
$karma_result = mysqli_query($link, $sql) or die ('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
$karma_object = mysqli_fetch_array($karma_result, MYSQLI_ASSOC);
mysqli_free_result($karma_result);
$karma_value = $karma_object['karma'];

if($karma == 'reset') {
    if($admin_key == $secret_key) {
        $karma_value = 0;
    } else {
        die ('{"status": "failed", "reason": "access denied"}');
    }
} else {
    if ($karma > 0) {
        $karma_value++;
    } else {
        $karma_value--;
    }
}

$sql = "UPDATE threads SET karma='" . $karma_value . "' WHERE thread_id='" . $thread_id . "'";

mysqli_query($link, $sql) or die ('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

mysqli_close($link);

echo '{"status": "ok", "thread_id": "' . $thread_id . '", "karma": "'
    . $karma_value . '"}';
?>
