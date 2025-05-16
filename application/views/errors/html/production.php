<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title>哎呀！</title>

    <style type="text/css">
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'debug.css')) ?>
    </style>
</head>
<body>

<div class="container text-center">

    <h1 class="headline">系统遇到问题</h1>

    <p class="lead">我们似乎遇到了一些技术问题，请稍后再试...</p>

</div>

</body>

</html>