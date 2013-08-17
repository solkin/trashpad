<?php
include_once './connect_db.php';
include_once './utils.php';

$thread_id = $_POST['thread_id'];
$admin_key = $_POST['admin_key'];

if($admin_key == $secret_key) {
    // Thread removing.
    $sql = "DELETE FROM threads WHERE thread_id='" . $thread_id . "'";
    mysqli_query($link, $sql) or die ('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');
    // Replies removing.
    $sql = "DELETE FROM reply WHERE thread_id='" . $thread_id . "'";
    mysqli_query($link, $sql) or die ('{"status": "failed", "reason": ' . json_encode(mysqli_error($link)) . '}');

    mysqli_close($link);

    echo '{"status": "ok", "thread_id": "' . $thread_id . '"}';
} else {
    die ('{"status": "failed", "reason": "access denied"}');
}
?>