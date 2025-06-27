<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>查看照片</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nbj {
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-top: 20px;
        }
        .img-container {
            margin-bottom: 20px;
        }
        .img-container img {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
        }
        .pagination-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        body {
            padding-bottom: 60px;
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">查看照片</a>
        </div>
    </nav>

    <!-- 主要内容 -->
    <div class="container mt-3">
        <div class="card nbj">
            <div class="card-body text-center">
                <?php
                error_reporting(0);
                $type = trim($_GET['type']);
                $page = isset($_GET['page']) ? $_GET['page'] : 0; // 从零开始
                $id = trim($_GET['id']);
                $imgnums = 10;    // 每页显示的图片数
                $path = "img";   // 图片保存的目录
                
                if ($type == "del") {
                    echo '<div class="alert alert-warning">确定清空所有照片？</div>';
                    echo "<div class='d-flex justify-content-center gap-3 mb-4'>";
                    echo "<a href='?type=dell&id=$id' class='btn btn-danger'>确定</a>";
                    echo "<button onclick='history.back(-1)' class='btn btn-secondary'>取消</button>";
                    echo "</div>";
                    exit;
                } elseif ($type == "dell") {
                    // 清空照片函数
                    $handle = opendir($path);
                    while (false !== ($file = readdir($handle))) {
                       list($filesname, $ext) = explode(".", $file);
                       if ($ext == "png" and explode('_', $filesname)[0] == $id) {
                           if (!is_dir('./'.$file)) {
                              unlink('./img/'.$file);
                           }
                       }
                    }
                    echo '<div class="alert alert-success">该ID下的所有照片已经清除！</div>';
                }
                
                $handle = opendir($path);
                $i = 0;
                while (false !== ($file = readdir($handle))) {
                   list($filesname, $ext) = explode(".", $file);
                   if ($ext == "png" and explode('_', $filesname)[0] == $id) {
                       if (!is_dir('./'.$file)) {
                          $array[] = $file; // 保存图片名称
                          ++$i;
                       }
                   }
                }
                
                if ($array) {
                   rsort($array); // 修改日期倒序排序
                   echo "<div class='mb-4'>";
                   echo "<a href='?page=$page&id=$id&type=del' class='btn btn-danger'>清空所有照片</a>";
                   echo "</div>";
                } else {
                    echo '<div class="alert alert-info">该ID下没有任何照片</div>';
                }
                
                // 显示图片
                for ($j = $imgnums * $page; $j < ($imgnums * $page + $imgnums) && $j < $i; ++$j) {
                   echo '<div class="img-container">';
                   echo "<img src='" . $path . "/" . $array[$j] . "' class='img-fluid'>";
                   echo '</div>';
                }
                
                // 分页按钮
                $realpage = @ceil($i / $imgnums) - 1;
                $Prepage = $page - 1;
                $Nextpage = $page + 1;
                
                echo '<div class="pagination-buttons">';
                if ($Prepage < 0) {
                   echo "<button class='btn btn-outline-secondary' disabled>上一页</button>";
                   echo "<a href='?page=$Nextpage&id=$id' class='btn btn-primary'>下一页</a>";
                   echo "<a href='?page=$realpage&id=$id' class='btn btn-primary'>末页</a>";
                } elseif ($Nextpage >= $realpage) {
                   echo "<a href='?page=0&id=$id' class='btn btn-primary'>首页</a>";
                   echo "<a href='?page=$Prepage&id=$id' class='btn btn-primary'>上一页</a>";
                   echo "<button class='btn btn-outline-secondary' disabled>下一页</button>";
                } else {
                   echo "<a href='?page=0&id=$id' class='btn btn-primary'>首页</a>";
                   echo "<a href='?page=$Prepage&id=$id' class='btn btn-primary'>上一页</a>";
                   echo "<a href='?page=$Nextpage&id=$id' class='btn btn-primary'>下一页</a>";
                   echo "<a href='?page=$realpage&id=$id' class='btn btn-primary'>末页</a>";
                }
                echo '</div>';
                ?>
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap JS 和 Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
