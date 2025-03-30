<?php

return [
    'id' => 'my-yii2-app',
    'basePath' => dirname(__DIR__),
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfValidation' => false, 
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ], 
        'mongodb' => [
            'class' => 'yii\mongodb\Connection',
            'dsn' => 'mongodb://mongo:27017/taskmanager',
            'options' => [
                  'readPreference' => 'primaryPreferred',  
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET users' => 'user/index',
                'POST users' => 'user/create',
                'GET users/<id:[a-f0-9]{24}>' => 'user/view',
                'PUT users/<id:[a-f0-9]{24}>' => 'user/update',
                'DELETE users/<id:[a-f0-9]{24}>' => 'user/delete',
                'POST users/<id:[a-f0-9]{24}>/tasks' => 'task/task-create',
                'GET users/<id:[a-f0-9]{24}>/tasks' => 'task/index',
                'GET users/<id:[a-f0-9]{24}>/tasks/<task_id:[a-f0-9]{24}>' => 'task/view-user-task',
                'PUT users/<id:[a-f0-9]{24}>/tasks/<task_id:[a-f0-9]{24}>' => 'task/update-user-task',
                'DELETE users/<id:[a-f0-9]{24}>/tasks/<task_id:[a-f0-9]{24}>' => 'task/delete',
                'DELETE users/<id:[a-f0-9]{24}>/tasks' => 'task/delete-all',
                'GET users/<id:[a-f0-9]{24}>/tasks/stats' => 'task/stats',
                'GET tasks/stats' => 'task/global-stats',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
];
