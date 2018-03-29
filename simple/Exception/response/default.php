<?php
if (PHP_SAPI == 'cli') {
    throw $exception;
}

header('HTTP/1.1 '.$httpMessage);

$env = config('env');
$debug = $env['debug'];

$file = $debug == true ? $file . ':' . $line : '';
$trace = $debug == true ? $trace : [];

if ($env['error_type'] == 'json') {
    $response = [
        'httpCode' => $httpCode,
        'message' => $message
    ];

    if (!empty($file)) {
        $response['file'] = $file;
    }

    if (!empty($trace)) {
        $response['trace'] = $trace;
    }

    return new \Simple\Http\Response($response);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Oh! There was an error！</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            padding: 20px;
            font-family: Palatino, Optima, Georgia, serif;
        }
        h1 {
            color: #b00;
            font-size: 30px;
            padding-bottom: 20px;
        }
        p {
            padding-bottom: 10px;
            padding-top: 10px;
        }
        ul li {
            list-style: inside;
            color: #444444;
            font-size: 14px;
            line-height: 2;
        }
    </style>
</head>
<body>
<h1><?php echo $httpMessage; ?></h1>
<p><?php echo $message; ?></p>
<?php if (!empty($file)) : ?>
    <p>File: <?php echo $file; ?></p>
<?php endif; ?>
<ul>
    <?php foreach ($trace as $value) : ?>
        <li><?php echo !empty($value['file']) ? $value['file'] : '' ?>
            <?php echo !empty($value['class']) ? $value['class'] .'::' : '' ?><?php echo $value['function'] ?></li>
    <?php endforeach; ?>
</ul>
<p>Response: <?php echo microtime(true) - START; ?></p>
</body>
</html>