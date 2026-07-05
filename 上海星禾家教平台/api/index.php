<?php
$conn = new mysqli("localhost", "root", "root", "上海星禾家教平台");
if ($conn->connect_error) die("连接失败: " . $conn->connect_error);

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'open';
$grade = isset($_GET['grade']) ? $_GET['grade'] : '';
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$district = isset($_GET['district']) ? $_GET['district'] : '';

if ($tab == 'trial') {
    $sql = "SELECT * FROM orders WHERE status = 1";
    $statusLabel = '待试课';
} else if ($tab == 'closed') {
    $sql = "SELECT * FROM orders WHERE status = 2";
    $statusLabel = '已成交';
} else {
    $sql = "SELECT * FROM orders WHERE status = 0";
    $statusLabel = '待接';
}

if ($grade) $sql .= " AND grade LIKE '%" . $conn->real_escape_string($grade) . "%'";
if ($subject) $sql .= " AND subject LIKE '%" . $conn->real_escape_string($subject) . "%'";
if ($district) $sql .= " AND district LIKE '%" . $conn->real_escape_string($district) . "%'";
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>家教单子管理系统</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: "PingFang SC", "Microsoft YaHei", sans-serif;
    background: #f0f2f5;
    padding: 30px 20px;
    min-height: 100vh;
}
.container { max-width: 1000px; margin: 0 auto; }

.header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 30px;
    border-radius: 16px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}
.header h1 { font-size: 26px; }
.header h1 span { font-weight: 300; font-size: 16px; opacity: 0.85; }
.badge {
    background: rgba(255,255,255,0.2);
    padding: 6px 20px;
    border-radius: 40px;
    font-size: 14px;
}
.badge strong { font-size: 20px; margin: 0 4px; }

