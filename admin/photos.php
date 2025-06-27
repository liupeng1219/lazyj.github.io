<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

include '../config.php';
$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 获取所有图片文件
$photos = glob('../img/*.{jpg,png,gif,jpeg}', GLOB_BRACE);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $success = true;
    foreach ($_POST['delete'] as $photo) {
        if (!unlink($photo)) {
            $success = false;
        }
    }
    // 重新获取照片列表
    $photos = glob('../img/*.{jpg,png,gif,jpeg}', GLOB_BRACE);
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>照片管理</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 引入 Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 60px;
            background-color: #f8f9fa;
        }
        .photo-card {
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .photo-card:hover {
            transform: translateY(-2px);
        }
        .photo-thumbnail {
            height: 150px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 0.375rem 0.375rem 0 0;
        }
        .photo-info {
            padding: 15px;
        }
        .photo-filename {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .action-buttons {
            margin-top: 20px;
        }
        /* 模态框图片样式 */
        .modal-img {
            max-width: 100%;
            max-height: 80vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 导航栏 -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">照片管理</a>
                <div class="d-flex">
                    <a href="index.php" class="btn btn-outline-secondary me-2">返回首页</a>
                    <a href="../logout.php" class="btn btn-outline-danger">退出登录</a>
                </div>
            </div>
        </nav>

        <!-- 操作结果提示 -->
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $success ? '删除成功！' : '部分照片删除失败！'; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- 照片列表 -->
        <form method="POST">
            <div class="row">
                <?php foreach ($photos as $photo): 
                    $filename = basename($photo);
                    $relativePath = '../img/' . $filename;
                ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="photo-card h-100">
                            <img src="<?php echo $relativePath; ?>" class="photo-thumbnail w-100" 
                                 data-bs-toggle="modal" data-bs-target="#photoModal" 
                                 data-bs-img="<?php echo $relativePath; ?>" 
                                 data-bs-filename="<?php echo $filename; ?>">
                            <div class="photo-info">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="delete[]" 
                                           value="<?php echo $photo; ?>" id="delete-<?php echo $filename; ?>">
                                    <label class="form-check-label photo-filename" for="delete-<?php echo $filename; ?>">
                                        <?php echo $filename; ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- 操作按钮 -->
            <div class="action-buttons d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-secondary" onclick="selectAll()">全选</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">取消全选</button>
                </div>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash me-2"></i>删除选中的照片
                </button>
            </div>
        </form>

        <!-- 照片预览模态框 -->
        <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalFilename"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" class="modal-img" id="modalImage">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap JS 和 Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 初始化照片预览模态框
        const photoModal = document.getElementById('photoModal');
        if (photoModal) {
            photoModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-bs-img');
                const filename = button.getAttribute('data-bs-filename');
                
                const modalImage = photoModal.querySelector('.modal-img');
                const modalTitle = photoModal.querySelector('.modal-title');
                
                modalImage.src = imgSrc;
                modalTitle.textContent = filename;
            });
        }

        // 全选/取消全选函数
        function selectAll() {
            document.querySelectorAll('input[name="delete[]"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAll() {
            document.querySelectorAll('input[name="delete[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
</body>
</html>
