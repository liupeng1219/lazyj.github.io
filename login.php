<?php
session_start();
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'config.php';
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: admin/index.php');
            exit();
        } else {
            $error_message = "用户名或密码错误！";
        }
    } catch (PDOException $e) {
        $error_message = "数据库错误: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>后台登录</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 引入 Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            margin: 0 auto;
        }
        .login-card {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border: none;
        }
        .login-header {
            background-color: #0d6efd;
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1.5rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-floating {
            margin-bottom: 1.5rem;
        }
        .btn-login {
            width: 100%;
            padding: 0.5rem;
            font-size: 1.1rem;
        }
        /* 移动端优化 */
        @media (max-width: 575.98px) {
            .login-container {
                padding: 15px;
            }
            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="card-header login-header">
                <h3><i class="bi bi-shield-lock"></i> 后台登录</h3>
            </div>
            <div class="card-body login-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="用户名" required>
                        <label for="username"><i class="bi bi-person"></i> 用户名</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="密码" required>
                        <label for="password"><i class="bi bi-key"></i> 密码</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login mt-3">
                        <i class="bi bi-box-arrow-in-right"></i> 登录
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap JS 和 Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
