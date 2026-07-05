<?php
$conn = new mysqli("localhost", "root", "root", "上海星禾家教平台");
if ($conn->connect_error) die("连接失败: " . $conn->connect_error);

// ===== 验证密码（哈希版） =====
$password = $_POST['password'] ?? '';
// 把下面这行换成你刚才生成的哈希值（就是那一长串）
$hashedPassword = '$2y$10$wiLrt4xB0Yg4rEzDYk9kyu.D9e1.6MIR2v9FuwSDmAEAB016Sllqe';

if (!password_verify($password, $hashedPassword)) {
    echo '<script>alert("⚠️ 密码错误！只有管理员可以操作。"); history.back();</script>';
    exit;
}
// ===== 验证通过 =====

$id = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
$tab = $_POST['tab'] ?? 'open';

if ($id == 0 || empty($action)) {
    header("Location: index.php?tab=" . $tab);
    exit;
}

$statusMap = [
    'to_trial'   => 1,
    'to_success' => 2,
    'to_fail'    => 0,
    'recycle'    => 0,
    'delete'     => -1
];

$newStatus = $statusMap[$action] ?? -2;

if ($newStatus == -1) {
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
} else if ($newStatus >= 0) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $newStatus, $id);
} else {
    header("Location: index.php?tab=" . $tab);
    exit;
}

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: index.php?tab=" . $tab);
exit;
?>