<?php
$conn = new mysqli("localhost", "root", "root", "上海星禾家教平台");
if ($conn->connect_error) die("连接失败: " . $conn->connect_error);

// ===== 验证密码（哈希版） =====
$password = $_POST['password'] ?? '';
// 把下面这行换成你刚才生成的哈希值（和上面保持一致）
$hashedPassword = '$2y$10$wiLrt4xB0Yg4rEzDYk9kyu.D9e1.6MIR2v9FuwSDmAEAB016Sllqe';

if (!password_verify($password, $hashedPassword)) {
    echo '<script>alert("⚠️ 密码错误！只有管理员可以添加单子。"); history.back();</script>';
    exit;
}
// ===== 验证通过 =====

$title = $_POST['title'] ?? '';
$grade = $_POST['grade'] ?? '';
$subject = $_POST['subject'] ?? '';
$district = $_POST['district'] ?? '';
$price = intval($_POST['price'] ?? 0);

if ($title && $grade && $subject && $district && $price > 0) {
    $stmt = $conn->prepare("INSERT INTO orders (title, grade, subject, district, price, status) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("ssssi", $title, $grade, $subject, $district, $price);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: index.php");
exit;
?>