<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

include '../config.php';
$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 查看照片总数
$photo_count = count(glob('../img/*.{jpg,png,gif,jpeg}', GLOB_BRACE));

// 获取站点设置
$stmt = $pdo->query("SELECT * FROM `settings` LIMIT 1");
$settings = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理后台</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 60px;
            background-color: #f8f9fa;
        }
        .dashboard-card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: none;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08);
            border-left: 4px solid #0d6efd;
        }
        .info-card {
            border-left: 4px solid #20c997;
        }
        .action-card {
            text-align: center;
            padding: 2rem 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 1.25rem;
            color: #0d6efd;
        }
        .action-card .btn {
            margin-top: auto;
            width: 80%;
            align-self: center;
        }
        .nav-tabs {
            margin-bottom: 20px;
        }
        .badge-lg {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .alert-light {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        /* 响应式调整 */
        @media (max-width: 767.98px) {
            .action-card {
                padding: 1.5rem 1rem;
            }
            .action-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 导航栏 -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded-3 shadow-sm mb-4">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <i class="bi bi-speedometer2 me-2"></i>后台管理系统
                </a>
                <div class="d-flex">
                    <a href="../logout.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>退出登录
                    </a>
                </div>
            </div>
        </nav>

        <!-- 主要内容 -->
        <div class="row mb-4">
            <!-- 程序信息卡片 -->
            <div class="col-md-6 mb-4">
                <div class="stat-card">
                    <h5 class="d-flex align-items-center">
                        <i class="bi bi-rocket-takeoff me-2"></i>程序信息
                    </h5>
                    <hr>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-tag me-2 text-muted"></i>
                        <span class="fs-5">软件版本：V1.0</span>
                    </div>

                </div>
            </div>
            
            <!-- 统计卡片 -->
            <div class="col-md-6 mb-4">
                <div class="stat-card info-card">
                    <h5 class="d-flex align-items-center">
                        <i class="bi bi-bar-chart me-2"></i>系统统计
                    </h5>
                    <hr>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-images me-2 text-muted"></i>
                        <span class="fs-5">照片总数: 
                            <span class="badge bg-primary badge-lg rounded-pill ms-2"><?php echo $photo_count; ?></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 网站信息卡片 -->
        <div class="card dashboard-card mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>网站信息
                </h5>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">网站标题</label>
                        <div class="fs-5 d-flex align-items-center">
                            <i class="bi bi-card-heading me-2 text-muted"></i>
                            <?php echo htmlspecialchars($settings['site_title']); ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">网站域名</label>
                        <div class="fs-5 d-flex align-items-center">
                            <i class="bi bi-link-45deg me-2 text-muted"></i>
                            <?php echo htmlspecialchars($settings['site_domain']); ?>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">公告内容</label>
                    <div class="alert alert-light rounded-3">
                        <i class="bi bi-megaphone me-2"></i>
                        <?php echo nl2br(htmlspecialchars($settings['announcement'])); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 操作按钮卡片 -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card dashboard-card action-card">
                    <div class="action-icon">
                        <i class="bi bi-gear"></i>
                    </div>
                    <h4>修改网站信息</h4>
                    <p class="text-muted">更新网站标题、域名和公告</p>
                    <a href="settings.php" class="btn btn-primary mt-3">
                        <i class="bi bi-pencil-square me-1"></i>前往设置
                    </a>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card dashboard-card action-card">
                    <div class="action-icon">
                        <i class="bi bi-images"></i>
                    </div>
                    <h4>管理照片</h4>
                    <p class="text-muted">查看和删除用户上传的照片</p>
                    <a href="photos.php" class="btn btn-success mt-3">
                        <i class="bi bi-eye me-1"></i>浏览照片
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- 引入 Bootstrap JS 和 Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>