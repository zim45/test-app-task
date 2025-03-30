<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

try {
    (new yii\web\Application($config))->run();
} catch (yii\base\InvalidConfigException $e) {
    // Handle configuration errors
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => 'Configuration error',
        'message' => $e->getMessage()
    ]);
    exit;
}