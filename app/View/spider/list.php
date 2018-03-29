<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Video List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Palatino, Optima, Georgia, serif, "Hiragino Sans GB", "Microsoft YaHei", "STHeiti", "SimSun", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", 'Segoe UI', AppleSDGothicNeo-Medium, 'Malgun Gothic', Verdana, Tahoma, sans-serif;
        }
        .container {
            padding: 20px;
        }
        .container table {
            width: 100%;
            border-top: 1px solid #DCDFE6;
            border-left: 1px solid #DCDFE6;
            border-spacing: 0;
        }
        .container table tr td {
            color: #606266;
            border-bottom: 1px solid #DCDFE6;
            border-right: 1px solid #DCDFE6;
            padding: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <div><a href="/spider/index/index">search</a></div>
    <table>
        <tr>
            <td>网站</td>
            <td>标题</td>
            <td>MD5</td>
            <td>大小</td>
            <td>状态</td>
        </tr>
        <?php foreach ($data as $k => $value) : ?>
        <tr>
            <td><?=$value['site']?></td>
            <td><?=$value['title']?></td>
            <td><?=$value['md5']?></td>
            <td><?=$value['size']?></td>
            <td><?= ($value['status']) ? '下载完成' : '排队中'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>