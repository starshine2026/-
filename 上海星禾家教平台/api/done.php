// done.php 重定向到 change_status.php 的 to_trial
header("Location: change_status.php?action=to_trial&id=" . $_POST['id']);
exit;