.tabs {
    display: flex;
    gap: 6px;
    margin-bottom: 18px;
    flex-wrap: wrap;
    background: white;
    padding: 6px;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.tab-btn {
    padding: 10px 22px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.25s;
    color: #6b7280;
    background: transparent;
}
.tab-btn:hover { background: #f3f4f6; }
.tab-btn.active {
    background: #667eea;
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}
.tab-btn .count {
    display: inline-block;
    background: rgba(0,0,0,0.08);
    padding: 0 10px;
    border-radius: 40px;
    font-size: 12px;
    margin-left: 4px;
}
.tab-btn.active .count { background: rgba(255,255,255,0.2); }

.filter-bar {
    background: white;
    padding: 16px 22px;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-bottom: 18px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}
.filter-bar input {
    padding: 8px 14px;
    border: 2px solid #e8ecf1;
    border-radius: 10px;
    font-size: 14px;
    outline: none;
    background: #f8f9fc;
    flex: 1 1 120px;
    min-width: 90px;
}
.filter-bar input:focus {
    border-color: #667eea;
    background: white;
}
.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 8px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
}
.btn-secondary {
    background: #f0f2f5;
    color: #4a4a5a;
    border: 2px solid #e8ecf1;
    padding: 8px 20px;
    border-radius: 10px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
}

.card {
    background: white;
    padding: 18px 22px;
    margin-bottom: 12px;
    border-radius: 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    border-left: 5px solid #667eea;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.card .left { flex: 1; }
.card .title {
    font-size: 17px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 5px;
}
.card .title .status-tag {
    display: inline-block;
    font-size: 12px;
    font-weight: 600;
    padding: 2px 14px;
    border-radius: 40px;
    margin-left: 10px;
    vertical-align: middle;
}
.status-0 { background: #d1fae5; color: #065f46; }
.status-1 { background: #fef3c7; color: #92400e; }
.status-2 { background: #dbeafe; color: #1e40af; }

.card .info {
    color: #6b7280;
    font-size: 14px;
    display: flex;
    flex-wrap: wrap;
    gap: 14px 22px;
}
.card .info .price { color: #ef4444; font-weight: 700; }
.card .right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.btn-action {
    border: none;
    padding: 6px 18px;
    border-radius: 40px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-action:hover { transform: scale(1.03); }
.btn-trial { background: #fef3c7; color: #92400e; }
.btn-trial:hover { background: #fde68a; }
.btn-success { background: #d1fae5; color: #065f46; }
.btn-success:hover { background: #a7f3d0; }
.btn-fail { background: #fee2e2; color: #dc2626; }
.btn-fail:hover { background: #fecaca; }
.btn-recycle { background: #fef3c7; color: #92400e; }
.btn-recycle:hover { background: #fde68a; }
.btn-delete { background: #f3f4f6; color: #6b7280; }
.btn-delete:hover { background: #e5e7eb; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
}
.empty-state .icon { font-size: 48px; margin-bottom: 12px; }
.empty-state h3 { color: #374151; }
.empty-state p { color: #9ca3af; }

.add-area {
    background: white;
    padding: 20px 24px;
    border-radius: 14px;
    margin-top: 28px;
    border: 2px dashed #d1d5db;
}
.add-area .add-title { font-weight: 600; color: #374151; margin-bottom: 12px; }
.add-area form {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}
.add-area input {
    padding: 8px 14px;
    border: 2px solid #e8ecf1;
    border-radius: 10px;
    font-size: 14px;
    background: #f8f9fc;
    flex: 1 1 100px;
    min-width: 70px;
}
.add-area input:focus {
    border-color: #667eea;
    background: white;
    outline: none;
}
.btn-add {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 8px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
}

@media (max-width: 600px) {
    .header { flex-direction: column; align-items: flex-start; gap: 8px; }
    .header h1 { font-size: 20px; }
    .tabs { flex-direction: column; }
    .tab-btn { text-align: center; }
    .card { flex-direction: column; align-items: stretch; }
    .card .right { justify-content: flex-end; }
}

/* 密码弹窗遮罩 */
.password-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 999;
    justify-content: center;
    align-items: center;
}
.password-overlay.show { display: flex; }
.password-box {
    background: white;
    padding: 30px 35px;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    max-width: 400px;
    width: 90%;
}
.password-box h3 { margin-bottom: 8px; color: #1f2937; }
.password-box p { color: #6b7280; font-size: 14px; margin-bottom: 16px; }
.password-box input {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e8ecf1;
    border-radius: 10px;
    font-size: 16px;
    margin-bottom: 14px;
    outline: none;
}
.password-box input:focus { border-color: #667eea; }
.password-box .btn-row {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}
.password-box .btn-row button {
    padding: 8px 24px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    cursor: pointer;
}
.password-box .btn-confirm {
    background: #667eea;
    color: white;
}
.password-box .btn-confirm:hover { background: #5a6fd6; }
.password-box .btn-cancel {
    background: #f3f4f6;
    color: #4a4a5a;
}
.password-box .btn-cancel:hover { background: #e5e7eb; }
</style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1>📚 家教单子 <span>· 管理员模式</span></h1>
        <div class="badge">
            <?php
            $counts = [];
            for ($i = 0; $i <= 2; $i++) {
                $c = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE status = $i")->fetch_assoc();
                $counts[$i] = $c['cnt'];
            }
            $total = array_sum($counts);
            ?>
            📊 共 <strong><?php echo $total; ?></strong> 单
        </div>
    </div>

    <div class="tabs">
        <a href="?tab=open" class="tab-btn <?php echo $tab == 'open' ? 'active' : ''; ?>">
            🟢 待接 <span class="count"><?php echo $counts[0]; ?></span>
        </a>
        <a href="?tab=trial" class="tab-btn <?php echo $tab == 'trial' ? 'active' : ''; ?>">
            🟡 待试课 <span class="count"><?php echo $counts[1]; ?></span>
        </a>
        <a href="?tab=closed" class="tab-btn <?php echo $tab == 'closed' ? 'active' : ''; ?>">
            🔵 已成交 <span class="count"><?php echo $counts[2]; ?></span>
        </a>
    </div>

    <div class="filter-bar">
        <form method="GET" style="display:contents;">
            <input type="hidden" name="tab" value="<?php echo $tab; ?>">
            <input type="text" name="grade" placeholder="年级" value="<?php echo htmlspecialchars($grade); ?>">
            <input type="text" name="subject" placeholder="科目" value="<?php echo htmlspecialchars($subject); ?>">
            <input type="text" name="district" placeholder="区域" value="<?php echo htmlspecialchars($district); ?>">
            <button type="submit" class="btn-primary">🔍 筛选</button>
            <a href="?tab=<?php echo $tab; ?>" class="btn-secondary">🔄 重置</a>
        </form>
    </div>

    <div id="resultArea">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $statusMap = [
                0 => ['label' => '待接', 'class' => 'status-0', 'color' => '#10b981'],
                1 => ['label' => '待试课', 'class' => 'status-1', 'color' => '#f59e0b'],
                2 => ['label' => '已成交', 'class' => 'status-2', 'color' => '#3b82f6']
            ];
            $st = $statusMap[$row['status']];
            echo '<div class="card" style="border-left-color: ' . $st['color'] . ';">';
            echo '  <div class="left">';
            echo '    <div class="title">' . htmlspecialchars($row['title']);
            echo '      <span class="status-tag ' . $st['class'] . '">' . $st['label'] . '</span>';
            echo '    </div>';
            echo '    <div class="info">';
            echo '      <span>📖 ' . htmlspecialchars($row['grade']) . '</span>';
            echo '      <span>📘 ' . htmlspecialchars($row['subject']) . '</span>';
            echo '      <span>📍 ' . htmlspecialchars($row['district']) . '</span>';
            echo '      <span class="price">💰 ' . $row['price'] . ' 元/小时</span>';
            echo '    </div>';
            echo '  </div>';
            echo '  <div class="right">';

            if ($row['status'] == 0) {
                echo '<button class="btn-action btn-trial" onclick="promptPassword(' . $row['id'] . ', \'to_trial\', \'' . $tab . '\')">✅ 接单（待试课）</button>';
            } else if ($row['status'] == 1) {
                echo '<button class="btn-action btn-success" onclick="promptPassword(' . $row['id'] . ', \'to_success\', \'' . $tab . '\')">✅ 试课成功</button>';
                echo '<button class="btn-action btn-fail" onclick="promptPassword(' . $row['id'] . ', \'to_fail\', \'' . $tab . '\')">❌ 试课失败</button>';
            } else if ($row['status'] == 2) {
                echo '<button class="btn-action btn-recycle" onclick="promptPassword(' . $row['id'] . ', \'recycle\', \'' . $tab . '\')">♻️ 回收</button>';
                echo '<button class="btn-action btn-delete" onclick="promptPassword(' . $row['id'] . ', \'delete\', \'' . $tab . '\')">🗑️ 删除</button>';
            }

            echo '  </div>';
            echo '</div>';
        }
    } else {
        echo '<div class="empty-state">';
        echo '  <div class="icon">📭</div>';
        echo '  <h3>暂无 ' . $statusLabel . ' 单子</h3>';
        echo '  <p>切换其他 Tab 查看，或添加新单子</p>';
        echo '</div>';
    }
    ?>
    </div>

    <div class="add-area">
        <div class="add-title">➕ 添加新单子（状态默认为「待接」）</div>
        <form method="POST" action="add.php">
            <input type="text" name="title" placeholder="标题（如：高一数学）" required>
            <input type="text" name="grade" placeholder="年级" required>
            <input type="text" name="subject" placeholder="科目" required>
            <input type="text" name="district" placeholder="区域" required>
            <input type="number" name="price" placeholder="价格" required>
            <button type="submit" class="btn-add">➕ 添加</button>
        </form>
    </div>

</div>

<!-- ===== 密码弹窗 ===== -->
<div class="password-overlay" id="passwordOverlay">
    <div class="password-box">
        <h3>🔐 管理员验证</h3>
        <p>操作需要管理员密码，只有管理员可以执行此操作。</p>
        <input type="password" id="passwordInput" placeholder="请输入管理员密码" onkeydown="if(event.key==='Enter') confirmPassword()">
        <div class="btn-row">
            <button class="btn-cancel" onclick="closePassword()">取消</button>
            <button class="btn-confirm" onclick="confirmPassword()">确认</button>
        </div>
    </div>
</div>

<script>
// ===== 密码弹窗逻辑 =====
let pendingAction = null;

function promptPassword(id, action, tab) {
    pendingAction = { id, action, tab };
    document.getElementById('passwordOverlay').classList.add('show');
    document.getElementById('passwordInput').value = '';
    document.getElementById('passwordInput').focus();
}

function closePassword() {
    document.getElementById('passwordOverlay').classList.remove('show');
    pendingAction = null;
}

function confirmPassword() {
    if (!pendingAction) return;
    const password = document.getElementById('passwordInput').value.trim();
    if (!password) {
        alert('请输入密码');
        return;
    }

    // 构造表单提交
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'change_status.php';

    const fields = {
        id: pendingAction.id,
        action: pendingAction.action,
        tab: pendingAction.tab,
        password: password
    };

    for (const [key, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
}

// 点击遮罩空白处关闭
document.getElementById('passwordOverlay').addEventListener('click', function(e) {
    if (e.target === this) closePassword();
});
</script>

<?php $conn->close(); ?>
</body>
</html>