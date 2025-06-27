<?php
// 定义数据库连接参数
$db_host = isset($_POST['db_host']) ? $_POST['db_host'] : '';
$db_name = isset($_POST['db_name']) ? $_POST['db_name'] : '';
$db_user = isset($_POST['db_user']) ? $_POST['db_user'] : '';
$db_pass = isset($_POST['db_pass']) ? $_POST['db_pass'] : '';
$admin_user = isset($_POST['admin_user']) ? $_POST['admin_user'] : '';
$admin_pass = isset($_POST['admin_pass']) ? $_POST['admin_pass'] : '';

$install_success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // 创建数据库连接
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 创建数据库
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name;");
        $pdo->exec("use $db_name;");
        
        // 创建必要的表格
        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(255) NOT NULL,
            `password` VARCHAR(255) NOT NULL
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `site_title` VARCHAR(255),
            `site_domain` VARCHAR(255),
            `announcement` TEXT
        );");

        // 插入管理员用户
        $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO `users` (`username`, `password`) VALUES (?, ?);");
        $stmt->execute([$admin_user, $hashed_password]);

        // 配置文件写入
        $configContent = "<?php\n";
        $configContent .= "\$db_host = '$db_host';\n";
        $configContent .= "\$db_name = '$db_name';\n";
        $configContent .= "\$db_user = '$db_user';\n";
        $configContent .= "\$db_pass = '$db_pass';\n";
        file_put_contents('config.php', $configContent);

        $install_success = true;

    } catch (PDOException $e) {
        $error_message = "安装失败: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>系统安装</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 20px;
            background-color: #f8f9fa;
        }
        .install-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }
        .install-card {
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .install-header {
            border-bottom: none;
            background-color: #f8f9fa;
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1.5rem 1rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .instructions-container {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
        }
        .instructions {
            padding-right: 10px;
        }
        .instructions h4 {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .instructions ol, .instructions ul {
            padding-left: 1.25rem;
            margin-bottom: 0;
        }
        .instructions li {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        /* 移动端优化 */
        @media (max-width: 767.98px) {
            .install-header {
                padding: 1rem;
            }
            .card-body {
                padding: 1.25rem;
            }
            .form-control {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem;
            }
            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
            .instructions-container {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container install-container">
        <div class="card install-card">
            <div class="card-header install-header text-center">
                <h2 class="mb-0">安装向导V1.0</h2>
            </div>
            
            <div class="card-body">
                <?php if ($install_success): ?>
                    <div class="alert alert-success text-center">
                        <h4 class="alert-heading">安装成功！</h4>
                        <p class="mb-3">系统已成功安装，现在可以开始使用。</p>
                        <p class="mb-3">后台地址：http://你的域名/admin</p>
                        <p class="mb-3">前台地址：http://你的域名/a.php</p>
                        <a href="login.php" class="btn btn-success">
                            登录后台
                        </a>
                    </div>
                <?php elseif (!empty($error_message)): ?>
                    <div class="alert alert-danger mb-3">
                        <?php echo $error_message; ?>
                    </div>
                    <a href="install.php" class="btn btn-outline-primary w-100">
                        重新尝试
                    </a>
                <?php else: ?>
                <h4>程序使用声明与条款</h4>
                    <!-- 安装说明 - 滚动区域 -->
                    <div class="instructions-container mb-3">
                        <div class="instructions">
                            
                            <?php
                            $readme_file = './readme.txt';
                            if (file_exists($readme_file)) {
                                $readme_content = file_get_contents($readme_file);
                                echo nl2br(htmlspecialchars($readme_content));
                            } else {
                                echo '<div class="alert alert-warning mb-0">未找到安装说明文件</div>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- 安装表单 -->
                    <form method="POST">
                        <h5 class="mb-3">数据库设置</h5>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="db_host" class="form-label">数据库主机</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" 
                                           value="<?php echo htmlspecialchars($db_host); ?>" required>
                                    <small class="form-text text-muted">通常是 localhost</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="db_name" class="form-label">数据库名称</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" 
                                           value="<?php echo htmlspecialchars($db_name); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="db_user" class="form-label">数据库用户名</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" 
                                           value="<?php echo htmlspecialchars($db_user); ?>" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="db_pass" class="form-label">数据库密码</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass" 
                                           value="<?php echo htmlspecialchars($db_pass); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">管理员账户</h5>
                        
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="admin_user" class="form-label">管理员用户名</label>
                                    <input type="text" class="form-control" id="admin_user" name="admin_user" 
                                           value="<?php echo htmlspecialchars($admin_user); ?>" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="admin_pass" class="form-label">管理员密码</label>
                                    <input type="password" class="form-control" id="admin_pass" name="admin_pass" 
                                           value="<?php echo htmlspecialchars($admin_pass); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                我已同意程序使用声明与条款开始安装
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap JS 和 Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>