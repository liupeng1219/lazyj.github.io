<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

include '../config.php';
$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 获取当前的站点设置
$stmt = $pdo->query("SELECT * FROM `settings` LIMIT 1");
$settings = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 更新网站设置
    $site_title = $_POST['site_title'];
    $site_domain = $_POST['site_domain'];
    $announcement = $_POST['announcement'];

    if ($settings) {
        // 更新现有记录
        $stmt = $pdo->prepare("UPDATE `settings` SET `site_title` = ?, `site_domain` = ?, `announcement` = ? WHERE `id` = 1;");
        $stmt->execute([$site_title, $site_domain, $announcement]);
    } else {
        // 插入新记录
        $stmt = $pdo->prepare("INSERT INTO `settings` (`site_title`, `site_domain`, `announcement`) VALUES (?, ?, ?);");
        $stmt->execute([$site_title, $site_domain, $announcement]);
    }

    // 显示成功提示
    $success_message = "设置更新成功！";
    // 重新获取设置以显示最新数据
    $stmt = $pdo->query("SELECT * FROM `settings` LIMIT 1");
    $settings = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>网站设置</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 60px;
            background-color: #f8f9fa;
        }
        .settings-card {
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        .form-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
  <div class="container">
        <!-- 导航栏 -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">网站设置</a>
                <div class="d-flex">
                    <a href="index.php" class="btn btn-outline-secondary me-2">返回首页</a>
                    <a href="../logout.php" class="btn btn-outline-danger">退出登录</a>
                </div>
            </div>
        </nav>

        <!-- 主要内容 -->
        <div class="card settings-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>网站设置</h5>
            </div>
            <div class="card-body">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="site_title" class="form-label">网站标题</label>
                        <input type="text" class="form-control" id="site_title" name="site_title" 
                               value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="site_domain" class="form-label">网站域名</label>
                        <input type="text" class="form-control" id="site_domain" name="site_domain" 
                               value="<?php echo htmlspecialchars($settings['site_domain'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="announcement" class="form-label">公告内容</label>
                        <textarea class="form-control" id="announcement" name="announcement" rows="5"><?php 
                            echo htmlspecialchars($settings['announcement'] ?? ''); 
                        ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>保存设置
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- 引入 Bootstrap JS 和 Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
